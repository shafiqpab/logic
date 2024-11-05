<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Wages Rate Variables
					 
Functionality	:	 

JS Functions	:

Created by		:	CTO 
Creation date 	: 	07-10-2012
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
 echo load_html_head_contents("Wages Rate Variables", "../../", 1, 1,$unicode,'','');

?>
	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_wages_rate_var( operation )
{
   if (form_validation('txt_variable_name*cbo_variable_for','Variable Name*Variable Applicable')==false)
	{
		return;
	}
	
	else
	{
		eval(get_submitted_variables('txt_variable_name*cbo_variable_for*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_variable_name*cbo_variable_for*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/wages_rate_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_wages_rate_var_reponse;
	}
}

function fnc_wages_rate_var_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		document.getElementById('update_id').value  = reponse[2];
		show_list_view('','wages_rate_list_view','wages_rate_list','../production/requires/wages_rate_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_wages_rate_var',1);
		reset_form('wagesratevariables_1','','');
		release_freezing();
	}
}	
 </script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%">
	<? echo load_freeze_divs ("../../",$permission);  ?>
                
	<fieldset style="width:500px;">
		<legend>Wages Rate Variables</legend>
		<form name="wagesratevariables_1" id="wagesratevariables_1" autocomplete="off">	
			<table width="100%">
				<tr>
					<td width="120" class="must_entry_caption">Variable Name</td>
                    <td >
                    	<input type="text" name="txt_variable_name" id="txt_variable_name" class="text_boxes" style="width:250px" autocomplete="off" />
                    </td>
				</tr>
                <tr>
                    <td width="120" class="must_entry_caption">Variable For</td>
                    <td><? 
                                    echo create_drop_down( "cbo_variable_for", 262, $wages_rate_var_for,'', '', '', 0 );
                                ?>
                         
                    </td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <? 
                                    echo create_drop_down( "cbo_status", 262, $row_status,'', '', '', 1 );
                                ?>
                    </td>     
                </tr>
                <tr>
                    <td colspan="2" height="25" align="center">						
                        <input type="hidden" name="update_id" id="update_id" >
                        
                    </td>					
				</tr>	
                <tr>
                    <td colspan="2" align="center" class="button_container">
                       <? 
                                    echo load_submit_buttons( $permission, "fnc_wages_rate_var", 0,0 ,"reset_form('wagesratevariables_1','','',1)");
                                ?>	
                    </td>					
				</tr>
                <tr>
                    <td colspan="2" height="10" align="center">
                    </td>					
				</tr>	
                
                <tr>
                    <td colspan="2" align="center" id="wages_rate_list">						
                         <?
                                        $arr=array(1=>$wages_rate_var_for,2=>$row_status);
                                        echo  create_list_view ( "list_view", "Variable Name,Applciable For,Status", "150,200","500","220",1, "select wages_rate_variable_name,wages_rate_variable_for,id,status_active from  lib_wages_rate_variable where is_deleted=0", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,wages_rate_variable_for,status_active", $arr , "wages_rate_variable_name,wages_rate_variable_for,status_active", "../production/requires/wages_rate_controller", 'setFilterGrid("list_view",-1);' ) ;	   ?>
                    </td>					
				</tr>		
			</table>
		</form>	
	</fieldset>
	 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
