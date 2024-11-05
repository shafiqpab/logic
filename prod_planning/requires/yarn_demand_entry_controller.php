<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_location") {
	$data = explode("_", $data);
	echo create_drop_down("cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/yarn_demand_entry_controller', this.value, 'load_drop_down_floor', 'td_floor_name' );");
	die;
}

if ($action == "load_drop_down_floor") {
	$data = explode("_", $data);
	echo create_drop_down("cbo_floor_name", 152, "select id,floor_name from lib_prod_floor where location_id='$data[0]' and production_process =2 and status_active =1 and is_deleted=0 order by floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "");
	die;
}

if ($action == "company_wise_report_button_setting") {
	extract($_REQUEST);
	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "' and module_id=4 and report_id=98 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",", $print_report_format);
	echo "$('#print').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print3').hide();\n";
	echo "$('#print4').hide();\n";
	if ($print_report_format != "") {
		foreach ($print_report_format_arr as $id) {
			if ($id == 35) {
				echo "$('#print2').show();\n";
			}
			if ($id == 36) {
				echo "$('#print3').show();\n";
			}
			if ($id == 78) {
				echo "$('#print').show();\n";
			}
			if ($id == 137) {
				echo "$('#print4').show();\n";
			}
		}
	}
	exit();
}

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	//print_r($data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_drop_down('requires/yarn_demand_entry_controller', document.getElementById('cbo_knitting_company').value, 'load_drop_down_location', 'location_td' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_drop_down('requires/yarn_demand_entry_controller', document.getElementById('cbo_knitting_company').value, 'load_drop_down_location', 'location_td' );");
	} else {
		echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 0, "load_drop_down('requires/yarn_demand_entry_controller', this.value, 'load_drop_down_location', 'location_td' );");
	}
	exit();
}

