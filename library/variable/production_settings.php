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
if( $_SESSION['logic_erp']['user_id'] == "" ) header(":login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Tracking Variable Settings", "../../", 1, 1,$unicode,'','');

$smv_source_arr=array(1=>"From Order Entry",2=>"From Pre-Costing",3=>"From GSD Entry");
$barcode_generation_arr=array(1=>"From System",2=>"External Device For Barcode");

?>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

<script language="javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	function fnc_load_preceding_process(data)
	{

 		load_drop_down( 'requires/production_settings_controller',data , 'load_drop_down_preceding_process', 'preceding_td' );

	}
	function fnc_enable_disable_excess(type,data,index)
	{
		if(type ==35)
		{
			if(data==1)
			{
				//$("#excess_title").css("display","block");
				$("#txtExcessPercent_"+index).css("display","block");
				$("#txtExcessQntyKG_"+index).css("display","block");
			}
			else
			{
				//$("#excess_title").hide();
				$("#txtExcessPercent_"+index).css("display","none");
				$("#txtExcessQntyKG_"+index).css("display","none");
				$("#txtExcessPercent_"+index).val('');
				$("#txtExcessQntyKG_"+index).val('');
			}
		}
		else if(type ==51)
		{
			if(data==1)
			{
				//$("#excess_title").css("display","block");
				$("#txtExcessPercent_"+index).css("display","block");
				$("#txtExcessQntyKG_"+index).css("display","block");
			}
			else
			{
				//$("#excess_title").hide();
				$("#txtExcessPercent_"+index).css("display","none");
				$("#txtExcessQntyKG_"+index).css("display","none");
				$("#txtExcessPercent_"+index).val('');
				$("#txtExcessQntyKG_"+index).val('');
			}
		}
		else if(type ==15)
		{
			if(data==2)
			{
				//$("#cbo_receive_basis_"+index).css("display","block");
				$("#rcvBasisId").css("display","block");

			}
			else
			{
				//$("#cbo_receive_basis_"+index).css("display","none");
				$("#rcvBasisId").css("display","none");
				$("#cbo_receive_basis_"+index).val('0');
				
			}
		}
	}

	function fnc_excess_field_vali(field_id,index_sl)
	{
		if(field_id== "txtExcessQntyKG_"+index_sl)
		{
			if($("#txtExcessQntyKG_"+index_sl).val()*1 >0)
			{
				$("#txtExcessPercent_"+index_sl).val('');
			}
		}
		else if(field_id== "txtExcessPercent_"+index_sl)
		{
			if($("#txtExcessPercent_"+index_sl).val()*1 >0)
			{
				$("#txtExcessQntyKG_"+index_sl).val('');
			}
		}
	}

	function fnc_load_preceding_process_sweater(data)
	{

 		load_drop_down( 'requires/production_settings_controller',data , 'load_drop_down_preceding_process_sweater', 'preceding_td' );

	}
	function chk_fnc()
	{
		if($('#apply_for_id').is(":checked")){
			$('#apply_for_id').val(1);
		}
		else
		{
			$('#apply_for_id').val(0);
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

	function fnc_production_variable_settings(operation){
		// alert(operation);
		/*if(operation==1)
		{
			alert("Update Is Restricted in Variable Settings");
			return;
		}*/
		if(operation==2)
		{
			alert("Delete Is Restricted in Variable Settings");
			return;
		}
		// if(operation==2)
		// {
		// 	show_msg('13');
		// 	return;
		// }
		//var update_id						= escape(document.getElementById('update_id').value);
		var cbo_company_name_production		= escape(document.getElementById('cbo_company_name_production').value);
		var cbo_variable_list_production	= escape(document.getElementById('cbo_variable_list_production').value);

		if ( form_validation('cbo_company_name_production*cbo_variable_list_production','Company Name*Variable List')==0 )
		{
			return;
		}
		

		if (cbo_variable_list_production==1)
		{

			/*	var cutting_update				= escape(document.getElementById('cutting_update').innerHTML);
				var printing_emb_production		= escape(document.getElementById('printing_emb_production').innerHTML);
				var sewing_production			= escape(document.getElementById('sewing_production').innerHTML);
				var iron_update					= escape(document.getElementById('iron_update').innerHTML);
				var finishing_update			= escape(document.getElementById('finishing_update').innerHTML);
				var production_entry			= escape(document.getElementById('production_entry').innerHTML);
				var cutting_delevary_input		= escape(document.getElementById('cutting_delevery_entry').innerHTML);*/
				
				var cutting_update				= escape(document.getElementById('cutting_update').innerHTML);
				var printing_emb_production		= escape(document.getElementById('printing_emb_production').innerHTML);
				var sewing_production			= escape(document.getElementById('sewing_production').innerHTML);
				var iron_update					= escape(document.getElementById('iron_update').innerHTML);
				var finishing_update			= escape(document.getElementById('finishing_update').innerHTML);
				//var production_entry			= escape(document.getElementById('production_entry').innerHTML);
				var cutting_delevary_input		= escape(document.getElementById('cutting_delevery_entry').innerHTML);
				
				var html_data ='&cutting_delevary_input_html='+cutting_delevary_input+'&cutting_update_html='+cutting_update+'&printing_emb_production_html='+printing_emb_production+'&sewing_production_html='+sewing_production+'&iron_update_html='+iron_update+'&finishing_update_html='+finishing_update;
				nocache = Math.random();

				eval(get_submitted_variables('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update*cbo_printing_emb_production*cbo_sewing_production*cbo_iron_update*cbo_finishing_update*cbo_ex_factory*cbo_leftover*cbo_leftover_country*cbo_leftover_source*cbo_finish_fabric_req_cutting*cbo_hang_tag_update*cbo_fin_gmt_transfer*cbo_wash_production*cbo_poly_update*cbo_fin_rcv_entry_update*cbo_buyer_inspection*cbo_wash_recive*update_id'));

				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update_to_input*cbo_cutting_update*cbo_printing_emb_production*cbo_sewing_production*cbo_iron_update*cbo_finishing_update*cbo_ex_factory*cbo_leftover*cbo_leftover_country*cbo_leftover_source*cbo_finish_fabric_req_cutting*cbo_hang_tag_update*cbo_fin_gmt_transfer*cbo_wo_maintain*cbo_wash_production*cbo_poly_update*cbo_fin_rcv_entry_update*cbo_buyer_inspection*cbo_wash_recive*update_id',"../../");
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
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_fabric_roll_level_1*update_id_1*cbo_item_category_2*cbo_fabric_roll_level_2*cbo_entry_form_roll_level_2*update_id_2*cbo_item_category_3*cbo_fabric_roll_level_3*cbo_entry_form_roll_level_3*update_id_3*cbo_item_category_4*cbo_fabric_roll_level_4*update_id_4*cbo_item_category_5*cbo_fabric_roll_level_5*update_id_5',"../../");

			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_roll_level;
		}
		else if(cbo_variable_list_production==4 || cbo_variable_list_production==69)
		{
				var fabric_machine_level_html	= escape(document.getElementById('fabric_machine_level').innerHTML);
				html_data = '&fabric_machine_level_html='+fabric_machine_level_html;

				var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_fabric_machine_level*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
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
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_auto_update_1*update_id_1*cbo_receive_basis_1*cbo_item_category_2*cbo_auto_update_2*update_id_2',"../../");

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
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_batch_no*cbo_yd_batch_no*cbo_add_year*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		
		else if(cbo_variable_list_production==68)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_replace_field_disable*update_id',"../../");
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
				data_all=data_all+get_submitted_data_string('shift_id'+i+'*update_id'+i+'*txt_prod_start_time'+i+'*txt_prod_end_time'+i+'*txt_lunch_start_time'+i+'*txt_lunch_end_time'+i,"../../",i)
			} //alert(data_all);return;

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production',"../../",i)+data_all+'&total_row='+row_num;
			freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}

		else if(cbo_variable_list_production==51)
		{
			var row_num=$('#fabric_details_tbl tbody tr').length;
			

			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				data_all=data_all+get_submitted_data_string('cbo_item_category_'+i+'*cbo_auto_update_'+i+'*update_id_'+i+'*txtExcessPercent_'+i+'*txtExcessQntyKG_'+i,"../../",i)
			}  
			//alert(data_all);return;
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
			var data="action=save_update_delete&operation="+operation+html_data+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cutting_update*cbo_iron_update*cbo_printing_emb_production*cbo_finishing_update*cbo_sewing_production*cbo_hangtag_production*update_id',"../../");
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
		else if(cbo_variable_list_production==33 || cbo_variable_list_production==50)
		{
			var control_value=$("#cbo_auto_update").val()*1;
			if(control_value==1)
			{
				var field="cbo_item_category*cbo_auto_update";
				var msg="Item Category*Control";

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
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_process_costing*cbo_rate_mandatory*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==35)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_auto_update_1*update_id_1*txtExcessPercent_1*txtExcessQntyKG_1*cbo_item_category_2*cbo_auto_update_2*update_id_2*txtExcessPercent_2*txtExcessQntyKG_2',"../../");

				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==36 || cbo_variable_list_production==45)
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
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*cbo_batch_selection*update_id',"../../");
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
        else if(cbo_variable_list_production==46 || cbo_variable_list_production==61)
        {
        	//alert(46);
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_rate_source*update_id',"../../");
            //freeze_window(operation);
            http.open("POST","requires/production_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_production_variable_settings_reponse;
        }
		else if(cbo_variable_list_production==48)//Mandatory QC For Delivery
        {
        	//alert(46);
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_qc_mandatory_for_delivery_1*update_id_1*cbo_item_category_2*cbo_qc_mandatory_for_delivery_2*update_id_2',"../../");
            //alert(data);//return;
            //freeze_window(operation);
            http.open("POST","requires/production_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
        }

        else if(cbo_variable_list_production==47)//Production Auto Production quantity update by QC
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_1*cbo_auto_update_1*update_id_1*cbo_item_category_2*cbo_auto_update_2*update_id_2',"../../");
			//alert(data);
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if (cbo_variable_list_production==49)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*txt_max_roll_weight*update_id',"../../");
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		 
		else if(cbo_variable_list_production==52)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_textile_business*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==53)//is_controll
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_sample_delivery_source*update_id',"../../");
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==54)//
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_stock_qty*update_id',"../../");
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==55)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==56 || cbo_variable_list_production==57 || cbo_variable_list_production==85 || cbo_variable_list_production==86)
		{
			if ( form_validation('cbo_company_name_production*cbo_variable_list_production','Company Name*Variable List')==false )
			{
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_store_name*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==58 || cbo_variable_list_production==59 || cbo_variable_list_production==65 || cbo_variable_list_production==67 || cbo_variable_list_production==71 || cbo_variable_list_production==72)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_production_entry*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==60)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_production_entry*cbo_style_attach*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==66 || cbo_variable_list_production==84)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_production_entry*update_id*cbo_process_loss_editable*cbo_po_level',"../../");
				freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==70)
		{
				//var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_production_entry*cbo_variable_auto_print*cbo_variable_apply_for*update_id',"../../");
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_production_entry*cbo_production_entry_2*apply_for_id*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==62)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_hide_qc_result*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==63 || cbo_variable_list_production==64) 
		{
			// 63 = Dyeing Production Control based on chemical issue, 64= Service Booking Mandatory For Outbound Subcontact Knitting
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_chemical_issue*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==74)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_item_category_A1*cbo_item_category_A2*cbo_item_category_B1*cbo_item_category_B2*update_id',"../../");

				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==73)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_source*txt_rate_allowe*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==75)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_fabric_allow_fabRecv*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==76)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_fabric_allow_fabRecv*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==77)
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_fin_fabric_issue_pro*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==78)// Lab Dip No From
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_labdip_no_from*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==79)// Data Update Period
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_entry_from*update_period*user_hidden_id*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==80)// Cut and Lay Available from Style  Wise Body part Entry
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_cut_lay_av_style*update_id',"../../");
			//freeze_window(operation);
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse;
		}
		else if(cbo_variable_list_production==81)// Sewing production Operation and Defect Control
		{
			//alert(operation); return 0;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*reject_action*alter_action*spot_action*reject_operation*alter_operation*spot_operation*reject_defect*alter_defect*spot_defect*update_id',"../../");
			
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
		else if(cbo_variable_list_production==82)//Piece Rate Work Order & Bill
		{
			let qty_label = rate_label = qty1 = qty2 = rate1 = rate2 = 0;

			qty1 = $("#qty_label_1").prop("checked");
			qty2 = $("#qty_label_2").prop("checked");
			rate1= $("#rate_label_1").prop("checked");
			rate2= $("#rate_label_2").prop("checked");
			if (qty1) 
			{
				qty_label = 1; // Color & Size Label 
			}
			else if(qty2)
			{
				qty_label = 2; // PO label
			}

			if (rate1) 
			{
				rate_label = 1; // Color & Size (Avg)
			}
			else if(rate2)
			{
				rate_label = 2; // Process Wise 
			}

			//alert(operation); return 0;
			var data="action=save_update_delete&operation="+operation+"&qty_label="+qty_label+"&rate_label="+rate_label+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*update_id',"../../");
			
			http.open("POST","requires/production_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update; 
		}
		else if(cbo_variable_list_production==83)
		{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name_production*cbo_variable_list_production*cbo_prod_resource*update_id',"../../");
				//freeze_window(operation);
				http.open("POST","requires/production_settings_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_production_variable_settings_reponse_for_auto_update;
		}
	}

	function func_operation(_this){
		var selected_id = $(_this).attr('id');
		var selected_val = $(_this).val();


		if(selected_id == 'reject_operation'){
			if(selected_val == 1){
				$("#reject_defect").val(1).trigger('change').attr("disabled", true);
			}else{
				$("#reject_defect").val(0).trigger('change').attr("disabled", false);
			}
		}

		if(selected_id == 'alter_operation'){
			if(selected_val == 1){
				$("#alter_defect").val(1).trigger('change').attr("disabled", true);
			}else{
				$("#alter_defect").val(0).trigger('change').attr("disabled", false);
			}
		}
		
		if(selected_id == 'spot_operation'){
			if(selected_val == 1){
				$("#spot_defect").val(1).trigger('change').attr("disabled", true);
			}else{
				$("#spot_defect").val(0).trigger('change').attr("disabled", false);
			}
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

			if(cbo_variable_list_production==26 || cbo_variable_list_production==82)
			{
				if(reponse[0]==0 || reponse[0]==1)
				{
					set_button_status(1, permission, 'fnc_production_variable_settings',1);
					show_list_view(cbo_variable_list_production+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');
				}
			}
			else
			{
				document.getElementById('update_id').value  = reponse[2];
				set_button_status(0, permission, 'fnc_production_variable_settings',1);
				reset_form('productionVariableSettings','variable_settings_container','');
			}
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
	function details_reset()
	{
		var cbo_variable = $('#cbo_variable_list_production').val();
		if(cbo_variable == 56)
		{
			$('#cbo_variable_list_production').val("0");
			$("#variable_settings_container").html("");
		}
	}
	//cbo_entry_form_roll_level_2
	function change_process_loss_editable_option(value){

		if (value==2) {

			$("#cbo_process_loss_editable").attr("disabled",false);
		}
		else{

			$("#cbo_process_loss_editable").attr("disabled",true);
			$("#cbo_process_loss_editable").val(0);
		}
	}

	function fn_year_change(batch_no_yes_no)
	{
		if(batch_no_yes_no==1)
		{
			$("#cbo_add_year").attr("disabled",true);
			$("#cbo_add_year").val(0);
		}
		else
		{
			$("#cbo_add_year").attr("disabled",false);
		}
	}
	function fn_disabled_field(source)
	{
		if(source==1)
		{
			$("#txt_rate_allowe").attr("disabled",true);
			$("#txt_rate_allowe").val('');
		}
		else
		{
			$("#txt_rate_allowe").attr("disabled",false);
		}
	}
	function setRateLabel(val)
	{
		if (val==1) 
		{
			$("#rate_label_1").attr('checked', 'checked');
		}
		else if(val==2)
		{
			$("#rate_label_2").attr('checked', 'checked'); 
		}
		else if(val==11)
		{
			$("#qty_label_1").attr('checked', 'checked'); 
		}
		else if(val==22)
		{
			$("#qty_label_2").attr('checked', 'checked'); 
		}
	}
</script>
</head>

<body  onLoad="set_hotkey();">
	<div align="center" style="width:100%;">

		<? echo load_freeze_divs ("../../",$permission);  ?>

        <fieldset style="width:850px;">
            <legend>Production Variable Settings</legend>
            <form name="productionVariableSettings" id="productionVariableSettings" >
                    <table  width="850px" cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td width="150" align="center" class="must_entry_caption">Company</td>
                            <td width="300">
                                <?
                                    echo create_drop_down( "cbo_company_name_production", 250, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--- Select Company ---', 0, "details_reset();show_list_view(document.getElementById('cbo_variable_list_production').value+'_'+this.value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');" );
                                    
                                ?>
                            </td>
                            <td width="150" align="center" class="must_entry_caption">Variable List</td>
                            <td width="300">
                                <?
                                echo create_drop_down( "cbo_variable_list_production", 250, $production_module,'', '1', '---- Select ----', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_name_production').value,'on_change_data','variable_settings_container','../variable/requires/production_settings_controller','');",'','','','','42,43'); //data, action, div, path, extra_func
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
