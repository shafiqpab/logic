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

/*echo "<pre>";
print_r($_SESSION['logic_erp']['mandatory_field'][109] );
echo "</pre>";
die;
*/
// $checkMandatory = sql_select("SELECT id, field_id, is_mandatory FROM mandatory_field where page_id=109 and status_active=1 and is_deleted=0");

$checkMandatory = $_SESSION['logic_erp']['mandatory_field'][109];

  //var_dump($checkMandatory);
  //die;

	if($checkMandatory[2] == 'Main Process')
	{
		$isMainProcessMandatory=1;
	}
	else
	{
		$isMainProcessMandatory=0;
	}

	if($checkMandatory[3] == 'Sub Process')
	{
		$isSubProcessMandatory=1;
	}
	else
	{
		$isSubProcessMandatory=0;
	}

?>
<script>

	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	var isMainProcessMandatory = '<? echo $isMainProcessMandatory; ?>';
	var isSubProcessMandatory = '<? echo $isSubProcessMandatory; ?>';

	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][109] );
    //echo "<pre>";print_r($data_arr);echo "</pre>";
	echo "var field_level_data= ". $data_arr . ";\n";

	?>

	function openmypage_fabricBooking() {
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}
		else {
			var title = 'Booking Selection Form';
			var page_link = 'requires/fabric_sales_order_entry_controller.php?cbo_company_id=' + cbo_company_id + '&action=fabricBooking_popup';

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
                $('#txt_attention').val(data[21]);
                $('#txt_fab_comp').val(data[22]);
                $('#cbo_cust_buyer_name').val(data[23]);

                $('#txt_action_name').val(data[14]);
                $('#txt_internal_ref').val(data[24]);
                $('#txt_internal_ref').attr('title', data[25]);
                $('#hdn_buyer_brand_id').val(data[26]);
                $('#txt_buyer_brand').val(data[27]);

                load_drop_down('requires/fabric_sales_order_entry_controller', document.getElementById('cbo_within_group').value + '_' + data[7], 'load_drop_down_dealing_merchant', 'team_td');

                $('#cbo_dealing_merchant').val(data[8]);
                $('#is_apply_last_update').val(0);

                show_list_view(data[1], 'show_fabric_details', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');

                var rowCount = $('#tbl_item_details tbody tr').length;
                for (var i = 1; i <=rowCount; i++) {
                   calculate_amount(i); calculate_grey_qty(i);
                }
                total_amount_cal();
				total_fin_grey_cal();
                if(data[15] == ""){
                	$("#txt_style_ref").removeAttr("readonly","readonly");
                }
            }
        }
    }

    function pending_booking_data_dtls(data_str, is_approved)
    {

        if (is_approved == 1)
        {
            //$('#hidden_booking_data').val(data);
           // parent.emailwindow.hide();
        }
        else if (is_approved == 3)
        {
            var response=return_global_ajax_value( data_str, 'check_approvl_necessity_setup', '', 'requires/fabric_sales_order_entry_controller');
            if(response==1)
            {
                //$('#hidden_booking_data').val(data);
                //parent.emailwindow.hide();
            }
            else
            {
                alert("Approved Booking First.");
                return;
            }
        }
        else
        {
            alert("Approved Booking First.");//FOUND
            return;
        }

        var booking_data = data_str;  //Access form field with id="emailfield"
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
        $('#txt_attention').val(data[21]);
        $('#txt_fab_comp').val(data[22]);
        $('#cbo_cust_buyer_name').val(data[23]);

        $('#txt_action_name').val(data[14]);

        load_drop_down('requires/fabric_sales_order_entry_controller', document.getElementById('cbo_within_group').value + '_' + data[7], 'load_drop_down_dealing_merchant', 'team_td');

        $('#cbo_dealing_merchant').val(data[8]);
        $('#is_apply_last_update').val(0);

        show_list_view(data[1], 'show_fabric_details', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');
        total_amount_cal();
        total_fin_grey_cal();
        if(data[15] == ""){
            $("#txt_style_ref").removeAttr("readonly","readonly");
        }
    }

    function active_inactive() {

		// var response=trim(return_global_ajax_value( $('#cbo_company_id').val(), 'is_validate', '', 'requires/fabric_sales_order_entry_controller'));
		// if(response == "editable")
		// {
		// 	$('#editableId').val(1);

		// }
		// else
		// {
		// 	$('#editableId').val(0);

		// }

    	reset_form('', 'approval_msg_td', 'txt_booking_no*txt_booking_no_id*txt_delivery_date*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*txt_season*cboBodyPart_1*cboColorType_1*txtFabricDesc_1*fabricDescId_1*txtFabricGsm_1*txtFabricDia_1*cboDiaWidthType_1*txtColor_1*colorId_1*txtFinishQty_1*txtAvgRate_1*txtAmount_1*txtProcessLoss_1*txtGreyQty_1*cboWorkScope_1*updateIdDtls_1*txtBookingQnty_1', '', '$(\'#tbl_item_details tbody tr:not(:first)\').remove();', '');

    	var within_group = $('#cbo_within_group').val();
    	var color_from_library = $('#color_from_library').val();
    	$('#txt_booking_date').val('<? echo date("d-m-Y"); ?>');
    	$('#is_apply_last_update').val(0);
    	$('#cboUom_1').val(12);


		//var datas = $('#editableId').val();
    	if (within_group == 1) {

            $("#list_change_booking_nos").css("display", "block");
    		$("#list_change_pending_booking_nos").css("display", "block");
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
    		//$('#cboUom_1').attr('disabled', 'disabled');
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
    		$("#list_change_pending_booking_nos").css("display", "none");
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
    		load_drop_down('requires/fabric_sales_order_entry_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'buyer_td');
            load_drop_down('requires/fabric_sales_order_entry_controller', within_group + '_' + company_id, 'load_drop_down_cust_buyer', 'cust_buyer_td');
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
    			$('#cboUom_' + i).val('12');
    			$('#txtFinishQty_' + i).val('');
    			$('#txtBookingQnty_' + i).val('');
    			$('#txtAvgRate_' + i).val('');
    			$('#txtAmount_' + i).val('');
    			$('#txtProcessLoss_' + i).val('');
    			$('#txtGreyQty_' + i).val('');
    			$('#txtGreyQtyBeforeAdjust_' + i).val('');
    			$('#cboWorkScope_' + i).val('');
                $('#txtProcessNameMain_' + i).val('');
                $('#txtProcessIdMain_' + i).val('');
                $('#txtProcessSeqMain_' + i).val('');
                $('#txtProcessSeqSub_' + i).val('');
    			$('#txtProcessName_' + i).val('');
    			$('#txtProcessId_' + i).val('');
    			$('#txtProcessSeq_' + i).val('');
    			$('#updateIdDtls_' + i).val('');

    			$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
    			$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
    			$('#tr_' + i).find("td:eq(0) span").text(i);
    			$('#tr_' + i).find('#txtSerial_' + i).val(i);
    			$('#txtSerial_' + i).val(i);

    			$('#txtFabricDesc_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_fabricDescription(" + i + ");");
    			$('#txtFinishQty_' + i).removeAttr("onblur").attr("onblur", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");");
    			$('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_fin_qty(" + i + ");copy_process_loss(" + i + ");");
    			$('#txtAvgRate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
    			$('#txtProcessLoss_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");");
    			$('#txtFabricGsm_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#txtFabricDia_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#cboConsUom_' + i).removeAttr("onchange").attr("onchange", "refresh_fields(" + i + ");");
                $('#txtProcessNameMain_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process_main(" + i + ");");
    			$('#txtProcessName_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process(" + i + ");");
                $('#cboUom_' + i).removeAttr("onchange").attr("onchange", "consumtion_calculate(" + i + ");");
    			$('#cboColorRange_' + i).removeAttr("onchange").attr("onchange", "copy_process_loss(" + i + ");");

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
    	var txt_booking_date = $('#txt_booking_date').val();
    	var txt_delivery_date = $('#txt_delivery_date').val();

		//alert('within_group : '+within_group);return;



    	// check booking no when insert
    	if(within_group == 2){
    		var data = company_id + "*" + booking_no + "*" + buyer_name + "*" + operation + "*" + update_id;
    		var response=trim(return_global_ajax_value( data, 'is_booking_duplicate', '', 'requires/fabric_sales_order_entry_controller'));
    		if(response == "invalid"){
    			alert("Sales/Booking No is duplicate");
    			return;
    		}
    	}

    	if(within_group == 2 && operation == 1){
    		if(booking_no != hdn_booking_no){
    			var data = company_id + "*" + hdn_booking_no + "*" + buyer_name;
    			var response=trim(return_global_ajax_value( data, 'is_booking_used_in_plan', '', 'requires/fabric_sales_order_entry_controller'));
    			if(response == "invalid"){
    				alert("Knitting plan found against Booking no.Booking can not be updated.");
    				return;
    			}
    		}
    	}

        if(within_group == 1 && operation == 1)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_controller'));
            if(response == "invalid" && $('#is_apply_last_update').val() ==0)
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }

		if(within_group == 1 && operation == 0)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_controller'));
            if(response == "invalid" && $('#is_apply_last_update').val() ==0)
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }

		if(within_group == 1 && (operation == 0 || operation == 1)){

			var response=trim(return_global_ajax_value( $('#cbo_company_id').val(), 'is_validate', '', 'requires/fabric_sales_order_entry_controller'));
			//console.log(response);

			if(response == 2)
			{
				var iseditable=1;
			}
			else
			{
				var iseditable=0;
			}
    	}




    	if (operation == 4) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}

    		return;
    	}

        if (operation == 7) {
            var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
            window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
            return;
        }

		if (operation == 6) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_yes_6', true);
    		} else {
				alert("This report generated only within group yes");
				return;
    		}

    		return;
    	}

        if (operation == 8) {

    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $('#txt_hidden_job_no').val() + '*' + $('#txt_order_id').val() + '*' + $('#txt_is_approved').val() + '*' + $('#txt_fabric_source').val() + '*' + $('#txt_item_category').val() +  '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_yes_7', true);
    		} else {
				alert("This report generated only within group yes");
				return;
    		}

    		return;
    	}

        if (operation == 9) {

            var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html()+ '*' + within_group + '*' + $('#cbo_buyer_name').val();

            window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_yes_8', true);

           return;
       }

       if (operation == 10) 
       {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $('#txt_fabric_source').val() + '*' + $('#txt_order_id').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_yes9', true);
    		} else {
    			alert("This report generated only within group yes");
				return;
    		}

    		return;
        }

    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}

    	if ($("#is_approved").val()==1 || $("#is_approved").val()==3) {
    		alert("This Sales Order Is Approved. Update Restricted.");
    		return;
    	}
    	if(hdn_job_no != ""){
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*txt_style_ref*cbo_currency*cbo_sales_order_type', 'Company*Sales/Booking No*Booking Date*Location*Style Ref.*Currency*Sales Type') == false) {
    			return;
    		}
    	}else{
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*cbo_currency*cbo_sales_order_type', 'Company*Sales/Booking No*Booking Date*Location*Currency*Sales Type') == false) {
    			return;
    		}
    	}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][109]); ?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][109]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][109]); ?>')==false) {return;}
		}

		if(within_group == 2 )
        {
			if (form_validation('txt_delivery_date', 'Delivery Date') == false) {
    			return;
    		}

            var delivery_date=txt_delivery_date.split("-");
            var booking_date=txt_booking_date.split("-");
            var delivery_date = new Date(parseInt(delivery_date[2], 10), parseInt(delivery_date[1], 10) - 1 , parseInt(delivery_date[0]), 10).getTime();
            var booking_date = new Date(parseInt(booking_date[2], 10), parseInt(booking_date[1], 10) - 1 , parseInt(booking_date[0]), 10).getTime();

            // alert(delivery_date +"<" +booking_date);
            if( delivery_date < booking_date)
			{
				alert("Delivery date must be greather than or equal booking date.");
    			return;
			}
		}


        var blank_finish_count = 0;
        var blank_grey_count = 0;
        var k = 0;
    	var j = 0;
    	var dataString = '';
    	//var mendatory_field_validation = 1;

    	var finishQty = "";
    	$("#tbl_item_details").find('tbody tr').each(function () {
    		var x = $(this).find('input[name="txtSerial[]"]').val();
    		var cboGarmItemId = $(this).find('select[name="cboGarmItemId[]"]').val();
    		var cboBodyPart = $(this).find('select[name="cboBodyPart[]"]').val();
    		var cboColorType = $(this).find('select[name="cboColorType[]"]').val();
    		var txtFabricDesc = encodeURIComponent($(this).find('input[name="txtFabricDesc[]"]').val());
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
    		var txtGreyQtyBeforeAdjust = $(this).find('input[name="txtGreyQtyBeforeAdjust[]"]').val();
    		var cboWorkScope = $(this).find('select[name="cboWorkScope[]"]').val();
    		var cboUom = $(this).find('select[name="cboUom[]"]').val();
    		var cboConsUom = $(this).find('select[name="cboConsUom[]"]').val();
    		var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
    		var txtRemarks = encodeURIComponent($(this).find('input[name="txtRemarks[]"]').val());
    		var txtProcessName = $(this).find('input[name="txtProcessName[]"]').val();
    		var txtProcessId = $(this).find('input[name="txtProcessId[]"]').val();
    		var txtProcessSeq = $(this).find('input[name="txtProcessSeq[]"]').val();

            var txtProcessNameMain = $(this).find('input[name="txtProcessNameMain[]"]').val();
            var txtProcessIdMain = $(this).find('input[name="txtProcessIdMain[]"]').val();
            var txtProcessSeqMain = $(this).find('input[name="txtProcessSeqMain[]"]').val();
            var txtProcessSeqSub = $(this).find('input[name="txtProcessSeqSub[]"]').val();
			var txtAdjustQnty = $(this).find('input[name="txtAdjustQnty[]"]').val();
            var txtAdjustQntyString = $(this).find('input[name="txtAdjustQntyString[]"]').val();

    		var rmgQty = $(this).find('input[name="rmgQty[]"]').val();
    		var pre_cost_fabric_cost_dtls_id = $(this).find('input[name="pre_cost_fabric_cost_dtls_id[]"]').val();
    		var booking_qnty_by_uom = $(this).find('input[name="txtBookingQnty[]"]').val();

    		var finishQty = $('#txtFinishQty_'+x).val();
    		if (finishQty * 1 > 0) {
    			var fabricDesc = $('#txtFabricDesc_'+x).val();
    			var fabricGsm = $('#txtFabricGsm_'+x).val();
    			var fabricDia = $('#txtFabricDia_'+x).val();
    			var txtProcessNameMain = $('#txtProcessNameMain_'+x).val();
    			var txtProcessName = $('#txtProcessName_'+x).val();
    			var txtAvgRate = $('#txtAvgRate_'+x).val();
    			var txtAmount = $('#txtAmount_'+x).val();

				if(isMainProcessMandatory == 1)
				{
					if(txtProcessNameMain == "" ){
					k = 1;
					return;
					}
				}

				if(isSubProcessMandatory == 1)
				{
					if(txtProcessName == "" ){
					k = 1;
					return;
					}
				}

				if(iseditable != 1)
				{
					if(txtAvgRate == 0 && txtAmount == 0){
					k = 1;
					return;
					}

				}

				// if((fabricDesc == "" || fabricGsm == "" || fabricDia == "" || cboColorRange == 0 || txtAvgRate == 0 || txtAmount == 0) && k == 0){

				if((fabricDesc == "" || fabricGsm == "" || fabricDia == "" || cboColorRange == 0 || cboBodyPart == 0 ) && k == 0){

					k = 1;
					return;
				}else{
					j++;
				}

    			dataString += '&cboBodyPart' + j + '=' + cboBodyPart + '&cboColorType' + j + '=' + cboColorType + '&txtFabricDesc' + j + '=' + txtFabricDesc + '&fabricDescId' + j + '=' + fabricDescId + '&txtFabricGsm' + j + '=' + txtFabricGsm + '&txtFabricDia' + j + '=' + txtFabricDia + '&cboDiaWidthType' + j + '=' + cboDiaWidthType + '&txtColor' + j + '=' + txtColor + '&colorId' + j + '=' + colorId + '&cboColorRange' + j + '=' + cboColorRange + '&txtFinishQty' + j + '=' + txtFinishQty + '&txtAvgRate' + j + '=' + txtAvgRate + '&txtAmount' + j + '=' + txtAmount + '&txtProcessLoss' + j + '=' + txtProcessLoss + '&txtGreyQty' + j + '=' + txtGreyQty + '&txtGreyQtyBeforeAdjust' + j + '=' + txtGreyQtyBeforeAdjust + '&cboWorkScope' + j + '=' + cboWorkScope + '&updateIdDtls' + j + '=' + updateIdDtls + '&cboUom' + j + '=' + cboUom + '&txtRemarks' + j + '=' + txtRemarks + '&rmgQty' + j + '=' + rmgQty + '&pre_cost_fabric_cost_dtls_id' + j + '=' + pre_cost_fabric_cost_dtls_id + '&cboGarmItemId' + j + '=' + cboGarmItemId + '&booking_qnty_by_uom' + j + '=' + booking_qnty_by_uom + '&cboConsUom' + j + '=' + cboConsUom + '&txtProcessId' + j + '=' + txtProcessId + '&txtProcessSeq' + j + '=' + txtProcessSeq + '&txtProcessIdMain' + j + '=' + txtProcessIdMain + '&txtProcessSeqMain' + j + '=' + txtProcessSeqMain + '&txtProcessSeqSub' + j + '=' + txtProcessSeqSub+ '&txtAdjustQnty' + j + '=' + txtAdjustQnty+ '&txtAdjustQntyString' + j + '=' + txtAdjustQntyString;

                if(txtGreyQty*1==0 && txtAdjustQnty*1==0)
                {
                    blank_grey_count++;
                }

    		}else{
    			//j = 0;
    			//return;
                blank_finish_count++;
    		}
    	});


    		if (k > 0) {
    			alert('You must fill the mendatory fields');
    			return;
    		}

            if (blank_finish_count > 0 && within_group==1) {
                alert('You must fill the finish qty.');
                return;
            }

            if (blank_grey_count > 0 ) {
                alert('Grey/Adjust qty. Can not be zero');
                return;
            }

    	var season_val=$('#txt_season :selected').text();

    	var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_job_no*cbo_company_id*cbo_within_group*txt_booking_no*txt_booking_no_id*txt_booking_date*txt_delivery_date*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*cbo_ship_mode*txt_season*txt_remarks*update_id*process_loss_method*color_from_library*is_apply_last_update*txt_hdn_booking_no*deletedDtlsIds*booking_without_order*booking_approval_date*cbo_ready_to_approved*txt_attention*txt_fab_comp*cbo_sales_order_type*cbo_cust_buyer_name*txt_fso_ref*txt_fso_ref_id*hdn_buyer_brand_id', "../") + dataString + '&total_row=' + j+ '&season_val=' + season_val;

    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_sales_order_entry_Reply_info;
    }

    function fnc_fabric_sales_order_entry_Reply_info() {

    	$("#print1").removeClass( "formbutton_disabled");
    	$("#print1").addClass( "formbutton");
    	$("#print_2").removeClass( "formbutton_disabled");
    	$("#print_2").addClass( "formbutton");
        $("#print_3").removeClass( "formbutton_disabled");
        $("#print_3").addClass( "formbutton");
        $("#print_4").removeClass( "formbutton_disabled");
        $("#print_4").addClass( "formbutton");
        $("#print_5").removeClass( "formbutton_disabled");
        $("#print_5").addClass( "formbutton");
		$("#print_6").removeClass( "formbutton_disabled");
        $("#print_6").addClass( "formbutton");
        $("#print_9").removeClass( "formbutton_disabled");
        $("#print_9").addClass( "formbutton");
        $("#print_10").removeClass( "formbutton_disabled");
        $("#print_10").addClass( "formbutton");
    	if (http.readyState == 4) {
    		var response = trim(http.responseText).split('**');

    		show_msg(response[0]);
    		if (response[0] == 5) {
    			//alert(response[1]);
    			release_freezing();
    			return;
    		}
            if (response[0] == 30) {
                alert(response[1]);
                release_freezing();
                return;
            }
    		if ((response[0] == 0 || response[0] == 1)) {
    			document.getElementById('update_id').value = response[1];
    			document.getElementById('txt_job_no').value = response[2];

    			var color_from_library = $('#color_from_library').val();
    			var cbo_within_group = $('#cbo_within_group').val();
    			var cbo_within_group = $('#cbo_within_group').val();
				var company_id = $('#cbo_company_id').val();

    			$('#cbo_company_id').attr('disabled', 'disabled');
    			$('#cbo_within_group').attr('disabled', 'disabled');

    			show_list_view(response[1] + "**" + color_from_library + "**" + cbo_within_group + "**" + company_id, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');
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
    	if ($("#is_approved").val()==1 || $("#is_approved").val()==3) {
    		alert("This Sales Order Is Approved. Save, Update , Delete Restricted.");
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
    		var cboColorRangeY = $(this).find('select[name="cboColorRangeY[]"]').val();
    		var cboColorTypeY = $(this).find('select[name="cboColorTypeY[]"]').val();
    		var txtGreyQtyY = $(this).find('input[name="txtGreyQtyY[]"]').val();
    		var yarnData = $(this).find('input[name="yarnData[]"]').val();
    		j++;
    		dataString += '&fabricDescIdY' + j + '=' + fabricDescIdY + '&txtFabricGsmY' + j + '=' + txtFabricGsmY + '&cboColorRangeY' + j + '=' + cboColorRangeY + '&cboColorTypeY' + j + '=' + cboColorTypeY + '&txtGreyQtyY' + j + '=' + txtGreyQtyY + '&yarnData' + j + '=' + yarnData;

    	});

    	if (j < 1) {
    		alert('No data');
    		return;
    	}


        if($('#cbo_within_group').val() == 1)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_controller'));
            if(response == "invalid")
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }

    	var data = "action=save_update_delete_yarn&operation=" + operation + get_submitted_data_string('txt_job_no*update_id', "../") + dataString + '&total_row=' + j;

    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_controller.php", true);
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
                
                var datas = return_global_ajax_value(response[1], 'yarn_details', '', 'requires/fabric_sales_order_entry_controller');
                var yarn_datas = trim(datas).split("##")
                $('#yarn_details_list_view').html(yarn_datas[0]);

                show_change_bookings();
    		}
           
    		release_freezing();
    	}
    }

    function fabric_sales_order_print3() {
    	var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
    	window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
    	return;
    }

    function fabric_sales_order_print_kds2() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html() + '*' + $('#cbo_within_group').val();
        window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_kds2', true);
        return;
        /*if (within_group == 1) {
            window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_kds2', true);
        } else {
            window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
        }*/
    }

    function fabric_sales_order_print4() {
		// freeze_window();
        // var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html() + '*' + $('#update_id').val();
        // window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print4', true);
        // return;

        freeze_window();
		var data="action=fabric_sales_order_print4"+'&companyId='+$('#cbo_company_id').val()+'&bookingId='+$('#txt_booking_no_id').val()+'&bookingNo='+$('#txt_booking_no').val()+'&salesOrderNo='+$('#txt_job_no').val()+'&formCaption='+$("div.form_caption").html()+'&update_id='+$('#update_id').val();
		http.open("POST","requires/fabric_sales_order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fabric_sales_order_print4_reponse;
    }

	function fabric_sales_order_print4_reponse(){
    	if(http.readyState == 4){
            release_freezing();
    		var file_data=http.responseText.split("****");
    		//alert(file_data);
    		$('#data_panel').html(file_data[1]);
    		$('#print_report_Excel').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
    		document.getElementById('print_report_Excel').click();

    		var w = window.open("Surprise", "_blank");
    		var d = w.document.open();
    		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
    		d.close();
    	}
    }

    function fabric_sales_order_print5() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
        var within_group=$('#cbo_within_group').val()
            if (within_group == 2) {
                window.open("requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print5', true);
            } else {
                alert("This report available for Within Group No");
            }

            return;
    }

    function func_send_mail()
    {
        var title = 'Send Mail Info';
        var page_link = 'requires/fabric_sales_order_entry_controller.php?action=send_mail_popup';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=200px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose=function()
        {
            //var test_mail=this.contentDoc.getElementById("test_mail");
            //document.getElementById('test_mail').value=test_mail.value;
        }
    }

    function openmypage_fabricDescription(i) {
    	var title = 'Fabric Description Info';
    	var page_link = 'requires/fabric_sales_order_entry_controller.php?action=fabricDescription_popup';

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
    	var page_link = 'requires/fabric_sales_order_entry_controller.php?action=color_popup';

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

        var fabric_Desc_IdY = $('#fabricDescIdY_' + i).val();
        var txt_Fabric_GsmY = $('#txtFabricGsmY_' + i).val();
        var cbo_Color_Range = $('#cboColorRangeY_' + i).val();
        var cbo_Color_type = $('#cboColorTypeY_' + i).val();
        var update_id = $('#update_id').val();


    	if (form_validation('cbo_company_id', 'Company') == false) {
    		return;
    	}

    	var title = 'Yarn Details Info';
    	var page_link = 'requires/fabric_sales_order_entry_controller.php?action=yarnDetails_popup&cbo_company_id=' + cbo_company_id + '&txtGreyQty=' + txtGreyQty + '&yarnData=' + yarnData + '&txtFabricDesc=' + txtFabricDesc + '&fabric_Desc_IdY=' + fabric_Desc_IdY + '&txt_Fabric_GsmY=' + txt_Fabric_GsmY + '&cbo_Color_Range=' + cbo_Color_Range + '&cbo_Color_type=' + cbo_Color_type + '&update_id=' + update_id;

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1310px,height=470px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var yarn_data = this.contentDoc.getElementById("hidden_yarn_data").value;

    		$('#yarnData_' + i).val(yarn_data);
    	}
    }

    function copy_process_loss(i)
	{
    	var row_num = $('#tbl_item_details tbody tr').length;
    	var process_loss = document.getElementById('txtProcessLoss_' + i).value;
        var deter_id = document.getElementById('fabricDescId_' + i).value;
        var txtColor = document.getElementById('txtColor_' + i).value;
    	var cboColorRange = document.getElementById('cboColorRange_' + i).value;

        var copy_color_wise=$("#copy_color_wise").is(":checked");
        var copy_to_all=$("#copy_to_all").is(":checked");
        var copy_fab_wise=$("#copy_fab_wise").is(":checked");

        if(copy_color_wise || copy_to_all)
        {
            $("#tbl_item_details").find('tbody tr').each(function () {
                var x = $(this).find('input[name="txtSerial[]"]').val();
                if(x >= i)
                {
                    var txtColor_check = document.getElementById('txtColor_' + x).value;

                    if(copy_color_wise)
                    {
                        if (txtColor == txtColor_check) {
                            $('#cboColorRange_' + x).val(cboColorRange);
                            $('#txtProcessLoss_' + x).val(process_loss);
                            calculate_grey_qty(x);
                        }
                    }

                    if(copy_to_all)
                    {
                        $('#cboColorRange_' + x).val(cboColorRange);
                        $('#txtProcessLoss_' + x).val(process_loss);
                        calculate_grey_qty(x);
                    }
                }
            });
        }
        else if(copy_fab_wise)
        {
            $("#tbl_item_details").find('tbody tr').each(function () {
                var x = $(this).find('input[name="txtSerial[]"]').val();
                if(x >= i)
                {
                    var deter_id_check = document.getElementById('fabricDescId_' + x).value;
                    if (deter_id == deter_id_check) {
                        $('#txtProcessLoss_' + x).val(process_loss);
                        calculate_grey_qty(x);
                    }
                }
            });
        }
    }

    function calculate_grey_qty(i)
    {
        var process_loss_method = $('#process_loss_method').val() * 1;
        var txt_booking_entry_form = $('#txt_booking_entry_form').val() * 1;
     	var cbo_within_group = $('#cbo_within_group').val() * 1;
        var textile_sales_maintain = $('#textile_sales_maintain').val();
        var process_loss_editable_maintain = $('#process_loss_editable_maintain').val();

     	var finish_qty = $('#txtFinishQty_' + i).val() * 1;
     	var processLoss = $('#txtProcessLoss_' + i).val() * 1;

		var txtAdjustQnty = $('#txtAdjustQnty_' + i).val() * 1;

     	var grey_qnty = 0;
     	if (finish_qty <= 0 || processLoss <= 0)
        {
     		grey_qnty = finish_qty;
        }
        else
        {
            /*if(cbo_within_group ==1 && txt_booking_entry_form ==108 && textile_sales_maintain==1 && process_loss_method == 1)
            {
                grey_qnty = finish_qty + ((finish_qty / 100) * processLoss);
            }
            else
            {
                if (process_loss_method == 1)
                {
                    grey_qnty = finish_qty + ((finish_qty / 100) * processLoss);
                }
                else
                {
                    var perc = 1 - (processLoss / 100);
                    grey_qnty = finish_qty / perc;
                }
            }*/

            if(cbo_within_group ==1 && textile_sales_maintain==1 && process_loss_editable_maintain==0)
            {
                //will not change
				//grey_qnty = $('#txtGreyQty_' + i).val() * 1;
				grey_qnty = $('#txtGreyQtyBeforeAdjust_' + i).val() * 1;
            }
            else
            {
                if (process_loss_method == 1)
                {
                    grey_qnty = finish_qty + ((finish_qty / 100) * processLoss);
                }
                else
                {
                    var perc = 1 - (processLoss / 100);
                    grey_qnty = finish_qty / perc;
                }
            }


     		grey_qnty = grey_qnty.toFixed(4);
     		//grey_qnty = grey_qnty;
        }

		grey_qnty = grey_qnty- txtAdjustQnty;

        $('#txtGreyQty_' + i).val(grey_qnty);
        total_fin_grey_cal();
    }

 function calculate_fin_qty(i) {
 	var process_loss_method = $('#process_loss_method').val() * 1;
    var textile_sales_maintain = $('#textile_sales_maintain').val()*1;
    var process_loss_editable_maintain = $('#process_loss_editable_maintain').val()*1;
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
 	else
    {
        if(cbo_within_group ==1 && textile_sales_maintain==1 && process_loss_editable_maintain==0)
        {
            //will not change
			grey_qnty = $('#txtGreyQty_' + i).val() * 1;
        }
        else
        {
     		if (process_loss_method == 1) {
     			grey_qnty = qnty_conversion + ((qnty_conversion / 100) * processLoss);
     		}
     		else {
     			var perc = 1 - (processLoss / 100);
     			grey_qnty = qnty_conversion / perc;
     		}
     		grey_qnty = grey_qnty.toFixed(4);
     		//grey_qnty = grey_qnty;
        }
 	}
 	$('#txtFinishQty_' + i).val(qnty_conversion);
 	$('#txtGreyQty_' + i).val(grey_qnty);
	 total_fin_grey_cal();
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
 		amount = amount.toFixed(4);
 	}
 	$('#txtAmount_' + i).val(amount);
 	total_amount_cal();
 }

 function total_amount_cal(){
 	var total_amnt = 0;
 	$("#tbl_item_details").find('tbody tr').each(function () {
 		var txtAmount    = $(this).find('input[name="txtAmount[]"]').val();
 		total_amnt += txtAmount*1;
 	});
 	total_amnt      = total_amnt.toFixed(4);

 	$('#total_amnt').html(total_amnt);
 }

 function total_fin_grey_cal(){

 	var total_FinishQty = 0;
 	var total_GreyQty = 0;
    var total_AdjustQty=0;
 	$("#tbl_item_details").find('tbody tr').each(function () {
 		var txtFinishQty = $(this).find('input[name="txtFinishQty[]"]').val();
 		var txtGreyQty   = $(this).find('input[name="txtGreyQty[]"]').val();
 		var txtAdjustQty   = $(this).find('input[name="txtAdjustQnty[]"]').val();




		total_FinishQty += txtFinishQty*1;
		total_GreyQty += txtGreyQty*1;
		total_AdjustQty += txtAdjustQty*1;
 	});

 	total_FinishQty = total_FinishQty.toFixed(4);
 	total_GreyQty   = total_GreyQty.toFixed(4);
 	total_AdjustQty   = total_AdjustQty.toFixed(4);


 	$('#total_FinishQty').html(total_FinishQty);
 	$('#total_GreyQty').html(total_GreyQty);
 	$('#total_AdjustQty').html(total_AdjustQty);
 }


 function openmypage_jobNo() {
 	var cbo_company_id = $('#cbo_company_id').val();
 	var color_from_library = $('#color_from_library').val();

 	if (form_validation('cbo_company_id', 'Company') == false) {
 		return;
 	}
 	else {
 		var title = 'Job Selection Form';
 		var page_link = 'requires/fabric_sales_order_entry_controller.php?cbo_company_id=' + cbo_company_id + '&action=jobNo_popup';

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
            $("#print_3").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_3").addClass( "formbutton"); //To make enable print to button
            $("#print_4").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_4").addClass( "formbutton"); //To make enable print to button
            $("#print_5").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_5").addClass( "formbutton"); //To make enable print to button
			$("#print_6").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_6").addClass( "formbutton"); //To make enable print to button
            $("#print_7").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_7").addClass( "formbutton"); //To make enable print to butto
            $("#print_8").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_8").addClass( "formbutton"); //To make enable print to butto
            $("#print_9").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_9").addClass( "formbutton"); //To make enable print to butto
            $("#print_10").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_10").addClass( "formbutton"); //To make enable print to butto

            get_php_form_data(job_id+'_'+booking_data[1], "populate_data_from_sales_order", "requires/fabric_sales_order_entry_controller");

            var cbo_within_group = $('#cbo_within_group').val();
			var company_id = $('#cbo_company_id').val();
            show_list_view(job_id + "**" + color_from_library + "**" + cbo_within_group + "**" + company_id, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');

            var rowCount = $('#tbl_item_details tbody tr').length;
            for (var i = 1; i <=rowCount; i++) {
               calculate_amount(i); calculate_grey_qty(i);
            }

            total_amount_cal();
			total_fin_grey_cal();
            show_fabric_yarn_details(job_id);
        }
    }
}

