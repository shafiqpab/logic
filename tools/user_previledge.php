<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Privilege Management
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
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("User Privilege", "../", 1, '', $unicode,1,'');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission='<?=$permission; ?>';

	function fnc_set_priviledge()
	{
		freeze_window(operation);
		if (form_validation('cbo_user_name*cbo_main_module','User Name*Main Module Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete"+get_submitted_data_string('cbo_user_name*cbo_main_module*cbo_set_module_privt*cbo_main_menu_name*cbo_sub_main_menu_name*cbo_sub_menu_name*cbo_visibility*cbo_insert*cbo_edit*cbo_delete*cbo_approve*update_id',"../");
			//alert(data);//return;
			http.open("POST","requires/user_priviledge_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_set_priviledge_reponse;
		}
	}

	function fnc_set_priviledge_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			
			show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_main_module').value,'load_priv_list_view','load_list_priv','../tools/requires/user_priviledge_controller','')
			//set_button_status(0, permission, 'fnc_menu_create',1);
			release_freezing();
		}
	}	
	
	function fnc_copy_previledge(operation)
	{
		
		if (form_validation('cbo_user_name*cbo_copyuser_name','User Name*Copy To User ID')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var from_user=$('#cbo_user_name').val();
			var to_user=$('#cbo_copyuser_name').val();

			var delimiter = ',';
			var from_user_ids = from_user.split(delimiter);
			if (from_user_ids.length === 1) {
				var data="action=save_copy_previledge&operation="+operation+get_submitted_data_string('cbo_user_name*cbo_main_module*cbo_copyuser_name',"../");
				//alert (operation); alert (old_po_no); return;
				freeze_window(operation);
				http.open("POST","requires/user_priviledge_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_copy_previledge_reponse;
			}
			else
			{
				alert("Please Select Single User")
			}
		}
	}
	
	function fnc_copy_previledge_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			var to_user=$('#cbo_copyuser_name').val();
			//$('#cbo_user_name').val(to_user);
			//$('#cbo_copyuser_name').val(0);
			//show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_main_module').value,'load_priv_list_view','load_list_priv','requires/user_priviledge_controller','')
			
			release_freezing();
		}
	}
	
    </script>
</head>
<body   onLoad="set_hotkey();">
	<div align="center">
		<?=load_freeze_divs ("../",$permission);  ?>
		<form name="userpriv_1" id="userpriv_1" autocomplete="off">
			<fieldset style="width:1030px;">
				<legend>Select user and module</legend>
				<table width="100%">
					<tr>
						<td width="70">User ID</td>
						<td width="200"><?=create_drop_down("cbo_user_name", 180, "select user_name,id from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '--- Select User ---', 0, "" ); ?></td>
						<td width="120">Main Module Name</td>
						<td width="200"><?=create_drop_down("cbo_main_module", 180, "select main_module,m_mod_id from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/user_priviledge_controller', document.getElementById('cbo_user_name').value+'_'+this.value, 'load_priviledge_list', 'load_priviledge')" ); ?></td>
                        
                        <td width="100">Copy To User ID</td>
						<td  width="200"><?=create_drop_down("cbo_copyuser_name", 180, "select user_name,id from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '--Select To User--', 0, "",$onchange_func_param_db,$onchange_func_param_sttc,"","","","","","","");//combo_boxes_search ?></td>
                        <td><input type="button" name="btnPreviledgeCopy" id="btnPreviledgeCopy" class="formbutton" value="Copy Previledge for New User" onClick="fnc_copy_previledge(0);" /></td>
					</tr>
                    <tr> 
                    	<td colspan="7" height="20"></td>
                    </tr>
                    <tr> 
                    	<td colspan="7" id="load_priviledge"></td>
                    </tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_user_name*cbo_copyuser_name','0*0','0*0','','0');</script>
</html>