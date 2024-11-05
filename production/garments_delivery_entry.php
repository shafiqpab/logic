<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Delivery Entry

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	06-09-2014
Updated  		: 	Kaiyum
Update date 	: 	04-10-2016
Purpose			:
QC Performed BY	:
QC Date			:
Comments		: [kaiyum: add 'Forwarding Agent' combo field]
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
$u_id = $_SESSION['logic_erp']['user_id'];
$level = return_field_value("user_level", "user_passwd", "id='$u_id' and valid=1 ", "user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments Delivery Info", "../", 1, 1, $unicode, '', '');

function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}

?>


<script>
	<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][198]);
	if ($data_arr)
		echo "var field_level_data= " . $data_arr . ";\n";
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][198] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	// echo "alert(JSON.stringify(field_level_data));";
	?>
	var tableFilters = {}
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	/* var str_track_no = [<? //echo substr(return_library_autocomplete("select distinct(truck_no) as truck_no from pro_ex_factory_delivery_mst where  entry_form!=85", "truck_no"), 0, -1); ?>];
	var str_driver_name = [<? //echo substr(return_library_autocomplete("select distinct(driver_name) as driver_name from pro_ex_factory_delivery_mst where  entry_form!=85", "driver_name"), 0, -1); ?>];
	var str_dl_no = [<? //echo substr(return_library_autocomplete("select distinct(dl_no) as dl_no from pro_ex_factory_delivery_mst where  entry_form!=85", "dl_no"), 0, -1); ?>];
	var str_mobile_no = [<? //echo substr(return_library_autocomplete("select distinct(mobile_no) as mobile_no from pro_ex_factory_delivery_mst where  entry_form!=85", "mobile_no"), 0, -1); ?>];
	var str_destination_place = [<? //echo substr(return_library_autocomplete("select distinct(destination_place) as destination_place from pro_ex_factory_delivery_mst where entry_form!=85", "destination_place"), 0, -1); ?>]; */

	$(document).ready(function(e) {
		/* $("#txt_truck_no").autocomplete({
			source: str_track_no
		});
		$("#txt_driver_name").autocomplete({
			source: str_driver_name
		});
		$("#txt_dl_no").autocomplete({
			source: str_dl_no
		});
		$("#txt_mobile_no").autocomplete({
			source: str_mobile_no
		});
		$("#txt_destination").autocomplete({
			source: str_destination_place
		}); */

		// setFilterGrid('details_table',-1,tableFilters);
		// alert('ok');
		$("#txt_ex_quantity").keyup(function() {
			// alert('ok');
			var txt_user_lebel = $('#txt_user_lebel').val();
			var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;
			var sewing_production_variable = $('#sewing_production_variable').val() * 1;
			var hidden_preceding_process = $('#hidden_preceding_process').val() * 1;
			var txt_yet_quantity = $('#txt_yet_quantity').val() * 1;
			var txt_cumul_quantity = $('#txt_cumul_quantity').val() * 1;
			var is_update_mood = $('#is_update_mood').val() * 1;
			var placeholderVal = document.getElementById("txt_ex_quantity").getAttribute("placeholder") * 1;
			if (is_update_mood) {
				txt_yet_quantity = txt_yet_quantity + placeholderVal;
			}
			// if (is_update_mood) {txt_yet_quantity = txt_yet_quantity+txt_cumul_quantity;}
			// alert(is_update_mood);
			var delvery_qty = $(this).val();

			if (sewing_production_variable == 1) {
				if (delvery_qty * 1 > txt_yet_quantity * 1) {
					if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
						alert("Qnty Excceded by " + txt_yet_quantity);
						$(this).val('');
					} else {
						if (confirm("Qnty Excceded by " + txt_yet_quantity))
							void(0);
						else {
							$(this).val('');
						}
					}

				}
			}
		});
	});

	function location_select() {
		if ($('#cbo_location_name option').length == 2) {
			if ($('#cbo_location_name option:first').val() == 0) {
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange'));
			}
		} else if ($('#cbo_location_name option').length == 1) {
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
	}

	function openmypage_lcsc() {
		var company = $("#cbo_company_name").val();
		var order_id = $("#hidden_po_break_down_id").val();
		if (form_validation('txt_order_no', 'Order Number') == false) {
			return;
		}

		//'requires/garments_delivery_entry_controller.php?action=lcsc_popup&company='+document.getElementById('cbo_company_name').value+&order_id='+document.getElementById('hidden_po_break_down_id').value,'Order Search'
		var page_link = "requires/garments_delivery_entry_controller.php?action=lcsc_popup&company=" + company + "&order_id=" + order_id;
		var title = "Order Search";
		//page_link=page_link+''
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=380px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			//hidden_invoice_no hidden_lcsc_no
			var theform = this.contentDoc.forms[0];
			var invoice_lcsc_no = (this.contentDoc.getElementById("lc_id_no").value).split("**");
			var invoice_id = invoice_lcsc_no[0];
			var lcsc_id = invoice_lcsc_no[2];
			$("#txt_invoice_no").val(invoice_lcsc_no[1]);
			$("#txt_lc_sc_no").val(invoice_lcsc_no[3]);
			$("#txt_invoice_no").attr('placeholder', invoice_lcsc_no[0]);
			$("#txt_lc_sc_no").attr('placeholder', invoice_lcsc_no[2]);
		}
	}

	function AdditionalInfoFnc(data) {
		//previous business was visible or hidden but now   if and else both contains visibility visible
		if (data == 7)
			$("#additional_info_id").css("visibility", "visible");
		else
			$("#additional_info_id").css("visibility", "visible");
	}

	function openmypage(page_link, title) {
		var company = $("#cbo_company_name").val();
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var po_id = this.contentDoc.getElementById("hidden_mst_id").value; //po id
			var item_id = this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty = this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id = this.contentDoc.getElementById("hidden_country_id").value;
			var country_id = this.contentDoc.getElementById("hidden_country_id").value;
			var ship_date = this.contentDoc.getElementById("hidden_ship_date").value;
			var pack_type = this.contentDoc.getElementById("hidden_pack_type").value;

			if (po_id != "") {
				freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#txt_pack_type").val(pack_type);
				$("#txt_country_ship_date").val(ship_date);
				$("#txt_actual_po").val('');
				$("#hidden_actual_po").val('');
				var company_id = $("#cbo_company_name").val();
				childFormReset(); //child from reset
				get_php_form_data(po_id + '**' + item_id + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#txt_mst_id').val() + '**2**' + $('#sewing_production_variable').val() + '**' + $('#variable_is_controll').val()+ '**' + ship_date+ '**' + pack_type, "populate_data_from_search_popup", "requires/garments_delivery_entry_controller");

				var variableSettings = $('#sewing_production_variable').val();
				var styleOrOrderWisw = $('#styleOrOrderWisw').val();
				if (variableSettings != 1) {
					get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#variable_is_controll').val()+ '**' + ship_date+ '**' + pack_type+ '**' + $('#garments_nature').val(), "color_and_size_level", "requires/garments_delivery_entry_controller");
				} else {
					$("#txt_ex_quantity").removeAttr("readonly");
				}
				change_shipping_status(0);
				show_list_view(po_id+'**'+company_id, 'show_country_listview', 'list_view_country', 'requires/garments_delivery_entry_controller', '');
				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 0);
				release_freezing();
			}
		}
	}

	function actual_po_popup()
	{
		let order_no = $("#txt_order_no").val();
		let po_id = $("#hidden_po_break_down_id").val();
		let item_id = $("#cbo_item_name").val();
		let act_po_id = $("#hidden_actual_po").val();
		let sewing_production_variable = $("#sewing_production_variable").val();
		let company_name = $("#cbo_company_name").val();
		if(sewing_production_variable=='4')
		{
			var page_link = "requires/garments_delivery_entry_controller.php?action=actual_po_action&po_id=" + po_id + "&item_id=" + item_id+ "&act_po_id=" + act_po_id;

			var title = "Actual Po No";
			if (form_validation('txt_order_no', 'Order Number') == false)
			{
				return;
			}
			else
			{
				emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=430px,center=1,resize=0,scrolling=0', '')
				emailwindow.onclose = function()
				{
					var theform = this.contentDoc.forms[0];
					var data_string = this.contentDoc.getElementById("data_string").value; //po id
					var data_arr = data_string.split("==");
					var actual_po_no = new Array();
					var po_qty = 0;
					for (let i = 0; i < data_arr.length; i++)
					{
						var dtls_data_arr =  data_arr[i].split("**");
						actual_po_no.push(dtls_data_arr[1]);
						po_qty += dtls_data_arr[8]*1;
					}

					if (data_string != "")
					{
						$("#txt_actual_po").val([...new Set(actual_po_no)]);
						$("#hidden_actual_po").val(data_string);
						$("#txt_ex_quantity").val(po_qty);
					}
					else
					{
						$("#txt_actual_po").val('');
						$("#hidden_actual_po").val('');
						$("#txt_ex_quantity").val('');
					}
				}

			}
		}
		else
		{
			var page_link = "requires/garments_delivery_entry_controller.php?action=actual_po_action_popup&po_id=" + po_id + "&item_id=" + item_id+ "&act_po_id=" + act_po_id+ "&company_name=" + company_name;
			var title = "Actual Po No";
			if (form_validation('txt_order_no', 'Order Number') == false)
			{
				return;
			}
			else
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var actual_po_no=this.contentDoc.getElementById("hidden_actual_po_no_return").value;//po id
					var actual_po_id=this.contentDoc.getElementById("hidden_actual_po_id_return").value;//po id
					if (actual_po_id!="")
					{
						$("#txt_actual_po").val(actual_po_no);
						$("#hidden_actual_po").val(actual_po_id);

					}
					else
					{
						$("#txt_actual_po").val('');
						$("#hidden_actual_po").val('');
					}
				}

			}
		}
	}

	function add_info_popup() {
		var cbo_shipping_mode = $("#cbo_shipping_mode").val();
		var txt_add_info = $("#txt_add_info").val();
		var hidden_add_info = $("#hidden_add_info").val();

		var page_link = "requires/garments_delivery_entry_controller.php?action=add_info_action&txt_add_info=" + txt_add_info + "&hidden_add_info=" + hidden_add_info + "&cbo_shipping_mode=" + cbo_shipping_mode;
		var title = "Additional Info";

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=200px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var txt_add_info = this.contentDoc.getElementById("all_field_data_value").value; //po id
			var add_info_no = this.contentDoc.getElementById("all_field_data").value; //po id

			if (txt_add_info != "") {
				$("#txt_add_info").val(txt_add_info);
				$("#hidden_add_info").val(add_info_no);

			} else {
				$("#txt_add_info").val('');
				$("#hidden_add_info").val('');
			}
		}
	}

	function fnc_print_report2(id_ref) {
		if (form_validation('txt_system_id', 'System Number') == false) {
			alert("Please save the delivery first");
			return;
		} else {
            if(id_ref == 2) {
                var report_title = $("div.form_caption").html();
                print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + $('#cbo_del_company').val(), "ex_factory_print_new", "requires/garments_delivery_entry_controller")
                return;
            }else if(id_ref == 4) {
                var report_title = $("div.form_caption").html();
                print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + $('#cbo_del_company').val(), "ex_factory_print_new_11", "requires/garments_delivery_entry_controller")
                return;
            }
		}
	}

	function fnc_print_report3(id_ref) {
		if (form_validation('txt_system_id', 'System Number') == false) {
			alert("Please save the delivery first");
			return;
		} else {
			var report_title = $("div.form_caption").html();
			if (id_ref == 5) {
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref, "ExFactoryPrintSonia", "requires/garments_delivery_entry_controller")

			} else if (id_ref == 6) {
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref, "ex_factory_print2", "requires/garments_delivery_entry_controller")

			} else if (id_ref == 7) {
				var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
				// alert(answer);
				var show_delv_info = (answer == true) ? 1 : 0;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + show_delv_info, "ex_factory_print_new3", "requires/garments_delivery_entry_controller");
			} else if (id_ref == 8) {
				// generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_ex_factory_date').val()+'*'+report_title+'*'+id_ref+'*'+show_delv_info,'ex_factory_print_new7','requires/garments_delivery_entry_controller');
				// var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
				// // alert(answer);
				// var show_delv_info = (answer==true) ? 1 : 0;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + show_delv_info, "ex_factory_print_new7", "requires/garments_delivery_entry_controller");
			} else if (id_ref == 9) {
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref, "ExFactoryPrint8", "requires/garments_delivery_entry_controller")

			}else if (id_ref == 12) {
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref, "ExFactoryPrint11", "requires/garments_delivery_entry_controller")

			} else if (id_ref == 10) {
				var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
				// alert(answer);
				var show_delv_info = (answer == true) ? 1 : 0;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + show_delv_info, "ex_factory_print_new9", "requires/garments_delivery_entry_controller");
			} else if (id_ref == 11) {
				var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
				// alert(answer);
				var show_delv_info = (answer == true) ? 1 : 0;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + show_delv_info, "ex_factory_print_new10", "requires/garments_delivery_entry_controller");
			} else {
				var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
				// alert(answer);
				var show_delv_info = (answer == true) ? 1 : 0;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref + '*' + show_delv_info, "ex_factory_print_new2", "requires/garments_delivery_entry_controller")
			}

			return;
		}
	}

	function generate_report_file(data, action, page) {
		window.open("requires/garments_delivery_entry_controller.php?data=" + data + '&action=' + action, true);
	}

	function fnc_print_report4() {
		if (form_validation('txt_order_no', 'Order No.') == false) {
			return;
		} else {
			var data = $('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_mst_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + $('#hidden_po_break_down_id').val();
			var title = "Deleted Color Size Popup";
			var page_link = 'requires/garments_delivery_entry_controller.php?action=deleted_col_size&data=' + data;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=300px,center=1,resize=0,scrolling=0', '')
			emailwindow.onclose = function() {

			}
			return;
		}
	}



	function fnc_exFactory_entry(operation)
	{
		if (operation == 4) {
			if (form_validation('txt_system_id', 'System Number') == false) {
				alert("Please save the delivery first");
				return;
			} else {
				var report_title = $("div.form_caption").html();
				var id_ref = 1;
				print_report($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#txt_ex_factory_date').val() + '*' + report_title + '*' + id_ref, "ex_factory_print", "requires/garments_delivery_entry_controller");
				return;
			}
		} else if (operation == 0 || operation == 1 || operation == 2) {

			var isFileMandatory = "";
			<?php

				if(!empty($_SESSION['logic_erp']['mandatory_field'][198][6])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][198][6] . ";\n";
			?>
			if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#txt_system_id').val()==''){

				document.getElementById("multiple_file_field").focus();
				var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
				document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
				alert("Please Add File in Master Part");
				return;
			}
			if (operation == 1) {
				var txt = $("#posted_msg_td_id").text();

				if (txt == "Already Posted In Accounting.") {
					release_freezing();
					alert("Already Posted In Accounting. Save Update Delete Restricted.");
					return;
				}
			}
			if (operation == 2) {
				if ($("#check_posted_in_accounce").val() == 1) {
					release_freezing();
					alert("This Challan Is Already Posted In Accounting. Delete Restricted.");
					return;
				}
			}

			if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][198],array(6))); ?>') {
				if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][198],array(6))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][198],array(6))); ?>') == false) {

					return;
				}
			}




			if (form_validation('cbo_company_name*cbo_del_company*txt_order_no*txt_ex_quantity*txt_ex_factory_date*cbo_source', 'Company Name*Order No*ex-factory Quantity*Date*Source') == false) {
				return;
			} else {
				var current_date = '<? echo date("d-m-Y"); ?>';
				if (date_compare($('#txt_ex_factory_date').val(), current_date) == false) {
					alert("Ex Factory Date Can not Be Greater Than Current Date");
					return;
				}

				if ($("#txt_invoice_no").val() != '')
					var invoice_id = $("#txt_invoice_no").attr('placeholder');
				else
					var invoice_id = '';

				if ($("#txt_lc_sc_no").val() != '')
					var lcsc_id = $("#txt_lc_sc_no").attr('placeholder');
				else
					var lcsc_id = '';


				var sewing_production_variable = $("#sewing_production_variable").val();
				if (sewing_production_variable == "" || sewing_production_variable == 0) {
					sewing_production_variable = 3;
				}
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i = 0;
				var colorIDvalue = '';
				if (sewing_production_variable == 2) //color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if ($(this).val() != '') {
							if (i == 0) {
								colorIDvalue = colorList[i] + "*" + $(this).val();
							} else {
								colorIDvalue += "**" + colorList[i] + "*" + $(this).val();
							}
						}
						i++;
					});
				} else if (sewing_production_variable == 3) //color and size level
				{
					$("input[name=colorSize]").each(function(index, element) {
						if ($(this).val() != '') {
							color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
							if (i == 0) {
								// colorIDvalue = colorList[i] + "*" + $(this).val();
								colorIDvalue = color_size_breakdown_id + "*" + $(this).val();
							} else {
								colorIDvalue += "***" + color_size_breakdown_id + "*" + $(this).val();
								// colorIDvalue += "***" + colorList[i] + "*" + $(this).val();
							}
						}
						i++;
					});
				}

				var data = "action=save_update_delete&operation=" + operation + '&invoice_id=' + invoice_id + '&lcsc_id=' + lcsc_id + "&colorIDvalue=" + colorIDvalue + get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*cbo_location_name*cbo_item_name*hidden_po_break_down_id*hidden_colorSizeID*txt_ex_factory_date*txt_ex_quantity*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*txt_remark*shipping_status*cbo_inco_term_id*hidden_actual_po*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_mst_id*txt_system_no*txt_system_id*cbo_transport_company*txt_dl_no*txt_lock_no*txt_truck_no*txt_driver_name*cbo_buyer_name*txt_destination*cbo_forwarder*cbo_forwarder_2*txt_mobile_no*txt_do_no*txt_gp_no*cbo_ins_qty_validation_type*cbo_del_company*cbo_source*cbo_delivery_location*cbo_foc_claim*cbo_shipping_mode*txt_add_info*hidden_add_info*cbo_delivery_floor*txt_attention*txt_remarks*txt_order_no*txt_escot_name*txt_depo_details*txt_escot_mobile*txt_country_ship_date*txt_pack_type*cbm*net_weight*gross_weight*txt_detail_destination', "../");

				//alert(data);
				freeze_window(operation);
				http.open("POST", "requires/garments_delivery_entry_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_exFactory_entry_Reply_info;
			}
		}
	}

	function fnc_exFactory_entry_Reply_info() {
		if (http.readyState == 4) {
			//release_freezing();
			//alert(http.responseText);return;
			var variableSettings = $('#sewing_production_variable').val();
			var styleOrOrderWisw = $('#styleOrOrderWisw').val();
			var item_id = $('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();

			var reponse = http.responseText.split('**');
			if (reponse[0] == 50) {
				release_freezing();
				alert("Buyer Mixed Not Allow");
				return;
			}
			if (reponse[0] == 37) {
				release_freezing();
				alert(reponse[1]);
				return;
			}
			if (reponse[0] == 38) {
				release_freezing();
				alert(reponse[1]);
				return;
			}

			if (reponse[0] == 15) {
				setTimeout('fnc_exFactory_entry(' + reponse[1] + ')', 8000);
			} else if (reponse[0] == 0) {
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				var check_system_id=$("#txt_system_id").val();
				$("#txt_system_id").val(trim(reponse[2]));
				$("#txt_system_no").val(trim(reponse[3]));
				$("#txt_challan_no").val(trim(reponse[4]));
				if (check_system_id=="") uploadFile( $("#txt_system_id").val());
				$("#txt_mst_id").val('0');
				$("#cbo_company_name").attr('disabled', 'true');

				show_list_view(reponse[2], 'show_dtls_listview_mst', 'ex_factory_list_view', 'requires/garments_delivery_entry_controller', 'setFilterGrid(\'details_table\',-1)');
				// setFilterGrid("details_table",-1);
				//setFilterGrid("details_table",-1);
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/garments_delivery_entry_controller','');		  //breakdown_td_id
				reset_form('', '', 'txt_order_no*txt_ex_quantity*hidden_actual_po*txt_add_info*hidden_add_info*txt_actual_po*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*cbo_inco_term_id*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*txt_order_no', '', '');
				if (variableSettings != 1) {
					get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#variable_is_controll').val()+ '**' + $('#txt_country_ship_date').val()+ '**' + $('#txt_pack_type').val()+ '**' + $('#garments_nature').val(), "color_and_size_level", "requires/garments_delivery_entry_controller");
				} else {
					$("#txt_ex_quantity").removeAttr("readonly");
				}

				$('#txt_invoice_no').attr('placeholder', 'Double Click To Search'); //placeholder value initilize
				$('#txt_lc_sc_no').attr('placeholder', ''); //placeholder value initilize
				$('#txt_ex_quantity').attr('placeholder', '');
				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 1);
				disable_enable_fields("cbo_location_name*cbo_source*cbo_del_company", 1);

				//$("#additional_info_id").css("visibility","hidden");
				$("#cbo_foc_claim").val(1);
				release_freezing();
			} else if (reponse[0] == 1) {
				$("#txt_mst_id").val('0');
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				$("#cbo_company_name").attr('disabled', 'true');
				show_list_view(reponse[2], 'show_dtls_listview_mst', 'ex_factory_list_view', 'requires/garments_delivery_entry_controller', 'setFilterGrid(\'details_table\',-1)');
				// setFilterGrid("details_table",-1);breakdown_td_id
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/garments_delivery_entry_controller','');
				reset_form('', '', 'txt_mst_id*txt_ex_quantity*hidden_actual_po*cbo_shipping_mode*txt_add_info*hidden_add_info*txt_actual_po*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*cbo_country_name*txt_order_no', '', '');
				if (variableSettings != 1) {
					get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#variable_is_controll').val()+ '**' + $('#txt_country_ship_date').val()+ '**' + $('#txt_pack_type').val()+ '**' + $('#garments_nature').val(), "color_and_size_level", "requires/garments_delivery_entry_controller");
				} else {
					$("#txt_ex_quantity").removeAttr("readonly");
				}
				$('#txt_lc_sc_no').attr('placeholder', ''); //placeholder value initilize
				$('#txt_ex_quantity').attr('placeholder', '');
				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 1);
				disable_enable_fields("cbo_location_name*cbo_source*cbo_del_company", 1);
				//$("#additional_info_id").css("visibility","hidden");
				$("#cbo_foc_claim").val(1);
				release_freezing();
			} else if (reponse[0] == 2) {
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(reponse[2], 'show_dtls_listview_mst', 'ex_factory_list_view', 'requires/garments_delivery_entry_controller', 'setFilterGrid(\'details_table\',-1)');
				// setFilterGrid("details_table",-1);
				//reset_form('','breakdown_td_id','txt_ex_factory_date*txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark*txt_order_no','','');

				reset_form('', 'breakdown_td_id', 'txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark*txt_order_no', '', '');


				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 1);
				release_freezing();
				disable_enable_fields("cbo_location_name*cbo_source*cbo_del_company", 0);
				$("#cbo_foc_claim").val(1);
			} else if (reponse[0] == 36) {
				alert('Gate Pass Found(' + reponse[5] + ').Update Restricted!');
				$("#txt_mst_id").val('0');
				var po_id = reponse[1];
				// show_msg(trim(reponse[0]));
				$("#cbo_company_name").attr('disabled', 'true');
				show_list_view(reponse[2], 'show_dtls_listview_mst', 'ex_factory_list_view', 'requires/garments_delivery_entry_controller', 'setFilterGrid(\'details_table\',-1)');
				// setFilterGrid("details_table",-1);breakdown_td_id
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/garments_delivery_entry_controller','');
				reset_form('', '', 'txt_mst_id*txt_ex_quantity*hidden_actual_po*cbo_shipping_mode*txt_add_info*hidden_add_info*txt_actual_po*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*cbo_country_name*txt_order_no', '', '');
				if (variableSettings != 1) {
					get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#variable_is_controll').val()+ '**' + $('#txt_country_ship_date').val()+ '**' + $('#txt_pack_type').val()+ '**' + $('#garments_nature').val(), "color_and_size_level", "requires/garments_delivery_entry_controller");
				} else {
					$("#txt_ex_quantity").removeAttr("readonly");
				}
				$('#txt_lc_sc_no').attr('placeholder', ''); //placeholder value initilize
				$('#txt_ex_quantity').attr('placeholder', '');
				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 1);
				disable_enable_fields("cbo_location_name*cbo_source*cbo_del_company", 1);
				//$("#additional_info_id").css("visibility","hidden");
				$("#cbo_foc_claim").val(1);
				release_freezing();
			} else if (reponse[0] == 25) {
				$("#txt_ex_quantity").val("");
				show_msg('30');
				release_freezing();
			} else if (reponse[0] == 35) {
				$("#txt_ex_quantity").val("");
				show_msg('30');
				alert(reponse[1]);
				release_freezing();
				return;
			} else {
				show_msg(trim(reponse[0]));
				release_freezing();
			}
		}
	}

	function childFormReset() {
		reset_form('', '', 'txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*txt_remark*shipping_status*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity', '', '');
		$('#txt_ex_quantity').attr('placeholder', ''); //placeholder value initilize
		$('#txt_finish_quantity').attr('placeholder', ''); //placeholder value initilize
		$('#txt_cumul_quantity').attr('placeholder', ''); //placeholder value initilize
		$('#txt_yet_quantity').attr('placeholder', ''); //placeholder value initilize ex_factory_list_view
		//$("#ex_factory_list_view").html('');
		$("#breakdown_td_id").html('');
		//disable_enable_fields( "cbo_location_name*cbo_transport_company*txt_ex_factory_date*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder", 0 );
		disable_enable_fields("cbo_location_name*cbo_transport_company*cbo_company_name*txt_challan_no*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder", 0);



	}

	function fn_qnty_per_ctn() {
		var exQnty = $('#txt_ex_quantity').val();
		var ctnQnty = $('#txt_total_carton_qnty').val();

		if (exQnty != "" && ctnQnty != "") {
			var ctn_per_qnty = parseInt(Number(exQnty / ctnQnty));
			$('#txt_ctn_qnty').val(ctn_per_qnty);
		}
	}

	function fn_total(tableName, index) // for color and size level
	{
		var filed_value = $("#colSize_" + tableName + index).val();
		var placeholder_value = $("#colSize_" + tableName + index).attr('placeholder');
		var txt_user_lebel = $('#txt_user_lebel').val();
		var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;
		if (filed_value * 1 > placeholder_value * 1) {
			if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
				alert("Qnty Excceded by" + (placeholder_value - filed_value));
				$("#colSize_" + tableName + index).val('');
				$("#txt_ex_quantity").val('');
			} else {
				if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
					void(0);
				else {
					$("#colSize_" + tableName + index).val('');
				}
			}

		}

		var totalRow = $("#table_" + tableName + " tr").length;
		//alert(tableName);
		math_operation("total_" + tableName, "colSize_" + tableName, "+", totalRow);
		if ($("#total_" + tableName).val() * 1 != 0) {
			$("#total_" + tableName).html($("#total_" + tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_ex_quantity").val(totalVal);
		var countryQty = $("#hidden_countryqty").val() * 1;
		var exFacCountryQty = $("#hidden_ex_fac_countryqty").val() * 1;
		//alert(countryQty+" cnty "+exFacCountryQty+" ex _fac "+totalVal+" running");
		if (countryQty <= (totalVal + exFacCountryQty) && countryQty > 0) {
			$("#shipping_status").val(3);
		} else {
			$("#shipping_status").val(2);
		}

		// ================== set delivery amount ======================
		let rate = $('#txt_commission_amt').val();
		let amount = rate*totalVal*1;

		$('#txt_order_amt').val(amount);
	}

	/*$(document).ready(function()
	{
		$("#txt_ex_quantity").keypress(function()
		{
			exQty = $(this).val();
			orderQty = $("#txt_order_qty").val();
			alert(exQty);
		});
	});*/

	function check_gross_shipping_status(val) {
		var placeholderVal = document.getElementById("txt_ex_quantity").getAttribute("placeholder") * 1;
		// alert(placeholderVal);
		var orderQty = $("#txt_order_qty").val() * 1;
		var txt_cumul_quantity = $('#txt_cumul_quantity').val() * 1;
		var exFqty = $("#txt_ex_quantity").val() * 1;
		var is_update_mood = $('#is_update_mood').val() * 1;
		var exQty = 0;
		// if (is_update_mood) {exQty = txt_cumul_quantity;}else{exQty = exFqty+txt_cumul_quantity;}
		if (is_update_mood) {
			exQty = (exFqty + txt_cumul_quantity) - placeholderVal;
		} else {
			exQty = exFqty + txt_cumul_quantity;
		}
		if (orderQty <= exQty * 1) {
			$("#shipping_status").val(3);
		} else {
			// alert(exFqty);
			$("#shipping_status").val(2);
		}
	}

	function change_shipping_status(totalVal) {

		var countryQty = $("#hidden_countryqty").val() * 1;
		var exFacCountryQty = $("#hidden_ex_fac_countryqty").val() * 1;
		//alert(countryQty+"= cnty, "+exFacCountryQty+"= ex _fac, "+totalVal+"= running");
		if (countryQty <= (totalVal * 1 + exFacCountryQty) && countryQty > 0) {
			$("#shipping_status").val(3);
		} else {
			$("#shipping_status").val(2);
		}
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_" + index).val();
		var placeholder_value = $("#colSize_" + index).attr('placeholder');
		var txt_user_lebel = $('#txt_user_lebel').val();
		var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;

		if (filed_value * 1 > placeholder_value * 1) {
			if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
				alert("Qnty Excceded by" + (placeholder_value - filed_value));
				$("#colSize_" + index).val('');
				$("#txt_ex_quantity").val('');
			} else {
				if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
				{

					if (txt_user_lebel == 1) //General User==1;
					{
						$("#colSize_" + index).val('');
						$("#txt_ex_quantity").val('');
					}
					else
					{
						void(0);
					}

				}
				else {
					$("#colSize_" + index).val('');
				}
			}

		}

		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation("total_color", "colSize_", "+", totalRow);
		$("#txt_ex_quantity").val($("#total_color").val());

		var countryQty = $("#hidden_countryqty").val() * 1;
		var exFacCountryQty = $("#hidden_ex_fac_countryqty").val() * 1;
		// alert(countryQty+" cnty "+exFacCountryQty+" ex _fac "+$("#total_color").val()+" running");
		// alert(countryQty +'<='+ ($("#total_color").val()*1 + exFacCountryQty) +'&&'+ countryQty +'>'+ 0);
		if (countryQty <= ($("#total_color").val()*1 + exFacCountryQty) && countryQty > 0)
		{
			$("#shipping_status").val(3);
		}
		else
		{
			$("#shipping_status").val(2);
		}
	}

	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty,ship_date,pack_type)
	{
		freeze_window(5);

		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);

		childFormReset(); //child from reset
		get_php_form_data(po_id + '**' + item_id + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#txt_mst_id').val() + '**2**' + $('#sewing_production_variable').val() + '**' + $('#variable_is_controll').val()+ '**' + ship_date+ '**' + pack_type, "populate_data_from_search_popup", "requires/garments_delivery_entry_controller");

		var variableSettings = $('#sewing_production_variable').val();
		var styleOrOrderWisw = $('#styleOrOrderWisw').val();
		if (variableSettings != 1) {
			get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + country_id + '**' + $('#hidden_preceding_process').val() + '**' + $('#variable_is_controll').val()+ '**' + ship_date+ '**' + pack_type+ '**' + $('#garments_nature').val(), "color_and_size_level", "requires/garments_delivery_entry_controller");
		} else {
			$("#txt_ex_quantity").removeAttr("readonly");
		}

		set_button_status(0, permission, 'fnc_exFactory_entry', 1, 0);
		release_freezing();
	}

	function delivery_sys_popup() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_name").val();
		var company_id = $("#cbo_company_name").val(); 
		var title = "Delivery System Popup";
		/*var page_link='requires/garments_delivery_entry_controller.php?action=sys_surch_popup&company='+company;*/
		var page_link = 'requires/garments_delivery_entry_controller.php?action=sys_surch_popup&company_id=' + company_id;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var delivery_id = this.contentDoc.getElementById("hidden_delivery_id").value;
			//alert(delivery_id);return;
			if (delivery_id != "") {
				//$("#txt_order_qty").val(po_qnty);
				//$("#cbo_item_name").val(item_id);
				//$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").attr('disabled', 'true');
				get_php_form_data(delivery_id, "populate_muster_from_date", "requires/garments_delivery_entry_controller");
				show_list_view(delivery_id, 'show_dtls_listview_mst', 'ex_factory_list_view', 'requires/garments_delivery_entry_controller', 'setFilterGrid(\'details_table\',-1)');
				get_php_form_data($("#cbo_company_name").val(), 'company_wise_report_button_setting', 'requires/garments_delivery_entry_controller');

				// setFilterGrid("details_table",-1);
				if ($("#check_posted_in_accounce").val() == 1) {
					disable_enable_fields("cbo_location_name*cbo_transport_company*txt_ex_factory_date*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder", 1);
				} else {
					//disable_enable_fields( "cbo_location_name*cbo_transport_company*txt_ex_factory_date*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder",0);
					disable_enable_fields("cbo_location_name*cbo_transport_company*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder", 0);
				}
				reset_form('', 'breakdown_td_id*list_view_country"', 'txt_order_no*txt_ex_quantity*hidden_actual_po*cbo_shipping_mode*txt_add_info*hidden_add_info*txt_actual_po*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*cbo_inco_term_id*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*cbo_country_name*cbo_buyer_name*txt_order_no', '', '');

				// setFilterGrid("details_table",-1);
				set_button_status(0, permission, 'fnc_exFactory_entry', 1, 1);

			}

		}

	}

	function file_uploader_popup(type) {
		var txt_system_no = $("#txt_system_no").val();
		if (form_validation('txt_system_no', 'Challan No') == false) {
			return;
		} else {
			//alert(20);
			if (type == 2) {
				file_uploader('../../', document.getElementById('system_id').value, '', 'gmts_delivery_entry', 2, 1);
			}
		}
	}

	function forwarding_agent_disable_1(id) {
		if (id > 0) {
			$("#cbo_forwarder_2").val('');
		}
	}

	function forwarding_agent_disable_2(id) {
		if (id > 0) {
			$("#cbo_forwarder").val('');
		}
	}

	function active_placeholder_qty(color_id) 
	{
		$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
			if ($('#set_all_' + color_id).prop('checked') == true) {
				if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
					$(this).val($(this).attr('placeholder'));
				}
			} else {
				$(this).val('');
			}
		});

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_ex_quantity").val(totalVal);

		// ================== set delivery amount ======================
		let rate = $('#txt_commission_amt').val();
		let amount = rate*totalVal*1;

		$('#txt_order_amt').val(amount);
	}

	function uploadFile(mst_id)
	{
		$(document).ready(function() {

			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i];
				fd.append('file', files);
				$.ajax({
					url: 'requires/garments_delivery_entry_controller.php?action=file_upload&mst_id='+ mst_id,
					type: 'post',
					data:fd,
					contentType: false,
					processData: false,
					success: function(response){
						var res=response.split('**');
						if(res[0] == 0){

							suc++;
						}
						else if(fail==0)
						{
							alert('file not uploaded');
							fail++;
						}
					},
				});
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
		});
	}

	function load_del_company(source_id)
	{
		if($('#cbo_del_company option').length==2)
		{
			if($('#cbo_del_company option:first').val()==0)
			{
				var cbo_del_company_id=$('#cbo_del_company option:last').val();
				$('#cbo_del_company').val(cbo_del_company_id);
				if(source_id==1)
				{
					load_drop_down( 'requires/garments_delivery_entry_controller',cbo_del_company_id, 'load_drop_down_del_location', 'del_location_td' );
				}
			}
		}
	}
	function refres_function()
	{
		$("#cbo_source").val(0);
		$("#cbo_del_company").val(0);

		$("#cbo_source").removeAttr('disabled', '');
		$("#cbo_del_company").removeAttr('disabled', '');
	}

	function show_cost_details()
	{
		var system_id=$("#txt_system_id").val();
		if(system_id=="")
		{
			alert('Challan No Required!');
			return;
		}

		var page_link='requires/garments_delivery_entry_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}

	function fnc_print_report13(id_ref)
	{
		let acc_po = $('#hidden_actual_po').val();
		if ( form_validation('txt_system_id','System Number')==false )
		{
			alert("Please save the delivery first"); return;
		}
		else if(!acc_po)
		{
			alert("Actual Po not found! or Please switch to update mode."); return;
		}		
		else
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_ex_factory_date').val()+'*'+report_title+'*'+id_ref+'*'+$('#cbo_del_company').val(), "ex_factory_print_urmi_new13", "requires/garments_delivery_entry_controller" ) 
			return;
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs("../", $permission);  ?>
		<div style="width:1010px; float:left" align="center">
			<form name="exFactory_1" id="exFactory_1" autocomplete="off">
				<fieldset style="width:1010px;">
					<legend>Production Module</legend>
					<fieldset>
						<table width="1000px" border="0">
							<tr>
								<td align="right" colspan="4">Challan No</td>
								<td colspan="4">
									<input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:160px" onDblClick="delivery_sys_popup()" placeholder="Browse or Search" />
									<input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" style="width:160px" />
								</td>
							</tr>
							<tr>
								<td width="110" class="must_entry_caption">Company</td>
								<td width="140">
									<?
									echo create_drop_down("cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down_multiple( 'requires/garments_delivery_entry_controller', this.value, 'load_drop_down_multiple', 'location_td*transfer_com*forwarder_td*forwarder_td2' ); get_php_form_data(this.value,'load_variable_settings','requires/garments_delivery_entry_controller');", 0); ?>
									<input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
									<input type="hidden" name="check_posted_in_accounce" id="check_posted_in_accounce" value="" />

									<input type="hidden" id="styleOrOrderWisw" />
									<input type="hidden" id="variable_is_controll" />
									<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
									<input type="hidden" id="txt_qty_source" />
									<input type="hidden" id="is_update_mood" />
                            		<input type="hidden" id="wip_valuation_for_accounts" />
                                    <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
									<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
								</td>
								<td width="110" class="must_entry_caption">Location</td>
								<td width="140" id="location_td"><?=create_drop_down("cbo_location_name", 130, $blank_array, "", 1, "--Select Location--", $selected, ""); ?></td>
								<td width="110"> Challan No</td>
								<td width="140"><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:120px" maxlength="50" readonly disabled /></td>
                                <td width="110" class="must_entry_caption">Ex- Factory Date</td>
								<td>
									<input name="txt_ex_factory_date" id="txt_ex_factory_date" class="datepicker" style="width:120px;" value="<? echo date('d-m-Y', time()); ?>" disabled readonly>
								</td>
							</tr>
							<tr>
								<td>Transport. Company </td>
								<td id="transfer_com"><?=create_drop_down("cbo_transport_company", 130, $blank_array, "", 1, "-- Select Transport --", $selected, ""); ?></td>
								<td>Truck No</td>
								<td id="section_td"><input type="text" name="txt_truck_no" id="txt_truck_no" class="text_boxes" style="width:120px;" maxlength="50"></td>
                                <td>Lock No</td>
								<td><input type="text" name="txt_lock_no" id="txt_lock_no" class="text_boxes" style="width:120px;" maxlength="50"></td>
                                <td>Driver Name</td>
								<td><input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:120px;" maxlength="50"></td>
							</tr>
							<tr>
								<td>DL/No</td>
								<td><input type="text" name="txt_dl_no" id="txt_dl_no" class="text_boxes" style="width:120px;" maxlength="50"></td>
								<td>Mobile No</td>
								<td><input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:120px;" maxlength="50"></td>
								<td>DO No</td>
								<td><input type="text" name="txt_do_no" id="txt_do_no" class="text_boxes" style="width:120px;" maxlength="50"></td>
								<td>GP No</td>
								<td><input type="text" name="txt_gp_no" id="txt_gp_no" class="text_boxes" style="width:120px;" maxlength="50" readonly disabled></td>
							</tr>
							<tr>
								<td>Final Destination</td>
								<td><input type="text" name="txt_destination" id="txt_destination" class="text_boxes" style="width:120px;" maxlength="50"></td>
								<td>C & F Agent</td>
								<td id="forwarder_td"><?=create_drop_down("cbo_forwarder", 130, $blank_array, "", 1, "-- Select--", $selected, "", "0"); ?></td>
								<td>Forwarding Agent</td>
								<td id="forwarder_td2"><?=create_drop_down("cbo_forwarder_2", 130, $blank_array, "", 1, "-- Select--", $selected, "", "0"); ?></td>
                                <td>Depo Details</td>
								<td><input type="text" name="txt_depo_details" id="txt_depo_details" class="text_boxes" style="width:120px;" maxlength="300"></td>
							</tr>
							<tr>
								<td class="must_entry_caption">Source</td>
								<td><?=create_drop_down("cbo_source", 130, $knitting_source, "", 0, "-- Select Source --", $selected, "load_drop_down( 'requires/garments_delivery_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_delivery_company', 'dev_company_td' );load_del_company(this.value);", 0, '1,3'); ?></td>
								<td class="must_entry_caption">Delivery Company</td>
								<td id="dev_company_td"><?=create_drop_down("cbo_del_company", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Delivery Company --", '', "load_drop_down( 'requires/garments_delivery_entry_controller', this.value, 'load_drop_down_del_location', 'del_location_td' );", 0); ?></td>
								<td>Delivery Location</td>
								<td id="del_location_td"><?=create_drop_down("cbo_delivery_location", 130, $blank_array, "", 1, "--Select Delivery Location--", $selected, ""); ?></td>
                                <td>Floor</td>
								<td id="del_floor_td"><?=create_drop_down("cbo_delivery_floor", 130, $blank_array, "", 1, "-- Select Delivery Floor --", $selected, ""); ?></td>
							</tr>
							<tr>

								<td>Attention</td>
								<td colspan="3"><input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:367px;" maxlength="300"></td>
								<td>Remarks</td>
								<td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:367px;" maxlength="300"></td>
							</tr>
							<tr>
								<td>Escort Name</td>
								<td id="del_floor_td"><input type="text" name="txt_escot_name" id="txt_escot_name" class="text_boxes" style="width:120px;" maxlength="300"></td>
								<td>Escort Mobile</td>
								<td><input type="text" name="txt_escot_mobile" id="txt_escot_mobile" class="text_boxes_numeric" style="width:120px;" maxlength="300"></td>
								<td>CBM</td>
								<td><input type="text" name="cbm" id="cbm" class="text_boxes_numeric" style="width:120px;" maxlength="300"></td>
                                
							</tr>
							<tr >
								<td style="display: hidden;"></td>
								<td><input type="button" class="image_uploader" style="width:110px" maxlength="300" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('txt_system_id').value,'', 'gmts_delivery_entry', 2 ,1)"></td>
                                <td><input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px"></td>

                                <td><input type="button" class="image_uploader" style="width:100px" maxlength="300" value="Add Image" onClick="file_uploader ( '../', document.getElementById('txt_system_id').value,'', 'gmts_delivery_entry_img', 1 ,1)"></td>
                                <td><input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onClick="show_cost_details();"></td>
							</tr>
						</table>
					</fieldset>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td width="30%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="110" class="must_entry_caption">Order No</td>
											<td><input name="txt_order_no" id="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/garments_delivery_entry_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&garments_nature='+$('#garments_nature').val()+'&hidden_variable_cntl='+$('#hidden_variable_cntl').val()+'&hidden_preceding_process='+$('#hidden_preceding_process').val()+'&sewing_production_variable='+$('#sewing_production_variable').val()+'&buyer_id='+$('#cbo_buyer_name').val(),'Order Search')" class="text_boxes" style="width:100px " readonly />
												<input type="hidden" id="hidden_po_break_down_id" value="" />
											</td>
										</tr>
										<tr>
											<td>Ins. Qty Validation</td>
											<td><?=create_drop_down("cbo_ins_qty_validation_type", 110, $validation_type, 1, "-- Select --", $selected, 1, 1); ?></td>
										</tr>
										<tr>
											<td class="must_entry_caption"> Ex-Factory Qty</td>
											<td>
												<input name="txt_ex_quantity" id="txt_ex_quantity" class="text_boxes_numeric" type="text" style="width:100px;" readonly onKeyUp="check_gross_shipping_status(this.value)" />
												<input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
												<input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
												<input type="hidden" name="is_posted_account" id="is_posted_account" value="" />
											</td>
										</tr>
										<tr>
											<td>Total Carton Qty</td>
											<td><input name="txt_total_carton_qnty" id="txt_total_carton_qnty" type="text" class="text_boxes_numeric" style="width:100px" onKeyUp="fn_qnty_per_ctn();" /></td>
										</tr>
										<tr>
											<td> Invoice No</td>
											<td><input name="txt_invoice_no" id="txt_invoice_no" type="text" style="width:100px;" onDblClick="openmypage_lcsc()" class="text_boxes" placeholder="Double Click To Search" maxlength="50" readonly /></td>
										</tr>
										<tr>
											<td> LC/SC No</td>
											<td><input name="txt_lc_sc_no" id="txt_lc_sc_no" class="text_boxes" type="text" style="width:100px" maxlength="50" readonly /></td>
										</tr>
										<tr>
											<td> Qty/Ctn[Pcs/Set]</td>
											<td>
                                                <input name="txt_ctn_qnty" id="txt_ctn_qnty" class="text_boxes_numeric" style="width:100px" readonly />
                                                <input name="txt_transport_com" id="txt_transport_com" class="text_boxes" type="hidden" style="width:100px" maxlength="50" />
                                            </td>
										</tr>
										<!--
                                        <tr style="visibility:hidden;">
                                            <td align="right">Trans. Company</td>
                                            <td><input name="txt_transport_com" id="txt_transport_com"  class="text_boxes" type="text" style="width:100px" maxlength="50" /></td>
                                        </tr>-->
										<tr>
											<td class="must_entry_caption">Shipping Status<span id="completion_perc"></span></td>
											<td><?=create_drop_down("shipping_status", 110, $shipment_status, "", 0, "-- Select --", 2, "", 0, '2,3', '', '', '', ''); ?></td>
										</tr>
										<tr>
											<td>Shipping Mode<span id="completion_perc"></span></td>
											<td><?=create_drop_down("cbo_shipping_mode", 110, $shipment_mode, "", 1, "-- Select --", 1, "AdditionalInfoFnc(this.value)", 0, '', '', '', '', ''); ?></td>
										</tr>
										<tr>
											<td>FOC/Claim</td>
											<td><?=create_drop_down("cbo_foc_claim", 110, $foc_claim_arr, "", 0, "-- Select --", 1, "", 0, '', '', '', '', ''); ?></td>
										</tr>
										<tr>
											<td>Inco Term<span id="completion_perc"></span></td>
											<td><?=create_drop_down("cbo_inco_term_id", 110, $incoterm, '', 1, '-Select-', 0, 0, 0); ?></td>
										</tr>
										<tr>
											<td>Actual Po</td>
											<td>
												<input name="txt_actual_po" id="txt_actual_po" type="text" class="text_boxes" style="width:100px;" onClick="actual_po_popup();" placeholder="Click" />
												<input type="hidden" name="hidden_actual_po" id="hidden_actual_po">
											</td>
										</tr>
										<tr id="additional_info_id">
											<td>Add. Info </td>
											<td>
												<input name="txt_add_info" id="txt_add_info" type="text" class="text_boxes" style="width:100px;" onClick="add_info_popup();" placeholder="Click" />
												<input type="hidden" name="hidden_add_info" id="hidden_add_info">
											</td>
										</tr>
										<tr>
											<td>Destination</td>
											<td><input name="txt_detail_destination" id="txt_detail_destination" type="text" class="text_boxes" style="width:100px;" /></td>
										</tr>
										<tr>
											<td>Net Weight</td>
											<td><input name="net_weight" id="net_weight" type="text" class="text_boxes_numeric" style="width:100px;" /></td>
										</tr>
										<tr>
											<td>Gross Weight</td>
											<td><input name="gross_weight" id="gross_weight" type="text" class="text_boxes_numeric" style="width:100px;"/></td>
										</tr>
                                        <tr>
											<td>Remarks</td>
											<td><input name="txt_remark" id="txt_remark" type="text" class="text_boxes" style="width:150px;" maxlength="450" /></td>
										</tr>
										
									</table>
								</fieldset>
							</td>
							<td width="1%" valign="top"></td>
							<td width="28%" valign="top">
								<fieldset>
									<legend>Display</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="110" id="source_msg"> Sewing Finish Qty</td>
											<td><input name="txt_finish_quantity" id="txt_finish_quantity" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly /></td>
										</tr>
										<tr>
											<td>Cuml. Ex-Factory Qty</td>
											<td><input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity" class="text_boxes_numeric" style="width:100px" disabled readonly /></td>
										</tr>
										<tr>
											<td>Yet to Ex-Factory Qty</td>
											<td>
												<input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric" style="width:100px" disabled readonly />
												<input type="hidden" id="hidden_ex_fac_poqty" name="hidden_ex_fac_poqty" value="">
												<input type="hidden" id="hidden_ex_fac_countryqty" name="hidden_ex_fac_countryqty" value="">
												<input type="hidden" id="hidden_countryqty" name="hidden_countryqty" value="">
											</td>
										</tr>
										<tr>
											<td>Job No.</td>
											<td><input style="width:100px;" type="text" class="text_boxes" name="txt_job_no" id="txt_job_no" disabled /></td>
										</tr>
										<tr>
											<td>Style Ref.</td>
											<td><input class="text_boxes" name="txt_style_no" id="txt_style_no" type="text" style="width:100px;" disabled /></td>
										</tr>
										<tr>
											<td>Shipment Date</td>
											<td><input class="text_boxes" name="txt_shipment_date" id="txt_shipment_date" style="width:100px" disabled /></td>
										</tr>
										<tr>
											<td>Order Qty.</td>
											<td><input class="text_boxes" name="txt_order_qty" id="txt_order_qty" type="text" style="width:100px;" disabled /></td>
										</tr>
										<tr>
											<td>Item</td>
											<td><?=create_drop_down("cbo_item_name", 112, $garments_item, "", 1, "-- Select Item --", $selected, "", 1, 0); ?></td>
										</tr>
										<tr>
											<td>Country</td>
											<td><?=create_drop_down("cbo_country_name", 112, "select id,country_name from lib_country", "id,country_name", 1, "-- Select Country --", $selected, "", 1); ?></td>
										</tr>
										<tr>
											<td>Country Short Name</td>
											<td><?=create_drop_down("short_country_name", 112, "select id,short_name from lib_country", "id,short_name", 1, "-- Select Country --", $selected, "", 1); ?></td>
										</tr>
										<tr>
											<td>Pack Type</td>
											<td><input class="text_boxes" name="txt_pack_type" id="txt_pack_type" style="width:100px" disabled /></td>
										</tr>
										<tr>
											<td>Country Ship Date</td>
											<td><input class="text_boxes" name="txt_country_ship_date" id="txt_country_ship_date" style="width:100px" disabled /></td>
										</tr>

										<tr>
											<td>Buyer Name</td>
											<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 112, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 1); ?></td>
										</tr>
										<tr>
											<td>Commission %</td>
											<td><input class="text_boxes" name="txt_commission" id="txt_commission" type="text" style="width:100px;" disabled /></td>
										</tr>
										<tr>
											<td>Commission Amt.</td>
											<td><input class="text_boxes" name="txt_commission_amt" id="txt_commission_amt" type="text" style="width:100px;" disabled /></td>
										</tr>
										<tr>
											<td>Tot. Com. Amt.</td>
											<td><input class="text_boxes" name="txt_order_amt" id="txt_order_amt" type="text" style="width:100px;" disabled /></td>
										</tr>
									</table>
								</fieldset>
							</td>
							<td width="41%" valign="top">
								<div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
								<div style="max-height:130px; font-size:24px; color:red; overflow-y:scroll" id="posted_msg_td_id" align="center"></div>
							</td>
						</tr>
					</table>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_exFactory_entry", 0, 1, "reset_form('exFactory_1','ex_factory_list_view*list_view_country','','','childFormReset()');refres_function()", 1);
								?>


								<input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly value="0">

								<input type="button" id="print_remarks_rpt" name="print_remarks_rpt" class="formbutton" style="width:100px;display: none;" value="Print Report2" onClick="fnc_print_report2(2);">

								<input type="button" id="print_remarks_rpt3" name="print_remarks_rpt3" class="formbutton" style="width:100px;display: none;" value="Print Report3" onClick="fnc_print_report3(2);">

								<input type="button" id="print_remarks_rpt_sonia" name="print_remarks_rpt_sonia" class="formbutton" style="width:100px;display: none;" value="Print Report4" onClick="fnc_print_report3(5);">

								<input type="button" id="print_remarks_rpt5" name="print_remarks_rpt5" class="formbutton" style="width:110px;display: none;" value="Print Report5" onClick="fnc_print_report3(6);">

								<input type="button" id="print_remarks_rpt6" name="print_remarks_rpt6" class="formbutton" style="width:110px;display: none;" value="Print Report6" onClick="fnc_print_report3(7);">

								<input type="button" id="print_remarks_rpt4" name="print_remarks_rpt4" class="formbutton" style="width:110px;display: none;" value="Deleted Color-Size" onClick="fnc_print_report4();">

								<input type="button" id="print_remarks_rpt7" name="print_remarks_rpt7" class="formbutton" style="width:100px;display: none;" value="Print Report7" onClick="fnc_print_report3(8);">

								<input type="button" id="print_remarks_rpt8" name="print_remarks_rpt8" class="formbutton" style="width:100px;display: none;" value="Print Report8" onClick="fnc_print_report3(9);">

								<input type="button" id="print_remarks_rpt9" name="print_remarks_rpt9" class="formbutton" style="width:100px;display: none;" value="Print Report9" onClick="fnc_print_report3(10);">

								<input type="button" id="print_remarks_rpt10" name="print_remarks_rpt10" class="formbutton" style="width:100px;display: none;" value="Print Report 10" onClick="fnc_print_report3(11);">

								<input type="button" id="print_remarks_rpt11" name="print_remarks_rpt11" class="formbutton" style="width:100px;display: none;" value="Print Report 11" onClick="fnc_print_report2(4);">
								<input type="button" id="print11" name="print11" class="formbutton" style="width:100px;" value="Print 11" onClick="fnc_print_report3(12);">
								<input type="button" id="print_remarks_rpt13" name="print_remarks_rpt13" class="formbutton" style="width:100px;" value="Print Report13" onClick="fnc_print_report13(13);">

							</td>
						</tr>
					</table>
					<div style="width:1000px; margin-top:5px;" id="ex_factory_list_view" align="center">
               		    <div style="width:900px; margin-top:5px;" id="printing_cost_list_view" align="center"></div>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="list_view_country" style="width:388px;float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$(function(){
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>