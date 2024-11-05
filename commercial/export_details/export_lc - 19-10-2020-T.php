<?
/* -------------------------------------------- Comments

  Purpose			: 	This form will create for Export LC entry

  Functionality	:


  JS Functions	:

  Created by		:	Bilas
  Creation date 	: 	14-11-2012
  Updated by 		: 	Fuad Shahriar
  Update date		: 	04-04-2013

  QC Performed BY	:

  QC Date			:

  Comments		:

  ==== Oracle ====
  Created by		:	Fuad Shahriar
  Creation date 	: 	21-05-2014
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Export LC Form", "../../", 1, 1, '', '1', '');
?>	

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';
    <?
        $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][106] );
        echo "var field_level_data= ". $data_arr . ";\n";
    ?>

    var str_port_of_entry = [<? echo substr(return_library_autocomplete("select distinct(port_of_entry) from com_export_lc", "port_of_entry"), 0, -1); ?>];
    var str_port_of_loading = [<? echo substr(return_library_autocomplete("select distinct(port_of_loading) from com_export_lc", "port_of_loading"), 0, -1); ?>];
    var str_port_of_discharge = [<? echo substr(return_library_autocomplete("select distinct(port_of_discharge) from com_export_lc", "port_of_discharge"), 0, -1); ?>];
    var str_inco_term_place = [<? echo substr(return_library_autocomplete("select distinct(inco_term_place) from com_export_lc", "inco_term_place"), 0, -1); ?>];

    $(document).ready(function (e)
    {
        $("#txt_port_of_entry").autocomplete({
            source: str_port_of_entry
        });
        $("#txt_port_of_loading").autocomplete({
            source: str_port_of_loading
        });
        $("#txt_port_of_discharge").autocomplete({
            source: str_port_of_discharge
        });
        $("#txt_inco_term_place").autocomplete({
            source: str_inco_term_place
        });

    });

    function party_loading_dischage_field(str)
    {
        if (str == 1)
        {
            reset_form("", "", "txt_port_of_entry*txt_port_of_loading*txt_port_of_dischage");
        } else if (str == 2)
        {
            $("#txt_port_of_entry").val("From Supplier Factory");
            $("#txt_port_of_loading").val("From Supplier Factory");
            $("#txt_port_of_dischage").val("To Buyer Factory");
        }
    }

    function replacement_lc_diplay(val)
    {
        if (val == '1')
        {
            $('#exportLcFrm_2').show();
        } else
        {
            $('#exportLcFrm_2').hide();
        }

    }

    function fn_add_date_field()
    {
        $("#txt_expiry_date").val(add_days($('#txt_last_shipment_date').val(), '15'));
    }

    function fnc_export_lc_entry(operation)
    {
        /*if (operation == 2)
        {
            show_msg('13');
            return;
        }*/

        if (form_validation('cbo_beneficiary_name*txt_internal_file_no*txt_year*txt_lc_number*txt_lc_value*txt_lc_date*cbo_buyer_name*cbo_lien_bank*txt_last_shipment_date*cbo_pay_term*cbo_export_item_category', 'Beneficiary Name*Internal File No*Year*LC Number*LC Value*LC Date*Buyer Name*Lean Bank*Shipment Date*Pay Term*Export Item Category') == false)
        {
            return;
        } 
		else
        {
            var txt_bl_clause = $("#txt_bl_clause").val();
            var txt_reimbursement_clauses = $("#txt_reimbursement_clauses").val();
            var txt_discount_clauses = $("#txt_discount_clauses").val();

            var data = "action=save_update_delete_mst&operation=" + operation + get_submitted_data_string('txt_system_id*cbo_beneficiary_name*txt_internal_file_no*txt_bank_file_no*txt_year*txt_lc_number*txt_lc_value*txt_lc_date*cbo_currency_name*cbo_buyer_name*cbo_applicant_name*cbo_notifying_party*cbo_consignee*txt_issuing_bank*cbo_lien_bank*txt_lien_date*txt_last_shipment_date*txt_expiry_date*txt_tolerance*cbo_shipping_mode*cbo_pay_term*txt_tenor*cbo_inco_term*txt_inco_term_place*cbo_lc_source*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*txt_doc_presentation_days*txt_max_btb_limit*txt_foreign_comn*txt_local_comn*txt_transfering_bank_ref*cbo_is_lc_transfarrable*cbo_replacement_lc*txt_transfer_bank*txt_negotiating_bank*txt_nominated_shipp_line*txt_re_imbursing_bank*txt_claim_adjustment*txt_expiry_place*txt_remarks*txt_bl_clause*txt_reason*txt_reimbursement_clauses*txt_discount_clauses*export_lc_system_id*cbo_export_item_category*import_btb_id*import_btb', "../../");
			//alert(data);return;

            freeze_window(operation);

            http.open("POST", "requires/export_lc_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_export_lc_entry_Reply_info;
        }
    }

    function fnc_export_lc_entry_Reply_info()
    {
        if (http.readyState == 4)
        {
           // alert(http.responseText);
            var reponse = trim(http.responseText).split('**');

            show_msg(reponse[0]);
			if(reponse[0]==31)
			{
				alert(reponse[1]);release_freezing();return;
			}
            if ((reponse[0] == 0 || reponse[0] == 1))
            {
                document.getElementById('txt_system_id').value = reponse[1];
                document.getElementById('export_lc_system_id').value = reponse[2];
                $('#cbo_beneficiary_name').attr('disabled', 'disabled');
                set_button_status(1, permission, 'fnc_export_lc_entry', 1);
            }
			if(reponse[0] == 2)
			{
				location.reload();
			}
            release_freezing();
        }
    }

    function openmypage_importPi()
    {
        var page_link = 'requires/export_lc_controller.php?action=btb_lc_search';
        var title = 'BTB L/C Search Form';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var btb_ref = this.contentDoc.getElementById("hidden_btb_id").value.split("__");
			var btb_id=btb_ref[0];
			var item_category_id=btb_ref[1];

            if (trim(btb_id) != "")
            {
                get_php_form_data(btb_id, "populate_data_from_btb_lc", "requires/export_lc_controller");
                show_list_view(btb_id+"****"+item_category_id, 'import_pi_details', 'po_list_view', 'requires/export_lc_controller', '');
                $('#tbl_order_list').hide();
            }
        }
    }

    function fnc_import_pi_save(operation)
    {
		var cbo_item_category_id=$('#cbo_export_item_category').val();
		//alert(cbo_item_category_id);return;
        if (operation == 2)
        {
            show_msg('13');
            return;
        }

        if (form_validation('txt_system_id', 'System ID') == false)
        {
            return;
        }

        var row_num = $('#tbl_pi_item tbody tr').length;
        var data_all = "";
		if(cbo_item_category_id==10)
		{
			for (var j = 1; j <= row_num; j++)
			{				
					data_all += "&workOrderNo_" + j + "='" + $('#workOrderNo_' + j).val() + "'" + "&hideWoId_" + j + "='" + $('#hideWoId_' + j).val() + "'" + "&hidePiId_" + j + "='" + $('#hidePiId_' + j).val() + "'" + "&hidePiDtlsId_" + j + "='" + $('#hidePiDtlsId_' + j).val() + "'" + "&construction_" + j + "='" + $('#construction_' + j).val() + "'" + "&composition_" + j + "='" + $('#composition_' + j).val() + "'" + "&colorId_" + j + "='" + $('#colorId_' + j).val() + "'" + "&gsm_" + j + "='" + $('#gsm_' + j).val() + "'" + "&diawidth_" + j + "='" + $('#diawidth_' + j).val() + "'" + "&uom_" + j + "='" + $('#uom_' + j).val() + "'" + "&quantity_" + j + "='" + $('#quantity_' + j).val() + "'" + "&rate_" + j + "='" + $('#rate_' + j).val() + "'" + "&amount_" + j + "='" + $('#amount_' + j).val() + "'" + "&isSalesId_" + j + "='" + $('#isSalesId_' + j).val() + "'" + "&updateIdDtls_" + j + "='" + $('#updateIdDtls_' + j).val() + "'" + "&hideDeterminationId_" + j + "='" + $('#hideDeterminationId_' + j).val() + "'";
						  
			} 
		}
		else if(cbo_item_category_id==45) // Accessories
		{
			for (var j = 1; j <= row_num; j++)
			{
			//4==workOrderNo_*hideWoId_*hidePiId_*hidePiDtlsId_*isSalesId_*bookingNo_*itemgroupid_*itemdescription_*itemColor_*itemSize_*uom_*quantity_*rate_*amount_*updateIdDtls_*bookingWithoutOrder_
            //4_Place==itemgroupidPlace_*itemColorPlace_*itemSizePlace_*uomPlace_*ratePlace_*amountPlace_

			data_all += "&workOrderNo_" + j + "='" + $('#workOrderNo_' + j).val() + "'" + "&hideWoId_" + j + "='" + $('#hideWoId_' + j).val() + "'" + "&hidePiId_" + j + "='" + $('#hidePiId_' + j).val() + "'" + "&hidePiDtlsId_" + j + "='" + $('#hidePiDtlsId_' + j).val() + "'" + "&bookingNo_" + j + "='" + $('#bookingNo_' + j).val() + "'" + "&itemgroupid_" + j + "='" + $('#itemgroupid_' + j).val() + "'" + "&itemgroupidPlace_" + j + "='" + $('#itemgroupid_' + j).attr('placeholder') + "'" + "&itemdescription_" + j + "='" + $('#itemdescription_' + j).val() + "'" + "&itemColor_" + j + "='" + $('#itemColor_' + j).val() + "'" + "&colorId_" + j + "='" + $('#itemColor_' + j).attr('placeholder') + "'" + "&itemSize_" + j + "='" + $('#itemSize_' + j).val() + "'" + "&itemSizePlace_" + j + "='" + $('#itemSize_' + j).attr('placeholder') + "'" + "&uomVal_" + j + "='" + $('#uom_' + j).val() + "'" + "&uom_" + j + "='" + $('#uom_' + j).attr('placeholder') + "'" + "&quantity_" + j + "='" + $('#quantity_' + j).val() + "'" + "&rate_" + j + "='" + $('#rate_' + j).val() + "'" + "&ratePlace_" + j + "='" + $('#rate_' + j).attr('placeholder') + "'" + "&amount_" + j + "='" + $('#amount_' + j).val() + "'" + "&amountPlace_" + j + "='" + $('#amount_' + j).attr('placeholder') + "'" + "&isSalesId_" + j + "='" + $('#isSalesId_' + j).val() + "'" + "&updateIdDtls_" + j + "='" + $('#updateIdDtls_' + j).val() + "'" + "&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_' + j).val() + "'";
			}
		}
		else if(cbo_item_category_id==23) // AOP
		{
			for (var j = 1; j <= row_num; j++)
			{
			// 74==workOrderNo_*hideWoId_*hidePiId_*hidePiDtlsId_*isSalesId_*bookingNo_*itemColor_*aopColor_*gsm_*bodyPart_*uom_*quantity_*amount_*updateIdDtls_*bookingWithoutOrder_
            // 74_Place==itemColorPlace_*aopColorPlace_*bodyPartPlace_*uomPlace_*ratePlace_*amountPlace_

				data_all += "&workOrderNo_" + j + "='" + $('#workOrderNo_' + j).val() + "'" + "&hideWoId_" + j + "='" + $('#hideWoId_' + j).val() + "'" + "&hidePiId_" + j + "='" + $('#hidePiId_' + j).val() + "'" + "&hidePiDtlsId_" + j + "='" + $('#hidePiDtlsId_' + j).val() + "'" + "&bookingNo_" + j + "='" + $('#bookingNo_' + j).val() + "'" + "&itemColor_" + j + "='" + $('#itemColor_' + j).val() + "'" + "&colorId_" + j + "='" + $('#itemColor_' + j).attr('placeholder') + "'" + "&aopColor_" + j + "='" + $('#aopColor_' + j).val() + "'" + "&aopColorPlace_" + j + "='" + $('#aopColor_' + j).attr('placeholder') + "'" + "&gsm_" + j + "='" + $('#gsm_' + j).val() + "'" + "&bodyPart_" + j + "='" + $('#bodyPart_' + j).val() + "'" + "&bodyPartPlace_" + j + "='" + $('#bodyPart_' + j).attr('placeholder') + "'" + "&uomVal_" + j + "='" + $('#uom_' + j).val() + "'" + "&uom_" + j + "='" + $('#uom_' + j).attr('placeholder') + "'" + "&quantity_" + j + "='" + $('#quantity_' + j).val() + "'" + "&rate_" + j + "='" + $('#rate_' + j).val() + "'" + "&ratePlace_" + j + "='" + $('#rate_' + j).attr('placeholder') + "'" + "&amount_" + j + "='" + $('#amount_' + j).val() + "'" + "&amountPlace_" + j + "='" + $('#amount_' + j).attr('placeholder') + "'" + "&isSalesId_" + j + "='" + $('#isSalesId_' + j).val() + "'" + "&updateIdDtls_" + j + "='" + $('#updateIdDtls_' + j).val() + "'" + "&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_' + j).val() + "'";
			}			
		}
		else if(cbo_item_category_id==35 || cbo_item_category_id==36) // Gmts Printing || Gmts Embroidery
		{
			for (var j = 1; j <= row_num; j++)
			{
			//25*102==workOrderNo_*hideWoId_*hidePiId_*hidePiDtlsId_*isSalesId_*bookingNo_*gmtsItem_*bodyPart_*embName_*embType_*itemdescription_*itemColor_*itemSize_*uom_*quantity_*amount_*updateIdDtls_*bookingWithoutOrder_
            //25*102_Place==gmtsItemPlace_*bodyPartPlace_*embNamePlace_*embTypePlace_*itemColorPlace_*itemSizePlace_*uomPlace_*ratePlace_*amountPlace_

				data_all += "&workOrderNo_" + j + "='" + $('#workOrderNo_' + j).val() + "'" + "&hideWoId_" + j + "='" + $('#hideWoId_' + j).val() + "'" + "&hidePiId_" + j + "='" + $('#hidePiId_' + j).val() + "'" + "&hidePiDtlsId_" + j + "='" + $('#hidePiDtlsId_' + j).val() + "'" + "&bookingNo_" + j + "='" + $('#bookingNo_' + j).val() + "'" + "&gmtsItem_" + j + "='" + $('#gmtsItem_' + j).val() + "'" + "&gmtsItemPlace_" + j + "='" + $('#gmtsItem_' + j).attr('placeholder') + "'" + "&bodyPart_" + j + "='" + $('#bodyPart_' + j).val() + "'" + "&bodyPartPlace_" + j + "='" + $('#bodyPart_' + j).attr('placeholder') + "'" + "&embName_" + j + "='" + $('#embName_' + j).val() + "'" + "&embNamePlace_" + j + "='" + $('#embName_' + j).attr('placeholder') + "'" + "&embType_" + j + "='" + $('#embType_' + j).val() + "'" + "&embTypePlace_" + j + "='" + $('#embType_' + j).attr('placeholder') + "'" + "&itemdescription_" + j + "='" + $('#itemdescription_' + j).val() + "'" + "&itemColor_" + j + "='" + $('#itemColor_' + j).val() + "'" + "&colorId_" + j + "='" + $('#itemColor_' + j).attr('placeholder') + "'" + "&itemSize_" + j + "='" + $('#itemSize_' + j).val() + "'" + "&itemSizePlace_" + j + "='" + $('#itemSize_' + j).attr('placeholder') + "'" + "&uomVal_" + j + "='" + $('#uom_' + j).val() + "'" + "&uom_" + j + "='" + $('#uom_' + j).attr('placeholder') + "'" + "&quantity_" + j + "='" + $('#quantity_' + j).val() + "'" + "&rate_" + j + "='" + $('#rate_' + j).val() + "'" + "&ratePlace_" + j + "='" + $('#rate_' + j).attr('placeholder') + "'" + "&amount_" + j + "='" + $('#amount_' + j).val() + "'" + "&amountPlace_" + j + "='" + $('#amount_' + j).attr('placeholder') + "'" + "&isSalesId_" + j + "='" + $('#isSalesId_' + j).val() + "'" + "&updateIdDtls_" + j + "='" + $('#updateIdDtls_' + j).val() + "'" + "&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_' + j).val() + "'";
			}
		}
		else if(cbo_item_category_id==37) // wash
		{
			for (var j = 1; j <= row_num; j++)
			{
			//103==workOrderNo_*hideWoId_*hidePiId_*hidePiDtlsId_*isSalesId_*bookingNo_*gmtsItem_*itemColor_*itemdescription_*embName_*embType_*uom_*quantity_*amount_*updateIdDtls_*bookingWithoutOrder_
            // 25*102_Place==gmtsItemPlace_*itemColorPlace_*embNamePlace_*embTypePlace_*uomPlace_*ratePlace_*amountPlace_

				data_all += "&workOrderNo_" + j + "='" + $('#workOrderNo_' + j).val() + "'" + "&hideWoId_" + j + "='" + $('#hideWoId_' + j).val() + "'" + "&hidePiId_" + j + "='" + $('#hidePiId_' + j).val() + "'" + "&hidePiDtlsId_" + j + "='" + $('#hidePiDtlsId_' + j).val() + "'" + "&bookingNo_" + j + "='" + $('#bookingNo_' + j).val() + "'" + "&gmtsItem_" + j + "='" + $('#gmtsItem_' + j).val() + "'" + "&gmtsItemPlace_" + j + "='" + $('#gmtsItem_' + j).attr('placeholder') + "'" + "&itemColor_" + j + "='" + $('#itemColor_' + j).val() + "'" + "&colorId_" + j + "='" + $('#itemColor_' + j).attr('placeholder') + "'" + "&itemdescription_" + j + "='" + $('#itemdescription_' + j).val() + "'" + "&embName_" + j + "='" + $('#embName_' + j).val() + "'" + "&embNamePlace_" + j + "='" + $('#embName_' + j).attr('placeholder') + "'" + "&embType_" + j + "='" + $('#embType_' + j).val() + "'" + "&embTypePlace_" + j + "='" + $('#embType_' + j).attr('placeholder') + "'" + "&uomVal_" + j + "='" + $('#uom_' + j).val() + "'" + "&uom_" + j + "='" + $('#uom_' + j).attr('placeholder') + "'" + "&quantity_" + j + "='" + $('#quantity_' + j).val() + "'" + "&rate_" + j + "='" + $('#rate_' + j).val() + "'" + "&ratePlace_" + j + "='" + $('#rate_' + j).attr('placeholder') + "'" + "&amount_" + j + "='" + $('#amount_' + j).val() + "'" + "&amountPlace_" + j + "='" + $('#amount_' + j).attr('placeholder') + "'" + "&isSalesId_" + j + "='" + $('#isSalesId_' + j).val() + "'" + "&updateIdDtls_" + j + "='" + $('#updateIdDtls_' + j).val() + "'" + "&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_' + j).val() + "'";
			}
		}
		
		//alert (data_all);//return;
		
        if (data_all == "")
        {
            alert("No Item");
            return;
        }

        var data = "action=save_update_delete_pi&operation=" + operation + '&total_row=' + row_num + get_submitted_data_string('txt_system_id*import_btb*import_btb_id*cbo_export_item_category', "../../") + data_all;

        freeze_window(operation);

        http.open("POST", "requires/export_lc_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_import_pi_save_Reply_info;
    }

    function fnc_import_pi_save_Reply_info()
    {
        if (http.readyState == 4)
        {
            //release_freezing(); return;
            var response = http.responseText.split('**');
            show_msg(trim(response[0]));

            if ((response[0] == 0 || response[0] == 1))
            {
                var btb_id = $("#import_btb_id").val();
                var export_lc_id = $("#txt_system_id").val();
				var item_category_id = $("#cbo_export_item_category").val();
                show_list_view(btb_id + "**" + export_lc_id+ "**" + item_category_id, 'import_pi_details', 'po_list_view', 'requires/export_lc_controller', '');
            }

            release_freezing();
        }
    }


    function export_lc_popup()
    {
        var cbo_company_id = $("#cbo_beneficiary_name").val();
        var page_link = 'requires/export_lc_controller.php?action=export_lc_popup_search&cbo_company_id='+cbo_company_id;
        var title = 'Export LC Form';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1065px,height=470px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var export_lc_id = this.contentDoc.getElementById("hidden_export_lc_id").value;
            if (trim(export_lc_id) != "")
            {
                freeze_window(5);
                reset_form('exportLcFrm_2*exportLcFrm_3', '', '', 'txt_tot_row_attach_sales,0*txt_tot_row,0', '$(\'#tbl_order_list tbody tr:not(:first)\').remove();$(\'#tbl_sales_contract tbody tr:not(:first)\').remove();');
                get_php_form_data(export_lc_id, "populate_data_from_export_lc", "requires/export_lc_controller");
                show_list_view(export_lc_id, 'show_sc_active_listview', 'sc_list_view', 'requires/export_lc_controller', '');

                var import_btb = $("#import_btb").val();
                var btb_id = $("#import_btb_id").val();
				var item_category_id = $("#cbo_export_item_category").val();
				//alert(item_category_id);
                if (import_btb == 1)
                {
                    show_list_view(btb_id + "**" + export_lc_id+ "**" + item_category_id, 'import_pi_details', 'po_list_view', 'requires/export_lc_controller', '');
                    $('#tbl_order_list').hide();
                } else
                {
                    show_list_view(export_lc_id+"_"+item_category_id, 'show_po_active_listview', 'po_list_view', 'requires/export_lc_controller', 'setFilterGrid(\'po_active_list\',-1)');
                    $('#tbl_order_list').show();
                }
                release_freezing();
            }

        }
    }

