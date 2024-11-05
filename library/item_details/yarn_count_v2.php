<?


/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Yarn count.
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	5-01-2021
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
echo load_html_head_contents("Yarn Count Information", "../../", 1, 1,$unicode,'','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

		var permission='<? echo $permission; ?>';	
		 
function fnc_yarn_count_info( operation )
{
	if (form_validation('txt_yarn_count','Yarn Count')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_yarn_count*cbo_status*txt_sequence*update_id*cbo_count_system*cbo_number_of_filament*cbo_yarn_spinning_system',"../../");
		
		freeze_window(operation);
		 
		http.open("POST","requires/yarn_count_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_count_info_reponse;
	}
}

function fnc_yarn_count_info_reponse()
{
	if(http.readyState == 4) 
	{
		//release_freezing(); return;
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_yarn_count_info( 0 )',8000); 
		}
		else
		{
			if (reponse[0].length>2) reponse[0]=10;
			
			show_msg(reponse[0]);
			show_list_view(reponse[1],'search_list_view','yarn_count_list_view','../item_details/requires/yarn_count_controller_v2','setFilterGrid("list_view",-1)');
			reset_form('yarncountinfo_1','','');
			set_button_status(0, permission, 'fnc_yarn_count_info',1);
			release_freezing();
		}
	}
}
	</script>

</head>

<body onLoad="set_hotkey()">

<div align="center">
     <? echo load_freeze_divs ("../../", $permission);  ?>
	<fieldset style="width:650px;">
		<legend>Yarn Count Info</legend>
		<form name="yarncountinfo_1" id="yarncountinfo_1">	
			<table cellpadding="0" cellspacing="2" width="100%">
				<tr>
					<td colspan="4" align="center">
                			Yarn Code <input type="text" id="txt_yarn_code" name="txt_yarn_code" class="text_boxes" style="width:170px" value="" placeholder="Yarn Code">
                		</td>
				</tr>
			 	<tr>
					<td width="100" class="must_entry_caption">Yarn Count</td>
					<td >
						<input type="text" name="txt_yarn_count" id="txt_yarn_count" class="text_boxes" style="width:150px" maxlength="15" title="Maximum 15 Character" />						
					</td>
					 <td  valign="top">Yarn Spinning System </td>
					<td valign="top">
						
                       <?
							
							echo create_drop_down( "cbo_yarn_spinning_system", 160, $yarn_spinning_system_arr,"", 1, "--Select--", 0, "" );
						?>
					</td>
				</tr>	
				
				
				
				<tr>
					
					<td  valign="top">Count System </td>
					<td valign="top">
							
						<?
							
							echo create_drop_down( "cbo_count_system", 160, $count_system_arr,"", 1, "--Select--", 0, "" );
						?>	
					</td>
                    <td  valign="top">Number Of Filament </td>
					<td valign="top">
						
                      
                        <?
							
							echo create_drop_down( "cbo_number_of_filament", 160, $number_of_filament_arr,"", 1, "--Select--",0, "" );
						?>
					</td>
				</tr>

				

				<tr>
					
					<td  valign="top">Status </td>
					<td valign="top">
						<?
							echo create_drop_down( "cbo_status", 160, $row_status,"", "", "", 1, "" );
						?> 
                        <input type="hidden" name="update_id" id="update_id" >
					</td>
                    <td  valign="top">Sequence No. </td>
					<td valign="top">
						
                        <input type="text" name="txt_sequence" id="txt_sequence" class="text_boxes_numeric" style="width:150px" >
					</td>
				</tr>
				
				<tr>
				  <td colspan="4" align="center" class="button_container">
						<? 
							echo load_submit_buttons( $permission, "fnc_yarn_count_info",0,1 ,"reset_form('yarncountinfo_1','','',1)");
						?>
					</td>				
				</tr>
				<tr>
			  		<td height="16" colspan="4"></td>
			  	</tr>
			</table>
		</form>	
	</fieldset>
	<fieldset style="width:770px; margin-top:10px">
		<legend>List View</legend>
		<form>
			<div style="width:740px; margin-top:10px" id="yarn_count_list_view" align="left">
                            <?
							//$arr=array (1=>$yarn_color_arr,2=>$yarn_fibre_type_arr,3=>$yarn_fibre_arr,4=>$count_system_arr,5=>$number_of_filament_arr,6=>$yarn_type_for_entry,8=>$yarn_finish_arr,9=>$yarn_spinning_system_arr,10=>$row_status);
							//echo  create_list_view ( "list_view,tbl_scroll_body", "Yarn Count Name,Yarn Color,Yarn Fibre Type,Yarn Fibre,Count System,Number Of Filament,Yarn Type,Yarn Color Code,Yarn Finish,Yarn Spinning System,Status,Sequence No", "150,100,80,80,80,80,80,90,80,80,80,50","1130","220",0, "select id,yarn_count,sequence_no,status_active,yarn_spinning_system,yarn_finish,yarn_color_code,yarn_type,number_of_filament,count_system,yarn_fibre,yarn_fibre_type,yarn_color from lib_yarn_count where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,yarn_color,yarn_fibre_type,yarn_fibre,count_system,number_of_filament,yarn_type,0,yarn_finish,yarn_spinning_system,status_active,0", $arr , "yarn_count,yarn_color,yarn_fibre_type,yarn_fibre,count_system,number_of_filament,yarn_type,yarn_color_code,yarn_finish,yarn_spinning_system,status_active,sequence_no", "../item_details/requires/yarn_count_controller_v2", 'setFilterGrid("list_view",-1);' ) ;

							$arr=array (1=>$count_system_arr,2=>$number_of_filament_arr,3=>$yarn_spinning_system_arr,4=>$row_status);
							echo  create_list_view ( "list_view,tbl_scroll_body", "Yarn Count Name,Count System,Number Of Filament,Yarn Spinning System,Status,Sequence No", "150,100,80,80,80","730","220",0, "select id,yarn_count,sequence_no,status_active,yarn_spinning_system,yarn_finish,yarn_color_code,yarn_type,number_of_filament,count_system,yarn_fibre,yarn_fibre_type,yarn_color from lib_yarn_count where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,count_system,number_of_filament,yarn_spinning_system,status_active,0", $arr , "yarn_count,count_system,number_of_filament,yarn_spinning_system,status_active,sequence_no", "../item_details/requires/yarn_count_controller_v2", 'setFilterGrid("list_view",-1);' ) ;
							 ?>
            </div>
		</form>
	</fieldset>	
</div>
  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
 


$( "thead tr th" ).dblclick(function() {
  $( "#tbl_scroll_body" ).animate({scrollTop:0}, 'slow');
});
 
</script>

</html>
