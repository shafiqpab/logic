<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Order Tracking Variable Settings
					Select company and select Variable List that onchange will change content

Functionality	:	Must fill Company, Variable List

JS Functions	:

Created by		:	K.M Nazim Uddin
Creation date 	: 	03-12-2020
Updated by 		:
Update date		:

QC Performed BY	:

QC Date			:

Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header(":login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("AOP Variable Settings", "../../../", 1, 1,$unicode,'','');
?>


<script language="javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	function fnc_delivery_variable_settings(operation)
	{
		var cbo_company_name_delivery		= escape(document.getElementById('cbo_company_name_delivery').value);
		var cbo_variable_list_delivery	= escape(document.getElementById('cbo_variable_list_delivery').value);

		if ( form_validation('cbo_company_name_delivery*cbo_variable_list_delivery','Company Name*Variable List')==0 )
		{
			return;
		}

		if (cbo_variable_list_delivery==1)
		{

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_delivery*cbo_variable_list_delivery*cbo_variable_dtls_status*update_id',"../../../");
			//alert(111);
			//freeze_window(operation);
			http.open("POST","requires/aop_production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_delivery_variable_settings_reponse;
		}
		else if (cbo_variable_list_delivery==3)
		{

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_delivery*cbo_variable_list_delivery*cbo_variable_dtls_status*update_id',"../../../");
			//alert(111);
			//freeze_window(operation);
			http.open("POST","requires/aop_production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_delivery_variable_settings_reponse;
		}
	}
	function fnc_delivery_variable_settings_reponse()
	{
		if(http.readyState == 4)
		{
			var cbo_variable_list=$('#cbo_variable_list_delivery').val();
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
				document.getElementById('update_id').value  = reponse[2];
				set_button_status(0, permission, 'fnc_delivery_variable_settings',1);
				reset_form('deliveryVariableSettings','variable_settings_container','');
			
			release_freezing();
		}
	}
</script>
</head>

<body  onLoad="set_hotkey();">
	<div align="center" style="width:100%;">

		<? echo load_freeze_divs ("../../../",$permission);  ?>

        <fieldset style="width:850px;">
            <legend>aop Variable Settings</legend>
            <form name="deliveryVariableSettings" id="deliveryVariableSettings" >
                    <table  width="850px" cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td width="150" align="center" class="must_entry_caption">Company</td>
                            <td width="300">
                                <?
                                    echo create_drop_down( "cbo_company_name_delivery", 250, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--- Select Company ---', 0, "show_list_view(document.getElementById('cbo_variable_list_delivery').value+'_'+this.value,'on_change_data','variable_settings_container','../AOP/requires/aop_production_settings_controller','');" );
                                ?>
                            </td>
                            <td width="150" align="center" class="must_entry_caption">Variable List</td>
                            <td width="300">
                                <?
                                $aop_module = array(1 =>"AOP Production");
                                //echo create_drop_down( "cbo_variable_list_delivery", 250, $wash_delivery_module,'', 1, '--- Select ---', 0, "" );


                               echo create_drop_down( "cbo_variable_list_delivery", 250, $aop_module,'', 1, '---- Select ----', 0,"show_list_view(this.value+'_'+document.getElementById('cbo_company_name_delivery').value,'on_change_data','variable_settings_container','../aop/requires/aop_production_settings_controller','');",'','','','',''); //data, action, div, path, extra_func
                                ?>
                            </td>
                        </tr>
                    </table>

                 <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>

            </form>
        </fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
