<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Yarn Serviec Work Order

Functionality	:

JS Functions	:

Created by		:	Md. Didarul Alam
Creation date 	: 	07-07-2019
Updated by 		:
Update date		:

QC Performed BY	:

QC Date			:
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Service Work Order", "../../", 1, 1, $unicode, '', '');
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	//-------------------------------function---------------------------------------------------------------

	function fnc_yarn_service_wo(operation) {
		if (form_validation('cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*cbo_pay_mode*txt_delivery_date', 'Company Name *Service Type*Factory*Booking Date*Pay Mode*Delivery Date') == false) {
			return;
		} else {
			var j = 0;
			var dtlsDataString = '';
			var cbo_with_order = $("#cbo_with_order").val();
			var cbo_service_type = $("#cbo_service_type").val();
			var validation = true;

			$("#dtls_container tbody tr").each(function() {
				j++;
				var txtJobNo = trim($("#txtJobNo_" + j).val());
				var txtJobId = $("#txtJobId_" + j).val();
				var cboCount = $("#cboCount_" + j).val();
				var cboComposition = $("#cboComposition_" + j).val();
				var txtParcent = $("#txtParcent_" + j).val();
				var cboYarnType = $("#cboYarnType_" + j).val();
				var yernColor = $("#yernColor_" + j).val();
				var cboColorRange = $("#cboColorRange_" + j).val();
				var cboUom = $("#cboUom_" + j).val();
				var txtWoQty = $("#txtWoQty_" + j).val();
				var txtRate = $("#txtRate_" + j).val();
				var txtAmount = $("#txtAmount_" + j).val();
				var txtBag = $("#txtBag_" + j).val();
				var txtCone = $("#txtCone_" + j).val();
				var txtMinReqCone = $("#txtMinReqCone_" + j).val();
				var txtRemarks = $("#txtRemarks_" + j).val();
				var dtlsUpdateId = $("#dtlsUpdateId_" + j).val();

				if (cboCount == "" || cboComposition == "" || txtParcent == "" || cboYarnType == "" || txtWoQty == "" || txtRate == "") {
					validation = false;
					return;
				}

				if ((cbo_with_order == 1 && txtJobNo == "")) {
					validation = false;
					return;
				}

				dtlsDataString += '&txtJobNo_' + j + '=' + txtJobNo + '&txtJobId_' + j + '=' + txtJobId + '&cboCount_' + j + '=' + cboCount + '&cboComposition_' + j + '=' + cboComposition + '&txtParcent_' + j + '=' + txtParcent + '&cboYarnType_' + j + '=' + cboYarnType + '&cboUom_' + j + '=' + cboUom + '&txtWoQty_' + j + '=' + txtWoQty + '&txtRate_' + j + '=' + txtRate + '&txtAmount_' + j + '=' + txtAmount + '&txtBag_' + j + '=' + txtBag + '&txtCone_' + j + '=' + txtCone + '&txtMinReqCone_' + j + '=' + txtMinReqCone + '&txtRemarks_' + j + '=' + txtRemarks + '&dtlsUpdateId_' + j + '=' + dtlsUpdateId;

			});

			if (cbo_service_type == 15 || cbo_service_type == 50 || cbo_service_type == 51) {
				if (form_validation('cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color', 'Count*Composition*%*Type*Color') == false) {
					return;
				}
			}

			if (validation == false) {
				alert("Required fields can not be empty");
				return;
			} else {
				var twistingDataStr = "*cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color*hdn_fin_update_id";
				var mstDataString = "cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*txt_attention*txt_tenor*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*txt_delivery_date*cbo_is_sales_order*update_id*txt_booking_no*cbo_with_order*update_dtls_ids*txt_ref_no" + twistingDataStr;

				var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string(mstDataString, "../../") + dtlsDataString;
				freeze_window(operation);
				http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_yarn_service_wo_response;
			}
		}
	}

	function fnc_yarn_service_wo_response() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split('**');
			if (response[0] == 0 || response[0] == 1 || response[0] == 2) {
				show_msg(trim(response[0]));
				disable_enable_fields('cbo_company_name*cbo_service_type*cbo_pay_mode*cbo_currency', 1, "", "");
				release_freezing();

			} else if (response[0] == 10 || response[0] == 11) {
				show_msg(trim(response[0]));
				release_freezing();
				return;
			} else if (response[0] == 22) {
				alert(response[1]);
				release_freezing();
				return;
			}

			show_msg(trim(response[0]));
			release_freezing();
			$("#update_id").val(response[2]);
			$("#txt_booking_no").val(response[1]);
			$('#cbo_is_sales_order').attr('disabled', 'disabled');
			var cbo_service_type = $("#cbo_service_type").val();
			show_list_view(response[2] + "_" + cbo_service_type, 'show_dtls_list_view', 'list_container', 'requires/yarn_service_work_order_without_lot_controller', '');
			if (response[0] == 0) {
				set_button_status(0, permission, 'fnc_yarn_service_wo', 1, 1);
			} else {
				set_button_status(0, permission, 'fnc_yarn_service_wo', 1, 1);
			}

			$reset_fin_prod_dtls = "";
			if (cbo_service_type == 15 || cbo_service_type == 50 || cbo_service_type == 51) {
				$reset_fin_prod_dtls = "*cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color";
			}

			reset_form('', '', 'txtJobNo_1*txtJobId_1*txtJobId_1*cboCount_1*cboComposition_1*txtParcent_1*cboYarnType_1*yernColor_1*cboColorRange_1*cboUom_1*txtWoQty_1*txtRate_1*txtAmount_1*txtBag_1*txtCone_1*txtMinReqCone_1*txtRemarks_1*dtlsUpdateId_1' + $reset_fin_prod_dtls, '', '', 'update_id*txt_booking_no*cbo_is_sales_order*');

			$('#dtls_container tbody tr:not(:first)').remove();

			show_msg(trim(response[0]));
			release_freezing();
		}
	}


	//=======================================new ================================
	function add_break_down_tr(i) { //*txtPoNo_'+i
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var row_num = $('#dtls_container tbody tr').length;
		if (row_num != i) {
			return false;
		} else {
			i++;

			$("#dtls_container tbody tr:last").clone().find("input,select").each(function() {

				$(this).attr({
					'id': function(_, id) {
						var id = id.split("_");
						return id[0] + "_" + i
					},
					'name': function(_, name) {
						var name = name.split("_");
						return name[0] + "_" + i
					},
					'value': function(_, value) {
						return value
					}
				});

			}).end().appendTo("#dtls_container");

			$("#dtls_container tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);

			$('#dtlsUpdateId_' + i).removeAttr("value").attr("value", "");

			$('#txtJobNo_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_po(" + i + ",'Job Search');");
			$('#txtJobNo_' + i).attr("disabled", "disabled");
			$('#cboCount_' + i).removeAttr("value").attr("value", "0");
			$('#cboComposition_' + i).removeAttr("value").attr("value", "0");
			//$('#txtParcent_'+i).removeAttr("value").attr("value","");
			$('#cboYarnType_' + i).removeAttr("value").attr("value", "0");
			$('#txtWoQty_' + i).removeAttr("value").attr("value", "");
			$('#txtWoQty_' + i).attr("onKeyUp", "fnc_calculate(this," + i + ")");
			$('#txtRate_' + i).removeAttr("value").attr("value", "");
			$('#txtRate_' + i).attr("onKeyUp", "fnc_calculate(this," + i + ")");;
			$('#txtAmount_' + i).removeAttr("value").attr("value", "");
			$('#txtBag_' + i).removeAttr("value").attr("value", "");
			$('#txtCone_' + i).removeAttr("value").attr("value", "");
			$('#txtMinReqCone_' + i).removeAttr("value").attr("value", "");
			$('#txtRemarks_' + i).removeAttr("value").attr("value", "");

			$('#increase_' + i).removeAttr("value").attr("value", "+");
			$('#decrease_' + i).removeAttr("value").attr("value", "-");
			$('#increase_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
			$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
		}
		set_all_onclick();
	}

	function fn_deleteRow(rowNo) {
		var numRow = $('#dtls_container tbody tr').length;
		if (numRow == rowNo && rowNo != 1) {
			var updateIdDtls = $('#updateIdDtls_' + rowNo).val();
			var txt_deleted_id = $('#txt_deleted_id').val();
			var selected_id = '';

			if (updateIdDtls != '') {
				if (txt_deleted_id == '') selected_id = updateIdDtls;
				else selected_id = txt_deleted_id + ',' + updateIdDtls;
				$('#txt_deleted_id').val(selected_id);
			}
			$('#dtls_container tbody tr:last').remove();
		} else {
			return false;
		}

		fnc_calculate();
	}


	// ===================================== end new ============================



	function fnc_calculate(thisVal, i) {
		var cbo_service_type = $("#cbo_service_type").val();
		if (cbo_service_type == 15 || cbo_service_type == 50 || cbo_service_type == 51) {
			//var dyeing_charge = $('#txt_rate_'+i).val()*1;
			var dyeing_charge = $('#txtRate_1').val();
			$('#dtls_container tr').each(function(j) {
				var wo_qty = $('#txtWoQty_' + j).val() * 1;
				/*
				var place_val=$('#txtWoQty_'+j).attr("placeholder")*1;
				
				if(place_val<wo_qty)
				{
					$('#txtWoQty_'+j).val("");
					$('#txtAmount_'+j).val("");
					$('#txtRate_'+j).val("");
					return;
				}*/
				$(".dc_rate").val(dyeing_charge);
				var amount = (wo_qty * 1) * (dyeing_charge * 1);
				$('#txtAmount_' + j).val(number_format_common(amount, 2));
			});
		} else {
			var wo_qty = $('#txtWoQty_1').val();
			/*
			var place_val=$('#txtWoQty_1').attr("placeholder")*1;
			if(place_val<wo_qty)
			{
				$('#txtWoQty_1').val("");
				return;
			}
			*/
			var dyeing_charge = $('#txtRate_1').val();
			var amount = (wo_qty * 1) * (dyeing_charge * 1);
			$('#txtAmount_1').val(number_format_common(amount, 2));
		}

	}

	function openmypage_job(thisVal, title) {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		} else {
			var is_sales_order = $("#cbo_is_sales_order").val();
			var company = $("#cbo_company_name").val();
			var width = "";
			if (is_sales_order == 1) {
				width = "720px";
			} else {
				width = "620px";
			}
			page_link = 'requires/yarn_service_work_order_without_lot_controller.php?action=job_search_popup&company=' + company + '&is_sales_order=' + is_sales_order;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=' + width + ',height=370px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var data = this.contentDoc.getElementById("hidden_job_no").value;
				var dataArr = data.split(',');
				freeze_window(5);

				$(thisVal).val(dataArr[1]);
				$(thisVal).siblings().val(dataArr[0]);
				$(thisVal).parent().parent().siblings().find("td:first input[type='text']").val(dataArr[1]);
				$(thisVal).parent().parent().siblings().find("td:first input[type='hidden']").val(dataArr[0]);
				release_freezing();

			}
		}
	}


	function create_row(booking_id) {
		var cbo_with_order = $("#cbo_with_order").val();

		var response = return_global_ajax_value(booking_id, 'child_form_input_row', '', 'requires/yarn_service_work_order_without_lot_controller');

		$("#dtls_container").find("tr:gt(1)").remove();
		$("#dtls_container tbody ").html(response);

		set_button_status(1, permission, 'fnc_yarn_service_wo', 1, 0);
		return;
	}


	function check_exchange_rate() {
		var cbo_currercy = $('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response = return_global_ajax_value(cbo_currercy + "**" + booking_date, 'check_conversion_rate', '', 'requires/yarn_service_work_order_without_lot_controller');
		var response = response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function openmypage_booking() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_name").val();
		page_link = 'requires/yarn_service_work_order_without_lot_controller.php?action=yern_service_wo_popup&company=' + company;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Dyeing Booking Search', 'width=885px, height=450px, center=1, resize=0, scrolling=0', '../');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var sys_number = this.contentDoc.getElementById("hidden_sys_number").value.split("_");

			if (sys_number != "") {
				freeze_window(5);

				get_php_form_data(sys_number[0] + "_" + sys_number[1], "populate_master_from_data", "requires/yarn_service_work_order_without_lot_controller");
				disable_enable_fields('cbo_company_name*cbo_service_type*cbo_pay_mode*cbo_currency', 1, "", "");
				show_list_view(sys_number[0] + "_" + sys_number[2], 'show_dtls_list_view', 'list_container', 'requires/yarn_service_work_order_without_lot_controller', '');
				set_button_status(0, permission, 'fnc_yarn_service_wo', 1, 1);
				release_freezing();
			}
		}
	}

	function open_terms_condition_popup(page_link, title) {
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		if (txt_booking_no == "") {
			alert("Save The Booking First")
			return;
		} else {
			page_link = page_link + get_submitted_data_string('txt_booking_no', '../../');
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0', '../')
			emailwindow.onclose = function() {}
		}
	}

	function generate_trim_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking_without_order";
			var data = "action=show_trim_booking_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type*cbo_supplier_name*cbo_pay_mode', "../../");
			http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}
	}

	function generate_trim_report_reponse() {
		if (http.readyState == 4) {
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function generate_without_rate_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking_without_order";
			var data = "action=show_without_rate_booking_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type*cbo_supplier_name*cbo_pay_mode', "../../");
			http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_without_rate_report_reponse;
		}
	}

	function generate_without_rate_report_reponse() {
		if (http.readyState == 4) {

			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function sales_order_report() {
		var update_id = $('#update_id').val();
		var is_sales = $('#cbo_is_sales_order').val();
		if (update_id == "" || is_sales == 2) {
			alert("This Report Is For Sales Order Only");
			return;
		}
		var show_rate_column = "";
		var r = confirm("Press \"OK\" to open with Rate column\nPress \"Cancel\" to open without Rate column");
		if (r == true) {
			show_rate_column = "1";
		} else {
			show_rate_column = "0";
		}

		var form_name = "yarn_dyeing_wo_booking_without_order";
		var data = "action=sales_order_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type', "../../") + "&show_val_column=" + show_rate_column;
		http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = sales_order_report_reponse;
	}

	function sales_order_report_reponse() {
		if (http.readyState == 4) {
			freeze_window(5);

			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
			release_freezing();
		}
	}

	function openmypage_charge() {
		if (form_validation('cbo_company_name', 'Company Name*Job Number') == false) {
			return;
		} else {
			var company = $("#cbo_company_name").val();

			page_link = 'requires/yarn_service_work_order_without_lot_controller.php?action=dyeing_search_popup&company=' + company;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Charge', 'width=600px,height=370px,center=1,resize=1,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];;
				var data = this.contentDoc.getElementById("hidden_rate").value;

				freeze_window(5);
				document.getElementById('txt_rate').value = data;
				release_freezing();
				fnc_calculate();
			}
		}
	}

	function generate_multiple_job_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking_without_order";
			var data = "action=show_with_multiple_job" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id', "../../");

			http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_multiple_job_report_reponse;
		}
	}

	function generate_multiple_job_report_reponse() {
		if (http.readyState == 4) {
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function multiple_job_without_rate_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking_without_order";
			var data = "action=show_with_multiple_job_without_rate" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id', "../../");
			http.open("POST", "requires/yarn_service_work_order_without_lot_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = multiple_job_without_qty_report_reponse;
		}
	}

	function multiple_job_without_qty_report_reponse() {
		if (http.readyState == 4) {
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function fnResetForm() {
		reset_form('yarn_service_work_order', 'list_container', '', 'txt_booking_date,<? echo date("d-m-Y"); ?>', 'disable_enable_fields("txtJobNo_1",0)', 'cbo_with_order*txtParcent_1*cboUom_1');

		$('#dtls_container tbody tr:not(:first)').remove();
		set_button_status(0, permission, 'fnc_yarn_service_wo', 1, 0);
	}


	function change_job_title(val) {
		if (val == 1) {
			$("#job_title").text("Sales Order No");
			$("#cbo_with_order").attr("disabled", "disabled");
		} else {
			$("#job_title").text("Job No");
		}
	}

	function change_job_priority(val) {
		if (val == 2) {
			$("#job_title").css("color", "blue").addClass("must_entry_caption").attr("title", "Must Entry Field");
			$(".job_field").attr("disabled", "disabled").removeAttr("placeholder");
		} else {
			$("#job_title").css("color", "#444").removeClass("must_entry_caption").removeAttr("title").attr("disabled");
			$(".job_field").removeAttr("disabled").attr("placeholder", "Doubole Click for Job");
		}
	}

	function set_fin_visibility(val) {
		if (val == 15 || val == 50 || val == 51) {
			$("#is_twisting").css("display", "block");
			$("#increase_1").removeAttr("disabled", "disabled");
		} else {
			$("#increase_1").attr("disabled", "disabled");
			$("#is_twisting").css("display", "none");

			$reset_fin_prod_dtls = "*cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color";

			reset_form('', '', 'txtJobNo_1*txtJobId_1*txtJobId_1*cboCount_1*cboComposition_1*txtParcent_1*cboYarnType_1*yernColor_1*cboColorRange_1*cboUom_1*txtWoQty_1*txtRate_1*txtAmount_1*txtBag_1*txtCone_1*txtMinReqCone_1*txtRemarks_1*dtlsUpdateId_1' + $reset_fin_prod_dtls, '', '', 'update_id*txt_booking_no*cbo_is_sales_order*');

			$('#dtls_container tbody tr:not(:first)').remove();
		}
	}
</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission);  ?>
		<form name="yarn_service_work_order" autocomplete="off" id="yarn_service_work_order">
			<fieldset style="width:800px; margin-bottom:5px;">
				<legend>Yarn Service Work Order</legend>
				<table cellspacing="4" cellpadding="8" border="0">
					<tr>
						<td colspan="6" align="center" height="30" valign="top"> Wo No
							<input class="text_boxes" type="text" style="width:190px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Work Order" name="txt_booking_no" id="txt_booking_no" />
						</td>
					</tr>

					<tr>
						<td align="right" class="must_entry_caption" width="80">Company</td>
						<td>
							<?
							echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_service_work_order_without_lot_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );get_php_form_data('94'+'_'+this.value, 'populate_field_level_access_data', 'requires/yarn_service_work_order_without_lot_controller' );", 0);
							?>
						</td>
						<td align="right" class="must_entry_caption" width="80">Service Type</td>
						<td>
							<?
							echo create_drop_down("cbo_service_type", 160, $yarn_issue_purpose, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_service_work_order_without_lot_controller',$('#cbo_company_name').val()+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' );set_fin_visibility(this.value);", 0, '12,15,38,46,7,50,51');
							?>
						</td>
						<td align="right" class="must_entry_caption">Pay Mode</td>
						<td><?
							echo create_drop_down("cbo_pay_mode", 160, $pay_mode, "", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/yarn_service_work_order_without_lot_controller',this.value, 'load_drop_down_supplier', 'supplier_td' )", "");
							?></td>


					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Booking Date</td>
						<td><input class="datepicker" type="text" style="width:150px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y") ?>" /></td>
						<td align="right">Attention</td>
						<td><input class="text_boxes" type="text" style="width:150px;" name="txt_attention" id="txt_attention" /></td>
						<td align="right">Currency</td>
						<td><?
							echo create_drop_down("cbo_currency", 160, $currency, "", 1, "-- Select --", 2, "check_exchange_rate();", 0);
							?></td>

					</tr>
					<tr>
						<td align="right">ExchangeRate</td>
						<td><input style="width:150px;" type="text" class="text_boxes_numeric" name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
						<td align="right" class="must_entry_caption" width="90">Factory</td>
						<td id="supplier_td">
							<?
							echo create_drop_down("cbo_supplier_name", 160, $blank_array, "", 1, "-- Select Supplier --", $selected, "", 0);
							?>
						</td>


						<td align="right">Source</td>
						<td><?
							echo create_drop_down("cbo_source", 160, $source, "", 1, "-- Select --", 3, "", 0);
							?></td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Delivery Date</td>
						<td align="left"><input class="datepicker" type="text" style="width:150px;" name="txt_delivery_date" id="txt_delivery_date" /></td>
						<td align="right">Sales Order</td>
						<td align="left">
							<? echo create_drop_down("cbo_is_sales_order", 160, $yes_no, "", 0, "-- Select --", 2, "change_job_title(this.value);", 0); ?>
						</td>
						<td align="center" colspan="2">
							<?
							include("../../terms_condition/terms_condition.php");
							terms_condition(94, 'txt_booking_no', '../../');
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Tenor</td>
						<td><input style="width:150px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
						<td align="right">With Order</td>
						<td align="left">
							<? echo create_drop_down("cbo_with_order", 160, $yes_no, "", 0, "-- Select --", 2, "change_job_priority(this.value);", 0); ?>
						</td>
						<td align="right">Ref No</td>
						<td align="left">
							<input class="text_boxes" type="text" style="width:150px;" name="txt_ref_no" id="txt_ref_no" />
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset style="width:1250px;">

				<legend>Yarn Service Work Order Without Lot Details </legend>
				<table cellpadding="0" cellspacing="0" width="1250" class="rpt_table" border="1" rules="all" id="dtls_container">
					<thead>
						<th width="80" class="must_entry_caption">Job No</th>
						<th width="70" class="must_entry_caption">Count</th>
						<th width="100" class="must_entry_caption">Composition</th>
						<th width="30" class="must_entry_caption">%</th>
						<th width="80" class="must_entry_caption">Yarn Type</th>

						<th width="60">UOM</th>
						<th width="55" class="must_entry_caption">WO Qnty</th>
						<th width="55" class="must_entry_caption">Rate</th>
						<th width="65">Amount</th>
						<th width="40">No of Bag</th>
						<th width="40">No of Cone</th>
						<th width="40">Min Req. Cone</th>
						<th width="100">Remarks</th>
						<th></th>
					</thead>
					<tbody id="batch_details_container">
						<tr class="general" id="tr_1">
							<td>
								<input type="text" id="txtJobNo_1" name="txtJobNo_1" disabled="disabled" placeholder="Doubole Click for Job" readonly style="width:100px;" class="text_boxes job_field" onDblClick="openmypage_job(this,'Job Search')" />
								<input type="hidden" id="txtJobId_1" name="txtJobId_1" />

								<input type="hidden" name="dtlsUpdateId_1" id="dtlsUpdateId_1" class="text_boxes" readonly />
							</td>
							<td>
								<?
								echo create_drop_down("cboCount_1", 70,  "Select id, yarn_count from  lib_yarn_count where  status_active=1", "id,yarn_count", 1, "-- Select --", 0, "");
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cboComposition_1", 100, $composition, "", 1, "-- Select --", 0, "");
								?>
							</td>
							<td>
								<input type="text" name="txtParcent_1" id="txtParcent_1" class="text_boxes" value="100" style="width:30px" />
							</td>
							<td>
								<?
								echo create_drop_down("cboYarnType_1", 80, $yarn_type, "", 1, "-- Select --", 0, "");
								?>
							</td>

							<td>
								<?
								echo create_drop_down("cboUom_1", 50, $unit_of_measurement, "", 1, "-- UOM--", 12, "", 1);
								?>
							</td>
							<td>
								<input type="text" id="txtWoQty_1" name="txtWoQty_1" style="width:55px;" class="text_boxes_numeric" onKeyUp="fnc_calculate(this,1)" placeholder="" />
							</td>
							<td>
								<input type="text" id="txtRate_1" name="txtRate_1" style="width:55px;" class="text_boxes_numeric dc_rate" onKeyUp="fnc_calculate(this,1)" />
							</td>
							<td>
								<input type="text" id="txtAmount_1" name="txtAmount_1" style="width:65px;" class="text_boxes_numeric" readonly />
							</td>
							<td>
								<input type="text" id="txtBag_1" name="txtBag_1" style="width:40px;" class="text_boxes_numeric" />
							</td>
							<td>
								<input type="text" id="txtCone_1" name="txtCone_1" style="width:40px;" class="text_boxes_numeric" />
							</td>
							<td>
								<input type="text" id="txtMinReqCone_1" name="txtMinReqCone_1" style="width:40px;" class="text_boxes_numeric" />
							</td>
							<td>
								<input type="text" id="txtRemarks_1" name="txtRemarks_1" style="width:100px;;" class="text_boxes" />
								<input type="hidden" name="txtDeletedId_1" id="txtDeletedId_1" class="text_boxes_numeric" readonly />
							</td>
							<td width="65">
								<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
								<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
							</td>
						</tr>
					</tbody>

				</table>

				<fieldset style="width:50%; margin:5px auto;display:none;" id="is_twisting">
					<legend>Finish Product Details</legend>
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" style="width:100%;">
						<tr>
							<th class="must_entry_caption">Count</th>
							<th class="must_entry_caption">Composition</th>
							<th class="must_entry_caption">%</th>
							<th class="must_entry_caption">Type</th>
							<th class="must_entry_caption">Color</th>
						</tr>

						<tr>
							<td>
								<? echo create_drop_down("cbo_fin_count", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1", "id,yarn_count", 1, "-select-", $selected, "", "0"); ?>
							</td>
							<td>
								<? echo create_drop_down("cbo_fin_composition", 150, "select id, composition_name from  lib_composition_array where  status_active=1", "id,composition_name", 1, "-select-", $selected, "", "0"); ?>
							</td>
							<td>
								<input type="text" name="txt_fin_perc" id="txt_fin_perc" class="text_boxes_numeric" style="width:45px" value="100" />
							</td>
							<td>
								<? echo create_drop_down("cbo_fin_type", 100, $yarn_type, 1, "-select-", $selected, "", "0"); ?>
							</td>
							<td>
								<input type="text" id="txt_fin_color" name="txt_fin_color" placeholder="Write" style="width:100px;" class="text_boxes" />
								<input type="hidden" id="hdn_fin_update_id" name="hdn_fin_update_id" />
							</td>
						</tr>

					</table>
				</fieldset>

				<table width="100%">
					<tr>
						<td align="center" class="button_container">
							<? echo load_submit_buttons($permission, "fnc_yarn_service_wo", 0, 0, "fnResetForm()", 1); ?>
							<input type="hidden" id="update_id">
							<input type="hidden" id="update_dtls_ids">
							<div id="pdf_file_name"></div>
							<input type="button" value="Print With Rate" onClick="generate_trim_report()" style="width:160px" name="print_booking" id="print_booking" class="formbutton" />
							<input type="button" value="Print Without Rate" onClick="generate_without_rate_report()" style="width:160px" name="print_booking2" id="print_booking2" class="formbutton" />
							<input type="button" value="Multiple Sample With Rate" onClick="generate_multiple_job_report()" style="width:160px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
							<input type="button" value="Multiple Sample Without Rate" onClick="multiple_job_without_rate_report()" style="width:170px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
							<input type="button" value="Print Report Sales" onClick="sales_order_report()" style="width:170px;" name="print_booking5" id="print_booking5" class="formbutton" />

						</td>
					</tr>
				</table>

			</fieldset>

		</form>
		<br>
		<div id="list_container"></div>
	</div>
	<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>