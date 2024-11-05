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
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Wash Order Entry Info', '../', 1, 1, $unicode, 1, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
    var permission='<?php echo $permission; ?>';

    function get_company_config(company_id) {
        load_drop_down('requires/yd_inspection_controller', company_id, 'load_drop_down_location', 'location_td');
    }

    function openSalesOrderPopup() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name*cbo_within_group', 'Company*Within Group')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;
        // console.log(data);
        var pageLink = 'requires/yd_inspection_controller.php?action=sales_no_popup&data='+data;
        var title = 'Search Sales No';
        salesPopup = dhtmlmodal.open('Sales No', 'iframe', pageLink, title, 'width=940px, height=400px, center=1, resize=0, scrolling=0', '../');
        salesPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var prod_id_mst=this.contentDoc.getElementById('selected_prod_id').value;
            // var jobDtlsIds = document.getElementById('hdnJobDtlsIds').value;
            //get_php_form_data('1**'+prod_id_mst, 'populate_mst_data_from_search_popup', 'requires/yd_inspection_controller');
            // alert (prod_id_mst);
            // var data="'1**"+prod_id_mst+"'";
            show_list_view('1**'+prod_id_mst, 'populate_dtls_data_from_search_popup', 'delivery_details_container', 'requires/yd_inspection_controller', '');
        }
    }

    function salesPopupCloseHandler() {
        // freeze_window(1);
        var theform=this.contentDoc.forms[0];
        var job_id_mst=this.contentDoc.getElementById('selected_prod_id').value;
        get_php_form_data(job_id_mst, 'populate_data_from_search_popup', 'requires/yd_inspection_controller');
        // release_freezing();
    }

    function fnc_inspection(operation) 
	{
        if ( !form_validation('cbo_company_name*txt_inspection_date', 'Company Name*Inspection date') ) {
            return;
        }

       // freeze_window(operation);

        var dataStr = '';
        var cbo_company_name    = $('#cbo_company_name').val();
        var cbo_location_name   = $('#cbo_location_name').val();
        var cbo_within_group    = $('#cbo_within_group').val();
        var cbo_party_name      = $('#cbo_party_name').val();
        var cbo_party_location  = $('#cbo_party_location').val();
        var txt_inspection_date = $('#txt_inspection_date').val();
        var txt_remarks         = $('#txt_remarks').val();
        var hdn_update_id       = $('#hdn_update_id').val();
        var txt_system_id       = $('#txt_system_id').val();
        var j=0; var check_field=0; data_all=""; var i=0;

        $("#tbl_dtls_yarn_dyeing tbody tr").each(function()
        {
            var txtSalesOrder       = $(this).find('input[name="txtSalesOrder[]"]').val();
            var txtstyle            = $(this).find('input[name="txtstyle[]"]').val();
            var txtLot              = $(this).find('input[name="txtLot[]"]').val();
            var txtcount            = $(this).find('input[name="txtcount[]"]').val();
            var txtYarnType         = $(this).find('input[name="txtYarnType[]"]').val();
            var txtComposition      = $(this).find('input[name="txtComposition[]"]').val();
            var txtYDcolor          = $(this).find('input[name="txtYDcolor[]"]').val();
            var txtTension          = $(this).find('input[name="txtTension[]"]').val();
            var txtWindConeQty      = $(this).find('input[name="txtWindConeQty[]"]').val();
            var txtProductQty       = $(this).find('input[name="txtProductQty[]"]').val();
            var txtInspectionSts    = $(this).find('input[name="txtInspectionSts[]"]').val();
            var txtCause            = $(this).find('input[name="txtCause[]"]').val();
            var txtBatchNumber      = $(this).find('input[name="txtBatchNumber[]"]').val();
            var txtBatchColour      = $(this).find('input[name="txtBatchColour[]"]').val();

            var txtUpDtlsId         = $(this).find('input[name="txtUpDtlsId[]"]').val();
            var txtJobDtlsId        = $(this).find('input[name="txtJobDtlsId[]"]').val();
            var txtSalesOrdId       = $(this).find('input[name="txtSalesOrdId[]"]').val();
            var txtColorId          = $(this).find('input[name="txtColorId[]"]').val();
            //txt_total_amount  += $(this).find('input[name="amount[]"]').val()*1;
            //alert(cboSection);
            j++;
            i++;
            data_all += "&txtSalesOrder_" + j + "='" + txtSalesOrder + "'&txtstyle_" + j + "='" + txtstyle+ "'&txtLot_" + j + "='" + txtLot+ "'&txtcount_" + j + "='" + txtcount + "'&txtYarnType_" + j + "='" + txtYarnType + "'&txtComposition_" + j + "='" + txtComposition + "'&txtYDcolor_" + j + "='" + txtYDcolor  + "'&txtTension_" + j + "='" + txtTension + "'&txtWindConeQty_" + j + "='" + txtWindConeQty + "'&txtProductQty_" + j + "='" + txtProductQty + "'&txtInspectionSts_" + j + "='" + txtInspectionSts + "'&txtCause_" + j + "='" + txtCause + "'&txtUpDtlsId_" + j + "='" + txtUpDtlsId + "'&txtJobDtlsId_" + j + "='" + txtJobDtlsId + "'&txtSalesOrdId_" + j + "='" + txtSalesOrdId + "'&txtColorId_" + j + "='" + txtColorId+ "'&txtBatchNumber_" + j + "='" + txtBatchNumber+ "'&txtBatchColour_" + j + "='" + txtBatchColour + "'";
        });

        // alert( data_all);


        if(check_field==0)
		{
            var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_inspection_date='+txt_inspection_date+'&txt_remarks='+txt_remarks+'&txt_system_id='+txt_system_id+'&hdn_update_id='+hdn_update_id+data_all;
            //alert (data); //return;
            freeze_window(operation);
            http.open("POST","requires/yd_inspection_controller.php",true);
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
                // console.log(response[0]);
                document.getElementById('txt_system_id').value= response[1];
                document.getElementById('hdn_update_id').value = response[2];
                // var jobDtlsIds = document.getElementById('hdnJobDtlsIds').value;
				
				//alert(1); 

                show_list_view('2**'+response[2], 'populate_dtls_data_from_search_popup', 'delivery_details_container', 'requires/yd_inspection_controller', ''); 
                set_button_status(1, permission, 'fnc_inspection', 1);
            }
			 ///alert(2); 

            release_freezing();
        }
    }

    function openmypage_system_id() {
        // console.log(rowId);
        if(!form_validation('cbo_company_name', 'Company')) {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;
        // console.log(data);
        var pageLink = 'requires/yd_inspection_controller.php?action=system_id_popup&data='+data;
        var title = 'Search Info';
        deliveryPopup = dhtmlmodal.open('Inspection Info', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../');
        deliveryPopup.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var mst_id=this.contentDoc.getElementById('selected_mst_id').value;
            document.getElementById('hdn_update_id').value = mst_id;
            get_php_form_data('2**'+mst_id, 'populate_mst_data_from_search_popup', 'requires/yd_inspection_controller');
            show_list_view('2**'+mst_id, 'populate_dtls_data_from_search_popup', 'delivery_details_container', 'requires/yd_inspection_controller', '');
            set_button_status(1, permission, 'fnc_inspection', 1);
        }
    }

    

    function fnc_load_party(type, within_group) {
        if ( form_validation('cbo_company_name','Company')==false ) {
            $('#cbo_within_group').val(1);
            return;
        }
        var company = $('#cbo_company_name').val();
        var party_name = $('#cbo_party_name').val();
        var location_name = $('#cbo_location_name').val();

        if(within_group==1 && type==1) 
		{
            load_drop_down( 'requires/yd_inspection_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
        }
        else if(within_group==2 && type==1) 
		{
            load_drop_down( 'requires/yd_inspection_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
        }
        else if(within_group==1 && type==2) 
		{
            load_drop_down( 'requires/yd_inspection_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' );
        }
    }

</script>
</head>

<body onLoad="set_hotkey()">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="dyeingorderentry_1" id="dyeingorderentry_1" autocomplete="off">
            <fieldset style="width:850px;">
            <legend>YD Inspection</legend>
                <table width="830" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" onDblClick="openmypage_system_id();" placeholder="Double Click" readonly style="width: 150px;" />
                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_inspection_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value); " ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>

                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                         <td class="must_entry_caption">Inspection date</td>
                        <td><input type="text" name="txt_inspection_date"  style="width:140px"  id="txt_inspection_date" value="<? echo date("d-m-Y")?>" class="datepicker" /></td>
                    </tr>
                    <tr>
                      <td >Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px"  placeholder="Entry" />
                        </td>
                    </tr>
                </table>
                </table>
        </fieldset>
        <fieldset style="width:1250px;">
           <legend>YD Inspection Details</legend>
                <table width="1250px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_yarn_dyeing">
                  <thead class="form_table_header">
                    <th width="130" id="sales_order">Job No/Sales order no</th>
                    <th width="100" id="">Batch Number</th>
                    <th width="100" id="">Batch Color</th>
                    <th width="100" id="style_td">Style</th>
                    <th width="80" id="lot_td">Lot</th>
                    <th width="80"  id="count_td">Count</th>
                    <th width="80" >Yarn Type</th>
                    <th width="120" id="composition_td">Composition</th>
                    <th width="80" >Y/D Color</th>
                    <th width="80" >Tension</th>
                    <th width="80" >Winding Cone Qty</th>
                    <th width="80" >Production QTY</th>
                    <th width="80" >Inspection Status</th>
                    <th>Cause</th>
                </thead>
                <tbody id="delivery_details_container">
                    <tr>
                        <td>
                            <input name="txtSalesOrder_1" id="txtSalesOrder_1" type="text" class="text_boxes" placeholder="Double Click" onDblClick="openSalesOrderPopup();" readonly style="width:117px" />
                        </td>
                        <td>
                            <input name="txtBatchNumber_1" id="txtBatchNumber" type="text" class="text_boxes" placeholder="Display" readonly style="width:87px"/>
                        </td>
                        <td>
                            <input name="txtBatchColour_1" id="txtBatchColour_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:87px"/>
                        </td>
                        <td>
                            <input name="txtstyle_1" id="txtstyle_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:87px"/>
                        </td>
                        <td>
                            <input name="txtLot_1" id="txtLot_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:67px"/>
                        </td>

                        <td>
                            <input name="txtcount_1" id="txtcount_1" type="text" class="text_boxes" placeholder="Display" readonly style="width:67px"/>
                        </td>

                        <td>
                            <input name="txtYarnType_1" id="txtYarnType_1" type="text" class="text_boxes" placeholder="Display" style="width:67px"readonly />
                        </td>

                        <td>
                            <input name="txtComposition_1" id="txtComposition_1" type="text" class="text_boxes" readonly placeholder="Display" style="width:107px"/>
                        </td>

                        <td>
                            <input name="txtYDcolor_1" id="txtYDcolor_1" type="text" class="text_boxes" readonly placeholder="Display" style="width:67px" />
                        </td>
                        <td>
                            <input name="txtTension_1" id="txtTension_1" class="text_boxes" type="text"  placeholder="Display" readonly style="width:67px" />
                        </td>
                        <td>
                            <input name="txtWindConeQty_1" id="txtWindConeQty_1" type="text"  class="text_boxes_numeric" onClick="" placeholder="Write" style="width:67px" />
                        </td>
                        <td>
                            <input name="txtProductQty_1" id="txtProductQty_1" type="text"  class="text_boxes_numeric" placeholder="Write" style="width:67px" />
                        </td>
                        <td>
                            <input name="txtInspectionSts_1" id="txtInspectionSts_1" type="text"  class="text_boxes_numeric" style="width:67px" />
                        </td>
                        <td>
                            <input name="txtCause_1" id="txtCause_1" type="text"  class="text_boxes_numeric" style="width:67px" />
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
            </table>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>