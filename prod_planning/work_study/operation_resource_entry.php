<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create operation resource Task Entry
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	16.10.2022
Updated by 		:   		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

// print_r($_SESSION['logic_erp']['mandatory_field'][680]);die;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Operation Resource Task Entry", "../../", 1, 1, $unicode, '', '');
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_resource_entry(operation) {

        if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][680]);?>')
        {
            if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][680]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][680]);?>')==false)
            {
                release_freezing();
                return;
            }
            var txt_needle_thread = parseInt($("#txt_needle_thread").val());
            var txt_bobbin_thread = parseInt($("#txt_bobbin_thread").val());
            var total_percent= txt_needle_thread+txt_bobbin_thread;
            if(total_percent > 100){
                alert('Total Percentage More Than 100 Not Allowed');
                release_freezing();
                return;
            }
            if(total_percent < 100){
                alert('Total Percentage less Than 100 Not Allowed');
                release_freezing();
                return;
            }
        } 

        if (form_validation('txt_resource_id*txt_resource_name*cbo_process_id', 'Resource*Resource Short Name*Process') ==
            false) {
            return;
        } else {
            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(
                'txt_resource_id*txt_resource_name*cbo_process_id*txt_consumption_factor*txt_needle_thread*txt_bobbin_thread*cbo_status*update_id', "../../");
            freeze_window(operation);
            http.open("POST", "requires/operation_resource_entry_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_resource_entry_reponse;
        }
    }

    function fnc_resource_entry_reponse() {
        if (http.readyState == 4) {
            var reponse = trim(http.responseText).split('**');
            var process_id = $('#cbo_process_id').val();
            show_msg(trim(reponse[0]));
            reset_form('resource_entry_1', '', '', '', '', 'cbo_process_id');
            set_button_status(0, permission, 'fnc_resource_entry', 1);
            show_list_view(process_id, 'operation_resource_list', 'list_view_operation_resource',
                'requires/operation_resource_entry_controller', 'setFilterGrid("tbl_operation_resource_list",-1)');
            show_list_view(process_id, 'saved_operation_resource_list', 'list_view_report_settings', 'requires/operation_resource_entry_controller', 'setFilterGrid("tbl_saved_operation_resource_list",-1)');
            release_freezing();
        }
    }


    function saved_resource_search_process_wise(process_id) {
        show_list_view(process_id, 'operation_resource_list', 'list_view_operation_resource',
            'requires/operation_resource_entry_controller', 'setFilterGrid("tbl_operation_resource_list",-1)');

        show_list_view(process_id, 'saved_operation_resource_list', 'list_view_report_settings', 'requires/operation_resource_entry_controller', 'setFilterGrid("tbl_saved_operation_resource_list",-1)');
    }

    function set_resource(resource_id) {
        reset_form('resource_entry_1', '', '', '', '', 'cbo_process_id');
        set_button_status(0, permission, 'fnc_resource_entry', 1);
        $('#txt_resource_id').val(resource_id);
    }
</script>


</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;">
        <?= load_freeze_divs("../../", $permission); ?>
        <table cellspacing="5">
            <tr>
                <td align="center" valign="top">
                    <fieldset style="width:690px;">
                        <legend>Operation Resource Rntry</legend>
                        <form id="resource_entry_1" autocomplete="off">
                            <table width="620" cellspacing="2" cellpadding="0" border="1">
                                <tr>
                                    <td class="must_entry_caption">Resource</td>
                                    <td id="resource_id_td" colspan="6">
                                        <?= create_drop_down("txt_resource_id", 460, $production_resource, "", 1, "--Resource--", $selected, "", 1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Resource Customize Name</td>
                                    <td colspan="6">
                                        <input type="text" name="txt_resource_name" id="txt_resource_name" class="text_boxes" style="width:450px" maxlength="50" title="Maximum 50 Character" placeholder="Maximum 50 Character" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Process</td>
                                    <td><?= create_drop_down("cbo_process_id", 110, $machine_category, "", 1, "-- Select --", 1, "saved_resource_search_process_wise(this.value)", '', "4,7,8");
                                        ?></td>
                                    <td>Status</td>
                                    <td><?= create_drop_down("cbo_status", 86, $row_status, 0, "", 1, "");  ?></td>
                                    <td>Image</td>
                                    <td><input type="button" class="image_uploader" style="width:120px;" value="Click Add/View IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_resource_id').value,'', 'operation_resource', 0 ,1)"></td>
                                </tr>
                                <tr> 
                                    <td>Needle Thread (%)</td>
                                    <td colspan="2"><input type="text" name="txt_needle_thread" id="txt_needle_thread" class="text_boxes_numeric" style="width:140px" placeholder="Write"></td>

                                    <td>Consumption Factor</td>
                                    <td colspan="2"><input type="text" name="txt_consumption_factor" id="txt_consumption_factor" class="text_boxes_numeric" style="width:140px" placeholder="Write"></td>

                                </tr>
                                <tr> 
                                    <td>Bobbin Thread (%)</td>
                                    <td><input type="text" name="txt_bobbin_thread" id="txt_bobbin_thread" class="text_boxes_numeric" style="width:140px" placeholder="Write"></td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="4" valign="middle" class="button_container">
                                        <?= load_submit_buttons($permission, "fnc_resource_entry", 0, 0, "reset_form('resource_entry_1','','')", 1); ?>
                                        <input type="hidden" id="update_id" readonly>
                                    </td>
                                </tr>
                            </table>
                            <div id="list_view_report_settings"></div>
                        </form>
                    </fieldset>
                </td>
                <td valign="top" id="list_view_operation_resource"></td>
            </tr>
        </table>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    $( "#cbo_process_id" ).val(8).trigger( "change" );
</script>

</html>