if ($action == "yarn_reqsn_popup") {
	echo load_html_head_contents("Requisition Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$data = explode("_", $data);
	$reqsn_no = $data[0];
	$type = $data[1];



	if ($type == 1) {
		$save_data = $data[2];
	}

	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	?>
	<script>
		function js_set_value(reqsn_no) {

			$('#reqsn_no').val(reqsn_no);
			show_list_view(reqsn_no + '_' + '1' + '_' + '<? echo $save_data; ?>', 'yarn_reqsn_popup', 'search_div', 'yarn_demand_entry_controller', '');
		}

		function color_row(tr_id) {
			var txt_demand_qnty = $('#txt_demand_qnty_' + tr_id).val() * 1;
			var hdn_dmnd_qnty = $('#hdn_dmnd_qnty' + tr_id).val() * 1;
			var txt_reqsn_bl = $('#txt_reqsn_bl' + tr_id).val() * 1;


			if (txt_demand_qnty > (hdn_dmnd_qnty + txt_reqsn_bl)) {
				alert("Demand Qnty Exceeds Requisition Qnty.");
				$('#txt_demand_qnty_' + tr_id).val('');
				$('#search' + tr_id).css('background-color', '#FFFFCC');
			} else {
				if (txt_demand_qnty > 0) {
					$('#search' + tr_id).css('background-color', 'yellow');
				} else {
					$('#search' + tr_id).css('background-color', '#FFFFCC');
				}
			}
			var tot_row = $("#tbl_list_search tbody tr").length;

			var ddd = {
				dec_type: 2,
				comma: 0,
				currency: ''
			}

			math_operation("txt_total_demand_qnty", "txt_demand_qnty_", "+", tot_row, ddd);
		}

		function fnc_close() {

			var save_data = '';
			var tot_demand_qnty = '';
			var operation_type = '<? echo $operation_type; ?>';

			$("#tbl_list_search tbody").find('tr').each(function() {
				var productId = $(this).find('input[name="productId[]"]').val();
				var reqsId = $(this).find('input[name="reqsId[]"]').val();
				var txt_demand_qnty = $(this).find('input[name="txt_demand_qnty[]"]').val();
				var coneQnty = $(this).find('input[name="txt_cone_qnty[]"]').val();
				var remarks = $(this).find('input[name="txt_remark[]"]').val();


				var txt_cone_qnty = 0;
				if (coneQnty == "" || coneQnty == 0) {
					txt_cone_qnty = 0;
				} else {
					txt_cone_qnty = coneQnty;
				}

				var ctnQnty = $(this).find('input[name="txt_ctn_qnty[]"]').val();
				var txt_ctn_qnty = 0;
				if (ctnQnty == "" || ctnQnty == 0) {
					txt_ctn_qnty = 0;
				} else {
					txt_ctn_qnty = ctnQnty;
				}



				//if (txt_demand_qnty * 1 > 0) {

				if (save_data == "") {
					save_data = productId + "_" + reqsId + "_" + txt_demand_qnty + "_" + txt_cone_qnty + "_" + txt_ctn_qnty + "_" + remarks;
				} else {
					save_data += "," + productId + "_" + reqsId + "_" + txt_demand_qnty + "_" + txt_cone_qnty + "_" + txt_ctn_qnty + "_" + remarks;
				}

				tot_demand_qnty = tot_demand_qnty * 1 + txt_demand_qnty * 1;

				//}

			});

			if (operation_type != 1) {
				if (tot_demand_qnty * 1 <= 0) {
					$('#reqsn_no').val('');
				}
			}

			$('#save_data').val(save_data);
			$('#tot_demand_qnty').val(tot_demand_qnty);

			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<?

			if ($type != 1) {
			?>
				<form name="yarnDemandQnty_1" id="yarnDemandQnty_1">
					<fieldset style="width:90%; margin-top:10px">
						<input type="hidden" name="reqsn_no" id="reqsn_no" class="text_boxes" value="<? echo $txt_requisition_no; ?>">
						<input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
						<input type="hidden" name="tot_demand_qnty" id="tot_demand_qnty" class="text_boxes" value="">

						<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
							<thead>
								<th>Buyer Name</th>
								<th>Source</th>
								<th>Requisition No</th>
								<th>Machine Dia</th>
								<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th>
							</thead>
							<tbody>
								<tr class="general">
									<td>
										<?
										if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
											if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id_cond = " and buy.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
											else $buyer_id_cond = "";
										} else {
											$buyer_id_cond = "";
										}

										echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
										?>
									</td>
									<td>
										<?
										$search_by_arr = array(1 => "Inside", 3 => "Outside");
										echo create_drop_down("cbo_type", 130, $search_by_arr, "", 0, "", "$knitting_source", '', 0);
										?>
									</td>
									<td>
										<input type="text" style="width:130px;" class="text_boxes" name="txt_requisition" id="txt_requisition" />
									</td>
									<td>
										<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
									</td>
									<td>
										<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_buyer_name').value+'**<? echo $companyID; ?>**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_requisition').value+'**<? echo $knitting_company; ?>', 'create_reqsn_search_list_view', 'search_div', 'yarn_demand_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
									</td>
								</tr>
							</tbody>
						</table>
						<div id="search_div" style="margin-top:10px">
							<?
							if ($save_data != "") {
								$tot_reqs_qnty = 0;
								$tot_demand_qnty = 0;
								$i = 1;
								$reqsn_array = array();
								$reqsn_remark_array = array();
								$reqsn_ctn_array = array();
								$reqsn_con_array = array();
								$explSaveData = explode(",", $save_data);
								for ($z = 0; $z < count($explSaveData); $z++) {
									$data_all = explode("_", $explSaveData[$z]);
									$prod_id = $data_all[0];
									$reqsn_id = $data_all[1];
									$demand_qnty = $data_all[2];
									$cone_qty = $data_all[3];
									$ctn_qty = $data_all[4];
									$remark = $data_all[5];

									$reqsn_array[$reqsn_id] = $demand_qnty;
									$reqsn_remark_array[$reqsn_id] = $remark;
									$reqsn_con_array[$reqsn_id] = $cone_qty;
									$reqsn_ctn_array[$reqsn_id] = $ctn_qty;
								}
							?>
								<table width="100%" border="1" rules="all" class="rpt_table">
									<thead>
										<th width="30">Sl</th>
										<th width="110">Supplier</th>
										<th width="50">Count</th>
										<th width="130">Composition</th>
										<th width="70">Type</th>
										<th width="80">Color</th>
										<th width="70">Lot No</th>
										<th width="90">Bl. Reqsn. Qnty</th>
										<th width="80">Demand Qty</th>
										<th width="55">No of Cone</th>
										<th width="55">No of Ctn</th>
										<th>Remark</th>
									</thead>
									</thead>
								</table>
								<div style="width:100%; overflow-y:scroll; max-height:300px;" id="scroll_body" align="left">
									<table class="rpt_table" rules="all" border="1" width="1000" id="tbl_list_search">
										<tbody>
											<?
											$sql = "select id, yarn_qnty, prod_id from ppl_yarn_requisition_entry where requisition_no=$txt_requisition_no and status_active=1 and is_deleted=0";
											$result = sql_select($sql);
											$prodIdArr = array();
											foreach ($result as $row) {
												$prodIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
											}

											//for product information
											$product_desc_array = array();
											$product_details_array = array();
											$sql_prod = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(',', $prodIdArr) . ")";
											$result_prod = sql_select($sql_prod);
											foreach ($result_prod as $row) {
												$compos = '';
												$desc = '';
												if ($row[csf('yarn_comp_percent2nd')] != 0) {
													$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
												} else {
													$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
												}

												$desc = $row[csf('lot')] . " " . $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_library[$row[csf('color')]];
												$product_desc_array[$row[csf('id')]] = $desc;
												$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
												$product_details_array[$row[csf('id')]]['comp'] = $compos;
												$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
												$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
												$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
												$product_details_array[$row[csf('id')]]['suppl'] = $supllier_arr[$row[csf('supplier_id')]];
												$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
											}
											//for product information end

											if ($txt_requisition_no != "") {

												$demand_sql = "select a.prod_id,a.requisition_no,sum(a.yarn_demand_qnty) as demand_qnty  from ppl_yarn_demand_reqsn_dtls a where requisition_no=$txt_requisition_no and a.status_active=1 and a.is_deleted=0 group by a.prod_id,a.requisition_no";
												$demand_sql_result  = sql_select($demand_sql);
												if (!empty($demand_sql_result)) {
													$demand_qty_array = array();
													foreach ($net_issue_result as $row) {
														$demand_qty_array[$row[csf('prod_id')]][$row[csf('requisition_no')]] = $row[csf('demand_qnty')];
													}
												}

												$net_issue_sql = "select t.prod_id,t.requisition_no,sum((case when t.transaction_type in(2) then t.cons_quantity else 0 end) -(case when t.transaction_type in(4) then t.cons_quantity else 0 end)) as net_issue_qty from inv_transaction t where  t.status_active=1 and t.is_deleted=0 and t.requisition_no=$txt_requisition_no group by t.prod_id,t.requisition_no";
												$net_issue_result  = sql_select($net_issue_sql);
												if (!empty($net_issue_result)) {
													$net_issue_qty_array = array();
													foreach ($net_issue_result as $row) {
														$net_issue_qty_array[$row[csf('prod_id')]][$row[csf('requisition_no')]] = $row[csf('net_issue_qty')];
													}
												}
											}


											foreach ($result as $row) {
												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";

												$demand_qnty = $reqsn_array[$row[csf('id')]];
												$demand_remark = $reqsn_remark_array[$row[csf('id')]];
												$demand_con = $reqsn_con_array[$row[csf('id')]];
												$demand_ctn = $reqsn_ctn_array[$row[csf('id')]];
												$prod_id = $row[csf('prod_id')];

												$prod_tot_demand_qnty = $demand_qty_array[$row[csf('prod_id')]][$txt_requisition_no]; // return_field_value("sum(yarn_demand_qnty)", "ppl_yarn_demand_reqsn_dtls", "requisition_id='" . $row[csf('id')] . "' and status_active=1 and is_deleted=0");
												$net_issue_qty = $net_issue_qty_array[$row[csf('prod_id')]][$txt_requisition_no];
												$bl_reqsn_qnty = 0;

												if ($net_issue_qty > $prod_tot_demand_qnty) {
													$bl_reqsn_qnty = $row[csf('yarn_qnty')] - $net_issue_qty;
												} else {
													$bl_reqsn_qnty = $row[csf('yarn_qnty')] - $prod_tot_demand_qnty;
												}

												if ($bl_reqsn_qnty < 0) $bl_reqsn_qnty = 0;

												if ($demand_qnty > 0) $bgcolor = "yellow";
												else $bgcolor = $bgcolor;
											?>
												<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
													<td width="30" align="center"><? echo $i; ?></td>
													<td width="110"><? echo $product_details_array[$row[csf('prod_id')]]['suppl']; ?></td>
													<td width="50"><? echo $product_details_array[$row[csf('prod_id')]]['count']; ?></td>
													<td width="130"><? echo $product_details_array[$row[csf('prod_id')]]['comp']; ?></td>
													<td width="70"><? echo $product_details_array[$row[csf('prod_id')]]['type']; ?></td>
													<td width="80"><? echo $product_details_array[$row[csf('prod_id')]]['color']; ?></td>
													<td width="70"><? echo $product_details_array[$row[csf('prod_id')]]['lot']; ?></td>
													<td width="90" align="right"><? echo number_format($bl_reqsn_qnty, 2); ?></td>
													<td width="80" align="center">
														<input type="text" name="txt_demand_qnty[]" id="txt_demand_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" onKeyUp="color_row(<? echo $i; ?>)" value="<? if ($demand_qnty > 0) echo $demand_qnty; ?>">
														<input type="hidden" id="hdn_dmnd_qnty<? echo $i; ?>" value="<? echo $demand_qnty; ?>">
													</td>
													<td width="55" align="center">
														<input type="text" name="txt_cone_qnty[]" id="txt_cone_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:42px" value="<? if ($demand_con > 0) echo $demand_con; ?>" placeholder="Cone">
													</td>
													<td align="center">
														<input type="text" name="txt_ctn_qnty[]" id="txt_ctn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? if ($demand_ctn > 0) echo $demand_ctn; ?>" placeholder="Ctn">
													</td>
													<input type="hidden" name="txt_reqsn_qnty[]" id="txt_reqsn_qnty_<? echo $i; ?>" value="<? echo $row[csf('yarn_qnty')]; ?>" class="text_boxes">
													<input type="hidden" name="txt_reqsn_bl[]" id="txt_reqsn_bl<? echo $i; ?>" value="<? echo $bl_reqsn_qnty; ?>" class="text_boxes">
													<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes">
													<input type="hidden" name="reqsId[]" id="reqsId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" class="text_boxes">
													<td align="center">
														<input type="text" name="txt_remark[]" id="txt_remark_<? echo $i; ?>" class="text_boxes" style="width:155px" value="<? echo $demand_remark; ?>" placeholder="Remark">
													</td>
												</tr>
											<?
												//$tot_reqs_qnty += $bl_reqsn_qnty;
												$tot_reqs_qnty += $row[csf('yarn_qnty')];
												$tot_demand_qnty += $demand_qnty;
												$tot_blreqs_qnty += $bl_reqsn_qnty;
												$i++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="7" align="right"><b>Total</b></th>
												<th><? echo number_format($tot_blreqs_qnty, 2); ?></th>
												<th align="center">
													<input type="text" name="txt_total_demand_qnty" id="txt_total_demand_qnty" class="text_boxes_numeric" style="width:65px" readonly disabled="disabled" value="<? echo number_format($tot_demand_qnty, 2); ?>" />

													<input type="hidden" id="total_req_qty" name="total_req_qty" value="<? echo $tot_reqs_qnty; ?>" </th>
												<th colspan="3">&nbsp;
													<input type="hidden" id="total_req_qty" name="total_req_qty" value="<? echo $tot_reqs_qnty; ?>" />
													<input type="hidden" id="totaldemandqty" name="totaldemandqty" value="<? echo $prod_tot_demand_qnty; ?>" />

													<input type="hidden" id="hdnprev_demandqty" name="hdnprev_demandqty" value="<? echo $hdn_previous_demand_qnty; ?>" />
												</th>
											</tr>
										</tfoot>
									</table>
								</div>
								<table width="620">
									<tr>
										<td align="center">
											<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
										</td>
									</tr>
								</table>
							<?
							}
							?>
						</div>
					<?
				} else {
					?>
						<div align="center" style="width:915px;">
							<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
							<table width="100%" border="1" rules="all" class="rpt_table">
								<thead>
									<th width="30">Sl</th>
									<th width="110">Supplier</th>
									<th width="50">Count</th>
									<th width="130">Composition</th>
									<th width="70">Type</th>
									<th width="80">Color</th>
									<th width="70">Lot No</th>
									<th width="90">Bl. Reqsn. Qnty</th>
									<th width="80">Demand Qty</th>
									<th width="55">No of Cone</th>
									<th width="55">No of Ctn</th>
									<th>Remark</th>
								</thead>
								</thead>
							</table>
							<div style="width:100%; overflow-y:scroll; max-height:300px;" id="scroll_body" align="left">
								<table class="rpt_table" rules="all" border="1" width="1000" id="tbl_list_search">
									<tbody>
										<?
										$tot_reqs_qnty = 0;
										$i = 1;
										$sql = "select id, yarn_qnty, prod_id from ppl_yarn_requisition_entry where requisition_no=$reqsn_no and status_active=1 and is_deleted=0";
										$result = sql_select($sql);
										$prodIdArr = array();
										foreach ($result as $row) {
											$prodIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
										}

										//for product information
										$product_desc_array = array();
										$product_details_array = array();
										$sql_prod = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(',', $prodIdArr) . ")";
										$result_prod = sql_select($sql_prod);
										foreach ($result_prod as $row) {
											$compos = '';
											$desc = '';
											if ($row[csf('yarn_comp_percent2nd')] != 0) {
												$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
											} else {
												$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
											}

											$desc = $row[csf('lot')] . " " . $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_library[$row[csf('color')]];
											$product_desc_array[$row[csf('id')]] = $desc;
											$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
											$product_details_array[$row[csf('id')]]['comp'] = $compos;
											$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
											$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
											$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
											$product_details_array[$row[csf('id')]]['suppl'] = $supllier_arr[$row[csf('supplier_id')]];
											$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
										}
										//for product information end
										if ($reqsn_no != "") {

											$demand_sql = "select a.prod_id,a.requisition_no,sum(a.yarn_demand_qnty) as demand_qnty  from ppl_yarn_demand_reqsn_dtls a where requisition_no=$reqsn_no and a.status_active=1 and a.is_deleted=0 group by a.prod_id,a.requisition_no";
											$demand_sql_result  = sql_select($demand_sql);
											if (!empty($demand_sql_result)) {
												$demand_qty_array = array();
												foreach ($net_issue_result as $row) {
													$demand_qty_array[$row[csf('prod_id')]][$row[csf('requisition_no')]] = $row[csf('demand_qnty')];
												}
											}

											$net_issue_sql = "select t.prod_id,t.requisition_no,sum((case when t.transaction_type in(2) then t.cons_quantity else 0 end) -(case when t.transaction_type in(4) then t.cons_quantity else 0 end)) as net_issue_qty from inv_transaction t where  t.status_active=1 and t.is_deleted=0 and t.requisition_no=$reqsn_no group by t.prod_id,t.requisition_no";
											$net_issue_result  = sql_select($net_issue_sql);
											if (!empty($net_issue_result)) {
												$net_issue_qty_array = array();
												foreach ($net_issue_result as $row) {
													$net_issue_qty_array[$row[csf('prod_id')]][$row[csf('requisition_no')]] = $row[csf('net_issue_qty')];
												}
											}
										}

										foreach ($result as $row) {
											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";


											$prod_tot_demand_qnty = $demand_qty_array[$row[csf('prod_id')]][$reqsn_no]; // return_field_value("sum(yarn_demand_qnty)", "ppl_yarn_demand_reqsn_dtls", "requisition_id='" . $row[csf('id')] . "' and status_active=1 and is_deleted=0");
											$net_issue_qty = $net_issue_qty_array[$row[csf('prod_id')]][$reqsn_no];
											$bl_reqsn_qnty = 0;

											if ($net_issue_qty > $prod_tot_demand_qnty) {
												$bl_reqsn_qnty = $row[csf('yarn_qnty')] - $net_issue_qty;
											} else {
												$bl_reqsn_qnty = $row[csf('yarn_qnty')] - $prod_tot_demand_qnty;
											}

											if ($bl_reqsn_qnty < 0) $bl_reqsn_qnty = 0;

											$demand_qnty = $reqsn_array[$row[csf('id')]];

										?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
												<td width="30" align="center"><? echo $i; ?></td>
												<td width="110">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['suppl']; ?></p>
												</td>
												<td width="50">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['count']; ?></p>
												</td>
												<td width="130">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['comp']; ?></p>
												</td>
												<td width="70">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['type']; ?></p>
												</td>
												<td width="80">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['color']; ?></p>
												</td>
												<td width="70" title="<?= $row[csf('prod_id')]; ?>">
													<p><? echo $product_details_array[$row[csf('prod_id')]]['lot']; ?></p>
												</td>
												<td width="90" align="right"><? echo number_format($bl_reqsn_qnty, 2); ?></td>
												<td width="80" align="center">
													<input type="text" name="txt_demand_qnty[]" id="txt_demand_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" onKeyUp="color_row(<? echo $i; ?>)" value="<? if ($demand_qnty > 0) echo $demand_qnty; ?>">
													<input type="hidden" id="hdn_dmnd_qnty<? echo $i; ?>" value="<? echo $demand_qnty; ?>">
												</td>

												<td width="55" align="center">
													<input type="text" name="txt_cone_qnty[]" id="txt_cone_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:42px" value="<? if ($demand_con > 0) echo $demand_con; ?>" placeholder="Cone">
												</td>
												<td align="center">
													<input type="text" name="txt_ctn_qnty[]" id="txt_ctn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? if ($demand_ctn > 0) echo $demand_ctn; ?>" placeholder="Ctn">
												</td>
												<input type="hidden" name="txt_reqsn_qnty[]" id="txt_reqsn_qnty_<? echo $i; ?>" value="<? echo $row[csf('yarn_qnty')]; ?>" class="text_boxes">
												<input type="hidden" name="txt_reqsn_bl[]" id="txt_reqsn_bl<? echo $i; ?>" value="<? echo $bl_reqsn_qnty; ?>" class="text_boxes">
												<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes">
												<input type="hidden" name="reqsId[]" id="reqsId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" class="text_boxes">
												<td align="center"><input type="text" name="txt_remark[]" id="txt_remark_<? echo $i; ?>" class="text_boxes" style="width:155px" value="<? echo $demand_remark; ?>" placeholder="Remark"></td>
											</tr>
										<?
											$tot_reqs_qnty += $row[csf('yarn_qnty')];
											$tot_blreqs_qnty += $bl_reqsn_qnty;
											$tot_demand_qnty += $demand_qnty;
											$i++;
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="7" align="right"><b>Total</b></th>
											<th><? echo number_format($tot_blreqs_qnty, 2); ?></th>
											<th align="center">
												<input type="text" name="txt_total_demand_qnty" id="txt_total_demand_qnty" class="text_boxes_numeric" style="width:65px" readonly disabled="disabled" value="<? echo number_format($tot_demand_qnty, 2); ?>" />

												<input type="hidden" id="total_req_qty" name="total_req_qty" value="<? echo $tot_reqs_qnty; ?>" </th>
											<th colspan="3">&nbsp;
												<input type="hidden" id="total_req_qty" name="total_req_qty" value="<? echo $tot_reqs_qnty; ?>" />

												<input type="hidden" id="totaldemandqty" name="totaldemandqty" value="<? echo $prod_tot_demand_qnty; ?>" />

												<input type="hidden" id="hdnprev_demandqty" name="hdnprev_demandqty" value="<? echo $hdn_previous_demand_qnty; ?>" />
											</th>
										</tr>
									</tfoot>
								</table>
							</div>
							<table width="620">
								<tr>
									<td align="center">
										<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
									</td>
								</tr>
							</table>
						</div>
					<?
				}
				if ($type != 1) {
					?>
					</fieldset>
				</form>
			<?
				}
			?>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}


if ($action == "create_reqsn_search_list_view") {
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$data = explode("**", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$company_id = $data[2];
	$type = $data[3];
	$txt_requisition = trim($data[4]);
	$knitting_company = $data[5];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$sales_buyer_id_cond = " and ( (e.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ") and e.within_group=1) or (e.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ") and e.within_group=2) )";
			} else {
				$buyer_id_cond = "";
				$sales_buyer_id_cond = "";
			}
		} else {
			$buyer_id_cond = "";
			$sales_buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$data[1]";
		$sales_buyer_id_cond = " and ( (e.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ") and e.within_group=1) or (e.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ") and e.within_group=2) )";
	}

	$reqsn_cond = '';
	if ($txt_requisition != "") {
		$reqsn_cond = " and c.requisition_no='" . $txt_requisition . "'";
	}

	if (trim($data[0]) != "") {
		$machine_dia_cond = " and b.machine_dia like '$search_string'";
	}

	if ($knitting_company > 0) {
		$knitting_company_cond = " and b.knitting_party=$knitting_company";
	}

	$plan_sql = "SELECT a.company_id, (case when e.within_group=1 then e.po_buyer when e.within_group=2 then e.buyer_id end) as buyer_id, a.booking_no, b.id, c.requisition_no, sum(c.yarn_qnty) as reqs_qnty,a.is_sales,b.knitting_party 
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d, fabric_sales_order_mst e
	where a.company_id=$company_id and a.id=b.mst_id and b.knitting_source=$type $knitting_company_cond $machine_dia_cond and b.id=c.knit_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 
	and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_sales=1 and c.knit_id=d.DTLS_ID and d.po_id=e.id and d.status_active=1  $sales_buyer_id_cond $reqsn_cond
	group by a.company_id, a.buyer_id,a.booking_no, b.id, c.requisition_no,a.is_sales,b.knitting_party,e.within_group, e.po_buyer, e.buyer_id 
	union all
	select a.company_id, a.buyer_id,a.booking_no, b.id, c.requisition_no, sum(c.yarn_qnty) as reqs_qnty,a.is_sales,b.knitting_party 
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c 
	where a.company_id=$company_id and a.id=b.mst_id and b.knitting_source=$type $knitting_company_cond $machine_dia_cond and b.id=c.knit_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 
	and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_sales!=1  $buyer_id_cond $sales_buyer_id_cond $reqsn_cond
	group by a.company_id, a.buyer_id,a.booking_no, b.id, c.requisition_no,a.is_sales,b.knitting_party 
	order by requisition_no ";

	//echo $plan_sql; die;
	$nameArray = sql_select($plan_sql);
	foreach ($nameArray as $row) {
		$program_ids .= $row[csf('id')] . ",";
		$requisition_no .= $row[csf('requisition_no')] . ",";
		$booking_nos .= "'" . $row[csf('booking_no')] . "',";
	}

	$requisitionNo = chop($requisition_no, ",");
	$bookingNos = chop($booking_nos, ",");

	if ($bookingNos != "") {
		$bookingNumbersArr = array_unique(explode(",", $bookingNos));
		if ($db_type == 2 && count($bookingNumbersArr) > 999) {
			$booking_cond = " and (";
			$bookingNumbersArr = array_chunk($bookingNumbersArr, 999);
			foreach ($bookingNumbersArr as $bookingNumber) {
				$bookingNumbers = implode(",", $bookingNumber);
				$booking_cond .= "a.booking_no in($bookingNumbers) or ";
			}

			$booking_cond = chop($booking_cond, 'or ');
			$booking_cond .= ")";
		} else {
			$booking_cond = " and a.booking_no in (" . implode(",", $bookingNumbersArr) . ")";
		}
	}

	if ($requisitionNo != "") {
		$requisitionNumbersArr = array_unique(explode(",", $requisitionNo));
		if ($db_type == 2 && count($requisitionNumbersArr) > 999) {
			$requisition_cond = " and (";
			$requisitionNumbersArr = array_chunk($requisitionNumbersArr, 999);
			foreach ($requisitionNumbersArr as $requisitionNumber) {
				$requisitionNumbers = implode(",", $requisitionNumber);
				$requisition_cond .= "requisition_no in($requisitionNumbers) or ";
			}

			$requisition_cond = chop($requisition_cond, 'or ');
			$requisition_cond .= ")";
		} else {
			$requisition_cond = " and requisition_no in (" . implode(",", $requisitionNumbersArr) . ")";
		}
	}

	$program_ids =  chop($program_ids, ",");
	if ($program_ids != "") {
		$program_idArr = array_unique(explode(",", $program_ids));
		if ($db_type == 2 && count($program_idArr) > 999) {
			$program_cond = " and (";
			$program_idArr = array_chunk($program_idArr, 999);
			foreach ($program_idArr as $program_id) {
				$programids = implode(",", $program_id);
				$program_cond .= "dtls_id in($programids) or ";
			}

			$program_cond = chop($program_cond, 'or ');
			$program_cond .= ")";
		} else {
			$program_cond = " and dtls_id in (" . implode(",", $program_idArr) . ")";
		}
	}

	$sqlDemB = sql_select("select requisition_no,sum(yarn_demand_qnty) as total_yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls where status_active=1 and is_deleted=0 $requisition_cond group by requisition_no");

	$demand_quantity_array = array();
	foreach ($sqlDemB as $row) {
		$demand_quantity_array[$row[csf('requisition_no')]] = $row[csf('total_yarn_demand_qnty')];
	}

	$net_issue_sql = "select t.requisition_no,sum((case when t.transaction_type in(2) then t.cons_quantity else 0 end) -(case when t.transaction_type in(4) then t.cons_quantity else 0 end)) as net_issue_qty from inv_transaction t where  t.status_active=1 and t.is_deleted=0 $requisition_cond group by t.requisition_no";
	$net_issue_result  = sql_select($net_issue_sql);
	if (!empty($net_issue_result)) {
		$net_issue_qty_array = array();
		foreach ($net_issue_result as $row) {
			$net_issue_qty_array[$row[csf('requisition_no')]] = $row[csf('net_issue_qty')];
		}
	}

	if ($db_type == 0) {
		$plan_po_rs = sql_select("select dtls_id, group_concat(distinct(po_id)) as po_id,is_sales from ppl_planning_entry_plan_dtls where company_id=$company_id $program_cond group by dtls_id,is_sales");
	} else {
		$plan_po_rs = sql_select("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id,is_sales from ppl_planning_entry_plan_dtls where company_id=$company_id $program_cond group by dtls_id,is_sales");
	}

	$plan_po_arr = $plan_sales_arr = array();
	foreach ($plan_po_rs as $row) {
		$plan_details_array[$row[csf('dtls_id')]] = $row[csf('po_id')];
		if ($row[csf('is_sales')] == 1) {
			$plan_sales_arr[] = $row[csf('po_id')];
		} else {
			$plan_po_arr[] = $row[csf('po_id')];
		}
	}

	$po_array = array();
	$booking_sql = sql_select("select a.booking_no,a.buyer_id,c.style_ref_no,d.id,d.file_no,d.grouping internal_ref,d.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and company_id=$company_id and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 $booking_cond group by a.booking_no,a.buyer_id,c.style_ref_no, d.id,d.file_no,d.grouping,d.po_number");

	foreach ($booking_sql as $row) {

		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('internal_ref')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
	}

	$sales_order_array = array();
	if (!empty($plan_sales_arr)) {
		$sales_order_sql = sql_select("select a.id,a.sales_booking_no, a.job_no,a.within_group,a.style_ref_no, a.buyer_id, a.po_buyer from fabric_sales_order_mst a where a.status_active = 1 and a.company_id=$company_id and a.id in(" . implode(",", $plan_sales_arr) . ")");
		foreach ($sales_order_sql as $row) {
			$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
			$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
			$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$sales_order_array[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];
			$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		}
	}
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Requisition No</th>
			<th width="90">Booking No</th>
			<th width="110">Buyer</th>
			<th width="80">File No</th>
			<th width="80">Internal Ref.</th>
			<th width="120"><?php echo ($row[csf('is_sales')] == 1) ? "Sales " : "" ?>Order No</th>
			<th width="90"><? echo $company_arr[str_replace("'", "", $company_id)]; ?></th>
			<th width="120">Style</th>
			<th width="80">Requisition Qty.</th>
			<th width="100">Net issue Qty.</th>
			<th>Banlace Qty.</th>
		</thead>
	</table>
	<div style="width:1110px; overflow-y:scroll; max-height:270px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($nameArray as $row) {
				if ($i == 1) {
			?>
					<tbody>
					<?php
				}
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if ($row[csf('is_sales')] == 1) {
					$po_ids_arr = $plan_details_array[$row[csf('id')]];
					$po_no = $sales_order_array[$po_ids_arr]['sales_order_no'];

					if ($sales_order_array[$po_ids_arr]['within_group'] == 1) {
						//$buyer_id = $po_array[$row[csf('booking_no')]]['buyer_id'];
						//$style_ref = $po_array[$row[csf('booking_no')]]['style_ref'];
						$buyer_id = $sales_order_array[$po_ids_arr]['po_buyer'];
					} else {
						$buyer_id = $sales_order_array[$po_ids_arr]['buyer_id'];
					}
					$style_ref = $sales_order_array[$po_ids_arr]['style_ref_no'];
				} else {
					$po_id = explode(",", $plan_details_array[$row[csf('id')]]);

					$po_no = '';
					$style_ref = '';
					$internal_ref = '';
					$file_no = '';
					foreach ($po_id as $val) {
						if ($po_no == '') {
							$po_no = $po_array[$val]['no'];
						} else {
							$po_no .= "," . $po_array[$val]['no'];
						}

						//echo $po_no;
						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
						if ($internal_ref == '') $internal_ref = $po_array[$val]['ref'];
						else $internal_ref .= "," . $po_array[$val]['ref'];
						if ($file_no == '') $file_no = $po_array[$val]['file'];
						else $file_no .= "," . $po_array[$val]['file'];
						$buyer_id = $row[csf('buyer_id')];
					}

					//echo $po_no;
				}

				$net_issue_qty = $net_issue_qty_array[$row[csf('requisition_no')]];
				$demand_quantity = $demand_quantity_array[$row[csf('requisition_no')]];

				if ($net_issue_qty > $demand_quantity) {
					$bl_reqsn_qnty = $row[csf('reqs_qnty')] - $net_issue_qty;
				} else if ($demand_quantity > 0) {
					$bl_reqsn_qnty = $row[csf('reqs_qnty')] - $demand_quantity;
				} else {
					$bl_reqsn_qnty = $row[csf('reqs_qnty')];
				}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $row[csf('requisition_no')]; ?>)">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="90" align="center"><? echo $row[csf('requisition_no')]; ?></td>
						<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
						<td width="110">
							<p><? echo $buyer_arr[$buyer_id]; ?></p>
						</td>
						<td width="80">
							<p><? echo $file_no; ?>&nbsp;</p>
						</td>
						<td width="80">
							<p><? echo $internal_ref; ?>&nbsp;</p>
						</td>
						<td width="120">
							<p><? echo $po_no; ?></p>
						</td>
						<td width="90">
							<p><? echo $plan_details_array[$row[csf('id')]]; ?></p>
						</td>
						<td width="120">
							<p><? echo $style_ref; ?></p>
						</td>
						<td width="80" align="right"><? echo number_format($row[csf('reqs_qnty')], 2); ?></td>
						<td width="100" align="right"><? echo number_format($net_issue_qty, 2); ?></td>
						<td align="right"><? echo number_format($bl_reqsn_qnty, 2); ?></td>
					</tr>
				<?
				if ($i == 1) {
					echo "</tbody>";
				}
				$i++;
			}
				?>
		</table>
	</div>
	<?
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$issue_basis_requisition_or_demand_variable = return_field_value("yarn_issue_basis", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=28");
	if ($issue_basis_requisition_or_demand_variable == 2) // Demand
	{
		$demandCond = "and a.demand_id=$update_id";
		$demandCond2 = "and mst_id=$update_id";

		$receiveBasisCond = "and b.receive_basis=8";
	} else {  // Requsition
		$demandCond = "";
		$demandCond2 = "";
		$receiveBasisCond = "and b.receive_basis=3";
	}

	if ($operation == 0) // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$demand_num = '';
		$demand_update_id = '';
		$flag = 1;

		if (str_replace("'", "", $update_id) == "") {
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = ""; //defined Later

			$new_demand_system_id = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'YDE', date("Y", time()), 5, "select demand_prefix, demand_prefix_number from ppl_yarn_demand_entry_mst where company_id=$cbo_company_id and $year_cond=" . date('Y', time()) . " order by id desc ", "demand_prefix", "demand_prefix_number"));

			$id = return_next_id("id", "ppl_yarn_demand_entry_mst", 1);

			$field_array = "id, demand_prefix, demand_prefix_number, demand_system_no, company_id, location_id,floor_name, demand_date, remarks,knitting_source,knitting_company, inserted_by, insert_date";

			$data_array = "(" . $id . ",'" . $new_demand_system_id[1] . "'," . $new_demand_system_id[2] . ",'" . $new_demand_system_id[0] . "'," . $cbo_company_id . "," . $cbo_location . "," . $cbo_floor_name . "," . $txt_demand_date . "," . $txt_remarks . "," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$demand_num = $new_demand_system_id[0];
			$demand_update_id = $id;
		} else {
			$field_array_update = "location_id*demand_date*remarks*knitting_source*knitting_company*updated_by*update_date";
			$data_array_update = $cbo_location . "*" . $txt_demand_date . "*" . $txt_remarks . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$demand_num = str_replace("'", "", $txt_demand_no);
			$demand_update_id = str_replace("'", "", $update_id);
		}

		$id_dtls = return_next_id("id", "ppl_yarn_demand_entry_dtls", 1);
		$field_array_dtls = "id, mst_id, requisition_no, demand_qnty, save_string, inserted_by, insert_date";
		$data_array_dtls = "(" . $id_dtls . "," . $demand_update_id . "," . $txt_requisition_no . "," . $txt_demand_qnty . "," . $save_data . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		$id_dtls_item = return_next_id("id", "ppl_yarn_demand_reqsn_dtls", 1);
		$field_array_dtls_item = "id, mst_id, dtls_id, requisition_no, requisition_id, prod_id, yarn_demand_qnty, cone_qty, ctn_qty,remarks,inserted_by, insert_date";
		$save_string = explode(",", str_replace("'", "", $save_data));
		for ($i = 0; $i < count($save_string); $i++) {
			$data = explode("_", $save_string[$i]);
			$productId = $data[0];
			$reqsnId = $data[1];
			$demandQnty = $data[2];
			$coneQnty = $data[3];
			$ctnQnty = $data[4];
			$remark = $data[5];
			$demandQnty = ($demandQnty == "") ? $demandQnty = 0 : $demandQnty;

			if ($i != 0) $data_array_dtls_item .= ",";
			$data_array_dtls_item .= "(" . $id_dtls_item . "," . $demand_update_id . "," . $id_dtls . "," . $txt_requisition_no . "," . $reqsnId . "," . $productId . "," . $demandQnty . "," . $coneQnty . "," . $ctnQnty . ",'" . $remark . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$id_dtls_item = $id_dtls_item + 1;
		}

		if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("ppl_yarn_demand_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1;
			else $flag = 0;
		} else {
			$rID = sql_update("ppl_yarn_demand_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
			if ($rID) $flag = 1;
			else $flag = 0;
		}
		$rID2 = sql_insert("ppl_yarn_demand_entry_dtls", $field_array_dtls, $data_array_dtls, 0);
		if ($flag == 1) {
			if ($rID2) $flag = 1;
			else $flag = 0;
		}
		//echo "insert into ppl_yarn_demand_reqsn_dtls (".$field_array_dtls_item.") Values ".$data_array_dtls_item."";die;
		$rID3 = sql_insert("ppl_yarn_demand_reqsn_dtls", $field_array_dtls_item, $data_array_dtls_item, 1);
		if ($flag == 1) {
			if ($rID3) $flag = 1;
			else $flag = 0;
		}

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $demand_num . "**" . $demand_update_id . "**0**1";
			} else {
				mysql_query("ROLLBACK");
				echo "5**" . $demand_numd . "**0**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $demand_num . "**" . $demand_update_id . "**0**1";
			} else {
				oci_rollback($con);
				echo "5**" . $demand_numd . "**0**0**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$is_sales = return_field_value("b.is_sales", "ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c", "c.company_id=$cbo_company_id and a.knit_id=b.id and b.mst_id=c.id and a.requisition_no=$txt_requisition_no group by b.is_sales", "is_sales");
		$is_sales = ($is_sales == 1) ? $is_sales : 0;

		$field_array_update = "location_id*floor_name*demand_date*remarks*knitting_source*knitting_company*updated_by*update_date";
		$data_array_update = $cbo_location . "*" . $cbo_floor_name . "*" . $txt_demand_date . "*" . $txt_remarks . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$rID = sql_update("ppl_yarn_demand_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		if ($rID) $flag = 1;
		else $flag = 0;

		$field_array_update_dtls = "requisition_no*demand_qnty*save_string*updated_by*update_date";
		$data_array_update_dtls = $txt_requisition_no . "*" . $txt_demand_qnty . "*" . $save_data . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$id_dtls_item = return_next_id("id", "ppl_yarn_demand_reqsn_dtls", 1);
		$field_array_dtls_item = "id, mst_id, dtls_id, requisition_no, requisition_id, prod_id, yarn_demand_qnty, cone_qty, ctn_qty,remarks, inserted_by, insert_date";
		$save_string = explode(",", str_replace("'", "", $save_data));
		for ($i = 0; $i < count($save_string); $i++) {
			$data = explode("_", $save_string[$i]);
			$productId = $data[0];
			$reqsnId = $data[1];
			$demandQnty = $data[2];
			$coneQnty = $data[3];
			$ctnQnty = $data[4];
			$remark = $data[5];

			$demandQnty = ($demandQnty == "") ? $demandQnty = 0 : $demandQnty;

			//for issue qty
			if ($db_type == 0) {
				$total_issue = sql_select("select group_concat(p.id) as issue_id,group_concat(p.issue_number) as issue_number,sum(p.issue_qnty) issue_qnty from(select b.issue_number_prefix_num issue_number ,b.id,(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.transaction_type=2  and a.requisition_no=$txt_requisition_no $demandCond and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId) p");
			} else {
				$total_issue = sql_select("select LISTAGG(cast(p.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.id) as issue_id,LISTAGG(cast(p.issue_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.issue_number) as issue_number,sum(p.issue_qnty) issue_qnty from(select b.issue_number_prefix_num issue_number ,b.id,(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.transaction_type=2 and a.requisition_no=$txt_requisition_no $demandCond and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId) p");
			}

			if ($is_sales == 1) {
				if ($issue_basis_requisition_or_demand_variable == 2) // Demand 
				{
					$return_requisition_cond = " and b.requisition_no=$txt_requisition_no and b.demand_id = $update_id";
				} else {
					$return_requisition_cond = " and b.requisition_no=$txt_requisition_no and b.demand_id = $txt_requisition_no";
				}
			}

			//for issue return qty
			$check_issue_return = sql_select("select LISTAGG(cast(m.recv_number_prefix_num as varchar2(4000)), ',') WITHIN GROUP (ORDER BY m.recv_number_prefix_num) as issue_return_number,sum(b.cons_quantity) issue_return_qnty from inv_receive_master m,inv_transaction b where m.id=b.mst_id and b.issue_id in(select a.mst_id from inv_transaction a where a.transaction_type=2 and a.requisition_no in($txt_requisition_no) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId $demandCond) and b.transaction_type=4 and b.prod_id=$productId and m.item_category=1 and m.status_active=1 and m.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $receiveBasisCond $return_requisition_cond");

			//for existing demand qty
			if ($issue_basis_requisition_or_demand_variable == 2) // Demand basis issue
			{
				//for existing demand qty
				$exist_dmnd_qty = return_field_value("sum(yarn_demand_qnty) as qty", "ppl_yarn_demand_reqsn_dtls", "requisition_no=$txt_requisition_no and prod_id=$productId and dtls_id<>$update_dtls_id and status_active=1 and is_deleted=0 $demandCond2 ", "qty");
			} else // Requisition basis issue
			{
				//for existing demand qty
				$exist_dmnd_qty = return_field_value("sum(yarn_demand_qnty) as qty", "ppl_yarn_demand_reqsn_dtls", "requisition_no=$txt_requisition_no and dtls_id<>$update_dtls_id and prod_id=$productId and status_active=1 and is_deleted=0", "qty");
			}

			$tot_demand_qnty = number_format($exist_dmnd_qty + $demandQnty, 2, '.', '');
			$issue_qnty = number_format($total_issue[0][csf('issue_qnty')], 2, '.', '');
			$issue_return_qty = number_format($check_issue_return[0][csf('issue_return_qnty')], 2, '.', '');
			$net_issue_qty = number_format(($issue_qnty - $issue_return_qty), 2, '.', '');
			$actual_demand_qnty = $tot_demand_qnty;
			$actual_issue_qty = $net_issue_qty;

			// Sum of all demand of a requisition of a Product ID cannot be less than sum of issue against requisition

			if ($actual_demand_qnty < $actual_issue_qty) {
				$issue_return_number = $check_issue_return[0][csf('issue_return_number')];
				if ($issue_return_number != "") {
					$retn_msg = "\nIssue Return No=" . $issue_return_number . "\nIssue Return Quantity=" . $issue_return_qty;
				}

				$lot_number = return_field_value("lot", "product_details_master a", "a.id=$productId ", "lot");
				$upto_reduce_qty = number_format(($net_issue_qty - $exist_dmnd_qty), 2, '.', '');
				echo "18**Demand quantity can not be less than cumulative issue quantity.\nIssue No=" . $total_issue[0][csf('issue_number')] . "\nIssue Quantity=" . $issue_qnty . $retn_msg . "\nUp to Reduce Quantity=" . $upto_reduce_qty . "\nLot No=$lot_number\nprod id = $productId";
				disconnect($con);
				exit();
			}

			if ($i != 0) $data_array_dtls_item .= ",";
			$data_array_dtls_item .= "(" . $id_dtls_item . "," . $update_id . "," . $update_dtls_id . "," . $txt_requisition_no . "," . $reqsnId . "," . $productId . "," . $demandQnty . "," . $coneQnty . "," . $ctnQnty . ",'" . $remark . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$id_dtls_item = $id_dtls_item + 1;
		}

		$rID = sql_update("ppl_yarn_demand_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		if ($rID) $flag = 1;
		else $flag = 0;

		$rID2 = sql_update("ppl_yarn_demand_entry_dtls", $field_array_update_dtls, $data_array_update_dtls, "id", $update_dtls_id, 0);
		if ($flag == 1) {
			if ($rID2) $flag = 1;
			else $flag = 0;
		}

		$delete_item_dtls = execute_query("delete from ppl_yarn_demand_reqsn_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_item_dtls) $flag = 1;
			else $flag = 0;
		}

		//echo "insert into ppl_yarn_demand_reqsn_dtls (".$field_array_dtls_item.") Values ".$data_array_dtls_item."";die;
		$rID3 = sql_insert("ppl_yarn_demand_reqsn_dtls", $field_array_dtls_item, $data_array_dtls_item, 1);
		if ($flag == 1) {
			if ($rID3) $flag = 1;
			else $flag = 0;
		}

		$demand_num = str_replace("'", "", $txt_demand_no);
		$demand_update_id = str_replace("'", "", $update_id);

		//echo "6**".$flag; oci_rollback($con);die();
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . $demand_num . "**" . $demand_update_id . "**0**1";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1**1**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . $demand_num . "**" . $demand_update_id . "**0**1";
			} else {
				oci_rollback($con);
				echo "6**0**1**1**1";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here
	{
		$save_string = explode(",", str_replace("'", "", $save_data));
		for ($i = 0; $i < count($save_string); $i++) {
			$data = explode("_", $save_string[$i]);
			$productId = $data[0];

			if ($db_type == 0) {
				$total_issue = sql_select("select group_concat(p.id) as issue_id,group_concat(p.issue_number) as issue_number,sum(p.issue_qnty) issue_qnty from(select b.issue_number_prefix_num issue_number ,b.id,(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.transaction_type=2  and a.requisition_no=$txt_requisition_no $demandCond and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId) p");
			} else {
				$total_issue = sql_select("select LISTAGG(cast(p.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.id) as issue_id,LISTAGG(cast(p.issue_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.issue_number) as issue_number,sum(p.issue_qnty) issue_qnty from(select b.issue_number_prefix_num issue_number ,b.id,(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.transaction_type=2 and a.requisition_no=$txt_requisition_no $demandCond and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId) p");
			}

			$check_issue_return = sql_select("select LISTAGG(cast(m.recv_number_prefix_num as varchar2(4000)), ',') WITHIN GROUP (ORDER BY m.recv_number_prefix_num) as issue_return_number,sum(b.cons_quantity) issue_return_qnty from inv_receive_master m,inv_transaction b where m.id=b.mst_id and b.issue_id in(select a.mst_id from inv_transaction a where a.transaction_type=2 and a.requisition_no in($txt_requisition_no) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$productId $demandCond) and b.transaction_type=4 and b.prod_id=$productId and m.item_category=1 and m.status_active=1 and m.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $receiveBasisCond");

			$issue_qnty = number_format($total_issue[0][csf('issue_qnty')], 2, '.', '');
			$issue_return_qty = number_format($check_issue_return[0][csf('issue_return_qnty')], 2, '.', '');
			$net_issue_qty = ($issue_qnty - $issue_return_qty);

			// IF (ISSUE-ISSUE RETURN) > 0 THEN DEMAND CAN NOT BE DELETED
			if ($net_issue_qty > 0) {
				$issue_return_number = $check_issue_return[0][csf('issue_return_number')];

				if ($issue_return_number != "") {
					$retn_msg = "\nIssue Return Found.\nIssue Return No=" . $issue_return_number . "\nIssue Return Quantity=" . $issue_return_qty;
				}
				echo "18**Can not delete demand,Issue found.\nIssue No=" . $total_issue[0][csf('issue_number')] . "\nIssue Quantity=" . $issue_qnty . $retn_msg;
				disconnect($con);
				exit();
			}
		}

		$sql = sql_select("select id from ppl_yarn_demand_entry_dtls where mst_id=" . $update_id . " and id !=" . $update_dtls_id . " and status_active=1 and is_deleted=0 ");

		//echo $sql;die;
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$rID = true;
		$form_reset_yesno = 0;
		if (empty($sql)) {
			$rID = sql_update("ppl_yarn_demand_entry_mst", $field_array_status, $data_array_status, "id", $update_id, 0);
			$form_reset_yesno = 1;
		}
		$rID2 = sql_update("ppl_yarn_demand_entry_dtls", $field_array_status, $data_array_status, "id", $update_dtls_id, 0);
		$rID3 = sql_update("ppl_yarn_demand_reqsn_dtls", $field_array_status, $data_array_status, "dtls_id", $update_dtls_id, 1);
		$demand_num = str_replace("'", "", $txt_demand_no);
		$demand_update_id = str_replace("'", "", $update_id);
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "2**" . $demand_num . "**" . $demand_update_id . "**0**0**" . $form_reset_yesno;
			} else {
				mysql_query("ROLLBACK");
				echo "7**" . $demand_num . "**" . $demand_update_id . "**1**1**" . $form_reset_yesno;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "2**" . $demand_num . "**" . $demand_update_id . "**0**0**" . $form_reset_yesno;
			} else {
				oci_rollback($con);
				echo "7**" . $demand_num . "**" . $demand_update_id . "**1**1**" . $form_reset_yesno;
			}
		}

		disconnect($con);
		die;
	}
}