//attached sales contract here 
    function fnc_sales_contract_selection(operation)
    {
        if (operation == 2)
        {
            show_msg('13');
            return;
        }

        if (form_validation('txt_system_id', 'Sales Contract') == false)
        {
            return;
        } else
        {
            var row_num = $('table#tbl_sales_contract tbody tr').length;
            var submit_data = "";

            for (var j = 1; j <= row_num; j++)
            {
                if (form_validation('txtSalesContractNo_' + j, 'Sales Contract No') == false)
                {
                    return;
                }

                if ($("#txtReplacementAmount_" + j).val() * 1 <= 0)
                {
                    alert("Please Insert Replaced Amount");
                    $("#txtReplacementAmount_" + j).focus();
                    return;
                } else
                {
                    submit_data += "*hiddenScId_" + j + "*txtReplacementAmount_" + j + "*txtBtbLcSelectedID_" + j + "*cbo_sc_status_" + j;
                }
            }
            var data = "action=save_update_delete_sc_info&noRow=" + row_num + "&operation=" + operation + get_submitted_data_string('txt_system_id*hiddenlcAttachSalesContractID' + submit_data, "../../");
            freeze_window(operation);

            http.open("POST", "requires/export_lc_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_sc_selection_save_Reply_info;
        }
    }

    function fnc_sc_selection_save_Reply_info()
    {
        if (http.readyState == 4)
        {
            // alert(http.responseText);
            var reponse = http.responseText.split('**');
            show_msg(trim(reponse[0]));

            if ((reponse[0] == 0 || reponse[0] == 1))
            {
                reset_form('exportLcFrm_2', '', '', 'txt_tot_row_attach_sales,0', '$(\'#tbl_sales_contract tbody tr:not(:first)\').remove();');
                show_list_view(reponse[1], 'show_sc_active_listview', 'sc_list_view', 'requires/export_lc_controller', '');
                load_sc_id();
                set_button_status(0, permission, 'fnc_sales_contract_selection', 2);
            }
            release_freezing();

        }
    }

    function load_sc_id()
    {
        var export_lc_id = $('#txt_system_id').val();
        if (export_lc_id != "")
        {
            get_php_form_data(export_lc_id, 'load_sc_id', 'requires/export_lc_controller');
        }
    }

    function add_sales_contract(row_num)
    {
        if (form_validation('txt_system_id', 'System ID') == false)
        {
            $('#export_lc_system_id').focus();
            return;
        }

        var companyID = $("#cbo_beneficiary_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var sc_selectedID = $("#hidden_sc_selectedID").val();
        var page_link = 'requires/export_lc_controller.php?action=sc_popup_search&companyID=' + companyID + '&buyerID=' + buyerID + '&sc_selectedID=' + sc_selectedID;
        var title = 'Export LC Sales Contract';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=380px,center=1,resize=1,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var scID_string = this.contentDoc.getElementById("txt_selected_id").value;

            if (trim(scID_string) != "")
            {
                var pre_selectID = $("#hidden_sc_selectedID").val();
                if (trim(pre_selectID) == "")
                    $("#hidden_sc_selectedID").val(scID_string);
                else
                    $("#hidden_sc_selectedID").val(pre_selectID + "," + scID_string);

                var txt_tot_row_attach_sales = $('#txt_tot_row_attach_sales').val();

                var data = scID_string + "**" + txt_tot_row_attach_sales;
                var list_view_orders = return_global_ajax_value(data, 'populate_data_sc_form', '', 'requires/export_lc_controller');
                var sales_contract_no = $('#txtSalesContractNo_' + row_num).val();

                if (sales_contract_no == "")
                {
                    $("#trs_" + row_num).remove();
                }

                $("#tbl_sales_contract tbody:last").append(list_view_orders);

                var numRow = $('table#tbl_sales_contract tbody tr').length;
                $('#txt_tot_row_attach_sales').val(numRow);

                math_operation("totalReplacedAmount", "txtReplacementAmount_", "+", numRow);
                math_operation("totalContractValue", "txtContractValue_", "+", numRow);
                math_operation("totalCumulativeReplaced", "txtCumulativeReplaced_", "+", numRow);
                math_operation("totalYettoReplace", "txtYetToReplace_", "+", numRow);
            }
        }
    }

    function CalculateCumulativeValue(field_val, field_id)
    {
        var index = field_id.split('_');

        var txtReplacementAmount = $('#txtReplacementAmount_' + index[1]).val() * 1;
        var hideReplacementAmount = $('#hideReplacementAmount_' + index[1]).val() * 1;

        var txtContractValue = $('#txtContractValue_' + index[1]).val() * 1;
        var txtCumulativeReplacedDB = $('#txtCumulativeReplacedDB_' + index[1]).val() * 1;

        var CumulativeTotal = txtReplacementAmount + txtCumulativeReplacedDB;

        if (CumulativeTotal > txtContractValue)
        {
            var yetValue = txtContractValue - txtCumulativeReplacedDB + hideReplacementAmount;
            alert("Cumulative Replaced Amount Exceeded Contract Value");
            $('#txtReplacementAmount_' + index[1]).val(hideReplacementAmount);
            $('#txtCumulativeReplaced_' + index[1]).val(txtCumulativeReplacedDB + hideReplacementAmount);
            $('#txtYetToReplace_' + index[1]).val(yetValue);
        } else
        {
            var txtYetToReplace = $('#txtYetToReplace_' + index[1]).val() * 1;

            $('#txtCumulativeReplaced_' + index[1]).val(CumulativeTotal);

            var yetValue = txtContractValue - CumulativeTotal
            $('#txtYetToReplace_' + index[1]).val(yetValue);
        }

        var numRow = $('table#tbl_sales_contract tbody tr').length;
        math_operation("totalReplacedAmount", "txtReplacementAmount_", "+", numRow);
        math_operation("totalContractValue", "txtContractValue_", "+", numRow);
        math_operation("totalCumulativeReplaced", "txtCumulativeReplaced_", "+", numRow);
        math_operation("totalYettoReplace", "txtYetToReplace_", "+", numRow);
    }

//-------------------------------------------oder attached js start here --------------------------------//

    function fnc_po_selection_save(operation)
    {
        if (operation == 2)
        {
            show_msg('13');
            return;
        }

        if (form_validation('txt_system_id', 'System ID') == false)
        {
            return;
        }
        var row_num = $('table#tbl_order_list tbody tr').length;
        var submit_data = "";
        for (var j = 1; j <= row_num; j++)
        {
            if (trim($("#txtordernumber_" + j).val()) != "")
            {
                if ($("#txtattachedqnty_" + j).val() * 1 <= 0)
                {
                    alert("Please Insert Attach Qnty");
                    $("#txtattachedqnty_" + j).focus();
                    return;
                }
                submit_data += "*hiddenwopobreakdownid_" + j + "*txtattachedqnty_" + j + "*hiddenunitprice_" + j + "*txtattachedvalue_" + j + "*cbopostatus_" + j + "*txtfabdescrip_" + j + "*txtcategory_" + j + "*txthscode_" + j + "*isSales_" + j;
            }
        }
        if (submit_data == "")
        {
            alert("Please Select Order No");
            return;
        }
        var data = "action=save_update_delete_lc_order_info&noRow=" + row_num + "&operation=" + operation + get_submitted_data_string('txt_system_id*hiddenexportlcorderid*cbo_export_item_category' + submit_data, "../../");

        freeze_window(operation);

        http.open("POST", "requires/export_lc_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_po_selection_save_Reply_info;
    }

    function fnc_po_selection_save_Reply_info()
    {
        if (http.readyState == 4)
        {
            var response = http.responseText.split('**');

            show_msg(trim(response[0]));

            if ((response[0] == 0 || response[0] == 1))
            {
                reset_form('exportLcFrm_3', '', '', 'txt_tot_row,0', '$(\'#tbl_order_list tbody tr:not(:first)\').remove();', 'hidden_selectedID');
                show_list_view(response[1]+"_"+response[3], 'show_po_active_listview', 'po_list_view', 'requires/export_lc_controller', 'setFilterGrid(\'po_active_list\',-1)');
                set_button_status(0, permission, 'fnc_po_selection_save', 3);
                load_po_id();
            } 
            else if (response[0] == 13)
            {
                alert('Bellow Invoice Found. Detach Not Allowed.\n Invoice No: ' + response[1] + "\n");
            }
            else if (response[0] == 11)
            {
                alert(response[1]);
            }

            release_freezing();
        }
    }

    function load_po_id()
    {
        var export_lc_id = $('#txt_system_id').val();
        if (export_lc_id != "")
        {
            get_php_form_data(export_lc_id, 'populate_attached_po_id', 'requires/export_lc_controller');
        }
    }

    function openmypage(page_link, title, row_num)
    {
        if (form_validation('txt_system_id', 'System ID') == false)
        {
            $('#export_lc_system_id').focus();
            return;
        } 
		/*else if ($('#cbo_export_item_category').val() == 10)
        {
            alert("Not For Knit Fabric Export Item Category.");
            $('#cbo_export_item_category').focus();
            return;
        } */
		else
        {
			var cbo_export_item_category=$('#cbo_export_item_category').val();
			var page_link = page_link+'&cbo_export_item_category=' + cbo_export_item_category;
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=360px,center=1,resize=1,scrolling=0', '../');
            emailwindow.onclose = function ()
            {
                var theform = this.contentDoc.forms[0];
                var order_id = this.contentDoc.getElementById("txt_selected_id").value;
				var is_sales = this.contentDoc.getElementById("txt_is_sales").value;
                //alert(order_id);
                if (order_id != "")
                {
                    var pre_selectID = $("#hidden_selectedID").val();

                    if (trim(pre_selectID) == "")
                        $("#hidden_selectedID").val(order_id);
                    else
                        $("#hidden_selectedID").val(pre_selectID + "," + order_id);

                    var tot_row = $('#txt_tot_row').val();

                    var data = order_id + "**" + tot_row+ "**" + is_sales+ "**" + cbo_export_item_category;
                    var list_view_orders = return_global_ajax_value(data, 'order_list_for_attach', '', 'requires/export_lc_controller');
                    var order_no = $('#txtordernumber_' + row_num).val();

                    if (order_no == "")
                    {
                        $("#tr_" + row_num).remove();
                    }

                    $("#tbl_order_list tbody:last").append(list_view_orders);

                    var numRow = $('table#tbl_order_list tbody tr').length;
                    $('#txt_tot_row').val(numRow);

                    var ddd = {dec_type: 2, comma: 0, currency: ''}
                    math_operation("totalOrderqnty", "txtorderqnty_", "+", numRow);
                    math_operation("totalOrdervalue", "txtordervalue_", "+", numRow, ddd);
                    math_operation("totalAttachedqnty", "txtattachedqnty_", "+", numRow);
                    math_operation("totalAttachedvalue", "txtattachedvalue_", "+", numRow, ddd);
                    set_all_onclick();
                }
            }
        }//end else
    }


    function validate_attach_qnty(row_id)
    {
        if (form_validation('txtordernumber_' + row_id, 'Order Number') == false)
        {
            $('#txtattachedqnty_' + row_id).val('');
            return;
        } else
        {
            var attached_qnty = 0;
            var txt_rate = parseFloat(Number($('#hiddenunitprice_' + row_id).val()));
            var txt_attach_order_qnty = parseInt(Number($('#txtattachedqnty_' + row_id).val()));
            var order_attached_qnty = parseInt(Number($('#order_attached_qnty_' + row_id).val()));
            var txt_order_qnty = parseInt(Number($('#txtorderqnty_' + row_id).val()));
            var hide_attached_qnty = parseInt(Number($('#hideattachedqnty_' + row_id).val()));

            var pre_att_value = hide_attached_qnty * txt_rate;

            var txt_lc_no = $('#order_attached_lc_no_' + row_id).val();
            var txt_lc_qnty = parseInt(Number($('#order_attached_lc_qty_' + row_id).val()));
            var txt_sc_no = $('#order_attached_sc_no_' + row_id).val();
            var txt_sc_qnty = parseInt(Number($('#order_attached_sc_qty_' + row_id).val()));

            attached_qnty = txt_attach_order_qnty + order_attached_qnty;

            var msg = '';

            if (attached_qnty > txt_order_qnty)
            {
                if (txt_lc_no == "" && txt_sc_no == "")
                {
                    msg = '';
                } else if (txt_lc_no != "" && txt_sc_no == "")
                {
                    msg = "\nPrevious Attached Info:\nLC NO: " + txt_lc_no + "; Attached Qty: " + txt_lc_qnty;
                } else if (txt_lc_no == "" && txt_sc_no != "")
                {
                    msg = "\nPrevious Attached Info:\nSC NO: " + txt_sc_no + "; Attached Qty: " + txt_sc_qnty;
                } else
                {
                    msg = "\nPrevious Attached Info:\nLC NO: " + txt_sc_no + "; Attached Qty: " + txt_sc_qnty + "\nSC NO: " + txt_sc_no + "; Attached Qty: " + txt_sc_qnty;
                }

                alert("Attached Qnty Exceeded Order Qnty" + msg);

                $('#txtattachedqnty_' + row_id).val(hide_attached_qnty);
                $('#txtattachedvalue_' + row_id).val(pre_att_value.toFixed(2));
                calculate_attach_val(row_id);
            } else
            {
                calculate_attach_val(row_id);
            }
        }
    }

    function calculate_attach_val(row_id)
    {
        if (form_validation('txtordernumber_' + row_id, 'Order Number') == false)
        {
            $('#hiddenunitprice_' + row_id).val('');
            return;
        }
        var attached_val = 0;
        var txt_rate = parseFloat(Number($('#hiddenunitprice_' + row_id).val()));
        var txt_attach_order_qnty = parseInt(Number($('#txtattachedqnty_' + row_id).val()));
        attached_val = txt_attach_order_qnty * txt_rate;
        $('#txtattachedvalue_' + row_id).val(attached_val.toFixed(2));

        var numRow = $('table#tbl_order_list tbody tr').length;

        var ddd = {dec_type: 2, comma: 0, currency: ''}
        math_operation("totalAttachedqnty", "txtattachedqnty_", "+", numRow);
        math_operation("totalAttachedvalue", "txtattachedvalue_", "+", numRow, ddd);
    }

//fnc_lien_letter()
    function fnc_lien_letter() // fnc_lien_letter 1
    {
        //alert("su..re");
        if (form_validation('txt_system_id', 'System ID') == false)
        {
            return;
        }
        print_report(4 + '**' + $('#txt_system_id').val(), 'export_lien_letter', 'requires/export_lc_controller');
    }

    function fnc_lien_letter2() // fnc_lien_letter 2
    {
        //alert("su..re");
        if (form_validation('txt_system_id', 'System ID') == false)
        {
            return;
        }
        print_report(4 + '**' + $('#txt_system_id').val(), 'export_lien_letter2', 'requires/export_lc_controller');
    }

//-------------------------------------------order attached js end here --------------------------------&companyID='+companyID//
    function fn_file_no()
    {
        //alert(1);return;
        if (form_validation('cbo_beneficiary_name', 'Company Name') == false)
        {
            return;
        }
        var companyID = $('#cbo_beneficiary_name').val();
        var page_link = 'requires/export_lc_controller.php?action=file_search&companyID=' + companyID;
        var title = 'File Search Form';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var file_ref = this.contentDoc.getElementById("hidden_file_id").value;
            $('#txt_internal_file_no').val(file_ref);

        }
    }
	
	function sendMail()
	{
		
		if (form_validation('export_lc_system_id','System Id')==false)
		{
			return;
		}
		
		var sys_id=$('#export_lc_system_id').val();
		
		
		var data="action=lc&sys_id="+sys_id;
 		freeze_window(operation);
		http.open("POST","../../auto_mail/lcsc_notification_auto_mail.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText);
				alert(reponse);
				release_freezing();
				
	
			}
		}

	}

	
	
