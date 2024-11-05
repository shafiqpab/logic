<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Scrap Transfer Data
Functionality	:
JS Functions	:
Created by		:	Md. Jakir Hosen
Creation date 	: 	30-07-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-----------------------------------------------------------------------------------------
echo load_html_head_contents("Scrap Material Issue", "../../", 1, 1, '', '', '', '');


//========== user credential  ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$category_credential_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and id in($company_id)";
}

if ($category_credential_id !='') {
    $category_credential_cond = "and CATEGORY_ID in($category_credential_id)";
}

?>
<script>

    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    var permission='<? echo $permission; ?>';

    function openmypage_systemId()
    {
        var company_id = $('#cbo_company_id').val();
        if (form_validation('cbo_company_id','Company')==false)
        {
            return;
        }
        else
        {
            var page_link='requires/scrap_transfer_entry_controller.php?company_id='+company_id+'&action=system_popup';
            var title='Search Transfer Id';

            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=390px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                freeze_window(5);
                var theform=this.contentDoc.forms[0];
                var sys_id=this.contentDoc.getElementById("hidden_mst_id").value;
                var store_id=this.contentDoc.getElementById("hidden_store_id").value;

                if(sys_id !="")
                {
                    get_php_form_data(sys_id+'*'+company_id, "populate_data_from_mst", "requires/scrap_transfer_entry_controller");
                    show_list_view('2*'+sys_id+'*'+company_id+'*'+store_id,'show_dtls_listview','tbl_material_details','requires/scrap_transfer_entry_controller','');
                    set_button_status(1, permission, 'fnc_material_transfer',1);
                }
                release_freezing();
            }
        }
    }

    function open_lot_search()
    {
        if( form_validation('cbo_company_id*cbo_from_store','Company*From Store')==false )
        {
            return;
        }
        var company = $("#cbo_company_id").val();
        var from_store = $("#cbo_from_store").val();
        var selected_lot = $("#txt_lot_no").val();
        var selected_lot_id = $("#selected_lot").val();
        var selected_lot_no = $("#selected_lot_no").val();


        var page_link='requires/scrap_transfer_entry_controller.php?action=lot_popup&company='+company+'&store='+from_store+'&selected_lot='+selected_lot+'&selected_lot_id='+selected_lot_id+'&selected_lot_no='+selected_lot_no;
        var title="Search Lot No.";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var selected_lot_id=this.contentDoc.getElementById("selected_lot_id").value; // product ID
            var lot_from_popup=this.contentDoc.getElementById("txt_selected_lot").value; // product Description
            var lot_sl_no=this.contentDoc.getElementById("selected_lot_no").value; // product Description
            $("#txt_lot_no").val(lot_from_popup);
            $("#selected_lot").val(selected_lot_id);
            $("#selected_lot_no").val(lot_sl_no);
            if(lot_from_popup != ''){
                show_list_view('1*'+selected_lot_id+'*'+company+'*'+from_store,'show_dtls_listview','tbl_material_details','requires/scrap_transfer_entry_controller','');
            }
        }
    }

    function clear_lot_dtls(){
        $("#txt_lot_no").val('');
        $("#selected_lot").val('');
        $("#selected_lot_no").val('');
        $('#tbl_material_details').empty();
        $('#cbo_company_id').prop('disabled',false);
        $('#cbo_from_store').prop('disabled',false);
        $('#cbo_item_category').prop('disabled',false);
        $('#txt_lot_no').prop('disabled',false);
    }

    function fnc_material_transfer(operation)
    {
        if(form_validation('cbo_transfer_criteria*cbo_company_id*cbo_from_store*txt_transfer_date*txt_challan_no*cbo_item_category*txt_lot_no','Transfer Criteria*Company*From Store*Transfer Date*Challan No.*Item Category*Lot No.')==false )
        {
            return;
        }

        var row_num = $('#tbl_material_details .data-row').length;
        if(row_num == 0){
            alert('Product details entry required.');
            return;
        }
        var dtls_data="";
        for(var i=1; i<=row_num; i++)
        {
            if (form_validation('trans_qty_'+i+'*to_store_'+i,'Transfer Qty*To Store')==false)
            {
                return;
            }
            if(operation == 0 || operation == 1) {
                if (parseFloat($("#trans_qty_" + i).val()) > parseFloat($("#current_stock_" + i).val())) {
                    alert("Transfer qty should be less than current stock qty.");
                    $("#trans_qty_" + i).focus();
                    return;
                }
            }
            dtls_data += "*trans_qty_"+i+"*dtls_update_id_"+i+"*to_store_"+i+"*cbo_room_"+i+"*cbo_rack_"+i+"*cbo_shelf_"+i+"*cbo_bin_"+i+"*prod_id_"+i+"*yarn_lot_"+i;
        }

        var data="action=save_update_delete&row_num="+row_num+"&operation="+operation+get_submitted_data_string('update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_from_store*txt_transfer_date*txt_challan_no*cbo_item_category*txt_lot_no*txt_remarks'+dtls_data,"../../");
        freeze_window(operation);
        http.open("POST","requires/scrap_transfer_entry_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange=fnc_material_transfer_response;

    }

    function fnc_material_transfer_response()
    {
        if(http.readyState == 4)
        {
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);

            document.getElementById('update_id').value = response[2];
            document.getElementById('txt_system_id').value = response[1];

            if((response[0]==0 || response[0]==1)){
                show_list_view('2*'+response[2]+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_from_store').val(),'show_dtls_listview','tbl_material_details','requires/scrap_transfer_entry_controller','');
                set_button_status(1, permission, 'fnc_material_transfer',1);
            }
            if (response[0]==2) {
                reset_form('materialTransfer_1','div_details_list_view','','','');
                clear_lot_dtls();
                set_button_status(0, permission, 'fnc_material_transfer',1,0);
            }
            release_freezing();
        }
    }

    function trans_qty_sum(){
        var total = parseFloat(0);
        $("input[name='trans_qty[]']").each(function (index){
            total += $(this).val() != '' ? parseFloat($(this).val()) : parseFloat(0);
        });
        $('#trans_total_qty').val(total);
    }


    function check_stock(row){
       var cur_stock = parseFloat($('#current_stock_'+row).val());
       var trans_qty = parseFloat($('#trans_qty_'+row).val());
       if(trans_qty > cur_stock){
           alert('Transfer qty. should be less than current stock qty.');
           $('#trans_qty_'+row).val(cur_stock);
       }
        trans_qty_sum();
    }

    function room_rack_shelf_bin_reset(label = 4, row){
        if(label == 4 && row != ""){
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_room_empty','room_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_rack_empty','rack_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_shelf_empty','shelf_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_bin_empty','bin_td_'+row);
        }else if(label == 3 && row != ""){
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_rack_empty','rack_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_shelf_empty','shelf_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_bin_empty','bin_td_'+row);
        }else if(label == 2 && row != ""){
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_shelf_empty','shelf_td_'+row);
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_bin_empty','bin_td_'+row);
        }else if(label == 1 && row != ""){
            load_drop_down('requires/scrap_transfer_entry_controller', row, 'load_drop_bin_empty','bin_td_'+row);
        }
    }

</script>
<body onLoad="set_hotkey()">
<div  align="center" style="width:100%;">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="materialTransfer_1" id="materialTransfer_1" autocomplete="off">
        <div style="width:1200px;">
            <fieldset style="width:1200px;">
                <legend>Scrap Transfer Entry</legend>
                <br>
                <fieldset style="width:850px;">
                    <table width="850" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="center">
                        <tr>
                            <td colspan="3" align="right"><strong>Transfer System ID</strong></td>
                            <td colspan="3" align="left">
                                <input type="hidden" name="update_id" id="update_id" class="text_boxes" />
                                <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Transfer Criteria</td>
                            <td>
                                <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", '0',"-- Select --",'0',"",'','2');
                                ?>
                            </td>
                            <td class="must_entry_caption">Company</td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "clear_lot_dtls();load_drop_down('requires/scrap_transfer_entry_controller', this.value, 'load_drop_down_store','store_td');" );
                                ?>
                            </td>
                            <td  class="must_entry_caption">From Store</td>
                            <td id="store_td">
                                <?
                                echo create_drop_down( "cbo_from_store", 160, "$blank_array","", 1, "--Select Store--", 0);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Transfer Date</td>
                            <td>
                                <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date("d-m-Y");?>"/>
                            </td>
                            <td  class="must_entry_caption">Challan No.</td>
                            <td>
                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" placeholder="Write" />
                            </td>
                            <td class="must_entry_caption">Item Category</td>
                            <td>
                                <?
                                echo create_drop_down("cbo_item_category", 160,$item_category_type_arr,"", 0,"-- Select --",'0',"",'','1');
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <td class="must_entry_caption">Lot No.</td>
                            <td>
                                <input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:148px;" readonly placeholder="Double Click to Browse" ondblclick="open_lot_search()" />
                                <input type="hidden" name="selected_lot" id="selected_lot" class="text_boxes"/>
                                <input type="hidden" name="selected_lot_no" id="selected_lot_no" class="text_boxes"/>
                            </td>
                            <td>Remarks</td>
                            <td colspan="3">
                                <input type="text" name="txt_remarks" id="txt_remarks" placeholder="Write" class="text_boxes" style="width:450px;"/>
                            </td>

                        </tr>
                    </table>
                </fieldset>
                <br>
                <fieldset style="width:1180px">
                    <legend>Product Details Entry</legend>
                    <table width="1180" cellspacing="1" cellpadding="1" border="1" rules="all" class="rpt_table">
                        <thead>
                            <tr>
                                <th width="30">SL No.</th>
                                <th width="80">Product ID</th>
                                <th width="160">Product Name</th>
                                <th width="80">Lot</th>
                                <th width="120">Supplier</th>
                                <th width="100">Current Stock</th>
                                <th width="90" class="must_entry_caption">Transfer Qty.</th>
                                <th width="110" class="must_entry_caption">To Store</th>
                                <th width="100" >Room</th>
                                <th width="100">Rack</th>
                                <th width="90">Self</th>
                                <th width="90">Bin</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_material_details">

                        </tbody>
                        <tr>
                            <td align="center" colspan="13" class="button_container" width="100%">
                                <? //Report Setting > inventory\purchase_requisition.php
                                echo load_submit_buttons($permission, "fnc_material_transfer", 0,0,"reset_form('materialTransfer_1','div_details_list_view','','','');clear_lot_dtls();",1);
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </fieldset>
            <div style="width:1000px;" id="div_details_list_view"></div>

        </div>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>