<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Marchandising team who will operate order 
					level entry and marketing. here 2 form is available where 1 is creating team leader 
					and 2nd is creating team member belongs to the team.					
Functionality	:	First create team info and save then add multiple members one by one.
					select a team from list view for update.
JS Functions	:
Created by		:	Monzu 
Creation date : 	04-10-2012
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
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Marketing Team Info", "../../", 1, 1,$unicode,'','');
$lib_designation_arr=return_library_array( "select id,custom_designation from lib_designation", "id","custom_designation");
$lib_designation_arr = json_encode($lib_designation_arr);

$lib_department_arr=return_library_array( "select id,DEPARTMENT_NAME from LIB_DEPARTMENT where status_active=1 and is_deleted=0", "id","DEPARTMENT_NAME");
$lib_department_arr = json_encode($lib_department_arr);


?>
<script>

/*var department_name = [< ? echo substr(return_library_autocomplete( "select distinct(department_name) from team_member_info where status_active=1 and is_deleted=0", "department_name"  ), 0, -1); ? >];
$(function() {
				$("#txt_department_name").autocomplete({
				source: department_name
		});
});*/

var lib_department_arr=<?= $lib_department_arr;?>;

  
  if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_marchant_team_info( operation )
{
  
  if (form_validation('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_project_type','Team Name*Team Leader Name*Team Leader Designation*Project Type')==false)
	{
		return;
	}	
	else
	{
		//eval(get_submitted_variables('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info*hidden_user_id'));

    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info*txt_team_contact_no*hidden_user_id*txt_team_leader_desig_id*cbo_project_type*txt_department_name*txt_department_id',"../../");

		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/marchant_team_info_v2_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_marchant_team_info_reponse;
	}
}
function fnc_marchant_team_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		
		document.getElementById('update_id').value  = reponse[2];
		show_list_view('','marchant_team_info_list_view','team_list_view','../merchandising_details/requires/marchant_team_info_v2_controller','setFilterGrid("list_view",-1)');
		show_list_view(reponse[2], 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_v2_controller', 'setFilterGrid(\'list_view1\',-1)');

		//set_button_status(0, permission, 'fnc_marchant_team_info',1);
		set_button_status(1, permission, 'fnc_marchant_team_info',1);
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
		http.open("POST","requires/marchant_team_info_v2_controller.php",true);
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
		reset_form('marchantteaminfodet_2','','');
		
		show_list_view(document.getElementById('update_id').value, 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_v2_controller', 'setFilterGrid(\'list_view1\',-1)');
		
		show_list_view('','marchant_team_info_list_view','team_list_view','../merchandising_details/requires/marchant_team_info_v2_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_marchant_team_info_det',2);
		release_freezing();		
	}
}
/* popup for User Tag leader*/
function popup_user_tag_info_leader()
{
	//alert('kaiyum');
	var title = 'User List';	
	var page_link ='requires/marchant_team_info_v2_controller.php?action=user_tag_popup_leader';
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
		$("#txt_team_leader_desig").val(designation[ddata[3]]);
		$("#txt_team_leader_desig_id").val(ddata[3]);
		$("#txt_team_leader_email").val(ddata[5]);
		$("#txt_department_name").val(lib_department_arr[ddata[4]]);
		$("#txt_department_id").val(ddata[4]);
		
		//onClick="is_check()";
		//alert (ddata[0]);
		//get_php_form_data(ddata[0], "vehicle_info_update", "requires/marchant_team_info_controller");
		//alert(ddata[0]);return;
	}
}
/* popup for User Tag Member*/
function popup_user_tag_info_member()
{
	//alert('kaiyum');
	var title = 'User List';	
	var page_link ='requires/marchant_team_info_v2_controller.php?action=user_tag_popup_member';
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
	  <? echo load_freeze_divs ("../../",$permission);  ?>
                
       <fieldset style="width:900px;">
        <legend>Team Info </legend>
                <table cellpadding="0" cellspacing="0" width="100%" align="center"> 
                    <tr>
                        <td> 
                         <div align="center">  		
                            <form name="marchantteaminfo_1" id="marchantteaminfo_1" autocomplete="off">	
                           
                                <table align="center" cellpadding="3" cellspacing="3">
                                    <tr>
                                        <td width="90" class="must_entry_caption" align="right"> Team Name </td>                          
                                        <td>
                                          <Input name="txt_team_name" class="text_boxes" ID="txt_team_name" style="width:150px" maxlength="50" title="Maximum 50 Character" placeholder="Write">
                                        </td>
                                        <td class="must_entry_caption" align="right"> Team Leader </td>                          
                                        <td>
                                          <input type="text" name="txt_team_leader_name" id="txt_team_leader_name" placeholder="Browse" readonly class="text_boxes" onDblClick="popup_user_tag_info_leader()" style="width:150px;" />
                                          <input type="hidden" name="hidden_user_id" id="hidden_user_id" class="text_boxes" />
                                        </td>
                                        <td class="must_entry_caption" align="right">Designation</td>                          
                                        <td>
                                          <Input name="txt_team_leader_desig" class="text_boxes" ID="txt_team_leader_desig" style="width:150px" maxlength="50" title="Maximum 50 Character" readonly>
                                          <Input type="hidden" name="txt_team_leader_desig_id" class="text_boxes" ID="txt_team_leader_desig_id">
                                        </td> 

                                    </tr>
                                    <tr>
                                       <td align="right">Department Name</td>
                                       <td><Input name="txt_department_name" class="text_boxes" ID="txt_department_name" style="width:150px" readonly><Input type="hidden" name="txt_department_id" class="text_boxes" ID="txt_department_id"  readonly></td>
                                       <td class="must_entry_caption" align="right">Team Status</td>
                                       <td><?= create_drop_down( "cbo_project_type", 163, $row_status,'', '', '', 1, '', "",'','' );?></td>
                                        <td align="right" class="must_entry_caption">Project Type</td>                          
                                        <td >
                                        <? 
                                        echo create_drop_down( "cbo_project_type", 163, $project_type_arr,'', '', '', 1, '', "",'','' );
                                        ?>
                                        </td>
                                    
                                    </tr>
                                    
                                   
                                     <tr>
                                        <td align="right">Email</td>
                                        <td>
                                          <Input name="txt_team_leader_email" class="text_boxes" ID="txt_team_leader_email" style="width:150px" maxlength="50" title="Maximum 50 Character" >
                                        </td>
                                        <td align="right">Contact No.</td>                          
                                        <td >
                                        	 <Input name="txt_team_contact_no" class="text_boxes_numeric" ID="txt_team_contact_no" style="width:150px" maxlength="50" title="Maximum 50 Character">
                                        </td> 
                                        <td align="right">Team Status</td>                          
                                        <td >
                                        <? 
                                        echo create_drop_down( "cbo_team_status", 163, $row_status,'', '', '', 1, '', "",'','' );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                      <td colspan="6" align="center" height="15">
                                        <input type="hidden" name="update_id" id="update_id" value="">
                                        <input type="hidden" name="id_lib_mkt_team_member_info" id="id_lib_mkt_team_member_info" value="">
                                      </td>		 
                                    </tr>
                                    <tr>
                                        <td colspan="6" height="40" valign="bottom" align="center" class="button_container">
                                        <? 
                                        echo load_submit_buttons( $permission, "fnc_marchant_team_info", 0,0 ,"reset_form('marchantteaminfo_1*marchantteaminfodet_2','member_list_view','')",1);
                                        ?>
                                        </td>		 
                                    </tr>
                                     </table>
                                  </form>
                                  </div>
                               </td>
                           </tr>
                        
                        <tr>

                          <!-- ###################################### Member Details ################################### -->
                            <td colspan="4" align="center">
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
                            <td colspan="4" align="center" height="5">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" height="15" valign="middle" align="center" id="team_list_view">
                            <fieldset style="width:800px">
                                    <legend>Leader Info</legend>
										<?
                                        $arr=array (0=>$project_type_arr,5=>$row_status);
                                        $sql= "select project_type,team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active,id from lib_team_mst where is_deleted=0 order by team_name";
                                        echo  create_list_view ( "list_view", "Project Type,Team Name,Team Leader Name,Designation,Email,Total Member,Status", "60,110,180,100,150,55","800","220",0, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "project_type,0,0,0,0,status_active", $arr , "project_type,team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active", "../merchandising_details/requires/marchant_team_info_v2_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,1,0' ) ;
                                        
                                        ?>
                             
                             </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                       </tr>
      </table>	
</fieldset>	
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			