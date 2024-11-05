<?php
/*--- ----------------------------------------- Comments
Purpose         :   Supplier Debit Note Entry
Functionality   :
JS Functions    :
Created by      :   Wayasel Ahmed
Creation date   :   12.04.23
Updated by      :  
Update date     :
Oracle Convert  :
Convert date    :
QC Performed BY :
QC Date         :
Comments        :
*/
session_start();
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Supplier Debit Note Entry', '../', 1, 1, $unicode, 1, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
    var permission='<?php echo $permission; ?>';

    function get_company_config(company_id) {
        load_drop_down('requires/supplier_debit_note_entry_controller', company_id, 'load_drop_down_location', 'location_td');
    }

    function openSalesOrderPopup() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name*cbo_supplier_name*cbo_debit_note_for', 'Company*supplier*debit_note')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_debit_note_for').value+"_"+document.getElementById('cbo_supplier_name').value;
        // console.log(data);
        var pageLink = 'requires/supplier_debit_note_entry_controller.php?action=work_order_popup&data='+data;
        var title = 'Word Ordere';
        salesPopup = dhtmlmodal.open('Sales No', 'iframe', pageLink, title, 'width=940px, height=400px, center=1, resize=0, scrolling=0', '../');
        salesPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var prod_id_mst=this.contentDoc.getElementById('selected_prod_id').value;
            show_list_view(prod_id_mst, 'populate_dtls_data_from_search_popup', 'delivery_details_container', 'requires/supplier_debit_note_entry_controller', '');
        }
    }

    function fnc_inspection(operation) 
	{
        if ( !form_validation('cbo_company_name*txt_debat_note_date', 'Company Name*Debat date') ) {
            return;
        }
       // freeze_window(operation);
        var dataStr = '';
        var cbo_company_name     = $('#cbo_company_name').val();
        var txt_debat_note_date  = $('#txt_debat_note_date').val();
        var cbo_supplier_name    = $('#cbo_supplier_name').val();
        var cbo_debit_note_for   = $('#cbo_debit_note_for').val();
        var cbo_currency         = $('#cbo_currency').val();
        var txt_exchange_rate    = $('#txt_exchange_rate').val();
        var cbo_pay_mode         = $('#cbo_pay_mode').val();
        var hdnupdateid        = $('#hdnupdateid').val();
        var txt_system_id        = $('#txt_system_id').val();
        var txt_remarks          = $('#txt_remarks').val();
        var txt_issuing_bank          = $('#txt_issuing_bank').val();
        var j=0; var check_field=0; data_all=""; var i=0; 

        $("#tbl_dtls_yarn_dyeing tbody tr").each(function()
        {
            var txtWoOrder             = $(this).find('input[name="txtWoOrder[]"]').val();
            var txtLcNumber            = $(this).find('input[name="txtLcNumber[]"]').val();
            var txtBookingId           = $(this).find('input[name="txtBookingId[]"]').val();
            var txtInvoiceNo           = $(this).find('input[name="txtInvoiceNo[]"]').val();
            var txtwoAmmount           = $(this).find('input[name="txtwoAmmount[]"]').val();
            var txtdebatNoteDetails    = $(this).find('input[name="txtdebatNoteDetails[]"]').val();
            var txtHiddenDtlsId    = $(this).find('input[name="txtHiddenDtlsId[]"]').val();
            var noteAmmount            = $(this).find('input[name="noteAmmount[]"]').val()*1;
          
            if(noteAmmount>0){
            j++;
            i++;
            data_all += "&txtWoOrder_" + j + "='" + txtWoOrder + "'&txtLcNumber_" + j + "='" + txtLcNumber+ "'&txtBookingId_" + j + "='" + txtBookingId+ "'&txtInvoiceNo_" + j + "='" + txtInvoiceNo  + "'&txtwoAmmount_" + j + "='" + txtwoAmmount + "'&txtdebatNoteDetails_" + j + "='" + txtdebatNoteDetails  + "'&noteAmmount_" + j + "='" + noteAmmount+ "'&txtHiddenDtlsId_" + j + "='" + txtHiddenDtlsId + "'";
            }
        });

       // alert( data_all); return;
        if(check_field==0)
		{
            var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&txt_debat_note_date='+txt_debat_note_date+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_debit_note_for='+cbo_debit_note_for+'&cbo_currency='+cbo_currency+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_pay_mode='+cbo_pay_mode+'&txt_system_id='+txt_system_id+'&txt_remarks='+txt_remarks+'&txt_issuing_bank='+txt_issuing_bank+'&hdnupdateid='+hdnupdateid+data_all;
            // alert (data); return;
            freeze_window(operation);
            http.open("POST","requires/supplier_debit_note_entry_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_inspection_response;
        }
		else
		{
            return;
        }
    }

    function fnResetForm() 
	{
        console.log('reset');
    }

    function fnc_inspection_response() 
	{
        if(http.readyState == 4) 
		{
            //alert(0); 
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            //$('#cbo_uom').val(12);
            if(response[0]==0 || response[0]==1) 
			{
                document.getElementById('txt_system_id').value= response[1];
                document.getElementById('hdnupdateid').value = response[2];
                show_list_view(+response[2], 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/supplier_debit_note_entry_controller', ''); 
                set_button_status(1, permission, 'fnc_inspection', 1);
            }
            else if(response[0]==2){
                document.getElementById('txt_system_id').value= response[1];
                document.getElementById('hdnupdateid').value = response[2];
                get_php_form_data(response[2], 'populate_mst_data_from_search_popup', 'requires/supplier_debit_note_entry_controller');
                show_list_view(response[2], 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/supplier_debit_note_entry_controller', ''); 
                set_button_status(0, permission, 'fnc_inspection', 1);
            }
            release_freezing();
        }
    }

    function print_report1()
	{
		if($('#hdnupdateid').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#hdnupdateid').val()+'*'+$('#cbo_debit_note_for').val()+'*'+report_title, "supplier_debit_note_print", "requires/supplier_debit_note_entry_controller") 
			return;
			//show_msg("3");
		}
	}

    function openmypage_system_id() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name', 'Company')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;
        // console.log(data);
        var pageLink = 'requires/supplier_debit_note_entry_controller.php?action=system_id_popup&data='+data;
        var title = 'Search Info';
        deliveryPopup = dhtmlmodal.open('Inspection Info', 'iframe', pageLink, title, 'width=800px, height=500px, center=1, resize=0, scrolling=0', '../');
        deliveryPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var mst_id=this.contentDoc.getElementById('selected_mst_id').value;
            document.getElementById('hdnupdateid').value = mst_id;
            get_php_form_data(mst_id, 'populate_mst_data_from_search_popup', 'requires/supplier_debit_note_entry_controller');
            
            show_list_view(mst_id, 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/supplier_debit_note_entry_controller', ''); 
            set_button_status(1, permission, 'fnc_inspection', 1);
        }
    }

    function fnc_exchange_rate()
	{
		var currency_id=$('#cbo_currency').val();
		var company_name = $('#cbo_company_name').val();
		var data =currency_id+'**'+company_name;
		var response=return_global_ajax_value(data, 'check_conversion_rate', '', 'requires/supplier_debit_note_entry_controller');
		    $('#txt_exchange_rate').val(response);
			$('#txt_exchange_rate').attr('readonly',true);	
	}

    function calculateTotal() {
        var total = 0;
        var noteAmountInputs = document.getElementsByName("noteAmmount[]");
        
        for (var i = 0; i < noteAmountInputs.length; i++) {
            var noteAmount = parseFloat(noteAmountInputs[i].value);
            
            if (!isNaN(noteAmount)) {
                total += noteAmount;
            }
        }
        
        document.getElementById("total_note_ammount").value = total;
    }

    function openmypage()
    {
        var title = 'Issuing Bank Info';
        var txt_issuing_bank = document.getElementById("txt_issuing_bank").value;
        var page_link = 'requires/supplier_debit_note_entry_controller.php?data='+txt_issuing_bank+'&action=issuing_bank_popup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];

            var issue_bank_data=this.contentDoc.getElementById("txt_hidden_issue_bank").value;

            document.getElementById("txt_issuing_bank").value=issue_bank_data;
        }
    }
	
</script>
</head>

<body onLoad="set_hotkey()">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="dyeingorderentry_1" id="dyeingorderentry_1" autocomplete="off">
            <fieldset style="width:850px;">
            <legend>Debit Note Master</legend>
                <table width="830" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" onDblClick="openmypage_system_id();" placeholder="Double Click" readonly style="width: 150px;" />
                            <input type="hidden" name="hdnupdateid" id="hdnupdateid">
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160">
                            <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_exchange_rate(); load_drop_down( 'requires/supplier_debit_note_entry_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );"); ?>
                        </td>
                        <td class="must_entry_caption">Debit note Entry Date:</td>
                        <td>
                            <input type="text" name="txt_debat_note_date"  style="width:140px"  id="txt_debat_note_date" value="<? echo date("d-m-Y")?>" class="datepicker" />
                        </td>
                        <td class="must_entry_caption">Supplier Name</td>
                        <td id="supplier_td">
                            <? echo create_drop_down( "cbo_supplier_name", 150, $blank_array, "", 1, "-- Select Supplier --", $selected, ""); ?>
                        </td>                    
                    </tr>
                    <tr>
                        <td >Debit Note For:</td>
                          <td><? echo create_drop_down( "cbo_debit_note_for", 150, "select category_id, short_name from  lib_item_category_list where status_active=1 and category_id in(1,3,2,4,25) and is_deleted=0 order by short_name","category_id,short_name", 1, "-- Select Debit Note --","","","","", "",1); ?></td>

                        <td width="110"  align="right"><strong>Work Order:</strong></td>
                            <td width="160">
                                <input class="text_boxes" type="text" name="txt_wo_system_id" id="txt_wo_system_id" onDblClick="openSalesOrderPopup();" placeholder="Double Click" readonly style="width: 150px;" />
                        </td>
                                               
                        <td >Debit Note Currency:</td>
                        <td width="">
                         	<?
							   	echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 0, "fnc_exchange_rate()",0 );
 							?>
                        </td>
                    </tr>
                    <tr>
                        <td >Exchange Rate</td>
                        <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate"  class="text_boxes" style="width:140px"  placeholder="Exchange Rate" />
                        </td>
                        <td class="must_entry_caption">Pay Mode</td>
						<td>
                        	<?
							   	echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select --", 4, "",0 );
 							?>
                        </td>
                      <td >Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px"  placeholder="Entry" />
                        </td>
                    </tr>
                    <tr>
                    <td>
                    Issuing Bank</td>
                        <td>
                            <input name="txt_issuing_bank" placeholder="Double Click to Search" id="txt_issuing_bank" onDblClick="openmypage(); return false"  class="text_boxes" style="width:150px" autocomplete="off" readonly>
                        </td>
                    </tr>
                </table>
                </table>
        </fieldset>
        <fieldset style="width:1050px;">
           <legend>Debit Note Details</legend>
                <table width="1050px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_yarn_dyeing">
                  <thead class="form_table_header">
                    <th width="120">Debit Note Details</th>
                    <th width="130" id="sales_order">Work Order No</th>
                    <th width="100" id="">LC NO</th>
                    <th width="100" >Invoice No</th>
                    <th width="100" >Work Order Value</th>         
                    <th width="100" >Amount</th>
                </thead>
                <tbody id="delivery_details_container">
                    <tr>
                       <td align="center">
                            <input name="txtdebatNoteDetails_1" id="txtdebatNoteDetails_1" type="text" class="text_boxes"  placeholder="write" style="width:100px"/>
                        </td>

                        <td align="center">
                            <input name="txtWoOrder_1" id="txtWoOrder_1" type="text" class="text_boxes"  placeholder="Display" readonly style="width:130px" />
                        </td>
                        <td align="center">
                            <input name="txtLcNumber_1" id="txtLcNumber_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txtInvoiceNo_1" id="txtInvoiceNo_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>

                        <td align="center">
                            <input name="txtwoAmmount_1" id="txtwoAmmount_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="noteAmmount_1" id="noteAmmount_1" type="text" class="text_boxes" placeholder="write" style="width:100px" />
                            <input name="txtBookingId[]" id="txtBookingId_1" type="hidden" value="" />
                        </td>                  
                    </tr>
                </tbody>
            </table>
            <table width="100%" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" colspan="11" class="button_container">
                        <?php echo load_submit_buttons($permission, 'fnc_inspection', 0, 0, 'fnResetForm();', 1); ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="11" >
                        <input type="button" id="Print" value="Print" class="formbutton" style="width:100px;" onClick="print_report1();" >
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>