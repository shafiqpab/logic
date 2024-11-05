<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Shipping Approval Req
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	24/04/2022
Updated by 		: 	Al-Hassan	
Update date		: 	22/08/2023	   
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
$menu_id = $_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];
//print_r($_SESSION['logic_erp']['data_arr'][741]);
//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Erosion Entry", "../../", 1, 1, $unicode, 1, 1);

//echo load_html_head_contents("Erosion Entry", "../../", 1, 1,'','1','');
echo load_html_head_contents("Erosion Entry", "../../", 1, 1,$unicode,1,'');
?>

<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission='<? echo $permission; ?>';
    var field_level_data=new Array();
    <?
    if(count($_SESSION['logic_erp']['data_arr'][741])>0)
	{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][741] );
	echo "var field_level_data= ". $data_arr . ";\n";
	}
    ?>

    function fn_save_update_delete(operation) {
        if (operation == 4) {
            fn_generate_print(operation);
        } else {
            if (form_validation('cbo_company_id*cbo_buyer_name*txt_erotion_value*txt_erosion_date*txt_shiped_qty*txt_expected_date*txt_problem*txt_root_cause*txt_corrective_action_plan*txt_precautionary_plan', 'Company*Buyer*Erosion Value*Erosion Date*Ship Qty*Expected Date*Problems*Root Cause*Corrective Action Plan*Precautionary Plan') == false) {
                return;
            }
            if (($('#approval_status_id').val() == 1 || $('#approval_status_id').val() == 3) && $('#txt_update_type').val() != 1) {
                alert("This is " + $('#approval_status').text() + ", can't update or delete");
                release_freezing();
                return;
            }
            else {
                var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_erosion_no*update_id*cbo_company_id*hidden_po_id*cbo_erosion_type*cbo_buyer_name*txt_erotion_value*txt_erosion_date*txt_shiped_qty*txt_expected_date*cbo_approved*txt_problem*txt_root_cause*txt_corrective_action_plan*txt_precautionary_plan*txt_final_comments*cbo_profit_center*txt_profit_center_hidden_id*txt_department_hidden_id*cbo_department*txt_update_type', "../../");
                // alert(data);
                freeze_window(operation);
                http.open("POST", "requires/erosion_entry_controller.php", true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fnc_save_update_delete_res;
            }
        }
    }

    function fnc_save_update_delete_res() {
        if (http.readyState == 4) {
            release_freezing();
            var response = trim(http.responseText).split('**');

           /*  if((response[3] == 0)) {
                alert("First Approved Than Profit Center & Department.");
            } */

            if ((response[0] == 0 || response[0] == 1)) {
                document.getElementById("update_id").value = response[1];
                document.getElementById("txt_erosion_no").value = response[2];
                show_msg(response[0]);
                disable_enable_fields('txt_po_no*cbo_buyer_name', 1);
                set_button_status(1, permission, 'fn_save_update_delete', 1);
            } else if (response[0] == 11) {
                show_msg(response[0]);
            } else if (response[0] == 2) {
                show_msg(response[0]);
            }
        }
    }

    function openmypage_order() {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/erosion_entry_controller.php?action=order_no_popup&cbo_company_id=' + document.getElementById('cbo_company_id').value, 'Order No Popup', 'width=900px,height=420px,center=1,resize=0', '');
        emailwindow.onclose = function() {
            var order_id = this.contentDoc.getElementById("po_id").value;
            var order_no = this.contentDoc.getElementById("po_no").value;
            var company_id = this.contentDoc.getElementById("company_id").value;
            var buyer_id = this.contentDoc.getElementById("buyer_id").value;
            freeze_window(5);
            document.getElementById("cbo_company_id").value = company_id;
            document.getElementById("hidden_po_id").value = order_id;
            document.getElementById("txt_po_no").value = order_no;
            document.getElementById("cbo_buyer_name").value = buyer_id;
            disable_enable_fields('txt_po_no*cbo_buyer_name', 1);
            release_freezing();
        }
    }

    function openmypage_sys_id() {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/erosion_entry_controller.php?action=sys_id_popup&cbo_company_id=' + document.getElementById('cbo_company_id').value, 'Order No Popup', 'width=900px,height=420px,center=1,resize=0', '../');
        emailwindow.onclose = function() {
            freeze_window(5);
            var sys_id = this.contentDoc.getElementById("sys_id").value;
            get_php_form_data(sys_id, "get_sys_info", "requires/erosion_entry_controller");
            release_freezing();
        }
    }

    function openmypage_profit_center() {
        if (form_validation('cbo_company_id*txt_erotion_value', 'Company*Erosion Value') == false) {
            return;
        } 
        var cbo_name = $("#cbo_profit_center").val();
      
        // txt_profit_center_hidden_id
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/erosion_entry_controller.php?action=profit_center_popup&company_id=' + document.getElementById('cbo_company_id').value+'&txt_erotion_value=' + document.getElementById('txt_erotion_value').value+'&txt_profit_center_hidden='+$("#txt_profit_center_hidden_id").val()+'&txt_profit_center='+$("#cbo_profit_center").val(), 'Profit Center Popup', 'width=390px,height=250px,center=1,resize=0', '../');
        emailwindow.onclose = function() { //txt_erotion_value
            freeze_window(5);
            var profit_center_id = this.contentDoc.getElementById("txt_profit_center_id").value;
            var profit_center_name = this.contentDoc.getElementById("txt_profit_center_name").value;
            document.getElementById("txt_profit_center_hidden_id").value = profit_center_id;
            document.getElementById("cbo_profit_center").value = profit_center_name;

            release_freezing();
        }
    }

    function openmypage_department() 
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/erosion_entry_controller.php?action=department_popup&company_id=' + document.getElementById('cbo_company_id').value+'&erotion_value=' + document.getElementById('txt_erotion_value').value+'&txt_department_hidden_id='+$("#txt_department_hidden_id").val()+'&txt_department_name='+$("#cbo_department").val(), 'Profit Center Popup', 'width=450px,height=360px,center=1,resize=0', '../');
        emailwindow.onclose = function() { //txt_erotion_value
            freeze_window(5);
            var department_id = this.contentDoc.getElementById("txt_department_id").value;
            var department_name = this.contentDoc.getElementById("txt_department_name").value;
            document.getElementById("txt_department_hidden_id").value = department_id;
            document.getElementById("cbo_department").value = department_name;
            release_freezing();
        }
    }

    function fn_generate_print(operation) {
        var data = "action=generate_print&operation=" + operation + get_submitted_data_string('txt_erosion_no*update_id*cbo_company_id*hidden_po_id*cbo_erosion_type*txt_problem*txt_root_cause*txt_corrective_action_plan*txt_precautionary_plan', "../../");

        freeze_window(operation);
        http.open("POST", "requires/erosion_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_generate_print_res;
    }

    function fn_generate_print_res() {

        if (http.readyState == 4) {
            release_freezing();
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><title></title></head><body>' + http.responseText + '</body</html>');
            d.close();
        }
    }

    function call_print_button_for_mail(mail) {

        var data = "action=generate_print&operation=4&mail_data=" + mail + "**1" + get_submitted_data_string('txt_erosion_no*update_id*cbo_company_id*hidden_po_id*cbo_erosion_type*txt_problem*txt_root_cause*txt_corrective_action_plan*txt_precautionary_plan', "../../");

        freeze_window(operation);
        http.open("POST", "requires/erosion_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = () => {
            if (http.readyState == 4) {
                release_freezing();
                alert(http.responseText);
            }
        }
    }
</script>
</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>
        <form name="shipping_app_form_1" id="shipping_app_form_1" autocomplete="off">
            <fieldset style="width:1000px;height:300px; ">
                <legend>Erosion Entry</legend>
                <table style="height:200px; " cellpadding="5" cellspacing="5" border="0">
                    <tr>
                        <td colspan="6" align="center"><strong>Erosion No</strong>
                            <input type="text" onDblClick="openmypage_sys_id();" class="text_boxes" name="txt_erosion_no" id="txt_erosion_no" readonly style="width:180px;" placeholder="Brows">
                            <input type="hidden" name="update_id" id="update_id" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" align="right">Company Name</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_company_id", 190, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/erosion_entry_controller',this.value+'***190', 'load_drop_down_buyer', 'buyer_td_id' );");
                            ?>
                            <input type="hidden" name="approval_status_id" id="approval_status_id" class="text_boxes" value="" style="width:150px;" />
                        </td>
                        <td align="right"><strong>Po No<strong></td>
                        <td>
                            <input type="text" onDblClick="openmypage_order();" class="text_boxes" name="txt_po_no" id="txt_po_no" readonly style="width:180px;" placeholder="Brows Order">
                            <input type="hidden" name="hidden_po_id" id="hidden_po_id" readonly>
                        </td>
                        <td align="right" class="must_entry_caption">Erosion Date</td>
                        <td><input type="text" name="txt_erosion_date" id="txt_erosion_date" class="datepicker" style="width:180px" value="<?= date('d-m-Y'); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" align="right" class="must_entry_caption">Erosion Type</td>
                        <td>
                            <?
                            $shipment_type = array(1 => "Discount Shipment", 2 => "Sea-Air Shipment", 3 => "Air Shipment",4 => "Re-Inspection");
                            echo create_drop_down("cbo_erosion_type", 190,  $shipment_type, "", 1, "-- Select--", 0, "", "", "");
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td_id">
                            <?
                            echo create_drop_down("cbo_buyer_name", 190, $blank_array, "", 1, "-- All Buyer --", 0, "");
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Erosion Value (USD)</td>
                        <td><input type="text" class="text_boxes_numeric" name="txt_erotion_value" id="txt_erotion_value" class="text_boxes" style="width:180px" /></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">To Be Shipped Qty</td>
                        <td><input type="text" style="width:180px" class="text_boxes_numeric" name="txt_shiped_qty" id="txt_shiped_qty" class="text_boxes" /></td>

                        <td align="right" class="must_entry_caption">Expected Ship Date</td>
                        <td><input type="text" name="txt_expected_date" id="txt_expected_date" class="datepicker" style="width:180px" value="<?= date('d-m-Y'); ?>" /></td>
                        <td align="right">Ready to Approve </td>
                        <td>
                            <?
                            $ready_to_approve = array(1 => "Yes", 2 => "No");
                            echo create_drop_down("cbo_approved", 190,  $ready_to_approve, "", 1, "-- Select--", 0, "", "", "");
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td align="right" class="must_entry_caption">The Problem</td>
                        <td><textarea name="txt_problem" id="txt_problem" class="text_boxes" style="width:180px"></textarea></td>
                        <td align="right" class="must_entry_caption">Root Cause</td>
                        <td align="left"><textarea name="txt_root_cause" id="txt_root_cause" class="text_boxes" style="width:180px"></textarea></td>
                        <td align="right" class="must_entry_caption">Corrective Action Plan</td>
                        <td><textarea name="txt_corrective_action_plan" id="txt_corrective_action_plan" class="text_boxes" style="width:180px"></textarea></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption"> Recovery Plan For Erosion</td>
                        <td><textarea name="txt_precautionary_plan" id="txt_precautionary_plan" class="text_boxes" style="width:180px"></textarea></td>

                        <td align="right" class="must_entry_caption">Profit Center</td>
                        <td id="profit_center_td">
                            <input type="text" onDblClick="openmypage_profit_center();" class="text_boxes" name="cbo_profit_center" id="cbo_profit_center" readonly style="width:180px;" placeholder="Browse">
                            <input type="hidden" name="txt_profit_center_hidden_id" id="txt_profit_center_hidden_id" class="text_boxes" value="" style="width:150px;"/>
                            <?
                            // echo create_drop_down("cbo_profit_center", 190, [], "", 1, "-- Select Profit Center --", $selected, 0);
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Department</td>
                        <td id="department_td">
                            <input type="text" onDblClick="openmypage_department();" class="text_boxes"  name="cbo_department" id="cbo_department" readonly style="width:180px;" placeholder="Browse">
                            <input type="hidden" name="department_id" id="txt_department_hidden_id" class="text_boxes" value="" style="width:150px;"/>
                            <?
                            // no need report department wise shaiful vai and beeresh sir.
                            // echo create_drop_down("cbo_department", 190, [], "", 1, "-- Select Department --", $selected, 0);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Final Comments</td>
                        <td colspan="3"><textarea name="txt_final_comments" id="txt_final_comments" class="text_boxes" style="width:93%"></textarea></td>

                        <td align="right">Attachment</td>
                        <td><input type="button" id="file_uploaded" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'erosion_entry', 2 ,1)"></td>
                    </tr>
                    <tr>
                        <td align="center" id="approval_status" colspan="6" style="color:#F00;"></td>
                    </tr>
                    <tr>
                        <td width="80%" align="center" colspan="6">
                            <input type="hidden" readonly id="txt_update_type">
                            <?
                            echo load_submit_buttons($permission, "fn_save_update_delete", 0, 1, "reset_form('shipping_app_form_1','','','','');set_button_status(0, permission, 'fn_save_update_delete', 1);", 1);
                            ?>
                            <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
                    </tr>
            </fieldset>
            </table>
        </form>
    </div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>