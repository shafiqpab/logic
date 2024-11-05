<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Sales Order Entry
Functionality	:
JS Functions	:
Created by		:	
Creation date 	:    31-03-2021
Updated by 		:
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
print_r($_SESSION['logic_erp']['data_arr'][109] );
echo "</pre>";
die;*/
$bodypart_with_type_arr = return_library_array("select id, body_part_type from lib_body_part where status_active=1 and is_deleted=0", "id", "body_part_type");
?>
<script>

	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
    var bodypart_with_type =new Array();
    <?
    $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][472] );
    echo "var field_level_data= ". $data_arr . ";\n";


    if(!empty($bodypart_with_type_arr))
    {
        $bodypart_with_type_json= json_encode( $bodypart_with_type_arr);
        echo "bodypart_with_type= ". $bodypart_with_type_json . ";\n";
    }
    
    ?>

    var txt_ship_to = [<? echo substr(return_library_autocomplete( "select ship_to  from fabric_sales_order_mst where  status_active=1 and is_deleted=0 group by ship_to", "ship_to" ), 0, -1); ?>];

    $(document).ready(function(e)
    {
        $("#txt_ship_to").autocomplete({
            source: txt_ship_to
        });
    });

	function openmypage_fabricBooking() {
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id', 'Company') == false) {
			return;
		}
		else {
			var title = 'Booking Selection Form';
			var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?cbo_company_id=' + cbo_company_id + '&action=fabricBooking_popup';

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
                //$('#txt_fab_comp').val(data[22]);

                $('#txt_action_name').val(data[14]);

                load_drop_down('requires/fabric_sales_order_entry_v2_controller', document.getElementById('cbo_within_group').value + '_' + data[7], 'load_drop_down_dealing_merchant', 'team_td');

                $('#cbo_dealing_merchant').val(data[8]);
                $('#is_apply_last_update').val(0);

                show_list_view(data[1], 'show_fabric_details', 'order_details_container', 'requires/fabric_sales_order_entry_v2_controller', '');
                total_amount_cal();
                if(data[15] == ""){
                	$("#txt_style_ref").removeAttr("readonly","readonly");
                }
            }
        }
    }

    function openmypage_from_fso()
    {
        var cbo_company_id = $('#cbo_company_id').val();
        var update_id = $('#update_id').val();

        if (form_validation('cbo_company_id', 'Company') == false) {
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
            var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?cbo_company_id=' + cbo_company_id + '&job_id=' + update_id + '&action=from_fso_popup';

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

	function active_inactive_check()
	{
		var copy_val=$('#copy_id').val();
		//alert(copy_val);
		if(copy_val ==2)
		{
			active_inactive();
		}
		
	}

    function active_inactive() {
    	reset_form('', 'approval_msg_td', 'txt_booking_no*txt_booking_no_id*txt_delivery_start_date*txt_delivery_date*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*txt_season*cboBodyPart_1*cboColorType_1*txtFabricDesc_1*fabricDescId_1*txtFabricGsm_1*txtFabricDia_1*cboDiaWidthType_1*txtColor_1*colorId_1*txtFinishQty_1*txtAvgRate_1*txtAmount_1*txtProcessLoss_1*txtGreyQty_1*cboWorkScope_1*updateIdDtls_1*txtBookingQnty_1', '', '$(\'#tbl_item_details tbody tr:not(:first)\').remove();', '');

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
            $('#txt_delivery_start_date').attr('disabled', 'disabled');
            $('#txt_delivery_start_date').attr('placeholder', 'Display');
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
    		$("#last_update").css("visibility", "hidden");
    		$('#txt_booking_no').removeAttr('onDblClick', 'onDblClick');
    		$('#txt_booking_no').removeAttr('readOnly', 'readOnly');
    		$('#txt_booking_date').removeAttr('readOnly', 'readOnly');
    		$('#txt_booking_date').removeAttr('disabled', 'disabled');
    		$('#txt_booking_no').attr('placeholder', 'Write');
            $('#txt_delivery_start_date').removeAttr('disabled', 'disabled');
            $('#txt_delivery_start_date').attr('placeholder', '');
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
    		$("#cbo_cust_buyer_name option[value!='0']").remove();
    	}
    	else {
            load_drop_down('requires/fabric_sales_order_entry_v2_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'buyer_td');
    		load_drop_down('requires/fabric_sales_order_entry_v2_controller', within_group + '_' + company_id, 'load_drop_down_cust_buyer', 'cust_buyer_td');
    	}
    }

    /*function add_break_down_tr(i) 
    {
    	var within_group = $('#cbo_within_group').val();
    	var color_from_library = $('#color_from_library').val();

    	if (within_group != 1) {
    		var lastTrId = $('#tbl_item_details tbody tr:last').attr('id').split('_');
    		var row_num = lastTrId[1];
    		if (row_num != i) {
    			return false;
    		}
    		else 
            {
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
    			$('#txtPPQty_' + i).val('');
    			$('#txtMTLQty_' + i).val('');
    			$('#txtFPTQty_' + i).val('');
    			$('#txtGPTQty_' + i).val('');
    			$('#txtTotalFinishQty_' + i).val('');
    			$('#txtGreyQty_' + i).val('');
    			$('#cboWorkScope_' + i).val('');
                $('#txtProcessNameMain_' + i).val('');
                $('#txtProcessIdMain_' + i).val('');
                $('#txtProcessSeqMain_' + i).val('');
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
    			$('#txtFinishQty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");");
                $('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_fin_qty(" + i + ");calculate_grey_qty(" + i + ");");


               $('#txtPPQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtMTLQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtFPTQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtGPTQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");


                if( bodypart_with_type[$('#cboBodyPart_'+i).val()] ==40 || bodypart_with_type[$('#cboBodyPart_'+i).val()] ==50)
                {
                    $('#txtBookingQnty_' + i).removeAttr("onClick").attr("onClick", "openmypage_ccmp(" + i + ");");
                    $('#txtBookingQnty_' + i).attr("readonly",true);
                    //alert("hi");
                    $('#txtPPQty_' + i).attr("disabled","disabled");
                    $('#txtMTLQty_' + i).attr("disabled","disabled");
                    $('#txtFPTQty_' + i).attr("disabled","disabled");
                    $('#txtGPTQty_' + i).attr("disabled","disabled");
                    $('#txtProcessLoss_' + i).attr("disabled","disabled");

                    
                }else{
                    $('#txtBookingQnty_' + i).removeAttr("readonly");
                   $('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onClick");
                    $('#txtProcessLoss_' + i).removeAttr("disabled");
                }
                $('#ccmpSaveData_' + i).val("");

    			$('#txtAvgRate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
    			$('#txtProcessLoss_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_grey_qty(" + i + ");");
    			$('#txtFabricGsm_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#txtFabricDia_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
    			$('#cboConsUom_' + i).removeAttr("onchange").attr("onchange", "refresh_fields(" + i + ");");
                $('#txtProcessNameMain_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process_main(" + i + ");");
    			$('#txtProcessName_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process(" + i + ");");
                $('#cboUom_' + i).removeAttr("onchange").attr("onchange", "consumtion_calculate(" + i + ");");
    			
                //$('#txtCcmp_' + i).removeAttr("onclick").attr("onclick", "openmypage_ccmp(" + i + ");");
                $('#cboBodyPart_' + i).removeAttr("onchange").attr("onchange", "show_ccmp(" + i + ");");

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
    }*/

    function add_break_down_tr(j) 
    {
        var within_group = $('#cbo_within_group').val();
        var color_from_library = $('#color_from_library').val();

        if (within_group != 1) {
            var lastTrId = $('#tbl_item_details tbody tr:last').attr('id').split('_');
            var row_num = lastTrId[1];
            if (row_num != i && 1==5) {
                return false;
            }
            else 
            {
                row_num++;
                var i = row_num;
                $("#tr_"+j).clone().find("input,select").each(function () {
                    $(this).attr({
                        'id': function (_, id) {var id = id.split("_");return id[0] + "_" + i},
                        'name': function (_, name) {return name},
                        'value': function (_, value) {return value}
                    });
                }).end().appendTo("#tbl_item_details");

                $('#cboBodyPart_' + i).val($('#cboBodyPart_' +(j)).val());
                $('#cboColorType_' + i).val($('#cboColorType_' +(j)).val());
                $('#cboDiaWidthType_' + i).val($('#cboDiaWidthType_' +(j)).val());
                $('#cboColorRange_' + i).val($('#cboColorRange_' + j).val());

                $('#txtColor_' + i).val('');
                //$('#cboColorRange_' + i).val('');    

                $('#cboUom_' + i).val('12');
                $('#txtFinishQty_' + i).val('');
                $('#txtBookingQnty_' + i).val('');
                $('#txtAvgRate_' + i).val('');
                $('#txtAmount_' + i).val('');
                $('#txtProcessLoss_' + i).val('');
                $('#txtPPQty_' + i).val('');
                $('#txtMTLQty_' + i).val('');
                $('#txtFPTQty_' + i).val('');
                $('#txtGPTQty_' + i).val('');
                $('#txtTotalFinishQty_' + i).val('');
                $('#txtGreyQty_' + i).val('');
                //$('#cboWorkScope_' + i).val('');
                $('#cboWorkScope_' + i).val($('#cboWorkScope_' + j).val());

                $('#txtLabDipNo_' + i).val('');

                $('#txtProcessNameMain_' + i).val('');
                $('#txtProcessIdMain_' + i).val('');
                $('#txtProcessSeqMain_' + i).val('');
                $('#txtProcessName_' + i).val('');
                $('#txtProcessId_' + i).val('');
                $('#txtProcessSeq_' + i).val('');
                $('#updateIdDtls_' + i).val('');

                $('#btnimg_' + i).attr("onClick", "file_uploader('../','','', 'fabric_sales_order_entry_v2', 0 ,1);");

                $("#tbl_item_details tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
                $('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
                $('#tr_' + i).find("td:eq(0) span").text(i);
                $('#tr_' + i).find('#txtSerial_' + i).val(i);
                $('#txtSerial_' + i).val(i);

                $('#txtFabricDesc_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_fabricDescription(" + i + ");");
                $('#txtFinishQty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");");
                $('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_fin_qty(" + i + ");calculate_grey_qty(" + i + ");");


               $('#txtPPQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtMTLQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtFPTQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");
               $('#txtGPTQty_' + i).removeAttr("disabled").removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");copy_process_loss(" + i + ");calculate_fin_qty(" + i + ");");

               $('#txtAdjustQnty_' + i).removeAttr("disabled").removeAttr("ondblclick").attr("ondblclick", "openmypage_adjust_qnty(" + i + ");");

			   	if($('#txt_fso_ref').val() == "")
			   	{
					$('#txtAdjustQnty_' + i).attr("disabled","disabled");
			   	}


                if( bodypart_with_type[$('#cboBodyPart_'+i).val()] ==40 || bodypart_with_type[$('#cboBodyPart_'+i).val()] ==50)
                {
                    $('#txtBookingQnty_' + i).removeAttr("onClick").attr("onClick", "openmypage_ccmp(" + i + ");");
                    $('#txtBookingQnty_' + i).attr("readonly",true);
                    //alert("hi");
                    $('#txtPPQty_' + i).attr("disabled","disabled");
                    $('#txtMTLQty_' + i).attr("disabled","disabled");
                    $('#txtFPTQty_' + i).attr("disabled","disabled");
                    $('#txtGPTQty_' + i).attr("disabled","disabled");
                    $('#txtProcessLoss_' + i).attr("disabled","disabled");
                    $('#cboConsUom_' + i).attr("disabled","disabled");
                    $('#cboConsUom_' + i).val(1);

                    $('#cboBodyPart_' + i).removeAttr("disabled");
                    $('#hdnCollerCuffCon_' + i).val("");
                }else{
                    $('#txtBookingQnty_' + i).removeAttr("readonly");
                   $('#txtBookingQnty_' + i).removeAttr("readonly").removeAttr("onClick");
                    $('#txtProcessLoss_' + i).removeAttr("disabled");
                }
                $('#ccmpSaveData_' + i).val("");

                $('#txtAvgRate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
                $('#txtProcessLoss_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_grey_qty(" + i + ");");
                $('#txtFabricGsm_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
                $('#txtFabricDia_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_fin_qty(" + i + ");");
                $('#cboConsUom_' + i).removeAttr("onchange").attr("onchange", "refresh_fields(" + i + ");");
                $('#txtProcessNameMain_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process_main(" + i + ");");
                $('#txtProcessName_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_process(" + i + ");");
                $('#cboUom_' + i).removeAttr("onchange").attr("onchange", "consumtion_calculate(" + i + ");");
                
                //$('#txtCcmp_' + i).removeAttr("onclick").attr("onclick", "openmypage_ccmp(" + i + ");");
                $('#cboBodyPart_' + i).removeAttr("onchange").attr("onchange", "show_ccmp(" + i + ");");

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

    function fnc_fabric_sales_order_entry_v2(operation) {
    	var within_group = $('#cbo_within_group').val();
    	var company_id = $('#cbo_company_id').val();
    	var booking_no = trim($('#txt_booking_no').val());
    	var hdn_booking_no = trim($('#txt_hdn_booking_no').val());
    	var buyer_name = $('#cbo_buyer_name').val();
    	var update_id = $('#update_id').val();
        var hdn_job_no = $('#txt_hidden_job_no').val();
    	var cbo_order_nature = $('#cbo_order_nature').val();
    	var txt_fso_ref_id = $('#txt_fso_ref_id').val();

    	// check booking no when insert
    	if(within_group == 2){
    		var data = company_id + "*" + booking_no + "*" + buyer_name + "*" + operation + "*" + update_id;
    		var response=trim(return_global_ajax_value( data, 'is_booking_duplicate', '', 'requires/fabric_sales_order_entry_v2_controller'));
    		if(response == "invalid"){
    			alert("Sales/Booking No is duplicate");
    			return;
    		}
    	}

    	if(within_group == 2 && operation == 1){
    		if(booking_no != hdn_booking_no){
    			var data = company_id + "*" + hdn_booking_no + "*" + buyer_name;
    			var response=trim(return_global_ajax_value( data, 'is_booking_used_in_plan', '', 'requires/fabric_sales_order_entry_v2_controller'));
    			if(response == "invalid"){
    				alert("Knitting plan found against Booking no.Booking can not be updated.");
    				return;
    			}
    		}
    	}

        if(within_group == 1 && operation == 1)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_v2_controller'));
            if(response == "invalid" && $('#is_apply_last_update').val() ==0)
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }


    	if (operation == 4) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}

    		return;
    	}

    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}

    	/*if ($("#is_approved").val()==1 || $("#is_approved").val()==3) {
    		alert("This Sales Order Is Approved. Update Restricted.");
    		return;
    	}*/
        
    	if(hdn_job_no != ""){
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*txt_style_ref*cbo_buyer_name*cbo_cust_buyer_name*cbo_currency*cbo_sales_order_type*txt_delivery_date*cbo_team_leader', 'Company*Sales/Booking No*Booking Date*Location*Style Ref.*Customer*Customer buyer*Currency*Sales Type*Delivery End Date*Team Leader') == false) {
    			return;
    		}
    	}else{
    		if (form_validation('cbo_company_id*txt_booking_no*txt_booking_date*cbo_location_name*cbo_buyer_name*cbo_cust_buyer_name*cbo_currency*cbo_sales_order_type*txt_delivery_date*cbo_team_leader', 'Company*Sales/Booking No*Booking Date*Location*Customer*Customer buyer*Currency*Sales Type*Delivery End Date*Team Leader') == false) {
    			return;
    		}
    	}

        var k = 0;
    	var j = 0;
		var m = 0;
    	var dataString = '';
    	//var mendatory_field_validation = 1;
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
    		var txtRemarks = encodeURIComponent($(this).find('input[name="txtRemarks[]"]').val());
    		var txtProcessName = $(this).find('input[name="txtProcessName[]"]').val();
    		var txtProcessId = $(this).find('input[name="txtProcessId[]"]').val();
    		var txtProcessSeq = $(this).find('input[name="txtProcessSeq[]"]').val();

            var txtProcessNameMain = $(this).find('input[name="txtProcessNameMain[]"]').val();
            var txtProcessIdMain = $(this).find('input[name="txtProcessIdMain[]"]').val();
            var txtProcessSeqMain = $(this).find('input[name="txtProcessSeqMain[]"]').val();

    		var rmgQty = $(this).find('input[name="rmgQty[]"]').val();
    		var pre_cost_fabric_cost_dtls_id = $(this).find('input[name="pre_cost_fabric_cost_dtls_id[]"]').val();
    		var booking_qnty_by_uom = $(this).find('input[name="txtBookingQnty[]"]').val();

            var txtAfterFabricGsm = $(this).find('input[name="txtAfterFabricGsm[]"]').val();
            var txtFabricCuttableDia = $(this).find('input[name="txtFabricCuttableDia[]"]').val();
            var cboPly = $(this).find('select[name="cboPly[]"]').val();
            var txtPPQty = $(this).find('input[name="txtPPQty[]"]').val();
            var txtMTLQty = $(this).find('input[name="txtMTLQty[]"]').val();
            var txtFPTQty = $(this).find('input[name="txtFPTQty[]"]').val();
            var txtGPTQty = $(this).find('input[name="txtGPTQty[]"]').val();
            var ccmpSaveData = $(this).find('input[name="ccmpSaveData[]"]').val();
            var hdnCollerCuffCon = $(this).find('input[name="hdnCollerCuffCon[]"]').val();
            var txtLabDipNo = $(this).find('input[name="txtLabDipNo[]"]').val();
            var txtAdjustQnty = $(this).find('input[name="txtAdjustQnty[]"]').val();
            var txtAdjustFinQty = $(this).find('input[name="txtAdjustFinQty[]"]').val();
            var txtAdjustQntyString = $(this).find('input[name="txtAdjustQntyString[]"]').val();
            

    		var finishQty = $('#txtFinishQty_'+x).val();
    		if (finishQty * 1 > 0) {
    			var fabricDesc = $('#txtFabricDesc_'+x).val();
    			var fabricGsm = $('#txtFabricGsm_'+x).val();
    			var fabricDia = $('#txtFabricDia_'+x).val();
    			var txtAdjustQnty = $('#txtAdjustQnty_'+x).val();
    			
				if(fabricDesc == "" || fabricGsm == "" || fabricDia == "" || cboColorRange == 0 || cboBodyPart == 0 || ((txtAvgRate == 0 || txtAmount == 0 ) && cbo_order_nature !=11))
                {
					k = 1;
					return;
				}else{
					j++;
				}


				if(txt_fso_ref_id != "" && txtAdjustQnty == "")
				{
					m++;
				}

    			
    			dataString += '&cboBodyPart' + j + '=' + cboBodyPart + '&cboColorType' + j + '=' + cboColorType + '&txtFabricDesc' + j + '=' + txtFabricDesc + '&fabricDescId' + j + '=' + fabricDescId + '&txtFabricGsm' + j + '=' + txtFabricGsm + '&txtFabricDia' + j + '=' + txtFabricDia + '&cboDiaWidthType' + j + '=' + cboDiaWidthType + '&txtColor' + j + '=' + txtColor + '&colorId' + j + '=' + colorId + '&cboColorRange' + j + '=' + cboColorRange + '&txtFinishQty' + j + '=' + txtFinishQty + '&txtAvgRate' + j + '=' + txtAvgRate + '&txtAmount' + j + '=' + txtAmount + '&txtProcessLoss' + j + '=' + txtProcessLoss + '&txtGreyQty' + j + '=' + txtGreyQty + '&cboWorkScope' + j + '=' + cboWorkScope + '&updateIdDtls' + j + '=' + updateIdDtls + '&cboUom' + j + '=' + cboUom + '&txtRemarks' + j + '=' + txtRemarks + '&rmgQty' + j + '=' + rmgQty + '&pre_cost_fabric_cost_dtls_id' + j + '=' + pre_cost_fabric_cost_dtls_id + '&cboGarmItemId' + j + '=' + cboGarmItemId + '&booking_qnty_by_uom' + j + '=' + booking_qnty_by_uom + '&cboConsUom' + j + '=' + cboConsUom + '&txtProcessId' + j + '=' + txtProcessId + '&txtProcessSeq' + j + '=' + txtProcessSeq + '&txtProcessIdMain' + j + '=' + txtProcessIdMain + '&txtProcessSeqMain' + j + '=' + txtProcessSeqMain + '&txtAfterFabricGsm' + j + '=' + txtAfterFabricGsm + '&txtFabricCuttableDia' + j + '=' + txtFabricCuttableDia + '&txtPPQty' + j + '=' + txtPPQty + '&txtMTLQty' + j + '=' + txtMTLQty + '&txtFPTQty' + j + '=' + txtFPTQty + '&txtGPTQty' + j + '=' + txtGPTQty + '&ccmpSaveData' + j + '=' + ccmpSaveData + '&cboPly' + j + '=' + cboPly + '&hdnCollerCuffCon' + j + '=' + hdnCollerCuffCon + '&txtLabDipNo' + j + '=' + txtLabDipNo + '&txtAdjustQnty' + j + '=' + txtAdjustQnty+ '&txtAdjustFinQty' + j + '=' + txtAdjustFinQty+ '&txtAdjustQntyString' + j + '=' + txtAdjustQntyString;

    		}else{
    			j = 0;
    			return;
    		}
    	});

		if(m>0)
		{
			alert('please give adjust quantity.');
			return;
		}
    
    		if (k > 0) {
    			alert('You must fill the mendatory fields');
    			return;
    		}
    	
    	var season_val=$('#txt_season :selected').text();

    	var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_job_no*cbo_company_id*cbo_within_group*txt_booking_no*txt_booking_no_id*txt_booking_date*txt_delivery_start_date*txt_delivery_date*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_currency*cbo_team_leader*cbo_dealing_merchant*cbo_ship_mode*txt_season*txt_remarks*update_id*process_loss_method*color_from_library*is_apply_last_update*txt_hdn_booking_no*deletedDtlsIds*booking_without_order*booking_approval_date*cbo_ready_to_approved*txt_attention*cbo_sales_order_type*cbo_cust_buyer_name*cbo_cust_buyer_brand*txt_fso_ref*txt_fso_ref_id*cbo_order_nature*cbo_season_year*txt_ship_to*txt_garments_merchant*txt_copy_from*cbo_fso_status*hdn_auto_allocation*hdn_auto_allocate_yarn', "../") + dataString + '&total_row=' + j+ '&season_val=' + season_val;
		//alert(data);return;
    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_v2_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_sales_order_entry_v2_Reply_info;
    }

    function fnc_fabric_sales_order_entry_v2_Reply_info() {

    	$("#print1").removeClass( "formbutton_disabled");
    	$("#print1").addClass( "formbutton");
    	$("#print_2").removeClass( "formbutton_disabled");
    	$("#print_2").addClass( "formbutton");
        $("#print_3").removeClass( "formbutton_disabled");
        $("#print_3").addClass( "formbutton");
        $("#print_5").removeClass( "formbutton_disabled");
        $("#print_5").addClass( "formbutton");
        $("#print_6").removeClass( "formbutton_disabled");
        $("#print_6").addClass( "formbutton");
        $("#print_7").removeClass( "formbutton_disabled");
        $("#print_7").addClass( "formbutton");
        $("#print_8").removeClass( "formbutton_disabled");
        $("#print_8").addClass( "formbutton");

		$("#btn_ld_fbl_approval").removeClass( "formbutton_disabled");
        $("#btn_ld_fbl_approval").addClass( "formbutton");
       

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

    			show_list_view(response[1] + "**" + color_from_library + "**" + cbo_within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_v2_controller', '');

                if($("#is_approved").val() ==1 || $("#is_approved").val() ==3)
                {
                    $('#fabricOrderEntry_1').find('input:not(#txt_job_no), textarea, button, select').attr('disabled','disabled');
                }

                $(".printBtn").removeAttr( "disabled");
                $("#update1").removeAttr( "disabled");

				if(response[0] == 0)
				{
					$("#copy_id").removeAttr( "disabled");
					$("#copy_id").prop( "checked", false );
				}

                $("#tbl_item_details").find('tbody tr').each(function () 
                {
                    $(this).find('input[name="txtProcessNameMain[]"]').removeAttr("disabled");
                    $(this).find('input[name="txtProcessName[]"]').removeAttr("disabled");
                });


    			set_button_status(1, permission, 'fnc_fabric_sales_order_entry_v2', 1);
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
    	/*if ($("#is_approved").val()==1 || $("#is_approved").val()==3) {
    		alert("This Sales Order Is Approved. Save, Update , Delete Restricted.");
    		return;
    	}*/
    	if (form_validation('txt_job_no', 'Sales Order No') == false) {
    		return;
    	}

    	var j = 0;
    	var dataString = ''; var missing_consRation = 0;
    	$("#table_yarn_details").find('tbody tr').each(function () {
    		var fabricDescIdY = $(this).find('input[name="fabricDescIdY[]"]').val();
    		var txtFabricGsmY = $(this).find('input[name="txtFabricGsmY[]"]').val();
            var cboColorRangeY = $(this).find('select[name="cboColorRangeY[]"]').val();
    		var txtGreyQtyY = $(this).find('input[name="txtGreyQtyY[]"]').val();
            var yarnData = $(this).find('input[name="yarnData[]"]').val();
    		var fsoDtlsIds = $(this).find('input[name="fsoDtlsIds[]"]').val();
			
			var yarnData_arr =  yarnData.split('|');
			for (let index = 0; index < yarnData_arr.length; index++) {
				var yarnData_details= yarnData_arr[index].split("_");
				//alert(yarnData_details[5]);
				if(yarnData_details[5] != null && yarnData_details[5] !== undefined)
				{
					if(yarnData_details[5] * 1 <= 0)
					{
						missing_consRation = missing_consRation + 1;
					}
				}else{
					missing_consRation = missing_consRation + 1;
				}
			}
    		j++;
    		dataString += '&fabricDescIdY' + j + '=' + fabricDescIdY + '&txtFabricGsmY' + j + '=' + txtFabricGsmY + '&cboColorRangeY' + j + '=' + cboColorRangeY + '&txtGreyQtyY' + j + '=' + txtGreyQtyY + '&yarnData' + j + '=' + yarnData + '&fsoDtlsIds' + j + '=' + fsoDtlsIds;

    	});

    	if (j < 1) {
    		alert('No data');
    		return;
    	}
		if(missing_consRation > 0){
			alert('Cons ratio data is missing');
    		return;
		}

        if($('#cbo_within_group').val() == 1)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_v2_controller'));
            if(response == "invalid")
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }

    	var data = "action=save_update_delete_yarn&operation=" + operation + get_submitted_data_string('txt_job_no*update_id', "../") + dataString + '&total_row=' + j;
		//alert(data);return;
    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_v2_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_yarn_dtls_entry_Reply_info;
    }

    function fnc_fabric_yarn_dtls_entry_Reply_info()
	{
		// $("#copy_id").removeAttr("disabled",true);
		// $('#copy_id').attr('checked', false);
		// document.getElementById('copy_id').value=2;
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
    	window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
    	return;
    }

    function fabric_sales_order_print4() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html() + '*' + $('#update_id').val();
        window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print4', true);
        return;
    }
    function fabric_sales_order_print5() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
        var within_group=$('#cbo_within_group').val()
            if (within_group == 2) {
                window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print5', true);
            } else {
                alert("This report available for Within Group No");
            }

            return;
    }
    function fabric_sales_order_print6() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
        var within_group=$('#cbo_within_group').val()
            if (within_group == 2) {
                window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
            } else {
                alert("This report available for Within Group No");
            }

            return;
    }
    function fabric_sales_order_print8() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
        var within_group=$('#cbo_within_group').val()
        if (within_group == 2) {
            window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print8', true);
        } else {
            alert("This report available for Within Group No");
        }

        return;
    }

    function fabric_sales_order_print7() // Merchant Print
    {
        var input_number = prompt("Please input to re-calculate Delivery Start and Delivery End date");
        if (input_number != null) {
            input_number=input_number;
        }
        else
        {
            input_number=0;
        }

        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html() + '*' + input_number;
        var within_group=$('#cbo_within_group').val()
        if (within_group == 2) 
        {
            window.open("requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print7', true);
        } else {
            alert("This report available for Within Group No");
        }

        return;
    }

    function openmypage_fabricDescription(i) {
    	var title = 'Fabric Description Info';
    	var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?action=fabricDescription_popup';

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
    	var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?action=color_popup';

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

		var copy_val=$('#copy_id').val();
		var update_type=$('#update_type').val();
		var txt_copy_from=$('#txt_copy_from').val();

        var update_id = $('#update_id').val();
		

    	if (form_validation('cbo_company_id', 'Company') == false) {
    		return;
    	}

    	var title = 'Yarn Details Info';
    	var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?action=yarnDetails_popup&cbo_company_id=' + cbo_company_id + '&txtGreyQty=' + txtGreyQty + '&yarnData=' + yarnData + '&txtFabricDesc=' + txtFabricDesc + '&fabric_Desc_IdY=' + fabric_Desc_IdY  + '&txt_Fabric_GsmY=' + txt_Fabric_GsmY + '&cbo_Color_Range=' + cbo_Color_Range + '&update_id=' + update_id + '&copy_val=' + copy_val + '&update_type=' + update_type + '&txt_copy_from=' + txt_copy_from;

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1140px,height=370px,center=1,resize=1,scrolling=0', '');

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

    var txtPPQty = $('#txtPPQty_' + i).val() * 1;
    var txtMTLQty = $('#txtMTLQty_' + i).val() * 1;
    var txtFPTQty = $('#txtFPTQty_' + i).val() * 1;
 	var txtGPTQty = $('#txtGPTQty_' + i).val() * 1;

 	var txtAdjustQnty = $('#txtAdjustQnty_' + i).val() * 1;

    finish_qty += txtPPQty + txtMTLQty + txtFPTQty + txtGPTQty; 
    $("#txtTotalFinishQty_"+i).val(finish_qty);

 	var processLoss = $('#txtProcessLoss_' + i).val() * 1;
 	var grey_qnty = 0;
 	if (finish_qty <= 0 || processLoss <= 0 ) {
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
 		//grey_qnty = grey_qnty.toFixed(2);
 	}
	grey_qnty = grey_qnty- txtAdjustQnty;
    grey_qnty = Math.round(grey_qnty);
 	$('#txtGreyQty_' + i).val(grey_qnty);

    total_amount_cal();
 }

function calculate_fin_qty(i) 
{
    var process_loss_method = $('#process_loss_method').val() * 1;
    var cbo_within_group = $('#cbo_within_group').val();
    var booking_qty = $('#txtBookingQnty_' + i).val() * 1;
    var processLoss = $('#txtProcessLoss_' + i).val() * 1;
    var cboConsUom = $('#cboConsUom_' + i).val() * 1;
    var txtFabricGsm = $('#txtFabricGsm_' + i).val() * 1;
    var txtFabricDia = $('#txtFabricDia_' + i).val() * 1;

    var txtPPQty = $('#txtPPQty_' + i).val() * 1;
    var txtMTLQty = $('#txtMTLQty_' + i).val() * 1;
    var txtFPTQty = $('#txtFPTQty_' + i).val() * 1;
    var txtGPTQty = $('#txtGPTQty_' + i).val() * 1;

    if(cboConsUom == 12){
        var qnty_conversion = booking_qty*1;
    }else if(cboConsUom == 27){
        var qnty_conversion = (booking_qty * 36 * txtFabricDia * txtFabricGsm) / (1550 * 1000);
    }
    //alert(qnty_conversion);
    $('#txtFinishQty_' + i).val(qnty_conversion);

    //sample quantities are summed up with calculative finish quantity
    qnty_conversion = qnty_conversion + txtPPQty + txtMTLQty + txtFPTQty + txtGPTQty;

    var grey_qnty = 0;
    if (qnty_conversion <= 0 || processLoss <= 0) 
    {
        grey_qnty = qnty_conversion;
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
        grey_qnty = grey_qnty.toFixed(2);
    }
    
    $('#txtGreyQty_' + i).val(grey_qnty);
}

 function calculate_amount(i) {
 	var finish_qty = $('#txtBookingQnty_' + i).val() * 1;

    var txtPPQty = $('#txtPPQty_' + i).val() * 1;
    var txtMTLQty = $('#txtMTLQty_' + i).val() * 1;
    var txtFPTQty = $('#txtFPTQty_' + i).val() * 1;
    var txtGPTQty = $('#txtGPTQty_' + i).val() * 1;

    var tot_finish_sample = finish_qty + txtPPQty + txtMTLQty + txtFPTQty + txtGPTQty;


 	var avgRate = $('#txtAvgRate_' + i).val() * 1;
 	var amount = 0;
 	if (tot_finish_sample <= 0 || avgRate <= 0) {
 		amount = '';
 	}
 	else {
 		amount = tot_finish_sample * avgRate;
 		amount = amount.toFixed(2);
 	}
 	$('#txtAmount_' + i).val(amount);
 	total_amount_cal();
 }

 function total_amount_cal(){
 	var total_amnt = 0;
    var total_finish_sum=0;
    var total_grey_sum=0;
 	$("#tbl_item_details").find('tbody tr').each(function () {
 		var txtAmount = $(this).find('input[name="txtAmount[]"]').val();
 		total_amnt += txtAmount*1;

        var txtTotalFinishQty = $(this).find('input[name="txtTotalFinishQty[]"]').val();
        total_finish_sum += txtTotalFinishQty*1;

        var txtGreyQty = $(this).find('input[name="txtGreyQty[]"]').val();
        total_grey_sum += txtGreyQty*1;
 	});
 	total_amnt = total_amnt.toFixed(4);

    $('#total_amnt').html(total_amnt);
    $('#total_finish_sum').html(total_finish_sum);
 	$('#total_grey_sum').html(total_grey_sum);
 }


 function openmypage_jobNo() {
 	var cbo_company_id = $('#cbo_company_id').val();
 	var color_from_library = $('#color_from_library').val();

 	if (form_validation('cbo_company_id', 'Company') == false) {
 		return;
 	}
 	else {
 		var title = 'Job Selection Form';
 		var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?cbo_company_id=' + cbo_company_id + '&action=jobNo_popup';

 		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0', '');

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
            $("#print_5").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_5").addClass( "formbutton"); //To make enable print to button
            $("#print_6").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_6").addClass( "formbutton"); //To make enable print to button
            $("#print_7").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_7").addClass( "formbutton"); //To make enable print to button
            $("#print_8").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_8").addClass( "formbutton"); //To make enable print to button

			$("#btn_ld_fbl_approval").removeClass( "formbutton_disabled");
       		$("#btn_ld_fbl_approval").addClass( "formbutton");


            get_php_form_data(job_id+'_'+booking_data[1], "populate_data_from_sales_order", "requires/fabric_sales_order_entry_v2_controller");

            var cbo_within_group = $('#cbo_within_group').val();
            show_list_view(job_id + "**" + color_from_library + "**" + cbo_within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_v2_controller', '');


            if($("#is_approved").val() ==1 || $("#is_approved").val() ==3)
            {
                $('#fabricOrderEntry_1').find('input:not(#txt_job_no), textarea, button, select').attr('disabled','disabled');
            }

            $(".printBtn").removeAttr( "disabled");

            $("#update1").removeAttr( "disabled");
            $("#copy_id").removeAttr( "disabled");

            $("#tbl_item_details").find('tbody tr').each(function () 
            {
                $(this).find('input[name="txtProcessNameMain[]"]').removeAttr("disabled");
                $(this).find('input[name="txtProcessName[]"]').removeAttr("disabled");
            });


            total_amount_cal();
            show_fabric_yarn_details(job_id);
        }
    }
}

function copy_check(type)
{
	
	var company_id     = $('#cbo_company_id').val();
	var booking_date   = $('#txt_booking_date').val();
	var job_no_prev_id = $('#txt_job_no').val();
	var update_id_copy = $('#update_id').val();	
	//alert(job_no_prev_id);
	
	if(type==1)
	{
		$('#update_id').val('');
		$('#txt_job_no').val('');
		$("#cbo_company_id").removeAttr("disabled",true);
		$('#txt_booking_no').val('');
		$('#txt_booking_no').removeAttr("disabled",true);
		$('#txt_booking_date').val('<? echo date("d-m-Y"); ?>');
		$('#txt_booking_date').removeAttr("disabled",true);
		$('#booking_approval_date').val('');
		$('#booking_approval_date').removeAttr("disabled",true);
		$('#txt_delivery_start_date').val('');
		$('#txt_delivery_start_date').removeAttr("disabled",true);
		$('#txt_delivery_date').val('');
		$('#txt_delivery_date').removeAttr("disabled",true);
		$('#cbo_buyer_name').val(0);
		$('#cbo_buyer_name').removeAttr("disabled",true);
		$('#cbo_cust_buyer_name').val(0);
		$('#cbo_cust_buyer_name').removeAttr("disabled",true);
		$('#cbo_cust_buyer_brand').val(0);
		$('#cbo_cust_buyer_brand').removeAttr("disabled",true);
		$('#txt_style_ref').val('');
		$('#txt_style_ref').removeAttr("disabled",true);
		$('#txt_fso_ref').val('');
		$('#txt_fso_ref').removeAttr("disabled",true);
		$('#txt_fso_ref_show').val('');
		$('#txt_fso_ref_show').removeAttr("disabled",true);
		$('#txt_fso_ref_id').val('');
		$('#txt_fso_ref_id').removeAttr("disabled",true);
		$('#cbo_order_nature').val(0);
		$('#cbo_order_nature').removeAttr("disabled",true);
		$('#txt_garments_merchant').val('');
		$('#txt_garments_merchant').removeAttr("disabled",true);
		$('#cbo_dealing_merchant').removeAttr("disabled",true);
		$('#txt_season').val(0);
		$('#txt_season').removeAttr("disabled",true);
		$('#cbo_ready_to_approved').val(2);
		$('#cbo_ready_to_approved').removeAttr("disabled",true);
		$('#txt_remarks').val('');
		$('#txt_remarks').removeAttr("disabled",true);
		$('#txt_attention').removeAttr("disabled",true);
		$('#txt_ship_to').removeAttr("disabled",true);

		$('#cbo_location_name').removeAttr("disabled",true);
		$('#cbo_ship_mode').removeAttr("disabled",true);
		$('#cbo_sales_order_type').removeAttr("disabled",true);
		$('#cbo_fso_status').removeAttr("disabled",true);
		$('#cbo_currency').removeAttr("disabled",true);
		$('#cbo_team_leader').removeAttr("disabled",true);
		$('#cbo_season_year').removeAttr("disabled",true);
		$('#set_button').removeAttr("disabled",true);
		$(".image_uploader").removeAttr("disabled",true);
		$('#is_approved').val(0);


        
		$('input[name="txtAdjustQnty[]"]').attr('disabled', 'disabled');
		$("#tbl_item_details").find('tbody tr').each(function () 
		{
			$('input[name="txtAdjustQnty[]"]').val('');
			$('input[name="txtAdjustQntyString[]"]').val('');
			$('input[name="txtAdjustFinQty[]"]').val('');
			$('input[name="txtBookingQnty[]"]').val('');
			$('input[name="updateIdDtls[]"]').val('');


			$('input[name="txtFinishQty[]"]').val('');
			$('input[name="txtPPQty[]"]').val('');
			$('input[name="txtMTLQty[]"]').val('');
			$('input[name="txtFPTQty[]"]').val('');
			$('input[name="txtGPTQty[]"]').val('');
			//$('input[name="txtAvgRate[]"]').val('');
			$('input[name="txtAmount[]"]').val('');

			var i = $(this).find('input[name="txtSerial[]"]').val();
			calculate_grey_qty(i);

			$('#processChk').removeAttr("disabled",true);
			$('#processChkMain').removeAttr("disabled",true);

			$("select[name='cboBodyPart[]']").removeAttr("disabled");
			$("select[name='cboColorType[]']").removeAttr("disabled");

			$("input[name='txtFabricDesc[]']").removeAttr("disabled");
			$("input[name='txtFabricGsm[]']").removeAttr("disabled");
			$("input[name='txtAfterFabricGsm[]']").removeAttr("disabled");
			$("input[name='txtFabricDia[]']").removeAttr("disabled");
			$("input[name='txtFabricCuttableDia[]']").removeAttr("disabled");
			$("select[name='cboDiaWidthType[]']").removeAttr("disabled");
			$("input[name='txtColor[]']").removeAttr("disabled");
			$("input[name='txtLabDipNo[]']").removeAttr("disabled");
			$("select[name='cboColorRange[]']").removeAttr("disabled");
			
			$("select[name='cboConsUom[]']").removeAttr("disabled");
			$("input[name='txtBookingQnty[]']").removeAttr("disabled");
			$("input[name='txtAvgRate[]']").removeAttr("disabled");
			$("input[name='txtAmount[]']").removeAttr("disabled");
			$("select[name='cboUom[]']").removeAttr("disabled");
			$("input[name='txtFinishQty[]']").removeAttr("disabled");
			
			$("input[name='txtPPQty[]']").removeAttr("disabled");
			$("input[name='txtMTLQty[]']").removeAttr("disabled");
			$("input[name='txtFPTQty[]']").removeAttr("disabled");
			$("input[name='txtGPTQty[]']").removeAttr("disabled");
			$("input[name='txtProcessLoss[]']").removeAttr("disabled");
			
			$("input[name='txtGreyQty[]']").removeAttr("disabled");
			$("select[name='cboWorkScope[]']").removeAttr("disabled");
			$("input[name='txtProcessNameMain[]']").removeAttr("disabled");
			$("input[name='txtProcessName[]']").removeAttr("disabled");
			$("input[name='txtRemarks[]']").removeAttr("disabled");
			$("input[name='barcode_no[]']").removeAttr("disabled");
			$("input[name='increase[]']").removeAttr("disabled");
			$("input[name='decrease[]']").removeAttr("disabled");

			$('#approval_msg_td').text("");
			$('#save1').removeAttr("disabled",true);

		});


		/* var row_num=$('#tbl_item_details tbody tr').length;
		for(var i=1; i<=row_num; i++)
		{
			$('#txtBookingQnty_'+i).val('');
			$('#updateIdDtls_' + i).val('');
		} */

		//Yarn popup data string modify here
		var modified_yarnData = "";
		$("#table_yarn_details").find('tbody tr').each(function () 
		{
            var yarnData = $(this).find('input[name="yarnData[]"]').val();
			var yarnData_id = $(this).find('input[name="yarnData[]"]').attr("id");
			if(yarnData != "") 
			{
				var yarnData_arr =  yarnData.split('|');
				if(yarnData_arr.length > 0)
				{
					for (let index = 0; index < yarnData_arr.length; index++) 
					{
						var yarnData_details= yarnData_arr[index].split("_");
						

						if (modified_yarnData == "") {
							modified_yarnData = yarnData_details[0] + "_" + yarnData_details[1] + "_" + yarnData_details[2] + "_" + yarnData_details[3] + "_" + yarnData_details[4] + "_" + "" + "_" + "" + "_" + yarnData_details[7] + "_" + yarnData_details[8] + "_" + "" + "_" + "";
						}
						else{
							modified_yarnData += "|" + yarnData_details[0] + "_" + yarnData_details[1] + "_" + yarnData_details[2] + "_" + yarnData_details[3] + "_" + yarnData_details[4] + "_" + "" + "_" + "" + "_" + yarnData_details[7] + "_" + yarnData_details[8] + "_" + "" + "_" + "";
						}

						//modified_yarnData = cboYarnCount + "_" + cboComposition + "_" + txtPerc + "_" + txtColor + "_" + cboYarnType + "_" + txtConsRatio + "_" + txtConsQty + "_" + cboSupplier+ "_" + cboBrand+ "_" + txtUnitRate+ "_" + txtConsAmount;
					}
					$("#"+yarnData_id).val(modified_yarnData);
					modified_yarnData ="";
				}
			}

    	});


		//end
		

	}

	if(type==1)
	{
		
		$('#txt_copy_from').val(job_no_prev_id);
		$('#update_id_copy').val(update_id_copy);
		$("#copy_id").attr("disabled","disabled");
		
	}
	else
	{
		$('#txt_copy_from').val('');

	}
	if ( document.getElementById('copy_id').checked==true)
	{
		document.getElementById('copy_id').value=1;
		set_button_status(0, permission, 'fnc_fabric_sales_order_entry_v2',1,1);
		//set_button_status(0, permission, 'fnc_fabric_yarn_dtls_entry',1,1);
		set_button_status(0, permission, 'fnc_fabric_yarn_dtls_entry', 2);
		
	}
	else if(document.getElementById('copy_id').checked==false)
	{
		document.getElementById('copy_id').value=2;
	}
	//alert(type );
	
}

function show_fabric_yarn_details(update_id) 
{
	var copy_val=$('#copy_id').val();
	// //alert(copy_val);
	var prev_update_id=$('#txt_copy_from').val();
	/* if(copy_val ==1)
	{
		prev_update_id=$('#txt_copy_from').val();
	}
	else
	{
		prev_update_id=$('#txt_job_no').val();
	} */

	//prev_update_id=$('#txt_copy_from').val();
	//prev_update_id=$('#txt_job_no').val();

    //show_list_view(update_id,'yarn_details','yarn_details_list_view','requires/fabric_sales_order_entry_v2_controller','',0);
    //var datas = return_global_ajax_value(update_id+'__'+prev_update_id, 'yarn_details', '', 'requires/fabric_sales_order_entry_v2_controller');
    var datas = return_global_ajax_value(update_id+'__'+prev_update_id, 'yarn_details', '', 'requires/fabric_sales_order_entry_v2_controller');

    var yarn_datas = trim(datas).split("##")
    $('#yarn_details_list_view').html(yarn_datas[0]);
    $('#total_yarn_sum').html(yarn_datas[2]);

    /*var button_status=0;
     //if(parseInt(yarn_datas[1])>1) {button_status=1;}
     if( $('#table_yarn_details tbody tr').length>0){button_status=1;}
     //if( $('#txtGreyQtyY_1').val()*1)>0  {button_status=1;}
     set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry',2); */

	var button_status = 0;
		if (parseInt(yarn_datas[3]) ==1) {
		button_status = 1;
	}

    set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry', 2);
	// $("#copy_id").removeAttr("disabled",true);
	// $('#copy_id').attr('checked', false);
	// document.getElementById('copy_id').value=2;

}

     function apply_last_update() {
     	if (form_validation('txt_job_no', 'Job No') == false) {
     		return;
     	}

     	var within_group = $('#cbo_within_group').val();
     	var update_id = $('#update_id').val();
     	var txt_booking_no = $('#txt_booking_no').val();
     	if (within_group == 1) {
     		var approved_data = trim(return_global_ajax_value(txt_booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_v2_controller'));
     		var approved_data_arr=approved_data.split('**');
     		var approved=approved_data_arr[0];
     		if (approved != 1) {

     			var data_for_setup=approved_data_arr[4]+"_"+approved_data_arr[1]+"_"+approved_data_arr[2]+"_"+approved_data_arr[3];
     			var response=return_global_ajax_value( data_for_setup, 'check_approvl_necessity_setup_revised', '', 'requires/fabric_sales_order_entry_v2_controller');
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
     		show_list_view(update_id + '**' + txt_booking_no, 'show_fabric_details_last_update', 'order_details_container', 'requires/fabric_sales_order_entry_v2_controller', '');

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
     	show_list_view('', 'show_change_bookings', 'list_change_booking_nos', 'requires/fabric_sales_order_entry_v2_controller', 'setFilterGrid(\'tbl_list_search_revised\',-1);');
     }

     function btn_load_change_bookings(){
     	var count = trim(return_global_ajax_value("", 'btn_load_change_bookings', '', 'requires/fabric_sales_order_entry_v2_controller'));
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


        var approved_data = trim(return_global_ajax_value(booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_v2_controller'));

        var approved_data_arr=approved_data.split('**');
        var approved=approved_data_arr[0];
        if (approved != 1) {

        	var data_for_setup=approved_data_arr[4]+"_"+approved_data_arr[1]+"_"+approved_data_arr[2]+"_"+approved_data_arr[3];
        	var response=return_global_ajax_value( data_for_setup, 'check_approvl_necessity_setup_revised', '', 'requires/fabric_sales_order_entry_v2_controller');
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


            load_drop_down('requires/fabric_sales_order_entry_v2_controller', cbo_company_id, 'load_drop_down_location', 'location_td');
            get_php_form_data(cbo_company_id, 'process_loss_method', 'requires/fabric_sales_order_entry_v2_controller');

            var color_from_library = $('#color_from_library').val();

            get_php_form_data(job_id, "populate_data_from_sales_order", "requires/fabric_sales_order_entry_v2_controller");

            var within_group = $('#cbo_within_group').val();
            show_list_view(job_id + "**" + color_from_library + "**" + within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_v2_controller', '');
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
        	var booking_without_order = document.getElementById('booking_without_order').value;
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
        	} else if (type == 4) {
        		if (booking_without_order==1) {
        			var action_method = "action=show_fabric_booking_report7";
        			report_title = "&report_title=Sample Fabric Booking -Without order";
        			http.open("POST", "../order/woven_order/requires/sample_booking_non_order_controller.php", true);
        		}else{
        			report_title = "&report_title=Sample Fabric Booking Urmi";
        			http.open("POST", "../order/woven_order/requires/sample_booking_controller.php", true);
        		}
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
function openmypage_process(incr_id)
{
	var txt_process_id = $('#txtProcessId_'+incr_id).val();
	var txt_process_seq = $('#txtProcessSeq_'+incr_id).val();

	var title = 'Process Name Selection Form';
	var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup';

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

function openmypage_process_main(incr_id)
{
    var txt_process_id = $('#txtProcessIdMain_'+incr_id).val();
    var txt_process_seq = $('#txtProcessSeqMain_'+incr_id).val();

    var title = 'Process Name Selection Form';
    var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup_main';

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
        var process_id=this.contentDoc.getElementById("hidden_process_id").value;    //Access form field with id="emailfield"
        var process_name=this.contentDoc.getElementById("hidden_process_name").value;
        var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;

        $('#txtProcessIdMain_'+incr_id).val(process_id);
        $('#txtProcessNameMain_'+incr_id).val(process_name);
        $('#txtProcessSeqMain_'+incr_id).val(process_seq);
        

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
        $('#txtGreyQty_'+i).val(finishQnty);

        if(bodypart_with_type[$('#cboBodyPart_'+i).val()] ==40 || bodypart_with_type[$('#cboBodyPart_'+i).val()] ==50)
        {
            if(cboUom==12)
            {
                $('#txtFinishQty_'+i).val($('#hiddenFinishQty_'+i).val());    
            }
        }

    calculate_grey_qty(i);
}

function show_ccmp(i)
{
    if(bodypart_with_type[$('#cboBodyPart_'+i).val()] ==40 || bodypart_with_type[$('#cboBodyPart_'+i).val()] ==50)
    {
        $('#txtBookingQnty_' + i).removeAttr("onClick").attr("onClick", "openmypage_ccmp(" + i + ");");

        $('#cboPly_' + i).removeAttr("disabled");
        $('#txtBookingQnty_' + i).attr("readonly",true);

        $('#txtPPQty_' + i).attr("disabled","disabled");
        $('#txtMTLQty_' + i).attr("disabled","disabled");
        $('#txtFPTQty_' + i).attr("disabled","disabled");
        $('#txtGPTQty_' + i).attr("disabled","disabled");
        $('#txtProcessLoss_' + i).attr("disabled","disabled");

        $('#cboConsUom_'+ i).val(1);
        refresh_fields(i);
        $('#cboConsUom_' + i).attr("disabled","disabled");
    }
    else
    {
        $('#txtPPQty_' + i).removeAttr("disabled");
        $('#txtMTLQty_' + i).removeAttr("disabled");
        $('#txtFPTQty_' + i).removeAttr("disabled");
        $('#txtGPTQty_' + i).removeAttr("disabled");
        $('#txtProcessLoss_' + i).removeAttr("disabled");

        $('#cboConsUom_'+ i).val(12);
        $('#cboConsUom_' + i).removeAttr("disabled");

        $('#txtBookingQnty_' + i).removeAttr("readonly");
        $('#txtBookingQnty_' + i).removeAttr("onClick");
        $('#cboPly_' + i).attr("disabled","disabled");
        $('#cboPly_' + i).val("0");
    }
}

function openmypage_ccmp(i)
{
    var update_dtls_id = $('#updateIdDtls_'+i).val();
    var ccmp_save_data = $('#ccmpSaveData_'+i).val();
    var hdnCollerCuffCon = $('#hdnCollerCuffCon_'+i).val();
    //var txtFinishQty = $('#txtFinishQty_'+i).val();

    var bodypart_type = bodypart_with_type[$('#cboBodyPart_'+i).val()];

    if(bodypart_type ==40 || bodypart_type ==50)
    {
        var title = 'Collar and cuff measurement Form';
        var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?update_dtls_id='+update_dtls_id+'&bodypart_type='+bodypart_type+'&ccmp_save_data='+ccmp_save_data+'&hdnCollerCuffCon='+hdnCollerCuffCon+'&action=collar_cuff_masurement_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=470px,center=1,resize=1,scrolling=0','');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hidden_ccmp_save_data=this.contentDoc.getElementById("hidden_ccmp_save_data").value;
            var booking_quantity=this.contentDoc.getElementById("hdn_total_collar_qnty_without_ex_percent").value;
            var finish_quantity=this.contentDoc.getElementById("grand_cons").value;
            var txt_collercuff_con=this.contentDoc.getElementById("txt_collercuff_con").value;

            if(bodypart_with_type[$('#cboBodyPart_'+i).val()] ==40 || bodypart_with_type[$('#cboBodyPart_'+i).val()] ==50)
            {
                $('#ccmpSaveData_'+i).val(hidden_ccmp_save_data);
                $('#txtBookingQnty_'+i).val(booking_quantity);
                $('#txtFinishQty_'+i).val(finish_quantity);
                $('#hiddenFinishQty_'+i).val(finish_quantity);
                $('#txtTotalFinishQty_'+i).val(finish_quantity);
                $('#txtGreyQty_'+i).val(finish_quantity);
                $('#hdnCollerCuffCon_'+i).val(txt_collercuff_con);
            }
            else
            {
                $('#ccmpSaveData_'+i).val("");
            }
        }
    }
    else
    {
         $('#ccmpSaveData_'+i).val("");
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
    var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?txtFabricDesc='+txtFabricDesc+'&txtFabricGsm='+txtFabricGsm+'&txtAfterFabricGsm='+txtAfterFabricGsm+'&txt_fso_ref_id='+txt_fso_ref_id+'&thisAdjustQntyString='+thisAdjustQntyString+'&all_adjust_string='+all_adjust_string+'&update_id='+update_id+'&action=adjust_quantity_popup';

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

		var process_loss_method = $('#txtProcessLoss_'+incr_id).val() * 1;
		var finish_qty = $('#txtFinishQty_' + incr_id).val() * 1;

		var txtPPQty = $('#txtPPQty_' + incr_id).val() * 1;
		var txtMTLQty = $('#txtMTLQty_' + incr_id).val() * 1;
		var txtFPTQty = $('#txtFPTQty_' + incr_id).val() * 1;
		var txtGPTQty = $('#txtGPTQty_' + incr_id).val() * 1;

		finish_qty +=txtPPQty+txtMTLQty+txtFPTQty+txtGPTQty;
		var adjust_finish_qnty = (finish_qty/(finish_qty + (finish_qty*process_loss_method/100)))*hdn_total_adjust;
		$("#txtAdjustFinQty_"+incr_id).val(adjust_finish_qnty);

    }
}

function company_wise_load(company_id) 
{
	get_php_form_data( company_id,'company_wise_load' ,'requires/fabric_sales_order_entry_v2_controller');	
}


function openmypage_ld_approval() 
{
	var cbo_company_id = $('#cbo_company_id').val();
	var txt_job_no = $('#txt_job_no').val();

	var update_id = $('#update_id').val();
	
	if (form_validation('cbo_company_id*txt_job_no', 'Company*Sales Order No') == false) {
		return;
	}

	var title = 'Yarn Details Info';
	var page_link = 'requires/fabric_sales_order_entry_v2_controller.php?action=ld_approval_popup&fso_mst_id=' + update_id;

	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0', '');

	emailwindow.onclose = function () {
		//var theform = this.contentDoc.forms[0];
		//var yarn_data = this.contentDoc.getElementById("hidden_yarn_data").value;

		//$('#yarnData_' + i).val(yarn_data);
		
	}

}


</script>
</head>
	<body onLoad="set_hotkey(); btn_load_change_bookings(); active_inactive();">
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
									<td>
										<strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled >
									</td>
								</tr>
								<tr>
									<td width="110" class="must_entry_caption">Company</td>
									<td width="190">
										<?
										echo create_drop_down("cbo_company_id", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', 0, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value,'process_loss_method' ,'requires/fabric_sales_order_entry_v2_controller'); active_inactive_check(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_sales_order_entry_v2_controller' );company_wise_load(this.value)", '', '', '', '', '');//load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', document.getElementById('cbo_within_group').value + '_' + this.value, 'load_drop_down_buyer', 'buyer_td' );
										?>
									</td>
									<td width="110" class="must_entry_caption">Within Group</td>
									<td>
										<?
										echo create_drop_down("cbo_within_group", 162, $yes_no, "", 0, "--  --", 2, "active_inactive();",1);
                                        //load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+'_80', 'load_drop_down_buyer', 'buyer_td' );
										?>
									</td>
									<td width="110" class="must_entry_caption">Sales Job/Booking No.</td>
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
										<input type="hidden" name="hdn_auto_allocation" id="hdn_auto_allocation">
										<input type="hidden" name="hdn_auto_allocate_yarn" id="hdn_auto_allocate_yarn">
									</td>
								</tr>
								<tr>
                                    
                                    <td class="must_entry_caption">Location</td>
                                    <td id="location_td">
                                        <?
                                        echo create_drop_down("cbo_location_name", 162, $blank_array, "", 1, "-- Select Location --", 0, "");
                                        ?>
                                    </td>
									<td class="must_entry_caption">Booking Date</td>
									<td>
										<input type="text" name="txt_booking_date" id="txt_booking_date" class="datepicker"
										style="width:150px;" value="<? echo date("d-m-Y"); ?>" readonly disabled/>
									</td>
									
									<td>Receive Date</td>
									<td>
										<input type="text" name="booking_approval_date" id="booking_approval_date" class="datepicker"
										style="width:150px;" readonly disabled/>
									</td>
								</tr>
                                <tr>
                                    <td>Delivery Start Date</td>
                                    <td>
                                        <input type="text" name="txt_delivery_start_date" id="txt_delivery_start_date" class="datepicker"
                                        style="width:150px;" placeholder="Display" readonly disabled/>
                                    </td>
                                    <td class="must_entry_caption">Delivery End Date</td>
                                    <td>
                                        <input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker"
                                        style="width:150px;" placeholder="Display" readonly disabled/>
                                    </td>
                                    <td title="buyer/Unit" class="must_entry_caption">Customer</td>
                                    <td id="buyer_td">
                                        <?
                                        echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "", 1);
                                        ?>
                                    </td>
                                </tr>
								<tr>
                                    <td class="must_entry_caption">Cust. Buyer</td>
                                    <td id="cust_buyer_td">
                                        <?
                                        echo create_drop_down("cbo_cust_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "");
                                        ?>
                                    </td>
									<td>Cust. Buyer Brand</td>
                                    <td id="cust_buyer_brand_td">
                                        <?
                                        echo create_drop_down("cbo_cust_buyer_brand", 162, $blank_array, "", 1, "-- Select Brand --", 0, "", 1);
                                        ?>
                                    </td>
									<td class="must_entry_caption">Currency</td>
                                    <td>
                                        <?
                                        echo create_drop_down("cbo_currency", 162, $currency, "", 1, "-- Select Currency --", 0, "", 1);
                                        ?>
                                    </td>
									
								</tr>
								<tr>
                                    <td class="must_entry_caption">Merch/Style Ref. No.</td>
                                    <td>
                                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:150px;" maxlength="500" placeholder="Display" readonly/>
                                    </td>
									<td>From FSO Ref.</td>
                                    <td>
                                        <input type="text" name="txt_fso_ref_show" id="txt_fso_ref_show" class="text_boxes" style="width:150px;" placeholder="Double Click" onDblClick="openmypage_from_fso()" onblur="chkHiddenFromFSO()"/>
                                        <input type="hidden" name="txt_fso_ref" id="txt_fso_ref" class="text_boxes" />
                                        <input type="hidden" name="txt_fso_ref_id" id="txt_fso_ref_id" class="text_boxes" />
                                    </td>
									<td class="must_entry_caption">Team Leader</td>
									<td>
										<?
                                        //echo create_drop_down("cbo_team_leader", 162, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select Team Leader--", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_dealing_merchant', 'team_td' );", 1);

                                        $teamArr=array();
                                        $teamSql=sql_select("select id, team_name, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name");
                                        foreach($teamSql as $row)
                                        {
                                            $teamArr[$row[csf("id")]]=$row[csf("team_leader_name")].'['.$row[csf("team_name")].']';
                                        }
                                        unset($teamSql);

                                        echo create_drop_down( "cbo_team_leader", 162, $teamArr,"", 1, "-- Select Team Leader--", 0, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', document.getElementById('cbo_within_group').value+'_'+this.value, 'load_drop_down_dealing_merchant', 'team_td' );", 1 );
										?>
									</td>
									
								</tr>
								<tr>
                                    <td>Dealing Merchant</td>
                                    <td id="team_td">
                                        <?
                                        echo create_drop_down("cbo_dealing_merchant", 162, $blank_array, "", 1, "-- Select Team Member --", $selected, "");
                                        ?>
                                    </td>
                                    <td>Order Nature</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_order_nature", 162, $fbooking_order_nature,"", 1, "--Select Type--", 0, "",0 );
                                        ?>
                                    </td>
                                    <td>Season Year</td>
                                    <td>
                                         <?
                                            $selected_year=date("Y");
                                            echo create_drop_down( "cbo_season_year", 162, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                                        ?>
                                    </td>
									
								</tr>
								<tr>
									<td>Garments Merchant</td>
                                    <td>
                                        <input type="text" name="txt_garments_merchant" id="txt_garments_merchant" class="text_boxes"
                                        style="width:150px;"/>
                                    </td>
									<td>Job Status</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_fso_status", 162, $row_status,"", 0, "--Select Status--", 1, "",0,'1,3' );
                                        ?>
                                    </td>
									<td>Attention</td>
                                    <td>
                                        <input type="text" name="txt_attention" id="txt_attention" class="text_boxes"
                                        style="width:150px;"/>
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
                                    
                                    <!--<td>Fabric Composition</td>
                                    <td>
                                        <input type="text" name="txt_fab_comp" id="txt_fab_comp" class="text_boxes"
                                        style="width:150px;"/>
                                    </td> -->
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE"
                                        onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry_v2', 0 ,1)">
                                    </td>
                                </tr>
								<tr>
									<td align="">Ready To Approved</td>
									<td align="left" height="10">
										<?
										echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", 2, "","","" );
										?>
									</td>
                                    <td>Ship to</td>
                                    <td>
                                        <input type="text" name="txt_ship_to" id="txt_ship_to" class="text_boxes" style="width:150px;"/>
                                    </td>
									<td></td>
									<td>
                                        <input type="button" class="image_uploader" style="width:140px" value="ADD FILE"
                                        onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'fabric_sales_order_entry_v2', 2 ,1)">
                                    </td>
								</tr>
                                <tr>
									<td>Copy From</td>
									<td>
										<input type="text" name="txt_copy_from" id="txt_copy_from" class="text_boxes" value="" style="width:150px;" disabled="disabled" />
										<input type="hidden" name="update_id_copy" id="update_id_copy"/>
										<input type="hidden" name="update_type" id="update_type" value="1"/>
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
                                        terms_condition(472,'txt_job_no','../');
                                        ?>
                                    </td>
                                </tr>
								<tr>
								<td>Remarks</td>
                                    <td colspan="3">
                                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
                                        style="width:455px;" maxlength="500" title="Maximum Characters 500"/>
                                    </td>
                                    <td>&nbsp;</td>
								</tr>
                                <tr>
                                    <td colspan="2" id="approval_msg_td" style="font-size:18px; color:#F00"  valign="top" align="center"></td>
                                </tr>
							</table>
						</fieldset>
					</div>
					<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div id="list_change_booking_nos"
					style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
					<div align="left">
						<fieldset style="width:2163px; margin-top:10px">
							<legend>Fabric Details</legend>
							<table cellpadding="0" cellspacing="0" width="2600" class="rpt_table" border="1" rules="all"
							id="tbl_item_thead">
							<thead>
                                <tr>
                                    <th colspan="5">&nbsp;</th>
                                    <th colspan="2">GSM</th>
                                    <th colspan="15">&nbsp;</th>
                                    <th colspan="4">Sample</th>
                                    <th colspan="11">&nbsp;</th>
                                </tr>
                                <tr>
    								<th width="25">SL</th>
    								<th width="82">Garment Item</th>
    								<th width="82" class="must_entry_caption">Body Part</th>
    								<th width="72">Color Type</th>
    								<th width="152" class="must_entry_caption">Fabric Description</th>
                                    <th width="57" class="must_entry_caption">Before Wash</th>
                                    <th width="57">After Wash</th>
    								<th width="57">Ply</th>
                                    <th width="52" class="must_entry_caption">Fabric Dia</th>
    								<th width="52">Cuttable Dia</th>
    								<th width="82">Dia/ Width Type</th>
                                    <th width="87">Color</th>
    								<th width="50">Image</th>
                                    <th width="100">Lab Dip No</th>
    								<th width="82" class="must_entry_caption">Color Range</th>
    								<th width="52">Cons. UOM</th>
    								<th width="67">Booking Qty.</th>
    								<th width="57" class="must_entry_caption">Avg. Price</th>
    								<th width="72" class="must_entry_caption">Amount</th>
    								<th width="52">UOM</th>
    								<th width="52">Adjusted Finish Qty</th>
                                    <th width="67" class="must_entry_caption">Finish Qty.</th>
                                    <th width="67">PP</th>
                                    <th width="67">MTL</th>
                                    <th width="67">FPT/GPT</th>
                                    <th width="67">Buying/SMS</th>
    								<th width="67">Total Finish</th>
    								<th width="52">Process <br>Loss %</th>
    								<th width="52">Adjust Grey Qty.</th>
    								<th width="67">Grey Qty.</th>
    								<th width="82">Work Scope</th>
                                    <th width="100">Main Process &nbsp; <input type="checkbox" checked id="processChkMain" name="processChkMain"/></th>
    								<th width="100">Sub Process &nbsp;<input type="checkbox" checked id="processChk" name="processChk"/></th>
    								<th width="100">Remarks</th>
                                    <th width="100">Barcode No</th>
    								<th width="70"></th>
                                </tr>
							</thead>
						</table>
						<div style="width:2620px; max-height:260px; overflow-y:scroll;" id="list_container_batch"
						align="left">
						<table cellpadding="0" cellspacing="0" width="2600" class="rpt_table" border="1" rules="all"
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
									echo create_drop_down("cboBodyPart_1", 80, $body_part, "", 1, "- Select -", 0, "show_ccmp(1);", "1", "", "", "", "", "", "", "cboBodyPart[]");
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

                                <td width="57">
                                    <input type="text" name="txtAfterFabricGsm[]" id="txtAfterFabricGsm_1" class="text_boxes_numeric" style="width:45px" />
                                </td>
                                <td width="52">
                                   <?
                                    $plyArr = array(3=>3,4=>4,5=>5);
                                    echo create_drop_down("cboPly_1", 57, $plyArr, "", 1, "- Select -", 0, "", "1", "", "", "", "", "", "", "cboPly[]");
                                    ?>
                                </td>

								<td width="52">
									<input type="text" name="txtFabricDia[]" id="txtFabricDia_1" class="text_boxes"
									style="width:40px"
									onKeyUp="calculate_fin_qty(1);copy_process_loss(1);"
									disabled="disabled"/>
								</td>
                                <td width="52">
                                    <input type="text" name="txtFabricCuttableDia[]" id="txtFabricCuttableDia_1" class="text_boxes" style="width:40px" />
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
                                <td width="50">
                                    <input type="button" class="image_uploader" style="width:50px" id="btnimg_1" value="IMG" onClick="file_uploader ( '../','','', 'fabric_sales_order_entry_v2', 0 ,1)" />
                                </td>
                                <td width="100">
                                    <input type="text" name="txtLabDipNo[]" id="txtLabDipNo_1" class="text_boxes" style="width:90px" value="" />
                                </td>
								<td width="82">
									<?
									echo create_drop_down("cboColorRange_1", 80, $color_range, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "cboColorRange[]");
									?>
								</td>
								<td width="52">
									<?
									echo create_drop_down("cboConsUom_1", 50, $unit_of_measurement, "", 0, "", 12, "refresh_fields(1)", "1", "1,12,27,23", "", "", "", "", "", "cboConsUom[]");
									?>
								</td>
								<td width="67">
									<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_fin_qty(1);copy_process_loss(1); calculate_grey_qty(1);" onclick="openmypage_ccmp(1);" 	readonly/>
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
								<td width="52">
									<input type="text" name="txtAdjustFinQty[]" id="txtAdjustFinQty_1" class="text_boxes_numeric"
									style="width:45px" readonly/>
								</td>
								<td width="67">
									<input type="text" name="txtFinishQty[]" id="txtFinishQty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);" readonly/>
									<input type="hidden" name="rmgQty[]" id="rmgQty_1" readonly/>
                                    <input type="hidden" name="hiddenFinishQty[]" id="hiddenFinishQty_1" readonly/>
								</td>

                                <td width="67">
                                    <input type="text" name="txtPPQty[]" id="txtPPQty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);calculate_fin_qty(1);" />
                                </td>
                                <td width="67">
                                    <input type="text" name="txtMTLQty[]" id="txtMTLQty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);calculate_fin_qty(1);" />
                                </td>
                                <td width="67">
                                    <input type="text" name="txtFPTQty[]" id="txtFPTQty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);calculate_fin_qty(1);" />
                                </td>
                                <td width="67">
                                    <input type="text" name="txtGPTQty[]" id="txtGPTQty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1); calculate_grey_qty(1);copy_process_loss(1);calculate_fin_qty(1);" />
                                </td>
                                <td width="67">
                                    <input type="text" name="txtTotalFinishQty[]" id="txtTotalFinishQty_1" class="text_boxes_numeric" style="width:55px" readonly disabled />
                                </td>

								<td width="52">
									<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_1" class="text_boxes_numeric" style="width:40px"
									onKeyUp="calculate_grey_qty(1)"/>
								</td>
								<td width="52">
									<input type="text" name="txtAdjustQnty[]" id="txtAdjustQnty_1" class="text_boxes_numeric" style="width:40px" ondblclick="openmypage_adjust_qnty(1)" placeholder="Double click" readonly disabled/>
									<input type="hidden" name="txtAdjustQntyString[]" id="txtAdjustQntyString_1" />
								</td>
								<td width="67">
									<input type="text" name="txtGreyQty[]" id="txtGreyQty_1" class="text_boxes_numeric" style="width:55px" readonly/>
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
                                </td>
								<td width="100">
									<input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(1);" readonly />
									<input type="hidden" name="txtProcessId[]" id="txtProcessId_1" value="" />
                                    <input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_1" value="" />
								</td>
								<td width="100">
									<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:90px;" readonly/>
								</td>
								<td width="100">
									<input type="text" name="barcode_no[]" id="barcode_no_1" class="text_boxes" style="width:90px;"  readonly/>
								</td>
								<td width="70">
									<input type="button" id="increase_1" name="increase[]" style="width:27px"
									class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"/>
									<input type="button" id="decrease_1" name="decrease[]" style="width:27px"
									class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes" readonly/>

									<input type="hidden" name="ccmpSaveData[]" id="ccmpSaveData_1" class="text_boxes" readonly/>
                                    <input type="hidden" name="hdnCollerCuffCon[]" id="hdnCollerCuffCon_1" class="text_boxes" readonly/>
								</td>
							</tr>

						</tbody>
						<tfoot>
							<tr style="font-weight: bold;">
								<td align="right" colspan="18">Total</td>
								<td align="right" id="total_amnt"></td>
								<td colspan="7"></td>
                                <td align="right" id="total_finish_sum"></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right" id="total_grey_sum"></td>
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
						echo load_submit_buttons($permission, "fnc_fabric_sales_order_entry_v2", 0, 0, "reset_form('fabricOrderEntry_1*fabricOrderEntry_2','yarn_details_list_view*approval_msg_td','','','disable_enable_fields(\'cbo_company_id*cbo_within_group\');active_inactive();','')", 1);



						?>
						<input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();"
						style="visibility: hidden;"/>
						<input id="print1" class="formbutton_disabled printBtn" type="button" style="width:80px; display:none;" onClick="fnc_fabric_sales_order_entry_v2(4)" name="print1" value="Print">

						<input type="button" name="print_2" class="formbuttonplasminus formbutton_disabled printBtn" value="Print 2" id="print_2" onClick="fabric_sales_order_print3();" style="width:80px; display:none;" />
                        <input type="button" name="print_3" class="formbuttonplasminus formbutton_disabled printBtn" value="KDS" id="print_3" onClick="fabric_sales_order_print4();" style="width:80px;" />
                        <input type="button" name="print_5" class="formbuttonplasminus formbutton_disabled printBtn" value="Print 4" id="print_5" onClick="fabric_sales_order_print5();" style="width:80px; display:none;" />
                        <input type="button" name="print_6" class="formbuttonplasminus formbutton_disabled printBtn" value="Print 6" id="print_6" onClick="fabric_sales_order_print6();" style="width:80px; display:none;" />
                        <input type="button" name="print_7" class="formbuttonplasminus formbutton_disabled printBtn" value="Merchant Print" id="print_7" onClick="fabric_sales_order_print7();" style="width:80px; display:none;" />
                        <input type="button" name="print_8" class="formbuttonplasminus formbutton_disabled printBtn" value=" CBD " id="print_8" onClick="fabric_sales_order_print8();" style="width:80px; display:none;" />
						<input type="hidden" name="process_loss_method" id="process_loss_method" readonly>
						<input type="hidden" name="color_from_library" id="color_from_library" readonly>
						<input type="hidden" name="is_approved" id="is_approved" readonly>
						<input type="hidden" name="update_id" id="update_id"/>
						
						<input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
                        <input type="hidden" name="deletedDtlsIds" id="deletedDtlsIds" readonly/>
						<input type="hidden" name="txt_sales_target" id="txt_sales_target" readonly/>
					</td>
				</tr>
			</table>
		</div>
	</form>
	
	<div style="float: left;">
		<input type="button" id="btn_ld_fbl_approval" class="formbutton_disabled" style="width:180px; margin-right:100px;" value="LD, FBL & Strike Approval" onClick="openmypage_ld_approval()" />
	</div>
	<div style="float: left;">
		<form name="fabricOrderEntry_2" id="fabricOrderEntry_2">
			<fieldset style="width:840px; margin-top:10px">
				<legend>Grey Qty. For Yarn Details Entry</legend>
				<table class="rpt_table" border="1" width="725" cellpadding="0" cellspacing="0" rules="all" id="table_yarn_details">
					<thead>
						<th width="400">Fabric Description</th>
						<th width="100">Fabric GSM</th>
						<th width="100">Color Range</th>
						<th class="must_entry_caption">Grey Quantity</th>
					</thead>
					<tbody id="yarn_details_list_view"></tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th>Total :</th>
							<th id="total_yarn_sum"></th>
						</tr>
					</tbody>
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
	</div>
</fieldset>
</div>
</body>
    <script type="text/javascript">
        
    //$("*", "#order_details_container").prop('disabled',true);
    //$('#fabricOrderEntry_1').find('input, textarea, button, select').prop('disabled',true);
    //$("#fabricOrderEntry_1 :input").prop("disabled", true);
    //$("#fabricOrderEntry_1 input, #fabricOrderEntry_1 select").attr('disabled',"disabled");

    /*var form  = document.getElementById("fabricOrderEntry_1");
    var allElements = form.elements;
    for (var i = 0, l = allElements.length; i < l; ++i) {
        // allElements[i].readOnly = true;
           allElements[i].disabled=true;
    }*/

    </script>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>