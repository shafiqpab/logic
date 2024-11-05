<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$truck_type_arr = array(1 => "Own", 2 => "Hired");
$transport_type_arr = array(1 => "Tailor", 2 => "Container");
$truck_type_arr_json = json_encode($truck_type_arr);
$transport_type_arr_json = json_encode($transport_type_arr);

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");

$location_id = $userCredential[0][csf('location_id')];
$location_credential_cond = "";

if ($location_id != '') {
	$location_credential_cond = " and id in($location_id)";
}

//************************************ Start **************************************************

if ($action == "company_wise_report_button_setting") {
	extract($_REQUEST);
	//echo " select format_id from lib_report_template where template_name ='".$data."'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1";
	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",", $print_report_format);
	echo "$('#Print1').hide();\n";
	echo "$('#print_remarks_rpt').hide();\n";
	echo "$('#print_remarks_rpt3').hide();\n";
	echo "$('#print_remarks_rpt_sonia').hide();\n";
	echo "$('#print_remarks_rpt4').hide();\n";
	echo "$('#print_remarks_rpt5').hide();\n";
	echo "$('#print_remarks_rpt6').hide();\n";
	echo "$('#print_remarks_rpt7').hide();\n";
	echo "$('#print_remarks_rpt8').hide();\n";
	echo "$('#print_remarks_rpt9').hide();\n";
	echo "$('#print_remarks_rpt10').hide();\n";
	echo "$('#print_remarks_rpt11').hide();\n";

	if ($print_report_format != "") {
		foreach ($print_report_format_arr as $id) {
			if ($id == 78) {
				echo "$('#Print1').show();\n";
			}
			if ($id == 121) {
				echo "$('#print_remarks_rpt').show();\n";
			}
			if ($id == 122) {
				echo "$('#print_remarks_rpt3').show();\n";
			}
			if ($id == 123) {
				echo "$('#print_remarks_rpt_sonia').show();\n";
			}
			if ($id == 127) {
				echo "$('#print_remarks_rpt4').show();\n";
			}
			if ($id == 169) {
				echo "$('#print_remarks_rpt6').show();\n";
			}
			if ($id == 580) {
				echo "$('#print_remarks_rpt5').show();\n";
			}
			if ($id == 758) {
				echo "$('#print_remarks_rpt7').show();\n";
			}
			if ($id == 227) {
				echo "$('#print_remarks_rpt8').show();\n";
			}
			if ($id == 235) {
				echo "$('#print_remarks_rpt9').show();\n";
			}
			if ($id == 274) {
				echo "$('#print_remarks_rpt10').show();\n";
			}
            if ($id == 241) {
                echo "$('#print_remarks_rpt11').show();\n";
            }
		}
	}
	exit();
}

if ($action == "load_variable_settings") {

	extract($_REQUEST);
	//echo " select format_id from lib_report_template where template_name ='".$data."'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1";
	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",", $print_report_format);
	echo "$('#Print1').hide();\n";
	echo "$('#print_remarks_rpt').hide();\n";
	echo "$('#print_remarks_rpt3').hide();\n";
	echo "$('#print_remarks_rpt_sonia').hide();\n";
	echo "$('#print_remarks_rpt4').hide();\n";
	echo "$('#print_remarks_rpt5').hide();\n";
	echo "$('#print_remarks_rpt6').hide();\n";
	echo "$('#print_remarks_rpt7').hide();\n";
	echo "$('#print_remarks_rpt8').hide();\n";
	echo "$('#print_remarks_rpt9').hide();\n";
	echo "$('#print_remarks_rpt10').hide();\n";
	echo "$('#print_remarks_rpt11').hide();\n";

	if ($print_report_format != "")
	{
		foreach ($print_report_format_arr as $id)
		{
			if ($id == 78) {
				echo "$('#Print1').show();\n";
			}
			if ($id == 121) {
				echo "$('#print_remarks_rpt').show();\n";
			}
			if ($id == 122) {
				echo "$('#print_remarks_rpt3').show();\n";
			}
			if ($id == 123) {
				echo "$('#print_remarks_rpt_sonia').show();\n";
			}
			if ($id == 127) {
				echo "$('#print_remarks_rpt4').show();\n";
			}
			if ($id == 169) {
				echo "$('#print_remarks_rpt6').show();\n";
			}
			if ($id == 580) {
				echo "$('#print_remarks_rpt5').show();\n";
			}
			if ($id == 758) {
				echo "$('#print_remarks_rpt7').show();\n";
			}
			if ($id == 227) {
				echo "$('#print_remarks_rpt8').show();\n";
			}
			if ($id == 235) {
				echo "$('#print_remarks_rpt9').show();\n";
			}
			if ($id == 274) {
				echo "$('#print_remarks_rpt10').show();\n";
			}
            if ($id == 241) {
                echo "$('#print_remarks_rpt11').show();\n";
            }
		}
	}
	// =====================================================
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
	foreach ($sql_result as $result) {
		echo "$('#sewing_production_variable').val(" . $result[csf("ex_factory")] . ");\n";
		echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
		if ($result[csf("ex_factory")] == 1) {
			echo "$('#txt_ex_quantity').attr('readonly',false);\n";
		}
	}

	$control_and_preceding = sql_select("select is_control, preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$data'");

	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";

	if (!$control_and_preceding[0][csf("is_control")]) $variable_is_control = 0;
	echo "document.getElementById('variable_is_controll').value='" . $variable_is_control . "';\n";
	echo "$('#hidden_variable_cntl').val('" . $variable_is_control . "');\n";
	echo "document.getElementById('txt_qty_source').value='" . $control_and_preceding[0][csf("preceding_page_id")] . "';\n";
	echo "$('#hidden_preceding_process').val('" . $control_and_preceding[0][csf("preceding_page_id")] . "');\n";
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 91; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry

	if ($qty_source != 0) {
		echo "$('#source_msg').text('');\n";
		if ($qty_source == 4) {
			echo "$('#source_msg').text('Sewing Input Qty');\n";
		} else if ($qty_source == 5) {
			echo "$('#source_msg').text('Sewing Output Qty');\n";
		} else if ($qty_source == 7) {
			echo "$('#source_msg').text('Iron Qty');\n";
		}else if ($qty_source == 8) {
			echo "$('#source_msg').text('Packing And Finishing');\n";
		} else if ($qty_source == 11) {
			echo "$('#source_msg').text('Poly Entry Qty');\n";
		} else if ($qty_source == 91) {
			echo "$('#source_msg').text('Buyer Inspection Qty');\n";
		} else if ($qty_source == 82) {
			echo "$('#source_msg').text('Finish Gmts Issue Qty');\n";
		} else if ($qty_source == 14) {
			echo "$('#source_msg').text('Gmts Finish Del. Qty.');\n";
		} else if ($qty_source == 81) {
			echo "$('#source_msg').text('Finish Gmts Rec. Qty.');\n";
		} else {
			echo "$('#source_msg').text('Sewing Finish Qty');\n";
		}
	}

	echo "$('#wip_valuation_for_accounts').val(0);\n";
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$data and variable_list=76 and status_active=1 and is_deleted=0");
	echo "$('#wip_valuation_for_accounts').val($wip_valuation_for_accounts);\n";
	if($wip_valuation_for_accounts==1)
	{
		echo "$('#wip_valuation_for_accounts_button').show();\n";
	}
	else
	{
		echo "$('#wip_valuation_for_accounts_button').hide();\n";
	}

	exit();
}

if($action=="show_cost_details")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');
	// $sqlResult =sql_select("SELECT b.po_number,a.country_id,a.item_number_id, a.cost_rate,a.cost_per_pcs from pro_ex_factory_mst a,wo_po_break_down b,lib_country c where b.id=a.po_break_down_id and a.country_id=c.id and a.delivery_mst_id='$sys_id' and a.status_active=1 and a.is_deleted=0");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_rate,a.cost_per_pcs,a.cut_fab_cot,a.cut_oh,a.print_cost,a.emb_cost,a.wash_cost,a.sew_oh,a.sew_trims_cost,a.fin_oh,a.fin_trims_cost from pro_ex_factory_dtls a,pro_ex_factory_mst e,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where a.mst_id=e.id and a.COLOR_SIZE_BREAK_DOWN_ID=c.id and e.po_break_down_id=b.id and b.id=c.po_break_down_id  and c.country_id=d.id and e.delivery_mst_id='$sys_id' and a.status_active=1 and a.is_deleted=0");

	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}

	$data_array = array();
	foreach ($sqlResult as $v)
	{
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_fab_cot'] = $v['CUT_FAB_COT'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh'] = $v['CUT_OH'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['print_cost'] = $v['PRINT_COST'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['emb_cost'] = $v['EMB_COST'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['wash_cost'] = $v['WASH_COST'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['sew_oh'] = $v['SEW_OH'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['sew_trims_cost'] = $v['SEW_TRIMS_COST'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fin_oh'] = $v['FIN_OH'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fin_trims_cost'] = $v['FIN_TRIMS_COST'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}

	?>
 		<table cellpadding="0" cellspacing="0" width="1100" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="100">PO</th>
				<th width="100">Item</th>
				<th width="100">Color</th>
				<th width="90">Cutting Fab Cost</th>
				<th width="90">Cutting OH</th>
				<th width="90">Print Cost</th>
				<th width="90">Emb Cost</th>
				<th width="90">Wash Cost</th>
				<th width="90">Sewing Oh</th>
				<th width="90">Sewing Trims Cost</th>
				<th width="90">Finish OH</th>
				<th width="90">Finish Trims Cost</th>
				<th width="90">Cost Per Pcs</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach ($data_array as $po_id=>$po_data)
				{
					foreach ($po_data as $itm_id=>$itm_data)
					{
						foreach ($itm_data as $color_id=>$v)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><?=$v['po_number'];?></td>
								<td><?=$garments_item[$itm_id];?></td>
								<td><?=$lib_color[$color_id];?></td>
								<td align="right"><?=$v['cut_fab_cot'];?></td>
								<td align="right"><?=$v['cut_oh'];?></td>
								<td align="right"><?=$v['print_cost'];?></td>
								<td align="right"><?=$v['emb_cost'];?></td>
								<td align="right"><?=$v['wash_cost'];?></td>
								<td align="right"><?=$v['sew_oh'];?></td>
								<td align="right"><?=$v['sew_trims_cost'];?></td>
								<td align="right"><?=$v['fin_oh'];?></td>
								<td align="right"><?=$v['fin_trims_cost'];?></td>
								<td align="right"><?=$v['cost_per_pcs'];?></td>
							</tr>
							<?
						}
					}
				}
				?>
			</tbody>
		</table>
	<?

	exit();
}
if ($action == "load_drop_delivery_company") {
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if ($data == 3) {
		if ($db_type == 0) {
			echo create_drop_down("cbo_del_company", 130, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "", 0, 0);
		} else {
			echo create_drop_down("cbo_del_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $selected, "");
		}
	} else if ($data == 1) {
		echo create_drop_down("cbo_del_company", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Delivery Company --", '', "load_drop_down( 'requires/garments_delivery_entry_controller', this.value, 'load_drop_down_del_location', 'del_location_td' );", 0);
	} else
		echo create_drop_down("cbo_del_company", 130, $blank_array, "", 1, "--- Select ---", $selected, "", 0, 0);
	exit();
}

if ($action == "load_drop_down_multiple")
{
	echo create_drop_down("cbo_location_name", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "");

	echo "****";

	echo create_drop_down("cbo_transport_company", 130, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name", "id,supplier_name", 1, "-- Select Transport --", $selected, "");

	echo "****";

	echo create_drop_down("cbo_forwarder", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select--", $selected, "", "0");//forwarding_agent_disable_1(this.value);

	echo "****";

	echo create_drop_down("cbo_forwarder_2", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select--", $selected, "", "0");//forwarding_agent_disable_2(this.value);
	exit();
}

if ($action == "load_all_drop_down")
{
	// location_td*transfer_com*forwarder_td*del_location_td*del_floor_td
	$dataEx = explode("_",$data);
	echo create_drop_down("cbo_location_name", 130, "select id,location_name from lib_location where company_id='$dataEx[0]' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "");

	echo "****";

	echo create_drop_down("cbo_transport_company", 130, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$dataEx[0]'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name", "id,supplier_name", 1, "-- Select Transport --", $selected, "");

	echo "****";

	echo create_drop_down("cbo_forwarder", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$dataEx[0]' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select--", $selected, "forwarding_agent_disable_1(this.value);", "0");

	echo "****";

	echo create_drop_down("cbo_delivery_location", 130, "select id,location_name from lib_location where company_id='$dataEx[1]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Delivery Location --", $selected, "load_drop_down( 'requires/garments_delivery_entry_controller', $dataEx[1]+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );");

	echo "****";

	echo create_drop_down("cbo_delivery_floor", 130, "select id,floor_name from lib_prod_floor where company_id='$dataEx[2]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "");

	exit();
}


if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "");
	exit();
}

if ($action == "load_drop_down_del_location") {
	echo create_drop_down("cbo_delivery_location", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Delivery Location --", $selected, "load_drop_down( 'requires/garments_delivery_entry_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );");
	exit();
}

if ($action == "load_drop_down_del_floor") {
	$data = explode('**', $data);
	echo create_drop_down("cbo_delivery_floor", 130, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "");
	exit();
}

if ($action == "load_drop_down_transport_com") {
	echo create_drop_down("cbo_transport_company", 130, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name", "id,supplier_name", 1, "-- Select Transport --", $selected, "");
	exit();
}

if ($action == "load_drop_down_forwarder") {
	echo create_drop_down("cbo_forwarder", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select--", $selected, "", "0");//forwarding_agent_disable_1(this.value); issue id = 18386
	exit();
}
if ($action == "load_drop_down_forwarder2") {
	echo create_drop_down("cbo_forwarder_2", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select--", $selected, "", "0");//forwarding_agent_disable_2(this.value); issue id = 18386
	exit();
}

if ($action == "load_drop_down_buyer")
{
	if ($data != 0) {
		echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		exit();
	} else {
		echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		exit();
	}
}

if ($action == "sys_surch_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);

	?>
	<script>
		var company_id = '<? echo $company_id; ?>';

		function js_set_value(str) {
			$("#hidden_delivery_id").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<th width="130">Company Name</th>
						<th width="120">Buyer Name</th>
						<th width="160">Transport Com.</th>
						<th width="160">Job No</th>
						<th width="100">IR/IB</th>
						<th width="100">Challan No</th>
						<th width="100">Order No</th>
						<th width="200">Ex-Factory Date Range</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
					</thead>
					<tr class="general">
						<td><? echo create_drop_down("cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'garments_delivery_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
						<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, ""); ?></td>
						<td><? echo create_drop_down("cbo_trans_com", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name", "id,supplier_name", 1, "-- Select --", $selected, "", 0); ?></td>
						<td><input type="text" style="width:90px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td>
						<td><input type="text" style="width:90px" class="text_boxes" name="txt_int_ref" id="txt_int_ref" /></td>
						<td><input type="text" style="width:90px" class="text_boxes" name="txt_challan_no" id="txt_challan_no" /></td>
						<td><input type="text" style="width:90px" class="text_boxes" name="txt_po_no" id="txt_po_no" /></td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td>
							<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_int_ref').value, 'create_delivery_search_list', 'search_div_delivery', 'garments_delivery_entry_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" colspan="9" valign="middle">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_delivery_id">
						</td>
					</tr>
				</table>
				<div id="search_div_delivery" style="margin-top:20px;"></div>
			</form>
		</div>
		<script type="text/javascript">
			$("#cbo_company_name").val(company_id);
		</script>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if ($action == "create_delivery_search_list")
{

	$ex_data = explode("_", $data);
	$trans_com = $ex_data[0];
	$txt_challan_no = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$po_no = str_replace("'", "", $ex_data[5]);
	$buyer = $ex_data[6];
	$job_no = $ex_data[7];
	$ir_ib_no = $ex_data[8];
	//echo $trans_com;die;
	// print_r($ex_data);die;

	if($txt_challan_no=="" && $po_no=="" && $job_no=="" && $ir_ib_no=="" && $txt_date_from=="" && $txt_date_to=="")
	{
		echo "<div style='color:red;font-size:20px;font-weight:bold;text-align:center;'>Please enter anyone of search field value.</div>";
		die();
	}
	if($txt_date_from !="" && $txt_date_to !="")
	{
		$tot_days = datediff('d',$txt_date_from,$txt_date_to);
		if($tot_days>93)// max 3 month
		{
			echo "<div style='color:red;font-size:16px;font-weight:bold;text-align:center;'>Invalid Date Range.</div>"; die;
		}
	}

	if (str_replace("'", "", $buyer) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") $buyerCond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else $buyerCond = "";
		} else $buyerCond = "";
	} else $buyerCond = " and a.buyer_id='$buyer'";

	$sql_cond = "";
	if ($txt_date_from != "" || $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= "and b.ex_factory_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		if ($db_type == 2 || $db_type == 1) {
			$sql_cond .= "and b.ex_factory_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
		}
	}

	if (trim($company) != 0) $sql_cond .= " and a.company_id='$company'";
	//if(trim($buyer)!=0) $sql_cond .= " and a.buyer_id='$buyer'";
	if (trim($txt_challan_no) != "") $sql_cond .= " and a.sys_number_prefix_num='$txt_challan_no'";
	if (trim($trans_com) != 0) $sql_cond .= " and a.transport_supplier='$trans_com'";
	if (trim($po_no) != "") {
		$po_no_id = return_field_value("id as po_id", "wo_po_break_down", "po_number='$po_no' and status_active=1", "po_id");
		$po_cond = "and b.po_break_down_id='$po_no_id'";
	} else $po_cond = "";

	$po_order_no = "and c.po_number like '%$po_no'";

	// if (trim($job_no) != "") {
	// 	$jon_no_id = return_field_value("id as po_id", "wo_po_break_down", "job_no_mst='$job_no' and status_active=1", "po_id");
	// 	$job_cond = "and b.po_break_down_id='$jon_no_id'";
	// } else $job_cond = "";
	   $job_cond = "and c.job_no_mst like '%$job_no'";
	   $ir_ib_no_cond = "and c.grouping like '%$ir_ib_no'";

	if ($db_type == 0)
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, year(a.insert_date) as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no,group_concat(b.po_break_down_id) as po_break_down_id,sum(b.ex_factory_qnty) as ex_factory_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=85 $sql_cond $po_order_no $buyerCond $job_cond
		group by  a.id, a.sys_number_prefix_num, year(a.insert_date), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	else
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY') as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no, listagg(CAST(b.po_break_down_id as VARCHAR(4000)),',') within group (order by b.po_break_down_id) as po_break_down_id,c.grouping
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b,wo_po_break_down c
		where a.id=b.delivery_mst_id and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=85 $sql_cond $po_order_no $buyerCond $job_cond $ir_ib_no_cond
		group by  a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY'), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no,c.grouping  order by a.id desc";
	}
	// echo $sql;die;
	$result = sql_select($sql);
	$all_po_arr = array();
	foreach ($result as $v)
	{
		$pid_arr = explode(",",$v[csf("po_break_down_id")]);
		foreach ($pid_arr as $r)
		{
			$all_po_arr[$r] = $r;
		}
	}

	// ============================= store data in gbl table ==============================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=71");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 71, 1, $all_po_arr, $empty_arr);//Po ID
	disconnect($con);

	$ids = implode(",", $all_po_arr);
	$all_po_cond = "";
	if ($db_type == 2 && count($all_po_arr) > 999) {
		$chnk = array_chunk($all_po_arr, 999);
		foreach ($chnk as $v) {
			$po_ids = implode(",", $v);
			if ($all_po_cond == "") $all_po_cond .= "  id in ($po_ids) ";
			else $all_po_cond .= " or   id in ($po_ids) ";
		}
	} else $all_po_cond = " id in($ids) ";

	// $po_status_arr = return_library_array("SELECT id, shiping_status from wo_po_break_down where $all_po_cond", "id", "shiping_status");

	/*$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
		}*/

	$order_sql = sql_select("SELECT a.id,a.job_no_mst, a.po_number,(a.po_quantity) as po_quantity,a.shiping_status,a.grouping  from wo_po_break_down a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1");
	foreach ($order_sql as $val)
	{
		$order_num_arr[$val[csf("id")]] = $val[csf("po_number")];
		$order_num_job_arr[$val[csf("id")]]["job_no_mst"] = $val[csf("job_no_mst")];
		$order_num_job_arr[$val[csf("id")]]["grouping"] = $val[csf("grouping")];
		$order_qnty_arr[$val[csf("id")]] += $val[csf("po_quantity")];
		$po_status_arr[$val[csf("id")]] += $val[csf("shiping_status")];
	}
	$all_po_cond2 = str_replace("","",$all_po_cond);


	$exfact_qty_arr = return_library_array("SELECT a.delivery_mst_id, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=1 and a.entry_form != 85 and a.status_active=1 and a.delivery_mst_id>0 group by a.delivery_mst_id", 'delivery_mst_id', 'ex_factory_qnty');

	$buyer_name_arr = return_library_array("select id, short_name from lib_buyer where status_active=1", 'id', 'short_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	$trans_com_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name", "id", "supplier_name");


	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=71");
	oci_commit($con);
	disconnect($con);
	?>
	<table cellspacing="0" width="1230" class="rpt_table" cellpadding="0" border="1" rules="all">
		<thead>
			<th width="30">SL</th>
			<th width="50">Sys Num</th>
			<th width="50">Year</th>
			<th width="80">Company</th>
			<th width="70">Buyer Name</th>
			<th width="70">Job No</th>
			<th width="70">IR/IB</th>
			<th width="100">Order No</th>
			<th width="155">Transport Company</th>
			<th width="50">Challan No</th>
			<th width="70">Delivery Date</th>
			<th width="120">Driver Name</th>
			<th width="90">Truck No</th>
			<th width="90">Lock No</th>
			<th width="80">Ex-fact Qty</th>
			<th >Ex-fact Status</th>
		</thead>
	</table>
	<div style="width:1230px; max-height:220px;overflow-y:scroll;">
		<table cellspacing="0" width="1212" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)  $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				// echo $order_num_job_arr[$row[csf("po_break_down_id")]]["job_no_mst"]."___".$row[csf("po_break_down_id")];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="50" align="center">
						<p><? echo $row[csf("sys_number_prefix_num")]; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $row[csf("delivery_year")]; ?></p>
					</td>
					<td width="80" align="center">
						<p><? echo $company_arr[$row[csf("company_id")]]; ?></p>
					</td>
					<td width="70">
						<p><? echo $buyer_name_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p>
					</td>
					<td width="70">
						<p><? echo $order_num_job_arr[$row[csf("po_break_down_id")]]["job_no_mst"]; ?>&nbsp;</p>
					</td>
					<td width="70">
						<p><? echo $order_num_job_arr[$row[csf("po_break_down_id")]]["grouping"]; ?>&nbsp;</p>
					</td>
					<td  width="100">
						<p>
							<?
							$po_id_arr = array_unique(explode(",", $row[csf("po_break_down_id")]));
							$all_po = "";
							foreach ($po_id_arr as $po_id) {
								if ($all_po == "") $all_po = $order_num_arr[$po_id];
								else $all_po .= ", " . $order_num_arr[$po_id];
							}
							echo $all_po;
							?>&nbsp;</p>
					</td>
					<td width="155" align="center">
						<p><? echo $trans_com_arr[$row[csf("transport_supplier")]]; ?>&nbsp;</p>
					</td>
					<td width="50" align="center">
						<p><? echo $row[csf("challan_no")]; ?>&nbsp;</p>
					</td>
					<td width="70" align="center">
						<p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p>
					</td>
					<td width="120">
						<p><? echo $row[csf("driver_name")]; ?>&nbsp;</p>
					</td>
					<td width="90">
						<p><? echo $row[csf("truck_no")]; ?>&nbsp;</p>
					</td>
					<td width="90">
						<p><? echo $row[csf("lock_no")]; ?>&nbsp;</p>
					</td>
					<? if ($db_type == 1) { ?>
						<td width="80" align="right">
							<p><? echo number_format($row[csf("ex_factory_qnty")], 0, "", ""); ?></p>
						</td>
					<? } else { ?>
						<td width="80" align="right">
							<p><? echo number_format($exfact_qty_arr[$row[csf("id")]], 0, "", ""); ?></p>
						</td>
					<? } ?>

					<td>
						<p>
							<?
							$po_id_arr = array_unique(explode(",", $row[csf("po_break_down_id")]));
							$arrs = $po_id_arr;
							$shiping_status_val = "";
							foreach ($arrs as $vals) {
								if ($shiping_status_val == "") {
									$shiping_status_val .= $shipment_status[$po_status_arr[$vals]];
								} else
									$shiping_status_val .= ',' . $shipment_status[$po_status_arr[$vals]];
							}
							echo $shiping_status_val;
							?>&nbsp;
						</p>
					</td>

				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
<?
	exit();
}

if ($action == "populate_muster_from_date")
{
	$sql_mst = sql_select("SELECT id, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no,dl_no,destination_place,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,mobile_no, do_no, gp_no,source,escot_mobile,escot_name,cbm
	from  pro_ex_factory_delivery_mst where id=$data and entry_form <> 85");

	/*	echo "select id, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no,destination_place,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,mobile_no, do_no, gp_no
		from  pro_ex_factory_delivery_mst where id=$data and entry_form!=85";*/

	foreach ($sql_mst as $row)
	{
		$company_id = $row[csf('company_id')];
		echo "load_drop_down_multiple( 'requires/garments_delivery_entry_controller',".$company_id."+'_'+" . $row[csf("delivery_company_id")] . "+'_'+" . $row[csf("delivery_location_id")] . ", 'load_all_drop_down', 'location_td*transfer_com*forwarder_td*del_location_td*del_floor_td' );\n";

		echo "$('#cbm').val('" . $row[csf('cbm')] . "');\n";
		echo "$('#txt_system_no').val('" . $row[csf('sys_number')] . "');\n";
		echo "$('#txt_system_id').val('" . $row[csf('id')] . "');\n";
		echo "$('#cbo_company_name').val(" . $row[csf('company_id')] . ");\n";
		echo "$('#cbo_location_name').val(" . $row[csf('location_id')] . ");\n";
		echo "$('#txt_challan_no').val('" . $row[csf('challan_no')] . "');\n";
		echo "$('#cbo_transport_company').val(" . $row[csf('transport_supplier')] . ");\n";
		echo "$('#txt_ex_factory_date').val('" . change_date_format($row[csf('delivery_date')]) . "');\n";
		echo "$('#txt_truck_no').val('" . $row[csf('truck_no')] . "');\n";
		echo "$('#txt_lock_no').val('" . $row[csf('lock_no')] . "');\n";
		echo "$('#txt_driver_name').val('" . $row[csf('driver_name')] . "');\n";
		echo "$('#txt_dl_no').val('" . $row[csf('dl_no')] . "');\n";
		echo "$('#txt_mobile_no').val('" . $row[csf('mobile_no')] . "');\n";
		echo "$('#txt_do_no').val('" . $row[csf('do_no')] . "');\n";
		if ($row[csf('gp_no')] != "") {
			echo "$('#txt_gp_no').val('" . $row[csf('gp_no')] . "');\n";
		} else {
			$gp_sys_number = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='".$row[csf('sys_number')]."' and basis=12 and status_active=1 and is_deleted=0");
			echo "$('#txt_gp_no').val('" . $gp_sys_number . "');\n";
		}
		echo "$('#txt_destination').val('" . $row[csf('destination_place')] . "');\n";
		echo "$('#cbo_forwarder').val(" . $row[csf('forwarder')] . ");\n";
		echo "$('#cbo_forwarder_2').val(" . $row[csf('forwarder_2')] . ");\n";
		echo "$('#cbo_source').val(" . $row[csf('source')] . ");\n";

		if ($row[csf('source')] == 1) {
			echo "load_drop_down( 'requires/garments_delivery_entry_controller', '1**$company_id', 'load_drop_delivery_company', 'dev_company_td' );\n";
		} else if ($row[csf('source')] == 3) {
			echo "load_drop_down( 'requires/garments_delivery_entry_controller', '3**$company_id', 'load_drop_delivery_company', 'dev_company_td' );\n";
		}
		echo "$('#cbo_del_company').val(" . $row[csf('delivery_company_id')] . ");\n";

		echo "$('#txt_attention').val('" . $row[csf('attention')] . "');\n";
		echo "$('#txt_remarks').val('" . $row[csf('remarks')] . "');\n";
		echo "$('#txt_escot_name').val('" . $row[csf('escot_name')] . "');\n";
		echo "$('#txt_escot_mobile').val('" . $row[csf('escot_mobile')] . "');\n";

		echo "$('#cbo_delivery_location').val(" . $row[csf('delivery_location_id')] . ");\n";
		echo "$('#cbo_delivery_floor').val(" . $row[csf('delivery_floor_id')] . ");\n";

		echo "$('#check_posted_in_accounce').val('');\n";

		$sql_is_posted_account = sql_select("select id from pro_ex_factory_mst where delivery_mst_id='".$row[csf('id')]."' and is_posted_account=1 and status_active=1 and is_deleted=0");
		if ($sql_is_posted_account[0][csf('id')] != "") {
			echo "$('#check_posted_in_accounce').val(1);\n";
		}
		//echo "set_button_status(0, permission, 'fnc_exFactory_entry',1,0);\n";
	}
	exit();
}


if ($action == "show_dtls_listview_mst")
{
	echo load_html_head_contents("Popup Info", "../", 1, 1, $unicode);
	$country_short_array = return_library_array("select id,short_name from lib_country", "id", "short_name");
	?>
	<div style="width:1650px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
			<thead>
				<th style="word-break: break-all;word-wrap: break-word;" width="20">SL</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="120">Item Name</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="110">Country</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="60">Country Short Name</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="120">Style</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="120">IR/IB</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="100">Order No</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="80">Order Qty.</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="90">Actual PO</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="90">Destination</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="90">Net Weight</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="90">Gross Weight</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="60">Ex-Fact. Date</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="60">Ex-Fact. Qty</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="90">Invoice No</th>
				<th style="word-break: break-all;word-wrap: break-word;" width="90">LC/SC No</th>
				<th width="70" style="word-break: break-all;word-wrap: break-word;" align="center">Challan No</th>
				<th style="word-break: break-all;word-wrap: break-word;" align="center">Delivery Status</th>
			</thead>
		</table>
	</div>
	<div style="width:1650px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="details_table">
			<?
			$i = 1;
			$total_production_qnty = 0;


			$sqlResult = sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,a.invoice_no,b.challan_no,a.shiping_status,b.cbm,a.destinatin, a.net_weight,a.gross_weight from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.delivery_mst_id=$data and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 order by id");
			$po_id_arr = array(); $mst_id_arr = array();
			foreach ($sqlResult as $row)
			{
				$po_id_arr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
				$mst_id_arr[$row[csf('id')]] = $row[csf('id')];
				$invoice_id_arr[$row[csf('invoice_no')]] = $row[csf('invoice_no')];
				$lc_sc_no_id_arr[$row[csf('lc_sc_no')]] = $row[csf('lc_sc_no')];
			}

			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4) and ENTRY_FORM=71");
			oci_commit($con);
			disconnect($con);

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 71, 1, $po_id_arr, $empty_arr);//PO ID
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 71, 2, $mst_id_arr, $empty_arr);//mst ID
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 71, 3, $invoice_id_arr, $empty_arr);//invoice ID
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 71, 4, $lc_sc_no_id_arr, $empty_arr);//lc/sc ID



			$mst_ids = implode(",", $mst_id_arr);
			$allPoId = implode(",", $po_id_arr);
			$style_sql = "SELECT a.style_ref_no,b.id,b.po_number,b.po_quantity,b.grouping from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=1";
			// echo $style_sql;die;
			$style_sql_res = sql_select($style_sql);
			$style_ref_arr = array();
			foreach ($style_sql_res as $val) {
				$style_ref_arr[$val[csf('id')]] = $val[csf('style_ref_no')];
				$order_num_arr[$val[csf("id")]] = $val[csf("po_number")];
				$order_qnty_arr[$val[csf("id")]] += $val[csf("po_quantity")];
				$int_ref_arr[$val[csf('id')]] = $val[csf('grouping')];
			}
			// echo"<pre>";print_r($int_ref_arr);die;

			$actual_po_library = return_library_array("SELECT a.id, a.acc_po_no from wo_po_acc_po_info a,GBL_TEMP_ENGINE tmp where  a.po_break_down_id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=1", 'id', 'acc_po_no');

			$actual_po = sql_select("SELECT a.mst_id, a.actual_po_id from pro_ex_factory_actual_po_details a,GBL_TEMP_ENGINE tmp where a.mst_id = tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=2 and status_Active=1 and is_deleted=0");
			$acc_po_arr = array();
			foreach ($actual_po as $val)
			{
				$acc_po_arr[$val[csf('mst_id')]]=$actual_po_library[$val[csf('actual_po_id')]];
			}
			unset($actual_po);

			$sqlEx = sql_select("SELECT a.id,a.invoice_no,a.is_lc,a.lc_sc_id from com_export_invoice_ship_mst a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=3 and status_active=1");
			foreach ($sqlEx as $row)
			{
				$invoice_data_arr[$row[csf("id")]]["id"] = $row[csf("id")];
				$invoice_data_arr[$row[csf("id")]]["invoice_no"] = $row[csf("invoice_no")];
				$invoice_data_arr[$row[csf("id")]]["is_lc"] = $row[csf("is_lc")];
				$invoice_data_arr[$row[csf("id")]]["lc_sc_id"] = $row[csf("lc_sc_id")];
			}
			// echo "<pre>";print_r($invoice_data_arr);die;
			$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");
			$lc_num_arr = return_library_array("SELECT a.id, a.export_lc_no from com_export_lc a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=4 and status_active=1 and is_deleted=0", "id", "export_lc_no");

			$sc_num_arr = return_library_array("SELECT a.id, a.contract_no from com_sales_contract a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=71  and tmp.user_id=$user_id and tmp.ref_from=4 and status_active=1 and is_deleted=0", "id", "contract_no");
			// echo "<pre>";print_r($lc_num_arr);die;
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4) and ENTRY_FORM=71");
			oci_commit($con);
			disconnect($con);


			foreach ($sqlResult as $selectResult)
			{
				
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$total_production_qnty += $selectResult[csf('ex_factory_qnty')];
				if ($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"] == 1) //  lc
					$lc_sc = $lc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];
				else if ($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"] == 2)
					$lc_sc = $sc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];

				$invoiceNo = $invoice_data_arr[$selectResult[csf("invoice_no")]]["invoice_no"];
				//$order_num_arr
				// echo $invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"];die;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/garments_delivery_entry_controller');get_php_form_data('<? echo $selectResult[csf('po_break_down_id')]; ?>+**+<? echo $selectResult[csf('item_number_id')]; ?>+**+<? echo $selectResult[csf('country_id')]; ?>'+'**'+$('#hidden_preceding_process').val()+'**'+$('#txt_mst_id').val()+'**1'+'**'+$('#sewing_production_variable').val()+'**'+$('#variable_is_controll').val()+'**'+$('#txt_country_ship_date').val()+'**'+$('#txt_pack_type').val(),'populate_data_from_search_popup','requires/garments_delivery_entry_controller');">

					<td style="word-break: break-all;word-wrap: break-word;" width="20" align="center"><? echo $i; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;" width="120" align="center">
						<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
					</td>

					<td style="word-break: break-all;word-wrap: break-word;" width="110" align="center">
						<p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo $country_short_array[$selectResult[csf('country_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="120" align="left">
						<p><? echo $style_ref_arr[$selectResult[csf('po_break_down_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="120" align="left">
						<p><?=$int_ref_arr[$selectResult[csf('po_break_down_id')]] ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
						<p><? echo $order_num_arr[$selectResult[csf('po_break_down_id')]]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="80" align="center">
						<p><? echo $order_qnty_arr[$selectResult[csf('po_break_down_id')]]; ?></p>
					</td>
                    <td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $acc_po_arr[$selectResult[csf('id')]]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $selectResult[csf('destinatin')]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $selectResult[csf('net_weight')]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $selectResult[csf('gross_weight')]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo $selectResult[csf('ex_factory_qnty')]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $invoiceNo; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $lc_sc; ?>&nbsp;</p>
					</td>
					<td width="70" style="word-break: break-all;word-wrap: break-word;" align="center">
						<p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" align="center">
						<p><? echo $shipment_status[$selectResult[csf('shiping_status')]]; ?>&nbsp;</p>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
<?
	exit();
}


if ($action == "lcsc_popup")
{
	extract($_REQUEST);
	$order_id = str_replace("'", "", $order_id);
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	?>
	<script>
		function js_set_value(str) {
			$("#lc_id_no").val(str);
			parent.emailwindow.hide();
			//parent.emailwindow.hide();
		}
	</script>

	<?
	if ($db_type == 0) {
		$sql = "SELECT a.id, a.invoice_no, a.invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, group_concat(b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=$order_id group by a.id order by a.invoice_no";
	} else {
		$sql = "SELECT a.id, a.invoice_no, max(a.invoice_date) as invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=$order_id group by a.id,a.invoice_no,a.buyer_id,a.lc_sc_id,a.benificiary_id,a.is_lc order by a.invoice_no";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	$invoice_id_array = array();
	$lcsc_id_array = array();
	foreach ($result as $val)
	{
		$invoice_id_array[$val[csf('id')]] = $val[csf('id')];
		$lcsc_id_array[$val[csf('lc_sc_id')]] = $val[csf('lc_sc_id')];
	}
	$invoiceId = implode(",", $invoice_id_array);
	$lcscId = implode(",", $lcsc_id_array);

	// =============================================
	$lc_num_arr = return_library_array("SELECT a.id, a.export_lc_no from com_export_lc a where  status_active=1 and is_deleted=0 and id in($lcscId)", "id", "export_lc_no");

	$sc_num_arr = return_library_array("SELECT a.id, a.contract_no from com_sales_contract a where  status_active=1 and is_deleted=0 and id in($lcscId)", "id", "contract_no");

	//===================== getting exfact qty ====================
	$sql_exfact = "SELECT c.invoice_no,c.lc_sc_no, d.production_qnty as ex_fact_qty from pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e where c.id=d.mst_id and e.id=c.delivery_mst_id and c.invoice_no in($invoiceId) and c.lc_sc_no in($lcscId) and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
	// echo $sql_exfact;die;
	$sql_exfact_res = sql_select($sql_exfact);
	$exfact_qty_array = array();
	foreach ($sql_exfact_res as $val) {
		$exfact_qty_array[$val['LC_SC_NO']][$val['INVOICE_NO']] += $val['EX_FACT_QTY'];
	}
	// print_r($exfact_qty_array);
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id=$order_id", 'id', 'po_number');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	if ($db_type == 0)
	{
		$po_num_arr = return_library_array("select id, group_concat(distinct(po_number)) as po_number from wo_po_break_down where id=$order_id and  status_active in(1,2,3) and is_deleted=0 ", "id", "po_number");
	}
	else
	{
		$po_num_arr = return_library_array("select id, listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as po_number from wo_po_break_down where id=$order_id and status_active in(1,2,3) and is_deleted=0 group by id", "id", "po_number");
	}
	//echo create_list_view("list_view","Invoice NO,Invoice Date,Buyer,LC/SC No,Order Qunty,Company","130,100,170,100,100,150","850","250",1,$sql,"js_set_value","invoice_no,lc_sc_no","",1,"0,0,buyer_id,0,0,benificiary_id",$printed_array,"invoice_no,invoice_date,buyer_id,lc_sc_no,order_quantity,benificiary_id","requires/garments_delivery_entry_controller","setFilterGrid('tbl_po_list',1)","0,0,0,0,0,1","","");

	//===================================== ACCTUAL PO DATA ==============================================
	$acc_po_sql = "SELECT a.acc_po_no,a.acc_po_qty,b.invoice_id from wo_po_acc_po_info a,export_invoice_act_po b where a.id=b.wo_po_act_id and b.invoice_id in(6498,6451) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $acc_po_sql; die;
	$acc_po_sql_res = sql_select($acc_po_sql);
	$acc_po_data = array();
	foreach ($acc_po_sql_res as $v) 
	{ 
		
		$acc_po_data[$v['INVOICE_ID']]['ACC_PO_NO'] 	 = $v['ACC_PO_NO'];
		$acc_po_data[$v['INVOICE_ID']]['ACC_PO_QTY'] 	+= $v['ACC_PO_QTY'];
	}
	// echo print_r($acc_po_data); die;
	?>
	<div style="width:1120px; margin-top:10px">
		<table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="80">Invoice No</th>
				<th width="75">Invoice Date</th>
				<th width="120">Buyer</th>
				<th width="150">LC/SC No</th>
				<th width="120">Order No</th>
				<th width="70">Order Qty</th>
				<th width="70">Invoice Qty</th>
				<th width="70">Att. Invoice Qty</th>
				<th width="70">Actul PO No</th>
				<th width="70">Actual PO Qty</th>
				<th width="70">Balance</th>
				<th width="">Company Name</th>
			</thead>
		</table>
	</div>

	<? if (count($result) == 0) {
		echo "<div style='text-align:center;color:red;font-size:18px;'>Invoice does not exist ! Please make sure invoice for this po number <b>{ $po_arr[$order_id] }</b>.</div>";
		die();
	} ?>

	<div style="width:1120px; max-height:320px;overflow-y:scroll;">

		<table cellspacing="0" width="1102" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)  $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$po_number = $po_num_arr[$row[csf("po_id")]];


				if ($row[csf("is_lc")] == 1) //  lc
				{
					$lc_sc = $lc_num_arr[$row[csf('lc_sc_id')]];
				} else {
					$lc_sc = $sc_num_arr[$row[csf('lc_sc_id')]];
				}
				$attInvQty = $exfact_qty_array[$row[csf("lc_sc_id")]][$row[csf("id")]];
				$balanceQty = $row[csf("order_quantity")] - $attInvQty;
				if ($balanceQty <= 0) {
					$bgcolor = "red";
				}

			?>
				<input type="hidden" id="lc_id_no" name="lc_id_no">
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>**<? echo $row[csf('invoice_no')]; ?>**<? echo $row[csf('lc_sc_id')]; ?>**<? echo $lc_sc; ?>');">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="80" align="left">
						<p><? echo $row[csf("invoice_no")]; ?></p>
					</td>
					<td width="75" align="center"><? echo change_date_format($row[csf("invoice_date")]); ?></td>
					<td width="120">
						<p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p>
					</td>
					<td width="150">
						<p><? echo $lc_sc; ?></p>
					</td>
					<td width="120">
						<p><? echo $po_number; ?></p>
					</td>
					<td width="70" align="right"><? echo $row[csf("order_quantity")]; ?> </td>
					<td width="70" align="right"><? echo $row[csf("order_quantity")]; ?> </td>
					<td width="70" align="right"><? echo $attInvQty; ?> </td>
					<td width="70" align="right"><? echo $acc_po_data[$row['ID']]['ACC_PO_NO']; ?> </td> 
					<td width="70" align="right"><? echo $acc_po_data[$row['ID']]['ACC_PO_QTY']; ?> </td> 
					<td width="70" align="right"><? echo $balanceQty; ?> </td>
					<td width="">
						<p><? echo $company_arr[$row[csf("benificiary_id")]]; ?></p>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
		<script>
			setFilterGrid("tbl_invoice_list", -1);
		</script>
	</div>
	<?
	exit();
}

if ($action == "order_popup") {
	extract($_REQUEST);
	if($hidden_variable_cntl=="") $hidden_variable_cntl=0;
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
?>
	<script>
		$(document).ready(function(e) {
			$("#txt_search_common").focus();
		});

		function search_populate(str) {
			if (str == 0) {
				document.getElementById('search_by_th_up').innerHTML = "Order No";
				document.getElementById('search_by_td').innerHTML = '<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			} else if (str == 1) {
				document.getElementById('search_by_th_up').innerHTML = "Style Ref. Number";
				document.getElementById('search_by_td').innerHTML = '<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			} else if (str == 3) {
				document.getElementById('search_by_th_up').innerHTML = "Actual PO Number";
				document.getElementById('search_by_td').innerHTML = '<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			} else if (str == 4) {
				document.getElementById('search_by_th_up').innerHTML = "Internal Ref. No";
				document.getElementById('search_by_td').innerHTML = '<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			} else if (str == 5) {
				document.getElementById('search_by_th_up').innerHTML = "Job No";
				document.getElementById('search_by_td').innerHTML = '<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			} else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?

				$buyer_arr = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", 'id', 'buyer_name');


				foreach ($buyer_arr as $key => $val) {
					echo "buyer_name += '<option value=\"$key\">" . ($val) . "</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML = "Select Buyer Name";
				document.getElementById('search_by_td').innerHTML = '<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">' + buyer_name + '</select>';

				document.getElementById('txt_search_common').value = '<? echo $buyer_id; ?>';
				<?
				if ($buyer_id != 0) {
				?>
					document.getElementById('txt_search_common').disabled = true;
				<? } ?>
			}
		}

		function js_set_value(id, item_id, po_qnty, plan_qnty, country_id,ship_date,pack_type)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
			$("#hidden_ship_date").val(ship_date);
			$("#hidden_pack_type").val(pack_type);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="9"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, "", 1, "-- Select --", $selected, "", 0); ?></th>
						</tr>
						<th width="130">Search By</th>
						<th width="240" align="center" id="search_by_th_up">Enter Order Number</th>
						<th width="200">Date Range</th>
						<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
					</thead>
					<tr class="general">
						<td>
							<?
							$searchby_arr = array(0 => "Order No", 1 => "Style Ref. Number", 2 => "Buyer Name", 3 => "Actual PO No", 4 => "Internal Ref. No", 5 => "Job No");
							echo create_drop_down("txt_search_by", 130, $searchby_arr, "", 1, "-- Select Sample --", $selected, "search_populate(this.value)", 0);
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td>
							<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_<? echo $hidden_preceding_process; ?>_'+<? echo $buyer_id; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'garments_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center" valign="middle">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_mst_id">
							<input type="hidden" id="hidden_grmtItem_id">
							<input type="hidden" id="hidden_po_qnty">
							<input type="hidden" id="hidden_country_id">
							<input type="hidden" id="hidden_ship_date">
							<input type="hidden" id="hidden_pack_type">
						</td>
					</tr>
				</table>
				<div style="font-weight: bold;font-size: 14px;color: red;padding: 5px 0 0 0;text-align: center;width: 100%">N.B : Buyer mixed not allow</div>
				<div style="margin-top:10px" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_po_search_list_view")
{
	$ex_data = explode("_", $data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$garments_nature = $ex_data[5];
	$preceding_process = $ex_data[6];
	$buyer_id = $ex_data[8];
	$year = $ex_data[9];
	$search_type = $ex_data[10];
	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	// else if ($preceding_process == 91) $qty_source = 82; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
	$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");

	$sql_cond = "";
	if ($search_type == 4 || $search_type == 0) {
		if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 0)
				$sql_cond = " and b.po_number like '%" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 1)
				$sql_cond = " and a.style_ref_no like '%" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if (trim($txt_search_by) == 3)
				$sql_cond = " and b.po_number_acc like '%" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 4)
				$sql_cond = " and b.grouping like '%" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 5)
				$sql_cond = " and a.job_no like '%" . trim($txt_search_common) . "%'";
		}
	} else if ($search_type == 1) {
		if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 0)
				$sql_cond = " and b.po_number ='$txt_search_common'";
			else if (trim($txt_search_by) == 1)
				$sql_cond = " and a.style_ref_no ='$txt_search_common'";
			else if (trim($txt_search_by) == 2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if (trim($txt_search_by) == 3)
				$sql_cond = " and b.po_number_acc='$txt_search_common'";
			else if (trim($txt_search_by) == 4)
				$sql_cond = " and b.grouping='$txt_search_common'";
			else if (trim($txt_search_by) == 5)
				$sql_cond = " and a.job_no='$txt_search_common'";
		}
	} else if ($search_type == 2) {
		if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 0)
				$sql_cond = " and b.po_number like '" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 1)
				$sql_cond = " and a.style_ref_no like '" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if (trim($txt_search_by) == 3)
				$sql_cond = " and b.po_number_acc like '" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 4)
				$sql_cond = " and b.grouping like '" . trim($txt_search_common) . "%'";
			else if (trim($txt_search_by) == 5)
				$sql_cond = " and a.job_no like '" . trim($txt_search_common) . "%'";
		}
	} else if ($search_type == 3) {
		if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 0)
				$sql_cond = " and b.po_number like '%" . trim($txt_search_common) . "'";
			else if (trim($txt_search_by) == 1)
				$sql_cond = " and a.style_ref_no like '%" . trim($txt_search_common) . "'";
			else if (trim($txt_search_by) == 2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if (trim($txt_search_by) == 3)
				$sql_cond = " and b.po_number_acc like '%" . trim($txt_search_common) . "'";
			else if (trim($txt_search_by) == 4)
				$sql_cond = " and b.grouping like '%" . trim($txt_search_common) . "'";
			else if (trim($txt_search_by) == 5)
				$sql_cond = " and a.job_no like '%" . trim($txt_search_common) . "'";
		}
	}
	// if(trim($txt_search_common)!="")
	// {
	// 	if(trim($txt_search_by)==0)
	// 	{
	// 		$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
	// 	}
	// 	else if(trim($txt_search_by)==1)
	// 	{
	// 		$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
	// 	}
	// 	else if(trim($txt_search_by)==2)
	// 	{
	// 		$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
	// 	}
	// 	else if(trim($txt_search_by)==3)
	// 	{
	// 		// $sql_cond = " and b.po_number_acc like '%".trim($txt_search_common)."%'";
	// 		$acc_po_arr=return_library_array( "select a.po_number, a.po_number as acc_po from wo_po_break_down a, wo_po_acc_po_info b where a.id=b.po_break_down_id and b.acc_po_no ='$txt_search_common' ",'po_number','acc_po');
	// 		$po_numbers = "'".implode("','", $acc_po_arr)."'";
	// 		$sql_cond = " and b.po_number in($po_numbers)";
	// 		$sql_cond .= " and b.po_number_acc is not null";
	// 	}
	// 	else if(trim($txt_search_by)==4)
	// 	{
	// 		$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
	// 	}
	// }

	/*if(str_replace("'","",$buyer_id)==0)
	{*/
	if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") $buyerCond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		else $buyerCond = "";
	} else $buyerCond = "";
	/*}
	else $buyerCond=" and a.buyer_name='$buyer_id'";*/

	if (trim($txt_search_by) != 2 && $buyer_id != 0) {
		$sql_cond .= " and a.buyer_name=trim('$buyer_id')";
	}
	if ($txt_date_from != "" || $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and b.shipment_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		if ($db_type == 2 || $db_type == 1) {
			$sql_cond .= " and b.shipment_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
		}
	}
	$qty_source_cond = "";
	if ($qty_source != 0) {
		$qty_source_cond = "and b.id in(select po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}

	if (trim($company) != "") $sql_cond .= " and a.company_name='$company'";
	if ($year != 0) {
		if ($db_type == 0) {
			$sql_shipment_year_cond = " and YEAR(b.shipment_date)=$year";
		}
		if ($db_type == 2) {
			$sql_shipment_year_cond = " and to_char(b.shipment_date,'YYYY')=$year";
		}
	}

	$is_projected_po_allow = return_field_value("production_entry", "variable_settings_production", "variable_list=58 and company_name=$company");
	$projected_po_cond = ($is_projected_po_allow == 2) ? " and b.is_confirmed=1" : "";

	// =========================== Approval Necessity Setup ========================

	$sql_app_res=sql_select("select approval_need, allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company and b.page_id in(25,37) and b.validate_page=1 and a.status_active=1 and b.status_active=1 order by a.setup_date desc fetch first 1 rows only");
	foreach ($sql_app_res as $row)
	{
		$approval_need=$row[csf('approval_need')];
		$allow_partial=$row[csf('allow_partial')];
	}
	//$is_approval_need = return_field_value("approval_need", "APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b", "a.id=b.mst_id and a.COMPANY_ID=$company and b.page_id in(25,37) and b.VALIDATE_PAGE=1 and a.status_active=1 and b.status_active=1 ORDER BY a.SETUP_DATE desc FETCH FIRST 1 ROWS ONLY");
	$approval_cond="";
	if ($approval_need==1) $approval_cond=" and c.approved in(1)";
	if ($allow_partial==1) $approval_cond=" and c.approved in(1,3)";

	if($approval_need==1 || $allow_partial==1)
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut, b.grouping
		from wo_po_details_master a, wo_po_break_down_vw b ,WO_PRE_COST_MST c
		where
		a.job_no = b.job_no_mst and a.id=c.job_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $approval_cond  $buyerCond $qty_source_cond $sql_shipment_year_cond___ $projected_po_cond order by b.shipment_date DESC";
	}
	else
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut, b.grouping
		from wo_po_details_master a, wo_po_break_down_vw b
		where
		a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond  $buyerCond $qty_source_cond $sql_shipment_year_cond___ $projected_po_cond order by b.shipment_date DESC";
	}
	//echo $sql;

	//echo $sql;
	$result = sql_select($sql);
	if(count($result)==0)
	{
		?>
		<div class="alert alert-danger">Data not found or please check precosting approve.</div>
		<?
		die;
	}
	$poId_arr = array();
	foreach ($result as $key => $val) {
		$poId_arr[$val[csf('id')]] = $val[csf('id')];
	}

	if (count($poId_arr) > 0) {
		$poIds = implode(",", $poId_arr);
		if (count($poId_arr) > 999 && $db_type == 2) {
			$po_chunk = array_chunk($poId_arr, 999);
			$po_cond = "";
			foreach ($po_chunk as $vals) {
				$imp_ids = implode(",", $vals);
				if ($po_cond == "") $po_cond .= " and (po_break_down_id in ($imp_ids) ";
				else $po_cond .= " or po_break_down_id in ($imp_ids) ";
			}
			$po_cond .= " )";
		} else $po_cond = " and po_break_down_id in($poIds) ";
	} else $po_cond = "";

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_cond group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_cond group by po_break_down_id",'po_break_down_id','country');
	}*/

	$po_country_data_arr = array();
	$po_country_arr = array();
	$poCountryData = sql_select("SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,pack_type,country_ship_date from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_cond group by po_break_down_id, item_number_id, country_id,pack_type,country_ship_date");

	foreach ($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['po_qnty'] = $row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['plan_cut_qnty'] = $row[csf('plan_cut_qnty')];
		if ($po_country_arr[$row[csf("po_break_down_id")]] == "") {
			$po_country_arr[$row[csf("po_break_down_id")]] .= $row[csf("country_id")];
		} else {
			$po_country_arr[$row[csf("po_break_down_id")]] .= ',' . $row[csf("country_id")];
		}
	}
	// echo "<pre>";print_r($po_country_data_arr);die;

	$total_ex_fac_data_arr = array();
	$total_ex_fac_arr = sql_select("SELECT po_break_down_id, item_number_id, country_id,pack_type,country_ship_date, sum( case when entry_form<>85 then ex_factory_qnty else 0 end ) -sum(case when ex_factory_qnty>0 and entry_form=85 then ex_factory_qnty  else 0 end) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond group by po_break_down_id, item_number_id, country_id,pack_type,country_ship_date");
	foreach ($total_ex_fac_arr as $row)
	{
		$total_ex_fac_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]] = $row[csf('ex_factory_qnty')];
	}
?>
	<div style="width:1065px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" align="left">
			<thead>
				<th width="30">SL</th>
				<th width="110">Company Name</th>
				<th width="70">Job No</th>
				<th width="100">Order No</th>
				<th width="90">Acc.Order No</th>
				<th width="60">Buyer</th>
				<th width="120">Style</th>
				<th width="110">Item</th>
				<th width="70">Internal Ref. No</th>
				<th width="90">Country</th>
				<th width="65">Country Ship Date</th>
				<th width="50">Pack Type</th>
				<th width="60">Order Qty</th>
				<th width="60">Total Ex-factory Qty</th>
				<th width="60">Balance</th>
			</thead>
		</table>
	</div>
	<div style="width:1065px; max-height:240px;overflow-y:auto;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_po_list" align="left">
			<?
			$i = 1;
			foreach ($result as $row)
			{
				$exp_grmts_item = explode("__", $row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty = "";
				$grmts_item = "";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country = array_unique(explode(",", $po_country_arr[$row[csf("id")]]));
				// print_r($country);
				$numOfCountry = count($country);

				for ($k = 0; $k < $numOfItem; $k++)
				{
					if ($row["total_set_qnty"] > 1)
					{
						$grmts_item_qty = explode("_", $exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
					else
					{
						$grmts_item_qty = explode("_", $exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach ($country as $country_id)
					{
						foreach ($po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id] as $coun_ship_date=>$coun_ship_date_data)
						{
							foreach ($coun_ship_date_data as $pack_type=>$pack_data)
							{
								if ($i % 2 == 0)  $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";

								//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
								$po_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
								$plan_cut_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")]; ?>,'<? echo $grmts_item; ?>','<? echo $po_qnty; ?>','<? echo $plan_cut_qnty; ?>','<? echo $country_id; ?>','<? echo $coun_ship_date; ?>','<? echo $pack_type; ?>');">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="110"><p><?= $company_arr[$row[csf("company_name")]]; ?></p> </td>
									<td width="70" align="center"><? echo $row[csf("job_no")]; ?></td>
									<td width="100"><p><? echo $row[csf("po_number")]; ?></p></td>
									<td width="90"><p><?= $row[csf("po_number_acc")]; ?></p></td>
									<td width="60"><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
									<td width="120"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
									<td width="110"><p><? echo $garments_item[$grmts_item]; ?></p></td>
									<td width="70"><p><? echo $row[csf("grouping")]; ?></p></td>
									<td width="90" title="Country ID = <? echo $country_id; ?>"><? echo $country_library[$country_id]; ?>&nbsp;</td>
									<td width="65" align="center"><? echo change_date_format($row[csf("shipment_date")]); ?></td>
									<td  width="50"><?=$pack_type; ?> </td>
									<td width="60" align="right"><? echo $po_qnty; //$po_qnty*$set_qty;
																	?>&nbsp;</td>
									<td width="60" align="right"><?= $total_cut_qty = $total_ex_fac_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]; ?>&nbsp;</td>
									<td width="60" align="right"><? $balance = $po_qnty - $total_cut_qty;
																	echo $balance; ?>&nbsp;</td>
								</tr>
								<?
								$i++;
							}
						}
					}
				}
			}
			?>
		</table>
	</div>
<?
	exit();
}

if ($action == "populate_data_from_search_popup")
{
	$dataArr = explode("**", $data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$ex_mst_id = $dataArr[4];
	$source_type = $dataArr[5];
	$sewing_production_variable = $dataArr[6];
	$preceding_process = $dataArr[3];
	$is_control = $dataArr[7];
	$ship_date = $dataArr[8];
	$pack_type = $dataArr[9];
	if($ship_date=="")
	{
		$ship_date=return_field_value("country_ship_date ","WO_PO_COLOR_SIZE_BREAKDOWN ","po_break_down_id=$po_id and item_number_id=$item_id and country_id=$country_id and status_active=1 and is_deleted=0","country_ship_date");
	}

	$ship_date = ($ship_date!="") ? date('d-M-Y',strtotime(str_replace("'","",$ship_date))) : "";

	if ($source_type == 2) echo "$('#txt_mst_id').val('0');\n";
	$conds = "";
	if ($ex_mst_id) $conds .= " and a.id<>$ex_mst_id ";
	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 91; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
	/* if ($is_control != 1) {
		$qty_source = 0;
	} */
	$res = sql_select("SELECT a.id,a.job_id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.shipment_date,b.company_name   from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.id=$po_id");

	$com_id = $res[0]['COMPANY_NAME'];
	$invoice_qty_source = return_field_value("export_invoice_qty_source", "variable_settings_commercial", "company_name=$com_id and variable_list=26 and status_active=1");
	$job_id = $res[0]['JOB_ID'];
	if($invoice_qty_source==0 || $invoice_qty_source=="" || $invoice_qty_source==1)
	{
		$costing_per_sql=sql_select("SELECT COSTING_PER from wo_pre_cost_mst where status_active=1 and job_id=$job_id");
		$costingPer = $costing_per_sql[0]['COSTING_PER'];
		unset($costing_per_sql);

		if($costingPer==1) $pcs_value=1*12;
		else if($costingPer==2) $pcs_value=1*1;
		else if($costingPer==3) $pcs_value=2*12;
		else if($costingPer==4) $pcs_value=3*12;
		else if($costingPer==5) $pcs_value=4*12;

		$sql = "SELECT COMMISION_RATE, COMMISSION_AMOUNT from WO_PRE_COST_COMMISS_COST_DTLS where job_id=$job_id and status_active=1 and PARTICULARS_ID=2";
		$re = sql_select($sql);
		$com_amount = 0;
		$com_rate = 0;
		foreach ($re as $v)
		{
			$com_rate += $v['COMMISION_RATE']/$pcs_value;
			$com_amount += $v['COMMISSION_AMOUNT']/$pcs_value;
		}
	}

	/*$ex_fac_poqty=return_field_value("sum(b.production_qnty)","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds");

	$ex_fac_countryqty=return_field_value("sum(b.production_qnty)","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds");

	$hidden_countryqty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0");*/
	if($ship_date!="") {$ship_date_cond = " and country_ship_date='$ship_date'";}
	if($pack_type!="") {$pack_type_cond = " and pack_type='$pack_type'";}
	if ($sewing_production_variable == 2 || $sewing_production_variable == 3 || $sewing_production_variable == 4)
	{
		$ex_fac_poqty = return_field_value("sum(b.production_qnty) as production_qnty", "pro_ex_factory_mst a,pro_ex_factory_dtls b ", " a.id=b.mst_id and a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds $ship_date_cond", "production_qnty");
		// echo "select sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where  a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds $ship_date_cond";
		$ex_fac_countryqty = return_field_value("sum(b.production_qnty) as production_qnty", "pro_ex_factory_mst a,pro_ex_factory_dtls b ", " a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds $ship_date_cond", "production_qnty");

		$hidden_countryqty = return_field_value("sum(order_quantity) as order_quantity", "wo_po_color_size_breakdown", "po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $ship_date_cond $pack_type_cond and status_active in(1,2,3) and is_deleted=0", "order_quantity");
	}
	else
	{
		$ex_fac_poqty = return_field_value("sum(a.ex_factory_qnty) as production_qnty", "pro_ex_factory_mst a ", " a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 $conds $ship_date_cond", "production_qnty");
		//echo "select sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where  a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds";
		$ex_fac_countryqty = return_field_value("sum(a.ex_factory_qnty) as production_qnty", "pro_ex_factory_mst a ", "  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0  $conds $ship_date_cond", "production_qnty");

		$hidden_countryqty = return_field_value("sum(order_quantity) as order_quantity", "wo_po_color_size_breakdown", "po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $ship_date_cond $pack_type_cond and status_active in(1,2,3) and is_deleted=0", "order_quantity");
	}

	$ex_fac_qty = return_field_value("sum(b.production_qnty) as production_qnty", "pro_ex_factory_mst a,pro_ex_factory_dtls b ", " a.id=b.mst_id and a.id='$ex_mst_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ship_date_cond", "production_qnty");

	//$challan_id = return_field_value("delivery_mst_id","pro_ex_factory_mst a "," a.id=$ex_mst_id  and a.status_active=1 and a.is_deleted=0","delivery_mst_id");
	$sqlLcScInfo = "SELECT a.ID,a.COMMISSION, a.COMMISSION_PERCENT,(c.EX_FACTORY_QNTY*b.CURRENT_INVOICE_RATE) as INVOICE_VALUE FROM com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b, pro_ex_factory_mst c WHERE a.id=b.mst_id and  a.id = c.invoice_no AND a.LC_SC_ID = c.LC_SC_no AND c.id = $ex_mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//echo $sqlLcScInfo;
	$lcscRes = sql_select($sqlLcScInfo);
	$commission = ($lcscRes[0]['INVOICE_VALUE'] * $lcscRes[0]['COMMISSION_PERCENT']) / 100;
	// echo $ex_fac_countryqty."*".$com_rate;
	if($invoice_qty_source==0 || $invoice_qty_source=="" || $invoice_qty_source==1)
	{
		echo "$('#txt_commission').val('" . number_format($com_rate,2) . "');\n";
		echo "$('#txt_commission_amt').val('" . number_format($com_amount,2) . "');\n";
		echo "$('#txt_order_amt').val('" . number_format(($ex_fac_qty*$com_amount),2) . "');\n";
	}
	else
	{
		echo "$('#txt_commission').val('" . number_format($lcscRes[0]['COMMISSION_PERCENT'],2) . "');\n";
		echo "$('#txt_commission_amt').val('" . number_format($commission,2) . "');\n";
		echo "$('#txt_order_amt').val('" . number_format(($ex_fac_qty*$commission),2) . "');\n";
	}
	// echo "$('#txt_order_amt').val('" . $lcscRes[0]['INVOICE_VALUE'] . "');\n";

	echo "$('#hidden_ex_fac_poqty').val('" . $ex_fac_poqty . "');\n";
	echo "$('#hidden_ex_fac_countryqty').val('" . $ex_fac_countryqty . "');\n";
	echo "$('#hidden_countryqty').val('" . $hidden_countryqty . "');\n";

	// echo "$('#txt_actual_po').val('');\n";
	// echo "$('#hidden_actual_po').val('');\n";

	foreach ($res as $result) {
		echo "$('#txt_order_qty').val('" . $result[csf('po_quantity')] . "');\n";
		echo "$('#cbo_item_name').val(" . $item_id . ");\n";
		echo "$('#cbo_country_name').val(" . $country_id . ");\n";
		echo "$('#short_country_name').val(" . $country_id . ");\n";

		echo "$('#txt_order_no').val('" . $result[csf('po_number')] . "');\n";
		echo "$('#hidden_po_break_down_id').val('" . $result[csf('id')] . "');\n";
		echo "$('#cbo_buyer_name').val('" . $result[csf('buyer_name')] . "');\n";
		echo "$('#txt_style_no').val('" . $result[csf('style_ref_no')] . "');\n";
		echo "$('#txt_shipment_date').val('" . change_date_format($result[csf('shipment_date')]) . "');\n";
		echo "$('#txt_country_ship_date').val('" . change_date_format($ship_date) . "');\n";
		echo "$('#txt_pack_type').val('" . $pack_type . "');\n";
		echo "$('#txt_job_no').val('" . $result[csf('job_no')] . "');\n";
		if ($qty_source != 0) {
			echo "$('#source_msg').text('');\n";
			if ($qty_source == 4) {
				echo "$('#source_msg').text('Sewing Input Qty');\n";
			} else if ($qty_source == 5) {
				echo "$('#source_msg').text('Sewing Output Qty');\n";
			} else if ($qty_source == 7) {
				echo "$('#source_msg').text('Iron Qty');\n";
			}else if ($qty_source == 8) {
				echo "$('#source_msg').text('Packing And Finishing');\n";
			} else if ($qty_source == 11) {
				echo "$('#source_msg').text('Poly Entry Qty');\n";
			} else if ($qty_source == 91) {
				echo "$('#source_msg').text('Buyer Inspection Qty');\n";
			} else if ($qty_source == 82) {
				echo "$('#source_msg').text('Finish Gmts Issue Qty');\n";
			} else if ($qty_source == 14) {
				echo "$('#source_msg').text('Gmts Finish Del. Qty.');\n";
			} else if ($qty_source == 81) {
				echo "$('#source_msg').text('Finish Gmts Rec. Qty.');\n";
			} else {
				echo "$('#source_msg').text('Sewing Finish Qty');\n";
			}

			if ($sewing_production_variable == 1) // gross level
			{
				if ($qty_source != 77)
				{
					$finish_qty = sql_select("SELECT sum(case when a.production_type='$qty_source' then a.production_quantity else 0 end) as production_qnty,(sum( CASE WHEN a.trans_type=5 and a.production_type=10 THEN a.production_quantity ELSE 0 END)-sum( CASE WHEN a.trans_type=6 and a.production_type=10 THEN a.production_quantity ELSE 0 END)) as trans_qnty from pro_garments_production_mst a where a.po_break_down_id=" . $result[csf('id')] . " and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond and production_type in($qty_source,10) and a.status_active=1 and a.is_deleted=0");
				}
				else // for Buyer Inspection Qnty
				{
					$finish_qty = sql_select("SELECT sum(b.ins_qty) as production_qnty from PRO_BUYER_INSPECTION a,PRO_BUYER_INSPECTION_BREAKDOWN b where a.id=b.mst_id and a.po_break_down_id=" . $result[csf('id')] . " and b.item_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}

				// receive from fin gmts order to order transfer
				// $receive_qty = sql_select("SELECT sum( a.production_quantity) as production_qnty from pro_gmts_delivery_dtls a where a.to_po_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='10' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0");

			}
			else
			{
				if($qty_source==82) // fin. gmts. issue
				{
					$finish_qty = sql_select("SELECT (sum(case when b.production_type='$qty_source' then b.production_qnty else 0 end) - sum(case when b.production_type=83 then b.production_qnty else 0 end)) as production_qnty,(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b, PRO_GMTS_DELIVERY_MST c where  a.id=b.mst_id and c.id=a.delivery_mst_id and c.purpose_id not in(2,3) and a.po_break_down_id=" . $result[csf('id')] . " and a.item_number_id='$item_id' and b.production_type in($qty_source,83,10) and a.country_id='$country_id' $ship_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}
				elseif ($qty_source==91) // for Buyer Inspection Qnty
				{
					$inspection_sql = "SELECT SUM(b.ins_qty) as ins_qty  from  pro_buyer_inspection a, pro_buyer_inspection_breakdown b where a.id = b.mst_id and a.po_break_down_id=" . $result['ID'] ." and b.item_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
					// echo $inspection_sql; die;
					$finish_qty = sql_select($inspection_sql);

				}
				else
				{
					$finish_qty = sql_select("SELECT c.country_ship_date, sum(case when b.production_type='$qty_source' then b.production_qnty else 0 end) as production_qnty,(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where  a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id  and c.id= b.color_size_break_down_id and a.po_break_down_id=" . $result[csf('id')] . " and a.item_number_id='$item_id' and b.production_type in($qty_source,10) and a.country_id='$country_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.country_ship_date");//$ship_date_cond;
				}
				// receive from fin gmts order to order transfer
				// $receive_qty = sql_select("SELECT sum( b.production_qnty) as production_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b where  a.id=b.mst_id and a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='10' and b.production_type='10' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=5 and b.trans_type=5");
			}
			if($qty_source==8)
			{
				$finish_qty = $finish_qty[0][csf("production_qnty")] + $finish_qty[0][csf("trans_qnty")];
			}
			else if($qty_source==91)
			{
				$finish_qty = $finish_qty[0][csf("ins_qty")];
			}
			else
			{
				$finish_qty = $finish_qty[0][csf("production_qnty")];
			}
			if ($finish_qty == "") $finish_qty = 0;


			$total_produced = sql_select(" SELECT sum(case when a.entry_form<>85 then b.PRODUCTION_QNTY else 0 end )- sum(case when a.entry_form=85 then b.PRODUCTION_QNTY else 0 end ) as  ex_factory_qnty from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.po_break_down_id=" . $result[csf('id')] . " and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond  and b.status_active=1 and b.is_deleted=0");

			$total_produced = $total_produced[0][csf("ex_factory_qnty")];
			//echo "reud $total_produced";
			if ($total_produced == "") $total_produced = 0;

			echo "$('#txt_finish_quantity').val('" . $finish_qty . "');\n";
			echo "$('#txt_cumul_quantity').attr('placeholder','" . $total_produced . "');\n";
			echo "$('#txt_cumul_quantity').val('" . $total_produced . "');\n";
			$yet_to_produced = $finish_qty - $total_produced;
			echo "$('#txt_yet_quantity').attr('placeholder','" . $yet_to_produced . "');\n";
			echo "$('#txt_yet_quantity').val('" . $yet_to_produced . "');\n";
		}

		if ($qty_source == 0)
		{
			$plan_cut_qnty = return_field_value("sum(plan_cut_qnty)", "wo_po_color_size_breakdown", "po_break_down_id=" . $result[csf('id')] . " and item_number_id='$item_id' and country_id='$country_id' $ship_date_cond $pack_type_cond and status_active in(1,2,3) and is_deleted=0");

			//$total_produced = return_field_value("sum(case when entry_form<>85 then ex_factory_qnty else 0 end )-sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty","pro_ex_factory_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and entry_form<>85 and country_id='$country_id'  and is_deleted=0");

			// echo " select sum(case when entry_form<>85 then ex_factory_qnty else 0 end )- sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty  from pro_ex_factory_mst where po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and status_active=1 and is_deleted=0";
			$total_produced = sql_select(" select sum(case when entry_form<>85 then ex_factory_qnty else 0 end )- sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty  from pro_ex_factory_mst where po_break_down_id=" . $result[csf('id')] . " and item_number_id='$item_id' and country_id='$country_id' $ship_date_cond  and status_active=1 and is_deleted=0");

			$total_produced = $total_produced[0][csf("ex_factory_qnty")];
			echo "$('#txt_finish_quantity').val('" . $plan_cut_qnty . "');\n";
			echo "$('#txt_cumul_quantity').attr('placeholder','" . $total_produced . "');\n";
			echo "$('#txt_cumul_quantity').val('" . $total_produced . "');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_quantity').attr('placeholder','" . $yet_to_produced . "');\n";
			echo "$('#txt_yet_quantity').val('" . $yet_to_produced . "');\n";
			// echo "change_shipping_status(0);\n";
		}
	}
	exit();
}

if ($action == "color_and_size_level")
{
	$dataArr = explode("**", $data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$preceding_process = $dataArr[5];
	$styleOrOrderWisw = $dataArr[3];
	$country_id = $dataArr[4];
	$ship_date = $dataArr[7];
	$pack_type = $dataArr[8];
	$garments_nature = $dataArr[9];

	$ship_date = date('d-M-Y',strtotime(str_replace("'","",$ship_date)));

	if($ship_date!="")
	{
		$ship_date_cond = " and a.country_ship_date='$ship_date'";
		$ship_date_cond2 = " and b.country_ship_date='$ship_date'";
		$ship_date_cond3 = " and c.country_ship_date='$ship_date'";
	}
	if($pack_type!="")
	{
		$pack_type_cond = " and a.pack_type='$pack_type'";
		$pack_type_cond2 = " and b.pack_type='$pack_type'";
		$pack_type_cond3 = " and c.pack_type='$pack_type'";
	}

	if($variableSettings==4)
	{
		$accPOArr=return_library_array( "SELECT id, po_break_down_id from WO_PO_ACC_PO_INFO_DTLS where status_active=1 and is_deleted=0 and po_break_down_id=$po_id",'id','po_break_down_id');
		if(count($accPOArr)>0)
		{
			die;
		}
		else
		{
			$variableSettings = 3;
			echo "$('#sewing_production_variable').val('3');\n";
		}

	}

	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 1000; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry

	if($qty_source == 82)
	{
		$issue_purpose_cond = "";
	}

	$is_control = $dataArr[6];
	/* if ($is_control != 1) {
		$qty_source = 0;
	} */

	$color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
	//#############################################################################################//
	// order wise - color level, color and size level

	$ex_fac_value = array();

	// echo $qty_source.'-'.$variableSettings;die;
	if ($qty_source != 0) {
		if ($variableSettings == 2) // color level
		{
			if ($db_type == 0)
			{
				if($qty_source==82) // finish gmts issue
				{
					$sql = "SELECT a.item_number_id, a.color_number_id,sum(a.order_quantity) as order_quantity,sum(a.plan_cut_qnty) as plan_cut_qnty,(sum(case when b.production_type=82 then b.production_qnty else 0 end) - sum(case when b.production_type=83 then b.production_qnty else 0 end)) as production_qnty,(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id and c.purpose_id not in(2,3) and b.status_active=1 and b.is_deleted=0
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and b.production_type in(82,83,10) group by a.item_number_id, a.color_number_id";
					// echo $sql;die;

				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";
				}
				
				$trans_sql = sql_select("SELECT c.item_number_id,c.color_number_id, (sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_color_size_breakdown c where c.id=b.color_size_break_down_id and c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and c.country_id=a.country_id and a.id=b.mst_id and a.po_break_down_id=$po_id and a.item_number_id='$item_id' and b.production_type=10 and a.country_id='$country_id' $ship_date_cond3 $pack_type_cond3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.item_number_id,c.color_number_id");
				$trans_qnty_arr = array();
				foreach ($trans_sql as $trans_val) {
					$trans_qnty_arr[$trans_val[csf("item_number_id")]][$trans_val[csf("color_number_id")]] = $trans_val[csf("trans_qnty")];
				}

				$sql_exfac = sql_select("SELECT a.id,a.item_number_id,a.color_number_id,sum(case when entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a
	                    ,pro_ex_factory_mst m, pro_ex_factory_dtls ex where  ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond  and a.is_deleted=0 and a.status_active in(1,2,3) group by a.id,a.item_number_id, a.color_number_id");
				foreach ($sql_exfac as $row_exfac) {
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]] = $row_exfac[csf("ex_production_qnty")];
					$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];
				}
			}
			else
			{
				if ($qty_source == 1000) //Buyer Inspection
				{
					$sql = "SELECT a.color_number_id,a.item_number_id,SUM(a.order_quantity) as order_quantity from  wo_po_color_size_breakdown a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 group by a.color_number_id,a.item_number_id order by a.color_number_id";
					// echo $sql;
					$inspection_sql = "SELECT b.ins_qty, b.color_id,b.item_id from  pro_buyer_inspection a, pro_buyer_inspection_breakdown b where a.id = b.mst_id and a.po_break_down_id='$po_id' and b.item_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.color_id";
					// echo $inspection_sql; die;
					$inspection_sql_res = sql_select($inspection_sql);
					$inspection_array = array();
					foreach ($inspection_sql_res as $v) {
						$inspection_array[$v['ITEM_ID']][$v["COLOR_ID"]] += $v['INS_QTY'];
					}
					// echo "<pre>";
					// print_r($inspection_array); die;
					$sql_exfac = sql_select("SELECT a.item_number_id,a.color_number_id,sum(case when entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a
							,pro_ex_factory_mst m, pro_ex_factory_dtls ex where  ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
							and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id");
					foreach ($sql_exfac as $row_exfac) {
						$ex_fac_value[$row_exfac["ITEM_NUMBER_ID"]][$row_exfac["COLOR_NUMBER_ID"]] = $row_exfac["EX_PRODUCTION_QNTY"];
					}
					// echo "<pre>";
					// print_r($ex_fac_value); die;
				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					(sum(CASE WHEN b.production_type='$qty_source'  then b.production_qnty ELSE 0 END) - sum(case when b.production_type=83  then b.production_qnty else 0 end)) as production_qnty,
					(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id and c.purpose_id not in(2,3)
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) and b.production_type in($qty_source,83,10) group by a.item_number_id, a.color_number_id";

					//$finish_qty = sql_select("SELECT (sum(case when b.production_type='$qty_source' then b.production_qnty else 0 end) - sum(case when b.production_type=83 then b.production_qnty else 0 end)) as production_qnty,(sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b, PRO_GMTS_DELIVERY_MST c where  a.id=b.mst_id and c.id=a.delivery_mst_id and c.purpose_id not in(2,3) and a.po_break_down_id=" . $result[csf('id')] . " and a.item_number_id='$item_id' and b.production_type in($qty_source,83,10) and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					$trans_sql = sql_select("SELECT c.item_number_id,c.color_number_id, (sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_color_size_breakdown c where c.id=b.color_size_break_down_id and c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and c.country_id=a.country_id and a.id=b.mst_id and a.po_break_down_id=$po_id and a.item_number_id='$item_id' and b.production_type=10 and a.country_id='$country_id' $ship_date_cond3 $pack_type_cond3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.item_number_id,c.color_number_id");
					$trans_qnty_arr = array();
					foreach ($trans_sql as $trans_val) {
						$trans_qnty_arr[$trans_val[csf("item_number_id")]][$trans_val[csf("color_number_id")]] = $trans_val[csf("trans_qnty")];
					}

					$sql_exfac = sql_select("SELECT  a.id,a.item_number_id,a.color_number_id,sum(case when entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a
							,pro_ex_factory_mst m, pro_ex_factory_dtls ex where  ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
							and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.id, a.item_number_id, a.color_number_id");
					foreach ($sql_exfac as $row_exfac) {
						$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]] = $row_exfac[csf("ex_production_qnty")];
						$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];

					}
				}
			}
		}
		else if ($variableSettings == 3) //color and size level
		{
			if($qty_source==82) // finish gmts issue
			{
				$prodData = sql_select("SELECT a.color_size_break_down_id,(sum(case when a.production_type=82 then a.production_qnty else 0 end) - sum(case when a.production_type=83 then a.production_qnty else 0 end)) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b, pro_gmts_delivery_mst d,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and d.id=b.delivery_mst_id and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type in(82,83) and d.purpose_id=1 $ship_date_cond3 $pack_type_cond3 and b.garments_nature=$garments_nature group by a.color_size_break_down_id");

				$color_size_pro_qnty_array = array();
				foreach ($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] += $row[csf('production_qnty')];
				}
				// print_r($color_size_pro_qnty_array); die;

			}
			else if($qty_source==1000) // Buyer inspection
			{
				$inspection_sql = "SELECT b.ins_qty,c.id as color_size_break_down_id from  pro_buyer_inspection a, pro_buyer_inspection_breakdown b,wo_po_color_size_breakdown c where a.id = b.mst_id and a.po_break_down_id='$po_id'and a.po_break_down_id=c.po_break_down_id  and b.item_id='$item_id' and a.country_id='$country_id' and b.color_id=c.color_number_id and b.size_id=c.size_number_id and b.item_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 order by b.color_id,b.size_id";
				// echo $inspection_sql; die;
				$inspection_sql_res = sql_select($inspection_sql);
				$color_size_pro_qnty_array = array();
				foreach ($inspection_sql_res as $v)
				{
					$color_size_pro_qnty_array[$v[csf('color_size_break_down_id')]] += $v['INS_QTY'];
				}

			}
			else
			{
				$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type='$qty_source' $ship_date_cond3 $pack_type_cond3 and b.garments_nature=$garments_nature group by a.color_size_break_down_id
				union all
				select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type=10 $ship_date_cond3 $pack_type_cond3 and b.garments_nature=$garments_nature group by a.color_size_break_down_id");

				$color_size_pro_qnty_array = array();

				foreach ($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] += $row[csf('production_qnty')];
				}
			}
			// print_r($color_size_pro_qnty_array); die;
			$sql_exfac = sql_select("SELECT a.id,a.item_number_id,a.color_number_id,a.size_number_id,sum(case when m.entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when m.entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a,pro_ex_factory_mst m
	                    , pro_ex_factory_dtls ex where ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 group by a.id,a.item_number_id, a.color_number_id, a.size_number_id");
			foreach ($sql_exfac as $row_exfac) {
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]] = $row_exfac[csf("ex_production_qnty")];
				$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];
			}

			$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";
		}
		else // by default color and size level
		{
			if($qty_source==82) // finish gmts issue
			{
				$prodData = sql_select("SELECT a.color_size_break_down_id,(sum(case when a.production_type=82 then a.production_qnty else 0 end) - sum(case when a.production_type=83 then a.production_qnty else 0 end)) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b, pro_gmts_delivery_mst d,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and d.id=b.delivery_mst_id and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type in(82,83) and d.purpose_id=1 $ship_date_cond3 $pack_type_cond3 group by a.color_size_break_down_id");

				foreach ($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] += $row[csf('production_qnty')];
				}

			}
			else
			{
				$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type='$qty_source' group by a.color_size_break_down_id
				union all
				select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");

				foreach ($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] += $row[csf('production_qnty')];
				}
			}

			$sql_exfac = sql_select("SELECT a.id,a.item_number_id,a.color_number_id,a.size_number_id,sum(case when m.entry_form<>85 then ex.production_qnty else 0 end ) - sum(case when m.entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a,pro_ex_factory_mst m, pro_ex_factory_dtls ex where ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0 and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.id, a.item_number_id, a.color_number_id, a.size_number_id");
			foreach ($sql_exfac as $row_exfac) {
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]] = $row_exfac[csf("ex_production_qnty")];
				$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];
			}

			$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
				from wo_po_color_size_breakdown a
				where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";
		}
	}
	else // if preceding process =0 in variable setting then plan cut quantity will show
	{
		if ($variableSettings == 2) // color level
		{

			/*$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(case when m.entry_form<>85 then b.production_qnty else 0 end ) - sum(case when m.entry_form=85 then b.production_qnty else 0 end ) as production_qnty
				from wo_po_color_size_breakdown a ,pro_ex_factory_mst m, pro_ex_factory_dtls b where a.po_break_down_id=m.po_break_down_id and m.id=b.mst_id and m.status_active=1 and m.is_deleted=0 and  a.id=b.color_size_break_down_id
				and  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";*/

			$dtlsData =sql_select("SELECT a.color_number_id, sum(case when m.entry_form<>85 then b.production_qnty else 0 end ) - sum(case when m.entry_form=85 then b.production_qnty else 0 end ) as production_qnty
				from wo_po_color_size_breakdown a ,pro_ex_factory_mst m, pro_ex_factory_dtls b where a.po_break_down_id=m.po_break_down_id and m.id=b.mst_id and m.status_active=1 and m.is_deleted=0 and  a.id=b.color_size_break_down_id
				and  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.color_number_id");
				// echo $dtlsData;die;

			foreach ($dtlsData as $row) {
				$color_size_qnty_array[$row[csf('color_number_id')]] += $row[csf('production_qnty')];
			}

			$sql = "SELECT a.color_number_id, sum(a.plan_cut_qnty) as plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond  and a.is_deleted=0 and a.status_active=1 group by a.color_number_id order by a.color_number_id"; //color_number_id, id


		}
		else if ($variableSettings == 3) //color and size level
		{

			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(case when b.entry_form<>85 then a.production_qnty else 0 end)-sum(case when b.entry_form=85 then a.production_qnty else 0 end) as production_qnty
										from pro_ex_factory_dtls a,pro_ex_factory_mst b where  a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.is_deleted=0 and a.status_active=1 and b.status_active=1  group by a.color_size_break_down_id");

			foreach ($dtlsData as $row) {
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut'] = $row[csf('production_qnty')];
			}

			$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 order by a.color_number_id,a.size_order"; //color_number_id, id


		} else // by default color and size level
		{


			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(case when b.entry_form<>85 then a.production_qnty else 0 end)-sum(case when b.entry_form=85 then a.production_qnty else 0 end) as production_qnty
										from pro_ex_factory_dtls a,pro_ex_factory_mst b where  a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 group by a.color_size_break_down_id");

			foreach ($dtlsData as $row) {
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut'] += $row[csf('production_qnty')];
			}

			$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active=1 order by a.color_number_id,a.size_order"; //color_number_id, id
		}
	}

	// echo $sql; die();
	$colorResult = sql_select($sql);
	$colorHTML = "";
	$colorID = '';
	$chkColor = array();
	$i = 0;
	$totalQnty = 0;
	if ($qty_source != 0) {
		foreach ($colorResult as $color) {
			if ($variableSettings == 2) // color level
			{
				if($qty_source==8)
				{
					$bal_qty = $color[csf("production_qnty")] + $trans_qnty_arr[$color[csf('item_number_id')]][$color[csf('color_number_id')]] - $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
				}
				if($qty_source==1000)
				{
					$bal_qty = $inspection_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]] - $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
				}
				else
				{
					$bal_qty = $color[csf("production_qnty")] - $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
				}

				$colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($bal_qty) . '" onkeyup="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
				$totalQnty += $bal_qty;
				$colorID .= $color[csf("color_number_id")] . ",";
			}
			else //color and size level
			{
				if (!in_array($color[csf("color_number_id")], $chkColor)) {
					if ($i != 0) $colorHTML .= "</table></div>";
					$i = 0;
					$colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_' . $color[csf("color_number_id")] . '">';
					$chkColor[] = $color[csf("color_number_id")];
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

				$pro_qnty = $color_size_pro_qnty_array[$color[csf('id')]];
				// echo $pro_qnty."<br>";
				// $exfac_qnty = $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];
				$clr_sz_brk_id = $color[csf('id')];
				$exfac_qnty = $ex_fac_val_clr_Size[$clr_sz_brk_id];


				$colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($pro_qnty - $exfac_qnty) . '" onkeyup="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"></td></tr>';
			}
			$i++;
		}
	}

	if ($qty_source == 0) {
		foreach ($colorResult as $color) {
			if ($variableSettings == 2) // color level
			{
				$colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="' . ($color[csf("plan_cut_qnty")] - $color_size_qnty_array[$color[csf("color_number_id")]]) . '" onkeyup="fn_colorlevel_total(' . ($i + 1) . ')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total(' . ($i + 1) . ') ' . $disable . '"></td></tr>';
				$totalQnty += $color[csf("plan_cut_qnty")] - $color_size_qnty_array[$color[csf("color_number_id")]];
				$colorID .= $color[csf("color_number_id")] . ",";
			} else //color and size level
			{
				if (!in_array($color[csf("color_number_id")], $chkColor)) {
					if ($i != 0) $colorHTML .= "</table></div>";
					$i = 0;
					$colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)">  <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span></h3>';
					$colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_' . $color[csf("color_number_id")] . '">';
					$chkColor[] = $color[csf("color_number_id")];
				}
				$bundle_mst_data = "";
				$bundle_dtls_data = "";
				$tmp_col_size = "'" . $color_library[$color[csf("color_number_id")]] . "__" . $size_library[$color[csf("size_number_id")]] . "'";
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";
				$cut_qnty = $color_size_qnty_array[$color[csf('id')]]['cut'];

				$clr_sz_brk_id = $color[csf('id')];

				$colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="hidden" name="bundlemst" id="bundle_mst_' . $color[csf("color_number_id")] . ($i + 1) . '" value="' . $bundle_mst_data . '"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" value="' . $bundle_dtls_data . '" ><input type="text" name="colorSize" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric set-active" style="width:50px" placeholder="' . ($color[csf("order_quantity")] - $cut_qnty) . '" onkeyup="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"></td></tr>';
			}

			$i++;
		}
	}
	//echo $colorHTML;die;
	if ($variableSettings == 2) {
		$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
	}
	echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
	$colorList = substr($colorID, 0, -1);
	echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
	//#############################################################################################//
	exit();
}

if ($action == "show_dtls_listview")
{
	$dataArr = explode("**", $data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	?>
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="150" align="center">Item Name</th>
				<th width="110" align="center">Country</th>
				<th width="110" align="center">Ex-Fact. Date</th>
				<th width="110" align="center">Ex-Fact. Qnty</th>
				<th width="120" align="center">Invoice No</th>
				<th width="120" align="center">LC/SC No</th>
				<th align="center">Challan No</th>
			</thead>
		</table>
	</div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
			<?
			$i = 1;
			$total_production_qnty = 0;

			$sqlResult = sql_select("select id,po_break_down_id,item_number_id,country_id,ex_factory_date,ex_factory_qnty,location,lc_sc_no,invoice_no,challan_no from  pro_ex_factory_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and entry_form<>85 and status_active=1 and is_deleted=0 order by id");
			foreach ($sqlResult as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$total_production_qnty += $selectResult[csf('ex_factory_qnty')];

				$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='" . $selectResult[csf('invoice_no')] . "'");
				foreach ($sqlEx as $val) {
					if ($val[csf("is_lc")] == 1) //  lc
						$lc_sc = $lc_num_arr[$val[csf('lc_sc_id')]];
					else
						$lc_sc = $sc_num_arr[$val[csf('lc_sc_id')]];

					$invoiceNo = $val[csf('invoice_no')];
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/garments_delivery_entry_controller');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="150" align="center">
						<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
					</td>
					<td width="110" align="center">
						<p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p>
					</td>
					<td width="110" align="center">
						<p><? echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p>
					</td>
					<td width="110" align="center">
						<p><? echo $selectResult[csf('ex_factory_qnty')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $invoiceNo; ?>&nbsp;</p>
					</td>
					<td width="120" align="center">
						<p><? echo $lc_sc; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
			<!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
	<?
	exit();
}

if ($action == "show_country_listview") 
{
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 82; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
 	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
		<thead>
			<th width="20">SL</th>
			<th width="100">Item Name</th>
			<th width="65">Country</th>
			<th width="40">Short Name</th>
			<th width="50">Cut Off No</th>
			<th width="55">Ship Date</th>
			<th width="50">Pack Type</th>
			<th width="50">Order Qty.</th>
			<?
				if($qty_source ==0)
				{
					?>
					<th width="50">Plan Cut Qty.</th>
					<?
				}else 
				{
					?>
					<th width="50">Packing Qty.</th>
					<?
				}
			?>
			<th width="50">Ex-Fact. Qty.</th>
			<th width="50">Balance</th>
		</thead>
	</table>
	<div id="scroll_body" style="width:650px; max-height:450px; overflow-x:hidden;  overflow-y:auto;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_body_1">
	<?
	$dataArr = explode("**", $data);

	// echo "<pre>";print_r($dataArr);die;
	$po  = $dataArr[0];
	$compan_id = $dataArr[1];
		$control_and_preceding = sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$compan_id'");
		$preceding_process = $control_and_preceding[0][csf("preceding_page_id")];

	//echo "$('#hidden_variable_cntl').val('" . $control_and_preceding[0][csf('is_control')] . "');\n";

	

	if ($qty_source == 0) 
	{
		$ex_fect_sql = "SELECT  plan_cut_qnty ,cartoon_qty,po_break_down_id,
		item_number_id,
		country_id,
		country_ship_date,
		pack_type
		FROM 
		WO_PO_COLOR_SIZE_BREAKDOWN 
		WHERE
		po_break_down_id=$po
		AND status_active = 1
		AND is_deleted = 0";
	}
	else
	 {
		$ex_fect_sql = "SELECT a.production_type , a.production_qnty , b.carton_qty , b.po_break_down_id,
		b.item_number_id,
		b.country_id,
		b.country_ship_date,
		b.pack_type
		FROM PRO_GARMENTS_PRODUCTION_DTLS a ,
		 PRO_GARMENTS_PRODUCTION_MST b
		WHERE
		 a.mst_id = b.id
		AND a.production_type = $qty_source
		AND b.po_break_down_id=$po

		AND a.status_active = 1
		AND a.is_deleted = 0
		AND b.status_active = 1
		AND b.is_deleted = 0";
	}
  
	// echo $ex_fect_sql;die;
	$sql_ex = sql_select( $ex_fect_sql);
	$ex_fact_arr = array();
	if ($qty_source == 0) 
	{
		foreach($sql_ex as $rows)
		{
			$ex_fact_arr[$rows['PO_BREAK_DOWN_ID']][$rows['ITEM_NUMBER_ID']][$rows['COUNTRY_ID']][$rows['COUNTRY_SHIP_DATE']][$rows['PACK_TYPE']] += $rows['PLAN_CUT_QNTY'] ;
		}
	}else 
	{
		foreach($sql_ex as $rows){
			$ex_fact_arr[$rows['PO_BREAK_DOWN_ID']][$rows['ITEM_NUMBER_ID']][$rows['COUNTRY_ID']][$rows['COUNTRY_SHIP_DATE']][$rows['PACK_TYPE']] += $rows['PRODUCTION_QNTY'] ;
		}
	}
	
	// echo "<pre>";
	// print_r($ex_fact_arr);


  // Ex-Factory Data Starts Here
 // <================================================>
   $sql_exf = "SELECT a.production_qnty,
    b.po_break_down_id,
	b.item_number_id,
	b.country_id,
	b.country_ship_date,
	b.pack_type
	FROM PRO_EX_FACTORY_DTLS  a,
		 PRO_EX_FACTORY_MST b
   WHERE a.mst_id = b.id
   AND b.po_break_down_id='$po'
	AND a.status_active = 1
	AND a.is_deleted = 0
	AND b.status_active = 1
	AND b.is_deleted = 0";
	// echo $sql_exf;
   $execute_query = sql_select($sql_exf);
   $ex_fect_qty_arr = array();
   foreach($execute_query as $val)
     {
	  	$ex_fect_qty_arr[$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COUNTRY_ID']][$val['COUNTRY_SHIP_DATE']][$val['PACK_TYPE']] += $val['PRODUCTION_QNTY'] ;
     }
	//  echo "<pre>";
	//  print_r( $ex_fect_qty_arr);die;
	// Ex-Factory Data Ends Here
	// <================================================>
			$i = 1;
			$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$po'  and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
			// echo $sqlResult;die;
			foreach ($sqlResult as $row) {

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<?=$row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')].",'".$row[csf('country_ship_date')]."','" .$row[csf('pack_type')]."'"; ?>);">
					<td width="20"><?=$i; ?></td>
					<td width="100" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
					<td width="65" style="word-break:break-all"><?=$country_library[$row[csf('country_id')]]; ?>&nbsp;</td>
					<td width="40" style="word-break:break-all"><?=$country_short_library[$row[csf('country_id')]]; ?>&nbsp;</td>
					<td width="50" style="word-break:break-all"><p><?=$cut_up_array[$row[csf('cutup')]]; ?></p></td>
					<td width="55" align="center" style="word-break:break-all"><? if ($row[csf('country_ship_date')] != "0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
					<td width="50" align="center" style="word-break:break-all"><?=$row[csf('pack_type')]; ?></td>
					<td align="right"width="50" style="word-break:break-all"><?=$row[csf('order_qnty')]; ?></td>
							<?
						if($qty_source ==0)
						{
							?>
							<td align="right"width="50" style="word-break:break-all"><? echo $ex_fact_total =  $ex_fact_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COUNTRY_SHIP_DATE']][$row['PACK_TYPE']]; ?></td>
							<?
						}else 
						{
							?>
							<td align="right"width="50" style="word-break:break-all"><? echo $ex_fact_total =  $ex_fact_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COUNTRY_SHIP_DATE']][$row['PACK_TYPE']]; ?></td>
							<?
						}
					   ?>
					<td align="right"width="50" style="word-break:break-all"><? echo $ex_fact_qty =  $ex_fect_qty_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COUNTRY_SHIP_DATE']][$row['PACK_TYPE']];  ?></td>
					<td align="right" style="word-break:break-all"><? echo $ex_fact_total-$ex_fact_qty; ?></td>
				</tr>
				<? $i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if ($action == "populate_exfactory_form_data")
{
	$ex_fac_value = array();
	$color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

	/*
	@update cuntry ship date and pack type
	@cause : user change cuntry ship date and pack type from order entry after ex-factory
	@issue ID : 14142
	*/
	$con = connect();
	$sqlRes = sql_select("SELECT po_break_down_id,item_number_id,country_id,lc_sc_no from pro_ex_factory_mst where id='$data' and status_active=1 and entry_form<>85 and is_deleted=0");
	$po_break_down_id = $sqlRes[0][csf('po_break_down_id')];
	$item_number_id = $sqlRes[0][csf('item_number_id')];
	$country_id = $sqlRes[0][csf('country_id')];
	$lcscId = $sqlRes[0][csf('lc_sc_no')];

	$lc_num_arr = return_library_array("SELECT a.id, a.export_lc_no from com_export_lc a where  status_active=1 and is_deleted=0 and id in($lcscId)", "id", "export_lc_no");

	$sc_num_arr = return_library_array("SELECT a.id, a.contract_no from com_sales_contract a where  status_active=1 and is_deleted=0 and id in($lcscId)", "id", "contract_no");


	$sql_colsize ="SELECT a.mst_id,b.pack_type,b.country_ship_date from  pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_break_down_id' and b.item_number_id='$item_number_id' and b.country_id='$country_id' and a.mst_id='$data'";
	// echo $sql_colsize;die;
	$colsize_res = sql_select($sql_colsize);

	$country_ship_date = $colsize_res[0][csf('country_ship_date')];
	$pack_type = $colsize_res[0][csf('pack_type')];
	$update_shidate = execute_query("UPDATE pro_ex_factory_mst set country_ship_date='$country_ship_date',pack_type='$pack_type' WHERE id=$data");
	// echo $update_shidate;die;
	if($update_shidate)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
	}
	disconnect($con);
	/*
	@end
	*/


	$sqlResult = sql_select("SELECT id,garments_nature,po_break_down_id,item_number_id,country_id,location,ex_factory_date,ex_factory_qnty,total_carton_qnty,challan_no,invoice_no,lc_sc_no,carton_qnty,transport_com,remarks,shiping_status,entry_break_down_type,inspection_qty_validation,delivery_mst_id,is_posted_account,shiping_mode,foc_or_claim,inco_terms,actual_po,additional_info,additional_info_id,country_ship_date,pack_type,destinatin,net_weight,gross_weight	from pro_ex_factory_mst where id='$data' and status_active=1 and entry_form<>85 and is_deleted=0 order by id");

	$po_break_down_id = $sqlResult[0][csf('po_break_down_id')];
	$mst_id = $sqlResult[0][csf('id')];

	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info where po_break_down_id = $po_break_down_id", 'id', 'acc_po_no');
	$actual_po = sql_select("SELECT a.entry_break_down_type, b.actual_po_id,b.actual_po_dtls_id,sum(b.ex_fact_qty) as ex_fact_qty from pro_ex_factory_mst a, pro_ex_factory_actual_po_details b where a.id=b.mst_id and b.mst_id = $mst_id and b.actual_po_id is not null and b.status_active=1 and b.is_deleted=0 group by a.entry_break_down_type,b.actual_po_id,b.actual_po_dtls_id");
	$actual_po_no = "";
	$actual_po_id = "";
	$acc_po_chk_arr = array();
	foreach ($actual_po as $val)
	{
		if($val[csf('entry_break_down_type')]==4)
		{
			if($acc_po_chk_arr[$val[csf('actual_po_id')]]=="")
			{
				if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val[csf('actual_po_id')]];
				else $actual_po_no .= ',' . $actual_po_library[$val[csf('actual_po_id')]];
				$acc_po_chk_arr[$val[csf('actual_po_id')]] = $val[csf('actual_po_id')];
			}

			if ($actual_po_id == "") $actual_po_id ='**'.$actual_po_library[$val[csf('actual_po_id')]].'**'.$val[csf('actual_po_id')]. '**' . $val[csf('actual_po_dtls_id')].'**********'.$val[csf('ex_fact_qty')];
			else $actual_po_id .= '==**'.$actual_po_library[$val[csf('actual_po_id')]].'**'.$val[csf('actual_po_id')] . '**' . $val[csf('actual_po_dtls_id')].'**********'.$val[csf('ex_fact_qty')];
		}
		else
		{
			if($acc_po_chk_arr[$val[csf('actual_po_id')]]=="")
			{
				if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val[csf('actual_po_id')]];
				else $actual_po_no .= ',' . $actual_po_library[$val[csf('actual_po_id')]];
				$acc_po_chk_arr[$val[csf('actual_po_id')]] = $val[csf('actual_po_id')];
			}

			if ($actual_po_id == "") $actual_po_id =$val[csf('actual_po_id')]. '_' . $val[csf('actual_po_dtls_id')];
			else $actual_po_id .= ','.$actual_po_id =$val[csf('actual_po_id')]. '_' . $val[csf('actual_po_dtls_id')];
		}
	}

	// $i."**".$row[csf('acc_po_no')]."**".$row[csf('id')]."**".$row[csf('dtls_id')]."**".$row[csf('country_id')]."**".$row[csf('gmts_item')]."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')];


	$invoice_id_arr = array();
	foreach ($sqlResult as $v)
	{
		$invoice_id_arr[$v[csf("invoice_no")]] = $v[csf("invoice_no")];
	}

	if(count($invoice_id_arr)>0)
	{
		$invoice_id_cond = where_con_using_array($invoice_id_arr,0,"id");
		$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where status_active=1 $invoice_id_cond");
		foreach ($sqlEx as $row)
		{
			$invoice_data_arr[$row[csf("id")]]["id"] = $row[csf("id")];
			$invoice_data_arr[$row[csf("id")]]["invoice_no"] = $row[csf("invoice_no")];
			$invoice_data_arr[$row[csf("id")]]["is_lc"] = $row[csf("is_lc")];
			$invoice_data_arr[$row[csf("id")]]["lc_sc_id"] = $row[csf("lc_sc_id")];
		}
	}

	$delivery_mst_id = $sqlResult[0][csf('delivery_mst_id')];
	$company_id = return_field_value("company_id", "pro_ex_factory_delivery_mst", " status_active=1 and  is_deleted=0 and id='$delivery_mst_id'");
	$depo_details = return_field_value("depo_details", "pro_ex_factory_delivery_mst", " status_active=1 and  is_deleted=0 and id='$delivery_mst_id'");
	$control_and_preceding = sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$company_id'");
	$preceding_process = $control_and_preceding[0][csf("preceding_page_id")];
	// echo "select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$company_id'";
	echo "$('#hidden_variable_cntl').val('" . $control_and_preceding[0][csf('is_control')] . "');\n";


	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 82; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry

	$is_control = $control_and_preceding[0][csf('is_control')];
	// if ($is_control != 1) {	$qty_source = 0;}

	foreach ($sqlResult as $result) {

		//echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		//echo "$('#txt_ex_factory_date').val('".change_date_format($result[csf('ex_factory_date')])."');\n";
		echo "$('#txt_ex_quantity').attr('placeholder','" . $result[csf('ex_factory_qnty')] . "');\n";
		echo "$('#txt_ex_quantity').val('" . $result[csf('ex_factory_qnty')] . "');\n";
		echo "$('#txt_total_carton_qnty').val('" . $result[csf('total_carton_qnty')] . "');\n";
		//echo "$('#txt_challan_no').val('".$result[csf('challan_no')]."');\n";
		echo "$('#cbo_ins_qty_validation_type').val('" . $result[csf('inspection_qty_validation')] . "');\n";
		echo "$('#txt_detail_destination').val('" . $result[csf('destinatin')] . "');\n";
		echo "$('#net_weight').val('" . $result[csf('net_weight')] . "');\n";
		echo "$('#gross_weight').val('" . $result[csf('gross_weight')] . "');\n";

		echo "$('#txt_invoice_no').val('');\n";
		echo "$('#txt_invoice_no').attr('placeholder','');\n";
		echo "$('#txt_lc_sc_no').val('');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','');\n";



		//$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$result[csf('invoice_no')]."'");
		/*foreach($sqlEx as $val)
		{*/
		echo "$('#txt_invoice_no').val('" . $invoice_data_arr[$result[csf('invoice_no')]]["invoice_no"] . "');\n";
		echo "$('#txt_invoice_no').attr('placeholder','" . $invoice_data_arr[$result[csf('invoice_no')]]["id"] . "');\n";


		if ($invoice_data_arr[$result[csf('invoice_no')]]["is_lc"] == 1) //  lc
			$lc_sc = $lc_num_arr[$invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"]];
		else
			$lc_sc = $sc_num_arr[$invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"]];

		echo "$('#txt_lc_sc_no').val('" . $lc_sc . "');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','" . $invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"] . "');\n";
		//}


		echo "$('#txt_ctn_qnty').val('" . $result[csf('carton_qnty')] . "');\n";
		echo "$('#txt_transport_com').val('" . $result[csf('transport_com')] . "');\n";
		echo "$('#txt_remark').val('" . $result[csf('remarks')] . "');\n";
		echo "$('#cbo_foc_claim').val('" . $result[csf('foc_or_claim')] . "');\n";
		echo "$('#cbo_shipping_mode').val('" . $result[csf('shiping_mode')] . "');\n";
		echo "$('#cbo_inco_term_id').val('" . $result[csf('inco_terms')] . "');\n";
		echo "$('#hidden_actual_po').val('" . $actual_po_id . "');\n";
		echo "$('#txt_actual_po').val('" . $actual_po_no . "');\n";

		echo "$('#txt_add_info').val('" . $result[csf('additional_info')] . "');\n";
		echo "$('#hidden_add_info').val('" . $result[csf('additional_info_id')] . "');\n";
		if($result[csf('country_ship_date')]!="")
		{
			echo "$('#txt_country_ship_date').val('" . $result[csf('country_ship_date')] . "');\n";
		}
		else
		{
			echo "$('#txt_country_ship_date').val('');\n";
		}
		echo "$('#txt_pack_type').val('" . $result[csf('pack_type')] . "');\n";
		echo "$('#txt_depo_details').val('" . $depo_details . "');\n";

		echo "$('#txt_mst_id').val('" . $result[csf('id')] . "');\n";
		echo "set_button_status(1, permission, 'fnc_exFactory_entry',1,1);\n";

		//break down of color and size------------------------------------------
		//#############################################################################################//
		// order wise - color level, color and size level


		$variableSettings = $result[csf('entry_break_down_type')];
		$is_posted_account = $result[csf('is_posted_account')];
		echo "$('#is_posted_account').val(" . $is_posted_account . ");\n";
		$disabled = "";
		$msg = "";
		if ($is_posted_account == 1) {
			$disabled = "disabled";
			$msg = "Already Posted In Accounting.";
			echo "disable_enable_fields( 'txt_order_no*cbo_ins_qty_validation_type*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty* txt_remark*shipping_status', 1 );\n";
		} else {
			echo "disable_enable_fields( 'txt_order_no*cbo_ins_qty_validation_type*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_remark*shipping_status', 0 );\n";
		}

		// echo "alert('ok');\n";
		// echo $qty_source;die();
		if($variableSettings!=4) // 4 means acc po wise
		{
			if ($qty_source != 0)
			{
				if ($variableSettings != 1) // gross level
				{
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];
					$ship_date = $result[csf('country_ship_date')];
					$pack_type = $result[csf('pack_type')];
					if($ship_date!="") {$ship_date_cond = " and a.country_ship_date='$ship_date'";$ship_date_cond2 = " and b.country_ship_date='$ship_date'";}
					if($pack_type!="") {$pack_type_cond = " and a.pack_type='$pack_type'";$pack_type_cond2 = " and pack_type='$pack_type'";}

					$sql_dtls =("SELECT color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2");
					//echo $sql_dtls;die;
					foreach (sql_select($sql_dtls) as $row)
					{
						if ($variableSettings == 2) $index = $row[csf('color_number_id')];
						else $index = $row[csf('size_number_id')] . $color_arr[$row[csf("color_number_id")]] . $row[csf('color_number_id')];
						$amountArr[$index] += $row[csf('production_qnty')];
						$amountArrClrSize[$row['COLOR_SIZE_BREAK_DOWN_ID']] += $row[csf('production_qnty')];
					}

					// =================return qty =========================
					/*$sql_dtls = sql_select("SELECT ex_factory_qnty as production_qnty,size_number_id, color_number_id from  pro_ex_factory_mst a,wo_po_color_size_breakdown b where  a.status_active=1 and b.status_active in(1,2,3) and a.po_break_down_id=b.po_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.entry_form=85");
					foreach($sql_dtls as $row)
					{
						if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
						$returnArr[$index] += $row[csf('production_qnty')];
					}*/

					if ($variableSettings == 2) // color level
					{
						if ($db_type == 0)
						{
							$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
									from wo_po_color_size_breakdown a
									left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.status_active=1
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";

							$sql_exfac = sql_select("SELECT a.item_number_id,a.color_number_id, SUM (case when m.entry_form!=85 then ex.production_qnty else 0 end) - SUM (case when m.entry_form=85 then ex.production_qnty else 0 end) as ex_production_qnty from wo_po_color_size_breakdown a
									left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1  left join pro_ex_factory_mst m on m.id=ex.mst_id and m.status_active=1
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id");
							foreach ($sql_exfac as $row_exfac)
							{
								$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]] = $row_exfac[csf("ex_production_qnty")];
							}
						}
						else
						{
							$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
									from wo_po_color_size_breakdown a
									left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.status_active=1 left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id AND c.purpose_id NOT IN (2, 3)
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) and  b.is_deleted=0 and b.status_active =1  group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id";

							$trans_sql = sql_select("SELECT c.item_number_id,c.color_number_id, (sum( CASE WHEN b.trans_type=5 and b.production_type=10 THEN b.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 and b.production_type=10 THEN b.production_qnty ELSE 0 END)) as trans_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_color_size_breakdown c where c.id=b.color_size_break_down_id and c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and c.country_id=a.country_id and a.id=b.mst_id and a.po_break_down_id=$po_id and a.item_number_id='$item_id' and b.production_type=10 and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.item_number_id,c.color_number_id");

							$trans_qnty_arr = array();
							foreach ($trans_sql as $trans_val) {
								$trans_qnty_arr[$trans_val[csf("item_number_id")]][$trans_val[csf("color_number_id")]] = $trans_val[csf("trans_qnty")];
							}

							$sql_exfac = sql_select("SELECT a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
									left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 and  ex.is_deleted=0
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id");
							foreach ($sql_exfac as $row_exfac) {
								$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]] = $row_exfac[csf("ex_production_qnty")];
							}
						}
					}
					else if ($variableSettings == 3) //color and size level
					{
						if($qty_source==82)
						{
							$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
							from pro_garments_production_dtls a,pro_garments_production_mst b,PRO_GMTS_DELIVERY_MST c where a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0  and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' and c.id=b.delivery_mst_id and c.purpose_id=1 group by a.color_size_break_down_id");
							foreach ($prodData as $row)
							{
								$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] = $row[csf('production_qnty')];
							}
						}
						else
						{
							$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
							from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' group by a.color_size_break_down_id
							union all
							select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0  and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
							foreach ($prodData as $row)
							{
								$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] = $row[csf('production_qnty')];
							}
						}

						$sql_exfac = sql_select("SELECT a.id,SUM (case when m.entry_form!=85 then ex.production_qnty else 0 end) - SUM (case when m.entry_form=85 then ex.production_qnty else 0 end) as ex_production_qnty from wo_po_color_size_breakdown a
								left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 and ex.is_deleted=0  LEFT JOIN pro_ex_factory_mst m on m.id=ex.mst_id and m.status_active=1
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.id");
						foreach ($sql_exfac as $row_exfac) {
							// $ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]] = $row_exfac[csf("ex_production_qnty")];
							$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];
						}

						/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_number_id";*/
						$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
							from wo_po_color_size_breakdown a
							where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";
					}
					else // by default color and size level
					{
						if($qty_source==82)
						{
							$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
							from pro_garments_production_dtls a,pro_garments_production_mst b,PRO_GMTS_DELIVERY_MST c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' and c.id=b.delivery_mst_id and c.purpose_id=1 group by a.color_size_break_down_id");
							foreach ($prodData as $row)
							{
								$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] = $row[csf('production_qnty')];
							}
						}
						else
						{
							$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
							from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1  and a.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' group by a.color_size_break_down_id
							union all
							select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.is_deleted=0  and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
							foreach ($prodData as $row)
							{
								$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]] = $row[csf('production_qnty')];
							}
						}

						$sql_exfac = sql_select("SELECT a.id,SUM (case when m.entry_form!=85 then ex.production_qnty else 0 end) - SUM (case when m.entry_form=85 then ex.production_qnty else 0 end) as ex_production_qnty from wo_po_color_size_breakdown a
								left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1  LEFT JOIN pro_ex_factory_mst m on m.id=ex.mst_id and m.status_active=1 m.is_deleted=0
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.id");
						foreach ($sql_exfac as $row_exfac) {
							// $ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]] += $row_exfac[csf("ex_production_qnty")];
							$ex_fac_val_clr_Size[$row_exfac["ID"]] = $row_exfac[csf("ex_production_qnty")];
						}

						/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_number_id";*/

						$sql = "SELECT a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
								from wo_po_color_size_breakdown a
								where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";
					}

					$colorResult = sql_select($sql);
					// echo $sql;die;
					$colorHTML = "";
					$colorID = '';
					$chkColor = array();
					$i = 0;
					$totalQnty = 0;
					$colorWiseTotal = 0;
					foreach ($colorResult as $color) {
						if ($variableSettings == 2) // color level
						{
							$amount = $amountArr[$color[csf("color_number_id")]];
							$colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] + $trans_qnty_arr[$color[csf('item_number_id')]][$color[csf('color_number_id')]] - $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]] + $amount) . '" value="' . $amount . '" onkeyup="fn_colorlevel_total(' . ($i + 1) . ')"' . $disabled . '></td></tr>';
							$totalQnty += $amount;
							$colorID .= $color[csf("color_number_id")] . ",";
						}
						else //color and size level
						{
							/* $index = $color[csf("size_number_id")] . $color_arr[$color[csf("color_number_id")]] . $color[csf("color_number_id")];
							$amount = $amountArr[$index];
							$returnQty = $returnArr[$index];
							$exfac_qnty = $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];
							*/

							$clr_sz_brk_id = $color[csf('id')];

							$amount = $amountArrClrSize[$clr_sz_brk_id];
							$exfac_qnty = $ex_fac_val_clr_Size[$clr_sz_brk_id];
							$pro_qnty = $color_size_pro_qnty_array[$clr_sz_brk_id];

							if (!in_array($color[csf("color_number_id")], $chkColor)) {
								if ($i != 0) $colorHTML .= "</table></div>";
								$i = 0;
								$colorWiseTotal = 0;
								$colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
								$colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
								$chkColor[] = $color[csf("color_number_id")];
								$totalFn .= "fn_total(" . $color[csf("color_number_id")] . ");";
							}
							$colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";



							// echo $pro_qnty."-".$exfac_qnty."+".$amount."<br>";

							$colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '" data-colorSizeBreakdown="'.$clr_sz_brk_id.'"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($pro_qnty - $exfac_qnty + $amount) . '" onkeyup="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $amount . '"' . $disabled . '></td></tr>';
							$colorWiseTotal += $amount;
						}
						$i++;
					}
					//echo $colorHTML;die;
					if ($variableSettings == 2) {
						$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" value="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
					}
					echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
					if ($variableSettings == 3) echo "$totalFn;\n";
					$colorList = substr($colorID, 0, -1);
					echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
				} //end if condtion $msg
			}
			if ($qty_source == 0)
			{
				if ($variableSettings != 1) // gross level
				{
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];
					$ship_date = $result[csf('country_ship_date')];
					$pack_type = $result[csf('pack_type')];
					if($ship_date!="") {$ship_date_cond = " and a.country_ship_date='$ship_date'";$ship_date_cond2 = " and b.country_ship_date='$ship_date'";}
					if($pack_type!="") {$pack_type_cond = " and a.pack_type='$pack_type'";$pack_type_cond2 = " and pack_type='$pack_type'";}


					$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, size_number_id, color_number_id from pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2");

					foreach ($sql_dtls as $row) {
						if ($variableSettings == 2) $index = $row[csf('color_number_id')];
						else $index = $row[csf('size_number_id')] . $color_arr[$row[csf("color_number_id")]] . $row[csf('color_number_id')];
						$amountArr[$index] = $row[csf('production_qnty')];
						$amountArrClrSize[$row['COLOR_SIZE_BREAK_DOWN_ID']] += $row[csf('production_qnty')];
					}

					if ($variableSettings == 2) // color level
					{
						if ($db_type == 0) {

							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_ex_factory_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_ex_factory_dtls where is_deleted=0  ) as production_qnty from wo_po_color_size_breakdown
								where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id";
						} else {
							$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
							from wo_po_color_size_breakdown a left join pro_ex_factory_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";
						}

					}

					else if ($variableSettings == 3) //color and size level
					{

						$dtlsData = sql_select("select a.color_size_break_down_id,
													sum( a.production_qnty) as production_qnty

													from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2 and a.color_size_break_down_id<>0 and b.entry_form<>85  group by a.color_size_break_down_id");


						foreach ($dtlsData as $row) {
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut'] = $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej'] = $row[csf('reject_qty')];
						}

						$sql = "select b.id, b.item_number_id, b.size_number_id, b.color_number_id, b.order_quantity, b.plan_cut_qnty
								from wo_po_color_size_breakdown b
								where b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2 and b.is_deleted=0 and b.status_active in(1,2,3) order by b.color_number_id,b.size_order";
					}
					else // by default color and size level
					{


						$dtlsData = sql_select("select a.color_size_break_down_id,
													sum( a.production_qnty) as production_qnty

													from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2 and a.color_size_break_down_id<>0 and b.entry_form<>85  group by a.color_size_break_down_id");


						foreach ($dtlsData as $row) {
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut'] = $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej'] = $row[csf('reject_qty')];
						}

						$sql = "select b.id, b.item_number_id, b.size_number_id, b.color_number_id, b.order_quantity, b.plan_cut_qnty
								from wo_po_color_size_breakdown b
								where b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $ship_date_cond2 $pack_type_cond2 and b.is_deleted=0 and b.status_active in(1,2,3) order by b.color_number_id,b.size_order";
					}
					// echo $sql;die;
					if ($variableSettingsRej != 1) {
						$disable = "";
					} else {
						$disable = "disabled";
					}

					$colorResult = sql_select($sql);
					//print_r($sql_dtls);die;
					$colorHTML = "";
					$colorID = '';
					$chkColor = array();
					$i = 0;
					$totalQnty = 0;
					$colorWiseTotal = 0;
					foreach ($colorResult as $color) {

						if ($variableSettings == 2) // color level
						{
							$amount = $amountArr[$color[csf("color_number_id")]];
							$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
							$colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="' . ($color[csf("plan_cut_qnty")] - $color[csf("production_qnty")] + $amount) . '" value="' . $amount . '" onkeyup="fn_colorlevel_total(' . ($i + 1) . ')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_' . ($i + 1) . '" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="' . $rejectAmt . '" onkeyup="fn_colorRej_total(' . ($i + 1) . ') ' . $disable . '"></td></tr>';
							$totalQnty += $amount;
							$totalRejQnty += $rejectAmt;
							$colorID .= $color[csf("color_number_id")] . ",";
						} else //color and size level
						{
							/* $index = $color[csf("size_number_id")] . $color_arr[$color[csf("color_number_id")]] . $color[csf("color_number_id")];

							$amount = $amountArr[$index]; */
							//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];

							$clr_sz_brk_id = $color[csf('id')];
							$amount = $amountArrClrSize[$clr_sz_brk_id];

							if (!in_array($color[csf("color_number_id")], $chkColor)) {
								if ($i != 0) $colorHTML .= "</table></div>";
								$i = 0;
								$colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ': <span id="total_' . $color[csf("color_number_id")] . '"></span></h3>';
								$colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_' . $color[csf("color_number_id")] . '">';
								$chkColor[] = $color[csf("color_number_id")];
								$totalFn .= "fn_total(" . $color[csf("color_number_id")] . ");";
							}


							$tmp_col_size = "'" . $color_library[$color[csf("color_number_id")]] . "__" . $size_library[$color[csf("size_number_id")]] . "'";
							$colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";
							$cut_qnty = $color_size_qnty_array[$color[csf('id')]]['cut'];
							$rej_qnty = $color_size_qnty_array[$color[csf('id')]]['rej'];


							$colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="hidden" name="bundlemst" id="bundle_mst_' . $color[csf("color_number_id")] . ($i + 1) . '" value="' . $bundle_mst_data . '"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" value="' . $bundle_dtls_data . '" ><input type="text" name="colorSize" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:50px" placeholder="' . ($color[csf("plan_cut_qnty")] - $cut_qnty + $amount) . '" onkeyup="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $amount . '" ></td></tr>';
							//$colorWiseTotal += $amount;
							$bundle_dtls_data = "";
							$bundle_dtls_data = "";
						}
						$i++;
					}
					//echo $colorHTML;die;
					if ($variableSettings == 2) {
						$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" value="' . $result[csf('production_quantity')] . '" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="' . $totalRejQnty . '" value="' . $totalRejQnty . '" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>';
					}
					echo "$('#breakdown_td_id').html('" . addslashes(trim($colorHTML)) . "');\n";
					if ($variableSettings == 3) echo "$totalFn;\n";
					$colorList = substr($colorID, 0, -1);
					echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
				}
			}
		}
		echo "$('#shipping_status').val('" . $result[csf('shiping_status')] . "');\n";
		echo "$('#posted_msg_td_id').text('" . $msg . "');\n";
		echo "$('#is_update_mood').val('1');\n";
		echo "set_field_level_access( " . $company_id . ");\n";
	}

	exit();
}

//pro_ex_factory_mst
if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbm = str_replace("'", "", $cbm); //echo $cbm; exit();
	$net_weight = str_replace("'", "", $net_weight);
	$gross_weight = str_replace("'", "", $gross_weight);
	$is_projected_po = return_field_value("is_confirmed", "wo_po_break_down", "id=$hidden_po_break_down_id and status_active=1 and is_deleted=0");
	if ($is_projected_po == 2) {
		echo "35**Projected PO is not allow to delivery.";
		die();
	}
	//echo $cbm." ".$net_weight." ".$gross_weight; exit();
	$is_control = return_field_value("is_control", "variable_settings_production", "company_name=$cbo_company_name and variable_list=33 and page_category_id=32");
	if (!str_replace("'", "", $sewing_production_variable)) $sewing_production_variable = 3;

	/* ======================================================================== /
	/							check variable setting							/
	========================================================================= */
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$cbo_company_name and variable_list=76 and status_active=1 and is_deleted=0");
	if($wip_valuation_for_accounts==1)
	{
		/* ================================= get fabric cost =================================== */
		// $sql = "SELECT po_break_down_id as po_id,item_number_id,country_id,production_type,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs,trims_cost_per_pcs from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=8 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name";

		$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.trims_cost_per_pcs,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3,5,8) and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name order by a.production_type asc";
		// echo "10**$sql";die;
		$res = sql_select($sql);
		$cut_cost_array = array();
		$emb_cost_array = array();
		$sew_cost_array = array();
		$fab_cost_array = array();
		$cut_rate_chk_arr = array();
		$sew_rate_chk_arr = array();
		$cut_rate_count = 0;
		$sew_rate_count_arr = array();
		foreach ($res as $v)
		{
			if($v['PRODUCTION_TYPE']==1)
			{
				if($cut_rate_chk_arr[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs']!=$v['COST_OF_FAB_PER_PCS'])
				{
					$cut_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] += $v['COST_OF_FAB_PER_PCS'];
					$cut_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];

					$cut_rate_chk_arr[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs']=$v['COST_OF_FAB_PER_PCS'];
					$sew_rate_count_arr[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]++;
				}
			}
			if($v['PRODUCTION_TYPE']==3)
			{
				$emb_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['EMBEL_NAME']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
			}

			if($v['PRODUCTION_TYPE']==5)
			{
				$sew_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
				$sew_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
			}

			if($v['PRODUCTION_TYPE']==8)
			{
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
				$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
			}
		}

		/* $finishing_qty = str_replace("'","",$txt_finishing_qty);
		$reject_qnty = str_replace("'","",$txt_reject_qnty);
		$trims_cost = (($finishing_qty+$reject_qnty)*$trims_issue_avg_rate)/$finishing_qty;
		$trims_cost = number_format($trims_cost,$dec_place[3],'.','');
		$finish_oh = $finishing_qty*$cpm*$item_smv;
		$finish_oh = number_format($finish_oh,$dec_place[3],'.','');
		$cost_per_pcs = $cost_per_pcs_prev+$trims_cost+$finish_oh;
		$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */
		/* ================================== end fabric cost ========================================= */
		$color_data_array = array();
		if(str_replace("'","",$sewing_production_variable)==2)
		{
			$rowEx = array_filter(explode("**",$colorIDvalue));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['ok']+=$colorSizeNumberIDArr[1];
			}
			// ===========================
			$rowEx = array_filter(explode("**",$colorIDvalueRej));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['rej']+=$colorSizeNumberIDArr[1];
			}
		}

		if(str_replace("'","",$sewing_production_variable)==3)
		{
			$rowEx = array_filter(explode("***",$colorIDvalue));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['ok']+=$colorSizeNumberIDArr[2];
			}
			// ===========================
			$rowEx = array_filter(explode("***",$colorIDvalueRej));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['rej']+=$colorSizeNumberIDArr[2];
			}
		}
	}
	// echo "10**$cut_rate_count<pre>";print_r($cut_cost_array);die;

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}  //table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$txt_country_ship_date = date('d-M-Y',strtotime(str_replace("'","",$txt_country_ship_date)));
		if (str_replace("'", "", $txt_system_id) == "") {
			$delivery_mst_id = return_next_id("id", "pro_ex_factory_delivery_mst", 1);

			if ($db_type == 2) $mrr_cond = "and  TO_CHAR(insert_date,'YYYY')=" . date('Y', time());
			else if ($db_type == 0) $mrr_cond = "and year(insert_date)=" . date('Y', time());
			$new_sys_number = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'GDE', date("Y", time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_ex_factory_delivery_mst where company_id=$cbo_company_name and entry_form!=85 $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num"));
			// cbo_del_company
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no, mobile_no, do_no, gp_no, destination_place, forwarder,forwarder_2,source,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,escot_name,escot_mobile,depo_details, inserted_by, insert_date,cbm";
			$data_array_delivery = "(" . $delivery_mst_id . ",'" . $new_sys_number[1] . "','" . $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . "," . $cbo_location_name . "," . $new_sys_number[2] . "," . $cbo_buyer_name . "," . $cbo_transport_company . "," . $txt_ex_factory_date . "," . $txt_lock_no . "," . $txt_driver_name . "," . $txt_truck_no . "," . $txt_dl_no . "," . $txt_mobile_no . "," . $txt_do_no . "," . $txt_gp_no . "," . $txt_destination . "," . $cbo_forwarder . "," . $cbo_forwarder_2 . "," . $cbo_source . "," . $cbo_del_company . "," . $cbo_delivery_location . "," . $cbo_delivery_floor . "," . $txt_attention . "," . $txt_remarks . "," . $txt_escot_name . "," . $txt_escot_mobile . "," . $txt_depo_details . "," . $user_id . ",'" . $pc_date_time . "','" . $cbm . "')";
			//echo "10**".$data_array_delivery; exit();
			$mrr_no = $new_sys_number[0];
			$mrr_no_challan = $new_sys_number[2];
		} else {
			$delivery_mst_id = str_replace("'", "", $txt_system_id);
			$mrr_no = str_replace("'", "", $txt_system_no);
			$mrr_no_challan = str_replace("'", "", $txt_challan_no);


			$is_gate_passed = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='$mrr_no' and basis=12 and status_active=1 and is_deleted=0");
			if ($is_gate_passed != "") {
				echo "38**Gate Pass Found($is_gate_passed).New Attachment not allow!";
				disconnect($con);
				die();
			}

			$field_array_delivery = "company_id*location_id*buyer_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*forwarder_2*source*delivery_company_id*delivery_location_id*delivery_floor_id*attention*remarks*updated_by*update_date*cbm";
			$data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location_name . "*" . $cbo_buyer_name . "*" . $cbo_transport_company . "*" . $txt_ex_factory_date . "*" . $txt_lock_no . "*" . $txt_driver_name . "*" . $txt_truck_no . "*" . $txt_dl_no . "*" . $txt_mobile_no . "*" . $txt_do_no . "*" . $txt_gp_no . "*" . $txt_destination . "*" . $cbo_forwarder . "*" . $cbo_forwarder_2 . "*" . $cbo_source . "*" . $cbo_del_company . "*" . $cbo_delivery_location . "*" . $cbo_delivery_floor . "*" . $txt_attention . "*" . $txt_remarks . "*" . $user_id . "*'" . $pc_date_time . "'*'" . $cbm."'";
		}

		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$txt_country_ship_date)=="") $country_ship_date_cond=""; else $country_ship_date_cond=" and country_ship_date='$txt_country_ship_date'";

		$country_order_qty = return_field_value("sum(order_quantity)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty = return_field_value("sum(ex_factory_qnty)", "pro_ex_factory_mst", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active=1 and is_deleted=0");
		$country_exfactory_qty = $country_exfactory_qty + str_replace("'", "", $txt_ex_quantity);

		if ($country_exfactory_qty >= $country_order_qty) $country_order_status = 3;
		else $country_order_status = str_replace("'", "", $shipping_status);
		// $country_order_status=str_replace("'","",$shipping_status);
		$cbo_inco_term_id = str_replace("'", "", $cbo_inco_term_id);
		//----------Compare buyer inspection qty and ex-factory qty for validation----------------

		/*if($is_control==1 && $user_level!=2)
		{
			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}*/
		//--------------------------------------------------------------Compare end;

		$id = return_next_id("id", "pro_ex_factory_mst", 1);
		$txt_country_ship_date = date('d-M-Y',strtotime(str_replace("'","",$txt_country_ship_date)));
		$field_array1 = "id, delivery_mst_id, garments_nature, po_break_down_id,additional_info,additional_info_id, item_number_id, country_id, location, ex_factory_date, ex_factory_qnty, total_carton_qnty, challan_no, invoice_no, lc_sc_no, carton_qnty, transport_com, remarks, shiping_status, entry_break_down_type,inspection_qty_validation,shiping_mode,foc_or_claim, inco_terms,country_ship_date,pack_type,cost_rate,cost_per_pcs,inserted_by, insert_date, destinatin,net_weight,gross_weight  ";
		$data_array1 = "(" . $id . "," . $delivery_mst_id . "," . $garments_nature . "," . $hidden_po_break_down_id .  "," . $txt_add_info . "," . $hidden_add_info . ", " . $cbo_item_name . "," . $cbo_country_name . "," . $cbo_location_name . "," . $txt_ex_factory_date . "," . $txt_ex_quantity . "," . $txt_total_carton_qnty . "," . $mrr_no_challan . ",'" . $invoice_id . "','" . $lcsc_id . "'," . $txt_ctn_qnty . "," . $txt_transport_com . "," . $txt_remark . "," . $shipping_status . "," . $sewing_production_variable . "," . $cbo_ins_qty_validation_type . "," . $cbo_shipping_mode . "," . $cbo_foc_claim . "," . $cbo_inco_term_id . ",'" . $txt_country_ship_date . "'," . $txt_pack_type . ",'" . $cost_of_fab_per_pcs . "','" . $cost_per_pcs . "'," . $user_id . ",'" . $pc_date_time . "'," . $txt_detail_destination . ",'" . $net_weight . "','" . $gross_weight . "')";

		$po_id=str_replace("'","",$hidden_po_break_down_id);
		$item_id=str_replace("'","",$cbo_item_name);
		$country_id=str_replace("'","",$cbo_country_name);

		if (str_replace("'", "", $sewing_production_variable) == 4)
		{
			$actual_po_arr=explode("==",str_replace("'","",$hidden_actual_po));
			$act_data="";
			$act_poid = return_next_id("id", "PRO_EX_FACTORY_ACTUAL_PO_DETAILS", 1);
			$color_id_arr = array();
			$size_id_arr = array();
			$acc_col_size_qty_arr = array();
			$accColorIDvalue = "";
			foreach($actual_po_arr as $act_po)
			{
				$ex=explode("**",$act_po);
				if ($act_data != "") $act_data .= ",";
				$act_data .= "(" . $act_poid . "," . $id . ",'" . $ex[2]. "','" . $ex[3] . "','" . $ex[8] . "','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
				$act_poid = $act_poid + 1;
				$color_id_arr[$ex[6]] = $ex[6];
				$size_id_arr[$ex[7]] = $ex[7];
				$accColorIDvalue.= ($accColorIDvalue=="") ? $ex[7]."*".$ex[6]."*".$ex[8] : "***".$ex[7]."*".$ex[6]."*".$ex[8];
				$acc_col_size_qty_arr[$ex[6]][$ex[7]] += $ex[8];
			}
		}
		else
		{
			if(str_replace("'","",$hidden_actual_po)!="")
			{
				$actual_po_arr=explode(",",str_replace("'","",$hidden_actual_po));
				$act_data="";
				$act_poid = return_next_id("id", "PRO_EX_FACTORY_ACTUAL_PO_DETAILS", 1);
				foreach($actual_po_arr as $act_po)
				{
					$ex=explode("_",$act_po);
					if ($act_data != "") $act_data .= ",";
					$act_data .= "(" . $act_poid . "," . $id . ",'" . $ex[0]. "','" . $ex[1] . "',0,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
					$act_poid = $act_poid + 1;
				}
			}
		}


		//echo "INSERT INTO pro_ex_factory_delivery_mst (".$field_array1.") VALUES ".$data_array1;die;

		//$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		//echo "10**update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name";die;




		// pro_ex_factory_dtls table entry here ----------------------------------///

		$pack_type_cond2 = str_replace("pack_type","c.pack_type",$pack_type_cond);
		$country_ship_date_cond2 = str_replace("country_ship_date","c.country_ship_date",$country_ship_date_cond);

		$prodData = sql_select("SELECT c.id,c.size_number_id, c.color_number_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $pack_type_cond2 $country_ship_date_cond2 and a.color_size_break_down_id!=0 and a.production_type=8 and c.id=a.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.item_number_id=b.item_number_id group by c.id,c.size_number_id,c.color_number_id
								union all
								select c.id,c.size_number_id,c.color_number_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $pack_type_cond2 $country_ship_date_cond2 and a.color_size_break_down_id<>0 and a.production_type=10 and c.id=a.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.item_number_id=b.item_number_id group by c.id,c.size_number_id,c.color_number_id");
		/*echo "10**SELECT c.color_number_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 and c.id=a.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.item_number_id=b.item_number_id group by c.color_number_id
								union all
								select c.color_number_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type=10 and c.id=a.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.item_number_id=b.item_number_id group by c.color_number_id";die;*/
		foreach ($prodData as $row) {
			// echo $row[csf('color_number_id')]."==".$row[csf('production_qnty')]."<br>";
			$color_data[trim($row[csf('color_number_id')])] += $row[csf('production_qnty')];
			$index = $row[csf("size_number_id")] . $color_arr[$row[csf("color_number_id")]] . $row[csf("color_number_id")];
			$color_size_data[$index] += $row[csf('production_qnty')];

			$color_size_data2[$row['ID']] += $row[csf('production_qnty')];
		}

		$sql_exfac = sql_select("SELECT c.id, c.size_number_id, c.color_number_id, sum(case when b.entry_form<>85 then a.production_qnty else 0 end )- sum(case when b.entry_form=85 then a.production_qnty else 0 end ) as ex_production_qnty
							from pro_ex_factory_dtls a, pro_ex_factory_mst b,wo_po_color_size_breakdown c
							where b.id=a.mst_id and c.po_break_down_id=b.po_break_down_id and c.id=a.color_size_break_down_id and a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $pack_type_cond2 $country_ship_date_cond2
							group by c.id,c.size_number_id,c.color_number_id");
		$ex_fac_data = array();
		$ex_fac_color_size_data =$ex_fac_color_size_data2 = array();
		foreach ($sql_exfac as $row_exfac) {
			$ex_fac_data[$row_exfac[csf("color_number_id")]] += $row_exfac[csf("ex_production_qnty")];

			$index = $row_exfac[csf("size_number_id")] . $color_arr[$row_exfac[csf("color_number_id")]] . $row_exfac[csf("color_number_id")];
			$ex_fac_color_size_data[$index] += $row_exfac[csf("ex_production_qnty")];

			$ex_fac_color_size_data2[$row_exfac['ID']] += $row_exfac[csf("ex_production_qnty")];
		}

		// echo "<pre>";print_r($ex_fac_color_size_data);die();


		$field_array = "id,mst_id,color_size_break_down_id,production_qnty,cost_rate,cost_per_pcs,cut_fab_cot,cut_oh,print_cost,emb_cost,wash_cost,sew_oh,sew_trims_cost,fin_oh,fin_trims_cost";

		if (str_replace("'", "", $sewing_production_variable) == 2) //color level wise
		{
			$color_sizeID_arr = sql_select("SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond $pack_type_cond  and status_active in(1,2,3)  and is_deleted=0  order by id"); //and color_mst_id<>0
			$colSizeID_arr = array();
			foreach ($color_sizeID_arr as $val) {
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index] = $val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowEx = explode("**", $colorIDvalue);
			$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array = "";
			$j = 0;
			// echo "10**$user_level";die();
			foreach ($rowEx as $rowE => $val) {
				$colorSizeNumberIDArr = explode("*", $val);
				if ($is_control == 1 && $user_level != 2) {
					$garments_delivery_data = 0;
					if ($colorSizeNumberIDArr[1] > 0) {
						// echo "10**".$color_data[$colorSizeNumberIDArr[0]]."-".$ex_fac_data[$colorSizeNumberIDArr[0]];
						$garments_delivery_data = $color_data[$colorSizeNumberIDArr[0]] - $ex_fac_data[$colorSizeNumberIDArr[0]];
						if (($colorSizeNumberIDArr[1] * 1) > ($garments_delivery_data * 1)) {
							echo "35**Delivery Quantity Not Over Finish Qnty";
							check_table_status($_SESSION['menu_id'], 0);
							disconnect($con);
							die;
						}
					}
				}

				if($wip_valuation_for_accounts==1)
				{
					// cutting===========
					$cut_fab_cost = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					$cut_rate_count = $sew_rate_count_arr[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]];
					$cut_fab_cost = ($cut_rate_count) ? $cut_fab_cost/$cut_rate_count : 0;
					$cut_fab_cost = number_format($cut_fab_cost,$dec_place[3],'.','');

					$cut_oh = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$cut_oh = number_format($cut_oh,$dec_place[3],'.','');

					// ============== emb =====================
					$print_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][1]['cost_of_fab_per_pcs'];
					$emb_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][2]['cost_of_fab_per_pcs'];
					$wash_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][3]['cost_of_fab_per_pcs'];

					// ============== sew cost =========================
					$sew_oh = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$sew_oh = number_format($sew_oh,$dec_place[3],'.','');
					$sew_trims_cost = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					$sew_trims_cost = number_format($sew_trims_cost,$dec_place[3],'.','');

					//  =================== finishing ====================
					$fin_trims_cost = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					$fin_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];
					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

					$cost_per_pcs = $amount/$prod_qty;


					$cost_of_fab_per_pcs = number_format($cost_of_fab_per_pcs,$dec_place[3],'.','');
					$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
				}

				if ($j == 0) $data_array = "(" . $dtls_id . "," . $id . ",'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $cost_of_fab_per_pcs . "','" . $cost_per_pcs . "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
				else $data_array .= ",(" . $dtls_id . "," . $id . ",'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $cost_of_fab_per_pcs . "','" . $cost_per_pcs . "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
				$dtls_id = $dtls_id + 1;
				$j++;
			}
		}
		// die('10**');
		if (str_replace("'", "", $sewing_production_variable) == 3) //color and size wise
		{
			/* $color_sizeID_arr = sql_select("SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond $pack_type_cond and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order");

			$colSizeID_arr = array();
			foreach ($color_sizeID_arr as $val) {
				$index = $val[csf("size_number_id")] . $color_arr[$val[csf("color_number_id")]] . $val[csf("color_number_id")];
				$colSizeID_arr[$index] = $val[csf("id")];
			} */
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowEx = explode("***", $colorIDvalue);
			$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array = "";
			$j = 0;
			foreach ($rowEx as $rowE => $valE) {
				$colorAndSizeAndValue_arr = explode("*", $valE);
				/* $sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID . $color_arr[$colorID] . $colorID; */

				$clr_sz_brk_id   = $colorAndSizeAndValue_arr[0];
				$colorSizeValue  = $colorAndSizeAndValue_arr[1];

				// echo "10**".$index."<br>";
				if ($is_control == 1 && $user_level != 2) {
					$garments_delivery_data = 0;
					if ($colorSizeValue > 0) {
						// $garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_color_size_data[$colSizeID_arr[$index]];
						$garments_delivery_data = $color_size_data2[$clr_sz_brk_id] - $ex_fac_color_size_data2[$clr_sz_brk_id];
						// echo $colorSizeValue.">".$garments_delivery_data."<br>";
						if (($colorSizeValue * 1) > ($garments_delivery_data * 1)) {
							echo "35**Delivery Quantity Not Over Finish Qnty";
							check_table_status($_SESSION['menu_id'], 0);
							disconnect($con);
							die;
						}
					}
				}

				if($wip_valuation_for_accounts==1)
				{
					// cutting===========
					$cut_fab_cost = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
					$cut_rate_count = $sew_rate_count_arr[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID];
					$cut_fab_cost = ($cut_rate_count) ? $cut_fab_cost/$cut_rate_count : 0;
					$cut_fab_cost = number_format($cut_fab_cost,$dec_place[3],'.','');

					$cut_oh = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$cut_oh = number_format($cut_oh,$dec_place[3],'.','');

					// ============== emb =====================
					$print_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][1]['cost_of_fab_per_pcs'];
					$emb_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][2]['cost_of_fab_per_pcs'];
					$wash_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][3]['cost_of_fab_per_pcs'];

					// ============== sew cost =========================
					$sew_oh = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$sew_oh = number_format($sew_oh,$dec_place[3],'.','');
					$sew_trims_cost = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
					$sew_trims_cost = number_format($sew_trims_cost,$dec_place[3],'.','');

					//  =================== finishing ====================
					$fin_trims_cost = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
					$fin_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
					// $cost_per_pcs = $wash_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];
					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

					$cost_per_pcs = $amount/$prod_qty;


					$cost_of_fab_per_pcs = number_format($cost_of_fab_per_pcs,$dec_place[3],'.','');
					$cost_per_pcs = is_nan($cost_per_pcs) ? 0 : number_format($cost_per_pcs,$dec_place[3],'.','');
				}

				/* if($colSizeID_arr[$index]!="")
				{ */
					$field_array = "id,mst_id,color_size_break_down_id,production_qnty,cost_rate,cost_per_pcs,cut_fab_cot,cut_oh,print_cost,emb_cost,wash_cost,sew_oh,sew_trims_cost,fin_oh,fin_trims_cost";


					if ($j == 0) $data_array = "(" . $dtls_id . "," . $id . ",'" . $clr_sz_brk_id . "','" . $colorSizeValue . "','" . $cost_of_fab_per_pcs . "','" . $cost_per_pcs . "','".$cut_fab_cost."','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
					else $data_array .= ",(" . $dtls_id . "," . $id . ",'" . $clr_sz_brk_id . "','" . $colorSizeValue . "','" . $cost_of_fab_per_pcs . "','" . $cost_per_pcs . "','".$cut_fab_cost."','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
					$dtls_id = $dtls_id + 1;
					$j++;
				/* } */
			}
		}
		// echo $data_array;die;
		if (str_replace("'", "", $sewing_production_variable) == 4) //Acc po wise color and size level
		{
			$chk=0;
			$field_array = "id,mst_id,color_size_break_down_id,production_qnty";
			$color_sizeID_arr = sql_select("SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order");
			$colSizeID_arr = array();
			foreach ($color_sizeID_arr as $val)
			{
				$index = $val[csf("size_number_id")] . $color_arr[$val[csf("color_number_id")]] . $val[csf("color_number_id")];
				$colSizeID_arr[$index] = $val[csf("id")];
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowEx = explode("***", $accColorIDvalue);
			$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array = "";
			$j = 0;
			foreach ($rowEx as $rowE => $valE) {
				$colorAndSizeAndValue_arr = explode("*", $valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID . $color_arr[$colorID] . $colorID;
				// echo "10**".$index."<br>";
				if ($is_control == 1 && $user_level != 2) {
					$garments_delivery_data = 0;
					if ($colorSizeValue > 0) {
						// $garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_color_size_data[$colSizeID_arr[$index]];
						$garments_delivery_data = $color_size_data[$index] - $ex_fac_color_size_data[$index];
						// echo $colorSizeValue.">".$garments_delivery_data."<br>";
						if (($colorSizeValue * 1) > ($garments_delivery_data * 1)) {
							echo "35**Delivery Quantity Not Over Finish Qnty";
							check_table_status($_SESSION['menu_id'], 0);
							disconnect($con);
							die;
						}
					}
				}

				if ($j == 0) $data_array = "(" . $dtls_id . "," . $id . ",'" . $colSizeID_arr[$index] . "','" . $acc_col_size_qty_arr[$colorID][$sizeID] . "')";
				else $data_array .= ",(" . $dtls_id . "," . $id . ",'" . $colSizeID_arr[$index] . "','" . $acc_col_size_qty_arr[$colorID][$sizeID] . "')";
				$dtls_id = $dtls_id + 1;
				$j++;
				if($acc_col_size_qty_arr[$colorID][$sizeID] > 0)
				{
					$chk = 1;
				}
			}
			if($chk==0)
			{
				echo "35**Something went wrong! Please try again.";
				disconnect($con);
				die;
			}
		}
		// die();
		//Ref Closing if not close by Ref close page
		$shipping_status_id = str_replace("'", "", $shipping_status);
		$cbo_ref_type = 163;
		$unclose_id = 1;
		$txt_ref_cls_date = str_replace("'", "", $txt_ex_factory_date);
		//$ref_close_max_id=return_field_value("max(id) as max_id","inv_reference_closing","inv_pur_req_mst_id=$hidden_po_break_down_id and reference_type=163 and status_active in(1) and is_deleted=0","max_id");
		if ($shipping_status_id == 3) {
			$ref_id = return_next_id("id", "inv_reference_closing", 1); //closing_status
			$field_array_ref_close = "id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";
			$data_array_ref_close = "(" . $ref_id . "," . $cbo_company_name . ",'" . $txt_ref_cls_date . "'," . $cbo_ref_type . "," . $unclose_id . "," . $hidden_po_break_down_id . "," . $txt_order_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$DeliveryrID = sql_insert("inv_reference_closing", $field_array_ref_close, $data_array_ref_close, 1);
			//echo "10**INSERT INTO inv_reference_closing (".$field_array_ref_close.") VALUES ".$data_array_ref_close;die;

		}


		//echo "35**$invoice_id";disconnect($con);die;
		if ($invoice_id != "") {
			$field_array_invoice = "ex_factory_date*shipping_mode*total_carton_qnty";
			$prev_carton_qnty = return_field_value("sum(total_carton_qnty) as total_carton_qnty", "pro_ex_factory_mst", "status_active=1 and is_deleted=0 and invoice_no=$invoice_id ", "total_carton_qnty");
			if ($prev_carton_qnty == "") $prev_carton_qnty = 0;
			$tot_carton_qnty = str_replace("'", "", $txt_total_carton_qnty) + $prev_carton_qnty;
			$data_array_invoice = "" . $txt_ex_factory_date . "*" . $cbo_shipping_mode . "*'" . $tot_carton_qnty . "'";
			//$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
			$invoiceID = sql_update("com_export_invoice_ship_mst", $field_array_invoice, $data_array_invoice, "id", $invoice_id, 1);
		}

		//echo "35**$invoiceID";disconnect($con);die;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3)", 0);

		$country_wise_status = return_field_value("count(id)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active in(1,2,3) and is_deleted=0");
		if ($country_wise_status > 0) $order_status = 2;
		else $order_status = 3;
		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id", 0);

		// echo "10**" . str_replace("'", "", $hidden_po_break_down_id) ."**insert into pro_ex_factory_mst ($field_array1) values $data_array1";die;
		$rID = sql_insert("pro_ex_factory_mst", $field_array1, $data_array1, 1);
		$DeliveryrID = true;
		// echo "10**" . str_replace("'", "", $hidden_po_break_down_id) ."**insert into pro_ex_factory_delivery_mst ($field_array_delivery) values $data_array_delivery";die;
		if (str_replace("'", "", $txt_system_id) == "") {
			$DeliveryrID = sql_insert("pro_ex_factory_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
		} else {
			$DeliveryrID = sql_update("pro_ex_factory_delivery_mst", $field_array_delivery, $data_array_delivery, "id", str_replace("'", "", $txt_system_id), 1);
		}
		// echo "insert into pro_ex_factory_dtls ($field_array) values $data_array";die;
		$dtlsrID = true;
		if (str_replace("'", "", $sewing_production_variable) != 1)
		{
			$dtlsrID = sql_insert("pro_ex_factory_dtls", $field_array, $data_array, 1);
		}

		$invoiceID = true;

		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);
		$actualPoId=true;
		if(!empty($act_data))
		{
			$field_actual_po = "id,mst_id,actual_po_id,actual_po_dtls_id,ex_fact_qty,inserted_by,insert_date,status_active,is_deleted";
			$actualPoId=sql_insert("pro_ex_factory_actual_po_details",$field_actual_po,$act_data,0);
		}
		// echo "10**insert into pro_ex_factory_actual_po_details ($field_actual_po) values $act_data";die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		// echo "10**" . str_replace("'", "", $hidden_po_break_down_id) ."**$rID && $DeliveryrID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID && $actualPoId";die;

		if ($db_type == 0) {

			if (str_replace("'", "", $sewing_production_variable) != 1) {
				if ($rID && $DeliveryrID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID && $actualPoId) {
					mysql_query("COMMIT");
					echo "0**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan;
				} else {
					mysql_query("ROLLBACK");
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			} else {
				if ($rID  && $DeliveryrID && $sts_ex_mst && $sts_ex && $sts_country && $actualPoId) {
					mysql_query("COMMIT");
					echo "0**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan;
				} else {
					mysql_query("ROLLBACK");
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if (str_replace("'", "", $sewing_production_variable) != 1) {
				if ($rID  && $DeliveryrID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID && $actualPoId) {
					oci_commit($con);
					echo "0**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan;
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			} else {
				if ($rID  && $DeliveryrID && $sts_ex_mst && $sts_ex && $sts_country && $actualPoId) {
					oci_commit($con);
					echo "0**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan;
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$delivery_mst_id = str_replace("'", "", $txt_system_id);
		$mrr_no = str_replace("'", "", $txt_system_no);
		$mrr_no_challan = str_replace("'", "", $txt_challan_no);
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		$cbo_inco_term_id = str_replace("'", "", $cbo_inco_term_id);


		$is_gate_passed = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='$mrr_no' and basis=12 and status_active=1 and is_deleted=0");
		if ($is_gate_passed != "")
		{
			// only invoice no update issue id = 8540(saeed vai)
			$rID = false;
			$rID2 = false;
			$field_array1 = "remarks*invoice_no*lc_sc_no*updated_by*update_date";
			$data_array1 = "" . $txt_remark . "*'" . $invoice_id . "'*'" . $lcsc_id . "'*" . $user_id . "*'" . $pc_date_time . "'";
			$rID = sql_update("pro_ex_factory_mst", $field_array1, $data_array1, "id", "" . $txt_mst_id . "", 1);

			// ===============================================================
			$field_array_delv = "depo_details*updated_by*update_date";
			$data_array_delv = "" . $txt_depo_details . "*" . $user_id . "*'" . $pc_date_time . "'";
			$rID2 = sql_update("PRO_EX_FACTORY_DELIVERY_MST", $field_array_delv, $data_array_delv, "id", "" . $txt_system_id . "", 1);
			// echo "10**".$rID."==".$rID2;die;
			if ($db_type == 0)
			{
				if (str_replace("'", "", $sewing_production_variable) != 1) {
					if ($rID && $rID2) {
						mysql_query("COMMIT");
						echo "36**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan . "**" . $is_gate_passed. "**";
					} else {
						mysql_query("ROLLBACK");
						echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
					}
				} else {
					if ($rID && $rID2) {
						mysql_query("COMMIT");
						echo "36**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan . "**" . $is_gate_passed. "**";
					} else {
						mysql_query("ROLLBACK");
						echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
					}
				}
			}
			if ($db_type == 2 || $db_type == 1)
			{
				if (str_replace("'", "", $sewing_production_variable) != 1) {
					if ($rID && $rID2) {
						oci_commit($con);
						echo "36**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan . "**" . $is_gate_passed. "**";
					} else {
						oci_rollback($con);
						echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
					}
				} else {
					if ($rID && $rID2) {
						oci_commit($con);
						echo "36**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan . "**" . $is_gate_passed. "**";
					} else {
						oci_rollback($con);
						echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
					}
				}
			}
			// disconnect($con);
			//echo "36**Gate Pass Found($is_gate_passed).Update Restricted!";
			disconnect($con); die();
		}

		/*$buyer_id_chack=return_field_value("buyer_id","pro_ex_factory_delivery_mst","id=$delivery_mst_id","buyer_id");
		if($buyer_id_chack!=$cbo_buyer_name)
		{
			echo "50";die;
		}*/

		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}


		if(str_replace("'","",$txt_country_ship_date)!="")
		{
			$txt_country_ship_date = date('d-M-Y',strtotime(str_replace("'","",$txt_country_ship_date)));
		}
		else
		{
			$txt_country_ship_date = "";
		}
		// $txt_country_ship_date = return_field_value("country_ship_date", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

		$field_array1 = "garments_nature*location*ex_factory_date*additional_info*additional_info_id*ex_factory_qnty*total_carton_qnty*challan_no*invoice_no*lc_sc_no*carton_qnty*transport_com*remarks*shiping_status*entry_break_down_type*inspection_qty_validation*shiping_mode*foc_or_claim*inco_terms*country_ship_date*pack_type*cost_rate*cost_per_pcs*updated_by*update_date*destinatin*net_weight*gross_weight ";
		$data_array1 = "" . $garments_nature . "*" . $cbo_location_name . "*" . $txt_ex_factory_date . "*" . $txt_add_info . "*" . $hidden_add_info . "*" . $txt_ex_quantity . "*" . $txt_total_carton_qnty . "*" . $txt_challan_no . "*'" . $invoice_id . "'*'" . $lcsc_id . "'*" . $txt_ctn_qnty . "*" . $txt_transport_com . "*" . $txt_remark . "*" . $shipping_status . "*" . $sewing_production_variable . "*" . $cbo_ins_qty_validation_type . "*" . $cbo_shipping_mode . "*" . $cbo_foc_claim . "*" . $cbo_inco_term_id. "*'" . $txt_country_ship_date. "'*" . $txt_pack_type . "*'" . $cost_of_fab_per_pcs. "'*'" . $cost_per_pcs. "'*" . $user_id . "*'" . $pc_date_time  . "'*" . $txt_detail_destination . "*'" . $net_weight . "'*'" . $gross_weight . "'";
		//echo $data_array1; exit("ee");
		/* $actual_po_arr=explode(",",str_replace("'","",$hidden_actual_po));
		$act_data="";
		$act_poid = return_next_id("id", "PRO_EX_FACTORY_ACTUAL_PO_DETAILS", 1);
		foreach($actual_po_arr as $act_po)
		{
			$ex=explode("_",$act_po);
			if ($act_data != "") $act_data .= ",";
			$act_data .= "(" . $act_poid . "," . $txt_mst_id . ",'" . $ex[0]. "','" . $ex[1] . "','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
			$act_poid = $act_poid + 1;
		} */
		$acc_color_lib = return_library_array("SELECT id, gmts_color_id from WO_PO_ACC_PO_INFO_DTLS where po_break_down_id = $hidden_po_break_down_id", 'id', 'gmts_color_id');
		$acc_size_lib = return_library_array("SELECT id, gmts_size_id from WO_PO_ACC_PO_INFO_DTLS where po_break_down_id = $hidden_po_break_down_id", 'id', 'gmts_size_id');

		if (str_replace("'", "", $sewing_production_variable) == 4)
		{
			$actual_po_arr=explode("==",str_replace("'","",$hidden_actual_po));
			$act_data="";
			$act_poid = return_next_id("id", "PRO_EX_FACTORY_ACTUAL_PO_DETAILS", 1);
			$color_id_arr = array();
			$size_id_arr = array();
			$acc_col_size_qty_arr = array();
			$accColorIDvalue = "";
			foreach($actual_po_arr as $act_po)
			{
				$ex=explode("**",$act_po);
				if ($act_data != "") $act_data .= ",";
				$act_data .= "(" . $act_poid . "," . $txt_mst_id . ",'" . $ex[2]. "','" . $ex[3] . "','" . $ex[8] . "','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
				$act_poid = $act_poid + 1;
				$color_id_arr[$ex[6]] = $ex[6];
				$size_id_arr[$ex[7]] = $ex[7];
				$accColorIDvalue.= ($accColorIDvalue=="") ? $acc_size_lib[$ex[3]]."*".$acc_color_lib[$ex[3]]."*".$ex[8] : "***".$acc_size_lib[$ex[3]]."*".$acc_color_lib[$ex[3]]."*".$ex[8];
				$acc_col_size_qty_arr[$acc_color_lib[$ex[3]]][$acc_size_lib[$ex[3]]] += $ex[8];
			}
			// echo '10**'.$accColorIDvalue;die;
		}
		else
		{
			if(str_replace("'","",$hidden_actual_po)!="")
			{
				$actual_po_arr=explode(",",str_replace("'","",$hidden_actual_po));
				$act_data="";
				$act_poid = return_next_id("id", "PRO_EX_FACTORY_ACTUAL_PO_DETAILS", 1);
				foreach($actual_po_arr as $act_po)
				{
					$ex=explode("_",$act_po);
					if ($act_data != "") $act_data .= ",";
					$act_data .= "(" . $act_poid . "," . $txt_mst_id . ",'" . $ex[0]. "','" . $ex[1] . "',0,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
					$act_poid = $act_poid + 1;
				}
			}
		}

		$field_array_delivery = "company_id*location_id*buyer_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*forwarder_2*source*delivery_company_id*delivery_location_id*delivery_floor_id*attention*remarks*escot_name*escot_mobile*depo_details*updated_by*update_date*cbm";
		$data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location_name . "*" . $cbo_buyer_name . "*" . $cbo_transport_company . "*" . $txt_ex_factory_date . "*" . $txt_lock_no . "*" . $txt_driver_name . "*" . $txt_truck_no . "*" . $txt_dl_no . "*" . $txt_mobile_no . "*" . $txt_do_no . "*" . $txt_gp_no . "*" . $txt_destination . "*" . $cbo_forwarder . "*" . $cbo_forwarder_2 . "*" . $cbo_source . "*" . $cbo_del_company . "*" . $cbo_delivery_location . "*" . $cbo_delivery_floor . "*" . $txt_attention . "*" . $txt_remarks . "*" . $txt_escot_name . "*" . $txt_escot_mobile . "*" . $txt_depo_details . "*" . $user_id . "*'" . $pc_date_time . "'*'" . $cbm."'";
		//echo $data_array_delivery; exit();
		//$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $country_order_qty."**".$data_array;die;
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$txt_country_ship_date)=="") $country_ship_date_cond=""; else $country_ship_date_cond=" and country_ship_date='$txt_country_ship_date'";
		// pro_ex_factory_mst table data entry here
		$country_order_qty = return_field_value("sum(order_quantity)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty = return_field_value("sum(ex_factory_qnty)", "pro_ex_factory_mst", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
		$country_exfactory_qty = $country_exfactory_qty + str_replace("'", "", $txt_ex_quantity);

		if ($country_exfactory_qty >= $country_order_qty) $country_order_status = 3;
		else $country_order_status = str_replace("'", "", $shipping_status);
		// $country_order_status=str_replace("'","",$shipping_status);


		//----------Compare buyer inspection qty and ex-factory qty for validation----------------
		/*if($is_control==1 && $user_level!=2)
		{

			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}*/
		//--------------------------------------------------------------Compare end;









		if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '') // check is not gross level
		{
			// pro_ex_factory_dtls table entry here ----------------------------------///
			$pack_type_cond2 = str_replace("pack_type","c.pack_type",$pack_type_cond);
			$country_ship_date_cond2 = str_replace("country_ship_date","c.country_ship_date",$country_ship_date_cond);

			$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c
								where a.status_active=1 and a.mst_id=b.id and c.id=a.color_size_break_down_id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $pack_type_cond2 $country_ship_date_cond2 and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id
								union all
								select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and c.id=a.color_size_break_down_id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $pack_type_cond2 $country_ship_date_cond2 and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
			foreach ($prodData as $row)
			{
				$color_size_data[$row[csf('color_size_break_down_id')]] += $row[csf('production_qnty')];
			}

			$sql_exfac = sql_select("SELECT a.color_size_break_down_id, sum(case when b.entry_form<>85 then a.production_qnty else 0 end )- sum(case when b.entry_form=85 then a.production_qnty else 0 end ) as ex_production_qnty
								from pro_ex_factory_dtls a, pro_ex_factory_mst b,wo_po_color_size_breakdown c
								where a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name  $pack_type_cond2 $country_ship_date_cond2
								and b.id=a.mst_id and b.id !=$txt_mst_id and c.id=a.color_size_break_down_id
								group by a.color_size_break_down_id");
			foreach ($sql_exfac as $row_exfac)
			{
				$ex_fac_data[$row_exfac[csf("color_size_break_down_id")]] = $row_exfac[csf("ex_production_qnty")];
			}



			$field_array = "id,mst_id,color_size_break_down_id,production_qnty,cost_rate,cost_per_pcs,cut_fab_cot,cut_oh,print_cost,emb_cost,wash_cost,sew_oh,sew_trims_cost,fin_oh,fin_trims_cost";

			if (str_replace("'", "", $sewing_production_variable) == 2) //color level wise
			{
				$color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active in(1,2,3) and is_deleted=0  order by id"); //and color_mst_id<>0
				$colSizeID_arr = array();
				foreach ($color_sizeID_arr as $val) {
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index] = $val[csf("id")];
				}
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**", $colorIDvalue);
				$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array = "";
				$j = 0;
				foreach ($rowEx as $rowE => $val) {
					$colorSizeNumberIDArr = explode("*", $val);
					if ($is_control == 1 && $user_level != 2) {
						$garments_delivery_data = 0;
						if ($colorSizeNumberIDArr[1] > 0) {
							$garments_delivery_data = $color_size_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]] - $ex_fac_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]];
							if (($colorSizeNumberIDArr[1] * 1) > ($garments_delivery_data * 1)) {
								echo "35**Delivery Qnty Quantity Not Over Finish Qnty";
								check_table_status($_SESSION['menu_id'], 0);
								disconnect($con);
								die;
							}
						}
					}

					if($wip_valuation_for_accounts==1)
					{
						// cutting===========
						$cut_fab_cost = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
						$cut_rate_count = $sew_rate_count_arr[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]];
						$cut_fab_cost = ($cut_rate_count) ? $cut_fab_cost/$cut_rate_count : 0;
						$cut_fab_cost = number_format($cut_fab_cost,$dec_place[3],'.','');

						$cut_oh = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
						$cut_oh = number_format($cut_oh,$dec_place[3],'.','');

						// ============== emb =====================
						$print_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][1]['cost_of_fab_per_pcs'];
						$emb_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][2]['cost_of_fab_per_pcs'];
						$wash_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]][3]['cost_of_fab_per_pcs'];

						// ============== sew cost =========================
						$sew_oh = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
						$sew_oh = number_format($sew_oh,$dec_place[3],'.','');
						$sew_trims_cost = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
						$sew_trims_cost = number_format($sew_trims_cost,$dec_place[3],'.','');

						//  =================== finishing ====================
						$fin_trims_cost = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
						$fin_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
						$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
						// $cost_per_pcs = $wash_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];
						$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
						$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

						$cost_per_pcs = $amount/$prod_qty;


						$cost_of_fab_per_pcs = number_format($cost_of_fab_per_pcs,$dec_place[3],'.','');
						$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
					}
					if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $cost_of_fab_per_pcs. "','" . $cost_per_pcs. "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
					else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $cost_of_fab_per_pcs. "','" . $cost_per_pcs. "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
					$dtls_id = $dtls_id + 1;
					$j++;
				}
			}

			if (str_replace("'", "", $sewing_production_variable) == 3) //color and size wise
			{
				/* $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order");
				$colSizeID_arr = array();
				foreach ($color_sizeID_arr as $val) {
					$index = $val[csf("size_number_id")] . $color_arr[$val[csf("color_number_id")]] . $val[csf("color_number_id")];
					$colSizeID_arr[$index] = $val[csf("id")];
				}
 */
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = explode("***", $colorIDvalue);
				$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array = "";
				$j = 0;
				foreach ($rowEx as $rowE => $valE) {
					$colorAndSizeAndValue_arr = explode("*", $valE);
					/* $sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID . $color_arr[$colorID] . $colorID; */
					$clr_sz_brk_id   = $colorAndSizeAndValue_arr[0];
					$colorSizeValue  = $colorAndSizeAndValue_arr[1];


					if ($is_control == 1 && $user_level != 2) {
						$garments_delivery_data = 0;
						if ($colorSizeValue > 0) {
							$garments_delivery_data = $color_size_data[$clr_sz_brk_id] - $ex_fac_data[$clr_sz_brk_id];
							if (($colorSizeValue * 1) > ($garments_delivery_data * 1)) {
								echo "35**Delivery Quantity Not Over Finish Qnty";
								check_table_status($_SESSION['menu_id'], 0);
								disconnect($con);
								die;
							}
						}
					}

					if($wip_valuation_for_accounts==1)
					{
						// cutting===========
						$cut_fab_cost = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
						$cut_rate_count = $sew_rate_count_arr[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID];
						$cut_fab_cost = ($cut_rate_count) ? $cut_fab_cost/$cut_rate_count : 0;
						// echo "10**".$cut_fab_cost."/".$cut_rate_count."<br>";die;
						$cut_fab_cost = number_format($cut_fab_cost,$dec_place[3],'.','');

						$cut_oh = $cut_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
						$cut_oh = number_format($cut_oh,$dec_place[3],'.','');

						// ============== emb =====================
						$print_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][1]['cost_of_fab_per_pcs'];
						$emb_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][2]['cost_of_fab_per_pcs'];
						$wash_cost = $emb_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID][3]['cost_of_fab_per_pcs'];

						// ============== sew cost =========================
						$sew_oh = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
						$sew_oh = number_format($sew_oh,$dec_place[3],'.','');
						$sew_trims_cost = $sew_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
						$sew_trims_cost = number_format($sew_trims_cost,$dec_place[3],'.','');

						//  =================== finishing ====================
						$fin_trims_cost = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
						$fin_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
						$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
						// $cost_per_pcs = $wash_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];
						$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
						$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

						$cost_per_pcs = $amount/$prod_qty;

						$cost_of_fab_per_pcs = number_format($cost_of_fab_per_pcs,$dec_place[3],'.','');
						$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
					}

					/* if($colSizeID_arr[$index]!="")
					{ */
						if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",'" . $clr_sz_brk_id . "','" . $colorSizeValue . "','" . $cost_of_fab_per_pcs. "','" . $cost_per_pcs. "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
						else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",'" . $clr_sz_brk_id . "','" . $colorSizeValue . "','" . $cost_of_fab_per_pcs. "','" . $cost_per_pcs. "','" . $cut_fab_cost . "','" . $cut_oh . "','" . $print_cost . "','" . $emb_cost . "','" . $wash_cost . "','" . $sew_oh . "','" . $sew_trims_cost . "','" . $fin_oh . "','" . $fin_trims_cost . "')";
						$dtls_id = $dtls_id + 1;
						$j++;
					// }
				}
			}

			if (str_replace("'", "", $sewing_production_variable) == 4) //acc po wise color and size wise
			{
				$chk=0;
				$field_array = "id,mst_id,color_size_break_down_id,production_qnty";
				$color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order");
				$colSizeID_arr = array();
				foreach ($color_sizeID_arr as $val) {
					$index = $val[csf("size_number_id")] . $color_arr[$val[csf("color_number_id")]] . $val[csf("color_number_id")];
					$colSizeID_arr[$index] = $val[csf("id")];
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				// echo '10**'.$accColorIDvalue;die;
				$rowEx = explode("***", $accColorIDvalue);
				$dtls_id = return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array = "";
				$j = 0;
				foreach ($rowEx as $rowE => $valE) {
					$colorAndSizeAndValue_arr = explode("*", $valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID . $color_arr[$colorID] . $colorID;

					if ($is_control == 1 && $user_level != 2) {
						$garments_delivery_data = 0;
						if ($colorSizeValue > 0) {
							$garments_delivery_data = $color_size_data[$colSizeID_arr[$index]] - $ex_fac_data[$colSizeID_arr[$index]];
							if (($colorSizeValue * 1) > ($garments_delivery_data * 1)) {
								echo "35**Delivery Quantity Not Over Finish Qnty";
								check_table_status($_SESSION['menu_id'], 0);
								disconnect($con);
								die;
							}
						}
					}


					if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",'" . $colSizeID_arr[$index] . "','" . $acc_col_size_qty_arr[$colorID][$sizeID] . "')";
					else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",'" . $colSizeID_arr[$index] . "','" . $acc_col_size_qty_arr[$colorID][$sizeID] . "')";
					$dtls_id = $dtls_id + 1;
					$j++;
					if($acc_col_size_qty_arr[$colorID][$sizeID] > 0)
					{
						$chk = 1;
					}
				}

				if($chk==0)
				{
					echo "35**Something went wrong! Please try again.";
					disconnect($con);
					die;
				}
			}
			//$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		} //end cond
		//=======Ref Closing if not close by Ref close page=====
		$shipping_status_id = str_replace("'", "", $shipping_status);
		$cbo_ref_type = 163;
		$unclose_id = 1;
		$txt_ref_cls_date = str_replace("'", "", $txt_ex_factory_date);
		$ref_close_max_id = return_field_value("max(id) as max_id", "inv_reference_closing", "inv_pur_req_mst_id=$hidden_po_break_down_id and reference_type=163 and status_active in(1) and is_deleted=0", "max_id");
		if ($shipping_status_id == 3) {
			$ref_id = return_next_id("id", "inv_reference_closing", 1); //closing_status
			$field_array_ref_close = "id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";
			$data_array_ref_close = "(" . $ref_id . "," . $cbo_company_name . ",'" . $txt_ref_cls_date . "'," . $cbo_ref_type . "," . $unclose_id . "," . $hidden_po_break_down_id . "," . $txt_order_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$DeliveryrID = sql_insert("inv_reference_closing", $field_array_ref_close, $data_array_ref_close, 1);
			//echo "10**INSERT INTO inv_reference_closing (".$field_array_ref_close.") VALUES ".$data_array_ref_close;die;
		} else {
			$shipping_status_id = str_replace("'", "", $shipping_status); //updated_by*update_date
			$cbo_ref_type = 163;
			$unclose_id = 0;
			$txt_ref_cls_date = str_replace("'", "", $txt_ex_factory_date);
			$DeliveryrID = execute_query("update inv_reference_closing set closing_status=$unclose_id,closing_date='" . $txt_ref_cls_date . "',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where inv_pur_req_mst_id=$hidden_po_break_down_id and id=$ref_close_max_id", 1);
			//echo "10**update inv_reference_closing set closing_status=$unclose_id,closing_date='".$txt_ref_cls_date."',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where inv_pur_req_mst_id=$hidden_po_break_down_id and id=$ref_close_max_id";die;
		}

		// echo "10**INSERT INTO pro_ex_factory_dtls (".$field_array.") VALUES ".$data_array;die;
		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name $pack_type_cond $country_ship_date_cond and status_active in(1,2,3)", 0);

		$country_wise_status = return_field_value("count(id)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active in(1,2,3) and is_deleted=0");
		if ($country_wise_status > 0) $order_status = 2;
		else $order_status = 3;
		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id", 0);

		$dtlsrDelete = execute_query("UPDATE pro_ex_factory_dtls set status_active=0,is_deleted=1 where mst_id=$txt_mst_id", 1);

		$actualPoDelete = execute_query("UPDATE pro_ex_factory_actual_po_details set status_active=0,is_deleted=1 where mst_id=$txt_mst_id", 1);

		$rID = $deliveryrID = $dtlsrID =$actualPoId= $invoiceID = true;
		$rID = sql_update("pro_ex_factory_mst", $field_array1, $data_array1, "id", "" . $txt_mst_id . "", 1);
		//echo $rID; exit();
		$deliveryrID = sql_update("pro_ex_factory_delivery_mst", $field_array_delivery, $data_array_delivery, "id", "" . $delivery_mst_id . "", 1);

		if(!empty($act_data))
		{
			$field_actual_po = "id,mst_id,actual_po_id,actual_po_dtls_id,ex_fact_qty,updated_by,update_date,status_active,is_deleted";
			$actualPoId=sql_insert("pro_ex_factory_actual_po_details",$field_actual_po,$act_data,0);
		}

		if (str_replace("'", "", $sewing_production_variable) != 1)
		{
			$dtlsrID = sql_insert("pro_ex_factory_dtls", $field_array, $data_array, 1);
		}
		if ($invoice_id != "") {
			$field_array_invoice = "ex_factory_date*shipping_mode*total_carton_qnty";
			$prev_carton_qnty = return_field_value("sum(total_carton_qnty) as total_carton_qnty", "pro_ex_factory_mst", "status_active=1 and is_deleted=0 and invoice_no=$invoice_id and id<>$txt_mst_id ", "total_carton_qnty");
			if ($prev_carton_qnty == "") $prev_carton_qnty = 0;
			$tot_carton_qnty = str_replace("'", "", $txt_total_carton_qnty) + $prev_carton_qnty;
			//echo "10**".$tot_carton_qnty; die;
			$data_array_invoice = "" . $txt_ex_factory_date . "*" . $cbo_shipping_mode . "*'" . $tot_carton_qnty . "'";
			$invoiceID = sql_update("com_export_invoice_ship_mst", $field_array_invoice, $data_array_invoice, "id", $invoice_id, 1);
			//$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
		}

		// echo "10**".$act_data; die;

		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);

		// echo '10**'.$rID .'**'. $deliveryrID .'**'. $sts_country .'**'. $sts_ex .'**'. $sts_ex_mst .'**'. $actualPoId .'**'. $actualPoDelete;die;
		//echo '10**'.$rID .'**'. $deliveryrID .'**'. $dtlsrID .'**'. $sts_country .'**'. $sts_ex .'**'. $sts_ex_mst .'**'. $dtlsrDelete .'**'. $actualPoId .'**'. $actualPoDelete; exit();
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if ($db_type == 0) {
			if (str_replace("'", "", $sewing_production_variable) != 1) {
				if ($rID && $deliveryrID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $dtlsrDelete && $actualPoId && $actualPoDelete) {
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan."**".$act_data;
				} else {
					mysql_query("ROLLBACK");
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			} else {
				if ($rID && $deliveryrID && $sts_country && $sts_ex && $sts_ex_mst && $actualPoId && $actualPoDelete) {
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan."**".$act_data;
				} else {
					mysql_query("ROLLBACK");
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
				}
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if (str_replace("'", "", $sewing_production_variable) != 1) {
				if ($rID && $deliveryrID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $dtlsrDelete && $actualPoId && $actualPoDelete) {
					oci_commit($con);
					echo "1**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan."**".$act_data;
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id)."**INSERT INTO pro_ex_factory_actual_po_details (".$field_actual_po.") VALUES ".$act_data;
				}
			} else {
				if ($rID && $deliveryrID && $sts_country && $sts_ex && $sts_ex_mst && $actualPoId && $actualPoDelete) {
					oci_commit($con);
					echo "1**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no . "**" . $mrr_no_challan."**".$act_data;
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $hidden_po_break_down_id)."**INSERT INTO pro_ex_factory_actual_po_details (".$field_actual_po.") VALUES ".$act_data;
				}
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$delivery_mst_id = str_replace("'", "", $txt_system_id);
		$mrr_no = str_replace("'", "", $txt_system_no);
		$mrr_no_challan = str_replace("'", "", $txt_challan_no);

		$is_gate_passed = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='$mrr_no' and basis=12 and status_active=1 and is_deleted=0");
		if ($is_gate_passed != "") {
			echo "37**Gate Pass Found($is_gate_passed).Delete operation not allow!";
			disconnect($con);
			die();
		}

		$is_return = return_field_value("challan_no","pro_ex_factory_mst","challan_no='$mrr_no' and ENTRY_FORM=85 and status_active=1 and is_deleted=0");
		if($is_return != "")
		{
			echo "37**Return Challan Found($is_return).Delete operation not allow!";
			disconnect($con); die();
		}

		$country_order_qty = return_field_value("sum(order_quantity)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty = return_field_value("sum(ex_factory_qnty)", "pro_ex_factory_mst", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

		if ($country_exfactory_qty >= $country_order_qty) $country_order_status = 3;
		else if ($country_exfactory_qty > 0 && $country_exfactory_qty < $country_order_qty) $country_order_status = 2;
		else $country_order_status = 1;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);

		$country_wise_status = return_field_value("count(id)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if ($country_wise_status > 0 && $country_exfactory_qty > 0) $order_status = 2;
		else if ($country_wise_status > 0 && $country_exfactory_qty <= 0) $order_status = 1;
		else $order_status = 3;

		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id", 1);
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);

		$rID = sql_delete("pro_ex_factory_mst", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'id', $txt_mst_id, 1);
		$dtlsrID = sql_delete("pro_ex_factory_dtls", "status_active*is_deleted", "0*1", 'mst_id', $txt_mst_id, 1);

		$actualPoDelete = execute_query("delete from pro_ex_factory_actual_po_details where mst_id=$txt_mst_id", 1);

		if ($db_type == 0) {
			if ($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $actualPoDelete) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $actualPoDelete) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}


if ($action == "ex_factory_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../" . $data[5], 1, 1, $unicode, '', '');
	//print_r ($data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$location_library_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library_arr = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$order_sql = sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach ($order_sql as $row) {
		$order_job_arr[$row[csf("id")]]['po_number'] = $row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name'] = $row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id'] = $row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
	}

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,delivery_company_id,delivery_location_id,delivery_floor_id,remarks from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$remarks = $row[csf("remarks")];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");

?>
	<div style="width:910px; margin-top:5px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:20px;">
			<tr>
				<td rowspan="2" align="center"><img src="../<? echo $data[5] . $image_location; ?>" height="60" width="200"></td>
				<td colspan="4" align="center" style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<td rowspan="2" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="center" style="font-size:14px;" valign="top">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					$company_address = "";
					foreach ($nameArray as $result) {
					?>
						<? if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", "; ?>
						<? if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", "; ?>
						<? if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", "; ?>
						<? if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", "; ?>
						<? if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . ", "; ?>
						<? if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", "; ?>
						<? if ($result[csf('province')] != "") $company_address .= $result[csf('province')]; ?>
						<? if ($result[csf('country_id')] != 0) $company_address .= $country_arr[$result[csf('country_id')]] . ", "; ?><br>
						<? if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", "; ?>
					<? if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
					}
					$company_address = chop($company_address, " , ");
					echo $company_address;
					?>
				</td>
			</tr>
			<?
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
			//echo $supplier_sql;die;

			?>
			<tr>
				<td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<? // echo $data[3];
																										?></strong></td>
				<td style="font-size:16px;">Date : <? echo change_date_format($data[2]); ?></td>
			</tr>
			<tr>
				<td width="100" valign="top" style="font-size:16px;"><strong>Name:</strong></td>
				<td width="200" valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?></td>
				<td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
				<td width="120" valign="top" style="font-size:16px;"><? echo $challan_no; ?> </td>
				<td width="80" valign="top" style="font-size:16px;"><strong>DL/NO:</strong></td>
				<td valign="top" style="font-size:16px;"><? echo $dl_no; ?> </td>
			</tr>

			<tr>
				<td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
				<td colspan="3" valign="top" style="font-size:16px;"><? echo $address_1 . "<br>";
																		if ($contact_no != "") echo "Phone : " . $contact_no; ?> </td>
				<td style="font-size:16px;"><strong>Truck No:</strong></td>
				<td style="font-size:16px;"><? echo $truck_no; ?> </td>
			</tr>
			<tr>
				<td style="font-size:16px;"><strong>Destination :</strong></td>
				<td style="font-size:16px;"><? echo $destination_place; ?> </td>
				<td valign="top" style="font-size:16px;"><strong>Driver Name :</strong></td>
				<td valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
				<td style="font-size:16px;"><strong>Lock No :</strong></td>
				<td style="font-size:16px;"><? echo $lock_no; ?> </td>
			</tr>
			<tr>
				<td style="font-size:16px;width:150px;"><strong>Delivery Company :</strong></td>
				<td style="font-size:16px;"><? echo $company_library[$delivery_company]; ?> </td>
				<td style="font-size:16px;width:160px;"><strong>Delivery Location :</strong></td>
				<td style="font-size:16px;"><? echo $location_library_arr[$delivery_location]; ?> </td>
				<td style="font-size:16px;width:150px;"><strong>Delivery Floor :</strong></td>
				<td style="font-size:16px;"><? echo $floor_library_arr[$delivery_floor]; ?> </td>

			</tr>
			<tr>
				<td style="font-size:16px;width:150px;"><strong>Remarks :</strong></td>
				<td style="font-size:16px;"><? echo $remarks; ?> </td>
				<td style="font-size:16px;width:160px;"><strong></strong></td>
				<td style="font-size:16px;"> </td>
				<td style="font-size:16px;width:150px;"><strong></strong></td>
				<td style="font-size:16px;"> </td>

			</tr>
		</table><br>
		<?
		//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
		if ($db_type == 2) {
			$sql = "SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty,shiping_mode, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po  from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
		} else if ($db_type == 0) {
			$sql = "SELECT po_break_down_id, group_concat(invoice_no) as invoice_no,shiping_mode, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks ,group_concat(actual_po) as actual_po from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
		}
		//echo $sql;die;
		$result = sql_select($sql);
		$table_width = 850;
		$col_span = 6;
		?>

		<div style="width:<? echo $table_width; ?>px;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="120">Style Ref.</th>
					<th width="120">Order No</th>
					<th width="100">Buyer</th>
					<th width="200">Invoice No</th>
					<th width="50">Ship Mode</th>
					<th width="50">NO Of Carton</th>
					<th>Quantity</th>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;">
								<p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} else  echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td style="font-size:12px;" align="center">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?> </p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>

				<tr>
					<td colspan="<? echo $col_span; ?>" align="right" style="font-size:12px;"><strong>Grand Total :</strong></td>
					<td align="right" style="font-size:12px;"><strong><? echo number_format($tot_carton_qnty, 0, "", ""); ?></strong></td>
					<td align="right" style="font-size:12px;"><strong><? echo number_format($tot_qnty, 0, "", ""); ?></strong></td>
				</tr>
			</table>
			<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
			<script type="text/javascript" src="../<? echo $data[5]; ?>js/jquery.js"></script>
			<script type="text/javascript" src="../<? echo $data[5]; ?>js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
		<?
		echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
		?>
	</div>
<?
	exit();
}

if ($action == "ex_factory_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../" . $data[5], 1, 1, $unicode, '', '');
	//print_r ($data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$location_library_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library_arr = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$order_sql = sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach ($order_sql as $row) {
		$order_job_arr[$row[csf("id")]]['po_number'] = $row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name'] = $row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id'] = $row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
	}

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier,forwarder, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,delivery_company_id,delivery_location_id,delivery_floor_id from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("forwarder")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");

?>
	<div style="width:910px; margin-top:5px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:20px;">
			<tr>
				<td rowspan="2" align="center"><img src="../<? echo $data[5] . $image_location; ?>" height="60" width="200"></td>
				<td colspan="4" align="center" style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<td rowspan="2" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="center" style="font-size:14px;" valign="top">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					$company_address = "";
					foreach ($nameArray as $result) {
					?>
						<? if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", "; ?>
						<? if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", "; ?>
						<? if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", "; ?>
						<? if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", "; ?>
						<? if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . ", "; ?>
						<? if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", "; ?>
						<? if ($result[csf('province')] != "") $company_address .= $result[csf('province')]; ?>
						<? if ($result[csf('country_id')] != 0) $company_address .= $country_arr[$result[csf('country_id')]] . ", "; ?><br>
						<? if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", "; ?>
					<? if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
					}
					$company_address = chop($company_address, " , ");
					echo $company_address;
					?>
				</td>
			</tr>
			<?
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
			//echo $supplier_sql;die;

			?>
			<tr>
				<td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<? // echo $data[3];
																										?></strong></td>
				<td style="font-size:16px;">Date : <? echo change_date_format($data[2]); ?></td>
			</tr>
			<tr>
				<td width="100" valign="top" style="font-size:16px;"><strong>Name:</strong></td>
				<td width="200" valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?></td>
				<td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
				<td width="120" valign="top" style="font-size:16px;"><? echo $challan_no; ?> </td>
				<td width="80" valign="top" style="font-size:16px;"><strong>DL/NO:</strong></td>
				<td valign="top" style="font-size:16px;"><? echo $dl_no; ?> </td>
			</tr>

			<tr>
				<td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
				<td colspan="3" valign="top" style="font-size:16px;"><? echo $address_1 . "<br>";
																		if ($contact_no != "") echo "Phone : " . $contact_no; ?> </td>
				<td style="font-size:16px;"><strong>Truck No:</strong></td>
				<td style="font-size:16px;"><? echo $truck_no; ?> </td>
			</tr>
			<tr>
				<td style="font-size:16px;"><strong>Destination :</strong></td>
				<td style="font-size:16px;"><? echo $destination_place; ?> </td>
				<td valign="top" style="font-size:16px;"><strong>Driver Name :</strong></td>
				<td valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
				<td style="font-size:16px;"><strong>Lock No :</strong></td>
				<td style="font-size:16px;"><? echo $lock_no; ?> </td>
			</tr>
			<tr>
				<td style="font-size:16px;width:150px;"><strong>Delivery Company :</strong></td>
				<td style="font-size:16px;"><? echo $company_library[$delivery_company]; ?> </td>
				<td style="font-size:16px;width:160px;"><strong>Delivery Location :</strong></td>
				<td style="font-size:16px;"><? echo $location_library_arr[$delivery_location]; ?> </td>
				<td style="font-size:16px;width:150px;"><strong>Delivery Floor :</strong></td>
				<td style="font-size:16px;"><? echo $floor_library_arr[$delivery_floor]; ?> </td>

			</tr>
		</table><br>
		<?
		//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
		if ($db_type == 2) {
			$sql = "SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty,shiping_mode, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po  from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
		} else if ($db_type == 0) {
			$sql = "SELECT po_break_down_id, group_concat(invoice_no) as invoice_no,shiping_mode, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks ,group_concat(actual_po) as actual_po from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
		}
		//echo $sql;die;
		$result = sql_select($sql);
		$table_width = 970;
		$col_span = 7;
		?>

		<div style="width:<? echo $table_width; ?>px;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="120">Style Ref.</th>
					<th width="120">Order No</th>
					<th width="120">Act. PO No</th>
					<th width="100">Buyer</th>
					<th width="200">Invoice No</th>
					<th width="50">Ship Mode</th>
					<th width="50">NO Of Carton</th>
					<th>Quantity</th>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;">
								<p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} ?>&nbsp;</p>
							</td>

							<td style="font-size:12px;">
								<p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td style="font-size:12px;" align="center">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?> </p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>

				<tr>
					<td colspan="<? echo $col_span; ?>" align="right" style="font-size:12px;"><strong>Grand Total :</strong></td>
					<td align="right" style="font-size:12px;"><strong><? echo number_format($tot_carton_qnty, 0, "", ""); ?></strong></td>
					<td align="right" style="font-size:12px;"><strong><? echo number_format($tot_qnty, 0, "", ""); ?></strong></td>
				</tr>
			</table>
			<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
			<script type="text/javascript" src="../<? echo $data[5]; ?>js/jquery.js"></script>
			<script type="text/javascript" src="../<? echo $data[5]; ?>js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
		<?
		// echo signature_table(63, $data[0], $table_width."px");
		echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
		?>
	</div>
<?
	exit();
}

if ($action == "ex_factory_print_new") {
	extract($_REQUEST);
	$data = explode('*', $data);
	if ($data[5] == "" or $data[5] == 0) {
		$data[5] = $data[0];
	}
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	$sys_arr = array();
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
	}
	$sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
?>
	<div style="width:1000px; margin-top:10px;">
		<table width="1000" cellspacing="0" align="right" style="margin-bottom:20px;">
			<tr>
				<td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="60" width="200"></td>
				<td colspan="4" align="center" style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			    <td rowspan="2" id="barcode_img_id"></td>
				<td></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="center" style="font-size:12px;">
					<?

					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[5]");
					$company_address = "";
					foreach ($nameArray as $result) {

						if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
						if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
						if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
						if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
						if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
						if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
						if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
						if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
							if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
						}
						if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
						if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
					}
					$company_address = chop($company_address, " , ");
					echo $company_address;
					?> <br>
					<span style="font-size:16px;">100% Export Oriented</span><br>
					<span style="font-size:22px;">Delivery Challan</span>
				</td>
			</tr>
			<?
			//echo "select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder";
			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}

			//echo $supplier_sql;die;

			?>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
			<tr style="font-size:15px;">
				<td width="100" valign="top"><strong>Challan No :</strong></td>
				<td width="200" valign="top"><? echo $system_num; ?></td>
				<td width="100" valign="top"><strong>Driver Name :</strong></td>
				<td width="120" valign="top"><? echo $driver_name; ?> </td>
				<td width="80" valign="top"><strong>Date:</strong></td>
				<td valign="top"><? echo change_date_format($data[2]); ?> </td>
			</tr>
			<tr style="font-size:15px;">
				<td valign="top"><strong><? if ($forwarder > 0) {
												echo 'C&F Name:';
											} else {
												echo 'Forwarding Agent';
											} ?></strong></td>
				<td valign="top"><? if ($forwarder > 0) {
										echo $supplier_library[$forwarder];
									} else {
										echo $supplier_library[$forwarder_2];
									}  ?></td>
				<td valign="top"><strong>Mobile Num :</strong></td>
				<td valign="top"><? echo $mobile_no; ?> </td>
				<td valign="top"><strong>Do No:</strong></td>
				<td valign="top"><? echo $do_no; ?> </td>
			</tr>
			<tr style="font-size:15px;">
				<td valign="top"><strong>Address:</strong></td>
				<td valign="top"><? echo $address_1 . "<br>";
									if ($contact_no != "") echo "Phone : " . $contact_no; ?> </td>
				<td><strong>DL No:</strong></td>
				<td><? echo $dl_no; ?> </td>
				<td><strong>GP No:</strong></td>
				<td><? echo $gp_no_arr[$system_num]; ?> </td>
			</tr>
			<tr style="font-size:15px;">
				<td valign="top"><strong>Trns. Comp:</strong></td>
				<td valign="top"><? echo $supplier_library[$supplier_name]; ?> </td>
				<td><strong>Truck No:</strong></td>
				<td><? echo $truck_no; ?> </td>
				<td><strong>Lock No:</strong></td>
				<td><? echo $lock_no; ?> </td>
			</tr>
			<tr style="font-size:15px;">
				<td><strong>Delivery Company:</strong></td>
				<td><? echo $supplier_library[$delivery_company]; ?> </td>
				<td><strong>Delivery Location:</strong></td>
				<td><? echo $location_library[$delivery_location]; ?> </td>
				<td><strong>Delivery Floor:</strong></td>
				<td><? echo $floor_library[$delivery_floor]; ?> </td>

			</tr>
			<tr style="font-size: 15px">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><strong>Final Destination</strong>:</td>
				<td><? echo $destination_place; ?></td>
			</tr>

		</table><br>
		<?
		//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

		if ($db_type == 2) {
			$sql = "SELECT c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
		} else if ($db_type == 0) {
			$sql = "SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks,group_concat(actual_po) as actual_po ,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
		}
		//echo $sql;
		$result = sql_select($sql);
		$table_width = 850;
		$col_span = 10;
		?>

		<div style="width:<? echo $table_width; ?>px;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="20">SL</th>
					<th width="60">Buyer</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="60">Country</th>
					<th width="60">Country Short Name</th>
					<th width="130">Item Name</th>
					<th width="150">Invoice No</th>
					<th width="50">Ship Mode</th>
					<th width="50">FOC/Claim</th>
					<th width="50">Delivery Qnty</th>
					<th width="50">NO Of Carton</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><?
									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} else  echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="center" style="font-size:12px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="center" style="font-size:12px;">
								<p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td style="font-size:12px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
				<tr bgcolor="#CCCCCC">
					<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>

					<td align="right" style="font-size:12px;"><? echo number_format($tot_qnty, 0, "", ""); ?></td>
					<td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty, 0, "", ""); ?></td>
					<td align="right" style="font-size:12px;">&nbsp;</td>
				</tr>

			</table>
			<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
		</div>
		<?
		// echo signature_table(63, $data[0], $table_width."px");
		echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
		?>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
		</script>
	</div>
<?
	exit();
}

if ($action == "ex_factory_print_new_11") {
    extract($_REQUEST);
    $data = explode('*', $data);
    if ($data[5] == "" or $data[5] == 0) {
        $data[5] = $data[0];
    }
    $id_ref = str_replace("'", "", $data[4]);
    echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
    //print_r ($data);
    $actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
    $buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
    $invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
    $country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
    //$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

    //echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
    $delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, remarks, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id, attention from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
    $sys_arr = array();
    foreach ($delivery_mst_sql as $row) {
        $supplier_name = $row[csf("transport_supplier")];
        $driver_name = $row[csf("driver_name")];
        $truck_no = $row[csf("truck_no")];
        $attention = $row[csf("attention")];
        $dl_no = $row[csf("dl_no")];
        $lock_no = $row[csf("lock_no")];
        $destination_place = $row[csf("destination_place")];
        $remarks = $row[csf("remarks")];
        $challan_no = $row[csf("challan_no")];
        $sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
        $mobile_no = $row[csf("mobile_no")];
        $do_no = $row[csf("do_no")];
        $gp_no = $row[csf("gp_no")];
        $forwarder = $row[csf("forwarder")];
        $forwarder_2 = $row[csf("forwarder_2")];
        $system_num = $row[csf("sys_number")];
        $delivery_company = $row[csf("delivery_company_id")];
        $delivery_location = $row[csf("delivery_location_id")];
        $delivery_floor = $row[csf("delivery_floor_id")];
        $sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
    }
    $sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
    $gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
    $image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
    //echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
    ?>
    <div style="width:850px; margin-top:10px;">
        <table width="850" cellspacing="0" align="right">
            <tr>
                <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="60" width="200"></td>
                <td colspan="4" align="center" style="font-size:xx-large;"><strong><? echo $company_library[$data[5]]; ?></strong></td>
                <td rowspan="2" id="barcode_img_id"></td>
            </tr>
            <tr class="form_caption">
                <td colspan="4" align="center" style="font-size:12px;">
                    <?

                    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[5]");
                    $company_address = "";
                    foreach ($nameArray as $result) {

                        if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
                        if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
                        if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
                        if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
                        if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
                        if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
                        if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
                        if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
                            if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
                        }
                        if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
                        if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
                    }
                    $company_address = chop($company_address, " , ");
                    echo $company_address;
                    ?> <br>
                    <span style="font-size:16px;">100% Export Oriented</span><br>
                    <span style="font-size:22px;">Delivery Challan</span>
                </td>
            </tr>
            <?
            //echo "select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder";
            if ($forwarder > 0) {
                $supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
                foreach ($supplier_sql as $row) {

                    $address_1 = $row[csf("address_1")];
                    $address_2 = $row[csf("address_2")];
                    $address_3 = $row[csf("address_3")];
                    $address_4 = $row[csf("address_4")];
                    $contact_no = $row[csf("contact_no")];
                }
            } else {
                $supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
                foreach ($supplier_sql as $row) {

                    $address_1 = $row[csf("address_1")];
                    $address_2 = $row[csf("address_2")];
                    $address_3 = $row[csf("address_3")];
                    $address_4 = $row[csf("address_4")];
                    $contact_no = $row[csf("contact_no")];
                }
            }
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>

            </tr>
        </table>
        <table width="850" cellspacing="0" align="left" style="margin-bottom:20px;">
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;" width="125" valign="top"><strong>Challan No</strong></td>
                <td style ="font-size: 11pt;" width="210" valign="top"><strong> : </strong><? echo $system_num; ?></td>
                <td style ="font-size: 11pt;" width="120" valign="top"><strong>Driver Name</strong></td>
                <td style ="font-size: 11pt;" width="130" valign="top"><strong> : </strong><? echo $driver_name; ?> </td>
                <td style ="font-size: 11pt;" width="110" valign="top"><strong>Date</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo change_date_format($data[2]); ?> </td>
            </tr>
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;" valign="top"><strong><? if ($forwarder > 0) {
                            echo 'C&F Name';
                        } else {
                            echo 'Forwarding Agent';
                        } ?></strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? if ($forwarder > 0) {
                        echo $supplier_library[$forwarder];
                    } else {
                        echo $supplier_library[$forwarder_2];
                    }  ?></td>
                <td style ="font-size: 11pt;" valign="top"><strong>Mobile Num</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo $mobile_no; ?> </td>
                <td style ="font-size: 11pt;" valign="top"><strong>Truck Out Time</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo $do_no; ?> </td>
            </tr>
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;" valign="top"><strong>Address</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo $address_1 . "<br>";
                    if ($contact_no != "") echo "Phone: " . $contact_no; ?> </td>
                <td style ="font-size: 11pt;"><strong>DL No</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $dl_no; ?> </td>
                <td style ="font-size: 11pt;"><strong>GP No</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $gp_no_arr[$system_num]; ?> </td>
            </tr>
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;" valign="top"><strong>Attention</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo $attention; ?> </td>
                <td style ="font-size: 11pt;"><strong>Final Destination</strong></td>
                <td style ="font-size: 11pt;" colspan="3"><strong> : </strong><? echo $destination_place; ?></td>
            </tr>
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;" valign="top"><strong>Trns. Comp</strong></td>
                <td style ="font-size: 11pt;" valign="top"><strong> : </strong><? echo $supplier_library[$supplier_name]; ?> </td>
                <td style ="font-size: 11pt;"><strong>Truck No</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $truck_no; ?> </td>
                <td style ="font-size: 11pt;"><strong>Lock No</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $lock_no; ?> </td>
            </tr>
            <tr style="font-size:15px;">
                <td style ="font-size: 11pt;"><strong>Delivery Company</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $company_library[$delivery_company]; ?> </td>
                <td style ="font-size: 11pt;"><strong>Delivery Location</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $location_library[$delivery_location]; ?> </td>
                <td style ="font-size: 11pt;"><strong>Delivery Floor</strong></td>
                <td style ="font-size: 11pt;"><strong> : </strong><? echo $floor_library[$delivery_floor]; ?> </td>

            </tr>
            <tr style="font-size: 15px">
                <td style ="font-size: 11pt;"><strong>Remarks</strong></td>
                <td style ="font-size: 11pt;" colspan="5"><strong> : </strong><? echo $remarks; ?></td>
            </tr>

        </table>
        <br>
        <?
        //listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

        if ($db_type == 2) {
            $sql = "SELECT c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
        } else if ($db_type == 0) {
            $sql = "SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks,group_concat(actual_po) as actual_po ,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
        }
        //echo $sql;
        $result = sql_select($sql);
        $table_width = 860;
        $col_span = 10;
        ?>

        <div style="width:<? echo $table_width; ?>px;">
            <table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="60">Buyer</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
                <th width="60">Country</th>
                <th width="60">Country Short Name</th>
                <th width="130">Item Name</th>
                <th width="150">Invoice No</th>
                <th width="50">Ship Mode</th>
                <th width="50">FOC/Claim</th>
                <th width="60">Delivery Qnty</th>
                <th width="50">NO Of Carton</th>
                <th>Remarks</th>
                </thead>
                <tbody>
                <?
                $i = 1;
                $tot_qnty = $tot_carton_qnty = 0;
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    $color_count = count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td valign="middle" style="font-size:14.5px;" align="center"><? echo $i;  ?></td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><?
                                $actual_po = $row[csf("actual_po")];
                                if ($actual_po) {
                                    $actual_po_no = "";
                                    $actual_po = explode(",", $actual_po);
                                    foreach ($actual_po as $val) {

                                        if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
                                        else $actual_po_no .= ',' . $actual_po_library[$val];
                                    }
                                    echo $actual_po_no;
                                } else  echo $row[csf("po_number")]; ?>&nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p>
                                <?
                                $garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
                                $garments_item_all = "";
                                foreach ($garments_item_arr as $item_id) {
                                    $garments_item_all .= $garments_item[$item_id] . ",";
                                }
                                $garments_item_all = substr($garments_item_all, 0, -1);
                                echo $garments_item_all;
                                ?>
                                &nbsp;</p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p>
                                <?
                                $invoice_id = "";
                                $invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
                                foreach ($invoice_id_arr as $inv_id) {
                                    if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
                                    else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
                                }
                                echo $invoice_id;
                                ?>&nbsp;</p>
                        </td>
                        <td valign="middle" align="center" style="font-size:14.5px;">
                            <p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
                        </td>
                        <td valign="middle" align="center" style="font-size:14.5px;">
                            <p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p>
                        </td>
                        <td valign="middle" align="right" style="font-size:14.5px;">
                            <p><? echo number_format($row[csf("total_qnty")], 0);
                                $tot_qnty += $row[csf("total_qnty")]; ?></p>
                        </td>
                        <td valign="middle" align="right" style="font-size:14.5px;">
                            <p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
                                $tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
                        </td>
                        <td valign="middle" style="font-size:14.5px;">
                            <p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
                        </td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tr bgcolor="#CCCCCC">
                    <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14.5px;"><strong>Grand Total :</strong></td>

                    <td align="right" style="font-size:14.5px;"><strong><? echo number_format($tot_qnty, 0, ".", ","); ?></strong></td>
                    <td align="right" style="font-size:14.5px;"><strong><? echo number_format($tot_carton_qnty, 0, ".", ","); ?></strong></td>
                    <td align="right" style="font-size:12px;">&nbsp;</td>
                </tr>

            </table>
            <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
        </div>
        <?
        // echo signature_table(63, $data[0], $table_width."px");
        echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
        ?>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
            fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
        </script>
    </div>
    <?
    exit();
}

if ($action == "ex_factory_print_new2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	$sys_arr = array();
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
	}
	$sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
?>
	<div style="width:900px; margin-top:10px; margin-left:55px;">

		<br>

		<?php
		$table_width = 1050;
		$col_span = 13;

		if ($forwarder > 0) {
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		} else {
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		}
		?>

		<div style="width:<? echo $table_width; ?>px;">
			<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<tr style="background-color:#fff;border-color:#fff;">
					<td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
					<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


						<div style="text-align:center;">
							<?

							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							$company_address = "";
							foreach ($nameArray as $result) {

								if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
								if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
								if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
								if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
								if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
								if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
								if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
								if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
									if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
								}
								if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
								if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
							}
							$company_address = chop($company_address, " , ");
							echo $company_address;
							?> <br>
							<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
							<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
						</div>


					</td>
					<td style="border:none; float:right;">
						<span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
						<span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
						<span style="float:left;" id="barcode_img_id"></span>

					</td>
				</tr>

			</table>

			<div style="width:950; ">
				<table border="1" cellpadding="1" cellspacing="1" style="width:950px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table">
					<tr>
						<td width="160" style="font-size:14px;"><? if ($forwarder > 0) {
																	echo 'C&F Name:';
																} else {
																	echo 'Forwarding Agent';
																} ?></td>
						<td width="160" style="font-size:14px;"><strong><? if ($forwarder > 0) {
																			echo $supplier_library[$forwarder];
																		} else {
																			echo $supplier_library[$forwarder_2];
																		}  ?></strong></td>
						<td width="160" style="font-size:14px;">Trns. Comp:</td>
						<td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
						<td width="160" style="font-size:14px;">Do No:</td>
						<td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
					</tr>
					<tr>
						<td style="font-size:14px;">Address:</td>
						<td style="font-size:14px;"><strong><? echo $address_1 . "<br>";
															if ($contact_no != "") echo "Phone : " . $contact_no; ?></strong></td>
						<td style="font-size:14px;">Driver Name :</td>
						<td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
						<td style="font-size:14px;">GP No:</td>
						<td style="font-size:14px;"><strong><? echo $gp_no_arr[$system_num]; ?></strong></td>
					</tr>
					<tr>
						<td style="font-size:14px;">Attention:</td>
						<td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
						<td style="font-size:14px;">Mobile No :</td>
						<td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
						<td style="font-size:14px;">Lock No:</td>
						<td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
					</tr>
					<tr>
						<td style="font-size:14px;">DL No:</td>
						<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
						<td style="font-size:14px;">Final Destination:</td>
						<td style="font-size:14px;"><strong><? echo $destination_place; ?></strong></td>
						<td style="font-size:14px;">Truck No:</td>
						<td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
					</tr>
					<? if ($show_hide_delv_info) { ?>
						<tr>
							<td style="font-size:14px;">Delivery Floor:</td>
							<td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
							<td style="font-size:14px;">Delivery Company:</td>
							<td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
							<td style="font-size:14px;">Delivery Location:</td>
							<td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
						</tr>
					<? } ?>
					<tr>
						<td style="font-size:14px;">Vat No.:</td>
						<td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
						<td style="font-size:14px;">Remarks:</td>
						<td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
					</tr>
				</table>
			</div>
			<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="border:none;">
					<tr>
						<th style="font-size:12px;" width="20">SL</th>
						<th style="font-size:12px;" width="60">Buyer</th>
						<th style="font-size:12px;" width="100">Style Ref.</th>
						<th style="font-size:12px;" width="100">Order No</th>
						<th style="font-size:12px;" width="100">Actual PO No. </th>
						<th style="font-size:12px;" width="100">IR/IB </th>
						<th style="font-size:12px;" width="60">Country</th>
						<th style="font-size:12px;" width="60">Country Short Name</th>
						<th style="font-size:12px;" width="130">Item Name</th>
						<th style="font-size:12px;" width="150">Invoice No</th>
						<th style="font-size:12px;" width="150">LC SC No</th>
						<th style="font-size:12px;" width="50">Ship Mode</th>
						<th style="font-size:12px;" width="50">FOC/Claim</th>
						<th style="font-size:12px;" width="50">Delivery Qnty</th>
						<th style="font-size:12px;" width="50">NO Of Carton</th>
						<th style="font-size:12px;">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
					$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
					if ($db_type == 2) {
						$sql = "SELECT  b.grouping, c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by  b.grouping,c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
					} else if ($db_type == 0) {
						$sql = "SELECT b.grouping, c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by  b.grouping, c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
					}
					//echo $sql;
					$result = sql_select($sql);
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><?
									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} else echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo  $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo  $row[csf("grouping")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $lc_num_arr[$row[csf("lc_sc_no")]] . $sc_num_arr[$row[csf("lc_sc_no")]]; ?></p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td style="font-size:12px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>

						<td align="right" style="font-size:12px;"><? echo number_format($tot_qnty, 0, "", ""); ?></td>
						<td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty, 0, "", ""); ?></td>
						<td align="right" style="font-size:12px;">&nbsp;</td>
					</tr>
					<tr style="border:none;">
						<td colspan="13" style=" border:none;border-color:#FFFFFF;">
							<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?
			// echo signature_table(63, $data[0], $table_width."px");
			echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
			?>
			<!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
						//  echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
		         	</td>
	         	</tr>
	        </tfoot> -->



		</div>

		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
		</script>
	</div>
<?
	exit();
}
if ($action == "ex_factory_print_new7") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	// print_r ($data);

	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");

	$fab_material = array(1 => "Organic", 2 => "BCI");

	// ==================================== additional information query =====================================================
	$additional_sql = "SELECT  additional_info_id from PRO_EX_FACTORY_MST where delivery_mst_id='$data[1]' order by id asc ";
	// echo $additional_sql;
	$additional_arr = array();
	$kk = 0;
	$add_data = "";
	foreach (sql_select($additional_sql) as $vals) {
		if ($vals[csf("additional_info_id")]) {
			$add_data .= $vals[csf("additional_info_id")] . "***";
		}
	}
	// echo "string $add_data";
	$add_data = explode("***", chop($add_data, "***"));
	foreach ($add_data as $additional_info_data) {
		$additional_data = explode("___", $additional_info_data);
		$truck_type = $truck_type_arr[$additional_data[0]];
		$trans_type = $transport_type_arr[$additional_data[1]];
		$sizes = $additional_data[2];
		$chassis_no = $additional_data[3];
		$courier_name = $additional_data[4];
		$cbm = $additional_data[5];
		$sample += $additional_data[6];
		$empty_carton += $additional_data[7];
		$gum_tape += $additional_data[8];
	}



	//================================================ for gate pass ==========================================================
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER,a.challan_no, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS,b.total_carton_qty FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = " . $data[0] . " AND a.basis = 12 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.issue_id LIKE '" . $data[1] . "%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach ($sql_get_pass_rslt as $row) {
		$exp = explode(',', $row['ISSUE_ID']);
		// echo "<pre>"; print_r($exp);
		foreach ($exp as $key => $val) {
			if ($val == $data[1]) {
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE'] != '' ? date('d-m-Y', strtotime($row['OUT_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');

				if ($row['WITHIN_GROUP'] == 1) {
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] = $location_arr[$row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];
				$gatePassDataArr[$val]['total_carton_qty'] += $row['TOTAL_CARTON_QTY'];

				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				// $gatePassDataArr[$val]['department'] = $row['DEPARTMENT_ID'];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$val]['challan_no'] = $row['challan_no'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if ($gate_pass_id != '') {
		$sql_gate_out = "SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='" . $gate_pass_id . "'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if (!empty($sql_gate_out_rslt)) {
			foreach ($sql_gate_out_rslt as $row) {
				$is_gate_out = 1;
				$gatePassDataArr[$data[1]]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$data[1]]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,update_date from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$update_date = $row[csf("update_date")];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
?>
	<div style="width:900px; margin-top:10px; margin-left:55px;">

		<br>

		<?php
		$table_width = 1050;
		$col_span = 8;

		if ($forwarder > 0) {
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		} else {
			$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		}
		?>

		<div style="width:<? echo $table_width; ?>px;">
			<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<tr style="background-color:#fff;border-color:#fff;">
					<td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
					<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


						<div style="text-align:center;">
							<?

							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							$company_address = "";
							foreach ($nameArray as $result) {

								if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
								if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
								if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
								if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
								if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
								if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
								if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
								if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
									if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
								}
								if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
								if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
							}
							$company_address = chop($company_address, " , ");
							echo $company_address;
							?> <br><br />
							<span> &nbsp; </span>
							<span style="font-size:17px;"><strong>Export Delivery Challan</strong></span><br />
							<span style="font-size:13px;"><strong>100% Export Oriented Readymade Garments Delivery For Export</strong></span><br>
							<span>&nbsp;</span>
						</div>


					</td>
					<td align="right" style="border:none;">
						<span>
							<?php echo $noOfCopy . ($is_gate_pass == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>" : '') . ($is_gate_out == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>" : ''); ?>
						</span>

					</td>
				</tr>

			</table>

			<div style="width:1050; ">
				<table border="1" cellpadding="1" cellspacing="1" style="width:1050px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table">
					<tr>
						<td colspan="6" align="right" style="border-left:hidden;border-right:hidden; border-top:hidden; font-size:14px;"> Last update Date & Time : <? echo $update_date; ?></td>
					</tr>
					<tr>
						<td width="160" style="font-size:16px;">
							<strong>
								Delivery To:
								<? //if( $forwarder>0) { echo 'Delivery To:';} else {echo 'Forwarding Agent';}
								?>
							</strong>
						</td>
						<td width="190" style="font-size:16px;"><strong>
								<? echo $supplier_library[$forwarder];
								//if( $forwarder>0){echo $supplier_library[$forwarder];} else { echo $supplier_library[$forwarder_2];}
								?>
							</strong></td>
						<td width="160" style="font-size:16px;"><strong>Delivery Company:</strong></td>
						<td width="190" style="font-size:16px;"><? echo $company_library[$delivery_company]; ?></td>
						<td width="160" style="font-size:16px;"><strong>Challan No:</strong></td>
						<td width="190" style="font-size:16px;"><strong><? echo $challan_no_full; ?></strong></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong>Address:</strong></td>
						<td style="font-size:16px;"><? echo $address_1 . "<br>";
													if ($contact_no != "") echo "Phone : " . $contact_no; ?></td>
						<td style="font-size:16px;"><strong>Delivery Location:</strong></td>
						<td style="font-size:16px;"><? echo $location_library[$delivery_location]; ?></td>
						<td style="font-size:16px;"><strong>Challan Date:</strong></td>
						<td style="font-size:16px;"><strong><? echo change_date_format($data[2]);  ?></strong></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong>Attention:</strong></td>
						<td style="font-size:16px;"><? echo $attention; ?></td>
						<td style="font-size:16px;"><strong>Driver Name :</strong></td>
						<td style="font-size:16px;"><? echo $driver_name; ?></td>
						<td style="font-size:16px;"><strong>Truck No:</strong></td>
						<td style="font-size:16px;"><? echo $truck_no; ?></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong>Final Destination:</strong></td>
						<td style="font-size:16px;"><? echo $destination_place; ?></td>
						<td style="font-size:16px;"> <strong> Mobile No :</strong></td>
						<td style="font-size:16px;"><? echo $mobile_no; ?></td>
						<td style="font-size:16px;"><strong>Lock No:</strong></td>
						<td style="font-size:16px;"><? echo $lock_no; ?></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong>Remarks</strong></td>
						<td colspan="3" style="font-size:16px;"><? echo $remarks; ?></td>
						<td style="font-size:16px;"><strong>DL No:</strong></td>
						<td style="font-size:16px;"><? echo $dl_no; ?></strong></td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle" height="50" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"><span id="barcode_img_id"></span> </td>
					</tr>
				</table>
			</div>
			<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="border:none;">
					<tr>
						<th style="font-size:16px;" width="20">SL</th>
						<th style="font-size:16px;" width="150">Buyer</th>
						<th style="font-size:16px;" width="200">Invoice No</th>
						<th style="font-size:16px;" width="150">Job No</th>
						<th style="font-size:16px;" width="150">Style Ref.</th>
						<th style="font-size:16px;" width="200">PO No.</th>
						<th style="font-size:16px;" width="200">Actual PO No.</th>
						<th style="font-size:16px;" width="200">Sustainability/ F. Material</th>
						<th style="font-size:16px;" width="50">Carton QTY</th>
						<th style="font-size:16px;" width="50">PCS QTY</th>
						<th style="font-size:16px;" width="80">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
					$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");

					$sql = "SELECT c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no,a.job_no,a.sustainability_standard,a.fab_material, b.id as po_break_down_id, b.po_number, c.country_id ,listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , c.shiping_mode,c.lc_sc_no,c.id as mst_id
					from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
					where a.id=b.job_id and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
					group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no,a.job_no,a.sustainability_standard,a.fab_material, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no,c.id
					order by a.style_ref_no";

					//  echo $sql;
					$result = sql_select($sql);
					$mst_id_arr = array();
					foreach ($result as $v)
					{
						$mst_id_arr[$v['MST_ID']] = $v['MST_ID'];
					}
					$mst_ids = implode(",",$mst_id_arr);
					$acc_po = "SELECT mst_id,actual_po_id from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where status_active=1 and mst_id in($mst_ids)";
					$res = sql_select($acc_po);
					foreach ($res as $v)
					{
						$actual_po_id_arr[$v['MST_ID']] .= $v['ACTUAL_PO_ID'].",";
					}
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);

						$sustainability = $row[csf("sustainability_standard")];
						$material = $row[csf("fab_material")];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:16px;"><? echo $i;  ?></td>
							<td style="font-size:16px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:16px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td style="font-size:16px;">
								<p><? echo $row[csf("job_no")]; ?></p>
							</td>
							<td style="font-size:16px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:16px;">
								<p><?

									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} else echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p>
									<?
									 $actual_po ="";
									$actual_po_arr = array_unique(array_filter(explode(",", $actual_po_id_arr[$row[csf("mst_id")]])));

									foreach ($actual_po_arr as $val) {
										if ($actual_po == "") $actual_po = $actual_po_library[$val];
										else $actual_po = $actual_po . "," . $actual_po_library[$val];
									}

									echo $actual_po;	?></p>
							</td>

							<td style="font-size:16px;" align="center">
								<p>
									<?
									//($sustainability*1 != ''?'::'.$sustainability_standard[$sustainability]:'').($material*1 != ''?'::'.$fab_material[$material]:'')
									if ($row[csf("sustainability_standard")] && $row[csf("fab_material")]) {
										echo  $sustainability_standard[$row[csf("sustainability_standard")]] . "::" . $fab_material[$row[csf("fab_material")]];
									} elseif ($row[csf("sustainability_standard")]) {
										echo $sustainability_standard[$row[csf("sustainability_standard")]];
									} else {
										echo $fab_material[$row[csf("fab_material")]];
									}
									?>
								</p>
							</td>
							<td align="right" style="font-size:16px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:16px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td style="font-size:16px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:16px;"><strong>Grand Total :</strong></td>

						<td align="right" style="font-size:16px;"><strong><? echo number_format($tot_carton_qnty, 0, "", ""); ?></strong></td>
						<td align="right" style="font-size:16px;"><strong><? echo number_format($tot_qnty, 0, "", ""); ?></strong></td>
						<td align="right" style="font-size:16px;">&nbsp;</td>
					</tr>
					<!-- <tr style="border:none;">
	        	<td colspan="13"  style=" border:none;border-color:#FFFFFF;">
	            	 <h3 align="center">In Words : &nbsp;<? // echo number_to_words($tot_qnty,"Pcs");
															?></h3>
	            </td>
	        </tr> -->
				</tbody>
			</table>
			<p>&nbsp;</p>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
				<thead bgcolor="#dddddd">
					<tr>
						<th>Sample (Pcs)</th>
						<th>Empty Carton [Pcs]</th>
						<th>Gumtape (Pcs)</th>
						<th width="70">CBM</th>
					</tr>
				</thead>
				<tr>
					<td align="center"><? echo $sample; ?></td>
					<td align="center"><? echo $empty_carton; ?></td>
					<td align="center"><? echo $gum_tape; ?></td>
					<td align="center"><? echo $cbm; ?></td>
				</tr>
			</table>

			<!-- ============= Gate Pass Info Start ========= -->
			<table style="margin-right:-40px;" cellspacing="0" width="1050" border="1" rules="all" class="rpt_table">
				<tr>
					<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
				</tr>
				<tr>
					<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
					<td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
				</tr>
				<tr>
					<td colspan="2" title="<? echo $data[1]; ?>"><strong>From Company:</strong></td>
					<td colspan="2" width="120"><?php echo $gatePassDataArr[$data[1]]['from_company']; ?></td>

					<td colspan="2"><strong>To Company:</strong></td>
					<td colspan="3" width="120"><?php echo $gatePassDataArr[$data[1]]['to_company']; ?></td>

					<td colspan="3"><strong>Carried By:</strong></td>
					<td colspan="3" width="120"><?php echo $gatePassDataArr[$data[1]]['carried_by']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>From Location:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['from_location']; ?></td>
					<td colspan="2"><strong>To Location:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['to_location']; ?></td>
					<td colspan="3"><strong>Driver Name:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['driver_name']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Gate Pass ID:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['gate_pass_id']; ?></td>
					<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
					<td align="center"><strong>Cartoon Qty</strong></td>
					<td align="center" colspan="2"><strong>PCS Qty</td>
					<!-- <td align="center"><strong>PCS</td> -->
					<td colspan="3"><strong>Vehicle Number:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['vhicle_number']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Gate Pass Date:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['gate_pass_date']; ?></td>
					<td align="center"><?php echo $gatePassDataArr[$data[1]]['total_carton_qty']; ?></td>
					<td align="center" colspan="2"><?php echo $gatePassDataArr[$data[1]]['delivery_kg']; ?></td>
					<!-- <td align="center"><?php
											// if ($gatePassDataArr[$data[1]]['gate_pass_id'] !="")
											// {
											// 	echo number_format($grnd_total_issue_qty_pcs_qnty, 2, '.', '');
											// }
											?></td> -->
					<td colspan="3"><strong>Driver License No.:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['driver_license_no']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Out Date:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['out_date']; ?></td>
					<td colspan="2"><strong>Dept. Name:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['department']; ?></td>
					<td colspan="3"><strong>Driver Mobile No.:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['mobile_no']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Out Time:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['out_time']; ?></td>
					<td colspan="2"><strong>Attention:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['attention']; ?></td>
					<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
					<td colspan="3"><?php echo $gatePassDataArr[$data[1]]['security_lock_no']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Returnable:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['returnable']; ?></td>
					<td colspan="2"><strong>Purpose:</strong></td>
					<td colspan="9"><?php echo $gatePassDataArr[$data[1]]['issue_purpose']; ?></td>
				</tr>
				<tr>
					<td colspan="2"><strong>Est. Return Date:</strong></td>
					<td colspan="2"><?php echo $gatePassDataArr[$data[1]]['est_return_date']; ?></td>
					<td colspan="2"><strong>Remarks:</strong></td>
					<td colspan="9"><?php echo $gatePassDataArr[$data[1]]['remarks']; ?></td>
				</tr>
			</table>
			<!-- ============= Gate Pass Info End =========== -->

			<?
			// echo signature_table(63, $data[0], $table_width."px");
			echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
			?>
			<!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
						//  echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
		         	</td>
	         	</tr>
	        </tfoot> -->



		</div>

		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			function fnc_generate_Barcode_new(valuess, img_id) {
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#" + img_id).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#" + img_id).show().barcode(value, btype, settings);
			}
			fnc_generate_Barcode_new('<? echo $system_num; ?>', 'barcode_img_id');

			function generateBarcodeGatePass(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#gate_pass_barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}

			if ('<? echo $gatePassDataArr[$data[1]]['gate_pass_id']; ?>' != '') {
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$data[1]]['gate_pass_id']); ?>');
			}
		</script>
	</div>
<?
	exit();
}

if ($action == "ex_factory_print_new3") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	// print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	$lib_color = return_library_array("select id, color_name from lib_color", "id", "color_name");

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,escot_name,escot_mobile,depo_details from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	$sys_arr = array();
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$escot_name=$row[csf("escot_name")];
		$escot_mobile=$row[csf("escot_mobile")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
		$depo_details = $row[csf("depo_details")];
	}
	$sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:1100px; margin-top:10px;">

		<br>

		<?php
		$table_width = 1080;
		$col_span = 13;

		if ($forwarder > 0) {
			$supplier_sql = sql_select("SELECT id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		} else {
			$supplier_sql = sql_select("SELECT id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
			foreach ($supplier_sql as $row) {

				$address_1 = $row[csf("address_1")];
				$address_2 = $row[csf("address_2")];
				$address_3 = $row[csf("address_3")];
				$address_4 = $row[csf("address_4")];
				$contact_no = $row[csf("contact_no")];
			}
		}
		?>

		<!--<div style="width:<? //echo $table_width;
								?>px;">-->
		<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
			<tr style="background-color:#fff;border-color:#fff;">
				<td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
				<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


					<div style="text-align:center;">
						<?

						$nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						$company_address = "";
						foreach ($nameArray as $result) {

							if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
							if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
							if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
							if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
							if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
							if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
							if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
							if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
								if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
							}
							if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
							if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
						}
						$company_address = chop($company_address, " , ");
						echo $company_address;
						?> <br>
						<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
						<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
					</div>
				</td>
				<td style="border:none; float:right;">
					<span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
					<span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
					<span style="float:left;" id="barcode_img_id"></span>

				</td>
			</tr>

		</table>

		<div style="width:1080px; margin:0 auto;">
			<table border="1" cellpadding="1" cellspacing="1" style="width:1080px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table">
				<tr>
					<td width="160" style="font-size:14px;">
						<?
						if ($forwarder > 0) {
							echo 'C&F Name:';
						}else{
							echo 'Forwarding Agent';
						}
						?>
					</td>
					<td width="160" style="font-size:14px;"><strong>
						<? if ($forwarder > 0) {
							echo $supplier_library[$forwarder];
							} else {
							echo $supplier_library[$forwarder_2];
							}
						?>
					</strong></td>
					<td width="160" style="font-size:14px;">Trns. Comp:</td>
					<td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
					<td width="160" style="font-size:14px;">Do No/Empty Carton:</td>
					<td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:14px;">Address:</td>
					<td style="font-size:14px;"><strong><? echo $address_1 . "<br>";
														if ($contact_no != "") echo "Phone : " . $contact_no; ?></strong></td>
					<td style="font-size:14px;">Driver Name :</td>
					<td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
					<td style="font-size:14px;">GP No:</td>
					<td style="font-size:14px;"><strong><? echo $gp_no_arr[$system_num]; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:14px;">Attention/Sample:</td>
					<td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
					<td style="font-size:14px;">Mobile No :</td>
					<td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
					<td style="font-size:14px;">Lock No:</td>
					<td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:14px;">DL No:</td>
					<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
					<td style="font-size:14px;">Final Destination:</td>
					<td style="font-size:14px;"><strong><? echo $destination_place; ?></strong></td>
					<td style="font-size:14px;">Truck No:</td>
					<td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
				</tr>
				<? if ($show_hide_delv_info) { ?>
					<tr>
						<td style="font-size:14px;">Delivery Floor:</td>
						<td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
						<td style="font-size:14px;">Delivery Company:</td>
						<td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
						<td style="font-size:14px;">Delivery Location:</td>
						<td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
					</tr>
				<? } ?>
				<tr>
					<td style="font-size:14px;">Vat No.:</td>
					<td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
					<td style="font-size:14px;">Remarks:</td>
					<td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:14px;">Escort Name:</td>
					<td style="font-size:14px;"><strong><? echo $escot_name; ?></strong></td>
					<td style="font-size:14px;">Escort Mob:</td>
					<td style="font-size:14px;"><strong><? echo $escot_mobile; ?></strong></td>
					<td style="font-size:14px;">Depo Details:</td>
					<td style="font-size:14px;"><strong><?echo $depo_details?></strong></td>
				</tr>
			</table>
		</div>
		<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="border:none;">
				<tr>
					<th style="font-size:12px;" width="20">SL</th>
					<th style="font-size:12px;" width="60">Buyer</th>
					<th style="font-size:12px;" width="100">Style Ref.</th>
					<th style="font-size:12px;" width="100">Order No</th>
					<th style="font-size:12px;" width="100">Actual PO</th>
					<th style="font-size:12px;" width="60">Country</th>
					<th style="font-size:12px;" width="60">Country Short Name</th>
					<th style="font-size:12px;" width="130">Item Name</th>
					<th style="font-size:12px;" width="130">Color Name</th>
					<th style="font-size:12px;" width="150">Invoice No</th>
					<th style="font-size:12px;" width="150">LC SC No</th>
					<th style="font-size:12px;" width="50">Ship Mode</th>
					<th style="font-size:12px;" width="50">FOC/Claim</th>
					<th style="font-size:12px;" width="50">Delivery Qnty</th>
					<th style="font-size:12px;" width="50">NO Of Carton</th>
					<th style="font-size:12px;">Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?
				$lc_num_arr = return_library_array("SELECT id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
				$sc_num_arr = return_library_array("SELECT id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");

				$sql = "SELECT d.foc_or_claim, d.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, d.country_id, d.invoice_no as invoice_no, d.total_carton_qnty as total_carton_qnty, sum(e.production_qnty) as total_qnty, d.remarks , actual_po as actual_po,d.shiping_mode,d.lc_sc_no,c.color_number_id,d.id as mst_id
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_ex_factory_mst d, pro_ex_factory_dtls e
				where a.id=b.job_id and b.id=d.po_break_down_id and d.delivery_mst_id=$data[1] and a.status_active=1 and b.status_active=1 and e.status_active=1 and d.status_active=1 and d.is_deleted=0 and b.id=c.po_break_down_id and a.id=c.job_id and c.id=e.color_size_break_down_id and d.id=e.mst_id
				group by d.foc_or_claim, d.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, d.country_id, d.invoice_no, d.total_carton_qnty, d.remarks , actual_po,d.shiping_mode,d.lc_sc_no,c.color_number_id,d.id
				order by a.style_ref_no";

				//  echo $sql;
				// $result = sql_select($sql);
				// $rowspan_arr = array();
				// foreach ($result as $v)
				// {
				// 	$po_break_down_ids .= $v['PO_BREAK_DOWN_ID'].",";
				// 	$rowspan_arr[$v['ID']]++;
				// }
				// $unique_po_break_down_ids = implode(",",array_unique(explode(",", chop($po_break_down_ids, ","))));

				// $actual_po_sql = "SELECT po_break_down_id, acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in ($unique_po_break_down_ids)";
				// $actual_po_result = sql_select($actual_po_sql);
				// $actual_po_arr = array();
				// foreach ($actual_po_result as $row) {
				// 	$actual_po_arr[$row['PO_BREAK_DOWN_ID']] = $row['ACC_PO_NO'];
				// }
				// echo "<pre>"; print_r($actual_po_arr); die;
					$result = sql_select($sql);
					$mst_id_arr = array();
					foreach ($result as $v)
					{
						$mst_id_arr[$v['MST_ID']] = $v['MST_ID'];
					}
					$mst_ids = implode(",",$mst_id_arr);
					$acc_po = "SELECT mst_id,actual_po_id from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where status_active=1 and mst_id in($mst_ids)";
					$res = sql_select($acc_po);
					foreach ($res as $v)
					{
						$actual_po_id_arr[$v['MST_ID']] .= $v['ACTUAL_PO_ID'].",";
					}

				$i = 1;
				$r = 0;
				$id_chk_arr = array();
				$tot_qnty = $tot_carton_qnty= 0;

				foreach ($result as $row) {
					if($id_chk_arr[$row['ID']]=="")
					{
						$id_chk_arr[$row['ID']] = $row['ID'];
						$r=0;
					}
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><?
									$actual_po = $row[csf("actual_po")];
									if ($actual_po) {
										$actual_po_no = "";
										$actual_po = explode(",", $actual_po);
										foreach ($actual_po as $val) {

											if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
											else $actual_po_no .= ',' . $actual_po_library[$val];
										}
										echo $actual_po_no;
									} else echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?

										$actual_po ="";
											$actual_po_arr = array_unique(array_filter(explode(",", $actual_po_id_arr[$row[csf("mst_id")]])));

											foreach ($actual_po_arr as $val) {
												if ($actual_po == "") $actual_po = $actual_po_library[$val];
												else $actual_po = $actual_po . "," . $actual_po_library[$val];
											}

											echo $actual_po;	?>
								</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:12px;">
								<p><? echo $lib_color[$row[csf("color_number_id")]]; ?></p>
							</td>
							<td style="font-size:12px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $lc_num_arr[$row[csf("lc_sc_no")]] . $sc_num_arr[$row[csf("lc_sc_no")]]; ?></p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="left" style="font-size:12px;">
								<p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p>
							</td>
							<td align="right" style="font-size:12px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>

							</td>
							<? if($r==0):?>
							<td valing="middle" align="center" style="font-size:12px;" rowspan="<?=$rowspan_arr[$row['ID']];?>">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<? endif;?>



							<td style="font-size:12px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
					$i++;
					$r++;
				}
				?>
				<tr bgcolor="#CCCCCC">
					<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>

					<td align="right" style="font-size:12px;"><? echo number_format($tot_qnty, 0, "", ""); ?></td>
					<td align="center" style="font-size:12px;"><? echo number_format($tot_carton_qnty, 0, "", ""); ?></td>
					<td align="right" style="font-size:12px;">&nbsp;</td>
				</tr>
				<tr style="border:none;">
					<td colspan="13" style=" border:none;border-color:#FFFFFF;">
						<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
					</td>
				</tr>
			</tbody>
		</table>
		<?
		// echo signature_table(63, $data[0], $table_width."px");
		echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
		?>
		<!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
						// echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
		         	</td>
	         	</tr>
	        </tfoot> -->



		<!--</div>-->

		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
		</script>
	</div>
<?
	exit();
}

if ($action == "production_process_control") {
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
	$control_and_preceding = sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$data'");
	if (count($control_and_preceding) > 0) {
		echo "$('#hidden_variable_cntl').val('" . $control_and_preceding[0][csf("is_control")] . "');\n";
		echo "$('#hidden_preceding_process').val('" . $control_and_preceding[0][csf("preceding_page_id")] . "');\n";
	}

	exit();
}

if ($action == "deleted_col_size") {

	$ex_data = explode("*", $data);
	$company = $ex_data[0];
	$sys_id = $ex_data[1];
	$mst_id = $ex_data[2];
	$dates = $ex_data[3];
	$po_id = $ex_data[4];
	if ($po_id) {
		$sql_cond .= "and  a.po_break_down_id in($po_id)";
	}
	//$sql_cond.=" and  a.delivery_mst_id in($sys_id)";

	/*if($dates!="")
	{
		if($db_type==0){$sql_cond .= " and a.ex_factory_date = '".change_date_format($dates,'yyyy-mm-dd')."' ";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.ex_factory_date = '".date("j-M-Y",strtotime($dates))."'";}
	}*/

	$sql = "SELECT a.challan_no, c.po_number,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty from pro_ex_factory_mst a ,pro_ex_factory_dtls b,wo_po_break_down c,wo_po_color_size_breakdown d where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and c.id=d.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and( c.status_active<>1 or d.status_active<>1) $sql_cond group by  a.challan_no,c.po_number,d.color_number_id,d.size_number_id";
	$result = sql_select($sql);
	$size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

?>
	<table cellspacing="0" width="600" class="rpt_table" cellpadding="0" border="1" rules="all">
		<thead>
			<th width="30">SL</th>
			<th width="100">Order No.</th>
			<th width="100">Challan No.</th>
			<th width="100">Color</th>
			<th width="50">Size</th>
			<th width="80">Ex-fact Qty</th>

		</thead>
		<tbody>
			<?
			$i = 1;
			$total_qty = 0;
			foreach ($result as $row) {
				if ($i % 2 == 0)  $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i++; ?></td>
					<td width="100" align="center">
						<p><? echo $row[csf("po_number")]; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $row[csf("challan_no")]; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $color_arr[$row[csf("color_number_id")]]; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $size_arr[$row[csf("size_number_id")]]; ?>&nbsp;</p>
					</td>
					<td width="80" align="right">
						<p><? echo $qty = number_format($row[csf("qnty")], 0, "", ""); ?></p>
					</td>

				</tr>

			<?
				$total_qty += $qty;
			}
			?>
			<tr bgcolor="#E4E4E4">
				<td colspan="5" align="right">Total</td>
				<td align="right"><? echo $total_qty; ?></td>

			</tr>

		</tbody>
	</table>
<?
	exit();
}

if ($action == "actual_po_action_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	//echo "string $act_po_id";
	$act_ar = explode(",", $act_po_id);
	$act_ar2 = explode("_", $act_po_id);
	$act_po_id_ar=array();
	foreach($act_ar as $val)
	{
		$act_po_id_ar[$val]=$val;
	}
	foreach($act_ar2 as $val)
	{
		$act_po_id_ar[$val]=$val;
	}

	?>
	<script>
		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			for (var i = 1; i <= tbl_row_count; i++) {
				if ($("#search" + i).css("display") != 'none') {
					js_set_value(i);
				}
			}
		}
		var selected_id = new Array();
		var selected_dtls_id = new Array();
		var selected_name = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_dtls' + str).val(), selected_dtls_id) == -1) {
				selected_id.push($('#txt_individual' + str).val());
				selected_dtls_id.push($('#txt_individual_dtls' + str).val());
				selected_name.push($('#txt_individual_name' + str).val());

			} else {
				for (var i = 0; i < selected_dtls_id.length; i++) {
					if (selected_dtls_id[i] == $('#txt_individual_dtls' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_dtls_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var dtls_id = '';
			var name = '';
			for (var i = 0; i < selected_dtls_id.length; i++) {
				id += selected_id[i] +'_'+ selected_dtls_id[i] + ',';
				dtls_id += selected_dtls_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			dtls_id = dtls_id.substr(0, dtls_id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hidden_actual_po_id').val(id);
			$('#hidden_dtls_id').val(dtls_id);
			$('#hidden_actual_po_no').val(name);
		}

		function fnc_close() {
			document.getElementById('hidden_actual_po_id_return').value = document.getElementById('hidden_actual_po_id').value;
			document.getElementById('hidden_dtls_id_return').value = document.getElementById('hidden_dtls_id').value;
			document.getElementById('hidden_actual_po_no_return').value = document.getElementById('hidden_actual_po_no').value;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>


		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="100">Actual Style</th>
				<th width="100">Po Number</th>
				<th width="100">Country</th>
				<th width="100">Gmts Item</th>
				<th width="100">Gmts Color</th>
				<th width="80">Gmts Size</th>
				<th width="80">Po Qnty.</th>
				<th width="70">Ship Date</th>

			</thead>
		</table>
		<div style="width:790px; max-height:400px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search">
				<?
				$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");
				$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
				$size_library = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

				$nameArray=sql_select( "select cm_cost_method,id from  variable_order_tracking where company_name='$company_name' and variable_list=93 order by id" );
				$acc_po_entry_controll = $nameArray[0][csf('cm_cost_method')];

				$i = 1;
				if($acc_po_entry_controll==1)
				{
					$sql = "SELECT a.id,a.acc_po_no,a.acc_ship_date,b.id as dtls_id,b.gmts_item,b.gmts_color_id,b.country_id,b.gmts_size_id,b.PO_QTY as acc_po_qty,acc_style_ref
					FROM wo_po_acc_po_info a left join WO_PO_ACC_PO_INFO_DTLS b on  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0  WHERE a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($po_id)
					order by a.acc_ship_date,a.id,b.country_id,b.gmts_item,b.gmts_color_id,b.gmts_size_id";
				}
				else
				{
					$sql = "SELECT a.id,a.acc_po_no,a.acc_ship_date,a.id as dtls_id,a.acc_PO_QTY as acc_po_qty,a.country_id,a.acc_po_qty,a.acc_ship_date,acc_style_ref
					FROM wo_po_acc_po_info a WHERE a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($po_id)
					order by a.acc_ship_date,a.id,a.country_id";
				}
				// echo $sql;die;
				$result = sql_select($sql);
				$js_set_string = "";
				foreach ($result as $row) {
					if (in_array($row[csf('id')]."_".$row[csf('dtls_id')], $act_po_id_ar)) {
						if ($js_set_string == "") $js_set_string = $i;
						else $js_set_string .= ',' . $i;
					}
					else if (in_array($row[csf('id')], $act_po_id_ar)) {
						if ($js_set_string == "") $js_set_string = $i;
						else $js_set_string .= ',' . $i;
					}

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40"><? echo $i; ?>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
							<input type="hidden" name="txt_individual_dtls" id="txt_individual_dtls<?php echo $i; ?>" value="<?php echo $row[csf('dtls_id')]; ?>" />
							<input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('acc_po_no')]; ?>" />
						</td>

						<td width="100"> <p><? echo $row['ACC_STYLE_REF']; ?></p></td>
						<td width="100">
							<p><? echo $row[csf('acc_po_no')]; ?></p>
						</td>
						<td width="100">
							<p><? echo $country_library[$row[csf('country_id')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $garments_item[$row[csf('gmts_item')]]; ?></p>
						</td>
						<td width="100" title="<?= $color_library[$row[csf('gmts_color_id')]]; ?>">
							<p><? echo substr($color_library[$row[csf('gmts_color_id')]], 0, 15); ?></p>
						</td>
						<td width="80">
							<p><? echo $size_library[$row[csf('gmts_size_id')]]; ?></p>
						</td>
						<td width="80" align="right"><? echo $row[csf('acc_po_qty')]; ?></td>
						<td width="70">
							<p><? echo change_date_format($row[csf('acc_ship_date')]); ?></p>
						</td>

					</tr>
				<?
					$i++;
				}
				?>

			</table>
			<input type="hidden" name="hidden_actual_po_id" id="hidden_actual_po_id">
			<input type="hidden" name="hidden_dtls_id" id="hidden_dtls_id">
			<input type="hidden" name="hidden_actual_po_id_return" id="hidden_actual_po_id_return">
			<input type="hidden" name="hidden_actual_po_no" id="hidden_actual_po_no">
			<input type="hidden" name="hidden_actual_po_no_return" id="hidden_actual_po_no_return">
			<input type="hidden" name="hidden_dtls_id_return" id="hidden_dtls_id_return">
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80">Total</th>
					<th width="80" align="right" id="total_qty"></th>
					<th width="70"></th>
				</tr>
			</tfoot>
		</table>
		<table width="670">
			<tr>
				<td align="center">
					<span style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
					<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			var js_set_string = '<? echo $js_set_string; ?>';
			js_set_arr = js_set_string.split(",");

			var i;
			for (i = 0; i < js_set_arr.length; i++) {
				js_set_value(js_set_arr[i]);
			}
		</script>
		<script type="text/javascript">
			var tableFilters = {
				col_operation: {
					id: ["total_qty"],
					col: [6],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			setFilterGrid("tbl_list_search", -1, tableFilters);
		</script>
	<?
	exit();
}

if ($action == "actual_po_action")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	//echo "string $act_po_id";
	$act_ar = explode("==", $act_po_id);
	$act_po_data_ar=array();
	$tot_cur_qty=0;
	$dtls_ids="";
	foreach($act_ar as $val)
	{
		$dt_arr =  explode("**",$val);
		$act_po_data_ar[$dt_arr[3]]=$dt_arr[8];
		$tot_cur_qty += $dt_arr[8];
		$dtls_ids .= ($dtls_ids=="") ? $dt_arr[3] : ",".$dt_arr[3];
	}

	?>
	<script>
		function checkDuplicate(arra1)
		{
			return arra1.length !== 0 && new Set(arra1).size !== 1;
		}

		function fnCheckQty(qty, i)
		{
			let cuml_qty = $('#hidden_cuml_qty_'+i).val()*1;
			let place_qty = $('#acc_po_qty_'+i).attr('placeholder')*1;
			if(qty*1 > (cuml_qty+place_qty))
			{
				alert('Ex-factory qty can not over than finishing qty. Please check carefully.');
				$('#acc_po_qty_'+i).val('');
				return;
			}
		}

		var data_ex_arr = new Array();
		function fnc_close()
		{
			var i=1;
			var data_string="";
			var flag = 1;
			$("input[name=acc_po_qty]").each(function (index, element)
			{
				if ($(this).val() != '')
                {
                    var hidden_data = $("#hidden_row_data_"+i).val()+'**'+$(this).val();
					var data_ex = hidden_data.split('**');
					data_ex_arr.push(data_ex[2]+'__'+data_ex[4]);

                    if (data_string == "")
                    {
                        data_string = hidden_data;
                    }
                    else
                    {
                        data_string += "==" + hidden_data;
                    }

					if (checkDuplicate(data_ex_arr))
					{
						$(this).val('');
						flag = 0;
					}
                }
                i++;
			});
			if(flag==0)
			{
				data_ex_arr = new Array();
				alert('Error: you can not mix PO and country !');
				return;
			}
			document.getElementById('data_string').value=data_string;
			parent.emailwindow.hide();
		}
		function fn_set_grnd_total()
		{
			var total=0;
			$("input[name=acc_po_qty]").each(function (index, element)
			{
				if ($(this).val() != '')
                {
                    total += parseInt($(this).val());
                }
			});
			$("#total_cur_qty").text(total);
		}
	</script>
	</head>

	<body>


		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="100">Po Number</th>
				<th width="100">Country</th>
				<th width="70">Ship Date</th>
				<th width="100">Gmts Item</th>
				<th width="100">Gmts Color</th>
				<th width="80">Gmts Size</th>
				<th width="80">Po Qnty.</th>
				<th width="60">Cum. Delv. Qty.</th>
				<th width="60">Cur. Delv. Qty.</th>
				<th width="60">Bal. Qty.</th>

			</thead>
		</table>
		<div style="width:870px; max-height:360px; overflow-y:auto" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
				<?
				$country_library = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
				$color_library = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
				$size_library = return_library_array("SELECT id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
				if($dtls_ids!="")
				{
					$acc_po_id_cond = " and a.actual_po_dtls_id in($dtls_ids)";
				}
				$prev_ex_qty = return_library_array("SELECT a.actual_po_dtls_id as id, sum(a.ex_fact_qty) as qty from pro_ex_factory_actual_po_details a, pro_ex_factory_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=$po_id and b.item_number_id=$item_id $acc_po_id_cond group by a.actual_po_dtls_id", "id", "qty");
				// echo "SELECT a.actual_po_dtls_id as id, sum(a.ex_fact_qty) as qty from pro_ex_factory_actual_po_details a, pro_ex_factory_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=$po_id $acc_po_id_cond group by a.actual_po_dtls_id";

				$i = 1;
				$sql = "SELECT a.id,a.acc_po_no,a.acc_ship_date,b.id as dtls_id,b.gmts_item,b.gmts_color_id,b.country_id,b.gmts_size_id,b.PO_QTY as acc_po_qty
		   		FROM wo_po_acc_po_info a, WO_PO_ACC_PO_INFO_DTLS b WHERE a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id) and b.gmts_item=$item_id
		   		order by a.acc_ship_date,a.id,b.country_id,b.gmts_item,b.gmts_color_id,b.gmts_size_id";
				// echo $sql;
				$result = sql_select($sql);
				$acc_po_dtls_id_arr = array();
				foreach ($result as $key => $v)
				{
					$acc_po_dtls_id_arr[$v[csf('dtls_id')]] = $v[csf('dtls_id')];
				}
				$acc_po_dtls_id_cond = where_con_using_array($acc_po_dtls_id_arr,0,"ACTUAL_PO_DTLS_ID");
				$prev_fin_qty_arr = return_library_array("select ACTUAL_PO_DTLS_ID, sum(prod_qty) as qty from PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS where status_active=1 and is_deleted=0 $acc_po_dtls_id_cond group by ACTUAL_PO_DTLS_ID", "ACTUAL_PO_DTLS_ID", "qty");

				$js_set_string = "";
				$row_data = "";
				foreach ($result as $row)
				{
					if (in_array($row[csf('id')]."_".$row[csf('dtls_id')], $act_po_id_ar))
					{
						if ($js_set_string == "") $js_set_string = $i;
						else $js_set_string .= ',' . $i;
					}
					else if (in_array($row[csf('id')], $act_po_id_ar))
					{
						if ($js_set_string == "") $js_set_string = $i;
						else $js_set_string .= ',' . $i;
					}
					$row_data = $i."**".$row[csf('acc_po_no')]."**".$row[csf('id')]."**".$row[csf('dtls_id')]."**".$row[csf('country_id')]."**".$row[csf('gmts_item')]."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')];
					$balance = $row[csf('acc_po_qty')]-$prev_ex_qty[$row[csf('dtls_id')]];
					$yet_to_delv_qty = $prev_fin_qty_arr[$row[csf('dtls_id')]] - $prev_ex_qty[$row[csf('dtls_id')]];
					// echo $row[csf('dtls_id')]."=".$prev_fin_qty_arr[$row[csf('dtls_id')]] ."-". $prev_ex_qty[$row[csf('dtls_id')]]."<br>";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
						<td width="40"><? echo $i; ?>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
							<input type="hidden" name="txt_individual_dtls" id="txt_individual_dtls<?php echo $i; ?>" value="<?php echo $row[csf('dtls_id')]; ?>" />
							<input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('acc_po_no')]; ?>" />
						</td>

						<td width="100">
							<p><? echo $row[csf('acc_po_no')]; ?></p>
						</td>
						<td width="100">
							<p><? echo $country_library[$row[csf('country_id')]]; ?></p>
						</td>
						<td width="70">
							<p><? echo change_date_format($row[csf('acc_ship_date')]); ?></p>
						</td>
						<td width="100">
							<p><? echo $garments_item[$row[csf('gmts_item')]]; ?></p>
						</td>
						<td width="100" title="<?= $color_library[$row[csf('gmts_color_id')]]; ?>">
							<p><? echo substr($color_library[$row[csf('gmts_color_id')]], 0, 15); ?></p>
						</td>
						<td width="80">
							<p><? echo $size_library[$row[csf('gmts_size_id')]]; ?></p>
						</td>
						<td width="80" align="right"><? echo $row[csf('acc_po_qty')]; ?></td>
						<td width="60" align="right"><?=$prev_ex_qty[$row[csf('dtls_id')]]; ?></td>
						<td width="60" align="right"><input type="text" class="text_boxes_numeric" id="acc_po_qty" name="acc_po_qty" value="<?=$act_po_data_ar[$row[csf('dtls_id')]];?>" style="width:50px" onKeyUp="fn_set_grnd_total();" placeholder="<?=$yet_to_delv_qty;?>" onblur="fnCheckQty(this.value,'<?=$i;?>');"></td>
						<td width="60" align="right">
							<?=$balance; ?>
							<input type="hidden" name="hidden_row_data_<?=$i;?>" id="hidden_row_data_<?=$i;?>" value="<?=$row_data;?>">
							<input type="hidden" name="hidden_cuml_qty_<?=$i;?>" id="hidden_cuml_qty_<?=$i;?>" value="<?=$prev_ex_qty[$row[csf('dtls_id')]];?>">
						</td>


					</tr>
				<?
					$i++;
				}
				?>

			</table>
			<input type="hidden" name="hidden_actual_po_id" id="hidden_actual_po_id">
			<input type="hidden" name="hidden_dtls_id" id="hidden_dtls_id">
			<input type="hidden" name="hidden_actual_po_id_return" id="hidden_actual_po_id_return">
			<input type="hidden" name="hidden_actual_po_no" id="hidden_actual_po_no">
			<input type="hidden" name="hidden_actual_po_no_return" id="hidden_actual_po_no_return">
			<input type="hidden" name="hidden_dtls_id_return" id="hidden_dtls_id_return">
			<input type="hidden" name="data_string" id="data_string">
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80">Total</th>
					<th width="80" align="right" id="total_po_qty"></th>
					<th width="60" align="right" id="total_delv_qty"></th>
					<th width="60" align="right" id="total_cur_qty"><?=$tot_cur_qty;?></th>
					<th width="60" align="right" id="total_bal_qty"></th>
				</tr>
			</tfoot>
		</table>
		<table width="850">
			<tr>
				<td align="center">
					<!-- <span style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span> -->
					<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			/* var js_set_string = '<? echo $js_set_string; ?>';
			js_set_arr = js_set_string.split(",");

			var i;
			for (i = 0; i < js_set_arr.length; i++) {
				js_set_value(js_set_arr[i]);
			} */
		</script>
		<script type="text/javascript">
			var tableFilters = {
				col_operation: {
					id: ["total_po_qty","total_delv_qty","total_bal_qty"],
					col: [7,8,10],
					operation: ["sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("tbl_list_search", -1, tableFilters);
		</script>
	<?
	exit();
}

if ($action == "add_info_action") {
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	$datas = explode("___", $hidden_add_info);
	$disabled_cond = "";
	if ($cbo_shipping_mode != 7) $disabled_cond = " disabled='true' ";

	?>
		<script>
			var truck_type_arr = '<? echo $truck_type_arr_json; ?>';
			var truck_type_arr = JSON.parse(truck_type_arr);
			var transport_type_arr = '<? echo $transport_type_arr_json; ?>';
			var transport_type_arr = JSON.parse(transport_type_arr);


			function fnc_close() {
				var truck_type = $("#cbo_truck_type").val() * 1;
				var truck_type_txt = "";
				if (truck_type) truck_type_txt = truck_type_arr[truck_type];
				var transport_type = $("#cbo_transport_type").val() * 1;
				var transport_type_txt = "";
				if (transport_type)
					transport_type_txt = transport_type_arr[transport_type];
				var vehicle_size = $("#txt_vehicle_size").val();
				var chassis_no = $("#txt_chassis_no").val();
				var currier = $("#txt_currier_name").val();
				var cbm = $("#txt_cbm_no").val();
				var sample = $("#txt_sample").val();
				var empty_carton = $("#txt_empty_carton").val();
				var gum_type = $("#txt_gum_type").val();
				var data = truck_type + "___" + transport_type + "___" + vehicle_size + "___" + chassis_no + "___" + currier + "___" + cbm + "___" + sample + "___" + empty_carton + "___" + gum_type;
				var data_val = truck_type_txt + " " + transport_type_txt + " " + vehicle_size + " " + chassis_no + " " + currier + " " + cbm + "___" + sample + "___" + empty_carton + "___" + gum_type;
				// alert(data_val);
				document.getElementById('all_field_data').value = data;
				document.getElementById('all_field_data_value').value = data_val;
				parent.emailwindow.hide();
			}
		</script>
		</head>

		<body>
			<form>
				<input type="hidden" name="all_field_data" id="all_field_data">
				<input type="hidden" name="all_field_data_value" id="all_field_data_value">


				<table cellspacing="0" cellpadding="0" cellpadding="0" border="0" rules="all" width="450" class="">
					<tr>
						<td colspan="5" height="10"></td>
					</tr>
					<tr>
						<td width="100"><strong>Truck Type</strong></td>
						<td> <? echo create_drop_down("cbo_truck_type", 122, $truck_type_arr, "", 1, "-- Select Truck Type --", $datas[0], ""); ?></td>


						<td width="110">&nbsp;&nbsp;<strong>Transport Type</strong></td>
						<td> <? echo create_drop_down("cbo_transport_type", 122, $transport_type_arr, "", 1, "-- Select Transport Type --", $datas[1], ""); ?></td>



					</tr>
					<tr>
						<td colspan="5" height="3"></td>
					</tr>

					<tr>
						<td width="100"><strong>Vehicle Size</strong></td>
						<td><input style="width: 112px;" type="text" class="text_boxes" name="txt_vehicle_size" id="txt_vehicle_size" value="<? echo $datas[2]; ?>"></td>


						<td width="110">&nbsp;&nbsp;<strong>Chassis Number</strong></td>
						<td> <input style="width: 112px;" type="text" class="text_boxes" name="txt_chassis_no" id="txt_chassis_no" value="<? echo $datas[3]; ?>"></td>



					</tr>
					<tr>
						<td colspan="5" height="3"></td>
					</tr>

					<tr>
						<td width="100"><strong>Courier Name</strong></td>
						<td><input style="width: 112px;" type="text" class="text_boxes" name="txt_currier_name" id="txt_currier_name" <? echo $disabled_cond; ?> value="<? echo $datas[4]; ?>"></td>
						<td width="110">&nbsp;&nbsp;<strong>CBM Of Goods</strong></td>
						<td> <input style="width: 112px;" type="text" class="text_boxes" name="txt_cbm_no" id="txt_cbm_no" value="<? echo $datas[5]; ?>"></td>


					</tr>
					<tr>
						<td width="100"><strong>Sample [Pcs]</strong></td>
						<td><input style="width: 112px;" type="text" class="text_boxes" name="txt_sample" id="txt_sample" value="<? echo $datas[6]; ?>"></td>
						<td width="110">&nbsp;&nbsp;<strong>Empty Carton [Pcs]</strong></td>
						<td> <input style="width: 112px;" type="text" class="text_boxes" name="txt_empty_carton" id="txt_empty_carton" value="<? echo $datas[7]; ?>"></td>
					</tr>
					<tr>
						<td width="100"><strong>Gum tape [Pcs]</strong></td>
						<td><input style="width: 112px;" type="text" class="text_boxes" name="txt_gum_type" id="txt_gum_type" value="<? echo $datas[8]; ?>"></td>
						<td width="110"></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="5" height="11"></td>
					</tr>
					<tr>
						<td colspan="5" align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px;" /> </td>
					</tr>


				</table>
			</form>
		</body>

		</html>

	<?
	exit();
}

if ($action == "ExFactoryPrintSonia") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");

	// ==================================== additional information query =====================================================
	$additional_sql = "SELECT  additional_info_id from PRO_EX_FACTORY_MST where delivery_mst_id='$data[1]' order by id asc ";
	$additional_arr = array();
	$kk = 0;
	$add_data = "";
	foreach (sql_select($additional_sql) as $vals) {
		if ($kk == 0) {
			if ($vals[csf("additional_info_id")]) {
				$add_data .= $vals[csf("additional_info_id")];
				$kk++;
			}
		}
	}
	//echo "string $add_data";
	$add_data = explode("___", $add_data);
	$truck_type = $truck_type_arr[$add_data[0]];
	$trans_type = $transport_type_arr[$add_data[1]];
	$sizes = $add_data[2];
	$chassis_no = $add_data[3];
	$courier_name = $add_data[4];
	$cbm = $add_data[5];
	$sample += $add_data[6];
	$empty_carton += $add_data[7];
	$gum_tape += $add_data[8];

	// echo "SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,delivery_date,depo_details,TO_CHAR(insert_date,'HH24:MI') as delv_time from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85";

	$delivery_mst_sql = sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,delivery_date,depo_details,escot_name,escot_mobile,TO_CHAR(insert_date,'HH24:MI') as delv_time
	from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	//echo $delivery_mst_sql;die;
	$sys_arr = array();
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$delivery_date = change_date_format($row[csf("delivery_date")]);
		$dev_time = $row[csf("delv_time")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
		$depo_details = $row[csf("depo_details")];
		$escot_name =$row[csf("escot_name")];
		$escot_mobile =$row[csf("escot_mobile")];
	}
	$sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;">

			<br>

			<?php
			$table_width = 950;
			$col_span = 6;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>


			<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<tr style="background-color:#fff;border-color:#fff;">
					<td valign="top" style="border:none;" align="left"><img src="../<? echo $image_location; ?>" height="60"></td>
					<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


						<div style="text-align:center;">
							<?

							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							$company_address = "";
							foreach ($nameArray as $result) {

								if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
								if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
								if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
								if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
								if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
								if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
								if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
								if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
									if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
								}
								if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
								if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
							}
							$company_address = chop($company_address, " , ");
							echo $company_address;
							?> <br>
							<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
							<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
						</div>


					</td>
					<td style="border:none; float:right;">

						<span style="float:left;" id="barcode_img_id"></span>

					</td>
				</tr>

			</table>


			<div style="width:950; margin-left:-50px;">
				<table style="border: none;" cellpadding="0" cellspacing="0" width="950" border="0" rules="" class="">
					<tr>
						<td colspan="6" height="10"></td>

					</tr>
					<tr>
						<td width="120"><strong>Challan No:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $challan_no; ?></td>

						<td width="120"><strong>Driver Name:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $driver_name; ?></td>


						<td width="120"><strong>Date:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $delivery_date; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>C&F Name:</strong></td>
						<td width="150"><?php echo $supplier_library[$forwarder]; ?></td>

						<td width="120"><strong>Mobile Num:</strong></td>
						<td width="150"><?php echo $mobile_no; ?></td>

						<td width="120"><strong>Delivery Time:</strong></td>
						<td width="150"><?php echo $dev_time; ?></td>



					</tr>


					<tr>
						<td width="120"><strong>C&F Address:</strong></td>
						<td width="150"><?php echo $address_1; ?></td>

						<td width="120"><strong>DL No:</strong></td>
						<td width="150"><?php echo $dl_no; ?></td>

						<td width="120"><strong>Do No:</strong></td>
						<td width="150"><?php echo $do_no; ?></td>

					</tr>

					<tr>
						<td width="120"><strong>Trns. Comp:</strong></td>
						<td width="150"><?php echo $supplier_library[$supplier_name]; ?></td>

						<td width="120"><strong>Truck No:</strong></td>
						<td width="150"><?php echo $truck_no; ?></td>
						<td width="120"><strong>GP No:</strong></td>
						<td width="150"><?php echo $gp_no_arr[$system_num]; ?></td>



					</tr>

					<tr>
						<td width="120"><strong>Trns. Type:</strong></td>
						<td width="150"><?php echo $truck_type; ?></td>

						<td width="120"><strong>Courier Company:</strong></td>
						<td width="150"><?php echo $courier_name; ?></td>

						<td width="120"><strong>Lock No:</strong></td>
						<td width="150"><?php echo $lock_no; ?></td>

					</tr>

					<tr>
						<td width="120"><strong>Forwarding Agent:</strong></td>
						<td width="150"><?php echo $supplier_library[$forwarder_2]; ?></td>

						<td width="120"><strong>Delivery Com:</strong></td>
						<td width="150"><?php echo $company_library[$delivery_company]; ?></td>

						<td width="120"><strong>Delivery Location:</strong></td>
						<td width="150"><?php echo $location_library[$delivery_location]; ?></td>

					</tr>

					<tr>
						<td width="120"><strong>Final Destination:</strong></td>
						<td width="150"><?php echo $destination_place; ?></td>

						<td width="120"><strong>Chassis No:</strong></td>
						<td width="150"><?php echo $chassis_no; ?></td>

						<td width="120"><strong>Delivery Floor:</strong></td>
						<td width="150"><?php echo $floor_library[$delivery_floor]; ?></td>

					</tr>

					<tr>
						<td width="120"><strong>Attention:</strong></td>
						<td width="150"><?php echo $attention; ?></td>

						<td width="120"><strong>CBM Of Goods:</strong></td>
						<td width="150"><?php echo $cbm; ?></td>

						<td width="120"><strong>Depo Details:</strong></td>
						<td colspan="3"><?php echo $depo_details; ?></td>


					</tr>
					<tr>
						<td width="120"><strong>Trns. Type / Size:</strong></td>
						<td width="150"><?php echo $trans_type . " / " . $sizes; ?></td>

						<td width="120"><strong>Escort Name:</strong></td>
						<td width="150"><?php echo $escot_name; ?></td>
						<td width="120"><strong>Escort Mobile:</strong></td>
						<td width="150"><?php echo $escot_mobile; ?></td>


					</tr>
					<tr>
					<td width="120"><strong>Remarks:</strong></td>
						<td width="150"><?php echo $remarks; ?></td>


					</tr>
					<tr>
					<td colspan="6" height="10"></td>
					</tr>


				</table>




			</div>
			<table style="margin-top:-0px;" align="right" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="border:none;">
					<tr>
						<!-- <th style="font-size:14px;" width="20">SL</th> -->
						<th style="font-size:14px;" width="70">Buyer</th>
						<th style="font-size:14px;" width="100">Style Ref.</th>
						<th style="font-size:14px;" width="100">Order No</th>
						<th style="font-size:14px;" width="60">Country</th>

						<th style="font-size:14px;" width="130">Item Name</th>
						<th style="font-size:14px;" width="150">Invoice No</th>
						<th style="font-size:14px;" width="50">Ship Mode</th>
						<th style="font-size:14px;" width="50">Delivery Qnty</th>
						<th style="font-size:14px;" width="50">Unit Price</th>
						<th style="font-size:14px;" width="50">Ex-Factory Value</th>
						<th style="font-size:14px;" width="50">NO Of Carton</th>
						<th style="font-size:14px;" width="100">Shipping Status</th>
						<th style="font-size:14px;">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
					if ($db_type == 2) {
						$sql = "SELECT c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty,sum(b.unit_price) as unit_price, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode, c.shiping_status
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by  c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id, c.shiping_mode, c.shiping_status
				order by a.style_ref_no";
					} else if ($db_type == 0) {
						$sql = "SELECT c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty, group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode, c.shiping_status
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode, c.shiping_status
				order by a.style_ref_no";
					}
					// echo $sql;
					$result = sql_select($sql);
										$exf_mst_ids=array();
					foreach ($result as $row) {
						$exf_mst_ids[$row[csf('id')]]=$row[csf('id')];
					}
					$mst_id_cond = where_con_using_array($exf_mst_ids, 1, 'a.mst_id');
					$act_po_res = sql_select("select  b.ACC_PO_NO , a.mst_id from  pro_ex_factory_actual_po_details a,wo_po_acc_po_info b  where a.actual_po_id=b.id and a.is_deleted=0 and b.is_deleted=0 $mst_id_cond");
					$act_po_arr=array();
					foreach ($act_po_res as $row) {
						$act_po_arr[$row[csf('mst_id')]][]=$row[csf('ACC_PO_NO')];
					}

					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<!-- <td style="font-size:14px;"><? // echo $i;
																?></td> -->
							<td style="font-size:14px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><?

									if (count($act_po_arr[$row[csf('id')]]))
									{
										echo implode(",", array_unique($act_po_arr[$row[csf('id')]]));
									}
									else echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>

							<td style="font-size:14px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td align="right">
							 <?
							    echo fn_number_format($row[csf("unit_price")], 4);
							 ?>
							</td>
							<td align="right">
								<?  $ex_factory_value=($row[csf("unit_price")])*$row[csf("total_qnty")];
								     echo fn_number_format($ex_factory_value, 4);
								 ?>
							</td>
							<td align="right" width="50" style="font-size:14px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo $shipment_status[$row[csf("shiping_status")]]; ?></p>
							</td>
							<td style="font-size:14px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr>
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
						<td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_qnty, 0, "", ""); ?></td>
						<td align="right" style="font-size:14px;font-weight: bold;"></td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
						<td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_carton_qnty, 0, "", ""); ?></td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
					</tr>
					<tr style="border:1px solid #FFFFFF;">
						<td colspan="11" style=" border:1px solid #FFFFFF;">
							<h3 align="left">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
						</td>
					</tr>
				</tbody>


			</table>

			<p>&nbsp;</p>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
				<thead bgcolor="#dddddd">
					<tr>
						<th>Sample (Pcs)</th>
						<th>Empty Carton (Pcs)</th>
						<th>Gumtape (Pcs)</th>
					</tr>
				</thead>
				<tr>
					<td align="center"><? echo $sample; ?></td>
					<td align="center"><? echo $empty_carton; ?></td>
					<td align="center"><? echo $gum_tape; ?></td>
				</tr>
			</table>

			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>" border="0" rules="all" class="">
				<tr>
					<td colspan="12" style=" border-color:#FFFFFF;">
						<?
						// echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
					</td>
				</tr>
			</table>

			<!--</div>-->

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}

if ($action == "ExFactoryPrint8") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");

	// ==================================== additional information query =====================================================
	$additional_sql = "SELECT  additional_info_id from PRO_EX_FACTORY_MST where delivery_mst_id='$data[1]' order by id asc ";
	$additional_arr = array();
	$kk = 0;
	$add_data = "";
	foreach (sql_select($additional_sql) as $vals) {
		if ($kk == 0) {
			if ($vals[csf("additional_info_id")]) {
				$add_data .= $vals[csf("additional_info_id")];
				$kk++;
			}
		}
	}
	//echo "string $add_data";
	$add_data = explode("___", $add_data);
	$truck_type = $truck_type_arr[$add_data[0]];
	$trans_type = $transport_type_arr[$add_data[1]];
	$sizes = $add_data[2];
	$chassis_no = $add_data[3];
	$courier_name = $add_data[4];
	$cbm = $add_data[5];
	$sample += $add_data[6];
	$empty_carton += $add_data[7];
	$gum_tape += $add_data[8];

	$delivery_mst_sql = sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,delivery_date,depo_details from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$delivery_date = change_date_format($row[csf("delivery_date")]);
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$depo_details = $row["DEPO_DETAILS"];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;">

			<br>

			<?php
			$table_width = 950;
			$col_span = 7;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>


			<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<tr style="background-color:#fff;border-color:#fff;">
					<td valign="top" style="border:none;" align="left"><img src="../<? echo $image_location; ?>" height="60"></td>
					<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


						<div style="text-align:center;">
							<?

							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							$company_address = "";
							foreach ($nameArray as $result) {

								if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
								if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
								if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
								if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
								if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
								if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
								if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
								if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
									if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
								}
								if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
								if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
							}
							$company_address = chop($company_address, " , ");
							echo $company_address;
							?> <br>
							<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
							<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
						</div>


					</td>
					<td style="border:none; float:right;">

						<span style="float:left;" id="barcode_img_id"></span>

					</td>
				</tr>

			</table>


			<div style="width:950; margin-left:-50px;">
				<table style="border: none;" cellpadding="0" cellspacing="0" width="950" border="0" rules="" class="">
					<tr>
						<td colspan="6" height="10"></td>

					</tr>
					<tr>
						<td width="120"><strong>Challan No:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $challan_no; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Driver Name:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $driver_name; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Date:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $delivery_date; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>C&F Name:</strong></td>
						<td width="150"><?php echo $supplier_library[$forwarder]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Mobile Num:</strong></td>
						<td width="150"><?php echo $mobile_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Do No:</strong></td>
						<td width="150"><?php echo $do_no; ?></td>
					</tr>


					<tr>
						<td width="120"><strong>C&F Address:</strong></td>
						<td width="150"><?php echo $address_1; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>DL No:</strong></td>
						<td width="150"><?php echo $dl_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>GP No:</strong></td>
						<td width="150">
							<?php
							if ($gp_no != "") {
								echo $gp_no;
							} else {
								$gp_sys_number = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='" . $challan_no_full . "' and basis=12 and status_active=1 and is_deleted=0");
								echo $gp_sys_number;
							}
							?>
						</td>
					</tr>

					<tr>
						<td width="120"><strong>Trns. Comp:</strong></td>
						<td width="150"><?php echo $supplier_library[$supplier_name]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Truck No:</strong></td>
						<td width="150"><?php echo $truck_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Lock No:</strong></td>
						<td width="150"><?php echo $lock_no; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Trns. Type:</strong></td>
						<td width="150"><?php echo $truck_type; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Courier Company:</strong></td>
						<td width="150"><?php echo $courier_name; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Trns. Type / Size:</strong></td>
						<td width="150"><?php echo $trans_type . " / " . $sizes; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Delivery Com:</strong></td>
						<td width="150"><?php echo $company_library[$delivery_company]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Delivery From:</strong></td>
						<td width="150"><?php echo $location_library[$delivery_location]; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Delivery Floor:</strong></td>
						<td width="150"><?php echo $floor_library[$delivery_floor]; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Final Destination:</strong></td>
						<td width="150"><?php echo $destination_place; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Chassis No:</strong></td>
						<td width="150"><?php echo $chassis_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>CBM Of Goods:</strong></td>
						<td width="150"><?php echo $cbm; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Attention:</strong></td>
						<td width="150"><?php echo $attention; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Remarks:</strong></td>
						<td width="150"><?php echo $remarks; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Depo Details:</strong></td>
						<td ><?php echo $depo_details; ?></td>
					</tr>
					<tr>
						<td colspan="6" height="10"></td>

					</tr>


				</table>




			</div>
			<table style="margin-top:-0px; margin-left: -50px;" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="border:none;">
					<tr>
						<!-- <th style="font-size:14px;" width="20">SL</th> -->
						<th style="font-size:14px;" width="70">Buyer</th>
						<th style="font-size:14px;" width="100">Style Ref.</th>
						<th style="font-size:14px;" width="100">Order No</th>
						<th style="font-size:14px;" width="100">Actual Po.</th>
						<th style="font-size:14px;" width="60">Country</th>

						<th style="font-size:14px;" width="130">Item Name</th>
						<th style="font-size:14px;" width="150">Invoice No</th>
						<th style="font-size:14px;" width="50">Ship Mode</th>
						<th style="font-size:14px;" width="50">Delivery Qnty</th>
						<th style="font-size:14px;" width="50">NO Of Carton</th>
						<!-- <th style="font-size:14px;" width="100">Shipping Status</th> -->
						<th style="font-size:14px;">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

				$sql = "SELECT c.id,a.buyer_name,a.gmts_item_id,a.style_ref_no,b.id	AS po_break_down_id,b.po_number,c.country_id,LISTAGG (CAST (c.invoice_no AS VARCHAR (4000)), ',')WITHIN GROUP (ORDER BY c.invoice_no)AS invoice_no,SUM (c.total_carton_qnty) AS total_carton_qnty,SUM (c.ex_factory_qnty)AS total_qnty,LISTAGG (CAST (c.remarks AS VARCHAR (4000)), ',')WITHIN GROUP (ORDER BY c.remarks)AS remarks,c.shiping_mode,c.shiping_status FROM wo_po_details_master a,wo_po_break_down b , pro_ex_factory_mst  c	WHERE a.id = b.job_id AND b.id = c.po_break_down_id	AND c.delivery_mst_id =$data[1] AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY c.id,a.buyer_name,a.gmts_item_id,a.style_ref_no,b.id,b.po_number,c.country_id,c.shiping_mode,c.shiping_status ORDER BY a.style_ref_no";
				// echo $sql; die;
				 $result = sql_select($sql);
				// echo "<pre>";
				//print_r($result);die;
				$mst_id_arr = array();
				$invoice_ids = "";
				foreach ($result as $v)
				{
					$mst_id_arr[$v['ID']] = $v['ID'];
					$po_break_down_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
					$invoice_ids .= $v['INVOICE_NO'].",";
				}
				$mst_id_cond = where_con_using_array($mst_id_arr,0,"mst_id");
				$po_break_down_id_cond = where_con_using_array($po_break_down_id_arr,0,"PO_BREAK_DOWN_ID");
				$actual_po_att_arr = return_library_array("SELECT mst_id, actual_po_id from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where status_active=1 $mst_id_cond", 'mst_id', 'actual_po_id');
				// echo "SELECT mst_id, actual_po_id from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where status_active=1 $mst_id_cond";
				// print_r($actual_po_library);

				$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info where status_active=1 $po_break_down_id_cond", 'id', 'acc_po_no');
				$inv_id = implode(",",array_unique(array_filter(explode(",",$invoice_ids))));
				$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst where id in($inv_id)", "id", "invoice_no");
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
						// echo $row[csf("total_qnty")]."<br>";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<!-- <td style="font-size:14px;"><? // echo $i;
																?></td> -->
							<td style="font-size:14px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><? echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><?
									echo $actual_po_library[$actual_po_att_arr[$row[csf("id")]]];
									?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
							</td>

							<td style="font-size:14px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<!-- <td align="right" style="font-size:14px;"><p><? //echo $shipment_status[$row[csf("shiping_status")]];
																				?></p></td> -->
							<td style="font-size:14px;">
								<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr>
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
						<td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_qnty, 0); ?></td>
						<td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_carton_qnty, 0); ?></td>
						<!-- <td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td> -->
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
					</tr>
					<tr style="border:1px solid #FFFFFF;">
						<td colspan="11" style=" border:1px solid #FFFFFF;">
							<h3 align="left">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
						</td>
					</tr>
				</tbody>


			</table>

			<p>&nbsp;</p>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px; margin-left: -50px;">
				<thead bgcolor="#dddddd">
					<tr>
						<th>Sample (Pcs)</th>
						<th>Empty Carton (Pcs)</th>
						<th>Gumtape (Pcs)</th>
					</tr>
				</thead>
				<tr>
					<td align="center"><? echo $sample; ?></td>
					<td align="center"><? echo $empty_carton; ?></td>
					<td align="center"><? echo $gum_tape; ?></td>
				</tr>
			</table>

			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>" border="0" rules="all" class="">
				<tr>
					<td colspan="12" style=" border-color:#FFFFFF;">
						<?
						// echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);

						?>
					</td>
				</tr>
			</table>

			<!--</div>-->

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}


if ($action == "ExFactoryPrint11") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");

	// ==================================== additional information query =====================================================
	$additional_sql = "SELECT  additional_info_id from PRO_EX_FACTORY_MST where delivery_mst_id='$data[1]' order by id asc ";
	$additional_arr = array();
	$kk = 0;
	$add_data = "";
	foreach (sql_select($additional_sql) as $vals) {
		if ($kk == 0) {
			if ($vals[csf("additional_info_id")]) {
				$add_data .= $vals[csf("additional_info_id")];
				$kk++;
			}
		}
	}
	//echo "string $add_data";
	$add_data = explode("___", $add_data);
	$truck_type = $truck_type_arr[$add_data[0]];
	$trans_type = $transport_type_arr[$add_data[1]];
	$sizes = $add_data[2];
	$chassis_no = $add_data[3];
	$courier_name = $add_data[4];
	$cbm = $add_data[5];
	$sample += $add_data[6];
	$empty_carton += $add_data[7];
	$gum_tape += $add_data[8];

	$delivery_mst_sql = sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,delivery_date,depo_details,cbm from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");

	
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$delivery_date = change_date_format($row[csf("delivery_date")]);
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$depo_details = $row["DEPO_DETAILS"];
		$cbm=$row["CBM"];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;">

			<br>

			<?php
			$table_width = 950;
			$col_span = 9;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>


			<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<tr style="background-color:#fff;border-color:#fff;">
					<td valign="top" style="border:none;" align="left"><img src="../<? echo $image_location; ?>" height="60"></td>
					<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$delivery_company];; ?></strong></span><br>


						<div style="text-align:center;">
							<?

							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							$company_address = "";
							foreach ($nameArray as $result) {

								if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
								if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
								if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
								if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
								if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
								if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
								if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
								if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
									if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
								}
								if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
								if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
							}
							$company_address = chop($company_address, " , ");
							echo $company_address;
							?> <br>
							<!-- <span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br> -->
							<span style="font-size:15px;"><strong>Export Delivery Challan</strong></span>
						</div>


					</td>
					<td style="border:none; float:right;">

						<span style="float:left;" id="barcode_img_id"></span>

					</td>
				</tr>

			</table>


			<div style="width:950; margin-left:-50px;">
				<table style="border: none;" cellpadding="0" cellspacing="0" width="950" border="0" rules="" class="">
					<tr>
						<td colspan="6" height="10"></td>

					</tr>
					<tr>
						<td width="120"><strong>Challan No:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $challan_no_full; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Transport Company:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $supplier_library[$supplier_name]; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Delivery Date Time:</strong></td>
						<td width="150" style="font-size:12px;"><?php echo $delivery_date; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Delivery Company:</strong></td>
						<td width="150"><?php echo $company_library[$delivery_company]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Truck No:</strong></td>
						<td width="150"><?php echo $truck_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Forwarding Agent:</strong></td>
						<td width="150"><?php echo  $supplier_library[$forwarder_2]; ?></td>
					</tr>


					<tr>
						<td width="120"><strong>Delivery Location:</strong></td>
						<td width="150"><?php echo $location_library[$delivery_location]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Chassis No:</strong></td>
						<td width="150"><?php echo $chassis_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>C&F Name:</strong></td>
						<td width="150">
							<?php
								echo $supplier_library[$forwarder];
							?>
						</td>
					</tr>

					<tr>
						<td width="120"><strong>Delivery floor:</strong></td>
						<td width="150"><?php echo $floor_library[$delivery_floor]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Trans Type/Size:</strong></td>
						<td width="150"><?php echo $trans_type . " / " . $sizes; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Depo Details:</strong></td>
						<td width="150"><?php echo $depo_details; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Do No.:</strong></td>
						<td width="150"><?php echo $do_no; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Lock No:</strong></td>
						<td width="150"><?php echo $lock_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Attention:</strong></td>
						<td width="150"><?php echo $attention; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>GP no:</strong></td>
						<td width="150"><?php echo $gp_no_arr[$challan_no_full]; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Driver Name:</strong></td>
						<td width="150"><?php echo $driver_name; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Remarks:</strong></td>
						<td width="150"><?php echo $remarks; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>Courier Company:</strong></td>
						<td width="150"><?php echo $courier_name; ?></td>

						<td width="120">&nbsp;&nbsp;<strong>Mobile No:</strong></td>
						<td width="150"><?php echo $mobile_no; ?></td>


						<td width="120">&nbsp;&nbsp;<strong>Final Destination:</strong></td>
						<td width="150"><?php echo $destination_place; ?></td>
					</tr>

					<tr>
						<td width="120"><strong>DL No:</strong></td>
						<td width="150"><?php echo $dl_no; ?></td>
						<td width="120"><strong></strong></td>
						<td width="150"><? ?></td>
						<td width="120">&nbsp;&nbsp;<strong> CBM :</strong></td>
						<td width="150"><? echo $cbm; ?></td>
					</tr>
					<tr>
						<td colspan="6" height="10"></td>

					</tr>


				</table>




			</div>

			<table style="margin-top:-0px; margin-left: -50px;" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="border:none;">
					<tr>
						<th style="font-size:14px;" width="20">SL</th>
						<th style="font-size:14px;" width="80">Buyer</th>
						<th style="font-size:14px;" width="80">Internal Ref</th>
						<th style="font-size:14px;" width="100">Style Ref.</th>
						<th style="font-size:14px;" width="100">Order No</th>
						<th style="font-size:14px;" width="100">Destination</th>
						<th style="font-size:14px;" width="130">Item Name</th>
						<th style="font-size:14px;" width="150">Invoice No</th>
						<th style="font-size:14px;" width="50">Ship Mode</th>
						<th style="font-size:14px;" width="50">Delivery Qnty</th>
						<th style="font-size:14px;" width="50">NO Of Carton</th>
						<th style="font-size:14px;" width="50">Net Weight</th>
						<th style="font-size:14px;" width="50">Gross Weight</th>
						<th style="font-size:14px;" width="100">Shipping Status</th> 
						<th style="font-size:14px;" width="100">Remarks</th> 
					</tr>
				</thead>
				<tbody>
					<?
					//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

				$sql = "SELECT c.id,a.buyer_name,a.gmts_item_id,a.style_ref_no,b.id	AS po_break_down_id,b.po_number,c.country_id,LISTAGG (CAST (c.invoice_no AS VARCHAR (4000)), ',')WITHIN GROUP (ORDER BY c.invoice_no)AS invoice_no,SUM (c.total_carton_qnty) AS total_carton_qnty,SUM (c.ex_factory_qnty)AS total_qnty,LISTAGG (CAST (c.remarks AS VARCHAR (4000)), ',')WITHIN GROUP (ORDER BY c.remarks)AS remarks,c.shiping_mode,c.shiping_status, sum(c.net_weight) as net_weight, sum(c.gross_weight) as gross_weight, c.destinatin,b.grouping,c.remarks FROM wo_po_details_master a,wo_po_break_down b , pro_ex_factory_mst  c	WHERE a.id = b.job_id AND b.id = c.po_break_down_id	AND c.delivery_mst_id =$data[1] AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY c.id,a.buyer_name,a.gmts_item_id,a.style_ref_no,b.id,b.po_number,c.country_id,c.shiping_mode,c.shiping_status, c.destinatin,b.grouping,c.remarks ORDER BY a.style_ref_no";
				//echo $sql; die;
				 $result = sql_select($sql);
				// echo "<pre>";
				//print_r($result);die;
				$mst_id_arr = array();
				$invoice_ids = "";
				foreach ($result as $v)
				{
					$mst_id_arr[$v['ID']] = $v['ID'];
					$po_break_down_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
					$invoice_ids .= $v['INVOICE_NO'].",";
				}
				$mst_id_cond = where_con_using_array($mst_id_arr,0,"mst_id");
				$po_break_down_id_cond = where_con_using_array($po_break_down_id_arr,0,"PO_BREAK_DOWN_ID");
				$actual_po_att_arr = return_library_array("SELECT mst_id, actual_po_id from PRO_EX_FACTORY_ACTUAL_PO_DETAILS where status_active=1 $mst_id_cond", 'mst_id', 'actual_po_id');
				
				$bookingSQL = "SELECT A.PO_BREAK_DOWN_ID, A.BOOKING_NO FROM WO_BOOKING_DTLS A WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  $po_break_down_id_cond";
				//echo $bookingSQL; exit();
				$poBooking = array();
				foreach(sql_select($bookingSQL) as $booking){
					$poBooking[$booking['PO_BREAK_DOWN_ID']] = $booking['BOOKING_NO'];
				}

				$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info where status_active=1 $po_break_down_id_cond", 'id', 'acc_po_no');
				$inv_id = implode(",",array_unique(array_filter(explode(",",$invoice_ids))));
				$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst where id in($inv_id)", "id", "invoice_no");
					$i = 1;
					$tot_qnty = $tot_carton_qnty = 0;
					foreach ($result as $row) 
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$color_count = count($cid);
						// echo $row[csf("total_qnty")]."<br>";
							?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:14px;"><? echo $i;?></td> 
							<td style="font-size:14px;">
								<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;" align="center">
								<p><? echo $row['GROUPING']; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;" align="center">
								<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;" align="center">
								<p><? echo $row[csf("po_number")]; ?>&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p><?
									echo $row[csf("destinatin")];
									?>&nbsp;</p>
							</td>

							<td style="font-size:14px;">
								<p>
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$garments_item_all = "";
									foreach ($garments_item_arr as $item_id) {
										$garments_item_all .= $garments_item[$item_id] . ",";
									}
									$garments_item_all = substr($garments_item_all, 0, -1);
									echo $garments_item_all;
									?>
									&nbsp;</p>
							</td>
							<td style="font-size:14px;">
								<p>
									<?
									$invoice_id = "";
									$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
									foreach ($invoice_id_arr as $inv_id) {
										if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
										else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
									}
									echo $invoice_id;
									?>&nbsp;</p>
							</td>
							<td align="right" style="font-size:14px;">
								<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
							</td>
							<td align="center" style="font-size:14px;">
								<p><? echo number_format($row[csf("total_qnty")], 0);
									$tot_qnty += $row[csf("total_qnty")]; ?></p>
							</td>
							<td align="center" style="font-size:14px;">
								<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
									$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
							</td>
							<td align="center" style="font-size:14px;">
								<p><? 
									echo $row[csf("net_weight")]; 
									$tot_net += $row[csf("net_weight")];
									?></p>
							</td>
							<td align="center" style="font-size:14px;">
								<p><? echo $row[csf("gross_weight")]; 
										$tot_gross += $row[csf("gross_weight")];
									?></p>
							</td>
							<td align="right" style="font-size:14px;"><p><? echo $shipment_status[$row[csf("shiping_status")]];?></p></td>
							<td align="left" style="font-size:14px;"><p><? echo $row[csf("remarks")];?></p></td>
							
						</tr>
						<?
						$i++;
					}
					?>
					<tr>
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
						<td align="center" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_qnty, 0); ?></td>
						<td align="center" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_carton_qnty, 0); ?></td>
						<td align="center" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_net, 0); ?></td>
						<td align="center" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_gross, 0); ?></td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
						<td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
					</tr>
					<tr style="border:1px solid #FFFFFF;">
						<td colspan="11" style=" border:1px solid #FFFFFF;">
							<h3 align="left">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
						</td>
					</tr>
				</tbody>


			</table>

			<p>&nbsp;</p>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px; margin-left: -50px;">
				<thead bgcolor="#dddddd">
					<tr>
						<th>Sample (Pcs)</th>
						<th>Empty Carton (Pcs)</th>
						<th>Gumtape (Pcs)</th>
					</tr>
				</thead>
				<tr>
					<td align="center"><? echo $sample; ?></td>
					<td align="center"><? echo $empty_carton; ?></td>
					<td align="center"><? echo $gum_tape; ?></td>
				</tr>
			</table>

			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>" border="0" rules="all" class="">
				<tr>
					<td colspan="12" style=" border-color:#FFFFFF;">
						<?
						// echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);

						?>
					</td>
				</tr>
			</table>

			<!--</div>-->

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}

if ($action == "ex_factory_print_new9")// For Auko Tex Group
	{
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,escot_name,escot_mobile from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$escot_name = $row[csf("escot_name")];
		$escot_mobile = $row[csf("escot_mobile")];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:1px; margin-left:10px;">

			<br>

			<?php
			$table_width = 1050;
			$col_span = 11;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>

			<div style="width:<? echo $table_width; ?>px;">
				<table style="border:hidden;" cellspacing="0" width="<? echo $table_width; ?>" border="0" rules="all" class="rpt_table">
					<tr style="background-color:#fff;border-color:#fff;">
						<td valign="top" style="border:none;" align="left"><img src="../<? echo $image_location; ?>" width="75"></td>
						<td valign="top" align="center" style="border:none; padding-right:100px;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>

							<div style="text-align:center;">
								<?

								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								$company_address = "";
								foreach ($nameArray as $result) {

									if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
									if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
									if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
									if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
									if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
									if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
									if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
									if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
										if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
									}
									if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
									if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
								}
								$company_address = chop($company_address, " , ");
								echo $company_address;
								?> <br>
								<span style="font-size:15px;"><strong>Export Delivery Challan</strong></span><br>
								<span style="font-size:12px;"><strong>100% Export Oriented Readymade Garments Delivery For Export</strong></span>
							</div>
						</td>
						<!-- <td style="border:none; float:right;">
	                <span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
	                 <span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
	                <span style="float:left;"   id="barcode_img_id"></span>
	                </td> -->
					</tr>
				</table>

				<div style="width:950; ">
					<table border="1" cellpadding="0" cellspacing="0" style="margin-top: 10px; font-size: 18px" rules="all" class="rpt_table" width="<? echo $table_width; ?>">
						<tr>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>C & F Information</strong></td>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Company Information</strong></td>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Transport Information</strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:20px"><? if ($forwarder > 0) {
															echo 'Delivery To';
														} else {
															echo 'Delivery To';
														} ?></td>
							<td style="font-size:16px; height:20px"><strong>: <? if ($forwarder > 0) {
																	echo $supplier_library[$forwarder];
																} else {
																	echo $supplier_library[$forwarder_2];
																}  ?></strong></td>
							<td style="font-size:16px; height:20px">Delivery Company</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $company_library[$delivery_company]; ?> </strong></td>
							<td style="font-size:16px; height:20px">Transport Company</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $supplier_library[$supplier_name]; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:20px" rowspan="2;">Address</td>
							<td style="font-size:10px;" rowspan="2;"><strong>: <? echo $address_1; ?></strong></td>
							<td style="font-size:16px; height:20px">Delivery Location</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $location_library[$delivery_location]; ?></strong>
							<td style="font-size:16px; height:20px">Vehical Number</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $truck_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:20px">Challan No</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $challan_no_full; ?></strong></td>
							<td style="font-size:16px; height:20px">Driver Name</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $driver_name; ?></strong></td>

						</tr>


						<tr>
							<td style="font-size:16px; height:20px">Attention</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $attention; ?></strong></td>
							<td style="font-size:16px; height:20px">Challan Date</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo change_date_format($data[2]);; ?></strong></td>
							<td style="font-size:16px; height:20px">D/L No</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $dl_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:20px" rowspan="1;">Mobile NO</td>
							<td style="font-size:16px; height:20px" rowspan="1;"><strong>: <? echo $contact_no; ?></strong></td>
							<td style="font-size:16px; height:20px">Escort Name & Mob</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $escot_name ?>,<? echo $escot_mobile ?></strong></td>
							<td style="font-size:16px; height:20px">Mobile No</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $mobile_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:20px">Final Destination</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $destination_place; ?></strong></td>
							<td style="font-size:16px; height:20px">Lock No</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $lock_no; ?></strong></td>
							<td style="font-size:16px; height:20px">Remarks</td>
							<td style="font-size:16px; height:20px"><strong>: <? echo $remarks; ?></strong></td>

						</tr>

					</table>
				</div>
				<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="border:none;">
						<tr>
							<th style="font-size:16px;" width="20">SL</th>
							<th style="font-size:16px;" width="60">Invoice No</th>
							<th style="font-size:16px;" width="70">Buyer</th>
							<th style="font-size:16px;" width="60">Job_No</th>
							<th style="font-size:16px;" width="60">IR/IB</th>
							<th style="font-size:16px;" width="100">Style Ref./Item Description</th>
							<th style="font-size:16px;" width="70">PO No</th>
							<th style="font-size:16px;" width="60">Carton QTY</th>
							<th style="font-size:16px;" width="60">Pcs QTY</th>
							<th style="font-size:16px;" width="50">Ship Mode</th>
							<th style="font-size:16px;" width="50">Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
						$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
						if ($db_type == 2) {
							$sql = "SELECT b.grouping, c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no ,b.grouping
				order by a.style_ref_no";
						} else if ($db_type == 0) {
							$sql = "SELECT b.grouping, c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no ,b.grouping
				order by a.style_ref_no";
						}
						//echo $sql;
						$result = sql_select($sql);
						$i = 1;
						$tot_qnty = $tot_carton_qnty = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$color_count = count($cid);
						?>

							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center" style="font-size:14px;"><? echo $i;  ?></td>
								<td style="font-size:14px;">
									<p>
										<?
										$invoice_id = "";
										$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
										foreach ($invoice_id_arr as $inv_id) {
											if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
											else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
										}
										echo $invoice_id;
										?>&nbsp;</p>
								</td>
								<td style="font-size:14px;">
									<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?></p>
								</td>
								<td style="font-size:14px;">
									<p><? echo $row[csf("job_no")]; ?>&nbsp;</p>
								</td>
								<td style="font-size:14px;">
									<p><? echo $row['GROUPING']; ?>&nbsp;</p>
								</td>
								<td style="font-size:14px;">
									<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
								</td>
								<td style="font-size:14px;">
									<p><?
										$actual_po = $row[csf("actual_po")];
										if ($actual_po) {
											$actual_po_no = "";
											$actual_po = explode(",", $actual_po);
											foreach ($actual_po as $val) {

												if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
												else $actual_po_no .= ',' . $actual_po_library[$val];
											}
											echo $actual_po_no;
										} else echo $row[csf("po_number")]; ?>&nbsp;</p>
								</td>

								<td align="center" style="font-size:14px;">
									<p><? echo number_format($row[csf("total_carton_qnty")], 0);
										$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
								</td>

								<td align="center" style="font-size:14px;">
									<p><? echo number_format($row[csf("total_qnty")], 0);
										$tot_qnty += $row[csf("total_qnty")]; ?></p>
								</td>

								<td align="center" style="font-size:14px;">
									<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
								</td>

								<td style="font-size:14px;">
									<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="7" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>

							<td align="center" style="font-size:14px;"><strong><? echo number_format($tot_carton_qnty, 0); ?></strong></td>
							<td align="center" style="font-size:14px;"><strong><? echo number_format($tot_qnty, 0); ?></strong></td>
							<td align="right" style="font-size:14px;">&nbsp;</td>
							<td align="right" style="font-size:14px;">&nbsp;</td>
						</tr>
						<tr style="border:none;">
							<td colspan="12" style=" border:none;border-color:#FFFFFF;">
								<h3 align="left">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<?
				// echo signature_table(63, $data[0], $table_width."px");
				echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
				?>
				<!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
						//  echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
		         	</td>
	         	</tr>
	        </tfoot> -->



			</div>

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}

if ($action == "ex_factory_print_new900") {
	echo 'ex_factory_print_new9';
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,escot_name,escot_mobile from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$escot_name = $row[csf("escot_name")];
		$escot_mobile = $row[csf("escot_mobile")];
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;">

			<br>

			<?php
			$table_width = 1050;
			$col_span = 11;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>

			<div style="width:<? echo $table_width; ?>px;">
				<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
					<tr style="background-color:#fff;border-color:#fff;">
						<td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
						<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


							<div style="text-align:center;">
								<?

								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								$company_address = "";
								foreach ($nameArray as $result) {

									if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
									if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
									if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
									if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
									if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
									if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
									if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
									if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
										if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
									}
									if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
									if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
								}
								$company_address = chop($company_address, " , ");
								echo $company_address;
								?> <br>
								<span style="font-size:15px;"><strong>Export Delivery Challan</strong></span><br>
								<span style="font-size:12px;"><strong>100% Export Oriented Readymade Garments Delivery For Export</strong></span>
							</div>


						</td>
						<!-- <td style="border:none; float:right;">
	                <span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
	                 <span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
	                <span style="float:left;"   id="barcode_img_id"></span>

	                </td> -->
					</tr>

				</table>

				<div style="width:950; ">
					<table border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px; font-size: 18px" rules="all" class="rpt_table" width="<? echo $table_width; ?>">
						<tr>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>C & F Information</strong></td>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Companies Information</strong></td>
							<td style="font-size:20px;" colspan="2;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Transport Information</strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:25px"><? if ($forwarder > 0) {
															echo 'Delivery To:';
														} else {
															echo 'Delivery To';
														} ?></td>
							<td style="font-size:16px; height:25px"><strong><? if ($forwarder > 0) {
																	echo $supplier_library[$forwarder];
																} else {
																	echo $supplier_library[$forwarder_2];
																}  ?></strong></td>
							<td style="font-size:16px; height:25px">Delivery Company:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
							<td style="font-size:16px; height:25px">Transport Company :</td>
							<td style="font-size:16px; height:25px"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>

						</tr>
						<tr>
							<td style="font-size:16px; height:25px" rowspan="2;">Address:</td>
							<td style="font-size:10px;" rowspan="2;"><strong><? echo $address_1; ?></strong></td>
							<td style="font-size:16px; height:25px">Delivery Location:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $location_library[$delivery_location]; ?>
							<td style="font-size:16px; height:25px">Vehical Number :</td>
							<td style="font-size:16px; height:25px"><strong><? echo $truck_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:25px">Challan No :</td>
							<td style="font-size:16px; height:25px"><strong><? echo $challan_no_full; ?></strong></td>
							<td style="font-size:16px; height:25px">Driver Name :</td>
							<td style="font-size:16px; height:25px"><strong><? echo $driver_name; ?></strong></td>

						</tr>


						<tr>
							<td style="font-size:16px; height:25px">Attention:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $attention; ?></strong></td>
							<td style="font-size:16px; height:25px">Challan Date :</td>
							<td style="font-size:16px; height:25px"><strong><? echo change_date_format($data[2]);; ?></strong></td>
							<td style="font-size:16px; height:25px">D/L No:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $dl_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:25px" rowspan="1;">Mobile NO :</td>
							<td style="font-size:16px; height:25px" rowspan="1;"><strong><? echo $contact_no; ?></strong></td>
							<td style="font-size:16px; height:25px">Escort Name & Mob:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $escot_name ?>,<? echo $escot_mobile ?></strong></td>
							<td style="font-size:16px; height:25px">Mobile No :</td>
							<td style="font-size:16px; height:25px"><strong><? echo $mobile_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:16px; height:25px">Final Destination:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $destination_place; ?></strong></td>
							<td style="font-size:16px; height:25px">Lock No:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $lock_no; ?></strong></td>
							<td style="font-size:16px; height:25px">Remarks:</td>
							<td style="font-size:16px; height:25px"><strong><? echo $remarks; ?></strong></td>

						</tr>

					</table>
				</div>
				<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="border:none;">
						<tr>
							<th style="font-size:16px;" width="20">SL</th>
							<th style="font-size:16px;" width="70">Invoice No</th>
							<th style="font-size:16px;" width="60">Buyer</th>
							<th style="font-size:16px;" width="60">Job_No</th>
							<th style="font-size:16px;" width="100">Style Ref./Item Description</th>
							<th style="font-size:16px;" width="70">PO No</th>
							<th style="font-size:16px;" width="60">Carton QTY</th>
							<th style="font-size:16px;" width="60">Pcs QTY</th>
							<th style="font-size:16px;" width="50">Ship Mode</th>
							<th style="font-size:16px;" width="50">Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
						$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
						if ($db_type == 2) {
							$sql = "SELECT c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
						} else if ($db_type == 0) {
							$sql = "SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.job_no, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
						}
						//echo $sql;
						$result = sql_select($sql);
						$i = 1;
						$tot_qnty = $tot_carton_qnty = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$color_count = count($cid);
						?>

							<tr bgcolor="<? echo $bgcolor; ?>">
								<td style="font-size:16px;"><? echo $i;  ?></td>
								<td style="font-size:16px;">
									<p>
										<?
										$invoice_id = "";
										$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
										foreach ($invoice_id_arr as $inv_id) {
											if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
											else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
										}
										echo $invoice_id;
										?>&nbsp;</p>
								</td>
								<td style="font-size:16px;">
									<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
								</td>
								<td style="font-size:16px;">
									<p><? echo $row[csf("job_no")]; ?>&nbsp;</p>
								</td>
								<td style="font-size:16px;">
									<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
								</td>
								<td style="font-size:16px;">
									<p><?
										$actual_po = $row[csf("actual_po")];
										if ($actual_po) {
											$actual_po_no = "";
											$actual_po = explode(",", $actual_po);
											foreach ($actual_po as $val) {

												if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
												else $actual_po_no .= ',' . $actual_po_library[$val];
											}
											echo $actual_po_no;
										} else echo $row[csf("po_number")]; ?>&nbsp;</p>
								</td>

								<td align="center" style="font-size:16px;">
									<p><? echo number_format($row[csf("total_carton_qnty")], 0, "", "");
										$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
								</td>

								<td align="center" style="font-size:16px;">
									<p><? echo number_format($row[csf("total_qnty")], 0);
										$tot_qnty += $row[csf("total_qnty")]; ?></p>
								</td>

								<td align="center" style="font-size:16px;">
									<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
								</td>

								<td style="font-size:16px;">
									<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="6" align="right" style="font-size:16px;"><strong>Grand Total :</strong></td>

							<td align="center" style="font-size:16px;"><strong><? echo number_format($tot_carton_qnty, 0, "", ""); ?></strong></td>
							<td align="center" style="font-size:16px;"><strong><? echo number_format($tot_qnty, 0, "", ""); ?></strong></td>
							<td align="right" style="font-size:16px;">&nbsp;</td>
							<td align="right" style="font-size:16px;">&nbsp;</td>
						</tr>
						<tr style="border:none;">
							<td colspan="12" style=" border:none;border-color:#FFFFFF;">
								<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<?
				// echo signature_table(63, $data[0], $table_width."px");
				echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
				?>
				<!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
						//  echo signature_table(63, $data[0], $table_width."px");
						echo signature_table(63, $data[0], $table_width . "px", "", "", $user_id);
						?>
		         	</td>
	         	</tr>
	        </tfoot> -->



			</div>

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}

if ($action == "ex_factory_print_new10") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$id_ref = str_replace("'", "", $data[4]);
	$show_hide_delv_info = str_replace("'", "", $data[5]);
	echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');
	//print_r ($data);
	$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info", 'id', 'acc_po_no');
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$vat_library = return_library_array("select id, vat_number from lib_company", "id", "vat_number");
	$location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, short_name from   lib_buyer", "id", "short_name");
	$invoice_library = return_library_array("select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no");
	$country_short_library = return_library_array("select id, short_name from  lib_country", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql = sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,inserted_by from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	$sys_arr = array();
	foreach ($delivery_mst_sql as $row) {
		$supplier_name = $row[csf("transport_supplier")];
		$driver_name = $row[csf("driver_name")];
		$truck_no = $row[csf("truck_no")];
		$dl_no = $row[csf("dl_no")];
		$lock_no = $row[csf("lock_no")];
		$destination_place = $row[csf("destination_place")];
		$challan_no = $row[csf("challan_no")];
		$challan_no_full = $row[csf("sys_number")];
		$sys_number_prefix_num = $row[csf("sys_number_prefix_num")];
		$mobile_no = $row[csf("mobile_no")];
		$do_no = $row[csf("do_no")];
		$gp_no = $row[csf("gp_no")];
		$forwarder = $row[csf("forwarder")];
		$forwarder_2 = $row[csf("forwarder_2")];
		$system_num = $row[csf("sys_number")];
		$delivery_company = $row[csf("delivery_company_id")];
		$delivery_location = $row[csf("delivery_location_id")];
		$delivery_floor = $row[csf("delivery_floor_id")];
		$attention = $row[csf("attention")];
		$remarks = $row[csf("remarks")];
		$sys_arr[$row[csf("sys_number")]] = $row[csf("sys_number")];
		$inserted_by = $row[csf("inserted_by")];
	}
	$sys_no_cond = where_con_using_array($sys_arr, 1, 'challan_no');
	$gp_no_arr = return_library_array("select challan_no, sys_number from  inv_gate_pass_mst where status_active=1 $sys_no_cond", "challan_no", "sys_number");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;">

			<br>

			<?php
			$table_width = 1050;
			$col_span = 12;

			if ($forwarder > 0) {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			} else {
				$supplier_sql = sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
				foreach ($supplier_sql as $row) {

					$address_1 = $row[csf("address_1")];
					$address_2 = $row[csf("address_2")];
					$address_3 = $row[csf("address_3")];
					$address_4 = $row[csf("address_4")];
					$contact_no = $row[csf("contact_no")];
				}
			}
			?>

			<div style="width:<? echo $table_width; ?>px;">
				<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
					<tr style="background-color:#fff;border-color:#fff;">
						<td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
						<td valign="top" align="center" style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


							<div style="text-align:center;">
								<?

								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								$company_address = "";
								foreach ($nameArray as $result) {

									if ($result[csf('plot_no')] != "") $company_address .= $result[csf('plot_no')] . ", ";
									if ($result[csf('level_no')] != "") $company_address .= $result[csf('level_no')] . ", ";
									if ($result[csf('road_no')] != "") $company_address .= $result[csf('road_no')] . ", ";
									if ($result[csf('block_no')] != "") $company_address .= $result[csf('block_no')] . ", ";
									if ($result[csf('city')] != "") $company_address .= $result[csf('city')] . "<br>";
									if ($result[csf('zip_code')] != "") $company_address .= $result[csf('zip_code')] . ", ";
									if ($result[csf('province')] != "") $company_address .= $result[csf('province')] . ", ";
									if ($result[csf('country_id')] != 0 && $result[csf('country_id')] != "") {
										if ($country_library[$result[csf('country_id')]] != "") $company_address .= $country_library[$result[csf('country_id')]] . ", ";
									}
									if ($result[csf('email')] != "") $company_address .= $result[csf('email')] . ", ";
									if ($result[csf('website')] != "") $company_address .= $result[csf('website')];
								}
								$company_address = chop($company_address, " , ");
								echo $company_address;
								?> <br>
								<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
								<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
							</div>


						</td>
						<td style="border:none; float:right;">
							<span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
							<span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
							<span style="float:left;" id="barcode_img_id"></span>

						</td>
					</tr>

				</table>

				<div style="width:1070; ">
					<table border="1" cellpadding="1" cellspacing="1" style="width:<? echo $table_width; ?>px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table">
						<tr>
							<td width="160" style="font-size:14px;"><? if ($forwarder > 0) {
																		echo 'C&F Name:';
																	} else {
																		echo 'Forwarding Agent';
																	} ?></td>
							<td width="160" style="font-size:14px;"><strong><? if ($forwarder > 0) {
																				echo $supplier_library[$forwarder];
																			} else {
																				echo $supplier_library[$forwarder_2];
																			}  ?></strong></td>
							<td width="160" style="font-size:14px;">Trns. Comp:</td>
							<td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
							<td width="160" style="font-size:14px;">Do No:</td>
							<td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:14px;">Address:</td>
							<td style="font-size:14px;"><strong><? echo $address_1 . "<br>";
																if ($contact_no != "") echo "Phone : " . $contact_no; ?></strong></td>
							<td style="font-size:14px;">Driver Name :</td>
							<td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
							<td style="font-size:14px;">GP No:</td>
							<td style="font-size:14px;"><strong><? echo $gp_no_arr[$system_num]; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:14px;">Attention:</td>
							<td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
							<td style="font-size:14px;">Mobile No :</td>
							<td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
							<td style="font-size:14px;">Lock No:</td>
							<td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
						</tr>
						<tr>
							<td style="font-size:14px;">DL No:</td>
							<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
							<td style="font-size:14px;">Final Destination:</td>
							<td style="font-size:14px;"><strong><? echo $destination_place; ?></strong></td>
							<td style="font-size:14px;">Truck No:</td>
							<td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
						</tr>
						<? if ($show_hide_delv_info) { ?>
							<tr>
								<td style="font-size:14px;">Delivery Floor:</td>
								<td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
								<td style="font-size:14px;">Delivery Company:</td>
								<td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
								<td style="font-size:14px;">Delivery Location:</td>
								<td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
							</tr>
						<? } ?>
						<tr>
							<td style="font-size:14px;">Vat No.:</td>
							<td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
							<td style="font-size:14px;">Remarks:</td>
							<td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
						</tr>
					</table>
				</div>
				<table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="border:none;">
						<tr>
							<th style="font-size:12px;" width="20">SL</th>
							<th style="font-size:12px;" width="60">Buyer</th>
							<th style="font-size:12px;" width="100">Style Ref.</th>
							<th style="font-size:12px;" width="100">Order No</th>
							<th style="font-size:12px;" width="60">Country</th>
							<th style="font-size:12px;" width="60">Country Short Name</th>
							<th style="font-size:12px;" width="130">Item Name</th>
							<th style="font-size:12px;" width="150">Invoice No</th>
							<!-- <th style="font-size:12px;" width="150">LC SC No</th> -->
							<th style="font-size:12px;" width="50">Ship Mode</th>
							<th style="font-size:12px;" width="50">FOC/Claim</th>
							<th style="font-size:12px;" width="90">Color</th>
							<th style="font-size:12px;" width="50">Delivery Qnty</th>
							<th style="font-size:12px;" width="50">NO Of Carton</th>
							<th style="font-size:12px;">Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no");
						$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
						if ($db_type == 2) {
							$sql = "SELECT c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id,d.color_number_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, c.total_carton_qnty as total_carton_qnty, sum(e.production_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
							from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c,wo_po_color_size_breakdown d,pro_ex_factory_dtls e
							where a.id=b.job_id
							AND c.id=e.mst_id
							And d.id=e.color_size_break_down_id
		 					and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and a.id=d.job_id and c.status_active=1 and c.is_deleted=0
							group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no,c.total_carton_qnty ,d.color_number_id
							order by a.style_ref_no";
						} else if ($db_type == 0) {
							$sql = "SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, d.color_number_id,
							group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(e.production_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
							from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c,wo_po_color_size_breakdown d,pro_ex_factory_dtls e
							where a.job_no=b.job_no_mst
							AND c.id=e.mst_id
							And d.id=e.color_size_break_down_id
							and b.id=c.po_break_down_id
							and c.delivery_mst_id=$data[1]
							and a.job_no=d.JOB_NO_MST
							and c.status_active=1 and c.is_deleted=0
							group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no,d.color_number_id
							order by a.style_ref_no";
						}
						//echo $sql;die;
						$result = sql_select($sql);

						$order_color_arr = array();

						foreach ($result as  $val) {
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["BUYER_NAME"] = $val["BUYER_NAME"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["STYLE_REF_NO"] = $val["STYLE_REF_NO"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["ACTUAL_PO"] = $val["ACTUAL_PO"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["PO_NUMBER"] = $val["PO_NUMBER"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["COUNTRY_ID"] = $val["COUNTRY_ID"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["GMTS_ITEM_ID"] = $val["GMTS_ITEM_ID"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["INVOICE_NO"] = $val["INVOICE_NO"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["LC_SC_NO"] = $val["LC_SC_NO"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["SHIPING_MODE"] = $val["SHIPING_MODE"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["FOC_OR_CLAIM"] = $val["FOC_OR_CLAIM"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["TOTAL_QNTY"] = $val["TOTAL_QNTY"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["TOTAL_CARTON_QNTY"] = $val["TOTAL_CARTON_QNTY"];
							$order_color_arr[$val["PO_NUMBER"]][$val["COLOR_NUMBER_ID"]]["REMARKS"] = $val["REMARKS"];
						}
						// echo "<pre>";
						// print_r($order_color_arr);
						// echo "</pre>";

						foreach ($order_color_arr as $order_key => $order_data) {
							foreach ($order_data as $color_key => $row) {
								$cound_order[$order_key]++;
								$cound_color[$order_key][$color_key]++;
							}
						}
						// echo "<pre>";
						// print_r($cound_invoice);
						// echo "</pre>";
						$i = 1;
						$order_chk = array();

						$tot_qnty = $tot_carton_qnty = 0;
						foreach ($order_color_arr as $order_key => $order_data) {
							foreach ($order_data as $color_key => $row) {
								$order_rowspan = $cound_order[$order_key];


								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								$color_count = count($cid);
						?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<?
									if (!in_array($order_key, $order_chk)) {

									?>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>"><? echo $i;  ?></td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p>
										</td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p>
										</td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><?
												$actual_po = $row[csf("actual_po")];
												if ($actual_po) {
													$actual_po_no = "";
													$actual_po = explode(",", $actual_po);
													foreach ($actual_po as $val) {

														if ($actual_po_no == "") $actual_po_no = $actual_po_library[$val];
														else $actual_po_no .= ',' . $actual_po_library[$val];
													}
													echo $actual_po_no;
												} else echo $row[csf("po_number")]; ?>&nbsp;</p>
										</td>

										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p>
										</td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p>
										</td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p>
												<?
												$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
												$garments_item_all = "";
												foreach ($garments_item_arr as $item_id) {
													$garments_item_all .= $garments_item[$item_id] . ",";
												}
												$garments_item_all = substr($garments_item_all, 0, -1);
												echo $garments_item_all;
												?>
												&nbsp;</p>
										</td>
										<td style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p>
												<?
												$invoice_id = "";
												$invoice_id_arr = array_unique(explode(",", $row[csf("invoice_no")]));
												foreach ($invoice_id_arr as $inv_id) {
													if ($invoice_id == "") $invoice_id = $invoice_library[$inv_id];
													else $invoice_id = $invoice_id . "," . $invoice_library[$inv_id];
												}
												echo $invoice_id;
												?>&nbsp;</p>
										</td>
										<!-- <td align="left" style="font-size:12px;">
											<p><? echo $lc_num_arr[$row[csf("lc_sc_no")]] . $sc_num_arr[$row[csf("lc_sc_no")]]; ?></p>
										</td> -->
										<td align="left" style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p>
										</td>
										<td align="left" style="font-size:12px;" rowspan="<?= $order_rowspan ?>">
											<p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p>
										</td>

									<?
									}
									?>

									<td align="right" style="font-size:12px;">
										<p><? echo $color_library[$color_key]; ?></p>
									</td>

									<td align="right" style="font-size:12px;">
										<p><? echo number_format($row[csf("total_qnty")], 0);
											$tot_qnty += $row[csf("total_qnty")]; ?></p>
									</td>
									<?
									if (!in_array($order_key, $order_chk)) {
										$order_chk[$order_key]=$order_key;
									?>
									<td align="right" style="font-size:12px;" rowspan="<?= $order_rowspan?>" >
										<p><? echo number_format($row[csf("total_carton_qnty")], 0);
											$tot_carton_qnty += $row[csf("total_carton_qnty")]; ?></p>
									</td>
									<td  align="center" style="font-size:12px;" rowspan="<?= $order_rowspan?>">
										<p><? echo implode(",", array_unique(explode(",", $row[csf("remarks")]))); ?></p>
									</td>

									<?
									}
									?>
								</tr>
						<?
								$i++;
							}
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="11" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>


							<td align="right" style="font-size:12px;"><? echo number_format($tot_qnty, 0, "", ""); ?></td>
							<td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty, 0, "", ""); ?></td>
							<td align="right" style="font-size:12px;">&nbsp;</td>

						</tr>
						<tr style="border:none;">
							<td colspan="14" style=" border:none;border-color:#FFFFFF;">
								<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty, "Pcs"); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<? echo signature_table(63, $data[0], $table_width . "px", "", "", $inserted_by); ?>
			</div>

			<script type="text/javascript" src="../js/jquery.js"></script>
			<script type="text/javascript" src="../js/jquerybarcode.js"></script>
			<script>
				fnc_generate_Barcode('<? echo $system_num; ?>', 'barcode_img_id');
			</script>
		</div>
	<?
	exit();
}


if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name'];
	$location = "../../file_upload/".$filename;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	}
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{
		$uploadOk = 1;
	}
	else
	{
		$uploadOk=0;
	}
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'gmts_delivery_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}

if($action=="ex_factory_print_urmi_new13")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	$show_hide_delv_info = str_replace("'","",$data[5]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$vat_library=return_library_array( "select id, vat_number from lib_company", "id", "vat_number"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$challan_no_full=$row[csf("sys_number")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$forwarder_2=$row[csf("forwarder_2")];
		$system_num=$row[csf("sys_number")];
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
		$attention=$row[csf("attention")];
		$remarks=$row[csf("remarks")];

	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
		<div style="width:900px; margin-top:10px; margin-left:55px;"> 
	    	<br> 
			<?php
				$table_width=950;
				$col_span=11;

				if($forwarder>0)
				{
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
				}else
				{
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
				}
	        ?>
 
	    	<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
		        <tr style="background-color:#fff;border-color:#fff;">
		            <td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
		            <td valign="top"  align="center"  style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>
						<div style="text-align:center;">
							<?

								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								$company_address="";
								foreach ($nameArray as $result)
								{

									if($result[csf('plot_no')]!="") $company_address.=$result[csf('plot_no')].", ";
									if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";
									if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", ";
									if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";
									if($result[csf('city')]!="") $company_address.= $result[csf('city')]."<br>";
									if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", ";
									if($result[csf('province')]!="") $company_address.= $result[csf('province')].", ";
									if($result[csf('country_id')]!=0 && $result[csf('country_id')]!=""){ if($country_library[$result[csf('country_id')]]!="") $company_address.= $country_library[$result[csf('country_id')]].", ";}
									if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";
									if($result[csf('website')]!="") $company_address.= $result[csf('website')];
								}
								$company_address=chop($company_address," , ");
								echo $company_address;
							?> <br>
							<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
							<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
						</div> 
	                </td>
		            <td style="border:none; float:right;">
	                <span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
	                 <span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
	                <span style="float:left;"   id="barcode_img_id"></span>

	                </td>
	       		</tr> 
	        </table>

	        <div style="width:950; margin-left:-50px;">
		        <table border="1" cellpadding="1" cellspacing="1" style="width:950px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table" >
			        <tr>
			        	<td width="160" style="font-size:14px;"><? if( $forwarder>0) { echo 'C&F Name:';} else {echo 'Forwarding Agent';}?></td>
			            <td width="160" style="font-size:14px;"><strong><? if( $forwarder>0){echo $supplier_library[$forwarder];} else { echo $supplier_library[$forwarder_2];}  ?></strong></td>
		                <td width="160" style="font-size:14px;">Trns. Comp:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
		              	<td width="160" style="font-size:14px;">Do No:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
			        </tr>
		            <tr>
		            	<td style="font-size:14px;">Address:</td>
			            <td style="font-size:14px;"><strong><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?></strong></td>
		                <td style="font-size:14px;">Driver Name :</td>
			            <td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
		                <td style="font-size:14px;">GP No:</td>
			            <td style="font-size:14px;"><strong><? echo $gp_no; ?></strong></td>
			        </tr>
		            <tr>
		           		<td style="font-size:14px;">Attention:</td>
			            <td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
		                <td style="font-size:14px;">Mobile No :</td>
			            <td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
		                <td style="font-size:14px;">Lock No:</td>
			            <td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
		            </tr>
		             <tr>
		                <td style="font-size:14px;">DL No:</td>
			           	<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
		                <td style="font-size:14px;">Final Destination:</td>
			            <td style="font-size:14px;"><strong><? echo $destination_place;?></strong></td>
			            <td style="font-size:14px;">Truck No:</td>
			            <td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
		            </tr>
		            <? if($show_hide_delv_info){?>
			        <tr>
		                <td style="font-size:14px;">Delivery Floor:</td>
			            <td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
			            <td style="font-size:14px;">Delivery Company:</td>
			            <td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
			            <td style="font-size:14px;">Delivery Location:</td>
			            <td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
		            </tr>
		            <? }?>
		            <tr>
		                <td style="font-size:14px;">Vat No.:</td> 
			            <td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
		                <td style="font-size:14px;">Remarks:</td>
			            <td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
		            </tr>
		        </table>
	      	</div>
			<br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

			
			$sql="SELECT c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode
			from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
			group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
			order by a.style_ref_no";
			
			// echo $sql;die;
			$result=sql_select($sql);
			$table_width=950;
			$col_span=10;
			$actual_po_id_array = array();
			foreach ($result as $v) 
			{
				if($v['ACTUAL_PO']){
					$actual_po_id_array[$v['ACTUAL_PO']] = $v['ACTUAL_PO'];
				}
			}		
			// print_r($actual_po_id_array); die;

			if (count($actual_po_id_array)>0 )
			{
				$actual_po_ids = implode(',',$actual_po_id_array);
				$acc_po_sql = "SELECT id,acc_po_no,acc_style_ref,po_break_down_id  from WO_PO_ACC_PO_INFO where id in($actual_po_ids) ";
				// echo $acc_po_sql;die;
				$acc_po_res=sql_select($acc_po_sql);
				$acc_po_array = array();
				foreach ($acc_po_res as  $v) 
				{
					$acc_po_array[$v['ID']]['ACC_PO_NO'] 		.=",".$v['ACC_PO_NO'];
					$acc_po_array[$v['ID']]['ACC_STYLE_REF'] 	.=",".$v['ACC_STYLE_REF'];
				}
				// echo "<pre>"; print_r($acc_po_array);
			}
			// echo $acc_po_sql; die;
			?>

			<div style="width:<? echo $table_width;?>px; margin-left:-50px;">
				<table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="20">SL</th>
						<th width="60">Buyer</th>
						<th width="100">Actual Style</th>
						<th width="110">Actual PO No</th>
						<th width="90">Country</th>
						<th width="60">Country Short Name</th>
						<th width="130">Item Name</th>
						<th width="150">Invoice No</th>
						<th width="50">Ship Mode</th>
						<th width="50">FOC/Claim</th>
						<th width="50">Delivery Qnty</th>
						<th width="50">NO Of Carton</th>
						<th >Remarks</th>
					</thead>
					<tbody>
					<?
					$i=1;
					$tot_qnty=$tot_carton_qnty=0;
					foreach($result as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						$color_count=count($cid);

						$acc_po_no 		= $acc_po_array[$row['ACTUAL_PO']]['ACC_PO_NO'];
						$acc_style_ref 	= $acc_po_array[$row['ACTUAL_PO']]['ACC_STYLE_REF'];

						$acc_po_no 		= ($acc_po_no !='')  		? $acc_po_no 		: $row['PO_NUMBER'];
						$acc_style_ref 	= ($acc_style_ref !='') 	? $acc_style_ref 	: $row["STYLE_REF_NO"];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td style="font-size:12px;"><? echo $i;  ?></td>
							<td style="font-size:12px;"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
							<td style="font-size:12px;"><p><?= trim($acc_style_ref,',') ?></p></td>
							<td style="font-size:12px;"><p><?= trim($acc_po_no,',') ?></p></td>
							<td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
							<td style="font-size:12px;"><p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
							<td style="font-size:12px;"><p>
							<?
							$garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
							$garments_item_all="";
							foreach($garments_item_arr as $item_id)
							{
								$garments_item_all .=$garments_item[$item_id].",";
							}
							$garments_item_all=substr($garments_item_all,0,-1);
							echo $garments_item_all;
							?>
							&nbsp;</p></td>
							<td style="font-size:12px;"><p>
							<?
							$invoice_id="";
							$invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
							foreach($invoice_id_arr as $inv_id)
							{
								if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

							}
							echo $invoice_id;
							?>&nbsp;</p></td>
							<td align="center" style="font-size:12px;"><p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p></td>
							<td align="center" style="font-size:12px;"><p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p></td>
							<td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
							<td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
							<td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>
					</tbody>
					<tr bgcolor="#CCCCCC">
						<td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
					
						<td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
						<td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
						<td align="right" style="font-size:12px;">&nbsp;</td>
					</tr>

				</table>
				<h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
			</div>
		</div>
		<?
		echo signature_table(63, $data[0], $table_width."px");
		?>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
	<?
	exit();
}
?>