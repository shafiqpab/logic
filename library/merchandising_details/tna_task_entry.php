<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Tna Task Entry
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	09.02.2013
Updated by 		:   Md. Saidul Islam Reza		
Update date		: 	20-12-2017	   
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
	if ( form_validation('txt_task_name*txt_short_name*txt_task_group','Task Name*Task Short Name*Task Group')==false )
	{
		return;
	}	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_task_type*cbo_task_catagory*txt_task_name*txt_short_name*cbo_module_name*cbo_page_link*txt_Penalty*cbo_row_status*update_id*txt_completion_percent*txt_task_sequence*txt_task_group*txt_group_sequence',"../../");
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
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		reset_form('tnataskentry_1','','','','','cbo_task_type');
		show_list_view($('#cbo_task_type').val(),'tna_task_list','list_view_task','requires/tna_task_entry_controller','setFilterGrid("tbl_tna_task_list",-1)');
		show_list_view($('#cbo_task_type').val(),'report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
		
		set_button_status(0, permission, 'fnc_tna_task_entry',1);
		release_freezing();
	}
}

$(document).ready(function() {
  //  show_list_view('','report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
});



// autocomplete brand script-------
var str_group = [<? echo substr(return_library_autocomplete( "select distinct(task_group) from lib_tna_task", "task_group"  ), 0, -1); ?>];
$(function() {
				var group_name = str_group;
				$("#txt_task_group").autocomplete({
				source: group_name 
		});
});


	function set_task(task_id,task_type){
		reset_form('tnataskentry_1','','');
		$('#txt_task_name').val(task_id);
		$('#cbo_task_type').val(task_type);
		//document.getElementById('cbo_task_type').disabled = false;
	}


function getTaskList(type){
	
	show_list_view(type,'tna_task_list','list_view_task','requires/tna_task_entry_controller','setFilterGrid("tbl_tna_task_list",-1)');
	reset_form('tnataskentry_1','','');
	$('#cbo_task_type').val(type);
	show_list_view(type,'report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');

}


</script>


</head>

<body onLoad="set_hotkey()">

<div style="width:100%;">

<? echo load_freeze_divs ("../../",$permission);  ?>
 <table cellspacing="10"><tr><td align="center" valign="top">  
<fieldset style="width:690px">
	<legend>TNA Task Entry</legend>
	<form name="tnataskentry_1" id="tnataskentry_1" method="" autocomplete="off">
        <table width="500" cellspacing="2" cellpadding="0" border="1">
             <tr>
                <td width="100">Task Type</td>
                <td colspan="3">
                <?
                	echo create_drop_down( "cbo_task_type", 180, $template_type_arr,"", 1, "-- Select --",1, "getTaskList(this.value);",'' );
                ?>                     
                </td>
            </tr>
            <tr>
                <td class="must_entry_caption">Task Name</td>
                <td id="task_name_td" colspan="3">
                    <? echo create_drop_down( "txt_task_name", 380, $tna_task_name,"", 1, "-- Select General Task --", $selected, "",1 );	 ?>				
                </td> 
            </tr>
            <tr>
                <td class="must_entry_caption">Task Short Name</td>
                <td colspan="3">
                    <input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:370px" maxlength="50" title="Maximum 50 Character" placeholder="Maximum 50 Character" /> 
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
                <td width="120">Link Page
                <input type="hidden" id="cbo_task_catagory" value="0">
                <input type="hidden" id="update_id">
                </td>
                <td id="page_link_td">
                <?
                echo create_drop_down( "cbo_page_link", 180, $blank_array,"", 1, "-- Select Link Page --", $selected, "",0 );	
                ?>
                </td>
            </tr>
            <tr>
                <td>Penalty</td>
                <td>
                 <input type="text" name="txt_Penalty" id="txt_Penalty" class="text_boxes_numeric" style="width:165px" />
                </td>
                <td>Sequence No</td>
                <td>
                 <input type="text" name="txt_task_sequence" id="txt_task_sequence" class="text_boxes_numeric" style="width:100px" />
                </td>
            </tr>
            <tr>
                <td>Completion %</td>
                <td id="">
                 <input type="text" name="txt_completion_percent" id="txt_completion_percent" class="text_boxes_numeric" value="100" style="width:165px" /></td>
                <td class="must_entry_caption">Group Name</td>
                <td><input type="text" name="txt_task_group" id="txt_task_group" class="text_boxes" style="width:100px" />
                </td>
            </tr>
             <tr>
                <td>Status</td>
                <td>
                <?
                 echo create_drop_down( "cbo_row_status", 180, $row_status, 0, "", 1, "" ); 
                ?></td>
                <td>Group Seq No</td>
                <td><input type="text" name="txt_group_sequence" id="txt_group_sequence" class="text_boxes_numeric" style="width:100px" />
                </td>
            </tr>
             <tr>
                <td colspan="4" height="15"></td>
            </tr>
            <tr>
                <td align="center" colspan="4" valign="middle" class="button_container">
                <?
                echo load_submit_buttons( $permission, "fnc_tna_task_entry", 0,0 ,"reset_form('tnataskentry_1','','')",1); 
                ?>
                </td>
                <td>&nbsp;</td>					
            </tr>
    	</table>
         <div id="list_view_report_settings"></div>
     </form>	
</fieldset>
</td>
<td valign="top" id="list_view_task"></td>
</tr>
</table>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 <script>
	show_list_view('1','tna_task_list','list_view_task','requires/tna_task_entry_controller','setFilterGrid("tbl_tna_task_list",-1)');
	
	show_list_view('1','report_settings_tna_task','list_view_report_settings','requires/tna_task_entry_controller','setFilterGrid("tbl_task_list",-1)');
	
</script>
</html>
