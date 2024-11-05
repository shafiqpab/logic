<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA Process","../", 1, 1, $unicode,'','');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';	

function fnc_tna_task_info( operation )
{
	 
		//cbo_task_catagory,txt_task_name,txt_short_name,cbo_task_link,cbo_status
		//eval(get_submitted_variables('cbo_task_catagory*txt_task_name*txt_short_name*cbo_task_link*cbo_status*update_id'));
		var data="action=tna_process&operation="+operation; //+get_submitted_data_string('cbo_task_catagory*txt_task_name*txt_short_name*cbo_task_link*cbo_status*update_id',"");
		//freeze_window(operation);
		 
		http.open("POST","requires/tna_process_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_task_library_info_reponse;
	 
}

function fnc_task_library_info_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		alert(http.responseText);
		if (reponse[0].length>2) reponse[0]=10;
		//show_msg(reponse[0]);
		// document.getElementById('update_id').value  = reponse[2];
		// show_list_view('','search_list_view','tna_task_library_list_view','requires/task_library_controller','setFilterGrid("list_view",-1)');
		// reset_form('tnatasklibinfo_1','','');
		//set_button_status(0, permission, 'fnc_tna_task_info',1);
		//release_freezing();
	}
}
	</script>

</head>

<body onLoad="set_hotkey()">

<div align="center">
     
    <? echo load_freeze_divs ("../",$permission);  ?>
	<fieldset style="width:850px;">
		<legend>Tna Process</legend>
		<form name="tnaprocess_1" id="tnaprocess_1">	
			<table cellpadding="0" cellspacing="2" width="100%">
			 	<tr>
					<td width="120"><a href="##" onClick="fnc_tna_task_info(0)"> Task Catagory</a></td>
					<td colspan="1">
					   <?
                           echo create_drop_down( "cbo_task_catagory", 175, $party_type_supplier,"", 1, "-- Select Month --", 0, "" );
                       ?>                     
					</td>
			
                    <td width="100">Task Name</td>
					<td colspan="1">
						<input type="text" name="txt_task_name" id="txt_task_name" class="text_boxes" style="width:150px" maxlength="15" title="Maximum 15 Character" />						
					</td>              		
                    <td width="100">Short Name</td>
					<td colspan="1">
						<input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:150px" maxlength="15" title="Maximum 15 Character" />						
					</td> 
               </tr>
				 
				<tr>
				  <td colspan="6" align="center" class="button_container">
						<? 
							echo load_submit_buttons( $permission, "fnc_tna_task_info", 0,0 ,"reset_form('tnatasklibinfo_1','','')",1);
						?>
			     </td>				
				</tr>
				<tr>
			  		<td height="16" colspan="4"></td>
			  	</tr>
			</table>
		</form>	
	</fieldset>
		
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
