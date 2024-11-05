<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Type Entry
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
echo load_html_head_contents("Yarn Type Entry", "../../", 1, 1,$unicode,'','');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_yarn_type_entry( operation )
{
	if ( form_validation('cbo_yarn_type_id*txt_yarn_type_short_name','Yarn Type Name*Yarn Type Short Name')==false )
	{
		return;
	}	
	else
	{
		 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_yarn_type_id*txt_yarn_type_short_name*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/yarn_type_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_type_entry_reponse;
	}
}
function fnc_yarn_type_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		show_list_view('','report_settings_yarn_type','list_view_report_settings','requires/yarn_type_entry_controller','setFilterGrid("tbl_task_list",-1)');
		
		show_list_view('','yarn_type_list','list_view_yarn_type','requires/yarn_type_entry_controller','setFilterGrid("tbl_yarn_type_list",-1)');

		reset_form('tnataskentry_1','','');
		set_button_status(0, permission, 'fnc_yarn_type_entry',1);
		release_freezing();
	}
}



function fn_set_yarn_type(task_id){
	reset_form('tnataskentry_1','','');
	$('#cbo_yarn_type_id').val(task_id);
}



</script>


</head>

<body onLoad="set_hotkey()">

<div style="width:100%;">

<? echo load_freeze_divs ("../../",$permission);  ?>
 <table cellspacing="10"><tr><td align="center" valign="top">  
<fieldset style="width:620px">
	<legend>Yarn Type Entry</legend>
	<form name="tnataskentry_1" id="tnataskentry_1" method="" autocomplete="off">
        <table  cellspacing="2" cellpadding="0" border="1">
            <tr>
                <td class="must_entry_caption" align="right">Yarn Type</td>
                <td id="task_name_td">
                <input type="hidden" id="update_id" value="">
                    <? echo create_drop_down( "cbo_yarn_type_id", 213, $yarn_type_for_entry,"", 1, "-- Select General Task --", $selected, "",1 );	 ?>				
                </td> 
            </tr>
            <tr>
                <td class="must_entry_caption" align="right">Yarn Type Short Name</td>
                <td>
                    <input type="text" name="txt_yarn_type_short_name" id="txt_yarn_type_short_name" class="text_boxes" style="width:200px" maxlength="50" title="Maximum 50 Character" placeholder="Maximum 50 Character" /> 
                </td> 
            </tr>
            
             <tr>
                <td align="right">Status</td>
                <td><? echo create_drop_down( "cbo_status", 213, $row_status, 0, "", 1, "" ); ?></td>
            </tr>
             
            <tr>
                <td align="center" colspan="2" valign="middle" class="button_container">
                <?
                echo load_submit_buttons( $permission, "fnc_yarn_type_entry", 0,0 ,"reset_form('tnataskentry_1','','')",1); 
                ?>
                </td>
            </tr>
    	</table>
        
         <div id="list_view_report_settings">
             <script>
				show_list_view('','report_settings_yarn_type','list_view_report_settings','requires/yarn_type_entry_controller','setFilterGrid("tbl_task_list",-1)');
			</script>
      </div>
     </form>	
</fieldset>
</td>
<td valign="top" id="list_view_yarn_type"></td>
</tr>
</table>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 <script>
	show_list_view('','yarn_type_list','list_view_yarn_type','requires/yarn_type_entry_controller','setFilterGrid("tbl_yarn_type_list",-1)');
	
	
</script>
</html>
