<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Functionality	         :
JS Functions	         :
Created by		         :	Md Mamun Ahmed Sagor
Creation date 	         : 	05-11-2023
Requirment Client        :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

function update_job_mast_bh($update_id) //md mamun ahmed sagor
{
	//$data_array_se=sql_select("select sum(a.po_quantity) as po_tot,sum(a.po_total_price) as po_tot_price,b.currency_id from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and job_no_mst=$update_id and a.is_deleted=0 and a.status_active=1");
	$data_array_se = sql_select("select sum(a.po_quantity) as po_tot,sum(a.po_total_price) as po_tot_price from bh_wo_po_break_down a, bh_wo_po_details_master b where a.job_no_mst=b.job_no and job_no_mst=$update_id and a.is_deleted=0 and a.status_active=1 group by job_no_mst");
	list($po_data) = $data_array_se;
	/*if($po_data[csf('currency_id')]==1)
		{
			$poavgprice=number_format($po_data[csf('po_tot_price')]/$po_data[csf('po_tot')],2);
		}
		else
		{
			$poavgprice=number_format($po_data[csf('po_tot_price')]/$po_data[csf('po_tot')],4);
		}*/
	$poavgprice = number_format($po_data[csf('po_tot_price')] / $po_data[csf('po_tot')], 4);

	$field_array = "job_quantity*avg_unit_price*total_price";
	$data_array = "" . $po_data[csf('po_tot')] . "*" . $poavgprice . "*" . $po_data[csf('po_tot_price')] . "";
	$rID = sql_update("bh_wo_po_details_master", $field_array, $data_array, "job_no", "" . $update_id . "", 1);
	$value = array(0 => $rID, 1 => $po_data[csf('po_tot')], 2 => $poavgprice, 3 => $po_data[csf('po_tot_price')]);
	return $value;
}


