<?php
/*--- ----------------------------------------- Comments
Purpose         :   Perpose to Take Export Local Person Commission
Functionality   :
JS Functions    :
Created by      :   Wayasel Ahmed
Creation date   :   22.10.2023
Updated by      :  
Update date     :
Oracle Convert  :
Convert date    :
QC Performed BY :
QC Date         :
Comments        :
*/
session_start();
require_once('../../includes/common.php'); 
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Local Commission Entry', '../../', 1, 1, $unicode, 1, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    function fnc_inspection(operation) 
	{
        if (!form_validation('cbo_company_name*txt_local_date*cbo_commison_name', 'Company Name*Local date*Commison name')){
            return;
        }
        var cbo_company_name     = $('#cbo_company_name').val();
        var txt_local_date  = $('#txt_local_date').val();
        var cbo_buyer_name    = $('#cbo_buyer_name').val();
        var cbo_commison_name   = $('#cbo_commison_name').val();
        var txt_remark         = $('#txt_remark').val();
        var hdnupdateid         = $('#hdnupdateid').val();

        var j=0; var check_field=0; data_all=""; var i=0; 
        $("#tbl_dtls_local_dyeing tbody tr").each(function()
        {
            var txt_challan_no      = $(this).find('input[name="txt_challan_no[]"]').val();
            var txt_exfactory_date  = $(this).find('input[name="txt_exfactory_date[]"]').val();
            var txtInvoiceNo        = $(this).find('input[name="txtInvoiceNo[]"]').val();
            var txt_buyer_name      = $(this).find('input[name="txt_buyer_name[]"]').val();
            var cbo_buyer_id        = $(this).find('input[name="cbo_buyer_id[]"]').val();
            var txt_jobNo           = $(this).find('input[name="txt_jobNo[]"]').val();
            var txt_style_ref       = $(this).find('input[name="txt_style_ref[]"]').val();
            var txt_order_no        = $(this).find('input[name="txt_order_no[]"]').val();
            var txtHiddenDtlsId     = $(this).find('input[name="txtHiddenDtlsId[]"]').val();
            var txt_ex_fac_qty      = $(this).find('input[name="txt_ex_fac_qty[]"]').val()*1;
            var txt_invoice_rate    = $(this).find('input[name="txt_invoice_rate[]"]').val()*1;
            var txt_ex_fac_value    = $(this).find('input[name="txt_ex_fac_value[]"]').val()*1;
            var txt_rate            = $(this).find('input[name="txt_rate[]"]').val()*1;
            var txt_value_qty       = $(this).find('input[name="txt_value_qty[]"]').val()*1;

            j++;
            i++;

            // alert(txt_value_qty+"___"+cbo_buyer_id+"__"+txt_rate);return;
            if(txt_value_qty>0){
                data_all += "&txt_challan_no_" + j + "='" + txt_challan_no + "'&txt_exfactory_date_" + j + "='" + txt_exfactory_date+ "'&txtInvoiceNo_" + j + "='" + txtInvoiceNo + "'&cbo_buyer_id_" + j + "='" + cbo_buyer_id + "'&txt_jobNo_" + j + "='" + txt_jobNo + "'&txt_style_ref_" + j + "='" + txt_style_ref+ "'&txt_order_no_" + j + "='" + txt_order_no+ "'&txt_ex_fac_qty_" + j + "='" + txt_ex_fac_qty+ "'&txt_invoice_rate_" + j + "='" + txt_invoice_rate + "'&txt_ex_fac_value_" + j + "='" + txt_ex_fac_value +  "'&txt_rate_" + j + "='" + txt_rate+  "'&txt_value_qty_" + j + "='" + txt_value_qty+  "'&txtHiddenDtlsId_" + j + "='" + txtHiddenDtlsId +  "'";
            }
            
        });
            //   alert( data_all); return;
        if(check_field==0)
		{
            var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&txt_local_date='+txt_local_date+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_commison_name='+cbo_commison_name+'&txt_remark='+txt_remark+'&hdnupdateid='+hdnupdateid+data_all;
            // alert (data); return;
            freeze_window(operation);
            http.open("POST","requires/local_commission_entry_controller.php",true);
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
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            if(response[0]==0 || response[0]==1) 
			{
                document.getElementById('txt_system_id').value= response[1];
                document.getElementById('hdnupdateid').value = response[2];
                get_php_form_data(response[2], 'populate_mst_data_from_search_popup', 'requires/local_commission_entry_controller');
                show_list_view(response[2], 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/local_commission_entry_controller', ''); 
                set_button_status(1, permission, 'fnc_inspection',1,1)
            }
            else if(response[0]==2) 
			{
                document.getElementById('txt_system_id').value= response[1];
                document.getElementById('hdnupdateid').value = response[2];
                get_php_form_data(response[2], 'populate_mst_data_from_search_popup', 'requires/local_commission_entry_controller');
                show_list_view(response[2], 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/local_commission_entry_controller', ''); 
                set_button_status(0, permission, 'fnc_inspection', 1);
            }
            release_freezing();
        }
    }

    function print_report_local()
	{
		if($('#hdnupdateid').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#hdnupdateid').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "local_commision_print", "requires/local_commission_entry_controller") 
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
        var pageLink = 'requires/local_commission_entry_controller.php?action=system_id_popup&data='+data;
        var title = 'Search Info';
        deliveryPopup = dhtmlmodal.open('Inspection Info', 'iframe', pageLink, title, 'width=800px, height=300px, center=1, resize=0, scrolling=0', '../../');
        deliveryPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var mst_id=this.contentDoc.getElementById('selected_mst_id').value;
            document.getElementById('hdnupdateid').value = mst_id;
            get_php_form_data(mst_id, 'populate_mst_data_from_search_popup', 'requires/local_commission_entry_controller');
            
            show_list_view(mst_id, 'populate_dtls_data_from_search_popup_update', 'delivery_details_container', 'requires/local_commission_entry_controller', ''); 
            set_button_status(1, permission, 'fnc_inspection', 1);
        }
    }

    function fnc_delevery_entry_data(){
        if (!form_validation('cbo_company_name*txt_local_from_date*txt_local_to_date','Company Name*Local From date*local to date')){
            return;
        }
        var cbo_buyer_name=$('#cbo_buyer_name').val();
        var company_name = $('#cbo_company_name').val();
        var cbo_commison_name = $('#cbo_commison_name').val();
        var txt_local_from_date = $('#txt_local_from_date').val();
        var txt_local_to_date = $('#txt_local_to_date').val();
        show_list_view(company_name+'_'+cbo_buyer_name+'_'+cbo_commison_name+'_'+txt_local_from_date+'_'+txt_local_to_date,'populate_dtls_data_from_search_popup','delivery_details_container','requires/local_commission_entry_controller','');
    }

    // function calculateTxtValueQty(sl) {
    //     var txt_ex_fac_value = parseFloat(document.getElementById('txt_ex_fac_value_' + sl).value);
    //     var txt_rate = parseFloat(document.getElementById('txt_rate_' + sl).value);
    //     var txt_value_qty = txt_ex_fac_value * (txt_rate / 100); // Divide by 100 to get the percentage
    //     document.getElementById('txt_value_qty_' + sl).value = txt_value_qty.toFixed(2); // Use toFixed(2) for two decimal places
    // }
    function BuyerLoadFnc(){
        var company_name = $('#cbo_company_name').val();
        var txt_local_from_date = $('#txt_local_from_date').val();
        var txt_local_to_date = $('#txt_local_to_date').val();
        load_drop_down( 'requires/local_commission_entry_controller',company_name+'_'+txt_local_from_date+'_'+txt_local_to_date, 'load_drop_down_buyer', 'buyer_td' )
    }
</script>

</head>
<body onLoad="set_hotkey()">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission); ?>
        <form name="dyeingorderentry_1" id="dyeingorderentry_1" autocomplete="off">
            <fieldset style="width:1050px;">
            <legend>Local Commission Master</legend>
                <table width="1030" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr style="height: 40;">
                        <td colspan="4" align="right"><strong>System ID</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" onDblClick="openmypage_system_id();" placeholder="Double Click" readonly style="width: 150px;" />
                            <input type="hidden" name="hdnupdateid" id="hdnupdateid">
                        </td>
                    </tr>
                    <tr>
                        <td width="130" class="must_entry_caption"  align="right"><strong>Company Name </strong></td>
                        <td width="150">
                            <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/local_commission_entry_controller',this.value, 'load_drop_down_comission_buyer', 'commission_td' );"); 
                         
                            ?>
                        </td>
                      
                        <td width="130" class="must_entry_caption"  align="right"><strong>Ex-Fac Date:</strong></td>                                              
                        <td><input class="datepicker" type="text" style="width:60px" name="txt_local_from_date" id="txt_local_from_date" placeholder="From Date" />&nbsp;<input class="datepicker" type="text" style="width:60px" name="txt_local_to_date" id="txt_local_to_date" placeholder="To Date"/>
                        </td>
                        <td width="130" class="must_entry_caption"  align="right"><strong>Commisson Party:</strong></td>
                        <td id="commission_td">
                            <? echo create_drop_down( "cbo_commison_name", 150, $blank_array, "", 1, "-- Select commison --", $selected, ""); ?>
                        </td>  
                        <td width="130" align="right" class="must_entry_caption"><strong>Local Date: </strong></td>
                        <td>
                            <input type="text" name="txt_local_date"  style="width:140px"  id="txt_local_date" value="<? echo date("d-m-Y")?>" class="datepicker" />
                        </td>                  
                    </tr>
                    <tr >          
                        <td width="130"  align="right"><strong>Remarks:</strong></td>
                        <td width="140">
                            <input class="text_boxes" type="text" name="txt_remark" id="txt_remark" placeholder="Entry" style="width: 140px;" />
                        </td>                   
                        <td width="130" class="must_entry_caption"  align="right"><strong>Buyer:</strong></td>
                        <td id="buyer_td">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                         <input name="cbo_buyer_id" id="cbo_buyer_id" type="hidden" />
                        </td>
                        <td></td>                         
                        <td colspan="4" align="center"> <input type="button"  class="formbutton" value="BuyerLoad" onclick="BuyerLoadFnc()"> </td>
                    </tr>                 
                </table>
                </table>
        </fieldset>
        <fieldset style="width:1250px;">
           <legend>Details</legend>
                <table width="1250px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_local_dyeing">
                  <thead class="form_table_header">
                    <tr>
                        <th colspan="11"></th>
                        <th colspan="2" >Local Commission</th>
                    </tr>
                    <tr>
                        <th rowspan="2" width="30">SL</th>
                        <th rowspan="2" width="100">Challan NO</th>
                        <th rowspan="2" width="100" >Ex-Factory Date</th>
                        <th rowspan="2" width="100" >Invoice No</th>
                        <th rowspan="2" width="100" >Buyer Name</th>
                        <th rowspan="2" width="100" >Job No</th>         
                        <th rowspan="2" width="100" >Style Ref. no</th>
                        <th rowspan="2" width="100" >Order NO</th>
                        <th rowspan="2" width="100" >Ex-Factory Qty</th>
                        <th rowspan="2" width="100" >Invoice Rate/FOB</th>
                        <th rowspan="2" width="100" >Ex-Factory Value</th>
                        <th width="100" >commission pcs]</th>
                        <th width="100" >Amount</th>
                    </tr>
                </thead>
                <tbody id="delivery_details_container">
                    <tr>
                       <td align="center"></td>
                        <td align="center">
                            <input name="txt_challan_no_1" id="txt_challan_no_1" type="text" class="text_boxes"  placeholder="write" style="width:100px"/>
                        </td>

                        <td align="center">
                            <input name="txt_exfactory_date_1" id="txt_exfactory_date_1" type="text" class="text_boxes"  placeholder="Display" readonly style="width:100px" />
                        </td>

                        <td align="center">
                            <input name="txtInvoiceNo_1" id="txtInvoiceNo_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>

                        <td align="center">
                            <input name="txt_buyer_name_1" id="txt_buyer_name_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                            <input name="cbo_buyer_id[]" id="cbo_buyer_id_1" type="hidden" />
                        </td>
                      
                        <td align="center">
                            <input name="txt_jobNo_1" id="txt_jobNo_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_style_ref_1" id="txt_style_ref_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_order_no_1" id="txt_order_no_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_ex_fac_qty_1" id="txt_ex_fac_qty_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_invoice_rate_1" id="txt_invoice_rate_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_ex_fac_value_1" id="txt_ex_fac_value_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_rate_1" id="txt_rate_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_value_qty_1" id="txt_value_qty_1" type="text" class="text_boxes" placeholder="write" style="width:100px" />
                            <input name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_1" type="hidden" value="" />
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
                        <input type="button" id="Print" value="Print" class="formbutton" style="width:100px;" onClick="print_report_local();" >
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>