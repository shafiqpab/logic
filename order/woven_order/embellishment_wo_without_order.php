﻿<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :
JS Functions	 :
Created by		 : Aziz
Creation date 	 : 17-5-2020
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
Report Created BY:
QC Performed BY	 :
QC Date			 :
Comments		 :
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Emblishment Booking Multi Job", "../../", 1, 1, $unicode, 1, '');
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) {
		window.location.href = "../../logout.php";
	}

	var permission = '<? echo $permission; ?>';

	Date.prototype.yyyymmdd = function() {
		var yyyy = this.getFullYear().toString();
		var mm = (this.getMonth() + 1).toString();
		var dd = this.getDate().toString();
		return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
	};

	Date.prototype.ddmmyyyy = function() {
		var yyyy = this.getFullYear().toString();
		var mm = (this.getMonth() + 1).toString();
		var dd = this.getDate().toString();
		return (dd[1] ? dd : "0" + dd[0]) + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + yyyy;
	};

	function fnc_generate_booking(param, po_id, pre_cost_id, cbo_company_name) {
		freeze_window(operation);
		var garments_nature = document.getElementById('garments_nature').value;
		var txt_delivery_date = document.getElementById('txt_delivery_date').value
		var cbo_currency = document.getElementById('cbo_currency').value;

		var param = "'" + param + "'"
		var data = "'" + po_id + "'"
		var precost_id = "'" + pre_cost_id + "'"
		var data = "action=generate_fabric_booking&data=" + data + '&cbo_company_name=' + cbo_company_name + '&txt_delivery_date=' + txt_delivery_date + '&garments_nature=' + garments_nature + '&cbo_currency=' + cbo_currency + '&pre_cost_id=' + precost_id + '&param=' + param;
		http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	}

	function fnc_generate_booking_reponse() {
		if (http.readyState == 4) {
			document.getElementById('booking_list_view').innerHTML = http.responseText;
			set_all_onclick();
			release_freezing();
		}
	}

	function open_consumption_popup(page_link, title, req_id, i) {
		var garments_nature = document.getElementById('garments_nature').value;
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var txt_req_no = document.getElementById('txtreqno_' + i).value;
		var txt_req_id = document.getElementById(req_id).value;
		var txtembcostid = document.getElementById('txtembcostid_' + i).value;
		var emb_name = document.getElementById('emb_name_' + i).value;
		var txt_update_dtls_id = document.getElementById('txtbookingid_' + i).value;
		var cbo_colorsizesensitive = document.getElementById('cbocolorsizesensitive_' + i).value;
		var txt_req_quantity = document.getElementById('txtreqqnty_' + i).value;
		var txtwoq = document.getElementById('txtwoq_' + i).value;
		var txt_req_amount = document.getElementById('txtreqamount_' + i).value;
		var txt_avg_price = document.getElementById('txtrate_' + i).value;
		//var txt_country=document.getElementById('txtcountry_'+i).value;
		var body_part_id = document.getElementById('body_part_id_' + i).value;
		var emb_type = document.getElementById('emb_type_' + i).value;
		var txtcuwoq = document.getElementById('txtcuwoq_' + i).value;
		var txtgmtitemid = document.getElementById('txtgmtitemid_' + i).value;
		var txtcuamount = document.getElementById('txtcuamount_' + i).value * 1;

		var cons_breck_downn = document.getElementById('consbreckdown_' + i).value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		if (txt_req_id == 0) {
			alert("Select Req No")
		} else {
			var page_link = page_link + '&garments_nature=' + garments_nature + '&cbo_company_name=' + cbo_company_name + '&txt_req_no=' + txt_req_no + '&txt_req_id=' + txt_req_id + '&txtembcostid=' + txtembcostid + '&emb_name=' + emb_name + '&txt_update_dtls_id=' + txt_update_dtls_id + '&cbo_colorsizesensitive=' + cbo_colorsizesensitive + '&txt_req_quantity=' + txt_req_quantity + '&txt_avg_price=' + txt_avg_price + '&body_part_id=' + body_part_id + '&emb_type=' + emb_type + "&txtwoq=" + txtwoq + "&txt_booking_no=" + txt_booking_no + "&txtgmtitemid=" + txtgmtitemid;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=450px,center=1,resize=1,scrolling=0', '../')
			emailwindow.onclose = function() {
				var cons_breck_down = this.contentDoc.getElementById("cons_breck_down");
				var woq = this.contentDoc.getElementById("woqty_sum");
				var rate = this.contentDoc.getElementById("rate_sum");
				var amount = this.contentDoc.getElementById("amount_sum");
				var json_data = this.contentDoc.getElementById("json_data");
				document.getElementById('consbreckdown_' + i).value = cons_breck_down.value;
				document.getElementById('txtwoq_' + i).value = woq.value;
				document.getElementById('txtrate_' + i).value = rate.value;
				document.getElementById('txtamount_' + i).value = amount.value;
				//document.getElementById('jsondata_'+i).value=json_data.value;
				calculate_amount(i)
			}
		}
	}

	function openmypage_booking(page_link, title) {
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		page_link += "&cbo_company_name=" + cbo_company_name;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=450px,center=1,resize=1,scrolling=0', '../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("selected_booking");
			if (theemail.value != "") {
				freeze_window(5);
				reset_form('printbooking_1', 'booking_list_view*booking_list_view_list', '', 'cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3');
				get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/embellishment_wo_without_order_controller");
				set_button_status(1, permission, 'fnc_fabric_booking', 1);
				release_freezing();
			}
		}
	}

	function openmypage_emb_item(page_link, title) {
		if (form_validation('cbo_company_name*txt_booking_no', 'Company*Booking No') == false) {
			return;
		}
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var garments_nature = document.getElementById('garments_nature').value;
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		var cbo_currency = document.getElementById('cbo_currency').value;
		var cbo_supplier_name = document.getElementById('cbo_supplier_name').value;

		page_link = page_link + '&company_id=' + cbo_company_name + '&garments_nature=' + garments_nature + '&cbo_currency=' + cbo_currency + '&cbo_supplier_name=' + cbo_supplier_name + '&cbo_buyer_name=' + cbo_buyer_name + '&txt_booking_no=' + txt_booking_no;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1120px,height=450px,center=1,resize=1,scrolling=0', '../')
		emailwindow.onclose = function() {
			//freeze_window(5);
			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("txt_selected_id");
			var theemail2 = this.contentDoc.getElementById("txt_job_id");
			var theemail3 = this.contentDoc.getElementById("txt_selected_po");
			var theemail4 = this.contentDoc.getElementById("emb_id");
			if (theemail.value != "") {

				document.getElementById('txt_select_item').value = theemail.value;
				document.getElementById('txt_selected_po').value = theemail3.value;
				document.getElementById('txt_selected_trim_id').value = theemail4.value;
				fnc_generate_booking(theemail.value, theemail3.value, theemail4.value, cbo_company_name)
				//release_freezing();
			} else {
				//release_freezing();
			}
		}
	}

	function set_cons_break_down(i) {
		document.getElementById('consbreckdown_' + i).value = "";
		document.getElementById('jsondata_' + i).value = "";
		var garments_nature = document.getElementById('garments_nature').value;
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var txt_job_no = document.getElementById('txtjob_' + i).value;
		var txt_po_id = document.getElementById('txtpoid_' + i).value;
		var txtembcostid = document.getElementById('txtembcostid_' + i).value;
		var txtgmtitemid = document.getElementById('txtgmtitemid_' + i).value;
		var txt_update_dtls_id = document.getElementById('txtbookingid_' + i).value;
		var cbo_colorsizesensitive = document.getElementById('cbocolorsizesensitive_' + i).value;
		var txt_req_quantity = document.getElementById('txtreqqnty_' + i).value;
		var txt_avg_price = document.getElementById('txtrate_' + i).value;
		var txt_country = document.getElementById('txtcountry_' + i).value;
		var txtwoq = document.getElementById('txtbalwoq_' + i).value;
		var txtcurwoq = document.getElementById('txtwoq_' + i).value * 1;
		var cbo_level = document.getElementById('cbo_level').value * 1;
		var emb_name = document.getElementById('emb_name_' + i).value;
		var emb_type = document.getElementById('emb_type_' + i).value;
		var cons_breack_down = trim(return_global_ajax_value(garments_nature + "_" + cbo_company_name + "_" + txt_job_no + "_" + txt_po_id + "_" + txtembcostid + "_" + txtgmtitemid + "_" + txt_update_dtls_id + "_" + cbo_colorsizesensitive + "_" + txt_req_quantity + "_" + txt_avg_price + "_" + txt_country + "_" + emb_name + "_" + emb_type + "_" + cbo_level + "_" + txtcurwoq, 'set_cons_break_down', '', 'requires/embellishment_wo_without_order_controller'));
		cons_breack_down = cons_breack_down.split("**");
		document.getElementById('consbreckdown_' + i).value = trim(cons_breack_down[0]);
		document.getElementById('jsondata_' + i).value = cons_breack_down[1];
	}

	function fnc_trims_booking_dtls(operation) {
		freeze_window(operation);
		var delete_cause = '';

		if (operation == 2) {
			release_freezing();
			alert('Not Allowed');
			return;
			delete_cause = prompt("Please enter your delete cause", "");
			if (delete_cause == "") {
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if (delete_cause == null) {
				release_freezing();
				return;
			}
			var r = confirm("Press OK to Delete Or Press Cancel");
			if (r == false) {
				release_freezing();
				return;
			}
		}

		var data_all = "";
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			release_freezing();
			return;
		}
		data_all = data_all + get_submitted_data_string('txt_booking_no*strdata*cbo_pay_mode', "../../");

		var row_num = $('#tbl_list_search tr').length;
		if (row_num < 1) {
			alert("Select Item");
			release_freezing();
			return;
		}
		var z = 1;
		var data_all = "";
		for (var i = 1; i <= row_num; i++) {

			var consbreckdown = document.getElementById('consbreckdown_' + i).value;
			var txtbookingid = document.getElementById('txtbookingid_' + i).value;
			var txtreqamount = document.getElementById('txtreqamount_' + i).value * 1;
			var txtcuamount = document.getElementById('txtcuamount_' + i).value * 1;
			var txtddate = document.getElementById('txtddate_' + i).value * 1;
			var wo_amount = document.getElementById('txtamount_' + i).value * 1;
			var tot_wo_amt = (wo_amount + txtcuamount);
			var total_curr_wo_amt = tot_wo_amt;
			//alert(total_curr_wo_amt+'=='+txtreqamount);
			if (total_curr_wo_amt > txtreqamount) {
				//var booking_msg="Exceed Req Amount not allowed.\n Req. Amount : "+txtreqamount+"\n Current Amount : "+total_curr_wo_amt;
				//alert(booking_msg);
				//release_freezing();
				//return;
			}

			if (txtddate == "") {
				alert('Delivery Date Empty');
				release_freezing();
				return;
			}
			if (consbreckdown == "") {
				//set_cons_break_down(i)
			}
			var consbreckdown = document.getElementById('consbreckdown_' + i).value;
			//alert(consbreckdown);
			if (trim(consbreckdown) == "") {
				alert("Unable to create Cons break down for minimum work order Qty, Data  not saved");
				release_freezing();
				$('#search' + i).css('background-color', 'red');
				return;
			}



			data_all += "&txtbookingid_" + z + "='" + $('#txtbookingid_' + i).val() + "'" + "&txtreqId_" + z + "='" + $('#txtreqId_' + i).val() + "'" + "&txtembcostid_" + z + "='" + $('#txtembcostid_' + i).val() + "'" + "&txtgmtitemid_" + z + "='" + $('#txtgmtitemid_' + i).val() + "'" + "&emb_name_" + z + "='" + $('#emb_name_' + i).val() + "'" + "&txtuom_" + z + "='" + $('#txtuom_' + i).val() + "'" + "&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_' + i).val() + "'" + "&txtwoq_" + z + "='" + $('#txtwoq_' + i).val() + "'" + "&txtrate_" + z + "='" + $('#txtrate_' + i).val() + "'" + "&txtamount_" + z + "='" + $('#txtamount_' + i).val() + "'" + "&txtddate_" + z + "='" + $('#txtddate_' + i).val() + "'" + "&consbreckdown_" + z + "='" + $('#consbreckdown_' + i).val() + "'" + "&txtexchrate_" + z + "='" + $('#txtexchrate_' + i).val() + "'" + "&ReqbookingNo_" + z + "='" + $('#ReqbookingNo_' + i).val() + "'" + "&txtreqno_" + z + "='" + $('#txtreqno_' + i).val() + "'" + "&txtreqqnty_" + z + "='" + $('#txtreqqnty_' + i).val() + "'" + "&body_part_id_" + z + "='" + $('#body_part_id_' + i).val() + "'" + "&emb_type_" + z + "='" + $('#emb_type_' + i).val() + "'" + "&txtreqamount_" + z + "='" + $('#txtreqamount_' + i).val() + "'";
			z++;



			//data_all=data_all+get_submitted_data_string('txtbookingid_'+i+'*txtreqId_'+i+'*txtembcostid_'+i+'*txtgmtitemid_'+i+'*emb_name_'+i+'*txtuom_'+i+'*cbocolorsizesensitive_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*consbreckdown_'+i+'*txtexchrate_'+i+'*ReqbookingNo_'+i+'*txtreqno_'+i+'*txtreqqnty_'+i+'*body_part_id_'+i+'*emb_type_'+i+'*txtreqamount_'+i,"../../",i);
		}
		if (z == 1) {
			alert('No data Found.');
			release_freezing();
			return;
		}
		// var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*txt_booking_no*txt_order_no_id*txt_job_no*selected_id_for_delete*cbo_pay_mode',"../../")+dataAll;

		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var update_id = document.getElementById('update_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;

		var data = "action=save_update_delete_dtls&operation=" + operation + '&total_row=' + row_num + data_all + '&delete_cause=' + delete_cause + '&cbo_company_name=' + cbo_company_name + '&txt_booking_no=' + txt_booking_no + '&update_id=' + update_id;
		//	alert(data); 

		http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_booking_dtls_reponse;
	}

	function fnc_trims_booking_dtls_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split('**');
			if (trim(reponse[0]) == 'app1') {
				alert("This booking is approved")
				release_freezing();
				return;
			}
			if (trim(reponse[0]) == 'lockAnotherProcess') {
				//alert("This booking is Attached In Embellishment Order Entry. Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
				// release_freezing();
				// return;
			}

			/*if(reponse[3]>0)
			{
				if(trim(reponse[0])=='recv1'){
					//alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Delete Not Possible")
					//release_freezing();
					//return;
				}
				if(trim(reponse[0])=='piNo'){
					//alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Delete Not Possible")
					//release_freezing();
					//return;
				}
			}
			else
			{
				if(trim(reponse[0])=='recv1'){
					alert("Receive Number Found "+ trim(reponse[2])+" \n So Delete Not Possible")
					release_freezing();
					return;
				}
				if(trim(reponse[0])=='piNo'){
					//alert(" PI Number Found"+ trim(reponse[2])+" \n So Delete Not Possible")
					alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Delete Not Possible");
					release_freezing();
					return;
				}
			}

			if(trim(reponse[0])=='vad1'){
				alert("Budget amount Exceed")
				$('#search'+reponse[2]).css('background-color', 'red');
			    release_freezing();
			    return;
			}
			if(trim(reponse[0])=='vad2'){
				alert("Budget Qty Exceed")
				$('#search'+reponse[2]).css('background-color', 'red');
			    release_freezing();
			    return;
			}*/

			if (reponse[0].length > 2) reponse[0] = 10;
			show_msg(trim(reponse[0]));
			if (trim(reponse[0]) == 0 || trim(reponse[0]) == 1 || trim(reponse[0]) == 2) {
				var str = "";
				if (trim(reponse[0]) == 0) {
					str = 'Saved';
					$("#cbo_supplier_name").attr("disabled", true);
				}
				if (trim(reponse[0]) == 1) {
					str = 'Updated';
					$("#cbo_supplier_name").attr("disabled", true);

				}
				if (trim(reponse[0]) == 2) {
					str = 'Deleted';
				}
				//alert(reponse[0]);
				fnc_show_booking_list();
				document.getElementById('txt_select_item').value = '';
				document.getElementById('booking_list_view').innerHTML = ''
				document.getElementById('booking_list_view').innerHTML = '<font id="save_sms" style="color:#F00">Data ' + str + ', Select new Item</font>';

			}
			release_freezing();
		}
	}

	function fnc_show_booking_list() {
		var garments_nature = document.getElementById('garments_nature').value;
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var cbo_currency = document.getElementById('cbo_currency').value;
		var data = document.getElementById('txt_selected_po').value;
		var precost_id = document.getElementById('txt_selected_trim_id').value;
		var param = document.getElementById('txt_select_item').value;
		var param = "'" + param + "'"
		var data = "'" + data + "'"
		var precost_id = "'" + precost_id + "'"

		var data = "action=show_trim_booking_list" + get_submitted_data_string('txt_booking_no', "../../") + '&cbo_company_name=' + cbo_company_name + '&garments_nature=' + garments_nature + '&data=' + data + '&param=' + param + '&pre_cost_id=' + precost_id + '&cbo_currency=' + cbo_currency;
		http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_list_reponse;
	}

	function fnc_show_booking_list_reponse() {
		if (http.readyState == 4) {
			$("#cbo_currency").attr("disabled", true);
			document.getElementById('booking_list_view_list').innerHTML = http.responseText;
			set_button_status(1, permission, 'fnc_trims_booking', 2);
			set_all_onclick();
		}
	}

	function fnc_show_booking(wo_cost_emb_id, req_embl_dtls_id, req_id, req_booking_no, req_no) {
		freeze_window(operation);
		var garments_nature = document.getElementById('garments_nature').value;
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var cbo_currency = document.getElementById('cbo_currency').value;
		var txt_selected_po = document.getElementById('txt_selected_po').value;
		var precost_id = document.getElementById('txt_selected_trim_id').value;
		var param = document.getElementById('txt_select_item').value;

		var data = "action=show_trim_booking" + get_submitted_data_string('txt_booking_no', "../../") + '&cbo_company_name=' + cbo_company_name + '&garments_nature=' + garments_nature + '&req_id=' + req_id + '&req_booking_no=' + req_booking_no + '&wo_cost_emb_id=' + wo_cost_emb_id + '&cbo_currency=' + cbo_currency + '&txt_selected_po=' + txt_selected_po + '&req_embl_dtls_id=' + req_embl_dtls_id;
		http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}

	function fnc_show_booking_reponse() {
		if (http.readyState == 4) {
			$("#cbo_currency").attr("disabled", true);
			document.getElementById('booking_list_view').innerHTML = http.responseText;
			set_all_onclick();
			release_freezing();
		}
	}

	function fnc_fabric_booking(operation) {
		freeze_window(operation);
		var delete_cause = '';
		if (operation == 2) {
			delete_cause = prompt("Please enter your delete cause", "");
			if (delete_cause == "") {
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if (delete_cause == null) {
				release_freezing();
				return;
			}
			var r = confirm("Press OK to Delete Or Press Cancel");
			if (r == false) {
				release_freezing();
				return;
			}
		}

		if (document.getElementById('id_approved_id').value == 1) {
			alert("This booking is approved")
			release_freezing();
			return;
		}
		if (form_validation('cbo_company_name*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name', 'Company Name*Booking Date*Delivery Date*Pay Mode*Supplier Name') == false) {
			release_freezing();
			return;
		} else {
			if ('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][399]); ?>') {
				if (form_validation('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][399]); ?>', '<? echo implode('*', $_SESSION['logic_erp']['mandatory_message'][399]); ?>') == false) {
					release_freezing();
					return;
				}
			}
			var data = "action=save_update_delete&operation=" + operation + "&delete_cause=" + delete_cause + get_submitted_data_string('txt_booking_no*id_approved_id*cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_currency*cbo_supplier_name*cbo_pay_mode*cbo_source*cbo_ready_to_approved*txt_attention*txt_tenor*remarks*txt_delivery_to', "../../");
			http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}

	function fnc_fabric_booking_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split('**');

			if (trim(response[0]) == 'lockAnotherProcess') {
				alert("This booking is Attached In Embellishment Order Entry. Ref :" + trim(response[1]) + " \n So Update/Delete Not Allowed.")
				release_freezing();
				return;
			}
			if (response[0] == 0 || response[0] == 1 || response[0] == 2) {
				show_msg(trim(response[0]));
				document.getElementById('txt_booking_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				$("#cbo_company_name").attr("disabled", true);
				$("#cbo_buyer_name").attr("disabled", true);
				$("#cbo_supplier_name").attr("disabled", true);
				$("#cbo_currency").attr("disabled", true);
				$("#cbo_level").attr("disabled", true);

				set_button_status(1, permission, 'fnc_fabric_booking', 1);
			}

			if (response[0] == 2) {
				reset_form('printbooking_1', 'booking_list_view*booking_list_view_list', '', 'cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3');
			}
			if (trim(response[0]) == 'approved') {
				alert("This booking is approved");
				release_freezing();
				return;
			}
			//alert(response[0]);
			if (trim(response[0]) == 'piNo') {
				alert("PI Number Found :" + trim(response[2]) + "\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			release_freezing();
		}
	}


	function auto_completesupplier() // Auto Complite Party/Transport Com
	{

		if (form_validation('cbo_company_name*cbo_pay_mode', 'Company Name*PayMode') == false) {
			return;
		}
		//cbo_supplier_name hidden_supplier_id
		var company_id = document.getElementById('cbo_company_name').value;
		var pay_mode = document.getElementById('cbo_pay_mode').value;

		var supplier = return_global_ajax_value(company_id + '_' + pay_mode, 'supplier_company_action', '', 'requires/embellishment_wo_without_order_controller');
		//alert(supplier);
		supplierInfo = eval(supplier);
		$("#cbo_supplier_name").autocomplete({
			source: supplierInfo,
			search: function(event, ui) {
				$("#hidden_supplier_id").val("");
				$("#hidden_supplier_name").val("");
			},
			select: function(e, ui) {
				$(this).val(ui.item.label);
				$("#hidden_supplier_name").val(ui.item.label);
				$("#hidden_supplier_id").val(ui.item.id);
			}
		});

		$(".supplier_name").live("blur", function() {
			if ($(this).siblings(".hdn_supplier_name").val() == "") {
				$(this).val("");
			}
		});

	}

	function supplier_attention_check() {
		var hidden_supplier_id = document.getElementById('cbo_supplier_name').value;
		var cbo_pay_mode = document.getElementById('cbo_pay_mode').value;
		//alert(hidden_supplier_id);
		get_php_form_data(hidden_supplier_id + '_' + cbo_pay_mode, 'load_drop_down_attention', 'requires/embellishment_wo_without_order_controller');
	}

	function supplier_empty_check() {
		$("#cbo_supplier_name").val('');
		//$("#hidden_supplier_name").val('');
		//$("#hidden_supplier_id").val('');
		$("#cbo_supplier_name").removeAttr('disabled', 'disabled');

	}

	function generate_trim_report(action, report_type) {
		if (form_validation('txt_booking_no', 'Booking No') == false) {
			return;
		}
		freeze_window(operation);
		$report_title = $("div.form_caption").html();
		var data = "action=" + action + get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_template_id', "../../") + '&report_title=' + $report_title + '&report_type=' + report_type;
		http.open("POST", "requires/embellishment_wo_without_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}

	function generate_trim_report_reponse() {
		if (http.readyState == 4) {
			var file_data = http.responseText.split("****");
			var title = "Multiple Req. Wise Embellishment Work Order without order";
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
			var report_title = $("div.form_caption").html();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>' + report_title + '</title></head><body>' + document.getElementById('data_panel').innerHTML + '</body></html>');
			d.close();
			release_freezing();
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission);  ?>
		<form name="printbooking_1" autocomplete="off" id="printbooking_1">
			<fieldset style="width:950px;">
				<legend>Embellishment Work Order</legend>
				<table width="900" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td colspan="3" align="right" class="must_entry_caption"> Wo No </td>
						<td><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/embellishment_wo_without_order_controller.php?action=fabric_booking_popup','Print Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" /></td>
						<td align="right">
							<input type="hidden" id="id_approved_id">
							<input style="width:50px;" type="hidden" class="text_boxes" name="report_ids" id="report_ids" />
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="130" align="right" class="must_entry_caption">Company Name</td>
						<td><? echo create_drop_down("cbo_company_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_wo_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )", '', "");  ?></td>
						<td width="130" align="right">Buyer Name</td>
						<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", '', ""); ?></td>
						<td width="130" align="right" class="must_entry_caption">WO Date</td>
						<td><input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo date("d-m-Y"); ?>" disabled /></td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Delivery Date</td>
						<td><input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" /></td>
						<td align="right">Currency</td>
						<td><? echo create_drop_down("cbo_currency", 172, $currency, "", 1, "-- Select --", 2, "", 0); ?></td>
						<td align="right">Source</td>
						<td><? echo create_drop_down("cbo_source", 172, $source, "", 1, "-- Select Source --", 3, "", ""); ?></td>
					</tr>
					<tr>
						<td class="must_entry_caption" align="right">Pay Mode</td>
						<td><? echo create_drop_down("cbo_pay_mode", 172, $pay_mode, "", 1, "-- Select Pay Mode --", 0, "supplier_empty_check();load_drop_down( 'requires/embellishment_wo_without_order_controller', this.value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )", ""); ?></td>
						<td align="right" class="must_entry_caption">Supplier Name</td>
						<td id="supplier_td">

							<?
							echo create_drop_down("cbo_supplier_name", 172, $blank_array, "", 1, "-- Select Supplier --", 1, "supplier_empty_check()", "");
							//echo create_drop_down( "cbo_ready_to_approved", 172, $blank_array,"", 1, "-- Select--", 2, "","","" ); 
							?>

						</td>
						<td align="right">Ready To Approve</td>
						<td><? echo create_drop_down("cbo_ready_to_approved", 172, $yes_no, "", 1, "-- Select--", 2, "", "", ""); ?></td>
					</tr>

					<tr>
						<td align="right">Tenor</td>
						<td><input style="width:160px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
						<td align="right">Attention</td>
						<td align="left"><input class="text_boxes" type="text" style="width:160px;" name="txt_attention" id="txt_attention" /></td>

						<td align="right">Remarks</td>
						<td align="left"><input class="text_boxes" type="text" style="width:160;" name="remarks" id="remarks" />
							<input type="hidden" id="update_id">
							<input type="hidden" id="dtls_update_id">
						</td>

					</tr>
					<tr>

						<td align="right">Delivery To</td>
						<td><input style="width:160px;" type="text" class="text_boxes" name="txt_delivery_to" id="txt_delivery_to" /></td>
						<td>&nbsp;</td>
						<td><? include("../../terms_condition/terms_condition.php");
							terms_condition(399, 'txt_booking_no', '../../'); ?>
							<input type="hidden" id="update_id">
							<input type="hidden" id="dtls_update_id">
						</td>

					</tr>

					<tr>
						<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">&nbsp;</td>
					</tr>
					<tr>
						<td align="center" colspan="6" valign="middle" class="button_container">
							<?
							$date = date("d-m-Y");
							$dd = "disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_currency*cbo_supplier_name*cbo_level')";
							echo load_submit_buttons($permission, "fnc_fabric_booking", 0, 0, "reset_form('printbooking_1','booking_list_view*booking_list_view_list','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*txt_booking_date,$date',$dd)", 1);
							?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6">
							<div id="pdf_file_name"></div>
							<? echo create_drop_down("cbo_template_id", 100, $report_template_list, '', 0, '', 0, ""); ?>
							<input type="button" value="Print Booking" onClick="generate_trim_report('show_embellishment_booking_report',1)" style="width:100px;" name="print_booking1" id="print_booking1" class="formbutton" />
							<input type="button" value="Print Booking 2" onClick="generate_trim_report('show_embellishment_booking_report2',1)" style="width:100px;" name="print_booking2" id="print_booking2" class="formbutton" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
		<fieldset style="width:1247px;">
			<legend>Embellishment WO Details &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select Embellishment <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_emb_item('requires/embellishment_wo_without_order_controller.php?action=fabric_emb_item_popup',' Item Search')" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item" /> <input class="text_boxes" type="hidden" style="width:160px" readonly placeholder="Double Click" name="txt_selected_po" id="txt_selected_po" /> <input class="text_boxes" type="hidden" style="width:160px" readonly placeholder="Double Click" name="txt_selected_trim_id" id="txt_selected_trim_id" /></legend>
			<div id="booking_list_view">
				<font id="save_sms" style="color:#F00">Select new Item</font>
			</div>
		</fieldset>
		<div id="booking_list_view_list"></div>
	</div>
	<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>