function show_fabric_yarn_details(update_id) {
        //show_list_view(update_id,'yarn_details','yarn_details_list_view','requires/fabric_sales_order_entry_controller','',0);
        var datas = return_global_ajax_value(update_id, 'yarn_details', '', 'requires/fabric_sales_order_entry_controller');
        var yarn_datas = trim(datas).split("##")
        $('#yarn_details_list_view').html(yarn_datas[0]);

        /*var button_status=0;
         //if(parseInt(yarn_datas[1])>1) {button_status=1;}
         if( $('#table_yarn_details tbody tr').length>0){button_status=1;}
         //if( $('#txtGreyQtyY_1').val()*1)>0  {button_status=1;}
         set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry',2); */

         var button_status = 0;
         if (parseInt(yarn_datas[1]) > 0) {
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
     		var approved_data = trim(return_global_ajax_value(txt_booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_controller'));
     		var approved_data_arr=approved_data.split('**');
     		var approved=approved_data_arr[0];
     		if (approved != 1) {

     			var data_for_setup=approved_data_arr[4]+"_"+approved_data_arr[1]+"_"+approved_data_arr[2]+"_"+approved_data_arr[3];
     			var response=return_global_ajax_value( data_for_setup, 'check_approvl_necessity_setup_revised', '', 'requires/fabric_sales_order_entry_controller');
     			if( approved==3){
     				if(response!=1){
     					alert("Approved Booking First.");
     					return;
     				}
     			}
     			else{
     				alert("Approved Booking First.");
     				return;
     			}

     		}

     		freeze_window(5);
     		show_list_view(update_id + '**' + txt_booking_no, 'show_fabric_details_last_update', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');
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
        if (form_validation('cbo_company_id', 'Company') == false)
        {
            return;
        }
        else
        {
     	  show_list_view($('#cbo_company_id').val(), 'show_change_bookings', 'list_change_booking_nos', 'requires/fabric_sales_order_entry_controller', 'setFilterGrid(\'tbl_list_search_revised\',-1);');
        }
     }

     // Pending Bookings List Action
     function show_change_pending_bookings()
     {
        if (form_validation('cbo_company_id', 'Company') == false)
        {
            return;
        }
        else
        {
            show_list_view($('#cbo_company_id').val(), 'show_change_pending_bookings', 'list_change_pending_booking_nos', 'requires/fabric_sales_order_entry_controller', 'setFilterGrid(\'tbl_list_search_pending_booking\',-1);');
        }
     }

     function btn_load_change_bookings(){
     	var count = trim(return_global_ajax_value("", 'btn_load_change_bookings', '', 'requires/fabric_sales_order_entry_controller'));
     	if(count > 0){
     		$("#list_change_booking_nos").html("<span id='btn_span' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_bookings()' type='button' class='formbutton' value='&nbsp;&nbsp;Revised Bookings&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Revised Booking List'></span>");
     	}
        else
     	{
     		$("#list_change_booking_nos").html("<span id='btn_span_disabled' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' type='button' class='formbutton_disabled' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#ccc !important; background-image:none !important;border-color: #ccc;' title='Revised Booking List'></span>");
     	}
        // Pending Bookings Button
        $("#list_change_pending_booking_nos").html("<span id='btn_span2' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_pending_bookings()' type='button' class='formbutton' value='&nbsp;&nbsp;Pending Bookings&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Pending Booking List'></span>");
     	(function blink() {
     		$('#btn_span').fadeOut(900).fadeIn(900, blink);
            $('#btn_span2').fadeOut(900).fadeIn(900, blink);
     	})();
     }

     function set_form_data(data) {
     	var data = data.split("**");
     	var job_id = data[0];
     	var booking_no = data[2];
     	var cbo_company_id = data[1];
		var booking_remarks = data[3];
     	$('#cbo_company_id').val(cbo_company_id);
     	$("#last_update").css("visibility", "visible");

        //eval($('#cbo_company_id').attr('onchange'));
        //$('#cbo_company_id').trigger('onchange');


        var approved_data = trim(return_global_ajax_value(booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_controller'));

        var approved_data_arr=approved_data.split('**');
        var approved=approved_data_arr[0];
        if (approved != 1) {

        	var data_for_setup=approved_data_arr[4]+"_"+approved_data_arr[1]+"_"+approved_data_arr[2]+"_"+approved_data_arr[3];
        	var response=return_global_ajax_value( data_for_setup, 'check_approvl_necessity_setup_revised', '', 'requires/fabric_sales_order_entry_controller');
        	if( approved==3){
        		if(response!=1){
        			alert("Approved Booking First.");
        			return;
        		}
        	}
        	else{
        		alert("Approved Booking First.");
        		return;
        	}

        }
           /* if (approved != 1) {
            	alert("Approved Booking First.");
            	return;
            }*/


            load_drop_down('requires/fabric_sales_order_entry_controller', cbo_company_id, 'load_drop_down_location', 'location_td');
            get_php_form_data(cbo_company_id, 'process_loss_method', 'requires/fabric_sales_order_entry_controller');

            var color_from_library = $('#color_from_library').val();

            get_php_form_data(job_id, "populate_data_from_sales_order", "requires/fabric_sales_order_entry_controller");

            var within_group = $('#cbo_within_group').val();
            var company_id = $('#cbo_company_id').val();
			$('#txt_remarks').val(booking_remarks);
            show_list_view(job_id + "**" + color_from_library + "**" + within_group + "**" + company_id, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');
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

    function generate_worder_report(type, txt_booking_no, cbo_company_name, txt_order_no_id, cbo_fabric_natu, cbo_fabric_source, txt_job_no, id_approved_id, booking_entry_form, is_short, action, revised_no="")
    {
        var within_group = document.getElementById('cbo_within_group').value;
        if (within_group==2)
        {
            alert('Only For Within Group Yes');return;
        }

        var booking_no_sm_smn=txt_booking_no.split('-');
        var booking_sm_smn=booking_no_sm_smn[1];
        if (booking_sm_smn=='SM') // Sample with order
        {
            var booking_entry_form='SM';
        }
        else if(booking_sm_smn=='SMN' && booking_entry_form!=140)// Sample without order
        {
            var booking_entry_form='SMN';
        }
        get_php_form_data(cbo_company_name+'_'+booking_entry_form, 'print_report_setting_action', 'requires/fabric_sales_order_entry_controller');
        var report_print_btn = document.getElementById('print_booking_details').value;
        // alert(report_print_btn);return;

        if (booking_entry_form==86) // Budget Wise Fabric Booking
        {
            if(report_print_btn==73) // Print B6
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==1) // Print GP
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==2) // Print B1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==3) // Print B2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==4) // Print Cut1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report1' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
            }
            else if(report_print_btn==5) // Print Cut2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==6) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==7) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==28) // Print B13
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_akh' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
            }
            else if(report_print_btn==39) // Print Booking2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==45) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==53) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_jk' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
            }
            else if(report_print_btn==78) // Print
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==84) // Print 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==85) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==93) // Print B9
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==129) // Print 5
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==269) // Print B12
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==280) // Print B14
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==304) // Print B15
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==339) // Print B18
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==719) // Print B16
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==723) // Print B17
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
        }
        if (booking_entry_form==118) // Main Fabric Booking V2
        {
            var report_title = 'Main Fabric Booking V2';
            if(report_print_btn==73) // Print B6
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'show_fabric_booking_report_mf' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
            }
            else if(report_print_btn==1) // Print GP
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_mf' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
            }
            else if(report_print_btn==2) // Print B1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==3) // Print B2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==4) // Print Cut1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report1' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
            }
            else if(report_print_btn==5) // Print Cut2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==6) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==7) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==28) // Print B13
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_akh' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
            }
            else if(report_print_btn==39) // Print Booking2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print39' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print39', true);
            }
            else if(report_print_btn==45) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;
                // http.open("POST", "../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==53) // Print B5
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_jk' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
            }
            else if(report_print_btn==78) // Print
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==84) // Print 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_islam' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_islam', true);
            }
            else if(report_print_btn==85) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==93) // Print B9
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_libas' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_libas', true);
            }
            else if(report_print_btn==129) // Print 5---pro
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'show_fabric_booking_report_print5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name='+"'"+cbo_company_name+"'"+'&txt_order_no_id='+"'"+txt_order_no_id +"'"+'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+'&id_approved_id='+"'"+id_approved_id+"'"+'&txt_job_no='+"'"+txt_job_no+"'"+'&report_title='+report_title+'&show_yarn_rate=' + show_yarn_rate+'&path=../';

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?action=" + data + '&action=show_fabric_booking_report_print5', true);
            }
            else if(report_print_btn==193) // Print 4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print4', true);
            }
            else if(report_print_btn==269) // Print B12
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_knit' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_knit', true);
            }
            else if(report_print_btn==280) // Print B14
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print14' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print14', true);
            }
            else if(report_print_btn==304) // Print B15
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report10' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report10', true);
            }
            else if(report_print_btn==339) // Print B18
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report18' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report18', true);
            }
            else if(report_print_btn==719) // Print B16
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report16' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report16', true);
            }
            else if(report_print_btn==723) // Print B17
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report17' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report17', true);
            }
        }
        if (booking_entry_form==88) // Short Fabric Booking
        {
            var report_title = 'Short Fabric Booking';
            if(report_print_btn==8) // Print Booking
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==9) // Print Booking 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==10) // Print Booking
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==46) // Short Fabric Booking Urmi
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==136) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=print_booking_3', true);
            }
            else if(report_print_btn==244) // Fabric For NTG
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_ntg' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_ntg', true);
            }
        }
        if (booking_entry_form==108) // Partial Fabric Booking
        {
            var report_title = 'Partial Fabric Booking';
            if(report_print_btn==84) // Print 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'show_fabric_booking_report_urmi_per_job' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi_per_job', true);
            }
            else if(report_print_btn==85) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_3', true);
            }
            else if(report_print_btn==143) // Print 1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==151) // AAL Print
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'show_fabric_booking_report_advance_attire_ltd' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_advance_attire_ltd', true);
            }
            else if(report_print_btn==160) // Print 4
            {
                var show_yarn_rate='';
                var data = 'action=' + 'print_booking_5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_5', true);
            }
            else if(report_print_btn==175) // Print 5
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_6', true);
            }
            else if(report_print_btn==218) // Northan
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_7', true);
            }
            else if(report_print_btn==220) // Print 8
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_northern_new' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_northern_new', true);
            }
            else if(report_print_btn==235) // Print 9
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_northern_9' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_northern_9', true);
            }
            else if(report_print_btn==241) // Print 11
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_11' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_11', true);
            }
            else if(report_print_btn==280) // Print B14
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_14' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_14', true);
            }
            else if(report_print_btn==304) // Print B15
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }

                var data = 'action=' + 'print_booking_11' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_15', true);
            }
            else if(report_print_btn==269) // Print 12
            {
                var show_yarn_rate='';
                var data = 'action=' + 'print_booking_12' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_12', true);
            }
            else if(report_print_btn==274) // Print 10
            {
                var show_yarn_rate='';
                var r=confirm("Do You Want to Hide Buyer and Style Name?");
                if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";

                var data = 'action=' + 'print_booking_10' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_10', true);
            }
        }
        if (booking_entry_form=='SM') // Sample with order
        {
            var report_title = 'Sample Fabric Booking -With order';
            if(report_print_btn==16) // Print Booking 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_print_booking_3', true);
            }
            else if(report_print_btn==38) // Print Booking
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==39) // Print Booking 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==64) // Metro Print
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
        }
        if (booking_entry_form=='SMN') // Sample without order
        {
            var report_title = 'Sample Fabric Booking -Without order';
            if(report_print_btn==34) // Print 1
            {
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==35) // Print 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==36) // Print 3
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==37) // Print 4
            {
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==64) // Print 5
            {
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==72) // Print 6
            {
                var data = 'action=' + 'show_fabric_booking_report6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report6', true);
            }
            else if(report_print_btn==174) // Print 7
            {
                var data = 'action=' + 'show_fabric_booking_report7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report7', true);
            }
        }
        if (booking_entry_form==140) //Sample Requisi Fabric Booking-Without order
        {
            var report_title = 'Sample Requisition Fabric Booking -Without order';
            if(report_print_btn==61) // Print 1
            {
                var data = 'action=' + 'show_fabric_booking_report_micro' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_requisition_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report_micro', true);
            }
            else if(report_print_btn==10) // Print 2
            {
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_requisition_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==17) // Print 3
            {
                var data = 'action=' + 'show_fabric_booking_report_barnali' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../order/woven_order/requires/sample_requisition_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report_barnali', true);
            }
        }
        return;
    }

    function generate_worder_report____(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short, action, revised_no="")
    {
        get_php_form_data(company_id+'_'+entry_form, 'print_report_setting_action', 'requires/fabric_sales_order_entry_controller');
        var print_button_id = document.getElementById('print_booking_details').value;
        // alert(print_button_id);return;

    	var booking_without_order = document.getElementById('booking_without_order').value;

        if (type==0)
        {
            if (type==1 || type==2  || type==3  || type==6)
            {
                if ($row[("is_short")]==1)
                {
                    $wo_type="Short";
                    $wo_typw_id=1;
                }
                else if (entry_form==108)
                {
                    $wo_type="Partial";
                    $wo_typw_id=5;
                }
                else
                {
                    $wo_type="Main";
                    $wo_typw_id=2;
                }
            }
            else if(type==4)
            {
                $wo_type="Sample With Order";
                $wo_typw_id=3;
            }
        }
        else
        {
            $wo_type="Sample Non Order";
            $wo_typw_id=4;
        }

        if (entry_form==118)  // Main Fabric Booking V2
        {
            if($print_button_id==1)
            {
                var action="show_fabric_booking_report_gr";
            }
            else if ($print_button_id==2)
            {
                var action="show_fabric_booking_report";
            }
            else if ($print_button_id==2)
            {
                var action="show_fabric_booking_report3";
            }
        }
        else if (entry_form==140) //Sampe Req Fab Without Order
        {

        }
        else
        {

        }

        if(print_button_id==4)//sample booking without order
        {
            report_title ='Sample Booking Without Order';
            var data="action=show_fabric_booking_report"+
            '&txt_booking_no='+"'"+booking_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&report_title='+""+report_title+""+
            '&id_approved_id='+"'"+approved+"'";

            http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
        }
        else if(print_button_id==108) // Partial Fabric Booking
        {
            var show_yarn_rate = '';
            var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
            if (r == true) {
                show_yarn_rate = 1;
            }
            else {
                show_yarn_rate = 0;
            }
            var report_title="Partial Fabric Booking";
            var data="action="+action+
            '&txt_booking_no='+"'"+booking_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&txt_order_no_id='+"'"+order_id+"'"+
            '&cbo_fabric_natu='+"'"+fabric_nature+"'"+
            '&cbo_fabric_source='+"'"+fabric_source+"'"+
            '&id_approved_id='+"'"+approved+"'"+
            '&report_title='+""+report_title+""+
            '&show_yarn_rate='+"'"+show_yarn_rate+"'"+
            '&txt_job_no='+"'"+job_no+"'";
            '&path=../';
            //alert(print_button_id);
            http.open("POST","../order/woven_order/requires/partial_fabric_booking_controller.php",true);
        }
        else
        {
            if(print_button_id==140 || print_button_id==139)
            {
                var show_comment = '';
                var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
                if (r == true) {
                    show_comment = "1";
                }
                else {
                    show_comment = "0";
                }
            }
            if(print_button_id==139 || print_button_id==89 || print_button_id==3 || print_button_id==4)
            {
                var report_title = "Sample Fabric Booking -With order ";
            }
            else if(print_button_id==1)
            {

                report_title = "Short Fabric Booking";
            }
            else if(print_button_id==2) //main fabric booking
            {
                report_title = "Main Fabric Booking";
            }
            else if((print_button_id==73) || (print_button_id==93 || print_button_id==269 || print_button_id==28 || print_button_id==45 || print_button_id==53 || print_button_id==93  || print_button_id==193 || print_button_id==719 || print_button_id==723 || print_button_id==383 || print_button_id==304 || print_button_id==426)) //
            {
                report_title = "Main Fabric Booking V2";
            }
            else if(print_button_id==271) //woven Partial main fabric booking
            {

                report_title = "Woven Partial Main Fabric Booking";
            }
            else if(print_button_id==140) //Sample fabric booking Req without order
            {
                //alert(print_button_id);
                report_title = "Sample Fabric Booking Req Without Order";
            }
            else if(print_button_id==139) //Sample fabric booking Req with order
            {
                //alert(print_button_id);
                report_title = "Sample Fabric Booking Req With Order";
            }
            else if(print_button_id==90) //Sample fabric booking withOut order
            {
                //alert(print_button_id);
                report_title = "Sample Fabric Booking WithOut Order";
            }
            else if(print_button_id==89) //Sample fabric booking with order
            {
                //alert(print_button_id);
                report_title = "Sample Fabric Booking With Order";
            }
            else if(print_button_id==88) //Short fabric booking
            {
                //alert(print_button_id);
                report_title = "Short Fabric Booking ";
            }
            else
            {
                report_title ='Sample Booking';
            }


            var data="action="+action+
            '&txt_booking_no='+"'"+booking_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&txt_order_no_id='+"'"+order_id+"'"+
            '&cbo_fabric_natu='+"'"+fabric_nature+"'"+
            '&cbo_fabric_source='+"'"+fabric_source+"'"+
            '&id_approved_id='+"'"+approved+"'"+
            '&report_title='+report_title+
            '&show_comment='+show_comment+
            '&txt_job_no='+"'"+job_no+"'"+
            '&revised_no='+"'"+revised_no+"'";

            '&path=../../';
            // alert(revised_no);
            if(print_button_id==1) //short fabric booking
            {
                http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
            }
            else if(print_button_id==2) //main fabric booking
            {
                http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
            }
            else if((print_button_id==73) || (print_button_id==93 || print_button_id==269 || print_button_id==28 || print_button_id==45 || print_button_id==53 || print_button_id==93  || print_button_id==193 || print_button_id==719 || print_button_id==723 || print_button_id==383  || print_button_id==304 || print_button_id==426)) //main fabric booking v2
            {
                // http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
                http.open("POST", "../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
            }
            /*else if(print_button_id==271 && i=='wvn_p') //woven Partial main fabric booking
            {
                http.open("POST","../woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
            }
            else if(print_button_id==271 && i!='wvn_p') //woven Partial main fabric booking
            {
                http.open("POST","../woven_gmts/requires/partial_fabric_booking_controller.php",true);
            }*/
            else if(print_button_id==140) //Sample fabric booking Req without order
            {
                //alert(print_button_id);
                http.open("POST","../order/woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
            }
            else if(print_button_id==139) //Sample fabric booking Req with order
            {
                //alert(print_button_id);
                http.open("POST","../order/woven_order/requires/sample_requisition_booking_with_order_controller.php",true);
            }
            else if(print_button_id==90) //Sample fabric booking withOut order
            {
                //alert(print_button_id);
                http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
            }
            else if(print_button_id==89 || print_button_id==3 || print_button_id==4) //Sample fabric booking with order
            {
                //alert(print_button_id);
                http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
            }
            else if(print_button_id==88) //Short fabric booking
            {
                //alert(print_button_id);
                http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
            }else if(print_button_id=="719_last_version_details") //last version check
            {
                //alert(print_button_id);
                http.open("POST","requires/wo_or_fabric_booking_report_controller.php",true);
            }
            else
            {
                http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
            }
        }
        // ==================
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = generate_fabric_report_reponse;
    }

    function generate_fabric_report_reponse_____()
    {
    	if (http.readyState == 4)
        {
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
function openmypage_process(incr_id)
{
	var txt_process_id = $('#txtProcessId_'+incr_id).val();
	var txt_process_seq = $('#txtProcessSeq_'+incr_id).val();

	var title = 'Process Name Selection Form';
	var page_link = 'requires/fabric_sales_order_entry_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
		var process_name=this.contentDoc.getElementById("hidden_process_name").value;
		var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;

		$('#txtProcessId_'+incr_id).val(process_id);
		$('#txtProcessName_'+incr_id).val(process_name);
        $('#txtProcessSeq_'+incr_id).val(process_seq);


        var selected_fabric = $('#fabricDescId_'+incr_id).val()*1;
        var selected_color = $('#colorId_'+incr_id).val()*1;
        var selected_color_type = $('#cboColorType_'+incr_id).val()*1;


        if($('#processChk').is(':checked'))
        {
            $("#tbl_item_details").find('tbody tr').each(function()
            {
                var fabricDescVal = $(this).find('input[name="fabricDescId[]"]').val();
                var colorVal = $(this).find('input[name="colorId[]"]').val();
                var colorTypeVal = $(this).find('select[name="cboColorType[]"]').val();

                var fabricDescId = $(this).find('input[name="fabricDescId[]"]').attr("id");
                var fabricDescIdArr = fabricDescId.split("_");

                // copy only that and below data with same fabrication and color
                if(incr_id <=fabricDescIdArr[1]*1)
                {
                    //alert(selected_fabric + '=' + fabricDescVal + ',' + selected_color + '=' + colorVal + 'process_id='+process_id+'name='+process_name+'seq='+process_seq);
                    if(selected_fabric==fabricDescVal && selected_color==colorVal && selected_color_type==colorTypeVal)
                    {
                         $(this).find('input[name="txtProcessId[]"]').val(process_id);
                         $(this).find('input[name="txtProcessName[]"]').val(process_name);
                         $(this).find('input[name="txtProcessSeq[]"]').val(process_seq);
                    }
                }
            });
        }
    }
}

function openmypage_process_main_____________________bk__________(incr_id)
{
    var txt_process_id = $('#txtProcessIdMain_'+incr_id).val();
    var txt_process_seq = $('#txtProcessSeqMain_'+incr_id).val();
    var txtProcessSeqSub = $('#txtProcessSeqSub_'+incr_id).val();

    var title = 'Process Name Selection Form';
    var page_link = 'requires/fabric_sales_order_entry_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&txtProcessSeqSub='+txtProcessSeqSub+'&action=process_name_popup_main';

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
        var process_id=this.contentDoc.getElementById("hidden_process_id").value;    //Access form field with id="emailfield"
        var process_name=this.contentDoc.getElementById("hidden_process_name").value;
        var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;
        var sub_process_seq=this.contentDoc.getElementById("hidden_sub_process_seq").value;
        // alert(sub_process_seq);
        $('#txtProcessIdMain_'+incr_id).val(process_id);
        $('#txtProcessNameMain_'+incr_id).val(process_name);
        $('#txtProcessSeqMain_'+incr_id).val(process_seq);
        $('#txtProcessSeqSub_'+incr_id).val(sub_process_seq);


        var selected_fabric = $('#fabricDescId_'+incr_id).val()*1;
        var selected_color = $('#colorId_'+incr_id).val()*1;
        var selected_color_type = $('#cboColorType_'+incr_id).val()*1;


        if($('#processChkMain').is(':checked'))
        {
            $("#tbl_item_details").find('tbody tr').each(function()
            {
                var fabricDescVal = $(this).find('input[name="fabricDescId[]"]').val();
                var colorVal = $(this).find('input[name="colorId[]"]').val();
                var colorTypeVal = $(this).find('select[name="cboColorType[]"]').val();

                var fabricDescId = $(this).find('input[name="fabricDescId[]"]').attr("id");
                var fabricDescIdArr = fabricDescId.split("_");

                // copy only that and below data with same fabrication and color
                if(incr_id <=fabricDescIdArr[1]*1)
                {
                    //alert(selected_fabric + '=' + fabricDescVal + ',' + selected_color + '=' + colorVal + 'process_id='+process_id+'name='+process_name+'seq='+process_seq);
                    if(selected_fabric==fabricDescVal && selected_color==colorVal && selected_color_type==colorTypeVal)
                    {
                         $(this).find('input[name="txtProcessIdMain[]"]').val(process_id);
                         $(this).find('input[name="txtProcessNameMain[]"]').val(process_name);
                         $(this).find('input[name="txtProcessSeqMain[]"]').val(process_seq);
                         $(this).find('input[name="txtProcessSeqSub[]"]').val(sub_process_seq);
                    }
                }
            });
        }
    }
}

function openmypage_process_main(incr_id)
{
    var txt_process_id = $('#txtProcessIdMain_'+incr_id).val();
    var txt_process_seq = $('#txtProcessSeqMain_'+incr_id).val();
    var txtProcessSeqSub = $('#txtProcessSeqSub_'+incr_id).val();

    var fabConstructionId = $('#fabConstructionId_'+incr_id).val();
    var cboDiaWidthType = $('#cboDiaWidthType_'+incr_id).val();
    var cboColorRange = $('#cboColorRange_'+incr_id).val();

    var cbo_within_group = $('#cbo_within_group').val();
    var cbo_buyer_name = $('#cbo_buyer_name').val();

    var title = 'Process Wise Rate Entry';
    var page_link = 'requires/fabric_sales_order_entry_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&txtProcessSeqSub='+txtProcessSeqSub+'&cbo_within_group='+cbo_within_group+'&cbo_buyer_name='+cbo_buyer_name+'&fabConstructionId='+fabConstructionId+'&cboDiaWidthType='+cboDiaWidthType+'&cboColorRange='+cboColorRange+'&action=process_name_popup_main';

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=370px,center=1,resize=1,scrolling=0','');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
        var process_id=this.contentDoc.getElementById("hidden_process_id").value;    //Access form field with id="emailfield"
        var process_name=this.contentDoc.getElementById("hidden_process_name").value;
        var hidden_process_rate_str=this.contentDoc.getElementById("hidden_process_rate_str").value;
        // alert(sub_process_seq);
        $('#txtProcessIdMain_'+incr_id).val(process_id);
        $('#txtProcessNameMain_'+incr_id).val(process_name);
        $('#txtProcessSeqMain_'+incr_id).val(hidden_process_rate_str);


        var selected_fabric = $('#fabricDescId_'+incr_id).val()*1;
        var selected_color = $('#colorId_'+incr_id).val()*1;
        var selected_color_type = $('#cboColorType_'+incr_id).val()*1;


        if($('#processChkMain').is(':checked'))
        {
            $("#tbl_item_details").find('tbody tr').each(function()
            {
                var fabricDescVal = $(this).find('input[name="fabricDescId[]"]').val();
                var colorVal = $(this).find('input[name="colorId[]"]').val();
                var colorTypeVal = $(this).find('select[name="cboColorType[]"]').val();

                var fabricDescId = $(this).find('input[name="fabricDescId[]"]').attr("id");
                var fabricDescIdArr = fabricDescId.split("_");

                // copy only that and below data with same fabrication and color
                if(incr_id <=fabricDescIdArr[1]*1)
                {
                    //alert(selected_fabric + '=' + fabricDescVal + ',' + selected_color + '=' + colorVal + 'process_id='+process_id+'name='+process_name+'seq='+process_seq);
                    if(selected_fabric==fabricDescVal && selected_color==colorVal && selected_color_type==colorTypeVal)
                    {
                         $(this).find('input[name="txtProcessIdMain[]"]').val(process_id);
                         $(this).find('input[name="txtProcessNameMain[]"]').val(process_name);
                         $(this).find('input[name="txtProcessSeqMain[]"]').val(hidden_process_rate_str);
                    }
                }
            });
        }
    }
}

	function consumtion_calculate(i)
	{
		var within_group    = $('#cbo_within_group').val();
		var cboConsUom      = $('#cboConsUom_'+i).val();
		var txtBookingQnty  = $('#txtBookingQnty_'+i).val();
		var cboUom          = $('#cboUom_'+i).val();
		var txtFabricGsm    = $('#txtFabricGsm_'+i).val();
		var txtFabricDia    = $('#txtFabricDia_'+i).val();
		var finishQnty="";

        if(cboUom==12) //kg
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==23)//mtr
            {
            	var meter_cal=(txtFabricDia*2.54/100);
            	finishQnty=(txtBookingQnty*meter_cal*txtFabricGsm/1000);
            }
            else if (cboConsUom==27)//yds
            {
                //yds to kg formula=
            }
        }
        else if(cboUom==27) //kg
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==23)//mtr
            {
            	var meter_cal=(txtFabricDia*2.54/100);
            	finishQnty=(txtBookingQnty*meter_cal*txtFabricGsm/1000);
            }
            else if (cboConsUom==27)//yds
            {
                //yds to kg formula=
            }
        }
        else if(cboUom==23) //mtr
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=(txtBookingQnty * 1000)/(txtFabricGsm * txtFabricDia * 0.0254);
            }
            else if (cboConsUom==23)//mtr
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==27)//yds
            {
               // yds to mtr
           }
       }
       /* else if(cboUom==27)//yds
        {
            if(cboConsUom==12)//kg
            {
                kg to yds
            }
            else if (cboConsUom==23)//mtr
            {
                mtr to yds
            }
            else if (cboConsUom==27)//yds
            {
               $('#txtFinishQty_'+i).val(txtBookingQnty);
            }
        }*/

        $('#txtFinishQty_'+i).val(finishQnty);

        calculate_grey_qty(i)
        //$('#txtGreyQty_'+i).val(finishQnty);

   // }
}


