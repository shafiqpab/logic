<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing & Finishing Bill & AOP Variable Settings
					Select company and select Variable List that onchange will change content
					
Functionality	:	Must fill Company, Variable List
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	09-08-2014
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
echo load_html_head_contents("Subcontract Variable Settings", "../../", 1, 1,$unicode,'','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_subcontract_variable_settings(operation)
	{

	// if(operation==1)
	// {
	// 	alert("Update Is Restricted in Variable Settings");
	// 	return;
	// }
	// if(operation==2)
	// {
	// 	alert("Delete Is Restricted in Variable Settings");
	// 	return;
	// }
		// if(operation==2)
		// {
		// 	show_msg('13');
		// 	return;
		// }
		if ( form_validation('cbo_company_id*cbo_variable_list','Company Name*Variable List')==0 )
		{
			return;
		}
		else
		{
			var variable_list=$('#cbo_variable_list').val();
		//	alert(variable_list);
			 if(variable_list==3)
			{
				eval(get_submitted_variables('cbo_company_id*cbo_variable_list*cbo_bill_on*cbo_yes_1*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_variable_list*cbo_bill_on*cbo_yes_1*update_id',"../../");
			}
			else if(variable_list==16)
			{
				//nocache = Math.random();
				//var profit_calculative_td	= escape(document.getElementById('profit_calculative_td').innerHTML);
				eval(get_submitted_variables('cbo_company_id*cbo_variable_list*cbo_bill_on*update_id'));
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_variable_list*cbo_bill_on*txt_excess_per*update_id',"../../");
			}
			else if(variable_list==18)
			{
				eval(get_submitted_variables('cbo_company_id*cbo_variable_list*cbo_bill_on_1*cbo_bill_on_2*cbo_yes_1*cbo_yes_2*update_id_1*update_id_2'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_variable_list*cbo_bill_on_1*cbo_bill_on_2*cbo_yes_1*cbo_yes_2*update_id_1*update_id_2',"../../");
			}
			
			else
			{
				//alert(1);
			//	nocache = Math.random();
				//var bill_on	= escape(document.getElementById('bill_on').innerHTML);
				eval(get_submitted_variables('cbo_company_id*cbo_variable_list*cbo_bill_on*update_id'));
				//alert(2);
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_variable_list*cbo_bill_on*update_id',"../../");
				//alert(3);
			}
			
			//freeze_window(operation);
			http.open("POST","requires/subcontract_variable_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_subcontract_variable_settings_reponse;
		}
	 }

	function fnc_subcontract_variable_settings_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			if(reponse[2]==18)
			{
				set_button_status(1, permission, 'fnc_subcontract_variable_settings',1);
				show_list_view(reponse[2]+'_'+document.getElementById('cbo_company_id').value,'on_change_data','variable_settings_container','requires/subcontract_variable_controller','')
			}
			else
			{
			document.getElementById('update_id').value  = reponse[2];
			}
			set_button_status(0, permission, 'fnc_subcontract_variable_settings',1);
			reset_form('subcontractVariable','variable_settings_container','');
			release_freezing();
		}
	}	

	function set_multi_function(id)
	{
		var data = id.split('_')
		if (data[0]==4) 
		{
			set_multiselect('cbo_bill_on','0','0','','0');
			var response = return_global_ajax_value( id, 'eval_multi_select', '', 'requires/subcontract_variable_controller');
			var response= trim(response);
			if(response != "EMPTY")
			{	
				set_multiselect('cbo_bill_on','0','1',response,'0');
			}
		}
	}
	
	function fnc_excessPer(val)
	{
		if(val==1)
		{
			$('#txt_excess_per').val(0);
			$('#txt_excess_per').attr('disabled',false);
		}
		else
		{
			$('#txt_excess_per').val(0);
			$('#txt_excess_per').attr('disabled',true);
		}
	}
	function fnc_editible(val)
	{
		if(val==5)
		{
			$("#td_show").show();
		}
		else{
			$("#td_show").hide();
		}
	}
</script>
</head>
<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:850px;">
            <legend>Subcontract Variable Settings</legend>
            <form name="subcontractVariable" id="subcontractVariable" >
                <table  width="850px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td width="150" align="center" class="must_entry_caption">Company</td>
                        <td width="300">
							<? 
								echo create_drop_down( "cbo_company_id", 250, "select company_name,id from lib_company where is_deleted=0 and status_active=1 $company_name order by company_name",'id,company_name', 1, '---Select Company---', 0, "" );
                            ?>
                        </td> 
                        <td width="150" align="center" class="must_entry_caption">Variable List</td>
                        <td width="300">
							<? 
								echo create_drop_down( "cbo_variable_list", 250, $subcon_variable,'', '1', '---Select---', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_id').value,'on_change_data','variable_settings_container','requires/subcontract_variable_controller',''); set_multi_function(this.value+'_'+document.getElementById('cbo_company_id').value);fnc_editible(document.getElementById('cbo_bill_on').value)",''); 
                            ?>
                        </td>
                    </tr>
                </table>
                 <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>
            </form>
        </fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
