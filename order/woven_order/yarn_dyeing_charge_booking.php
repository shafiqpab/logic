<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Dyeing Charge Booking
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-03-2013
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
//-----------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1, $unicode, '', '');
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	<?
	$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][41]);
	echo "var field_level_data= " . $data_arr . ";\n";
	echo "var mandatory_field = '" . implode('*', $_SESSION['logic_erp']['mandatory_field'][41]) . "';\n";
	echo "var mandatory_message = '" . implode('*', $_SESSION['logic_erp']['mandatory_message'][41]) . "';\n";

	?>
	//set_field_level_access( document.getElementById('cbo_company_id').value);
	function openmypage_job(title) {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		} else {
			var company = $("#cbo_company_name").val();
			var txt_job_no = $("#txt_job_no").val();
			var txt_booking_date = $("#txt_booking_date").val();
			var budget_version = $("#cbo_budget_version").val();

			if (title == 'job Search') {
				page_link = 'requires/yarn_dyeing_charge_booking_controller.php?action=order_search_popup&company=' + company + '&txt_booking_date=' + txt_booking_date + '&budget_version=' + budget_version;
			} else {
				if (form_validation('txt_job_no', 'Job No') == false) {
					return;
				}
				page_link = 'requires/yarn_dyeing_charge_booking_controller.php?action=booking_search_popup&company=' + company + '&txt_booking_date=' + txt_booking_date + '&budget_version=' + budget_version + '&txt_job_no=' + txt_job_no;
			}

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=390px,center=1,resize=1,scrolling=0', '../')

			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];;
				var data = this.contentDoc.getElementById("hidden_tbl_id").value.split("_");

				freeze_window(5);

				if (title == 'job Search') {
					document.getElementById('txt_job_id').value = data[0];
					document.getElementById('txt_job_no').value = data[1];
					document.getElementById('txt_file_no').value = data[2];
					document.getElementById('txt_int_ref_no').value = data[3];
					$("#txt_fab_booking_no").val('');
					load_drop_down('requires/yarn_dyeing_charge_booking_controller', data[1], 'load_drop_down_color', 'color_td');
				} else {
					document.getElementById('txt_fab_booking_no').value = data[0];
					document.getElementById('txt_po_id').value = data[2];
					document.getElementById('cbo_is_short').value = data[2];
					document.getElementById('txt_lot').value = '';
				}

				release_freezing();
			}
		}
	}

	function fnc_yarn_dyeing(operation) {
		if (form_validation('cbo_company_name*cbo_supplier_name*txt_booking_date*txt_job_no*txt_fab_booking_no*txt_lot*cbo_source*txt_yern_color*txt_wo_qty*cbo_pay_mode', 'Company Name *Supplier Name*Booking Date*Job Number*Booking No*Yarn Lot*Source Name*Color*Order Quanty*Pay Mode') == false) {
			return;
		}

		if (mandatory_field) {
			if (form_validation(mandatory_field, mandatory_message) == false) {
				return;
			}
		}

		var exchange_rate = $('#txt_exchange_rate').val() * 1;
		if (exchange_rate <= 0) {
			alert("Exchange Rate Must be Greater then 0");
			return;
		}

		var cumalitive_precost_yarn_deing_amount = $('#txt_amount').attr('placeholder');

		var dataString = "cbo_company_name*cbo_supplier_name*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_attention*txt_delivery_date*txt_delivery_end*dy_delevery_start*dy_delevery_end*cbo_item_category_id*txt_job_no*cbo_count*txt_item_des*txt_yern_color*cbo_color_range*cbo_uom*txt_wo_qty*txt_dyeing_charge*txt_amount*txt_bag*txt_cone*update_id*txt_booking_no*txt_job_id*dtls_update_id*txt_pro_id*txt_min_req_cone*cbo_source*txt_ref_no*txt_file_no*txt_int_ref_no*txt_remarks*cbo_is_short*cbo_budget_version*cbo_ready_to_approved*txt_fab_booking_no*hdn_wo_qty*hdn_pre_prod_id*txt_tenor";
		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../../") + "&cumalitive_precost_yarn_deing_amount=" + cumalitive_precost_yarn_deing_amount; //alert(data);
		freeze_window(operation);
		http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_dyeing_response;
	}

	function fnc_yarn_dyeing_response() {
		if (http.readyState == 4) {
			//alert(http.responseText);
			var response = trim(http.responseText).split('**');
			//alert (response);
			if (trim(response[0]) == 23) {
				show_msg(trim(response[0]));
				alert(trim(response[1]));
				release_freezing();
				return;
			}
			if (response[0] == 40) {
				alert(response[1]);
				release_freezing();
				return;
			}

			if (response[0] == 0 || response[0] == 1 || response[0] == 2) {
				show_msg(trim(response[0]));
				$('#cbo_company_name').attr('disabled', true);
				$('#cbo_budget_version').attr('disabled', true);
				release_freezing();
			} else if (response[0] == 10 || response[0] == 11) {
				show_msg(trim(response[0]));
				release_freezing();
				return;
			}

			if (trim(response[0]) == 'approve') {
				alert("This booking is approved")
				release_freezing();
				return;
			}

			if (trim(response[0]) == 13) {
				alert(response[1] + '=' + response[2])
				release_freezing();
				return;
			}

			$("#update_id").val(response[2]);
			$("#txt_booking_no").val(response[1]);
			show_list_view(response[2], 'show_dtls_list_view', 'list_container', 'requires/yarn_dyeing_charge_booking_controller', '');
			set_button_status(0, permission, 'fnc_yarn_dyeing', 1, 1);
			reset_form('', '', 'txt_yern_color*cbo_color_range*txt_wo_qty*txt_dyeing_charge*txt_amount*txt_bag*txt_cone*txt_min_req_cone*txt_ref_no*txt_file_no*txt_int_ref_no*txt_remarks*txt_budget_wo_qty*dtls_update_id', '', '', '');

			if (trim(response[0]) == 0) {
				$('#txt_job_no').attr('disabled', 'disabled');
			}

			if (trim(response[0]) == 1) {
				$('#txt_lot').removeAttr('disabled', 'disabled');
				$('#cbo_count').removeAttr('disabled', 'disabled');
				$('#txt_item_des').removeAttr('disabled', 'disabled');
				$('#txt_yern_color').removeAttr('disabled', 'disabled');
			}
		}
	}

	function fnc_calculate() {
		var is_short = $('#cbo_is_short').val();
		var wo_qty = $('#txt_wo_qty').val() * 1;
		var dyeing_charge = $('#txt_dyeing_charge').val();
		var budget_wo_qty = $('#txt_budget_wo_qty').val() * 1;
		var txt_booking_req_qty = $('#txt_booking_req_qty').val() * 1;
		var booking_bal = $('#txt_booking_req_qty').attr('booking_bal') * 1;


		var amount = (wo_qty * 1) * (dyeing_charge * 1);
		$('#txt_amount').val(number_format_common(amount, 2));

		var wo_amount = $('#txt_amount').val() * 1;
		var budget_wo_amount = $('#txt_budget_wo_amount').val() * 1;
		var txt_booking_req_amount = $('#txt_booking_req_amount').val() * 1;
		var booking_amount = $('#txt_booking_req_amount').attr('booking_amount') * 1;
		//alert(txt_booking_req_amount);
		var precost_yarn_dyeing_cumalitive_amount = $('#hdn_precost_yarn_dyeing_cumalitive_amount').val() * 1;

		var vs_exceed_budge_qty_percentage = $('#hdn_exceed_budge_qty').val() * 1; // from vs
		var vs_exceed_budge_amount_percentage = $('#hdn_exceed_budge_amount').val() * 1; // from vs
		var vs_amount_exceed_level = $('#hdn_amount_exceed_level').val() * 1; // from vs
		var vs_exceed_yes_no = $('#hdn_exceed_yes_no').val() * 1; // from vs

		if (is_short == 2) // fabric
		{
			if (budget_wo_qty > 0) {
				if ((wo_qty * 1) > (budget_wo_qty * 1)) // qty level
				{
					alert("Work order quantity does not allow more then fabric required.");
					$('#txt_wo_qty').val("");
					$('#txt_wo_qty').focus();
					return;
				}
			}
		}

		if (is_short == 1) // fabric
		{
			if (txt_booking_req_qty > 0) // qty level
			{
				if ((wo_qty * 1) > (booking_bal * 1)) {
					alert("Work Order Quantity Does Not Allow More Then Fabric Booking Required.");
					$('#txt_wo_qty').val("");
					$('#txt_wo_qty').focus();
					return;
				}
			}
		}

		// amount level
		if (is_short == 2) //fabric 
		{
			if (budget_wo_amount > 0) {
				if ((wo_amount * 1) > (budget_wo_amount * 1)) {
					alert("Work order amount does not allow more then fabric required amount.");
					$('#txt_amount').val("");
					$('#txt_amount').focus();
					return;
				}
			}
		}

		// amount level
		if (is_short == 1) // fabric 
		{
			if (txt_booking_req_amount > 0) {
				if ((wo_amount * 1) > (booking_amount * 1)) {
					alert("Work order quantity does not allow more then fabric booking required amount.");
					$('#txt_amount').val("");
					$('#txt_amount').focus();
					return;
				}
			}
		}

		/* // amount level precost yarn dyeing
		if (is_short == 2) // yarn dyeing
		{
			if (vs_amount_exceed_level == 1 && vs_exceed_yes_no == 1) //Level: total amount, Exceed control yes
			{
				if (wo_amount > precost_yarn_dyeing_cumalitive_amount) {
					alert("Work order amount does not allow more then yarn dyeing budget amount.\nIncluding exceed percentage(" + vs_exceed_budge_amount_percentage + "%) allow amount is= " + precost_yarn_dyeing_cumalitive_amount);
					$('#txt_amount').val("");
					$('#txt_amount').focus();
					return;
				}
			}
		} */

	}

	function chack_variable(id) {
		//alert(id);
		var data = "action=variable_chack&company=" + id;
		http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_variable_response;
	}

	function fnc_variable_response() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split('**');
			if (response == 1) {
				$('#dyeing_charge_td').html('<input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric" placeholder="Browse" onDblClick="openmypage_charge()" readonly />');
			} else {
				$('#dyeing_charge_td').html('<input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric"  onKeyUp="fnc_calculate()"/>');
			}
		}
	}

	function openmypage_booking() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var company = $("#cbo_company_name").val();
		var pay_mode = $("#cbo_pay_mode").val();
		page_link = 'requires/yarn_dyeing_charge_booking_controller.php?action=yern_dyeing_booking_popup&company=' + company + '&pay_mode=' + pay_mode;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Dyeing Booking Search', 'width=950px, height=450px, center=1, resize=0, scrolling=0', '../');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var sys_number = this.contentDoc.getElementById("hidden_sys_number").value.split("_");

			if (sys_number != "") {
				//alert(b_date);
				freeze_window(5);
				get_php_form_data(sys_number[0], "populate_master_from_data", "requires/yarn_dyeing_charge_booking_controller");
				show_list_view(sys_number[0], 'show_dtls_list_view', 'list_container', 'requires/yarn_dyeing_charge_booking_controller', '');
				$('#cbo_company_name').attr('disabled', true);
				set_button_status(0, permission, 'fnc_yarn_dyeing', 1, 1);
				release_freezing();

			}
		}
	}

	function openmypage_charge() {
		if (form_validation('cbo_company_name*txt_job_no', 'Company Name*Job Number') == false) {
			return;
		} else {
			var company = $("#cbo_company_name").val();
			//alert(company);
			page_link = 'requires/yarn_dyeing_charge_booking_controller.php?action=dyeing_search_popup&company=' + company;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Charge', 'width=600px,height=370px,center=1,resize=1,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];;
				var data = this.contentDoc.getElementById("hidden_rate").value;
				//alert(data);
				freeze_window(5);
				document.getElementById('txt_dyeing_charge').value = data;
				release_freezing();
				fnc_calculate();

			}
		}
	}

	function openmypage_lot() {
		if (form_validation('cbo_company_name*txt_job_no*txt_fab_booking_no', 'Company Number*Job No*Fabric Booking No') == false) {
			return;
		} else {

			var company = $("#cbo_company_name").val();
			var job_no = $("#txt_job_no").val();
			var fab_booking_no = $("#txt_fab_booking_no").val();
			//alert(job_no);
			page_link = 'requires/yarn_dyeing_charge_booking_controller.php?action=lot_search_popup&company=' + company + '&job_no=' + job_no + '&fab_booking_no=' + fab_booking_no;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Lot Number Search', 'width=975px,height=380px,center=1,resize=1,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];;
				var data = this.contentDoc.getElementById("hidden_product").value.split("**");
				//alert(data[0]);return;
				//alert(data[4]);
				freeze_window(5);
				$('#txt_item_des').val(data[0]).attr('disabled', true);
				$('#cbo_count').val(data[1]).attr('disabled', true);
				$('#txt_lot').val(data[2]);
				$('#txt_pro_id').val(data[3]);
				$('#txt_po_id').val(data[4]);
				if (data[5] == 1 && data[6] == 2) {
					if (data[2] != "" && fab_booking_no != "") {
						//alert(data[4]);
						load_drop_down('requires/yarn_dyeing_charge_booking_controller', data[4], 'load_drop_down_po_color', 'color_td');
					} else {
						load_drop_down('requires/yarn_dyeing_charge_booking_controller', job_no, 'load_drop_down_color', 'color_td');
					}
				} else {
					load_drop_down('requires/yarn_dyeing_charge_booking_controller', job_no, 'load_drop_down_color', 'color_td');
				}

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
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r == true) show_comment = "1";
			else show_comment = "0";
			var path = 1;
			var budget_version_id = $('#cbo_budget_version').val();
			var form_name = "yarn_dyeing_wo_booking";
			var data = "action=show_trim_booking_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../") + '&show_comment=' + show_comment + '&path=' + path;
			//var data="action=show_trim_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}
	}

	function generate_trim_report_reponse() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
			var file_data = http.responseText.split('****');
			// $('#pdf_file_name').html(file_data[1]);
			// $('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>' + http.responseText + '</body</html>');
			d.close();
		}
	}

	function generate_print_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var show_comment = '';
			var r = confirm("Press  \"OK\"  to hide  Style No and Buyer Name\nPress  \"CANCEL\"  to Show Style No and Buyer Name");
			if (r == true) show_comment = "0";
			else show_comment = "1";
			var budget_version_id = $('#cbo_budget_version').val();
			var form_name = "yarn_dyeing_wo_booking";
			var path = 1;
			var data = "action=show_print_booking_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../") + '&show_comment=' + show_comment + '&path=' + path;
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}
	}

	function generate_print_report_reponse() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function generate_print_report_two() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide  Comment, Amount and Rate\nPress  \"OK\"  to Show Comment, Amount and Rate");
			if (r == true) show_comment = "1";
			else show_comment = "0";
			var path = 1;
			var budget_version_id = $('#cbo_budget_version').val();
			var form_name = "yarn_dyeing_wo_booking";
			var data = "action=show_trim_booking_report_two" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../") + '&show_comment=' + show_comment + '&path=' + path;
			//var data="action=show_trim_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}
	}

	function generate_print_report_reponse_two() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function generate_without_rate_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking";
			var data = "action=show_without_rate_booking_report" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../");
			//var data="action=show_without_rate_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
			var budget_version_id = $('#cbo_budget_version').val();
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}

			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_without_rate_report_reponse;
		}
	}

	function generate_without_rate_report_reponse() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
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


	function generate_multiple_job_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r == true) show_comment = "1";
			else show_comment = "0";

			var form_name = "yarn_dyeing_wo_booking";
			var data = "action=show_with_multiple_job" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../") + '&show_comment=' + show_comment;
			//var data="action=show_with_multiple_job"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
			var budget_version_id = $('#cbo_budget_version').val();
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_multiple_job_report_reponse;
		}
	}

	function generate_multiple_job_report_reponse() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
			var file_data = http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
			d.close();
		}
	}

	function multiple_job_without_rate_report() {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		} else {
			var form_name = "yarn_dyeing_wo_booking";
			var data = "action=show_with_multiple_job_without_rate" + '&form_name=' + form_name + get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_supplier_name*cbo_pay_mode', "../../");
			var budget_version_id = $('#cbo_budget_version').val();
			if (budget_version_id == 1) {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller.php", true);
			} else {
				http.open("POST", "requires/yarn_dyeing_charge_booking_controller2.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = multiple_job_without_qty_report_reponse;
		}
	}

	function multiple_job_without_qty_report_reponse() {
		if (http.readyState == 4) {
			//alert( http.responseText);return;
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
		reset_form('yarn_dyeing_wo_booking', 'list_container', '', 'txt_booking_date,<? echo date("d-m-Y"); ?>', 'disable_enable_fields("txt_item_des*txt_lot*cbo_count",0)', 'cbo_uom');
		set_button_status(0, permission, 'fnc_yarn_dyeing', 1, 0);
	}

	function set_exchang(id) {
		if (id == 1) {
			$('#txt_exchange_rate').val(id).attr('disabled', true);
		} else {
			$('#txt_exchange_rate').val("").attr('disabled', false);
		}
	}

	function check_exchange_rate() {
		var cbo_currercy = $('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response = return_global_ajax_value(cbo_currercy + "**" + booking_date + "**" + cbo_company_name, 'check_conversion_rate', '', 'requires/yarn_dyeing_charge_booking_controller');
		var response = response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	//for print button
	function print_report_button_setting(report_ids) {

		$("#print_btn").hide();
		$("#print_btn1").hide();
		$("#print_booking").hide();
		$("#print_booking2").hide();
		$("#print_booking3").hide();
		$("#print_booking4").hide();

		var report_id = report_ids.split(",");
		for (var k = 0; k < report_id.length; k++) {
			if (report_id[k] == 13) $("#print_btn").show();
			if (report_id[k] == 15) $("#print_btn1").show();
			if (report_id[k] == 74) $("#print_booking").show();
			if (report_id[k] == 75) $("#print_booking2").show();
			if (report_id[k] == 76) $("#print_booking3").show();
			if (report_id[k] == 77) $("#print_booking4").show();
		}
	}

	function change_lot() {
		$("#txt_lot").val("");
	}
</script>
</head>

<body onLoad="set_hotkey();check_exchange_rate();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission);  ?>
		<form name="yarn_dyeing_wo_booking" autocomplete="off" id="yarn_dyeing_wo_booking">
			<fieldset style="width:1200px;">
				<legend>Yarn Dyeing Wo</legend>
				<table width="1200" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td colspan="4" align="right" class="must_entry_caption"> Yarn Dyeing Wo No </td> <!-- 11-00030  -->
						<td colspan="4">
							<input class="text_boxes" type="text" style="width:130px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" />
						</td>
					</tr>
					<tr>
						<td width="120" class="must_entry_caption">Company Name</td>
						<td width="150"><? echo create_drop_down("cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "chack_variable(this.value);get_php_form_data( this.value, 'button_setting_data', 'requires/yarn_dyeing_charge_booking_controller' );check_exchange_rate()", 0); ?></td>
						<td width="120" class="must_entry_caption">Pay Mode</td>
						<td width="150"><? echo create_drop_down("cbo_pay_mode", 140, $pay_mode, "", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/yarn_dyeing_charge_booking_controller',this.value, 'load_drop_down_supplier', 'supplier_td' )", ""); ?></td>
						<td width="120" class="must_entry_caption">Yarn Dyeing Factory</td>
						<td width="150" id="supplier_td"><? echo create_drop_down("cbo_supplier_name", 140, $blank_array, "", 1, "-- Select Supplier --", $selected, "", 0); ?></td>
						<td width="120" class="must_entry_caption">Booking Date</td>
						<td><input class="datepicker" type="text" style="width:130px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y") ?>" disabled /></td>
					</tr>
					<tr>
						<td>Currency</td>
						<td><? echo create_drop_down("cbo_currency", 140, $currency, "", 1, "-- Select --", 2, "check_exchange_rate();", 0); ?></td>
						<td>Exchange Rate</td>
						<td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
						<td class="must_entry_caption">Source</td>
						<td><? echo create_drop_down("cbo_source", 140, $source, "", 1, "-- Select --", 3, "", 0); ?></td>
						<td>Attention</td>
						<td><input class="text_boxes" type="text" style="width:130px;" name="txt_attention" id="txt_attention" /></td>
					</tr>
					<tr>
						<td>G/Y Issue Start</td>
						<td><input class="datepicker" type="text" style="width:130px" name="txt_delivery_date" id="txt_delivery_date" /></td>
						<td>G/Y Issue End</td>
						<td><input class="datepicker" type="text" style="width:130px" name="txt_delivery_end" id="txt_delivery_end" /></td>
						<td>D/Y Delivery Start</td>
						<td><input class="datepicker" type="text" style="width:130px" name="dy_delevery_start" id="dy_delevery_start" /></td>
						<td>D/Y Delivery End</td>
						<td><input class="datepicker" type="text" style="width:130px" name="dy_delevery_end" id="dy_delevery_end" /></td>
					</tr>
					<tr>
						<td>Item Category</td>
						<td><? echo create_drop_down("cbo_item_category_id", 140, $item_category, '', 0, '', 24, "", 0, 24); ?></td>
						<td>Is Short</td>
						<td><? echo create_drop_down("cbo_is_short", 140, $yes_no, '', 0, '', 2, ""); ?></td>
						<td>Budget Version</td>
						<td><?
							$pre_cost_class_arr = array(1 => 'Pre Cost 1', 2 => 'Pre Cost 2', 3 => 'Pre Cost 3');
							echo create_drop_down("cbo_budget_version", 140, $pre_cost_class_arr, "", 0, "-- Select Version --", 2);
							?>
						</td>
						<td>Ready To Approved</td>
						<td><? echo create_drop_down("cbo_ready_to_approved", 140, $yes_no, "", 1, "-- Select--", 2, "", "", ""); ?></td>
					</tr>
					<tr>
						<td>Tenor</td>
						<td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
						<td align="right" colspan="2">
							<input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'yarn_dyeing_wo_booking', 0 ,1)">
						</td>
						<td align="center" colspan="2">
							<?
							include("../../terms_condition/terms_condition.php");
							terms_condition(41, 'txt_booking_no', '../../');
							?>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="center" height="10" colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="8">
							<table width="1220" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="dtls_part">
								<thead>
									<tr>
										<th width="80" class="must_entry_caption">Job No</th>
										<th width="100" class="must_entry_caption">Booking No</th>
										<th width="70" class="must_entry_caption">Lot No</th>
										<th width="70">Count</th>
										<th width="150">Yarn Description</th>
										<th width="90" class="must_entry_caption">Yarn Color</th>
										<th width="90">Color Range</th>
										<th width="60">Ref. No</th>
										<th width="50">UOM</th>
										<th width="50">Booking Balance</th>
										<th width="50" class="must_entry_caption">Yarn Wo. Qnty</th>
										<th width="50">Dyeing Charge</th>
										<th width="60">Amount</th>
										<th width="40">No of Bag</th>
										<th width="40">No of Cone</th>
										<th width="40">Min Req. Cone</th>
										<th width="40">File No</th>
										<th width="40">Internal Ref. No</th>
										<th>Remarks/Shade</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<input type="text" id="txt_job_no" name="txt_job_no" placeholder="Double Click for Job" readonly style="width:80px;" class="text_boxes" onDblClick="openmypage_job('job Search')" />
											<input type="hidden" id="txt_job_id" name="txt_job_id">
										</td>

										<td>
											<input type="text" id="txt_fab_booking_no" name="txt_fab_booking_no" placeholder="Double Click for Booking" readonly style="width:100px;" class="text_boxes" onDblClick="openmypage_job('booking Search')" />
											<input type="hidden" id="txt_po_id" name="txt_po_id" />
										</td>

										<td>
											<input type="text" id="txt_lot" name="txt_lot" style="width:70px;" class="text_boxes" placeholder="Browse" onDblClick="openmypage_lot();" readonly />
											<input type="hidden" id="txt_pro_id" name="txt_pro_id" style="width:70px;" />
											<input type="hidden" id="txt_po_id" name="txt_po_id" style="width:70px;" />
											<input type="hidden" id="hdn_pre_prod_id" name="hdn_pre_prod_id" readonly />
										</td>
										<td><? echo create_drop_down("cbo_count", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1", "id,yarn_count", 1, "-select-", $selected, "", "0"); ?></td>
										<td><input type="text" id="txt_item_des" name="txt_item_des" style="width:140px;" class="text_boxes" /></td>
										<td id="color_td"><? echo create_drop_down("txt_yern_color", 90, $blank_array, "", 1, "-- Select--", $selected); ?></td>
										<td><? echo create_drop_down("cbo_color_range", 90, $color_range, "", 1, "-- Select--", $selected); ?></td>
										<td><input type="text" id="txt_ref_no" name="txt_ref_no" style="width:55px;" class="text_boxes" /></td>
										<td><? echo create_drop_down("cbo_uom", 50, $unit_of_measurement, "", 1, "-- UOM--", 12, "", 1); ?></td>
										<td>
											<input type="text" id="txt_budget_wo_qty" name="txt_budget_wo_qty" style="width:50px;" class="text_boxes_numeric" readonly />
											<input type="hidden" id="txt_booking_req_qty" name="txt_booking_req_qty" class="text_boxes_numeric" readonly />
										</td>
										<td>
											<input type="text" id="txt_wo_qty" name="txt_wo_qty" style="width:50px;" class="text_boxes_numeric" onKeyUp="fnc_calculate()" />
											<input type="hidden" id="hdn_wo_qty" name="hdn_wo_qty" readonly />
										</td>
										<td id="dyeing_charge_td"><input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric" />
										</td>

										<td>
											<input type="text" id="txt_amount" name="txt_amount" style="width:55px;" class="text_boxes_numeric" readonly />
											<input type="hidden" id="txt_budget_wo_amount" name="txt_budget_wo_amount" class="text_boxes_numeric" readonly />
											<input type="hidden" id="txt_booking_req_amount" name="txt_booking_req_amount" class="text_boxes_numeric" readonly />
											<input type="hidden" id="hdn_precost_yarn_dyeing_cumalitive_amount" class="text_boxes_numeric" readonly />
											<input type="hidden" id="hdn_exceed_budge_qty" class="text_boxes_numeric" readonly />
											<input type="hidden" id="hdn_exceed_budge_amount" class="text_boxes_numeric" readonly />
											<input type="hidden" id="hdn_amount_exceed_level" class="text_boxes_numeric" readonly />
											<input type="hidden" id="hdn_exceed_yes_no" class="text_boxes_numeric" readonly />
										</td>

										<td><input type="text" id="txt_bag" name="txt_bag" style="width:35px;" class="text_boxes_numeric" /></td>
										<td><input type="text" id="txt_cone" name="txt_cone" style="width:35px;" class="text_boxes_numeric" /></td>
										<td><input type="text" id="txt_min_req_cone" name="txt_min_req_cone" style="width:35px;" class="text_boxes_numeric" /></td>
										<td><input type="text" id="txt_file_no" name="txt_file_no" style="width:35px;" class="text_boxes" readonly /></td>
										<td><input type="text" id="txt_int_ref_no" name="txt_int_ref_no" style="width:35px;" class="text_boxes" readonly /></td>
										<td><input type="text" id="txt_remarks" name="txt_remarks" style="width:80px;;" class="text_boxes" /></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<br>
					<tr>
						<td align="center" colspan="8" valign="middle" class="button_container">
							<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<? $date = date('d-m-Y');
							echo load_submit_buttons($permission, "fnc_yarn_dyeing", 0, 0, "fnResetForm()", 1); ?>
							<input type="hidden" id="update_id">
							<input type="hidden" id="dtls_update_id">
							<input type="hidden" id="service_rate_from">
						</td>
					</tr>
					<tr>
						<td align="center" colspan="8">
							<div id="pdf_file_name"></div>
							<input type="button" value="Print" onClick="generate_print_report()" style="width:100px;display:none;" name="print_btn" id="print_btn" class="formbutton" />
							<input type="button" value="Print2" onClick="generate_print_report_two()" style="width:100px;display:none;" name="print_btn2" id="print_btn1" class="formbutton" />
							<input type="button" value="Print Order With Rate" onClick="generate_trim_report()" style="width:160px; display:none;" name="print_booking" id="print_booking" class="formbutton" />
							<input type="button" value="Print Order Without Rate" onClick="generate_without_rate_report()" style="width:160px; display:none;" name="print_booking2" id="print_booking2" class="formbutton" />
							<input type="button" value="Print With Multiple Job" onClick="generate_multiple_job_report()" style="width:160px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
							<input type="button" value="Multiple Job Without Rate" onClick="multiple_job_without_rate_report()" style="width:170px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
		<br>
		<fieldset style="width:1200px;">
			<div id="list_container"></div>
		</fieldset>
	</div>
	<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>