function update_cost_sheet_bh($job_no) //md mamun ahmed sagor 
{
	$data_array = sql_select("select a.company_name, a.buyer_name, a.brand_id, a.avg_unit_price, a.currency_id, b.costing_per, c.fabric_cost, c.trims_cost, c.embel_cost, c.wash_cost, c.comm_cost, c.lab_test, c.inspection, c.cm_cost, c.freight, c.currier_pre_cost, c.currier_percent, c.deffdlc_cost, c.deffdlc_percent, c.certificate_pre_cost, c.common_oh, c.commission, c.depr_amor_pre_cost, c.total_cost from bh_wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_dtls c where a.job_no=b.job_no and b.job_no =c.job_no and a.job_no=$job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	if (count($data_array) > 0)
	{
		$company_name = ""; $buyer_name = ""; $avg_unit_price = 0; $currency_id = ''; $costing_per = 0; $fabric_cost_o = 0; $trims_cost_o = 0; $embel_cost_o = 0; $wash_cost_o = 0; $commarcial_o = 0; $lab_test_o = 0; $inspection_o = 0; $cm_cost_o = 0; $freight_o = 0; $currier_pre_cost_o = 0; $currier_percent = 0; $deffdlc_cost = 0; $deffdlc_percent = 0; $certificate_pre_cost_o = 0; $common_oh_o = 0; $commision_o = 0; $depr_amor_pre_cost_o = 0; $total_cost = 0; $price_dzn = 0;

		foreach ($data_array as $row)
		{
			if ($row[csf('currier_percent')] == "")  $row[csf('currier_percent')] = 0;
			if ($row[csf('deffdlc_percent')] == "")  $row[csf('deffdlc_percent')] = 0;

			$company_name = $row[csf('company_name')];
			$buyer_name = $row[csf('buyer_name')];
			$brand_id = $row[csf('brand_id')];
			$avg_unit_price = $row[csf('avg_unit_price')];
			$currency_id = $row[csf('currency_id')];
			$costing_per = $row[csf('costing_per')];

			$fabric_cost_o = $row[csf('fabric_cost')];
			$trims_cost_o = $row[csf('trims_cost')];
			$embel_cost_o = $row[csf('embel_cost')];
			$wash_cost_o = $row[csf('wash_cost')];
			$commarcial_o = $row[csf('comm_cost')];
			$lab_test_o = $row[csf('lab_test')];
			$inspection_o = $row[csf('inspection')];
			$cm_cost_o = $row[csf('cm_cost')];
			$freight_o = $row[csf('freight')];
			$currier_pre_cost_o = $row[csf('currier_pre_cost')];
			$certificate_pre_cost_o = $row[csf('certificate_pre_cost')];
			//echo $currier_pre_cost_o.'=DD';

			$currier_percent = $row[csf('currier_percent')];
			$deffdlc_cost = $row[csf('deffdlc_cost')];
			$deffdlc_percent = $row[csf('deffdlc_percent')];
			$common_oh_o = $row[csf('common_oh')];
			$commision_o = $row[csf('commission')];
			$depr_amor_pre_cost_o = $row[csf('depr_amor_pre_cost')];
			$total_cost_o = $row[csf('total_cost')];

			$costing_per_pcs = 0;
			if ($costing_per == 1) $costing_per_pcs = 12;
			else if ($costing_per == 2) $costing_per_pcs = 1;
			else if ($costing_per == 3) $costing_per_pcs = 12 * 2;
			else if ($costing_per == 4) $costing_per_pcs = 12 * 3;
			else if ($costing_per == 5) $costing_per_pcs = 12 * 4;

			$price_dzn = $row[csf('avg_unit_price')] * $costing_per_pcs;
			$price_pcs_set = $row[csf('avg_unit_price')];
		}

		$sql_f = sql_select("select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0");

		$depreciation_amorti_per = 0;
		$operating_expn_per = 0;
		foreach ($sql_f as $sql_f_row) {
			$depreciation_amorti_per = $sql_f_row[csf('depreciation_amorti')];
			$operating_expn_per = $sql_f_row[csf('operating_expn')];
		}

		//==================================Deffd. LC %==============
		$data_deffdlc_per = sql_select("select deffd_lc_cost_percent from  lib_buyer where id='$buyer_name'");
		if (count($data_deffdlc_per) > 0) {
			foreach ($data_deffdlc_per as $rowdefflc) {
				if ($rowdefflc[csf('deffd_lc_cost_percent')] == "") $rowdefflc[csf('deffd_lc_cost_percent')] = 0;
				if ($deffdlc_percent != 0) $rowdefflc[csf('deffd_lc_cost_percent')] = $deffdlc_percent;

				if ($rowdefflc[csf('deffd_lc_cost_percent')] != 0) {
					$deffdlc_cost = ($price_dzn * $rowdefflc[csf('deffd_lc_cost_percent')]) / 100;
					$deffdlc_percent = $rowdefflc[csf('deffd_lc_cost_percent')];
				} else {
					$deffdlc_cost = $deffdlc_cost;
					$deffdlc_percent = $deffdlc_percent;
				}
			}
		} else {
			$deffdlc_cost = $deffdlc_cost;
			$deffdlc_percent = $deffdlc_percent;
		}

		//==================================Currier Cost %==============

		$fob_value = $price_dzn - $commision_o;
		
		$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent from variable_order_tracking where company_name=" . $company_name . "  and variable_list=57 and tna_integrated='$buyer_name' and profit_calculative='$brand_id' and status_active=1 and is_deleted=0");
		if (count($data_currier_per)<1) {
			$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent from variable_order_tracking where company_name=" . $company_name . "  and variable_list=57 and status_active=1 and is_deleted=0");
		}
		if (count($data_currier_per) > 0) {
			$currier_cost_method = 0;
			foreach ($data_currier_per as $row)
			{
				$currier_cost_method = $row[csf("commercial_cost_method")];
				if ($currier_cost_method == "" || $currier_cost_method == 0) $currier_cost_percent = 1;
				else $currier_cost_percent = $currier_cost_method;

				$currier_cost_percent = $row[csf("commercial_cost_percent")];
				if ($currier_cost_percent == "") $currier_cost_percent == 0;
				if ($currier_percent != 0) $currier_cost_percent = $currier_percent;
				if ($currier_cost_percent == 0) $currier_cost_percent = $currier_percent;
				else $currier_cost_percent = $currier_cost_percent;
			}
			//echo "10**=".$currier_cost_percent.'=S='.$currier_cost_method.'AA';die;

			if ($currier_cost_method == 1) {
				$data_array = sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no' and status_active=1 and is_deleted=0");
				foreach ($data_array as $row) {
					$amount = def_number_format($row[csf("amount")], 8, "");
				}
			}
			else if ($currier_cost_method == 2) {
				$amount = def_number_format($price_dzn, 8, "");
			}
			else if ($currier_cost_method == 3) //On Net Selling
			{
				$amount = def_number_format($fob_value, 8, "");
			}
			else
			{
				$currier_pre_cost_o = $currier_pre_cost_o;
				$currier_percent = $currier_cost_percent;
				//echo "D=".$currier_pre_cost_o;
			}
			if($amount>0)
			{
			$currier_pre_cost_o = def_number_format(($amount * ($currier_cost_percent / 100)), 8, "");
			$currier_percent = $currier_cost_percent;
			}
		} else {
			$currier_pre_cost_o = $currier_pre_cost_o;
			$currier_percent = $currier_percent;
		}
		//echo "10**=".$currier_pre_cost_o.'='.$currier_percent.'AA';die;

		//==================================Commision==============
		$data_array_commision = sql_select("select id, commision_rate, commission_amount, commission_base_id from  wo_pre_cost_commiss_cost_dtls where job_no=$job_no and commision_rate !=0 and status_active=1 and is_deleted=0");
		if (count($data_array_commision) > 0) {
			$commision_amount_tot = 0;
			$field_array_up_comision = "commission_amount";
			foreach ($data_array_commision as $row_commision)
			{
				if ($row_commision[csf('commission_base_id')] == 1) $commision_amount = ($row_commision[csf('commision_rate')] * $price_dzn) / 100;
				else if ($row_commision[csf('commission_base_id')] == 2) $commision_amount = $row_commision[csf('commision_rate')] * $costing_per_pcs;
				else if ($row_commision[csf('commission_base_id')] == 3)
				{
					if ($costing_per == 1) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 1;
					else if ($costing_per == 2) $commision_amount = $row_commision[csf('commision_rate')] / 12;
					else if ($costing_per == 3) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 2;
					else if ($costing_per == 4) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 3;
					else if ($costing_per == 5) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 4;
					else $commision_amount = 0;
				}

				if ($currency_id == 1) {
					$commision_amount = number_format($commision_amount, 2,'.','');
					$commision_amount_tot = number_format(($commision_amount_tot + $commision_amount), 2,'.','');
				} else {
					$commision_amount = number_format($commision_amount, 4,'.','');
					$commision_amount_tot = number_format(($commision_amount_tot + $commision_amount), 4,'.','');
				}

				$id_arr[] = $row_commision[csf('id')];
				$data_array_up_comision[$row_commision[csf('id')]] = explode(",", ("" . $commision_amount . ""));
			}
			$rID = execute_query(bulk_update_sql_statement("wo_pre_cost_commiss_cost_dtls", "id", $field_array_up_comision, $data_array_up_comision, $id_arr));
		}
		//============================
		$total_cost = ($total_cost_o - $commision_o) + $commision_amount_tot;
		$fob_value = $price_dzn - $commision_amount_tot;
		//==================================Comarcial==============
		$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and tna_integrated='$buyer_name' and profit_calculative='$brand_id' and variable_list=27 and status_active=1 and is_deleted=0");
		
		if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
			$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and variable_list=27 and status_active=1 and is_deleted=0");
		}
		
		if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
			$commercial_cost_method = 1;
		}

		$amount = 0;
		$operating_expn_value = number_format(($fob_value * $operating_expn_per / 100), 4);
		if ($commercial_cost_method == 1)
		{
			$data_array = sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
			foreach ($data_array as $row) {
				$amount = def_number_format($row[csf("amount")], 8, "");
			}
		}
		else if ($commercial_cost_method == 2) $amount = def_number_format($price_dzn, 8, "");
		else if ($commercial_cost_method == 3) $amount = def_number_format($fob_value, 8, "");
		else if ($commercial_cost_method == 5 || $commercial_cost_method == 6 || $commercial_cost_method == 7)
		{
			$amount=0;
			$sql_fab="select amount as amount from wo_pre_cost_fabric_cost_dtls where job_no=$job_no and fabric_source in (1,2) and status_active=1 and is_deleted=0";
			$data_fab=sql_select($sql_fab);
			$fab_amount=0;
			foreach($data_fab as $row )
			{
				$fab_amount+=$row[csf("amount")];
			}
			unset($data_fab);
			$sql_yarn="select amount as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0";
			$data_yarn=sql_select($sql_yarn);
			$yarn_amount=0;
			foreach($data_yarn as $row )
			{
				$yarn_amount+=$row[csf("amount")];
			}
			unset($data_yarn);
			$data_array=sql_select("select fabric_cost, trims_cost, embel_cost, wash_cost, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh from wo_pre_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
			$ft_amount=0;
			foreach( $data_array as $row )
			{
				if ($commercial_cost_method == 5)
				{
					$ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
				else if ($commercial_cost_method == 6)
				{
					$ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
				else if ($commercial_cost_method == 7)
				{
					$ft_amount=$row[csf("fabric_cost")]+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
			}
			unset($data_array);
			if ($commercial_cost_method== 7) $fob_value=$ft_amount;
			else $fob_value=$ft_amount+$yarn_amount+$fab_amount;
			$amount = def_number_format($fob_value, 8, "");
		}

		$tot_com_amount = 0;

		$data_array1 = sql_select("select id, rate from wo_pre_cost_comarci_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
		foreach ($data_array1 as $row1) {
			$com_amount = def_number_format(($amount * ($row1[csf("rate")] / 100)), 8, "");
			$tot_com_amount += $com_amount;
			$rID_de = execute_query("update wo_pre_cost_comarci_cost_dtls set amount=$com_amount where id='" . $row1[csf("id")] . "'", 1);
		}
		//execute_query( "update wo_pre_cost_dtls set comm_cost=$tot_com_amount where job_no =$job_no",1 );
		execute_query("update wo_pre_cost_sum_dtls set comar_amount=$tot_com_amount where job_no =$job_no", 1);
		//============================

		$total_cost = ($total_cost - $commarcial_o) + $tot_com_amount;
		$depreciation_amorti_value = number_format(($fob_value * $depreciation_amorti_per / 100), 6,'.','');
		$total_cost = ($total_cost - $depr_amor_pre_cost_o) + $depreciation_amorti_value;

		$total_cost = ($total_cost - $common_oh_o) + $operating_expn_value;

		$margin_dzn = $price_dzn - $total_cost;

		$margin_pcs = $margin_dzn / $costing_per_pcs;

		if ($currency_id == 1) {
			$price_dzn = number_format($price_dzn, 2,'.','');
			$margin_pcs = number_format($margin_pcs, 2,'.','');
		} else {
			$price_dzn = number_format($price_dzn, 4,'.','');
			$margin_pcs = number_format($margin_pcs, 4,'.','');
		}

		$txt_fabric_po_price_percent = number_format((($fabric_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_trim_po_price_percent = number_format((($trims_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_embel_po_price_percent = number_format((($embel_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_wash_po_price_percent = number_format((($wash_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_comml_po_price_percent = number_format((($tot_com_amount / $price_dzn) * 100), 2,'.','');
		$txt_lab_test_po_price_percent = number_format((($lab_test_o / $price_dzn) * 100), 2,'.','');
		$txt_inspection_po_price_percent = number_format((($inspection_o / $price_dzn) * 100), 2,'.','');
		$txt_cm_po_price_percent = number_format((($cm_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_freight_po_price_percent = number_format((($freight_o / $price_dzn) * 100), 2,'.','');

		$txt_currier_po_price_percent = number_format($currier_percent, 2,'.','');
		$txt_currier_po_cost = number_format($currier_pre_cost_o, 4,'.','');

		$txt_deffdlc_po_price_percent = number_format($deffdlc_percent, 2,'.','');
		$txt_deffdlc_po_cost = number_format($deffdlc_cost, 4,'.','');

		$txt_certificate_po_price_precent = number_format((($certificate_pre_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_common_oh_po_price_percent = number_format((($operating_expn_value / $price_dzn) * 100), 2,'.','');
		$txt_commission_po_price_percent = number_format((($commision_amount_tot / $price_dzn) * 100), 2,'.','');
		$txt_depr_amor_po_price_percent = number_format((($depreciation_amorti_value / $price_dzn) * 100), 2,'.','');
		$txt_total_po_price_percent = number_format((($total_cost / $price_dzn) * 100), 2,'.','');

		$txt_final_price_dzn_po_price_percent = number_format((($price_dzn / $price_dzn) * 100), 2,'.','');
		$txt_margin_dzn_po_price_percent = number_format((($margin_dzn / $price_dzn) * 100), 2,'.','');
		$txt_final_price_pcs_po_price_percent = number_format((($avg_unit_price / $price_pcs_set) * 100), 2,'.','');
		//$txt_margin_pcs_po_price_percent = number_format((($margin_pcs / $price_dzn) * 100), 2);
		$txt_margin_pcs_po_price_percent = number_format((($margin_pcs / $price_pcs_set) * 100), 2,'.',''); // issue no 10695
		$cm_for_shipment_sche = number_format(($margin_dzn + $cm_cost_o), 4,'.','');

		//echo "10**".$txt_currier_po_price_percent."*".$txt_currier_po_cost."*".$txt_deffdlc_po_price_percent."*".$txt_deffdlc_po_cost; die;
		$field_array = "price_pcs_or_set*price_dzn*margin_dzn*margin_pcs_set*commission*comm_cost*depr_amor_pre_cost*total_cost*fabric_cost_percent*trims_cost_percent*embel_cost_percent*wash_cost_percent*comm_cost_percent*commission_percent *lab_test_percent*inspection_percent*cm_cost_percent*freight_percent*currier_percent*currier_pre_cost*deffdlc_percent*deffdlc_cost*certificate_percent*common_oh*common_oh_percent*depr_amor_po_price*total_cost_percent*price_dzn_percent*margin_dzn_percent*price_pcs_or_set_percent*margin_pcs_set_percent*cm_for_sipment_sche";
		$data_array = "'" . $avg_unit_price . "'*'" . $price_dzn . "'*'" . $margin_dzn . "'*'" . $margin_pcs . "'*'" . $commision_amount_tot . "'*'" . $tot_com_amount . "'*'" . $depreciation_amorti_value . "'*'" . $total_cost . "'*'" . $txt_fabric_po_price_percent . "'*'" . $txt_trim_po_price_percent . "'*'" . $txt_embel_po_price_percent . "'*'" . $txt_wash_po_price_percent . "'*'" . $txt_comml_po_price_percent . "'*'" . $txt_commission_po_price_percent . "'*'" . $txt_lab_test_po_price_percent . "'*'" . $txt_inspection_po_price_percent . "'*'" . $txt_cm_po_price_percent . "'*'" . $txt_freight_po_price_percent . "'*'" . $txt_currier_po_price_percent . "'*'" . $txt_currier_po_cost . "'*'" . $txt_deffdlc_po_price_percent . "'*'" . $txt_deffdlc_po_cost . "'*'" . $txt_certificate_po_price_precent . "'*'" . $operating_expn_value . "'*'" . $txt_common_oh_po_price_percent . "'*'" . $txt_depr_amor_po_price_percent . "'*'" . $txt_total_po_price_percent . "'*'" . $txt_final_price_dzn_po_price_percent . "'*'" . $txt_margin_dzn_po_price_percent . "'*'" . $txt_final_price_pcs_po_price_percent . "'*'" . $txt_margin_pcs_po_price_percent . "'*'" . $cm_for_shipment_sche . "'";
		$rID = sql_update("wo_pre_cost_dtls", $field_array, $data_array, "job_no", "" . $job_no . "", 1);
	}
	//update_comarcial_cost($job_no, $company_name);
}

function update_color_size_sequence_bh($txt_job_no, $btn_mood) //md mamun ahmed sagor 
{
	global $db_type;
	$colororder_by = ""; $sizeorder_by = "";
	if ($btn_mood == 1) {
		$colororder_by = "order by id ASC"; $sizeorder_by = "order by id ASC";
		//$colororder_by="order by color_order, id ASC"; $sizeorder_by="order by size_order, id ASC";
	} else if ($btn_mood == 2) {
		$colororder_by = "order by color_order ASC"; $sizeorder_by = "order by size_order ASC";
	}
	
	if($db_type==0)
	{
		$sqlc ="select color_number_id, color_order as color_order from bh_wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id, color_order $colororder_by";
	}
	else
	{
		$sqlc ="select min(id) as id, color_number_id, min(color_order) as color_order from bh_wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by";
	}
	//$sql_data = sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by");
	//echo "10**".$sqlc; die;
	$sql_data = sql_select($sqlc);
	$color_order = 1;
	foreach ($sql_data as $row) {
		$rID = execute_query("update bh_wo_po_color_size_breakdown set color_order=" . $color_order . " where color_number_id=" . $row[csf('color_number_id')] . " and job_no_mst=$txt_job_no", 0);
		$color_order++;
	}
	unset($sql_data);

	//$sql_size = sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id $sizeorder_by");
	if($db_type==0)
	{
		$sqls = "select size_number_id, size_order as size_order from bh_wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id, size_order $sizeorder_by";
	}
	else
	{
		$sqls ="select min(id) as id, size_number_id, min(size_order) as size_order from bh_wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id $sizeorder_by";
	}
	$sql_size = sql_select($sqls);
	$size_order = 1;
	foreach ($sql_size as $rows) {
		$rID = execute_query("update bh_wo_po_color_size_breakdown set size_order=" . $size_order . " where size_number_id=" . $rows[csf('size_number_id')] . " and job_no_mst=$txt_job_no", 0);
		$size_order++;
	}
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Sample Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( po_id )
		{
			//alert(po_id)
			document.getElementById('po_id').value=po_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1000" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                        <thead>
                            <tr>
                                <th colspan="7" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                            </tr>
                            <tr>
                                <th width="150">Company Name</th>
                                <th width="150">Buyer Name</th>
                                <th width="80">Job No</th>
                                <th width="100">Style Ref </th>
                                <th width="120">Order No</th>
                                <th width="200">Date Range</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="selected_job"/> <input type="hidden" id="po_id">
                                <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                                <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'bh_size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'bh_size_color_breakdown_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle" colspan="7">
                            <?=load_month_buttons(1); ?>
                            </td>
                        </tr>
                	</table>
                </td>
            </tr>
            <tr>
                <td align="center"valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_item")
{
    echo create_drop_down( "cbogmtsitem", 170, $garments_item,"", 0, "","", "","",$data);
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { $buyer=""; }

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		$year_val_cond="SUBSTRING_INDEX(a.`insert_date`, '-', 1)";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		$year_val_cond="to_char(a.insert_date,'YYYY')";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, $year_val_cond as year, b.id from bh_wo_po_details_master a, bh_wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[4] and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $order_cond $style_cond order by a.job_no DESC";

	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "90,60,120,100,100,90,90,90,80,80","1080","320",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature", "",'','0,0,0,0,0,1,0,1,3,0');
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select a.garments_nature, a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.location_name, a.style_ref_no, a.style_description, a.product_dept, a.currency_id, a.agent_name, a.order_repeat_no, a.region, a.team_leader, a.dealing_marchant, a.packing, a.ship_mode, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.plan_cut, b.shipment_date, b.pub_shipment_date, b.packing from bh_wo_po_details_master a, bh_wo_po_break_down b where  a.job_no=b.job_no_mst and  b.id='$data' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	
	
	foreach ($data_array as $row)
	{
		//echo "reset_form('sizecolormaster_1','size_color_breakdown','')\n";
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_order_no').value = '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('order_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("job_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('txt_ship_date').value = '".change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_po_qnty').value = '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_plan_cut_qnty').value = '".$row[csf("plan_cut")]."';\n";
		echo "document.getElementById('cbo_order_uom_2').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_cutup_date').value = '".change_date_format($row[csf("shipment_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_country_ship_date').value = '".change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('cbo_packing_country_level').value = '".$row[csf("packing")]."';\n";
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n";
		$company_name=$row[csf("company_name")];

		$sql_slap=sql_select("select variable_list, excut_source, editable from variable_order_tracking where company_name=$company_name and variable_list in (45,65) and is_deleted=0 and status_active=1");
		$excess_variable=$excess_per_level=$editable_id=0;
		$variable_excut="";
		if(count($sql_slap>0))
		{
			foreach($sql_slap as $row )
			{
				if($row[csf('variable_list')]==45)
				{
					$excess_variable=$row[csf('excut_source')];

					if($excess_variable==2) //Slap
					{
						$editable_id=$row[csf('editable')];
					}
					else $editable_id=1;
				}
				if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
				if($row[csf('variable_list')]==65) $excess_per_level=$row[csf('excut_source')];
			}
		}
		$variable_excut=$excess_variable."_".$excess_per_level."_".$editable_id;

		echo "$('#txt_style_description').attr('excutvariable','".$variable_excut."')".";\n";
		/*if($excess_variable==3)
		{
			//$disabled_ex_cut='disabled';
			echo "$('#txt_excess_cut').attr('disabled','disabled');\n";
		}
		else*/
		if($editable_id==1) //Slap
		{
			//$disabled_ex_cut='';
			echo "$('#txt_excess_cut').removeAttr('disabled','disabled');\n";
		}
		else if($editable_id==2 || $editable_id==0)
		{
			//$disabled_ex_cut='disabled';
			echo "$('#txt_excess_cut').attr('disabled','disabled');\n";
		}
		 else {

			echo "$('#txt_excess_cut').attr('disabled','disabled');\n";
		 }
	}
	exit();
}

if ($action=="populate_size_color_breakdown")
{
	echo load_html_head_contents("Color Size Break Down","../../../", 1, 1, $unicode,1,'');
	extract($_REQUEST);
	$data=explode("_",$data);
	$order_id=$data[0];
	$po_break_down_data=sql_select("select a.excess_cut,a.po_quantity,a.po_number,a.unit_price,b.company_name, 	b.buyer_name,b.gmts_item_id,b.order_uom,b.set_break_down,b.total_set_qnty from bh_wo_po_break_down a, bh_wo_po_details_master b where a.job_no_mst=b.job_no and a.id='$order_id'");
	list($po_break_down_data_array )=$po_break_down_data;
	$company_name=$po_break_down_data_array[csf(company_name)];
	$buyer_name=$po_break_down_data_array[csf(buyer_name)];
	$gmt_item_id=$po_break_down_data_array[csf(gmts_item_id)];
	$order_uom=$po_break_down_data_array[csf(order_uom)];
	$set_break_down=$po_break_down_data_array[csf(set_break_down)];
	$total_set_qnty=$po_break_down_data_array[csf(total_set_qnty)];
	$order_number=$po_break_down_data_array[csf(po_number)];
	$po_qnty=$po_break_down_data_array[csf(po_quantity)];
	$excess_cut=$po_break_down_data_array[csf(excess_cut)];
	if($order_uom==58)
	{
		$unit_price=$po_break_down_data_array[csf('unit_price')]/$total_set_qnty;
		//$unit_price=$po_break_down_data_array[csf('unit_price')];
	}
	else
	{
		$unit_price=$po_break_down_data_array[csf('unit_price')];
	}
	$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$company_name  and variable_list=23  and status_active=1 and is_deleted=0");
	if($color_from_library==1)
	{
		$readonly="readonly"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup($buyer_name,this.id)'";
	}
	else
	{
		$readonly=""; $plachoder=""; $onClick="";
	}
	$disabled_ex_cut='';
	$sql_slap=sql_select("select excut_source,editable from variable_order_tracking where company_name=$company_name and variable_list=45 and is_deleted=0 and status_active=1");
	if(count($sql_slap>0))
	{
		foreach($sql_slap as $row )
		{
			$excess_variable=$row[csf('excut_source')];

			if($excess_variable==2) //Slap
			{
				$editable_id=$row[csf('editable')];
			}
			else $editable_id=1;
		}
		if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
	}

	//$excess_variable=return_field_value("excut_source","variable_order_tracking"," company_name =$company_name and variable_list=45 and is_deleted=0 and status_active=1");
	if($excess_variable==3)
	{
		$disabled_ex_cut='disabled';
	}
	else if($editable_id==1) //Manual
	{
		$disabled_ex_cut='';
	}
	else if($editable_id==2 || $editable_id==0)
	{
		$disabled_ex_cut='disabled';
	}
	else $disabled_ex_cut='disabled';
	//echo $disabled_ex_cut.'='.$excess_variable.'='.$editable_id;
	?>
	<fieldset style="width:1440px;">
        <form id="colorsize_1">
        <div style="display: none;" id="clear_button_holder" >
            <input type="hidden" id="order_id" value="<?=$order_id ; ?>">
            <input type="hidden" id="total" value="<?=$po_qnty; ?>" />
            <input type="button" class="image_uploader" id="article_clear" value="Clear Article Number" onClick="clear_color('txtarticleno_');">
            <input type="button" class="image_uploader" id="color_clear" value="Clear Color" onClick="clear_color('txtcolor_');">
            <input type="button" class="image_uploader" id="size_clear" value="Clear Size" onClick="clear_color('txtsize_');">
            <input type="button" class="image_uploader" id="ord_qty_clear" value="Clear Order Qnty" onClick="clear_color('txtorderquantity_');">
            <input type="button" class="image_uploader" id="rate_clear" value="Clear Rate" onClick="clear_color('txtorderrate_');">
            <input type="button" class="image_uploader" id="excess_cut_clear" value="Clear Excess Cut" onClick="clear_color('txtorderexcesscut_');">
            <!--<input type="button" class="image_uploader" id="plancut_clear" value="Clear Plan Cut Qnty." onClick="clear_color('txtorderplancut_');">-->
        </div>
        <table width="1430px" border="1" cellspacing="0" cellpadding="0" class="rpt_table" id="size_color_break_down_list" rules="all">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" name="checkalltr" id="checkalltr" onClick="checkalltr_f(this.value)" value="1"></th>
                    <th width="140" >Gmts Item</th>
                    <th width="80">Article Number</th>
                    <th width="100">Color </th>
                    <th width="80">Size</th>
                    <th width="60">Order Qty</th>
					<th width="60" style="display:none;">Actual Qty</th>
                    <th width="60">Order Rate</th>
					<th width="60" style="display:none;">Actual Rate</th>
                    <th width="60">Amount</th>
                    <th width="50">Excess Cut</th>
                    <th width="60">Plan Cut Qty.</th>
					<th width="60">Com. value type</th>
					<th width="60">Comm. (%)</th>
					<th width="60">Local Comm.</th>
					<th width="60">Foreign Comm.</th>
					<th width="60">Factory Price</th>
					<th width="60">Factory Value</th>
                    <th width="60">Status</th>
                    <th width="60"><input type="button" id="btn_barcode" style="width:55px" class="formbutton" value="Bar-Code" onClick="fnc_barcode_generate();" /></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody id="data_form">
                <tr id="break_1"><!--onClick="tr_index(this)"-->
                    <td align="center"><input type="checkbox" name="checktr_1" id="checktr_1" onClick="tr_check(1,event);"></td>
                    <td><?=create_drop_down( "cbogmtsitem_1", 140, $garments_item,"", 1, "-- Select Item --", $selected, "calculate_total_amnt(1); check_duplicate(1,this.id);","",$gmt_item_id); ?></td>
                    <td><input type="text" id="txtarticleno_1" name="txtarticleno_1" onChange="check_duplicate(1,this.id);" class="text_boxes" style="width:80px" gmtsitem="" countryid="" articleno="" gmtscolor="" gmtssize=""/></td>
                    <td><input type="text" id="txtcolor_1" onChange="check_duplicate(1,this.id);" name="txtcolor_1" class="text_boxes" style="width:100px" <?=$readonly." ".$onClick." ".$plachoder; ?> /></td>
                    <td><input type="text" id="txtsize_1" name="txtsize_1" onChange="check_duplicate(1,this.id);" class="text_boxes" style="width:60px" /></td>
                    <td><input type="text" id="txtorderquantity_1" name="txtorderquantity_1" onBlur="set_excess_cut(this.value,document.getElementById('txtorderexcesscut_1').value, 1);" class="text_boxes_numeric" style="width:68px" onChange="validate_po_qty_co_si(1);"/></td>
                    
                    <td style="display:none;">
                    	<input type="hidden" id="txtactquantity_1" name="txtactquantity_1" class="text_boxes_numeric" style="width:60px" disabled/>
                    </td>

                    <td><input type="text" id="txtorderrate_1" onBlur="calculate_total_amnt(1);" value="<?=$unit_price; ?>"  name="txtorderrate_1"  class="text_boxes_numeric" style="width:58px" /></td>

                    <td style="display:none;">
                    	<input type="hidden" id="txtactrate_1" value=""  name="txtactrate_1"  class="text_boxes_numeric" style="width:58px" disabled/>
                    </td>

                    <td><input type="text" id="txtorderamount_1" name="txtorderamount_1" readonly class="text_boxes_numeric" style="width:60px" /></td>
                    <td><input type="text" id="txtorderexcesscut_1" onBlur="set_excess_cut(document.getElementById('txtorderquantity_1').value, this.value, 1);" value="<?=$excess_cut; ?>" name="txtorderexcesscut_1" class="text_boxes_numeric" style="width:30px" <?=$disabled_ex_cut; ?> /></td>
                    <td><input type="text" id="txtorderplancut_1"    name="txtorderplancut_1" class="text_boxes_numeric" style="width:60px" readonly /></td>
					
					<td>
						<?
                    		$commision_type = array(1=>"Percentage",2=>"Amount");
                    		echo create_drop_down( "cboComValueType_1", 80, $commision_type,"", 0, "--Select--", 2,"comm_caption_field( this.value )", "","","","" );
                    	?>
					</td>
					<td><input type="text" id="txtComm_1"    name="txtComm_1" class="text_boxes_numeric" style="width:60px"  /></td>
					<td><input type="text" id="txtLocalComm_1"    name="txtLocalComm_1" class="text_boxes_numeric" style="width:60px"   /></td>
					<td><input type="text" id="txtForeignComm_1"    name="txtForeignComm_1" class="text_boxes_numeric" style="width:60px"   /></td>
					<td><input type="text" id="txtFactoryPrice_1"    name="txtFactoryPrice_1" class="text_boxes_numeric" style="width:60px"   /></td>
					<td><input type="text" id="txtFactoryValue_1"    name="txtFactoryValue_1" class="text_boxes_numeric" style="width:60px"  /></td>

                    <td><?=create_drop_down( "cbostatus_1", 60, $row_status, 0, "", 1, "" ); ?></td>
                    <td><input type="checkbox" name="checkbarcode" id="checkbarcode_1" style="width:60px" value="2" onClick="fnc_checkbarcode(1);"></td>
                    <td>
                        <input type="button" id="increaseset_1" style="width:20px" class="formbutton" value="+" onClick="add_break_down_tr(1,this)" />
                        <input type="button" id="decreaseset_1" style="width:20px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'size_color_break_down_list',this );"/>
                        <input type="hidden" id="hiddenid_1" name="hiddenid_1" value="" style="width:15px;" class="text_boxes"/>
                        <input type="hidden" id="hidbarcode_1" name="hidbarcode_1" value="" style="width:15px;" class="text_boxes" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="1450px" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
            <tr style="text-align:right">
                <td width="40">&nbsp;</td>
                <td width="160">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="120"><input type="text" id="txt_total_order_item_qnty"  name="txt_total_order_item_qnty"  class="text_boxes" style="width:68px;text-align:right;" disabled /></td>
                <td width="80"><input type="text" id="txt_total_order_item_yetto_qnty"  name="txt_total_order_item_yetto_qnty" class="text_boxes" style="width:68px;text-align:right;" disabled /></td>
                <td width="80"><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" value="<? //echo $total_qnty; ?>"    class="text_boxes" style="width:68px;text-align:right;" disabled></td>
                <td width="70"><input type="text" id="txt_avg_rate"  name="txt_avg_rate" value="<? //echo $total_amount/$total_qnty; ?>" class="text_boxes" style="width:58px;text-align:right;" disabled></td>
                <td width="80"><input type="text" id="txt_total_amt"  name="txt_total_amt" value="<? //echo $total_amount; ?>"   class="text_boxes" style="width:68px;text-align:right;" disabled></td>
                <td width="50"><input type="text" id="txt_avg_excess_cut"  name="txt_avg_excess_cut" value="<? //echo number_format(((($total_plan_cut-$total_qnty)/$total_qnty)*100),2); ?>"  class="text_boxes" style="width:38px;text-align:right;" disabled></td>
                <td width="60"><input type="text" id="txt_total_plan_cut"  name="txt_total_plan_cut" value="<? //echo $total_plan_cut; ?>" class="text_boxes" style="width:68px;text-align:right;" disabled></td>
                <td width="60">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="center" colspan="13"  class="button_container">
                	<?=load_submit_buttons( $permission, "fnc_size_color_breakdown", 0,0,"reset_form('sizecolormaster_1','po_list_view*size_color_breakdown','')",1); ?>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="13" id="country_po_active_listview11">
                <?
                $country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );//order_quantity
                $arr=array (0=>$order_status,4=>$country_library,6=>$unit_of_measurement);
                if($db_type==0)
                {
                	$sql= "select a.is_confirmed, a.po_number, a.po_received_date, a.pub_shipment_date, c.country_id, sum(c.order_quantity) as po_quantity, b.order_uom, a.unit_price, sum(c.order_total) as po_total_price, sum(c.excess_cut_perc) as excess_cut, sum(c.plan_cut_qnty) as plan_cut, c.cutup_date, c.cutup, c.country_ship_date, c.country_remarks, c.country_type, c.packing, DATEDIFF(a.shipment_date,a.po_received_date) date_diff, a.status_active, a.details_remarks, a.id, b.job_no, b.id as job_id from bh_wo_po_break_down a, bh_wo_po_details_master b,bh_wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and a.job_id=c.job_id and c.job_id=b.id and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$order_id' group by a.id, c.country_id, b.job_no, b.id order by c.cutup_date asc";
                }
                else if($db_type==2)
                {
                	$sql= "select a.is_confirmed, a.po_number, a.po_received_date, a.pub_shipment_date, c.country_id, sum(c.order_quantity) as po_quantity, b.order_uom, a.unit_price, sum(c.order_total) as po_total_price, sum(c.excess_cut_perc) as excess_cut, sum(c.plan_cut_qnty) as plan_cut, c.cutup_date,	c.cutup, c.country_ship_date, c.country_remarks, c.country_type, c.packing, a.status_active, a.details_remarks, a.id, b.job_no, b.id as job_id from bh_wo_po_break_down a, bh_wo_po_details_master b,bh_wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and a.job_id=c.job_id and c.job_id=b.id and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$order_id' group by a.id, a.is_confirmed, a.po_number, a.po_received_date, a.pub_shipment_date, a.status_active, a.details_remarks, c.country_id, c.country_id, c.cutup_date, c.cutup, c.country_ship_date, c.country_remarks, c.country_type, c.packing, b.order_uom, a.unit_price, b.job_no, b.id order by c.cutup_date ASC";
                }
                $data_array=sql_select($sql);
	$sql_prod_data=sql_select( "select a.country_id,a.po_break_down_id, (b.production_qnty) as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$order_id'  and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ");
	 
	foreach($sql_prod_data as $row)
	{
		if($row[csf('production_qnty')]>0)
		{
			$prod_cutting_arr[$row[csf('country_id')]]+=$row[csf('production_qnty')];
		}
	}
	
                ?>
                    <table width="1050px" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="75">Order Status</th>
                                <th width="120">PO No</th>
                                <th width="70">PO Recv.Date</th>
                                <th width="70">Ship Date</th>
                                <th width="70">Cut-Off Date</th>
                                <th width="80">Country</th>
                                <th width="70">PO Qty</th>
                                <th width="50">Order Uom</th>
                                <th width="60">Avg. Rate</th>
                                <th width="80">Amount</th>
                                <th width="60">Excess Cut %</th>
                                <th width="70">Plan Cut Qty</th>
                                <th width="70">Lead Time</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                    <!-- </table>
                    <table width="1050px" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all"> -->
                        <tbody>
                        <?
                        $i=0; $txt_tot_po_qnty=0; $txt_tot_avg_rate=0; $txt_tot_amount=0; $txt_tot_excess_cut=0; $txt_tot_plancut=0;
                        foreach($data_array as $row)
                        {
							$i++;
							$txt_tot_po_qnty+=$row[csf("po_quantity")];
							$txt_tot_amount+=$row[csf("po_total_price")];
							$txt_tot_plancut+=$row[csf("plan_cut")];
							$prod_cutting=$prod_cutting_arr[$row[csf('country_id')]];
							if($prod_cutting=='') $prod_cutting=0;
							$excess_cut=number_format(((($row[csf("plan_cut")]-$row[csf("po_quantity")])/$row[csf("po_quantity")])*100),4);
							$rate=number_format(($row[csf("po_total_price")]/$row[csf("po_quantity")]),4);
							//echo $prod_cutting.'d';
							
							$onlick_data=$row[csf("id")]."_".$row[csf("country_id")]."_".change_date_format($row[csf("country_ship_date")],"dd-mm-yyyy","-")."_".change_date_format($row[csf("cutup_date")],"dd-mm-yyyy","-")."_".$row[csf("cutup")]."_".$row[csf("country_remarks")]."_".$row[csf("country_type")]."_".$row[csf("packing")]."_".$prod_cutting."_".$product_value_price;
							$onClick="onClick=populate_size_color_breakdown_with_data('$onlick_data')";
							?>
							<tr style="cursor:pointer">
								<td width="30" align="center" <?= $onClick ?>><?=$i; ?></td>
                                <td width="75" style="word-break:break-all" <?= $onClick ?>><?=$order_status[$row[csf("is_confirmed")]]; ?></td>
                                <td width="120" style="word-break:break-all" <?= $onClick ?>><?=$row[csf("po_number")]; ?></td>
                                <td width="70" <?= $onClick ?>><?=change_date_format($row[csf("po_received_date")],"dd-mm-yyyy","-"); ?></td>
                                <td width="70" <?= $onClick ?>><?=change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-"); ?></td>
                                <td width="70" <?= $onClick ?>><?=change_date_format($row[csf("cutup_date")],"dd-mm-yyyy","-"); ?></td>
                                <td width="80" style="word-break:break-all" <?= $onClick ?>><?=$country_library[$row[csf("country_id")]]; ?></td>
                                <td width="70" align="right" <?= $onClick ?>><?=number_format($row[csf("po_quantity")],0); ?></td>
                                <td width="50" <?= $onClick ?>><? //echo $unit_of_measurement[$row[csf("order_uom")]]; ?>Pcs</td>
                                <td width="60" align="right" <?= $onClick ?>><?=number_format($rate,4); ?></td>
                                <td width="80" align="right" <?= $onClick ?>><?=number_format($row[csf("po_total_price")],4); ?></td>
								<td width="60" align="right" <?= $onClick ?>><?=number_format($excess_cut,4); ?></td>
								<td width="70" align="right" <?= $onClick ?>><?=number_format($row[csf("plan_cut")],0); ?></td>
                                <td width="70" align="right" <?= $onClick ?>><?=$row[csf("date_diff")]; ?></td>
                                <td width="70" align="center"><input type="button" style="width:30px" class="formbutton" value="-" onClick="fn_delete_county_po('<? echo $row[csf("job_no")]; ?>',<? echo $row[csf("id")]; ?>, <? echo $row[csf("country_id")]; ?>, <? echo $row[csf("job_id")]; ?>);" /></td>
							</tr>
							<?
                        }
                        $txt_tot_avg_rate=number_format(($txt_tot_amount/$txt_tot_po_qnty),4);
                        $txt_tot_excess_cut=number_format(((($txt_tot_plancut-$txt_tot_po_qnty)/$txt_tot_po_qnty)*100),2)
                        ?>
                        </tbody>
                        <tfoot>
                        	<th width="30">&nbsp;</th>
                            <th width="75">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="70"><input type="text" class="text_boxes_numeric" id="txt_tot_po_qnty" style="width:58px" value="<?=$txt_tot_po_qnty; ?>" disabled /></th>
                            <th width="50">&nbsp;</th>
                            <th width="60"><input type="text" class="text_boxes_numeric" id="txt_tot_avg_rate" style="width:48px" value="<?=$txt_tot_avg_rate; ?>" disabled /></th>
                            <th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_amount" style="width:68px" value="<?=$txt_tot_amount; ?>" disabled /></th>
                            <th width="60"><input type="text" class="text_boxes_numeric" id="txt_tot_excess_cut" style="width:48px" value="<?=$txt_tot_excess_cut; ?>" disabled /></th>
                            <th width="70"><input type="text" class="text_boxes_numeric" id="txt_tot_plancut" style="width:58px" value="<?=$txt_tot_plancut; ?>" disabled /></th>
                            <th align="center"><input type="button" id="reorder" value="Re-Order" class="image_uploader" onClick="reorder_size_color();" style="width:100px"/></th>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="13" align="center">
                    <input type="button" id="colse_1" name="colse_1" onClick="parent.emailwindow.hide();" class="formbutton" value="Close"/>
                    <input type="button" id="colse_2" name="colse_2" onClick="updatepo();" class="formbutton" value="Update PO" style="display:none"/>
                </td>
            </tr>
        </table>
        </form>

	</fieldset>
	<?
	exit();
}

if($action=="populate_size_color_breakdown_with_data")
{
	$data=explode("_",$data);
	$production_quantity_arr=array();
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$sql_data=sql_select( "select b.color_size_break_down_id, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data[0]' and a.country_id=$data[1] and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
	 
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_qnty')]>0)
		{
			$production_quantity_arr[$row_data[csf('color_size_break_down_id')]]=$row_data[csf('production_qnty')];
		}
	}

	$po_break_down_data=sql_select("select a.excess_cut, a.po_quantity, a.po_number, a.unit_price, b.company_name, b.buyer_name, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.job_no from bh_wo_po_break_down a, bh_wo_po_details_master b where a.job_no_mst=b.job_no and a.id='$data[0]'");
	list($po_break_down_data_array )=$po_break_down_data;
	$gmt_item_id=$po_break_down_data_array[csf(gmts_item_id)];
	$company_name=$po_break_down_data_array[csf(company_name)];
	$buyer_name=$po_break_down_data_array[csf(buyer_name)];
	$txt_job_no=$po_break_down_data_array[csf(job_no)];

	$actpo_break_down_data=sql_select("SELECT po_break_down_id,gmts_item,gmts_color_id,gmts_size_id,po_qty,unit_value from wo_po_acc_po_info_dtls where po_break_down_id='$data[0]' and status_active=1 and is_deleted=0");
	$actual_po_data_array=array();

	foreach ($actpo_break_down_data as $row)
	{
		$actual_po_data_array[$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]]['actual_qty']+=$row[csf('po_qty')];
		$actual_po_data_array[$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]]['actual_value']+=$row[csf('unit_value')];
		
	}
	$disabled_ex_va=0;
		
	$sql=sql_select("select variable_list, copy_quotation, color_from_library, excut_source, editable from variable_order_tracking where company_name='$company_name' and variable_list in (23,45,78) order by id");
	$poEntryControlWithBomApproval=2; $color_from_library=2; $excess_variable=0; $editable_id=0;
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==23) $color_from_library=$vrow[csf('color_from_library')];
		if($vrow[csf('variable_list')]==45) 
		{
			$excess_variable=$vrow[csf('excut_source')];
			if($excess_variable==2) //slap
			{
				$editable_id=$row[csf('editable')];
			}
			else $editable_id=1;
		}
		
		if($vrow[csf('variable_list')]==78) $poEntryControlWithBomApproval=$vrow[csf('copy_quotation')];
	}
	
	if($color_from_library==1)
	{
		$readonly="readonly='readonly'"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup($buyer_name,this.id)'";
	}
	else
	{
		$readonly=""; $plachoder=""; $onClick="";
	}
	
	if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
	
	
	$bomApproved=0;
	if($poEntryControlWithBomApproval==3) 
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1 || $isapproved==3)
		{
			$bomApproved=1;
		}
	}
	if($bomApproved==1) { $fldDisable="disabled"; $cboDisable="1"; } else { $fldDisable=""; $cboDisable=""; }
	//echo $poEntryControlWithBomApproval.'-'.$bomApproved.'-'.$fldDisable.'='.$cboDisable;

	$break_data=sql_select("select * from bh_wo_po_color_size_breakdown where po_break_down_id='$data[0]' and country_id=$data[1] and is_deleted=0 and status_active=1 order by item_number_id,color_order,size_order,id");
	foreach($break_data as $row)
	{
		$k++;
		$id=$row[csf("id")];
		$total_qnty+=$row[csf("order_quantity")];
		$total_amount+=$row[csf("order_total")];
		$total_plan_cut+=$row[csf("plan_cut_qnty")];
		$item_number_id=$row[csf("item_number_id")];
		$article_number=$row[csf("article_number")];
		$color_number_id=$color_library[$row[csf("color_number_id")]];
		$size_number_id=$size_library[$row[csf("size_number_id")]];
		$order_quantity=$row[csf("order_quantity")];
		$order_rate=$row[csf("order_rate")];
		$order_total=$row[csf("order_total")];
		$excess_cut_perc=$row[csf("excess_cut_perc")];
		$plan_cut_qnty=$row[csf("plan_cut_qnty")];
		$status_active=$row[csf("status_active")];
		$barcode_no=$row[csf("barcode_no")];
		 

		$commission_value_type=$row[csf("commission_value_type")];
		$commission_per_pcs=$row[csf("commission_per_pcs")];
		$commission=$row[csf("commission")];
		$foreign_commission=$row[csf("foreign_commission")];
		$po_unit_price=$row[csf("po_unit_price")];
		$factory_price=$row[csf("factory_price")];


		$disabled_ex_cut=0;
		if($production_quantity_arr[$row[csf("id")]]>0)
		{
			$disabled_ex_cut=1; $title='Cutting Qty Found'; $fld_disable="disabled";
		}
		else
		{
			$disabled_ex_cut=0; $title=""; $fld_disable="";//$excess_fld_disable="";
		}

		if($editable_id==1) //Slap//Yes
		{
			$excess_fld_disable="";
		}
		else if($editable_id==2 || $editable_id==0) //Slap//No
		{
			$excess_fld_disable="disabled";
		}
		else $excess_fld_disable="disabled";

		$actual_qty=$actual_po_data_array[$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['actual_qty'];
		$actual_value=$actual_po_data_array[$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['actual_value'];
		$actual_rate=0;
		if($actual_value>0 && $actual_qty>0){
			$actual_rate=$actual_value/$actual_qty;
		}		
		$actualqtycolor="" ; $actualratecolor="";
		if($actual_qty!=$order_quantity) $actualqtycolor='background-color:#FF0000';
		if($actual_rate!=$order_rate) $actualratecolor='background-color:#FF0000';
		?>
		<tr id="break_<?=$k; ?>" ><!--onClick="tr_index(this)"-->
            <td align="center"><input type="checkbox" name="checktr_<?=$k; ?>" id="checktr_<?=$k; ?>" onClick="tr_check(<?=$k; ?>,event);" <?=$fldDisable; ?> ></td>
            <td><?=create_drop_down( "cbogmtsitem_".$k, 140, $garments_item,"", 0, "",$item_number_id, "calculate_total_amnt(".$k." ); check_duplicate(".$k.",this.id)","$cboDisable",$gmt_item_id,""); ?></td>
            <td><input type="text" id="txtarticleno_<?=$k; ?>" name="txtarticleno_<?=$k; ?>" value="<?=$article_number; ?>" onChange="check_duplicate(1,this.id)" class="text_boxes" style="width:80px" gmtsitem="<?=$item_number_id; ?>" countryid="<?=$data[1]; ?>" articleno="<?=$article_number; ?>" gmtscolor="<?=$color_number_id; ?>"gmtssize="<?=$size_number_id; ?>" <?=$fldDisable; ?>></td>
            <td><input type="text" id="txtcolor_<?=$k; ?>" onChange="check_duplicate(<?=$k; ?> ,this.id);"name="txtcolor_<?=$k; ?>" value="<?=$color_number_id; ?>"  class="text_boxes" style="width:100px" <?=$readonly." ".$onClick ?> <?=$fld_disable; ?> title="<?=$title; ?>" <?=$fldDisable; ?>/></td>
            <td><input type="text" id="txtsize_<?=$k; ?>" name="txtsize_<?=$k; ?>" onChange="check_duplicate(<?=$k; ?>,this.id);" value="<?=$size_number_id; ?>"  class="text_boxes" style="width:68px" <?=$fld_disable; ?> title="<?=$title; ?>" <?=$fldDisable; ?>/></td>
            <td><input type="text" id="txtorderquantity_<?=$k; ?>" name="txtorderquantity_<?=$k; ?>" onBlur="set_excess_cut(this.value,document.getElementById('txtorderexcesscut_<?=$k; ?>').value, <?=$k; ?>);" value="<?=$order_quantity; ?>" saved_po_quantity="<?=$order_quantity; ?>" production_quantity="<?=$production_quantity_arr[$id]?>" onChange="validate_po_qty_co_si(<?=$k; ?>);" class="text_boxes_numeric" style="width:68px"></td>
			<td style="display:none;">
				<input type="hidden" id="txtactquantity_<?=$k; ?>" name="txtactquantity_<?=$k; ?>"  class="text_boxes_numeric" value="<?=$actual_qty; ?>" style="width:60px; <?=$actualqtycolor; ?>" readonly disabled>
			</td>
            <td><input type="text" id="txtorderrate_<?=$k; ?>" onBlur="calculate_total_amnt(<?=$k; ?>);" value="<?=$order_rate; ?>" name="txtorderrate_<?=$k; ?>"  class="text_boxes_numeric" style="width:58px" <?=$fldDisable; ?>></td>			
			<td style="display:none;">
				<input type="hidden" id="txtactrate_<?=$k; ?>" name="txtactrate_<?=$k; ?>"  class="text_boxes_numeric" value="<?=$actual_rate; ?>" style="width:50px; <?=$actualratecolor; ?>" readonly disabled>
			</td>
            <td><input type="text" id="txtorderamount_<? echo $k; ?>" name="txtorderamount_<? echo $k; ?>"  value="<? echo $order_total; ?>" readonly class="text_boxes_numeric" style="width:68px" <?=$fldDisable; ?>></td>
            <td><input type="text" id="txtorderexcesscut_<?=$k; ?>" onBlur="set_excess_cut(document.getElementById('txtorderquantity_<?=$k; ?>').value, this.value, <?=$k; ?>);" name="txtorderexcesscut_<?=$k; ?>" value="<?=$excess_cut_perc; ?>" class="text_boxes_numeric" style="width:38px" <?=$excess_fld_disable; ?> title="<?=$title;  ?>" <?=$fldDisable; ?>/></td>
            <td><input type="text" id="txtorderplancut_<?=$k; ?>" name="txtorderplancut_<?=$k; ?>" value="<?=$plan_cut_qnty; ?>" class="text_boxes_numeric" style="width:68px" readonly <?=$fldDisable; ?>/></td>
            

			<td>
				<?
                    $commision_type = array(1=>"Percentage",2=>"Amount");
                    echo create_drop_down( "cboComValueType_".$k, 80, $commision_type,"", 0, "--Select--", $commission_value_type,"comm_caption_field( this.value )", "","","","" );
                ?>
			</td>            
			<td><input type="text" id="txtComm_<?=$k; ?>" name="txtComm_<?=$k; ?>" value="<?=$commission_per_pcs; ?>" class="text_boxes_numeric" style="width:60px"   /></td>            
			<td><input type="text" id="txtLocalComm_<?=$k; ?>" name="txtLocalComm_<?=$k; ?>" value="<?=$commission; ?>" class="text_boxes_numeric" style="width:60px"  /></td>            
			<td><input type="text" id="txtForeignComm_<?=$k; ?>" name="txtForeignComm_<?=$k; ?>" value="<?=$foreign_commission; ?>" class="text_boxes_numeric" style="width:60px"    /></td>            
			<td><input type="text" id="txtFactoryPrice_<?=$k; ?>" name="txtFactoryPrice_<?=$k; ?>" value="<?=$po_unit_price; ?>" class="text_boxes_numeric" style="width:60px"    /></td>
            <td><input type="text" id="txtFactoryValue_<?=$k; ?>" name="txtFactoryValue_<?=$k; ?>" value="<?=$factory_price; ?>" class="text_boxes_numeric" style="width:60px"    /></td>

			 

            
			
			<td><?=create_drop_down( "cbostatus_".$k, 60, $row_status, 0, "", 1, $status_active,"","$cboDisable" ); ?></td>
            <td><input type="checkbox" name="checkbarcode" id="checkbarcode_<?=$k; ?>" style="width:60px" value="2" onClick="fnc_checkbarcode(<?=$k; ?>);"></td>
            <td>
                <input type="button" id="increaseset_<?=$k; ?>" style="width:20px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$k; ?>,this);" <?=$fldDisable; ?>/>
                <input type="button" id="decreaseset_<?=$k; ?>" style="width:20px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$k; ?> ,'size_color_break_down_list',this );" <?=$fld_disable; ?> title="<?=$title; ?>" <?=$fldDisable; ?>/>

                <input type="hidden" id="hiddenid_<?=$k; ?>" name="hiddenid_<?=$k; ?>" value="<?=$id; ?>" style="width:15px;" class="text_boxes" />
                <input type="hidden" id="hidbarcode_<?=$k; ?>" name="hidbarcode_<?=$k; ?>" value="<?=$barcode_no; ?>" style="width:15px;" class="text_boxes" />
                <input type="hidden" id="cutcountry_<?=$k; ?>" name="cutcountry_<?=$k; ?>" value="<?=array_sum($production_quantity_arr); ?>" style="width:15px;" class="text_boxes" />
            </td>
		</tr>
		<?
	}
	exit();
}

if($action=="check_country")
{
	$data=explode("_",$data);
	if($data[2]>0) $itemCond="and item_number_id=$data[2]";else $itemCond="";
	$sql="Select country_id from bh_wo_po_color_size_breakdown where po_break_down_id=$data[0] and country_id=$data[1] and is_deleted =0 and status_active=1 $itemCond";
	$data_array=sql_select($sql);
	$country=count($data_array);

	$sql_cut_off="Select cut_off from lib_country where id=$data[1] and is_deleted =0 and status_active=1";
	$res_cut_off=sql_select($sql_cut_off);
	$cut_off=$res_cut_off[0][csf('cut_off')];

	echo $country."_".$cut_off."_".$data[1];
}

if($action=="delete_row_color_size")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
		$sql_data=sql_select("select color_number_id as color_number_id,job_no_mst  as job_no_mst,po_break_down_id  from  bh_wo_po_color_size_breakdown where id=$data  and status_active=1 and is_deleted=0 ");
		$color_number_id=$sql_data[0][csf('color_number_id')];
		$job_no=$sql_data[0][csf('job_no_mst')];
		$po_id=$sql_data[0][csf('po_break_down_id')];
		/*$color_number_id=return_field_value("color_number_id as color_number_id", " wo_pre_cos_fab_co_avg_con_dtls", "job_no='$job_no' and po_break_down_id=$po_id and color_number_id=$color_number_id ","color_number_id");
		if($color_number_id!="")
		{
			echo "100**Color ID Found, Deleting Not Allowed";
			die;
		}*/
	///$rID_de1=execute_query( "delete from bh_wo_po_color_size_breakdown where id =".$data."",0);

	$rID_de1=execute_query( "update bh_wo_po_color_size_breakdown set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =".$data."",0);
	//echo "10**select po_break_down_id from wo_po_sample_approval_info where po_break_down_id =$po_id and color_number_id=".$data." ";die;
	//execute_query( "update  wo_po_sample_approval_info set status_active=0,is_deleted=1  where  po_break_down_id =$po_id and color_number_id=".$data." ",1);
	//execute_query( "update  wo_po_lapdip_approval_info set status_active=0,is_deleted=1  where  po_break_down_id =$po_id and color_name_id=".$color_number_id." ",1);
	if($db_type==0)
	{
		if($rID_de1 ){
		mysql_query("COMMIT");
		echo "2";
		}
		else{
		mysql_query("ROLLBACK");
		echo "10";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID_de1 ){
		oci_commit($con);
		echo "2";
		}
		else{
		oci_rollback($con);
		echo "10";
		}
	}
	disconnect($con);
	die;
}

if($action=="inserted_po_qnty")
{
	$data=explode("_",$data);
	$order_quantity=return_field_value("sum(order_quantity)","bh_wo_po_color_size_breakdown","po_break_down_id='$data[0]' and status_active =1 and is_deleted=0 group by po_break_down_id");
	$order_quantity_country=return_field_value("sum(order_quantity)","bh_wo_po_color_size_breakdown","po_break_down_id='$data[0]' and country_id=$data[1] and status_active =1 and is_deleted=0 group by po_break_down_id");
	echo $order_quantity."_".$order_quantity_country;
	die;
}

if($action == "populate_po_data_from_color_size_popup")
{
	$sql = "SELECT b.ship_to,b.product_value_price,b.unit_lot FROM  bh_wo_po_break_down b where b.id = $data";
	//echo "console.log(`$sql`);\n";
	$res = sql_select($sql);
	if(count($res))
	{
		foreach($res as $row)
		{
			echo "document.getElementById('txt_ship_to').value='".$row[csf('ship_to')]."';\n";
			echo "document.getElementById('txt_product_value_price').value='".$row[csf('product_value_price')]."';\n";
			echo "document.getElementById('txt_unit_lot').value='".$row[csf('unit_lot')]."';\n";
		}
		
	}
}

if($action=="populate_size_color_breakdown_pop_up")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode,1,'');
	extract($_REQUEST);
	$excess_per_level=$excess_variable=$editable_id=0; $poEntryControlWithBomApproval=2;
	$excess_sql="select variable_list, excut_source, editable, copy_quotation from variable_order_tracking where company_name ='$cbo_company_name' and variable_list in (45,65,78) and is_deleted=0 and status_active=1";

	$excess_sql_res=sql_select($excess_sql);
	foreach($excess_sql_res as $row)
	{
		if( $row[csf("variable_list")]==45) $excess_variable=$row[csf("excut_source")];
		if( $row[csf("variable_list")]==45) $editable_id=$row[csf("editable")];
		if( $row[csf("variable_list")]==65) $excess_per_level=$row[csf("excut_source")];
		if( $row[csf('variable_list')]==78) $poEntryControlWithBomApproval=$row[csf('copy_quotation')];
	}
	unset($excess_sql_res);
	
	$bomApproved=0;
	if($poEntryControlWithBomApproval==3) 
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_id='$hidd_job_id' and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1 || $isapproved==3)
		{
			$bomApproved=1;
		}
	}
	if($bomApproved==1) { $fldDisable="disabled"; $cboDisable="1"; } else { $fldDisable=""; $cboDisable=""; }
	//echo $poEntryControlWithBomApproval.'='.$bomApproved.'='.$fldDisable.'='.$cboDisable;
	?>
	<script>
	var permission='<?=$permission; ?>';
	var cbo_company_name='<?=$cbo_company_name; ?>';
	var cbo_buyer_name='<?=$cbo_buyer_name; ?>';
	var excess_per_level='<?=$excess_per_level; ?>';
	var excess_variable='<?=$excess_variable; ?>';
	var editable_id='<?=$editable_id; ?>';
	var with_qty='<?=$with_qty; ?>';
	var hidd_po_poQty='<?=$txt_po_quantity; ?>';
	var cbo_order_status='<?=$cbo_order_status; ?>';
	var working_company_id='<?=$working_company_id; ?>';
	var working_location_id='<?=$working_location_id; ?>';
	var cbo_status='<?=$cbo_status; ?>';
	var tot_smv_qnty='<?=$tot_smv_qnty; ?>';
	var bomApproved='<?=$bomApproved; ?>';
	
	if(bomApproved==1) { var isDisable="disabled"; var cboDisable="1"; } else { var isDisable=""; var cboDisable=""; }
	//alert(permission);
	var str_size = [<?=substr(return_library_autocomplete("select size_name from lib_size where status_active=1 and is_deleted=0", "size_name"), 0, -1); ?>];
	var str_color = [<?=substr(return_library_autocomplete("select color_name from lib_color where status_active=1 and is_deleted=0", "color_name"), 0, -1); ?>];

	function fn_delete_county_po(txt_job_no, order_id,country_id,hidd_job_id){
		freeze_window(2);
		if(bomApproved==1) { alert("BOM Approved"); release_freezing(); return; }

		var cutting_qty=return_global_ajax_value(order_id+"_"+country_id, 'get_cutting_qty_country', '', 'bh_size_color_breakdown_controller');
		if(cutting_qty>0){
			alert("Production found; So delete not allowed");
			release_freezing();
			return;
		}

		var po_id=order_id;
		if(po_id && txt_job_no)
		{
			var shipping_status = trim(return_global_ajax_value(txt_job_no+'***'+po_id, 'check_po_shipping_status_po_id', '', 'order_entry_by_buying_house_controller'));
				console.log(shipping_status);
				var d=shipping_status.split("***");
				if((d[0]*1)==3)
				{
				alert('Shipping Status is Full Delivery/Closed . Any change in not allowed');
				release_freezing();
				return;
				}
		}
		if(cbo_order_status==1)//issue id 12070
		{
			var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
			var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
			if(trim(booking_no_with_approvet_status_arr[0]) !="")
			{
				var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
				if(booking_no_with_approvet_status_arr[1] !="")
				{
					al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
				}
				al_magg+=" found,\nPlease Un-approved the booking first";
				alert(al_magg);
				release_freezing();
				return;
			}

			if(trim(booking_no_with_approvet_status_arr[1]) !="")
			{
				var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
				var r=confirm(al_magg);
				release_freezing();
				if(r==false) return;
			}
		}
		var operation=2;
		var data="action=save_update_delete&operation="+operation+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&order_id='+order_id+'&cbo_po_country='+country_id;
		
		http.open("POST","bh_size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}

	//alert(with_qty);
	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("po_id") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/bh_size_color_breakdown_controller" );
				show_list_view(theemail.value,'populate_size_color_breakdown','size_color_breakdown','../woven_order/requires/bh_size_color_breakdown_controller','');
				//set_button_status(0, permission, 'fnc_size_color_breakdown',1);
				release_freezing();
				navigate_arrow_key();
			}
		}
	}

	function fnc_size_color_breakdown( operation )
	{
		//fnc_size_color_breakdown
		//cbo_po_country
		
		freeze_window(operation);
		if(operation==0 || operation==2)
		{
			if(bomApproved==1) { alert("BOM Approved"); release_freezing(); return; }
		}
		var row_num=$('#size_color_break_down_list tr').length-1;
		var data_all="";
		var po_qnty="";
		var total=(document.getElementById('total').value)*1;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1;
		var txt_total_order_qnty=(document.getElementById('txt_total_order_qnty').value)*1;
		var order_id=document.getElementById('order_id').value;
		var hidd_job_id=document.getElementById('hidd_job_id').value;
	 
		console.log(`${with_qty} ==> ${hidd_po_poQty} ==> ${txt_total_order_qnty} ==> ${cbo_order_uom}`)
	 
		if(with_qty==1 && hidd_po_poQty!=txt_total_order_qnty && cbo_order_uom==1) //Issue id 8333 -Hams Garments
		{
			 	alert("PO Qty and break down Qty should be same");
				release_freezing();
				return;
		}
		
		var cbo_po_country=document.getElementById('cbo_po_country').value;
		var hid_old_country=document.getElementById('hid_old_country').value;
		var cbo_buyer_name= document.getElementById('cbo_buyer_name').value
		var hidd_order_status_id= document.getElementById('hidd_order_status_id').value
		if(operation==2)
		{
			var cutting_qty=return_global_ajax_value(order_id+"_"+cbo_po_country, 'get_cutting_qty_country', '', 'bh_size_color_breakdown_controller');
			if(cutting_qty>0){
				alert("Production found; So delete not allowed");
				release_freezing();
				return;
			}
		}
		//alert(operation)
		if(operation==1 || operation==2)
		{
			var po_id=order_id;
			var txt_job_no=document.getElementById('txt_job_no').value;
			  if(po_id && txt_job_no)
		      {
			        var shipping_status = trim(return_global_ajax_value(txt_job_no+'***'+po_id, 'check_po_shipping_status_po_id', '', 'order_entry_by_buying_house_controller'));
			          console.log(shipping_status);
			          var d=shipping_status.split("***");
			          if((d[0]*1)==3)
			          {
			            alert('Shipping Status is Full Delivery/Closed . Any change in not allowed');
			            release_freezing();
			            return;
			          }
		      }
			if(cbo_order_status==1)//issue id 12070
			{
				var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
				var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
				if(trim(booking_no_with_approvet_status_arr[0]) !="")
				{
					var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
					if(booking_no_with_approvet_status_arr[1] !="")
					{
						al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
					}
					al_magg+=" found,\nPlease Un-approved the booking first";
					alert(al_magg);
					release_freezing();
					return;
				}

				if(trim(booking_no_with_approvet_status_arr[1]) !="")
				{
					var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
					var r=confirm(al_magg);
					release_freezing();
					if(r==false) return;
				}
			}
		}

		if(cbo_order_uom==58) po_qnty=total*tot_set_qnty; else po_qnty=total;

		//alert(inserted_po_qnty_arr[0])
		if(operation==0)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+cbo_po_country, 'inserted_po_qnty', '', 'bh_size_color_breakdown_controller');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			//alert(inserted_po_qnty_arr[0])
			if((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)>po_qnty*1 && with_qty==1)
			{
				alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
				release_freezing();
				return;
			}
		}

		if(operation==1)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+hid_old_country, 'inserted_po_qnty', '', 'bh_size_color_breakdown_controller');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			if((((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)-inserted_po_qnty_arr[1]*1))>po_qnty && with_qty==1)
			{
				alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
				release_freezing();
				return;
			}
		}

		var z=1; var bom_sync_msg=0;
		var product_value_flag = 1;
		var product_value_row=$('#tbl_product_price_list tbody tr').length;
		for(var sl_p = 1;sl_p <=product_value_row ; sl_p++)
		{
			if('<?=$_SESSION["logic_erp"]["mandatory_field"][163][12]?>' && '<?=$_SESSION["logic_erp"]["mandatory_field"][163][13]?>')
			{
				if (form_validation(`txtpricevalue_${sl_p}*cbopricecurrency_${sl_p}`,'Price*Curency')==false)
				{
					alert("Product Value Price and Currency can't be empty");
					release_freezing();
					product_value_flag = 0;
					break;
					return;
				}
			}
			else if('<?=$_SESSION["logic_erp"]["mandatory_field"][163][12]?>')
			{
				if (form_validation(`txtpricevalue_${sl_p}`,'Price')==false)
				{
					alert("Price can't be empty");
					release_freezing();
					product_value_flag = 0;
					break;
					return;
				}
			}
			else if('<?=$_SESSION["logic_erp"]["mandatory_field"][163][13]?>')
			{
				if (form_validation(`cbopricecurrency_${sl_p}`,'Curency')==false)
				{
					alert("Currency can't be empty");
					release_freezing();
					product_value_flag = 0;
					break;
					return;
				}
			}
			
		}
		
	
		
		if(product_value_flag == 0 ) return ;
		
		for (var i=1; i<=row_num; i++)
		{
			if('<?=$_SESSION["logic_erp"]["mandatory_field"][163][11]?>')
			{
				if (form_validation(`txt_unit_lot`,'Unit Lot')==false)
				{
					//alert("Currency can't be empty");
					release_freezing();
					break;
					return;
				}
			}
			
			if (form_validation('cbo_po_country*txt_country_ship_date*txt_ship_to*cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i,'Country*Company Name*Location Name*Ship Date*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
			{
				return;
			}
			if($('#txtorderrate_'+i).val()==0)
			{
				alert("Fill Up Rate");
				$('#txtorderrate_'+i).focus();
				release_freezing();
				return;
			}
			eval(get_submitted_variables('txt_job_no*hidd_job_id*order_id*txt_tot_avg_rate*txt_tot_amount*cbo_order_uom*tot_set_qnty*txt_tot_excess_cut*txt_tot_plancut*cbo_po_country*txt_country_ship_date*hiddenid_'+i+'*cbogmtsitem_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cboComValueType_'+i+'*txtComm_'+i+'*txtLocalComm_'+i+'*txtForeignComm_'+i+'*txtFactoryPrice_'+i+'*txtFactoryValue_'+i+'*cbostatus_'+i+'*hidbarcode_'+i));
			
			var precountryId=$('#txtarticleno_'+i).attr('countryid');
			var pregmtsitem=$('#txtarticleno_'+i).attr('gmtsitem');
			var prearticleno=$('#txtarticleno_'+i).attr('articleno');
			var pregmtscolor=$('#txtarticleno_'+i).attr('gmtscolor');
			var pregmtssize=$('#txtarticleno_'+i).attr('gmtssize');
			
			if(precountryId!="" && precountryId!=$('#cbo_po_country').val()) bom_sync_msg=1;
			if(pregmtsitem!="" && pregmtsitem!=$('#cbogmtsitem_'+i).val()) bom_sync_msg=1;
			if(prearticleno!="" && prearticleno!=$('#txtarticleno_'+i).val()) bom_sync_msg=1;
			if(pregmtscolor!="" && pregmtscolor!=$('#txtcolor_'+i).val()) bom_sync_msg=1;
			if(pregmtssize!="" && pregmtssize!=$('#txtsize_'+i).val()) bom_sync_msg=1;
			
			if( $('#hiddenid_'+i).val()=="" ||  $('#hiddenid_'+i).val()==0) bom_sync_msg=1;

			//data_all=data_all+get_submitted_data_string('txt_country_ship_date*txt_cutup_date*cbo_cut_up*txt_country_remarks*cbo_po_country_type*cbo_packing_country_level*cbogmtsitem_'+i+'*hiddenid_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cbostatus_'+i+'*hidbarcode_'+i,"../../../",i);
			data_all+="&cbogmtsitem_" + z + "='" + $('#cbogmtsitem_'+i).val()+"'"+"&hiddenid_" + z + "='" + $('#hiddenid_'+i).val()+"'"+"&txtarticleno_" + z + "='" + $('#txtarticleno_'+i).val()+"'"+"&txtcolor_" + z + "='" + $('#txtcolor_'+i).val()+"'"+"&txtsize_" + z + "='" + $('#txtsize_'+i).val()+"'"+"&txtorderquantity_" + z + "='" + $('#txtorderquantity_'+i).val()+"'"+"&txtorderrate_" + z + "='" + $('#txtorderrate_'+i).val()+"'"+"&txtorderamount_" + z + "='" + $('#txtorderamount_'+i).val()+"'"+"&txtorderexcesscut_" + z + "='" + $('#txtorderexcesscut_'+i).val()+"'"+"&txtorderplancut_" + z + "='"
			 + $('#txtorderplancut_'+i).val()+"'"+"&cboComValueType_" + z + "='" + $('#cboComValueType_'+i).val()+"'"+"&txtComm_" + z + "='" + $('#txtComm_'+i).val()+"'"+"&txtLocalComm_" + z + "='" + $('#txtLocalComm_'+i).val()+"'"+"&txtForeignComm_" + z + "='" + $('#txtForeignComm_'+i).val()+"'"+"&txtFactoryPrice_" + z + "='" + $('#txtFactoryPrice_'+i).val()+"'"+"&txtFactoryValue_" + z + "='" + $('#txtFactoryValue_'+i).val()+"'"+"&cbostatus_" + z + "='" + $('#cbostatus_'+i).val()+"'"+"&hidbarcode_" + z + "='" + $('#hidbarcode_'+i).val()+"'";
			z++;
		}
		var is_po_levelqty_update=0;
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&order_id='+order_id+'&txt_avg_rate='+txt_tot_avg_rate+'&txt_total_amt='+txt_tot_amount+'&cbo_order_uom='+cbo_order_uom +'&tot_set_qnty='+tot_set_qnty+'&txt_avg_excess_cut='+txt_tot_excess_cut+'&txt_tot_plancut='+txt_tot_plancut+'&cbo_po_country='+cbo_po_country+'&hid_old_country='+hid_old_country+'&cbo_buyer_name='+cbo_buyer_name+'&hidd_order_status_id='+hidd_order_status_id+'&is_po_levelqty_update='+is_po_levelqty_update+'&cbo_company_name='+cbo_company_name+'&bom_sync_msg='+bom_sync_msg+'&cbo_working_company_id='+working_company_id+'&cbo_working_location_id='+working_location_id+'&cbo_status='+cbo_status+'&tot_smv_qnty='+tot_smv_qnty+get_submitted_data_string('txt_country_ship_date*txt_cutup_date*cbo_cut_up*txt_country_remarks*cbo_po_country_type*cbo_packing_country_level*txt_total_plan_cut*txt_ship_to*txt_product_value_price*txt_unit_lot',"../../../")+data_all;
		//'&txt_country_ship_date='+txt_country_ship_date+
		//alert(data);
		
		http.open("POST","bh_size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}

	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0]) ==12)
			{
				alert("Country Shipment Date Not Allowed");
				release_freezing();
				return;
			}
			if(reponse[0]==16)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityQty')
			{
				alert("Capacity Qty Over");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityValue')
			{
				alert("Capacity Value Over");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityMin')
			{
				alert("Order entry not allowed over Capacity SAH\nPlease check capacity balance SAH and input accordingly");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='WorkingCapacityMin') //For Working Company
			{
				// alert("Capacity Qty Over\n"+'Total Capacity Min='+reponse[2]);
				var bal_min = number_format(reponse[1]-reponse[2],2);
				alert('Total Capacity Min='+reponse[2]+'\n Capacity Min Over By= '+bal_min);
				release_freezing();
				return;
			}
			
			show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','bh_size_color_breakdown_controller','');
		    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','bh_size_color_breakdown_controller','');
			document.getElementById('cbo_po_country').value=0;
			document.getElementById('hid_old_country').value="";
			$("#cbo_po_country").attr("disabled",false);
			document.getElementById('txt_first_range').value="";
		    document.getElementById('txt_second_range').value="";
		    document.getElementById('txt_click_range').value="";
		    document.getElementById('txt_copy_color').value="";
			document.getElementById('txt_avg_price').value="";
			document.getElementById('txt_excess_cut').value="";
			var row_num=$('#size_color_break_down_list tr').length-1;
			for (var i=1; i<=row_num; i++)
		    {
				document.getElementById('hiddenid_'+i).value="";
			}
			set_button_status(0, permission, 'fnc_size_color_breakdown',1);
			if(reponse[0] !=2)
			{
				calculate_total_amnt( 1 )
			}

			if(reponse[0] ==2)
			{
				reset_form('colorsize_1','','');
				show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','bh_size_color_breakdown_controller','');
			}
			for (var i=1; i<=reponse[3]; i++)
		    {
				reset_form('size_color_break_down_list','','');
			}
			release_freezing();
			navigate_arrow_key();
		}
	}

	function add_break_down_tr( i,tr )
	{
		var row_num=$('#size_color_break_down_list tbody tr').length;
		if (i==0)
		{
			i=1;
			$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
			$("#txtsize_"+i).autocomplete({
				source:  str_size
			});
			return;
		}
		/*if (row_num!=i)
		{
			return false;
		}*/
		if (form_validation('cbogmtsitem_'+row_num+'*txtcolor_'+row_num+'*txtsize_'+row_num+'*txtorderquantity_'+row_num+'*txtorderrate_'+row_num,'Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
		{
			return;
		}
		if($('#txtorderrate_'+i).val()==0)
		{
			alert("Fill Up Rate");
			$('#txtorderrate_'+i).focus();
			return;
		}
		else
		{
			var j=i;
			//var tr_af=i;
			//var index=i-1;
			var index = $(tr).closest("tr").index();
			//alert(index)
			var i=row_num;
			i++;
			var tr=$("#size_color_break_down_list tbody tr:eq("+index+")");
			//alert(tr)
			var cl=$("#size_color_break_down_list tbody tr:eq("+index+")").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
					});
				}).end();
			tr.after(cl);

			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list',this);");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");

			$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");

			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			$('#checktr_'+i).removeAttr("onClick").attr("onClick","tr_check("+i+",event);");
			$('#txtsize_'+i).val('');
			// onBlur="(document.getElementById('txtorderquantity_1').value, this.value, 1)"
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtsize_'+i).removeAttr("disabled");
			//$('#txtorderexcesscut_'+i).removeAttr("disabled");
			//$('#txtcolor_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr("+j+",this);");
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val());
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
			$('#hiddenid_'+i).val("");
			$('#txtorderquantity_'+i).val("");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size
			});
			calculate_total_amnt( i )
			navigate_arrow_key();
			//re_order()
		}
	}

	function copyset_tr()
	{
		
		if(bomApproved==1) 
		{
			 alert("BOM Approved"); return;
		}
	   var rowNum=$('#size_color_break_down_list tr').length-1;
	   var checked=0;
		for (var k=1;k<=rowNum; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
				 
				if (form_validation('txt_copy_color','New Color')==false){
						return;
				}
				var txt_copy_color=(document.getElementById('txt_copy_color').value).toUpperCase();
				var txtcolor=(document.getElementById('txtcolor_'+k).value).toUpperCase();
				var cbogmtsitem=(document.getElementById('cbogmtsitem_'+k).value);
				var cbogmtsitem_copy=(document.getElementById('cbogmtsitem').value);
				if(txt_copy_color==txtcolor && cbogmtsitem_copy==cbogmtsitem)
				{
					//$("#size_color_break_down_list tr:eq("+i+")").css('background-color', 'Red');
					alert("Duplicate Item, Color and Size found")
					continue;
				}
				var row_num=$('#size_color_break_down_list tr').length-1;
				row_num+=1;
				$("#size_color_break_down_list tr:eq("+k+")").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						'name': function(_, name) { return name + row_num },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#size_color_break_down_list");
				$('#cbogmtsitem_'+row_num).removeAttr("onChange").attr("onChange","calculate_total_amnt("+row_num+");check_duplicate("+row_num+",this.id)");
				$('#txtcolor_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
				$('#txtsize_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
				$('#txtarticleno_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");


				$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_break_down_tr("+row_num+",this);");
				$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",'size_color_break_down_list',this);");
				$('#txtorderquantity_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+row_num+"').value,"+row_num+")");
				$('#txtorderquantity_'+k).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+k+")");

				$('#txtorderrate_'+row_num).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+row_num+")");
				$('#txtorderexcesscut_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+row_num+"').value,this.value,"+row_num+")");
				$('#checktr_'+row_num).removeAttr("onClick").attr("onClick","tr_check("+row_num+",event);");
				if(txt_copy_color !="")
				{
				$('#txtcolor_'+row_num).val(txt_copy_color);
				}
				$('#cbogmtsitem_'+row_num).val(cbogmtsitem_copy);
				$('#hiddenid_'+row_num).val("");
				$('#checktr_'+row_num).prop('checked', false);
				calculate_total_amnt( k )
				checked+=1;
				navigate_arrow_key();
			}
		}
		if(checked==0)
		{
			alert("Check row First")
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id,tr)
	{
		if(table_id=='size_color_break_down_list')
		{
			var numRow = $('table#size_color_break_down_list tbody tr').length;

			if(rowNo==1 && numRow >1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#hiddenid_'+rowNo).val();
				var po_id=document.getElementById('order_id').value;
		        var txt_job_no=document.getElementById('txt_job_no').value;
				//alert(po_id+"=="+txt_job_no);
				if(updateid !="" && permission_array[2]==1)
				{
					var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
					var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
					if(trim(booking_no_with_approvet_status_arr[0]) !=""){
						var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
						if(booking_no_with_approvet_status_arr[1] !=""){
							al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
						}
						al_magg+=" found,\nPlease Un-approved the booking first";
						alert(al_magg)
						return;
					}

					if(trim(booking_no_with_approvet_status_arr[1]) !=""){
						var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
						var r=confirm(al_magg);
						if(r==false){
							return;
						}
						else{
							//continue;
						}
					}
					var booking=return_global_ajax_value(updateid, 'delete_row_color_size', '', 'bh_size_color_breakdown_controller');
				}
				//var index=rowNo-1
				var index = $(tr).closest("tr").index();
				$("table#size_color_break_down_list tbody tr:eq("+index+")").remove()
				var numRow = $('table#size_color_break_down_list tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});
						$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
						$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

						$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
						$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list',this);");
						$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
						$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
						$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
						$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
					})
				}
				calculate_total_amnt( rowNo+1)
			}
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#hiddenid_'+rowNo).val();
				var po_id=document.getElementById('order_id').value;
		        var txt_job_no=document.getElementById('txt_job_no').value;
				//alert(po_id+"=="+txt_job_no);
				if(updateid !="" && permission_array[2]==1)
				{
					var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
					var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
					if(trim(booking_no_with_approvet_status_arr[0]) !=""){
						var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
						if(booking_no_with_approvet_status_arr[1] !=""){
							al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
						}
						al_magg+=" found,\nPlease Un-approved the booking first";
						alert(al_magg)
						return;
					}

					if(trim(booking_no_with_approvet_status_arr[1]) !=""){
						var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
						var r=confirm(al_magg);
						if(r==false){
							return;
						}
						else{
							//continue;
						}
					}
					var booking=return_global_ajax_value(updateid, 'delete_row_color_size', '', 'bh_size_color_breakdown_controller');
				}
				//var index=rowNo-1
				var index = $(tr).closest("tr").index();
				$("table#size_color_break_down_list tbody tr:eq("+index+")").remove()
				var numRow = $('table#size_color_break_down_list tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});
						$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
						$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

						$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
						$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list',this);");
						$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			            $('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
						$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
						$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
					})
				}
				calculate_total_amnt( rowNo-1 )
			}
		}
		navigate_arrow_key();
	}

	function re_order()
	{
		if(bomApproved==1) alert("BOM Approved"); return;
		var row_num=$('#size_color_break_down_list tr').length-1;
		for(i=0;i<=row_num;i++)
		{
		$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
			'value': function(_, value) { return value }
			});

			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list',this);");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size
			});
			})
		}
	}

	function populate_size_color_breakdown_with_data(data)
	{
		freeze_window(5);
		var data=data.split("_");
		

		document.getElementById('cbo_po_country').value=data[1];
		document.getElementById('hid_old_country').value=data[1];
		document.getElementById('txt_country_ship_date').value=data[2];
		document.getElementById('txt_cutup_date').value=data[3];
		document.getElementById('cbo_cut_up').value=data[4];
		document.getElementById('txt_country_remarks').value=data[5];
		document.getElementById('cbo_po_country_type').value=data[6];
		document.getElementById('cbo_packing_country_level').value=data[7];
		if(data[3] !="")
		{
			 $("#txt_country_ship_date").attr("disabled",true);
		}
		else
		{
			$("#txt_country_ship_date").attr("disabled",false);
		}
		document.getElementById('txt_first_range').value="";
		document.getElementById('txt_second_range').value="";
		document.getElementById('txt_click_range').value="";
		document.getElementById('txt_copy_color').value="";
		const elelment_exist = document.querySelector("#txt_ship_to");
		if(elelment_exist)
		{
			get_php_form_data(data[0], "populate_po_data_from_color_size_popup", "bh_size_color_breakdown_controller" );
		}
		
		show_list_view(data[0]+"_"+data[1],'populate_size_color_breakdown_with_data','data_form','bh_size_color_breakdown_controller','');
		set_button_status(1, permission, 'fnc_size_color_breakdown',1);
		calculate_total_amnt( 1 );
		release_freezing();
		navigate_arrow_key();
		var cutcountry=document.getElementById('cutcountry_1').value;
		if((cutcountry*1)>0){
			$('#cbo_po_country').attr('disabled','true');
			$('#cbo_po_country').attr('title','Cutting Qty Found')
		}else{
			$('#cbo_po_country').removeAttr('disabled');
			$('#cbo_po_country').removeAttr('title')
		}
		if(bomApproved==1) 
		{
			$('#cbogmtsitem').attr('disabled','true');
			$('#txt_copy_color').attr('disabled','true');
			$('#copyset1').attr('disabled','true');
			$('#txt_avg_price').attr('disabled','true');
			$('#copyset2').attr('disabled','true');
			$('#txt_excess_cut').attr('disabled','true');
			$('#copyset3').attr('disabled','true');
			$('#txt_artical_no').attr('disabled','true');
			$('#copyset4').attr('disabled','true');
			$('#cbo_po_country').attr('disabled','true');
			$('#cbo_po_country_type').attr('disabled','true');
			$('#txt_cutup_date').attr('disabled','true');
			$('#cbo_cut_up').attr('disabled','true');
			$('#txt_country_ship_date').attr('disabled','true');
			$('#txt_country_remarks').attr('disabled','true');
			$('#cbo_packing_country_level').attr('disabled','true');
		}
	}

	function check_country(country_id)
	{
		var po_id=document.getElementById('order_id').value;
		var  item_id=document.getElementById('cbogmtsitem_1').value;
		//alert(item_id);
		var country=return_global_ajax_value(po_id+"_"+country_id+"_"+item_id, 'check_country', '', 'bh_size_color_breakdown_controller');
		country=country.split("_");
		if(country[0]>0)
		{
			alert("This Country Data Already Inserted");
			document.getElementById('cbo_po_country').value=0;
			//else document.getElementById('cbo_po_country').value=0;
		}else{
			if(country[1]=="") country[1]=0;
			document.getElementById('cbo_cut_up').value=country[1];
			set_ship_date();
		}
	}

	/*function set_excess_cut( val, excs, id )
	{
		document.getElementById('txtorderplancut_'+id).value=(val*1)+((excs*val)/100);
		calculate_total_amnt(id);
	}*/

	function calculate_total_amnt( id )
	{
		//alert(id);
		//document.getElementById('txtorderamount_'+id).value=document.getElementById('txtorderrate_'+id).value*document.getElementById('txtorderquantity_'+id).value
		var order_amount  = document.getElementById('txtorderrate_'+id).value*document.getElementById('txtorderquantity_'+id).value;
		document.getElementById('txtorderamount_'+id).value= number_format_common(order_amount,3,0,2);
		var po_qnty=(document.getElementById('total').value)*1;
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		if(cbo_order_uom==58)
		{
			var set_breck_down=document.getElementById('set_breck_down').value;
			set_breck_down=set_breck_down.split("__");
			var item_id_value_array=new Array();
			for (var si=0;si<set_breck_down.length; si++)
			{
			var item_id_value=set_breck_down[si].split("_");
			item_id_value_array[item_id_value[0]]=item_id_value[1]
			}
			po_qnty=po_qnty*item_id_value_array[item_id];
			//document.getElementById('set_item_qnty_level').innerHTML="Set Item Qnty";
			//document.getElementById('set_item_qnty').innerHTML=item_id_value_array[item_id];
			//document.getElementById('qnty_eq_in_pcs_level').innerHTML="Qnty in Pcs";
			//document.getElementById('qnty_eq_in_pcs').innerHTML=po_qnty;
		}
		var row_num=$('#size_color_break_down_list tr').length-1;
		var tot=0;
		var item_tot=0;
		var avg_rate = 0;
		var tot_amount = 0;
		var avg_excess_cut = 0;
		var tot_plan_cut = 0;
		for (var k=1;k<=row_num; k++)
		{
			if(item_id==document.getElementById('cbogmtsitem_'+k).value)
			{
				item_tot=(item_tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			}
			tot=(tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			avg_rate=((avg_rate*1)+(document.getElementById('txtorderrate_'+k).value*1));
			tot_amount=(tot_amount*1)+(document.getElementById('txtorderamount_'+k).value*1);
			avg_excess_cut=((avg_excess_cut*1)+(document.getElementById('txtorderexcesscut_'+k).value*1))
			tot_plan_cut=(tot_plan_cut*1)+(document.getElementById('txtorderplancut_'+k).value*1);
		}
		avg_excess_cut=((tot_plan_cut-tot)/tot)*100;
		avg_rate=tot_amount/tot*1;
		$('#txt_total_order_qnty').val(tot);
		$('#txt_total_order_item_qnty').val(item_tot);
		$('#txt_total_order_item_yetto_qnty').val(po_qnty-item_tot);
		$('#txt_avg_rate').val(number_format_common(avg_rate, 3, 0,2));
		$('#txt_total_amt').val(tot_amount);
		$('#txt_avg_excess_cut').val(number_format_common(avg_excess_cut,6, 0,2));
		$('#txt_total_plan_cut').val(tot_plan_cut);
		//alert(hidd_po_poQty);
		console.log(`${item_tot} ==> ${with_qty} ==> ${po_qnty} ==> ${hidd_po_poQty}`)
		if (item_tot>po_qnty && with_qty==1 && hidd_po_poQty>0)
		{
			alert('Breakdown Quantity Over The Po Qnty Not Allowed.');
			document.getElementById('txtorderquantity_'+id).value="";
			document.getElementById('txtorderplancut_'+id).value="";
			document.getElementById('txtorderamount_'+id).value="";
			$('#txtorderquantity_'+id).focus();
			return;
		}
	}

	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var txtcolor=(document.getElementById('txtcolor_'+id).value).toUpperCase();
		var txtsize=(document.getElementById('txtsize_'+id).value).toUpperCase();
		var txtarticleno= (document.getElementById('txtarticleno_'+id).value).toUpperCase();
		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				//alert(item_id+"="+document.getElementById('cbogmtsitem_'+k).value);
				//alert(txtcolor+"="+document.getElementById('txtcolor_'+k).value);
				//alert(txtsize+"="+document.getElementById('txtsize_'+k).value);
				if(item_id==document.getElementById('cbogmtsitem_'+k).value && trim(txtcolor)==trim(document.getElementById('txtcolor_'+k).value.toUpperCase()) && trim(txtsize)==trim(document.getElementById('txtsize_'+k).value.toUpperCase()) && trim(txtarticleno)==trim(document.getElementById('txtarticleno_'+k).value.toUpperCase()))
				{
				alert("Same Gmts Item, Same Article Number,Same Color and Same Size Duplication Not Allowed.");
				document.getElementById(td).value="";
				document.getElementById(td).focus();
				}
			}
		}
	}

	function tr_index(tr)
	{
		var index_main=$(tr).index();
		var index=$(tr).index()+1;
		document.getElementById('txt_click_range').value=document.getElementById('txt_click_range').value*1+1;
		if(document.getElementById('txt_click_range').value==1)
		{
		document.getElementById('txt_first_range').value=index;
		$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
		//$(tr).css('background-color', 'Red');
		}
		else
		{
			var color_remove_in=document.getElementById('txt_second_range').value;
			//alert(color_remove_in)
			$("#size_color_break_down_list tr:eq("+color_remove_in+")").css('background-color', '');
			if(document.getElementById('txt_first_range').value<index)
			{
				document.getElementById('txt_second_range').value=index;
				$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
				//$(tr).css('background-color', 'Red');
			}
			else
			{
				document.getElementById('txt_second_range').value=document.getElementById('txt_first_range').value;
				document.getElementById('txt_first_range').value=index
				$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
			}
		}
	}

	function checkalltr_f(value)
	{
		if(bomApproved==1) { alert("BOM Approved"); $('#checkalltr').prop('checked', false); return; }
		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(value==1)
			{
				$('#checktr_'+k).prop('checked', true);
				document.getElementById('checkalltr').value=2
			}
			if(value==2)
			{
				$('#checktr_'+k).prop('checked', false);
				document.getElementById('checkalltr').value=1
			}
			//$('#checktr_'+k).click();
		}
		show_hide_button_holder()
	}

	function tr_check(i,e)
	{
		if (e.ctrlKey) {
		   var row_num=$('#size_color_break_down_list tr').length-1;
		   var checked=[];
		   var i=0;
			for (var k=1;k<=row_num; k++)
			{
				var is_checked=$("#checktr_"+k).is(':checked');
				if(is_checked)
				{
					checked[i]=k;
					i++;
				}
			}
			checked.sort(function(a, b){return b-a});
			var highest=checked[0];
			//alert(highest);
			checked.sort(function(a, b){return a-b});
			var lowest=checked[0];
			//alert(lowest);
			for (var j=lowest+1;j<=highest-1; j++)
			{
				$('#checktr_'+j).prop('checked', true);
			}
		}
		show_hide_button_holder();
	}

	function show_hide_button_holder()
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		var checked=0;
		for (var k=1;k<=row_num; k++)
		{
			if(checked==0)
			{
				var is_checked=$("#checktr_"+k).is(':checked');
				if(is_checked) checked=1; else checked=0
			}
		}
		if(checked==1)
		{
			$('#clear_button_holder').show();
		}
		if(checked==0)
		{
			$('#clear_button_holder').hide();
		}
	}

	function clear_color(type)
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		var row_num=$('#size_color_break_down_list tr').length-1;
		var checked=0;
		for (var k=1;k<=row_num; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
				$("#"+type+k).val('');
				checked+=1;
			}
		}
		if(checked==0)
		{
			alert("Check row First")
		}
	}

	function check_copy(val)
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		copied_table="";
		if (val==0)
		{
			$('#chk_copy').val(1);	// attr('checked',true);
			copied_table=$("#size_color_break_down_list tbody").html();
		}
		else
		$('#chk_copy').val(0); 	//attr('checked',false);
		//alert(copied_table);
	}

	function add_copied_po_breakdown()
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		$("#size_color_break_down_list tbody").html('');
		$("#size_color_break_down_list tbody").html(copied_table);
	}

	function color_select_popup(buyer_name,texbox_id)
	{
		//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
		//alert(texbox_id)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'bh_size_color_breakdown_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
			}
		}
	}

	function copy_avg_price()
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		//var permission_array=permission.split("_");
		//var updateid=$('#hiddenid_'+rowNo).val();
		var avg_price=document.getElementById('txt_avg_price').value;

		if(avg_price =="")
		{
			alert("Insert rate");
			return;
		}
		if(avg_price ==0)
		{
			alert("Insert rate");
			return;
		}

		var order_id=document.getElementById('order_id').value;
		/*var po_id=order_id;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
		var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
		if(trim(booking_no_with_approvet_status_arr[0]) !="")
		{
			var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
			if(booking_no_with_approvet_status_arr[1] !="")
			{
				al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
			}
			al_magg+=" found,\nPlease Un-approved the booking first";
			alert(al_magg)
			return;
		}

		if(trim(booking_no_with_approvet_status_arr[1]) !="")
		{
			var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
			var r=confirm(al_magg);
			if(r==false)
			{
				return;
			}
			else
			{
				//continue;
			}

		}*/

		var tot_row=return_global_ajax_value(order_id+'_'+avg_price+'_'+txt_job_no, 'update_avg_rate', '', 'bh_size_color_breakdown_controller');
		show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','bh_size_color_breakdown_controller','');
		show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','bh_size_color_breakdown_controller','');
		navigate_arrow_key();
		document.getElementById('cbo_po_country').value=0;
		document.getElementById('hid_old_country').value=0;
		alert("Total "+trim(tot_row)+" Rows Updated");
	}

	function copy_excess_cut()
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		//var permission_array=permission.split("_");
		//var updateid=$('#hiddenid_'+rowNo).val();
		var txt_excess_cut=document.getElementById('txt_excess_cut').value;

		if(txt_excess_cut =="")
		{
			alert("Insert rate");
			return;
		}
		if(txt_excess_cut ==0)
		{
			alert("Insert rate");
			return;
		}

		var order_id=document.getElementById('order_id').value;
		var po_id=order_id;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
		var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
		if(trim(booking_no_with_approvet_status_arr[0]) !="")
		{
			var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
			if(booking_no_with_approvet_status_arr[1] !="")
			{
				al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
			}
			al_magg+=" found,\nPlease Un-approved the booking first";
			alert(al_magg)
			return;
		}

		if(trim(booking_no_with_approvet_status_arr[1]) !="")
		{
			var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
			var r=confirm(al_magg);
			if(r==false)
			{
				return;
			}
			else
			{
				//continue;
			}

		}

		var tot_row=return_global_ajax_value(order_id+'_'+txt_excess_cut+'_'+txt_job_no, 'update_txt_excess_cut', '', 'bh_size_color_breakdown_controller');
		show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','bh_size_color_breakdown_controller','');
		show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','bh_size_color_breakdown_controller','');
		navigate_arrow_key();
		document.getElementById('cbo_po_country').value=0;
		document.getElementById('hid_old_country').value=0;
		alert("Total "+trim(tot_row)+" Rows Updated");
	}

	function copy_artical_no()
	{
		if(bomApproved==1) { alert("BOM Approved"); return; }
		var txt_artical_no=document.getElementById('txt_artical_no').value;

		if(txt_artical_no =="")
		{
			alert("Insert Artical No");
			return;
		}

		var order_id=document.getElementById('order_id').value;
		var po_id=order_id;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'order_entry_by_buying_house_controller')
		var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
		if(trim(booking_no_with_approvet_status_arr[0]) !="")
		{
			var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
			if(booking_no_with_approvet_status_arr[1] !="")
			{
				al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
			}
			al_magg+=" found,\nPlease Un-approved the booking first";
			alert(al_magg)
			return;
		}

		if(trim(booking_no_with_approvet_status_arr[1]) !="")
		{
			var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
			var r=confirm(al_magg);
			if(r==false)
			{
				return;
			}
			else
			{
				//continue;
			}
		}

		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			$("#txtarticleno_"+k).val( txt_artical_no );
		}
	}

	function vacant_form()
	{
		if(bomApproved==1) alert("BOM Approved"); return;
		document.getElementById('cbogmtsitem').value="";
		document.getElementById('txt_first_range').value="";
		document.getElementById('txt_second_range').value="";
		document.getElementById('txt_copy_color').value="";
	}

	function set_ship_date()
	{
		var txt_cutup_date=document.getElementById('txt_cutup_date').value;
		var cbo_cut_up=document.getElementById('cbo_cut_up').value;

		var po_id=document.getElementById('order_id').value;
		var country_id=$("#cbo_po_country").val();
		var cutt_off=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'bh_size_color_breakdown_controller');

		var is_cutt=cutt_off.split("_");
		if(txt_cutup_date=="")
		{
			alert("Insert Cutup Date");
			 $("#txt_country_ship_date").attr("disabled",false);
			return;
		}

		if(is_cutt[1]!=0 || is_cutt[1]!="")
		{
			if(cbo_cut_up==0)
			{
				alert("Select Cutup");
				$("#txt_country_ship_date").attr("disabled",false);
				return;
			}
			else
			{
				$("#txt_country_ship_date").attr("disabled",true);
			}
		}
		else
		{
			$("#txt_country_ship_date").attr("disabled",false);
			//return;
		}
		var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'bh_size_color_breakdown_controller');
		document.getElementById('txt_country_ship_date').value=set_ship_date;
    }

	function reorder_size_color()
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
	  	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'bh_size_color_breakdown_controller.php?action=reorder_size_color&txt_job_no='+txt_job_no, 'Color Size Ordering', 'width=600px,height=400px,center=1,resize=1,scrolling=0','../../')
	}

	function validate_po_qty_co_si(i)
	{
		var saved_po_quantity=$('#txtorderquantity_'+i).attr('saved_po_quantity');
		//alert(saved_po_quantity)
	    var txt_po_quantity=$('#txtorderquantity_'+i).val()*1;
		var hiddenid=document.getElementById('hiddenid_'+i).value;
		var txt_excess_cut=$('#txtorderexcesscut_'+i).val()*1;
		var po_id=document.getElementById('order_id').value;
		if(hiddenid>0 && hiddenid !=''){
		//var cutting_qty=return_global_ajax_value(hiddenid, 'get_cutting_qty', '', 'bh_size_color_breakdown_controller');
		var cutting_qty=$('#txtorderquantity_'+i).attr('production_quantity');
		}
		//alert(cutting_qty)
		var excess_cut_per=(1+(txt_excess_cut/100));
		var allowed_qty=cutting_qty/excess_cut_per;
		allowed_qty=Math.ceil(allowed_qty);

		if(txt_po_quantity<allowed_qty)
		{
			alert("Cutting Qty Found,You can update upto"+allowed_qty+" Qty");
			$('#txtorderquantity_'+i).val(saved_po_quantity);
			return;
		}
	}

	$(document).ready(function(){
	  navigate_arrow_key()
	});

	 new function ($) {
        $.fn.getCursorPosition = function () {
            var pos = 0;
            var el = $(this).get(0);
            // IE Support
            if (document.selection) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            // Firefox support
            else if (el.selectionStart || el.selectionStart == '0')
                pos = el.selectionStart;
            return pos;
        }
    } (jQuery);

	function navigate_arrow_key()
	{
		$('input').keyup(function(e){

			if( e.which==39 )
			{
				 if( $(this).getCursorPosition() == $(this).val().length )
				 	$(this).closest('td').next().find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
			}
			else if( e.which==37 )
			{
				if( $(this).getCursorPosition() == 0 )
					$(this).closest('td').prev().find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
			}
			else if( e.which==40 )
			{
				$(this).closest('tr').next().find('td:eq('+$(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
			}
			else if( e.which==38 )
			{
				$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
			}
		});
	}

	function fnc_checkbarcode( tid)
	{
		if(document.getElementById('checkbarcode_'+tid).checked==true)
		{
			if($('#hiddenid_'+tid).val()!="")
			{
				document.getElementById('checkbarcode_'+tid).value=1;
			}
			else
			{
				alert("Save First");
				$("#checkbarcode_"+tid).attr("checked", false);
				$('#checkbarcode_'+tid).val(2);
			}
		}
		else if(document.getElementById('checkbarcode_'+tid).checked==false)
		{
			document.getElementById('checkbarcode_'+tid).value=2;
		}
	}

	function fnc_barcode_generate()
	{
		var dataid="";
		var error=1;
		var po_id=document.getElementById('order_id').value;
		$("input[name=checkbarcode]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				if(dataid=="") dataid=$('#hiddenid_'+idd[1] ).val(); else dataid=dataid+","+$('#hiddenid_'+idd[1] ).val();
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		data=dataid+"***"+po_id;
		//alert(data);
		var url=return_ajax_request_value(data, "report_barcode_generation", "bh_size_color_breakdown_controller");
		//alert (url);
		window.open(url,"##");
	}

	<?
	$sql_temp=sql_select("SELECT percentage, upper_limit_qty, comapny_id, buyer_id, lower_limit_qty FROM lib_excess_cut_slab WHERE comapny_id='$cbo_company_name' and buyer_id='$cbo_buyer_name' and status_active=1 and is_deleted=0 order by comapny_id,buyer_id,lower_limit_qty asc");
	$i=0;
	foreach($sql_temp  as $row)
	{
		if( $exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=='') $i=0;
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['limit'][$i]=$row[csf("lower_limit_qty")]."__".$row[csf("upper_limit_qty")];
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['val'][$i]=$row[csf("percentage")];
		$exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=1;
		//echo $i."=";
		$i++;
	}
	unset($sql_temp);
	?>
	var exc_perc =<? echo json_encode($exc_perc); ?>;

	function excess_percentage( comp, buyer, qnty )
	{
		//var exc_perc=new Array();
		//alert (comp+'='+buyer+'='+qnty); return;

		//if( exc_perc[comp][buyer]!="undefined" )
		if(typeof(exc_perc[comp])!= 'undefined')
		{
			if(typeof(exc_perc[comp][buyer])!= 'undefined')
			{
				var newp=exc_perc[comp][buyer]["limit"];
				var newp= JSON.stringify(newp);
				var newstr=newp.split(",");
				for(var m=0; m< newstr.length; m++)
				{
					var limit=exc_perc[comp][buyer]["limit"][m].split("__");
					if((limit[1]*1)==0 && (qnty*1)>=(limit[0]*1))
					{
						return ( exc_perc[comp][buyer]["val"][m]*1);
					}
					if( (qnty*1)>=(limit[0]*1) && (qnty*1)<=(limit[1]*1) )
					{
						return exc_perc[comp][buyer]["val"][m];
					}
					// alert( newstr[m]+"=="+m)
				}
			}
		}
		return 0;
	}

	function set_excess_cut( val, excs, inc )
	{
		if ( val!="" || val!=0 )
		{
			var excut_fmLib =0;
			//var excs_cut=return_ajax_request_value(val+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_buyer_name').value, "get_excess_cut_percent", "requires/order_entry_by_buying_house_controller") ;
			if((excess_variable*1)==2 && excess_per_level==1)
			{
				var excut_fmLib = excess_percentage(cbo_company_name,cbo_buyer_name,val);

				document.getElementById('txtorderexcesscut_'+inc).value=excut_fmLib;
				var txt_plan_cut=(val*1)+((excut_fmLib*val*1)/100);
				document.getElementById('txtorderplancut_'+inc).value=number_format_common(txt_plan_cut, 6, 0);
				if(editable_id==1) //Slap// Yes
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',false);
				}
				else
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',true);
				}
			}
			else if((excess_variable*1)==2 && excess_per_level==2)
			{
				
				document.getElementById('txtorderexcesscut_'+inc).value=excs;
				var txt_plan_cut=(val*1)+((excs*val*1)/100);
				document.getElementById('txtorderplancut_'+inc).value=number_format_common(txt_plan_cut, 6, 0);
				if(editable_id==1) //Slap// Yes
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',false);
				}
				else
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',true);
				}
			}
			else if(excess_variable*1==3){
				document.getElementById('txtorderexcesscut_'+inc).value='';
				$('#txtorderexcesscut_'+inc).attr('disabled',true);
				document.getElementById('txtorderplancut_'+inc).value=val;
			}
			else{
				document.getElementById('txtorderexcesscut_'+inc).value=excs;
				$('#txtorderexcesscut_'+inc).attr('disabled',false);
				//document.getElementById('txtorderplancut_'+inc).value=val;
				document.getElementById('txtorderplancut_'+inc).value=(val*1)+((excs*val)/100);
			}
		}
		//}
		/*else
		{

			var txt_plan_cut=(val*1)+((excs*val)/100);
			document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
		}*/
		var isDisabledQty = document.getElementById('txtarticleno_'+inc).disabled;
		
		if(isDisabledQty==true) $('#txtorderexcesscut_'+inc).attr('disabled',true);

		calculate_total_amnt(inc);
		//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		//math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','', ddd );
	}
	// ES6 arrow function , here i is a parameter 
	const fn_addPriceRow = i => {
		var row_num=$('#tbl_product_price_list tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			$("#tbl_product_price_list tbody tr:last").clone().find("input,select").each(function(){

			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }
			});

			}).end().appendTo("#tbl_product_price_list");

			
			$('#txtpricevalue'+i).val('');
			$('#cbopricecurrency'+i).val(0);
			$("#tbl_product_price_list tbody tr:last").removeAttr('id').attr('id','trprice_'+i);
			
			$('#increasePrice_'+i).removeAttr("value").attr("value","+");
			$('#decreasePrice_'+i).removeAttr("value").attr("value","-");
			$('#increasePrice_'+i).removeAttr("onclick").attr("onclick","fn_addPriceRow("+i+");");
			$('#decreasePrice_'+i).removeAttr("onclick").attr("onclick","fn_deletePriceRow("+i+");");
		}
	}

	const fn_deletePriceRow = rowNo =>
	{
		var row_num=$('#tbl_product_price_list tbody tr').length;
		if(row_num!=1)
		{
			//alert(row_num);
			$("#trprice_"+rowNo).remove();
			var i = 1;
			$("#tbl_product_price_list tbody").find('tr').each(function()
			{
				$(this).removeAttr('id').attr('id','trprice_'+i);

				var tr_id = $(this).attr('id');
				console.log('tr => '+tr_id);

				$("#"+tr_id).find("input").each(function(){
					$(this).attr({
						'id': function(_, id) {var id=id.split("_"); return id[0] +"_"+ i }
					});
				});
				$("#"+tr_id).find("select").each(function(){
					$(this).attr({
						'id': function(_, id) {var id=id.split("_"); return id[0] +"_"+ i }
					});
				});
				$('#decreasePrice_'+i).removeAttr("value").attr("value","-");
				$('#increasePrice_'+i).removeAttr("onclick").attr("onclick","fn_addPriceRow("+i+");");
				$('#decreasePrice_'+i).removeAttr("onclick").attr("onclick","fn_deletePriceRow("+i+");");
				i++;
			});
		}
	}

	const bind_product_value = () =>
	{
		var save_data=''; 
		$("#tbl_product_price_list").find('tr').each(function()
		{
			var txtpricevalue=$(this).find('input[name="txtpricevalue[]"]').val();
			var cbopricecurrency=$(this).find('select[name="cbopricecurrency[]"]').val();
			if(txtpricevalue || cbopricecurrency)
			{
				if(save_data=="")
				{
					save_data=txtpricevalue+","+cbopricecurrency;
				}
				else
				{
					save_data+="_"+txtpricevalue+","+cbopricecurrency;
				}
			}
		});
		$('#txt_product_value_price').val( save_data );
		
	}

    </script>
    </head>
    <body onLoad="set_hotkey();">
        <div style="width:100%;" align="center">
            <!-- Important Field outside Form -->
            <input type="hidden" id="garments_nature" value="2">
            <!-- End Important Field outside Form -->
            <div style="display:none"><?=load_freeze_divs ("../../../",$permission);  ?></div>
            <fieldset style="width:950px; display:none">
                <legend>Color & Size Breakdown Entry</legend>
                <form name="sizecolormaster_1" id="sizecolormaster_1" autocomplete="off">
                    <table  width="950" cellspacing="2" cellpadding="0" border="0">
                        <tr style="display:none">
                            <td width="130" align="right">&nbsp;</td>
                            <td width="170">&nbsp;</td>
                            <td width="130" align="right">Order No</td>
                            <td width="170">
                            	<input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/bh_size_color_breakdown_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Order No" name="txt_order_no" id="txt_order_no" readonly />
                            	<input type="hidden" id="order_id" name="order_id"  value="<? echo $data;?>" readonly />
								<input type="hidden" id="hidd_job_id" name="hidd_job_id"  value="<? echo $hidd_job_id;?>" readonly />
                                <input type="text" id="hidd_order_status_id" name="hidd_order_status_id" value="<? echo $cbo_order_status;?>" readonly />
                               
                            </td>
                            <td width="130" align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr style="display:none">
                            <td width="130" align="right">Job No</td>
                            <td width="170">
                            	<input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/bh_size_color_breakdown_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled />
                            </td>
                            <td width="130" align="right">Company Name </td>
                            <td width="170"><? echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",1 ); ?>
                            </td>
                            <td width="130" align="right">Location Name</td>
                            <td id="location"><? echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "",1 );
                            ?>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td align="right">Buyer Name</td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "" ,1); ?>
                            </td>
                            <td align="right">Style Ref.</td>
                            <td><input class="text_boxes" type="text" style="width:160px" disabled placeholder="Click for Quotation" name="txt_style_ref" id="txt_style_ref"/></td>
                            <td align="right">Style Description</td>
                            <td><input class="text_boxes" type="text" style="width:160px;" disabled name="txt_style_description" id="txt_style_description"/></td>
                        </tr>
                        <tr style="display:none">
                            <td align="right">Pord. Dept.</td>
                            <td><? echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,1); ?></td>
                            <td align="right">Currency</td>
                            <td><? echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "-- Select Currency--", 2, "",1 ); ?></td>
                            <td align="right">Agent </td>
                            <td id="agent_td"><? echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "",1 ); ?></td>
                        </tr>
                        <tr style="display:none">
                            <td align="right">Region</td>
                            <td><? echo create_drop_down( "cbo_region", 172, $region, "",1, "-- Select Region --", $selected, "",1 ); ?></td>
                            <td align="right">Team Leader</td>
                            <td><? echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1 ); ?></td>
                            <td align="right">Dealing Merchant</td>
                            <td><? echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 ); ?>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td align="right">Shipment Date</td>
                            <td><input class="datepicker" type="text" style="width:160px;"  name="txt_ship_date" id="txt_ship_date" disabled/></td>
                            <td align="right">Po Qnty</td>
                            <td><input class="text_boxes" type="text" style="width:100px;"  name="txt_po_qnty" id="txt_po_qnty" disabled/>
                            	<? echo create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" ); ?>
                            </td>
                            <td align="right">Plan Cut Qnty</td>
                            <td><input class="text_boxes" type="text" style="width:100px;"  name="txt_plan_cut_qnty" id="txt_plan_cut_qnty" disabled/>
                            	<? echo create_drop_down( "cbo_order_uom_2",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" ); ?>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td align="center" height="20" colspan="6" class="image_uploaders">
                                <input type="hidden" id="update_id">
                                <input type="hidden" id="set_breck_down" />
                                <input type="hidden" id="item_id" />
                                <input type="hidden" id="tot_set_qnty" />
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>

			<?
			$buyer_name=$cbo_buyer_name;
			$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
			if($color_from_library==1)
			{
				$readonly="readonly='readonly'"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup($buyer_name,this.id)'";
			}
			else
			{
				$readonly=""; $plachoder=""; $onClick="";
			}

			$sql_slap=sql_select("select excut_source,editable from variable_order_tracking where company_name=$cbo_company_name and variable_list=45 and is_deleted=0 and status_active=1");
			if(count($sql_slap>0))
			{
				foreach($sql_slap as $row )
				{
					$excess_variable=$row[csf('excut_source')];

					if($excess_variable==2) //slap
					{
						$editable_id=$row[csf('editable')];
					}
					else $editable_id=1;
					if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
				}
				//echo $editable_id.'=='.$excess_variable;
				if($editable_id==1) //Slap//Yes
				{
					$excess_fld_disable="";
				}
				else if($editable_id==2 || $editable_id==0) //Slap//No
				{
					$excess_fld_disable="disabled";
				}
				else $excess_fld_disable="disabled";
				//echo $excess_fld_disable.'DD';
			}

			?>
            <fieldset style="width:1080px">
            <legend>Copy Panel</legend>
                <table>
                    <tr>
                        <td>New Item: </td>
                        <td><? echo create_drop_down( "cbogmtsitem", 150, $garments_item,"", 0, "","", "","",$item_id); ?>
                            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_first_range" id="txt_first_range"/>
                            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_second_range" id="txt_second_range"/>
                            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_click_range" id="txt_click_range" value="0"/>
                            <input class="text_boxes" type="hidden" style="width:100px;"  name="checked_value" id="checked_value" value=""/>
                        </td>
                        <td>New Color:</td>
                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_copy_color" id="txt_copy_color" <? echo $onClick." ".$readonly." ".$plachoder; ?>/></td>
                        <td>Copy Rate</td>
                        <td><input name="txt_avg_price" id="txt_avg_price" class="text_boxes_numeric" type="text" value="" style="width:100px" /></td>
                        <td align="right">Copy Excess Cut %</td>
                        <td><input name="txt_excess_cut" id="txt_excess_cut"  class="text_boxes_numeric" type="text" style="width:100px"  <? echo $excess_fld_disable; ?> /></td>
                        <td align="right">Copy Article Number</td>
                        <td><input name="txt_artical_no" id="txt_artical_no"  class="text_boxes" type="text" style="width:100px"  <? echo $excess_fld_disable; ?> /></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                            <input type="reset" value="Reset Range" class="formbutton"  onClick="vacant_form()"/>
                            <input type="button" id="copyset1" style="width:50px" class="formbutton" value="Copy" onClick="copyset_tr()" />
                        </td>
                        <td colspan="2" align="center">
                        	<input type="button" id="copyset2" style="width:100px" class="formbutton" value="Copy Rate" onClick="copy_avg_price()" />
                        </td>
                        <td colspan="2" align="center">
                        	<input type="button" id="copyset3" style="width:100px" class="formbutton" value="Copy Excess Cut" onClick="copy_excess_cut()" />
                        </td>
                        <td colspan="2" align="center">
                        	<input type="button" id="copyset4" style="width:100px" class="formbutton" value="Copy Article No" onClick="copy_artical_no()" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br/>
            <fieldset style="width:1180px">
            <legend>Input Panel</legend>
                <table>
                    <tr>
                        <td align="right">Country</td>
                        <td><?=create_drop_down("cbo_po_country", 150, "select id,country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "Select", "", "check_country(this.value);"); ?>
                        	<input type="hidden" id="hid_old_country" />
                        </td>
                        <td align="right">Country Type</td>
                        <td><?=create_drop_down("cbo_po_country_type", 80, $country_type, "", 0, "", "", ""); ?></td>
                        <td align="right">Cut-off Date</td>
                        <td><input class="datepicker" type="text" style="width:60px;"  name="txt_cutup_date" id="txt_cutup_date" onChange="set_ship_date();" value="<?=$txt_org_shipment_date; ?>"/></td>
                        <td align="right">Cutoff</td>
                        <td><?=create_drop_down( "cbo_cut_up",90, $cut_up_array, "",1, "Select", "", "set_ship_date()","","" ); ?></td>
                        <td align="right">Country Shipment Date</td>
                        <td><input class="datepicker" type="text" style="width:60px;" name="txt_country_ship_date" id="txt_country_ship_date"  value="<?=$txt_pub_shipment_date; ?>"/></td>
                        <td align="right">Remarks</td>
                        <td><input class="text_boxes" type="text" style="width:140px;" name="txt_country_remarks" id="txt_country_remarks" /></td>
                        <td align="right">Packing </td>
                        <td>
							<?=create_drop_down( "cbo_packing_country_level", 90, $packing,"", 1, "--Select--", $cbo_packing_po_level, "","","" ); ?>
                            <input class="text_boxes" type="hidden" style="width:40px;"  name="with_qty" id="with_qty" value="<?=$with_qty; ?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Ship To/Distance</td>
                    	<td ><input class="text_boxes" type="text" style="width:120px;"  name="txt_ship_to" id="txt_ship_to"  value="<?=$txt_ship_to; ?>"/></td>
                    	<td class="must_entry_caption">Unit Lot</td>
                    	<td ><input class="text_boxes" type="text" style="width:120px;"  name="txt_unit_lot" id="txt_unit_lot"  value="<?=$txt_unit_lot; ?>"/></td>
                    	
                    	<td colspan="5">
                    		
                    		<table class="rpt_table" cellspacing="0" cellpadding="0" id="tbl_product_price_list">
                    			<caption>Product Value Price</caption>
                    			<thead>
                    				<tr>
                    					<th class="must_entry_caption">Price</th>
                    					<th class="must_entry_caption">Curency</th>
                    					<th width="140">Action
                    						<input class="text_boxes" type="hidden" style="width:120px;"  name="txt_product_value_price" id="txt_product_value_price"  value="<?=$txt_product_value_price; ?>"/>
                    					</th>
                    				</tr>
                    				<? $price_sl = 1;
                    				$txt_product_value_price = str_replace("'","",$txt_product_value_price) ;
				                	if(empty($txt_product_value_price))
				                	{
				                		$txt_product_value_price = return_field_value("PRODUCT_VALUE_PRICE as PRODUCT_VALUE_PRICE","bh_wo_po_break_down"," id =$data","PRODUCT_VALUE_PRICE");
				                	}
                    				$product_value_arr = explode("_",$txt_product_value_price);

                    				?>
                    			</thead>
                    			<tbody>
                    				<?php foreach ($product_value_arr as $product_val): 
                    					$prod_exp = explode(",",$product_val);

                    				?>
	                    				<tr id="trprice_<? echo $price_sl;?>">
	                    					<td>
					                       		 <input type="text" name="txtpricevalue[]" id="txtpricevalue_<? echo $price_sl;?>" class="text_boxes" style="width:70px;"  value="<? echo $prod_exp[0];?>"  placeholder="Write" onKeyup="bind_product_value()" />
					                        </td>
					                        <td>
					                       		 
					                       		 <?=create_drop_down( "cbopricecurrency_".$price_sl, 80, $currency,"", "1", "Select",$prod_exp[1], "bind_product_value()","","","","","","","","cbopricecurrency[]" ); ?>
										
					                        </td>
					                        <td>
					                        	<input type="button" id="increasePrice_<? echo $price_sl;?>" name="increasePrice[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addPriceRow(<? echo $price_sl;?>)" />
					                       		 <input type="button" id="decreasePrice_<? echo $price_sl;?>" name="decreasePrice[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletePriceRow(<? echo $price_sl;?>);"/>
					                        </td>
	                    				</tr>

                    				<?php 
                    					$price_sl++;
                    				 	endforeach
                    				?>
                    				
                    			</tbody>
                    		</table>
                    	</td>
                    	

                    </tr>
                </table>
            </fieldset>
            <br/>
            <div id="size_color_breakdown"></div>
        </div>
    </body>
    <script>
		get_php_form_data(<?=$data; ?>, "populate_data_from_search_popup", "bh_size_color_breakdown_controller" );
		show_list_view('<?=$data; ?>','populate_size_color_breakdown','size_color_breakdown','bh_size_color_breakdown_controller','');
		$( "#txtsize_1").autocomplete({
                source: function( request, response ) {
                    var matcher =  new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( str_size, function( item ){
                        return matcher.test( item );
                    }) );
                }
            });
		navigate_arrow_key();
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="get_cutting_qty")
{
	$production_quantity=0;
	$sql_data=sql_select( "select color_size_break_down_id,sum(production_qnty) as production_qnty from  pro_garments_production_dtls where color_size_break_down_id='$data' and production_type=1 and  status_active=1 and is_deleted=0 group by color_size_break_down_id");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_qnty')]>0)
		{
		$production_quantity=$row_data[csf('production_qnty')];
		}
	}
	echo trim($production_quantity);
}

