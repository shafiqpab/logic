<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Tna Task Entry
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	09.02.2013
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
echo load_html_head_contents("TNA task Information", "../../", 1, 1,$unicode,'','');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_tna_task_entry( operation )
{
	if ( form_validation('cbo_task_catagory*txt_task_name*txt_short_name','Task Catagory*Task Name*Task Short Name')==false )
	{
		return;
	}	
	else
	{
		//eval(get_submitted_variables('cbo_task_catagory*txt_task_name*txt_short_name*cbo_module_name*cbo_page_link*cbo_row_status'));
		 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_task_catagory*txt_task_name*txt_short_name*cbo_module_name*cbo_page_link*txt_Penalty*cbo_row_status*update_id*txt_completion_percent*chk_task_type*txt_task_sequence',"../../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/tna_task_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_task_entry_reponse;
	}
}
function fnc_tna_task_entry_reponse()
{
	if(http.readyState == 4) 
	{
		
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		show_list_view('','report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
		reset_form('tnataskentry_1','','');
		set_button_status(0, permission, 'fnc_tna_task_entry',1);
		release_freezing();
	}
}

$(document).ready(function() {
  //  show_list_view('','report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
});
</script>
</head>

<body onLoad="set_hotkey()">
<br/>

<div align="center" style="width:100%;">

   <? echo load_freeze_divs ("../../",$permission);  ?>
   
<fieldset style="width:790px "><!--------------------------------------- Start Field Set--------------------------- -->
		<legend>TNA Task Entry</legend>
        
		<form name="tnataskentry_1" id="tnataskentry_1" method="" autocomplete="off">  <!------------------ Start Form ------------>
        
			<table width="500" cellspacing="2" cellpadding="0" border="0">
            
            	 <tr style="display:none">
					<td width="120" class="must_entry_caption">Task Category</td>
					<td colspan="1">
					<?
                    echo create_drop_down( "cbo_task_catagory", 180, $tna_task_catagory,"", 1, "-- Select Catagory--", 0, "load_drop_down( 'requires/tna_task_entry_controller', this.value, 'load_drop_down_task_category','task_name_td');" );
                    ?>                     
					</td>
				</tr>
                <tr>
                    <td width="120" class="must_entry_caption">Task Name</td>
					<td id="task_name_td" colspan="1">
						<? echo create_drop_down( "txt_task_name", 380, $tna_task_name,"", 1, "-- Select General Task --", $selected, "",0 );	 ?>				
					</td> 
                </tr>
                <tr>
                    <td width="120" class="must_entry_caption">Task Short Name</td>
					<td colspan="1">
						<input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:165px" maxlength="15" title="Maximum 15 Character" />&nbsp;&nbsp;Task Type 
                        <?
                    echo create_drop_down( "chk_task_type", 130, $lapdip_task_name,"", 1, "-- Select --", 0, "" );
                    ?>
                        
                        		 
					</td> 
                </tr>
				<tr style="display:none">
                    <td width="120">Module Name</td>
					<td colspan="1"> 
					<?
					echo create_drop_down( "cbo_module_name", 180, "select m_mod_id,main_module from main_module where status=1 order by mod_slno","m_mod_id,main_module", 1, "-- Select Module--", $selected, "load_drop_down( 'requires/tna_task_entry_controller', this.value, 'load_drop_down_page_link','page_link_td');" );
                    ?>                     
                    </td>
                 </tr>
                 <tr style="display:none">
					<td width="120">Link Page<input type="hidden" id="update_id"></td>
					<td id="page_link_td">
					<?
					echo create_drop_down( "cbo_page_link", 180, $blank_array,"", 1, "-- Select Link Page --", $selected, "",0 );	
					?> 
					</td>
				</tr>
                <tr>
					<td width="120">Penalty</td>
					<td id="">
					 <input type="text" name="txt_Penalty" id="txt_Penalty" class="text_boxes_numeric" style="width:165px" />
                     &nbsp;&nbsp;Sequence No 
                        <input type="text" name="txt_task_sequence" id="txt_task_sequence" class="text_boxes_numeric" style="width:100px" />
					</td>
				</tr>
                <tr>
					<td width="120">Completion %</td>
					<td id="">
					 <input type="text" name="txt_completion_percent" id="txt_completion_percent" class="text_boxes_numeric" value="100" style="width:165px" />
					</td>
				</tr>
                 <tr>
					<td width="120">Status</td>
					<td colspan="1">
					<?
					//echo create_drop_down( "cbo_row_status", 210, $row_status,"", 1, "-- Select status--", $selected, "",0 );	
					 echo create_drop_down( "cbo_row_status", 180, $row_status, 0, "", 1, "" ); 
					?> 
					</td>
				</tr>
                 <tr>
					<td colspan="2" height="15"></td>
					 
				</tr>
				<tr>
				  	<td align="center" colspan="9" valign="middle" class="button_container">
                    <?
					echo load_submit_buttons( $permission, "fnc_tna_task_entry", 0,0 ,"reset_form('tnataskentry_1','','')",1); 
					?>
                    </td>
                    <td>&nbsp;</td>					
			    </tr>
		</table>
        		<div style="width:790px; float:left; min-height:40px; margin:auto" align="center" id="list_view_report_settings">
                <script>
				show_list_view('','report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
				</script>
                </div>
     </form>	
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>


			<?
				/*$sql="select id, task_name, task_short_name , task_catagory, module_name, link_page,row_status from lib_tna_task where is_deleted=0";
				$module_lib=return_library_array("select m_mod_id, main_module from main_module",'m_mod_id','main_module');
				$link_lib=return_library_array("select m_menu_id,menu_name from  main_menu",'m_menu_id','menu_name');
				$arr=array(0=>$tna_task_catagory,3=>$module_lib,4=>$link_lib,5=>$row_status);
				
				echo create_list_view("list_view", "Task Catagory,Task Name,Task Short Name,Module Name,Link Page,Status", "100,100,100,100,150,100","750","240",0, $sql , "get_php_form_data", "id", "'set_update_form_data'", 1, "task_catagory,0,0,module_name,link_page,row_status", $arr, "task_catagory,task_name,task_short_name,module_name,link_page,row_status", "requires/tna_task_entry_controller",'setFilterGrid("list_view",-1)','0,0,0,0,0,0');
	*/
 			?>