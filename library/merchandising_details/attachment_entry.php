<?php

/******************************************************************
|	Purpose			:	This form will create Operation Bulletin Attachment Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Saidul Islam REZA
|	Creation date 	:	31-12-2022
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
 ********************************************************************/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Attachment Entry", "../../", 1, 1, $unicode, 1, '');
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    let permission = '<? echo $permission; ?>';

    let fn_attachment_entry = (operation) => {

        if (form_validation('txt_attachment_name', 'Composition Name') == false) {
            return;
        } else {

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_attachment_name*cbo_status*update_id', "../../");
            freeze_window(operation);
            http.open("POST", "requires/attachment_entry_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = () => {

                if (http.readyState == 4) {
                    var reponse = trim(http.responseText).split('**');
                    if (reponse[0] == 121) {
                        alert('This attachment used in gsd entry. Can\'t be deleted');
                        release_freezing();
                        return;
                    } else {
                        show_msg(reponse[0]);
                        show_list_view('', 'attachment_list_view', 'attachment_list_view', 'requires/attachment_entry_controller', 'setFilterGrid("list_view",-1)');
                        reset_form('attachmententry_1', '', '');
                        set_button_status(0, permission, 'fn_attachment_entry', 1);
                        release_freezing();
                    }

                }

            }
        }
    }
</script>
</head>

<body onload="set_hotkey()">
    <? echo load_freeze_divs("../../", $permission);  ?>
    <div align="center" style="width:100%;">
        <form name="attachmententry_1" id="attachmententry_1" autocomplete="off">
            <fieldset style="width:550px;">
                <legend>Attachment Entry</legend>
                <table cellpadding="0" cellspacing="2" width="520px">
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Attachment Name</td>
                        <td width="160"><input type="text" name="txt_attachment_name" id="txt_attachment_name" class="text_boxes" style="width:330px" /></td>
                    </tr>
                    <tr>
                        <td align="right">Status</td>
                        <td><?= create_drop_down("cbo_status", 340, $row_status, '', $is_select, $select_text, 1, $onchange_func, '', '', '', '', 3); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" style="padding-top:10px;" class="button_container">
                            <?= load_submit_buttons($permission, "fn_attachment_entry", 0, 0, "reset_form('attachmententry_1','','','','','')"); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width:550px;">
                <div id="attachment_list_view">
                    <?php
                    $arr = array(1 => $row_status);
                    echo  create_list_view("list_view", "Attachment Name,Status", "400,100", "550", "220", 0, "select id, attachment_name, status_active from LIB_ATTACHMENT where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "attachment_name,status_active", "requires/attachment_entry_controller", 'setFilterGrid("list_view",-1);', '0,0');
                    ?>
                </div>
            </fieldset>
        </form>
    </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>