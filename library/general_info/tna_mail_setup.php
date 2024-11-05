<?
/*-------------------------------------------- Comments
Purpose			: 	This form will be used for mail recipient group
Functionality	:					
JS Functions	:
Created by		:	Saidul Reza 
Creation date 	: 	25-2-2018
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
//---------------------------------------------------------------------------------------------------
echo load_html_head_contents("Employee Info", "../../", 1, 1,$unicode,'','');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

function openmy_user_popup(inputID,action,title)
{ 
	if( form_validation('cbo_task_type','Task Type')==false && action == 'task_popup')
	{
		return;
	}
	
	
	inputIDArr=inputID.split('*');
	var data = $("#"+inputIDArr[1]).val();
	var task_type = $("#cbo_task_type").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_mail_setup_controller.php?data='+data+'&action='+action+'&task_type='+task_type,title, 'width=600px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		freeze_window(5);
		var theemailid=this.contentDoc.getElementById("selected_id");
		var theemailval=this.contentDoc.getElementById("selected_value");
		if (theemailid.value!="" || theemailval.value!="")
		{
			$("#"+inputIDArr[1]).val(theemailid.value);
			$("#"+inputIDArr[0]).val(theemailval.value);
		}
		release_freezing();
	}
}
	


function tna_mail_setup(operation)
{
	if( form_validation('txt_user_id*cbo_task_type*txt_tna_task_id*txt_mail_type_id','User*Task Type * TNA Task* Mail Type')==false )
	{
		return;
	}
	var dataString = "txt_user_id*txt_tna_task_id*txt_mail_type_id*update_id*cbo_task_type";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	http.open("POST","requires/tna_mail_setup_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = tna_mail_setup_reponse;
}

function tna_mail_setup_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			show_msg(trim(response[0]));
			//get_php_form_data(response[1], 'tna_mail_setup_from_data', 'requires/tna_mail_setup_controller' );
			show_list_view(0,'show_listview','list_container','requires/tna_mail_setup_controller','');

			reset_form('mailrecipientgroup','','','','','');
			set_button_status(0, permission, 'tna_mail_setup',1,1);
			release_freezing();
		}
	}
}

					  
</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div style="width:500px; margin:0 auto;">
	
<fieldset style="width:450px;">
	<legend>Mail Recipient Group</legend>
    <form name="mailrecipientgroup" id="mailrecipientgroup" autocomplete = "off">	
        <table cellpadding="5" cellspacing="5" align="center">

		
			<tr>
                <td width="80" class="must_entry_caption" align="right"><strong>User </strong></td>
                <td>
                    <input type="text" name="txt_user" id="txt_user" class="text_boxes" style="width:300px" placeholder="Double Click" onDblClick="openmy_user_popup('txt_user*txt_user_id','user_popup','User List');" />
                    <input type="hidden" name="txt_user_id" id="txt_user_id" />
                </td>                
            </tr>
			<tr>
				<td align="right" class="must_entry_caption"><strong>Task Type</strong></td>
				<td>
					<? 
						echo create_drop_down( "cbo_task_type", 310, $template_type_arr,"", 1, "-- Select --",0, "",'' );		
					?>	
				</td>
			</tr>
            <tr>
                <td class="must_entry_caption" align="right"><strong>TNA Task </strong></td>
                <td>
                    <input type="text" name="txt_tna_task" id="txt_tna_task" class="text_boxes" style="width:300px" placeholder="Double Click" onDblClick="openmy_user_popup('txt_tna_task*txt_tna_task_id','task_popup','TNA Task List');" />
                    <input type="hidden" name="txt_tna_task_id" id="txt_tna_task_id" />
                </td>                
            </tr>
            <tr>
                <td class="must_entry_caption" align="right"><strong>Mail Type </strong></td>
                <td>
                    <input type="text" name="txt_mail_type" id="txt_mail_type" class="text_boxes" style="width:300px" placeholder="Double Click" onDblClick="openmy_user_popup('txt_mail_type*txt_mail_type_id','mail_type_popup','Mail Type List');" />
                    <input type="hidden" name="txt_mail_type_id" id="txt_mail_type_id" />
                </td> 
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
                <td align="center" colspan="2" class="button_container">
                    <input type="hidden" id="update_id" name="update_id" />
                    <? 
                    	echo load_submit_buttons( $permission, "tna_mail_setup", 0,0 ,"reset_form('mailrecipientgroup','','');set_button_status(0, permission, 'tna_mail_setup',1,1);",1);
                    ?>  
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</div>

<div id="list_container"></div>


</body>
    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script type="text/javascript">
	show_list_view(0,'show_listview','list_container','requires/tna_mail_setup_controller','');
	setTimeout(function(){setFilterGrid("table_body",-1)}, 500);
</script>

</html>
