<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Sewing Production Line
					Selected company will populate Location and location onchange will change Floor
					
Functionality	:	Must fill Company, Location, Floor, Line Name

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
	var permission='<? echo $permission; ?>';

function fnc_set_priviledge(  )
{
	if (form_validation('cbo_user_name*cbo_main_module','User Name*Main Module Name')==false)
	{
		return;
	}
	
	else
	{
		//eval(get_submitted_variables('cbo_user_name*cbo_main_module*cbo_set_module_privt*cbo_main_menu_name*cbo_sub_main_menu_name*cbo_sub_menu_name*cbo_visibility*cbo_insert*cbo_edit*cbo_delete*cbo_approve*update_id'));
		
		var data="action=save_update_delete"+get_submitted_data_string('cbo_user_name*cbo_main_module*cbo_set_module_privt*cbo_main_menu_name*cbo_sub_main_menu_name*cbo_sub_menu_name*cbo_visibility*cbo_insert*cbo_edit*cbo_delete*cbo_approve*update_id',"../");
		 
		///freeze_window(operation);
		 
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
		//release_freezing();
		//alert(http.responseText);
	
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		
		show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_main_module').value,'load_priv_list_view','load_list_priv','../tools/requires/user_priviledge_controller','')

		//set_button_status(0, permission, 'fnc_menu_create',1);
		release_freezing();
	
	
	
	
	}
}	
	
   
    
    </script>
</head>

<body   onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs ("../",$permission);  ?>
		<form name="userpriv_1" id="userpriv_1" autocomplete="off">
			<fieldset style="width:1030px;">
				<legend>Select user and module</legend>
				<table width="100%">
					<tr>
						<td width="150">User ID</td>
						<td width="220">
							<? 
                                echo create_drop_down( "cbo_user_name", 250, "select user_name,id from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '--- Select User ---', 0, "" );
                            ?>
							 
						</td>
						<td width="150">Main Module Name</td>
						<td>
							<? 
                                echo create_drop_down( "cbo_main_module", 250, "select main_module,m_mod_id from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/user_priviledge_controller', document.getElementById('cbo_user_name').value+'_'+this.value, 'load_priviledge_list', 'load_priviledge' )" );
                            ?>
          
						</td>
					</tr>
                    <tr> 
                    	<td colspan="4" height="20"></td>
                    </tr>
                    <tr> 
                    	<td colspan="4" id="load_priviledge"></td>
                    </tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_user_name','0','0','','0');</script>
</html>