if ($action == "systemId_popup") {
	echo load_html_head_contents("System ID Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id) {
			$('#hidden_sys_id').val(id);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:640px;">
			<form name="searchsystemidfrm" id="searchsystemidfrm">
				<fieldset style="width:630px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th width="250" id="search_by_td_up">Enter System Id</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								$search_by_arr = array(1 => "System ID", 2 => "Requisition No.");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value, 'create_demand_search_list_view', 'search_div', 'yarn_demand_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_demand_search_list_view") {
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');

	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];

	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.demand_prefix_number like '" . $search_string . "%'";
		else
			$search_field_cond = "and b.requisition_no like '" . $search_string . "%'";
	} else {
		$search_field_cond = "";
	}

	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	if ($db_type == 0) {
		$sql = "select a.id, a.demand_system_no, $year_field a.demand_prefix_number, a.company_id, a.location_id, a.demand_date, group_concat(distinct(b.requisition_no)) as reqsn_no from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.status_active = '1' and a.is_deleted = '0' and b.status_active = '1' and b.is_deleted = '0' $search_field_cond group by a.id, a.demand_system_no, a.demand_prefix_number, a.company_id,a.location_id,a.demand_date,a.insert_date order by a.id desc";
	} else {
		$sql = "select a.id, a.demand_system_no, $year_field a.demand_prefix_number, a.company_id, a.location_id, a.demand_date, LISTAGG(b.requisition_no, ',') WITHIN GROUP (ORDER BY b.requisition_no) as reqsn_no from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.status_active = '1' and a.is_deleted = '0' and b.status_active = '1' and b.is_deleted = '0' $search_field_cond group by a.id, a.demand_system_no, a.demand_prefix_number, a.company_id,a.location_id,a.demand_date,a.insert_date order by a.id desc";
	}

	$arr = array(2 => $company_arr, 3 => $location_arr);
	echo create_list_view("tbl_list_search", "Demand No,Year,Company,Location,Demand Date,Requisition No", "70,70,80,120,80", "610", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,company_id,location_id,0,0", $arr, "demand_prefix_number,year,company_id,location_id,demand_date,reqsn_no", "", '', '0,0,0,0,3,0');

	exit();
}

if ($action == 'populate_data_from_demand_update') {
	$data_array = sql_select("select id, demand_system_no, company_id, location_id,floor_name, demand_date, remarks,knitting_source,knitting_company from ppl_yarn_demand_entry_mst where id='$data'");
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_demand_no').value                = '" . $row[csf("demand_system_no")] . "';\n";
		echo "document.getElementById('cbo_company_id').value               = '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "load_drop_down('requires/yarn_demand_entry_controller', " . $row[csf("company_id")] . ", 'load_drop_down_location', 'location_td' );\n";
		if ($row[csf("location_id")] != "") {
			$location = $row[csf("location_id")];
		} else {
			$location = "''";
		}
		echo "load_drop_down('requires/yarn_demand_entry_controller', " . $location . ", 'load_drop_down_floor', 'td_floor_name' );\n";
		echo "document.getElementById('cbo_location').value                 = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_demand_date').value              = '" . change_date_format($row[csf("demand_date")]) . "';\n";
		echo "document.getElementById('txt_remarks').value                  = '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('cbo_knitting_source').value          = '" . $row[csf("knitting_source")] . "';\n";
		//echo "load_drop_down('requires/yarn_demand_entry_controller', " .$row[csf("knitting_source")]. "+'_'+" .$row[csf("knitting_company")] . ", 'load_drop_down_knitting_com', 'knitting_com' );\n";
		$knitting_source = ($row[csf("knitting_source")] != "") ? $row[csf("knitting_source")] : 0;
		$knitting_company = ($row[csf("knitting_company")] != "") ? $row[csf("knitting_company")] : 0;
		echo "load_drop_down( 'requires/yarn_demand_entry_controller', " . $knitting_source . "+'_'+" . $knitting_company . ", 'load_drop_down_knitting_com','knitting_com');\n";
		echo "document.getElementById('cbo_knitting_company').value         = '" . $row[csf("knitting_company")] . "';\n";
		echo "$('#cbo_knitting_company').attr('disabled','true')" . ";\n";
		echo "document.getElementById('update_id').value                    = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_floor_name').value               = '" . $row[csf("floor_name")] . "';\n";
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_demand_entry',1,1);\n";
		exit();
	}
}

