<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create shareholder profile
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	09.03.2013
Updated by 		: 	Shajjad	
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
echo load_html_head_contents("Shareholder Profile", "../../", 1, 1,$unicode,'','');

?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<?php echo $permission; ?>';
 
function fnc_integration_variable( operation )
{
	if ( form_validation('cbo_company_id*cbo_project_name*txt_database_name*txt_server_name*txt_login_name*txt_login_password','Company Name*Project Name*Database Name*Server Name*Login Name*Login Pasword')==false )
	{
		return;
	}
	else
	{
	 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_project_name*txt_database_name*txt_server_name*txt_ip_address*txt_login_name*txt_login_password*txt_admin_mail*txt_server_id*txt_port*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/integration_variables_intercompany_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_integration_variable_reponse;
	}
}

function fnc_integration_variable_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'search_list_view','list_view_container','requires/integration_variables_intercompany_controller','setFilterGrid("list_view",-1)');
		reset_form('integration_1','','');
		set_button_status(0, permission, 'fnc_integration_variable',1);
		release_freezing();
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<center>
	<?php echo load_freeze_divs ("../../",$permission);  ?>

<form name="integration_1" id="integration_1" autocomplete="off"> 
 	<fieldset style="width:150px;height:auto;">
     	<legend>Accounts Integration</legend>    	
        <table align="center" width="100%" >
             <tr class="form_table_header">
                <th width="80">Company Name</th>
                <th width="130">Project Name
                	<input type="hidden" id="update_id" name="update_id">
                </th>
                <th width="80">Database Name</th>
                <th width="80">Server Name</th>
                <th width="80">IP Address</th>
                <th width="80">Login Name</th>
                <th width="80">Login Pasword</th>
                <th width="80">Admin Mail</th>
                <th width="80">Server ID</th>
                <th width="">Port</th>
  
            </tr>
                <td width="">
                    <? 
                    echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                    ?>
                </td>
             	<td>
                <?php 
					
                	echo create_drop_down( "cbo_project_name", 130, $integrated_project_list,"", 1, "-- Select Project --", 1, "",1 );
                ?>	 
                </td>
                <td>
                <input type="text" id="txt_database_name" name="txt_database_name" class="text_boxes" style="width:90px" />
                </td>
                <td >
                <input type="text" id="txt_server_name" name="txt_server_name" class="text_boxes"  style="width:80px"/>
                </td>
                <td >
                <input type="text" id="txt_ip_address" name="txt_ip_address" class="text_boxes"   style="width:80px"/>
                </td>
                <td >
                <input type="text" id="txt_login_name" name="txt_login_name" class="text_boxes" style="width:80px" />
                </td> 
                <td >
                <input type="text" id="txt_login_password" name="txt_login_password" class="text_boxes" style="width:80px" />
                </td>
                <td >
                <input type="text" id="txt_admin_mail" name="txt_admin_mail" class="text_boxes" style="width:80px" />
                </td>
                <td >
                <input type="text" id="txt_server_id" name="txt_server_id" class="text_boxes" style="width:80px" />
                </td>
                <td >
                <input type="text" id="txt_port" name="txt_port" class="text_boxes" style="width:80px" />
                </td>
                
                 
             <tr>
            	<td colspan="10" height="15" align="center" valign="bottom"></td>
             </tr>
            <tr>
            	<td colspan="10" height="35" class="button_container" align="center" valign="bottom">
					<?php 
					  echo load_submit_buttons( $permission, "fnc_integration_variable", 0,0 ,"reset_form('integration_1','','')",1);
					?> 
                </td>
            </tr>
            <tr>
            	<td colspan="10" height="15"  align="center" valign="bottom"></td>
            </tr>
            <tr>
            	<td colspan="10" align="center" valign="bottom" id="list_view_container">
					<?php 

                      $company_arr = return_library_array("select id, company_name from lib_company",'id','company_name'); 
					  $arr = array(0=>$company_arr,1=>$integrated_project_list);	
		echo  create_list_view ( "list_view", "Company Name,Project Name,Server Name,Database Name,User Name", "130,120,130,130,110","600","250",0, "select  company_id,project_name,server_name,database_name,login_name,id from lib_db_integration_variables", "get_php_form_data", "id", "'load_php_data_to_form'", 0,"company_id,project_name", $arr ,"company_id,project_name,server_name,database_name,login_name", "../variable/requires/integration_variables_intercompany_controller", 'setFilterGrid("list_view",-1);' );
					?> 
                </td>
            </tr>
        </table>
             
      
 	</fieldset>	  
</form>

</center>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>