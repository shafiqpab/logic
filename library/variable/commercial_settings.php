<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Order Tracking Variable Settings
					Select company and select Variable List that onchange will change content
					
Functionality	:	Must fill Company, Variable List

JS Functions	:

Created by		:	Bilas 
Creation date 	: 	12-10-2012
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
echo load_html_head_contents("Order Tracking Variable Settings", "../../", 1, 1,$unicode,'','');

if ($_SESSION['logic_erp']["data_level_secured"]==1) 
{
	if ($_SESSION['logic_erp']["buyer_id"]!=0 && $_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
	if ($_SESSION['logic_erp']["company_id"]!=0 && $_SESSION['logic_erp']["company_id"]!="") $company_name="and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_name="";
}
else
{
	$buyer_name="";
	$company_name="";
}
?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


function fnc_variable_settings_commercial( operation )
{
		
	if (document.getElementById('cbo_variable_list').value*1==5)
	{
		
		if ( form_validation('cbo_company_name*txt_capacity_value*cbo_currency_id','Company Name*Capacity Value*Currency')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var capacity_in_value	= escape(document.getElementById('capacity_in_value').innerHTML);
				var currency	= escape(document.getElementById('currency').innerHTML);
					
				//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*txt_capacity_value*cbo_currency_id*update_id'));
				var data="action=save_update_delete&operation="+operation+'&capacity_in_value='+capacity_in_value+'&currency='+currency+get_submitted_data_string('cbo_company_name*cbo_variable_list*txt_capacity_value*cbo_currency_id*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/commercial_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 5
	
	
	if (document.getElementById('cbo_variable_list').value*1==6)
	{
		var cbo_data_source=$("#cbo_data_source").val()*1;
		if(cbo_data_source==1)
		{
			if ( form_validation('cbo_company_name*cbo_variable_list*cbo_contorll_status*txt_max_btb_limit','Company Name*variable List*Controll Status*Max BTB Limit')==0 )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_variable_list*cbo_contorll_status','Company Name*variable List*Controll Status')==0 )
			{
				return;
			}
		}
		
		
		nocache = Math.random();
		var max_btb_limit	= escape(document.getElementById('max_btb_limit').innerHTML);
		eval(get_submitted_variables('cbo_company_name*cbo_variable_list*txt_max_btb_limit*update_id'));
		var data="action=save_update_delete&operation="+operation+"&max_btb_limit="+max_btb_limit+get_submitted_data_string('cbo_company_name*cbo_variable_list*txt_max_btb_limit*update_id*cbo_contorll_status*cbo_data_source',"../../");
		freeze_window(operation);
		http.open("POST","requires/commercial_settings_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_variable_settings_commercial_reponse;
	
	} // end cbo_variable_list type 6
	
	if (document.getElementById('cbo_variable_list').value*1==7)
	{
		
		if ( form_validation('cbo_company_name*txt_max_pc_limit','Company Name*Max PC Limit')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
					
				var max_pc_limit	= escape(document.getElementById('max_pc_limit').innerHTML);
				eval(get_submitted_variables('cbo_company_name*cbo_variable_list*txt_max_pc_limit*update_id'));
				var data="action=save_update_delete&operation="+operation+"&max_pc_limit="+max_pc_limit+get_submitted_data_string('cbo_company_name*cbo_variable_list*txt_max_pc_limit*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/commercial_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 7
	
	if (document.getElementById('cbo_variable_list').value*1==17)
	{
		if ( form_validation('cbo_company_name*cbo_cost_heads*cbo_cost_heads_status','Company Name*Cost Head*Status')==0 )
		{
			return;
		}
		else
		{					
												
				nocache = Math.random();
				
				//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/commercial_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 17
	
	if (document.getElementById('cbo_variable_list').value*1==18 || document.getElementById('cbo_variable_list').value*1==33 || document.getElementById('cbo_variable_list').value*1==36)
	{
		if ( form_validation('cbo_company_name*cbo_rate_status','Company Name*Rate Status')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_rate_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 18
	
	if (document.getElementById('cbo_variable_list').value*1==40)
	{
		if ( form_validation('cbo_company_name*cbo_mixing_allowed','Company Name*Buyer Mixing')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_mixing_allowed*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 18

	if (document.getElementById('cbo_variable_list').value*1==19)
	{
		if ( form_validation('cbo_company_name','Company Name')==0 )
		{
			return;
		}
		var total_row = $("#tbl_monitor tbody tr").length;
		// save data here
		var detailsData="";
		var head_check=new Array;
		for(var i=1;i<=total_row;i++)
		{
			try
			{
				if($('#txtmonday_'+i).val()!="" && $('#monitorhead_'+i).val()>0)
				{
					if( $.inArray( $('#monitorhead_'+i).val(), head_check ) !== -1 )
					{
						alert("Monitoring Head Not Allow Once Again");return;
					}
					else
					{
						
						head_check.push($('#monitorhead_'+i).val());
						if( form_validation('monitorhead_'+i+'*txtmonday_'+i,'Monitor Head*monitoring Standard Day')==false )
						{
							return;
						}
	
						if( $("#txtmonday_"+i).val()*1 <= 0)
						{
							alert("monitoring Standard Day Can not be 0 or less than 0");
							$("#txtmonday_"+i).focus();
							return;
						}
						detailsData+='*monitorhead_'+i+'*txtmonday_'+i+'*hiderow_'+i;
					}
				}
			}
			catch(err){}
		}
		
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+get_submitted_data_string('cbo_company_name*cbo_variable_list'+detailsData,"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/commercial_settings_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_variable_settings_commercial_reponse;
	
	} // end cbo_variable_list type 19
	
	
	if (document.getElementById('cbo_variable_list').value*1==20 || document.getElementById('cbo_variable_list').value*1==21)
	{
		if ( form_validation('cbo_company_name*cbo_file_status','Company Name*Rate Status')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_file_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 20 || 21
	
	
	if (document.getElementById('cbo_variable_list').value*1==22 || document.getElementById('cbo_variable_list').value*1==23 || document.getElementById('cbo_variable_list').value*1==24 || document.getElementById('cbo_variable_list').value*1==26)
	{
		if ( form_validation('cbo_company_name*cbo_file_status','Company Name*Export Invoice Qty Source')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_file_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 22


	if (document.getElementById('cbo_variable_list').value*1==25)
	{
		if ( form_validation('cbo_company_name*cbo_pi_source','Company Name*PI Source BTB LC')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_pi_source*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 25

	if (document.getElementById('cbo_variable_list').value*1==27 || document.getElementById('cbo_variable_list').value*1==28  || document.getElementById('cbo_variable_list').value*1==38  )
	{

		if ( form_validation('cbo_company_name*cbo_export_pino_status','Company Name*Export PI No Status')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_export_pino_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 27

	if (document.getElementById('cbo_variable_list').value*1==29)
	{
		if ( form_validation('cbo_company_name*cbo_office_note_source','Company Name*Com Office Note Source')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_office_note_source*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 29

	if (document.getElementById('cbo_variable_list').value*1==30)
	{
		if ( form_validation('cbo_company_name*cbo_item_category*cbo_do_control','Company Name*Item Category*Do Control')==0 )
		{
			return;
		}
		else
		{											
			nocache = Math.random();				
			//eval(get_submitted_variables('cbo_company_name*cbo_variable_list*cbo_cost_heads*cbo_cost_heads_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_do_control*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 30

	if (document.getElementById('cbo_variable_list').value*1==31)
	{
		if ( form_validation('cbo_company_name*cbo_sc_lc_attachInternalFile','Company Name*SC/LC Attach Internal File')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_sc_lc_attachInternalFile*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 31

	if (document.getElementById('cbo_variable_list').value*1==32)
	{
		if ( form_validation('cbo_company_name*cbo_contract_number_status','Company Name*Contract Number Status')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_contract_number_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_variable_list type 31

    if (document.getElementById('cbo_variable_list').value*1==35)
    {
        if($('#txt_budget_value').val() == 1){
            if ( form_validation('validate_page_name','Validate With Page')==0 )
            {
                return;
            }
        }
        nocache = Math.random();
        var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*txt_budget_value*validate_page_name*update_id',"../../");
        freeze_window(operation);
        http.open("POST","requires/commercial_settings_controller.php", true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_variable_settings_commercial_reponse;

    } // General Category Budget Validation 35

	if (document.getElementById('cbo_variable_list').value*1==37)
	{
		if ( form_validation('cbo_company_name*cbo_actual_cost_source','Company Name*Com Office Note Source')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_actual_cost_source*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/commercial_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_commercial_reponse;
		}
	
	} // end cbo_actual_cost_entry type 37

}	

function fnc_variable_settings_commercial_reponse()
{
	if(http.readyState == 4) 
	{
		//release_freezing();alert(http.responseText);return;
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		document.getElementById('update_id').value  = reponse[2];
		set_button_status(0, permission, 'fnc_variable_settings_commercial',1);
		reset_form('commercialvariablesettings_1','variable_settings_container','','','','cbo_company_name');

		//show_list_view(document.getElementById('cbo_variable_list').value+'_'+document.getElementById('cbo_company_name').value,'on_change_data','variable_settings_container','../variable/requires/commercial_settings_controller','');

		release_freezing();
	}
}


function add_break_down_tr(i)
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var row_num=$('#tbl_monitor tbody tr').length;
	if (row_num!=i)
	{
		return false;
	}
	else
	{ 
		i++;
		var k=i-1;
		$("#tbl_monitor tbody tr:last").clone().find("input,select").each(function(){
		$(this).attr({ 
		  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
		  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
		  'value': function(_, value) { return value }              
		});
		}).end().appendTo("#tbl_monitor");
		
		$("#tbl_monitor tbody tr:last").css({"height":"10px","background-color":"#FFF"});	
		$("#tbl_monitor tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		
		$('#monitorhead_'+i).val('');
		$('#txtmonday_'+i).val('');
		
		$('#txtmonday_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
		$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+i+");");
	}
}

function fn_deletebreak_down_tr(rowNo) 
{
	var row_num=$('#tbl_monitor tbody tr').length;
	if(row_num!=rowNo)
	{  
		$('#tr_'+rowNo).hide();
		$('#hiderow_'+rowNo).val(1);
	}
}

function fn_btb_data_source(str) 
{
	if(str==1)
	{
		$('#cbo_data_source').attr("disabled",false);
	}
	else
	{
		$('#cbo_data_source').val(0);
		$('#txt_max_btb_limit').val("");
		$('#cbo_data_source').attr("disabled",true);
	}
}

function fn_btb_limit(str) 
{
	if(str==1)
	{
		$('#txt_max_btb_limit').attr("disabled",false);
	}
	else
	{
		$('#txt_max_btb_limit').val("");
		$('#txt_max_btb_limit').attr("disabled",true);
	}
}
function budget_validation_change(){
    $('#validate_page_name').val(0);
}

</script>

</head>

<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
	<?= load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:850px;">
		<legend>Commercial Variable Settings</legend>
		<form name="commercialvariablesettings_1" id="commercialvariablesettings_1" autocomplete="off">	
      			<table  width="750" cellspacing="2" cellpadding="0" border="0">
            		<tr>
                		<td width="200" align="left" class="must_entry_caption">Company Name</td>
                        <td width="250">
                   			<?= create_drop_down( "cbo_company_name", 250, "select company_name,id from lib_company where is_deleted=0  and status_active=1 $company_name order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "show_list_view(document.getElementById('cbo_variable_list').value+'_'+this.value,'on_change_data','variable_settings_container','../variable/requires/commercial_settings_controller','')" );?>
                        </td>
                		<td width="200" align="center">Variable List</td>
                        <td width="250">
                    		<?= create_drop_down( "cbo_variable_list", 250, $commercial_module,'', '1', '---- Select ----', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_name').value,'on_change_data','variable_settings_container','../variable/requires/commercial_settings_controller','')",'','','','','39'); //data, action, div, path, extra_func?>
                        </td>
            		</tr>
        		</table>
            <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container">
            </div>
		</form>	
	</fieldset>
    </div>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
 </body>
    

