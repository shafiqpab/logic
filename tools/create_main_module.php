<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("User Creation","../", 1, 1, $unicode,'','');
 
?> 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';
	
function fnc_main_module( operation )
{
	if (form_validation('txt_module_name*txt_module_seq','Module Name*Module Sequence')==false)
	{
		return;
	}
	
	else
	{
		eval(get_submitted_variables('txt_module_name*txt_module_link*txt_module_seq*cbo_module_sts*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_module_name*txt_module_link*txt_module_seq*cbo_module_sts*update_id',"../");
		 
		freeze_window(operation);
		 
		http.open("POST","requires/create_main_module_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}

function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(0,'show_module_list_view','list_view_div','../tools/requires/create_main_module_controller','setFilterGrid("list_view",-1)');

		//reset_form('user_creation_form','','');
		set_button_status(0, permission, 'fnc_main_module',1);
		release_freezing();
		
	}
}	 
</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../",$permission);  ?>
<div align="center" style="width:1000px;">
             
	<fieldset style="width:500px">
		<legend>Main Module</legend>
		<form name="mainmodule_1" id="mainmodule_1" autocomplete="off">
			<table>
				<tr>
					<td class="must_entry_caption">Main Module Name</td>
					<td>
						<input type="text" name="txt_module_name" id="txt_module_name" class="text_boxes" style="width:250px" />
					 
					</td>
				</tr>
				<tr>
					<td>Main Module Link</td>
					<td><input type="text" name="txt_module_link" id="txt_module_link" class="text_boxes" style="width:250px" /></td>
				</tr>
				<tr>
				<td>Sequence</td>
				<td>
					<div style="float:left" id="mod_seq_mod_create">
						<div style="float:left"> 
							<input type="text" name="txt_module_seq" id="txt_module_seq" class="text_boxes" onKeyDown="javascript:checkKeycode(this.event,2)" style="width:100px" />
						</div>
						
						<div style="float:left"> 
							&nbsp;Status
							<select name="cbo_module_sts" id="cbo_module_sts"   class="combo_boxes" style="width:86px" >
								<option	 value="1" >Visible</option>
								<option	 value="2" >Not visible</option>
							</select>
						</div>
					</div>
				</td>
				</tr>
                <tr>
					 
					<td align="center"  colspan="2" height="20"> 
					</td>
				</tr>
				<tr>
					 
					<td align="center"  colspan="2"><input type="hidden" value="" name="update_id" id="update_id"/>
						  <? 
					echo load_submit_buttons( $permission, "fnc_main_module", 0,0 ,"reset_form('mainmodule_1','','',1)");
				?> 
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
	
	<div style="width:1000px; float:left; margin:auto" align="center" id="list_view_div">
		<fieldset style="width:500px">
			 <? $arr=array (3=>$yes_no); 
			 echo  create_list_view ( "list_view", " Module Name,File Location,Sequence,Visiblity", "150,100,150","600","220",0, "select  main_module,file_name,mod_slno,status,m_mod_id from main_module order by mod_slno", "get_php_form_data", "m_mod_id", "'load_php_data_to_form'", 1, "0,0,0,status", $arr , "main_module,file_name,mod_slno,status", "../tools/requires/create_main_module_controller", 'setFilterGrid("list_view",-1);' ) ;  ?>
		</fieldset>
		 
	</div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>