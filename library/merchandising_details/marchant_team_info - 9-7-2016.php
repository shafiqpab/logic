<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Marchandising team who will operate order 
					level entry and marketing. here 2 form is available where 1 is creating team leader 
					and 2nd is creating team member belongs to the team.					
Functionality	:	First create team info and save then add multiple members one by one.
					select a team from list view for update.
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	04-10-2012
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

?>
<script>

  if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_marchant_team_info( operation )
{
   if (form_validation('txt_team_name*txt_team_leader_name*txt_team_leader_desig','Team Name*Team Leader Name*Team Leader Designation')==false)
	{
		return;
	}	
	else
	{
		eval(get_submitted_variables('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info*txt_capacity_smv_leader*txt_capacity_basic_leader*txt_team_contact_no',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/marchant_team_info_controller.php", true);
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
		show_list_view('','marchant_team_info_list_view','team_list_view','../merchandising_details/requires/marchant_team_info_controller','setFilterGrid("list_view",-1)');
		show_list_view(reponse[2], 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_controller', 'setFilterGrid(\'list_view1\',-1)');

		set_button_status(0, permission, 'fnc_marchant_team_info',1);
		release_freezing();
	}
}	
function fnc_marchant_team_info_det( operation )
{
	if (form_validation('txt_member_name*txt_member_designation*update_id','Member Name*Member Designation*')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('txt_member_name*txt_member_designation*txt_member_email*cbo_team_member_status*update_id_dtl*update_id'));
		var data="action=save_update_delete_dtl&operation="+operation+get_submitted_data_string('txt_member_name*txt_member_designation*txt_member_email*cbo_team_member_status*update_id_dtl*update_id*txt_capacity_smv_member*txt_capacity_basic_member*txt_member_contact_no',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/marchant_team_info_controller.php",true);
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
		
		show_list_view(document.getElementById('update_id').value, 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_controller', 'setFilterGrid(\'list_view1\',-1)');
		
		show_list_view('','marchant_team_info_list_view','team_list_view','../merchandising_details/requires/marchant_team_info_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_marchant_team_info_det',2);
		release_freezing();		
	}
}
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
	  <? echo load_freeze_divs ("../../",$permission);  ?>
                
       <fieldset style="width:800px;">
        <legend>Team Info </legend>
                <table cellpadding="0" cellspacing="0" width="100%" align="center"> 
                    <tr>
                        <td> 
                         <div align="center">  		
                            <form name="marchantteaminfo_1" id="marchantteaminfo_1" autocomplete="off">	
                           
                                <table align="center">
                                    <tr>
                                        <td width="100" class="must_entry_caption">
                                        Team Name
                                        </td>                          
                                        <td width="200">
                                        <Input name="txt_team_name" class="text_boxes" ID="txt_team_name" style="width:200px" maxlength="50" title="Maximum 50 Character">
                                        </td>
                                        <td width="100" class="must_entry_caption">
                                        Team Leader
                                        </td>                          
                                        <td width="200">
                                        <Input name="txt_team_leader_name" class="text_boxes" ID="txt_team_leader_name" style="width:190px" maxlength="50" title="Maximum 50 Character">                                   </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Designation</td>                          
                                        <td >
                                        <Input name="txt_team_leader_desig" class="text_boxes" ID="txt_team_leader_desig" style="width:200px" maxlength="50" title="Maximum 50 Character">
                                        </td> 
                                        <td>Capacity (SMV)</td>                          
                                        <td >
                                        <Input name="txt_capacity_smv_leader" class="text_boxes_numeric" ID="txt_capacity_smv_leader" style="width:190px" maxlength="50" title="Maximum 50 Character">
                                        </td> 
                                    </tr>
                                    <tr>
                                       <td>Capacity(Basic Qty)</td>                          
                                        <td >
                                        <Input name="txt_capacity_basic_leader" class="text_boxes_numeric" ID="txt_capacity_basic_leader" style="width:200px" maxlength="50" title="Maximum 50 Character">
                                        </td>
                                        <td>Email</td>                          
                                        <td >
                                        <Input name="txt_team_leader_email" class="text_boxes" ID="txt_team_leader_email" style="width:190px" maxlength="50" title="Maximum 50 Character">
                                        </td> 
                                       
                                    </tr>
                                     <tr>
                                        <td>Team Status</td>                          
                                        <td >
                                        <? 
                                        echo create_drop_down( "cbo_team_status", 213, $row_status,'', '', '', 1, '', "",'','' );
                                        ?>
                                        </td>
                                        <td>Contact No.</td>                          
                                        <td >
                                        	 <Input name="txt_team_contact_no" class="text_boxes_numeric" ID="txt_team_contact_no" style="width:190px" maxlength="50" title="Maximum 50 Character">
                                        </td> 
                                    </tr>
                                    <tr>
                                    
                                    
                                    
                                    
                                        <td colspan="4" align="center" height="15">
                                        <input type="hidden" name="update_id" id="update_id" value="">
                                        <input type="hidden" name="id_lib_mkt_team_member_info" id="id_lib_mkt_team_member_info" value="">
                                        </td>		 
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40" valign="bottom" align="center" class="button_container">
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
                            <td colspan="4" align="center">
                                <fieldset style="width:800px;">
                                    <legend>Add Members Info</legend>
                                    <form name="marchantteaminfodet_2" id="marchantteaminfodet_2" autocomplete="off" >
                                        <table width="100%" cellpadding="0" cellspacing="0"  align="center">
                                          
                                            <tr>
                                                  <td  width="120" class="must_entry_caption">Member Name</td>
                                                <td >
                                               
                                                <input type="text" name="txt_member_name" id="txt_member_name" class="text_boxes" style="width:140px" maxlength="50" title="Maximum 50 Character">
                                                </td>
                                                <td  width="100"   class="must_entry_caption"> Designation</td>
                                                <td >
                                                <input type="text" name="txt_member_designation" id="txt_member_designation" class="text_boxes" style="width:140px" maxlength="50" title="Maximum 50 Character">
                                                  </td>
                                                  <td align="" width="100" > Capacity(SMV)</td>
                                                  <td align="">
                                                 <input type="text" name="txt_capacity_smv_member" id="txt_capacity_smv_member" class="text_boxes_numeric" style="width:120px" maxlength="50" title="Maximum 50 Character">
                                                    </td>
                                                </tr>
                                                <tr>
                                                
                                               
                                                    <td  width=""> Capacity(Basic Qty)</td>
                                                    <td >
                                                      <input type="text" name="txt_capacity_basic_member" id="txt_capacity_basic_member" class="text_boxes_numeric" style="width:140px" maxlength="50" title="Maximum 50 Character">								
                                                   </td>
                                                    <td align="" >Email</td>
                                                   <td align="">
                                                 <input type="text" name="txt_member_email" id="txt_member_email" class="text_boxes" style="width:140px" maxlength="50" title="Maximum 50 Character">
                                                    </td>
                                               
                                                    <td  width="">  Status</td>
                                                    <td >
                                                        <? 
                                                        echo create_drop_down( "cbo_team_member_status", 130, $row_status,'', '', '', 1, '', "",'','' );
                                                        ?>								
                                                   </td>
                                            </tr>
                                                <tr>
                                                	<td  width="">Contact No.</td>
                                                    <td >
                                                      <input type="text" name="txt_member_contact_no" id="txt_member_contact_no" class="text_boxes_numeric" style="width:140px" maxlength="50" title="Maximum 50 Character">								
                                                   </td>
                                                   <td align="" ></td>
                                                   <td align="">
                                                    </td>
                                                    <td  width="">  </td>
                                                    <td >
                                                   </td>
                                                </tr>
                                              <tr>
                                                  
                                            </tr>
                                            
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
        $arr=array (5=>$row_status);
      //$sql= "select a.id,a.team_name,a.team_leader_name,a.status_active,count(b.team_id) as team from lib_marketing_team a, lib_mkt_team_member_info b  where a.id=b.team_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.team_name, a.team_leader_name order by a.id";
       $sql= "select team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active,id from lib_marketing_team where is_deleted=0 order by team_name";
      echo  create_list_view ( "list_view", "Team Name,Team Leader Name,Designation,Email,Total Member,Status", "150,200,100,150,55","800","220",0, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,0,status_active", $arr , "team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active", "../merchandising_details/requires/marchant_team_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,1,0' ) ;
                             
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
		
			