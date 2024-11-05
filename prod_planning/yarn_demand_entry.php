<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Demand Entry
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	19-08-2013
Updated by 		: 	MD. Didarul Alam	
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Requisition Entry", "../", 1, 1, '', '', '');

?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';

	function fnc_yarn_demand_entry(operation) {
		if (operation == 4) {
			print_report($('#update_id').val() + "**" + $('#cbo_company_id').val() + "**" + $('#txt_demand_date').val(), "print", "requires/yarn_demand_entry_controller")
			return;
		}

		if (operation == 6) {
			print_report($('#update_id').val() + "**" + $('#cbo_company_id').val(), "print2", "requires/yarn_demand_entry_controller")
			return;
		}

		if (operation == 7) {
			print_report($('#update_id').val() + "**" + $('#cbo_company_id').val(), "print3", "requires/yarn_demand_entry_controller")
			return;
		}

		if (operation == 8) {
			print_report($('#update_id').val() + "**" + $('#cbo_company_id').val() + "**" + $('#txt_demand_date').val() + "**" + $('#txt_demand_no').val() + "**" + $('#txt_requisition_no').val() + "**" + $('#txt_remarks').val(), "print4", "requires/yarn_demand_entry_controller")
			return;
		}

		if (form_validation('cbo_company_id*txt_demand_date*txt_requisition_no', 'Comapny Name*Demand Date*Requisition No') == false) {
			return;
		}

		//for date compare
		var current_date = new Date('<?php echo date('Y-m-d'); ?>');
		var split_date = $('#txt_demand_date').val().split('-');
		var demand_date = new Date(split_date[2] + '-' + split_date[1] + '-' + split_date[0]);

		if (current_date.getTime() != demand_date.getTime()) {
			alert('Demand date forword/backword date not allow.');
			return;
		}

		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_demand_no*cbo_company_id*cbo_location*txt_demand_date*txt_requisition_no*txt_demand_qnty*txt_remarks*save_data*update_id*update_dtls_id*cbo_floor_name*cbo_knitting_source*cbo_knitting_company', "../");

		freeze_window(operation);

		http.open("POST", "requires/yarn_demand_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_demand_entry_Reply_info;
	}

	function fnc_yarn_demand_entry_Reply_info() {
		if (http.readyState == 4) {
			//alert(http.responseText);return;
			var reponse = trim(http.responseText).split('**');

			show_msg(reponse[0]);

			if ((reponse[0] == 0 || reponse[0] == 1)) {
				reset_form('', 'demand_items_list_view', 'txt_requisition_no*txt_demand_qnty*txt_yarn_blance_qnty*save_data*update_dtls_id*requisitionqty', '', 'disable_enable_fields(\'cbo_company_id*cbo_knitting_company\',1)');
				var cbo_company_id = $('#cbo_company_id').val();
				$('#txt_demand_no').val(reponse[1]);
				$('#update_id').val(reponse[2]);
				show_list_view(reponse[2] + "**" + cbo_company_id, 'show_demand_listview', 'demand_items_list_view', 'requires/yarn_demand_entry_controller', '');
			} else if (reponse[0] == 2) {
				if (reponse[5] == 1) {
					reset_form('demandEntry_1', 'demand_items_list_view', '', '', 'disable_enable_fields(\'cbo_company_id*cbo_knitting_company\',0)');
				} else {
					reset_form('', 'demand_items_list_view', 'txt_requisition_no*txt_demand_qnty*save_data*update_dtls_id*requisitionqty', '', 'disable_enable_fields(\'cbo_company_id\',1)');
					var cbo_company_id = $('#cbo_company_id').val();
					$('#txt_demand_no').val(reponse[1]);
					$('#update_id').val(reponse[2]);
					show_list_view(reponse[2] + "**" + cbo_company_id, 'show_demand_listview', 'demand_items_list_view', 'requires/yarn_demand_entry_controller', '');
				}
			} else if (reponse[0] == 17) {
				alert("Demand Qnty Exceeds Requisition Qnty");
				return;
			} else if (reponse[0] == 18) {
				alert(reponse[1]);
				release_freezing();
				return;
			}

			set_button_status(reponse[3], permission, 'fnc_yarn_demand_entry', 1, reponse[4]);
			release_freezing();
		}
	}

	function openmypage_reqsn() {
		var companyID = $('#cbo_company_id').val();
		var txt_requisition_no = $('#txt_requisition_no').val();
		var save_data = $('#save_data').val();
		var knitt_source = $('#cbo_knitting_source').val();
		var knitt_company = $('#cbo_knitting_company').val();
		var hdn_previous_demand_qnty = $('#hdn_previous_demand_qnty').val();
		var operation_type = $('#operation_type').val();


		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}

		var page_link = 'requires/yarn_demand_entry_controller.php?action=yarn_reqsn_popup&txt_requisition_no=' + txt_requisition_no + '&companyID=' + companyID + '&save_data=' + save_data + '&knitting_source=' + knitt_source + '&knitting_company=' + knitt_company + '&hdn_previous_demand_qnty=' + hdn_previous_demand_qnty + '&operation_type=' + operation_type;;
		var title = 'Yarn Requisition Info';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=390px,center=1,resize=1,scrolling=0', '');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var reqsn_no = this.contentDoc.getElementById("reqsn_no").value;
			var save_data = this.contentDoc.getElementById("save_data").value;
			var tot_demand_qnty = this.contentDoc.getElementById("tot_demand_qnty").value * 1;
			var total_reqisiont_qty = this.contentDoc.getElementById("total_req_qty").value * 1;
			var totaldemandqty = this.contentDoc.getElementById("totaldemandqty").value * 1;
			var hdnprev_demandqty = this.contentDoc.getElementById("hdnprev_demandqty").value * 1;

			if (hdnprev_demandqty != "") {
				var actualtotal_demand = (totaldemandqty - hdnprev_demandqty + tot_demand_qnty);
				var cumbalance = total_reqisiont_qty - actualtotal_demand;
			} else {
				var cumbalance = ((total_reqisiont_qty - totaldemandqty) - tot_demand_qnty);
			}
			//var remark=this.contentDoc.getElementById("tot_remark").value;
			$('#txt_requisition_no').val(reqsn_no);
			$('#save_data').val(save_data);
			$('#requisitionqty').val(total_reqisiont_qty);
			$('#txt_demand_qnty').val(tot_demand_qnty);
			$('#txt_yarn_blance_qnty').val(cumbalance);
			//$('#txt_remark').val(remark);
		}
	}

	function openmypage_demandNo() {
		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		} else {
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'Demand No Info';
			var page_link = 'requires/yarn_demand_entry_controller.php?cbo_company_id=' + cbo_company_id + '&action=systemId_popup';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=350px,center=1,resize=0,scrolling=0', '')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var demand_id = this.contentDoc.getElementById("hidden_sys_id").value;

				reset_form('demandEntry_1', 'demand_items_list_view', '', '', '', '');
				get_php_form_data(demand_id, "populate_data_from_demand_update", "requires/yarn_demand_entry_controller");
				show_list_view(demand_id + "**" + cbo_company_id, 'show_demand_listview', 'demand_items_list_view', 'requires/yarn_demand_entry_controller', '');
			}
		}
	}
	/*function fn_test(loca_value)
	{
		alert(loca_value);
	}*/
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../", $permission); ?>
		<fieldset style="width:850px;"><br>
			<legend>Demand Entry</legend>
			<form name="demandEntry_1" id="demandEntry_1">
				<fieldset style="width:820px;">
					<table width="810" align="center" border="0">
						<tr>
							<td colspan="3" align="right"><strong>Demand No</strong></td>
							<td colspan="3" align="left">
								<input type="text" name="txt_demand_no" id="txt_demand_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_demandNo();" readonly />
							</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Company Name</td>
							<td>
								<?
								echo create_drop_down("cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/yarn_demand_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/yarn_demand_entry_controller', document.getElementById('cbo_knitting_source').value+'_'+this.value, 'load_drop_down_knitting_com','knitting_com');get_php_form_data(this.value, 'company_wise_report_button_setting','requires/yarn_demand_entry_controller');");
								?>
							</td>
							<td> Knitting Source </td>
							<td>
								<?
								echo create_drop_down("cbo_knitting_source", 150, $knitting_source, "", 0, "-- Select --", $selected, "load_drop_down( 'requires/yarn_demand_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');load_drop_down('requires/yarn_demand_entry_controller', document.getElementById('cbo_knitting_company').value, 'load_drop_down_location', 'location_td' );", "", '1,3');
								?>
							</td>
							<td>Knitting Com</td>
							<td id="knitting_com">
								<?
								echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 1, "");
								?>
							</td>
						</tr>
						<tr>
							<td>Location</td>
							<td id="location_td">
								<?
								echo create_drop_down("cbo_location", 152, $blank_array, "", 1, "-- Select Location --", 0, "");
								?>
							</td>
							<td>Floor Name</td>
							<td id="td_floor_name">
								<? echo create_drop_down("cbo_floor_name", 152, $blank_array, "", 1, "-- Select Floor --", 0, ""); ?>
							</td>
							<td class="must_entry_caption">Demand Date</td>
							<td>
								<input type="text" name="txt_demand_date" id="txt_demand_date" class="datepicker" style="width:140px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" readonly />
							</td>
						</tr>
						<tr>
							<td>Remarks</td>
							<td align="left" colspan="3">
								<input type="text" name="txt_remarks" id="txt_remarks" style="width:395px" class="text_boxes" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset style="width:810px; margin-top:10px">
					<legend>Requisition Details</legend>
					<table cellpadding="1" cellspacing="1" border="0" width="100%">
						<tr>
							<td width="110" class="must_entry_caption">Requisition No</td>
							<td width="150" align="left">
								<input type="text" name="txt_requisition_no" id="txt_requisition_no" style="width:140px" class="text_boxes" readonly placeholder="Single Click" onClick="openmypage_reqsn()" />
							</td>

							<td width="100">Total Req. Qty</td>
							<td width="80" align="left">
								<input type="text" name="requisitionqty" id="requisitionqty" style="width:80px" class="text_boxes" value="" readonly />
							</td>

							<td width="110">Demand Qnty</td>
							<td align="left">
								<input type="text" name="txt_demand_qnty" id="txt_demand_qnty" style="width:90px" class="text_boxes_numeric" placeholder="Display" disabled />
								<input type="hidden" name="hdn_previous_demand_qnty" id="hdn_previous_demand_qnty" />
							</td>

							<td width="110">Cum .Balance Qnty</td>
							<td align="left">
								<input type="text" name="txt_yarn_blance_qnty" id="txt_yarn_blance_qnty" style="width:90px" class="text_boxes_numeric" placeholder="Display" disabled value="" />
							</td>

							<!--<td width="110">Remark</td>
                            <td align="left">						 
                                <input type="text" name="txt_remark" id="txt_remark" style="width:110px" class="text_boxes" placeholder="Display" disabled/>
                            </td> -->
						</tr>
					</table>
				</fieldset>
				<table width="810">
					<tr>
						<td colspan="4" align="center" class="button_container">
							<?
							echo load_submit_buttons($permission, "fnc_yarn_demand_entry", 0, 0, "reset_form('demandEntry_1','demand_items_list_view','','','disable_enable_fields(\'cbo_company_id\',0)');", 1);
							?>

							<input type="hidden" name="operation_type" id="operation_type" />
							<input type="hidden" name="save_data" id="save_data" />
							<input type="hidden" name="update_id" id="update_id" />
							<input type="hidden" name="update_dtls_id" id="update_dtls_id" />
							<input type="button" class="formbutton" id="print" style="width:80px; display: none;" value="Print" onClick="fnc_yarn_demand_entry(4)" />
							<input type="button" class="formbutton" id="print2" style="width:100px; display: none;" value="Print Booking 2" onClick="fnc_yarn_demand_entry(6)" />
							<input type="button" class="formbutton" id="print3" style="width:80px; display: none;" value="Print Amana" onClick="fnc_yarn_demand_entry(7)" />
							<input type="button" class="formbutton" id="print4" style="width:80px; display: none;" value="Print 4" onClick="fnc_yarn_demand_entry(8)" />
						</td>
					</tr>
				</table>
			</form>
			<div id="demand_items_list_view" style="margin-top:10px"></div>
		</fieldset>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>

</html>