if ($action == "show_demand_listview") {
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$data = explode("**", $data);
	$po_array = array();
	$costing_sql = sql_select("select a.job_no, a.buyer_name, b.id, b.grouping as internal_ref,b.file_no,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[1]");
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	foreach ($costing_sql as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('internal_ref')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
	}

	$sales_order_array = array();
	$sales_order_sql = sql_select("select a.id,a.sales_booking_no, a.job_no,a.style_ref_no, a.buyer_id,a.within_group from fabric_sales_order_mst a where a.status_active = 1 and a.is_deleted = 0 and a.company_id=$data[1]");
	foreach ($sales_order_sql as $row) {
		$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
		$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
	}

	if ($db_type == 0) {
		$plan_details = sql_select("select a.requisition_no,sum(a.yarn_qnty) as requisition_qty,group_concat(distinct(c.po_id)) as po_id,b.is_sales,b.id from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.knit_id=b.id and b.id=c.dtls_id group by a.requisition_no,b.is_sales,b.id");
	} else {
		$plan_details = sql_select("select a.requisition_no,sum(a.yarn_qnty) as requisition_qty , listagg(c.po_id, ',') within group (order by c.po_id) as po_id,b.is_sales,b.id from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.knit_id=b.id and b.id=c.dtls_id group by a.requisition_no,b.is_sales,b.id");
	}

	$plan_details_array = array();
	foreach ($plan_details as $row) {
		$plan_details_array[$row[csf("requisition_no")]]["po_id"] = $row[csf("po_id")];
		$plan_details_array[$row[csf("requisition_no")]]["is_sales"] = $row[csf("is_sales")];
		$plan_details_array[$row[csf("requisition_no")]]["requisition_qty"] = $row[csf("requisition_qty")];
	}
?>
	<div style="width:800px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="782" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$tot_demand_qnty = 0;
			$sql = "select id, requisition_no, demand_qnty from ppl_yarn_demand_entry_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0";
			$nameArray = sql_select($sql);
			$buyer_id == '';
			foreach ($nameArray as $selectResult) {

				$requisitionNo = $selectResult[csf("requisition_no")];
				$is_sales = $plan_details_array[$requisitionNo]["is_sales"];
				$requisition_qty = $plan_details_array[$requisitionNo]["requisition_qty"];

				if ($i == 1) {
			?>
					<thead>
						<th width="40">SL</th>
						<th width="100">Requisition No</th>
						<th width="150">Buyer/Unit</th>
						<?php if ($is_sales == 0) { ?>
							<th width="75">File No</th>
							<th width="75">Internal Ref.</th>
							<th width="130">PO No</th>
							<th width="110"><? echo $company_arr[$data[1]]; ?></th>
						<?php } else { ?>
							<th width="130">Sales Order No</th>
						<?php } ?>
						<th width="80"> Requisition Qty </th>
						<th>Demand Qnty</th>
					</thead>
				<?
				}
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if ($is_sales == 1) {
					$po_ids_arr = explode(",", $plan_details_array[$selectResult[csf("requisition_no")]]["po_id"]);
					$po_no = $sales_order_array[$po_ids_arr[0]]['sales_order_no'];
					$style_ref = $sales_order_array[$po_ids_arr[0]]['style_ref_no'];
					if ($sales_order_array[$po_ids_arr[0]]['within_group'] == 1) {
						$buyer_id = $company_library[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
					} else {
						$buyer_id = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
					}
				} else {
					$po_id = array_unique(explode(",", $plan_details_array[$selectResult[csf("requisition_no")]]["po_id"]));
					$po_no = '';
					$style_ref = '';
					$internal_ref = '';
					$file_no = '';
					foreach ($po_id as $val) {
						if ($po_no == '') $po_no = $po_array[$val]['no'];
						else $po_no .= "," . $po_array[$val]['no'];
						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
						if ($internal_ref == '') $internal_ref = $po_array[$val]['ref'];
						else $internal_ref .= "," . $po_array[$val]['ref'];
						if ($file_no == '') $file_no = $po_array[$val]['file'];
						else $file_no .= "," . $po_array[$val]['file'];
						$buyer_id = $buyer_arr[$po_array[$val]['buyer']];
					}
					//echo $po_array[$val]['buyer']."==";
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>, 'populate_data_from_demand_dtls', 'requires/yarn_demand_entry_controller' );">
					<td width="40" align="center"><? echo $i; //echo $i; 
													?></td>
					<td width="100">
						<p><? echo $selectResult[csf('requisition_no')]; ?></p>
					</td>
					<td width="150">
						<p><? echo $buyer_id; ?></p>
					</td>
					<?php if ($is_sales == 0) { ?>
						<td width="75">
							<p><? echo implode(",", array_unique(explode(",", $file_no))); ?></p>
						</td>
						<td width="75">
							<p><? echo implode(",", array_unique(explode(",", $internal_ref))) ?></p>
						</td>
						<td width="130">
							<p><? echo $po_no; ?></p>
						</td>
						<td width="110">
							<p>&nbsp;</p>
						</td>
					<?php } else { ?>
						<td width="130">
							<p><? echo $po_no; ?></p>
						</td><?php } ?>
					<td width="80" align="right"><? echo number_format($requisition_qty, 2); ?></td>
					<td align="right"><? echo number_format($selectResult[csf('demand_qnty')], 2); ?></td>
				</tr>
			<?
				$tot_demand_qnty += $selectResult[csf('demand_qnty')];
				$tot_requisition_qty += $requisition_qty;
				$i++;
			}
			?>
			<tfoot>
				<th colspan="<?php echo ($is_sales == 1) ? 4 : 7 ?>">Total</th>
				<th><? echo number_format($tot_requisition_qty, 2); ?></th>
				<th><? echo number_format($tot_demand_qnty, 2); ?></th>
			</tfoot>
		</table>
	</div>

<?
	exit();
}

if ($action == 'populate_data_from_demand_dtls') {
	$data_array = sql_select("select id, requisition_no, demand_qnty, save_string from ppl_yarn_demand_entry_dtls where id='$data'");
	foreach ($data_array as $row) {
		$requisitionNo = $row[csf("requisition_no")];
		$demandArr[$requisitionNo]['demand_qnty'] = $row[csf("demand_qnty")];

		echo "document.getElementById('txt_requisition_no').value           = '" . $row[csf("requisition_no")] . "';\n";
		echo "document.getElementById('txt_demand_qnty').value              = '" . number_format($row[csf("demand_qnty")], 2) . "';\n";

		echo "document.getElementById('hdn_previous_demand_qnty').value     = '" . number_format($row[csf("demand_qnty")], 2) . "';\n";

		echo "document.getElementById('save_data').value                    = '" . $row[csf("save_string")] . "';\n";
		echo "document.getElementById('update_dtls_id').value               = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('operation_type').value               = 1;\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_demand_entry',1);\n";
	}

	/*$sql = "select requisition_no, sum(yarn_qnty) as total_yarn_qnty from ppl_yarn_requisition_entry where requisition_no=$requisitionNo and status_active=1 and is_deleted=0 group by requisition_no";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$balanceQty = ($row[csf("total_yarn_qnty")]-$demandArr[$row[csf("requisition_no")]]['demand_qnty']);

		echo "document.getElementById('txt_yarn_blance_qnty').value              = '" . number_format($balanceQty, 2) . "';\n";
	}*/


	$total_yarn_qnty = return_field_value("sum(yarn_qnty)", "ppl_yarn_requisition_entry", "requisition_no='" . $requisitionNo . "' and status_active=1 and is_deleted=0");

	echo "document.getElementById('requisitionqty').value           = '" . $total_yarn_qnty . "';\n";

	$prod_tot_demand_qnty = return_field_value("sum(demand_qnty)", "ppl_yarn_demand_entry_dtls", "requisition_no='" . $requisitionNo . "' and status_active=1 and is_deleted=0");

	$balanceQty = ($total_yarn_qnty - $prod_tot_demand_qnty);
	echo "document.getElementById('txt_yarn_blance_qnty').value              = '" . number_format($balanceQty, 2) . "';\n";

	exit();
}

if ($action == "print") {
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	echo load_html_head_contents("Demand Print", "../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[1]'", "image_location");

	$po_array = array();
	$costing_sql = sql_select("select a.job_no, a.buyer_name, b.id,b.grouping,b.file_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[1]");
	foreach ($costing_sql as $row) {
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];

		$job_intfarray[$row[csf('job_no')]][] = $row[csf('grouping')];
	}

	$sales_order_array = array();
	$sales_order_sql = sql_select("select a.id,a.sales_booking_no, a.job_no,a.style_ref_no, a.buyer_id,b.job_no po_job_no,b.buyer_id po_buyer,a.within_group, a.customer_buyer from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.status_active = 1 and a.is_deleted = 0 and a.company_id=$data[1]");
	foreach ($sales_order_sql as $row) {
		$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
		$sales_order_array[$row[csf('id')]]['po_job_no'] = $row[csf('po_job_no')];
		$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_order_array[$row[csf('id')]]['po_buyer'] = $row[csf('customer_buyer')];
		$sales_order_array[$row[csf('id')]]['id'] = $row[csf('id')];
	}

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	//lib_prod_floor
?>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180" align="right">
					<img src='../<? echo $image_location; ?>' height='100%' width='100%' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:x-large"><strong><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id='$data[1]' and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result) {
								?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Level No: <? echo $result[csf('level_no')] ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City Name: <? echo $result[csf('city')]; ?>
									Zip Code: <? echo $result[csf('zip_code')]; ?>
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}
												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><b><u>Daily Yarn Demand</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
		$dataArray = sql_select("SELECT a.booking_no,a.body_part_id,d.location_id, d.demand_date, d.demand_system_no, d.floor_name, d.remarks, d.insert_date, d.update_date from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c, ppl_yarn_demand_entry_mst d, ppl_yarn_demand_entry_dtls e where a.id=b.mst_id and b.id=c.knit_id  and d.id=e.mst_id and e.requisition_no=c.requisition_no and d.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		?>
		<table width="1270" style="margin-top:10px">
			<tr>
				<td width="70"><b>Demand No</b></td>
				<td width="250"><b>:</b> <? echo $dataArray[0][csf('demand_system_no')]; ?></td>
				<td width="70"><b>Demand Date</b></td>
				<td width="250"><b>:</b> <? echo change_date_format($dataArray[0][csf('demand_date')]); ?></td>
				<td width="70"><b>Location</b></td>
				<td width="250"><b>:</b> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td width="70"><b>Floor</b></td>
				<td width="250"><b>:</b> <? echo $floor_arr[$dataArray[0][csf('floor_name')]]; ?></td>
			</tr>
			<tr>
				<td><b>Remarks</b></td>
				<td><b>:</b> <? echo $dataArray[0][csf('remarks')]; ?></td>
				<td><b>Body Part</b></td>
				<td><b>:</b> <? echo $body_part[$dataArray[0][csf('body_part_id')]]; ?></td>
				<td><b>Insert Time</b></td>
				<td><b>:</b>
					<?
					if ($dataArray[0][csf('update_date')] != '') {
						echo date("h:i A", strtotime($dataArray[0][csf('update_date')]));
					} else {
						echo date("h:i A", strtotime($dataArray[0][csf('insert_date')]));
					}

					?>
				</td>
			</tr>
		</table>
		<table style="margin-top:10px;" width="1400" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<?
			$kintting_arr = array();
			$kintting_source_data = sql_select("select a.requisition_no,a.knit_id,b.knitting_source,b.knitting_party,b.is_sales from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach ($kintting_source_data as $row) {
				$kintting_arr[$row[csf('requisition_no')]]['source'] = $row[csf('knitting_source')];
				$kintting_arr[$row[csf('requisition_no')]]['party'] = $row[csf('knitting_party')];
				$kintting_arr[$row[csf('requisition_no')]]['prog_no'] = $row[csf('knit_id')];
				$kintting_arr[$row[csf('requisition_no')]]['is_sales'] = $row[csf('is_sales')];
			}

			if ($db_type == 0) {
				$program_po_arr = sql_select("select dtls_id, group_concat(po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls where group by dtls_id");
			} else {
				$program_po_arr = sql_select("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls group by dtls_id,booking_no"); //status_active=1 and is_deleted=0
			}

			$program_po_array = array();
			foreach ($program_po_arr as $row) {
				$program_po_array[$row[csf("dtls_id")]]["po_id"] = $row[csf("po_id")];
				$program_po_array[$row[csf("dtls_id")]]["booking_no"] = $row[csf("booking_no")];
			}

			$i = 1;
			$sql = "select a.requisition_no, b.prod_id, sum(b.yarn_demand_qnty) as yarn_demand_qnty, sum(b.cone_qty) as cone_qty, sum(b.ctn_qty) as ctn_qty, b.remarks from ppl_yarn_demand_entry_dtls a, ppl_yarn_demand_reqsn_dtls b where a.id=b.dtls_id and a.mst_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.requisition_no, b.prod_id, b.remarks order by a.requisition_no";
			$nameArray = sql_select($sql);
			$prodIdArr = array();
			foreach ($nameArray as $row) {
				$prodIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
			}

			//for product information
			$product_desc_array = array();
			$product_details_array = array();
			$sql_prod = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(',', $prodIdArr) . ")";
			$result_prod = sql_select($sql_prod);
			foreach ($result_prod as $row) {
				$compos = '';
				$desc = '';
				if ($row[csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
				}

				$desc = $row[csf('lot')] . " " . $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_library[$row[csf('color')]];
				$product_desc_array[$row[csf('id')]] = $desc;
				$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
				$product_details_array[$row[csf('id')]]['comp'] = $compos;
				$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
				$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
				$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
				$product_details_array[$row[csf('id')]]['suppl'] = $supllier_arr[$row[csf('supplier_id')]];
				$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
			}
			//for product information end

			$requisitionNoArr = array();
			foreach ($nameArray as $selectResult) {
				$is_sales = $kintting_arr[$selectResult[csf('requisition_no')]]['is_sales'];
				if ($i == 1) {
			?>
					<thead>
						<th width="30">SL</th>
						<th width="100">Buyer/Unit</th>
						<?php if ($is_sales == 0) { ?>
							<th width="75">File No</th>
							<th width="75">Internal Ref</th>
							<th width="100">Order No</th>
							<th width="100">Booking No</th>
							<th width="100">Job No</th>
						<?php } else {
						?>
							<th width="100">Sales Order No</th>
							<th width="100">Booking No</th>
							<th width="100">Job No</th>
							<th width="75">Internal Ref</th>
							<th width="100">PO Buyer</th>
						<? } ?>
						<th width="80">Knitting Com.<? //echo $company_arr[$data[1]];
													?></th>
						<th width="80">Program No</th>
						<th width="50">Reqsn. No</th>
						<th width="50">Count</th>
						<th width="50">Brand</th>
						<th width="50">Lot</th>
						<th width="100">Supplier</th>
						<th width="110">Composition</th>
						<th width="70">Type</th>
						<th width="70">Color</th>
						<th width="80">Demand Qnty</th>
						<th width="60">No of Cone</th>
						<th width="70">No of Ctn</th>
						<th width="">Remark</th>
					</thead>
				<?
				}
				$kintting_source = $kintting_arr[$selectResult[csf('requisition_no')]]['source'];
				$kintting_com = $kintting_arr[$selectResult[csf('requisition_no')]]['party'];
				$program_no = implode(",", array_unique(explode(",", $kintting_arr[$selectResult[csf('requisition_no')]]['prog_no'])));
				$job_no = "";
				$job_no_arr = array();

				if ($is_sales == 1) {
					$po_ids_arr = explode(",", $program_po_array[$program_no]["po_id"]);
					$within_group = $sales_order_array[$po_ids_arr[0]]['within_group'];
					$po_no = $sales_order_array[$po_ids_arr[0]]['sales_order_no'];
					$po_id = $sales_order_array[$po_ids_arr[0]]['id'];
					//$job_no = $sales_order_array[$po_ids_arr[0]]['po_job_no'];
					$style_ref = $sales_order_array[$po_ids_arr[0]]['style_ref_no'];

					$job_no_arr[$sales_order_array[$po_ids_arr[0]]['po_job_no']] = $sales_order_array[$po_ids_arr[0]]['po_job_no'];

					if ($within_group == 1) {
						$po_job = $sales_order_array[$po_ids_arr[0]]['po_job_no'];
						$internal_ref_arr = $job_intfarray[$po_job];
						$internal_ref = implode(', ', $internal_ref_arr);
						$buyer_id = $company_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
					} else {
						$internal_ref = "";
						$buyer_id = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
					}

					$po_buyer = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['po_buyer']];
				} else {
					$po_id = $program_po_array[$program_no]["po_id"];
					$booking_no = $program_po_array[$program_no]["booking_no"];
					$all_po_id = array_unique(explode(",", $po_id));
					$po_no = '';
					$buyer_id = '';
					$file_no = '';
					$internal_ref = '';
					$all_fso = '';
					foreach ($all_po_id as $val) {
						//if ($job_no == '') $job_no = $po_array[$val]['job_no']; else $job_no .= "," . $po_array[$val]['job_no'];
						if ($po_no == '') $po_no = $po_array[$val]['no'];
						else $po_no .= "," . $po_array[$val]['no'];
						if ($buyer_id == '') $buyer_id = $buyer_arr[$po_array[$val]['buyer']];
						if ($internal_ref == '') $internal_ref = $po_array[$val]['ref'];
						else $internal_ref .= "," . $po_array[$val]['ref'];
						if ($file_no == '') $file_no = $po_array[$val]['file'];
						else $file_no .= "," . $po_array[$val]['file'];

						$job_no_arr[$po_array[$val]['job_no']] = $po_array[$val]['job_no'];
					}
				}

				$job_no = implode(', ', $job_no_arr);
				?>
				<tr>
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="100" align="center">
						<p><? echo $buyer_id; ?></p>
					</td>
					<?php if ($is_sales == 0) {
					?>
						<td width="75">
							<p><? echo implode(",", array_unique(explode(",", $file_no))); ?></p>
						</td>
						<td width="75">
							<p><? echo implode(",", array_unique(explode(",", $internal_ref))); ?></p>
						</td>
						<td width="100" align="center" style="word-break:break-all">
							<p><? echo $po_no; ?></p>
						</td>
						<td width="100" align="center"><?php echo $booking_no; ?></td>
						<td width="100" align="center">
							<p><?php echo $job_no; ?></p>
						</td>
					<?php
					} else {
					?>
						<td width="100" align="center">
							<p><? 
							$all_fso .= $po_id.",";
							echo $po_no;  ?></p>
						</td>
						<td width="100" align="center"><?php echo $sales_order_array[$po_ids_arr[0]]['sales_booking_no']; ?></td>
						<td width="100" align="center"><?php echo $job_no; ?></td>
						<td width="75">
							<p><? echo implode(",", array_unique(explode(",", $internal_ref))); ?></p>
						</td>
						<td width="100" align="center"><?php echo $po_buyer; ?></td>
					<?
					}
					?>
					<td width="80">
						<p>
							<?
							if ($kintting_source == 1) echo $company_library[$kintting_com];
							else if ($kintting_source == 3) echo $supplier_arr[$kintting_com];
							else echo "&nbsp;";
							?>
						</p>
					</td>
					<td width="80" align="center"><? echo $program_no; ?></td>
					<td width="60" align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
					<td width="50" align="center"><? echo $product_details_array[$selectResult[csf('prod_id')]]['count']; ?></td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p>
					</td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['suppl']; ?></p>
					</td>
					<td width="110" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['comp']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
					</td>
					<td width="80" align="right"><? echo number_format($selectResult[csf('yarn_demand_qnty')], 2); ?></td>
					<td width="60" align="right"><? echo number_format($selectResult[csf('cone_qty')], 2); ?></td>
					<td width="70" align="right"><? echo number_format($selectResult[csf('ctn_qty')], 2); ?></td>
					<td align="right">
						<p><? echo $selectResult[csf('remarks')]; ?></p>
					</td>
				</tr>
			<?
				$requisitionNoArr[$selectResult[csf('requisition_no')]] = $selectResult[csf('requisition_no')];
				$tot_demand_qnty += $selectResult[csf('yarn_demand_qnty')];
				$tot_cone_qnty += $selectResult[csf('cone_qty')];
				$tot_ctn_qnty += $selectResult[csf('ctn_qty')];

				$i++;
			}
			?>
			<tfoot>
				<th colspan="<?php echo ($is_sales == 0) ? 17 : 17; // 16:15;
								?>" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_demand_qnty, 2); ?></th>
				<th align="right"><? echo number_format($tot_cone_qnty, 2); ?></th>
				<th align="right"><? echo number_format($tot_ctn_qnty, 2); ?></th>
				<th></th>
			</tfoot>
		</table>
		<table style="margin-top:10px;" width="600" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<th width="100">Requisition No</th>
				<th width="100">Lot</th>
				<th width="100">Requisition Qty</th>
				<th width="100">Pre Demand Qty</th>
				<th width="100">Current Demand Qty</th>
				<th width="100">Balance Qty</th>
			</thead>
			<tbody>
				<?php
				$sql = "
	            SELECT
	                a.demand_date AS DEMAND_DATE,
	                b.requisition_no AS REQUISITION_NO,
	                c.yarn_demand_qnty AS YARN_DEMAND_QNTY,
	                d.lot AS LOT,
	                e.yarn_qnty AS YARN_QNTY
	            FROM ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_reqsn_dtls c, product_details_master d, ppl_yarn_requisition_entry e
	            WHERE
	                a.id = b.mst_id
	                AND b.id = c.dtls_id
	                AND c.prod_id = d.id
	                AND b.requisition_no = e.requisition_no
	                AND d.id = e.prod_id
	                AND a.status_active = 1
	                AND a.is_deleted = 0
	                AND b.status_active = 1
	                AND b.is_deleted = 0
	                AND c.status_active = 1
	                AND c.is_deleted = 0
	                AND d.status_active = 1
	                AND d.is_deleted = 0
	                AND e.status_active = 1
	                AND e.is_deleted = 0
	                --AND b.mst_id = " . $data[0] . "
	                AND a.company_id = " . $data[1] . "
	                AND b.requisition_no IN(" . implode(',', $requisitionNoArr) . ")
	            ORDER BY b.requisition_no
	        ";
				//echo $sql;
				$sqlRslt = sql_select($sql);
				$dataArr = array();
				foreach ($sqlRslt as $row) {
					$demandDate = date('d-m-Y', strtotime($row['DEMAND_DATE']));
					if (strtotime($data[2]) == strtotime($demandDate)) {
						$dataArr[$row['REQUISITION_NO']][$row['LOT']]['CURRENT_DEMAND_QNTY'] += $row['YARN_DEMAND_QNTY'];
					} else {
						$dataArr[$row['REQUISITION_NO']][$row['LOT']]['PREVIOUS_DEMAND_QNTY'] += $row['YARN_DEMAND_QNTY'];
					}
					$dataArr[$row['REQUISITION_NO']][$row['LOT']]['YARN_QNTY'] += $row['YARN_QNTY'];
				}

				foreach ($dataArr as $reqNo => $reqNoArr) {
					foreach ($reqNoArr as $lot => $row) {
						$balanceQnty = $row['YARN_QNTY'] - ($row['PREVIOUS_DEMAND_QNTY'] + $row['CURRENT_DEMAND_QNTY']);
				?>
						<tr>
							<td align="center"><?php echo $reqNo; ?></td>
							<td align="center"><?php echo $lot; ?></td>
							<td align="right"><?php echo number_format($row['YARN_QNTY'], 2); ?></td>
							<td align="right"><?php echo number_format($row['PREVIOUS_DEMAND_QNTY'], 2); ?></td>
							<td align="right"><?php echo number_format($row['CURRENT_DEMAND_QNTY'], 2); ?></td>
							<td align="right"><?php echo number_format($balanceQnty, 2); ?></td>
						</tr>
				<?php
						$totalRequisitionQty += number_format($row['YARN_QNTY'], 2, '.', '');
						$totalPreviousDemandQty += number_format($row['PREVIOUS_DEMAND_QNTY'], 2, '.', '');
						$totalCurrentDemandQty += number_format($row['CURRENT_DEMAND_QNTY'], 2, '.', '');
						$totalBalanceQty += number_format($balanceQnty, 2, '.', '');
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2">Total</th>
					<th><?php echo number_format($totalRequisitionQty, 2); ?></th>
					<th><?php echo number_format($totalPreviousDemandQty, 2); ?></th>
					<th><?php echo number_format($totalCurrentDemandQty, 2); ?></th>
					<th><?php echo number_format($totalBalanceQty, 2); ?></th>
				</tr>
			</tfoot>
		</table>
		<? 
			$all_fso_unique = array_unique(explode(",", trim($all_fso, ","))); 		
			$all_fso_unique_str = implode(",", $all_fso_unique);

			$fso = sql_select("select b.id, a.sales_booking_no, a.JOB_NO, b.GREY_QTY as fso_qty from FABRIC_SALES_ORDER_MST a, FABRIC_SALES_ORDER_DTLS b where a.id = b.mst_id and a.id in ($all_fso_unique_str) and a.status_active = 1 and b.status_active = 1");

			$data_array = array();
			foreach($fso as $row)
			{
				$data_array[$row['JOB_NO']]['FSO'] =  $row['JOB_NO'];
				$data_array[$row['JOB_NO']]['BOOKING_NO'] =  $row['SALES_BOOKING_NO'];
				$data_array[$row['JOB_NO']]['FSO_QNTY'] +=  $row['FSO_QTY'];
			}

			$allocation_sql = sql_select("select a.job_no, a.QNTY as allocated_qty from inv_material_allocation_dtls a where a.po_break_down_id in ($all_fso_unique_str)");
			foreach($allocation_sql as $row)
			{
				$data_array[$row['JOB_NO']]['ALLOCATED_QTY'] += $row['ALLOCATED_QTY'];
			}

			$requisition_sql = sql_select("SELECT a.JOB_NO, c.YARN_QNTY AS req_qnty, c.REQUISITION_NO FROM FABRIC_SALES_ORDER_MST  a, PPL_PLANNING_ENTRY_PLAN_DTLS b, PPL_YARN_REQUISITION_ENTRY c WHERE a.id = b.po_id AND b.dtls_id = c.knit_id  AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 and a.ID in ($all_fso_unique_str)");
			// echo "SELECT a.JOB_NO, c.YARN_QNTY AS req_qnty, d.DEMAND_QNTY, c.REQUISITION_NO, d.mst_id as demand_id FROM FABRIC_SALES_ORDER_MST  a, PPL_PLANNING_ENTRY_PLAN_DTLS b, PPL_YARN_REQUISITION_ENTRY c, PPL_YARN_DEMAND_ENTRY_DTLS d WHERE a.id = b.po_id AND b.dtls_id = c.knit_id AND c.REQUISITION_NO = d.REQUISITION_NO AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 and a.ID in ($all_fso_unique_str)";
			
			$current_demand = array();
			$requisition_no = '';
			foreach($requisition_sql as $row)
			{
				$data_array[$row['JOB_NO']]['REQUISITION_QNTY'] += $row['REQ_QNTY'];
				$data_array[$row['JOB_NO']]['REQUISITION_NOS'] .= $row['REQUISITION_NO'].',';
				$data_array[$row['JOB_NO']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
				$requisition_no  .= $data_array[$row['JOB_NO']]['REQUISITION_NO'].',';
			}

			$requisition_no_arr = explode(",", trim($requisition_no, ','));
			$requisiton_nos = implode(",", array_unique($requisition_no_arr));

			$demand_sql = sql_select("SELECT distinct a.JOB_NO, d.DEMAND_QNTY, c.REQUISITION_NO, d.mst_id as demand_id FROM FABRIC_SALES_ORDER_MST  a, PPL_PLANNING_ENTRY_PLAN_DTLS b, PPL_YARN_REQUISITION_ENTRY c, PPL_YARN_DEMAND_ENTRY_DTLS d WHERE a.id = b.po_id AND b.dtls_id = c.knit_id AND c.REQUISITION_NO = d.REQUISITION_NO AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 and c.REQUISITION_NO in ($requisiton_nos)");

			foreach($demand_sql as $row)
			{
				if($row['DEMAND_ID'] == $data[0])
				{
					$data_array[$row['JOB_NO']]["CURRENT_DEMAND"] += $row['DEMAND_QNTY'];
				}
				$data_array[$row['JOB_NO']]["TOTAL_DEMAND"] += $row['DEMAND_QNTY'];
			}
			
			// echo $requisiton_nos;
			// echo "<pre>";
			// print_r($requisition_no_arr);
			
		?>
		<table style="margin-top:10px; font-size:x-large;" width="1200" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<tr>
					<th style="font-size: 17px;" align="center" width="30">FSO Number</th>
					<th style="font-size: 17px;" align="center" width="60">FSO Required Qty</th>
					<th style="font-size: 17px;" align="center" width="60">Allocation Qty</th>
					<th style="font-size: 17px;" align="center" width="60">Requisition Qty</th>
					<th style="font-size: 17px;" align="center" width="60">Prev. Demand Qty</th>
					<th style="font-size: 17px;" align="center" width="60">Current Demand Qty</th>
					<th style="font-size: 17px;" align="center" width="60">Total Demand</th>
					<th style="font-size: 17px;" align="center" width="60">Balance Qty</th>
				</tr>					
			</thead>
			<tbody>
				<? 
					foreach ($data_array as $row) { 
						$previous_demand = $row['TOTAL_DEMAND'] - $row['CURRENT_DEMAND'];
						$balance_qty = $row['REQUISITION_QNTY'] - $row['TOTAL_DEMAND'];
						$req_nos = implode(",", array_unique(explode(",",trim($row['REQUISITION_NOS'], ","))));
				?>
				<tr>
					<td style="font-size: 17px;" align="center" ><?= $row['FSO']; ?></td>
					<td style="font-size: 17px;" align="center" ><?= number_format($row['FSO_QNTY'],2) ;?></td>
					<td style="font-size: 17px;" align="center" ><?= number_format($row['ALLOCATED_QTY'],2) ;?></td>
					<td style="font-size: 17px;" align="center" title="<?=$req_nos;?>" ><?= number_format($row['REQUISITION_QNTY'],2) ; ?></td>
					<td style="font-size: 17px;" align="center" ><?= number_format($previous_demand,2) ; ?></td>
					<td style="font-size: 17px;" align="center" ><?= number_format($row['CURRENT_DEMAND'],2) ; ?></td>
					<td style="font-size: 17px;" align="center" ><?= number_format($row['TOTAL_DEMAND'],2) ; ?></td>
					<td style="font-size: 17px;" align="center"><?= number_format($balance_qty,2); ?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<?
		echo signature_table(40, $data[1], "1180px");
		?>
	</div>
<?
	exit();
}

if ($action == "print2") {
	echo load_html_head_contents("Demand Print", "../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[1]'", "image_location");
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$booking_type_arr = array("1" => "Main", "2" => "Partial", "3" => "Short", "4" => "Sample With Order", "5" => "Sample Without Order");
	$entry_booking_type_arr = array("118" => "Main", "108" => "Partial", "88" => "Short", "89" => "Sample With Order", "90" => "Sample Without Order");

	$sql = "select a.requisition_no, b.prod_id, sum(b.yarn_demand_qnty) as yarn_demand_qnty, sum(b.cone_qty) as cone_qty, sum(b.ctn_qty) as ctn_qty,b.remarks
	from ppl_yarn_demand_entry_dtls a, ppl_yarn_demand_reqsn_dtls b
	where a.id=b.dtls_id and a.mst_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.requisition_no, b.prod_id,b.remarks order by a.requisition_no";
	$nameArray = sql_select($sql);
	$prodIdArr = array();
	foreach ($nameArray as $selectResult) {
		$requisition_no_arr[$selectResult[csf('requisition_no')]] = $selectResult[csf('requisition_no')];

		$prodIdArr[$selectResult[csf('prod_id')]] = $selectResult[csf('prod_id')];
	}

	//for product information
	$product_desc_array = array();
	$product_details_array = array();
	$sql_prod = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(',', $prodIdArr) . ")";
	$result_prod = sql_select($sql_prod);
	foreach ($result_prod as $row) {
		$compos = '';
		$desc = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$desc = $row[csf('lot')] . " " . $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_library[$row[csf('color')]];
		$product_desc_array[$row[csf('id')]] = $desc;
		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
		$product_details_array[$row[csf('id')]]['suppl'] = $supplier_arr[$row[csf('supplier_id')]];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
	}
	//for product information end

	$po_array = array();
	$costing_sql = sql_select("select a.job_no, a.buyer_name,a.style_ref_no, b.id,b.grouping,b.file_no, b.po_number,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=C.PO_BREAK_DOWN_ID group by a.job_no, a.buyer_name,a.style_ref_no, b.id,b.grouping,b.file_no, b.po_number,c.booking_no
		union all
		select cast( '' as nvarchar2(2000)) as job_no,buyer_id,cast( '' as nvarchar2(2000)) as style_ref_no,0 as id,cast( '' as nvarchar2(2000)) as grouping,0 as file_no,cast( '' as nvarchar2(2000)) as po_number,booking_no
		from wo_non_ord_samp_booking_mst where status_active=1");
	foreach ($costing_sql as $row) {
		$po_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$po_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$po_array[$row[csf('booking_no')]]['buyer'] = $row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
	}

	$sales_order_array = array();
	$sales_order_sql = sql_select("SELECT a.id,a.sales_booking_no, a.job_no,a.style_ref_no, a.buyer_id,a.within_group, a.booking_type, a.entry_form, a.booking_entry_form, a.booking_without_order ,
		b.short_booking_type
		FROM fabric_sales_order_mst a
		LEFT JOIN wo_booking_mst b ON a.booking_id = b.id
		WHERE a.company_id=$data[1]");

	foreach ($sales_order_sql as $row) {
		$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
		$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$sales_order_array[$row[csf('id')]]['booking_type'] = $row[csf('booking_type')];
		$sales_order_array[$row[csf('id')]]['booking_entry_form'] = $row[csf('booking_entry_form')];
		$sales_order_array[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];
		$sales_order_array[$row[csf('id')]]['entry_form_no'] = $row[csf('entry_form')];
		$sales_order_array[$row[csf('id')]]['short_booking_type'] = $row[csf('short_booking_type')];
	}
?>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180" align="right">
					<img src='../<? echo $image_location; ?>' height='100%' width='100%' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:x-large"><strong><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td align="center" style="font-size:14px">
								<?
								$nameArray_com = sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id='$data[1]' and status_active=1 and is_deleted=0");
								foreach ($nameArray_com as $result) {
								?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Level No: <? echo $result[csf('level_no')] ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City Name: <? echo $result[csf('city')]; ?>
									Zip Code: <? echo $result[csf('zip_code')]; ?>
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}
												?>
							</td>
						</tr>

						<tr>
							<td align="center" style="font-size:18px"><b><u>Daily Yarn Demand</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		$dataArray = sql_select("select a.booking_no,a.body_part_id,d.location_id, d.demand_date, d.demand_system_no, d.floor_name, d.remarks from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c ,ppl_yarn_demand_entry_mst d, ppl_yarn_demand_entry_dtls e where a.id=b.mst_id and b.id=c.knit_id  and d.id=e.mst_id and e.requisition_no=c.requisition_no and d.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		?>
		<table width="1270" style="margin-top:10px">
			<tr>
				<td><b>Demand No:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $dataArray[0][csf('demand_system_no')]; ?></td>
				<td><b>Demand
						Date:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo change_date_format($dataArray[0][csf('demand_date')]); ?>
				</td>
				<td><b>Location:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?>
				</td>
				<td><b>Floor:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $floor_arr[$dataArray[0][csf('floor_name')]]; ?></td>
			</tr>
			<tr>
				<td><b>Remarks:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td><b>Body Part:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $body_part[$dataArray[0][csf('body_part_id')]]; ?></td>
			</tr>
		</table>
		<table style="margin-top:10px;" width="1400" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<?
			$kintting_arr = array();
			if (!empty($requisition_no_arr)) {
				$kintting_source_data = sql_select("select a.requisition_no,a.knit_id,b.knitting_source,b.knitting_party,b.is_sales from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.requisition_no in(" . implode(",", $requisition_no_arr) . ")");
				foreach ($kintting_source_data as $row) {
					$kintting_arr[$row[csf('requisition_no')]]['source'] = $row[csf('knitting_source')];
					$kintting_arr[$row[csf('requisition_no')]]['party'] = $row[csf('knitting_party')];
					$kintting_arr[$row[csf('requisition_no')]]['prog_no'] = $row[csf('knit_id')];
					$kintting_arr[$row[csf('requisition_no')]]['is_sales'] = $row[csf('is_sales')];
					$program_no_arr[$row[csf('knit_id')]] = $row[csf('knit_id')];
				}

				if (!empty($program_no_arr)) {
					if ($db_type == 0) {
						$program_po_arr = sql_select("select dtls_id, group_concat(po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls where dtls_id in(" . implode(",", $requisition_no_arr) . ") group by dtls_id");
					} else {
						$program_po_arr = sql_select("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls where dtls_id in(" . implode(",", $requisition_no_arr) . ") group by dtls_id,booking_no");
					}

					$program_po_array = array();
					foreach ($program_po_arr as $row) {
						$program_po_array[$row[csf("dtls_id")]]["po_id"] = $row[csf("po_id")];
						$program_po_array[$row[csf("dtls_id")]]["booking_no"] = $row[csf("booking_no")];
					}
				}
			}

			$i = 1;
			foreach ($nameArray as $selectResult) {
				$is_sales = $kintting_arr[$selectResult[csf('requisition_no')]]['is_sales'];
				if ($i == 1) {
			?>
					<thead>
						<th width="30">SL</th>
						<th width="100">PO Buyer</th>
						<th width="100">PO Company</th>
						<th width="100">Job No</th>
						<th width="100">Sales Order No.</th>
						<th width="100">Booking Type</th>
						<th width="100">Booking No</th>
						<th width="80">Knitting Com.<? //echo $company_arr[$data[1]]; 
													?></th>
						<th width="80">Program No</th>
						<th width="50">Reqsn. No</th>
						<th width="50">Count</th>
						<th width="110">Composition</th>
						<th width="100">Supplier</th>
						<th width="70">Type</th>
						<th width="70">Color</th>
						<th width="50">Lot</th>
						<th width="50">Brand</th>
						<th width="80">Demand Qnty</th>
						<th width="60">No of Cone</th>
						<th width="70">No of Ctn</th>
						<th width="">Remark</th>
					</thead>
				<?
				}

				$kintting_source = $kintting_arr[$selectResult[csf('requisition_no')]]['source'];
				$kintting_com = $kintting_arr[$selectResult[csf('requisition_no')]]['party'];
				$program_no = implode(",", array_unique(explode(",", $kintting_arr[$selectResult[csf('requisition_no')]]['prog_no'])));
				$job_no = "";
				if ($is_sales == 1) {
					$po_ids_arr = explode(",", $program_po_array[$program_no]["po_id"]);
					$book_type = $sales_order_array[$po_ids_arr[0]]['booking_type'];
					$entry_book_type = $sales_order_array[$po_ids_arr[0]]['entry_form_no'];
					$booking_entry_form = $sales_order_array[$po_ids_arr[0]]['booking_entry_form'];
					$booking_without_order = $sales_order_array[$po_ids_arr[0]]['booking_without_order'];
					$short_book_type = $sales_order_array[$po_ids_arr[0]]['short_booking_type'];
					$po_no = $sales_order_array[$po_ids_arr[0]]['sales_order_no'];

					if ($sales_order_array[$po_ids_arr[0]]['within_group'] == 2) {
						$po_buyer = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
						$job_no = $sales_order_array[$po_ids_arr[0]]['po_job_no'];
						$style_ref = $sales_order_array[$po_ids_arr[0]]['style_ref_no'];
					} else {

						$po_buyer = $buyer_arr[$po_array[$sales_order_array[$po_ids_arr[0]]['sales_booking_no']]['buyer']];
						$job_no = $po_array[$sales_order_array[$po_ids_arr[0]]['sales_booking_no']]['job_no'];
						$style_ref = $po_array[$sales_order_array[$po_ids_arr[0]]['sales_booking_no']]['style_ref_no'];
					}
					$buyer_id = $company_library[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
				} else {
					$po_id 				= $program_po_array[$program_no]["po_id"];
					$booking_no 		= $program_po_array[$program_no]["booking_no"];
					$booking_entry_form = $program_po_array[$program_no]["booking_entry_form"];
					$all_po_id 			= array_unique(explode(",", $po_id));
					$po_no 				= '';
					$buyer_id 			= '';
					$file_no 			= '';
					$internal_ref 		= '';

					foreach ($all_po_id as $val) {
						if ($job_no == '') $job_no = $po_array[$val]['job_no'];
						else $job_no .= "," . $po_array[$val]['job_no'];
						if ($po_no == '') $po_no = $po_array[$val]['no'];
						else $po_no .= "," . $po_array[$val]['no'];
						if ($buyer_id == '') $buyer_id = $buyer_arr[$po_array[$val]['buyer']];
						if ($internal_ref == '') $internal_ref = $po_array[$val]['ref'];
						else $internal_ref .= "," . $po_array[$val]['ref'];
						if ($file_no == '') $file_no = $po_array[$val]['file'];
						else $file_no .= "," . $po_array[$val]['file'];
					}
				}
				?>
				<tr>
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="100" align="center">
						<p><? echo $po_buyer; ?></p>
					</td>
					<td width="100" align="center">
						<p><?php echo $company_library[$data[1]]; ?></p>
					</td>
					<td width="100" align="center"><?php echo $job_no; ?></td>
					<td width="100" align="center" style="word-break:break-all">
						<p><? echo $po_no; ?></p>
					</td>
					<?
					if ($booking_entry_form == 88) {
						if ($short_book_type == 0) {
							$sort_book_type = "Short";
						} elseif ($short_book_type == 1) {
							$sort_book_type = "Additional";
						} elseif ($short_book_type == 2) {
							$sort_book_type = "Compensative";
						} elseif ($short_book_type == 3) {
							$sort_book_type = "Compensative -Dia Change";
						}
					?>
						<td width="100" align="center"><?php echo $sort_book_type; ?></td>
					<?
					} else {
					?>
						<td width="100" align="center">
							<?
							if ($book_type == 4) {
								if ($booking_without_order == 0) {
									echo "Sample With Order";
								} else {
									echo "Sample Without Order";
								}
							} else {
								echo $entry_booking_type_arr[$booking_entry_form];
							}

							?></td>
					<?
					}
					?>
					<td width="100" align="center"><?php echo $sales_order_array[$po_ids_arr[0]]['sales_booking_no']; ?></td>
					<td width="80">
						<p>
							<?
							if ($kintting_source == 1) echo $company_library[$kintting_com];
							else if ($kintting_source == 3) echo $supplier_arr[$kintting_com];
							else echo "&nbsp;";
							?>
						</p>
					</td>
					<td width="80" align="center"><? echo $program_no; ?></td>
					<td width="60" align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
					<td width="50" align="center"><? echo $product_details_array[$selectResult[csf('prod_id')]]['count']; ?></td>
					<td width="110" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['comp']; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['suppl']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></p>
					</td>
					<td width="80" align="right"><? echo number_format($selectResult[csf('yarn_demand_qnty')], 2); ?></td>
					<td width="60" align="right"><? echo number_format($selectResult[csf('cone_qty')], 2); ?></td>
					<td width="70" align="right"><? echo number_format($selectResult[csf('ctn_qty')], 2); ?></td>
					<td align="right">
						<p><? echo $selectResult[csf('remarks')]; ?></p>
					</td>
				</tr>
			<?
				$tot_demand_qnty += $selectResult[csf('yarn_demand_qnty')];
				$tot_cone_qnty 	 += $selectResult[csf('cone_qty')];
				$tot_ctn_qnty 	 += $selectResult[csf('ctn_qty')];
				$i++;
			}
			?>
			<tfoot>
				<th colspan="<?php echo ($is_sales == 0) ? 17 : 17; // 16:15;
								?>" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_demand_qnty, 2); ?></th>
				<th align="right"><? echo number_format($tot_cone_qnty, 2); ?></th>
				<th align="right"><? echo number_format($tot_ctn_qnty, 2); ?></th>
			</tfoot>
		</table>
		<br>
		<?
		echo signature_table(40, $data[1], "1180px");
		?>
	</div>
<?
	exit();
}

if ($action == "print3") {
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	echo load_html_head_contents("Demand Print", "../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$location_arr 	 = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$country_arr 	 = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$supplier_arr 	 = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$floor_arr 		 = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[1]'", "image_location");

	$po_array = array();
	$costing_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, b.id,b.grouping,b.file_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[1]");
	foreach ($costing_sql as $row) {
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
	}

	$sales_order_array = array();
	$sales_order_sql = sql_select("select a.id,a.sales_booking_no, a.job_no,a.style_ref_no, a.buyer_id,b.job_no po_job_no,b.buyer_id po_buyer,a.within_group from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.status_active = 1 and a.is_deleted = 0 and a.company_id=$data[1]");
	foreach ($sales_order_sql as $row) {
		$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
		$sales_order_array[$row[csf('id')]]['po_job_no'] = $row[csf('po_job_no')];
		$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_order_array[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];
	}
?>
	<div style="margin-left:20px">

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180" align="right">
					<img src='../<? echo $image_location; ?>' height='100%' width='100%' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center" style="font-family:'Arial Narrow'; font-size:18px; ">
						<tr>
							<td align="center" style="font-size:x-large"><strong><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td align="center" style="font-size:14px">
								<?
								$nameArray_com = sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id='$data[1]' and status_active=1 and is_deleted=0");
								foreach ($nameArray_com as $result) {
								?>
									<? echo $result[csf('plot_no')]; ?>,
									<? echo $result[csf('level_no')] ?>,
									<? echo $result[csf('road_no')]; ?>,
									<? echo $result[csf('block_no')]; ?>,
									<? echo $result[csf('city')]; ?>,
									<? echo $result[csf('zip_code')]; ?>,
									<?php echo $result[csf('province')]; ?>
									<? echo $country_arr[$result[csf('country_id')]]; ?><br>
									<? echo $result[csf('email')]; ?>,
								<? echo $result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><b><u>Daily Yarn Demand</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table width="1400" style="margin-top:10px;font-family:'Arial Narrow'; font-size:18px;">

			<?
			$dataArray = sql_select("select a.booking_no,a.buyer_id,a.body_part_id,d.location_id, d.demand_date, d.demand_system_no, d.floor_name, d.remarks, c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c ,ppl_yarn_demand_entry_mst d, ppl_yarn_demand_entry_dtls e where a.id=b.mst_id and b.id=c.knit_id  and d.id=e.mst_id and e.requisition_no=c.requisition_no and d.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

			/*echo "<pre>";
			print_r($dataArray);*/

			$booking_no_array = array();
			foreach ($dataArray as $row) {
				$booking_no_array[$row[csf("requisition_no")]]["booking_no"] = $row[csf("booking_no")];
				$booking_no_array[$row[csf("requisition_no")]]["buyer_id"] = $row[csf("buyer_id")];
			}
			/*echo "<pre>";
			print_r($booking_no_array);*/

			?>
			<table width="1270" style="margin-top:10px; font-family:'Arial Narrow'; font-size:18px;">

				<tr>
					<td><b>Demand No:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $dataArray[0][csf('demand_system_no')]; ?></td>
					<td><b>Demand
							Date:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo change_date_format($dataArray[0][csf('demand_date')]); ?>
					</td>
					<td><b>Location:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?>
					</td>
					<td><b>Floor:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $floor_arr[$dataArray[0][csf('floor_name')]]; ?></td>
				</tr>
				<tr>
					<td><b>Remarks:&nbsp;&nbsp;&nbsp;&nbsp;</b><? echo $dataArray[0][csf('remarks')]; ?></td>

				</tr>
			</table>
			<table style="margin-top:10px; font-family:'Arial Narrow'; font-size:18px; " width="1400" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
				<?
				$kintting_arr = array();
				$kintting_source_data = sql_select("select a.prod_id,a.requisition_no,a.knit_id,a.yarn_qnty,a.total_distribution_qnty,b.knitting_source,b.knitting_party,b.is_sales from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.id");

				foreach ($kintting_source_data as $row) {

					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['source'] = $row[csf('knitting_source')];
					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['party'] = $row[csf('knitting_party')];
					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['prog_no'] = $row[csf('knit_id')];
					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['is_sales'] = $row[csf('is_sales')];
					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['req_qnty'] = $row[csf('yarn_qnty')];
					$kintting_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]['distribution_qnty'] = $row[csf('total_distribution_qnty')];
				}

				if ($db_type == 0) {
					$program_po_arr = sql_select("select dtls_id, group_concat(po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls  group by dtls_id"); //where status_active=1 and is_deleted=0
				} else {
					$program_po_arr = sql_select("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id,booking_no from ppl_planning_entry_plan_dtls  group by dtls_id,booking_no"); //where status_active=1 and is_deleted=0
				}

				$program_po_array = array();
				foreach ($program_po_arr as $row) {

					$program_po_array[$row[csf("dtls_id")]]["po_id"] = $row[csf("po_id")];
					$program_po_array[$row[csf("dtls_id")]]["booking_no"] = $row[csf("booking_no")];
				}

				$i = 1;
				$sql = "select a.requisition_no, b.prod_id, sum(b.yarn_demand_qnty) as yarn_demand_qnty, sum(b.cone_qty) as cone_qty, sum(b.ctn_qty) as ctn_qty,b.remarks from ppl_yarn_demand_entry_dtls a, ppl_yarn_demand_reqsn_dtls b where a.id=b.dtls_id and a.mst_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id,a.requisition_no,b.remarks order by b.prod_id,a.requisition_no";
				$nameArray = sql_select($sql);
				$prodIdArr = array();
				foreach ($nameArray as $rowdata) {
					$prod_id = $rowdata[csf('prod_id')];
					$lot = $product_details_array[$rowdata[csf('prod_id')]]['lot'];
					$requisitionNo = $rowdata[csf('requisition_no')];
					$data_array[$prod_id][$requisitionNo]["prod_id"] = $prod_id;
					$data_array[$prod_id][$requisitionNo]["requisition_no"] = $requisitionNo;
					$data_array[$prod_id][$requisitionNo]["distributionqty"] += $kintting_arr[$prod_id][$requisitionNo]['distribution_qnty'];
					$data_array[$prod_id][$requisitionNo]["req_qty"] += $kintting_arr[$prod_id][$requisitionNo]['req_qnty'];
					$data_array[$prod_id][$requisitionNo]["yarn_demand_qnty"] += $rowdata[csf('yarn_demand_qnty')];
					$data_array[$prod_id][$requisitionNo]["cone_qty"] += $rowdata[csf('cone_qty')];
					$data_array[$prod_id][$requisitionNo]["ctn_qty"] += $rowdata[csf('ctn_qty')];
					$data_array[$prod_id][$requisitionNo]["remarks"] = $rowdata[csf('remarks')];

					$lot_wise_array[$prod_id]["yarn_demand_qnty"] += $rowdata[csf('yarn_demand_qnty')];
					$lot_wise_array[$prod_id]["cone_qty"] += $rowdata[csf('cone_qty')];
					$lot_wise_array[$prod_id]["ctn_qty"] += $rowdata[csf('ctn_qty')];

					$prodIdArr[$prod_id] = $prod_id;
				}
				/*echo "<pre>";
		print_r($data_array);*/

				//for product information
				$product_desc_array = array();
				$product_details_array = array();
				$sql_prod = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(',', $prodIdArr) . ")";
				$result_prod = sql_select($sql_prod);
				foreach ($result_prod as $row) {
					$compos = '';
					$desc = '';
					if ($row[csf('yarn_comp_percent2nd')] != 0) {
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
					} else {
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
					}

					$desc = $row[csf('lot')] . " " . $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_library[$row[csf('color')]];
					$product_desc_array[$row[csf('id')]] = $desc;
					$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
					$product_details_array[$row[csf('id')]]['comp'] = $compos;
					$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
					$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
					$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
					$product_details_array[$row[csf('id')]]['suppl'] = $supllier_arr[$row[csf('supplier_id')]];
					$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
				}
				//for product information end

				foreach ($data_array as $prod_id => $reqNoArr) {
					foreach ($reqNoArr as $reqNo => $selectResult) {
						$is_sales = $kintting_arr[$prod_id][$reqNo]['is_sales'];

						if ($i == 1) {
				?>
							<thead>
								<th width="30">SL</th>
								<th width="90">Buyer</th>
								<?php if ($is_sales == 0) { ?>
									<th width="50">In.Ref.</th>
									<th width="100">Order No</th>
									<th width="100">Style Reference</th>
									<th width="100">Booking No</th>
									<th width="100">Job No</th>
								<?php } else {
								?>
									<th width="100">Sales Order No</th>

									<th width="100">Style Reference</th>
									<th width="100">Booking No</th>
									<th width="100">Job No</th>
									<th width="100">PO Buyer</th>
								<? } ?>
								<th width="80">Knitting Com.<? //echo $company_arr[$data[1]];
															?></th>
								<th width="80">Program No</th>
								<th width="50">Reqsn. No</th>
								<th width="300">Yarn Deascription</th>
								<th width="100">Supplier</th>
								<th width="70">Color</th>
								<th width="80">Distribution Qty</th>
								<th width="80">Req. Qty</th>
								<th width="80">Today Demand Qnty</th>

								<th width="50">Cum Demand</th>
								<th width="80">Balance</th>
								<th width="50">Ret/Exc Qty</th>

								<th width="60">No of Cone</th>
								<th width="70">No of Ctn</th>
								<th width="">Remark</th>
							</thead>
						<?
						}
						$kintting_source = $kintting_arr[$prod_id][$reqNo]['source'];
						$kintting_com = $kintting_arr[$prod_id][$reqNo]['party'];
						$program_no = implode(",", array_unique(explode(",", $kintting_arr[$prod_id][$reqNo]['prog_no'])));
						$job_no = "";

						if ($is_sales == 1) {
							$po_ids_arr = explode(",", $program_po_array[$program_no]["po_id"]);
							$within_group = $sales_order_array[$po_ids_arr[0]]['within_group'];

							if ($row[csf('within_group')] == 1) {
								$buyer_id = $company_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
							} else {
								$buyer_id = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['buyer_id']];
							}

							$po_no = $sales_order_array[$po_ids_arr[0]]['sales_order_no'];
							$job_no = $sales_order_array[$po_ids_arr[0]]['po_job_no'];
							$style_ref = $sales_order_array[$po_ids_arr[0]]['style_ref_no'];
							$po_buyer = $buyer_arr[$sales_order_array[$po_ids_arr[0]]['po_buyer']];
						} else {
							$po_id = $program_po_array[$program_no]["po_id"];
							// $booking_no = $program_po_array[$program_no]["booking_no"];
							$all_po_id = array_unique(explode(",", $po_id));
							$po_no = '';
							$style_ref = '';
							$buyer_id = '';
							$file_no = '';
							$internal_ref = '';

							foreach ($all_po_id as $val) {
								if ($job_no == '') $job_no = $po_array[$val]['job_no'];
								else $job_no .= "," . $po_array[$val]['job_no'];

								if ($style_ref == '') $style_ref = $po_array[$val]['style_ref_no'];
								else $style_ref .= "," . $po_array[$val]['style_ref_no'];
								if ($po_no == '') $po_no = $po_array[$val]['no'];
								else $po_no .= "," . $po_array[$val]['no'];
								if ($buyer_id == '') $buyer_id = $buyer_arr[$po_array[$val]['buyer']];
								if ($internal_ref == '') $internal_ref = $po_array[$val]['ref'];
								else $internal_ref .= "," . $po_array[$val]['ref'];
								if ($file_no == '') $file_no = $po_array[$val]['file'];
								else $file_no .= "," . $po_array[$val]['file'];
							}
						}

						$count = $product_details_array[$prod_id]['count'];
						$brand = $product_details_array[$prod_id]['brand'];
						$lot = $product_details_array[$prod_id]['lot'];
						$composition = $product_details_array[$prod_id]['comp'];
						$yarntype = $product_details_array[$prod_id]['type'];
						$yarnDescription = $count . " " . $composition . " " . $brand . " " . $lot . " " . $yarntype;

						$distribution_qnty = $kintting_arr[$prod_id][$reqNo]['distribution_qnty'];
						$req_qnty = $kintting_arr[$prod_id][$reqNo]['req_qnty'];
						$demand_qnty = $selectResult['yarn_demand_qnty'];

						$cumulative_demand_qnty = return_field_value("sum(yarn_demand_qnty)", "ppl_yarn_demand_reqsn_dtls", "requisition_no='" . $reqNo . "' and status_active=1 and is_deleted=0 and prod_id = $prod_id");

						$balanceQty = ($req_qnty - $cumulative_demand_qnty);

						$returnable_qnty = ($cumulative_demand_qnty - $distribution_qnty);
						if ($returnable_qnty < 0) {
							$returnable_qnty = 0;
						}
						$booking_no = $booking_no_array[$selectResult['requisition_no']]["booking_no"];
						$buyer_id_new = $buyer_arr[$booking_no_array[$selectResult['requisition_no']]["buyer_id"]];
						// New Booking_no source confirm by Mr. Mamun and Mr. Helal
						$style_ref = implode(",", array_unique(explode(',', $style_ref)));
						?>
						<tr>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="90" align="center">
								<p><? echo $buyer_id_new; ?></p>
							</td>
							<?php if ($is_sales == 0) { ?>
								<td width="50" align="center"><?php echo $internal_ref; ?></td>
								<td width="100" align="center" style="word-break:break-all">
									<p><? echo $po_no; ?></p>
								</td>
								<td width="100" align="center"><?php echo $style_ref; ?></td>

								<td width="100" align="center"><?php echo $booking_no; ?></td>
								<td width="100" align="center"><?php echo $job_no; ?></td>
							<?php
							} else {
							?>
								<td width="100" align="center">
									<p><? echo $po_no; ?></p>
								</td>
								<td width="100" align="center"><?php echo $style_ref; ?></td>
								<td width="100" align="center"><?php echo $booking_no; //$sales_order_array[$po_ids_arr[0]]['sales_booking_no']; 
																?></td>
								<td width="100" align="center"><?php echo $job_no; ?></td>
								<td width="100" align="center"><?php echo $po_buyer; ?></td>
							<?
							}
							?>
							<td width="80">
								<p>
									<?
									if ($kintting_source == 1) echo $company_library[$kintting_com];
									else if ($kintting_source == 3) echo $supplier_arr[$kintting_com];
									else echo "&nbsp;";
									?>
								</p>
							</td>
							<td width="80" align="center"><? echo $program_no; ?></td>
							<td width="60" align="center"><? echo $selectResult['requisition_no']; ?></td>
							<td width="300" align="center"><? echo $yarnDescription; ?></td>
							<td width="100" align="center">
								<p><? echo $product_details_array[$selectResult['prod_id']]['suppl']; ?></p>
							</td>
							<td width="70" align="center">
								<p><? echo $product_details_array[$selectResult['prod_id']]['color']; ?></p>
							</td>
							<td width="80" align="right"><? echo number_format($distribution_qnty, 2); ?></td>
							<td width="50" align="right"><? echo number_format($req_qnty, 2); ?></td>
							<td width="80" align="right"><? echo number_format($demand_qnty, 2); ?></td>
							<td width="50" align="right"><? echo number_format($cumulative_demand_qnty, 2); ?></td>
							<td width="80" align="right" title="Requisition Qty-Demand Qty"><? echo number_format($balanceQty, 2); ?></td>
							<td width="80" align="right" title="Cumulative Demand Qty-Distribution Qty"><? echo number_format($returnable_qnty, 2); ?></td>

							<td width="60" align="right"><? echo number_format($selectResult['cone_qty'], 2); ?></td>
							<td width="70" align="right"><? echo number_format($selectResult['ctn_qty'], 2); ?></td>
							<td align="right">
								<p><? echo $selectResult['remarks']; ?></p>
							</td>
						</tr>

					<?

						$tot_demand_qnty += $demand_qnty;
						$tot_cone_qnty += $selectResult['cone_qty'];
						$tot_ctn_qnty += $selectResult['ctn_qty'];


						$i++;
					}
					?>
					<tr style="background-color: #ccc;">
						<th colspan="<?php echo ($is_sales == 0) ? 15 : 15; // 15:13;
										?>" align="right"><b>Sub Total</b></th>
						<th align="right"><? echo number_format($lot_wise_array[$prod_id]["yarn_demand_qnty"], 2); ?></th>
						<th align="right">&nbsp;</th>
						<th align="right">&nbsp;</th>
						<th align="right">&nbsp;</th>
						<th align="right"><? echo number_format($lot_wise_array[$prod_id]['cone_qty'], 2); ?></th>
						<th align="right"><? echo number_format($lot_wise_array[$prod_id]['ctn_qty'], 2); ?></th>
						<th>&nbsp;</th>
					</tr>
				<?
				}
				?>

				<tfoot>
					<th colspan="<?php echo ($is_sales == 0) ? 15 : 15; // 15:13;
									?>" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_demand_qnty, 2); ?></th>
					<th align="right">&nbsp;</th>
					<th align="right">&nbsp;</th>
					<th align="right">&nbsp;</th>
					<th align="right"><? echo number_format($tot_cone_qnty, 2); ?></th>
					<th align="right"><? echo number_format($tot_ctn_qnty, 2); ?></th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(40, $data[1], "1180px");
			?>
	</div>
<?
	exit();
}

if ($action == "print4") {
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	echo load_html_head_contents("Demand Print", "../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);

	$sql = "SELECT C.DEMAND_SYSTEM_NO, C.DEMAND_DATE, C.LOCATION_ID, C.FLOOR_NAME, C.REMARKS, A.REQUISITION_NO, B.PROD_ID, SUM(B.YARN_DEMAND_QNTY) AS YARN_DEMAND_QNTY, SUM(B.CONE_QTY) AS CONE_QTY, SUM(B.CTN_QTY) AS CTN_QTY, B.REMARKS FROM PPL_YARN_DEMAND_ENTRY_MST C, PPL_YARN_DEMAND_ENTRY_DTLS A, PPL_YARN_DEMAND_REQSN_DTLS B WHERE C.ID=A.MST_ID AND A.ID=B.DTLS_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.ID='" . $data[0] . "' GROUP BY C.DEMAND_SYSTEM_NO, C.DEMAND_DATE, C.LOCATION_ID, C.FLOOR_NAME, C.REMARKS, A.REQUISITION_NO, B.PROD_ID, B.REMARKS ORDER BY A.REQUISITION_NO";
	//echo $sql;
	$nameArray = sql_select($sql);
	$prodIdArr = array();
	$req_data = array();
	$dmnd_data = array();


	// echo "<pre>";
	// print_r($nameArray);
	// echo "</pre>"; die;


	foreach ($nameArray as $row) {
		$prodIdArr[$row['PROD_ID']] = $row['PROD_ID'];
		$requisition_no_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];

		$dmnd_data['DEMAND_NO'] = $row['DEMAND_SYSTEM_NO'];
		$dmnd_data['DEMAND_DATE'] = change_date_format($row['DEMAND_DATE']);
		$dmnd_data['LOCATION_ID'] = $location_arr[$row['LOCATION_ID']];
		$dmnd_data['FLOOR_NAME'] = $floor_arr[$row['FLOOR_NAME']];
		$dmnd_data['REMARKS'] = $row['REMARKS'];

		$demandDate = date('d-m-Y', strtotime($row['DEMAND_DATE']));
		if (strtotime($data[2]) == strtotime($demandDate)) {
			$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['DMND_QTY'] += $row['YARN_DEMAND_QNTY'];
		} else {
			$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['PRE_DMND_QTY'] += $row['YARN_DEMAND_QNTY'];
		}
	}
	// echo "<pre>";
	// print_r($dmnd_data);
	// echo "</pre>"; die;


	//for product information
	$product_desc_array = array();
	$product_details_array = array();
	$sql_prod = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(" . implode(',', $prodIdArr) . ")";
	$result_prod = sql_select($sql_prod);
	foreach ($result_prod as $row) {
		$compos = '';
		$desc = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0) {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		} else {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}

		$desc = $row['LOT'] . " " . $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']] . " " . $color_library[$row['COLOR']];
		$product_desc_array[$row['ID']] = $desc;
		$product_details_array[$row['ID']]['count'] = $count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['comp'] = $compos;
		$product_details_array[$row['ID']]['type'] = $yarn_type[$row['YARN_TYPE']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
		$product_details_array[$row['ID']]['suppl'] = $supplier_arr[$row['SUPPLIER_ID']];
		$product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
	}

	// echo "<pre>";
	// print_r($result_prod);
	// echo "</pre>"; die;
	//for product information end

	//for sales order information
	$sql_sales = "SELECT A.ID, A.YARN_QNTY, A.REQUISITION_NO, A.PROD_ID, A.KNIT_ID, C.SALES_BOOKING_NO, C.JOB_NO, C.BUYER_ID, C.CUSTOMER_BUYER, C.WITHIN_GROUP, D.KNITTING_SOURCE, D.KNITTING_PARTY, C.BOOKING_ID FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_INFO_ENTRY_DTLS D, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C WHERE A.KNIT_ID=B.DTLS_ID AND A.KNIT_ID=D.ID AND B.DTLS_ID=D.ID AND B.PO_ID = C.ID AND B.IS_SALES = 1 AND D.IS_SALES = 1  AND A.REQUISITION_NO IN(" . implode(",", $requisition_no_arr) . ")";


	$sql_sales_result = sql_select($sql_sales);


	$sql_non_sales = "SELECT A.ID, A.YARN_QNTY, A.REQUISITION_NO, A.PROD_ID, A.KNIT_ID, C.BOOKING_NO AS SALES_BOOKING_NO, C.BUYER_ID,D.KNITTING_SOURCE, D.KNITTING_PARTY,E.ID AS BOOKING_ID FROM PPL_YARN_REQUISITION_ENTRY  A, PPL_PLANNING_INFO_ENTRY_DTLS  D, PPL_PLANNING_ENTRY_PLAN_DTLS  B, WO_NON_ORD_SAMP_BOOKING_MST   C, WO_NON_ORD_SAMP_BOOKING_MST E WHERE     A.KNIT_ID = B.DTLS_ID AND A.KNIT_ID = D.ID AND B.DTLS_ID = D.ID AND B.BOOKING_NO = C.BOOKING_NO AND C.BOOKING_NO = E.BOOKING_NO AND B.IS_SALES = 2 AND D.IS_SALES = 2 AND A.REQUISITION_NO IN (" . implode(",", $requisition_no_arr) . ")";


	// echo $sql_non_sales;die;
	$sql_non_sales_result = sql_select($sql_non_sales);

	$sales_data = array();
	$booking_id_arr = array();
	$duplicate_check = array();
	$duplicateBookId_check = array();

	// echo count($sql_sales_result);die;
	if (count($sql_sales_result) <= 0) {
		$sample_flag = true;
	}
	if ($sample_flag) {
		$sql_sales_result = $sql_non_sales_result;
	}
	// echo "<pre>";
	// print_r($sql_sales_result);
	// echo "</pre>"; die;



	foreach ($sql_sales_result as $row) {
		if ($duplicate_check[$row['ID']] != $row['ID']) {
			$duplicate_check[$row['BOOKING_ID']] = $row['BOOKING_ID'];
			$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['REQ_QTY'] += $row['YARN_QNTY'];
		}

		if ($duplicateBookId_check[$row['BOOKING_ID']] != $row['BOOKING_ID']) {
			$duplicateBookId_check[$row['BOOKING_ID']] = $row['BOOKING_ID'];
			$booking_id_arr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
		}

		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['PROG_NO'] = $row['KNIT_ID'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['SALES_NO'] = $row['JOB_NO'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_PARTY'] = $row['KNITTING_PARTY'];
		if ($sample_flag) {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'] = $buyer_arr[$row['BUYER_ID']];
		} else {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'] = $buyer_arr[$row['CUSTOMER_BUYER']];
		}
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_ID'] = $row['BOOKING_ID'];

		if ($row['WITHIN_GROUP'] == 1) {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'] = $company_library[$row['BUYER_ID']];
		} else {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'] = $buyer_arr[$row['BUYER_ID']];
		}
		// $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'] = $buyer_arr[$row['CUSTOMER_BUYER']];

	}
	// echo "<pre>";
	// print_r($sales_data);
	// echo "</pre>"; die;
	//echo "<pre>";print_r($booking_id_arr);



	$booking_sql = "select a.id as booking_id,a.booking_no,a.buyer_id,c.style_ref_no,d.id,d.file_no,d.grouping internal_ref,d.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c, wo_po_break_down d where a.id=b.booking_mst_id and b.job_no=c.job_no and c.job_no=d.job_no_mst and company_id = " . $data[1] . " and a.id in(" . implode(",", $booking_id_arr) . ") and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and d.status_active = 1 group by a.id,a.booking_no,a.buyer_id,c.style_ref_no, d.id,d.file_no,d.grouping,d.po_number";
	// echo $booking_sql;
	$rslt_booking_sql = sql_select($booking_sql);
	$booking_info_arr = array();
	foreach ($rslt_booking_sql as $row) {
		$booking_info_arr[$row['BOOKING_ID']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
	}
	unset($rslt_booking_sql);
	// echo "<pre>";print_r($booking_info_arr);


	//end for sales order information

	/*$sql_dmnd = sql_select("SELECT DEMAND_SYSTEM_NO, DEMAND_DATE, LOCATION_ID, FLOOR_NAME, REMARKS FROM PPL_YARN_DEMAND_ENTRY_MST WHERE ID=".$data[0]);
	$dmnd_data = array();
	foreach($sql_dmnd as $row)
	{
		$dmnd_data['DEMAND_NO'] = $row['DEMAND_SYSTEM_NO'];
		$dmnd_data['DEMAND_DATE'] = change_date_format($row['DEMAND_DATE']);
		$dmnd_data['LOCATION_ID'] = $location_arr[$row['LOCATION_ID']];
		$dmnd_data['FLOOR_NAME'] = $floor_arr[$row['FLOOR_NAME']];
		$dmnd_data['REMARKS'] = $row['REMARKS'];
	}*/
?>
	<style>
		.demand td {
			font-size: 14px;
		}
	</style>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180" align="right">&nbsp;</td>
				<td>
					<table width="1220" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:x-large"><strong><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td align="center" style="font-size:14px"><?
																		$nameArray_com = sql_select("SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID='" . $data[1] . "' AND STATUS_ACTIVE=1 AND IS_DELETED=0");
																		$loc = '';
																		foreach ($nameArray_com as $result) {
																			if ($result['PLOT_NO'] != '') {
																				$loc .= $result['PLOT_NO'];
																			}

																			if ($result['LEVEL_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['PLOT_NO'];
																				} else {
																					$loc .= $result['PLOT_NO'];
																				}
																			}

																			if ($result['ROAD_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['ROAD_NO'];
																				} else {
																					$loc .= $result['ROAD_NO'];
																				}
																			}

																			if ($result['BLOCK_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['BLOCK_NO'];
																				} else {
																					$loc .= $result['BLOCK_NO'];
																				}
																			}

																			if ($result['CITY'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['CITY'];
																				} else {
																					$loc .= $result['CITY'];
																				}
																			}
																		}
																		echo $loc;
																		?></td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><b><u>Daily Yarn Demand</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="1500" style="margin-top:10px;" class="demand">
			<tr>
				<td width="70"><b>Demand No</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="150"><? echo $dmnd_data['DEMAND_NO']; ?></td>

				<td width="80"><b>Demand Date</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="100"><? echo $dmnd_data['DEMAND_DATE']; ?></td>

				<td width="60"><b>Location</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="250"><? echo $dmnd_data['LOCATION_ID']; ?></td>

				<td width="60"><b>Floor</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="690"><? echo $dmnd_data['FLOOR_NAME']; ?></td>
			</tr>
			<tr>
				<td><b>Remarks</b>
				<td>
				<td><b>:</b></td>
				<td colspan="10"><? echo $data[5]; ?></td>
			</tr>
		</table>
		<table style="margin-top:10px;" width="1600" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Sales Order No.</th>
				<th width="100">Sales Job/ Booking No</th>
				<th width="100">IR/IB</th>
				<th width="100">Customer</th>
				<th width="100">Cust. Buyer</th>
				<th width="80">Knitting Com.</th>
				<th width="80">Program No</th>
				<th width="50">Reqsn. No</th>
				<th width="50">Count</th>
				<th width="110">Composition</th>
				<th width="70">Type</th>
				<th width="70">Color</th>
				<th width="100">Supplier</th>
				<th width="50">Brand</th>
				<th width="50">Lot</th>
				<th width="80">Demand Qnty</th>
				<th width="60">No of Cone</th>
				<th width="70">No of Ctn</th>
				<th>Remark</th>
			</thead>
			<?
			$i = 1;

			// echo "<pre>";
			// print_r($sales_data);
			// echo "</pre>"; die;

			foreach ($nameArray as $row) {
				$PROG_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['PROG_NO'];
				$SALES_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['SALES_NO'];
				$BOOKING_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_NO'];
				$CUSTOMER = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'];

				$CUSTOMER_BUYER = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'];
				// print_r($CUSTOMER_BUYER);die;

				$KNITTING_SOURCE = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_SOURCE'];
				$KNITTING_PARTY = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_PARTY'];
				$BOOKING_ID = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_ID'];
				if (!$sample_flag) {
					$INTERNAL_REF = $booking_info_arr[$BOOKING_ID]['INTERNAL_REF'];
				} else {
					$sql = "select b.internal_ref from WO_NON_ORD_SAMP_BOOKING_DTLS a, SAMPLE_DEVELOPMENT_MST b where a.style_id = b.id and a.booking_no = '$BOOKING_NO'";
					$sql_res = sql_select($sql);
					// echo $sql;
					$INTERNAL_REF = $sql_res[0]['INTERNAL_REF'];
					// echo $INTERNAL_REF;die;
				}
				$KNITTING_COMPANY = '';
				if ($KNITTING_SOURCE == 1) {
					$KNITTING_COMPANY = $company_library[$KNITTING_PARTY];
				} elseif ($KNITTING_SOURCE == 3) {
					$KNITTING_COMPANY = $supplier_arr[$KNITTING_PARTY];
				}
			?>
				<tr>
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="100"><?php echo $SALES_NO; ?></td>
					<td width="100"><?php echo $BOOKING_NO; ?></td>
					<td width="100" title="<?php echo "Booking Id:" . $BOOKING_ID; ?>" align="center"><?php echo $INTERNAL_REF; ?></td>
					<td width="100">
						<p><?php echo $CUSTOMER; ?></p>
					</td>
					<td width="100">
						<p><? echo $CUSTOMER_BUYER; ?></p>
					</td>
					<td width="80">
						<p><? echo $KNITTING_COMPANY; ?></p>
					</td>
					<td width="80" align="center"><? echo $PROG_NO; ?></td>
					<td width="60" align="center"><? echo $row['REQUISITION_NO']; ?></td>
					<td width="50" align="center"><? echo $product_details_array[$row['PROD_ID']]['count']; ?></td>
					<td width="110">
						<p><? echo $product_details_array[$row['PROD_ID']]['comp']; ?></p>
					</td>
					<td width="70">
						<p><? echo $product_details_array[$row['PROD_ID']]['type']; ?></p>
					</td>
					<td width="70">
						<p><? echo $product_details_array[$row['PROD_ID']]['color']; ?></p>
					</td>
					<td width="100">
						<p><? echo $product_details_array[$row['PROD_ID']]['suppl']; ?></p>
					</td>
					<td width="50">
						<p><? echo $product_details_array[$row['PROD_ID']]['brand']; ?></p>
					</td>
					<td width="50">
						<p><? echo $product_details_array[$row['PROD_ID']]['lot']; ?></p>
					</td>
					<td width="80" align="right"><? echo decimal_format($row['YARN_DEMAND_QNTY'], '1', ','); ?></td>
					<td width="60" align="right"><? echo decimal_format($row['CONE_QTY'], '1', ','); ?></td>
					<td width="70" align="right"><? echo decimal_format($row['CTN_QTY'], '1', ','); ?></td>
					<td>
						<p><? echo $row['REMARKS']; ?></p>
					</td>
				</tr>
			<?
				$tot_demand_qnty += $row['YARN_DEMAND_QNTY'];
				$tot_cone_qnty 	 += $row['CONE_QTY'];
				$tot_ctn_qnty 	 += $row['CTN_QTY'];
				$i++;
			}
			?>
			<tfoot>
				<th colspan="16" align="right"><b>Total</b></th>
				<th align="right"><? echo decimal_format($tot_demand_qnty, '1', ','); ?></th>
				<th align="right"><? echo decimal_format($tot_cone_qnty, '1', ','); ?></th>
				<th align="right"><? echo decimal_format($tot_ctn_qnty, '1', ','); ?></th>
				<th></th>
			</tfoot>
		</table>
		<br>
		<table style="margin-top:10px;" width="600" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<th width="100">Requsition No.</th>
				<th width="100">Lot</th>
				<th width="100">Requsition Qty</th>
				<th width="100">Pre Demand Qty</th>
				<th width="100">Current Demand Qty</th>
				<th width="100">Balance Qty</th>
			</thead>
			<tbody>
				<?
				//$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['REQ_QTY']
				foreach ($req_data as $k_req => $v_req) {
					foreach ($v_req as $k_prd => $row) {
						$pre_dmnd_qty = 0.00;
						$balance_qty = $row['REQ_QTY'] - ($row['DMND_QTY'] + $row['PRE_DMND_QTY']);
				?>
						<tr>
							<td><? echo $k_req; ?></td>
							<td><? echo $product_details_array[$k_prd]['lot']; ?></td>
							<td align="right"><? echo decimal_format($row['REQ_QTY'], '1', ','); ?></td>
							<td align="right"><? echo decimal_format($row['PRE_DMND_QTY'], '1', ','); ?></td>
							<td align="right"><? echo decimal_format($row['DMND_QTY'], '1', ','); ?></td>
							<td align="right"><? echo decimal_format($balance_qty, '1', ','); ?></td>
						</tr>
				<?
						$total_req_qty += $row['REQ_QTY'];
						$total_pre_qty += $row['PRE_DMND_QTY'];
						$total_dmnd_qty += $row['DMND_QTY'];
						$total_bal_qty += $balance_qty;
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2">Total</th>
					<th><? echo decimal_format($total_req_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_pre_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_dmnd_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_bal_qty, '1', ','); ?></th>
				</tr>
			</tfoot>
		</table>
		<?
		echo signature_table(40, $data[1], "1180px");
		?>
	</div>
<?
	exit();
}

if ($action == "print4_01122021") {
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	echo load_html_head_contents("Demand Print", "../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);

	$sql = "SELECT A.REQUISITION_NO, B.PROD_ID, SUM(B.YARN_DEMAND_QNTY) AS YARN_DEMAND_QNTY, SUM(B.CONE_QTY) AS CONE_QTY, SUM(B.CTN_QTY) AS CTN_QTY, B.REMARKS FROM PPL_YARN_DEMAND_ENTRY_DTLS A, PPL_YARN_DEMAND_REQSN_DTLS B WHERE A.ID=B.DTLS_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.MST_ID='" . $data[0] . "' GROUP BY A.REQUISITION_NO, B.PROD_ID, B.REMARKS ORDER BY A.REQUISITION_NO";
	$nameArray = sql_select($sql);
	$prodIdArr = array();
	$req_data = array();
	foreach ($nameArray as $row) {
		$prodIdArr[$row['PROD_ID']] = $row['PROD_ID'];
		$requisition_no_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['DMND_QTY'] = $row['YARN_DEMAND_QNTY'];
	}

	//for product information
	$product_desc_array = array();
	$product_details_array = array();
	$sql_prod = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(" . implode(',', $prodIdArr) . ")";
	$result_prod = sql_select($sql_prod);
	foreach ($result_prod as $row) {
		$compos = '';
		$desc = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0) {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		} else {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}

		$desc = $row['LOT'] . " " . $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']] . " " . $color_library[$row['COLOR']];
		$product_desc_array[$row['ID']] = $desc;
		$product_details_array[$row['ID']]['count'] = $count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['comp'] = $compos;
		$product_details_array[$row['ID']]['type'] = $yarn_type[$row['YARN_TYPE']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
		$product_details_array[$row['ID']]['suppl'] = $supplier_arr[$row['SUPPLIER_ID']];
		$product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
	}
	//for product information end

	//for sales order information
	$sql_sales = sql_select("SELECT A.ID, A.YARN_QNTY, A.REQUISITION_NO, A.PROD_ID, A.KNIT_ID, C.SALES_BOOKING_NO, C.JOB_NO, C.BUYER_ID, C.CUSTOMER_BUYER, C.WITHIN_GROUP, D.KNITTING_SOURCE, D.KNITTING_PARTY FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_INFO_ENTRY_DTLS D, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C WHERE A.KNIT_ID=B.DTLS_ID AND A.KNIT_ID=D.ID AND B.DTLS_ID=D.ID AND B.BOOKING_NO = C.SALES_BOOKING_NO AND B.IS_SALES = 1 AND D.IS_SALES = 1 AND B.COMPANY_ID = " . $data[1] . " AND C.COMPANY_ID = " . $data[1] . " AND A.REQUISITION_NO IN(" . implode(",", $requisition_no_arr) . ")");
	$sales_data = array();
	$duplicate_check = array();
	foreach ($sql_sales as $row) {
		if ($duplicate_check[$row['ID']] != $row['ID']) {
			$duplicate_check[$row['ID']] = $row['ID'];
			$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['REQ_QTY'] += $row['YARN_QNTY'];
		}

		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['PROG_NO'] = $row['KNIT_ID'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['SALES_NO'] = $row['JOB_NO'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_PARTY'] = $row['KNITTING_PARTY'];
		$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'] = $buyer_arr[$row['CUSTOMER_BUYER']];

		if ($row['WITHIN_GROUP'] == 1) {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'] = $company_library[$row['BUYER_ID']];
		} else {
			$sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'] = $buyer_arr[$row['BUYER_ID']];
		}
	}
	//end for sales order information

	$sql_dmnd = sql_select("SELECT DEMAND_SYSTEM_NO, DEMAND_DATE, LOCATION_ID, FLOOR_NAME, REMARKS FROM PPL_YARN_DEMAND_ENTRY_MST WHERE ID=" . $data[0]);
	$dmnd_data = array();
	foreach ($sql_dmnd as $row) {
		$dmnd_data['DEMAND_NO'] = $row['DEMAND_SYSTEM_NO'];
		$dmnd_data['DEMAND_DATE'] = change_date_format($row['DEMAND_DATE']);
		$dmnd_data['LOCATION_ID'] = $location_arr[$row['LOCATION_ID']];
		$dmnd_data['FLOOR_NAME'] = $floor_arr[$row['FLOOR_NAME']];
		$dmnd_data['REMARKS'] = $row['REMARKS'];
	}
?>
	<style>
		.demand td {
			font-size: 14px;
		}
	</style>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180" align="right">&nbsp;</td>
				<td>
					<table width="1220" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:x-large"><strong><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td align="center" style="font-size:14px"><?
																		$nameArray_com = sql_select("SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID='" . $data[1] . "' AND STATUS_ACTIVE=1 AND IS_DELETED=0");
																		$loc = '';
																		foreach ($nameArray_com as $result) {
																			if ($result['PLOT_NO'] != '') {
																				$loc .= $result['PLOT_NO'];
																			}

																			if ($result['LEVEL_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['PLOT_NO'];
																				} else {
																					$loc .= $result['PLOT_NO'];
																				}
																			}

																			if ($result['ROAD_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['ROAD_NO'];
																				} else {
																					$loc .= $result['ROAD_NO'];
																				}
																			}

																			if ($result['BLOCK_NO'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['BLOCK_NO'];
																				} else {
																					$loc .= $result['BLOCK_NO'];
																				}
																			}

																			if ($result['CITY'] != '') {
																				if ($loc != '') {
																					$loc .= ', ' . $result['CITY'];
																				} else {
																					$loc .= $result['CITY'];
																				}
																			}
																		}
																		echo $loc;
																		?></td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><b><u>Daily Yarn Demand</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="1500" style="margin-top:10px;" class="demand">
			<tr>
				<td width="70"><b>Demand No</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="150"><? echo $dmnd_data['DEMAND_NO']; ?></td>

				<td width="80"><b>Demand Date</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="100"><? echo $dmnd_data['DEMAND_DATE']; ?></td>

				<td width="60"><b>Location</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="250"><? echo $dmnd_data['LOCATION_ID']; ?></td>

				<td width="60"><b>Floor</b>
				<td>
				<td width="10"><b>:</b></td>
				<td width="690"><? echo $dmnd_data['FLOOR_NAME']; ?></td>
			</tr>
			<tr>
				<td><b>Remarks</b>
				<td>
				<td><b>:</b></td>
				<td colspan="10"><? echo $dmnd_data['REMARKS']; ?></td>
			</tr>
		</table>
		<table style="margin-top:10px;" width="1500" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Sales Order No.</th>
				<th width="100">Sales Job/Booking No</th>
				<th width="100">Customer</th>
				<th width="100">Cust. Buyer</th>
				<th width="80">Knitting Com.</th>
				<th width="80">Program No</th>
				<th width="50">Reqsn. No</th>
				<th width="50">Count</th>
				<th width="110">Composition</th>
				<th width="100">Supplier</th>
				<th width="70">Type</th>
				<th width="70">Color</th>
				<th width="50">Lot</th>
				<th width="50">Brand</th>
				<th width="80">Demand Qnty</th>
				<th width="60">No of Cone</th>
				<th width="70">No of Ctn</th>
				<th>Remark</th>
			</thead>
			<?
			$i = 1;
			foreach ($nameArray as $row) {
				$PROG_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['PROG_NO'];
				$SALES_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['SALES_NO'];
				$BOOKING_NO = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['BOOKING_NO'];
				$CUSTOMER = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER'];
				$CUSTOMER_BUYER = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'];
				$KNITTING_SOURCE = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_SOURCE'];
				$KNITTING_PARTY = $sales_data[$row['REQUISITION_NO']][$row['PROD_ID']]['KNITTING_PARTY'];
				$KNITTING_COMPANY = '';
				if ($KNITTING_SOURCE == 1) {
					$KNITTING_COMPANY = $company_library[$KNITTING_PARTY];
				} elseif ($KNITTING_SOURCE == 3) {
					$KNITTING_COMPANY = $supplier_arr[$KNITTING_PARTY];
				}
			?>
				<tr>
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><?php echo $SALES_NO; ?></td>
					<td width="100" align="center"><?php echo $BOOKING_NO; ?></td>
					<td width="100" align="center">
						<p><?php echo $CUSTOMER; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $CUSTOMER_BUYER; ?></p>
					</td>
					<td width="80">
						<p><? echo $KNITTING_COMPANY; ?></p>
					</td>
					<td width="80" align="center"><? echo $PROG_NO; ?></td>
					<td width="60" align="center"><? echo $row['REQUISITION_NO']; ?></td>
					<td width="50" align="center"><? echo $product_details_array[$row['PROD_ID']]['count']; ?></td>
					<td width="110" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['comp']; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['suppl']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['type']; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['color']; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['lot']; ?></p>
					</td>
					<td width="50" align="center">
						<p><? echo $product_details_array[$row['PROD_ID']]['brand']; ?></p>
					</td>
					<td width="80" align="right"><? echo decimal_format($row['YARN_DEMAND_QNTY'], '1', ','); ?></td>
					<td width="60" align="right"><? echo decimal_format($row['CONE_QTY'], '1', ','); ?></td>
					<td width="70" align="right"><? echo decimal_format($row['CTN_QTY'], '1', ','); ?></td>
					<td align="right">
						<p><? echo $row['REMARKS']; ?></p>
					</td>
				</tr>
			<?
				$tot_demand_qnty += $row['YARN_DEMAND_QNTY'];
				$tot_cone_qnty 	 += $row['CONE_QTY'];
				$tot_ctn_qnty 	 += $row['CTN_QTY'];
				$i++;
			}
			?>
			<tfoot>
				<th colspan="15" align="right"><b>Total</b></th>
				<th align="right"><? echo decimal_format($tot_demand_qnty, '1', ','); ?></th>
				<th align="right"><? echo decimal_format($tot_cone_qnty, '1', ','); ?></th>
				<th align="right"><? echo decimal_format($tot_ctn_qnty, '1', ','); ?></th>
				<th></th>
			</tfoot>
		</table>
		<br>
		<table style="margin-top:10px;" width="600" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
			<thead>
				<th width="100">Requsition No.</th>
				<th width="100">Lot</th>
				<th width="100">Requsition Qty</th>
				<th width="100">Pre Demand Qty</th>
				<th width="100">Current Demand Qty</th>
				<th width="100">Balance Qty</th>
			</thead>
			<tbody>
				<?
				//$req_data[$row['REQUISITION_NO']][$row['PROD_ID']]['REQ_QTY']
				foreach ($req_data as $k_req => $v_req) {
					foreach ($v_req as $k_prd => $row) {
						$pre_dmnd_qty = 0.00;
						$balance_qty = $row['REQ_QTY'] - $row['DMND_QTY'];
				?>
						<tr>
							<td><? echo $k_req; ?></td>
							<td><? echo $product_details_array[$k_prd]['lot']; ?></td>
							<td align="right"><? echo decimal_format($row['REQ_QTY'], '1', ','); ?></td>
							<td align="right"><? echo decimal_format($pre_dmnd_qty, '1', ','); ?></td>
							<td align="right"><? echo decimal_format($row['DMND_QTY'], '1', ','); ?></td>
							<td align="right"><? echo decimal_format($balance_qty, '1', ','); ?></td>
						</tr>
				<?
						$total_req_qty += $row['REQ_QTY'];
						$total_pre_qty += $pre_dmnd_qty;
						$total_dmnd_qty += $row['DMND_QTY'];
						$total_bal_qty += $balance_qty;
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2">Total</th>
					<th><? echo decimal_format($total_req_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_pre_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_dmnd_qty, '1', ','); ?></th>
					<th><? echo decimal_format($total_bal_qty, '1', ','); ?></th>
				</tr>
			</tfoot>
		</table>
		<?
		echo signature_table(40, $data[1], "1180px");
		?>
	</div>
<?
	exit();
}
?>