</script>
<style>
    #exportlc_tbl input:not([type=checkbox]) input:not([class=flt]{
        width:150px;
    }

    /*#exportlc_tbl input[name=checkbox]{
            width:10px; Both Works Perfectly
    }
    */
</style> 

</head>

<body onLoad="set_hotkey();replacement_lc_diplay(2);">
    <div style="width:100%;" align="center">																	
        <? echo load_freeze_divs("../../", $permission); ?>
        <fieldset style="width:930px; margin-bottom:10px;">
            <legend>Export LC Entry</legend>
            <form name="exportLcFrm_1" id="exportLcFrm_1" autocomplete="off" method="POST"  >
                <table cellpadding="0" cellspacing="1" width="100%" id="exportlc_tbl">
                    <tr> 
                        <td colspan="6" align="center" ><b>System ID</b> 
                            <input type="hidden" name="txt_system_id" id="txt_system_id"  readonly class="text_boxes">
                            <input type="text" name="export_lc_system_id" id="export_lc_system_id"  placeholder="Double Click" onDblClick="export_lc_popup()" readonly class="text_boxes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Import BTB</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_import_btb" id="txt_import_btb" style="width:140px" class="text_boxes" placeholder="Double Click to Search L/C" onDblClick="openmypage_importPi()" readonly />
                            <input type="hidden" name="import_btb" id="import_btb"/>
                            <input type="hidden" name="import_btb_id" id="import_btb_id"/>
                        </td>
                    </tr>
                    <tr><td height="5" colspan="6"></td></tr>
                    <tr>
                        <td class="must_entry_caption">Beneficiary</td>
                        <td>
						<?
                        //get_php_form_data(903, 'populate_data_from_export_lc', 'requires/export_lc_controller');
                        //get_php_form_data( this.value, 'get_btb_limit', 'requires/export_lc_controller' );
                        //get_php_form_data( this.value, 'file_write_mathod', 'requires/export_lc_controller' );
                        //set_field_level_access(this.value);
                        echo create_drop_down("cbo_beneficiary_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Beneficiary --", 0, "load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_notifying_party', 'notifying_party_td' );load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_consignee', 'consignee_td' );load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_applicant_name', 'applicant_name_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/export_lc_controller' );get_php_form_data( this.value, 'get_btb_limit', 'requires/export_lc_controller' );get_php_form_data( this.value, 'file_write_mathod', 'requires/export_lc_controller' );set_field_level_access(this.value);");
                        ?>                          
                        </td>
                        <td class="must_entry_caption">Internal File No</td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" style="width:150px" class="text_boxes_numeric"  /></td>
                        <td width="127">Bank File No</td>
                        <td width="181"><input type="text" name="txt_bank_file_no" id="txt_bank_file_no" class="text_boxes" style="width:150px" maxlength="50" title="Maximum Character 50" /></td>
                    </tr>
                    <tr>
                        <td width="144" class="must_entry_caption">Year</td>
                        <td width="168"><input name="txt_year" id="txt_year" class="text_boxes" style="width:150px" maxlength="10" title="Maximum Character 10"></td>

                        <td width="123" class="must_entry_caption">LC Number </td>
                        <td width="176">
                            <input type="text"  name="txt_lc_number"  id="txt_lc_number" style="width:150px" class="text_boxes" maxlength="50" title="Maximum Character 50">
                        </td>
                        <td width="127" class="must_entry_caption">LC Value</td>
                        <td width="181"><input type="text" name="txt_lc_value" id="txt_lc_value" style="width:90px" class="text_boxes_numeric" ><input type="text" name="txt_lc_ini_value" id="txt_lc_ini_value" style="width:50px" class="text_boxes_numeric" placeholder="Initial Value" title="Initial Value" readonly disabled ></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">LC Date</td>
                        <td><input type="text" name="txt_lc_date" id="txt_lc_date" style="width:150px" class="datepicker" value="<?echo date('d-m-Y')?>" readonly /></td>
                        <td>Currency</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_currency_name", 162, $currency, "", 0, "", 2, "");
                            ?>
                        </td>
                        <td class="must_entry_caption">Buyer Name</td>
                       	<td id="buyer_td_id"> 
                            <?
                            echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Applicant Name</td>
                        <td id="applicant_name_td"> 
                            <?
                            echo create_drop_down("cbo_applicant_name", 162, $blank_array, "", 1, "---- Select ----", 0, "");
                            ?>
                        </td>
                        <td>Notifying Party</td>
                        <td id="notifying_party_td">
                            <?
                            echo create_drop_down("cbo_notifying_party", 162, $blank_array, "", 0, "---- Select ----", 0, "");
                            ?>
                        </td>
                        <td>Consignee</td>
                        <td id="consignee_td">
                            <?
                            echo create_drop_down("cbo_consignee", 162, $blank_array, "", 0, "---- Select ----", 0, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Issuing Bank</td>
                        <td><input type="text" name="txt_issuing_bank" style="width:150px" id="txt_issuing_bank" class="text_boxes" maxlength="100" title="Maximum Character 100" /></td>
                        <td class="must_entry_caption">Lien Bank</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_lien_bank", 162, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name", "id,bank_name", 1, "-- Select Lien Bank --", 0, "");
                            ?>
                        </td>
                        <td>Lien Date</td>
                        <td><input type="text" name="txt_lien_date" style="width:150px" id="txt_lien_date" class="datepicker" readonly ></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Last Shipment Date</td>
                        <td><input type="text" name="txt_last_shipment_date" style="width:150px" id="txt_last_shipment_date" class="datepicker" readonly onChange="fn_add_date_field();"></td>
                        <td>LC Expiry Date </td>
                        <td><input type="text" name="txt_expiry_date" style="width:150px" id="txt_expiry_date" class="datepicker" readonly ></td>
                        <td><p>Tolerance %</p></td>
                        <td><input type="text" name="txt_tolerance" style="width:150px" id="txt_tolerance" class="text_boxes_numeric" value="5" ></td>
                    </tr>
                    <tr>
                        <td>Shipping Mode</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_shipping_mode", 162, $shipment_mode, "", 0, "", 0, "");
                            ?>
                        </td>
                        <td class="must_entry_caption">Pay Term</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_pay_term", 162, $pay_term, "", 1, "--- Select ---", 0, "", '', '1,2');
                            ?>
                        </td>
                        <td>Tenor</td>
                        <td><input type="text" name="txt_tenor" id="txt_tenor" style="width:150px" class="text_boxes_numeric"  /></td>
                    </tr>
                    <tr>
                        <td>Inco Term</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_inco_term", 162, $incoterm, "", 0, "", 0, "");
                            ?>
                        </td>
                        <td>Inco Term Place</td>
                        <td><input type="text" name="txt_inco_term_place" style="width:150px" id="txt_inco_term_place" class="text_boxes" value="" maxlength="50" title="Maximum Character 50"/></td>
                        <td>LC Source </td>
                        <td>
                            <?
                            echo create_drop_down("cbo_lc_source", 162, $contract_source, "", 0, "", 0, "party_loading_dischage_field(this.value)");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><p>Port of Entry</p></td>
                        <td><input type="text" name="txt_port_of_entry" style="width:150px" id="txt_port_of_entry" class="text_boxes" value="Ctg" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Port of Loading</td>
                        <td><input type="text" name="txt_port_of_loading" style="width:150px" id="txt_port_of_loading" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Port of Discharge</td>
                        <td><input type="text" name="txt_port_of_discharge" style="width:150px" id="txt_port_of_discharge" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                    </tr>
                    <tr>
                        <td>Doc Present Days</td>
                        <td><input type="text" name="txt_doc_presentation_days" style="width:150px" id="txt_doc_presentation_days" class="text_boxes_numeric" maxlength="50" title="Maximum Character 50"/></td>
                        <td>BTB Limit %</td>
                        <td><input type="text" name="txt_max_btb_limit" style="width:150px" id="txt_max_btb_limit" class="text_boxes_numeric" ></td>
                        <td>Foreign Comn%</td>
                        <td><input type="text" name="txt_foreign_comn" style="width:150px" id="txt_foreign_comn" class="text_boxes_numeric"  /></td>
                    </tr>
                    <tr>
                        <td>Local Comn%</td>
                        <td><input type="text" name="txt_local_comn" style="width:150px" id="txt_local_comn" class="text_boxes_numeric"  /></td>
                        <td>Transfering Bank Ref</td>
                        <td><input type="text" name="txt_transfering_bank_ref" style="width:150px" id="txt_transfering_bank_ref" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Transferable </td>
                        <td>
                            <?
                            echo create_drop_down("cbo_is_lc_transfarrable", 162, $yes_no, "", 0, "", 0, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Replacement LC </td>
                        <td>
                            <?
                            echo create_drop_down("cbo_replacement_lc", 162, $yes_no, "", 0, "", 2, "replacement_lc_diplay(this.value)");
                            ?>
                        </td>
                        <td>Transfering Bank </td>
                        <td><input name="txt_transfer_bank" style="width:150px" id="txt_transfer_bank" class="text_boxes" maxlength="50" title="Maximum Character 50"></td>
                        <td>Negotiating Bank </td>
                        <td><input type="text" name="txt_negotiating_bank" style="width:150px" id="txt_negotiating_bank" class="text_boxes" maxlength="50" title="Maximum Character 50"/></td>
                    </tr>
                    <tr>
                        <td>Nominated Ship. Line</td>
                        <td><input type="text" name="txt_nominated_shipp_line" style="width:150px" id="txt_nominated_shipp_line" class="text_boxes" maxlength="100" title="Maximum Character 100"/></td>
                        <td>Re-Imbursing Bank</td>
                        <td><input name="txt_re_imbursing_bank" style="width:150px" id="txt_re_imbursing_bank" class="text_boxes" maxlength="50" title="Maximum Character 50"/></td>
                        <td>Claim Adjustment </td>
                        <td><input type="text" name="txt_claim_adjustment" style="width:150px" id="txt_claim_adjustment" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                        <td>Expiry Place </td>
                        <td><input type="text" name="txt_expiry_place" style="width:150px" id="txt_expiry_place" class="text_boxes" value="Bangladesh" maxlength="50" title="Maximum Character 50"/></td>
                        <td>Reason</td>
                        <td colspan="3"><input type="text" name="txt_reason" id="txt_reason" class="text_boxes" style="width:456px" maxlength="250" title="Maximum Character 250"></td>
                    </tr>
                    <tr>
                        <td>BL Clause</td>
                        <td colspan="5"><textarea name="txt_bl_clause" id="txt_bl_clause" style="width:750px" class="text_area" maxlength="2000" title="Maximum Character 2000" ></textarea></td>
                    </tr>
                    <tr>
                        <td>Reimbursement Clauses</td>
                        <td colspan="5"><textarea name="txt_reimbursement_clauses" id="txt_reimbursement_clauses" style="width:750px" class="text_area" maxlength="2000" title="Maximum Character 2000" ></textarea></td>
                    </tr>
                    <tr>
                        <td>Discount Clauses</td>
                        <td colspan="5"><textarea name="txt_discount_clauses"  id="txt_discount_clauses" style="width:750px" class="text_area" maxlength="2000" title="Maximum Character 2000" ></textarea></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Export Item Category</td>
                        <td>
                            <? echo create_drop_down("cbo_export_item_category", 162, $export_item_category, "", 1, "--- Select ---", 1, ""); ?>
                        </td>
                        <td>Remarks</td>
                        <td colspan="2"><input name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:256px" maxlength="255" title="Maximum Character 255"></td>
                        <td align="right" colspan="2">
                                <input type="button" id="image_button" class="image_uploader" style="width:152px; float:right; margin-right:120px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('txt_system_id').value, '', 'Export LC Entry', 2, 1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                            
							<?
                            echo load_submit_buttons($permission, "fnc_export_lc_entry", 0, 0, "reset_form('exportLcFrm_1*exportLcFrm_2*exportLcFrm_3','po_list_view*sc_list_view','','txt_port_of_entry,Ctg*txt_tot_row,0*txt_tolerance,5*cbo_currency_name,2*cbo_export_item_category,1*cbo_replacement_lc,2*txt_expiry_place,Bangladesh','disable_enable_fields(\'cbo_beneficiary_name*txt_lc_value*txt_lc_number*cbo_buyer_name*txt_lc_date*cbo_currency_name*cbo_export_item_category*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_tolerance*txt_issuing_bank*txt_claim_adjustment*txt_discount_clauses*txt_remarks*txt_doc_presentation_days\',0)');replacement_lc_diplay(2);$('#cbo_buyer_name option[value!=\'0\']').remove();$('#tbl_order_list tbody tr:not(:first)').remove();$('#tbl_order_list').show();", 1);
                            ?>
                            <input type="button" value="Lien Letter" id="btn_lien_letter" name="btn_lien_letter" class="formbutton" style="width:100px;" onClick="fnc_lien_letter()" />
                            <input type="button" value="Check List" id="btn_lien_letter" name="btn_lien_letter" class="formbutton" style="width:100px;" onClick="fnc_lien_letter2()" />
                            <input class="formbutton" type="button" onClick="sendMail()" value="Mail Send" style="width:80px;">
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
        <!-------------------------- 1st form end here ------------------------------------------->

        <!------------------------------ 2nd form start here ----------------------------------------->
        <form name="exportLcFrm_2" id="exportLcFrm_2" autocomplete="off"  >
            <fieldset style="width:930px;">
                <table cellpadding="0"  cellspacing="0" width="100%" id="tbl_sales_contract" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th class="must_entry_caption">Sales Contract</th>
                    <th>Replaced Amount</th>
                    <th>Contract Value</th>
                    <th>Cumulative Replaced</th>
                    <th>Yet to Replace</th>
                    <th>Attached BTB LC</th>
                    <th>Status</th>
                    </thead>
                    <tbody>
                        <tr class="general" id="trs_1">
                            <td>
                                <input type="text" name="txtSalesContractNo_1" id="txtSalesContractNo_1" placeholder="Double Click"  class="text_boxes" style="width:125px" onDblClick="add_sales_contract(1)" readonly/>
                                <input type="hidden" name="hiddenScId_1" id="hiddenScId_1">
                            </td>
                            <td>
                                <input type="text" name="txtReplacementAmount_1" id="txtReplacementAmount_1" class="text_boxes_numeric" style="width:115px;" onKeyup="CalculateCumulativeValue(this.value, this.id);"/>
                                <input type="hidden" name="hideReplacementAmount_1" id="hideReplacementAmount_1" readonly/>
                            </td>
                            <td><input type="text" name="txtContractValue_1" id="txtContractValue_1" class="text_boxes_numeric" style="width:90px" readonly /></td>
                            <td>
                                <input type="text" name="txtCumulativeReplaced_1" id="txtCumulativeReplaced_1" class="text_boxes_numeric" style="width:110px" readonly />
                                <input type="hidden" name="txtCumulativeReplacedDB_1" id="txtCumulativeReplacedDB_1" class="text_boxes" style="width:110px" />
                            </td>
                            <td><input type="text" name="txtYetToReplace_1" id="txtYetToReplace_1" class="text_boxes_numeric" style="width:110px" readonly/></td>
                            <td>
                                <input type="text" name="txtBtbLcSelected_1" id="txtBtbLcSelected_1" placeholder="" class="text_boxes" style="width:130px" disabled="disabled"/>
                                <input type="hidden" name="txtBtbLcSelectedID_1" id="txtBtbLcSelectedID_1" class="text_boxes"  style="width:130px" />
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_sc_status_1", 100, $attach_detach_array, "", 0, "", 1, "");
                                ?>
                            </td>                           
                        </tr>        	
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td>Total</td>   	
                            <td style="margin-right:5px"><input type="text" name="totalReplacedAmount" id="totalReplacedAmount" class="text_boxes_numeric" style="width:115px;" readonly= "readonly" />&nbsp;&nbsp;&nbsp;</td>
                            <td><input type="text" name="totalContractValue" id="totalContractValue" class="text_boxes_numeric" style="width:90px;" readonly />&nbsp;</td>
                            <td><input type="text" name="totalCumulativeReplaced" id="totalCumulativeReplaced" class="text_boxes_numeric" style="width:110px;" readonly />&nbsp;&nbsp;</td>
                            <td><input type="text" name="totalYettoReplace" id="totalYettoReplace" class="text_boxes_numeric" style="width:110px;" readonly />&nbsp;&nbsp;</td>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="7" height="30" valign="middle" align="center" class="button_container">
                                <? echo load_submit_buttons($permission, "fnc_sales_contract_selection", 0, 0, "reset_form('exportLcFrm_2','','','txt_tot_row_attach_sales,0','$(\'#tbl_sales_contract tbody tr:not(:first)\').remove();load_sc_id();','hidden_sc_selectedID')", 2); ?>
                                <input type="hidden" name="hiddenlcAttachSalesContractID" id="hiddenlcAttachSalesContractID" readonly= "readonly" /><!-- for update --> 
                                <input type="hidden" id="hidden_sc_selectedID" readonly= "readonly" />
                                <input type="hidden" name="txt_tot_row_attach_sales" id="txt_tot_row_attach_sales" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                            </td>
                        </tr>
                    </tfoot>	
                </table>
                <div style="width:930px; margin-top:10px" id="sc_list_view" align="left"></div>
            </fieldset>
        </form>

        <input type="button" value="Click for already attached PO" name="Attached PO" id="Attached PO" class="formbutton" onClick="openmypage('requires/export_lc_controller.php?action=order_popup&types=attached_po_status&buyer_id=' + document.getElementById('cbo_buyer_name').value + '&selectID=' + document.getElementById('hidden_selectedID').value + '&export_lcID=' + document.getElementById('txt_system_id').value + '&company_id=' + document.getElementById('cbo_beneficiary_name').value+ '&import_btb=' + document.getElementById('import_btb').value+ '&cbo_export_item_category=' + document.getElementById('cbo_export_item_category').value, 'Attached PO', '')" style="width:210px"/>

        <form name="exportLcFrm_3" id="exportLcFrm_3" method="POST" action="">
            <fieldset style="width:1200px; margin:5px">
                <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" class="rpt_table" id="tbl_order_list">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Order Number</th>
                            <th>Acc.PO No.</th>
                            <th>Order Qty</th>
                            <th>Order Value</th>
                            <th class="must_entry_caption">Attach. Qty</th>
                            <th>Rate</th>
                            <th>Attach. Val.</th>
                            <th>Style Ref</th>
                            <th>Item</th>
                            <th>Job No.</th>
                            <th>Fabric Description</th>
                            <th>Categroy</th>
                            <th>Hs Code</th>
                            <th>Status</th> 
                        </tr>                         
                    </thead>
                    <tbody>
                        <tr class="general" id="tr_1">
                            <td><input type="text" name="txtordernumber_1" id="txtordernumber_1" class="text_boxes" style="width:100px"  onDblClick= "openmypage('requires/export_lc_controller.php?action=order_popup&types=order_select_popup&buyer_id=' + document.getElementById('cbo_buyer_name').value + '&selectID=' + document.getElementById('hidden_selectedID').value + '&export_lcID=' + document.getElementById('txt_system_id').value + '&company_id=' + document.getElementById('cbo_beneficiary_name').value+ '&import_btb=' + document.getElementById('import_btb').value+ '&cbo_export_item_category=' + document.getElementById('cbo_export_item_category').value, 'PO Selection Form', 1)" readonly= "readonly" placeholder="Double Click" />
                            <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly value="">
                            <input type="hidden" name="isSales_1" id="isSales_1" value="">
                            </td>
                            <td><input type="text" name="txtaccordernumber_1" id="txtaccordernumber_1" class="text_boxes" style="width:100px;" readonly= "readonly" /></td>
                            <td><input type="text" name="txtorderqnty_1" id="txtorderqnty_1" class="text_boxes_numeric" style="width:65px;" readonly= "readonly" /></td>
                            <td><input type="text" name="txtordervalue_1" id="txtordervalue_1" class="text_boxes_numeric" style="width:80px;" readonly= "readonly"/></td>
                            <td><input type="text" name="txtattachedqnty_1" id="txtattachedqnty_1" class="text_boxes_numeric" style="width:65px" onKeyUp="validate_attach_qnty(1)" />
                                <input type="hidden" name="hideattachedqnty_1" id="hideattachedqnty_1" class="text_boxes_numeric"/>
                            </td>
                            <td>
                                <input type="text" name="hiddenunitprice_1" id="hiddenunitprice_1" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_attach_val(1)"  readonly disabled >
                            </td>
                            <td><input type="text" name="txtattachedvalue_1" id="txtattachedvalue_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtstyleref_1" id="txtstyleref_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtitemname_1" id="txtitemname_1" class="text_boxes" style="width:110px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>
                    <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_qnty_1" id="order_attached_qnty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_1" id="order_attached_lc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_1" id="order_attached_lc_qty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_1" id="order_attached_sc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_1" id="order_attached_sc_qty_1" readonly= "readonly" />
                    <td><input type="text" name="txtfabdescrip_1" id="txtfabdescrip_1" class="text_boxes" style="width:90px" /></td>
                    <td><input type="text" name="txtcategory_1" id="txtcategory_1" class="text_boxes_numeric" style="width:50px" /></td>
                    <td><input type="text" name="txthscode_1" id="txthscode_1" class="text_boxes" style="width:40px"/></td>       
                    <td>                             
                        <? echo create_drop_down("cbopostatus_1", 60, $attach_detach_array, "", 0, "", 1, ""); ?>
                    </td> 
                    </tr>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td>&nbsp;</td>
                            <td>Total</td>
                            <td><input type="text" name="totalOrderqnty" id="totalOrderqnty" class="text_boxes_numeric" style="width:70px;" readonly= "readonly" /></td>
                            <td><input type="text" name="totalOrdervalue" id="totalOrdervalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                            <td><input type="text" name="totalAttachedqnty" id="totalAttachedqnty" class="text_boxes_numeric" style="width:70px;" readonly= "readonly" /></td>
                            <td>&nbsp;</td>
                            <td><input type="text" name="totalAttachedvalue" id="totalAttachedvalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                            <td colspan="7">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="14" height="50" valign="middle" align="center" class="button_container">
                                <? echo load_submit_buttons($permission, "fnc_po_selection_save", 0, 0, "reset_form('exportLcFrm_3','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();load_po_id();','hidden_selectedID')", 3); ?>
                                <input type="hidden" name="hiddenexportlcorderid" id="hiddenexportlcorderid" readonly= "readonly" /> <!-- for update --> 
                                <input type="hidden" id="hidden_selectedID" readonly= "readonly" />
                                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div style="width:100%; margin-top:10px" id="po_list_view" align="left"></div>
            </fieldset>
        </form>
        <!-- 3rd form end here -->
    </div>
</body>
<script>
    set_multiselect('cbo_notifying_party*cbo_consignee', '0*0', '0', '', '0*0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>