<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for BL Charge Entry
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	13-4-2021
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
echo load_html_head_contents("BL Charge Entry", "../../",  1, 1, $unicode,'','');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_Invoice()
    {
        if (form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link='requires/bl_charge_entry_controller.php?action=invoice_popup_search&cbo_company_name='+cbo_company_name;
        var title='Export Information Entry Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var invoice_id=this.contentDoc.getElementById("hidden_invoice_id").value;
            var company_id=this.contentDoc.getElementById("company_id").value;
            if(trim(invoice_id)!="")
            {
                freeze_window(5);
                get_php_form_data(invoice_id, "populate_data_from_invoice", "requires/bl_charge_entry_controller" );
                $("#cbo_company_name").attr('disabled',true);
                release_freezing();
            }
        }
    }
    function total_calculate(){
        var bl_charge = $("#txt_bl_charge").val();
        var stamp_charge = $("#txt_stamp_charge").val();
        var air_company_charge = $("#txt_air_company_charge").val();
        var air_buyer_charge = $("#txt_air_buyer_charge").val();
        var adjustment_local_charge = $("#txt_adjustment_local_charge").val();
        var mbl_surrendered_charge = $("#txt_mbl_surrendered_charge").val();
        var special_permission_charge = $("#txt_special_permission_charge").val();
        var others_charge = $("#txt_others_charge").val();

        var total_amount=bl_charge*1+stamp_charge*1+air_company_charge*1+air_buyer_charge*1+adjustment_local_charge*1+mbl_surrendered_charge*1+special_permission_charge*1+others_charge*1;
        $("#total_amount").val(total_amount.toFixed(2));
    }
    function fnc_bl_charge_entry (operation) 
    {
        if (form_validation('cbo_company_name*invoice_id*txt_bl_change_date','Company Name*Invoice No*BL Change Entry Date')==false)
        {
            return;
        }
        
        var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*update_id*cbo_company_name*invoice_id*txt_bl_change_date*txt_bl_no*txt_bl_date*cbo_forwarder_name*txt_bl_charge*txt_stamp_charge*txt_air_company_charge*txt_air_buyer_charge*txt_adjustment_local_charge*txt_mbl_surrendered_charge*txt_special_permission_charge*txt_others_charge*txt_remarks*ready_to_approve',"../../");
        // alert(data);
        // return;
        freeze_window(operation);
        http.open("POST","requires/bl_charge_entry_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_bl_charge_entry_reponse;

    }
    function fnc_bl_charge_entry_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_invoice_no').attr('disabled',true);
				set_button_status(1, permission, 'fnc_bl_charge_entry',1);
			}
			if(parseInt(trim(reponse[0]))==2)
			{
                form_reset_blce();
				reset_form('blchargeentry_1','','','','','');
                set_button_status(0, permission, 'fnc_bl_charge_entry',1);
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }
    function openmypage_sys_no()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/bl_charge_entry_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search BL Charge Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                freeze_window();
                get_php_form_data( theemail, 'populate_data_from_search_popup','requires/bl_charge_entry_controller');
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_invoice_no').attr('disabled',true);
                total_calculate();
                set_button_status(1, permission, 'fnc_bl_charge_entry',1);
            }
            release_freezing();
        }
    }
    function form_reset_blce() {
        reset_form('blchargeentry_1','','','','','');
        $('#cbo_company_name').removeAttr('disabled');
        $('#txt_invoice_no').removeAttr('disabled');
        $('#txt_bl_change_date').val('<? echo date("d-m-Y"); ?>');
    }
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
            <form name="blchargeentry_1" id="blchargeentry_1" autocomplete="off">
                <fieldset style="width:880px;">
                    <legend>BL Charge Entry</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="2">
                        <tr style="width:800px;">
                            <td colspan="3" align="right"><strong>System ID</strong></td> 
                            <td colspan="3">
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                                <input type="hidden" id="update_id">
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="must_entry_caption">Company</td>
                            <td width="150">
                                <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---",  $cbo_country_id, "");
                                ?>
                            </td>
                            <td width="100" class="must_entry_caption">Invoice No.</td>
                            <td width="150">
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_Invoice()" class="text_boxes" placeholder="Browse" name="txt_invoice_no" id="txt_invoice_no" readonly />
                                <input type="hidden" id="invoice_id">
                            </td>
                            <td width="100">BL Change Entry Date</td>
                            <td width="150">
                                <input style="width:140px " name="txt_bl_change_date" id="txt_bl_change_date" class="datepicker" value="<?echo date('d-m-Y');?>" placeholder="Display" disabled/>
                            </td>
                        </tr>
                        <tr>
                            <td>Invoice value</td>
                            <td>
                                <input style="width:140px " name="txt_invoice_value" id="txt_invoice_value" class="text_boxes" placeholder="Display" disabled/>
                            </td>
                            <td>Invoice Date</td>
                            <td>
                                <input type="text" name="txt_invoice_date" id="txt_invoice_date" style="width:140px"  class="datepicker"  placeholder="Display" disabled>
                            </td>
                            <td>Invoice Qnty. Pcs</td>
                            <td>
                                <input type="text" name="txt_invoice_qnty" id="txt_invoice_qnty" style="width:140px" class="text_boxes" placeholder="Display" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>B/L No</td>
                            <td>
                                <input type="text" name="txt_bl_no" id="txt_bl_no" style="width:140px" class="text_boxes" placeholder="Write">
                            </td>
                            <td >B/L Date</td>
                            <td>
                                <input class="datepicker" type="text" name="txt_bl_date" id="txt_bl_date" style="width:140px" class="datepicker" placeholder="Select Date" >
                            </td>
                            <td>Original BL Rev. Date</td>
                            <td>
                                <input style="width:140px " name="txt_bl_rev_date" id="txt_bl_rev_date" class="datepicker" placeholder="Display" disabled/>
                            </td>
                        </tr>
                        <tr>
                            <td>Buyer Name</td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 ); 
                                ?></td>
                            <td >Forwarder Name</td>
                            <td>
                                <? 
                                // echo create_drop_down( "cbo_forwarder_name", 150, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name","id,supplier_name", 1, " Display ", $selected, "",1 );

                                echo create_drop_down( "cbo_forwarder_name", 150, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name","id,supplier_name", 1, " -Select- ", $selected, "",0 );
                                ?>
                            </td>
                            <td >Ship Mode</td>
                            <td>
                                <? echo create_drop_down( "cbo_shipment_id", 150, $shipment_mode,"", 1, " Display ", 0, "",1 );?>
                            </td>
                        </tr>
                        <tr>
                            <td>Ex-Factory Date</td>
                            <td>
                                <input type="text" name="txt_ex_factory" id="txt_ex_factory" style="width:140px" class="datepicker" placeholder="Display" disabled >
                            </td>
                            <td>Remarks</td>
                            <td colspan='3'>
                                <input type="text" name="txt_remarks" id="txt_remarks" style="width:450px" class="text_boxes" >
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" id="posted_account_td" style="max-width:100px; color:red; font-size:20px;">
                            </td>												
						</tr>

                        <tr>
                            <td> Ready to approve</td>
                            <td>
                                <? echo create_drop_down( "ready_to_approve", 150, $yes_no,"", 1, " --Select-- ", 0, "", );?>
                            </td>
                        </tr>
                        
                    </table>
                    <br>
                    <table cellspacing="0" width="300" class="rpt_table" >
                        <thead>
                            <tr>
                                <th colspan="3" >Charge Entry Head</th>
                            </tr>
                            <tr>
                                <th width="40">Sl No</th>
                                <th width="150">Charge Head</th>
                                <th width="90">Amount TAKA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td align="right">B/ L Charge &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_bl_charge" id="txt_bl_charge" class="text_boxes_numeric" style="width:80px"  onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center">2</td>
                                <td align="right">Stamp Charge &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_stamp_charge" id="txt_stamp_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()"/>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">3</td>
                                <td align="right">Air Freight Charge -Company &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_air_company_charge" id="txt_air_company_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center">4</td>
                                <td align="right">Air Freight Charge -Buyer &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_air_buyer_charge" id="txt_air_buyer_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td align="center">5</td>
                                <td align="right">Freight Adjustment/Local Charges &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_adjustment_local_charge" id="txt_adjustment_local_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center">6</td>
                                <td align="right">MBL Surrendered Fee &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_mbl_surrendered_charge" id="txt_mbl_surrendered_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center">7</td>
                                <td align="right">Special Permission &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_special_permission_charge" id="txt_special_permission_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>

                            <tr>
                                <td align="center">8</td>
                                <td align="right">Others &nbsp;</td>
                                <td>
                                    <input type="text" name="txt_others_charge" id="txt_others_charge" class="text_boxes_numeric" style="width:80px" onKeyUp="total_calculate()" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right"><strong>Total &nbsp;</strong></td>
                                <td>
                                    <input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:80px;font-weight: bold;" readonly />
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <br>
                    <? echo load_submit_buttons( $permission, "fnc_bl_charge_entry", 0,0,"form_reset_blce();",1); ?>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>