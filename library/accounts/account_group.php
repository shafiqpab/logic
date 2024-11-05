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

function fnc_accounts_group( operation )
{
	if (form_validation('txt_sub_group_code*cbo_main_accounts_group*text_sub_group','Sub Group Code*Main Group*Sub Group Name')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_main_accounts_group*text_sub_group*txt_sub_group_code*cbo_statement_type*cbo_account_type*cbo_cash_flow_group*cbo_retained_earning*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_main_accounts_group*text_sub_group*txt_sub_group_code*cbo_statement_type*cbo_account_type*cbo_cash_flow_group*cbo_retained_earning*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/account_group_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_accounts_group_reponse;
	}
}

function fnc_accounts_group_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'search_list_view','subgroup_list_view','../accounts/requires/account_group_controller','setFilterGrid("list_view",-1)');
		reset_form('subgroup_1','','');
		set_button_status(0, permission, 'fnc_accounts_group',1);
		release_freezing();
	}
}
</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>

	<fieldset style="width:820px;height:auto;">
    	<legend>Sub Group</legend> 
		<form name="subgroup_1" id="subgroup_1" autocomplete="off"> 
            <table width="780px" align="center" border="0" >
                    <tr align="left">
                        <td width="120" class="must_entry_caption">Main Group </td>
                        <td width="200">
						<?php echo create_drop_down( "cbo_main_accounts_group", 215, $accounts_main_group,'', 0, '',0,0); ?>
                        </td>
                        <td width="120" align="right" class="must_entry_caption">Subgroup</td>
                        <td colspan="3"><input class="text_boxes" style="width:95%"  name="text_sub_group" id="text_sub_group" type="text" /> </td>
                     </tr>
                     <tr>
                        <td width="" class="must_entry_caption">Sub Group Code</td>
                        <td width=""><input type="text" id="txt_sub_group_code"  name="txt_sub_group_code" class="text_boxes" style="width:205px" /></td>
                        <td width="100" align="right">Statement Type</td>
                        <td width="100">
                        <?php echo create_drop_down( "cbo_statement_type", 120, $accounts_statement_type,'', 0, '',0,0); ?>
                       
                        </td>
                        <td width="80" align="right">Balance Type</td>
                        <td width="100">
                        <?php echo create_drop_down( "cbo_account_type", 95,  $accounts_account_type,'', 0, '',0,0); ?> 
                        </td>
                     </tr>
                    <tr>
                        <td>Cash Flow Group</td>
                        <td>
                        <?php echo create_drop_down( "cbo_cash_flow_group", 215, $accounts_cash_flow_group,'', 0, '',0,0); ?>  
                        </td>
                        <td align="right">Retained Earnings</td>
                        <td>
                        <?php echo create_drop_down( "cbo_retained_earning", 120, $yes_no,'', 0, '',2,0); ?>   
                        </td>
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
                        echo load_submit_buttons( $permission, "fnc_accounts_group", 0,0 ,"reset_form('subgroup_1','','',1)");
                        ?> 
                        </td>				
                    </tr>
                    
                    <tr>
                        <td colspan="6" align="center" height="15">
                           
                        </td>				
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="subgroup_list_view">
                        <?
						$arr = array(1=>$accounts_main_group,3=>$accounts_statement_type,4=>$accounts_account_type,5=>$accounts_cash_flow_group,6=>$row_status);	
						echo  create_list_view ( "list_view", "Sub Group Code,Main Group,Sub Group,Statement Type,Balance Type,Cash Flow Group,Status", "100,150,150,110,60,120,60","800","250",0, "select  sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,status_active,id from lib_account_group  where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,main_group,0,statement_type,account_type,cash_flow_group,status_active", $arr ,"sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,status_active", "../accounts/requires/account_group_controller", 'setFilterGrid("list_view",-1);' ) ;
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