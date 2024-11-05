<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA task Information","../", 1, 1, $unicode,'','');
 
?>

<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
  var permission='<? echo $permission; ?>';	
function fnc_tna_task_info( operation )
{
	if (form_validation('cbo_task_catagory*txt_task_name*txt_short_name','Task Catagory*Task Name*Short Name')==false)
	{
		return;
	}
	else
	{	
		//cbo_task_catagory,txt_task_name,txt_short_name,cbo_task_link,cbo_status
		eval(get_submitted_variables('cbo_task_catagory*txt_task_name*txt_short_name*cbo_task_link*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_task_catagory*txt_task_name*txt_short_name*cbo_task_link*cbo_status*update_id',"");
		freeze_window(operation);
		 
		http.open("POST","requires/task_library_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_task_library_info_reponse;
	}
}

function fnc_task_library_info_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		// document.getElementById('update_id').value  = reponse[2];
		 show_list_view('','search_list_view','tna_task_library_list_view','requires/task_library_controller','setFilterGrid("list_view",-1)');
		 reset_form('tnatasklibinfo_1','','');
		set_button_status(0, permission, 'fnc_tna_task_info',1);
		release_freezing();
	}
}
	</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../",$permission);  ?>
<div align="center">
     
	<fieldset style="width:850px;">
		<legend>Tna Task Info</legend>
		<form name="tnatasklibinfo_1" id="tnatasklibinfo_1">	
			<table cellpadding="0" cellspacing="2" width="100%">
			 	<tr>
					<td width="120" class="must_entry_caption">Task Catagory</td>
					<td colspan="1">
					   <?
                           echo create_drop_down( "cbo_task_catagory", 175, $party_type_supplier,"", 1, "-- Select Month --", 0, "" );
                       ?>                     
					</td>
			
                    <td width="100" class="must_entry_caption">Task Name</td>
					<td colspan="1">
						<input type="text" name="txt_task_name" id="txt_task_name" class="text_boxes" style="width:150px" maxlength="15" title="Maximum 15 Character" />						
					</td>              		
                    <td width="100" class="must_entry_caption">Short Name</td>
					<td colspan="1">
						<input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:150px" maxlength="15" title="Maximum 15 Character" />						
					</td> 
               </tr>
				<tr>
					
                    <td width="80">Task Link</td>
					<td colspan="1"> 
						<?
                           echo create_drop_down( "cbo_task_link", 175, $party_type_supplier,"", 1, "-- Select Month --", 0, "" );
                        ?>                     
                     </td>
					<td  valign="top">Status </td>
					<td valign="top">
						<?
							echo create_drop_down( "cbo_status", 165, $row_status,"", "", "", 1, "" );
						?> 
                        <input type="hidden" name="update_id" id="update_id" >
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
	<fieldset style="width:850px; margin-top:10px">
		<legend>List View</legend>
		<form>
			<div style="width:850px; margin-top:10px" id="tna_task_library_list_view" align="left">
              <?
				$arr=array (0=>$party_type_supplier,3=>$party_type_supplier,4=>$row_status); 
				echo  create_list_view ( "list_view", "Task Catagory, Task Name,Short Name,Task Link,Status", "150,150,150,200,100","850","220",0, "select task_category,task_name,task_name_short,task_link,status_active,id from tna_lib_task_details where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "task_category,0,0,task_link,status_active", $arr , "task_category,task_name,task_name_short,task_link,status_active", "requires/task_library_controller", 'setFilterGrid("list_view",-1);' ) ;
			 ?>
            </div>
		</form>
	</fieldset>	
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
