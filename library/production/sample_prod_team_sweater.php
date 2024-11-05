<?
/* -------------------------------------------- Comments

  Purpose	: 	This form will create Sample Production Team Details

  Functionality	:

  JS Functions	:

  Created by	:	Shajib Jaman 
  Creation date : 	015-010-2022
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
$lib_designation_arr=return_library_array( "select id,custom_designation from lib_designation", "id","custom_designation");
$lib_designation_arr = json_encode($lib_designation_arr);

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

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_teamname*cbo_location*txt_no_of_member*txt_team_efficiency*cbo_status*update_id*cbo_product_category*txt_style_capacity*txt_team_email*hidden_user_id*txt_team_leader_name', "../../");
            //alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/sample_prod_team_sweater_controller.php", true);
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
            show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid("list_view",-1)');

            show_list_view(reponse[2], 'marchant_team_info_det_list_view', 'member_list_view', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid(\'list_view1\',-1)');

            set_button_status(0, permission, 'fnc_sample_prduction', 1);
            reset_form('sewingoperationentry_1', '', '');
            release_freezing();
        }
    }

    function fnc_marchant_team_info_det( operation )
{
	if (form_validation('update_id*txt_member_name*txt_member_designation','Team Name*Member Name*Member Designation')==false)
	{
		return;
	}
	else
	{
		//eval(get_submitted_variables('txt_member_name*txt_member_designation*txt_member_email*cbo_team_member_status*update_id_dtl*update_id*hidden_user_id_member'));
		
    var data="action=save_update_delete_dtl&operation="+operation+get_submitted_data_string('txt_member_name*txt_member_designation*txt_member_email*cbo_team_member_status*update_id_dtl*update_id*txt_member_contact_no*hidden_user_id_member*txt_member_designation_id',"../../");

		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/sample_prod_team_sweater_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_marchant_team_info_det_response;
	}
}

function fnc_marchant_team_info_det_response()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
        
		show_msg(reponse[0]);
        release_freezing();	
		reset_form('marchantteaminfodet_2','','');
       	
		
		show_list_view(document.getElementById('update_id').value, 'marchant_team_info_det_list_view', 'member_list_view', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid(\'list_view1\',-1)');
		
        show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_marchant_team_info_det',2);
		release_freezing();		
	}
}
    function popup_user_tag_info_leader()
{
	//alert('kaiyum');
	var title = 'User List';	
	var page_link ='requires/sample_prod_team_sweater_controller.php?action=user_tag_popup_leader';
	var popup_width="600px";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var data=this.contentDoc.getElementById("hidden_selected_usertag_popup_id_leader").value;	
		var ddata=data.split("_");
       
		//reset_form('pickdropinterruptionform_1','','');
		document.getElementById('hidden_user_id').value = ddata[0];
		document.getElementById('txt_team_leader_name').value = ddata[2];

		var designation =JSON.parse('<? echo $lib_designation_arr; ?>');
		
		$("#txt_team_email").val(ddata[5]);
		$("#txt_department_name").val(lib_department_arr[ddata[4]]);
		
		
		//onClick="is_check()";
		//alert (ddata[0]);
		//get_php_form_data(ddata[0], "vehicle_info_update", "requires/marchant_team_info_controller");
		//alert(ddata[0]);return;
	}
}
function popup_user_tag_info_member()
{
	//alert('kaiyum');
	var title = 'User List';	
	var page_link ='requires/sample_prod_team_sweater_controller.php?action=user_tag_popup_member';
	var popup_width="600px";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var data=this.contentDoc.getElementById("hidden_selected_usertag_popup_id_member").value;	
		var ddata=data.split("_");
		//reset_form('pickdropinterruptionform_1','','');
		document.getElementById('hidden_user_id_member').value = ddata[0];
		document.getElementById('txt_member_name').value = ddata[2];

    var designation =JSON.parse('<? echo $lib_designation_arr; ?>');
    $("#txt_member_designation").val(designation[ddata[3]]);
    $("#txt_member_designation_id").val(ddata[3]);
    $("#txt_member_email").val(ddata[5]);

		//onClick="is_check()";
		//alert (ddata[0]);
		//get_php_form_data(ddata[0], "vehicle_info_update", "requires/marchant_team_info_controller");
		//alert(ddata[0]);return;
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
                        <td class="must_entry_caption" align="right"> Team Leader </td>                          
                                        <td>
                                          <input type="text" name="txt_team_leader_name" id="txt_team_leader_name" placeholder="Browse" readonly class="text_boxes" onDblClick="popup_user_tag_info_leader()" style="width:150px;" />
                                          <input type="hidden" name="hidden_user_id" id="hidden_user_id" class="text_boxes" />
                                        </td>
                       
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption" align="right">Number of Members</td>
                        <td>
                            <input type="text" name="txt_no_of_member" id="txt_no_of_member" class="text_boxes_numeric" style="width:142px;" />						
                        </td>
                        <td align="right">Team Efficiency</td>
                        <td>
                            <input type="text" name="txt_team_efficiency" id="txt_team_efficiency" class="text_boxes_numeric" style="width:142px;" />						
                        </td>
                        <td align="right">Status</td>
                        <td>
							<? echo create_drop_down("cbo_status", 142, $row_status, '', '', '');?> 					
                        </td>
                        
                   </tr>
                   <tr>
                        <td align="right" class="must_entry_caption">Product Category</td>
                        <td>
							<? echo create_drop_down("cbo_product_category", 154, $project_type_arr, '', '1', '--Select--','','','','6');?> 					
                        </td>
                    	<td align="right">Style Capacity</td>
                		<td>
                           <input type="text" name="txt_style_capacity" id="txt_style_capacity" class="text_boxes_numeric" style="width:142px;" />						
                     	</td>
                    	<td align="right">Team Email</td>
                		<td>
                           <input type="text" name="txt_team_email" id="txt_team_email" class="text_boxes" style="width:150px;" />						
                     	</td>
                   </tr>
                   <tr>
                        <td colspan="6" align="center" class="button_container">
                            <? echo load_submit_buttons($permission, "fnc_sample_prduction", 0, 0, "reset_form('sewingoperationentry_1*marchantteaminfodet_2','member_list_view','',1)"); ?>
                            <input type="hidden" name="update_id" id="update_id" >		
                        </td>					
                    </tr>
        <tr>
       
  <!-- ###################################### Member Details ################################### -->
            <td colspan="6" align="center">
                <fieldset style="width:800px;">
                    <legend>Add Members Info</legend>
                    <form name="marchantteaminfodet_2" id="marchantteaminfodet_2" autocomplete="off" >
                        <table width="100%" cellpadding="2" cellspacing="2"  align="center">
                            
                            <tr>
                                <td  width="120" class="must_entry_caption" align="right">Member Name</td>
                                <td >
                                

                                    <input type="text" name="txt_member_name" id="txt_member_name" placeholder="Browse" readonly class="text_boxes" onDblClick="popup_user_tag_info_member()" style="width:140px;" />
                                    <input type="hidden" class="text_boxes" name="hidden_user_id_member" id="hidden_user_id_member"/>
                                </td>
                                <td  width="100"   class="must_entry_caption" align="right"> Designation</td>
                                <td >
                                    <input type="text" name="txt_member_designation" id="txt_member_designation" class="text_boxes" style="width:140px" maxlength="50" title="Maximum 50 Character" readonly>
                                    <input type="hidden" name="txt_member_designation_id" id="txt_member_designation_id">
                                </td>

                                <td align="right">Email</td>
                                <td align="">
                                    <input type="text" name="txt_member_email" id="txt_member_email" class="text_boxes" style="width:140px" maxlength="50" title="Maximum 50 Character">
                                </td>


                            </tr>

                            <tr>
                                    

                                <td  width="" align="right">  Status</td>
                                <td >
                                    <? 
                                    echo create_drop_down( "cbo_team_member_status", 150, $row_status,'', '', '', 1, '', "",'','' );
                                    ?>								
                                </td>
                                <td  width="" align="right">Contact No.</td>
                                    <td >
                                        <input type="text" name="txt_member_contact_no" id="txt_member_contact_no" class="text_boxes_numeric" style="width:140px" maxlength="50" title="Maximum 50 Character">                
                                </td>
                                <td  width="">  </td>
                                <td > </td>
                            </tr>
                            

                            <tr> </tr>
                            <tr>
                                <td colspan="6" align="center" height="15">
                                <input type="hidden" name="update_id_dtl" id="update_id_dtl" value="">	
                                </td>		 
                            </tr>
                            <tr>
                                <td colspan="6"  height="40" valign="bottom" align="center" class="button_container">
                                <? 
                                echo load_submit_buttons( $permission, "fnc_marchant_team_info_det", 0,0 ,"reset_form('marchantteaminfodet_2','','')",2);
                                ?>						
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" id="member_list_view" align="center">
                                <?

                            
                                ?>
                                                                
                                </td>
                            </tr>
              </table>
          </form>
      </fieldset>
  </td>
</tr>
                    <tr>
                        <td colspan="6" height="15" align="center"></td>					
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="sewing_operation_list">						
                            <script>
							 	show_list_view('', 'sewing_operation_list_view', 'sewing_operation_list', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid("list_view",-1)');
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
