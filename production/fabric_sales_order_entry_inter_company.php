<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Sales Order Entry
Functionality	:
JS Functions	:
Created by		:	Reza
Creation date 	: 	17.01.2016
Updated by 		:   Jahid Hasan
Update date		:
Report by		:
Creation date 	:
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
echo load_html_head_contents("Fabric Sales Order Entry", "../", 1, 1, '', '', '');
?>
<script>

	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';

	<?
	//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][109] );
	//echo "var field_level_data= ". $data_arr . ";\n";
	?>

	function openmypage_fabricBooking() {
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}
		else {
			var title = 'Booking Selection Form';
			var page_link = 'requires/fabric_sales_order_entry_inter_company_controller.php?cbo_company_id=' + cbo_company_id + '&action=fabricBooking_popup';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=420px,center=1,resize=1,scrolling=0', '');

			emailwindow.onclose = function () {
                var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
                var booking_data = this.contentDoc.getElementById("hidden_booking_data").value;	 //Access form field with id="emailfield"
                var data = booking_data.split("__");
                $('#txt_season').val(0)
                $('#txt_booking_no_id').val(data[0]);
                $('#txt_booking_no').val(data[1]);
                $('#cbo_buyer_name').val(data[2]);
                $('#txt_style_ref').val(data[3]);
                $('#txt_delivery_date').val(data[4]);
                $('#cbo_currency').val(data[5]);
                $('#txt_season').val(data[6]);
                $('#cbo_team_leader').val(data[7]);
                $('#txt_remarks').val(data[9]);
                $('#txt_is_approved').val(data[10]);
                $('#txt_is_short').val(data[12]);
                $('#txt_fabric_source').val(data[13]);
                $('#txt_item_category').val(data[14]);
                $('#txt_hidden_job_no').val(data[15]);
                $('#txt_booking_type').val(data[16]);
                $('#txt_order_id').val(data[17]);
                $('#txt_booking_entry_form').val(data[18]);
                $('#booking_without_order').val(data[19]);
                $('#booking_approval_date').val(data[20]);

                $('#txt_action_name').val(data[14]);

                load_drop_down('requires/fabric_sales_order_entry_inter_company_controller', document.getElementById('cbo_within_group').value + '_' + data[7], 'load_drop_down_dealing_merchant', 'team_td');

                $('#cbo_dealing_merchant').val(data[8]);
                $('#is_apply_last_update').val(0);

                show_list_view(data[1], 'show_fabric_details', 'order_details_container', 'requires/fabric_sales_order_entry_inter_company_controller', '');
                total_amount_cal();
                if(data[15] == ""){
                	$("#txt_style_ref").removeAttr("readonly","readonly");
                }
            }
        }
    }

    function active_inactive() {
    	reset_form('', '', 'txt_booking_no*txt_booking_no_id*txt_delivery_date*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*txt_season*cboBodyPart_1*cboColorType_1*txtFabricDesc_1*fabricDescId_1*txtFabricGsm_1*txtFabricDia_1*cboDiaWidthType_1*txtColor_1*colorId_1*txtFinishQty_1*txtAvgRate_1*txtAmount_1*txtProcessLoss_1*txtGreyQty_1*cboWorkScope_1*updateIdDtls_1*txtBookingQnty_1', '', '$(\'#tbl_item_details tbody tr:not(:first)\').remove();', '');

    	var within_group = $('#cbo_within_group').val();
    	var color_from_library = $('#color_from_library').val();
    	$('#txt_booking_date').val('<? echo date("d-m-Y"); ?>');
    	$('#is_apply_last_update').val(0);
    	$('#cboUom_1').val(12);

    	if (within_group == 1) {
    		$("#list_change_booking_nos").css("display", "block");
    		$('#txt_booking_no').attr('readOnly', 'readOnly');
    		$('#txt_booking_no').attr('onDblClick', 'openmypage_fabricBooking();');
    		$('#txt_booking_no').attr('placeholder', 'Double Click To Import');
    		$('#txt_booking_date').attr('readOnly', 'readOnly');
    		$('#txt_booking_date').attr('disabled', 'disabled');
    		$('#txt_delivery_date').attr('disabled', 'disabled');
    		$('#txt_delivery_date').attr('placeholder', 'Display');
    		$('#txt_style_ref').attr('readOnly', 'readOnly');
    		$('#txt_style_ref').attr('placeholder', 'Display');
    		$('#txt_season').attr('readOnly', 'readOnly');
    		$('#txt_season').attr('placeholder', 'Display');
    		$('#cbo_team_leader').attr('disabled', 'disabled');
    		$('#cbo_currency').attr('disabled', 'disabled');
    		$('#cboBodyPart_1').attr('disabled', 'disabled');
    		$('#cboColorType_1').attr('disabled', 'disabled');
    		$('#txtFabricDesc_1').attr('disabled', 'disabled');
    		$('#txtFabricDia_1').attr('disabled', 'disabled');
    		$('#cboUom_1').attr('disabled', 'disabled');
    		$('#cboConsUom_1').attr('disabled', 'disabled');
    		$('#cboDiaWidthType_1').attr('disabled', 'disabled');
    		$('#txtAvgRate_1').attr('readOnly', 'readOnly');
    		$('#txtBookingQnty_1').attr('readOnly', 'readOnly');
    		$('#txtFinishQty_1').attr('readOnly', 'readOnly');
    		$('#txtColor_1').attr('readOnly', 'readOnly');
    		$('#txtColor_1').attr('placeholder', 'Display');
    		$('#txtColor_1').removeAttr('onDblClick', 'onDblClick');
    		$('#txtRemarks_1').attr('readOnly', 'readOnly');
    		$('#txtFabricGsm_1').attr('disabled', 'disabled');
    		$('#increase_1').attr('disabled', 'disabled');
            $('#booking_approval_date').attr('disabled', 'disabled');
            $('#booking_approval_date').attr('readonly', 'readonly');
            $("#txt_season").attr('disabled','disabled'); 
             
    	}
    	else {
    		$("#list_change_booking_nos").css("display", "none");
    		$("#last_update").css("visibility", "hidden");
    		$('#txt_booking_no').removeAttr('onDblClick', 'onDblClick');
    		$('#txt_booking_no').removeAttr('readOnly', 'readOnly');
    		$('#txt_booking_date').removeAttr('readOnly', 'readOnly');
    		$('#txt_booking_date').removeAttr('disabled', 'disabled');
    		$('#txt_booking_no').attr('placeholder', 'Write');
    		$('#txt_delivery_date').removeAttr('disabled', 'disabled');
    		$('#txt_delivery_date').attr('placeholder', '');
    		$('#txt_style_ref').removeAttr('readOnly', 'readOnly');
    		$('#txt_style_ref').attr('placeholder', 'Write');
    		$('#txt_season').removeAttr('readOnly', 'readOnly');
    		$('#txt_season').attr('placeholder', 'Write');
    		$('#cbo_team_leader').removeAttr('disabled', 'disabled');
    		$('#cbo_currency').removeAttr('disabled', 'disabled');
    		$('#cboBodyPart_1').removeAttr('disabled', 'disabled');
    		$('#cboColorType_1').removeAttr('disabled', 'disabled');
    		$('#txtFabricDesc_1').removeAttr('disabled', 'disabled');
    		$('#txtFabricDia_1').removeAttr('disabled', 'disabled');
    		$('#cboUom_1').removeAttr('disabled', 'disabled');
    		$('#cboConsUom_1').removeAttr('disabled', 'disabled');
    		$('#cboDiaWidthType_1').removeAttr('disabled', 'disabled');
    		$('#txtBookingQnty_1').removeAttr('readOnly', 'readOnly');
    		$('#txtFinishQty_1').attr('readOnly', 'readOnly');
    		$('#txtAvgRate_1').removeAttr('readOnly', 'readOnly');
    		$('#txtRemarks_1').removeAttr('readOnly', 'readOnly');
    		$('#txtFabricGsm_1').removeAttr('disabled', 'disabled');
    		$('#increase_1').removeAttr('disabled', 'disabled');
            $('#booking_approval_date').removeAttr('disabled', 'disabled');
            $('#booking_approval_date').removeAttr('readonly', 'readonly');
            $("#txt_season").removeAttr('disabled'); 
    		if (color_from_library == 2) {
    			$('#txtColor_1').removeAttr('readOnly', 'readOnly');
    			$('#txtColor_1').attr('placeholder', 'Write');
    			$('#txtColor_1').removeAttr('onDblClick', 'onDblClick');
    		}
    		else {
    			$('#txtColor_1').attr('placeholder', 'Display');
    			$('#txtColor_1').attr('placeholder', 'Double Click');
    			$('#txtColor_1').attr('onDblClick', 'openmypage_color(1);');
    		}
    	}

    	var company_id = document.getElementById('cbo_company_id').value;
    	if (company_id == 0) {
    		$("#cbo_buyer_name option[value!='0']").remove();
    	}
    	else {
    		load_drop_down('requires/fabric_sales_order_entry_inter_company_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'buyer_td');
    	}
    }

    function add_break_down_tr(i) {
    	var within_group = $('#cbo_within_group').val();
    	var color_from_library = $('#color_from_library').val();

    	if (within_group != 1) {
    		var lastTrId = $('#tbl_item_details tbody tr:last').attr('id').split('_');
    		var row_num = lastTrId[1];
    		if (row_num != i) {
    			return false;
    		}
    		else {
    			i++;
    			$("#tbl_item_details tbody tr:last").clone().find("input,select").each(function () {
    				$(this).attr({
    					'id': function (_, id) {var id = id.split("_");return id[0] + "_" + i},
    					'name': function (_, name) {return name},
    					'value': function (_, value) {return value}
    				});
    			}).end().appendTo("#tbl_item_details");

    			$('#cboBodyPart_' + i).val($('#cboBodyPart_' +(i-1)).val());
    			$('#cboColorType_' + i).val($('#cboColorType_' +(i-1)).val());
    			$('#cboDiaWidthType_' + i).val($('#cboDiaWidthType_' +(i-1)).val());

    			$('#txtColor_' + i).val('');
    			$('#cboColorRange_' + i).val('');
    			$('#cboUom_' + i).val('');
    			$('#txtFinishQty_' + i).val('');
    			$('#txtBookingQnty_' + i).val('');
    			$('#txtAvgRate_' + i).val('');
    			$('#txtAmount_' + i).val('');
    			$('#txtProcessLoss_' + i).val('');
    			$('#txtGreyQty_' + i).val('');
    			$('#cboWorkScope_' + i).val('');
    			$('#updateIdDtls_' + i).val('');

    			$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
    			$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);    			
    			$('#tr_' + i).find("td:eq(0) span").text(i);
    			$('#tr_' + i).find('#txtSerial_' + i).val(i);
    			$('#txtSerial_' + i).val(i);

    			$('#txtFabricDesc_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_fabricDescription(" + i + ");");
    			$('#txtFinishQty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");");
    			$('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_fin_qty(" + i + ");");
    			$('#txtAvgRate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
    			$('#txtProcessLoss_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_grey_qty(" + i + ");");
    			$('#txtFabricGsm_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#txtFabricDia_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#cboConsUom_' + i).removeAttr("onchange").attr("onchange", "refresh_fields(" + i + ");");

    			if (color_from_library == 2) {
    				$('#txtColor_' + i).removeAttr('readOnly', 'readOnly');
    				$('#txtColor_' + i).attr('placeholder', 'Write');
    				$('#txtColor_' + i).removeAttr('onDblClick', 'onDblClick');
    			}
    			else {
    				$('#txtColor_' + i).attr('placeholder', 'Display');
    				$('#txtColor_' + i).attr('placeholder', 'Double Click');
    				$('#txtColor_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_color(" + i + ");");
    			}

    			$('#increase_' + i).removeAttr("value").attr("value", "+");
    			$('#decrease_' + i).removeAttr("value").attr("value", "-");
    			$('#increase_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
    			$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
    		}

    		set_all_onclick();
    	}
    }

    function fn_deleteRow(rowNo) {
    	if ($('#cbo_within_group').val() != 1) {
    		var numRow = $('#tbl_item_details tbody tr').length;
    		if (rowNo != 1) {
    			var deleted_ids = $("#deletedDtlsIds").val();
    			if(deleted_ids != ""){
    				$("#deletedDtlsIds").val(deleted_ids+","+$('#updateIdDtls_' + rowNo).val());
    			}else{
    				$("#deletedDtlsIds").val($('#updateIdDtls_' + rowNo).val());
    			}
    			$('#tr_' + rowNo).remove();
    		}
    		else {
    			return false;
    		}
    	}
    }

    function fnc_fabric_sales_order_entry(operation) {
    	var within_group = $('#cbo_within_group').val();
    	var company_id = $('#cbo_company_id').val();
    	var booking_no = trim($('#txt_booking_no').val());
    	var hdn_booking_no = trim($('#txt_hdn_booking_no').val());
    	var buyer_name = $('#cbo_buyer_name').val();
    	var update_id = $('#update_id').val();
    	var hdn_job_no = $('#txt_hidden_job_no').val();

    	// check booking no when insert
    	if(within_group == 2){
    		var data = company_id + "*" + booking_no + "*" + buyer_name + "*" + operation + "*" + update_id;
    		var response=trim(return_global_ajax_value( data, 'is_booking_duplicate', '', 'requires/fabric_sales_order_entry_inter_company_controller'));
    		if(response == "invalid"){
    			alert("Sales/Booking No is duplicate");
    			return;
    		}
    	}

    	if(within_group == 2 && operation == 1){            
    		if(booking_no != hdn_booking_no){
    			var data = company_id + "*" + hdn_booking_no + "*" + buyer_name;
    			var response=trim(return_global_ajax_value( data, 'is_booking_used_in_plan', '', 'requires/fabric_sales_order_entry_inter_company_controller'));
    			if(response == "invalid"){
    				alert("Knitting plan found against Booking no.Booking can not be updated.");
    				return;
    			}
    		}
    	}

    	if (operation == 4) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_inter_company_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("requires/fabric_sales_order_entry_inter_company_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}

    		return;
    	}

    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}
    	if(hdn_job_no != ""){
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*txt_style_ref*cbo_currency', 'Company*Sales/Booking No*Booking Date*Location*Style Ref.*Currency') == false) {
    			return;
    		}
    	}else{
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*cbo_currency', 'Company*Sales/Booking No*Booking Date*Location*Currency') == false) {
    			return;
    		}
    	}


    	var j = 0;
    	var dataString = '';
    	//var x = 1;
    	var finishQty = "";
    	$("#tbl_item_details").find('tbody tr').each(function () {
    		var x = $(this).find('input[name="txtSerial[]"]').val();
    		var cboGarmItemId = $(this).find('select[name="cboGarmItemId[]"]').val();
    		var cboBodyPart = $(this).find('select[name="cboBodyPart[]"]').val();
    		var cboColorType = $(this).find('select[name="cboColorType[]"]').val();
    		var txtFabricDesc = $(this).find('input[name="txtFabricDesc[]"]').val();
    		var fabricDescId = $(this).find('input[name="fabricDescId[]"]').val();
    		var txtFabricGsm = $(this).find('input[name="txtFabricGsm[]"]').val();
    		var txtFabricDia = $(this).find('input[name="txtFabricDia[]"]').val();
    		var cboDiaWidthType = $(this).find('select[name="cboDiaWidthType[]"]').val();
    		var txtColor = $(this).find('input[name="txtColor[]"]').val();
    		var colorId = $(this).find('input[name="colorId[]"]').val();
    		var cboColorRange = $(this).find('select[name="cboColorRange[]"]').val();
    		var txtFinishQty = $(this).find('input[name="txtFinishQty[]"]').val();
    		var txtAvgRate = $(this).find('input[name="txtAvgRate[]"]').val();
    		var txtAmount = $(this).find('input[name="txtAmount[]"]').val();
    		var txtProcessLoss = $(this).find('input[name="txtProcessLoss[]"]').val();
    		var txtGreyQty = $(this).find('input[name="txtGreyQty[]"]').val();
    		var cboWorkScope = $(this).find('select[name="cboWorkScope[]"]').val();
    		var cboUom = $(this).find('select[name="cboUom[]"]').val();
    		var cboConsUom = $(this).find('select[name="cboConsUom[]"]').val();
    		var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
    		var txtRemarks = $(this).find('input[name="txtRemarks[]"]').val();
    		var rmgQty = $(this).find('input[name="rmgQty[]"]').val();
    		var pre_cost_fabric_cost_dtls_id = $(this).find('input[name="pre_cost_fabric_cost_dtls_id[]"]').val();
    		var booking_qnty_by_uom = $(this).find('input[name="txtBookingQnty[]"]').val();

    		var finishQty = $('#txtFinishQty_'+x).val();
    		if (finishQty * 1 > 0) {
    			var fabricDesc = $('#txtFabricDesc_'+x).val();
    			var fabricGsm = $('#txtFabricGsm_'+x).val();
    			var fabricDia = $('#txtFabricDia_'+x).val();
    			if (within_group == 2) {
    				if(fabricDesc == "" || fabricGsm == "" || fabricDia == ""){
    					j = 0;
    					return;
    				}else{
    					j++;
    				}    				
    			}else{
    				j++;
    			}
    			dataString += '&cboBodyPart' + j + '=' + cboBodyPart + '&cboColorType' + j + '=' + cboColorType + '&txtFabricDesc' + j + '=' + txtFabricDesc + '&fabricDescId' + j + '=' + fabricDescId + '&txtFabricGsm' + j + '=' + txtFabricGsm + '&txtFabricDia' + j + '=' + txtFabricDia + '&cboDiaWidthType' + j + '=' + cboDiaWidthType + '&txtColor' + j + '=' + txtColor + '&colorId' + j + '=' + colorId + '&cboColorRange' + j + '=' + cboColorRange + '&txtFinishQty' + j + '=' + txtFinishQty + '&txtAvgRate' + j + '=' + txtAvgRate + '&txtAmount' + j + '=' + txtAmount + '&txtProcessLoss' + j + '=' + txtProcessLoss + '&txtGreyQty' + j + '=' + txtGreyQty + '&cboWorkScope' + j + '=' + cboWorkScope + '&updateIdDtls' + j + '=' + updateIdDtls + '&cboUom' + j + '=' + cboUom + '&txtRemarks' + j + '=' + txtRemarks + '&rmgQty' + j + '=' + rmgQty + '&pre_cost_fabric_cost_dtls_id' + j + '=' + pre_cost_fabric_cost_dtls_id + '&cboGarmItemId' + j + '=' + cboGarmItemId + '&booking_qnty_by_uom' + j + '=' + booking_qnty_by_uom + '&cboConsUom' + j + '=' + cboConsUom;
    			//x++;
    		}else{
    			j = 0;
    			return;
    		}
    	});
    	if (within_group == 1){
    		if (j < 1) {
    			alert('You must fill the mendatory fields');
    			return;
    		}
    	}else{
    		if (j < 1) {
    			alert('You must fill the mendatory fields');
    			return;
    		}
    	}
        var season_val=$('#txt_season :selected').text();
        //alert(season_val);
    	var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_job_no*cbo_company_id*cbo_within_group*txt_booking_no*txt_booking_no_id*txt_booking_date*txt_delivery_date*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*cbo_ship_mode*txt_season*txt_remarks*update_id*process_loss_method*color_from_library*is_apply_last_update*txt_hdn_booking_no*deletedDtlsIds*booking_without_order*booking_approval_date*cbo_ready_to_approved', "../") + dataString + '&total_row=' + j+ '&season_val=' + season_val;

    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_inter_company_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_sales_order_entry_Reply_info;
    }

    function fnc_fabric_sales_order_entry_Reply_info() {

        $("#print1").removeClass( "formbutton_disabled"); //To make disable print to button
        $("#print1").addClass( "formbutton"); //To make enable print to button
        $("#print_2").removeClass( "formbutton_disabled"); //To make disable print to button
        $("#print_2").addClass( "formbutton"); //To make enable print to button
        if (http.readyState == 4) {
        	var response = trim(http.responseText).split('**');

        	show_msg(response[0]);
        	if (response[0] == 5) {
        		alert(response[1]);
        		release_freezing();
        		return;
        	}
        	if ((response[0] == 0 || response[0] == 1)) {
        		document.getElementById('update_id').value = response[1];
        		document.getElementById('txt_job_no').value = response[2];

        		var color_from_library = $('#color_from_library').val();
        		var cbo_within_group = $('#cbo_within_group').val();

        		$('#cbo_company_id').attr('disabled', 'disabled');
        		$('#cbo_within_group').attr('disabled', 'disabled');

        		show_list_view(response[1] + "**" + color_from_library + "**" + cbo_within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_inter_company_controller', '');
        		set_button_status(1, permission, 'fnc_fabric_sales_order_entry', 1);
        		show_fabric_yarn_details(response[1]);
    			//show_change_bookings();
    		}
    		release_freezing();
    	}
    }

    function fnc_fabric_yarn_dtls_entry(operation) {
    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}

    	if (form_validation('txt_job_no', 'Sales Order No') == false) {
    		return;
    	}

    	var j = 0;
    	var dataString = '';
    	$("#table_yarn_details").find('tbody tr').each(function () {
    		var fabricDescIdY = $(this).find('input[name="fabricDescIdY[]"]').val();
    		var txtFabricGsmY = $(this).find('input[name="txtFabricGsmY[]"]').val();
    		var txtGreyQtyY = $(this).find('input[name="txtGreyQtyY[]"]').val();
    		var yarnData = $(this).find('input[name="yarnData[]"]').val();
    		j++;
    		dataString += '&fabricDescIdY' + j + '=' + fabricDescIdY + '&txtFabricGsmY' + j + '=' + txtFabricGsmY + '&txtGreyQtyY' + j + '=' + txtGreyQtyY + '&yarnData' + j + '=' + yarnData;

    	});

    	if (j < 1) {
    		alert('No data');
    		return;
    	}

    	var data = "action=save_update_delete_yarn&operation=" + operation + get_submitted_data_string('txt_job_no*update_id', "../") + dataString + '&total_row=' + j;

    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_inter_company_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_yarn_dtls_entry_Reply_info;
    }

    function fnc_fabric_yarn_dtls_entry_Reply_info() {
    	if (http.readyState == 4) {
    		var response = trim(http.responseText).split('**');

    		show_msg(response[0]);
    		if ((response[0] == 0 || response[0] == 1)) {
    			set_button_status(1, permission, 'fnc_fabric_yarn_dtls_entry', 2);
    		}
    		show_change_bookings();
    		release_freezing();
    	}
    }

    function fabric_sales_order_print3() {
    	var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
    	window.open("requires/fabric_sales_order_entry_inter_company_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
    	return;
    }

    function openmypage_fabricDescription(i) {
    	var title = 'Fabric Description Info';
    	var page_link = 'requires/fabric_sales_order_entry_inter_company_controller.php?action=fabricDescription_popup';

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var theemail = this.contentDoc.getElementById("hidden_desc_id").value;
    		var theename = this.contentDoc.getElementById("hidden_desc_no").value;
    		var theegsm = this.contentDoc.getElementById("hidden_gsm").value;
    		var theecolorrange = this.contentDoc.getElementById("hidden_color_range").value;

    		$('#txtFabricDesc_' + i).val(theename);
    		$('#fabricDescId_' + i).val(theemail);
    		$('#txtFabricGsm_' + i).val(theegsm);
    		$('#cboColorRange_' + i).val(theecolorrange);
    	}
    }

    function openmypage_color(i) {
    	var title = 'Color Info';
    	var page_link = 'requires/fabric_sales_order_entry_inter_company_controller.php?action=color_popup';

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var theemail = this.contentDoc.getElementById("hidden_color_id").value;
    		var theename = this.contentDoc.getElementById("hidden_color_no").value;

    		$('#txtColor_' + i).val(theename);
    		$('#colorId_' + i).val(theemail);
    	}
    }

    function openmypage_yarnDetails(i) {
    	var cbo_company_id = $('#cbo_company_id').val();
    	var txtGreyQty = $('#txtGreyQtyY_' + i).val();
    	var yarnData = $('#yarnData_' + i).val();
    	var txtFabricDesc = $('#txtFabricDescY_' + i).val();

    	if (form_validation('cbo_company_id', 'Company') == false) {
    		return;
    	}

    	var title = 'Yarn Details Info';
    	var page_link = 'requires/fabric_sales_order_entry_inter_company_controller.php?action=yarnDetails_popup&cbo_company_id=' + cbo_company_id + '&txtGreyQty=' + txtGreyQty + '&yarnData=' + yarnData + '&txtFabricDesc=' + txtFabricDesc;

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var yarn_data = this.contentDoc.getElementById("hidden_yarn_data").value;

    		$('#yarnData_' + i).val(yarn_data);
    	}
    }

    function copy_process_loss(i) {
    	var row_num = $('#tbl_item_details tbody tr').length;
    	var process_loss = document.getElementById('txtProcessLoss_' + i).value;
    	var deter_id = document.getElementById('fabricDescId_' + i).value;

    	for (var j = (i * 1 + 1); j <= row_num; j++) {
    		var deter_id_check = document.getElementById('fabricDescId_' + j).value;
    		if (deter_id == deter_id_check) {
    			$('#txtProcessLoss_' + j).val(process_loss);
    			calculate_grey_qty(j);
    		}
    	}
    }
    //fnc copy process loss behavior on all fields
    /*
     function copy_process_loss_all_fields(i)
     {
     var row_num=$('#tbl_item_details tbody tr').length;
     var process_loss=document.getElementById('txtProcessLoss_'+i).value;
     //var deter_id=document.getElementById('fabricDescId_'+i).value;

     for (var j=(i*1+1); j<=row_num; j++)
     {
     $('#txtProcessLoss_'+j).val(process_loss);
     calculate_grey_qty(j);
     */

    //var deter_id_check=document.getElementById('fabricDescId_'+j).value;
    /*if(deter_id==deter_id_check)
     {
     $('#txtProcessLoss_'+j).val(process_loss);
     calculate_grey_qty(j);
     }
     }
 } */

 function calculate_grey_qty(i) {
 	var process_loss_method = $('#process_loss_method').val() * 1;
 	var finish_qty = $('#txtFinishQty_' + i).val() * 1;
 	var processLoss = $('#txtProcessLoss_' + i).val() * 1;
 	var grey_qnty = 0;
 	if (finish_qty <= 0 || processLoss <= 0) {
 		grey_qnty = finish_qty;
 	}
 	else {
 		if (process_loss_method == 1) {
 			grey_qnty = finish_qty + ((finish_qty / 100) * processLoss);
 		}
 		else {
 			var perc = 1 - (processLoss / 100);
 			grey_qnty = finish_qty / perc;
 		}
 		grey_qnty = grey_qnty.toFixed(2);
 	}
 	$('#txtGreyQty_' + i).val(grey_qnty);
 }

 function calculate_fin_qty(i) {
 	var process_loss_method = $('#process_loss_method').val() * 1;
 	var cbo_within_group = $('#cbo_within_group').val();
 	var booking_qty = $('#txtBookingQnty_' + i).val() * 1;
 	var processLoss = $('#txtProcessLoss_' + i).val() * 1;
 	var cboConsUom = $('#cboConsUom_' + i).val() * 1;
 	var txtFabricGsm = $('#txtFabricGsm_' + i).val() * 1;
 	var txtFabricDia = $('#txtFabricDia_' + i).val() * 1;
 	if(cboConsUom == 12){
 		var qnty_conversion = booking_qty*1;
 	}else if(cboConsUom == 27){
 		var qnty_conversion = (booking_qty * 36 * txtFabricDia * txtFabricGsm) / (1550 * 1000);
 	}
 	var grey_qnty = 0;
 	if (qnty_conversion <= 0 || processLoss <= 0) {
 		grey_qnty = qnty_conversion;
 	}
 	else {
 		if (process_loss_method == 1) {
 			grey_qnty = qnty_conversion + ((qnty_conversion / 100) * processLoss);
 		}
 		else {
 			var perc = 1 - (processLoss / 100);
 			grey_qnty = qnty_conversion / perc;
 		}
 		grey_qnty = grey_qnty.toFixed(2);
 	}
 	$('#txtFinishQty_' + i).val(qnty_conversion);
 	$('#txtGreyQty_' + i).val(grey_qnty);
 }

 function calculate_amount(i) {
 	var finish_qty = $('#txtBookingQnty_' + i).val() * 1;
 	var avgRate = $('#txtAvgRate_' + i).val() * 1;
 	var amount = 0;
 	if (finish_qty <= 0 || avgRate <= 0) {
 		amount = '';
 	}
 	else {
 		amount = finish_qty * avgRate;
 		amount = amount.toFixed(2);
 	}
 	$('#txtAmount_' + i).val(amount);
 	total_amount_cal();
 }

 function total_amount_cal(){
 	var total_amnt = 0;
 	$("#tbl_item_details").find('tbody tr').each(function () {
 		var txtAmount = $(this).find('input[name="txtAmount[]"]').val();
 		total_amnt += txtAmount*1;
 	});
 	total_amnt = total_amnt.toFixed(4);

 	$('#total_amnt').html(total_amnt);
 }


 function openmypage_jobNo() {
 	var cbo_company_id = $('#cbo_company_id').val();
 	var color_from_library = $('#color_from_library').val();

 	if (form_validation('cbo_company_id', 'Company') == false) {
 		return;
 	}
 	else {
 		var title = 'Job Selection Form';
 		var page_link = 'requires/fabric_sales_order_entry_inter_company_controller.php?cbo_company_id=' + cbo_company_id + '&action=jobNo_popup';

 		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '');

 		emailwindow.onclose = function () {
 			var theform = this.contentDoc.forms[0];
 			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
 			var booking_data = hidden_booking_data.split("**");
 			var job_id = booking_data[9];

 			$('#txt_booking_no_id').val(booking_data[0]);
 			$('#txt_booking_no').val(booking_data[1]);
 			$('#txt_booking_type').val(booking_data[2]);
 			$('#txt_booking_entry_form').val(booking_data[3]);
 			$('#txt_fabric_source').val(booking_data[4]);
 			$('#txt_item_category').val(booking_data[5]);
 			$('#txt_hidden_job_no').val(booking_data[6]);
 			$('#txt_order_id').val(booking_data[7]);
 			$('#txt_is_approved').val(booking_data[8]);
 			$('#txt_is_short').val(booking_data[10]);


            $("#print1").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print1").addClass( "formbutton"); //To make enable print to button
            $("#print_2").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_2").addClass( "formbutton"); //To make enable print to button

            get_php_form_data(job_id, "populate_data_from_sales_order", "requires/fabric_sales_order_entry_inter_company_controller");
            var cbo_within_group = $('#cbo_within_group').val();
            show_list_view(job_id + "**" + color_from_library + "**" + cbo_within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_inter_company_controller', '');
            total_amount_cal();
            show_fabric_yarn_details(job_id);
        }
    }
}

function show_fabric_yarn_details(update_id) {
        //show_list_view(update_id,'yarn_details','yarn_details_list_view','requires/fabric_sales_order_entry_inter_company_controller','',0);
        var datas = return_global_ajax_value(update_id, 'yarn_details', '', 'requires/fabric_sales_order_entry_inter_company_controller');
        var yarn_datas = trim(datas).split("##")
        $('#yarn_details_list_view').html(yarn_datas[0]);

        /*var button_status=0;
         //if(parseInt(yarn_datas[1])>1) {button_status=1;}
         if( $('#table_yarn_details tbody tr').length>0){button_status=1;}
         //if( $('#txtGreyQtyY_1').val()*1)>0  {button_status=1;}
         set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry',2); */

         var button_status = 0;
         if (parseInt(yarn_datas[1]) > 1) {
         	button_status = 1;
         }
         set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry', 2);

     }

     function apply_last_update() {
     	if (form_validation('txt_job_no', 'Job No') == false) {
     		return;
     	}

     	var within_group = $('#cbo_within_group').val();
     	var update_id = $('#update_id').val();
     	var txt_booking_no = $('#txt_booking_no').val();
     	if (within_group == 1) {
     		var approved = trim(return_global_ajax_value(txt_booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_inter_company_controller'));
     		if (approved != 1) {
     			alert("Approved Booking First.");
     			return;
     		}

     		freeze_window(5);
     		show_list_view(update_id + '**' + txt_booking_no, 'show_fabric_details_last_update', 'order_details_container', 'requires/fabric_sales_order_entry_inter_company_controller', '');
     		total_amount_cal();
     		$('#is_apply_last_update').val(1);
     		release_freezing();
     	}
     	else {
     		alert("Not Applicable For Outside Group");
     		return;
     	}
     }

     function show_change_bookings() {
     	show_list_view('', 'show_change_bookings', 'list_change_booking_nos', 'requires/fabric_sales_order_entry_inter_company_controller', 'setFilterGrid(\'tbl_list_search_revised\',-1);');
     }

     function btn_load_change_bookings(){
     	var count = trim(return_global_ajax_value("", 'btn_load_change_bookings', '', 'requires/fabric_sales_order_entry_inter_company_controller'));
     	if(count > 0){
     		$("#list_change_booking_nos").html("<span id='btn_span' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_bookings()' type='button' class='formbutton' value='&nbsp;&nbsp;Revised Bookings&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Revised Booking List'></span>");
     	}else
     	{
     		$("#list_change_booking_nos").html("<span id='btn_span_disabled' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' type='button' class='formbutton_disabled' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#ccc !important; background-image:none !important;border-color: #ccc;' title='Revised Booking List'></span>");
     	}
     	(function blink() { 
     		$('#btn_span').fadeOut(900).fadeIn(900, blink); 
     	})();
     }

     function set_form_data(data) {
     	var data = data.split("**");
     	var job_id = data[0];
     	var booking_no = data[2];
     	var cbo_company_id = data[1];
     	$('#cbo_company_id').val(cbo_company_id);
     	$("#last_update").css("visibility", "visible");

        //eval($('#cbo_company_id').attr('onchange'));
        //$('#cbo_company_id').trigger('onchange');


        var approved = trim(return_global_ajax_value(booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_inter_company_controller'));
        if (approved != 1) {
        	alert("Approved Booking First.");
        	return;
        }


        load_drop_down('requires/fabric_sales_order_entry_inter_company_controller', cbo_company_id, 'load_drop_down_location', 'location_td');
        get_php_form_data(cbo_company_id, 'process_loss_method', 'requires/fabric_sales_order_entry_inter_company_controller');

        var color_from_library = $('#color_from_library').val();

        get_php_form_data(job_id, "populate_data_from_sales_order", "requires/fabric_sales_order_entry_inter_company_controller");

        var within_group = $('#cbo_within_group').val();
        show_list_view(job_id + "**" + color_from_library + "**" + within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_inter_company_controller', '');
        total_amount_cal();

        show_fabric_yarn_details(job_id);
    }


    function open_terms_condition_popup(page_link, title) {
    	var txt_job_no = document.getElementById('txt_job_no').value;
    	if (txt_job_no == "") {
    		alert("Save The Sales Order First")
    		return;
    	}
    	else {
    		page_link = page_link + get_submitted_data_string('txt_booking_no*txt_job_no', '../');
    		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0', '../')
    		emailwindow.onclose = function () {
    		}
    	}
    }

    function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short, action) {
    	var report_title = "";
    	if (entry_form == 108) {
    		var action_method = "action=show_fabric_booking_report_urmi_per_job";
    	} else {
    		var action_method = "action=" + action;
    	}
    	if (entry_form == 118) {
    		report_title = "&report_title=Main Fabric Booking Urmi";
    		http.open("POST", "../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
    	} else if (type == 1) {
    		if (entry_form == 108) {
    			report_title = "&report_title=Partial Fabric Booking";
    			http.open("POST", "../order/woven_order/requires/partial_fabric_booking_controller.php", true);
    		} else {
    			report_title = "&report_title=Short Fabric Booking";
    			http.open("POST", "../order/woven_order/requires/short_fabric_booking_controller.php", true);
    		}
    	} else if (type == 2) {
    		report_title = "&report_title=Main Fabric Booking Urmi";
    		http.open("POST", "../order/woven_order/requires/fabric_booking_controller.php", true);
    	} else {
    		report_title = "&report_title=Sample Fabric Booking Urmi";
    		http.open("POST", "../order/woven_order/requires/sample_booking_controller.php", true);
    	}
    	var data = action_method + report_title +
    	'&txt_booking_no=' + "'" + booking_no + "'" +
    	'&cbo_company_name=' + "'" + company_id + "'" +
    	'&txt_order_no_id=' + "'" + order_id + "'" +
    	'&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
    	'&cbo_fabric_source=' + "'" + fabric_source + "'" +
    	'&id_approved_id=' + "'" + approved + "'" +
    	'&id_approved_id=' + "'" + approved + "'" +
    	'&txt_job_no=' + "'" + job_no + "'";
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = generate_fabric_report_reponse;
    }

    function generate_fabric_report_reponse() {
    	if (http.readyState == 4) {
    		var w = window.open("Surprise", "#");
    		var d = w.document.open();
    		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
    		d.close();
    	}
    }

    function refresh_fields(i){
    	var within_group = $('#cbo_within_group').val();
    	if(within_group == 2){
    		$('#txtAvgRate_'+i).val('');
    		$('#txtAmount_'+i).val('');
    		$('#txtBookingQnty_'+i).val('');
    		$('#txtFinishQty_'+i).val('');
    		$('#txtProcessLoss_'+i).val('');
    		$('#txtGreyQty_'+i).val('');
    	}
    }

</script>
</head>

<!-- <body onLoad="set_hotkey(); show_change_bookings();"> -->
	<body onLoad="set_hotkey(); btn_load_change_bookings();">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../", $permission); ?>
			<fieldset style="width:1445px;">
				<legend>Fabric Sales Order Entry</legend>
				<form name="fabricOrderEntry_1" id="fabricOrderEntry_1">
					<div style="width:975px; float:left;" align="center">
						<fieldset style="width:970px;">
							<table width="920" align="center" border="0">
								<tr>
									<td align="right" colspan="3"><strong>Sales Order No</strong></td>
									<td width="190">
										<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes"
										style="width:150px;" placeholder="Double Click To Edit"
										onDblClick="openmypage_jobNo()" readonly/>
									</td>
								</tr>
								<tr>
									<td width="110" class="must_entry_caption">Company</td>
									<td width="190">
										<?
										echo create_drop_down("cbo_company_id", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', 0, "load_drop_down( 'requires/fabric_sales_order_entry_inter_company_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/fabric_sales_order_entry_inter_company_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value,'process_loss_method' ,'requires/fabric_sales_order_entry_inter_company_controller'); active_inactive(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_sales_order_entry_inter_company_controller' )", '', '', '', '', '');
										?>
									</td>
									<td width="110" class="must_entry_caption">Within Group</td>
									<td>
										<?
										echo create_drop_down("cbo_within_group", 162, $yes_no, "", 0, "--  --", 0, "active_inactive();load_drop_down( 'requires/fabric_sales_order_entry_inter_company_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+'_80', 'load_drop_down_buyer', 'buyer_td' );");
										?>
									</td>
									<td width="110" class="must_entry_caption">Sales/Booking No.</td>
									<td>
										<?php
										$booking_print_arr = array();
										$booking_print_sql = sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
										foreach ($booking_print_sql as $print_id) {
											$booking_print_arr[$print_id[csf('report_id')]] = (int)$print_id[csf('format_id')];
										}
										unset($booking_print_sql);
										?>
										<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
										style="width:150px;" placeholder="Double Click To Import"
										onDblClick="openmypage_fabricBooking()" maxlength="30"
										title="Maximum Characters 30" readonly/>

										<span style="font-size:20px !important; cursor: pointer;"
										onClick="generate_worder_report(document.getElementById('txt_booking_type').value,document.getElementById('txt_booking_no').value, document.getElementById('cbo_company_id').value,document.getElementById('txt_order_id').value,document.getElementById('txt_item_category').value,document.getElementById('txt_fabric_source').value,document.getElementById('txt_hidden_job_no').value,document.getElementById('txt_is_approved').value,document.getElementById('txt_booking_entry_form').value,document.getElementById('txt_is_short').value,'show_fabric_booking_report_urmi')" title="Print Booking Details">⎙</span>
										<input type="hidden" id="txt_hdn_booking_no">
										<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id">
										<input type="hidden" name="txt_item_category" id="txt_item_category">
										<input type="hidden" name="txt_fabric_source" id="txt_fabric_source">
										<input type="hidden" name="txt_hidden_job_no" id="txt_hidden_job_no">
										<input type="hidden" name="txt_is_approved" id="txt_is_approved">
										<input type="hidden" name="txt_action_name" id="txt_action_name">
										<input type="hidden" name="txt_is_short" id="txt_is_short">
										<input type="hidden" name="txt_booking_type" id="txt_booking_type">
										<input type="hidden" name="txt_order_id" id="txt_order_id">
										<input type="hidden" name="txt_booking_entry_form" id="txt_booking_entry_form">
										<input type="hidden" name="booking_without_order" id="booking_without_order">
									</td>
								</tr>
								<tr>
									<td class="must_entry_caption">Booking Date</td>
									<td>
										<input type="text" name="txt_booking_date" id="txt_booking_date" class="datepicker"
										style="width:150px;" value="<? echo date("d-m-Y"); ?>" readonly disabled/>
									</td>
									<td>Delivery Date</td>
									<td>
										<input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker"
										style="width:150px;" placeholder="Display" readonly disabled/>
									</td>
									<td>Receive Date</td>
									<td>
										<input type="text" name="booking_approval_date" id="booking_approval_date" class="datepicker"
										style="width:150px;" readonly disabled/>
									</td>									
								</tr>
								<tr>
									<td class="must_entry_caption">Location</td>
									<td id="location_td">
										<?
										echo create_drop_down("cbo_location_name", 162, $blank_array, "", 1, "-- Select Location --", 0, "");
										?>
									</td>
									<td>Buyer/Unit</td>
									<td id="buyer_td">
										<?
										echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "", 1);
										?>
									</td>
									<td class="must_entry_caption">Style Ref.</td>
									<td>
										<input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes"
										style="width:150px;" maxlength="30" title="Maximum Characters 30"
										placeholder="Display" readonly/>
									</td>									
								</tr>
								<tr>
									<td class="must_entry_caption">Currency</td>
									<td>
										<?
										echo create_drop_down("cbo_currency", 162, $currency, "", 1, "-- Select Currency --", 0, "", 1);
										?>
									</td>
									<td>Team Leader</td>
									<td>
										<?
										echo create_drop_down("cbo_team_leader", 162, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select Team Leader--", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_inter_company_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_dealing_merchant', 'team_td' );", 1);
										?>
									</td>
									<td>Dealing Merchant</td>
									<td id="team_td">
										<?
										echo create_drop_down("cbo_dealing_merchant", 162, $blank_array, "", 1, "-- Select Team Member --", $selected, "");
										?>
									</td>
								</tr>
								<tr>
									<td>Ship Mode</td>
									<td>
										<?
										echo create_drop_down("cbo_ship_mode", 162, $shipment_mode, "", 0, "", 1, "");
										?>
									</td>
									<td>Season</td>
									<td id="season_td">
										 
                                        <?
                                        echo create_drop_down("txt_season", 162, "select id,season_name from lib_buyer_season ", "id,season_name", 1, "Select Season", 0, "",1); ?>
									</td>
									<td>&nbsp;</td>
									<td colspan="2">
										<? 
										include("../terms_condition/terms_condition.php");
										terms_condition(109,'txt_job_no','../');
										?>
									</td>									
								</tr>
								<tr> 
									<td>Remarks</td>
									<td colspan="3">
										<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
										style="width:455px;" maxlength="500" title="Maximum Characters 500"/>
									</td>
									<td></td>
									<td>
										<input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE"
										onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry', 0 ,1)">
									</td>
								</tr>
								<tr>
									<td align="">Ready To Approved</td>  
                                    <td align="left" height="10">
                                      <?
                                            echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", 2, "","","" );
                                       ?>
                                    </td>
									<td colspan="3"></td>
									<td>
										<input type="button" class="image_uploader" style="width:140px" value="ADD FILE"
										onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry', 2 ,1)">
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
					<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div id="list_change_booking_nos"
					style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div align="left">
						<fieldset style="width:1459px; margin-top:10px">
							<legend>Fabric Details</legend>
							<table cellpadding="0" cellspacing="0" width="1459" class="rpt_table" border="1" rules="all"
							id="tbl_item_thead">
							<thead>
								<th width="25">SL</th>
								<th width="82">Garment Item</th>
								<th width="82">Body Part</th>
								<th width="72">Color Type</th>
								<th width="152" class="must_entry_caption">Fabric Description</th>
								<th width="57" class="must_entry_caption">Fabric GSM</th>
								<th width="52" class="must_entry_caption">Fabric Dia</th>
								<th width="82">Dia/ Width Type</th>
								<th width="87">Color</th>
								<th width="82">Color Range</th>
								<th width="52">Cons. UOM</th>
								<th width="67">Booking Qty.</th>
								<th width="57" class="must_entry_caption">Avg. Price</th>
								<th width="72" class="must_entry_caption">Amount</th>
								<th width="52">UOM</th>
								<th width="67" class="must_entry_caption">Finsh Qty.</th>
								<th width="52">Process <br>Loss %</th>
								<th width="67">Grey Qty.</th>
								<th width="82">Work Scope</th>
								<th width="100">Remarks</th>
								<th></th>
							</thead>
						</table>
						<div style="width:1549px; max-height:260px; overflow-y:scroll;" id="list_container_batch"
						align="left">
						<table cellpadding="0" cellspacing="0" width="1529" class="rpt_table" border="1" rules="all"
						id="tbl_item_details">
						<tbody id="order_details_container">
							<tr class="general" id="tr_1">
								<td width="25" id="slTd_1">
									<span>1</span>
									<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboGarmItemId_1", 80, $garments_item, "", 1, "- Select -", $row[csf('item_number_id')], "", "1", "", "", "", "", "", "", "garmItemId[]");
									?>
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboBodyPart_1", 80, $body_part, "", 1, "- Select -", 0, "", "1", "", "", "", "", "", "", "cboBodyPart[]");
									?>
								</td>
								<td width="72">
									<?
									echo create_drop_down("cboColorType_1", 70, $color_type, "", 1, "- Select -", 0, "", "1", "", "", "", "", "", "", "cboColorType[]");
									?>
								</td>
								<td width="152">
									<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_1" class="text_boxes"
									style="width:140px" placeholder="Double Click To Search"
									onDblClick="openmypage_fabricDescription(1)" disabled="disabled" readonly/>
									<input type="hidden" name="fabricDescId[]" id="fabricDescId_1" class="text_boxes">
								</td>
								<td width="57">
									<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_1" class="text_boxes"
									style="width:45px"
									onKeyUp="calculate_fin_qty(1);copy_process_loss(1);"
									disabled="disabled"/>
								</td>
								<td width="52">
									<input type="text" name="txtFabricDia[]" id="txtFabricDia_1" class="text_boxes"
									style="width:40px"
									onKeyUp="calculate_fin_qty(1);copy_process_loss(1);"
									disabled="disabled"/>
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboDiaWidthType_1", 80, $fabric_typee, "", 1, "-- Select --", 0, "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
									?>
								</td>
								<td width="87">
									<input type="text" name="txtColor[]" id="txtColor_1" class="text_boxes"
									style="width:75px" placeholder="Display" readonly/>
									<input type="hidden" name="colorId[]" id="colorId_1" class="text_boxes">
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboColorRange_1", 80, $color_range, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "cboColorRange[]");
									?>
								</td>
								<td width="52">
									<?
									echo create_drop_down("cboConsUom_1", 50, $unit_of_measurement, "", 0, "", 12, "refresh_fields(1)", "1", "12,27,23", "", "", "", "", "", "cboConsUom[]");
									?>
								</td>
								<td width="67">
									<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_1" class="text_boxes_numeric"
									style="width:55px"
									onKeyUp="calculate_amount(1); calculate_fin_qty(1);copy_process_loss(1);"
									readonly/>
								</td>
								<td width="57">
									<input type="text" name="txtAvgRate[]" id="txtAvgRate_1" class="text_boxes_numeric"
									style="width:45px" onKeyUp="calculate_amount(1);" readonly/>
								</td>
								<td width="72">
									<input type="text" name="txtAmount[]" id="txtAmount_1" class="text_boxes_numeric"
									style="width:60px" readonly/>
								</td>
								<td width="52">
									<?
									echo create_drop_down("cboUom_1", 50, $unit_of_measurement, "", 0, "", 12, "", "1", "12,27,23", "", "", "", "", "", "cboUom[]");
									?>
								</td>
								<td width="67">
									<input type="text" name="txtFinishQty[]" id="txtFinishQty_1"
									class="text_boxes_numeric" style="width:55px"
									onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);"
									readonly/>
									<input type="hidden" name="rmgQty[]" id="rmgQty_1" readonly/>
								</td>
								<td width="52">
									<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_1"
									class="text_boxes_numeric" style="width:40px"
									onKeyUp="calculate_grey_qty(1)"/>
								</td>
								<td width="67">
									<input type="text" name="txtGreyQty[]" id="txtGreyQty_1" class="text_boxes_numeric"
									style="width:55px" readonly/>
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboWorkScope_1", 80, $item_category, "", 1, "-- Select --", 2, "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
									?>
								</td>
								<td width="100">
									<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes"
									style="width:90px" readonly/>
								</td>
								<td>
									<input type="button" id="increase_1" name="increase[]" style="width:27px"
									class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"/>
									<input type="button" id="decrease_1" name="decrease[]" style="width:27px"
									class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
									<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes" readonly/>							
								</td>
							</tr>

						</tbody>
						<tfoot>
							<tr style="font-weight: bold;">
								<td align="right" colspan="13">Total</td>
								<td align="right" id="total_amnt"></td>
								<td colspan="7"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<table width="1340">
				<tr>
					<td colspan="17" align="center" class="button_container">
						<?
						$date = date('d-m-Y');
						echo load_submit_buttons($permission, "fnc_fabric_sales_order_entry", 0, 0, "reset_form('fabricOrderEntry_1*fabricOrderEntry_2','yarn_details_list_view','','','disable_enable_fields(\'cbo_company_id*cbo_within_group\');active_inactive();','')", 1);



						?>
						<input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();"
						style="visibility: hidden;"/>
						<input id="print1" class="formbutton_disabled" type="button" style="width:80px; display:none;" onClick="fnc_fabric_sales_order_entry(4)" name="print1" value="Print">

						<input type="button" name="print_2" class="formbuttonplasminus formbutton_disabled" value="Print 2" id="print_2" onClick="fabric_sales_order_print3();" />
						<input type="hidden" name="process_loss_method" id="process_loss_method" readonly>
						<input type="hidden" name="color_from_library" id="color_from_library" readonly>
						<input type="hidden" name="update_id" id="update_id"/>
						<input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
						<input type="hidden" name="deletedDtlsIds" id="deletedDtlsIds" readonly/>
					</td>
				</tr>
			</table>
		</div>
	</form>

	<form name="fabricOrderEntry_2" id="fabricOrderEntry_2">
		<fieldset style="width:740px; margin-top:10px">
			<legend>Grey Qty. For Yarn Details Entry</legend>
			<table class="rpt_table" border="1" width="675" cellpadding="0" cellspacing="0" rules="all"
			id="table_yarn_details">
			<thead>
				<th width="400">Fabric Description</th>
				<th width="100">Fabric GSM</th>
				<th class="must_entry_caption">Grey Quantity</th>
			</thead>
			<tbody id="yarn_details_list_view"></tbody>
		</table>
		<table>
			<tr>
				<td width="100%" align="center" colspan="3">
					<? echo load_submit_buttons($_SESSION['page_permission'], "fnc_fabric_yarn_dtls_entry", 0, 0, "reset_form('','','','','')", 2); ?>
				</td>
			</tr>
		</table>
	</fieldset>

</form>
</fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>