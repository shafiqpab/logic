<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Garments Sample  List
					
Functionality	:	
				

JS Functions	:

Created by		:	Md. Rabiul Islam
Creation date 	: 	04-11-2012
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


//cbo_cost_component,txt_component_rate,cbo_status

echo load_html_head_contents("Cost Component Info", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';
function fnc_costcomponent_info( operation )
{
	if (form_validation('cbo_cost_component*txt_component_rate','Cost Component*Rate')==false)
	{
		return;
	}
	else		
	{
		eval(get_submitted_variables('cbo_cost_component*txt_component_rate*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_cost_component*txt_component_rate*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/cost_component_rate_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_costcomponent_info_reponse;
	}
}

function fnc_costcomponent_info_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		show_list_view(reponse[1],'search_list_view','cost_component_list_view','../merchandising_details/requires/cost_component_rate_controller','setFilterGrid("list_view",-1)');
		reset_form('componentinfo_1','','');
		set_button_status(0, permission, 'fnc_costcomponent_info',1);
		release_freezing();
	}
}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
     
	<fieldset style="width:500px;">
		<legend>Cost Component Info</legend>
		<form name="componentinfo_1" id="componentinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="75%">
			 	<tr>
					<td width="109" class="must_entry_caption">Cost Component</td>
					<td colspan="3">
						<?
                        echo create_drop_down( "cbo_cost_component", 228, $conversion_cost_head_array,"", "", "", 1, "" );
						?>
					</td>
				</tr>
                <tr>
					<td width="109" class="must_entry_caption">Rate</td>
					<td colspan="3">
							<input type="text" name="txt_component_rate" id="txt_component_rate" class="text_boxes" style="width:216px" maxlength="50" title="Maximum 50 Character"/>
                         
					</td>
				</tr>			
				<tr >
					<td width="109">Status</td>
					<td valign="top" width="107" colspan="3">
                    	<?
                        echo create_drop_down( "cbo_status", 110, $row_status,"", "",1, "", 1, "" );
						?> 
					</td>
					 
				</tr>	
			  	<tr>
					 <td colspan="4" align="center">&nbsp;						
						<input type="hidden" name="update_id" id="update_id" >
					</td>					
				</tr>
				<tr>
					<td colspan="4" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_costcomponent_info", 0,0 ,"reset_form('componentinfo_1','','')",1);
				        ?> 
					</td>				
				</tr>	
			</table>
		</form>	
	</fieldset>	
	<div style="width:650px;" align="center">
		<fieldset style="width:500px;">
			<legend>Cost Component List</legend>
			 
            	<table width="470" cellspacing="2" cellpadding="0" border="0">
                     
					<tr>
						<td colspan="3" id="cost_component_list_view">
							
							<?
							$arr=array (0=>$conversion_cost_head_array,2=>$row_status);
							echo  create_list_view ( "list_view", "Cost Component,Rate,Status", "200,80,120","470","220",0, "select  cost_component_name,rate,status_active,id from  lib_cost_component where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "cost_component_name,0,status_active", $arr , "cost_component_name,rate,status_active", "../merchandising_details/requires/cost_component_rate_controller", 'setFilterGrid("list_view",-1);' ) ;
							 ?>
                       
						</td>
					</tr>
				</table>
			 
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
