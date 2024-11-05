<?
/*-------------------------------------------- Comments
Purpose         :   This form will create Yarn Issue Entry

Functionality   : 
JS Functions    :
Created by      :   Bilas
Creation date   :   07-05-2013
Updated by      :   Kausar,Didar
Update date     :   29-10-2013,01_01_2018
QC Performed BY :
QC Date         :
Comments        :
*/


session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

$independent_control_arr = return_library_array("select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=3 and status_active=1 and is_deleted=0", 'company_name', 'independent_controll');

$YarnIssueValidationBasedOnServiceApproval = return_library_array("select yarn_iss_with_serv_app, company_name from  variable_order_tracking where variable_list=60 and status_active = 1 and is_deleted = 0 order by id", 'company_name', 'yarn_iss_with_serv_app');

//$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Issue Info", "../", 1, 1, $unicode, 1, 1);

?>

<script>
	<?
	$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][3]);
	echo "var field_level_data= " . $data_arr . ";\n";
	?>

	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";


	// popup for booking no ----------------------
	function popuppage_fabbook() {
		if (form_validation('cbo_company_id*cbo_issue_purpose', 'Company Name*Issue Purpose') == false) {
			return;
		}

		var company = $("#cbo_company_id").val();
		var issue_purpose = $("#cbo_issue_purpose").val();
		var basis = $("#cbo_basis").val();

		var page_link = 'requires/yarn_issue_controller.php?action=fabbook_popup&company=' + company + '&issue_purpose=' + issue_purpose + '&basis=' + basis;
		var title = "K&D Information";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px, height=400px, center=1, resize=0, scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value;

			if (bookingNumber != "") {
				bookingNumber = bookingNumber.split("__");
				freeze_window(5);
				// 2960_MFG-FSOE-22-00031_445_Check Fabric Sales
				$("#txt_booking_id").val(bookingNumber[0]);
				$("#txt_booking_no").val(bookingNumber[1]);

				if (basis == 4) {
					$("#txt_buyer_job_no").val(bookingNumber[5]);
					$("#txt_issue_qnty").attr('placeholder', 'Entry');
					$("#txt_issue_qnty").removeAttr('ondblclick');
					$("#txt_issue_qnty").removeAttr('readOnly');
					$("#txt_returnable_qty").removeAttr('readOnly');
					$("#txt_returnable_qty").attr('placeholder', 'Entry');
					$("#txt_style_ref").val(bookingNumber[4]);
					$("#cbo_buyer_name option[value!='0']").remove();
					$("#cbo_buyer_name").append("<option selected value='" + bookingNumber[2] + "'>" + bookingNumber[3] + "</option>");
				} else {
					$("#cbo_buyer_name").val(bookingNumber[2]);

					if (issue_purpose == 2 || issue_purpose == 7 || issue_purpose == 12 || issue_purpose == 15 || issue_purpose == 38 || issue_purpose == 44 || issue_purpose == 46 || issue_purpose == 50 || issue_purpose == 51) {
						$("#txt_buyer_job_no").val('');
						$("#txt_style_ref").val('');
						$("#save_data").val('');
						$("#all_po_id").val('');
						$("#txt_issue_qnty").val('');
						$("#txt_returnable_qty").val('');

						if (bookingNumber[8] == 3 || bookingNumber[8] == 5) {
							var kniting_wo_company = bookingNumber[6];
							load_drop_down('requires/yarn_issue_controller', 1 + '**' + kniting_wo_company + '**' + issue_purpose, 'load_drop_down_knit_com', 'knitting_company_td');
							$("#cbo_knitting_source").val(1);
							$("#cbo_knitting_company").val(bookingNumber[6]);
							load_drop_down('requires/yarn_issue_controller', kniting_wo_company + '_' + 1, 'load_drop_down_location', 'location_td');
						} else {
							var kniting_wo_company = bookingNumber[6];
							load_drop_down('requires/yarn_issue_controller', 3 + '**' + kniting_wo_company + '**' + issue_purpose, 'load_drop_down_knit_com', 'knitting_company_td');
							$("#cbo_knitting_source").val(3);
							$("#cbo_knitting_company").val(bookingNumber[6]);
							load_drop_down('requires/yarn_issue_controller', kniting_wo_company + '_' + 3, 'load_drop_down_location', 'location_td');
						}

						$("#txt_buyer_job_no").val(bookingNumber[3]);

						if (bookingNumber[5] == 42 || bookingNumber[5] == 114) {

							$("#txt_issue_qnty").attr('placeholder', 'Entry');
							$("#txt_issue_qnty").removeAttr('ondblclick');
							$("#txt_issue_qnty").removeAttr('readOnly');
							$("#txt_returnable_qty").removeAttr('readOnly');
							$("#txt_returnable_qty").attr('placeholder', 'Entry');
						} else {
							$("#txt_issue_qnty").attr('placeholder', 'Double Click');
							$("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
							$("#txt_issue_qnty").attr('readOnly', true);
							$("#txt_returnable_qty").attr('readOnly', true);
							$("#txt_returnable_qty").attr('placeholder', 'Display');
						}

						show_list_view(bookingNumber[0], 'show_yarn_dyeing_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
					} else {
						$("#txt_buyer_job_no").val(bookingNumber[3]);
						$("#txt_style_ref").val(bookingNumber[4]);
						$("#dyeingColor_td").html('<? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?>');
					}
				}

				$("#txt_entry_form").val(bookingNumber[5]);
				release_freezing();
			}
		}
	}

	function openmypage_service_booking() {
		//*cbo_knitting_source*txt_booking_no*cbo_knitting_company
		if (form_validation('cbo_basis*txt_booking_no', 'Basis*Booking No') == false) {
			return;
		}

		var company_id = $("#cbo_company_id").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var txt_buyer_job_no = $("#txt_buyer_job_no").val();

		var page_link = 'requires/yarn_issue_controller.php?action=service_booking_popup&company_id=' + company_id + '&txt_buyer_job_no=' + txt_buyer_job_no + '&cbo_buyer_name=' + cbo_buyer_name;

		var title = "Service  Booking Search";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=350px, center=1, resize=0, scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("selected_booking");
			if (theemail.value != "") {
				$('#txt_service_booking_no').val(theemail.value);
			}
		}
	}

	function openmypage_lot() {
		var yarn_rate_match = $("#yarn_rate_match").val();
		var txt_booking_no = $("#txt_booking_no").val();
		var cbo_basis = $("#cbo_basis").val();
		var issue_purpose = $("#cbo_issue_purpose").val();
		var job_no = $("#txt_buyer_job_no").val();

		if (cbo_basis == 1 && issue_purpose == 2) {
			if (form_validation('cbo_company_id*cbo_basis*cbo_store_name*txt_composition', 'Company Name*Basis*Store Name*Composition') == false) {
				return;
			}
		}
		else if(cbo_basis == 2 || cbo_basis == 4) //independent and sales order basis
		{
			if (form_validation('cbo_company_id*cbo_basis', 'Company Name*Basis') == false) {
				return;
			}
		}
		else {
			if (form_validation('cbo_company_id*cbo_basis*cbo_store_name', 'Company Name*Basis*Store Name') == false) {
				return;
			}
		}

		if (yarn_rate_match == 1 && cbo_basis == 1 && txt_booking_no == "") {
			alert("Select Booking First.");
			return;
		}

		var company = $("#cbo_company_id").val();
		var supplier = $("#cbo_supplier").val();
		var issue_purpose = $("#cbo_issue_purpose").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_composition_id = $("#txt_composition_id").val();
		var txt_composition_percent = $("#txt_composition_percent").val();
		var cbo_yarn_type = $("#cbo_yarn_type").val();
		var cbo_color = $("#cbo_color").val();
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		var page_link = 'requires/yarn_issue_controller.php?action=yarnLot_popup&company=' + company + '&supplier=' + supplier + '&issue_purpose=' + issue_purpose + '&cbo_store_name=' + cbo_store_name + '&txt_composition_id=' + txt_composition_id + '&txt_composition_percent=' + txt_composition_percent + '&cbo_yarn_type=' + cbo_yarn_type + '&cbo_color=' + cbo_color + '&cbo_yarn_count=' + cbo_yarn_count + '&yarn_rate_match=' + yarn_rate_match + '&txt_booking_no=' + txt_booking_no + '&cbo_basis=' + cbo_basis + '&issue_purpose=' + issue_purpose + '&job_no=' + job_no;
		var title = "Yarn Lot Search";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px, height=350px, center=1, resize=0, scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var hidden_data = this.contentDoc.getElementById("hidden_prod_id").value;
			//alert(hidden_data); // 23752**0**2.0000**0.0000**21**1** ** ** **387**388**389**390**0
			var hidden_prod_id = hidden_data.split("**")
			if (hidden_prod_id[0] != "") {
				freeze_window(5);
				$("#txt_prod_id").val(hidden_prod_id[0]);
				get_php_form_data(hidden_prod_id[0] + "**" + issue_purpose + "**" + cbo_store_name + "**" + hidden_prod_id[1] + "**" + company + "**" + hidden_prod_id[9] + "**" + hidden_prod_id[10] + "**" + hidden_prod_id[11] + "**" + hidden_prod_id[12] + "**" + hidden_prod_id[13], "populate_data_child_from", "requires/yarn_issue_controller");

				get_php_form_data(company + "**" + cbo_store_name + "**" + hidden_prod_id[9] + "**" + hidden_prod_id[10] + "**" + hidden_prod_id[11] + "**" + hidden_prod_id[12] + "**" + hidden_prod_id[13], "floor_room_rack_populate_data", "requires/yarn_issue_controller");
				release_freezing();
			}
		}
	}

	function popup_description(prod_id, stores) {
		//alert(prod_id+'='+stores);
		var company = $("#cbo_company_id").val();
		$('#txt_current_stock').val("");
		$('#cbo_floor').val(0);
		$('#cbo_room').val(0);
		$('#txt_rack').val(0);
		$('#txt_shelf').val(0);
		$('#cbo_bin').val(0);
		var page_link = "requires/yarn_issue_controller.php?action=item_description_popup&company_id=" + company + "&prod_id=" + prod_id + "&stores=" + stores;
		var title = "Item Description Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=320px,center=1,resize=1,scrolling=0', '../../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var product_id_td = this.contentDoc.getElementById("product_id_td").value;
			var store_name = this.contentDoc.getElementById("store_id").value;
			var floor = this.contentDoc.getElementById("floor_id").value; //new dev
			var room = this.contentDoc.getElementById("room").value; //new dev
			var rack = this.contentDoc.getElementById("rack").value; //new dev
			var shelf = this.contentDoc.getElementById("shelf").value; //new dev
			var bin = this.contentDoc.getElementById("bin_box").value; //new dev
			var current_stock = this.contentDoc.getElementById("current_stock").value;
			//alert(company+'='+store_name+'='+floor+'='+room+'='+rack+'='+shelf+'='+bin);
			get_php_form_data(company + "**" + store_name + "**" + floor + "**" + room + "**" + rack + "**" + shelf + "**" + bin, "floor_room_rack_populate_data", "requires/yarn_issue_controller");

			$("#txt_current_stock").val(current_stock);
		}
	}

	function openmypage_requis() {
		if (form_validation('cbo_company_id*cbo_basis', 'Company Name*Basis') == false) {
			return;
		}

		var company = $("#cbo_company_id").val();
		var YarnIssueValidationBasedOnServiceApproval_arr = JSON.parse('<? echo json_encode($YarnIssueValidationBasedOnServiceApproval); ?>');
		if (YarnIssueValidationBasedOnServiceApproval_arr) {
			if (YarnIssueValidationBasedOnServiceApproval_arr[company] == 1) {
				if (form_validation('cbo_knitting_source', 'knitting_source') == false) {
					return;
				}
			}
		}

		var knitting_source = $("#cbo_knitting_source").val();
		var cbo_basis = $("#cbo_basis").val();
		var cbo_issue_purpose = $("#cbo_issue_purpose").val();
		var txt_system_no = $("#txt_system_no").val();
		var cbo_location_id = $("#cbo_location_id").val();

		var page_link = 'requires/yarn_issue_controller.php?action=requis_popup&company=' + company + "&knitting_source_id=" + knitting_source + "&cbo_location_id=" + cbo_location_id + "&txt_system_no=" + txt_system_no + "&cbo_basis=" + cbo_basis;

		var title = "Yarn Requisition Search";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px, height=350px, center=1, resize=0, scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var hidden_req_no = this.contentDoc.getElementById("hidden_req_no").value;

			if (hidden_req_no != "") {
				hidden_req_no = hidden_req_no.split(",");
				freeze_window(5);

				if (cbo_basis == 8) {
					$('#txt_req_no').val(hidden_req_no[9]);
				} else {
					$('#txt_req_no').val(hidden_req_no[0]);
				}

				$('#hdn_requis_qnty').val(hidden_req_no[3]);
				$('#hidden_p_issue_qnty').val(hidden_req_no[4]);
				$('#cbo_knitting_source').val(hidden_req_no[5]);
				$('#cbo_buyer_name').val(hidden_req_no[2]);
				$('#demand_id').val(hidden_req_no[8]);
				$('#hdn_req_no').val(hidden_req_no[0]);
				var with_order = hidden_req_no[10];

				//alert(hidden_req_no[8]+'='+hidden_req_no[9]+'='+hidden_req_no[10]);

				load_drop_down('requires/yarn_issue_controller', hidden_req_no[5] + '**' + company + '**' + cbo_issue_purpose, 'load_drop_down_knit_com', 'knitting_company_td');
				$('#cbo_knitting_company').val(hidden_req_no[6]).attr('disabled', 'disabled');
				show_list_view(hidden_req_no[0] + ',' + hidden_req_no[1] + ',' + hidden_req_no[2] + ',' + hidden_req_no[8] + ',' + cbo_basis, 'show_req_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
				load_drop_down('requires/yarn_issue_controller', hidden_req_no[6] + '_' + hidden_req_no[5], 'load_drop_down_location', 'location_td');

				if (with_order != 1) {
					load_drop_down('requires/yarn_issue_controller', cbo_basis + '_0' + '_' + with_order, 'load_drop_down_purpose', 'issue_purpose_td');
				}

				if (knitting_source == 1 && cbo_basis == 3) {
					$('#cbo_location_id').val(hidden_req_no[7]);
				}

				//for sample without order
				if ((cbo_basis == 3 || cbo_basis == 8) && with_order == 1) {
					$('#txt_returnable_qty').attr({
						'readonly': true,
						'placeholder': 'Display'
					});
					$('#txt_issue_qnty').attr('readonly', 'readonly').attr('onDblClick', 'openmypage_po()').removeAttr('placeholder').attr('placeholder', 'Double Click');
				} else {
					$('#txt_returnable_qty').attr({
						'readonly': false,
						'placeholder': 'Write'
					});
					$('#txt_issue_qnty').removeAttr('readonly').removeAttr('onDblClick').removeAttr('placeholder').attr('placeholder', 'Write');
				}

				if (hidden_req_no[5] == 1) // knitting source 1== inhouse
				{
					$("#caption_location").css("color", "blue");

				}

				release_freezing();
			}
		}
	}

	function openmypage_po() {
		var purpose = $("#cbo_issue_purpose").val();
		var receive_basis = $('#cbo_basis').val();
		var booking_no = $('#txt_booking_no').val();
		var booking_id = $('#txt_booking_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();

		var original_save_data = $('#original_save_data').val();

		var all_po_id = $('#all_po_id').val();
		var issueQnty = $('#txt_issue_qnty').val();
		var retnQnty = $('#txt_returnable_qty').val();

		var distribution_method = $('#distribution_method_id').val();
		var job_no = $('#job_no').val();
		var buyer_job_no = $('#txt_buyer_job_no').val();
		var txt_lot_no = $('#txt_lot_no').val();
		var txt_prod_id = $('#txt_prod_id').val();
		var req_no = $('#txt_req_no').val();
		var extra_quantity = $('#extra_quantity').val();
		var entry_form = $('#txt_entry_form').val();
		var update_id = $('#update_id').val();
		var demand_id = $('#demand_id').val();
		var hdn_req_no = $('#hdn_req_no').val();
		var cbo_dyeing_color = $('#cbo_dyeing_color').val();
		var hdn_dmnd_req_id = $('#hdn_dmnd_req_id').val();

		if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose', 'Company*Basis*Issue Purpose') == false) {
			return;
		} else if (receive_basis == 1 && (purpose == 1 || purpose == 2 || purpose == 4 || purpose == 12 || purpose == 15 || purpose == 38 || purpose == 44 || purpose == 46 || purpose == 50 || purpose == 51)) {
			if (form_validation('txt_booking_no', 'Booking') == false) {
				return;
			}
		} else if (receive_basis == 3) {
			if (form_validation('txt_req_no', 'Requisition. No') == false) {
				return;
			}
		}

		if (receive_basis == 1 && purpose == 2 && job_no == "") {
			alert("Please Select Job From Right Side List View");
			return;
		}

		if (receive_basis == 3 && txt_lot_no == "") {
			alert("Please Select Yarn From Right Side List View");
			return;
		}

		if (receive_basis == 1 && purpose == 2) {
			if (form_validation('cbo_dyeing_color', 'Dyeing_color') == false) {
				return;
			}
		}


		var title = 'PO Info';
		var page_link = 'requires/yarn_issue_controller.php?receive_basis=' + receive_basis + '&cbo_company_id=' + cbo_company_id + '&booking_no=' + booking_no + '&booking_id=' + booking_id + '&all_po_id=' + all_po_id + '&save_data=' + save_data + '&original_save_data=' + original_save_data + '&issueQnty=' + issueQnty + '&retnQnty=' + retnQnty + '&distribution_method=' + distribution_method + '&job_no=' + job_no + '&issue_purpose=' + purpose + '&req_no=' + req_no + '&extra_quantity=' + extra_quantity + '&txt_prod_id=' + txt_prod_id + '&entry_form=' + entry_form + '&update_id=' + update_id + '&demand_id=' + demand_id + '&hdn_req_no=' + hdn_req_no + '&buyer_job_no=' + buyer_job_no + '&cbo_dyeing_color=' + cbo_dyeing_color + '&hdn_dmnd_req_id=' + hdn_dmnd_req_id + '&lot=' + txt_lot_no + '&action=po_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0', '');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var save_string = this.contentDoc.getElementById("save_string").value;
			var tot_issue_qnty = this.contentDoc.getElementById("tot_grey_qnty").value;
			var tot_retn_qnty = this.contentDoc.getElementById("tot_retn_qnty").value;
			var all_po_id = this.contentDoc.getElementById("all_po_id").value;
			var distribution_method = this.contentDoc.getElementById("distribution_method").value;
			var extra_quantity = this.contentDoc.getElementById("extra_quantity").value;


			$('#save_data').val(save_string);
			$('#txt_issue_qnty').val(tot_issue_qnty);
			$('#txt_returnable_qty').val(tot_retn_qnty);
			$('#all_po_id').val(all_po_id);
			$('#distribution_method_id').val(distribution_method);
			$('#extra_quantity').val(extra_quantity);
		}
	}

	function fn_room_rack_self_box() {
		if ($("#cbo_room").val() != 0)
			disable_enable_fields('txt_rack', 0, '', '');
		else {
			reset_form('', '', 'txt_rack*txt_shelf', '', '', '');
			disable_enable_fields('txt_rack*txt_shelf', 1, '', '');
		}
		if ($("#txt_rack").val() != 0)
			disable_enable_fields('txt_shelf', 0, '', '');
		else {
			reset_form('', '', 'txt_shelf', '', '', '');
			disable_enable_fields('txt_shelf', 1, '', '');
		}
	}

	function generate_report_file(data, action, page) {
		window.open("requires/yarn_issue_controller.php?data=" + data + '&action=' + action, true);
	}

	function fnc_yarn_issue_entry(operation) {
		var cbo_basis = $('#cbo_basis').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var no_copy = $("#no_copy").val();
		var knitting_source = document.getElementById("cbo_knitting_source").value;

		if ((operation == 0 || operation == 1) && knitting_source == 1) {
			if (form_validation('cbo_location_id', 'Location') == false) {
				$("#cbo_location_id").css("color", "blue");
				return;
			} else {
				$("#cbo_location_id").css("color", "black");
			}
		}

		//cbo_basis==2 ||
		if ((cbo_basis == 1 || cbo_basis == 4) && cbo_knitting_source == 1) {
			if (form_validation('cbo_knitting_company', 'Knitting Company') == false) {
				$("#knit_com").css("color", "blue");
				return;
			} else {
				$("#knit_com").css("color", "black");
			}
		}

		if (operation == 4 || operation == 16 || operation == 20 || operation == 22 || operation == 23 || operation == 30) {

			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}

			var show_val_column = "0";
			var print_with_vat = 0;

			var report_title = $("div.form_caption").html();
			var print_action = '';

			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}

			if (operation == 4) {
				print_action = 'yarn_issue_print';
			} else if (operation == 16) {
				print_action = 'yarn_issue_store_print';
			} else if (operation == 20) {
				print_action = 'yarn_issue_store_print12';
			} else if (operation == 22) {
				print_action = 'yarn_issue_print14';
			} else if (operation == 23) {
				print_action = 'yarn_issue_print23';
			} else if (operation == 30) {
				print_action = 'yarn_issue_print30';
			}

			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + $('#cbo_basis').val() + '*' + no_copy, print_action, 'requires/yarn_issue_controller');

			return;
		} else if (operation == 25) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}

			var show_val_column = "0";
			var print_with_vat = 0;

			var report_title = $("div.form_caption").html();
			var print_action = '';


			var r = confirm("Press \"OK\" to open with Cust Buyer.\nPress \"Cancel\" to open without Cust Buyer.");
			if (r == true) {
				show_cbuyer_column = "1";
			} else {
				show_cbuyer_column = "0";
			}
			print_action = 'yarn_issue_store_printccl';
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_cbuyer_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + $('#cbo_basis').val() + '*' + $("#cbo_buyer_name").val() + '*' + no_copy, print_action, 'requires/yarn_issue_controller');
			return;
		} else if (operation == 5) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print', 'requires/yarn_issue_controller');

			return;
		} else if (operation == 6) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print2', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 29) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print211', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 21) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print21', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 7 || operation == 19) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();

			var print_action = '';
			if (operation == 7) {
				print_action = 'yarn_issue_print3';
			} else {
				print_action = 'yarn_issue_fso_v2';
			}

			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + $('#txt_booking_no').val(), print_action, 'requires/yarn_issue_controller');
			return;
		} else if (operation == 8) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print8', 'requires/yarn_issue_controller');
			return;
		}
		/*############## created by foysal ##################*/
		else if (operation == 9) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}

			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_store_name').val(), 'yarn_issue_print5', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 10) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}

			var show_val_column = "0";
			var print_with_vat = 0;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print10', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 11) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 0;

			if ($('#checkbox_organic').prop("checked") == true) {
				var organ_print = 1;
			} else {
				var organ_print = 0;
			}

			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + organ_print + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print6', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 12) {

			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}

			var show_val_column = "0";
			var print_with_vat = 0;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}

			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + no_copy + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print12', 'requires/yarn_issue_controller');

			return;
		}
		/*############## created by foysal ##################*/
		else if (operation == 15) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print15', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 27) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_printEKL', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 17) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			// var datas = $("#cbo_knitting_source").val();
			// alert(datas);
			if ($("#cbo_knitting_source").val() == 3) {
				var show_val_column = "0";
				var print_with_vat = 1;
				var r = confirm("Press \"OK\" to open with actual buyer or Cancel.");
				//var r = confirm("'Do you want to print with actual buyer?' Ok/Cancel");
				if (r == true) {
					show_val_column = "1";
				} else {
					show_val_column = "0";
				}
			}
			// else
			// {
			// 	var show_val_column = "0";
			// 	var print_with_vat = 1;
			// 	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");

			// 	if (r == true)
			// 	{
			// 		show_val_column = "1";
			// 	}
			// 	else
			// 	{
			// 		show_val_column = "0";
			// 	}
			// }


			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + no_copy, 'yarn_issue_print17', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 28) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			// var datas = $("#cbo_knitting_source").val();
			// alert(datas);
			if ($("#cbo_knitting_source").val() == 3) {
				var show_val_column = "0";
				var print_with_vat = 1;
				var r = confirm("Press \"OK\" to open with actual buyer or Cancel.");
				//var r = confirm("'Do you want to print with actual buyer?' Ok/Cancel");
				if (r == true) {
					show_val_column = "1";
				} else {
					show_val_column = "0";
				}
			}
			// else
			// {
			// 	var show_val_column = "0";
			// 	var print_with_vat = 1;
			// 	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");

			// 	if (r == true)
			// 	{
			// 		show_val_column = "1";
			// 	}
			// 	else
			// 	{
			// 		show_val_column = "0";
			// 	}
			// }


			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + no_copy, 'yarn_issue_print20', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 18) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			//var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");

			// if (r == true) {
			//  show_val_column = "1";
			// }
			// else {
			//  show_val_column = "0";
			// }
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print11', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 24) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print16', 'requires/yarn_issue_controller');
			return;
		} else if (operation == 26) {
			if ($("#txt_system_no").val() == "") {
				alert("Please Save First.");
				return;
			}
			var show_val_column = "0";
			var print_with_vat = 1;
			var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val(), 'yarn_issue_print_btn17', 'requires/yarn_issue_controller');
			return;
		} else {

			if ($("#is_posted_account").val() == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			var is_approved = $('#is_approved').val();

			if (is_approved == 1) {
				alert("Yarn issue is Approved. So Change Not Allowed");
				return;
			}

			if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date', 'Company Name*Basis*Issue Purpose*Issue Date') == false) {
				return;
			}
			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_issue_date').val(), current_date) == false) {
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}

			var purpose = parseInt($("#cbo_issue_purpose").val());
			if (purpose == 1) {
				if (form_validation('cbo_knitting_source*cbo_knitting_company', 'Knitting Source*Knitting Company') == false) {
					return;
				}
			} else if ((cbo_basis != 3) && (purpose == 4 || purpose == 8)) {
				if (form_validation('txt_booking_no', 'Fabric Booking No.') == false) {
					return;
				}
			} else if (purpose == 5) {
				if (form_validation('cbo_loan_party', 'Loan Party') == false) {
					return;
				}
			}

			if (form_validation('cbo_supplier*txt_lot_no*txt_issue_qnty*cbo_store_name', 'Supplier*Lot No*Issue Quantity*Store Name') == false) {
				return;
			}

			if (($('#cbo_basis').val() * 1 == 1) && ($('#cbo_issue_purpose').val() * 1 == 2)) {
				if (form_validation('cbo_dyeing_color', 'Dyeing Color') == false) {
					return;
				}
			}

			if (($('#cbo_basis').val() * 1 == 1) && ($('#cbo_issue_purpose').val() * 1 == 8)) {
				if (form_validation('cbo_knitting_source*cbo_knitting_company', 'Knitting Source*Knitting Company') == false) {
					return;
				}
			}

			if (operation == 0) {

				if ($('#txt_current_stock').val() <= 0) {
					alert("Current Stock Quantity can not less than Zero");
					return;
				}

				var req_bal = (($("#hdn_requis_qnty").val() * 1) - ($("#hidden_p_issue_qnty").val() * 1)).toFixed(2);
				//if (($('#cbo_basis').val() * 1 == 3) && ($("#txt_issue_qnty").val() * 1 > (($("#hdn_requis_qnty").val() * 1) - ($("#hidden_p_issue_qnty").val() * 1)))) {
				if (($('#cbo_basis').val() * 1 == 3) && ($("#txt_issue_qnty").val() * 1 > req_bal)) {
					alert("Issue Quantity can not be greater than Requisition Quantity.\nRequisition quantity = " + ($("#hdn_requis_qnty").val() * 1).toFixed(2) + "\nPrevious Issue quantity = " + ($("#hidden_p_issue_qnty").val() * 1).toFixed(2) + "\nAvailable Issue quantity = " + req_bal);
					return;
				}
			} else if (operation == 1) {
				if (($('#cbo_basis').val() * 1 == 3) && ($("#txt_issue_qnty").val() * 1 > ($("#hdn_requis_qnty").val() * 1))) {
					alert("Issue Quantity can not be greater than Requisition Quantity.\nRequisition quantity = " + $("#hdn_requis_qnty").val());
					return;
				}
			}

			var comany_name = $("#cbo_company_id").val();
			var hdn_composition_id = $("#hdn_composition_id").val();
			var yarn_type = $("#cbo_yarn_type").val();
			var color_id = $("#hdn_color_id").val();
			var yarn_count = $("#cbo_yarn_count").val();
			var supplier = $("#cbo_supplier").val();
			var txt_lot_no = $("#txt_lot_no").val();
			var txt_prod_id = $("#txt_prod_id").val();

			var dataStr = comany_name + '*' + hdn_composition_id + '*' + yarn_type + '*' + color_id + '*' + yarn_type + '*' + color_id + '*' + yarn_count + '*' + supplier + '*' + txt_lot_no + '*' + txt_prod_id;
			var pi_found = return_global_ajax_value(dataStr, 'find_similer_yarn_in_pi_ajax_responds', '', 'requires/yarn_issue_controller');

			if ('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][3]); ?>' && pi_found > 0) {
				if (form_validation('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][3]); ?>', '<? echo implode('*', $_SESSION['logic_erp']['field_message'][3]); ?>') == false) {
					return;
				}
			}

			// Store upto validation start
			var store_update_upto = $('#store_update_upto').val() * 1;
			var cbo_floor = $('#cbo_floor').val() * 1;
			var cbo_room = $('#cbo_room').val() * 1;
			var txt_rack = $('#txt_rack').val() * 1;
			var txt_shelf = $('#txt_shelf').val() * 1;
			var cbo_bin = $('#cbo_bin').val() * 1;

			if (store_update_upto > 1) {
				if (store_update_upto == 6 && (cbo_floor == 0 || cbo_room == 0 || txt_rack == 0 || txt_shelf == 0 || cbo_bin == 0)) {
					alert("Up To Bin Value Full Fill Required For Inventory");
					return;
				} else if (store_update_upto == 5 && (cbo_floor == 0 || cbo_room == 0 || txt_rack == 0 || txt_shelf == 0)) {
					alert("Up To Shelf Value Full Fill Required For Inventory");
					return;
				} else if (store_update_upto == 4 && (cbo_floor == 0 || cbo_room == 0 || txt_rack == 0)) {
					alert("Up To Rack Value Full Fill Required For Inventory");
					return;
				} else if (store_update_upto == 3 && (cbo_floor == 0 || cbo_room == 0)) {
					alert("Up To Room Value Full Fill Required For Inventory");
					return;
				} else if (store_update_upto == 2 && cbo_floor == 0) {
					alert("Up To Floor Value Full Fill Required For Inventory");
					return;
				}
			}
			// Store upto validation End

			if ((operation == 0 || operation == 1) && cbo_basis == 3 && knitting_source == 1) {
				var hdn_req_info = $('#hdn_req_info').val();
				alert(hdn_req_info);
			}

			var dataString = 'txt_system_no*cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date*txt_booking_no*txt_booking_id*cbo_location_id*cbo_knitting_source*cbo_knitting_company*cbo_supplier*cbo_store_name*txt_challan_no*cbo_loan_party*cbo_buyer_name*txt_style_ref*txt_buyer_job_no*cbo_sample_type*txt_remarks*txt_req_no*txt_lot_no*cbo_yarn_count*cbo_color*cbo_floor*cbo_room*txt_issue_qnty*txt_returnable_qty*txt_composition*cbo_brand*txt_rack*txt_no_bag*txt_no_cone*txt_weight_per_bag*txt_weight_per_cone*cbo_yarn_type*cbo_dyeing_color*txt_shelf*txt_current_stock*cbo_uom*cbo_item*update_id_mst*update_id*save_data*all_po_id*txt_prod_id*job_no*cbo_ready_to_approved*cbo_supplier_lot*txt_btb_lc_id*extra_quantity*txt_entry_form*hidden_p_issue_qnty*hdn_wo_qnty*txt_service_booking_no*demand_id*hdn_req_no*original_save_data*cbo_bin*saved_knitting_company*txt_attention*txt_remarks_dtls*txt_wo_id*txt_pi_id*hdn_fabric_booking_no';

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../");
			freeze_window(operation);
			http.open("POST", "requires/yarn_issue_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_issue_entry_reponse;
		}
	}

	function fnc_yarn_issue_entry_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split('**');
			release_freezing();
			if (reponse[0] * 1 == 20 * 1) {
				alert(reponse[1]);
				return;
			} else if (reponse[0] == 10) {
				show_msg(reponse[0]);
				return;
			} else if (reponse[0] == 11) {
				alert(reponse[1]);
				return;
			} else if (reponse[0] == 23) {
				alert(reponse[1]);
				return;
			} else if (reponse[0] == 30) {

				var returnData = reponse[1].split(',');

				alert("Can't delete, Issue return found accross this issue id" + "\n" + "Issue Return number and qty" + "\n" + returnData[0] + "\n" + returnData[1]);
				return;
			} else if (reponse[0] == 40) {
				alert(reponse[1]);
				return;
			} else if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2) {

				show_msg(reponse[0]);
				$("#txt_system_no").val(reponse[1]);
				$("#update_id_mst").val(reponse[2]);
				$("#saved_knitting_company").val(reponse[3]);

				disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");

				$("#tbl_child").find('select,input:not([name="txt_req_no"],[name="issue_view"])').val('');

				if (reponse[0] == 1 || reponse[0] == 2) {
					$("#cbo_store_name").removeAttr('disabled', 'disabled');
				}

				$("#save_data").val('');
				$("#all_po_id").val('');
				$("#distribution_method_id").val('');
				show_list_view(reponse[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_controller', '');
				set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
			}
		}
	}

	function active_inactive() {
		var basis = parseInt($("#cbo_basis").val());
		var purpose = parseInt($("#cbo_issue_purpose").val());
		if (form_validation('cbo_basis', 'Basis') == false) {
			$("#cbo_issue_purpose").val(0);
			return;
		}

		$('#tbl_child').find('input,select').not('#issue_view').val("");

		if (basis == 1 || basis == 4) {
			$("#txt_booking_no").val('');
			$("#txt_req_no").val('');
			$("#txt_lot_no").val('');
			$("#requisition_item").html('');

			disable_enable_fields('txt_req_no', 1, "", "");
			disable_enable_fields('txt_booking_no*txt_lot_no*cbo_sample_type*cbo_buyer_name', 0, "", "");
		} else if (basis == 3 || basis == 8) //requisition
		{
			$("#txt_booking_no").val('');
			disable_enable_fields('txt_req_no', 0, "", ""); // disable false
			disable_enable_fields('txt_booking_no*txt_lot_no*cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
		} else //idependent
		{
			if (basis == 2 && purpose == 54) {
				$("#txt_issue_qnty").attr('placeholder', 'Entry');
				$("#txt_issue_qnty").removeAttr('ondblclick');
				$("#txt_issue_qnty").removeAttr('readOnly');
			}

			$("#txt_booking_no").val('');
			$("#txt_req_no").val('');
			$("#txt_lot_no").val('');
			$("#requisition_item").html('');

			disable_enable_fields('txt_lot_no*cbo_sample_type*cbo_buyer_name', 0, "", ""); // disable false
			disable_enable_fields('txt_booking_no*txt_req_no', 1, "", ""); // disable false
		}

		if (purpose == 2) {
			document.getElementById('knit_source').innerHTML = 'Dyeing Source';
			document.getElementById('knit_com').innerHTML = 'Dyeing Company';
		} else {
			document.getElementById('knit_source').innerHTML = 'Knitting Source';
			document.getElementById('knit_com').innerHTML = 'Knitting Company';
		}

		if (purpose == 5) {
			$('#cbo_loan_party').removeAttr('disabled', 'disabled');
			$('#loanParty_td').css('color', 'blue');
		} else {
			$('#cbo_loan_party').attr('disabled', 'disabled');
			$('#loanParty_td').css('color', 'black');
		}

		if (basis == 2 || basis == 4) //for 2=>Independent and 4=>Sales Order
		{
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else {
			$("#txt_issue_qnty").attr('placeholder', 'Double Click');
			$("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
			$("#txt_issue_qnty").attr('readOnly', true);
			$("#txt_returnable_qty").attr('readOnly', true);
			$("#txt_returnable_qty").attr('placeholder', 'Display');
		}

		if (basis == 1 && (purpose == 1 || purpose == 12 || purpose == 26 || purpose == 29)) {
			disable_enable_fields('txt_booking_no*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
		} else if (basis == 1 && purpose == 2) {
			disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
			$("#cbo_sample_type").val(0);
			$("#cbo_buyer_name").val(0);
		} else if (basis == 1 && (purpose == 3 || purpose == 5 || purpose == 15 || purpose == 30 || purpose == 38 || purpose == 39 || purpose == 50 || purpose == 51)) {
			disable_enable_fields('cbo_sample_type', 1, "", ""); // disable true
			disable_enable_fields('cbo_buyer_name', 0, "", ""); // disable false
			$("#cbo_sample_type").val(0);
		} else if (basis == 1 && purpose == 4) {
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('cbo_buyer_name', 1, "", ""); // disable true
		} else if (basis == 1 && purpose == 8) {
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company*cbo_buyer_name', 0, "", ""); // disable false
			disable_enable_fields('cbo_buyer_name', 1, "", ""); // disable true
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');

		} else if (basis == 2 && (purpose == 1 || purpose == 29)) {
			disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true
		} else if (basis == 2 && purpose == 12) {
			disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true

			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
		} else if (basis == 2 && purpose == 2) {
			disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			//disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_other_party*cbo_buyer_name', 1, "", ""); // disable true
		} else if (basis == 2 && (purpose == 3 || purpose == 5 || purpose == 15 || purpose == 26 || purpose == 30 || purpose == 38 || purpose == 44 || purpose == 39 || purpose == 50 || purpose == 51)) {
			disable_enable_fields('cbo_buyer_name', 0, "", ""); // disable false
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company', 1, "", ""); // disable true

			if (purpose == 15 || purpose == 50 || purpose == 51) {
				disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			}

			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else if (basis == 2 && purpose == 4) {
			disable_enable_fields('cbo_sample_type*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('txt_booking_no', 1, "", ""); // disable true
		} else if (basis == 2 && purpose == 6) {
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 1, "", ""); // disable true
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else if (basis == 2 && purpose == 7) {
			disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name*cbo_buyer_name', 1, "", ""); // disable true
			disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else if (basis == 2 && purpose == 8) {
			disable_enable_fields('cbo_sample_type*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			//disable_enable_fields( 'cbo_loan_party', 1, "", "" ); // disable true
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else if (basis == 2 && purpose == 10) {
			disable_enable_fields('cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
			disable_enable_fields('cbo_sample_type', 1, "", ""); // disable true
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
			$("#txt_returnable_qty").removeAttr('readOnly');
			$("#txt_returnable_qty").attr('placeholder', 'Entry');
		} else if (basis == 2 && purpose == 54) {
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
		} else if (basis == 4 && (purpose == 1 && purpose == 2 || purpose == 12 || purpose == 26 || purpose == 29 || purpose == 3 || purpose == 5 || purpose == 15 || purpose == 30 || purpose == 38 || purpose == 44 || purpose == 39 || purpose == 50 || purpose == 51)) {
			disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
		} else if (basis == 4 && purpose == 4 && purpose == 8) {
			disable_enable_fields('cbo_sample_type', 0, "", ""); // disable false
		}

		if (purpose == 3) {
			load_drop_down('requires/yarn_issue_controller', document.getElementById('cbo_company_id').value + '_' + 0, 'load_drop_down_buyer', 'buyer_td_id');
		} else {
			load_drop_down('requires/yarn_issue_controller', document.getElementById('cbo_company_id').value + '_' + 1, 'load_drop_down_buyer', 'buyer_td_id');
		}
	}

	function open_mrrpopup() {
		if (form_validation('cbo_company_id', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_id").val();
		var page_link = 'requires/yarn_issue_controller.php?action=mrr_popup&company=' + company;
		var title = "Search Issue Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var sysNumber = this.contentDoc.getElementById("hidden_sys_number").value.split(","); // system number
			$("#txt_system_no").val(sysNumber[0]);
			$("#is_approved").val(sysNumber[1]);
			$("#is_posted_account").val(sysNumber[6]);
		   var newsupplierId = 	$("#supp_id").val(sysNumber[7]);
			if (sysNumber[6] == 1)
				document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting.";
			else
				document.getElementById("accounting_posted_status").innerHTML = "";

			// master part call here
			get_php_form_data(sysNumber[2], "populate_data_from_data", "requires/yarn_issue_controller");
			load_drop_down('requires/yarn_issue_controller', company+'*'+sysNumber[7], 'load_drop_down_supplier_new', 'supplier');
			if (sysNumber[3] == 1) {
				$("#cbo_buyer_name option[value!='0']").remove();
				$("#cbo_buyer_name").append("<option selected value='" + sysNumber[4] + "'>" + sysNumber[5] + "</option>");
			}

			//list view call here
			show_list_view(sysNumber[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_controller', '');

			//for yarn service work order side list view
			var issue_purpose = $('#cbo_issue_purpose').val();
			if (issue_purpose == 7 || issue_purpose == 12 || issue_purpose == 15 || issue_purpose == 38 || issue_purpose == 44 || issue_purpose == 46 || issue_purpose == 50 || issue_purpose == 51) {
				show_list_view($('#txt_booking_id').val(), 'show_yarn_dyeing_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
			}

			disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");
			set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
		}
	}

	//form reset/refresh function here
	function fnResetForm() {
		$("#tbl_master").find('input').attr("disabled", false);
		$("#dyeingColor_td").html('<? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?>');
		$("#tbl_master").find('input,select').attr("disabled", false);
		set_button_status(0, permission, 'fnc_yarn_issue_entry', 1);
		reset_form('yarn_issue_1', 'list_container_yarn*requisition_item', '', '', '', 'cbo_uom');
		document.getElementById("accounting_posted_status").innerHTML = "";
	}

	function generate_report_req(req_id) {
		if ($("#txt_system_no").val() == "") {
			alert("Please Save First.");
			return;
		} else if ($("#txt_req_no").val() == "") {
			alert("Please Select Requisition Number.");
			return;
		} else {
			if ($("#cbo_basis").val() == 3) {
				generate_report_file($("#cbo_company_id").val() + '_' + req_id + '_' + $('#txt_system_no').val(), 'requisition_print', 'requires/yarn_issue_controller');
			} else {
				alert("Basis is not Requisition.");
				return;
			}
		}
	}

	function generate_report_widthout_prog(i) {
		if ($("#txt_system_no").val() == "") {
			alert("Please Save First.");
			return;
		}

		if ($("#cbo_basis").val() == 3) {
			var show_val_column = "0";
			var print_with_vat = 1;
			var report_title = $("div.form_caption").html();
			print_report($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + i + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val(), "yarn_issue_print", "requires/yarn_issue_controller")
		} else {
			alert("Basis is not Requisition.");
			return;
		}
	}

	function load_list_view(str) {
		if (str == "") {
			$('#requisition_item').html('');
			return;
		}
		var demand_id = $('#demand_id').val();
		var cbo_basis = $('#cbo_basis').val();
		var cbo_issue_purpose = $('#cbo_issue_purpose').val();
		var company = $("#cbo_company_id").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();

		show_list_view(str + ',' + company + ',' + cbo_buyer_name + ',' + demand_id + ',' + cbo_basis, 'show_req_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');

		// new
		var return_requisition_data = return_global_ajax_value(str + '**' + company + '**' + cbo_buyer_name, 'find_requisition_data', '', 'requires/yarn_issue_controller').trim();
		var requisition_data_arr = return_requisition_data.split('**');

		var requ_buyer_id = requisition_data_arr[0];
		var prog_no = requisition_data_arr[1];
		var knitting_source = requisition_data_arr[2];
		var knitting_party = requisition_data_arr[3];
		var location_id = requisition_data_arr[4];

		load_drop_down('requires/yarn_issue_controller', knitting_source + '**' + company + '**' + cbo_issue_purpose, 'load_drop_down_knit_com', 'knitting_company_td');
		$('#cbo_knitting_company').val(knitting_party).attr('disabled', 'disabled');
		load_drop_down('requires/yarn_issue_controller', knitting_party + '_' + knitting_source, 'load_drop_down_location', 'location_td');
		if (knitting_source == 1 && cbo_basis == 3) {
			$('#cbo_location_id').val(location_id);
		}

		$('#cbo_knitting_source').val(knitting_source);
		$('#cbo_buyer_name').val(requ_buyer_id);


		//for sample without order
		if ((cbo_basis == 3 || cbo_basis == 8) && with_order == 1) {
			$('#txt_returnable_qty').attr({
				'readonly': true,
				'placeholder': 'Display'
			});
			$('#txt_issue_qnty').attr('readonly', 'readonly').attr('onDblClick', 'openmypage_po()').removeAttr('placeholder').attr('placeholder', 'Double Click');
		} else {
			$('#txt_returnable_qty').attr({
				'readonly': false,
				'placeholder': 'Write'
			});
			$('#txt_issue_qnty').removeAttr('readonly').removeAttr('onDblClick').removeAttr('placeholder').attr('placeholder', 'Write');
		}

	}

	function load_supplier() {
		var issue_purpose = $("#cbo_issue_purpose").val();
		var company = $("#cbo_company_id").val();
		var loan_party = $("#cbo_loan_party").val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			$("#cbo_issue_purpose").val(0);
			return;
		}

		if (issue_purpose == 5) {
			load_drop_down('requires/yarn_issue_controller', company, 'load_drop_down_supplier_loan', 'loanParty');
			if ($('#update_id_mst').val() != '') {
				$('#cbo_loan_party').val(loan_party);
			}
		}
	}

	function load_purpose() {
		var cbo_basis = $("#cbo_basis").val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}
		load_drop_down('requires/yarn_issue_controller', cbo_basis + '_0', 'load_drop_down_purpose', 'issue_purpose_td');
	}

	function change_basis(purpose_id) {
		var purpose_arr = [3, 5, 26, 29, 30, 39, 54];
		var selectedValue = purpose_id * 1;
		if (jQuery.inArray(selectedValue, purpose_arr) !== -1) {
			$("#cbo_basis").val('2');
			$("#txt_issue_qnty").attr('placeholder', 'Entry');
			$("#txt_issue_qnty").removeAttr('ondblclick');
			$("#txt_issue_qnty").removeAttr('readOnly');
		} else {
			if (purpose_id == 8) {
				$("#txt_issue_qnty").attr('placeholder', 'Entry');
				$("#txt_issue_qnty").removeAttr('ondblclick');
				$("#txt_issue_qnty").removeAttr('readOnly');
			} else {
				var basis = $("#cbo_basis").val();
				if (basis != 2 && basis != 4) {
					$("#txt_issue_qnty").attr('placeholder', 'Double Click');
					$("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
				}
			}
		}
	}

	function fn_empty_lot(str_id) {
		var receive_basis = $('#cbo_basis').val();
		var company_id = $('#cbo_company_id').val();
		if (receive_basis == 1 || receive_basis == 3 || receive_basis == 8) {
			var prod_id = $('#txt_prod_id').val();
			if (str_id > 0 && prod_id != "") {
				get_php_form_data(prod_id + '**' + str_id + '**' + company_id, "populate_req_store_data", "requires/yarn_issue_controller");
			} else {
				$('#txt_current_stock').val('');
			}
		} else {
			$('#txt_lot_no').val("");
			$('#txt_prod_id').val("");
			$('#txt_issue_qnty').val("");
			$('#txt_current_stock').val("");
		}
	}

	function openmypage_btb_selection() {

		if (form_validation('cbo_company_id*txt_lot_no', 'Company*Lot No.') == false) {
			return;
		}
		var comany_name = $("#cbo_company_id").val();
		var lot_no = $("#txt_lot_no").val();
		var supplier = $("#cbo_supplier").val();
		var update_id_mst = $("#update_id_mst").val();
		var page_link = 'requires/yarn_issue_controller.php?action=btb_selection_popup&lot_no=' + lot_no + '&supplier=' + supplier + '&comany_name=' + comany_name + '&update_id_mst=' + update_id_mst;
		var title = "Search BTB Selection Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var btb_id = this.contentDoc.getElementById("hidden_btb_id").value;
			var btb_lc_no = this.contentDoc.getElementById("hidden_btb_lc_no").value;

			$('#txt_btb_selection').val(btb_lc_no);
			$('#txt_btb_lc_id').val(btb_id);

		}
	}


	function openmypage_pi_selection() {

		if (form_validation('cbo_company_id*txt_lot_no', 'Company*Lot No.') == false) {
			return;
		}

		var comany_name = $("#cbo_company_id").val();
		var hdn_composition_id = $("#hdn_composition_id").val();
		var yarn_type = $("#cbo_yarn_type").val();
		var color_id = $("#hdn_color_id").val();
		var yarn_count = $("#cbo_yarn_count").val();
		var supplier = $("#cbo_supplier").val();
		var txt_lot_no = $("#txt_lot_no").val();
		var txt_prod_id = $("#txt_prod_id").val();
		//alert(yarn_type+'**'+yarn_count+'**'+hdn_composition_id+'**'+color_id);
		var page_link = 'requires/yarn_issue_controller.php?action=pi_selection_popup&hdn_composition_id=' + hdn_composition_id + '&supplier=' + supplier + '&yarn_type=' + yarn_type + '&color_id=' + color_id + '&yarn_count=' + yarn_count + '&comany_name=' + comany_name + '&txt_lot_no=' + txt_lot_no + '&txt_prod_id=' + txt_prod_id;
		var title = "Search PI Selection Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var pi_id = this.contentDoc.getElementById("hidden_pi_id").value;
			var pi_no = this.contentDoc.getElementById("hidden_pi_no").value;

			$('#txt_pi_selection').val(pi_no);
			$('#txt_pi_id').val(pi_id);

		}

	}

	function openmypage_wo_selection() {

		if (form_validation('cbo_company_id*txt_lot_no', 'Company*Lot No.') == false) {
			return;
		}
		var comany_name = $("#cbo_company_id").val();
		var hdn_composition_id = $("#hdn_composition_id").val();
		var yarn_type = $("#cbo_yarn_type").val();
		var color_id = $("#hdn_color_id").val();
		var yarn_count = $("#cbo_yarn_count").val();
		var supplier = $("#cbo_supplier").val();
		var update_id_mst = $("#update_id_mst").val();
		//alert(yarn_type+'**'+yarn_count+'**'+hdn_composition_id+'**'+color_id);
		var page_link = 'requires/yarn_issue_controller.php?action=wo_selection_popup&hdn_composition_id=' + hdn_composition_id + '&supplier=' + supplier + '&yarn_type=' + yarn_type + '&color_id=' + color_id + '&yarn_count=' + yarn_count + '&comany_name=' + comany_name + '&update_id_mst=' + update_id_mst;
		var title = "Search WO Selection Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=685px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var wo_id = this.contentDoc.getElementById("hidden_wo_id").value;
			var wo_no = this.contentDoc.getElementById("hidden_wo_no").value;

			$('#txt_wo_selection').val(wo_no);
			$('#txt_wo_id').val(wo_id);

		}
	}

	function independence_basis_controll_function(data) {
		var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
		$("#cbo_basis option[value='2']").show();
		$("#cbo_basis").val(0);
		if (independent_control_arr && independent_control_arr[data] == 1) {
			$("#cbo_basis option[value='2']").hide();
		}

		var status = return_global_ajax_value(data, 'upto_variable_settings', '', 'requires/yarn_issue_controller').trim();
		$('#store_update_upto').val(status);
	}

	function openmypage_issue_view() {
		if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_lot_no', 'Company*Basis*Issue Purpose*Lot Number') == false) {
			return;
		} else if (receive_basis == 1 && (purpose == 1 || purpose == 2 || purpose == 4 || purpose == 12 || purpose == 15 || purpose == 38 || purpose == 44 || purpose == 46 || purpose == 50 || purpose == 51)) {
			if (form_validation('txt_booking_no', 'Booking') == false) {
				return;
			}
		} else if (receive_basis == 3) {
			if (form_validation('txt_req_no', 'Requisition. No') == false) {
				return;
			}
		}

		var purpose = $("#cbo_issue_purpose").val();
		var receive_basis = $('#cbo_basis').val();
		var booking_no = $('#txt_booking_no').val();
		var cbo_company_id = $('#cbo_company_id').val();

		//var save_data = $('#save_data').val();
		//var original_save_data = $('#original_save_data').val();
		//var distribution_method = $('#distribution_method_id').val();
		//var update_id = $('#update_id').val();

		var all_po_id = $('#all_po_id').val();
		var issueQnty = $('#txt_issue_qnty').val();
		var retnQnty = $('#txt_returnable_qty').val();
		var job_no = $('#job_no').val();
		var buyer_job_no = $('#txt_buyer_job_no').val();
		var txt_lot_no = $('#txt_lot_no').val();
		var txt_prod_id = $('#txt_prod_id').val();
		var req_no = $('#txt_req_no').val();
		var extra_quantity = $('#extra_quantity').val();
		var entry_form = $('#txt_entry_form').val();
		var demand_id = $('#demand_id').val();

		var data_ref = 'requires/yarn_issue_controller.php?action=existing_issues_view&cbo_company_id=' + cbo_company_id + '&job_no=' + job_no + '&req_no=' + req_no + '&receive_basis=' + receive_basis + '&purpose=' + purpose + '&txt_prod_id=' + txt_prod_id;
		//alert(data_ref);
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', data_ref, 'Issue Details', 'width=1100px,height=200px,center=1,resize=1,scrolling=0', '');
	}

	// ==============End Floor Room Rack Shelf Bin disable============
	function storeUpdateUptoDisable() {
		//$('#txt_current_stock').val("");
		$('#cbo_floor').prop("disabled", true);
		$('#cbo_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_shelf').prop("disabled", true);
		$('#cbo_bin').prop("disabled", true);
	}
	// ==============End Floor Room Rack Shelf Bin disable============

	//for func_multiple_issue_no_print
	function func_multiple_issue_no_print(type) {
		if (form_validation('cbo_company_id*txt_system_no', 'Company Name*System Number') == false) {
			return;
		}
		//cbo_basis
		//cbo_issue_purpose

		var company = $("#cbo_company_id").val();
		var issue_basis = $("#cbo_basis").val();
		var issue_purpose = $("#cbo_issue_purpose").val();
		var knitting_source = $("#cbo_knitting_source").val();
		var txt_system_no = $("#txt_system_no").val();
		var update_id_mst = $("#update_id_mst").val();
		var txt_issue_date = $("#txt_issue_date").val();

		if (type == 1) {
			var page_link = 'requires/yarn_issue_controller.php?action=multiple_issue_no_popup&company=' + company + '&issue_basis=' + issue_basis + '&issue_purpose=' + issue_purpose + '&knitting_source=' + knitting_source;
		} else if (type == 2) {
			var page_link = 'requires/yarn_issue_controller.php?action=multiple_issue_no_popup2&company=' + company + '&issue_basis=' + issue_basis + '&issue_purpose=' + issue_purpose + '&knitting_source=' + knitting_source + '&txt_system_no=' + txt_system_no + '&update_id_mst=' + update_id_mst + '&txt_issue_date=' + txt_issue_date;
		}

		var title = "Search Issue No Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0', ' ')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			//var returnNumber=this.contentDoc.getElementById("hidden_return_number").value;
			var issue_id = this.contentDoc.getElementById("hnd_issue_id").value;
			var report_title = $("div.form_caption").html();
			if (type == 1) {
				print_report(issue_id + '*' + $('#cbo_company_id').val(), 'multiple_issue_no_print', 'requires/yarn_issue_controller');
			} else if (type == 2) {

				var report_title = $("div.form_caption").html();
				var no_copy = $("#no_copy").val();
				var show_val_column = "0";
				var print_with_vat = 0;


				if ($("#cbo_knitting_source").val() == 3) {
					var show_val_column = "0";
					var print_with_vat = 1;
					var r = confirm("Press \"OK\" to open with actual buyer or Cancel.");
					//var r = confirm("'Do you want to print with actual buyer?' Ok/Cancel");
					if (r == true) {
						show_val_column = "1";
					} else {
						show_val_column = "0";
					}
				}

				print_report(issue_id + '*' + $('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat + '*' + $('#cbo_location_id').val() + '*' + $('#cbo_store_name').val() + '*' + no_copy, 'multiple_issue_no_print2', 'requires/yarn_issue_controller');
			}
			return;
		}
	}

	/*
	Yarn Issue> Sample Non Order Yarn Returnable qty Formula : Yarn Issue qty- Distribution Qnty, When issue qty will over than distribution qty then that qty will show Returnable Qty. field and this field will disable, Need proper work for single challan and multiple challan. Note: Returnable Qty. field Minus qty will not show just plus qty will come*/

	function calculate_smn_returnable_qnty(given_issue) {
		var basis = $("#cbo_basis").val();
		var purpose = $("#cbo_issue_purpose").val();
		var issue_trans_id = $("#update_id").val();
		var user_given_qnty = given_issue * 1;

		if (basis != 1 && purpose != 8) {
			var smn_requsition = $("#hdn_smn_requsition").val() * 1;
			var smn_distribution_qnty = $("#hdn_distribution_qnty").val() * 1;
			var smn_cum_issue_qnty = $("#hdn_cum_issue_qnty").val() * 1;
			var smn_cum_returnable_qnty = $("#hdn_cum_returnable_qnty").val() * 1;

			var returnable_qnty = ((smn_cum_issue_qnty - smn_cum_returnable_qnty + user_given_qnty) - smn_distribution_qnty);
		} else {
			var smn_booking_no = $("#txt_booking_no").val();
			var yarn_count = $("#cbo_yarn_count").val();
			var yarn_composition = $("#hdn_composition_id").val();
			var dataStr = smn_booking_no + '**' + yarn_count + '**' + yarn_composition + '**' + issue_trans_id;
			var responds_string = return_global_ajax_value(dataStr, 'find_smnbooking_required_qnty_ajax_responds', '', 'requires/yarn_issue_controller');
			var responds_array = responds_string.split("***");
			var smn_booking_required_qnty = responds_array[0]
			var smn_cum_issue_qnty = responds_array[1]
			var smn_cum_returnable_qnty = responds_array[2]

			//alert("( (" + smn_cum_issue_qnty + "-" + smn_cum_returnable_qnty + "+" + user_given_qnty + ")-" + smn_booking_required_qnty + ")");

			var returnable_qnty = (((smn_cum_issue_qnty - smn_cum_returnable_qnty) + user_given_qnty) - smn_booking_required_qnty);
		}

		//alert(returnable_qnty);

		if (returnable_qnty < 0) {
			returnable_qnty = 0;
		} else {
			returnable_qnty = returnable_qnty;
		}

		$("#txt_returnable_qty").val(number_format_common(returnable_qnty, 2));
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs("../", $permission); ?><br />
		<form name="yarn_issue_1" id="yarn_issue_1" autocomplete="off">
			<div style="width:980px; float:left; position:relative" align="center">
				<table width="80%" cellpadding="0" cellspacing="2">
					<tr>
						<td width="100%" align="center" valign="top">
							<fieldset style="width:980px;">
								<legend>Yarn Issue</legend>
								<br />
								<fieldset style="width:950px;">
									<table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
										<tr>
											<td colspan="6" align="center"><b>System ID</b>
												<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
											</td>
										</tr>
										<tr>
											<td width="120" align="right" class="must_entry_caption">Company Name</td>
											<td width="170">
												<?
												echo create_drop_down("cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_issue_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/yarn_issue_controller',this.value+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/yarn_issue_controller' ); independence_basis_controll_function(this.value);load_drop_down( 'requires/yarn_issue_controller', this.value, 'load_drop_down_basis', 'receive_baisis_td' );load_room_rack_self_bin('requires/yarn_issue_controller*1', 'store','store_td', this.value,'','','','','','','','fn_empty_lot(this.value);');set_field_level_access(this.value);");
												?>
												<input type="hidden" id="supp_id">
											</td>
											<td width="120" align="right" class="must_entry_caption">Basis</td>
											<td width="160" id="receive_baisis_td">
												<?
												echo create_drop_down("cbo_basis", 170, $issue_basis, "", 1, "-- Select Basis --", $selected, "active_inactive();load_purpose();", "", "");
												?>
											</td>
											<td width="120" align="right" class="must_entry_caption">Issue Purpose</td>
											<td id="issue_purpose_td">
												<?
												echo create_drop_down("cbo_issue_purpose", 170, $blank_array, "", 1, "-- Select Purpose --", $selected, "", "", "");
												?>
											</td>
										</tr>
										<tr>
											<td align="right" class="must_entry_caption">Issue Date</td>
											<td>
												<input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" value="<? echo date('d-m-Y'); ?>" readonly />
											</td>
											<td align="right">Fab Booking / FSO No</td>
											<td>
												<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px" placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly />
												<input type="hidden" name="txt_booking_id" id="txt_booking_id" />
												<input type="hidden" name="txt_entry_form" id="txt_entry_form" />
												<input type="hidden" name="hdn_fabric_booking_no" id="hdn_fabric_booking_no" />
											</td>
											<td align="right" id="knit_source">Knitting Source</td>
											<td width="170">
												<?
												echo create_drop_down("cbo_knitting_source", 170, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_issue_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_knit_com', 'knitting_company_td' );", "", "1,3");
												?>
											</td>
										</tr>
										<tr>
											<td align="right" id="knit_com"> Issue To</td>
											<td id="knitting_company_td">
												<? echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", ""); ?>
											</td>
											<td align="right" id="caption_location">Location</td>
											<td id="location_td">
												<? echo create_drop_down("cbo_location_id", 170, $blank_array, "", 1, "-- Select Location --", "", ""); ?>
											</td>
											<td align="right" class="must_entry_caption" id="supplier_td">Supplier</td>
											<td id="supplier">
												<? echo create_drop_down("cbo_supplier", 170, $blank_array, "", 1, "-- Select --", 0, "", 1); ?>
											</td>
										</tr>
										<tr>

											<td align="right">Challan/Program No</td>
											<td>
												<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry">
											</td>
											<td id="loanParty_td" align="right">Loan Party</td>
											<td id="loanParty">
												<? echo create_drop_down("cbo_loan_party", 170, $blank_array, "", 1, "-- Select Party --", $selected, "", 1); ?>
											</td>
											<td align="right">Sample Type</td>
											<td><? echo create_drop_down("cbo_sample_type", 170, "select id,sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name", "id,sample_name", 1, "-- Select --", $selected, "", "", "");
												?>
											</td>
										</tr>
										<tr>
											<td align="right">Buyer Name</td>
											<td id="buyer_td_id">
												<? echo create_drop_down("cbo_buyer_name", 170, $blank_array, "", 1, "-- Select Buyer --", 0, "", 1); ?>
											</td>
											<td align="right">Style Reference</td>
											<td>
												<input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:160px" readonly placeholder="Display" />
											</td>
											<td align="right">Buyer Job No</td>
											<td>
												<input type="text" name="txt_buyer_job_no" id="txt_buyer_job_no" class="text_boxes" style="width:160px" readonly placeholder="Display" />
											</td>
										</tr>
										<tr>
											<td align="right">Service Booking</td>
											<td>
												<input type="text" name="txt_service_booking_no" id="txt_service_booking_no" class="text_boxes" onDblClick="openmypage_service_booking()" placeholder="Browse or Write" readonly style="width:150px;" />
											</td>
											<td align="right">Ready to Approve</td>
											<td>
												<?
												echo create_drop_down("cbo_ready_to_approved", 172, $yes_no, "", 1, "-- Select--", 2, "", "", "");
												?>
											</td>
											<td align="right">Attention</td>
											<td>
												<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:160px;" />
											</td>
										</tr>
										<tr>
											<td align="right">Remarks</td>
											<td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:480px" placeholder="Entry" /></td>
										</tr>
										<tr>
											<td align="right">&nbsp;</td>
											<td colspan="3">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
									<tr>
										<td width="49%" valign="top">
											<fieldset style="width:950px;">
												<legend>New Issue Item</legend>
												<table width="100%" cellspacing="2" cellpadding="0" border="0">
													<tr>
														<td width="110" align="right">Requs./Demand No</td>
														<td>
															<input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" onDblClick="openmypage_requis()" placeholder="Browse or Write" style="width:150px;" onBlur="load_list_view(this.value);" />
															<input type="hidden" name="demand_id" id="demand_id">
															<input type="hidden" name="hdn_req_no" id="hdn_req_no">
															<input type="hidden" name="hdn_req_info" id="hdn_req_info">
														</td>
														<td align="right">Composition</td>
														<td>
															<input type="text" name="txt_composition" id="txt_composition" class="text_boxes" style="width:130px;" placeholder="Display" readonly>
															<input type="hidden" name="txt_composition_id" id="txt_composition_id">
															<input type="hidden" name="txt_composition_percent" id="txt_composition_percent">
															<input type="hidden" name="hdn_composition_id" id="hdn_composition_id">

														</td>
														<td align="right">UOM</td>
														<td><? echo create_drop_down("cbo_uom", 162, $unit_of_measurement, "", 1, "--Select--", $selected, "", 1); ?></td>
														<td align="right" class="must_entry_caption">Store</td>
														<td id="store_td">
															<?
															echo create_drop_down("cbo_store_name", 162, $blank_array, "", 1, "-- Select Store --", 0, "fn_empty_lot(this.value);", 0);
															?>
														</td>
													</tr>
													<tr>
														<td width="110" align="right" class="must_entry_caption">Lot No</td>
														<td>
															<input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" onDblClick="openmypage_lot()" placeholder="Double Click" style="width:150px;" readonly />
															<input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly />
														</td>
														<td align="right">Weight per Bag</td>
														<td>
															<input name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" type="text" style="width:130px;" placeholder="Entry" />
														</td>
														<td align="right">Yarn Type</td>
														<td><? echo create_drop_down("cbo_yarn_type", 162, $yarn_type, "", 1, "--Select--", 0, "", 1); ?></td>
														<td align="right">Floor</td>
														<td id="floor_td">
															<? echo create_drop_down("cbo_floor", 162, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td align="right" class="must_entry_caption">Issue Qty.</td>
														<td>
															<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:100px;" placeholder="Double Click" readonly onDblClick="openmypage_po()" onblur="calculate_smn_returnable_qnty(this.value)" />

															<input type="hidden" name="hidden_p_issue_qnty" id="hidden_p_issue_qnty" readonly />
															<input type="hidden" name="extra_quantity" id="extra_quantity" readonly />
															<input type="hidden" name="hdn_requis_qnty" id="hdn_requis_qnty" readonly />
															<input type="hidden" name="hdn_wo_qnty" id="hdn_wo_qnty" readonly />

															<input type="hidden" name="hdn_smnbooking_required_qnty" id="hdn_smnbooking_required_qnty" readonly />
															<input type="hidden" name="hdn_smn_requsition" id="hdn_smn_requsition" readonly />
															<input type="hidden" name="hdn_distribution_qnty" id="hdn_distribution_qnty" readonly />
															<input type="hidden" name="hdn_cum_issue_qnty" id="hdn_cum_issue_qnty" readonly />
															<input type="hidden" name="hdn_cum_returnable_qnty" id="hdn_cum_returnable_qnty" readonly />

															<input type="button" name="issue_view" id="issue_view" value="View" onClick="openmypage_issue_view()" style="width: 46px;" class="formbutton">
														</td>
														<td align="right">Wght @ Cone</td>
														<td><input class="text_boxes_numeric" name="txt_weight_per_cone" id="txt_weight_per_cone" type="text" style="width:130px;" placeholder="Entry" /></td>
														<td align="right">Color</td>
														<td>
															<select id="cbo_color" name="cbo_color" class="combo_boxes" style="width:162px;" disabled="disabled">
																<option value="0">--Select--</option>
															</select>
															<input type="hidden" name="hdn_color_id" id="hdn_color_id">
														</td>
														<td align="right">Room</td>
														<td id="room_td">
															<?
															echo create_drop_down("cbo_room", 162, $blank_array, "", 1, "--Select--", 0, "", 0);
															?>

														</td>
													</tr>
													<tr>
														<td align="right">Current Stock</td>
														<td>
															<input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly />
														</td>
														<td align="right">No. Of Cone</td>
														<td>
															<input type="text" name="txt_no_cone" id="txt_no_cone" class="text_boxes_numeric" style="width:130px;" placeholder="Entry" />
														</td>
														<td align="right">Brand</td>
														<td>
															<?
															echo create_drop_down("cbo_brand", 162, $blank_array, "", 1, "--Select--", "", "", 1);
															?>
														</td>
														<td align="right">Rack</td>
														<td id="rack_td">
															<? echo create_drop_down("txt_rack", 162, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td align="right">No. Of Bag</td>
														<td><input type="text" name="txt_no_bag" id="txt_no_bag" class="text_boxes_numeric" style="width:150px;" placeholder="Entry" /></td>
														<td align="right">Dyeing Color</td>
														<td id="dyeingColor_td"><? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?></td>
														<td align="right">Yarn Count</td>
														<td>
															<?
															echo create_drop_down("cbo_yarn_count", 162, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "--Select--", 0, "", 1);
															?>
														</td>
														<td align="right">Shelf</td>
														<td id="shelf_td">
															<? echo create_drop_down("txt_shelf", 162, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
															<!-- <select id="txt_shelf" name="txt_shelf" class="combo_boxes " style="width:162px">
                                                            <option value="">--Select--</option>
                                                        </select> -->
														</td>

													</tr>
													<tr>
														<td align="right">Returnable Qty.</td>
														<td><input type="text" name="txt_returnable_qty" id="txt_returnable_qty" class="text_boxes_numeric" placeholder="Display" style="width:150px;" readonly />
														</td>
														<td align="right">Supplier</td>
														<td>
															<?
															echo create_drop_down("cbo_supplier_lot", 142, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0", "id,supplier_name", 1, "-- Display --", 0, "", 1);
															?>
														</td>
														<td align="right">BTB Selection</td>
														<td>
															<input type="text" class="text_boxes" id="txt_btb_selection" name="txt_btb_selection" value="" onDblClick="openmypage_btb_selection()" placeholder="Double Click" style="width:150px;" readonly>
															<input type="hidden" class="text_boxes" id="txt_btb_lc_id" name="txt_btb_lc_id" value="">
														</td>
														<td height="18" align="right">Bin Box</td>
														<td id="bin_td">
															<? echo create_drop_down("cbo_bin", 162, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td align="right">Using Item</td>
														<td>
															<?
															echo create_drop_down("cbo_item", 162, $using_item_arr, "", 1, "--Select--", "", "", 0);
															?>
														</td>
														<td align="right">PI Selection</td>
														<td>
															<input type="text" class="text_boxes" id="txt_pi_selection" name="txt_pi_selection" value="" onDblClick="openmypage_pi_selection()" placeholder="Double Click" style="width:130px;" readonly>
															<input type="hidden" class="text_boxes" id="txt_pi_id" name="txt_pi_id" value="">
														</td>
														<td align="right">WO Selection</td>
														<td>
															<input type="text" class="text_boxes" id="txt_wo_selection" name="txt_wo_selection" value="" onDblClick="openmypage_wo_selection()" placeholder="Double Click" style="width:150px;" readonly>
															<input type="hidden" class="text_boxes" id="txt_wo_id" name="txt_wo_id" value="">
														</td>
													</tr>
													<tr>
														<td align="right">Remarks</td>
														<td colspan="3"><input type="text" name="txt_remarks_dtls" id="txt_remarks_dtls" class="text_boxes" style="width:370px" placeholder="Entry" /></td>
													</tr>
												</table>
											</fieldset>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="1" width="100%">
									<tr>
										<td colspan="6" align="center"></td>
									</tr>
									<tr>
										<td align="center" colspan="6" valign="middle" class="button_container">
											<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
											<!-- details table id for update -->
											<input type="hidden" id="is_approved" name="is_approved" value="" readonly />
											<input type="hidden" id="update_id_mst" name="update_id_mst" readonly />
											<input type="hidden" id="update_id" name="update_id" readonly />

											<input type="hidden" name="save_data" id="save_data" readonly />
											<input type="hidden" name="original_save_data" id="original_save_data" readonly />

											<input type="hidden" name="saved_knitting_company" id="saved_knitting_company">

											<input type="hidden" name="all_po_id" id="all_po_id" readonly />
											<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
											<input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
											<!--Check Posted in account-->
											<input type="hidden" name="job_no" id="job_no" readonly />
											<input type="hidden" name="yarn_rate_match" id="yarn_rate_match" readonly />
											<input type="hidden" name="store_update_upto" id="store_update_upto">
											<input type="hidden" name="hdn_dmnd_req_id" id="hdn_dmnd_req_id">
											<!--For Basis Bokking and Yarn Dyeing Purpose-->&nbsp;
											<? echo load_submit_buttons($permission, "fnc_yarn_issue_entry", 0, 0, "fnResetForm()", 1); ?>

											<input type="button" name="print" id="Printt1" value="Print" onClick="fnc_yarn_issue_entry(4)" style="width: 80px; display:none;" class="formbutton">

											<input type="button" name="print_vat" id="print_vat1" value="Print 2" onClick="fnc_yarn_issue_entry(6)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="print_vat" id="print_vat2" value="Print 3" onClick="fnc_yarn_issue_entry(7)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print_vat" id="print_vat4" value="Print 4" onClick="fnc_yarn_issue_entry(10)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print_vat" id="print_vat3" value="Print With VAT" onClick="fnc_yarn_issue_entry(5)" style="width:100px;display:none;" class="formbutton" />

											<input type="button" name="print_vat" id="print_7" value="Print 7" onClick="fnc_yarn_issue_entry(12)" style="width:100px;display:none;" class="formbutton" /> <input type="text" value="1" title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;display:none;" />
											<input type="button" name="print_vat" id="print_vat8" value="Print Outbound" onClick="fnc_yarn_issue_entry(8)" style="width:100px;display:none;" class="formbutton" />

											<input type="button" name="print_vat" id="print_vat9" value="Print 5" onClick="fnc_yarn_issue_entry(9)" style="width:100px;display:none;" class="formbutton" />

											<input type="button" name="search" id="search1" value="Requisition Details" onClick="generate_report_req(document.getElementById('txt_req_no').value)" style="width:130px;display:none;" class="formbutton" />

											<input type="button" name="without_prog" id="without_prog1" value="Without Program" onClick="generate_report_widthout_prog(1)" style="width:130px;display:none;" class="formbutton" />

											<input type="button" name="print_vat" id="print_vat15" value="Print 8" onClick="fnc_yarn_issue_entry(15)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print" id="Printt16" value="Print 9" onClick="fnc_yarn_issue_entry(16)" style="width: 80px; display:none;" class="formbutton">
											<input type="button" name="print_vat" id="print_vat17" value="Print 10" onClick="fnc_yarn_issue_entry(17)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="print_vat" id="print_vat28" value="Print 20" onClick="fnc_yarn_issue_entry(28)" style="width:80px;display:none;" class="formbutton" />


											<input type="button" name="print_vat11" id="print_vat11" value="Print 11" onClick="fnc_yarn_issue_entry(18)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print_fso_v2" id="print_fso_v2" value="FSO(V2)" onClick="fnc_yarn_issue_entry(19)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print_6" id="print_6" value="Print6" onClick="fnc_yarn_issue_entry(11)" style="width:100px;display:none;" class="formbutton" /><span style="font-weight:bold;display:none;" id="organic_check">
												ORGANIC <input type="checkbox" id="checkbox_organic"></span>
											<input type="button" name="multiple_issue_no_print" value="Print Multi Issue No" id="multiple_issue_no_print" class="formbutton" style="width: 120px;display:none;" onClick="func_multiple_issue_no_print(1)" />
											<input type="button" name="multiple_issue_no_print2" value="Print Multi Issue No 2" id="multiple_issue_no_print2" class="formbutton" style="width: 120px;display:none;" onClick="func_multiple_issue_no_print(2)" />
											<input type="button" name="print12" id="Printt12" value="Print 12" onClick="fnc_yarn_issue_entry(20)" style="width: 80px;display:none;" class="formbutton">
											<input type="button" name="print_vat" id="Printt13" value="Print 13" onClick="fnc_yarn_issue_entry(21)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="Printt14" id="Printt14" value="Print 14" onClick="fnc_yarn_issue_entry(22)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="Printt15" id="Printt15" value="Print 15" onClick="fnc_yarn_issue_entry(23)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="Printt17" id="Printt17" value="Print 16" onClick="fnc_yarn_issue_entry(24)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="print18" id="print18" value="Print 18" onClick="fnc_yarn_issue_entry(25)" style="width: 80px;display:none;" class="formbutton">
											<input type="button" name="Printb17" id="Printb17" value="Print 17" onClick="fnc_yarn_issue_entry(26)" style="width:80px;display:none;" class="formbutton" />

											<input type="button" name="print_EKL" id="print_vatEKL" value="Print EKL" onClick="fnc_yarn_issue_entry(27)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="print_vat21" id="print_vat21" value="Print 21" onClick="fnc_yarn_issue_entry(29)" style="width:80px;display:none;" class="formbutton" />
											<input type="button" name="print22" id="print22" value="Print 22" onClick="fnc_yarn_issue_entry(30)" style="width:80px;display:none;" class="formbutton" />
											<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
										</td>
									</tr>
								</table>
							</fieldset>
							<fieldset>
								<div style="width:970px;" id="list_container_yarn"></div>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
			<div style="float:left; position:relative; margin-left:15px" align="left" id="requisition_item"></div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>

</html>