function call_print_button_for_mail(mail_address,mail_body){

	//get_php_form_data($('#txt_booking_no').val()+'__seperate__'+mail_address+'__seperate__'+mail_body, 'auto_mail_send', 'requires/fabric_sales_order_entry_controller');

    var mail_data = $('#txt_booking_no').val()+'__seperate__'+mail_address+'__seperate__'+mail_body+'__seperate__1';

    //var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
    //window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3&mail_data='+mail_data, true);return;


    //-----------------------------------

    var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $('#txt_hidden_job_no').val() + '*' + $('#txt_order_id').val() + '*' + $('#txt_is_approved').val() + '*' + $('#txt_fabric_source').val() + '*' + $('#txt_item_category').val() +  '*' + $("div.form_caption").html();
    var within_group    = $('#cbo_within_group').val();
    if (within_group == 1) {
        window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print_yes_7&mail_data='+mail_data, true);
    } else {
        alert("This report generated only within group yes");
        return;
    }

    return;




}

function fnc_copyColorRangeProcess(id)
{
    if(id=="copy_color_wise")
    {
        var copy_color_wise=$("#copy_color_wise").is(":checked");
        if(copy_color_wise)
        {
            $("#copy_to_all").prop("checked", false);
            $("#copy_fab_wise").prop("checked", false);
        }
    }

    if(id=="copy_to_all")
    {
        var copy_to_all=$("#copy_to_all").is(":checked");
        if(copy_to_all)
        {
            $("#copy_color_wise").prop("checked", false);
			$("#copy_fab_wise").prop("checked", false);
        }
    }

	if(id=="copy_fab_wise")
    {
        var copy_fab_wise=$("#copy_fab_wise").is(":checked");
        if(copy_fab_wise)
        {
            $("#copy_to_all").prop("checked", false);
			$("#copy_color_wise").prop("checked", false);
        }
    }
}

