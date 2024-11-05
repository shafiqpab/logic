<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create KNit Garments Order Entry
Functionality	:	
JS Functions	:
Created by		:	Md. Rabiul islam
Creation date 	: 	18-10-2012
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:

-----------------------------------------------------*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:../../login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sales Terget Info", "../../", 1, 1, $unicode, '', '');
?>
<script>
	//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

	var permission = '<? echo $permission; ?>';

	function fnc_sales_target_entry(operation) {
		if (form_validation('cbo_company_id*cbo_buyer_name*cbo_team_leader*cbo_starting_month*cbo_starting_year', 'Company Name*Buyer*Team Leader*text designation*starting month*starting year') == false) {
			return;
		} else {
			var cbo_excut_source = $('#cbo_excut_source').val() * 1;
			if (cbo_excut_source == 2) {
				if (form_validation('cbo_brand_id', 'Brand') == false) {
					return;
				}
			}
			var target_month = $("#cbo_starting_month").val() * 1;
			var target_year = $("#cbo_starting_year").val() * 1;

			var k = 1;
			var month_data = '';
			for (var i = 0; i < 12; i++) {
				if (k < 13) {
					var month = target_month + i;
					var yy = target_year;



					month_data += "*month_" + String(month) + String(yy) + "*qty_" + String(month) + String(yy) + "*val_" + String(month) + String(yy) + "*mint_" + String(month) + String(yy) + "*cm_" + String(month) + String(yy) + "*cmval_" + String(month) + String(yy) + "*rmval_" + String(month) + String(yy) + "*actualmargin_" + String(month) + String(yy) + "*savedata_1" + "_" + String(month) + "_" + String(yy) + "*savedata_2" + "_" + String(month) + "_" + String(yy) + "*savedata_3" + "_" + String(month) + "_" + String(yy);
					if (month == 12) {
						target_month = 0;
						i = 0;
						target_year = target_year + 1;
					}
				}
				k++;
			}
			var data = "action=save_update_delete_mst&operation=" + operation + get_submitted_data_string('update_id*cbo_company_id*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_starting_month*cbo_starting_year*txt_total_qty*txt_total_val*txt_total_alo_prcnt*cbo_brand_id' + month_data, "../../");
			//alert(data);
			freeze_window(operation);

			http.open("POST", "requires/sales_target_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sales_target_entry_reponse;
		}
	}

	function fnc_sales_target_entry_reponse() {
		if (http.readyState == 4) {
			var reponse = http.responseText.split('**');
			show_msg(trim(reponse[0]));
			$("#update_id").val(reponse[1]);
			permission = '1_1_2_1';
			if (reponse[0] == 0) {
				set_button_status(1, permission, 'fnc_sales_target_entry', 1);
			}
			/*else if(reponse[1]==1)
			{
				set_button_status(1, permission, 'fnc_sales_target_entry',1);
			}*/
			else {
				set_button_status(0, permission, 'fnc_sales_target_entry', 1);
			}
			//new development
			//if(reponse[0]==1 && reponse[1]==1){set_button_status(0, permission, 'fnc_sales_target_entry',1); reset_form('salesTargetForm','','');}
			release_freezing();
		}
	}

	function fn_calculate() {
		var target_month = $("#cbo_starting_month").val() * 1;
		var target_year = $("#cbo_starting_year").val() * 1;

		var tot_qty = tot_val = tot_alo_prcnt = 0;
		var k = 1;
		for (var i = 0; i < 12; i++) {
			if (k < 13) {
				var month = target_month + i;
				var yy = target_year;

				tot_qty += $("#qty_" + String(month) + String(yy)).val() * 1;
				tot_val += $("#val_" + String(month) + String(yy)).val() * 1;
				tot_alo_prcnt += $("#mint_" + String(month) + String(yy)).val() * 1;

				if (month == 12) {
					target_month = 0;
					i = 0;
					target_year = target_year + 1;
				}
			}
			k++;
		}

		$("#txt_total_qty").val(tot_qty);
		$("#txt_total_val").val(tot_val);
		$("#txt_total_alo_prcnt").val(tot_alo_prcnt);
	}

	function fnc_load_sales_target_data() {
		var cbo_excut_source = $('#cbo_excut_source').val() * 1;
		if (form_validation('cbo_company_id*cbo_buyer_name*cbo_team_leader*cbo_starting_month*cbo_starting_year', 'Company Name*Buyer*Team Leader*text designation*starting month*starting year') == false) {
			return;
		} else {
			if (cbo_excut_source == 2) {
				if (form_validation('cbo_brand_id', 'Brand') == false) {
					return;
				}
			}
			var fill_data = document.getElementById('cbo_company_id').value + '_' + document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_agent').value + '_' + document.getElementById('cbo_team_leader').value + '_' + document.getElementById('cbo_starting_month').value + '_' + document.getElementById('cbo_starting_year').value + '_' + document.getElementById('cbo_brand_id').value;

			var data = "action=generate_list_view&operation=" + operation + '&data=' + fill_data;
			//alert(data);
			freeze_window(operation);
			http.open("POST", "requires/sales_target_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_load_sales_target_data_reponse;
		}
	}

	function fnc_load_sales_target_data_reponse() {
		if (http.readyState == 4) {

			var reponse = http.responseText.split('*_*');
			$("#list_view").html(reponse[0]);

			permission = '1_1_2_1';
			if (reponse[1]) {
				set_button_status(1, permission, 'fnc_sales_target_entry', 1);
			} else {
				set_button_status(0, permission, 'fnc_sales_target_entry', 1);
			}
			set_all_onclick();
			release_freezing();
		}
	}

	function openTarget(type, monthid, year, id) {
		var cbo_is_popup_yes = $('#cbo_is_popup_yes').val() * 1;
		if (cbo_is_popup_yes != 1) return;
		var defect_qty = $('#' + id).val();
		var save_data = $('#savedata_' + type + '_' + monthid + '_' + year).val();
		var all_defect_id = $('#allnatureid_' + type + '_' + monthid + '_' + year).val();


		var title = '';
		if (type == 1) {
			title = 'Qty Target';
		} else if (type == 2) {
			title = 'Value Target';
		} else if (type == 3) {
			title = 'Target Mint';
		}

		var page_link = 'requires/sales_target_controller.php?month=' + monthid + '&year=' + year + '&save_data=' + save_data + '&defect_qty=' + defect_qty + '&all_defect_id=' + all_defect_id + '&type=' + type + '&action=target_data';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=400px,center=1,resize=1,scrolling=0', '../');

		emailwindow.onclose = function() {
			var save_string = this.contentDoc.getElementById("save_string").value;
			var tot_defectQnty = this.contentDoc.getElementById("tot_defectQnty").value;
			var all_defect_id = this.contentDoc.getElementById("all_defect_id").value;
			var defect_type_id = this.contentDoc.getElementById("defect_type_id").value;
			$('#savedata_' + type + '_' + monthid + '_' + year).val(save_string);
			$('#allnatureid_' + type + '_' + monthid + '_' + year).val(all_defect_id);
			$('#' + id).val(tot_defectQnty);
			var target_month = $("#cbo_starting_month").val() * 1;
			var target_year = $("#cbo_starting_year").val() * 1;

			var tot_qty = tot_val = tot_alo_prcnt = 0;
			var k = 1;
			for (var i = 0; i < 12; i++) {
				if (k < 13) {
					var month = target_month + i;
					var yy = target_year;

					tot_qty += $("#qty_" + String(month) + String(yy)).val() * 1;
					tot_val += $("#val_" + String(month) + String(yy)).val() * 1;
					tot_alo_prcnt += $("#mint_" + String(month) + String(yy)).val() * 1;

					if (month == 12) {
						target_month = 0;
						i = 0;
						target_year = target_year + 1;
					}
				}
				k++;
			}

			$("#txt_total_qty").val(tot_qty);
			$("#txt_total_val").val(tot_val);
			$("#txt_total_alo_prcnt").val(tot_alo_prcnt);
			release_freezing();
		}

	}

	function fuc_select_month(comp_id) {
		get_php_form_data(comp_id, "select_month_from_variable", "requires/sales_target_controller");
	}

	function get_buyer_config(buyer_id) {
		load_drop_down('requires/sales_target_controller', buyer_id, 'load_drop_down_brand', 'brand_td');
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs("../../", $permission);  ?>
		<fieldset style=" width:860px;">
			<legend>Sales Target</legend>
			<form name="salesTargetForm" id="salesTargetForm" autocomplete="off">
				<table cellpadding="0" cellspacing="">
					<tr>
						<td width="850" align="center">
							<fieldset style="width:850px;">
								<table width="800" cellspacing="2" cellpadding="0" border="0">
									<tr>
										<td width="100" align="right" class="must_entry_caption">Company Name</td>
										<td width="80"><? echo create_drop_down("cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) order by comp.company_name", "id,company_name", 1, "-- Select --", 0, "fuc_select_month(this.value)", 0); ?></td>
										<td width="80" align="right" class="must_entry_caption">Buyer Name</td>
										<td width="80" id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 172, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "fnc_load_sales_target_data();get_buyer_config(this.value)"); ?> </td>
										<td>Brand</td>
										<td id="brand_td"><? echo create_drop_down("cbo_brand_id", 172, $blank_array, '', 1, "--Brand--", $selected, "fnc_load_sales_target_data();"); ?>

									</tr>
									<tr>
										<td width="100" align="right" class="must_entry_caption">Team Leader</td>
										<td width="86"><?
														$teamArr = array();
														$teamSql = sql_select("select id, team_name, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name");
														foreach ($teamSql as $row) {
															$teamArr[$row[csf("id")]] = $row[csf("team_leader_name")] . '[' . $row[csf("team_name")] . ']';
														}
														unset($teamSql);

														echo create_drop_down("cbo_team_leader", 172, $teamArr, "", 1, "-- Select Team --", 0, "fnc_load_sales_target_data();"); ?> </td>
										<td align="right">Designation </td>
										<td><input style="width:160px;" type="text" class="text_boxes" name="text_designation_value" id="text_designation_value" /></td>
										<td align="right" class="must_entry_caption"> Starting Month</td>
										<td><? echo create_drop_down("cbo_starting_month", 172, $months, "", 1, "-- Select Month --", 0, "fnc_load_sales_target_data()", 1); ?></td>
									</tr>
									<tr>
										<td align="right" class="must_entry_caption">Year</td>
										<td>
											<?
											$c_year = date("Y", time());
											$year_array = array();
											for ($i = -10; $i < 11; $i++) {
												$opt_yr = $c_year + $i;
												$year_array[$opt_yr] = $opt_yr;
											}
											echo create_drop_down("cbo_starting_year", 172, $year_array, "", 1, "-- Select Year --", 0, "fnc_load_sales_target_data();");
											?>
										</td>
										<td align="right">Agent</td>
										<td><? echo create_drop_down("cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select --", 0, "fnc_load_sales_target_data();", 0); ?></td>
									</tr>
								</table>
							</fieldset>
					<tr>
						<td align="center">
							<input type="hidden" name="cbo_excut_source" id="cbo_excut_source" value="0">
							<input type="hidden" name="cbo_is_popup_yes" id="cbo_is_popup_yes" value="0">
							<?
							echo load_submit_buttons($permission, "fnc_sales_target_entry", 0, 0, "reset_form('orderentry_1','','')", 1);
							?>
						</td>
					</tr>
					<tr>
						<td id="list_view"></td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>