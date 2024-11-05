<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("A/C SubGroup Info", "../../", 1, 1,$unicode,'','');

?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_journal_prefix( operation )
{
	if (form_validation('cbo_accounts_journal_type*text_pre_fix*text_starting_number','Journal Type*Pre-fix*Starting Number')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_accounts_journal_type*text_pre_fix*text_starting_number*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_accounts_journal_type*text_pre_fix*text_starting_number*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/journal_prefix_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_journal_prefix_reponse;
	}
}

function fnc_journal_prefix_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'search_list_view','jr_prefix_list_view','../accounts/requires/journal_prefix_controller','setFilterGrid("list_view",-1)');
		reset_form('journalprefix_1','','');
		set_button_status(0, permission, 'fnc_journal_prefix',1);
		release_freezing();
	}
}
</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>

	<fieldset style="width:820px;height:auto;">
    	<legend>Journal Type Prefix</legend> 
		<form name="journalprefix_1" id="journalprefix_1" autocomplete="off"> 
            <table width="650px" align="center" border="0" >
                    <tr align="left">
                        <td align="right" class="must_entry_caption">Journal Type</td>
                        <td width="200">
						<?php echo create_drop_down( "cbo_accounts_journal_type", 210, $accounts_journal_type,'', 0, '',0,0); ?>
                        </td>
                        <td align="right" class="must_entry_caption">Pre-Fix</td>
                        <td colspan="3"><input class="text_boxes" style="width:200px"  name="text_pre_fix" id="text_pre_fix" type="text" /> </td>
                     </tr>
                     <tr>
                       <td align="right" class="must_entry_caption">Starting Number</td>
                        <td width=""> <input class="text_boxes_numeric" style="width:200px"  name="text_starting_number" id="text_starting_number" type="text" /> </td>
                        <td align="right">Status</td>
                        <td>
                        <?php echo create_drop_down( "cbo_status", 95, $row_status,'', 0, '',1,0); ?> 
                        </td>
                     </tr>
                     <tr>
                         <td colspan="6" align="center">&nbsp;						
                         <input type="hidden" name="update_id" id="update_id" >
                        </td>					
                    </tr>
                    <tr>
                        <td colspan="6" align="center" class="button_container">
                        <? 
                        echo load_submit_buttons( $permission, "fnc_journal_prefix", 0,0 ,"reset_form('journalprefix_1','','',1)");
                        ?> 
                        </td>				
                    </tr>
                    <tr>
                        <td colspan="6" align="center" height="15">
                        </td>				
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="jr_prefix_list_view">
                        <?
						$arr = array(0=>$accounts_journal_type,3=>$row_status);	
						echo  create_list_view ( "list_view", "Journal Type,Pre-Fix,Starting Number,Status", "200,100,100,100","600","250",0, "select  journal_type,pre_fix,starting_number,status_active,id from lib_account_journal  where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"journal_type,0,0,status_active", $arr ,"journal_type,pre_fix,starting_number,status_active", "../accounts/requires/journal_prefix_controller", 'setFilterGrid("list_view",-1);','0,0,1,0' ) ;
						?>
                        </td>				
                    </tr>		
            </table> 
  		</form> 
	</fieldset>
    
</div>               
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>