function openmypage_from_fso()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var update_id = $('#update_id').val();

	if (form_validation('cbo_company_id*txt_job_no', 'Company*Sales Order No.') == false) {
		return;
	}
	else
	{
		var total_adjust =0;
		$("#tbl_item_details").find('tbody tr').each(function ()
		{
			var txtAdjustQnty = $(this).find('input[name="txtAdjustQnty[]"]').val();
			total_adjust = txtAdjustQnty*1;
		});

		var r=true;
		if(total_adjust > 0)
		{
			r=confirm("Press  \"OK\"  to remove adjust quantity and open Popup for From FSO.");
		}
		if(r==false)
		{
			return;
		}
		var title = 'Block FSO Ref. Form';
		var page_link = 'requires/fabric_sales_order_entry_controller.php?cbo_company_id=' + cbo_company_id + '&job_id=' + update_id + '&action=from_fso_popup';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '');

		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
			var booking_data = hidden_booking_data.split("**");

			$('#txt_fso_ref_id').val(booking_data[0]);
			$('#txt_fso_ref').val(booking_data[1]);
			$('#txt_fso_ref_show').val(booking_data[1]);
			$('input[name="txtAdjustQnty[]"]').removeAttr('disabled');
			$("#tbl_item_details").find('tbody tr').each(function ()
			{
				$('input[name="txtAdjustQnty[]"]').val('');
				$('input[name="txtAdjustQntyString[]"]').val('');
				var i = $(this).find('input[name="txtSerial[]"]').val();
				calculate_grey_qty(i);
			});

			//reset_form('', '', 'cboBodyPart_1*cboColorType_1*txtFabricDesc_1*fabricDescId_1*txtFabricGsm_1*txtAfterFabricGsm_1*txtPPQty_1*txtMTLQty_1*txtFPTQty_1*txtGPTQty_1*txtAdjustFinQty_1*txtTotalFinishQty_1*txtAdjustQnty_1*txtAdjustQntyString_1*txtFabricDia_1*cboDiaWidthType_1*txtColor_1*colorId_1*txtFinishQty_1*txtAvgRate_1*txtAmount_1*txtProcessLoss_1*txtGreyQty_1*cboWorkScope_1*updateIdDtls_1*txtBookingQnty_1', '', '$(\'#tbl_item_details tbody tr:not(:first)\').remove();', '');

			//$("#total_grey_sum").text("");
			//$("#total_amnt").text("");
			//$("#total_finish_sum").text("");
		}
	}
}

