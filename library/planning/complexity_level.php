<?
/* -------------------------------------------- Comments

  Purpose			: 	This form will create complexity level

  Functionality	:

  JS Functions	:

  Created by		:	Feroz Mhamud
  Creation date 	: 	01-03-2019
  Updated by 		:
  Update date		:

  QC Performed BY	:

  QC Date			:

  Comments		:

 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Complexity Level Entry", "../../", 1, 1, $unicode, '', '');
?>

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_complexity_level(operation)
    {
        if(operation == 2)
        {
            var update_id = document.getElementById('update_id').value;
            var txt_level_type=$("#txt_level_type").val();
            txt_level_type=trim(txt_level_type).toLowerCase();
            //alert(txt_level_type);
            var level=(txt_level_type=='basic') ||  (txt_level_type=='fancy') ||  (txt_level_type=='critical') ||  (txt_level_type=='average') ;
            if(level)
            {
                alert("You can not delete this data");
                return;
            }

        }
        if (form_validation('txt_level_type*txt_first_day*txt_increment_type*txt_target', 'Level*First Day*Increment*Target') == false)
        {
            return;  //'txt_operation*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv
        }
        else
        {
            eval(get_submitted_variables('txt_level_type*txt_first_day*txt_increment_type*txt_target*cbo_status*update_id'));
            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_level_type*txt_first_day*txt_increment_type*txt_target*cbo_status*update_id', "../../");
            //alert (data);
            freeze_window(operation);
            http.open("POST", "requires/complexity_level_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_complexity_level_reponse;
        }
    }

    function fnc_complexity_level_reponse()
    {
        if (http.readyState == 4)
        {
            //alert(http.responseText);
            var reponse = trim(http.responseText).split('**');
            if (reponse[0].length > 2)
                reponse[0] = 10;
            show_msg(reponse[0]);
            document.getElementById('update_id').value = reponse[2];
            show_list_view('', 'complexity_level_list_view', 'complexity_level_list', '../planning/requires/complexity_level_controller', 'setFilterGrid("list_view",-1)');
            set_button_status(0, permission, 'fnc_complexity_level', 1);
            reset_form('complexitylevel_1', '', '');
            release_freezing();
        }
    }

</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>

        <fieldset style="width:600px;">
            <legend>Complexity Level Entry</legend>
            <form name="complexitylevel_1" id="complexitylevel_1" autocomplete="off">
                <table width="100%">
                    <tr>
                        <td width="120" class="must_entry_caption">Level</td>
                        <td colspan="3">
                            <input type="text" name="txt_level_type" id="txt_level_type" class="text_boxes" style="width:150px"/>
                        </td>
                        <td width="120" class="must_entry_caption">First Day</td>
                        <td>
                            <input type="text" name="txt_first_day" id="txt_first_day" class="text_boxes_numeric" style="width:150px;" />
                        </td>
                    </tr>

                    <tr>
                        <td width="120" class="must_entry_caption">Increment</td>
                        <td colspan="3">
                            <input type="text" name="txt_increment_type" id="txt_increment_type" class="text_boxes_numeric" onKeyUp="" style="width:150px;"/>
                        </td>
                        <td width="120" class="must_entry_caption">Target</td>
                        <td >
                            <input type="text" name="txt_target" id="txt_target" class="text_boxes_numeric" onKeyUp="" style="width:150px;"/>
                        </td>
                        </tr>
                        <tr>
                        <td>Status</td>
                        <td >		<?
                            echo create_drop_down("cbo_status", 142, $row_status, '', '', '');
                            ?>

                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" height="15" align="center">
                            <input type="hidden" name="update_id" id="update_id" >

                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_complexity_level", 0, 0, "reset_form('complexitylevel_1','','','')");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" height="15" align="center"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" id="complexity_level_list">
                            <?
                            //$row_status = array(1 => "Active", 2 => "InActive", 3 => "Cancelled");
                            $arr = array(4=> $row_status);
                            echo create_list_view("list_view", "Level,First Day,Increment,Target,Status", "200,100,100,100,50", "650", "220", 1, "select level_type,first_day,increment_type,target,id,status from  lib_complexity_level where status_active=1 and  is_deleted=0 order by id asc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,status", $arr, "level_type,first_day,increment_type,target,status", "../planning/requires/complexity_level_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0');
                            ?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>

    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript">//set_bangla();</script>
</html>
