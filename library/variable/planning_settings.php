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
if( $_SESSION['logic_erp']['user_id'] == "" ) header("

	:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Tracking Variable Settings", "../../", 1, 1,$unicode,1,1);

$smv_source_arr=array(1=>"From Order Entry",2=>"From Pre-Costing",3=>"From GSD Entry");
$barcode_generation_arr=array(1=>"From System",2=>"External Device For Barcode");

?> 

<script language="javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

	function fnc_load_preceding_process(data)
	{
 		
 		load_drop_down( 'requires/production_settings_controller',data , 'load_drop_down_preceding_process', 'preceding_td' );

	}

	function fnc_production_variable_settings(operation)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		//var update_id						= escape(document.getElementById('update_id').value);
		var cbo_company_name_production		= escape(document.getElementById('cbo_company_name_production').value);
		var cbo_variable_list_production	= escape(document.getElementById('cbo_variable_list_production').value);
		
		if ( form_validation('cbo_company_name_production*cbo_variable_list_production','Company Name*Variable List')==0 )
		{
			return;
		}

		if (cbo_variable_list_production==1)
		{
					
				var cutting_update				= escape(document.getElementById('cutting_update').innerHTML);
				var printing_emb_production		= escape(document.getElementById('printing_emb_production').innerHTML);
				var sewing_production			= escape(document.getElementById('sewing_production').innerHTML);
				var iron_update					= escape(document.getElementById('iron_update').innerHTML);
				var finishing_update			= escape(document.getElementById('finishing_update').innerHTML);
				var production_entry			= escape(document.getElementById('production_entry').innerHTML);
				var cutting_delevary_input		= escape(document.getElementById('cutting_delevery_entry').innerHTML);
				var html_data ='&cutting_delevary_input_html='+cutting_delevary_input+'&cutting_update_html='+cutting_update+'&printing_emb_production_html='+printing_emb_production+'&sewing_production_html='+sewing_production+'&iron_update_html='+iron_update+'&finishing_update_html='+finishing_update+'&production_entry_html='+production_entry;
				nocache = Math.random();					
				
				eval(get_submitted_variables('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update*cbo_printing_emb_production*cbo_sewing_production*cbo_iron_update*cbo_finishing_update*cbo_ex_factory*cbo_production_entry*update_id'));
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update_to_input*cbo_cutting_update*cbo_printing_emb_production*cbo_sewing_production*cbo_iron_update*cbo_finishing_update*cbo_ex_factory*cbo_production_entry*update_id',"../../");
				//alert(data)
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
		
		}
		else if(cbo_variable_list_production==2)
		{
			
			var data_part="";
			var ct=0;
			$("#tbl_slab tbody tr").each(function() {
				ct++;
				data_part = data_part+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*update_id*txt_slab_rang_start'+ct+'*txt_slab_rang_end'+ct+'*txt_excess_percent'+ct,"../../");
			});
			var data="action=save_update_delete&operation="+operation+'&counter='+ct+data_part;
	 
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==3)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_fabric_roll_level_1*update_id_1*cbo_item_category_2*cbo_fabric_roll_level_2*cbo_entry_form_roll_level_2*update_id_2*cbo_item_category_3*cbo_fabric_roll_level_3*update_id_3*cbo_item_category_4*cbo_fabric_roll_level_4*update_id_4*cbo_item_category_5*cbo_fabric_roll_level_5*update_id_5',"../../");
				
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_roll_level;
			
		}
		else if(cbo_variable_list_production==4)
		{
				var fabric_machine_level_html	= escape(document.getElementById('fabric_machine_level').innerHTML);
				html_data = '&fabric_machine_level_html='+fabric_machine_level_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_fabric_machine_level*update_id',"../../");
				
				
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==5)
		{
				var distribute_qnty_html = escape(document.getElementById('distribute_qnty').innerHTML);
				html_data = '&distribute_qnty_html='+distribute_qnty_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_distribute_qnty*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==6)
		{			
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_auto_allocate_yarn*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==7)
		{			
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_rms_integretion*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==8)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_color_mandatory*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==9 || cbo_variable_list_production==10)
		{
				var distribute_qnty_html	= escape(document.getElementById('distribute_qnty').innerHTML);
				html_data = '&distribute_qnty_html='+distribute_qnty_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_work_study*cbo_smv*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==11)
		{
			var row_num=$('#tbl_list_search tr').length;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				data_all=data_all+get_submitted_data_string('txt_bulletin_type_'+i+'*cbo_is_editiable_'+i+'*update_id_'+i,"../../",i)	
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production',"../../",i)+data_all+'&total_row='+row_num;
			freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
        else if(cbo_variable_list_production==12)
		{
			
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*txt_bulletin_type*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==154)
		{
			
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*txt_bulletin_type*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==13)
		{
				var batch_maintained_html	= escape(document.getElementById('batch_maintained').innerHTML);
				html_data = '&batch_maintained_html='+batch_maintained_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_batch_maintained*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==15)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_auto_update_1*update_id_1*cbo_item_category_2*cbo_auto_update_2*update_id_2',"../../");
				
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==23)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_prod_resource*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==24)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_batch_no*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==25)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==26)
		{
			var row_num=$('#tbl_list_search tr').length-1;
			
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				data_all=data_all+get_submitted_data_string('shift_id'+i+'*update_id'+i+'*txt_prod_start_time'+i+'*txt_lunch_start_time'+i,"../../",i)	
			} //alert(data_all);return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production',"../../",i)+data_all+'&total_row='+row_num;
			freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==27)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if (cbo_variable_list_production==28)
		{
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update*cbo_iron_update*cbo_printing_emb_production*cbo_finishing_update*cbo_sewing_production*update_id*update_id',"../../");
			//alert(data)
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if (cbo_variable_list_production==29)
		{
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_piece_rate_wo_limit*update_id',"../../");
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if (cbo_variable_list_production==30)
		{
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*txt_cut_sefty_parcent*txt_sewing_sefty_parcent*txt_iron_sefty_parcent*txt_finish_sefty_parcent*update_id',"../../");
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==31)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_prod_resource*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==32)
		{
				var cut_panel_basis_html	= escape(document.getElementById('cut_panel_basis').innerHTML);
				html_data = '&cut_panel_basis_html='+cut_panel_basis_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cut_panel_basis*update_id',"../../");
				//alert(data)
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==33)
		{
			var control_value=$("#cbo_auto_update").val()*1;
			if(control_value==1)
			{
				var field="cbo_item_category*cbo_auto_update*cbo_preceding_item_category";
				var msg="Item Category*Control*Preceding Process";

			}
			if(control_value==2 || control_value==0)
			{
				var field="cbo_item_category*cbo_auto_update";
				var msg="Item Category*Control";
			}
		
			if( form_validation(field,msg)==false )
			{
				return;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category*cbo_auto_update*update_id*cbo_preceding_item_category',"../../");
				//alert(data);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;

				}
			
		}
		else if(cbo_variable_list_production==34)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_process_costing*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==35)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_auto_update_1*update_id_1*cbo_item_category_2*cbo_auto_update_2*update_id_2',"../../");
				
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==36)
		{
			var row_num=$('#table_body tbody tr').length;
			//alert(row_num);return;
			var data_string="";
			for(var i=1;i<=row_num;i++)
			{
				//var fabricGrade=$("#fabricGrade_"+i).val();
				data_string+=get_submitted_data_string('fabricGrade_'+i+'*cboGetUptoFirst_'+i+'*valueFirst_'+i+'*cboGetUptoSecond_'+i+'*valueSecond_'+i,"../../",i);
				//data_string+='&fabricGrade_'+i+'='+fabricGrade;
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production',"../../")+data_string+'&row_num='+row_num;
			//alert(data);return;
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==37 || cbo_variable_list_production==39)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==38)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==40)
		{
			if ( form_validation('cbo_service_process*cbo_service_rate','Service Type*Status')==0 )
			{
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_service_process*cbo_service_rate*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==41)
		{
			var working_company_mandatory_html	= escape(document.getElementById('fabric_machine_level').innerHTML);
			html_data = '&working_company_mandatory_html='+working_company_mandatory_html;
			
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_working_company_mandatory*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==42 || cbo_variable_list_production==43)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_qtySource*update_id',"../../");
			
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
        else if(cbo_variable_list_production==44)
        {
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_bill_on*update_id',"../../");
            //freeze_window(operation);
            http.open("POST","requires/production_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_production_variable_settings_reponse;
        }      
		else if(cbo_variable_list_production==53)
		{			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_color_mixing*cbo_is_coller_cuff*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==54)
		{			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_capacity_allocation*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==155)
		{
			var fabric_machine_level_html	= escape(document.getElementById('fabric_machine_level').innerHTML);
			html_data = '&fabric_machine_level_html='+fabric_machine_level_html;
			
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_pattern_numbering_sequence*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==156)
		{			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_allocation_control*txt_minimum_available_qty*txt_age_limit*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==159)
		{			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_pcq*txt_value*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/planning_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==157)
		{			
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_machine_mixing*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==158)
		{			
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_auto_balancing*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==160)
		{
				var distribute_qnty_html = escape(document.getElementById('distribute_qnty').innerHTML);
				html_data = '&distribute_qnty_html='+distribute_qnty_html;
				
				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_is_distribute_qnty*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==161)
		{
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_country_sequence*update_id',"../../");

				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		else if(cbo_variable_list_production==162)
		{
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_order_priority*update_id',"../../");

				//freeze_window(operation);
				http.open("POST","requires/planning_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
			
		}
		
		
	
		
	}

	function fnc_production_variable_settings_reponse()
	{
		if(http.readyState == 4) 
		{
			var cbo_variable_list=$('#cbo_variable_list_production').val();

			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			
			if(cbo_variable_list==26)
			{
				if(reponse[0]==0 || reponse[0]==1)
				{
					set_button_status(1, permission, 'fnc_production_variable_settings',1);
					show_list_view(cbo_variable_list_production+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');
				}
			}
			if(cbo_variable_list==11)
			{
				if(reponse[0]==0 || reponse[0]==1)
				{
					set_button_status(1, permission, 'fnc_production_variable_settings',1);
					show_list_view(cbo_variable_list+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/planning_settings_controller','');
				}
			}
			if(cbo_variable_list==12)
			{
				if(reponse[0]==0 || reponse[0]==1)
				{
					set_button_status(1, permission, 'fnc_production_variable_settings',1);
					show_list_view(cbo_variable_list+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/planning_settings_controller','');
				}
			}
			else
			{
			   document.getElementById('update_id').value  = reponse[2]; 
			   set_button_status(0, permission, 'fnc_production_variable_settings',1);

			   if(cbo_variable_list!=8 && cbo_variable_list!=9 && cbo_variable_list!=10 && cbo_variable_list!=53 && cbo_variable_list!=54)
			   {
			   		reset_form('productionVariableSettings','variable_settings_container','');
			   }
			}			
			set_button_status(1, permission, 'fnc_production_variable_settings',1);
			release_freezing();
			
			
			
		}
	}	

	function fnc_production_variable_settings_reponse_for_roll_level()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1)
			{
				set_button_status(1, permission, 'fnc_production_variable_settings',1);
				show_list_view(document.getElementById('cbo_variable_list_production').value+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');
			}
			
			release_freezing();
		}
	}	

	function fnc_production_variable_settings_reponse_for_auto_update()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1)
			{
				set_button_status(1, permission, 'fnc_production_variable_settings',1);
				show_list_view(document.getElementById('cbo_variable_list_production').value+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');
			}
			 
			
			release_freezing();
		}
	}	

	function next_number(i)
	{
		var j=i-1;
		if(i!=1)
		{
			var aa = document.getElementById('txt_slab_rang_start'+i).value;
			var bb = document.getElementById('txt_slab_rang_end'+j).value;
			var k=aa*1-bb*1;
			//alert(aa+'k='+k);
			if(k>1 || k<=0) 
			{
				document.getElementById('txt_slab_rang_start'+i).value="";
				alert('Please Enter Next Slab Range End Number');
			}
		}
	}
		
	function add_variable_row( counter_id ) 
	{
		var rowCount = document.getElementById('tbl_slab').rows.length;
 		if(counter_id<=10)
		{
			if (counter_id!=rowCount)
			{
				return false;
			}			
			counter_id++;			
			$('#tbl_slab tbody').append(
				'<tr id="po_' + counter_id + '">'
					+ '<td><input	type="text"	name="txt_slab_rang_start' + counter_id + '" autocomplete="off" style="width:150px;" onchange="next_number('+counter_id+')" class="text_boxes_numeric" id="txt_slab_rang_start' + counter_id + '"		value="" 	/></td>'
					+ '<td><input	type="text"	name="txt_slab_rang_end' + counter_id + '" 	autocomplete="off" style="width:150px; " class="text_boxes_numeric" id="txt_slab_rang_end' + counter_id + '"		value="" 	/></td>'
					+ '<td><input	type="text"	name="txt_excess_percent' + counter_id + '"	autocomplete="off" style="width:150px " class="text_boxes_numeric" id="txt_excess_percent' + counter_id + '"		value=""	onfocus="add_variable_row( ' + counter_id + ' );" 	/></td>'
 				+ '</tr>'
			);
		}
	}
	
	function fnc_valid_time(val,field_id)
	{
		var colon_contains=val.contains(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			
			if(str_length>2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}

				var valid_time=data[0]+":"+minutes;
				document.getElementById(field_id).value=valid_time;
			}
		}
	}
	
	function add_break_down_tr(i)
	{
		var row_num=$('#table_body tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			i++;
		    var k=i-1;
			$("#table_body tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i }
			});
			}).end().appendTo("#table_body");
			
			$("#table_body tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
			$('#fabricGrade_'+i).val('');
			$('#valueFirst_'+i).val('');
			$('#valueSecond_'+i).val('');
			
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+i+");");
		}
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{
		if(rowNo>1)
		{  
			$('#tr_'+rowNo).remove();
		}
	}
	
	function search_populate(type)
	{
		if(type==1)
		{
			$('#cbo_fabric_roll_level_2').focus();   
			$("#cbo_entry_form_roll_level_2").removeAttr("disabled",true); 
			 document.getElementById('upto_td').innerHTML="Upto Receive By Batch";
		}
		else
		{
			$('#cbo_fabric_roll_level_2').focus();
			$('#cbo_entry_form_roll_level_2').val('');   
			$("#cbo_entry_form_roll_level_2").attr("disabled",true); 
			 document.getElementById('upto_td').innerHTML="";
		}
	}
	
	function search_populate2(type)
	{
		if(type==7)
		{
			$('#cbo_fabric_roll_level_3').focus();   
			$("#cbo_fabric_roll_level_3").removeAttr("disabled",true); 
			// document.getElementById('upto_td').innerHTML="Upto Receive By Batch";
		}
		else
		{
			$('#cbo_fabric_roll_level_3').focus();
			//$('#cbo_entry_form_roll_level_2').val('');   
			$("#cbo_fabric_roll_level_3").attr("disabled",true); 
			 //document.getElementById('upto_td').innerHTML="";
		}
	}
	//cbo_entry_form_roll_level_2
	function setMultiSelect(variable_type){
		if(variable_type==4){
			set_multiselect('cbo_fabric_machine_level','2','0','0','0');
			set_multiselect('cbo_fabric_machine_level','2','1',document.getElementById('planning_board_strip_caption_val').value,'0');
		}
	}

	function enable_disable(cbo_is_work_study)
	{
		//console.log(cbo_is_work_study);
		if (cbo_is_work_study == 1) {
			$("#cbo_smv").val(1);
			$("#cbo_smv").removeAttr("disabled");
        } else {
			$("#cbo_smv").val(0);
			$("#cbo_smv").attr("disabled","disabled");
        }
	}

	function enable_disable_plan(cbo_pcq)
	{
		//console.log(cbo_is_work_study);
		if (cbo_pcq == 1) {
			$("#txt_value").val();
			$("#txt_value").removeAttr("disabled");
        } else {
			$("#txt_value").val('');
			$("#txt_value").attr("disabled","disabled");
        }
	}