function chkHiddenFromFSO()
{
	var total_adjust = 0;
	$("#tbl_item_details").find('tbody tr').each(function()
	{
		var txtAdjustQnty = $(this).find('input[name="txtAdjustQnty[]"]').val();
		total_adjust += txtAdjustQnty*1;
	});

	var txt_fso_ref_show = trim($('#txt_fso_ref_show').val());
	var txt_fso_ref = trim($('#txt_fso_ref').val());

	//alert(total_adjust);
	if(total_adjust > 0)
	{
		if(txt_fso_ref != txt_fso_ref_show)
		{
			var r=true;
			r=confirm("Press  \"OK\"  to remove adjust quantity and From FSO.");
			if(r==false)
			{
				$('#txt_fso_ref_show').val(txt_fso_ref);
				return;
			}

			$('input[name="txtAdjustQnty[]"]').attr('disabled', 'disabled');
			$("#tbl_item_details").find('tbody tr').each(function ()
			{
				$('input[name="txtAdjustQnty[]"]').val('');
				$('input[name="txtAdjustQntyString[]"]').val('');
				$('input[name="txtAdjustFinQty[]"]').val('');
				var i = $(this).find('input[name="txtSerial[]"]').val();
				calculate_grey_qty(i);
			});

			$('#txt_fso_ref_show').val('')
			$('#txt_fso_ref').val('');
			$('#txt_fso_ref_id').val('');
		}
	}
	else
	{
		if(txt_fso_ref != txt_fso_ref_show)
		{
			$('#txt_fso_ref_show').val('')
			$('#txt_fso_ref').val('');
			$('#txt_fso_ref_id').val('');
		}
	}
}

