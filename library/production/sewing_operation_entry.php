<?
/* -------------------------------------------- Comments

  Purpose			: 	This form will create Sewing Operation Entry

  Functionality	:

  JS Functions	:

  Created by		:	CTO
  Creation date 	: 	07-10-2012
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
echo load_html_head_contents("Sewing Operation Entry", "../../", 1, 1, $unicode, '', '');
?>	

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_sewing_operation_entry(operation)
    {
        if (form_validation('txt_operation*txt_rate*cbo_uom*cbo_resource*txt_total_smv', 'Operation Name*Rate*UOM*Resource*Total SMV') == false)
        {
            return;  //'txt_operation*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv
        } else
        {
            eval(get_submitted_variables('txt_operation*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv*cbo_status*update_id'));
            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_operation*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv*cbo_status*update_id', "../../");
            freeze_window(operation);
            http.open("POST", "requires/sewing_operation_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_sewing_operation_entry_reponse;
        }
    }

    function fnc_sewing_operation_entry_reponse()
    {
        if (http.readyState == 4)
        {
            //alert(http.responseText);
            var reponse = trim(http.responseText).split('**');
            if (reponse[0].length > 2)
                reponse[0] = 10;
            show_msg(reponse[0]);
            document.getElementById('update_id').value = reponse[2];
            show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sewing_operation_controller', 'setFilterGrid("list_view",-1)');
            set_button_status(0, permission, 'fnc_sewing_operation_entry', 1);
            reset_form('sewingoperationentry_1', '', '');
            release_freezing();
        }
    }


    function calculate_total()
    {
        var number1 = ($('#txt_operator_smv').val());
        var number2 = ($('#txt_helper_smv').val());
        $('#txt_total_smv').val(((number1 * 1) + (number2 * 1)).toFixed(4));
    }
</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>

        <fieldset style="width:750px;">
            <legend>Sewing Operation Entry</legend>
            <form name="sewingoperationentry_1" id="sewingoperationentry_1" autocomplete="off">	
                <table width="100%">
                    <tr>
                        <td width="120" class="must_entry_caption">Operation</td>
                        <td colspan="3">
                            <input type="text" name="txt_operation" id="txt_operation" class="text_boxes" style="width:398px"/>
                        </td>
                        <td width="120" class="must_entry_caption">Rate</td>
                        <td>
                            <input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:130px;" />						
                        </td>
                    </tr>
                    <tr>
                        <td width="120" class="must_entry_caption">UOM</td>
                        <td width="150"><?
                            echo create_drop_down("cbo_uom", 142, $unit_of_measurement, '', '', '');
                            ?> 					
                        </td>
                        <td width="120" class="must_entry_caption">Resource</td>
                        <td width="150"><?
                            echo create_drop_down("cbo_resource", 142, $production_resource, '', '', '');
                            ?> 	

                        </td>
                        <td width="120">Operator SMV</td>
                        <td >
                            <input type="text" name="txt_operator_smv" id="txt_operator_smv" class="text_boxes_numeric" onKeyUp="calculate_total()" style="width:130px; " />						
                        </td>
                    </tr>
                    <tr>
                        <td>Helper SMV</td>
                        <td >
                            <input type="text" name="txt_helper_smv" id="txt_helper_smv" class="text_boxes_numeric" onKeyUp="calculate_total()" style="width:130px;"/>		
                        </td>
                        <td class="must_entry_caption"> Total SMV</td>
                        <td >
                            <input type="text" name="txt_total_smv" id="txt_total_smv" class="text_boxes_numeric" style="width:130px;" readonly />						
                        </td>
                        <td >Action</td>
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
                            echo load_submit_buttons($permission, "fnc_sewing_operation_entry", 0, 0, "reset_form('sewingoperationentry_1','','',1)");
                            ?>		
                        </td>					
                    </tr>

                    <tr>
                        <td colspan="6" height="15" align="center"></td>					
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="sewing_operation_list">						
                            <?
                            $arr = array(2 => $unit_of_measurement, 3 => $production_resource);
                            echo create_list_view("list_view", "Operation Name,Rate,UOM,Resources,Operator SMV,Helper SMV,Total SMV", "200,60,80,120,80,80", "750", "220", 1, "select operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,id from  lib_sewing_operation_entry where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,uom,resource_sewing", $arr, "operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv", "../production/requires/sewing_operation_controller", 'setFilterGrid("list_view",-1);', '0,2,0,0,2,2,2');
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
