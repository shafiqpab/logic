<?php
/*--- ----------------------------------------- Comments
Purpose         :   Soft Conning Delivery entry
Functionality   :   
JS Functions    :
Created by      :   Shakil Ahmed
Creation date   :   10-02-2020
Updated by      :   Sapayth Hossain     
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
echo load_html_head_contents('Wash Order Entry Info', '../../', 1, 1, $unicode, 1, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    function get_company_config(company_id) {
        load_drop_down('requires/yd_soft_conning_delivery_entry_controller', company_id, 'load_drop_down_location', 'location_td');
    }

    function openSalesOrderPopup() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name', 'Company')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;
        // console.log(data);
        var pageLink = 'requires/yd_soft_conning_delivery_entry_controller.php?action=sales_no_popup&data='+data;
        var title = 'Search Sales No';
        salesPopup = dhtmlmodal.open('Sales No', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../');
        salesPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var prod_id_mst=this.contentDoc.getElementById('selected_prod_id').value;
            // var jobDtlsIds = document.getElementById('hdnJobDtlsIds').value;
            get_php_form_data('1**'+prod_id_mst, 'populate_mst_data_from_search_popup', 'requires/yd_soft_conning_delivery_entry_controller');
            show_list_view('1**'+prod_id_mst, 'populate_dtls_data_from_search_popup', 'delivery-details', 'requires/yd_soft_conning_delivery_entry_controller', '');
        }
    }

    function salesPopupCloseHandler() {
        // freeze_window(1);
        var theform=this.contentDoc.forms[0];
        var job_id_mst=this.contentDoc.getElementById('selected_prod_id').value;
        get_php_form_data(job_id_mst, 'populate_data_from_search_popup', 'requires/yd_soft_conning_delivery_entry_controller');
        // release_freezing();
    }

    function saveUpdateDelete(operation) {
        if ( !form_validation('cbo_company_name*txt_delivery_date*cbo_party_name', 'Company Name*Delivery Date*Party Name') ) {
            return;
        }

        freeze_window(operation);

        var dataStr = '';
        var totalRow = document.getElementById('delivery-details-rows').children.length;
        // console.log(totalRow);        
        
        for(var i=1; i<=totalRow; i++) {
            var qty=$('#txtDeliveryQty_'+i).val();
            if(!qty) {
                alert('Please fill Delivery Quantity'); 
                return;
            }
            dataStr+=get_submitted_data_string('txtDeliveryQty_'+i+'*hdnDtlsId_'+i+'*hdnProd_dtls_id_'+i+'*hdnSalesOrderId_'+i+'*txtSalesOrder_'+i+'*hdnProductId_'+i+'*hdnDeliveryDtlsId_'+i, '../../');
        }

        dataStr+=get_submitted_data_string('cbo_company_name*cbo_location_name*txt_delivery_date*cbo_party_name*txt_remarks*txt_style*txtProductionQty*txt_balance*hdnOrderId*hdnOrderNo*hdnBookingWithoutOrder*hdnBookingType*hdn_update_id*txt_delivery_id', '../../');
                
        var data='action=save_update_delete&operation='+operation+'&total_row='+totalRow+dataStr;        
        http.open('POST', 'requires/yd_soft_conning_delivery_entry_controller.php', true);
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.send(data);
        http.onreadystatechange = saveUpdateDeleteResponseHandler;
    }

    function fnResetForm() {
        console.log('reset');
    }

    function saveUpdateDeleteResponseHandler() {
        if(http.readyState == 4) {
            //alert(http.responseText);//return;
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            //$('#cbo_uom').val(12);
            if(response[0]==0 || response[0]==1) {
                // console.log(response[0]);
                document.getElementById('txt_delivery_id').value= response[1];
                document.getElementById('hdn_update_id').value = response[2];
                // var jobDtlsIds = document.getElementById('hdnJobDtlsIds').value;

                show_list_view('2**'+response[2], 'populate_dtls_data_from_search_popup', 'delivery-details', 'requires/yd_soft_conning_delivery_entry_controller', '');

                set_button_status(1, permission, 'saveUpdateDelete', 1);
            }

            release_freezing();
        }
    }

    function openmypage_delivery() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name', 'Company')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;
        // console.log(data);
        var pageLink = 'requires/yd_soft_conning_delivery_entry_controller.php?action=delivery_id_popup&data='+data;
        var title = 'Search Delivery';
        deliveryPopup = dhtmlmodal.open('Delivery ID', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../');
        deliveryPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var delivery_id_mst=this.contentDoc.getElementById('selected_delivery_id').value;
            document.getElementById('hdn_update_id').value = delivery_id_mst;
            get_php_form_data('2**'+delivery_id_mst, 'populate_mst_data_from_search_popup', 'requires/yd_soft_conning_delivery_entry_controller');
            show_list_view('2**'+delivery_id_mst, 'populate_dtls_data_from_search_popup', 'delivery-details', 'requires/yd_soft_conning_delivery_entry_controller', '');
        }
    }


	function checkPackgQty222(value,i)
	{
		var placeholder_value = $("#txtDeliveryQty_"+i).attr('placeholder')*1;
 		var pre_delv_qty = $("#txtCurrDelv_"+i).attr('pre_delv_qty')*1;
		var delv_qty = $("#txtCurrDelv_"+i).attr('delv_qty')*1;
		var order_qty_with_wastage = $("#txtCurrDelv_"+i).attr('order_qty_with_wastage')*1;
		
		var variable_status = $("#txtCurrDelv_"+i).attr('variable_status')*1;
		var productQty = $("#txtCurrDelv_"+i).attr('productQty')*1;
		var orderQty = $("#txtCurrDelv_"+i).attr('orderQty')*1;
		
		var current_delv_qty=value*1;
		var total_delv_qty=(value*1)+pre_delv_qty;
		//alert(productQty); //alert(order_qty_with_wastage);
		
		if(variable_status==3) // qc qty 
		{
		
			if((value*1)+pre_delv_qty<order_qty_with_wastage)
			{
				//alert(value); alert(delv_qty);
				if(((value*1)+pre_delv_qty)>delv_qty)
				{
					//alert("Qnty Excceded");
					/*var confirm_value=confirm("Delivery qty Excceded by QC qty. Press cancel to proceed otherwise press ok.");
					if(confirm_value!=0)
					{
						$("#txtCurrDelv_"+i).val('');
					}
					return;*/
					
					 alert("Delivery qty Excceded by QC qty Not Allow.");
				     $("#txtCurrDelv_"+i).val('');
				     return;
				}
			}
			else
			{
				//validation off for mettro 
				/*alert('Delivery Qty can not Exceed Order Quantity with Wastage');
				$("#txtCurrDelv_"+i).val('');
				return;*/
			}
		
		}
		
		if(variable_status==2) // production 
		{
			if(total_delv_qty>productQty)
			{ 
				alert('Delivery Qty can not Exceed Production Quantity');
				$("#txtCurrDelv_"+i).val('');
				return;
			}
		}
		
		if(variable_status==1)// order qty 
		{
			if(total_delv_qty>orderQty)
			{ 
				alert('Delivery Qty can not Exceed Order Quantity');
				$("#txtCurrDelv_"+i).val('');
				return;
			}
		}
		
	}
     