</script>
</head>

<body  onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
     
		<? echo load_freeze_divs ("../../",$permission);  ?>
        
        <fieldset style="width:850px;">
            <legend>Planning Variable Settings</legend>
            <form name="productionVariableSettings" id="productionVariableSettings" >
                    <table  width="850px" cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td width="150" align="center" class="must_entry_caption">Company</td>
                            <td width="300">
                                <? 
                                    echo create_drop_down( "cbo_company_name_production", 250, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--- Select Company ---', 0, "show_list_view(document.getElementById('cbo_variable_list_production').value+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/planning_settings_controller','');setMultiSelect(document.getElementById('cbo_variable_list_production').value);" );
                                ?>
                            </td> 
                            <td width="150" align="center" class="must_entry_caption">Variable List</td>
                            <td width="300">
                                <? 
								/* 
								@note: do not mix array index with $production_module and $planning_board array
								@cause: both array data store in same table
								@table Name: VARIABLE_SETTINGS_PRODUCTION 
								*/
								$planning_board=array(4=>"Planning Board Strip Caption",5=>"Show Distribute Quantity field in Yarn Requisition",6=>"Auto Allocated Yarn From Requisition",7=>" RMS Integration Planning Info Entry For Sales Order",8=>"Color Type Mandatory",9=>"Work Study Integrated In Planning",10=>"Cut & Lay Size Disable Status",11=>"SMV Editable In Work Study",12=>"Plan Level",53=>"Color Mixing In Knitting Plan",54=>"Capacity Allocation",154=>"Learning Curve Method",155=>"Pattern Numbering Sequence",156=>"Age wise Yarn Selection in Yarn Allocation",157=>"Machine Mixing In Knitting Plan",158=>"Auto Balance For Planning Board",159=>"Sewing Planning Quantity Limit",160=>"Cut and Lay Fab. Conj. Validation",161=>"Woven Cut and Lay Country Sequence",162=>"Woven Cut and Lay Order Priority");
								// asort($planning_board);
                                echo create_drop_down( "cbo_variable_list_production", 250, $planning_board,'', '1', '---- Select ----', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/planning_settings_controller','');setMultiSelect(this.value);",'','','','','42,43'); //data, action, div, path, extra_func 
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
