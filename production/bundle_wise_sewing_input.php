<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create bundle wise sewing inpur

Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	11-10-2015
Updated by 		: 
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Delivery Entry","../", 1, 1, $unicode,'','');
$mandatory_field_arr = json_encode($_SESSION['logic_erp']['mandatory_field'][96]);
?>

<script>
    var permission = '<? echo $permission; ?>';
    var mandatoryFields = "<?php echo implode('*', $_SESSION['logic_erp']['mandatory_field'][96]);?>";
	var mandatoryFieldArr = mandatoryFields.split('*');

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../logout.php";

    function fnc_company_check(val)
    {
        if(val==1)
        {
            if($("#cbo_company_name").val()==0)
            {
                alert("Please Select Company.");
                $("#cbo_source").val(0);
                $("#cbo_emb_company").val(0);
                return;
            }
            else
            {
                // get_php_form_data(document.getElementById('cbo_emb_company').value,'production_process_control','requires/sewing_input_controller' );
            }
        }
        else
        {
            get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/sewing_input_controller' );
        }
    }

    function openmypage_sysNo()
    {
        var cbo_company_name = $('#cbo_company_name').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_emb_company = $('#cbo_emb_company').val();
        var cbo_location=$('#cbo_location').val();
		var cbo_floor=$('#cbo_floor').val();
		var txt_issue_date=$('#txt_issue_date').val();
        var title = 'Challan Selection Form';
        var page_link = 'requires/bundle_wise_sewing_input_controller.php?cbo_company_name=' + cbo_company_name +'&cbo_source=' + cbo_source +'&cbo_emb_company=' + cbo_emb_company +'&cbo_location='+cbo_location+'&cbo_floor='+cbo_floor+'&txt_issue_date='+txt_issue_date+ '&action=challan_no_popup';

        if (form_validation('cbo_source', 'Source') == false)
        {
            return;
        }
        if(cbo_source==1)
        {
            if (form_validation('cbo_company_name*cbo_source*cbo_emb_company', 'Company Name*Source*Sewing Company') == false)
            {
                return;
            }
        }
        else
        {
            if (form_validation('cbo_company_name*cbo_source*cbo_emb_company', 'Company Name*Source*Sewing Company') == false)
            {
                return;
            }
        }

        
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1080px,height=370px,center=1,resize=0,scrolling=0', '')
        emailwindow.onclose = function()
        {
            var theform = this.contentDoc.forms[0];
            var mst_id = this.contentDoc.getElementById("hidden_mst_id").value;//po id
            var hidden_txt_challan_no = this.contentDoc.getElementById("hidden_txt_challan_no").value;//po id
            //document.getElementById("txt_challan_no").value = "5555";
            document.getElementById("txt_system_id").value = mst_id;
            document.getElementById("txt_challan_no").value = hidden_txt_challan_no;
            if (mst_id != "")
            {
                freeze_window(5);
                reset_form('printembro_1', 'list_view_country*breakdown_td_id', '', '', 'txt_issue_date,<? echo date("d-m-Y"); ?>', 'cbo_company_name*sewing_production_variable*styleOrOrderWisw*delivery_basis*txt_challan_no*txt_system_id*cbo_source*cbo_emb_company');
                get_php_form_data(mst_id, "populate_data_from_challan_popup", "requires/bundle_wise_sewing_input_controller");
                
                var delivery_basis = $('#delivery_basis').val();
                // alert(delivery_basis);
                if (delivery_basis == 3)
                {
                    var bundle_nos = return_global_ajax_value(mst_id, 'bundle_nos', '', 'requires/bundle_wise_sewing_input_controller');
					bundle_nos=bundle_nos.split("**");
						if( bundle_nos[1]==1 )
                    {
						disable_enable_fields( 'cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*cbo_line_no', 1, "", "" );
                    }
					else
                    {
						// disable_enable_fields( 'cbo_source*cbo_emb_company*cbo_location*cbo_floor', 1, "", "" );
                    }
						
                    var response_data = return_global_ajax_value_post(trim(bundle_nos[0]) + "**0**" + mst_id + "**" + $('#cbo_company_name').val() + "**" + $('#cbo_line_no').val(), 'populate_bundle_data_update', '', 'requires/bundle_wise_sewing_input_controller');

                    //var response_data=return_global_ajax_value(trim(bundle_nos)+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+$('#cbo_line_no').val(), 'populate_bundle_data', '', 'requires/bundle_wise_sewing_input_controller');
                    $('#tbl_details tbody tr').remove();
                    $('#tbl_details tbody').prepend(response_data);
                    var tot_row = $('#tbl_details tbody tr').length;
                    $('#txt_tot_row').val(tot_row);
                    var total_qty = 0;
                    $("#tbl_details").find('tbody tr').each(function()
                    {
                        total_qty+=$(this).find('input[name="qty[]"]').val()*1;
                    });
                    $("#total_bndl_qty").text(total_qty);
                   
                    set_button_status(1, permission, 'fnc_issue_print_embroidery_entry', 1, 0);
                }
                else
                {
                    show_list_view(mst_id, 'show_dtls_listview', 'printing_production_list_view', 'requires/bundle_wise_sewing_input_controller', '');
                }
                release_freezing();
            }
        }
        
    }//end function

    function openmypage(page_link, title)
    {
        if (form_validation('cbo_company_name', 'Company Name') == false)
        {
            return;
        }
        else
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0', '')
            emailwindow.onclose = function()
            {
                var theform = this.contentDoc.forms[0];
                var po_id = this.contentDoc.getElementById("hidden_mst_id").value;//po id
                var item_id = this.contentDoc.getElementById("hidden_grmtItem_id").value;
                var po_qnty = this.contentDoc.getElementById("hidden_po_qnty").value;
                var country_id = this.contentDoc.getElementById("hidden_country_id").value;

                if (po_id != "")
                {
                    freeze_window(5);
                    $("#txt_order_qty").val(po_qnty);
                    $('#cbo_item_name').val(item_id);
                    $("#cbo_country_name").val(country_id);

                    childFormReset();//child form initialize

                    get_php_form_data(po_id + '**' + item_id + '**' + $('#cbo_embel_name').val() + '**' + country_id, "populate_data_from_search_popup", "requires/bundle_wise_sewing_input_controller");

                    var variableSettings = $('#sewing_production_variable').val();
                    var styleOrOrderWisw = $('#styleOrOrderWisw').val();

                    if (variableSettings == 1)
                    {
                        $("#txt_issue_qty").removeAttr("readonly");
                    }
                    else
                    {
                        $('#txt_issue_qty').attr('readonly', 'readonly');
                        get_php_form_data(po_id + '**' + item_id + '**' + variableSettings + '**' + styleOrOrderWisw + '**' + $("#cbo_embel_name").val() + '**' + country_id, "color_and_size_level", "requires/bundle_wise_sewing_input_controller");
                    }

                    show_list_view(po_id, 'show_country_listview', 'list_view_country', 'requires/bundle_wise_sewing_input_controller', '');
                    set_button_status(0, permission, 'fnc_issue_print_embroidery_entry', 1, 0);
                    release_freezing();
                }
            }
        }//end else
    }//end function

    function generate_report_file(data, action, page)
    {
        window.open("requires/bundle_wise_sewing_input_controller.php?data=" + data + '&action=' + action, true);
    }


    function fnc_issue_print_embroidery_entry(operation,is_mail_send,mail_id,mail_body)
    {
        freeze_window(operation);
        var company_id = $('#cbo_company_name').val();
        if (operation == 4)
        {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body, 'emblishment_issue_print', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;
        }
        else if (operation == 5)
        {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'emblishment_issue_print_2', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;
        }
        else if (operation == 6)
        {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'emblishment_issue_print_3', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;
        }else if(operation == 7)
        {
            //alert("Test");
            if (confirm('Press  OK to show trims required part\n Press  Cancel to not show trims required part')) {
                show_value=1;
            } else {
                show_value=0
            }
           var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+'*'+show_value + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'emblishment_issue_print_4', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;

        }else if(operation == 8)
        {
            //alert("Test");
            if (confirm('Press  OK to show trims required part\n Press  Cancel to not show trims required part')) {
                show_value=1;
            } else {
                show_value=0
            }
           var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+'*'+show_value + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'sewing_input_issue_print_5', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;

        }else if(operation == 11)
        {
           var report_title = $("div.form_caption").html();
           generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'emblishment_issue_print_11', 'requires/bundle_wise_sewing_input_controller');
           release_freezing();
           return;
        }else if(operation == 13)
        {
            //alert("Test");
            if (confirm('Press  OK to show trims required part\n Press  Cancel to not show trims required part')) {
                show_value=1;
            } else {
                show_value=0
            }
           var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+'*'+show_value + '*' + is_mail_send + '*' + mail_id+ '*' + mail_body,'emblishment_issue_print_13', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;

        }else if(operation == 14)
        {
            //alert("Test");
            /* if (confirm('Press  OK to show trims required part\n Press  Cancel to not show trims required part')) {
                show_value=1;
            } else {
                show_value=0
            } */
           var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+'*'+$('#cbo_source').val()+'*'+$('#cbo_emb_company').val()+'*'+$('#cbo_location').val() +'*'+$('#cbo_floor').val() +'*'+$('#cbo_line_no').val()  +'*'+$('#txt_issue_date').val()  ,'emblishment_issue_print_14', 'requires/bundle_wise_sewing_input_controller');
            release_freezing();
            return;

        }

        if (operation == 0 || operation == 1 || operation == 2)
        {
            var delivery_basis = $('#delivery_basis').val();

            if (delivery_basis == 3)
            {


                var cbo_source = $('#cbo_source').val();
                if (cbo_source == 1) {
                    if (form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date*cbo_location*cbo_floor*cbo_line_no', 'Company Name*Source*Embel.Company*Issue Date*Location*Floor*Line No.') == false)
                    {
                        release_freezing();
                        return;
                    }
                }
                else
                {
                    if (form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date', 'Company Name*Source*Embel.Company*Issue Date') == false)
                    {
                        release_freezing();
                        return;
                    }
                }

                for (var i = 0; i < mandatoryFieldArr.length; i++) {

                if(mandatoryFieldArr[i]=='cbo_shift_name') {
                    if ( form_validation('cbo_shift_name','Shift Name')==false )
                    {
                        release_freezing();
                        return;
                    }
                }
                }

                var current_date = '<? echo date("d-m-Y"); ?>';
                if (date_compare($('#txt_issue_date').val(), current_date) == false)
                {
                    alert("Print Delivery Date Can not Be Greater Than Current Date");
                    release_freezing();
                    return;
                }

                var j = 0;
                var dataString = '';
                $("#tbl_details").find('tbody tr').each(function()
                {
                    var cutNo = $(this).find('input[name="cutNo[]"]').val();
                    var bundleNo = $(this).find("td:eq(1)").text();
					var barcodeNo=$(this).find("td:eq(1)").attr('title');
                    var colorSizeId = $(this).find('input[name="colorSizeId[]"]').val();
                    var orderId = $(this).find('input[name="orderId[]"]').val();
                    var gmtsitemId = $(this).find('input[name="gmtsitemId[]"]').val();
                    var countryId = $(this).find('input[name="countryId[]"]').val();
                    var colorId = $(this).find('input[name="colorId[]"]').val();
                    var sizeId = $(this).find('input[name="sizeId[]"]').val();
                    var qty = $(this).find('input[name="qty[]"]').val();
                    var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
					var isrescan=$(this).find('input[name="isRescan[]"]').val();
					
                    try
                    {
                        j++;

                        dataString += '&bundleNo_' + j + '=' + bundleNo + '&orderId_' + j + '=' + orderId + '&gmtsitemId_' + j + '=' + gmtsitemId + '&countryId_' + j + '=' + countryId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&colorSizeId_' + j + '=' + colorSizeId + '&qty_' + j + '=' + qty + '&dtlsId_' + j + '=' + dtlsId + '&cutNo_' + j + '=' + cutNo+ '&isRescan_' + j + '=' + isrescan+ '&barcodeNo_' + j + '=' + barcodeNo; 
                    }
                    catch (e)
                    {
                        //got error no operation
                    }
                });

                if (j < 1)
                {
                    alert('No data');
                    release_freezing();
                    return;
                }

                var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_organic*txt_system_id*delivery_basis*txt_challan_no*cbo_line_no*cbo_shift_name*cbo_working_company_name*cbo_working_location*txt_remarks', "../") + dataString;
				//alert(data);return;
            }
            else
            {
                if (form_validation('cbo_company_name*txt_order_no*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty*cbo_line_no', 'Company Name*Order No*Source*Embel.Company*Issue Date*Issue Quantity*Line No.') == false)
                {
                    release_freezing();
                    return;
                }
                else
                {
                    var current_date = '<? echo date("d-m-Y"); ?>';
                    if (date_compare($('#txt_issue_date').val(), current_date) == false)
                    {
                        alert("Print Delivery Date Can not Be Greater Than Current Date");
                        release_freezing();
                        return;
                    }
                    var sewing_production_variable = $("#sewing_production_variable").val();
                    var colorList = ($('#hidden_colorSizeID').val()).split(",");

                    var i = 0;
                    var colorIDvalue = '';
                    if (sewing_production_variable == 2)//color level
                    {
                        $("input[name=txt_color]").each(function(index, element) {
                            if ($(this).val() != '')
                            {
                                if (i == 0)
                                {
                                    colorIDvalue = colorList[i] + "*" + $(this).val();
                                }
                                else
                                {
                                    colorIDvalue += "**" + colorList[i] + "*" + $(this).val();
                                }
                            }
                            i++;
                        });
                    }
                    else if (sewing_production_variable == 3)//color and size level
                    {
                        $("input[name=colorSize]").each(function(index, element) {
                            if ($(this).val() != '')
                            {
                                if (i == 0)
                                {
                                    colorIDvalue = colorList[i] + "*" + $(this).val();
                                }
                                else
                                {
                                    colorIDvalue += "***" + colorList[i] + "*" + $(this).val();
                                }
                            }
                            i++;
                        });
                    }

                    var data = "action=save_update_delete&operation=" + operation + "&colorIDvalue=" + colorIDvalue  + get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*cbo_line_no*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*txt_organic*txt_challan_no*txt_system_id*delivery_basis*cbo_working_company_name*cbo_working_location*txt_remarks', "../");
                }
            }

            //alert (data);return;
            // freeze_window(operation);
            http.open("POST", "requires/bundle_wise_sewing_input_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
        }
    }
    
    
   
		
    function fnc_issue_print_embroidery_Reply_info()
    {
        if (http.readyState == 4)
        {
            //release_freezing();return;
            var variableSettings = $('#sewing_production_variable').val();
            var styleOrOrderWisw = $('#styleOrOrderWisw').val();
            var item_id = $('#cbo_item_name').val();
            var country_id = $("#cbo_country_name").val();

            var reponse = http.responseText.split('**');
            if (reponse[0] == 15)
            {
                setTimeout('fnc_issue_print_embroidery_entry(' + reponse[1] + ')', 4000);
            }
			else if(reponse[0]==20)
			{
				alert(reponse[1]); 
				// release_freezing();
				// return;
			}
            else if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)
            {
                if (reponse[3]) {
                    alert("Receive Found Bundle List : " + reponse[3] + " This Bundle Not Any Change.");
                }
                show_msg(trim(reponse[0]));
                $('#cbo_line_no').attr('disabled','true');
                $('#cbo_source').attr('disabled','true'); 
                $('#cbo_emb_company').attr('disabled','true');
                $('#cbo_location').attr('disabled','true');
                $('#cbo_floor').attr('disabled','true');
                $('#txt_issue_date').attr('disabled','true');

                document.getElementById('txt_system_id').value = reponse[1];
                document.getElementById('txt_challan_no').value = reponse[2];
                var delivery_basis = $('#delivery_basis').val();
                if (delivery_basis == 3)
                {
                    set_button_status(1, permission, 'fnc_issue_print_embroidery_entry', 1, 1);
                    if(reponse[0]==2)
                    {
                         window.location.reload();
                    }
                }
                else
                {
                    if(reponse[0]==2)
                    {
                         window.location.reload();
                    }

                    reset_form('', 'list_view_country*breakdown_td_id', '', '', 'txt_issue_date,<? echo date("d-m-Y"); ?>', 'cbo_company_name*sewing_production_variable*styleOrOrderWisw*cbo_source*cbo_emb_company*cbo_knitting_source*cbo_location*cbo_floor*txt_organic*txt_issue_date*txt_remarks');
                    show_list_view(reponse[1], 'show_dtls_listview', 'printing_production_list_view', 'requires/bundle_wise_sewing_input_controller', '');
                    set_button_status(0, permission, 'fnc_issue_print_embroidery_entry', 1, 1);

                }
            }
            else if(reponse[0] == 141)
            {
                alert('This challan can\'t be deleted because bundle no '+reponse[1]+' found in sewing output ');
            }
            else if(reponse[0]==786)
            {
                alert("Projected PO is not allowed to production. Please check variable settings."); return;
            }
            if(reponse[0]!=15)
            {
              release_freezing();
            }
        }
    }


    function childFormReset()
    {
        reset_form('', '', 'txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id', '', '');
        $('#txt_issue_qty').attr('placeholder', '');//placeholder value initilize
        $('#txt_cutting_qty').attr('placeholder', '');//placeholder value initilize
        $('#txt_cumul_issue_qty').attr('placeholder', '');//placeholder value initilize
        $('#txt_yet_to_issue').attr('placeholder', '');//placeholder value initilize
        $("#breakdown_td_id").html('');

    }

    function fn_total(tableName, index) // for color and size level
    {
        var filed_value = $("#colSize_" + tableName + index).val();
        var placeholder_value = $("#colSize_" + tableName + index).attr('placeholder');
        if (filed_value * 1 > placeholder_value * 1)
        {
            if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
                void(0);
            else
            {
                $("#colSize_" + tableName + index).val('');
            }
        }

        var totalRow = $("#table_" + tableName + " tr").length;
        //alert(tableName);
        math_operation("total_" + tableName, "colSize_" + tableName, "+", totalRow);
        if ($("#total_" + tableName).val() * 1 != 0)
        {
            $("#total_" + tableName).html($("#total_" + tableName).val());
        }
        var totalVal = 0;
        $("input[name=colorSize]").each(function(index, element) {
            totalVal += ($(this).val()) * 1;
        });
        $("#txt_issue_qty").val(totalVal);
    }

    function fn_colorlevel_total(index) //for color level
    {
        var filed_value = $("#colSize_" + index).val();
        var placeholder_value = $("#colSize_" + index).attr('placeholder');
        if (filed_value * 1 > placeholder_value * 1)
        {
            if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
                void(0);
            else
            {
                $("#colSize_" + index).val('');
            }
        }

        var totalRow = $("#table_color tbody tr").length;
        //alert(totalRow);
        math_operation("total_color", "colSize_", "+", totalRow);
        $("#txt_issue_qty").val($("#total_color").val());
    }

    function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
    {
        freeze_window(5);

        //childFormReset();//child from reset
        $("#cbo_item_name").val(item_id);
        $("#txt_order_qty").val(po_qnty);
        $("#cbo_country_name").val(country_id);

        get_php_form_data(po_id + '**' + item_id + '**' + $('#cbo_embel_name').val() + '**' + country_id, "populate_data_from_search_popup", "requires/bundle_wise_sewing_input_controller");

        var variableSettings = $('#sewing_production_variable').val();
        var styleOrOrderWisw = $('#styleOrOrderWisw').val();

        if (variableSettings == 1)
        {
            $("#txt_issue_qty").removeAttr("readonly");
        }
        else
        {
            $('#txt_issue_qty').attr('readonly', 'readonly');
        }

        set_button_status(0, permission, 'fnc_issue_print_embroidery_entry', 1, 0);
        release_freezing();
    }

    function pageReset()
    {
        reset_form('printembro_1', 'list_view_country*printing_production_list_view', '', 'txt_issue_date,<? echo date("d-m-Y"); ?>', '');

        $('#cbo_company_name').attr('disabled', 'false');
        $('#tbl_details_order').show();
        $('#printing_production_list_view').show();
        $('#tbl_details_bundle').hide();
        $('#bundle_list_view').hide();
		disable_enable_fields( 'cbo_company_name*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date', 0, "", "" );
    }

    function load_html()
    {
        var delivery_basis = $('#delivery_basis').val();
        $('#printing_production_list_view').val('');

        if (delivery_basis == 3)
        {
            $('#tbl_details_order').hide();
            $('#printing_production_list_view').hide();
            $('#tbl_details_bundle').show();
            $('#tbl_details tbody tr').remove();
            $('#bundle_list_view').show();
            $('#list_view_country').hide();
            $("#txt_bundle_no").focus();
        }
        else
        {
            $('#tbl_details_order').show();
            $('#printing_production_list_view').show();
            $('#tbl_details_bundle').hide();
            $('#bundle_list_view').hide();
            $('#list_view_country').show();
            childFormReset();
        }
        set_button_status(0, permission, 'fnc_issue_print_embroidery_entry', 1, 1);
    }

    function openmypage_bundle(page_link, title)
    {
        if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
		{
			return;
		}
        else
        {
            var is_controll         = trim($('#hidden_variable_cntl').val());
            var preceding_process   = trim($('#hidden_preceding_process').val());

            var bundleNo = '';
            $("#tbl_details").find('tbody tr').each(function()
            {
                bundleNo += ',' + $(this).find("td:eq(1)").text();

            });

            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link + '&bundleNo=' + bundleNo+ '&is_controll=' + is_controll+ '&preceding_process=' + preceding_process, title, 'width=1185px,height=370px,center=1,resize=0,scrolling=0', '')
            emailwindow.onclose = function()
            {
                var theform = this.contentDoc.forms[0];
                var hidden_bundle_nos = this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
                var hidden_source_cond = this.contentDoc.getElementById("hidden_source_cond").value;//bundle no
                fnc_company_check(3);
				//alert(hidden_bundle_nos+" and "+hidden_source_cond)
                if (hidden_bundle_nos != "")
                {
                    //fnc_duplicate_bundle(hidden_bundle_nos);
                    create_row(hidden_bundle_nos, "Browse", hidden_source_cond);
                }
            }
        }//end else
    }//end function

    function fnc_duplicate_bundle(bundle_no)
    {
        // var challan_duplicate = return_ajax_request_value(bundle_no + "__" + $('#cbo_company_name').val(), "challan_duplicate_check", "requires/bundle_wise_sewing_input_controller");
        var challan_duplicate = '';
        var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( ex_challan_duplicate[0]) != '')
        {
           // var alt_str = ex_challan_duplicate[1].split("##");
           // var al_msglc = "Bundle No '" + trim(alt_str[0]) + "' Found in Challan No '" + trim(alt_str[1]) + "'";
            alert(trim(ex_challan_duplicate[0]));
            $('#txt_bundle_no').val('');
            return;
        }
        else
        {
            create_row(bundle_no,'scan','');
        }
        $('#txt_bundle_no').val('');
    }


    $('#txt_bundle_no').live('keydown', function(e) {
        if (e.keyCode === 13)
        {
			if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
			{
				return;
			}
		
            e.preventDefault();
            var txt_bundle_no = trim($('#txt_bundle_no').val().toUpperCase());
		
            var flag = 1;
            $("#tbl_details").find('tbody tr').each(function()
            {
                var bundleNo = $(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
                if (txt_bundle_no == barcodeNo) {
                    alert("Bundle No: " + bundleNo + " already scan, try another one.");
                    $('#txt_bundle_no').val('');
                    flag = 0;
                    return false;
                }
            });

            if (flag == 1)
            {
                fnc_duplicate_bundle(txt_bundle_no);
            }
        }
    });

    function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);

        var row_num =  $('#tbl_details tbody tr').length; //$('#txt_tot_row').val();
   
        var response_data = return_global_ajax_value_post(bundle_nos + "**" + row_num + "****" + $('#cbo_company_name').val() + "**" + vscan + "**" + hidden_source_cond, 'populate_bundle_data', '', 'requires/bundle_wise_sewing_input_controller');
        if (trim(response_data) == '')
        {
            alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
        }

        $('#tbl_details tbody').prepend(response_data);
        var tot_row = $('#tbl_details tbody tr').length;
        if ((tot_row * 1) > 0)
        {
            $('#cbo_company_name').attr('disabled', 'disabled');
        }
        $('#txt_tot_row').val(tot_row);
        var total_qty = 0;
        $("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		$("#total_bndl_qty").text(total_qty);
        release_freezing();
    }



    function fn_deleteRow(rid)
    {
        $("#tr_" + rid).remove();
        var total_qty = 0;
		$("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		$("#total_bndl_qty").text(total_qty);
    }

    function change_mode(source_id)
    {
        if (source_id == 1) {
            get_php_form_data($('#cbo_company_name').val(), 'load_variable_settings', 'requires/bundle_wise_sewing_input_controller');
            // get_php_form_data($('#cbo_company_name').val(), 'load_variable_settings_for_working_company', 'requires/bundle_wise_sewing_input_controller');
            load_html();
        }
        else
        {
            get_php_form_data($('#cbo_company_name').val(), 'load_variable_settings', 'requires/bundle_wise_sewing_input_controller');
            // get_php_form_data($('#cbo_emb_company').val(), 'load_variable_settings_for_working_company', 'requires/bundle_wise_sewing_input_controller');
            load_html();
        }
    }

    function fnc_bundle_wise_input_print(type,is_mail_send,mail_id,mail_body)
    {
        var txt_system_id = $('#txt_system_id').val();
        if (txt_system_id == '')
        {
            alert("Pls,Browse Challan No. ");
            $('#txt_challan_no').focus();
            return;
        }
        var report_title = $("div.form_caption").html();
        
			if(type==7){
				generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print_5', 'requires/bundle_wise_sewing_input_controller');
        		return;
			}
			else if(type==8)
			{
				generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print_8', 'requires/bundle_wise_sewing_input_controller');
			}
            else if(type==9)
			{
				generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+'*'+$('#cbo_emb_company').val()+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body, 'sewing_input_challan_print7', 'requires/bundle_wise_sewing_input_controller');
			}
            else if(type==10)
            {
                generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print_10', 'requires/bundle_wise_sewing_input_controller');
            }
            else if(type==12)
            {
                generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print_12', 'requires/bundle_wise_sewing_input_controller');
                return;
            }
            else if(type==13){
				generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print_15', 'requires/bundle_wise_sewing_input_controller');
        		return;
			}
			else
			{
				generate_report_file($('#cbo_company_name').val() + '*' + $('#txt_system_id').val() + '*' + $('#delivery_basis').val() + '*' + report_title+ '*' + is_mail_send+ '*' + mail_id+ '*' + mail_body,
                'sewing_input_challan_print', 'requires/bundle_wise_sewing_input_controller');
			}
		
		
		

    }



	function openmypage_bundle_rescan(page_link,title)
	{
		if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
		{
			return;
		}
		else
		{
			var bundleNo='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				bundleNo+=$(this).find("td:eq(1)").text()+',';
				
			});
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//bundle no
				var hidden_source_cond=this.contentDoc.getElementById("hidden_source_cond").value;//bundle no
				//alert(hidden_source_cond+"**hhh")
				if (hidden_bundle_nos!="")
				{ 
					create_rescanrow(hidden_bundle_nos,"Browse",hidden_source_cond);
				}
			}
		}//end else
	}//end function	
	
	function create_rescanrow(bundle_nos,vscan,hidden_source_cond)
	{
		var error=0;
		var bundle_arr=bundle_nos.split(",");
		for(var i=0;i<bundle_arr.length;i++)
		{ 
			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo=$(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
				if(bundle_arr[i]==barcodeNo){
					alert("Bundle No: "+bundleNo+" already scan, try another one.");
					$('#txt_bundle_rescan').val('');
					error=1;
					flag=0;
					return false;
				}
			});
		}
		if(error==0)
		{
			freeze_window(5);
			var row_num=$('#txt_tot_row').val();
			var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+vscan+"**"+hidden_source_cond, 'populate_bundle_data_rescan', '', 'requires/bundle_wise_sewing_input_controller');
			$('#tbl_details tbody').prepend(response_data);
			var tot_row=$('#tbl_details tbody tr').length; 
			$('#txt_tot_row').val(tot_row);
			release_freezing();
		}
	}
	
	
	$('#txt_bundle_rescan').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
			{
				return;
			}
			e.preventDefault();
			var txt_bundle_no=trim($('#txt_bundle_rescan').val().toUpperCase());
			var flag=1;
			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo=$(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
				if(txt_bundle_no==barcodeNo){
					alert("Bundle No: "+bundleNo+" already scan, try another one.");
					
					flag=0;
					return false;
				}
			});
		
			if(flag==1)
			{
				create_rescanrow(txt_bundle_no,"scan",'');
				$('#txt_bundle_rescan').val('');
			}
		}
	});
    function fn_checkLine(date)
    {
        load_drop_down( 'requires/bundle_wise_sewing_input_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location').val()+'_'+$('#cbo_floor').val()+'_'+date, 'load_drop_down_line', 'line_td' );       
    }
    
    /*function working_com_fnc()
     {
     var company_id=$('#cbo_company_name').val();
     var working_company_mandatory=return_global_ajax_value(company_id, 'load_variable_settings_for_working_company', '', 'requires/bundle_wise_sewing_input_controller');
     
     if(working_company_mandatory==1)
     {
     $('#working_company').css('color','blue');
     //alert('Working Company is Mandatory');
     return;
     }
     else
     {
     $('#working_company').css('color','black');
     }
     }*/
     	

	function show_cost_details()
	{
		var system_id=$("#txt_system_id").val();
		if(system_id=="")
		{
			alert('Challan No Required!');
			return;
		}

		var page_link='requires/bundle_wise_sewing_input_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}

    function call_print_button_for_mail(mail,mail_body,type){
		get_php_form_data( document.getElementById('cbo_company_name').value+'**'+mail+'**'+mail_body, "get_first_selected_print_report", "requires/bundle_wise_sewing_input_controller" );
    }
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;">
        <?= load_freeze_divs ("../",$permission); ?>
        <div style="width:1000px; margin:0 auto;"> 
            <fieldset style="width:930px;">
                <legend>Production Module</legend>
                <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
                    <fieldset>
                        <table width="100%">
                            <tr>
                                <td align="right" colspan="3">Challan No</td>
                                <td colspan="3"> 
                                    <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:167px" onDblClick="openmypage_sysNo()" placeholder="Double click to search" />
                                    <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" />
                                </td>
                            </tr>
                            <tr>
                                <td width="110" class="must_entry_caption">Company</td>
                                <td>
                                    <?= create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/bundle_wise_sewing_input_controller');" );?>
                                    <input type="hidden" id="sewing_production_variable" />	 
                                    <input type="hidden" id="styleOrOrderWisw" />
                                    <input type="hidden" id="delivery_basis" />
                                    <input type="hidden" id="wip_valuation_for_accounts" />
                                    <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
                                    <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
                                </td>
                                <td class="must_entry_caption">Source</td>
                                <td>
                                    <? 
                                    echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );", 0, '1,3' );
                                    //embroidery_delivery_entry_controller  change_mode(this.value);
                                    ?>
                                </td>
                                <td class="must_entry_caption">Sewing Company</td>
                                <td id="emb_company_td">
                                    <?= create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "-- Select --", $selected, "" );?>
                                </td>
                            </tr>
                            <tr>

                                <td class="must_entry_caption">Location</td>
                                <td id="location_td">
                                    <?= create_drop_down( "cbo_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                                </td>
                                <td class="must_entry_caption">Floor</td>
                                <td id="floor_td">
                                    <?= create_drop_down( "cbo_floor", 180, $blank_array,"", 1, "-- Select Floor --", $selected, "" );?>
                                </td>
                                <td>Organic</td>
                                <td>
                                    <input name="txt_organic" id="txt_organic" class="text_boxes" type="text" style="width:167px" />
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Input Date</td>
                                <td> 
                                    <input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;" onchange="fn_checkLine(this.value)" />
                                </td>
                                <td class="must_entry_caption">Line No.</td>
                                <td id="line_td"> 
                                    <?= create_drop_down( "cbo_line_no", 180, $blank_array,"", 1, "-- Select Floor --", $selected, "" );?>
                                </td>
                                <td>Remarks</td> 
                                <td > 
                                     <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:167px" title="450 Characters Only." />
                                </td>
                                
                                <td id="working_company" style="display:none;">Working Company</td>
                                <td style="display:none;">
                                    <?= create_drop_down( "cbo_working_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --",$selected,"load_drop_down( 'requires/bundle_wise_sewing_input_controller', $('#cbo_working_company_name').val(), 'load_drop_down_working_location', 'working_location_td' );" );?>
                                </td>
                            </tr>
                            <tr>
                                <td>Shift Name</td>
                                <td>
                                   <?= create_drop_down( "cbo_shift_name", 180, $shift_name,"", 1, "-- Select --", $selected, "" );?>
                                </td>
                              
                              
                            </tr>
                            <tr>
                                <td style="display:none;">Working Location</td>
                                <td id="working_location_td" style="display:none;">
                                    <?=create_drop_down( "cbo_working_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                                </td>
                            </tr>
                        </table>
                    </fieldset> <br />
                    <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order"  style="display:none">
                        <tr>
                            <td width="35%" valign="top">
                                <fieldset>
                                    <legend>New Entry</legend>
                                    <table  cellpadding="0" cellspacing="2" width="100%">
                                        <tr>
                                            <td width="80" class="must_entry_caption" id="td_caption">Order No</td>
                                            <td colspan="3" width="110"> 
                                                <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/bundle_wise_sewing_input_controller.php?action=order_popup&company=' + document.getElementById('cbo_company_name').value + '&garments_nature=' + document.getElementById('garments_nature').value, 'Order Search')" id="txt_order_no" class="text_boxes" style="width:212px" readonly />
                                                <input type="hidden" id="hidden_po_break_down_id" value="" />
                                            </td>
                                        </tr> 
                                        <!--<tr>
                                             <td width="80" class="must_entry_caption">Issue Date</td>
                                             <td colspan="3" width="110"> 
                                                  <input type="text" name="txt_issue_date" id="txt_issue_date" value="<?echo date("d-m-Y")?>" class="datepicker" style="width:100px;"  />
                                             </td>
                                        </tr> -->
                                        <tr>
                                            <td class="must_entry_caption">Issue Qty</td> 
                                            <td colspan="3"> 
                                                <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:100px" readonly >
                                                <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                                <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                            </td> 
                                        </tr> 
                                        <tr>    
                                            <td>Order Qnty</td>
                                            <td>
                                                <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:100px" disabled readonly>
                                            </td>
                                        </tr>
                                        <tr>   
                                            <td>Buyer</td>
                                            <td>
                                                <?= create_drop_down( "cbo_buyer_name", 112, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "Dispaly", $selected, "",1,0 );?>	
                                            </td> 
                                        </tr>
                                        <tr>    
                                            <td>Style</td>
                                            <td>
                                                <input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" disabled  readonly>
                                            </td>
                                        </tr> 
                                        <tr>    
                                            <td>Item</td>
                                            <td>
                                                <?= create_drop_down( "cbo_item_name", 110, $garments_item,"", 1, "Display", $selected, "",1,0 ); ?>
                                            </td>
                                        </tr> 
                                        <tr>    
                                            <td>Country</td>
                                            <td>
                                                <?= create_drop_down('cbo_country_name',110,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);?> 
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td>Challan No</td> 
                                            <td>
                                               	<input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" disabled readonly />
                                            </td>
                                            <td>Iss. ID</td> 
                                            <td>
                                               	<input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:50px" disabled readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Remarks</td> 
                                            <td colspan="3"> 
                                                <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:217px" title="450 Characters Only." />
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                            <td width="1%" valign="top"></td>
                            <td width="22%" valign="top">
                                <fieldset>
                                    <legend>Display</legend>
                                    <table  cellpadding="0" cellspacing="2" width="100%" >
                                        <tr>
                                            <td width="100">Cutt. Qty</td>
                                            <td width="90"> 
                                                <input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Cuml. Issue Qty</td>
                                            <td > 
                                                <input type="text" name="txt_cumul_issue_qty" id="txt_cumul_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Yet to Issue</td>
                                            <td> 
                                                <input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>	
                            </td>
                            <td width="40%" valign="top">
                                <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_bundle">
                        <tr>
                            <td>
                                <fieldset>
                                    <legend>New Entry</legend>
                                    <table  cellpadding="0" cellspacing="2" width="100%">
                                        <tr>
                                            <td width="80" class="must_entry_caption" id="td_caption">Barcode No</td>
                                            <td colspan="2" width="110"> 
                                                <input name="txt_bundle_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle('requires/bundle_wise_sewing_input_controller.php?action=bundle_popup&company=' + document.getElementById('cbo_company_name').value + '&garments_nature=' + document.getElementById('garments_nature').value, 'Bundle Search')" id="txt_bundle_no" class="text_boxes" style="width:212px" />
                                            </td>
                                            <td width="100" class="must_entry_caption" id="td_caption">Re-Scan Barcode</td>
                                            <td colspan="2" width="110"> 
                                               <input name="txt_bundle_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan('requires/bundle_wise_sewing_input_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Search Bundle For Rescan')"  id="txt_bundle_rescan" class="text_boxes" style="width:212px" />
                                            </td>
                                            <td>
                                                <input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onclick="show_cost_details();">
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                    <div id="bundle_list_view">
                        <table cellpadding="0" width="1000" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                            <th width="30">SL</th>
                            <th width="90">Bundle No</th>
                            <th width="50">Year</th>
                            <th width="60">Job No</th>
                            <th width="70">Style Ref.</th>
                            <th width="65">Buyer</th>
                            <th width="90">Order No</th>
                            <th width="120">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="80">Color</th>
                            <th width="70">Size</th>
                            <th width="80">Qty.</th>
                            <th></th>
                            </thead>
                        </table>
                        <div style="width:1000px; max-height:250px; overflow-y:auto" align="left">    
                            <table cellpadding="0" width="1000" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                                <tbody>

                                </tbody>
                            </table>
                            
                        </div>
                        <table cellpadding="0" width="1000" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <tfoot>
                            <th width="30"></th>
                            <th width="90"></th>
                            <th width="50"></th>
                            <th width="60"></th>
                            <th width="70"></th>
                            <th width="65"></th>
                            <th width="90"></th>
                            <th width="120"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="70">Total</th>
                            <th width="80" id="total_bndl_qty"></th>
                            <th></th>
                        </tfoot>
              		</table>
                    </div>
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr>
                            <td align="center" colspan="9" valign="middle" class="button_container">
                                <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','pageReset();')",1); 
                                ?>
                                <input class="formbutton" type="button" onClick="fnSendMail('../','txt_system_id',1,1,0,1,0,document.getElementById('cbo_company_name').value+'_120_0')" value="Mail Send" style="width:80px;">

                                <input id="Print2" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(5)" name="print" value="Print 2">
                                <input id="Print3" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(6)" name="print3" value="Print 3">
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                                <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="0" readonly >
                                <input id="Print4" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print()" name="print4" value="Print 4">
                                <input id="Print5" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(7)" name="print4" value="Print 5">
                                <input id="Print6" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(8)" name="print6" value="Print 6">
                                <input id="Print7" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(9)" name="print7" value="Print 7">
                                <input id="Print8" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(10)" name="print8" value="Print 8">
                                <input id="Print9" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(7)" name="print9" value="Print 9">
                                <input id="Print10" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(8)" name="print10" value="Print 10">
                                <input id="Print11" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(11)" name="print11" value="Print 11">
                                <input id="Print12" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(12)" name="print12" value="Print 12">
                                <input id="Print13" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(13)" name="Print13" value="Print 13">
                                <input id="Print14" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_issue_print_embroidery_entry(14)" name="Print14" value="Print 14">
                                <input id="Print15" class="formbutton" type="button" style="display: none; width:80px;" onClick="fnc_bundle_wise_input_print(13)" name="print15" value="Print 15"> 
                            </td>
                            <td>&nbsp;</td>				
                        </tr>
                    </table>
                    <div style="width:900px; margin-top:5px;" id="printing_production_list_view" align="center"></div>
                </form>
            </fieldset>
        </div>
        <div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