if($action=="get_cutting_qty_country")
{
	$data=explode("_",$data);
	$production_quantity=0;
	$sql_data=sql_select( "select count(id) as id from  pro_garments_production_mst where po_break_down_id='$data[0]' and country_id='$data[1]' and  status_active=1 and is_deleted=0");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('id')]>0)
		{
		$production_quantity=$row_data[csf('id')];
		}
	}
	echo trim($production_quantity);
}

if($action=="reorder_size_color")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode,1,'');
	extract($_REQUEST);
?>
	<script>
	var permission='<? echo $permission; ?>';

	function fnc_size_color_reorder(operation)
	{
		var row_num_color=$('#color_order tbody tr').length;
		var data_all_color="";
		for (var i=1; i<=row_num_color; i++)
		{
			if (form_validation('colorordering_'+i,'Color Ordering')==false)
			{
				return;
			}
			data_all_color=data_all_color+get_submitted_data_string('txt_job_no*colorid_'+i+'*colorordering_'+i,"../../../",i);
		}

		var row_num_size=$('#size_order tbody tr').length;
		var data_all_size="";
		for (var i=1; i<=row_num_size; i++)
		{
			if (form_validation('sizeordering_'+i,'Size Ordering')==false)
			{
				return;
			}
			data_all_size=data_all_size+get_submitted_data_string('txt_job_no*sizeid_'+i+'*sizeordering_'+i,"../../../",i);
		}

		var data="action=save_update_color_size_ordering&operation="+operation+'&total_row_color='+row_num_color+data_all_color+'&total_row_size='+row_num_size+data_all_size;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","bh_size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_color_reorder_reponse;
	}

	function fnc_size_color_reorder_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			release_freezing();
		}
	}
    </script>

    </head>
    <body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <!-- Important Field outside Form -->
        <input type="hidden" id="garments_nature" value="2">
        <!-- End Important Field outside Form -->
        <?=load_freeze_divs ("../../../",$permission);  ?>
        <fieldset style="width:500px;">
            <form id="colorsizeorder_1">
            <input type="hidden" class="text_boxes_numeric" id="txt_job_no" value="<? echo $txt_job_no; ?>" style="widows:60px"/>
                <table>
                    <tr>
                        <td valign="top">
                            <table id="color_order" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="150">Color</th>
                                        <th>Color Ordering</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? $sql_data=sql_select("select min(id) as id, color_number_id,min(color_order) as color_order from bh_wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by color_number_id order by color_order ASC ");
                                    $i=1;
                                    foreach($sql_data as $sql_row)
                                    {
										?>
										<tr>
											<td><?=$i; ?></td>
											<td><?=$color_library[$sql_row[csf('color_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="colorid_<?=$i; ?>" value="<?=$sql_row[csf('color_number_id')]; ?>" style="widows:60px"/></td>
											<td><input type="text" class="text_boxes_numeric" id="colorordering_<?=$i; ?>" style="widows:60px" value="<?=$sql_row[csf('color_order')];  ?>"/></td>
										</tr>
										<?
										$i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                        <td valign="top">
                            <table id="size_order" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="150">Size</th>
                                        <th>Size Ordering</th>
                                    </tr>
                                </thead>
                                <tbody>
									<? $sql_data=sql_select("select min(id) as id, size_number_id,min(size_order) as size_order from bh_wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by size_number_id order by size_order ASC ");
                                    $i=1;
                                    foreach($sql_data as $sql_row)
                                    {
										?>
										<tr>
                                            <td><?=$i; ?></td>
                                            <td><?=$size_library[$sql_row[csf('size_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="sizeid_<?=$i; ?>" value="<?=$sql_row[csf('size_number_id')]; ?>" style="widows:60px"/></td>
                                            <td><input type="text" class="text_boxes_numeric" id="sizeordering_<?=$i; ?>" style="widows:60px" value="<?=$sql_row[csf('size_order')]; ?>"/></td>

										</tr>
										<?
										$i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12"  class="button_container">
                        	<?=load_submit_buttons( $permission, "fnc_size_color_reorder", 1,0 ,"",1) ; ?>
                    	</td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="save_update_color_size_ordering")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	/*
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row_color;$i++)
		{
			$colorid="colorid_".$i;
			$colorordering="colorordering_".$i;
			$rID=execute_query( "update bh_wo_po_color_size_breakdown set color_order=".$$colorordering." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
		}

		for ($i=1;$i<=$total_row_size;$i++)
		{
			$sizeid="sizeid_".$i;
			$sizeordering="sizeordering_".$i;
			$rID=execute_query( "update bh_wo_po_color_size_breakdown set  size_order=".$$sizeordering."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
		}
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}

	*/
	$new_array_color_ordering=[];
	$new_array_size_ordering=[];
	$total_row_color=str_replace("'", "", $total_row_color);
	$total_row_size=str_replace("'", "", $total_row_size);
	$pre_cost=return_field_value("id","wo_pre_cost_mst","status_active=1 and is_deleted=0 and job_no=$txt_job_no","id");
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$flag=1;
		 for ($i=1;$i<=$total_row_color;$i++)
		 {
			 $colorid="colorid_".$i;
			 $colorordering="colorordering_".$i;
			 $change_color="change_color_".$i;

			 if(str_replace("'","",$$change_color)!="")
			 {
				if (!in_array(str_replace("'","",$$change_color),$new_array_color_ordering))
				{
					$color_id = return_id( str_replace("'","",$$change_color), $color_library, "lib_color", "id,color_name","401");
					$new_array_color_ordering[$color_id]=str_replace("'","",$$change_color);
				}
				else $color_id =  array_search(str_replace("'","",$$change_color), $new_array_color_ordering);
			}
			else $color_id=0;
			$rID=execute_query( "update bh_wo_po_color_size_breakdown set color_order=".$$colorordering." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
			if(!$rID) {
				$flag=0;
				break;
			}
			$rID1=true;
			if($color_id!=str_replace("'", "", $$colorid) && !empty($color_id))
			{
				if(!empty($pre_cost))
				{
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$colorid);
						die;
					}
					else if($db_type==2 || $db_type==1 )
					{
						oci_rollback($con);
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$colorid);
						die;
					}
	
				}
				$rID1=execute_query( "update bh_wo_po_color_size_breakdown set COLOR_NUMBER_ID=".$color_id.",COLOR_NUMBER_ID_PREV=".$$colorid." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
				if(!$rID1) {
					$flag=0;
					break;
				}
			}

			

			 
		 }

		 for ($i=1;$i<=$total_row_size;$i++)
		 {
			 $sizeid="sizeid_".$i;
			 $sizeordering="sizeordering_".$i;
			 $change_size="change_size_".$i;
			 $rID2=execute_query( "update bh_wo_po_color_size_breakdown set  size_order=".$$sizeordering."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
			 if(!$rID2) {
					$flag=0;
					break;
			 }

			if(str_replace("'","",$$change_size)!="")
			{
				if (!in_array(str_replace("'","",$$change_size),$new_array_size_ordering, TRUE))
				{
					$size_id_val = return_id( str_replace("'","",$$change_size), $size_library, "lib_size", "id,size_name","401");
					$new_array_size_ordering[$size_id_val]=str_replace("'","",$$change_size);
				}
				else $size_id_val =  array_search(str_replace("'","",$$change_size), $new_array_size_ordering);
			}
			else $size_id_val=0;
			$rID3=true;
			if($size_id_val!=str_replace("'", "", $$sizeid) && !empty($size_id_val))
			{
				if(!empty($pre_cost))
				{
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$sizeid);
						die;
					}
					else if($db_type==2 || $db_type==1 )
					{
						oci_rollback($con);
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$sizeid);
						die;
					}
	
				}
				$rID3=execute_query( "update bh_wo_po_color_size_breakdown set  size_number_id=".$size_id_val.",size_number_id_prev=".$$sizeid."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
				if(!$rID3) {
					$flag=0;
					break;
			 	}
			}
		 }
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $flag)
			{
				mysql_query("COMMIT");
				echo "0**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3 && $flag)
			{
				oci_commit($con);
				echo "0**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="set_ship_date")
{
	$data=explode("_",$data);
	$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1)
	{
		echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	}
	if($data[1]==2)
	{
		echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	}
	if($data[1]==3)
	{
		echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	}
	exit();
}

if($action=="update_avg_rate")
{
	$i=0;
	$data=explode("_",$data);
	$data_array=sql_select("select id,order_quantity from bh_wo_po_color_size_breakdown where po_break_down_id=$data[0] and is_deleted=0 and status_active=1");
	foreach($data_array as $row)
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$order_total=$data[1]*$row[csf('order_quantity')];
		$rID=execute_query( "update  bh_wo_po_color_size_breakdown set order_rate=$data[1],order_total=$order_total  where  po_break_down_id =$data[0] and id=".$row[csf('id')]."",1);
		if($db_type==0)
		{
			if($rID ){
			mysql_query("COMMIT");
			//echo "2";
			}
			else{
			mysql_query("ROLLBACK");
			//echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
			oci_commit($con);
			//echo "2";
			}
		else{
			oci_rollback($con);
			//echo "10";
			}
		}
		disconnect($con);
		$i++;
	}
	echo $i;
	disconnect($con);die;
}

if($action=="update_txt_excess_cut")
{
	$i=0;
	$data=explode("_",$data);
	$data_array=sql_select("select id,order_quantity from bh_wo_po_color_size_breakdown where po_break_down_id=$data[0] and is_deleted=0 and status_active=1");
	foreach($data_array as $row)
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$excess_cut_perc=($data[1]*$row[csf('order_quantity')]/100)+$row[csf('order_quantity')];
		$rID=execute_query( "update  bh_wo_po_color_size_breakdown set excess_cut_perc=$data[1],plan_cut_qnty=$excess_cut_perc  where  po_break_down_id =$data[0] and id=".$row[csf('id')]."",1);
		$rID1=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no ='".$data[2]."' and booking_type=1 and is_short=2 ",1);

		if($db_type==0)
		{
			if($rID && $rID1){
			mysql_query("COMMIT");
			//echo "2";
			}
			else{
			mysql_query("ROLLBACK");
			//echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
			oci_commit($con);
			//echo "2";
			}
			else{
			oci_rollback($con);
			//echo "10";
			}
		}
		disconnect($con);
		$i++;
	}
	echo $i;
	 disconnect($con);die;
}

if($action=="color_popup")
{
	echo load_html_head_contents("Color Select PopUP","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            if($buyer_name=="" || $buyer_name=="")
            {
            	$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
            }
            else
            {
            	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0";
            }
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if (file_exists('dateretriction_color_size.php'))
	{
		require('dateretriction_color_size.php');
	}
	$btn_mood=1;
	//echo "10**";
	$sql= sql_select("select id from bh_wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and is_deleted=0 and status_active=1 ");
	if(count($sql)>0) $btn_mood=2; else $btn_mood=1;
	//echo "select id from bh_wo_po_color_size_breakdown where po_break_down_id='$txt_job_no' and is_deleted=0 and status_active=1 ";
	//echo $btn_mood;
	//die;
	$cu_country_id=str_replace("'","",$cbo_po_country);
	
	


	
	 
	
	$sql=sql_select("select variable_list, copy_quotation, cost_control_source, excut_source, buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name='$cbo_company_name' and variable_list in (20,45,52,53,78, 65) order by id");
	$cost_control_source=0; $is_copy_quatation=2; $poEntryControlWithBomApproval=2; $excess_variable=0; $buyer_allocation_maintain=2; $capacity_exceed_level=0; $excess_per_level=0;
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==20) $is_copy_quatation=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==45) $excess_variable=$vrow[csf('excut_source')];
		if($vrow[csf('variable_list')]==52) 
		{	
			$buyer_allocation_maintain=$vrow[csf('buyer_allocation_maintain')];
			$capacity_exceed_level=$vrow[csf('capacity_exceed_level')];
		}
		if($vrow[csf('variable_list')]==53) $cost_control_source=$vrow[csf('cost_control_source')];
		if($vrow[csf('variable_list')]==78) $poEntryControlWithBomApproval=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==65) $excess_per_level=$vrow[csf('excut_source')];
	}
	

	
	//==============================capacity Validation ==============================

	if(str_replace("'","",$cbo_status)==1)
	{	
		$update_id_details=str_replace("'","",$order_id);
		$country_id=str_replace("'","",$hid_old_country);
		if($update_id_details!='') $po_id_con="and a.id !=$update_id_details";
		else $po_id_con="";
		
		//echo "16**".$po_id_con; die;
					
		$capaBuyerCond=""; $poBuyerCond="";
		if($buyer_allocation_maintain==1){
			$capaBuyerCond="and a.buyer_id=$cbo_buyer_name";
			$poBuyerCond="and b.buyer_name=$cbo_buyer_name";
		}else{
			$capaBuyerCond=""; $poBuyerCond="";
		}
		//==============================capacity Validation For Working Company==============================
		$year=date("Y",strtotime(str_replace("'","",$txt_country_ship_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$txt_country_ship_date)));
		$lc_company_id=str_replace("'","",$cbo_company_name);
		$w_company_id=str_replace("'","",$cbo_working_company_id);
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		
		$w_buyer_allocation_maintain=2; $w_capacity_exceed_level=0;
		$w_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_working_company_id and variable_list=52");
		foreach($w_capa_vari as $row_capa_vari){
			$w_buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
			$w_capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
		}
		$w_capaBuyerCond=""; $w_poBuyerCond="";
		if($w_buyer_allocation_maintain==1){
			if($lc_company_id==$w_company_id)
			{
				//$w_capaBuyerCond="and b.buyer_id=$cbo_buyer_name";
				//$w_poBuyerCond="and b.buyer_name=$cbo_buyer_name";
			}
			else
			{
				$w_capaBuyerCond="";
				//$w_poBuyerCond="";
			}
		}else{
			$w_capaBuyerCond=""; $w_poBuyerCond="";
		}
		//End
	
		$year_month_name=$month.",".$year;
	 
		
		 
		$sql_con_capa="SELECT   sum(d.capacity_min) as capacity_min FROM  lib_capacity_calc_mst c,  lib_capacity_calc_dtls d
		WHERE c.id=d.mst_id AND c.comapny_id=$cbo_working_company_id AND c.year=$year and d.month_id=$month and  d.capacity_min>0 and c.status_active=1 and c.is_deleted=0 and c.location_id = $cbo_working_location_id";
		//echo "16**".$sql_con_capa;die;
	 	$con_capa_result=sql_select($sql_con_capa);
		foreach($con_capa_result as $row)
		{
			$tot_company_capacity_min=$row[csf('capacity_min')];
		}
		//echo "10**".__LINE__.'allow'; die;
		if($capacity_exceed_level==12){ //Working Company
			$pub_shipment_date=str_replace("'","",$txt_country_ship_date);
			if($db_type==2)
			{
				$date_from=change_date_format($pub_shipment_date,'','',1);
				$date_to=change_date_format($pub_shipment_date,'','',1);
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$second_month_ldate=date("Y-M-t",strtotime($date_to));
			    $ship_last_day=change_date_format($second_month_ldate,'','',1);
				$pub_date_upto="and  c.country_ship_date BETWEEN '$fromdate' AND '$ship_last_day'";
			}
			else
			{
				$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$second_month_ldate=date("Y-m-t",strtotime($date_to));
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
				$pub_date_upto=" and  c.country_ship_date BETWEEN '$fromdate' AND '$ship_last_day' ";
			}	
		if(str_replace("'","",$is_po_levelqty_update)!=0){
			$w_sql_po=sql_select("SELECT sum(c.plan_cut_qnty) as po_quantity, b.set_smv from bh_wo_po_break_down a join bh_wo_po_details_master b on a.job_id=b.id join bh_wo_po_color_size_breakdown c on a.id=c.po_break_down_id where b.style_owner=$cbo_working_company_id $w_poBuyerCond $allocat_buyer_cond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_break_down_id!=$update_id_details and c.country_id!=$country_id and b.working_location_id = $cbo_working_location_id $pub_date_upto group by b.set_smv");

		}
		else{
			$w_sql_po=sql_select("SELECT sum(c.plan_cut_qnty) as po_quantity, b.set_smv from bh_wo_po_break_down a join bh_wo_po_details_master b on a.job_id=b.id join bh_wo_po_color_size_breakdown c on a.id=c.po_break_down_id where b.style_owner=$cbo_working_company_id $w_poBuyerCond $allocat_buyer_cond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.working_location_id = $cbo_working_location_id $pub_date_upto group by b.set_smv");
		}
		$w_tot_prev_po_qty=0;
		foreach($w_sql_po as $row_po){
			$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
				//$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
			if($w_buyer_allocation_maintain==1)//Yes
			{
				if($lc_company_id==$w_company_id)
				{
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
					
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
				}
				else
				{
					$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
			}
			else
			{
				$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
			}
			
		}
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		if($w_buyer_allocation_maintain==1)//Yes
		{
			$tot_buyer_capacity_min=$tot_company_capacity_min;
			$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
			$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
			
			if($lc_company_id==$w_company_id)
			{
				if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
				{
					$buyer_allocate_percent=$buyer_remain_allocate_percent;
					$total_company_capacity_min=$tot_buyer_capacity_min-$w_tot_prev_po_qty;
					$tota_country_wise_po_qty=str_replace("'","",$txt_total_plan_cut)+str_replace("'","",$txt_tot_plancut);
					$w_curr_po_qty=$tota_country_wise_po_qty*str_replace("'","",$tot_smv_qnty);
					//$w_curr_po_qty=str_replace("'","",$txt_total_plan_cut)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				else
				{
					$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
					$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
					$tota_country_wise_po_qty=str_replace("'","",$txt_total_plan_cut)+str_replace("'","",$txt_tot_plancut);
					$w_curr_po_qty=$tota_country_wise_po_qty*str_replace("'","",$tot_smv_qnty);
					//$w_curr_po_qty=str_replace("'","",$txt_total_plan_cut)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
			}
			else
			{
				$buyer_allocate_percent=100-$tot_allocation_percentage;
				$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
				$tota_country_wise_po_qty=str_replace("'","",$txt_total_plan_cut)+str_replace("'","",$txt_tot_plancut);
				$w_curr_po_qty=$tota_country_wise_po_qty*str_replace("'","",$tot_smv_qnty);
				//$w_curr_po_qty=str_replace("'","",$txt_total_plan_cut)*str_replace("'","",$tot_smv_qnty);
				$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
			}
			
			//$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
			//$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
			//$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
		}
		else
		{
			$total_company_capacity_min=$tot_company_capacity_min;
			$tota_country_wise_po_qty=str_replace("'","",$txt_total_plan_cut)+str_replace("'","",$txt_tot_plancut);
			$w_curr_po_qty=$tota_country_wise_po_qty*str_replace("'","",$tot_smv_qnty);
			$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
		}
		
		//echo "16**".str_replace("'","",$txt_total_plan_cut).'='.str_replace("'","",$tot_smv_qnty);die;
		//echo "16**".$w_tot_po_qty.'='.$total_company_capacity_min;die;
		if($w_tot_po_qty>$total_company_capacity_min){
			//echo "CapaCityMin";
			echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
			disconnect($con);die;
		}
	 } //End
	}
	//==============================capacity Validation ==============================
	//echo "16**here"; die;
	if ($operation==0)  //Insert Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if($cbo_order_uom==58)
		 {
			 $txt_avg_rate=$txt_avg_rate*$tot_set_qnty;
			 $txt_total_plan_cut=$txt_total_plan_cut/$tot_set_qnty;
		 }
		 else
		 {
			$txt_avg_rate=$txt_avg_rate;
			$txt_total_plan_cut=$txt_total_plan_cut;
		 }
		 //echo "10**select a.size_number_id,b.id,b.size_name from bh_wo_po_color_size_breakdown a, lib_size b where b.id=a.size_number_id and a.po_break_down_id=$order_id"; die;
		 $new_array_color=return_library_array( "select a.color_number_id,b.id,b.color_name from bh_wo_po_color_size_breakdown a, lib_color b where b.id=a.color_number_id and a.po_break_down_id=$order_id", "id", "color_name"  );

		 $new_array_size=return_library_array( "select a.size_number_id,b.id,b.size_name from bh_wo_po_color_size_breakdown a, lib_size b where b.id=a.size_number_id and a.po_break_down_id=$order_id", "id", "size_name"  );

		 $color_mst=return_library_array( "select color_mst_id,color_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and color_mst_id!=0", "color_number_id", "color_mst_id"  );

		 $size_mst=return_library_array( "select size_mst_id,size_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id"  );

		 $item_mst=return_library_array( "select item_mst_id,item_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id"  );

		 $id=return_next_id( "id", "bh_wo_po_color_size_breakdown",1);

		 $barcode_year = date("y");
		 $barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","bh_wo_po_color_size_breakdown","barcode_year=$barcode_year","suffix_no");

		 $field_array="id, po_break_down_id,job_id,job_no_mst, color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty,commission_value_type,commission_per_pcs,commission,foreign_commission,po_unit_price,factory_price, country_remarks, country_type, packing, is_deleted, status_active, inserted_by, insert_date, barcode_suffix_no, barcode_year, barcode_no";

		 //echo "10**".$excess_variable; die;
		if($excess_variable==2 && $excess_per_level==1){
			$color_wise_ord_qty=array();
			for ($i=1;$i<=$total_row;$i++){
			 	$txtcolor="txtcolor_".$i;
			 	$txtorderquantity="txtorderquantity_".$i;
			 	$cbogmtsitem="cbogmtsitem_".$i;

			 	$gmtsitem=str_replace("'","",$$cbogmtsitem);

			 	if(str_replace("'","",$$txtcolor)!="")
				{
					if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
						$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
				}
				else $color_id =0;
				$order_qty= str_replace("'","",$$txtorderquantity);
				$color_wise_ord_qty[$gmtsitem][$color_id] +=$order_qty*1;
			}
			$item_details=sql_select("SELECT gmts_item_id, printdiff,embrodiff,washdiff,spwdiff from bh_wo_po_details_mas_set_details where job_id=$hidd_job_id");
			foreach ($item_details as $item) {
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['print_difficulty'] = $item[csf('printdiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['emb_difficulty'] = $item[csf('embrodiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['wash_difficulty'] = $item[csf('washdiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['splwork_difficulty'] = $item[csf('spwdiff')];
			}
			$attr_arr=array('cutting', 'sewing', 'finishing', 'cutting_difficulty', 'sewing_difficulty', 'finishing_difficulty');
			$march_arr=array('print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty');
			foreach ($color_wise_ord_qty as $item_id => $color_dat) {
			 	foreach ($color_dat as $cid => $data) {
			 		$slab_data=sql_select("SELECT id, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, total, 'print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty' from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$cbo_company_name and buyer_id=$cbo_buyer_name and $data >= lower_limit_qty AND $data <= upper_limit_qty");
			 		foreach ($slab_data as $row) {
			 			foreach ($attr_arr as $attr) {
				 			$slab_data_arr[$item_id][$cid][$attr] = $row[csf($attr)];
				 		}
			 			foreach ($march_arr as $diff_attr) {
				 			if($item_dtls_arr[$item_id][$diff_attr]==$row[csf($diff_attr)]){
				 				$field_arr=explode("_", $diff_attr);
				 				$slab_data_arr[$item_id][$cid][$diff_attr] =$row[csf($diff_attr)];
				 				$slab_data_arr[$item_id][$cid][$field_arr[0]] =$row[csf($field_arr[0])];
				 			}
				 		}
			 		}		 				 		
			 	}
			}

			foreach ($slab_data_arr as $gmt_id=>$item_data) {
			 	foreach ($item_data as $c_id => $sdata) {
			 		foreach ($march_arr as $diff_attr) {
			 			if($item_dtls_arr[$gmt_id][$diff_attr]==$slab_data_arr[$gmt_id][$c_id][$diff_attr]){
			 				$field_arr=explode("_", $diff_attr);
			 				$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id][$field_arr[0]];
			 			}
			 		}
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['cutting'];		 		
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['sewing'];		 		
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['finishing'];		 		
			 	}
			}
		}
		// echo "10**<pre>";
		// print_r($slab_plan_cut); die;


		 $new_array_size_arr=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtarticleno="txtarticleno_".$i;
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtarticleno="txtarticleno_".$i;
			 $txtcolor="txtcolor_".$i;
			 $txtsize="txtsize_".$i;
			 $txtorderquantity="txtorderquantity_".$i;
			 $txtorderrate="txtorderrate_".$i;
			 $txtorderamount="txtorderamount_".$i;
			 $txtorderexcesscut="txtorderexcesscut_".$i;
			 $txtorderplancut="txtorderplancut_".$i;

			 $cboComValueType="cboComValueType_".$i;
			 $txtComm="txtComm_".$i;
			 $txtLocalComm="txtLocalComm_".$i;
			 $txtForeignComm="txtForeignComm_".$i;
			 $txtFactoryPrice="txtFactoryPrice_".$i;
			 $txtFactoryValue="txtFactoryValue_".$i;


			 $cbostatus="cbostatus_".$i;
			 $hidbarcode="hidbarcode_".$i;

			if(str_replace("'","",$$txtcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
					$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else $color_id =0;

			if(str_replace("'","",$$txtsize)!="")
			{
				if (!in_array(str_replace("'","",$$txtsize),$new_array_size_arr,true))
				{
					$size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name","401");
					$new_array_size_arr[$size_id]=str_replace("'","",$$txtsize);
				}
				else $size_id =  array_search(str_replace("'","",$$txtsize), $new_array_size_arr);
			}
			else $size_id =0;

			if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
			{
				$item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
			}
			else
			{
				$item_mst[str_replace("'","",$$cbogmtsitem)]=$id;
				$item_mst_id=$id;
			}

			if(array_key_exists($color_id,$color_mst))
			{
				$color_mst_id=$color_mst[$color_id];
			}
			else
			{
				$color_mst[$color_id]=$id;
				$color_mst_id=$id;
			}
			if(array_key_exists($size_id,$size_mst))
			{
				$size_mst_id=$size_mst[$size_id];
			}
			else
			{
				$size_mst[$size_id]=$id;
				$size_mst_id=$id;
			}

			if($excess_variable==2 && $excess_per_level==1)
			{
			 	$orderexcesscut=$slab_plan_cut[str_replace("'","",$$cbogmtsitem)][$color_id];
			 	if($orderexcesscut==''){
			 		$orderexcesscut=0;
			 	}
			 	if($orderexcesscut>0)
			 	{
			 		$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
			 		$orderplancut=$plancut+str_replace("'","",$$txtorderquantity);
			 	}
			 	else{
			 		$orderplancut=str_replace("'","",$$txtorderquantity);
			 	}
			}
			else{
				$orderexcesscut=$$txtorderexcesscut;
			 	$orderplancut=$$txtorderplancut;
			}

			$barcode_suffix_no++;
			$barcode_no=$barcode_year.str_pad($barcode_suffix_no,8,"0",STR_PAD_LEFT);
			$txtarticleno=str_replace("'","",$$txtarticleno);
			if($txtarticleno==""){
				$txtarticleno="no article";
			}

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$order_id.",".$hidd_job_id.",'".$txt_job_no."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$txtarticleno."',".$$cbogmtsitem.",".$cbo_po_country.",".$txt_cutup_date.",".$cbo_cut_up.",".$txt_country_ship_date.",".$size_id.",".$color_id.",".$$txtorderquantity.",".$$txtorderrate.",".$$txtorderamount.",".$orderexcesscut.",".$orderplancut.",".$$cboComValueType.",".$$txtComm.",".$$txtLocalComm.",".$$txtForeignComm.",".$$txtFactoryPrice.",".$$txtFactoryValue.",".$txt_country_remarks.",".$cbo_po_country_type.",".$cbo_packing_country_level.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$barcode_suffix_no."','".$barcode_year."','".$barcode_no."')";
			$id=$id+1;
		 }
		// echo "10**insert into bh_wo_po_color_size_breakdown (".$field_array.") values ".$data_array;die;

		 $rID=sql_insert("bh_wo_po_color_size_breakdown",$field_array,$data_array,0);
		 $field_array1 = "ship_to*product_value_price*unit_lot";
		 $data_array1 = "'".str_replace("'","",$txt_ship_to)."'*'".str_replace("'","",$txt_product_value_price)."'*'".str_replace("'","",$txt_unit_lot)."'";

		 $rID1=sql_update("bh_wo_po_break_down",$field_array1,$data_array1,"id","".$order_id."",1);

		 $return_data= update_job_mast_bh("'".$txt_job_no."'"); //define in common_functions.php

		 update_color_size_sequence_bh("'".$txt_job_no."'",$btn_mood);
		 //echo "10**";
		 // $rID
		 if(str_replace("'","",$is_po_levelqty_update)==1)
		 {
			 //job_order_qty_update("'".$txt_job_no."'",$order_id);
			// echo $r;
		 }
		 update_cost_sheet_bh("'".$txt_job_no."'");
		 // die;
		 


	 
		 

		//============================================================================================
	//	echo $rID."**".$rID1."**".$rID3."**".$rID4; die;
			
		$jobidArr[str_replace("'","",$hidd_job_id)]=str_replace("'","",$hidd_job_id);
		
		fnc_isdyeingplan("bh_wo_po_details_master", $jobidArr);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID."**".$total_row;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID."**".$total_row;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID."**".$total_row;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID."**".$total_row;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$hidd_order_status_id=str_replace("'","",$hidd_order_status_id);

		if($cbo_order_uom==58)
		{
			$txt_avg_rate=$txt_avg_rate*$tot_set_qnty;
			$txt_total_plan_cut=$txt_total_plan_cut/$tot_set_qnty;
		}
		else
		{
			$txt_avg_rate=$txt_avg_rate;
			$txt_total_plan_cut=$txt_total_plan_cut;
		}

		$new_array_size=return_library_array( "select a.size_number_id,b.id,b.size_name from bh_wo_po_color_size_breakdown a, lib_size b where b.id=a.size_number_id and a.po_break_down_id=$order_id", "id", "size_name"  );
		$new_array_color=return_library_array( "select a.color_number_id,b.id,b.color_name from bh_wo_po_color_size_breakdown a, lib_color b where b.id=a.color_number_id and a.po_break_down_id=$order_id", "id", "color_name"  );
		$color_mst=return_library_array( "select color_mst_id,color_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id"  );
		$size_mst=return_library_array( "select size_mst_id,size_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id"  );
		$item_mst=return_library_array( "select item_mst_id,item_number_id from bh_wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id"  );

		$barcode_year = date("y");
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","bh_wo_po_color_size_breakdown","barcode_year=$barcode_year","suffix_no");

		  //echo "10**".$barcode_year; die;
		if($excess_variable==2 && $excess_per_level==1){
			 $color_wise_ord_qty=array();
			 for ($i=1;$i<=$total_row;$i++){
			 	$txtcolor="txtcolor_".$i;
			 	$txtorderquantity="txtorderquantity_".$i;
			 	$cbogmtsitem="cbogmtsitem_".$i;

			 	$gmtsitem=str_replace("'","",$$cbogmtsitem);

			 	if(str_replace("'","",$$txtcolor)!="")
				{
					if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
						$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
				}
				else $color_id =0;
				$order_qty= str_replace("'","",$$txtorderquantity);
				$color_wise_ord_qty[$gmtsitem][$color_id] +=$order_qty*1;

			 }
			 $item_details=sql_select("SELECT gmts_item_id, printdiff,embrodiff,washdiff,spwdiff from bh_wo_po_details_mas_set_details where job_id=$hidd_job_id");
			 foreach ($item_details as $item) {
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['print_difficulty'] = $item[csf('printdiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['emb_difficulty'] = $item[csf('embrodiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['wash_difficulty'] = $item[csf('washdiff')];
			 	$item_dtls_arr[$item[csf('gmts_item_id')]]['splwork_difficulty'] = $item[csf('spwdiff')];
			 }
			 $attr_arr=array('cutting', 'sewing', 'finishing', 'cutting_difficulty', 'sewing_difficulty', 'finishing_difficulty');
			 $march_arr=array('print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty');
			 foreach ($color_wise_ord_qty as $item_id => $color_dat) {
			 	foreach ($color_dat as $cid => $data) {
			 		$slab_data=sql_select("SELECT id, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, total, 'print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty' from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$cbo_company_name and buyer_id=$cbo_buyer_name and $data >= lower_limit_qty AND $data <= upper_limit_qty");
			 		foreach ($slab_data as $row) {
			 			foreach ($attr_arr as $attr) {
				 			$slab_data_arr[$item_id][$cid][$attr] = $row[csf($attr)];
				 		}
			 			foreach ($march_arr as $diff_attr) {
				 			if($item_dtls_arr[$item_id][$diff_attr]==$row[csf($diff_attr)]){
				 				$field_arr=explode("_", $diff_attr);
				 				$slab_data_arr[$item_id][$cid][$diff_attr] =$row[csf($diff_attr)];
				 				$slab_data_arr[$item_id][$cid][$field_arr[0]] =$row[csf($field_arr[0])];
				 			}
				 		}
			 		}		 				 		
			 	}
			 }

			 foreach ($slab_data_arr as $gmt_id=>$item_data) {
			 	foreach ($item_data as $c_id => $sdata) {
			 		foreach ($march_arr as $diff_attr) {
			 			if($item_dtls_arr[$gmt_id][$diff_attr]==$slab_data_arr[$gmt_id][$c_id][$diff_attr]){
			 				$field_arr=explode("_", $diff_attr);
			 				$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id][$field_arr[0]];
			 			}
			 		}
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['cutting'];		 		
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['sewing'];		 		
			 		$slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['finishing'];		 		
			 	}
			 }
		}
		$new_size_array_size=array(); $iscolorchanged=0;		
		for ($i=1;$i<=$total_row;$i++)
		{
			$hiddenid="hiddenid_".$i;
			$txtarticleno="txtarticleno_".$i;
			$cbogmtsitem="cbogmtsitem_".$i;
			$txtarticleno="txtarticleno_".$i;
			$txtcolor="txtcolor_".$i;
			$txtsize="txtsize_".$i;
			$txtorderquantity="txtorderquantity_".$i;
			$txtorderrate="txtorderrate_".$i;
			$txtorderamount="txtorderamount_".$i;
			$txtorderexcesscut="txtorderexcesscut_".$i;
			$txtorderplancut="txtorderplancut_".$i;
			$cbostatus="cbostatus_".$i;
			$hidbarcode="hidbarcode_".$i;


			$cboComValueType="cboComValueType_".$i;
			$txtComm="txtComm_".$i;
			$txtLocalComm="txtLocalComm_".$i;
			$txtForeignComm="txtForeignComm_".$i;
			$txtFactoryPrice="txtFactoryPrice_".$i;
			$txtFactoryValue="txtFactoryValue_".$i;


			if(str_replace("'","",$$txtcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
					$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else $color_id =0;


			if(str_replace("'","",$$txtsize)!="")
			{
				if (!in_array(str_replace("'","",$$txtsize),$new_size_array_size,true))
				{
					$size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name","401");
					$new_size_array_size[$size_id]=str_replace("'","",$$txtsize);
					//echo "10**=".$size_id.'=';die;
				}
				else $size_id =  array_search(str_replace("'","",$$txtsize), $new_size_array_size,true);
				//echo "6**=".$size_id.'='.$$txtsize.',';die;
			}
			else $size_id =0;

			$txtarticleno=str_replace("'","",$$txtarticleno);
			 if($txtarticleno==""){
				 $txtarticleno="no article";
			 }
			 if($excess_variable==2 && $excess_per_level==1)
			{
			 	$orderexcesscut=$slab_plan_cut[str_replace("'","",$$cbogmtsitem)][$color_id];
			 	if($orderexcesscut==''){
			 		$orderexcesscut=0;
			 	}
			 	if($orderexcesscut>0)
			 	{
			 		$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
			 		$orderplancut=$plancut+str_replace("'","",$$txtorderquantity);
			 	}
			 	else{
			 		$orderplancut=str_replace("'","",$$txtorderquantity);
			 	}
			}
			else{
				$orderexcesscut=$$txtorderexcesscut;
			 	$orderplancut=$$txtorderplancut;
			}

			if(str_replace("'",'',$$hiddenid)!="")
			{
				if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
				{
					$item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
				}
				else
				{
					$item_mst[str_replace("'","",$$cbogmtsitem)]=str_replace("'",'',$$hiddenid);
					$item_mst_id=str_replace("'",'',$$hiddenid);
				}

				if(array_key_exists($color_id,$color_mst))
				{
					$color_mst_id=$color_mst[$color_id];
				}
				else
				{
					$color_mst[$color_id]=str_replace("'",'',$$hiddenid);
					$color_mst_id=str_replace("'",'',$$hiddenid);
				}
				if(array_key_exists($size_id,$size_mst))
				{
					$size_mst_id=$size_mst[$size_id];
				}
				else
				{
					$size_mst[$size_id]=str_replace("'",'',$$hiddenid);
					$size_mst_id=str_replace("'",'',$$hiddenid);
				}

				$PrevData=sql_select("select color_number_id, country_ship_date, country_ship_date_prev, color_number_id_prev from bh_wo_po_color_size_breakdown where id=".$$hiddenid);
				$PrevColorNumberId=$PrevData[0][csf('color_number_id')];
				$PrevCountryShipDate=$PrevData[0][csf('country_ship_date')];

				$pre_color_id=$PrevData[0][csf('color_number_id_prev')];
				$pre_country_date=$PrevData[0][csf('country_ship_date_prev')];

				if($PrevColorNumberId==$color_id)
				{
					$pre_colorid=$pre_color_id;
				}
				else 
				{
					$pre_colorid=$PrevColorNumberId;
					$iscolorchanged=1;
				}

				if(change_date_format($PrevCountryShipDate)==change_date_format(str_replace("'","",$txt_country_ship_date)))
				{
					$pre_countryship_date=$pre_country_date;
				}
				else $pre_countryship_date=$PrevCountryShipDate;
				
				if($poEntryControlWithBomApproval==3 && $bomApproved==1)//issue id ISD-21-19965
				{
					$field_array="order_quantity*order_total*plan_cut_qnty*updated_by*update_date";
					$data_array="".$$txtorderquantity."*".$$txtorderamount."*".$orderplancut."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$rID=sql_update("bh_wo_po_color_size_breakdown",$field_array,$data_array,"id","".$$hiddenid."",1);
				}
				else
				{
					$field_array="job_no_mst* 	color_mst_id*size_mst_id*item_mst_id*article_number*item_number_id*country_id*cutup_date*cutup*country_ship_date*size_number_id*color_number_id*order_quantity*order_rate*order_total*excess_cut_perc*plan_cut_qnty*commission_value_type*commission_per_pcs*commission*foreign_commission*po_unit_price*factory_price*country_remarks*country_type*packing*color_number_id_prev*country_ship_date_prev*is_deleted*status_active*updated_by*update_date";
					$data_array="'".$txt_job_no."'*'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*'".$txtarticleno."'*".$$cbogmtsitem."*".$cbo_po_country."*".$txt_cutup_date."*".$cbo_cut_up."*".$txt_country_ship_date."*".$size_id."*".$color_id."*".$$txtorderquantity."*".$$txtorderrate."*".$$txtorderamount."*".$orderexcesscut."*".$orderplancut."*".$$cboComValueType."*".$$txtComm."*".$$txtLocalComm."*".$$txtForeignComm."*".$$txtFactoryPrice."*".$$txtFactoryValue."*".$txt_country_remarks."*".$cbo_po_country_type."*".$cbo_packing_country_level."*'".$pre_colorid."'*'".$pre_countryship_date."'*0*".$$cbostatus."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$rID=sql_update("bh_wo_po_color_size_breakdown",$field_array,$data_array,"id","".$$hiddenid."",1);
				}
			}

			if(str_replace("'",'',$$hiddenid)=="")
			{
				$id=return_next_id( "id", "bh_wo_po_color_size_breakdown", 1);

				if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
				{
					$item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
				}
				else
				{
					$item_mst[str_replace("'","",$$cbogmtsitem)]=$id;
					$item_mst_id=$id;
				}

				if(array_key_exists($color_id,$color_mst))
				{
					$color_mst_id=$color_mst[$color_id];
				}
				else
				{
					$color_mst[$color_id]=$id;
					$color_mst_id=$id;
				}
				if(array_key_exists($size_id,$size_mst))
				{
					$size_mst_id=$size_mst[$size_id];
				}
				else
				{
					$size_mst[$size_id]=$id;
					$size_mst_id=$id;
				}

				$barcode_suffix_no++;
				$barcode_no=$barcode_year.str_pad($barcode_suffix_no,8,"0",STR_PAD_LEFT);
				/*$txtarticleno=str_replace("'","",$$txtarticleno);
				if(str_replace("'","",$$txtarticleno)=="")
				{
					$txtarticleno=$barcode_no;
				}
				else $txtarticleno=str_replace("'","",$$txtarticleno);*/ 

				$field_array="id, po_break_down_id,job_id,job_no_mst, color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc,plan_cut_qnty,commission_value_type,commission_per_pcs,commission,foreign_commission,po_unit_price,factory_price, country_remarks, country_type, packing, is_deleted, status_active, inserted_by, insert_date, barcode_suffix_no, barcode_year, barcode_no";
				$data_array="(".$id.",".$order_id.",".$hidd_job_id.",'".$txt_job_no."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$txtarticleno."',".$$cbogmtsitem.",".$cbo_po_country.",".$txt_cutup_date.",".$cbo_cut_up.",".$txt_country_ship_date.",".$size_id.",".$color_id.",".$$txtorderquantity.",".$$txtorderrate.",".$$txtorderamount.",".$orderexcesscut.",".$orderplancut.",".$$cboComValueType.",".$$txtComm.",".$$txtLocalComm.",".$$txtForeignComm.",".$$txtFactoryPrice.",".$$txtFactoryValue.",".$txt_country_remarks.",".$cbo_po_country_type.",".$cbo_packing_country_level.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$barcode_suffix_no."','".$barcode_year."','".$barcode_no."')";
				//echo "10**insert into bh_wo_po_color_size_breakdown (".$field_array.") values ".$data_array;die;

				$rID=sql_insert("bh_wo_po_color_size_breakdown",$field_array,$data_array,1);
				$barcode_suffix_no++;
				$iscolorchanged=1;
			}
		}
		 $field_array1="unit_price*po_total_price*excess_cut*plan_cut*updated_by*update_date";
		 $data_array1="'".$txt_avg_rate."'*'".$txt_total_amt."'*'".$txt_avg_excess_cut."'*'".$txt_total_plan_cut."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //$rID1=sql_update("bh_wo_po_break_down",$field_array1,$data_array1,"id","".$order_id."",1);
		$return_data= update_job_mast_bh("'".$txt_job_no."'");//define in common_functions.php

		update_color_size_sequence_bh("'".$txt_job_no."'",$btn_mood);
 

		//===============================================================================================
	 
 
		$jobidArr[str_replace("'","",$hidd_job_id)]=str_replace("'","",$hidd_job_id);

		$field_array1 = "ship_to*product_value_price*unit_lot";
		$data_array1 = "'".str_replace("'","",$txt_ship_to)."'*'".str_replace("'","",$txt_product_value_price)."'*'".str_replace("'","",$txt_unit_lot)."'";
		$rID1=sql_update("bh_wo_po_break_down",$field_array1,$data_array1,"id","".$order_id."",1);
		
		if($iscolorchanged==1)
		{
			fnc_isdyeingplan("bh_wo_po_details_master", $jobidArr);
		}
		//============================================================================================
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$txt_job_no."**".$rID."**".$total_row;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job_no."**".$rID."**".$total_row;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$txt_job_no."**".$rID."**".$total_row;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID."**".$total_row;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();

 
		$rID=execute_query( "update  bh_wo_po_color_size_breakdown set status_active=0,is_deleted=1  where  po_break_down_id =$order_id and country_id=$cbo_po_country and job_no_mst='".$txt_job_no."' ",1);
	 
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}
}

if ($action=="get_excess_cut_percent")
{
	 $data=explode("_",$data);
	 $qry_result=sql_select( "select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	 foreach ($qry_result as $row)
	 {
		 if ( $data[0]>=$row[csf("slab_rang_start")] && $data[0]<=$row[csf("slab_rang_end")] )
		 {
			 echo $row[csf("excess_percent")];  disconnect($con);die;
		 }
	 }
	 echo "0";  disconnect($con);die;
}

if ($action == "report_barcode_generation")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');

	$data = explode("***", $data);
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$size_arr = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$sql = "select a.company_name, a.job_no, a.style_ref_no, a.currency_id, b.po_number, c.item_number_id, c.article_number, c.color_number_id, c.size_number_id, c.barcode_no, c.order_quantity as po_qty, c.plan_cut_qnty as plan_qty, c.order_rate from bh_wo_po_details_master a, bh_wo_po_break_down b, bh_wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id in ($data[0]) and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$comapny_name = ''; $job_no = ''; $po_no = ''; $gmts_item = ''; $article_no = ''; $size_id = ''; $barcode_no="";

	$po_arr=array();
	foreach ($result as $row)
	{
		$gmts_item = $garments_item[$row[csf('item_number_id')]];
		$color_id = $color_arr[$row[csf('color_number_id')]];
		$size_id = $size_arr[$row[csf('size_number_id')]];
		$rate_symbol="";
		if($row[csf('currency_id')]==1) $rate_symbol="BDT: ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==2) $rate_symbol="$ ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==3) $rate_symbol="€ ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==4) $rate_symbol="CHF ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==5) $rate_symbol="S$ ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==6) $rate_symbol="£ ".number_format($row[csf('order_rate')],2,'.','');
		else if($row[csf('currency_id')]==7) $rate_symbol="¥ ".number_format($row[csf('order_rate')],2,'.','');
		//$rate_symbol="<b>".$rate_symbol."</b>";

		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['comp']=$row[csf('company_name')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['job']=$row[csf('job_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ref']=$row[csf('style_ref_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po']=$row[csf('po_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['article']=$row[csf('article_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['barcode']=$row[csf('barcode_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_qty')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['rate']=$rate_symbol;
	}

	$pdf=new PDF_Code39('p','mm',array(100,28));
	//$pdf->SetTextColor(255, 255, 255);
	//$pdf->Code39(80,40,'CODE 39',1,10);

	$html .="<style type=\"text/css\" media=\"print\">
			.breakAfter
				{
					page-break-before: always;
				}
			}
		 </style>";
	$pdf->SetFont('Arial','B',5);

	foreach ($po_arr as $item_id=>$item_data)
	{
		foreach ($item_data as $color_id=>$color_data)
		{
			$i=0;
		  	$wid="margin-top:-20px;";
			foreach ($color_data as $size_id=>$val)
			{
				for ($q=1; $q<=$val["plan"]; $q++)
				{
					$mn=$i%2;
					if($val["article"]=="" || $val["article"]=="no article") $val["article"]=$val["barcode"];
					if( $mn==0 ){
						$pdf->AddPage();
						$pdf->SetFont('Arial','B','');
						$style_ref="Style: ".$val['ref'];
						$item_des=$garments_item[$item_id];
						$size_rate='Size: '.$size_arr[$size_id].'; '.$val['rate'];
						$color_size=$color_arr[$color_id].'; Size: '.$size_arr[$size_id];
						//function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.22, $h = 10, $wide = true, $textonly=false,$fontSize=11)

						$pdf->Code39(10, 1,$val['barcode'], $ext = "", $cks = "", $w = 0.15, $h = 6, $wide = true, $textonly=false, $fontSize=6);
						$pdf->Code39(10, 1,$style_ref, $ext = true, $cks = false, $w = 0.3, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(10, 3.5,$item_des, $ext = true, $cks = false, $w = 0.3, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(10, 6,$color_arr[$color_id], $ext = true, $cks = false, $w = 0.3, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(10, 8.5,$size_rate, $ext = true, $cks = false, $w = 0.3, $h = 8, $wide = true, true,6) ;
					}
					else
					{
                    	$style_ref="Style: ".$val['ref'];
						$item_des=$garments_item[$item_id];
						$size_rate='Size: '.$size_arr[$size_id].'; '.$val['rate'];
						$color_size=$color_arr[$color_id].'; Size: '.$size_arr[$size_id];

						$pdf->Code39(51, 1, $val['barcode'], $ext = '', $cks = '', $w = 0.15, $h = 6, $wide = true, $textonly=false, $fontSize=6);
						$pdf->Code39(51, 1,$style_ref, $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(51, 3.5,$item_des, $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(51, 6,$color_arr[$color_id], $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,6) ;
						$pdf->Code39(51, 8.5,$size_rate, $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,6) ;
					}
					$i++;
				}
				//$pdf .="</table>";
			}
		}
	}
	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}

	$name = 'po_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if ($action == "report_barcode_generation128")
{
	//require_once('../../../ext_resource/mpdf60/mpdf.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code128.php');


	//$mpdf->writeBarcode();

	$data = explode("***", $data);
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$size_arr = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$sql = "select a.company_name, a.job_no, a.style_ref_no, b.po_number, c.item_number_id, c.article_number, c.color_number_id, c.size_number_id, c.barcode_no, c.order_quantity as po_qty, c.plan_cut_qnty as plan_qty from bh_wo_po_details_master a, bh_wo_po_break_down b, bh_wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id in ($data[0]) and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$comapny_name = ''; $job_no = ''; $po_no = ''; $gmts_item = ''; $article_no = ''; $size_id = ''; $barcode_no="";

	$po_arr=array();
	foreach ($result as $row)
	{
		$gmts_item = $garments_item[$row[csf('item_number_id')]];
		$color_id = $color_arr[$row[csf('color_number_id')]];
		$size_id = $size_arr[$row[csf('size_number_id')]];

		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['comp']=$row[csf('company_name')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['job']=$row[csf('job_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ref']=$row[csf('style_ref_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po']=$row[csf('po_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['article']=$row[csf('article_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['barcode']=$row[csf('barcode_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_qty')];
	}

	//$pdf=new PDF_Code128('P','mm',array(35,35));
	$pdf=new PDF_Code128('L','mm',array(32,40));

	$pdf->AddPage();
	$pdf->SetFont('Arial','',7);
	$i=2; $j=2; $k=0; $br=0; $n=0;

	foreach ($po_arr as $item_id=>$item_data)
	{
		foreach ($item_data as $color_id=>$color_data)
		{
			foreach ($color_data as $size_id=>$val)
			{
				//$j=1; $m=5; $pdf .="<table>";
				if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
				for ($q=1; $q<=$val["plan"]; $q++)
				{
					if($val["article"]=="" || $val["article"]=="no article") $val["article"]=$val["barcode"];
					if($br==1)
					{
						$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
					}

					//if($j==1) { $pdf .="<tr>"; }
					//if($j==7) { $j=1; $pdf .="<tr>"; }


					$pdf->SetXY($i, $j);
					$pdf->Write(0, $val['job']);

					$pdf->SetXY($i, $j+3);
					$pdf->Write(0, "REF: ".$val['ref']);//24

					$pdf->SetXY($i, $j+6);
					$pdf->Write(0, "Item: ".$garments_item[$item_id]);


					$pdf->SetXY($i, $j+9);
					$pdf->Write(0, "Color: ".$color_arr[$color_id]."; Size: ".$size_arr[$size_id]);//24 $style_name

					//$pdf->SetXY($i, $j+12);
					//$pdf->Write(0, "Size: ".substr($size_arr[$size_id], 0, 35));
					$pdf->Code128($i+1,$j+12,$val['barcode'],30,10);

					$k++;
					$br++;

					//for ($i=1; $i<=$m; $i++)
					//{
						/*$pdf .="<td><div><table style=\"width:150px; font-size:16px;\">
								<tr>
									<td align=\"left\">".$com_logo_pic."</td>
								</tr>
							</table>
							<table style=\"width:150px; font-size:12px;\">
								<tr>
									<td>Job No.".$val["job"]."</td>
								</tr>
								<tr>
									<td>Ref : ".$val["ref"]."</td>
								</tr>
								<tr>
									<td>Gmts Item : ".$garments_item[$item_id]."</td>
								</tr>
								<tr>
									<td>Color : ".$color_arr[$color_id]."</td>
								</tr>
								<tr>
									<td>Size : ".$size_arr[$size_id]."</td>
								</tr>
								<tr>
									<td>Article : ".$val["article"]."</td>
								</tr>
								<tr>
									<td align=\"left\"><barcode code=".$val["barcode"]." type=\"C39\" size=\"0.5\" height=\"2.0\" /></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
						</div></td><td>&nbsp;</td>";
						$j++;
						if($j==7) { $pdf .="</tr>"; }*/
					//}
				}
				//$pdf .="</table>";
			}
		}
	}
	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'po_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
	/*$mpdf->WriteHTML($pdf);

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'po_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output("".$name, 'F');

	echo "".$name;*/
	exit();
}

if ($action == "report_barcode_generation_mpdf")
{
	require_once('../../../ext_resource/mpdf60/mpdf.php');

	$mpdf = new mPDF('utf-8','A4','10','ARIAL');//,'0.5','0.5','0.5','0.5'
	$mpdf = new mPDF(bn);
	$mpdf->SetCreator("Kausar");
	$mpdf->SetAuthor("Logic");
	$mpdf->SetTitle("Logic Platform");
	$mpdf->SetSubject("Barcode");
	$mpdf->AddPage('L','','','','',5,5,5,5,1,1);
	//$mpdf->writeBarcode();

	$data = explode("***", $data);
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$size_arr = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$sql = "select a.company_name, a.job_no, a.style_ref_no, b.po_number, c.item_number_id, c.article_number, c.color_number_id, c.size_number_id, c.barcode_no, c.order_quantity as po_qty, c.plan_cut_qnty as plan_qty from bh_wo_po_details_master a, bh_wo_po_break_down b, bh_wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id in ($data[0]) and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$comapny_name = ''; $job_no = ''; $po_no = ''; $gmts_item = ''; $article_no = ''; $size_id = ''; $barcode_no="";

	$po_arr=array();
	foreach ($result as $row)
	{
		$gmts_item = $garments_item[$row[csf('item_number_id')]];
		$color_id = $color_arr[$row[csf('color_number_id')]];
		$size_id = $size_arr[$row[csf('size_number_id')]];

		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['comp']=$row[csf('company_name')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['job']=$row[csf('job_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ref']=$row[csf('style_ref_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po']=$row[csf('po_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['article']=$row[csf('article_number')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['barcode']=$row[csf('barcode_no')];
		$po_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_qty')];
	}

	$html .="<style type=\"text/css\" media=\"print\">
			.breakAfter
				{
					page-break-before: always;
				}
			.page *{
				margin-top: 2.54cm;
				margin-bottom: 2.54cm;
				margin-left: 3.175cm;
				margin-right: 3.175cm;
			}
		 </style>";
	foreach ($po_arr as $item_id=>$item_data)
	{
		foreach ($item_data as $color_id=>$color_data)
		{
			foreach ($color_data as $size_id=>$val)
			{
				$j=1; $m=5; $pdf .="<table>";
				for ($q=1; $q<=$val["plan"]; $q++)
				{
					if($val["article"]=="" || $val["article"]=="no article") $val["article"]=$val["barcode"];
					$location = $imge_arr[$val["comp"]];
					$com_logo_pic = ( $location != '' && file_exists( "../../../$location" ))?"<img src=\"../../../$location\" width=\"140px\" height=\"40px\" border=\"0\" />":"<img src=\"../../../file_upload/blank_file.png\" width=\"140\" height=\"40\" />";
					if($j==1) { $pdf .="<tr>"; }
					if($j==7) { $j=1; $pdf .="<tr>"; }

					//for ($i=1; $i<=$m; $i++)
					//{
						$pdf .="<td><div><table style=\"width:150px; font-size:16px;\">
								<tr>
									<td align=\"left\">".$com_logo_pic."</td>
								</tr>
							</table>
							<table style=\"width:150px; font-size:12px;\">
								<tr>
									<td>Job No.".$val["job"]."</td>
								</tr>
								<tr>
									<td>Ref : ".$val["ref"]."</td>
								</tr>
								<tr>
									<td>Gmts Item : ".$garments_item[$item_id]."</td>
								</tr>
								<tr>
									<td>Color : ".$color_arr[$color_id]."</td>
								</tr>
								<tr>
									<td>Size : ".$size_arr[$size_id]."</td>
								</tr>
								<tr>
									<td>Article : ".$val["article"]."</td>
								</tr>
								<tr>
									<td align=\"left\"><barcode code=".$val["barcode"]." type=\"C39\" size=\"0.5\" height=\"2.0\" /></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
						</div></td><td>&nbsp;</td>";
						$j++;
						if($j==7) { $pdf .="</tr>"; }
					//}
				}
				$pdf .="</table>";
			}
		}
	}
	$mpdf->WriteHTML($pdf);

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'po_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output("".$name, 'F');

	echo "".$name;
	exit();
}

function job_order_qty_update($job_no,$po_id)
{
	//($job_no,$po_id,$set_data,$breakdown_type,$order_status)
	$po_data_arr=array(); $job_data_arr=array(); $item_set_arr=array(); $item_ratio=0;
	//print_r($set_data);
	$sql_job=sql_select("select a.set_break_down, b.is_confirmed from bh_wo_po_details_master a, bh_wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no=$job_no and b.id=$po_id and a.is_deleted=0 and b.is_deleted=0");
	$set_data=explode('__',$sql_job[0][csf('set_break_down')]);
	$order_status=$sql_job[0][csf('is_confirmed')];
	unset($sql_job);

	//echo $job_no.'='.$po_id.'='.$set_data.'='.$breakdown_type.'='.$order_status; die;
	foreach($set_data as $exSet)
	{
		$exItemRatio=explode('_',$exSet);
		//$item_ratio_arr[$exItemRatio[0]]=$exItemRatio[1];
		$item_ratio+=$exItemRatio[1];
	}
	//echo "select po_break_down_id, item_number_id, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from bh_wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active=1 group by po_break_down_id, item_number_id";
	$data_array_se=sql_select("select po_break_down_id, status_active, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from bh_wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active!=0 group by po_break_down_id, status_active");
	foreach($data_array_se as $row)
	{
		//$item_ratio=0;
		$item_qty=0; $item_amt=0; $item_planCut=0;
		//$item_ratio=$item_ratio_arr[$row[csf('item_number_id')]];
		$item_qty=$row[csf('po_tot')]/$item_ratio;
		$item_amt=$row[csf('po_tot_price')];//*$item_ratio;
		$item_planCut=$row[csf('plan_cut')]/$item_ratio;
		$po_data_arr[$row[csf('po_break_down_id')]]['qty']+=$item_qty;
		$po_data_arr[$row[csf('po_break_down_id')]]['amt']+=$item_amt;
		$po_data_arr[$row[csf('po_break_down_id')]]['plan']+=$item_planCut;
		$job_item_qty=0; $job_item_amt=0;
		if($row[csf('status_active')]==1)
		{
			$job_item_qty=$row[csf('po_tot')]/$item_ratio;
			$job_item_amt=$row[csf('po_tot_price')];
		}
		$job_data_arr['qty']+=$job_item_qty;
		$job_data_arr['amt']+=$job_item_amt;
	}
	//echo $item_ratio; die;
	//list($po_data)=$data_array_se;
	$set_qnty=str_replace("'","",$set_qnty);
	$job_qty=0; $job_amt=0; $poavgprice=0;
	$job_qty=$job_data_arr['qty'];
	$job_amt=$job_data_arr['amt'];

 	$poavgprice=number_format($job_amt/$job_qty,4);
	if($job_qty==0) $job_qty=0;
	if($job_amt==0) $job_amt=0;
	if($poavgprice==0) $poavgprice=0;
	//echo $job_qty_set.'='.$job_amt_set.'='.$job_price; die;
	$field_array_job="job_quantity*avg_unit_price*total_price";
	$data_array_job="".$job_qty."*".$poavgprice."*".$job_amt."";
	//echo $field_array_job."****".$data_array_job;
	$po_qty=$po_data_arr[str_replace("'","",$po_id)]['qty'];
	$po_unit_price=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4);
	$poavgprice_po=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4);

	$po_ex_per=number_format((($po_data_arr[str_replace("'","",$po_id)]['plan']-$po_qty)/$po_qty)*100,2);
	//$field_array_po="plan_cut*excess_cut";
	//$data_array_po="'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";

	//$field_array_po="po_quantity*po_total_price*plan_cut*excess_cut";
	//$data_array_po="".$po_qty."*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";

	//echo $data_array_po;
	$rID2=sql_update("bh_wo_po_details_master",$field_array_job,$data_array_job,"job_no","".$job_no."",1);
	$rID3=execute_query( "update bh_wo_po_break_down set po_total_price='".$po_data_arr[str_replace("'","",$po_id)]['amt']."', unit_price='".$poavgprice_po."',  plan_cut='".$po_data_arr[str_replace("'","",$po_id)]['plan']."', excess_cut='".$po_ex_per."' where id=".$po_id."",0);
	//sql_update("bh_wo_po_break_down",$field_array_po,$data_array_po,"id","".$po_id."",1);

	/*$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,
	sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from bh_wo_po_break_down where job_no_mst='$job_no' ");

	$jobQtyProjected=0; $jobPriceProjected=0; $jobAmtProjected=0; $jobQtyOriginal=0; $jobPriceOriginal=0; $jobAmtOriginal=0;
	$job_projected_price=0;
	$job_projected_price=$projected_data_array[0][csf('job_projected_total')]/$projected_data_array[0][csf('job_projected_qty')];

	$jobQtyProjected= number_format($projected_data_array[0][csf('job_projected_qty')]);
	$jobPriceProjected= number_format($job_projected_price,4);
	$jobAmtProjected= number_format($projected_data_array[0][csf('job_projected_total')],2);

	$jobQtyOriginal= number_format($projected_data_array[0][csf('projected_qty')]);
	$jobPriceOriginal= number_format($projected_data_array[0][csf('projected_rate')],4);
	$jobAmtOriginal= number_format($projected_data_array[0][csf('projected_amount')],2);

	$value= $job_qty."**".$poavgprice."**".$job_amt."**".$jobQtyProjected."**".$jobPriceProjected."**".$jobAmtProjected."**".$jobQtyOriginal."**".$jobPriceOriginal."**".$jobAmtOriginal."**".$po_unit_price;
	//array(0=>$rID,1=>$po_data[csf('po_tot')],2=>$poavgprice,3=>$po_data[csf('po_tot_price')]);
	 $value;*///return;
	//exit();
}

/*if ($action=="show_po_active_listview")
{
	$arr=array (0=>$order_status,5=>$unit_of_measurement);
 	$sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,a.po_quantity,b.order_uom,a.unit_price,a.po_total_price,a.excess_cut,a.plan_cut,DATEDIFF(a.shipment_date,a.po_received_date) date_diff,a.status_active,a.details_remarks ,a.id from  bh_wo_po_break_down a, bh_wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='$data'";

	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,PO Qnty, Order Uom,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time ", "90,130,80,80,80,50,80,80,80,80","975","220",0, $sql , "show_list_view", "id,po_number", "'populate_size_color_breakdown','size_color_breakdown'", 1, "is_confirmed,0,0,0,0,order_uom,0,0,0,0,0", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,po_quantity,order_uom,unit_price,po_total_price,excess_cut,plan_cut,date_diff", "../woven_order/requires/bh_size_color_breakdown_controller",'','0,0,3,3,1,0,2,2,2,1,1');
}

if ($action=="show_country_po_active_listview")
{
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$arr=array (0=>$order_status,5=>$country_library);
 	$sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,a.po_quantity,c.country_id,a.unit_price,a.po_total_price,a.excess_cut,sum(c.plan_cut_qnty) as plan_cut,DATEDIFF(a.shipment_date,a.po_received_date) date_diff,a.status_active,a.details_remarks ,a.id from  bh_wo_po_break_down a, bh_wo_po_details_master b,bh_wo_po_color_size_breakdown c  where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no_mst and c.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$data' group by a.id,c.country_id";

	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,PO Qnty, Order Uom,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time ", "90,130,80,80,80,50,80,80,80,80","975","220",0, $sql , "show_list_view", "id,po_number", "'populate_size_color_breakdown','size_color_breakdown'", 1, "is_confirmed,0,0,0,0,country_id,0,0,0,0,0", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,po_quantity,country_id,unit_price,po_total_price,excess_cut,plan_cut,date_diff", "bh_size_color_breakdown_controller",'','0,0,3,3,1,0,2,2,2,1,1');
}*/
if($action=="get_jobno_by_poid")
{
	$sql_data=sql_select("SELECT job_no_mst from bh_wo_po_break_down where id='$data' and is_deleted=0 and status_active=1");
	$job_no=$sql_data[0][csf("job_no_mst")];
	echo trim($job_no);
	die;
}
if ($action=="order_listview")
{
	$data=explode("*",$data);
	$sql= "select id, po_number, po_received_date, pub_shipment_date, po_quantity,plan_cut, status_active, is_confirmed from bh_wo_po_break_down where is_deleted=0 and job_no_mst='$data[0]' order by id ASC";
	$data_array=sql_select($sql);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450">
        <thead>
            <th width="20">SL</th>
            <th width="80">Po No.</th>
            <th width="70">Po Qty</th>
            <th width="70">Plan Cut Qty</th>
            <th width="70">Ship Date</th>
            <th width="60">Lead Time</th>
            <th width="40">Status</th>
        </thead>
     </table>
     <div style="width:468px; max-height:340px; overflow:scroll;">
         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450" id="tbl_po_list">
            <tbody>
            <?
            $i=1;
            foreach($data_array as $row)
            {
               // if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
                if($data[1]==$row[csf('id')]) $bgcolor="#33CC00"; else $bgcolor;
				$daysOnHand=0;
				if($db_type==2)
				{
					$today= change_date_format($row[csf('pub_shipment_date')],'','',1);
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')],'','',1),$today);
				}
				else
				{
					$today= change_date_format($row[csf('pub_shipment_date')]);
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')]),$today);
				}

                ?>
                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<? echo $i; ?>','<? echo $bgcolor; ?>'); loaddata_by_poid('<? echo $row[csf('id')]; ?>')">
                    <td width="20" align="center"><? echo $i; ?></td>
                    <td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('po_number')]; ?></div></td>
                    <td width="70" align="center"><? echo number_format($row[csf('po_quantity')]); ?></td>
                    <td width="70" align="center"><? echo number_format($row[csf('plan_cut')]); ?></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    <td width="60" align="center"><? echo $daysOnHand; ?></td>
                    <td width="40" align="center"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
<?
exit();
}

?>