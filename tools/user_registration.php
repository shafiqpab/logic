<?php

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("User PC Registration Information", "../", 1, 1,$unicode,1,'');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function fnc_user_pc_registration( operation )
	{
		if (operation == 2)
		{
			alert("Data is not Deleted !!");return;	
		}	
		if (form_validation('txt_user_ip*txt_user_mac*txt_executive_user_id*txt_executive_user_password','User IP*User MAC*User ID*User Password')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_user_ip*txt_user_mac*txt_executive_user_id*txt_executive_user_password',"../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/user_registration_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_user_pc_registration_response;
		}
	}

	function fnc_user_pc_registration_response()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			set_button_status(1, permission, 'fnc_user_pc_registration',1);	
			release_freezing();
			if (response[0] == 0)
			{
				show_msg('0');
			}
			if (response[0] == 1)
			{
				show_msg('1');
			}		
			if (response[1] == 'OK')
			{				
				var user_ip 	= document.getElementById('txt_user_ip').value;
				var user_mac 	= document.getElementById('txt_user_mac').value;
				window.localStorage.setItem('user_ip', user_ip);
				window.localStorage.setItem('user_mac', user_mac);
			}
			elseif (response[1] == 'Not OK')
			{ 
				alert('User ID Not Matched !!!');return;
			}
		}
	}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../",$permission);  ?>
<div align="center" style="width:100%;">
	<fieldset style="width:500px;">
		<legend>User PC Registration</legend>
		<form name="userPcRegistration" id="userPcRegistration">
			<table cellpadding="0" cellspacing="2" width="75%">
                <tr>
					<td width="100" class="must_entry_caption">User IP</td>
					<td width="150">
						<input type="text" name="txt_user_ip" id="txt_user_ip" class="text_boxes" style="width:150px" />
					</td>
				</tr>
				<tr>
					<td width="100" class="must_entry_caption">User MAC</td>
					<td width="150">
						<input type="text" name="txt_user_mac" id="txt_user_mac" class="text_boxes" style="width:150px" />
					</td>
				</tr>
				<tr>
					<td width="100" class="must_entry_caption">User ID</td>
					<td width="150">
						<input type="text" name="txt_executive_user_id" id="txt_executive_user_id" class="text_boxes" style="width:150px;" />
					</td>
				</tr>
				<tr>
					<td width="100" class="must_entry_caption">User Password</td>
					<td width="150">
						<input type="password" name="txt_executive_user_password" id="txt_executive_user_password" class="text_boxes" style="width:150px;" autocomplete="new-password" />
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_user_pc_registration", 0,0 ,"reset_form('userPcRegistration','','')",1);
				        ?>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
