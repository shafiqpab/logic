<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("User Creation","../", $filter, 1, $unicode,'','');
 
?>
<script language="javascript">
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	
function fnc_change_password( operation )
{
	if (form_validation('txt_user_id*txt_old_passwd*txt_new_passwd*txt_conf_passwd','User Name*Old Password*New Password*Confirm Password')==false)
	{
		return;
	}
	else if (trim(document.getElementById('txt_new_passwd').value)!=trim(document.getElementById('txt_conf_passwd').value))	
	{
		$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
		 { 
			$(this).html('Password and Confirm Password Should be Same.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 document.getElementById('txt_conf_passwd').focus();
		 });
	}
	else
	{
		eval(get_submitted_variables('txt_user_id*txt_old_passwd*txt_new_passwd*txt_conf_passwd'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_user_id*txt_old_passwd*txt_new_passwd*txt_conf_passwd',"../");
		 
		freeze_window(operation);
		 
		http.open("POST","requires/change_password_controller.php",true);
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
		//reset_form('user_creation_form','','');
		set_button_status(1, permission, 'fnc_change_password',1);
		release_freezing();
		
	}
}
</script>
</head>
 

<body onLoad="set_hotkey()">
 <? echo load_freeze_divs ("../",$permission);  ?>
	<div align="center">
		<div style="width:500px; height:60px" align="center">
           
        </div>
		<form name="changepasswd_1" id="changepasswd_1" autocomplete="off">
			<fieldset style="width:500px;">
				<legend>Change Password</legend>
				<div style="width:100%; float:left;" align="center">
					<table>
						<tr>
							<td>User ID</td>
							<td>
								<input type="text" name="txt_user_id" id="txt_user_id"  readonly  class="text_boxes" style="width:210px;"  value="<? echo $_SESSION['logic_erp']["user_name"]; ?>"/>
								 
							</td>
						</tr>
						<tr>
							<td>Old Password</td>
							<td>
								<input type="password" name="txt_old_passwd" onKeyUp="" id="txt_old_passwd" class="text_boxes" style="width:210px; height:20px" />
								 
							</td>
						</tr>
						<tr>
							<td>New Password</td>
							<td>
								
									<input type="password" name="txt_new_passwd" id="txt_new_passwd" class="text_boxes" style="width:210px; height:20px" />
									 
								
							</td>
						</tr>
						<tr>
							<td>Confirm Password</td>
							<td valign="top">
								
									<input type="password" name="txt_conf_passwd" id="txt_conf_passwd" class="text_boxes" style="width:210px; height:20px" />
									 
							</td>
						</tr> 
						<tr>
							 
							<td colspan="2" align="center" style="padding-top:10px;">
                            	  <? echo load_submit_buttons( $permission, "fnc_change_password", 1,0 ,"reset_form('changepasswd_1','','')",1) ; ?>
							</td>
						</tr>
					</table>
				</div>
			</fieldset>	
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>