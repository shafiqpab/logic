<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Receive Entry

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	07-05-2013
Updated by 		: 	Kausar
Update date		: 	28-10-2013
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

$sql_variable_inventory = sql_select("select company_name as COMPANY_NAME, independent_controll as INDEPENDENT_CONTROLL, rate_optional as RATE_OPTIONAL, is_editable as IS_EDITABLE, rate_edit as RATE_EDIT from variable_settings_inventory where variable_list=20 and status_active=1 and menu_page_id=1");
foreach ($sql_variable_inventory as $row) {
	$independent_control_arr[$row['COMPANY_NAME']]['independent_controll'] = $row['INDEPENDENT_CONTROLL'];
	$independent_control_arr[$row['COMPANY_NAME']]['rate_optional'] = $row['RATE_OPTIONAL'];
	$independent_control_arr[$row['COMPANY_NAME']]['is_editable'] = $row['IS_EDITABLE'];
	$independent_control_arr[$row['COMPANY_NAME']]['rate_edit'] = $row['RATE_EDIT'];
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Receive Info", "../", 1, 1, $unicode, 1, 1);
?>

<script>
	<?

	$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][1]);
	echo "var field_level_data= " . $data_arr . ";\n";

	if ($_SESSION['logic_erp']['mandatory_field'][1] != "") {
		$mandatory_field_arr = json_encode($_SESSION['logic_erp']['mandatory_field'][1]);
		echo "var mandatory_field_arr= " . $mandatory_field_arr . ";\n";
	}
	?>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	var str_brand = [<? echo substr(return_library_autocomplete("select distinct(a.brand_name) from lib_brand a, product_details_master b where a.id=b.brand and b.item_category_id=1", "brand_name"), 0, -1); ?>];
	$(function() {
		var brand_name = str_brand;
		$("#txt_brand").autocomplete({
			source: brand_name
		});
	});

	var str_lot = [<? echo substr(return_library_autocomplete("select distinct(lot) from product_details_master where item_category_id=1", "lot"), 0, -1); ?>];
	$(function() {
		var lot = str_lot;
		$("#txt_yarn_lot").autocomplete({
			source: lot
		});
	});


	function rcv_basis_reset() {
		reset_form('yarn_receive_1', 'list_container_yarn*list_product_container', '', '', '', 'cbo_company_id*cbo_receive_purpose*percentage1*cbo_color*cbo_uom*cbo_currency*txt_exchange_rate*txt_receive_date');
		//document.getElementById('cbo_receive_basis').value=0;
	}

	// popup for WO/PI----------------------
	function openmypage(page_link, title) {
		if (form_validation('cbo_company_id*cbo_receive_basis', 'Company Name*Receive Basis') == false) {
			return;
		}

		var company = $("#cbo_company_id").val();
		var receive_basis = $("#cbo_receive_basis").val();
		var receive_purpose = $("#cbo_receive_purpose").val();
		var vs_rate_hide = $("#vs_rate_hide").val();

		page_link = 'requires/yarn_receive_controller.php?action=wopi_popup&company=' + company + '&receive_basis=' + receive_basis + '&receive_purpose=' + receive_purpose;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px, height=400px, center=1, resize=0, scrolling=0', '')

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var rowID = this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
			var wopiNumber = this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
			var hidden_paymode = this.contentDoc.getElementById("hidden_paymode").value;
			var hidden_entry_form = this.contentDoc.getElementById("hidden_entry_form").value;
			var booking_without_order = this.contentDoc.getElementById("booking_without_order").value * 1;
			var is_sales = this.contentDoc.getElementById("is_sales").value * 1;
			//alert(hidden_entry_form);
			if (receive_basis == 2 && (hidden_entry_form == 42 || hidden_entry_form == 114)) {
				$('#txt_receive_qty').removeAttr('onclick', 'func_onclick_qty()').attr({
					'onblur': 'fn_calile()',
					'readonly': false
				});
			} else if (receive_basis == 2 && hidden_entry_form == 144) {
				$("#txt_grey_qty").attr("disabled", true);
				$("#txt_dyeing_charge").attr("disabled", true);
			} else if (receive_basis == 2 && (hidden_entry_form == 94 || hidden_entry_form == 340)) {
				if (booking_without_order == 2 && is_sales == 2) {
					$('#txt_receive_qty').removeAttr('onclick', 'func_onclick_qty()').attr({
						'onblur': 'fn_calile()',
						'readonly': false
					});
				} else {
					$('#txt_receive_qty').attr({
						'onclick': 'func_onclick_qty()',
						'readonly': true
					});
				}
			}

			if (rowID != "") {
				freeze_window(5);
				$("#txt_wo_pi").val(wopiNumber);
				$("#txt_wo_pi_id").val(rowID);
				$("#hdn_entry_form").val(hidden_entry_form);
				$("#tbl_child").find('input[type="text"],input[type="hidden"],select').val('');
				$("#cbo_uom").val(12);

				if (receive_purpose == 16) {
					$('#cbo_color').val($('#cbo_color option:last').val());
					$('#cbo_color').attr('disabled', 'disabled');
				}

				load_drop_down('requires/yarn_receive_controller', receive_basis + '_' + receive_purpose + '_' + rowID, 'load_drop_down_color', 'color_td_id');

				if (receive_basis == 2 && (hidden_paymode == 3 || hidden_paymode == 5) && (receive_purpose == 2 || receive_purpose == 15 || receive_purpose == 38 || receive_purpose == 44 || receive_purpose == 46 || receive_purpose == 50 || receive_purpose == 51)) {
					load_drop_down('requires/yarn_receive_controller', receive_basis + '_' + receive_purpose + '_' + wopiNumber + '_' + hidden_paymode + '_' + hidden_entry_form, 'load_drop_down_company_from_eheck_wo_paymode', 'supplier');
				}

				if (receive_basis == 1 || receive_basis == 2) {
					get_php_form_data(receive_basis + "**" + rowID + "**" + receive_purpose + "**" + company, "populate_data_from_wopi_popup", "requires/yarn_receive_controller");
				}

				//for exchange rate
				exchange_rate($("#cbo_currency").val());
				show_list_view(receive_basis + "**" + rowID + "**" + receive_purpose + "**" + company + "**" + vs_rate_hide, 'show_product_listview', 'list_product_container', 'requires/yarn_receive_controller', '');

				release_freezing();
			}
		}
	}
	//hidden_entry_form
	// enable disable field for independent
	function fn_independent(val) {
		var company_id = $('#cbo_company_id').val();
		var MRR_Number = $('#txt_mrr_no').val();
		var hdn_entry_form = $('#hdn_entry_form').val();
		var cbo_receive_purpose = $("#cbo_receive_purpose").val();
		if (val == 4 || val == 14) {
			if (MRR_Number == '') {
				reset_form('', 'list_product_container', 'txt_wo_pi*txt_wo_pi_id*txt_lc_no*hidden_lc_id*txt_exchange_rate*cbo_currency', '', '', ''); //cbo_currency,1*
				$("#cbo_supplier").attr("disabled", false);
				$("#cbo_currency").attr("disabled", false);
				$("#cbo_source").attr("disabled", false);

				if (val == 4) {
					$("#txt_wo_pi").attr("disabled", true);
				} else {
					$("#txt_wo_pi").attr("disabled", false);
				}

				$("#cbo_yarn_count").attr("disabled", false).val("");
				$("#cbocomposition1").attr("disabled", false).val("");
				$("#cbo_yarn_type").attr("disabled", false).val("");
			} else {
				$("#cbo_supplier").attr("disabled", true);
				$("#cbo_currency").attr("disabled", true);
				$("#cbo_source").attr("disabled", true);
				$("#txt_wo_pi").attr("disabled", true);
				$("#cbo_yarn_count").attr("disabled", false).val("");
				$("#cbocomposition1").attr("disabled", false).val("");
				$("#cbo_yarn_type").attr("disabled", false).val("");
			}
		} else {
			if (MRR_Number == '') {
				if (val == 1) {
					$("#txt_grey_qty").attr("disabled", true);
					$("#txt_dyeing_charge").attr("disabled", true);
				} else {
					$("#txt_grey_qty").attr("disabled", false);
					$("#txt_dyeing_charge").attr("disabled", false);
				}

				$("#cbo_supplier").attr("disabled", true);
				$("#cbo_currency").attr("disabled", true);
				$("#cbo_source").attr("disabled", true);
				$("#txt_wo_pi").attr("disabled", false);
				$("#cbo_yarn_count").attr("disabled", true);
				$("#cbocomposition1").attr("disabled", true);
				$("#cbo_yarn_type").attr("disabled", true);
			} else {
				if (val == 1) {
					$("#txt_grey_qty").attr("disabled", true);
					$("#txt_dyeing_charge").attr("disabled", true);
				} else {
					$("#txt_grey_qty").attr("disabled", false);
					$("#txt_dyeing_charge").attr("disabled", false);
				}
				if (val == 2 && hdn_entry_form == 144) {
					$("#txt_grey_qty").attr("disabled", true);
					$("#txt_dyeing_charge").attr("disabled", true);
				} else {
					$("#txt_grey_qty").attr("disabled", false);
					$("#txt_dyeing_charge").attr("disabled", false);
				}

				$("#cbo_supplier").attr("disabled", true);
				$("#cbo_currency").attr("disabled", true);
				$("#cbo_source").attr("disabled", true);
				$("#txt_wo_pi").attr("disabled", true);
				$("#cbo_yarn_count").attr("disabled", true);
				$("#cbocomposition1").attr("disabled", true);
				$("#cbo_yarn_type").attr("disabled", true);
				$("#txt_exchange_rate").attr("disabled", true);
			}
		}

		if (val == 1 || val == 4 || val == 14) // pi / independent/sales order
		{
			$('#txt_receive_qty').removeAttr('onclick', 'func_onclick_qty()').attr({
				'onblur': 'fn_calile()',
				'readonly': false
			});
		} else {
			$('#txt_receive_qty').attr({
				'onclick': 'func_onclick_qty()',
				'readonly': true
			});
		}

		rate_cond(cbo_receive_purpose);
	}

	// LC pop up script here-----------------------------------Not Used
	function popuppage_lc() {
		if (form_validation('cbo_company_id', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_id").val();
		var page_link = 'requires/yarn_receive_controller.php?action=lc_popup&company=' + company;
		var title = "Search LC Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0', ' ')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var rowID = this.contentDoc.getElementById("hidden_tbl_id").value; // lc table id
			var wopiNumber = this.contentDoc.getElementById("hidden_wopi_number").value; // lc number
			$("#txt_lc_no").val(wopiNumber);
			$("#hidden_lc_id").val(rowID);
		}
	}

	// calculate ILE ---------------------------
	function fn_calile() {
		if ($('#cbo_receive_basis').val() == 14) {
			return;
		}

		if (form_validation('cbo_company_id*cbo_source*txt_rate', 'Company Name*Source*Rate') == false) {
			return;
		}

		//grey used qty calculate--------------//
		var vs_process_loss_perc = $("#hdn_service_process_loss_percentage").val() * 1;
		if (vs_process_loss_perc > 0) {
			var rcv_quantity = $("#txt_receive_qty").val() * 1
			var grey_yarn_avgrate = $("#hidden_grey_yarn_avg_rate").val() * 1;
			var dyeing_charge = $("#txt_dyeing_charge").val() * 1;

			var percentage_value = ((vs_process_loss_perc / 100) * rcv_quantity);
			var grey_yarn_used_qty = (rcv_quantity + percentage_value);
			var new_grey_yarn_avgrate = ((grey_yarn_used_qty * grey_yarn_avgrate) / rcv_quantity);

			$("#txt_avg_rate").val(new_grey_yarn_avgrate);
			$("#txt_rate").val(new_grey_yarn_avgrate + dyeing_charge);
			$("#txt_grey_qty").val(grey_yarn_used_qty);
		}
		//grey used qty calculate end --------------//

		var company = $('#cbo_company_id').val();
		var source = $('#cbo_source').val();
		var rate = $('#txt_rate').val();

		var responseHtml = return_ajax_request_value(company + '**' + source + '**' + rate, 'show_ile', 'requires/yarn_receive_controller');
		var splitResponse = "";

		if (responseHtml != "") {
			splitResponse = responseHtml.split("**");
			$("#ile_td").html('ILE% ' + splitResponse[0]);
			$("#txt_ile").val(splitResponse[1]);
		} else {
			$("#ile_td").html('ILE% 0');
			$("#txt_ile").val(0);
		}

		//amount and book currency calculate--------------//
		var quantity = $("#txt_receive_qty").val();
		var exchangeRate = $("#txt_exchange_rate").val();
		var ile_cost = $("#txt_ile").val();
		var amount = quantity * 1 * (rate * 1 + ile_cost * 1);
		var bookCurrency = (rate * 1 + ile_cost * 1) * exchangeRate * 1 * quantity * 1;
		$("#txt_amount").val(number_format_common(amount, "", "", 1));
		$("#txt_book_currency").val(number_format_common(bookCurrency, "", "", 1));
	}

	function fn_room_rack_self_box() {
		if ($("#cbo_room").val() * 1 > 0)
			disable_enable_fields('txt_rack', 0, '', '');
		else {
			reset_form('', '', 'txt_rack*txt_shelf*cbo_bin', '', '', '');
			disable_enable_fields('txt_rack*txt_shelf*cbo_bin', 1, '', '');
		}
		if ($("#txt_rack").val() * 1 > 0)
			disable_enable_fields('txt_shelf', 0, '', '');
		else {
			reset_form('', '', 'txt_shelf*cbo_bin', '', '', '');
			disable_enable_fields('txt_shelf*cbo_bin', 1, '', '');
		}
		if ($("#txt_shelf").val() * 1 > 0)
			disable_enable_fields('cbo_bin', 0, '', '');
		else {
			reset_form('', '', 'cbo_bin', '', '', '');
			disable_enable_fields('cbo_bin', 1, '', '');
		}
	}

	function fn_comp_new(val) {

		if (document.getElementById(val).value == 'N') // when new(N) button click
		{
			load_drop_down('requires/yarn_receive_controller', 1, 'load_drop_down_composition', 'composition_td');
		} else // When F button click
		{
			load_drop_down('requires/yarn_receive_controller', 2, 'load_drop_down_composition', 'composition_td');
		}
	}

	function fn_color_new(val) {
		if (form_validation('cbo_receive_purpose', 'Receive Purpose') == false) {
			return;
		}

		var receive_basis = $("#cbo_receive_basis").val();
		var receive_purpose = $("#cbo_receive_purpose").val();
		var txt_wo_pi_id = $("#txt_wo_pi_id").val();
		var cbo_company_id = $("#cbo_company_id").val();

		/* if (receive_purpose == 16) {
			return;
		} */ // Omit instructed by Saeed via and jahid hasan vai 

		if (receive_basis == 2 && receive_purpose == 2) {
			load_drop_down('requires/yarn_receive_controller', receive_basis + '_' + receive_purpose + '_' + txt_wo_pi_id, 'load_drop_down_color', 'color_td_id');
			return;
		}

		if (document.getElementById(val).value == 'N') // when new(N) button click
		{
			document.getElementById('color_td_id').innerHTML = '<input type="text" name="cbo_color" id="cbo_color" class="text_boxes" style="width:100px" /><input type="button" class="formbuttonplasminus" name="btn_color" id="btn_color" width="15" onClick="fn_color_new(this.id)" value="F" />';
			$('#cbo_color').attr('readonly', false);
			$('#cbo_color').removeAttr('placeholder', 'Click');
		} else // When F button click
		{
			load_drop_down('requires/yarn_receive_controller', '', 'load_drop_down_color', 'color_td_id');
		}
	}

	function fnc_yarn_receive_entry(operation) {

		if (operation == 4) {
			print_report($('#cbo_company_id').val() + '*' + $('#txt_mrr_no').val(), "yarn_receive_print", "requires/yarn_receive_controller")
			return;
		} else if (operation == 5) {
			print_report($('#cbo_company_id').val() + '*' + $('#txt_mrr_no').val(), "yarn_receive_print2", "requires/yarn_receive_controller")
			return;
		} else if (operation == 6) {
			print_report($('#cbo_company_id').val() + '*' + $('#txt_mrr_no').val(), "yarn_receive_print3", "requires/yarn_receive_controller")
			return;
		} else {

			if ($("#is_posted_account").val() == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}

			if (form_validation('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_challan_no*cbo_store_name*cbo_supplier*txt_rate*cbo_currency*cbo_source*cbo_yarn_count*txt_brand*cbo_yarn_type*cbo_color*txt_yarn_lot*txt_receive_qty', 'Company Name*Receive Basis*Receive Purpose*Receive Date*Challan No*Store Name*Supplier*Rate*Currency*Source*Yarn Count*Brand*Yarn Type*Color*Yarn Lot*Receive Quantity') == false) {
				return;
			}

			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_receive_date').val(), current_date) == false) {
				alert("Receive Date Can not Be Greater Than Current Date");
				return;
			}

			if ($("#cbo_receive_purpose").val() == 5) {
				if (form_validation('cbo_party', 'Loan Party') == false) {
					return;
				}
			}

			var isFileMandatory = "";
			<?
			if (!empty($_SESSION['logic_erp']['mandatory_field'][1][9])) echo " isFileMandatory = " . $_SESSION['logic_erp']['mandatory_field'][1][9] . ";\n";
			?>
			// alert(isFileMandatory); return;
			if ($("#multiple_file_field")[0].files.length == 0 && isFileMandatory != "" && $('#txt_mrr_no').val() == '') {

				document.getElementById("multiple_file_field").focus();
				var bgcolor = '-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
				document.getElementById("multiple_file_field").style.backgroundImage = bgcolor;
				alert("Please Add File in Master Part");
				return;
			}


			if ('<?php echo implode('*', $_SESSION['logic_erp']['mandatory_field'][1]); ?>') {
				if (form_validation('<?php echo implode('*', $_SESSION['logic_erp']['mandatory_field'][1]); ?>', '<?php echo implode('*', $_SESSION['logic_erp']['field_message'][1]); ?>') == false) {

					return;
				}
			}

			if (operation != 2) {
				var perc = $("#percentage1").val() * 1 + $("#percentage2").val() * 1;
				if (perc != 100) {
					alert('Percentage Should Be 100');
					return;
				}
			}

			if (($("#percentage1").val() != "" && $("#cbocomposition1").val() == 0) || ($("#percentage1").val() == "" && $("#cbocomposition1").val() != 0)) {
				alert('First Composition');
				return;
			} else if (($("#percentage2").val() != "" && $("#cbocomposition2").val() == 0) || ($("#percentage2").val() == "" && $("#cbocomposition2").val() != 0)) {
				alert('2nd Composition');
				return;
			} else if ($("#cbocomposition1").val() == $("#cbocomposition2").val()) {
				alert('2nd Composition');
				return;
			} else if ($("#txt_exchange_rate").val() == "" || $("#txt_exchange_rate").val() == 0) {
				$("#txt_exchange_rate").val('');
				form_validation('txt_exchange_rate', 'Exchange Rate');
				return;
			} else {
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

				var txt_rate = $("#txt_rate").val();
				var txt_avg_rate = $("#txt_avg_rate").val();
				var txt_dyeing_charge = $("#txt_dyeing_charge").val();
				var txt_amount = $("#txt_amount").val();
				var txt_book_currency = $("#txt_book_currency").val();


				var dataString = "txt_mrr_no*cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_challan_no*cbo_store_name*txt_lc_no*hidden_lc_id*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*txt_wo_pi*txt_wo_pi_id*cbo_yarn_count*cbocomposition1*cbocomposition2*percentage1*percentage2*cbo_yarn_type*btn_color*cbo_color*txt_yarn_lot*txt_brand*txt_receive_qty*txt_ile*cbo_uom*txt_order_qty*txt_no_bag*txt_prod_code*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_floor*txt_prod_id*update_id*txt_cone_per_bag*txt_no_loose_cone*txt_remarks*txt_weight_per_bag*txt_weight_per_cone*job_no*txt_issue_challan_no*txt_issue_id*allocation_maintained*cbo_party*txt_mst_remarks*cbo_buyer_name*hdn_receive_qty*txt_pi_basis*txt_overRecPerc*txt_wo_pi_dtls_id*txt_grey_qty*hdn_grey_qty*hdnReceiveString*hdnOldReceiveString*hdnYarnDyingDtlsId*hdnPayMode*hdn_entry_form*txt_challan_date*txt_gate_entry_no*txt_gate_entry_date*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date";
				var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../") + "&txt_rate=" + txt_rate + "&txt_avg_rate=" + txt_avg_rate + "&txt_dyeing_charge=" + txt_dyeing_charge + "&txt_amount=" + txt_amount + "&txt_book_currency=" + txt_book_currency;
				freeze_window(operation);
				http.open("POST", "requires/yarn_receive_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_yarn_receive_entry_reponse;
			}
		}
	}

	function fnc_yarn_receive_entry_reponse() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var reponse = trim(http.responseText).split('**');
			if (reponse[0] == 15) {
				setTimeout('fnc_yarn_receive_entry(' + reponse[2] + ')', 8000);
				return;
			} else if (reponse[0] == 40) {
				alert(reponse[1]);
				release_freezing();
				return;
			} else if (reponse[0] == 50) {
				alert(reponse[1]);
				release_freezing();
				return;
			} else if (reponse[0] == 30 || reponse[0] == 20 || reponse[0] == 13) {
				show_msg(reponse[0]);
				alert(reponse[1]);
				release_freezing();
				return;
			} else if (reponse[0] == 10) {
				release_freezing();
				return;
			} else if (reponse[0] == 0) {
				show_msg(reponse[0]);
				var check_system_id = $("#txt_mrr_no").val();
				$("#txt_mrr_no").val(reponse[1]);
				if (check_system_id == "") uploadFile($("#txt_mrr_no").val());
			} else if (reponse[0] == 1 || reponse[0] == 2) {
				if (reponse[2] == 0) {
					fnResetForm();
				}
				show_msg(reponse[0]);

				if (reponse[2] == 4) {
					$("#txt_rate").removeAttr('disabled', 'disabled');
				}

				set_button_status(0, permission, 'fnc_yarn_receive_entry', 1, 1);
			}

			var vs_rate_hide = $("#vs_rate_hide").val();

			show_list_view(reponse[1] + "**" + '' + "**" + vs_rate_hide, 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_receive_controller', '');
			set_button_status(0, permission, 'fnc_yarn_receive_entry', 1, 1);
			disable_enable_fields('txt_mrr_no*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*txt_yarn_lot', 0, "", "");
			disable_enable_fields('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_wo_pi*cbo_currency*txt_receive_date*cbo_store_name*cbo_supplier*cbo_source', 1, "", "");

			if ($("#cbo_receive_basis").val() == 1 || $("#cbo_receive_basis").val() == 2) {
				change_color_tr(0, '');
			}
			//child form reset here after save data-------------//
			var txt_wo_pi_id = $("#txt_wo_pi_id").val();
			var txt_wo_pi = $("#txt_wo_pi").val();
			var btn_color = $("#btn_color").val();
			var hdn_entry_form = $("#hdn_entry_form").val();
			$("#tbl_child").find('input,select:not([name="cbo_color"])').val('');
			$("#txt_wo_pi_id").val(txt_wo_pi_id);
			$("#hdn_entry_form").val(hdn_entry_form);
			$("#txt_wo_pi").val(txt_wo_pi);
			$("#btn_color").val(btn_color);
			$("#cbo_uom").val(12);
			$("#percentage1").val(100);

			release_freezing();
		}
	}

	function uploadFile(txt_mrr_no) {
		// alert(txt_mrr_no);
		$(document).ready(function() {

			var suc = 0;
			var fail = 0;
			for (var i = 0; i < $("#multiple_file_field")[0].files.length; i++) {
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i];
				fd.append('file', files);
				// alert(txt_mrr_no);
				$.ajax({
					url: 'requires/yarn_receive_controller.php?action=file_upload&txt_mrr_no=' + txt_mrr_no,
					type: 'post',
					data: fd,
					contentType: false,
					processData: false,
					success: function(response) {
						var res = response.split('**');
						if (res[0] == 0) {

							suc++;
						} else if (fail == 0) {
							alert('file not uploaded');
							fail++;
						}
					},
				});
			}

			if (suc > 0) {
				document.getElementById('multiple_file_field').value = '';
			}
		});
	}

	function control_composition(type) {
		var cbocompone = (document.getElementById('cbocomposition1').value);
		var cbocomptwo = (document.getElementById('cbocomposition2').value);
		var percentone = (document.getElementById('percentage1').value) * 1;
		var percenttwo = (document.getElementById('percentage2').value) * 1;

		if (percentone > 100) {
			alert("Percentage Greater Than 100 Not Allowed");
			document.getElementById('percentage1').value = "";
		}

		return;
		// Previous validation
		if (type == 'percent_one' && percentone > 100) {
			alert("Greater Than 100 Not Allowed");
			document.getElementById('percentage1').value = "";
		}

		if (type == 'percent_one' && percentone <= 0) {
			alert("0 Or Less Than 0 Not Allowed")
			document.getElementById('percentage1').value = "";
			document.getElementById('percentage1').disabled = true;
			document.getElementById('cbocomposition1').value = 0;
			document.getElementById('cbocomposition1').disabled = true;
			document.getElementById('percentage2').value = 100;
			document.getElementById('percentage2').disabled = false;
			document.getElementById('cbocomposition2').disabled = false;
		}

		if (type == 'percent_one' && percentone == 100) {
			document.getElementById('percentage2').value = "";
			document.getElementById('cbocomposition2').value = 0;
			document.getElementById('percentage1').disabled = false;
			document.getElementById('cbocomposition1').disabled = false;
			document.getElementById('percentage2').disabled = true;
			document.getElementById('cbocomposition2').disabled = true;
		}

		if (type == 'percent_one' && percentone < 100 && percentone > 0) {
			document.getElementById('percentage2').value = 100 - percentone;
			document.getElementById('percentage2').disabled = false;
			document.getElementById('cbocomposition2').disabled = false;
		}

		if (type == 'comp_one' && cbocompone == cbocomptwo) {
			alert("Same Composition Not Allowed");
			document.getElementById('cbocomposition1').value = 0;
		}

		if (type == 'percent_two' && percenttwo > 100) {
			alert("Greater Than 100 Not Allwed")
			document.getElementById('percentage2').value = "";
		}
		if (type == 'percent_two' && percenttwo <= 0) {
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percentage2').value = "";
			document.getElementById('percentage2').disabled = true;
			document.getElementById('cbocomposition2').value = 0;
			document.getElementById('cbocomposition2').disabled = true;
			document.getElementById('percentage1').value = 100;
			document.getElementById('percentage1').disabled = false;
			document.getElementById('cbocomposition1').disabled = false;
		}

		if (type == 'percent_two' && percenttwo == 100) {
			document.getElementById('percentage1').value = "";
			document.getElementById('cbocomposition1').value = 0;
			document.getElementById('percentage1').disabled = false;
			document.getElementById('cbocomposition1').disabled = false;
			document.getElementById('percentage2').disabled = true;
			document.getElementById('cbocomposition2').disabled = true;
		}

		if (type == 'percent_two' && percenttwo < 100 && percenttwo > 0) {
			document.getElementById('percentage1').value = 100 - percenttwo;
			document.getElementById('percentage1').disabled = false;
			document.getElementById('cbocomposition1').disabled = false;
		}

		if (type == 'comp_two' && cbocomptwo == cbocompone) {
			alert("Same Composition Not Allowed");
			document.getElementById('cbocomposition2').value = 0;
		}
	}

	function open_mrrpopup() {
		if (form_validation('cbo_company_id', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_id").val();
		var vs_rate_hide = $("#vs_rate_hide").val();
		var page_link = 'requires/yarn_receive_controller.php?action=mrr_popup_info&company=' + company;
		var title = "Search MRR Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0', ' ')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var recv_id = this.contentDoc.getElementById("hidden_recv_id").value;
			var mrrNumber = this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
			var posted_in_account = this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
			var supp_id = this.contentDoc.getElementById("supp_id").value; 
			var newsupplierId = $("#supplier_id_new").val(supp_id);
			$("#txt_mrr_no").val(mrrNumber);
			$("#is_posted_account").val(posted_in_account);
			$("#tbl_child").find('input,select').val('');

			$("#btn_color").val('N');
			if (posted_in_account == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting.";
			else document.getElementById("accounting_posted_status").innerHTML = "";

			get_php_form_data(mrrNumber + "_" + recv_id + "_" + vs_rate_hide, "populate_data_from_data", "requires/yarn_receive_controller");
			//$("#tbl_master").find('input,select').attr("disabled", true);
			$("#cbo_company_id").attr("disabled", "disabled");

			//disable_enable_fields( 'txt_mrr_no', 0, "", "" );
			var receive_purpose = $("#cbo_receive_purpose").val();
			var receive_basis = $("#cbo_receive_basis").val();
			$("#cbo_currency").attr("disabled", "disabled");
			rate_cond(receive_purpose);
			//fn_independent($("#cbo_receive_basis").val());
			disable_enable_fields('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_currency*txt_receive_date*cbo_currency', 1, "", "");
			fn_independent(receive_basis);
			set_button_status(0, permission, 'fnc_yarn_receive_entry', 1, 1);
			$('#percentage1').val(100);
			$("#cbo_uom").val(12);
		}
	}

	function change_color_tr(v_id, e_color) {
		var tot_row = $("#tbl_product tbody tr").length;
		for (var i = 1; i <= tot_row; i++) {
			if (v_id == i) {
				document.getElementById("tr_" + v_id).bgColor = "#33CC00";
			} else {
				if (i % 2 == 0) Bcolor = "#E9F3FF";
				else Bcolor = "#FFFFFF";
				document.getElementById("tr_" + i).bgColor = Bcolor;
			}
		}
	}

	function openpage_challan() {
		if (form_validation('cbo_company_id*cbo_supplier', 'Company Name*Supplier') == false) {
			return;
		}

		var receive_purpose = $("#cbo_receive_purpose").val();
		var company = $("#cbo_company_id").val();
		var supplier = $("#cbo_supplier").val();

		if (receive_purpose == 15 || receive_purpose == 50 || receive_purpose == 51) {
			var page_link = 'requires/yarn_receive_controller.php?action=issue_challan_popup_info&company=' + company + '&supplier=' + supplier;
			var title = "Issue Challan No Popup";
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0', ' ')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];

				var issue_id = this.contentDoc.getElementById("hidden_issue_id").value;
				var challan_number = this.contentDoc.getElementById("hidden_challan_number").value; // mrr number
				$("#txt_issue_challan_no").val(issue_id);
				$("#txt_issue_id").val(challan_number);
			}
		}
	}

	function load_supplier() {
		var receive_purpose = $("#cbo_receive_purpose").val();
		var receive_basis = $("#cbo_receive_basis").val();
		var txt_wo_pi_id = $("#txt_wo_pi_id").val();
		var company = $("#cbo_company_id").val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			$("#cbo_receive_purpose").val(0);
			return;
		}

		$("#txt_issue_challan_no").val('');
		$("#txt_issue_id").val('');
		$("#cbo_party").val(0);
		$('#loanParty_td').css('color', 'black');
		var newsupplierId = $("#supplier_id_new").val();
		
		load_drop_down('requires/yarn_receive_controller', receive_basis + "_" + receive_purpose + "_" + txt_wo_pi_id, 'load_drop_down_color', 'color_td_id');
		if (receive_purpose == 15 || receive_purpose == 50 || receive_purpose == 51) {
			$("#txt_issue_challan_no").attr("disabled", false);

			load_drop_down('requires/yarn_receive_controller', company, 'load_drop_down_supplier_from_issue', 'supplier');
			if ($('#cbo_supplier option').length == 2) {
				$('#cbo_supplier').val($('#cbo_supplier option:last').val());
			}
			$('#cbo_party').attr('disabled', 'disabled');
		} else if (receive_purpose == 5) {
			$("#txt_issue_challan_no").attr("disabled", true);
			load_drop_down('requires/yarn_receive_controller', company, 'load_drop_down_supplier', 'supplier');
			load_drop_down('requires/yarn_receive_controller', company, 'load_drop_down_party', 'loanParty');
			$('#cbo_party').removeAttr('disabled', 'disabled');
			$('#loanParty_td').css('color', 'blue');
		} else if (receive_purpose == 16) {
			$("#txt_issue_challan_no").attr("disabled", true);
			//load_drop_down('requires/yarn_receive_controller', company, 'load_drop_down_supplier', 'supplier');


			load_drop_down('requires/yarn_receive_controller', company+'*'+newsupplierId, 'load_drop_down_supplier_new', 'supplier');

			$('#cbo_party').attr('disabled', 'disabled');
			$('#cbo_color').val($('#cbo_color option:last').val());
			$('#cbo_color').attr('disabled', 'disabled');
		} else {
			$("#txt_issue_challan_no").attr("disabled", true);
			load_drop_down('requires/yarn_receive_controller', company, 'load_drop_down_supplier', 'supplier');
			$('#cbo_party').attr('disabled', 'disabled');
		}

		if (receive_basis == 4) $('#cbo_supplier').removeAttr('disabled', 'disabled');
		else $('#cbo_supplier').attr('disabled', 'disabled');

		$("#tbl_child").find("input[type=text],input[type=hidden],select").val('');
		$('#cbo_uom').val(12);
		$('#percentage1').val(100);
	}

	//form reset/refresh function here
	function fnResetForm() {
		$("#tbl_master").find('input').attr("disabled", false);
		disable_enable_fields('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_store_name*txt_wo_pi*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*cbo_color', 0, "", "");
		disable_enable_fields('cbo_party*txt_lc_no*txt_issue_challan_no', 1, "", "");
		set_button_status(0, permission, 'fnc_yarn_receive_entry', 1);
		reset_form('yarn_receive_1', 'list_container_yarn*list_product_container', '', '', '', 'cbo_uom*cbo_currency*txt_exchange_rate');
		document.getElementById("accounting_posted_status").innerHTML = "";
		$("#txt_rate").val(0);
		$("#ile_td").text('ILE%');
	}

	function rate_cond(val) {
		if (form_validation('cbo_company_id*cbo_receive_basis', 'Company Name*Receive Basis') == false) {
			return;
		} else {
			$("#txt_rate").val('');
			$("#txt_dyeing_charge").val('');
			$("#txt_avg_rate").val('');
			var company_id = $("#cbo_company_id").val();
			var cbo_receive_basis = $("#cbo_receive_basis").val();
			var hdn_entry_form = $("#hdn_entry_form").val();

			if (cbo_receive_basis == 4) // independent
			{
				$("#txt_rate").attr("disabled", false);
				$("#txt_dyeing_charge").attr("disabled", true);
			} else if (cbo_receive_basis == 2 && hdn_entry_form == 144) // wo/booking
			{
				$("#txt_rate").attr("disabled", false);
				$("#txt_dyeing_charge").attr("disabled", true);
				$("#txt_grey_qty").attr("disabled", true);
			} else if (cbo_receive_basis == 2 && hdn_entry_form != 144) // wo/booking
			{
				$("#txt_dyeing_charge").attr("disabled", false);
				$("#txt_grey_qty").attr("disabled", false);
			} else if (cbo_receive_basis == 1) // pi
			{
				$("#txt_rate").attr("disabled", false);
				$("#txt_dyeing_charge").attr("disabled", true);
				$("#txt_grey_qty").attr("disabled", true);
			} else {
				$("#txt_rate").attr("disabled", true);
				$("#txt_dyeing_charge").attr("disabled", false);
			}
		}
	}

	function exchange_rate(val) {
		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}
		var company_id = $("#cbo_company_id").val();
		if (val == 1) {
			$("#txt_exchange_rate").val(1);
			//$("#txt_exchange_rate").attr("disabled",true);
		} else {
			var recv_date = $('#txt_receive_date').val();
			var response = return_global_ajax_value(val + "**" + recv_date + "**" + company_id, 'check_conversion_rate', '', 'requires/yarn_receive_controller');
			$('#txt_exchange_rate').val(response);
			//$("#txt_exchange_rate").attr("disabled",false);
		}
	}

	function calculate_rate() {
		var receive_purpose = $("#cbo_receive_purpose").val();
		if (receive_purpose == 2 || receive_purpose == 15 || receive_purpose == 50 || receive_purpose == 51) {
			$("#txt_rate").val(($("#txt_avg_rate").val() * 1) + ($("#txt_dyeing_charge").val() * 1));
		} else {
			$("#txt_rate").val(0);
		}
	}

	function get_receive_basis(company_id) {
		var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');

		$("#cbo_receive_basis option[value='4']").show();

		if (independent_control_arr) {
			if (independent_control_arr[company_id]['independent_controll'] == 1) {
				$("#cbo_receive_basis option[value='4']").hide();
			} else {
				$("#cbo_receive_basis option[value='4']").show();
			}

			if (independent_control_arr[company_id]['rate_optional'] == 1 && independent_control_arr[company_id]['is_editable'] == 1) //is_editable=== cbo_rate_hide
			{
				$('#rate_td').css("display", "none");
				$('#amount_td').css("display", "none");
				$('#book_currency_td').css("display", "none");
			} else {
				$('#rate_td').css("display", "");
				$('#amount_td').css("display", "");
				$('#book_currency_td').css("display", "");
			}

			if (independent_control_arr[company_id]['rate_edit'] == 1) {
				$('#txt_rate').attr("readonly", false);
			} else {
				$('#txt_rate').attr("readonly", true);
			}

			$("#vs_rate_hide").val(independent_control_arr[company_id]['is_editable']);
		} else {
			$("#txt_rate").attr("disabled", false);
			$("#txt_rate").attr("readonly", false);
			$("#txt_dyeing_charge").attr("disabled", true);
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
			$('#book_currency_td').css("display", "");
		}

		var status = return_global_ajax_value(company_id, 'upto_variable_settings', '', 'requires/yarn_receive_controller').trim();
		$('#store_update_upto').val(status);
	}

	// ==============End Floor Room Rack Shelf Bin upto disable============
	function storeUpdateUptoDisable() {
		var store_update_upto = $('#store_update_upto').val() * 1;
		if (store_update_upto == 5) {
			$('#cbo_bin').prop("disabled", true);
		}
		if (store_update_upto == 4) {
			$('#txt_shelf').prop("disabled", true);
			$('#cbo_bin').prop("disabled", true);
		} else if (store_update_upto == 3) {
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);
			$('#cbo_bin').prop("disabled", true);
		} else if (store_update_upto == 2) {
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);
			$('#cbo_bin').prop("disabled", true);
		} else if (store_update_upto == 1) {
			$('#cbo_floor').prop("disabled", true);
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);
			$('#cbo_bin').prop("disabled", true);
		}
	}
	// ==============End Floor Room Rack Shelf Bin upto disable============

	function change_placeholder(purpose_id) {
		var receive_basis = $("#cbo_receive_basis").val();

		if (purpose_id == "15" || purpose_id == "50" || purpose_id == "51") {
			$('#txt_dyeing_charge').attr("placeholder", "T.ch");
		} else if (purpose_id == "38") {
			$('#txt_dyeing_charge').attr("placeholder", "W.ch");
		} else if (purpose_id == "46") {
			$('#txt_dyeing_charge').attr("placeholder", "Dr.ch");
		} else {
			$('#txt_dyeing_charge').attr("placeholder", "D.ch");
		}

		if ((receive_basis == 1 || receive_basis == 2) && (purpose_id == 2 || purpose_id == 12 || purpose_id == 15 || purpose_id == 38)) {
			$("#txt_grey_qty").attr('disabled', false);
		} else {
			$("#txt_grey_qty").attr('disabled', true);
		}

		//qnt popup decide here
		if (receive_basis == 2 && (purpose_id == 2 || purpose_id == 12 || purpose_id == 15 || purpose_id == 38 || purpose_id == 44 || purpose_id == 46 || purpose_id == 50 || purpose_id == 51)) {

			$('#txt_receive_qty').removeAttr('onBlur').removeAttr('onClick').attr({
				'onClick': 'func_onclick_qty()',
				'readonly': true
			});
		}
	}

	function autoCalculateWeightPerBag() {
		//Wght @ Cone =   (Recv. Qnty)/((No. Of Bag*No. Of Cone per bag)+No. Of Loose Co))
		var txt_receive_qty = $('#txt_receive_qty').val() * 1;
		var txt_no_bag = $('#txt_no_bag').val() * 1;
		var txt_cone_per_bag = $('#txt_cone_per_bag').val() * 1;
		var txt_no_loose_cone = $('#txt_no_loose_cone').val() * 1;

		if (txt_receive_qty > 0 && txt_no_bag > 0 && txt_cone_per_bag > 0) {

			var txt_weight_per_cone = (txt_receive_qty / ((txt_no_bag * txt_cone_per_bag) + txt_no_loose_cone));
		}

		if (txt_weight_per_cone > 0) {
			$('#txt_weight_per_cone').val(txt_weight_per_cone.toFixed(2));
		} else {
			$('#txt_weight_per_cone').val(0);
		}
	}

	//func_onclick_qty
	function func_onclick_qty() {
		//alert('su..re'); return;
		if (form_validation('cbo_color*cbo_yarn_count*cbocomposition1*cbo_yarn_type', 'Color*Count*Composition*Yarn type') == false) {
			return;
		}

		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_receive_purpose = $('#cbo_receive_purpose').val();
		var hdn_ydsw_entry_form = $('#hdn_entry_form').val();
		var txtRcvQty = $('#txt_receive_qty').val();
		var originalRcvQty = $('#hdn_receive_qty').val();
		var work_order_pi = $('#txt_wo_pi').val();
		var work_order_pi_id = $('#txt_wo_pi_id').val();
		var work_order_pi_dtls_id = $('#hdnYarnDyingDtlsId').val();
		var grey_yarn_prod_id = $('#txt_grey_yarn_prod_id').val();
		var yarn_count_id = $('#cbo_yarn_count').val();
		var composition_id = $('#cbocomposition1').val();
		var yarn_type_id = $('#cbo_yarn_type').val();
		var color_id = $('#cbo_color').val();
		var transId = $('#update_id').val();
		var title = 'Yarn Receive Info';

		page_link = 'requires/yarn_receive_controller.php?action=actn_onclick_qty';
		page_link = page_link + '&work_order_pi=' + work_order_pi + '&work_order_pi_id=' + work_order_pi_id + '&work_order_pi_dtls_id=' + work_order_pi_dtls_id + '&grey_yarn_prod_id=' + grey_yarn_prod_id + '&yarn_count_id=' + yarn_count_id + '&composition_id=' + composition_id + '&yarn_type_id=' + yarn_type_id + '&color_id=' + color_id + '&originalRcvQty=' + originalRcvQty + '&txtRcvQty=' + txtRcvQty + '&transId=' + transId + '&cbo_receive_purpose=' + cbo_receive_purpose + '&cbo_company_id=' + cbo_company_id + '&hdn_ydsw_entry_form=' + hdn_ydsw_entry_form;

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=350px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var receiveQty = this.contentDoc.getElementById("txt_receive_qnty").value;
			var hdnReceiveString = this.contentDoc.getElementById("hdnReceiveString").value;

			if (receiveQty != "") {
				freeze_window(5);
				$('#txt_receive_qty').val(receiveQty);
				$('#hdnReceiveString').val(hdnReceiveString);
				fn_calile();
				release_freezing();
			}
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs("../", $permission);  ?><br />
		<form name="yarn_receive_1" id="yarn_receive_1" autocomplete="off">
			<div style="width:75%;">
				<table width="100%" cellpadding="0" cellspacing="2" align="left">
					<tr>
						<td width="100%" align="center" valign="top">
							<fieldset style="width:950px; float:left;">
								<legend>Yarn Receive</legend>
								<br />
								<fieldset style="width:950px;">
									<table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
										<tr>
											<td colspan="6" align="center">&nbsp;<b>MRR Number</b>
												<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
											
											</td>
										</tr>
										<tr>
											<td width="130" class="must_entry_caption">Company Name </td>
											<td width="170">
												<?
												echo create_drop_down("cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/yarn_receive_controller' );load_drop_down( 'requires/yarn_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data(this.value, 'is_allocation_maintained', 'requires/yarn_receive_controller');load_drop_down( 'requires/yarn_receive_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_room_rack_self_bin('requires/yarn_receive_controller*1', 'store','store_td', this.value);get_receive_basis(this.value)");
												?>
												<input type="hidden" id="variable_recv_level" name="variable_recv_level" />
												<input type="hidden" id="vs_rate_hide" name="vs_rate_hide" value="0" />
												<input type="hidden" id="supplier_id_new" name="supplier_id_new"  />
											</td>
											<td width="94" class="must_entry_caption"> Receive Basis </td>
											<td width="160" id="receive_baisis_td">
												<?
												echo create_drop_down("cbo_receive_basis", 170, $receive_basis_arr, "", 1, "- Select Receive Basis -", $selected, "fn_independent(this.value)", "", "1,2,4");
												?>
											</td>
											<td width="130" class="must_entry_caption">Receive Purpose</td>
											<td width="170">
												<?
												echo create_drop_down("cbo_receive_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", 16, "load_supplier(); rate_cond(this.value); change_placeholder(this.value)", "", "2,5,6,7,12,15,16,38,43,44,46,50,51,81");
												?>
											</td>
										</tr>
										<tr>
											<td width="130" class="must_entry_caption">Receive Date </td>
											<td width="170">
												<input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:158px;" placeholder="Select Date" onChange="exchange_rate(document.getElementById('cbo_currency').value)" value="<? echo date('d-m-Y'); ?>" readonly />
											</td>
											<td width="94" class="must_entry_caption"> Challan No </td>
											<td width="160"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:158px"></td>
											<td width="130" class="must_entry_caption">Store Name</td>
											<td width="170" id="store_td">
												<?
												echo create_drop_down("cbo_store_name", 170, $blank_array, "", 1, "-- Select Store --", 0, "");
												?>
											</td>
										</tr>
										<tr>
											<td width="130">WO / PI / FSO</td>
											<td width="170"><input class="text_boxes" type="text" name="txt_wo_pi" id="txt_wo_pi" onDblClick="openmypage('xx','WO/PI/FSO')" placeholder="Double Click" style="width:158px;" readonly disabled />
												<input type="hidden" id="txt_fso_no" name="txt_fso_no" value="" />
												<input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
												<input type="hidden" id="txt_pi_basis" name="txt_pi_basis" value="" />
												<input type="hidden" id="hdn_entry_form" name="hdn_entry_form" value="" />
											</td>
											<td width="94" class="must_entry_caption" id="supplier_td"> Supplier </td>
											<td id="supplier" width="160">
												<?
												echo create_drop_down("cbo_supplier", 170, $blank_array, "", 1, "--- Select Supplier ---", $selected, "", 1);
												?>
											</td>
											<td width="94" id="loanParty_td"> Loan Party </td>
											<td id="loanParty" width="160">
												<?
												echo create_drop_down("cbo_party", 170, $blank_array, "", 1, "--- Select Party ---", $selected, "", 1);
												?>
											</td>
										</tr>

										<tr>
											<td width="60" class="must_entry_caption">MRR. / WO. Currency</td>
											<td width="80" id="currency">
												<?
												echo create_drop_down("cbo_currency", 80, $currency, "", 1, "-- Select Currency --", 0, "exchange_rate(this.value)", 1);
												?>
												&nbsp;
												<?
												echo create_drop_down("cbo_wo_currency", 80, $currency, "", 1, "-- Select Currency --", 0, "exchange_rate(this.value)", 1);
												?>
											</td>

											<td width="130" class="must_entry_caption">Exchange Rate</td>
											<td width="170">
												<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:158px" onBlur="fn_calile()" disabled />
											</td>
											<td width="94" class="must_entry_caption">Source</td>
											<td width="160" id="sources">
												<?
												echo create_drop_down("cbo_source", 170, $source, "", 1, "-- Select --", $selected, "", 1);
												?>
											</td>
										</tr>

										<tr>
											<td width="130"> L/C No </td>
											<td id="lc_no" width="170">
												<input class="text_boxes" type="text" name="txt_lc_no" id="txt_lc_no" style="width:158px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled />
												<input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
											</td>
											<td width="94">Issue Challan No.</td>
											<td width="160" id="sources">
												<input type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" class="text_boxes" style="width:158px" onDblClick="openpage_challan();" placeholder="Double Click To Search" disabled readonly />
												<input type="hidden" id="txt_issue_id" name="txt_issue_id" value="" />
											</td>
											<td>Remarks</td>
											<td><input type="text" id="txt_mst_remarks" name="txt_mst_remarks" class="text_boxes" style="width:160px" /></td>
										</tr>
										<tr>
											<td width="130"> Gate Entry Date </td>
											<td width="170"><input type="text" name="txt_gate_entry_date" id="txt_gate_entry_date" class="datepicker" style="width:158px;" placeholder="Select Date" /></td>
											<td width="130">Challan Date </td>
											<td width="170">
												<input type="text" name="txt_challan_date" id="txt_challan_date" class="datepicker" style="width:158px;" placeholder="Select Date" />
											</td>
											<td width="130" class=""> Gate Entry No </td>
											<td width="170"><input type="text" name="txt_gate_entry_no" id="txt_gate_entry_no" class="text_boxes" style="width:158px"></td>
										</tr>
										<tr>
											<td width="130">BOE/Mushak Challan No</td>
											<td width="170"><input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:158px"></td>
											<td width="130">BOE/Mushak Challan Date</td>
											<td width="170"><input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" placeholder="Select Date" class="datepicker" style="width:158px"></td>
											<td>File</td>
											<td>
												<input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:170px">
												<input type="button" class="image_uploader" style="width:170px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('txt_mrr_no').value,'', 'yarn_receive', 2 ,1)">
											</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
									<tr>
										<td width="49%" valign="top">
											<fieldset style="width:950px;">
												<legend>New Receive Item</legend>
												<table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">
													<tr>
														<td class="must_entry_caption" width="100">Yarn Count</td>
														<td width="150">
															<?
															echo create_drop_down("cbo_yarn_count", 130, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "--Select--", 0, "", 0);
															?>
															<input type="hidden" id="txt_wo_pi_dtls_id" name="txt_wo_pi_dtls_id" value="" />
															<input type="hidden" id="txt_grey_yarn_prod_id" name="txt_grey_yarn_prod_id" value="" />
														</td>
													</tr>
													<tr>
														<td>Composition</td>
														<td id="composition_td">
															<style>
																.display {
																	display: none;
																}
															</style>
															<?
															echo create_drop_down("cbocomposition1", 80, $composition, "", 1, "-- Select --", "", "", $disabled, "");
															echo '<input type="text" id="percentage1" name="percentage1" class="text_boxes_numeric" style="width:40px" placeholder="%" value="100" onBlur="control_composition(\'percent_one\')" disabled/>'; //
															echo create_drop_down("cbocomposition2", 80, $composition, "", 1, "-- Select --", "", "", 1, "", "", "", "", "", "", "", "display");
															echo '<input type="hidden" id="percentage2" name="percentage2" class="text_boxes_numeric" style="width:40px" disabled />';
															// placeholder="%" onBlur="control_composition(\'percent_two\')"
															?>
															<!--script>load_drop_down( 'requires/yarn_receive_controller', '', 'load_drop_down_composition', 'composition_td' );</script-->

														</td>
													</tr>
													<tr>
														<td class="must_entry_caption">Yarn Type</td>
														<td>
															<?
															asort($yarn_type);
															//echo "<pre>";print_r($yarn_type);
															echo create_drop_down("cbo_yarn_type", 130, $yarn_type, "", 1, "--Select--", 0, "", 0);
															?>
														</td>
													</tr>
													<tr>
														<td class="must_entry_caption">Color</td>
														<td id="color_td_id">
															<?
															if ($db_type == 0) $color_cond = " and color_name!=''";
															else $color_cond = " and color_name IS NOT NULL";
															echo create_drop_down("cbo_color", 110, "select id,color_name from lib_color where status_active=1 and grey_color=1 $color_cond order by color_name", "id,color_name", 1, "--Select--", 0, "", 1);
															?>
															<input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus" style="width:20px" onClick="fn_color_new(this.id)" value="N" />
														</td>
													</tr>
													<tr>
														<td class="must_entry_caption"><!--Yarn -->Lot/ Batch</td>
														<td><input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:120px;" /></td>
													</tr>
													<tr>
														<td>Buyer</td>
														<td id="buyer_td_id" width="135">
															<?
															echo create_drop_down("cbo_buyer_name", 133, $blank_array, "", 1, "-- Select Buyer --", 0, "", "");
															?>
														</td>
													</tr>
													<tr>
														<td>Bal. PI/ Ord. Qty</td>
														<td><input class="text_boxes_numeric" name="txt_order_qty" id="txt_order_qty" type="text" style="width:120px;" readonly />
															<input class="text_boxes_numeric" name="txt_woQnty" id="txt_woQnty" type="hidden" style="width:120px;" readonly />
															<input class="text_boxes_numeric" name="txt_overRecPerc" id="txt_overRecPerc" type="hidden" style="width:120px;" readonly />
															<input class="text_boxes_numeric" name="txt_totRecv" id="txt_totRecv" type="hidden" style="width:120px;" readonly />
														</td>
													</tr>
												</table>

												<table width="240" cellspacing="2" cellpadding="0" border="0" style="float:left">
													<tr>
														<td width="88" class="must_entry_caption">Brand</td>
														<td width="146"><input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:120px;" /></td>
													</tr>

													<tr>
														<td class="must_entry_caption">Recv. Qty.</td>
														<td>
															<input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:60px;" onBlur="fn_calile()" readonly="" />
															<input name="hdn_receive_qty" id="hdn_receive_qty" type="hidden" />
															<input name="txt_grey_qty" id="txt_grey_qty" class="text_boxes_numeric" type="text" style="width:45px;" placeholder="Grey Q." />
															<input name="hdn_grey_qty" id="hdn_grey_qty" type="hidden" />
														</td>
													</tr>

													<tr>
														<td>UOM</td>
														<td><? echo create_drop_down("cbo_uom", 130, $unit_of_measurement, "", 0, "--Select--", 12, "", 1, "12"); ?></td>
													</tr>

													<tr id="rate_td">
														<td class="must_entry_caption">Rate</td>
														<td>
															<input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:30px;" onBlur="fn_calile()" value="0" />
															<input name="txt_avg_rate" id="txt_avg_rate" class="text_boxes_numeric" type="text" style="width:30px;" onBlur="calculate_rate()" placeholder="Avg" disabled />
															<input name="txt_dyeing_charge" id="txt_dyeing_charge" class="text_boxes_numeric" type="text" style="width:30px;" placeholder="D.Ch" onBlur="calculate_rate()" />
															<input name="hdn_service_process_loss_percentage" id="hdn_service_process_loss_percentage" type="hidden" readonly />
															<input type="hidden" id="hidden_grey_yarn_avg_rate" name="hidden_grey_yarn_avg_rate" value="" readonly />

														</td>
													</tr>
													<tr>
														<td id="ile_td">ILE%</td>
														<td>
															<input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="ILE COST" readonly />
														</td>
													</tr>
													<tr id="amount_td">
														<td>Amount</td>
														<td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
													</tr>

												</table>

												<table width="244" cellspacing="2" cellpadding="0" border="0" style="float:left">
													<tr id="book_currency_td">
														<td width="111">Book Currency.</td>
														<td width="133">
															<input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:120px;" readonly disabled />
														</td>
													</tr>
													<tr>
														<td>No. Of Bag</td>
														<td><input name="txt_no_bag" id="txt_no_bag" type="text" class="text_boxes_numeric" style="width:120px;" onBlur="autoCalculateWeightPerBag()" /></td>
													</tr>
													<tr>
														<td>No. Of Cone Per Bag</td>
														<td>
															<input name="txt_cone_per_bag" id="txt_cone_per_bag" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="autoCalculateWeightPerBag()" />
														</td>
													</tr>

													<tr>
														<td>No. Of Loose Cone</td>
														<td>
															<input name="txt_no_loose_cone" id="txt_no_loose_cone" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="autoCalculateWeightPerBag()" />
														</td>
													</tr>

													<tr>
														<td>Weight per Bag</td>
														<td>
															<input name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" type="text" style="width:120px;" />
														</td>
													</tr>
													<tr>
														<td>Wght @ Cone</td>
														<td><input class="text_boxes_numeric" name="txt_weight_per_cone" id="txt_weight_per_cone" type="text" style="width:120px;" onBlur="autoCalculateWeightPerBag()" title="(Recv. Qnty)/{(No. Of Bag*No. Of Cone per bag)+No. Of Loose Co)}" /></td>
													</tr>
													<tr>
														<td>Product Code</td>
														<td><input class="text_boxes" name="txt_prod_code" id="txt_prod_code" type="text" style="width:120px;" readonly /></td>
													</tr>

												</table>


												<table width="230" cellspacing="2" cellpadding="0" border="0">

													<tr>
														<td width="118">Floor</td>

														<td id="floor_td" width="106">
															<? echo create_drop_down("cbo_floor", 152, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td>Room</td>

														<td id="room_td">
															<? echo create_drop_down("cbo_room", 152, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td width="118">Rack</td>
														<td id="rack_td" width="106">
															<? echo create_drop_down("txt_rack", 152, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td>Shelf</td>
														<td id="shelf_td">
															<? echo create_drop_down("txt_shelf", 152, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>
													<tr>
														<td>Bin/Box</td>
														<td id="bin_td">
															<? echo create_drop_down("cbo_bin", 152, $blank_array, "", 1, "--Select--", 0, "", 0); ?>
														</td>
													</tr>

													<tr>
														<td>Remarks</td>
														<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;" /></td>
													</tr>
													<tr>
														<td>Over Recv.Qty</td>
														<td> <input class="text_boxes_numeric" name="txt_over_recv_limt" id="txt_over_recv_limt" type="text" style="width:140px;" readonly disabled="disabled" /></td>

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
											<div id="audited" style="float:left; font-size:24px; color:#FF0000;"></div>
											<!-- details table id for update -->
											<input type="hidden" id="txt_prod_id" name="txt_prod_id" value="" />
											<input type="hidden" id="allocation_maintained" name="allocation_maintained" value="" />
											<input type="hidden" id="update_id" name="update_id" value="" />
											<input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
											<input type="hidden" name="job_no" id="job_no" readonly /><!--For Basis Bokking and Yarn Dyeing Purpose-->
											<input type="hidden" name="store_update_upto" id="store_update_upto">
											<input type="hidden" name="hdnReceiveString" id="hdnReceiveString">
											<input type="hidden" name="hdnOldReceiveString" id="hdnOldReceiveString">

											<input type="hidden" name="hdnYarnDyingDtlsId" id="hdnYarnDyingDtlsId">
											<input type="hidden" name="hdnPayMode" id="hdnPayMode" value="0">
											<? echo load_submit_buttons($permission, "fnc_yarn_receive_entry", 0, 0, "fnResetForm()", 1); ?>
											<input type="button" name="print" id="print" value="Print" onClick="fnc_yarn_receive_entry(4)" style="width:80px; display:none;" class="formbutton" />
											<input type="button" name="print2" id="print2" value="Print2" onClick="fnc_yarn_receive_entry(5)" style="width: 80px;display:none;" class="formbutton">
											<input type="button" name="print3" id="print3" value="Print3" onClick="fnc_yarn_receive_entry(6)" style="width:80px; display:none;" class="formbutton" />
											<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
										</td>
									</tr>
								</table>
							</fieldset>
							<fieldset style="width:950px; float:left;">
								<div style="width:950px;" id="list_container_yarn"></div>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
			<div id="list_product_container" style="max-height:500px; width:25%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_color').val($('#cbo_color option:last').val());
</script>

</html>