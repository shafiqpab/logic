<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Trasport Bill Entry
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	29-3-2021
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
//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Transport Bill Entry", "../../",1,1, $unicode,'','');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_sys_no()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/transport_bill_entry_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search Transport Bill Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                freeze_window();
                get_php_form_data( theemail, 'populate_data_from_search_popup','requires/transport_bill_entry_controller');
                var type = document.getElementById('cbo_type_name').value;
                $('#cbo_company_name').attr('disabled',true);
                $('#cbo_type_name').attr('disabled',true);
                show_list_view(theemail+"**"+type,'populate_dtls_data_from_search_popup','bill_tbl','requires/transport_bill_entry_controller','');
                calculate_total_amount();
				set_button_status(1, permission, 'fnc_transport_bill_entry',1);
            }
            release_freezing();
        }
    }
    
    function numberPopup(id)
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_trans_company_name = $("#cbo_transport_company").val();
        var type= $("#cbo_type_name").val();
        if(type==1){
            var page_link = 'requires/transport_bill_entry_controller.php?action=challan_popup&cbo_company_name='+cbo_company_name+'&cbo_trans_company_name='+cbo_trans_company_name;
            var title = 'Search Challan Details';
        }else{
            var page_link = 'requires/transport_bill_entry_controller.php?action=btb_lc_popup&cbo_company_name='+cbo_company_name;
            var title = 'Search BTB LC Details';
        }
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                var data=theemail+"**"+id;
                if(type==1){
                    get_php_form_data( data, 'load_challan_info','requires/transport_bill_entry_controller');
                    calculate_total_amount();
                }else{
                    get_php_form_data( data, 'load_btb_lc_info','requires/transport_bill_entry_controller');
                }
                // set_all_onclick();
            }
            release_freezing();
        }
    }

    function fn_inc_decr_row(rowid,type)
    {
        if(type=="increase")
        {
            var row = $("#tbl_details tbody tr:last").attr('id');
            row = row*1+1;
            var type_name = $("#cbo_type_name").val();
            var responseHtml = return_ajax_request_value(row+'**'+type_name, 'append_load_details_container', 'requires/transport_bill_entry_controller');
            $("#tbl_details tbody").append(responseHtml);
        }
        else if(type=="decrease")
        {
            var row = $("#tbl_details tbody tr").length;
            if(rowid*1!="" && row*1>1)
            {
                $("#tbl_details tbody tr#"+rowid).remove();
            }
            else
                return;
        }
        calculate_total_amount();
    }

    function calculate_row_amount(id)
    {
        var base = document.getElementById('cbo_type_name').value;

        var vehicle_rent=$('#txt_vehicle_rent_'+id).val();
        var loading_unloading=$('#txt_loading_unloading_'+id).val();
        var local_vechicle=$('#txt_local_vechicle_'+id).val();
        var other=$('#txt_other_'+id).val();
        if(base==1){
            var point_unloading=$('#txt_point_unloading_'+id).val();
            var txt_demurrage=$('#txt_demurrage_'+id).val();

            var amount =  vehicle_rent*1+point_unloading*1+loading_unloading*1+local_vechicle*1+txt_demurrage*1+other*1;
        }
        if(base==2){
            var amount =  vehicle_rent*1+loading_unloading*1+local_vechicle*1+other*1;
        }

        if(amount>0)
        {
            $('#txt_amount_'+id).val(amount.toFixed(2));
        }
        calculate_total_payable(id);
    }

    function calculate_total_amount()
    {
        var base = document.getElementById('cbo_type_name').value;
        var ddd={ dec_type:2, comma:0, currency:''}
        math_operation_name( "txt_rent_amount", "txt_vehicle_rent", "+", "tbl_details" , ddd );
        math_operation_name( "txt_loading_amount", "txt_loading_unloading", "+", "tbl_details" , ddd );
        math_operation_name( "txt_local_amount", "txt_local_vechicle", "+", "tbl_details" , ddd );
        math_operation_name( "txt_other_amount", "txt_other", "+", "tbl_details" , ddd );
        math_operation_name( "txt_total_amount", "txt_amount", "+", "tbl_details" , ddd );
        math_operation_name( "txt_deduction_amount", "txt_deduction", "+", "tbl_details" , ddd );
        math_operation_name( "txt_payable_amount", "txt_payable", "+", "tbl_details" , ddd );
        if(base==1){
            math_operation_name( "txt_demurrage_amount", "txt_demurrage", "+", "tbl_details" , ddd );
            math_operation_name( "txt_point_amount", "txt_point_unloading", "+", "tbl_details" , ddd );
            math_operation_name( "txt_crtn_qnt", "txt_carton_qty", "+", "tbl_details" , ddd );
        }
    }

    function fnc_transport_bill_entry(operation) 
    {
        if (form_validation('cbo_company_name*cbo_transport_company*cbo_type_name*txt_bill_date','Company Name*Transport. Company*Type*Bill Date')==false)
        {
            return;
        }
        var type = document.getElementById('cbo_type_name').value;
        if(type==1)
        {
            if (form_validation('cbo_location_name*cbo_shipment_id','Location*SHIP MODE')==false)
            {
                return;
            }
        }else{
            if (form_validation('txt_depo','DEPO')==false)
            {
                return;
            }
        }

        var i=1; var dataString='';var necessity_setup_chk=0;
        if(type==1){
            $("#tbl_details").find('tbody tr').each(function()
            {
                var txt_challan_no=$(this).find('input[name="txt_challan_no[]"]').val();
                var txt_challan_no_id=$(this).find('input[name="txt_challan_no_id[]"]').val();
                var txt_invoice_id=$(this).find('input[name="txt_invoice_id[]"]').val();
                var cbo_buyer=$(this).find('select[name="cbo_buyer[]"]').val();
                var txt_cbm=$(this).find('input[name="txt_cbm[]"]').val();
                var txt_vehicle_no=$(this).find('input[name="txt_vehicle_no[]"]').val();
                var txt_no_vehicle=$(this).find('input[name="txt_no_vehicle[]"]').val();
                var txt_vehicle_rent=$(this).find('input[name="txt_vehicle_rent[]"]').val();
                var txt_point_unloading=$(this).find('input[name="txt_point_unloading[]"]').val();
                var txt_loading_unloading=$(this).find('input[name="txt_loading_unloading[]"]').val();
                var txt_local_vechicle=$(this).find('input[name="txt_local_vechicle[]"]').val();
                var txt_demurrage=$(this).find('input[name="txt_demurrage[]"]').val();
                var txt_other=$(this).find('input[name="txt_other[]"]').val();
                var txt_amount=$(this).find('input[name="txt_amount[]"]').val();
                var txt_deduction=$(this).find('input[name="txt_deduction[]"]').val();
                var txt_payable=$(this).find('input[name="txt_payable[]"]').val();
                if (txt_amount=='' || txt_amount <=0){
                    alert("Total Amount can not Zero or Blank ");
                    necessity_setup_chk=1;
                    return;
                }
                dataString+='&txt_challan_no_' + i + '=' + txt_challan_no + '&txt_challan_no_id_' + i + '=' + txt_challan_no_id + '&txt_invoice_id_' + i + '=' + txt_invoice_id + '&cbo_buyer_' + i + '=' + cbo_buyer + '&txt_cbm_' + i + '=' + txt_cbm + '&txt_vehicle_no_' + i + '=' + txt_vehicle_no + '&txt_no_vehicle_' + i + '=' + txt_no_vehicle + '&txt_vehicle_rent_' + i + '=' + txt_vehicle_rent + '&txt_point_unloading_' + i + '=' + txt_point_unloading + '&txt_loading_unloading_' + i + '=' + txt_loading_unloading + '&txt_local_vechicle_' + i + '=' + txt_local_vechicle + '&txt_demurrage_' + i + '=' + txt_demurrage + '&txt_other_' + i + '=' + txt_other + '&txt_amount_' + i + '=' + txt_amount + '&txt_deduction_' + i + '=' + txt_deduction + '&txt_payable_' + i + '=' + txt_payable;
                i++;
            });
        }
        if(type==2){
            $("#tbl_details").find('tbody tr').each(function()
            {
                var txt_btb_lc_id=$(this).find('input[name="txt_btb_lc_id[]"]').val();
                var cbo_supp_id=$(this).find('select[name="cbo_supp_id[]"]').val();

                var txt_challan_no=$(this).find('input[name="txt_challan_no[]"]').val();
                var txt_qty=$(this).find('input[name="txt_qty[]"]').val();
                var txt_cbm=$(this).find('input[name="txt_cbm[]"]').val();
                var txt_vehicle_no=$(this).find('input[name="txt_vehicle_no[]"]').val();
                var txt_no_vehicle=$(this).find('input[name="txt_no_vehicle[]"]').val();
                var txt_vehicle_rent=$(this).find('input[name="txt_vehicle_rent[]"]').val();
                var txt_loading_unloading=$(this).find('input[name="txt_loading_unloading[]"]').val();
                var txt_local_vechicle=$(this).find('input[name="txt_local_vechicle[]"]').val();
                var txt_other=$(this).find('input[name="txt_other[]"]').val();
                var txt_amount=$(this).find('input[name="txt_amount[]"]').val();
                var txt_deduction=$(this).find('input[name="txt_deduction[]"]').val();
                var txt_payable=$(this).find('input[name="txt_payable[]"]').val();
                
                if (txt_amount=='' || txt_amount <=0){
                    
                    ("Total Amount can not Zero or Blank ");
                    necessity_setup_chk=1;
                    return;
                }

                dataString+='&txt_btb_lc_id_' + i + '=' + txt_btb_lc_id + '&cbo_supp_id_' + i + '=' + cbo_supp_id  +'&txt_challan_no_' + i + '=' + txt_challan_no + '&txt_qty_' + i + '=' + txt_qty + '&txt_cbm_' + i + '=' + txt_cbm + '&txt_vehicle_no_' + i + '=' + txt_vehicle_no + '&txt_no_vehicle_' + i + '=' + txt_no_vehicle + '&txt_vehicle_rent_' + i + '=' + txt_vehicle_rent  + '&txt_loading_unloading_' + i + '=' + txt_loading_unloading+ '&txt_local_vechicle_' + i + '=' + txt_local_vechicle + '&txt_other_' + i + '=' + txt_other+ '&txt_amount_' + i + '=' + txt_amount + '&txt_deduction_' + i + '=' + txt_deduction + '&txt_payable_' + i + '=' + txt_payable;
                i++;
            });
        }
        if(necessity_setup_chk !=0){
            return;
        }else{
            var total_row = $("#tbl_details tbody tr").length;
            var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+get_submitted_data_string('update_id*cbo_company_name*cbo_location_name*cbo_transport_company*txt_bill_date*txt_payable_date*txt_bill_no*cbo_type_name*cbo_shipment_id*txt_depo*txt_port*txt_remarks*cbo_approve_status*txt_system_id',"../../")+dataString;
            // alert(data);
            // return;
            freeze_window(operation);
            http.open("POST","requires/transport_bill_entry_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_transport_bill_entry_reponse;
        }

    }

    function fnc_transport_bill_entry_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                $('#cbo_company_name').attr('disabled',true);
                $('#cbo_type_name').attr('disabled',true);

				set_button_status(1, permission, 'fnc_transport_bill_entry',1);
			}
            if(parseInt(trim(reponse[0]))==11)
			{
                alert("DUPLICATE BILL NO NOT ALLOWED");
                release_freezing();
				return;		
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				reset_form('transportbillentry_1','bill_tbl','','','','');
                set_button_status(0, permission, 'fnc_transport_bill_entry',1);
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }

    function form_reset_tbe() {
        $('#cbo_company_name').removeAttr('disabled');
        $('#cbo_type_name').removeAttr('disabled');
    }

    function calculate_total_payable(id)
    {
        var amount=$('#txt_amount_'+id).val()*1;
        var deduction=$('#txt_deduction_'+id).val()*1;
        var payable=amount-deduction
        $('#txt_payable_'+id).val(payable.toFixed(2));
        calculate_total_amount();
    }
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="transportbillentry_1" id="transportbillentry_1" autocomplete="off" data-entry_form="105">
            <fieldset style="width:1280px;">
                <legend>Trasport Bill Entry</legend>
                <table width="850" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                    <tr width="800" >
                        <td colspan="3" align="right"><strong>System ID</strong></td> 
                        <td colspan="3">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" id="update_id">
                        </td>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="150">
                             <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_country_id, "load_drop_down( 'requires/transport_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/transport_bill_entry_controller', this.value, 'load_drop_down_transport_com', 'transfer_com' );");?>
                        </td>
                        <td width="100" class="must_entry_caption">Location</td>
                        <td width="150" id="location_td">
                            <? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="100" class="must_entry_caption">Transport. Company</td>
                        <td width="150" id="transfer_com">
                            <? echo create_drop_down( "cbo_transport_company", 150, $blank_array,"", 1, "-- Select Transport --", $selected, "" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Bill Date</td>
                        <td>
                            <input style="width:140px " name="txt_bill_date" id="txt_bill_date" class="datepicker" readonly/>
                        </td>
                        <td>Bill NO</td>
                        <td>
                            <input type="text" name="txt_bill_no" id="txt_bill_no" style="width:140px" class="text_boxes" >
                        </td>
                        <td class="must_entry_caption">Type</td>
                        <td>
                            <? echo create_drop_down( "cbo_type_name",150,array(1=>"Export",2=>"Import"),'',1,'--Select--',0,"load_drop_down( 'requires/transport_bill_entry_controller', this.value, 'load_bill_tbl', 'bill_tbl' );",0); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Ship Mode</td>
                        <td>
                            <? echo create_drop_down( "cbo_shipment_id", 150, $shipment_mode,"", 1, "-- Select --", 0, "" );?>
                        </td>
                        <td class="must_entry_caption">DEPO</td>
                        <td>
                            <input type="text" name="txt_depo" id="txt_depo" style="width:140px" class="text_boxes" >
                        </td>
                        <td>Port Name</td>
                        <td>
                            <input type="text" name="txt_port" id="txt_port" style="width:140px" class="text_boxes" >
                        </td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan="3">
                            <input type="text" name="txt_remarks" id="txt_remarks" style="width:430px" class="text_boxes" >
                        </td>
                        <td>Payable Date</td>
                        <td>
                        <input style="width:140px " name="txt_payable_date" id="txt_payable_date" class="datepicker" readonly>                        
                        </td>
                    </tr> 
                    <tr>
                    <td>Ready To Approve</td>
                            <td><? echo create_drop_down( "cbo_approve_status", 140, $yes_no,"", 1, "-- Select --", "", "","" ); ?> </td>

                    </tr> 
                </table>
                <br>
                <div id="bill_tbl"></div><br>
                <? echo load_submit_buttons( $permission, "fnc_transport_bill_entry", 0,0,"form_reset_tbe();reset_form('transportbillentry_1','bill_td','','','','');",1); ?>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>