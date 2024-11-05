<?
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Count Information", "../../", 1, 1, $unicode, '', '');
?>
<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var permission = '<? echo $permission; ?>';

    function fnc_yarn_count_info(operation)
    {
        if (form_validation('txt_yarn_brand', 'Yarn Brand') == false)
        {
            return;
        } else
        {
             if (operation == 2)
            {
                var con = confirm("Do You Want To Delete Data Permanently ?");
                if (con == false)
                {
                    void(0);
                    return;
                }
            }
            
            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_yarn_brand*cbo_status*txt_sequence*update_id', "../../");

            freeze_window(operation);

            http.open("POST", "requires/yarn_brand_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_yarn_count_info_reponse;
        }
    }

    function fnc_yarn_count_info_reponse()
    {
        if (http.readyState == 4)
        {
            //release_freezing(); return;
            var reponse = trim(http.responseText).split('**');
            if (reponse[0] == 15)
            {
                setTimeout('fnc_yarn_count_info( 0 )', 8000);
            } else
            {
                if (reponse[0].length > 2)
                    reponse[0] = 10;

                show_msg(reponse[0]);
                show_list_view(reponse[1], 'search_list_view', 'yarn_count_list_view', '../item_details/requires/yarn_brand_controller', 'setFilterGrid("list_view",-1)');
                reset_form('yarncountinfo_1', '', '');
                set_button_status(0, permission, 'fnc_yarn_count_info', 1);
                release_freezing();
            }
        }
    }
</script>

</head>

<body onLoad="set_hotkey()">

    <div align="center">
        <? echo load_freeze_divs("../../", $permission); ?>
        <fieldset style="width:400px;">
            <legend>Yarn Brand Info</legend>
            <form name="yarncountinfo_1" id="yarncountinfo_1">	
                <table cellpadding="0" cellspacing="2" width="100%">
                    <tr>
                        <td width="130" class="must_entry_caption">Yarn Brand</td>
                        <td colspan="3">
                            <input type="text" name="txt_yarn_brand" id="txt_yarn_brand" class="text_boxes" style="width:245px" maxlength="15" title="Maximum 15 Character" />						
                        </td>
                    </tr>	
                    <tr>

                        <td  valign="top">Status </td>
                        <td valign="top">
                            <?
                            echo create_drop_down("cbo_status", 110, $row_status, "", "", "", 1, "");
                            ?> 
                            <input type="hidden" name="update_id" id="update_id" >
                        </td>
                        <td  valign="top">Sequence No. </td>
                        <td valign="top">

                            <input type="text" name="txt_sequence" id="txt_sequence" class="text_boxes_numeric" style="width:50px" >
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4" align="center" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_yarn_count_info", 0, 1, "reset_form('yarncountinfo_1','','',1)");
                            ?>
                        </td>				
                    </tr>
                    <tr>
                        <td height="16" colspan="4"></td>
                    </tr>
                </table>
            </form>	
        </fieldset>
        <fieldset style="width:300px; margin-top:10px">
            <legend>List View</legend>
            <form>
                <div style="width:460px; margin-top:10px" id="yarn_count_list_view" align="left">
                    <?
                    $arr = array(1 => $row_status);
                    echo create_list_view("list_view", "Yarn Brand Name,Status,Sequence No", "150,190,50", "450", "220", 0, "select id,yarn_brand,sequence_no,status_active from lib_yarn_brand where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active,0", $arr, "yarn_brand,status_active,sequence_no", "../item_details/requires/yarn_brand_controller", 'setFilterGrid("list_view",-1);');
                    ?>
                </div>
            </form>
        </fieldset>	
    </div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
