<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create MRR Auditing Report
Functionality	:	
JS Functions	:
Created by		:	Shakil Ahmed
Creation date 	: 	15-09-2020
Updated by 		: 	Rakib 
Update date		: 	28-10-20
QC Performed BY	:		
QC Date			:	
Comments		:

*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] 	= $permission;
$menu_id 						= $_SESSION['menu_id'];

/*========== user credential  ========*/
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("select unit_id as COMPANY_ID, item_cate_id as ITEM_CATE_ID, company_location_id as COMPANY_LOCATION_ID, store_location_id as STORE_LOCATION_ID from user_passwd where id=$user_id");

$category_credential_id = $userCredential[0]['ITEM_CATE_ID'];

if ($category_credential_id != '') {
	$category_credential_cond = " and category_id in($category_credential_id)";
}
//---------------------------------------------------------------------------------------------
echo load_html_head_contents("MRR Auditing Report", "../../", 1, 1, '', '', '');

$permitted_item_category = return_field_value("item_cate_id", "user_passwd", "id='" . $_SESSION['logic_erp']['user_id'] . "'");
$approval_setup			 = is_duplicate_field("page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0");
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated() {

		 
		var mrr_no=document.getElementById("txt_mrr_no").value;
		var challan_no=document.getElementById("txt_challan_no").value;
		var wo_id=document.getElementById("txt_wo_no").value;		
		var pi_no=document.getElementById("txt_pi_no").value;		 
		if (mrr_no == '' && challan_no == '' &&  wo_id == '' &&  pi_no =='') {
			 
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company Name*Date Range*Date Range') == false) {
				return;
			}
		}
		else{
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
		}

		var data = "action=report_generate&" + get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_store_name*cbo_item_category_id*txt_challan_no*txt_challan_id*txt_mrr_no*txt_date_from*txt_date_to*cbo_suppler_name*txt_wo_no*txt_wo_id*txt_pi_no*cbo_audit_type*cbo_date_basis*cbo_year', "../../");
		freeze_window();
		http.open("POST", "requires/mrr_auditing_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_for_audit_unaudit() {

		/*if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		if ($('#txt_mrr_no').val() == '' && $('#txt_challan_id').val() == '' && $('#txt_wo_id').val() == '') {
			if (form_validation('txt_date_from*txt_date_to', 'Date Range*Date Range') == false) {
				return;
			}
		}*/

		  
		var mrr_no=document.getElementById("txt_mrr_no").value;
		var challan_no=document.getElementById("txt_challan_no").value;
		var wo_id=document.getElementById("txt_wo_no").value;		
		var pi_no=document.getElementById("txt_pi_no").value;		 
		if (mrr_no == '' && challan_no == '' &&  wo_id == '' &&  pi_no =='') {
			 
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company Name*Date Range*Date Range') == false) {
				return;
			}
		}
		else{
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
		}

		var data = "action=report_generate&" + get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_store_name*cbo_item_category_id*txt_challan_no*txt_challan_id*txt_mrr_no*txt_date_from*txt_date_to*cbo_suppler_name*txt_wo_no*txt_wo_id*txt_pi_no*cbo_audit_type*cbo_date_basis', "../../");
		http.open("POST", "requires/mrr_auditing_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}


	function fn_report_generated_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			// document.getElementById('report_container').innerHTML = report_convert_button('../../');
			// append_report_checkbox('table_body', 1);
			//var tableFilters = { col_0: "none",col_3: "select", display_all_text: " --- All Category ---" }
			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = {
				col_50: "none",
				col_operation: {
					id: ["value_mrr_total_dlr_qnty", "value_mrr_total_bdt_qnty"],
					//fixed_headers: true,  
					col: [11, 12],
					//decimal_precision: [2,2],
					operation: ["sum", "sum"],
					write_method: ["innerHTML", "innerHTML"],
				}
			}
			setFilterGrid("table_body", -1, tableFilters);
			show_msg('3');
			release_freezing();
		}
		// release_freezing();
	}

	function new_window() {
		// document.getElementById('list_view').style.overflow='auto';
		// document.getElementById('list_view').style.maxHeight='none';
		$('#table_body tbody').find('tr:first').hide();
		$('.txtRemarksInput input[type=text]').hide();
		$('.txtRemarksInput span').css('display', 'block');
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		$('.txtRemarksInput input[type=text]').show();
		$('.txtRemarksInput span').css('display', 'none');
		$('#table_body tbody').find('tr:first').show();
		// document.getElementById('list_view').style.overflowY='scroll';
		// document.getElementById('list_view').style.maxHeight='300px';	
	}


	function fn_audited_un_audited() {
		freeze_window();

		var data_all = '';
		var error = 1;

		$("input[name=chkAudit]").each(function(index, element) {
			if ($(this).prop('checked') == true) {
				error = 0;
				var idd = $(this).attr('id').split('_');

				if (data_all == '') {
					data_all = $('#hiddenid_' + idd[1]).val() + "**" + $('#txtRemarks_' + idd[1]).val() + "**" + $('#hiddenEntryForm_' + idd[1]).val();
				} else {
					data_all = data_all + "__" + $('#hiddenid_' + idd[1]).val() + "**" + $('#txtRemarks_' + idd[1]).val() + "**" + $('#hiddenEntryForm_' + idd[1]).val();
				}
			}
		});
		// alert(data_all); return;
		if (error == 1) {
			release_freezing();
			alert('No data selected');
		} else {
			var data = "action=save_update_delete&" + get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_store_name*cbo_item_category_id*txt_challan_no*txt_mrr_no*txt_date_from*txt_date_to*cbo_suppler_name*txt_wo_no*txt_pi_no*cbo_audit_type*cbo_date_basis', "../../") + "&data_all=" + data_all;
			http.open("POST", "requires/mrr_auditing_report_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = function() {
				if (http.readyState == 4) {
					var response = trim(http.responseText).split('**');
					if (response[0] == 0) {
						fn_report_generated_for_audit_unaudit();
						release_freezing();
					} else if (response[0] == 50) {
						$("input[name=chkAudit]").each(function(index, element) {
							$(this).removeAttr('checked');
						});
						alert(response[1]);
						release_freezing();
					} else {
						show_msg(trim(response[0]));
						release_freezing();
					}
				}
				release_freezing();
			}
		}
	}

	function generate_report_file(data, action, page) {
		window.open("requires/bill_processing_controller.php?data=" + data + '&action=' + action, true);
	}

	function openmypage_challan() {
		var cbo_company_name = $("#cbo_company_name").val();
		var challan_no = $("#txt_challan_no").val();
		var challan_id = $("#txt_challan_id").val();
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/mrr_auditing_report_controller.php?action=challan_popup&cbo_company_name=' + cbo_company_name + '&selected_challan=' + challan_no + '&selected_challan_id=' + challan_id, 'Challan Popup', 'width=640px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var selected_name = this.contentDoc.getElementById("selected_name").value;
			var selected_id = this.contentDoc.getElementById("selected_id").value;
			$("#txt_challan_no").val(selected_name);
			$("#txt_challan_id").val(selected_id);
		}
	}

	function openmypage_wo() {
		var cbo_company_name = $("#cbo_company_name").val();
		var wo_no = $("#txt_wo_no").val();
		var wo_id = $("#txt_wo_id").val();
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/mrr_auditing_report_controller.php?action=wo_popup&cbo_company_name=' + cbo_company_name + '&selected_challan=' + wo_no + '&selected_challan_id=' + wo_id, 'Work Order Popup', 'width=440px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var selected_name = this.contentDoc.getElementById("selected_name").value;
			var selected_id = this.contentDoc.getElementById("selected_id").value;
			$("#txt_wo_no").val(selected_name);
			$("#txt_wo_id").val(selected_id);
		}
	}

	function check_all() {
		$("input[name=chkAudit]").each(function(index, element) {

			if ($('#all_check').prop('checked') == true)
				$(this).attr('checked', 'true');
			else
				$(this).removeAttr('checked');
		});
	}

	function show_mrr_dtls(data) {

		var d = data.split("__");
		// alert(d[7]+"_"+d[9]);
		// Entry Form Check
		if (d[7] == 1) {
			// Yarn Receive
			//yarn 1
			// print button 
			print_report(d[0] + '*' + d[2], "yarn_receive_print", "../requires/yarn_receive_controller");
		}
		else if (d[7] == 24 && d[9]==1) {
			// alert(d[7]+"_"+d[9]);
			if (d[10] == 86) { //Trims Receive Entry Multi Ref.
				var action = d[8].length > 0 ? d[8] : "trims_receive_entry_print";
				print_report(d[0] + '*' + d[1] + '*' + 'Trims Receive Entry Multi Ref.', action, "../trims_store/requires/trims_receive_multi_ref_entry_controller");
			} else if(d[10] == 110) {
				var action = d[8].length > 0 ? d[8] : "trims_receive_entry_print_2";
				print_report(d[0] + '*' + d[1] + '*' + 'Trims Receive Entry Multi Ref.', action, "../trims_store/requires/trims_receive_multi_ref_entry_controller");
			}else{
				alert("Not yet develop");
			}
		} else if (d[7] == 24) {
			if (d[9] == 3) { //Trims Receive Entry Multi Ref V3
				var action = d[8].length > 0 ? d[8] : "trims_receive_entry_print3";
				print_report(d[0] + '*' + d[1] + '*' + 'Trims Receive Entry Multi Ref V3', action, "../trims_store/requires/trims_receive_multi_ref_entry_v3_controller");
			} else {
				var action = d[8].length > 0 ? d[8] : "trims_receive_entry_print_2";
				print_report(d[0] + '__' + d[1] + '__' + 'Trims Receive Entry', action, "../trims_store/requires/trims_receive_entry_controller");
			}
		} else if (d[7] == 20) {
			// General Item Receive
			//General Store 4,8,9,10,11,15,16,17,18,19,20,21,22
			// print button
			print_report(d[0] + '__' + d[1] + '__' + 'General Item Receive' + '__' + d[4], "general_item_receive_print", "../general_store/requires/general_item_receive_controller");
		} else if (d[7] == 4) {
			// Dyes and Chemical Receive
			// Dyes and chemical  5,6,7,23		
			// print button
			var action = d[8].length > 0 ? d[8] : "chemical_dyes_receive_print";
			if(d[8] == 'chemical_dyes_receive_print_new')
			{
				var rateAmount = 'no';
				if(confirm('Print with rate and amount')){
					rateAmount = 'yes';
				}else{
					rateAmount = 'no';
				}
			}
			var data= d[0] + '*' + d[1] + '*' + 'Dyes And Chemical Receive' + '*' + d[5] + '*' + d[0] + '*' + d[6] + '*' + rateAmount;

			window.open("../chemical_dyes/requires/chemical_dyes_receive_controller.php?data=" + data+'&action='+d[8], true );

			//print_report(d[0] + '*' + d[1] + '*' + 'Dyes And Chemical Receive' + '*' + d[5] + '*' + d[0] + '*' + d[6] + '*' + rateAmount, action, "../chemical_dyes/requires/chemical_dyes_receive_controller");

		} else if (d[7] == 37) {
			// knit finish fabric receive by garments
			// print 4 button
			print_report(d[0] + '*' + d[1] + '*' + 'Knit Finish Fabric Receive By Garments' + '*' + d[2], 'finish_fabric_receive_print_4', '../finish_fabric/requires/knit_finish_fabric_receive_by_garments_controller');
		} else if (d[7] == 7) {
			// Finish fabric Production Entry
			// print button
			print_report(d[0] + '*' + d[1] + '*' + d[2] + '*' + 'Finish Fabric Production Entry', 'finish_fab_production_print', '../../production/requires/finish_fabric_receive_controller');
		} 
		else if (d[7] == 2) 
		{
			// Finish fabric Production Entry
			// print button
			print_report(d[0] + '*' + d[2] + '*' + d[1], 'rejection_challan_print', '../../production/requires/grey_production_entry_controller');
		} 
		else if (d[7] == 558)
		{
			// alert(d[0]+"*"+d[1]+"*"+d[2]+"*"+d[3]+"*"+d[4]+"*"+d[5]+"*"+d[6]+"*"+d[7]+"*"+d[8]+"*"+d[9]+"*"+d[10]); return;
			// Service Acknowledgement
			// print button
			// print_report(d[0] + '*' + d[1] + '*' + d[2]+ '*' + d[3]+ '*' + d[4]+ '*' + d[5]+ '*' + d[6]+ '*' + d[10]+ '*' + d[8]+ '*' + d[9], 'show_service_ackn_report', '../../commercial/work_order/requires/service_acknowledgement_controller');

			// txt_booking_no*cbo_company_name*cbo_wo_type*txt_workorder_no*txt_manual_challan*txt_booking_date*cbo_service_company*txt_exchange_rate*txt_remarks*booking_mst_id

			var action = 'show_service_ackn_report';
			window.open("../../commercial/work_order/requires/service_acknowledgement_controller.php?action=" + action + "&txt_booking_no='" + d[0] + "'&cbo_company_name=" + d[1] + "&cbo_wo_type=" + d[2] + "&txt_workorder_no=" + d[3] + "&txt_manual_challan=" + d[4] + "&txt_booking_date=" + d[5] + "&cbo_service_company=" + d[6] + "&txt_exchange_rate=" + d[10] + "&txt_remarks=" + d[8] + "&booking_mst_id=" + d[9], true);
		} 
		else {
			alert('Develop Later');
			return;
		}
	}

	function generate_row_matarial_item_rec_report(print_btn, company_id, update_id, basis) {
		var tittle = "Raw Material Receive";
		if (print_btn == 78) {
			window.open("../../trims_erp/raw_material_store/requires/raw_material_item_receive_controller.php?data=" + company_id + '*' + update_id + '*' + tittle + '*' + basis + '&action=general_item_receive_print&template_id=1', true);
		} else {
			window.open("../../trims_erp/raw_material_store/requires/raw_material_item_receive_controller.php?data=" + company_id + '*' + update_id + '*' + tittle + '*' + basis + '&action=general_item_receive_print_new&template_id=1', true);
		}
	}

	function generate_knit_grey_fabric_receive_print(print_btn, company_id, update_id, txt_booking_no, cbo_receive_basis, cbo_location) {
		var title = "Knit Grey Fabric Receive";
		if (print_btn == 78) {
			print_report(company_id + '*' + update_id + '*' + title + '*' + txt_booking_no + '*' + cbo_receive_basis + '*' + cbo_location, "grey_fabric_receive_print", "../../inventory/grey_fabric/requires/grey_fabric_receive_controller")
		}
	}

	function generate_bill_process_print(company_id, update_id, txt_system_no, hidden_party_id, txt_reference_no, hidden_reference_id, recvids, txt_party, txt_bill_no, txt_bill_date, cbo_buyer_name) {
		var report_title = "Knit Grey Fabric Receive";

		print_report(company_id + '*' + update_id + '*' + txt_system_no + '*' + hidden_party_id + '*' + txt_reference_no + '*' + hidden_reference_id + '*' + recvids + '*' + txt_party + '*' + txt_bill_no + '*' + txt_bill_date + '*' + report_title + '*' + cbo_buyer_name, "print_bill_processing_action", "../../inventory/requires/bill_processing_controller")

	}

	function generate_service_bill_print(company_id, update_id, bill_no) {
		var report_title = "General Service Bill Entry";

		print_report( company_id +'*'+update_id+'*'+bill_no+'*'+report_title,"general_service_bill_entry_print", "../../subcontract_bill/outbound_billing/requires/general_service_bill_entry_controller")
	}


	//Stationary Purchase Order
	function generate_stationary_purchase_order(company_id, print_btn, txt_wo_number, cbo_item_category, cbo_supplier, txt_wo_date, cbo_currency, cbo_wo_basis, cbo_pay_mode, cbo_source, txt_delivery_date, txt_attention, txt_req_numbers, txt_req_numbers_id, txt_delete_row, txt_delivery_place, update_id, cbo_location, cbo_template_id, txt_contact, mail_data, cbo_payterm_id, txt_remarks_mst, txt_tenor, cbo_inco_term, cbo_wo_type, txt_reference) {
		var report_title = "Stationary Purchase Order";
		if (print_btn == 66) {
			print_report(company_id + '*' + txt_wo_number + '*' + cbo_item_category + '*' + cbo_supplier + '*' + txt_wo_date + '*' + cbo_currency + '*' + cbo_wo_basis + '*' + cbo_pay_mode + '*' + cbo_source + '*' + txt_delivery_date + '*' + txt_attention + '*' + txt_req_numbers + '*' + txt_req_numbers_id + '*' + txt_delete_row + '*' + txt_delivery_place + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + cbo_template_id + '*' + txt_contact + '*' + mail_data, "stationary_work_order_print", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");

		} else if (print_btn == 732) {
			if (confirm('Press  OK to open with Size/MSR and Narration\n Press Cancel to open without Size/MSR and Narration')) {
				show = 1;
			} else {
				show = 0;
			}
			print_report(company_id + '*' + txt_wo_number + '*' + cbo_item_category + '*' + cbo_supplier + '*' + txt_wo_date + '*' + cbo_currency + '*' + cbo_wo_basis + '*' + cbo_pay_mode + '*' + cbo_source + '*' + txt_delivery_date + '*' + txt_attention + '*' + txt_req_numbers + '*' + txt_req_numbers_id + '*' + txt_delete_row + '*' + txt_delivery_place + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + cbo_template_id + '*' + txt_contact + '*' + show + '*' + mail_data, "stationary_work_order_po_print", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		} else if (print_btn == 85) {
			print_report(company_id + '*' + txt_wo_number + '*' + cbo_item_category + '*' + cbo_supplier + '*' + txt_wo_date + '*' + cbo_currency + '*' + cbo_wo_basis + '*' + cbo_pay_mode + '*' + cbo_source + '*' + txt_delivery_date + '*' + txt_attention + '*' + txt_req_numbers + '*' + txt_req_numbers_id + '*' + txt_delete_row + '*' + txt_delivery_place + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + cbo_template_id + '*' + txt_contact + '*' + show + '*' + mail_data, "stationary_work_order_print3", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		} else if (print_btn == 137) {
			print_report(company_id + '*' + txt_wo_number + '*' + cbo_item_category + '*' + cbo_supplier + '*' + txt_wo_date + '*' + cbo_currency + '*' + cbo_wo_basis + '*' + cbo_pay_mode + '*' + cbo_source + '*' + txt_delivery_date + '*' + txt_attention + '*' + txt_req_numbers + '*' + txt_req_numbers_id + '*' + txt_delete_row + '*' + txt_delivery_place + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + cbo_payterm_id + '*' + txt_remarks_mst + '*' + txt_contact + '*' + txt_tenor + '*' + cbo_template_id + '*' + mail_data, "stationary_work_order_print4", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		} else if (print_btn == 129) {
			print_report(company_id + '*' + txt_wo_number + '*' + cbo_item_category + '*' + cbo_supplier + '*' + txt_wo_date + '*' + cbo_currency + '*' + cbo_wo_basis + '*' + cbo_pay_mode + '*' + cbo_source + '*' + txt_delivery_date + '*' + txt_attention + '*' + txt_req_numbers + '*' + txt_req_numbers_id + '*' + txt_delete_row + '*' + txt_delivery_place + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + cbo_payterm_id + '*' + txt_remarks_mst + '*' + txt_contact + '*' + txt_tenor + '*' + cbo_template_id + '*' + cbo_inco_term + '*' + cbo_wo_type + '*' + txt_reference + '*' + mail_data, "stationary_work_order_print5", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		} else if (print_btn == 430) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_req_numbers_id + '*' + cbo_template_id + '*' + mail_data, "stationary_work_order_po_print2", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		} else if (print_btn == 72) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_req_numbers_id + '*' + cbo_template_id + '*' + mail_data, "stationary_work_order_print6", "../../commercial/work_order/requires/stationary_work_order_controller")
			show_msg("3");
		}
	}


	//Others Purchase Order
	function generate_Others_Purchase_Order(company_id, print_btn, update_id, req_no, cbo_location, cbo_template_id, txt_wo_number, txt_wo_date) {
		var report_title = "Others Purchase Order";
		if (print_btn == 66) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_print2", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 85) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_print3", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 732) {
			if (confirm('Press  OK to open with Size/MSR and Narration\n Press Cancel to open without Size/MSR and Narration')) {
				show = 1;
			} else {
				show = 0
			}
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id + '*' + show, "spare_parts_work_order_po_print", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 137) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + 5 + '*' + cbo_template_id, "spare_parts_work_print", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 129) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + 6 + '*' + cbo_template_id, "spare_parts_work_print", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 191) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + 7 + '*' + cbo_template_id, "spare_parts_work_print_urmi", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 227) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + cbo_location + '*' + 8 + '*' + cbo_template_id, "spare_parts_work_order_print8", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 235) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_print9", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");

		} else if (print_btn == 274) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_print10", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");
		} else if (print_btn == 430) {
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + cbo_template_id, "spare_parts_work_order_po_print2", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");
		} else if (print_btn == 241) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_po_print_11", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");
		} else if (print_btn == 427) {
			print_report(company_id + '*' + update_id + '*' + req_no + '*' + report_title + '*' + txt_wo_number + '*' + txt_wo_date + '*' + cbo_location + '*' + cbo_template_id, "spare_parts_work_order_print12", "../../commercial/work_order/requires/spare_parts_work_order_controller")
			show_msg("3");
		}
	}

	function generate_pi_proforma_report(company_id, update_id, print_btn, item_catagory, entry_form) {
		if (print_btn == 751) {
			window.open("../../commercial/import_details/requires/pi_print_urmi.php?data=" + company_id + '*' + update_id + '*' + item_catagory + '&action=print_pi&template_id=1', true);
		} else if (print_btn == 86) {
			window.open("../../commercial/import_details/requires/pi_print_urmi.php?data=" + company_id + '*' + update_id + '*' + entry_form + '*' + item_catagory + '&action=print&template_id=1', true);
		} else if (print_btn == 116) {
			window.open("../../commercial/import_details/requires/pi_print_urmi.php?data=" + company_id + '*' + update_id + '*' + entry_form + '*' + item_catagory + '&action=print_wf&template_id=1', true);
		} else if (print_btn == 85) {
			window.open("../../commercial/import_details/requires/pi_print_urmi.php?data=" + company_id + '*' + update_id + '*' + entry_form + '*' + item_catagory + '&action=print_sf&template_id=1', true);

		}
	}

	
    function generate_report_grey_fabric_mrr(print_btn, company_id, update_id, rec_id, location_id, store_id) {
        var report_title="Knit Grey Fabric Roll Receive";

		if (print_btn == 86) {
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print&template_id=1', true);
		}
        else if(print_btn == 84){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print&template_id=1', true);
        }
        else if(print_btn == 85){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print3&template_id=1', true);
        }
        else if(print_btn == 89){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print4&template_id=1', true);
        }
        else if(print_btn == 129){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print5&template_id=1', true);
        }
        else if(print_btn == 848){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_printmg&template_id=1', true);
        }
	}


	function fabric_sales_order_entry_fnc(print_btn, within_group, cbo_company_id, txt_booking_no_id, txt_booking_no, txt_job_no, update_id) {
		var title = "Fabric Sales Order Entry";
		if (print_btn == 115) {

			if (within_group == 1) {
				window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '&action=fabric_sales_order_print', true);

			} else {
				window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '&action=fabric_sales_order_print2', true);
			}
		} else if (print_btn == 72) {
			window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '&action=fabric_sales_order_print6', true);

		} else if (print_btn == 129) {
			if (within_group == 1) {
				window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '&action=fabric_sales_order_print_yes_6', true);
			} else {
				alert("This report generated only within group yes");
				return;
			}
		} else if (print_btn == 136) {
			var action = "fabric_sales_order_print4";
			window.open("../../production/requires/fabric_sales_order_entry_controller.php?action=" + action + "&companyId=" + cbo_company_id + "&txt_booking_no_id=" + txt_booking_no_id + "&bookingNo=" + txt_booking_no + "&salesOrderNo=" + txt_job_no + "&title=" + title + "&update_id=" + update_id, true);
		} else if (print_btn == 137) {
			window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '*' + within_group + '&action=fabric_sales_order_print_kds2', true);

		} else if (print_btn == 116) {
			window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + cbo_company_id + '*' + txt_booking_no_id + '*' + txt_booking_no + '*' + txt_job_no + '*' + title + '&action=fabric_sales_order_print3', true)

		}
	}



	function generate_dyes_camical_print(company_id, print_btn, update_id, cbo_template_id, txt_req_numbers_id) {
		var title = "Dyes And Chemical Purchase Order";
		if (print_btn == 78) {
			print_report(company_id + '*' + update_id + '*' + title + '*' + cbo_template_id, "dyes_chemical_work_print", "../../commercial/work_order/requires/dyes_and_chemical_work_order_controller")
		} else if (print_btn == 84) {
			print_report(company_id + '*' + update_id + '*' + txt_req_numbers_id + '*' + title + '*' + cbo_template_id, "dyes_chemical_work_print2", "../../commercial/work_order/requires/dyes_and_chemical_work_order_controller");
			show_msg("3");
		} else if (print_btn == 732) {

			print_report(company_id + '*' + update_id + '*' + txt_req_numbers_id + '*' + title + '*' + cbo_template_id, "dyes_chemical_work_po_print", "../../commercial/work_order/requires/dyes_and_chemical_work_order_controller");
			show_msg("3");
		} else if (print_btn == 85) {

			print_report(company_id + '*' + update_id + '*' + txt_req_numbers_id + '*' + title + '*' + cbo_template_id, "dyes_chemical_work_print3", "../../commercial/work_order/requires/dyes_and_chemical_work_order_controller");
			show_msg("3");
		} else if (print_btn == 430) {
			print_report(company_id + '*' + update_id + '*' + txt_req_numbers_id + '*' + title + '*' + cbo_template_id, "dyes_chemical_work_po_print2", "../../commercial/work_order/requires/dyes_and_chemical_work_order_controller");
			show_msg("3");
		}

	}

	function generate_swo_print(company_id, print_btn, update_id, cbo_template_id) 
	{
		var report_title = "Service Work Order";
		86/1,84/3,85/4,732/2
		if(print_btn == 86)
		{
			print_report( company_id+'*'+update_id+'*'+report_title+'*'+cbo_template_id, "service_work_order_print", "../../commercial/work_order/requires/service_work_order_controller" );
		}
		else if(print_btn == 84)
		{
			print_report( company_id+'*'+update_id+'*'+report_title+'*'+cbo_template_id, "service_work_order_print_2", "../../commercial/work_order/requires/service_work_order_controller" );
		}
		else if(print_btn == 85)
		{
			print_report( company_id+'*'+update_id+'*'+report_title+'*'+cbo_template_id, "service_work_order_print_3", "../../commercial/work_order/requires/service_work_order_controller" );
		}
		else if(print_btn == 732)
		{
			print_report( company_id+'*'+update_id+'*'+report_title+'*'+cbo_template_id, "service_work_order_po_print", "../../commercial/work_order/requires/service_work_order_controller" );
		}
	}

	function generate_ydw_print(cbo_company_name,cbo_supplier_name,txt_booking_no,cbo_pay_mode,update_id, print_btn,budget_version) 
	{
	

		var form_name="yarn_dyeing_wo_booking";

		if(print_btn == 13)  //Print 
		{
		
			var show_comment='';
			var r=confirm("Press  \"OK\"  to hide  Style No and Buyer Name\nPress  \"CANCEL\"  to Show Style No and Buyer Name");
			if (r==true) show_comment="0"; else show_comment="1";
			
			var form_name="yarn_dyeing_wo_booking";

			var action = "show_print_booking_report";
			var path=1;

			if(budget_version==1)
			{
			
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment +"&path="+path , true);
			}
			else
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment +"&path="+path , true);
			}

		
		}

		if(print_btn == 15)  //Print 2
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment, Amount and Rate\nPress  \"OK\"  to Show Comment, Amount and Rate");
			if (r==true) show_comment="1"; else show_comment="0";
			
			var form_name="yarn_dyeing_wo_booking";

			var action = "show_trim_booking_report_two";

			if(budget_version==1)
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}
			else
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}

		}

		if(print_btn == 74)  //Print Order With Rate
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r==true) show_comment="1"; else show_comment="0";
			
			var form_name="yarn_dyeing_wo_booking";

			var action = "show_trim_booking_report";

			if(budget_version==1)
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}
			else
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}

		}

		if(print_btn == 75)  //Print Order Without Rate
		{
		
			var form_name="yarn_dyeing_wo_booking";

			var action = "show_without_rate_booking_report";

			if(budget_version==1)
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name , true);
			}
			else
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name , true);
			}

		}

		else if(print_btn == 77)  //Multiple Job Without Rate
		{
			var action = "show_with_multiple_job_without_rate";
			window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name , true);

			//print_report(txt_booking_no+'*'+cbo_company_name+'*'+update_id+'*'+cbo_supplier_name+'*'+cbo_pay_mode+'*'+form_name, "show_with_multiple_job_without_rate", "../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2" );
		}
		else if(print_btn == 76)  //Print With Multiple Job
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r==true) show_comment="1"; else show_comment="0";

			var action = "show_with_multiple_job";

			if(budget_version==1)
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}
			else
			{
				window.open("../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php?action=" + action + "&txt_booking_no='" + txt_booking_no + "'&cbo_company_name=" + cbo_company_name + "&update_id=" + update_id + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_pay_mode=" + cbo_pay_mode + "&form_name=" + form_name + "&show_comment=" + show_comment , true);
			}
			
		}
		else if(print_btn == 732)
		{
			print_report( company_id+'*'+update_id+'*'+report_title+'*'+cbo_template_id, "service_work_order_po_print", "../../commercial/work_order/requires/service_work_order_controller" );
		}
	}

	function generate_purchase_requisition_print(company_id, print_btn, update_id, txt_remark, cbo_template_id, cbo_location_name, is_approved) {
		var report_title = "Purchase Requisition";
		if (print_btn == 120) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_2", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 122) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name + '*' + is_approved, "purchase_requisition_print_3", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 169) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_8", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 425) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_26", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 123) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_4", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 165) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_9", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 129) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_5", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 227) {
			var show_item = "";
			//var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			var r = confirm("Press  \"Cancel\"  to hide Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value \nPress  \"OK\"  to Show Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_10", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 241) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_11", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 580) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_4_akh", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 28) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_13", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 280) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_14", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 688) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_15", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 243) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_16", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 310) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_category_wise_print", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 304) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_18", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 719) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_19", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 723) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_20", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 339) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_21", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 370) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_22", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 382) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_23", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 235) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_24", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 768) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_25", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else if (print_btn == 419) {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print_27", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		} else {
			var show_item = "";
			print_report(company_id + '*' + update_id + '*' + report_title + '*' + txt_remark + '*' + show_item + '*' + cbo_template_id + '*' + cbo_location_name, "purchase_requisition_print", "../../inventory/requires/purchase_requisition_controller")
			show_msg("3");
		}
	}

	function generate_req_booking_print(booking_no, print_btn, company_id, is_approved, report_type, mail_send_data) {
		var report_title = "Multiple Job Wise Trims Booking V2"
		var action = "";
		if (print_btn == 14) {
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate & Amount\nPress  \"OK\"  to Show Rate & Amount");
			if (r == true) show_comment = "1";
			else show_comment = "0";
			var action = 'show_trim_booking_report16';

		} else if (print_btn == 28) {
			var action = 'show_trim_booking_report13';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 235) {

			var action = 'show_trim_booking_report5';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 183) {

			var action = 'show_trim_booking_report3';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 269) {

			var action = 'show_trim_booking_report12';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 176) {

			var action = 'show_trim_booking_report6';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 339) {

			var action = 'show_trim_booking_report18';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 719) {

			var action = 'show_trim_booking_report17';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 177) {

			var action = 'show_trim_booking_report9';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 502) {

			var action = 'show_trim_booking_report26';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 786) {

			var action = 'show_trim_booking_report25';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 419) {

			var action = 'show_trim_booking_report22';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 404) {

			var action = 'show_trim_booking_report21';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 768) {

			var action = 'show_trim_booking_report20';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 433) {

			var action = 'show_trim_booking_report19';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 304) {

			var action = 'show_trim_booking_report15';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 280) {

			var action = 'show_trim_booking_report14';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 274) {

			var action = 'show_trim_booking_report10';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 241) {

			var action = 'show_trim_booking_report11';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 746) {

			var action = 'show_trim_booking_report7';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 209) {

			var action = 'show_trim_booking_report4';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 227) {

			var action = 'show_trim_booking_report8';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		} else if (print_btn == 67) {

			var action = 'show_trim_booking_report2';
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r == true) show_comment = "1";
			else show_comment = "0";
		}

		window.open("../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php?action=" + action + "&txt_booking_no='" + booking_no + "'&cbo_company_name=" + company_id + "&id_approved_id=" + is_approved + "&id_approved_id=" + is_approved + "&report_title=" + report_title + "&show_comment=" + show_comment + "&report_type=" + report_type + "&mail_send_data=" + mail_send_data, true);
	}

	function generate_wo_booking_print(booking_no, print_btn, company_id, txt_order_no_id, cbo_fabric_natu, cbo_fabric_source, id_approved_id, path, report_type, mail_id, is_mail_send) {

		var report_title = "Partial Fabric Booking";
		if (print_btn == 84) {
			var show_yarn_rate = '';

			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "show_fabric_booking_report_urmi_per_job";
		} else if (print_btn == 85) {
			var show_yarn_rate = '';

			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_3";
		} else if (print_btn == 151) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "show_fabric_booking_report_advance_attire_ltd";
		} else if (print_btn == 160) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "print_booking_5";

		} else if (print_btn == 175) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_6";
		} else if (print_btn == 155) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "fabric_booking_report";
		} else if (print_btn == 235) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "print_9";
		} else if (print_btn == 191) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "print_booking_7";
		}
		window.open("../../order/woven_gmts/requires/partial_fabric_booking_controller.php?action=" + action + "&txt_booking_no='" + booking_no + "'&cbo_company_name=" + company_id + "&txt_order_no_id=" + txt_order_no_id + "&cbo_fabric_natu=" + cbo_fabric_natu + "&cbo_fabric_source=" + cbo_fabric_source + "&id_approved_id=" + id_approved_id + "&report_title=" + report_title + "&show_yarn_rate=" + show_yarn_rate + "&path=" + path + "&report_type=" + report_type + "&mail_id=" + mail_id + "&is_mail_send=" + is_mail_send, true);


	}

	function generate_knitting_booking_print(booking_no, print_btn, company_id, txt_order_no_id, cbo_fabric_natu, cbo_fabric_source, id_approved_id, path, report_type, mail_id, is_mail_send) {

		var report_title = "Partial Fabric Booking";
		if (print_btn == 143) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "show_fabric_booking_report_urmi";
		} else if (print_btn == 84) {
			var show_yarn_rate = '';

			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "show_fabric_booking_report_urmi_per_job";
		} else if (print_btn == 85) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_3";
		} else if (print_btn == 151) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "show_fabric_booking_report_advance_attire_ltd";
		} else if (print_btn == 160) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}

			var action = "print_booking_5";

		} else if (print_btn == 171) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_6";
		} else if (print_btn == 218) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_7";
		} else if (print_btn == 220) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_northern_new";
		} else if (print_btn == 235) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_northern_9";
		} else if (print_btn == 274) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_10";
		} else if (print_btn == 241) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_11";
		} else if (print_btn == 269) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_12";
		} else if (print_btn == 28) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_13";
		} else if (print_btn == 280) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_14";
		} else if (print_btn == 304) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_15";
		} else if (print_btn == 719) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_16";
		} else if (print_btn == 723) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_17";
		} else if (print_btn == 339) {
			var show_yarn_rate = '';
			var r = confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r == true) {
				show_yarn_rate = "1";
			} else {
				show_yarn_rate = "0";
			}
			var action = "print_booking_18";
		}
		window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?action=" + action + "&txt_booking_no='" + booking_no + "'&cbo_company_name=" + company_id + "&txt_order_no_id=" + txt_order_no_id + "&cbo_fabric_natu=" + cbo_fabric_natu + "&cbo_fabric_source=" + cbo_fabric_source + "&id_approved_id=" + id_approved_id + "&report_title=" + report_title + "&show_yarn_rate=" + show_yarn_rate + "&path=" + path + "&report_type=" + report_type + "&mail_id=" + mail_id + "&is_mail_send=" + is_mail_send, true);
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission); ?>
		<form name="mrraudit_1" id="mrraudit_1">
			<h3 align="left" style="width:1120px;" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1120px;">
					<table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="120" class="must_entry_caption">Company Name</th>
							<th width="80">Location</th>
							<th width="100">Store</th>
							<th width="120">Item Category</th>
							<th width="60">Challan No</th>
							<th width="60">MRR No</th>
							<th width="90">Date Basis</th>
							<th width="90">Year</th>
							<th width="120" colspan="2" class="must_entry_caption">Date Range</th>
							<th width="100">Supplier</th>
							<th width="70">WO Number</th>
							<th width="70">PI Number</th>
							<th width="90">Audit Status</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('mrraudit_1','report_container','','','')" class="formbutton" style="width:70px" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?
									echo create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/mrr_auditing_report_controller',this.value, 'load_drop_down_location', 'com_location_td' ); load_drop_down( 'requires/mrr_auditing_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td'); load_drop_down( 'requires/mrr_auditing_report_controller', this.value, 'load_drop_down_store', 'com_store_td' );");
									?>
								</td>
								<td id="com_location_td">
									<?
									echo create_drop_down("cbo_location_id", 80, $blank_array, "", 1, "-- All  --", 0, "", 0);
									?>
								</td>
								<td id="com_store_td">
									<?
									echo create_drop_down("cbo_store_name", 100, $blank_array, "", 1, "-- Select Store --", 0, "");
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_item_category_id", 120, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond order by short_name", "category_id,short_name", 1, "-- Select Category --", 0, "");
									?>
								</td>
								<td>

								
									<input name="txt_challan_no" id="txt_challan_no" style="width:60px" class="text_boxes" placeholder="Write/Browse" ondblclick="openmypage_challan()">
									<input type="hidden" name="txt_challan_id" id="txt_challan_id" value="">
								</td>
								<td><input name="txt_mrr_no" id="txt_mrr_no" style="width:60px" class="text_boxes" placeholder="Write"></td>
								<td>
									<?
									$databasis_type_arr = array(1 => "MRR Date", 2 => "Audit Date");
									echo create_drop_down("cbo_date_basis", 90, $databasis_type_arr, "", 0, "", $selected, "", "", "");
									?>
								</td>
								<td>
									<?
                            			echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        			?>
								</td>

								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
								<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
								<td id="supplier_td">
									<?
									echo create_drop_down("cbo_suppler_name", 100, $blank_array, "", 1, "-- Select Supplier --", 0, "");
									?>
								</td>
								<td>
									<input name="txt_wo_no" id="txt_wo_no" style="width:70px" class="text_boxes" placeholder="Write/Browse" ondblclick="openmypage_wo()">
									<input type="hidden" name="txt_wo_id" id="txt_wo_id" value="">
								</td>
								<td><input name="txt_pi_no" id="txt_pi_no" style="width:70px" class="text_boxes" placeholder="Write"></td>
								<td>
									<?
									$auditmrr_type_arr = array(0 => "Un-Audited", 1 => "Audited");
									echo create_drop_down("cbo_audit_type", 90, $auditmrr_type_arr, "", 0, "", $selected, "", "", "");
									?>
								</td>
								<td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated()" /></td>
							</tr>
							<tr>
								<td colspan="13" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>
	</div>
	<div id="report_container" align="center"></div>
	<div id="report_container2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_audit_type').val(0);
</script>

</html>