function openmypage_adjust_qnty(incr_id)
{
    var txtFabricDesc = $('#fabricDescId_'+incr_id).val();
    var txtFabricGsm = $('#txtFabricGsm_'+incr_id).val();
    var txtAfterFabricGsm = $('#txtAfterFabricGsm_'+incr_id).val();
	var thisAdjustQntyString = $('#txtAdjustQntyString_'+incr_id).val();
    var txt_fso_ref_id = $('#txt_fso_ref_id').val();
    var update_id = $('#update_id').val();


	var all_adjust_string = "";

	$("#tbl_item_details").find('tbody tr').each(function()
	{
		var txtAdjustQntyString = $(this).find('input[name="txtAdjustQntyString[]"]').val();

		if(all_adjust_string =="")
		{
			all_adjust_string += txtAdjustQntyString;
		}
		else
		{
			all_adjust_string += "@@" + txtAdjustQntyString;
		}
	});

    var title = 'Adjust Quantity Popup';
    var page_link = 'requires/fabric_sales_order_entry_controller.php?txtFabricDesc='+txtFabricDesc+'&txtFabricGsm='+txtFabricGsm+'&txtAfterFabricGsm='+txtAfterFabricGsm+'&txt_fso_ref_id='+txt_fso_ref_id+'&thisAdjustQntyString='+thisAdjustQntyString+'&all_adjust_string='+all_adjust_string+'&update_id='+update_id+'&action=adjust_quantity_popup';

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
        var hdn_total_adjust=this.contentDoc.getElementById("hdn_total_adjust").value;    //Access form field with id="emailfield"
        var hdn_adjust_strint=this.contentDoc.getElementById("hdn_adjust_strint").value;

        $('#txtAdjustQnty_'+incr_id).val(hdn_total_adjust);
        $('#txtAdjustQntyString_'+incr_id).val(hdn_adjust_strint);
		calculate_grey_qty(incr_id);


		//total finish/(total finish + (total finish*process loss/100)))*adjustqnty

		/* var process_loss_method = $('#txtProcessLoss_'+incr_id).val() * 1;
		var finish_qty = $('#txtFinishQty_' + incr_id).val() * 1;

		var txtPPQty = $('#txtPPQty_' + incr_id).val() * 1;
		var txtMTLQty = $('#txtMTLQty_' + incr_id).val() * 1;
		var txtFPTQty = $('#txtFPTQty_' + incr_id).val() * 1;
		var txtGPTQty = $('#txtGPTQty_' + incr_id).val() * 1;

		finish_qty +=txtPPQty+txtMTLQty+txtFPTQty+txtGPTQty;
		var adjust_finish_qnty = (finish_qty/(finish_qty + (finish_qty*process_loss_method/100)))*hdn_total_adjust;
		$("#txtAdjustFinQty_"+incr_id).val(adjust_finish_qnty); */

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
                    <div style="display: none;">
                        <?
                            echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
                        ?>
                    </div>
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
										echo create_drop_down("cbo_company_id", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', 0, "load_drop_down( 'requires/fabric_sales_order_entry_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value,'process_loss_method' ,'requires/fabric_sales_order_entry_controller'); active_inactive(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_sales_order_entry_controller' )", '', '', '', '', '');
                                        //load_drop_down( 'requires/fabric_sales_order_entry_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );
										?>
										<input type="hidden" id="editableId" value=""/>
									</td>
									<td width="110" class="must_entry_caption">Within Group</td>
									<td>
										<?
										echo create_drop_down("cbo_within_group", 162, $yes_no, "", 0, "--  --", 0, "active_inactive();");
                                        //load_drop_down( 'requires/fabric_sales_order_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+'_80', 'load_drop_down_buyer', 'buyer_td' );
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

                                        /*echo "<script>document.getElementById('cbo_company_id').value;</script>";
                                        $print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=2 and report_id=1 and is_deleted=0 and status_active=1 and template_name=$company_name", "template_name", "format_id");*/
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
                                        <input type="hidden" name="print_booking_details" id="print_booking_details" readonly>
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
									<td title="Receive Date/ Booking Approval Date">Receive/Approval Date</td>
									<td>
										<input type="text" name="booking_approval_date" id="booking_approval_date" class="datepicker"
										style="width:150px;" readonly disabled/>
									</td>
								</tr>
								<tr>
									<td class="must_entry_caption">Location</td>
									<td id="location_td">
										<?
										echo create_drop_down("cbo_location_name", 162, $blank_array, "",1, "-- Select Location --", 1, "");
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
										style="width:150px;" maxlength="1000" title="Maximum Characters 1000"
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
										echo create_drop_down("cbo_team_leader", 162, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select Team Leader--", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_dealing_merchant', 'team_td' );", 1);
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
									<td class="must_entry_caption">Cust. Buyer</td>
                                    <td id="cust_buyer_td">
                                        <?
                                        echo create_drop_down("cbo_cust_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "");
                                        ?>
                                    </td>
								</tr>
                                <tr>
                                    <td>Buyer brand</td>
                                    <td>
                                        <input type="text" name="txt_buyer_brand" id="txt_buyer_brand" class="text_boxes" style="width:150px;" disabled readonly/>
                                        <input type="hidden" name="hdn_buyer_brand_id" id="hdn_buyer_brand_id" class="text_boxes" style="width:150px;" disabled/>
                                    </td>
                                    <td>Fabric Composition</td>
                                    <td>
                                        <input type="text" name="txt_fab_comp" id="txt_fab_comp" class="text_boxes" style="width:150px;"/>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE"
                                        onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry', 0 ,1)">
                                    </td>
                                </tr>
								<tr>
									<td>Remarks</td>
									<td>
										<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
										style="width:150px;" maxlength="500" title="Maximum Characters 500"/>
									</td>
                                    <td>Internal Ref</td>
                                    <td>
                                        <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:150px;" placeholder="Display" disabled="" readonly/>
                                    </td>
									<td></td>
									<td>
                                        <input type="button" class="image_uploader" style="width:140px" value="ADD FILE"
                                        onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry', 2 ,1)">
                                    </td>
								</tr>
								<tr>
                                    <td>Attention</td>
                                    <td>
                                        <input type="text" name="txt_attention" id="txt_attention" class="text_boxes"
                                        style="width:150px;"/>
                                    </td>
                                    <td class="must_entry_caption">Sales Order Type</td>
                                    <td align="left" height="10">
                                        <?
                                        echo create_drop_down( "cbo_sales_order_type", 162, $sales_order_type_arr,"", 1, "-- Select--", 1, "","","" );
                                        ?>
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
									<td>From FSO Ref.</td>
                                    <td>
                                        <input type="text" name="txt_fso_ref_show" id="txt_fso_ref_show" class="text_boxes" style="width:150px;" placeholder="Double Click" onDblClick="openmypage_from_fso()" onblur="chkHiddenFromFSO()"/>
                                        <input type="hidden" name="txt_fso_ref" id="txt_fso_ref" class="text_boxes" />
                                        <input type="hidden" name="txt_fso_ref_id" id="txt_fso_ref_id" class="text_boxes" />
                                    </td>
                                    <td align="">Ready To Approved</td>
									<td align="left" height="10">
										<?
										echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", 2, "","","" );
										?>
									</td>
								</tr>
                                <tr>
                                    <td colspan="6" id="approval_msg_td" style="font-size:18px; color:#F00"  valign="top" align="center"></td>
                                </tr>
							</table>
						</fieldset>
					</div>
					<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div id="list_change_booking_nos"
					style="max-height:300px; width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                    <div id="list_change_pending_booking_nos"
                    style="max-height:300px; width:310px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div align="left">
						<fieldset style="width:1659px; margin-top:10px">
							<legend>
                            Fabric Details
                            <span style="margin-left: 650px">
                                Copy All <input type="checkbox" id="copy_to_all" name="copy_to_all" onClick="fnc_copyColorRangeProcess(this.id)" />
                                &nbsp;
                                Color wise <input type="checkbox" id="copy_color_wise" name="copy_color_wise" onClick="fnc_copyColorRangeProcess(this.id)"/>  -  applicable for color range & process loss
								&nbsp;
                                Fabric wise <input type="checkbox" id="copy_fab_wise" name="copy_fab_wise" onClick="fnc_copyColorRangeProcess(this.id)"/>  -  applicable for Fabric wise
							</span>
                            </legend>
							<table cellpadding="0" cellspacing="0" width="1875" class="rpt_table" border="1" rules="all"
							id="tbl_item_thead">
							<thead>
								<th width="25">SL</th>
								<th width="82">Garment Item</th>
								<th width="82" class="must_entry_caption">Body Part</th>
								<th width="72">Color Type</th>
								<th width="152" class="must_entry_caption">Fabric Description</th>
								<th width="57" class="must_entry_caption">Fabric GSM</th>
								<th width="52" class="must_entry_caption">Fabric Dia</th>
								<th width="82">Dia/ Width Type</th>
								<th width="87">Fabric Color</th>
								<th width="82" class="must_entry_caption">Color Range</th>
								<th width="52">Cons. UOM</th>
								<th width="67">Booking Qty.</th>
								<th width="57" class="must_entry_caption">Avg. Price</th>
								<th width="72" class="must_entry_caption">Amount</th>
								<th width="52">UOM</th>
								<th width="67" class="must_entry_caption">Finish Qty.</th>
								<th width="52">Process <br>Loss %</th>
								<th width="52">Adjust Grey Qty.</th>
								<th width="67">Grey Qty.</th>
								<th width="82">Work Scope</th>
                                <th width="100" id="main_process">Main Process &nbsp; <input type="checkbox" checked id="processChkMain" name="processChkMain"/></th>
								<th width="100" id="main_process">Sub Process &nbsp;<input type="checkbox" checked id="processChk" name="processChk"/></th>
								<th width="100">Remarks</th>
								<th width="100">Barcode No</th>
								<th width="60"></th>
							</thead>
						</table>
						<div style="width:1880px; max-height:260px; overflow-y:scroll;" id="list_container_batch"
						align="left">
						<table cellpadding="0" cellspacing="0" width="1875" class="rpt_table" border="1" rules="all"
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
									echo create_drop_down("cboColorRange_1", 80, $color_range, "", 1, "-- Select --", 0, "copy_process_loss(1)", "0", "", "", "", "", "", "", "cboColorRange[]");
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
									echo create_drop_down("cboUom_1", 50, $unit_of_measurement, "", 0, "", 12, "consumtion_calculate(1)", "1", "12,27,23", "", "", "", "", "", "cboUom[]");
									?>
								</td>
								<td width="67">
									<input type="text" name="txtFinishQty[]" id="txtFinishQty_1"
									class="text_boxes_numeric" style="width:55px"
									onblur="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);"
									readonly/>
									<input type="hidden" name="rmgQty[]" id="rmgQty_1" readonly/>
								</td>
								<td width="52">
									<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_1"
									class="text_boxes_numeric" style="width:40px"
									onKeyUp="calculate_grey_qty(1);copy_process_loss(1);"/>
								</td>
								<td width="52">
									<input type="text" name="txtAdjustQnty[]" id="txtAdjustQnty_1" class="text_boxes" style="width:40px" ondblclick="openmypage_adjust_qnty(1)" value="<? ?>" readonly/>
									<input type="hidden" name="txtAdjustQntyString[]" id="txtAdjustQntyString_1" />
								</td>
								<td width="67">
									<input type="text" name="txtGreyQty[]" id="txtGreyQty_1" class="text_boxes_numeric" style="width:55px" readonly/>
                                    <input type="hidden" name="txtGreyQtyBeforeAdjust[]" id="txtGreyQtyBeforeAdjust_1" class="text_boxes_numeric" style="width:55px" value=""  readonly/>
								</td>
								<td width="82">
									<?
									echo create_drop_down("cboWorkScope_1", 80, $item_category, "", 1, "-- Select --", 2, "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
									?>
								</td>
                                <td width="100">
                                    <input type="text" name="txtProcessNameMain[]" id="txtProcessNameMain_1" class="text_boxes" style="width:70px;" placeholder="Double Click To Search" onDblClick="openmypage_process_main(1);" readonly />
                                    <input type="hidden" name="txtProcessIdMain[]" id="txtProcessIdMain_1" value="" />
                                    <input type="hidden" name="txtProcessSeqMain[]" id="txtProcessSeqMain_1" value="" />
                                    <input type="hidden" name="txtProcessSeqSub[]" id="txtProcessSeqSub_1" value="" />
                                </td>
								<td width="100">
									<input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(1);" disabled="" readonly />
									<input type="hidden" name="txtProcessId[]" id="txtProcessId_1" value="" />
                                    <input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_1" value="" />
								</td>
								<td width="100">
									<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:90px;" readonly/>
								</td>
								<td width="100">
									<input type="text" name="barcode_no[]" id="barcode_no_1" class="text_boxes" style="width:90px;"  readonly/>
								</td>
								<td width="60">
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
								<td align="right">&nbsp;</td>
								<td align="right" id="total_FinishQty"></td>
								<td align="right" >&nbsp;</td>
								<td align="right" id="total_AdjustQty">&nbsp;</td>
								<td align="right" id="total_GreyQty"></td>
								<td colspan="5"></td>
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
						echo load_submit_buttons($permission, "fnc_fabric_sales_order_entry", 0, 0, "reset_form('fabricOrderEntry_1*fabricOrderEntry_2','yarn_details_list_view*approval_msg_td','','','disable_enable_fields(\'cbo_company_id*cbo_within_group\');active_inactive();','')", 1);



						?>
						<input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();"
						style="visibility: hidden;"/>
						<input id="print1" class="formbutton_disabled" type="button" style="width:80px; display:none;" onClick="fnc_fabric_sales_order_entry(4)" name="print1" value="Print">

						<input type="button" name="print_2" class="formbuttonplasminus formbutton_disabled" value="Print 2" id="print_2" onClick="fabric_sales_order_print3();" />
                        <input type="button" name="print_3" class="formbuttonplasminus formbutton_disabled" value="KDS" id="print_3" onClick="fabric_sales_order_print4();" />
                        <input type="button" name="print_4" id="print_4" class="formbuttonplasminus formbutton_disabled" value="KDS 2" onClick="fabric_sales_order_print_kds2();" />
                        <input type="button" name="print_5" class="formbuttonplasminus formbutton_disabled" value="Print 4" id="print_5" onClick="fabric_sales_order_print5();" />
						<input type="button" name="print_6" class="formbuttonplasminus formbutton_disabled" value="Print 5" id="print_6" onClick="fnc_fabric_sales_order_entry(6);" />
                        <input type="button" name="print_7" class="formbuttonplasminus formbutton_disabled" value="Print 6" id="print_7" onClick="fnc_fabric_sales_order_entry(7);" />
                        <input type="button" name="print_8" class="formbuttonplasminus formbutton_disabled" value="Print 7" id="print_8" onClick="fnc_fabric_sales_order_entry(8);" />
                        <input type="button" name="print_9" class="formbuttonplasminus formbutton_disabled" value="Print 8" id="print_9" onClick="fnc_fabric_sales_order_entry(9);" />
                        <input type="button" name="print_10" class="formbuttonplasminus formbutton_disabled" value="Print 9" id="print_10" onClick="fnc_fabric_sales_order_entry(10);" />

                        <input type="button" name="send_mail" class="formbuttonplasminus formbutton" value=" Send Mail " id="send_mail" onClick="fnSendMail('../','txt_booking_no',1,1,0,0);" style="width:80px;" />

                        <input type="hidden" name="process_loss_method" id="process_loss_method" readonly>
						<input type="hidden" name="color_from_library" id="color_from_library" readonly>
						<input type="hidden" name="is_approved" id="is_approved" readonly>
						<input type="hidden" name="update_id" id="update_id"/>
						<input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
						<input type="hidden" name="deletedDtlsIds" id="deletedDtlsIds" readonly/>
						<input type="hidden" name="textile_sales_maintain" id="textile_sales_maintain" readonly/>
                        <input type="hidden" name="process_loss_editable_maintain" id="process_loss_editable_maintain" readonly/>
					</td>
				</tr>
			</table>
		</div>
	</form>

	<form name="fabricOrderEntry_2" id="fabricOrderEntry_2">
		<fieldset style="width:840px; margin-top:10px">
			<legend>Grey Qty. For Yarn Details Entry</legend>
			<table class="rpt_table" border="1" width="775" cellpadding="0" cellspacing="0" rules="all"
			id="table_yarn_details">
			<thead>
				<th width="400">Fabric Description</th>
                <th width="100">Fabric GSM</th>
				<th width="100">Color Range</th>
				<th width="100">Color Type</th>
				<th class="must_entry_caption">Grey Quantity</th>
			</thead>
			<tbody id="yarn_details_list_view"></tbody>
		</table>
		<table>
			<tr>
				<td width="100%" align="center" colspan="4">
					<? echo load_submit_buttons($_SESSION['page_permission'], "fnc_fabric_yarn_dtls_entry", 0, 0, "reset_form('','','','','')", 2); ?>
				</td>
			</tr>
		</table>
	</fieldset>

</form>
</fieldset>
</div>
	<div style="display:none" id="data_panel"></div>
	<a id="print_report_Excel" href="" style="text-decoration:none" download hidden>#</a>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>