<?
/* -------------------------------------------- Comments

  Purpose	: 	This form will create Sample Production Team Details

  Functionality	:

  JS Functions	:

  Created by	:	Mezbah
  Creation date : 	02-01-2017
  Updated by 	:
  Update date	:

  QC Performed BY:

  QC Date	:

  Comments	:

 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Production Team Entry", "../../", 1, 1, $unicode, '', '');

?>	

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_sample_prduction(operation)
    {
        if (form_validation('txt_teamname*cbo_location*txt_no_of_member*cbo_product_category', 'Team Name*Location*No of Members*Product Category') == false)
        {
            return;  //'txt_teamname*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv
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

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_teamname*cbo_location*txt_no_of_member*txt_team_efficiency*cbo_status*update_id*cbo_product_category*txt_style_capacity*txt_team_email', "../../");
            //alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/sample_prod_team_controller.php", true);
			//alert('checked');
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			//alert('checked');
            http.send(data);
            http.onreadystatechange = fnc_sample_prduction_reponse;
			
        }
    }

    function fnc_sample_prduction_reponse()
    {
        if (http.readyState == 4)
        {
			
            //alert(http.responseText);
            var reponse = trim(http.responseText).split('**');
            
            if (reponse[0].length > 2)
                reponse[0] = 10;
            show_msg(reponse[0]);
            show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sample_prod_team_controller', 'setFilterGrid("list_view",-1)');
            set_button_status(0, permission, 'fnc_sample_prduction', 1);
            reset_form('sewingoperationentry_1', '', '');
            release_freezing();
        }
    }


</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>

        <fieldset style="width:750px;">
            <legend>Sample Production Team Entry</legend>
            <form name="sewingoperationentry_1" id="sewingoperationentry_1" autocomplete="off">	
                <table width="100%">
                    <tr>
                        <td width="80" class="must_entry_caption" align="right">Team Name</td>
                        <td width="145">
                            <input type="text" name="txt_teamname" id="txt_teamname" class="text_boxes" style="width:142px"/>
                        </td>
                        <td width="80" class="must_entry_caption" align="right">Location</td>
                        <td width="145">
							<? echo create_drop_down("cbo_location", 142, "select id, location_name from lib_location where status_active=1", 'id,location_name', 1, '--Select Location--', "", "", 0);?> 					
                        </td>
                        <td width="100" class="must_entry_caption" align="right">Number of Members</td>
                        <td>
                            <input type="text" name="txt_no_of_member" id="txt_no_of_member" class="text_boxes_numeric" style="width:142px;" />						
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Team Efficiency</td>
                        <td>
                            <input type="text" name="txt_team_efficiency" id="txt_team_efficiency" class="text_boxes_numeric" style="width:142px;" />						
                        </td>
                        <td align="right">Status</td>
                        <td>
							<? echo create_drop_down("cbo_status", 142, $row_status, '', '', '');?> 					
                        </td>
                        <td align="right" class="must_entry_caption">Product Category</td>
                        <td>
							<? echo create_drop_down("cbo_product_category", 154, $project_type_arr, '', '1', '--Select--','','','','1,2,6');?> 					
                        </td>
                   </tr>
                   <tr>
                    	<td align="right">Style Capacity</td>
                		<td>
                           <input type="text" name="txt_style_capacity" id="txt_style_capacity" class="text_boxes_numeric" style="width:142px;" />						
                     	</td>
                    	<td align="right">Team Email</td>
                		<td colspan="3">
                           <input type="text" name="txt_team_email" id="txt_team_email" class="text_boxes" style="width:90%;" />						
                     	</td>
                   </tr>
                   <tr>
                        <td colspan="6" align="center" class="button_container">
                            <? echo load_submit_buttons($permission, "fnc_sample_prduction", 0, 0, "reset_form('sewingoperationentry_1','','',1)"); ?>
                            <input type="hidden" name="update_id" id="update_id" >		
                        </td>					
                    </tr>
                    <tr>
                        <td colspan="6" height="15" align="center"></td>					
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="sewing_operation_list">						
                            <script>
							 	show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sample_prod_team_controller', 'setFilterGrid("list_view",-1)');
							 </script>
                        </td>					
                    </tr>		
                </table>
            </form>	
        </fieldset>

    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript">//set_bangla();</script>
</html>
