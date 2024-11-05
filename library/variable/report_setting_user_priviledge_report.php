<?
/*-------------------------------------------- Comments

Purpose			: 	This Form Will Create Report  Setting Previliage.
Functionality	:	 
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	15-05-2015
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
echo load_html_head_contents("User Report Setting Previliage Report", "../../", 1, 1, $unicode,'1','');
//echo load_html_head_contents("Report Settings", "../../", 1, 1,'','1','');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';

function fnc_set_priviledge(operation)
{
	
	//alert(operation);
	if (form_validation('cbo_user_name*cbo_company_id*cbo_main_module*cbo_report_name','User Name*Company*Main Module Name*Report Name')==false)
	{
		return;
	}
	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_user_name*cbo_main_module*cbo_report_name*cbo_format_name*update_id*cbo_company_id',"../../");
		///freeze_window(operation);
		 
		http.open("POST","requires/report_setting_user_priviledge_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_set_priviledge_reponse;
	}
}

function fnc_set_priviledge_reponse()
{
	if(http.readyState == 4) 
	{  
		//release_freezing();
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		document.getElementById('update_id').value  = reponse[1];
			if(reponse[0]==0)
			{
				set_button_status(1, permission, 'fnc_set_priviledge',1); 
			}
			else if(reponse[0]==10)
			{
				set_button_status(0, permission, 'fnc_set_priviledge',1); 
			}
		//if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		
		//show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_report_name').value+'_'+document.getElementById('cbo_main_module').value,'load_drop_down_report_list_view','load_list_priv','requires/report_setting_user_priviledge_report_controller','')

		//set_button_status(0, permission, 'fnc_menu_create',1);
		release_freezing();
	
	
	
	
	}
}	
	</script>
</head>

<body   onLoad="set_hotkey();">
	<div align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
		<form name="userpriv_1" id="userpriv_1" autocomplete="off">
			<fieldset style="width:824px;">
				<legend>Select user and module</legend>
				<table width="100%">
					<tr>
						<td width="150" class="must_entry_caption" >User Name</td>
						<td width="200">
                      
							<? 
                                echo create_drop_down( "cbo_user_name", 200, "select user_name,id from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '--- Select User ---', 0, "" );
                            ?>
							 
						</td>
                        <td width="150" class="must_entry_caption">Company Name</td>
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
						//load_drop_down( 'requires/report_settings_controller', this.value, 'load_drop_down_report_list_view', 'list_view_report_settings' );
						 ?>
                        </td>
						
					</tr>
                    <tr>
                    <td width="150" class="must_entry_caption">Module Name</td>
						<td width="200">
							<? 
                                echo create_drop_down( "cbo_main_module", 200, "select main_module,m_mod_id from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/report_setting_user_priviledge_report_controller', this.value , 'load_drop_down_report_module', 'report_name_td' );" );
                            ?>
          
						</td>
			<td width="150" class="must_entry_caption">Report Name</td>
            <td width="200" id="report_name_td">
					<?
                    echo create_drop_down( "cbo_report_name", 200, $report_name, 1, "-- Select Menu --", $selected, "load_drop_down( 'requires/report_setting_user_priviledge_report_controller', this.value+'_'+document.getElementById('cbo_company_id').value,'load_drop_down_report_name', 'report_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/report_setting_user_priviledge_report_controller' );" );
					//document.getElementById('cbo_main_module').value+'_'+;
                    ?>
			</td>
           
			
		</tr>
        <tr>
        <td width="150"> Print Format</td>
            <td width="200" id="report_td">
					<?
                   // echo create_drop_down( "cbo_format_name", 200, $blank_array, 1, "-- Select Menu --", $selected, "" );
					 echo create_drop_down( "cbo_format_name", 200, $blank_array,'', 1, '--- Select Format ---', 1, "","",""  );
                    ?>
			</td>
            <td width="150"></td>
            <td width=""></td>
        </tr>
        <br>
        <tr>
        <td colspan="6" height="6" align="center">  </td>
        </tr>
          <tr>
        <td colspan="6" height="6" align="center"> <input type="button" name="load_data" id="load_data" class="formbutton" value="Load Data" tabindex="10" onClick="show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_report_name').value+'_'+document.getElementById('cbo_main_module').value+'_'+document.getElementById('cbo_company_id').value,'load_drop_down_report_list_view','load_priviledge','requires/report_setting_user_priviledge_report_controller','')" /> </td>
        </tr>
         <tr>
        <td colspan="6" height="6" align="center"> </td>
        </tr>
		<tr>
			<td colspan="6" height="50" valign="middle" align="center" class="button_container">
            
              <? echo load_submit_buttons($permission, "fnc_set_priviledge", 0,0 ,"reset_form('userpriv_1','','','')",1); ?>
             
             <input type="hidden" id="update_id" />
             
             </td>
		</tr>
                    <tr> 
                    	<td colspan="6" height="20"></td>
                    </tr>
                    <tr> 
                    	<td colspan="6" id="load_priviledge2"></td>
                    </tr>
				</table>
                 <div style="width:750px; float:left; min-height:40px; margin:auto" align="center" id="load_priviledge"></div>
			</fieldset>
		</form>
	</div>
</body>
 <script>
	set_multiselect('cbo_format_name','0','0','','0');
 </script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>


</html>