</script>
</head>
<body>
    <div style="width:100%;" align="center">
        <?php echo load_freeze_divs ('../../', $permission); ?>
        <form name="deliveryentry_1" id="deliveryentry_1" autocomplete="off"> 
            <fieldset style="width:70%;">
                <legend>Soft Coning Production Delivery entry</legend>
                <table cellspacing="2" cellpadding="0" border="0" id="tbl_master" width="100%;">
                    <tr>
                        <td colspan="3" align="right"><strong>Delivery ID</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_delivery_id" id="txt_delivery_id" onDblClick="openmypage_delivery();" placeholder="Double Click" readonly style="width: 150px;" />
                            <input type="hidden" name="hdnOrderId" id="hdnOrderId">
                            <input type="hidden" name="hdnOrderNo" id="hdnOrderNo">
                            <input type="hidden" name="hdnJobDtlsIds" id="hdnJobDtlsIds">
                            <input type="hidden" name="hdnBookingWithoutOrder" id="hdnBookingWithoutOrder">
                            <input type="hidden" name="hdnBookingType" id="hdnBookingType">
                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" align="right">Company Name</td>
                        <td>
                            <?php echo create_drop_down('cbo_company_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, 'get_company_config(this.value);'); ?>
                        </td>
                        <td align="right">Location Name</td>
                        <td id="location_td">
                            <?php
                                echo create_drop_down('cbo_location_name', 163, $blank_array, '', 1, '-- Select Location --', $selected, '');
                            ?>
                        </td>

                        <td class="must_entry_caption" align="right">Delivery Date</td>
                        <td>
                            <input type="text" name="txt_delivery_date" id="txt_delivery_date" style="width: 150px;" value="<?php echo date("d-m-Y")?>" class="datepicker" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Party</td>
                        <td id="buyer_td">
                            <?php echo create_drop_down('cbo_party_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $selected, ''); ?>
                        </td>
                        <td align="right">Remarks</td>
                        <td colspan="3">
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width: 95%;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Style</td>
                        <td>
                            <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width: 150px;" />
                        </td>

                        <td align="right"><strong>Production Qty</strong></td>
                        <td>
                            <input name="txtProductionQty" id="txtProductionQty" type="text"  class="text_boxes_numeric" style="width: 150px;" readonly />
                        </td>
                        <td align="right">Balance</td>
                        <td>
                            <input type="text" name="txt_balance" id="txt_balance" class="text_boxes_numeric" readonly style="width: 150px;" />
                        </td>
                    </tr> 
                </table>
            </fieldset>
        </form>                     
    </div>
    <div id="delivery-details" style="margin-top: 20px;" align="center">
        <fieldset style="width: 90%;">
            <legend>Soft Coning Production Delivery entry Details</legend>
            <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_delivery" style="table-layout: fixed; width: 100%;">
                <thead class="form_table_header">
                    <th id="sales_order">Sales order no / Job No</th>
                    <th id="lot_td">Lot</th>
                    <th id="count_td">Count</th>
                    <th>Yarn Type</th>
                    <th id="composition_td">Yarn Composition</th>
                    <th>Y/D Color</th>
                    <th>Bobbin Type</th>
                    <th id="order_uom_td">Winding Package Qty(PCS)</th>
                    <th>Delivery Qty</th>
                </thead>
                <tbody id="delivery_details_container">
                    <tr>
                        <td>
                            <input name="txtSalesOrder_1" id="txtSalesOrder_1" type="text" class="text_boxes" placeholder="Double Click" onDblClick="openSalesOrderPopup();" readonly style="width: 90%;" />
                        </td>
                        <td>
                            <input name="txtLot_1" id="txtLot_1" type="text" class="text_boxes" placeholder="Display" readonly style="width: 90%;"/>
                        </td>

                        <td>
                            <input name="txtcount_1" id="txtcount_1" type="text" class="text_boxes" placeholder="Display" readonly style="width: 90%;"/>
                        </td>

                        <td>
                            <input name="txtYarnType_1" id="txtYarnType_1" type="text" class="text_boxes" placeholder="Display" style="width: 90%;"readonly />
                        </td>

                        <td>
                            <input name="txtComposition_1" id="txtComposition_1" type="text" class="text_boxes" readonly placeholder="Display" style="width: 90%;"/>
                        </td>

                        <td>
                            <input name="txtYDcolor_1" id="txtYDcolor_1" type="text" class="text_boxes" readonly placeholder="Display" style="width: 90%;" />
                        </td>
                        <td>
                            <input name="txtBobbinType_1" id="txtBobbinType_1" class="text_boxes" type="text"  placeholder="Plastic" readonly style="width: 90%;" />
                        </td>
                        <td>
                            <input name="txtPackageQty_1" id="txtPackageQty_1" type="text"  class="text_boxes_numeric" onClick="" placeholder="Browse" readonly style="width: 90%;" />
                        </td>
                        <td>
                            <input name="txtDeliveryQty_1" id="txtDeliveryQty_1" type="text"  class="text_boxes_numeric" style="width: 90%;" />
                        </td>
                    </tr>                     
                </tbody>
            </table>
            <table width="80%" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" colspan="11" class="button_container">
                        <?php echo load_submit_buttons($permission, 'saveUpdateDelete', 0, 0, 'fnResetForm();', 1); ?>
                    </td>
                </tr>   
            </table>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>