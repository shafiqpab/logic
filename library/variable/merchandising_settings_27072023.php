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
	$buyer_name=""; $company_name="";
}
?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
function fnc_order_tracking_variable_settings( operation )
{
	var cbo_sales_year_started_date=""; var cbo_tna_integrated=""; var cbo_profit_calculative=""; var cbo_consumption_basis=""; var cbo_copy_quotation="";
 	if (document.getElementById('cbo_variable_list_wo').value*1==12)
	{
		if ( form_validation('cbo_company_name_wo*cbo_sales_year_started_date','Company Name*Sales Year started')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var sales_year_started	= escape(document.getElementById('sales_year_started').innerHTML);
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_sales_year_started_date*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+sales_year_started+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_sales_year_started_date*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	} // end cbo_variable_list_wo type 12
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==14)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_tna_integrated','Company Name*TNA Integrated')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var tna_integrated_td	= escape(document.getElementById('tna_integrated_td').innerHTML);
			eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_tna_integrated*update_id'));
			var data="action=save_update_delete&operation="+operation+"&sales_year_started="+tna_integrated_td+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_tna_integrated*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	} // end cbo_variable_list_wo type 14
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==15)
	{
		if ( form_validation('cbo_company_name_wo*cbo_profit_calculative','Company Name*Profit Calculative')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var profit_calculative_td	= escape(document.getElementById('profit_calculative_td').innerHTML);
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_profit_calculative*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+profit_calculative_td+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_profit_calculative*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	} // end cbo_variable_list_wo type 15
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==18)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				var item_category_id='';
				var process_loss_method='';
 				var countrow='';
				$(document).ready(function() {
                   	countrow = $('.rpt_table tbody tr').length; 
                });
				 
				for(i=1;i<=countrow;i++)
				{
					if(i==1) 
					{
						item_category_id=$('#item_category_id_'+i).val();
						process_loss_method=$('#process_loss_method_'+i).val();
					}
					else 
					{
						item_category_id+=","+$('#item_category_id_'+i).val();
						process_loss_method+=","+$('#process_loss_method_'+i).val();
					}
				}
				
				nocache = Math.random();
				var update_id = $('#update_id').val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete_process_loss_method&operation="+operation+"&item_category_id="+item_category_id+"&process_loss_method="+process_loss_method+"&update_id="+update_id+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 18
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==19)
	{
		if ( form_validation('cbo_company_name_wo*cbo_consumption_basis','Company Name*Consumption Basis')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var consumption_td	= escape(document.getElementById('consumption_td').innerHTML);
				var cbo_consumption_basis	= $("#cbo_consumption_basis").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+consumption_td+'&cbo_consumption_basis='+cbo_consumption_basis+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 19
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==20 || document.getElementById('cbo_variable_list_wo').value*1==78 || document.getElementById('cbo_variable_list_wo').value*1==79 || document.getElementById('cbo_variable_list_wo').value*1==96)
	{
		if ( form_validation('cbo_company_name_wo*cbo_copy_quotation','Company Name*Consumption Basis')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var copy_quotation_td	= escape(document.getElementById('copy_quotation_td').innerHTML);
				var cbo_copy_quotation	= $("#cbo_copy_quotation").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+copy_quotation_td+'&cbo_copy_quotation='+cbo_copy_quotation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 20
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==21)
	{
		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo','Company Name*Variable List')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//var conversion_from_chart_td	= escape(document.getElementById('conversion_from_chart_td').innerHTML);
			
			var countrow=''; var rate_type=''; var conversion_from_chart=""; var update_id="";
			$(document).ready(function() {
				countrow = $('.rpt_table tbody tr').length; 
			});
			
			//alert(countrow);
			for(i=1; i<=countrow; i++)
			{
				var cbo_conversion_from_chart	= $("#cbo_conversion_from_chart"+i).val();
				if(cbo_conversion_from_chart==1)
				{
					if($("#cbo_rate_type"+i).val()==0)
					{
						alert("Please Select Rate Type");
						return;
					}
				}
				
				if(i==1) 
				{
					rate_type=$('#cbo_rate_type'+i).val();
					conversion_from_chart=$('#cbo_conversion_from_chart'+i).val();
					update_id=$('#update_id'+i).val();
				}
				else 
				{
					rate_type+=","+$('#cbo_rate_type'+i).val();
					conversion_from_chart+=","+$('#cbo_conversion_from_chart'+i).val();
					update_id+=","+$('#update_id'+i).val();
				}
			}
			
			eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo'));
			
			var data="action=save_update_delete_process_loss_method&operation="+operation+"&rate_type="+rate_type+"&conversion_from_chart="+conversion_from_chart+"&update_id="+update_id+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	} // end cbo_variable_list_wo type 21
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==22)
	{
		
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var cbo_cm_cost_method_td	= escape(document.getElementById('cbo_cm_cost_method_td').innerHTML);
				var cbo_cm_cost_method	= $("#cbo_cm_cost_method").val();
				var cbo_cm_cost_method_based_on	= $("#cbo_cm_cost_method_based_on").val();
				var cbo_cm_cost_compulsory	= $("#cbo_cm_cost_compulsory").val();
				var cbo_cm_cost_editable	= $("#cbo_cm_cost_editable").val();
				
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+cbo_cm_cost_method_td+'&cbo_cm_cost_method='+cbo_cm_cost_method+'&cbo_cm_cost_method_based_on='+cbo_cm_cost_method_based_on+'&cbo_cm_cost_compulsory='+cbo_cm_cost_compulsory+'&cbo_cm_cost_editable='+cbo_cm_cost_editable+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 22
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==23)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_color_from_library','Company Name*Color From Library')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var color_from_library_td	= escape(document.getElementById('color_from_library_td').innerHTML);
				var cbo_color_from_library	= $("#cbo_color_from_library").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+color_from_library_td+'&cbo_color_from_library='+cbo_color_from_library+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} 
	else if (document.getElementById('cbo_variable_list_wo').value*1==24)
	{
		if ( form_validation('cbo_company_name_wo*cbo_yarn_dyeing_charge','Company Name*Yarn Dyeing Charge')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var color_from_library_td	= escape(document.getElementById('yarn_dyeing_charge_td').innerHTML);
				var cbo_color_from_library	= $("#cbo_yarn_dyeing_charge").val();
				
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+color_from_library_td+'&cbo_color_from_library='+cbo_color_from_library+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==25)
	{
		if ( form_validation('cbo_company_name_wo*publish_shipment_date','Company Name*Publish Shipment Date')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var publish_shipment_date_td	= escape(document.getElementById('publish_shipment_date_td').innerHTML);
				var publish_shipment_date	= $("#publish_shipment_date").val();
				var cbo_next_process_shipdate	= $("#cbo_next_process_shipdate").val();
				eval(get_submitted_variables('cbo_company_name_wo*publish_shipment_date*cbo_next_process_shipdate*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+publish_shipment_date_td+'&publish_shipment_date='+publish_shipment_date+'&cbo_next_process_shipdate='+cbo_next_process_shipdate+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==26)
	{
		
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				
				var item_category_id='';
				var process_loss_method='';
 				var countrow='';
				$(document).ready(function() {
                   	countrow = $('.rpt_table tbody tr').length; 
                });
				 
				for(i=1;i<=countrow;i++)
				{
					if(i==1) 
					{
						item_category_id=$('#item_category_id_'+i).val();
						exeed_budget_qty=$('#txt_exeed_qty_'+i).val();
						exeed_budget_amt=$('#txt_exeed_amount_'+i).val();
						amt_exceed_lavel=$('#cbo_exceed_level_'+i).val();
						cbo_exceed_qty_level=$('#cbo_exceed_qty_level_'+i).val();
					}
					else 
					{
						item_category_id+=","+$('#item_category_id_'+i).val();
						exeed_budget_qty+=","+$('#txt_exeed_qty_'+i).val();
						exeed_budget_amt+=","+$('#txt_exeed_amount_'+i).val();
						amt_exceed_lavel+=","+$('#cbo_exceed_level_'+i).val();
						cbo_exceed_qty_level+=","+$('#cbo_exceed_qty_level_'+i).val();
					}
					
				}
				
				nocache = Math.random();
				var update_id = $('#update_id').val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete_material_control&operation="+operation+"&item_category_id="+item_category_id+"&exeed_budget_qty="+exeed_budget_qty+"&exeed_budget_amt="+exeed_budget_amt+"&amt_exceed_lavel="+amt_exceed_lavel+"&cbo_exceed_qty_level="+cbo_exceed_qty_level+"&update_id="+update_id+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==27 || document.getElementById('cbo_variable_list_wo').value*1==57 || document.getElementById('cbo_variable_list_wo').value*1==58 || document.getElementById('cbo_variable_list_wo').value*1==84)
	{
		
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var cbo_commercial_cost_method_td	= escape(document.getElementById('cbo_commercial_cost_method_td').innerHTML);
				var cbo_commercial_cost_method	= $("#cbo_commercial_cost_method").val();
				var txt_commercial_cost_percent	= $("#txt_commercial_cost_percent").val();
				var cbo_editable	= $("#cbo_editable").val();
				//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+cbo_commercial_cost_method_td+'&cbo_commercial_cost_method='+cbo_commercial_cost_method+'&txt_commercial_cost_percent='+txt_commercial_cost_percent+'&cbo_editable='+cbo_editable+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==28)
	{
		
		if ( form_validation('cbo_company_name_wo*txt_size_wise_repeat','Company Name*Consumption Basis')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var copy_quotation_td	= escape(document.getElementById('copy_quotation_td').innerHTML);
				var txt_size_wise_repeat	= $("#txt_size_wise_repeat").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+copy_quotation_td+'&txt_size_wise_repeat='+txt_size_wise_repeat+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 28
	else if (document.getElementById('cbo_variable_list_wo').value*1==29)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_duplicate_ship_date','Company Name*Duplicate Ship')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var cbo_duplicate_ship_date	= $("#cbo_duplicate_ship_date").val();
				
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				
				var data="action=save_update_delete&operation="+operation+"&cbo_duplicate_ship_date="+cbo_duplicate_ship_date+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 29
	else if (document.getElementById('cbo_variable_list_wo').value*1==30)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_image_mandatory','Company Name*Mandatory')==0 )
		{
			return;
		}
		else
		{				
				nocache = Math.random();
				var image_mandatory	= $("#cbo_image_mandatory").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&image_mandatory="+image_mandatory+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 30
	else if (document.getElementById('cbo_variable_list_wo').value*1==31)
	{
		
		if ( form_validation('cbo_company_name_wo*txt_tna_process_type','Company Name*TNA Process Type')==0 )
		{
			return;
		}
		else
		{				
				nocache = Math.random();
				var txt_tna_process_type = $("#txt_tna_process_type").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&tna_process_type="+txt_tna_process_type+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				
				
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==33) //cbo_po_current_date
	{
		
		
		if ( form_validation('cbo_company_name_wo*cbo_po_current_date','Company Name*Po Current Date Type')==0 )
		{
			return;
		}
		else
		{				
			
				var po_current_date = $("#cbo_po_current_date").val();
				
				var data="action=save_update_delete&operation="+operation+"&po_current_date="+po_current_date+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				//alert(data);
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==32) //Update Period
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{				
			
				var update_period = $("#update_period").val();
				var user_hidden_id = $("#user_hidden_id").val();
				
				var data="action=save_update_delete&operation="+operation+"&update_period="+update_period+"&user_hidden_id="+user_hidden_id+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				//alert(data);
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==34)
	{
		
		if (form_validation('cbo_company_name_wo*cbo_inquery_id_mandatory','Company Name*Inquery ID Mandatory')==0 )
		{
			return;
		}
		else
		{				
				var inquery_id_mandatory = $("#cbo_inquery_id_mandatory").val();
				var data="action=save_update_delete&operation="+operation+"&inquery_id_mandatory="+inquery_id_mandatory+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==35)
	{
		
		if (form_validation('cbo_company_name_wo*cbo_trim_rate','Company Name*Trim Rate Mandatory')==0 )
		{
			return;
		}
		else
		{				
				var cbo_trim_rate = $("#cbo_trim_rate").val();
				var data="action=save_update_delete&operation="+operation+"&cbo_trim_rate="+cbo_trim_rate+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}//35
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==36)
	{
		
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var cbo_cm_cost_method_td	= escape(document.getElementById('cbo_cm_cost_method_td').innerHTML);
				var cbo_cm_cost_method_quata	= $("#cbo_cm_cost_method_quata").val();
				var cbo_cm_cost_compulsory	= $("#cbo_cm_cost_compulsory").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+cbo_cm_cost_method_td+'&cbo_cm_cost_method_quata='+cbo_cm_cost_method_quata+'&cbo_cm_cost_compulsory='+cbo_cm_cost_compulsory+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 36
	else if (document.getElementById('cbo_variable_list_wo').value*1==37)
	{
		
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var budgetexceedsquot_td	= escape(document.getElementById('budgetexceedsquot_td').innerHTML);
				var cbo_budget_exceeds_quot	= $("#cbo_budget_exceeds_quot").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+budgetexceedsquot_td+'&cbo_budget_exceeds_quot='+cbo_budget_exceeds_quot+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 37
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==38)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_s_f','Company Name*S F Booking Before M F')==0 )
		{
			return;
		}
		else
		{
				nocache = Math.random();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_s_f*update_id'));
				var data="action=save_update_delete_s_f_before_m_f&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_s_f*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 14
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==39)
	{
		if ( form_validation('cbo_company_name_wo*cbo_lab_test_rate','Company Name*Lab Test Rate Update')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
					
				var lab_test_rate_update_td	= escape(document.getElementById('lab_test_rate_update_td').innerHTML);
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_lab_test_rate*update_id'));
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_lab_test_rate*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} 
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==40)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var colar_culff_percent_td	= escape(document.getElementById('colar_culff_percent_td').innerHTML);
				var cbo_colar_culff_percent	= $("#cbo_colar_culff_percent").val();
				//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+colar_culff_percent_td+'&cbo_colar_culff_percent='+cbo_colar_culff_percent+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==41)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_pre_cost_approval*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}  
	else if (document.getElementById('cbo_variable_list_wo').value*1==42)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_report_date_catagory*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} 
	else if (document.getElementById('cbo_variable_list_wo').value*1==43) // TNA Process Start Date
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*txt_tna_process_start_date*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}   
	else if ((document.getElementById('cbo_variable_list_wo').value*1)==44 || (document.getElementById('cbo_variable_list_wo').value*1)==63 || (document.getElementById('cbo_variable_list_wo').value*1)==64)//63 is Sequence validation with Booking
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var data="action=save_update_delete_season_mandatory&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_season_mandatory*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==45)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var data="action=save_update_delete_excess_cut_source&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_excess_cut_source*cbo_editable_id*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==46) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_ship_date','Company Name*Ship Date')==0 )
		{
			return;
		}
		else
		{				
			var publish_shipment_date = $("#cbo_ship_date").val();
			
			var data="action=save_update_delete&operation="+operation+"&publish_shipment_date="+publish_shipment_date+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==47) //// Style & SMV Source/Combinations
	{
		if ( form_validation('cbo_company_name_wo*cbo_smv_in_order_entry','Company Name*Ship Date')==0 )
		{
			return;
		}
		else
		{				
			var publish_shipment_date = $("#cbo_smv_in_order_entry").val();
			var style_from_library = $("#cbo_style_from_library").val();
			var style_editable = $("#cbo_style_editable").val();
			
			var data="action=save_update_delete&operation="+operation+"&publish_shipment_date="+publish_shipment_date+"&style_from_library="+style_from_library+"&style_editable="+style_editable+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	//Default fabric nature
	else if (document.getElementById('cbo_variable_list_wo').value*1==48) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_default_febric_nature','Company Name')==0 )
		{
			return;
		}
		else
		{				
			var cbo_default_febric_nature = $("#cbo_default_febric_nature").val();
			
			var data="action=save_update_delete&operation="+operation+"&cbo_default_febric_nature="+cbo_default_febric_nature+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}

	//Default fabric SOURCE
	else if (document.getElementById('cbo_variable_list_wo').value*1==49) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_default_fabric_source','Company Name')==0 )
		{
			return;
		}
		else
		{				
			var cbo_default_fabric_source = $("#cbo_default_fabric_source").val();
			
			var data="action=save_update_delete&operation="+operation+"&cbo_default_fabric_source="+cbo_default_fabric_source+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	//Default fabric SOURCE
	else if (document.getElementById('cbo_variable_list_wo').value*1==50) //cbo_bom_page
	{
		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo*cbo_bom_page','Company Name*Variable List*Page Name')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_bom_page*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	//Min Lead Time Control
	else if (document.getElementById('cbo_variable_list_wo').value*1==51 || document.getElementById('cbo_variable_list_wo').value*1==95)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var data="action=min_lead_time_control&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_min_lead_time_control*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	
	// PO Entry Limit On Capacity
	else if (document.getElementById('cbo_variable_list_wo').value*1==52)
	{
		//alert(document.getElementById('cbo_variable_list_wo').value*1);
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var data="action=po_entry_limit_on_capacity&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_buyer_allocation_maintain*cbo_capacity_exceed_level*update_id*cbo_actpo_exceed_level',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==53) //cbo_bom_page
	{
		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo*cbo_cost_control_source','Company Name*Variable List*Cost Control Source')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_cost_control_source*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}

	else if (document.getElementById('cbo_variable_list_wo').value*1==54)
	{
 		if ( form_validation('cbo_company_name_wo*cbo_efficiency_source_for_pre_cost','Company Name*Efficiency Source For Pre Cost')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				var data="action=save_update_delete_effeciency_slab&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_efficiency_source_for_pre_cost*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}


	else if (document.getElementById('cbo_variable_list_wo').value*1==55)
	{
 		if ( form_validation('cbo_company_name_wo*cbo_work_study_mapping','Company Name*Work Study Mapping')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_work_study_mapping*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}


	else if (document.getElementById('cbo_variable_list_wo').value*1==92)
	{
 		if ( form_validation('cbo_company_name_wo*cbo_work_study_mapping','Company Name*Work Study Mapping')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_work_study_mapping*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}

	else if (document.getElementById('cbo_variable_list_wo').value*1==59) //Fabric Source Aop
	{
 		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo','Company Name*Variable List')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_fabric_source_aop_id*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==60)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_yarn_iss_with_serv_app','Company Name*Yarn Issue Validation Based on Service Approval')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
					
				var tna_integrated_td	= escape(document.getElementById('tna_integrated_td').innerHTML);
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_yarn_iss_with_serv_app*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+tna_integrated_td+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_yarn_iss_with_serv_app*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 60
	else if (document.getElementById('cbo_variable_list_wo').value*1==61)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_price_quo_approval*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==62)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_textile_tna_process_base','Company Name*Textile TNA Process Base')==0 )
		{
			return;
		}
		else
		{				
				nocache = Math.random();
				var cbo_textile_tna_process_base = $("#cbo_textile_tna_process_base").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&cbo_textile_tna_process_base="+cbo_textile_tna_process_base+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				
				
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==65 || document.getElementById('cbo_variable_list_wo').value*1==66 || document.getElementById('cbo_variable_list_wo').value*1==68 || document.getElementById('cbo_variable_list_wo').value*1==73 || document.getElementById('cbo_variable_list_wo').value*1==90)//Excess Cut % Level in Order Entry-65; Fabric Req. Qty. Source in Service Booking-66 and QC Cons. From=68
	{
		if ( form_validation('cbo_company_name_wo*cbo_excesscut_per_level','Company Name*Excess Cut % Level in Order Entry')==0 )
		{
			return;
		}
		else
		{				
			nocache = Math.random();
			var cbo_excesscut_per_level = $("#cbo_excesscut_per_level").val();
			eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+"&cbo_excesscut_per_level="+cbo_excesscut_per_level+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==74)
	{
		
		var without_emblish = $("#txt_without_emblish").val()*1;
		var with_emblish = $("#txt_with_emblish").val()*1;
		if(with_emblish=="" || with_emblish=="")
		{
			alert("Day Field Empty,Please fillup.");
			return;
		}
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{				
				nocache = Math.random();
				var txt_without_emblish = $("#txt_without_emblish").val();
				var txt_with_emblish = $("#txt_with_emblish").val();
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+"&txt_without_emblish="+txt_without_emblish+"&txt_with_emblish="+txt_with_emblish+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				
				
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==56 || document.getElementById('cbo_variable_list_wo').value*1==75)//56=embellishment Budget On; 75=Fabric Budget On
	{
 		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{				
 			var row = $("#embellishment_tbl tbody tr:last").attr('id');
			var detailsData="";
			for(var i=1;i<=row;i++)
			{
				try
				{ 
					/*if( form_validation('cbo_embellishment_type_'+i+'*cbocount_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i,'Color*Count*Yarn Type*UOM*Quantity*Rate*Amount')==false )
					{
						return;
					}
					*/	
								  
					detailsData+='*cboEmbellishmentTypeHidden_'+i+'*embellishmentName_'+i+'*updateidRequiredEmbellishdtl_'+i;
				}
				catch(err){}
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id'+detailsData,"../../")+"&total_row="+row;
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==76)//  Budget Un-Approved  
	{
 		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{				
 			var row = $("#embellishment_tbl tbody tr:last").attr('id');
			var detailsData="";
			for(var i=1;i<=row;i++)
			{
				try
				{ 
					/*if( form_validation('cbo_embellishment_type_'+i+'*cbocount_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i,'Color*Count*Yarn Type*UOM*Quantity*Rate*Amount')==false )
					{
						return;
					}
					*/	
								  
					detailsData+='*cbo_validation_'+i+'*cbo_validation_'+i;
				}
				catch(err){}
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id'+detailsData,"../../")+"&total_row="+row;
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==67)
	{
		if ( form_validation('cbo_company_name_wo*cbo_yarn_iss_with_serv_app','Company Name*Location Wise Cost Per Minute')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var cost_per_minute_td	= escape(document.getElementById('cost_per_minute_td').innerHTML);
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_yarn_iss_with_serv_app*update_id'));
				var data="action=save_update_delete&operation="+operation+"&sales_year_started="+cost_per_minute_td+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_yarn_iss_with_serv_app*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	} // end cbo_variable_list_wo type 60

	else if (document.getElementById('cbo_variable_list_wo').value*1==69)
	{
		if ( form_validation('cbo_company_name_wo*cbo_yarn_dyeing_lot_used','Company Name*Yarn Dyeing Lot')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				//var yarn_dyeing_lot_td	= escape(document.getElementById('yarn_dyeing_lot_td').innerHTML);
				var cbo_yarn_lot_used_from_library	= $("#cbo_yarn_dyeing_lot_used").val();
				
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+'&cbo_yarn_lot_used_from_library='+cbo_yarn_lot_used_from_library+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				// alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==70)//Knittng Charge Source
	{
		if ( form_validation('cbo_company_name_wo*cbo_knitting_charge_source','Company Name*Kntting Charge Source')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
 				//var yarn_dyeing_lot_td	= escape(document.getElementById('yarn_dyeing_lot_td').innerHTML);
				var cbo_editable	= $("#cbo_knitting_charge_source").val();
				
				eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
				var data="action=save_update_delete&operation="+operation+'&cbo_editable='+cbo_editable+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				// alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}	
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==71)
	{
		if ( form_validation('cbo_company_name_wo*cbo_fabric_ref_automation','Company Name*Fabric Ref Automation')==0 )
		{
			return;
		}
		else
		{				
			nocache = Math.random();
			var cbo_fabric_ref_automation = $("#cbo_fabric_ref_automation").val();
			eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+"&cbo_fabric_ref_automation="+cbo_fabric_ref_automation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	
	
	else if (document.getElementById('cbo_variable_list_wo').value*1==72)
	{
		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo','Company Name*Variable List')==0 )
		{
			return;
		}
		else
		{					
			
			var countrow=''; var booking_type=''; var source_id=""; var update_id="";
			$(document).ready(function() {
				countrow = $('.rpt_table tbody tr').length; 
			});
			
			for(i=1; i<=countrow; i++)
			{
				if(i==1) 
				{
					booking_type=$('#cbo_booking_type'+i).val();
					source_id=$('#cbo_source_id'+i).val();
					update_id=$('#update_id'+i).val();
				}
				else 
				{
					booking_type+=","+$('#cbo_booking_type'+i).val();
					source_id+=","+$('#cbo_source_id'+i).val();
					update_id+=","+$('#update_id'+i).val();
				}
			}
			
			var data="action=save_update_delete_booking_source&operation="+operation+"&source_id="+source_id+"&booking_type="+booking_type+"&update_id="+update_id+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo',"../../");
			//alert(data);return;
			
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	} // end cbo_variable_list_wo type 21
	else if (document.getElementById('cbo_variable_list_wo').value*1==77)
	{
		if ( form_validation('style_from_library','Sample Style Source')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var style_from_library	= $("#style_from_library").val();
				eval(get_submitted_variables('style_from_library*update_id'));
				var data="action=save_update_delete&operation="+operation+"&style_from_library="+style_from_library+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==80 || document.getElementById('cbo_variable_list_wo').value*1==81 || document.getElementById('cbo_variable_list_wo').value*1==86)
	{
		if ( form_validation('style_from_library_1','Sample Style Source')==0 )
		{
			return;
		}
		else
		{					
				nocache = Math.random();
				var style_from_library_1	= $("#style_from_library_1").val();
				eval(get_submitted_variables('style_from_library_1*update_id'));
				var data="action=save_update_delete&operation="+operation+"&style_from_library_1="+style_from_library_1+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
				freeze_window(operation);
				http.open("POST","requires/merchandising_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
		
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==82)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_bom_yarn_approval*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==83)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_excut_source*cbo_style_from_library*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==87)
	{
		if ( form_validation('cbo_company_name_wo','Company Name')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			//eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_stripe_yarn_details_calculation*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
			
	//Default fabric SOURCE
	else if (document.getElementById('cbo_variable_list_wo').value*1==88) //cbo_bom_page
	{
		if ( form_validation('cbo_company_name_wo*cbo_variable_list_wo*cbo_gsm','Company Name*Variable List*Page Name')==0 )
		{
			return;
		}
		else
		{				
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_gsm*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}

	else if (document.getElementById('cbo_variable_list_wo').value*1==89) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_short_quatation_on_budget','Company Name*Short Quatation  On Budget ')==0 )
		{
			return;
		}
		else
		{				
			var short_quatation_on_budget = $("#cbo_short_quatation_on_budget").val();
			
			var data="action=save_update_delete&operation="+operation+"&short_quatation_on_budget="+short_quatation_on_budget+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==91) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_service_booking_dying_amount_vali','Company Name*Service Booking Dyeing Amount validation ')==0 )
		{
			return;
		}
		else
		{				
			var cbo_service_booking_dying_amount_vali = $("#cbo_service_booking_dying_amount_vali").val();
			
			var data="action=save_update_delete&operation="+operation+"&cbo_service_booking_dying_amount_vali="+cbo_service_booking_dying_amount_vali+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
	else if (document.getElementById('cbo_variable_list_wo').value*1==93)
	{
		
		if ( form_validation('cbo_company_name_wo*cbo_act_po','Company Name*Act PO')==0 )
		{
			return;
		}
		else
		{					
			nocache = Math.random();
			var cbo_act_po_td	= escape(document.getElementById('cbo_act_po_td').innerHTML);
			eval(get_submitted_variables('cbo_company_name_wo*cbo_variable_list_wo*cbo_act_po*update_id'));
			var data="action=save_update_delete&operation="+operation+"&sales_year_started="+cbo_act_po_td+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*cbo_act_po*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}else if (document.getElementById('cbo_variable_list_wo').value*1==94) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_validation_yes_no','Company Name*Select validation Option')==0 )
		{
			return;
		}
		else
		{				
			var cbo_validation_yes_no = $("#cbo_validation_yes_no").val();
			
			var data="action=save_update_delete&operation="+operation+"&cbo_validation_yes_no="+cbo_validation_yes_no+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/merchandising_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	}
}	


function fnc_order_tracking_variable_settings_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		//document.getElementById('update_id').value  = reponse[2];
		set_button_status(0, permission, 'fnc_order_tracking_variable_settings',1);
		reset_form('ordertrackingvariablesettings_1','variable_settings_container','');
		release_freezing();
	}
}	

function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}
	
function users_popup(page_link,title)
{
 	var data= document.getElementById('user_hidden_id').value;
	page_link=page_link+"&data="+data;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=210px,height=320px,center=1,resize=1,scrolling=0','../')
emailwindow.onclose=function()
        {
            var selected_id=this.contentDoc.getElementById("txt_selected_id")          
            var selected_name=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
           
                document.getElementById('users_name_id').value=selected_name.value;	
                document.getElementById('user_hidden_id').value=selected_id.value;	           
        }
}	
function fnc_check_yes_no(type)
{
		//var cbo_excess_cut_source= $("#cbo_excess_cut_source").val();
		if(type==1 || type==3)
		{
			$('#cbo_editable_id').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_editable_id').removeAttr('disabled','disabled');
		}
		
		//
}

function ena_dib(val,i){
	if(val==1){
		$('#txt_exeed_qty_'+i).val(0);
		$('#txt_exeed_qty_'+i).attr('disabled','disabled');
	}else{
		//$('#txt_exeed_qty_'+i).val(0);
		$('#txt_exeed_qty_'+i).removeAttr('disabled','disabled');
	}
}
function fnc_check_field(type)
{
	if(type==2 || type==0) //No
	{
		$('#cbo_capacity_exceed_level').attr('disabled','disabled');
		$('#cbo_capacity_exceed_level').val(0);
	}
	else
	{
		$('#cbo_capacity_exceed_level').removeAttr('disabled','disabled');
	}
}
</script>

</head>

<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:850px;">
		<legend>Merchandising Variable Settings</legend>
		<form name="ordertrackingvariablesettings_1" id="ordertrackingvariablesettings_1" autocomplete="off">	
      			<table  width="750" cellspacing="2" cellpadding="0" border="0">
            		<tr>
                		<td width="200" align="left" class="must_entry_caption">Company Name</td>
                        <td width="250"><? echo create_drop_down( "cbo_company_name_wo", 250, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '--- Select Company ---', '', "show_list_view(document.getElementById('cbo_variable_list_wo').value+'_'+this.value+'_'+document.getElementById('garments_nature').value,'on_change_data','variable_settings_container','requires/merchandising_settings_controller','')", '' ); ?></td>
                		<td width="200" align="center">Variable List</td>
                        <td width="250" class="must_entry_caption">
							<? asort($order_tracking_module);
                                echo create_drop_down( "cbo_variable_list_wo", 250, $order_tracking_module,'', '1', '---- Select ----', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_name_wo').value+'_'+document.getElementById('garments_nature').value,'on_change_data','variable_settings_container','requires/merchandising_settings_controller','')",''); ?>
                        </td>
            		</tr>
        		</table>
            <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container">
            </div>
		</form>	
	</fieldset>
    </div>
 </body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>    

