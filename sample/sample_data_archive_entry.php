<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Checklist
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	26-JULY-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
 //--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Data Archive Entry", "../", 1, 1,'','','');
if (!$TabIndexNo) {
	$TabIndexNo = 0;
}

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
 	 
	
	 
	//========Yarn Save Area================
	function fnc_yarn_entry( operation ) //
	{
	 var cbo_fabrication=$("#cbo_yarn_fabrication").val();
	 if(cbo_fabrication>0)
	 {
	  var fabric_desc= $("#cbo_yarn_fabrication :selected").text();
	 }
	 else fabric_desc=''; 
		//   alert(fabric_desc);
	   if (form_validation('txt_booking_no*cbo_yarn_fab_color_code*cbo_yarn_fabrication','Booking No*Fabric Color*Fabric Desc')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_yarn&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*cbo_yarn_fab_color_code*cbo_yarn_fabrication*yarn_update_id*txt_yarn_color_type*cbo_yarn_composition_1*cbo_yarn_composition_2*cbo_yarn_composition_3*cbo_yarn_composition_4*cbo_yarn_count_1*cbo_yarn_count_2*cbo_yarn_count_3*cbo_yarn_count_4*cbo_yarn_brand_1*cbo_yarn_brand_2*cbo_yarn_brand_3*cbo_yarn_brand_4*cbo_lot_1*cbo_lot_2*cbo_lot_3*cbo_lot_4*txt_ratio_1*txt_ratio_2*txt_ratio_3*txt_ratio_4*txt_act_count_1*txt_act_count_2*txt_act_count_3*txt_act_count_4',"../")+'&fabric_desc='+fabric_desc;
			  //alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/yarn_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_entry_response2;
		}
	}
	
	function fnc_yarn_entry_response2()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('yarn_update_id').value = '';
				show_list_view(response[3], 'listview_yarn_info', 'yarn_entry_info_list', 'requires/yarn_info_entry_controller', 'setFilterGrid(\'tbl_details_yarn\',-1);');
				
 					reset_form('yarnentry_2','','');
				//alert(permission);
				
				set_button_status(0, permission, 'fnc_yarn_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}
	//=========Knitting==============================
	 
	function fnc_kntting_entry( operation ) // fnc_kntting_entry
	{
	 var construction=$("#cbo_construction").val();
	 if(construction>0)
	 {
	  var construction_desc= $("#cbo_construction :selected").text();
	 }
	 else construction_desc=''; 
		//   alert(fabric_desc);
	   if (form_validation('txt_booking_no*cbo_knit_fab_color_code*cbo_prog_no','Booking No*Fabric Color*Fabric Desc')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_knit&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*cbo_knit_fab_color_code*cbo_prog_no*knitting_update_id*cbo_construction*txt_dia*txt_greige_dia*txt_mc_no*txt_mc_dia*txt_dia_type*txt_required_gsm*txt_mc_gauge*txt_mc_type*txt_lycra_feeding*txt_greige_gsm*txt_mc_brand*txt_brand_dia_type*txt_remarks*txt_cotton*txt_polyester*txt_modal*txt_viscose*txt_nylon*txt_elastane*txt_others*txt_knit*txt_binding*txt_loop*txt_yarn_dyed*txt_no_of_color*txt_repeat*txt_no_of_feeder',"../")+'&construction_desc='+construction_desc;
			 //alert(data); 
			freeze_window(operation);
			http.open("POST","requires/knitting_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_kntting_entry_response;
		}
	}
	
	function fnc_kntting_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('knitting_update_id').value = '';
				show_list_view(response[3], 'listview_knit_info', 'knit_entry_info_list', 'requires/knitting_info_entry_controller', 'setFilterGrid(\'tbl_details_knit\',-1);');
				
 				reset_form('knitentry_2','','');
				//alert(permission);
				//$('#save3').removeClass('formbutton').addClass('formbutton_disabled');
				$('#save3').removeClass('formbutton_disabled').addClass('formbutton');
				 $('#save3').removeAttr('onclick').attr('onclick','fnc_kntting_entry(0);')
				 $('#update3').removeAttr('onclick').attr('onclick','')
				 $('#update3').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				 $('#Delete4').removeAttr('onclick').attr('onclick','')
				 $('#Delete4').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}
	
	//=========Batchitting==============================
	 
	function fnc_batch_entry( operation ) // 
	{
	   if (form_validation('txt_booking_no*cbo_batch_no*txt_dia_setting','Booking No*Batch No*Dia Setting')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_batch&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*batchting_update_id*cbo_fabric_batch_color_code*txt_dia_setting*txt_dia_extension*cbo_batch_no*txt_batching_speed*txt_hs_dia*txt_mc_name_brand*txt_batching_greige_dia*txt_batching_greige_gsm*txt_temperature*txt_hs_gsm*txt_no_of_chamber*txt_chemical*txt_overfeed*txt_batching_remarks*txt_no_burners*txt_speed_min*txt_intensity*txt_singeing*txt_burner_distance*txt_singeing_pos',"../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/batchting_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_batch_entry_response;
		}
	}
	
	function fnc_batch_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('batchting_update_id').value = '';
				show_list_view(response[3], 'listview_batch_info', 'batching_entry_info_list', 'requires/batchting_info_entry_controller', 'setFilterGrid(\'tbl_details_batch\',-1);');
				
 				reset_form('batchentry_2','','');
				//alert(permission);
				//$('#save3').removeClass('formbutton').addClass('formbutton_disabled');
				 $('#save4').removeClass('formbutton_disabled').addClass('formbutton');
				 $('#save4').removeAttr('onclick').attr('onclick','fnc_batch_entry(0);')
				 $('#update4').removeAttr('onclick').attr('onclick','')
				 $('#update4').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				 $('#Delete4').removeAttr('onclick').attr('onclick','')
				 $('#Delete4').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	//=========Dyeingitting==============================
	 
	function fnc_dyeing_entry( operation ) // 
	{
	 
	 
	   if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_dyeing&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*dyeing_update_id*cbo_dying_fab_color_code*cbo_dying_batch_no*txt_dyeing_scouring*txt_dyeing_recipe_num*cbo_dyeing_reactive*cbo_dyeing_both_part*txt_dyeing_enzyme_per*txt_dyeing_dyes_orginal*cbo_dyeing_direct*cbo_dyeing_wash*txt_dyeing_remarks*txt_dyeing_dyes_add*cbo_dyeing_disperse*cbo_dyeing_white',"../");
			 //alert(data); 
			freeze_window(operation);
			http.open("POST","requires/dyeing_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_entry_response;
		}
	}
	
	function fnc_dyeing_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('dyeing_update_id').value = '';
				show_list_view(response[3], 'listview_dyeing_info', 'dyeing_entry_info_list', 'requires/dyeing_info_entry_controller', 'setFilterGrid(\'tbl_details_dyeing\',-1);');
				
 				reset_form('dyeingentry_2','','');
				//alert(permission);
				//$('#save3').removeClass('formbutton').addClass('formbutton_disabled');
				$('#save5').removeClass('formbutton_disabled').addClass('formbutton');
				 $('#save5').removeAttr('onclick').attr('onclick','fnc_dyeing_entry(0);')
				 $('#update5').removeAttr('onclick').attr('onclick','')
				 $('#update5').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				 $('#Delete5').removeAttr('onclick').attr('onclick','')
				 $('#Delete5').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}
	//=========Mercerized and Sunforized==============================

	function fnc_merceSunfoRized_entry( operation )  
	{
	 
	 
	   if (form_validation('txt_booking_no*txt_mer_total_liquare','Booking No*Total Liquare')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_merceSunfoRized&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*merceSunfoRized_update_id*cbo_msrce_sunfo_fab_color_code*cbo_sunfo_batch_no*txt_mer_total_liquare*txt_mer_caustic_solution*txt_sun_temperature*txt_sun_taflon_pressureCompection*txt_mer_temperature*txt_mer_mc_speed*txt_sun_over_feed*txt_sun_speed*txt_mer_mercerizedph*txt_mer_normalWash*txt_sun_steam*txt_mer_aceticAcid*txt_mer_unloadingPH*txt_sun_remarks*txt_mer_remarks',"../");
			 //alert(data); 
			freeze_window(operation);
			http.open("POST","requires/merce_sunfo_rized_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_merceSunfoRized_entry_response;
		}
	}
	
	function fnc_merceSunfoRized_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('merceSunfoRized_update_id').value = '';
				show_list_view(response[3], 'listview_merceSunfoRized_info', 'merceSunfoRized_entry_info_list', 'requires/merce_sunfo_rized_entry_controller', 'setFilterGrid(\'tbl_details_merceSunfoRized\',-1);');
				
 				reset_form('merceSunfoRized_2','','');
				//alert(permission);
				//$('#save3').removeClass('formbutton').addClass('formbutton_disabled');
				 $('#save6').removeClass('formbutton_disabled').addClass('formbutton');
				 $('#save6').removeAttr('onclick').attr('onclick','fnc_merceSunfoRized_entry(0);')
				 $('#update6').removeAttr('onclick').attr('onclick','')
				 $('#update6').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				 $('#Delete6').removeAttr('onclick').attr('onclick','')
				 $('#Delete6').removeClass('formbutton').addClass('formbutton_disabled'); 
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}
	//=========Finishing==============================

	function fnc_finishing_entry( operation )  
	{
	 
	 
	   if (form_validation('txt_booking_no*txt_slitting_machine_no','Booking No* Machine No')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_finishing&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*finishing_update_id*cbo_finishing_fab_color_code*cbo_finishing_batch_no*txt_slitting_machine_no*txt_slitting_after_dia*txt_peach_machine_no*txt_peach_after_dia*txt_slitting_process*txt_slitting_remarks*txt_peach_fabric_speed*txt_peach_after_gsm*txt_peach_drum_rpm*txt_peach_tension*txt_stenter_machine_no*txt_stenter_dia_setting*txt_peach_remarks*txt_stenter_mc_brand*txt_stenter_paddar_pressure*txt_stenter_no_chamber*txt_stenter_used_chemical*txt_brushing_machine_no*txt_brushing_pile_rpm_2nd*txt_stenter_temperature*txt_stenter_after_dia*txt_brushing_fabric_speed*txt_brushing_counter_pile_rpm_2nd*txt_stenter_overfeed*txt_stenter_after_gsm*txt_brushing_tension*txt_brushing_after_dia*txt_stenter_speed*txt_stenter_remarks*txt_brushing_pile_rpm_1st*txt_brushing_after_gsm*txt_brushing_counter_pile_rpm_1st*txt_brushing_remarks*txt_dryer_machine_no*txt_dryer_vibration*txt_dryer_temperature*txt_dryer_used_chemical*txt_shearing_fabric_speed*txt_shearing_drum_rpm*txt_dryer_overfeed*txt_dryer_after_dia*txt_shearing_distance_blade*txt_shearing_after_dia*txt_dryer_speed*txt_dryer_after_gsm*txt_shearing_from_comber*txt_shearing_after_gsm*txt_dryer_dia_settings*txt_dryer_remarks*txt_shearing_remarks*txt_stenter_machine_no_apb*txt_stenter_dia_setting_apb*txt_compacting_machine_no*txt_compacting_steam_pressure*txt_stenter_mc_brand_apb*txt_stenter_padder_pressure_apb*txt_compacting_mc_brand*txt_compacting_blanket_pressure*txt_stenter_no_chamber_apb*txt_stenter_used_chemical_apb*txt_compacting_temperature*txt_compacting_after_dia*txt_stenter_temperature_apb*txt_stenter_after_dia_apb*txt_compacting_speed*txt_compacting_after_gsm*txt_stenter_overfeed_apb*txt_stenter_after_gsm_apb*txt_compacting_over_feed*txt_compacting_dia_setting*txt_stenter_speed_apb*txt_stenter_remarks_apb*txt_compacting_remarks*txt_fabWash_before_dia*txt_fabWash_time*finish_remarks*txt_fabWash_before_gsm*txt_fabWash_after_dia*txt_fabWash_temperature*txt_fabWash_after_gsm*txt_fabWash_remarks',"../");
			// alert(data); 
			freeze_window(operation);
			http.open("POST","requires/finishing_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finishing_entry_response;
		}
	}
	
	function fnc_finishing_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('finishing_update_id').value = '';
				show_list_view(response[3], 'listview_finishing_info', 'finishing_entry_info_list', 'requires/finishing_info_entry_controller', 'setFilterGrid(\'tbl_details_finishing\',-1);');
				
 				reset_form('finishingentry_2','','');
				//alert(permission);
				$('#save7').removeClass('formbutton_disabled').addClass('formbutton');
				$('#save7').removeAttr('onclick').attr('onclick','fnc_finishing_entry(0);')
				$('#update7').removeAttr('onclick').attr('onclick','')
				$('#update7').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				$('#Delete7').removeAttr('onclick').attr('onclick','')
				$('#Delete7').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	//=========Physical Test==============================

	function fnc_physicalTest_entry( operation )  
	{
	 
	   if (form_validation('txt_booking_no*txt_dry_process','Booking No* Dry process')==false)
		{
			return;
		}	
		else
		{ 

			var data="action=save_update_delete_physicalTest&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*physicalTest_update_id*cbo_physical_test_color_code*cbo_physical_batch_no*txt_dry_process*txt_actual_gsm*txt_phenolic_yellowing*txt_length*txt_test_method*txt_pilling*txt_cf_to_light*txt_width*txt_req_dia*txt_bursting_strength*txt_cf_to_saliva*txt_twisting*txt_actual_dia*txt_dry_rubbing*txt_wpi*txt_wash_temperature*txt_req_gsm*txt_wet_rubbing*txt_cpi*txt_acetate_1*txt_acetate_2*txt_acetate_3*txt_acetate_4*txt_acetate_5*txt_acetate_6*txt_Water_1*txt_Water_2*txt_Water_3*txt_Water_4*txt_Water_5*txt_Water_6*txt_perspiration_acid_1*txt_perspiration_acid_2*txt_perspiration_acid_3*txt_perspiration_acid_4*txt_perspiration_acid_5*txt_perspiration_acid_6*txt_perspiration_alkali_1*txt_perspiration_alkali_2*txt_perspiration_alkali_3*txt_perspiration_alkali_4*txt_perspiration_alkali_5*txt_perspiration_alkali_6*txt_physicaltest_remarks*txt_delivery_date',"../");
			 //alert(data); 
			freeze_window(operation);
			http.open("POST","requires/physical_test_Info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_physicalTest_entry_response;
		}
	}
	
	function fnc_physicalTest_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('physicalTest_update_id').value = '';
				show_list_view(response[3], 'listview_physicalTest_info', 'physicalTest_entry_info_list', 'requires/physical_test_Info_entry_controller', 'setFilterGrid(\'tbl_details_physicalTest\',-1);');
				
 				reset_form('physicalTestentry_2','','');
				//alert(permission);
				$('#save8').removeClass('formbutton_disabled').addClass('formbutton');
				$('#save8').removeAttr('onclick').attr('onclick','fnc_physicalTest_entry(0);')
				$('#update8').removeAttr('onclick').attr('onclick','')
				$('#update8').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				$('#Delete8').removeAttr('onclick').attr('onclick','')
				$('#Delete8').removeClass('formbutton').addClass('formbutton_disabled'); //formbutton 
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	//=========Washing==============================

	function fnc_washing_entry( operation ) 
	{
	 
	   if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}	
		else
		{ 
			var data="action=save_update_delete_washing&operation="+operation+get_submitted_data_string('company_id*req_id*txt_booking_no*txt_booking_id*washing_update_id*cbo_fabric_wash_color_code*cbo_batch_no_wash*txt_wash_mc_no*txt_wash_rpm*txt_chemical_name*txt_tumble_dryer_no*txt_process_name*txt_pre_treatment*txt_dyes_name*txt_extra_process*txt_wash_info_temperature*txt_recepi_no*txt_hydro_no*txt_wash_dry_process*txt_wash_remarks',"../");
			 //alert(data); 
			freeze_window(operation);
			http.open("POST","requires/fabric_wash_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_washing_entry_response;
		}
	}
	
	function fnc_washing_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
				
				document.getElementById('washing_update_id').value = '';
				show_list_view(response[3], 'listview_washing_info', 'washing_entry_info_list', 'requires/fabric_wash_info_entry_controller', 'setFilterGrid(\'tbl_details_washing\',-1);');
				
 				reset_form('washentry_2','','');

				$('#save9').removeClass('formbutton_disabled').addClass('formbutton');
				$('#save9').removeAttr('onclick').attr('onclick','fnc_washing_entry(0);')
				$('#update9').removeAttr('onclick').attr('onclick','')
				$('#update9').removeClass('formbutton').addClass('formbutton_disabled'); 
			    $('#Delete9').removeAttr('onclick').attr('onclick','')
				$('#Delete9').removeClass('formbutton').addClass('formbutton_disabled'); 
				
				//set_button_status(0, permission, 'fnc_kntting_entry',1,1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	function fnc_yarn_button_status(type)
	{
		//alert(type);
		if(type==2)
		{
			//reset_form('samplechecklist_1','','');
			$('#save1').removeClass('formbutton').addClass('formbutton_disabled');
			$('#update1').removeClass('formbutton_disabled').addClass('formbutton');
			//set_button_status(1, permission, 'fnc_yarn_entry',1);
		}
		else  
		{
			//reset_form('samplechecklist_1','','');
			set_button_status(0, permission, 'fnc_yarn_entry',1);
		}
	}
	
	
	function button_status222(type)
	{
		alert(type);
		if(type==2)
		{
			//reset_form('samplechecklist_1','','');
			set_button_status(1, permission, 'fnc_yarn_entry',1);
		}
		else  
		{
			//reset_form('samplechecklist_1','','');
			set_button_status(0, permission, 'fnc_yarn_entry',1);
		}
	}
	function openmypage_booking()
	{
		var company_id=$("#company_id").val();
		var title = 'Booking Info';	//
		var page_link = 'requires/sample_data_archive_entry_controller.php?&action=booking_popup'+'&company_id='+company_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("update_id").value;//mst id
			var mst_tbl_idArr=mst_tbl_id.split("_");
			var booking_id=mst_tbl_idArr[0];
			//alert(mst_tbl_id);
			
			if (mst_tbl_id!="")
			{
				//freeze_window(5); 
				
				get_php_form_data(mst_tbl_id, "populate_data_from_booking_search_popup", "requires/sample_data_archive_entry_controller" );
				reset_form('basicinfo_1','','');
				show_list_view(booking_id, 'listview_basic_info', 'basic_entry_info_list', 'requires/basic_info_entry_controller', 'setFilterGrid(\'tbl_details\',-1);');
				 
				
			}
		}
	}	
    $(document).ready(function() {
		$('#example').tabs();
		$('#example').tabs('paging', {
			cycle: true,
			follow: true
		});
		$('#example').tabs('select', <?php echo $TabIndexNo; ?>);
	});

	function showData() {
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		$('#list_operation_container').show();
		//$('#reArrange_seqNo').show();
		$('#yarn_entry_info_list').hide();
		$('#knit_entry_info_list').hide();
		$('#batching_entry_info_list').hide();
		$('#dyeing_entry_info_list').hide();
		$('#merceSunfoRized_entry_info_list').hide();
		$('#finishing_entry_info_list').hide();
		$('#physicalTest_entry_info_list').hide();
		$('#washing_entry_info_list').hide();
	 
		show_list_view(txt_booking_id, 'listview_basic_info', 'basic_entry_info_list', 'requires/basic_info_entry_controller', 'setFilterGrid(\'tbl_details\',-1);');
			
	}

	function showResult() { //===Yarn==yarn_info_from_sample_populate
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		if (txt_booking_id != '') {
			//alert()
			//reset_form('','list_operation_container*reArrange_seqNo','','','');
			//$('#tbl_operation_list2 tbody tr').remove();
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#dyeing_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			
			$('#yarn_entry_info_list').show();
			

			var totRows = $('#tbl_operation_list tbody tr').length; 
			//if(totRows<1)
			//{
			get_php_form_data(txt_booking_id, "yarn_info_from_sample_populate", "requires/yarn_info_entry_controller");
			show_list_view(txt_booking_id, 'listview_yarn_info', 'yarn_entry_info_list', 'requires/yarn_info_entry_controller', 'setFilterGrid(\'tbl_details_yarn\',-1);');
		 
		}
		//}
	}

	function resetForm() {
		//reset_form('gsdentry_2','tbl_details','','','breakdown_id');
		$('#tbl_operation_list tbody tr').remove();
		showResult();
	}
	function showResult2() { // Knitting 
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#dyeing_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			$('#knit_entry_info_list').show();


			//var totRows = $('#tbl_operation_list tbody tr').length; 
			//if(totRows<1)
			//{
			get_php_form_data(booking_data, "knitting_info_from_sample_populate", "requires/knitting_info_entry_controller");
			show_list_view(txt_booking_id, 'listview_knit_info', 'knit_entry_info_list', 'requires/knitting_info_entry_controller', 'setFilterGrid(\'tbl_details_knit\',-1);');
		 
		}
		 
	}
	function showResultBatching() { //Batching
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#dyeing_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			$('#batching_entry_info_list').show();

			get_php_form_data(booking_data, "batching_info_from_sample_populate", "requires/batchting_info_entry_controller");
			show_list_view(txt_booking_id, 'listview_batch_info', 'batching_entry_info_list', 'requires/batchting_info_entry_controller', 'setFilterGrid(\'tbl_details_batch\',-1);');
			 
		}
		 
	}	
	function showResultDyeinging() { //  showResultDyeinging
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			$('#dyeing_entry_info_list').show();
			get_php_form_data(booking_data, "dyeing_info_from_sample_populate", "requires/dyeing_info_entry_controller");
			show_list_view(txt_booking_id, 'listview_dyeing_info', 'dyeing_entry_info_list', 'requires/dyeing_info_entry_controller', 'setFilterGrid(\'tbl_details_dyeing\',-1);');
			 
		}
		 
	}
	function showResultFinishing(){	//showResultFinishing
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			 $('#list_operation_container').hide();
			 $('#reArrange_seqNo').hide();
			 $('#yarn_entry_info_list').hide();
			 $('#knit_entry_info_list').hide();
			 $('#batching_entry_info_list').hide();
			 $('#dyeing_entry_info_list').hide();	 
			 $('#merceSunfoRized_entry_info_list').hide();
			 $('#physicalTest_entry_info_list').hide();
			 $('#washing_entry_info_list').hide();
			 $('#finishing_entry_info_list').show();
 
			 get_php_form_data(booking_data, "finishing_info_from_sample_populate", "requires/finishing_info_entry_controller");
			 show_list_view(txt_booking_id, 'listview_finishing_info', 'finishing_entry_info_list', 'requires/finishing_info_entry_controller', 'setFilterGrid(\'tbl_details_finishing\',-1);');
			  
		 }

	}
	function showResultMerceSunfoRized() { //  showResultMerceSunfoRized
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#dyeing_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').show();

			get_php_form_data(booking_data, "merceSunfoRized_info_from_sample_populate", "requires/merce_sunfo_rized_entry_controller");
			show_list_view(txt_booking_id, 'listview_merceSunfoRized_info', 'merceSunfoRized_entry_info_list', 'requires/merce_sunfo_rized_entry_controller', 'setFilterGrid(\'tbl_details_merceSunfoRized\',-1);');
			 
		}
		 
	}
	function showResultPhysicalTest() { //  showResultphysicalTest
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#dyeing_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#washing_entry_info_list').hide();
			$('#physicalTest_entry_info_list').show();

			get_php_form_data(booking_data, "physicalTest_info_from_sample_populate", "requires/physical_test_Info_entry_controller");
			show_list_view(txt_booking_id, 'listview_physicalTest_info', 'physicalTest_entry_info_list', 'requires/physical_test_Info_entry_controller', 'setFilterGrid(\'tbl_details_physicalTest\',-1);');
			 
		}
		 
	}
	function showResultWashing() { //  showResultWashing
		var txt_booking_id = document.getElementById('txt_booking_id').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var booking_data=txt_booking_id+'_'+txt_booking_no;
		if (txt_booking_id != '') {
			 
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#yarn_entry_info_list').hide();
			$('#knit_entry_info_list').hide();
			$('#batching_entry_info_list').hide();
			$('#dyeing_entry_info_list').hide();
			$('#finishing_entry_info_list').hide();
			$('#merceSunfoRized_entry_info_list').hide();
			$('#physicalTest_entry_info_list').hide();
			$('#washing_entry_info_list').show();

			get_php_form_data(booking_data, "washing_info_from_sample_populate", "requires/fabric_wash_info_entry_controller");
			show_list_view(txt_booking_id, 'listview_washing_info', 'washing_entry_info_list', 'requires/fabric_wash_info_entry_controller', 'setFilterGrid(\'tbl_details_washing\',-1);');
			 
		}
		 
	}

	function resetForm2() {
		$('#tbl_operation_list2 tbody tr').remove();
		//showResult2();
	}

	function showResult3() {
		var update_id = document.getElementById('update_id').value;
		$('#list_operation_container').hide();
		$('#reArrange_seqNo').hide();
		$('#list_operation_container_thread').show();

		var prev_data = $('#tbl_list_search_tc tbody tr').length;

		//if(prev_data<1)
		//{	
		if (update_id != "") {
			show_list_view(update_id, 'show_operation_list', 'list_operation_container_thread', 'requires/thread_consumption_controller', 'setFilterGrid(\'list_view_tc\',-1);');
			get_php_form_data(update_id, "populate_data_from_breakdown", "requires/thread_consumption_controller");
			var bl_update_id = document.getElementById('bl3_update_id').value;
			show_list_view(bl_update_id, 'details_list_view', 'operation_details_tc', 'requires/thread_consumption_controller', '');
		}

		resetFormTC();
		//}
	}
	
	function fnc_color_type_load(deter_id)
	{
		var data=deter_id+'_'+$('#txt_booking_no').val();
		
		get_php_form_data(data, "load_drop_down_basic_color_type", "requires/basic_info_entry_controller");
	}

	function fnc_compostion_load(type)
	{
		var data=type+'_'+$('#txt_booking_no').val();
		
		get_php_form_data(data, "load_drop_down_yarn_composition", "requires/yarn_info_entry_controller");
	}
	//fnc_count_load
	function fnc_count_load(compo_id,type)
	{
		var data=compo_id+'_'+$('#txt_booking_no').val()+'_'+$('#cbo_yarn_fabrication').val()+'_'+type;
		
		get_php_form_data(data, "load_drop_down_yarn_count_brand", "requires/yarn_info_entry_controller");
	}
	// for Knnitng
	function fnc_knit_construction_load(prog_no)
	{
		var data=$('#txt_booking_no').val()+'_'+$('#cbo_knit_fab_color_code').val()+'_'+prog_no;
		get_php_form_data(data, "load_drop_down_knit_construction", "requires/knitting_info_entry_controller");
	}
	function fnc_batching_info_batch_load(type)//batching batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_batching_batch", "requires/batchting_info_entry_controller");
	}
	function fnc_wash_batch_load(type)//wash batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_wash_batch", "requires/fabric_wash_info_entry_controller");
	}
	function fnc_physicalTest_batch_load(type)//physicalTest batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_physical_test_batch", "requires/physical_test_Info_entry_controller");
	}
	function fnc_merceSunfoRized_batch_load(type)//merceSunfoRized batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_merceSunfoRized_batch", "requires/merce_sunfo_rized_entry_controller");
	}
	function fnc_finishing_info_batch_load(type)//finishing_info batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_finishing_info_batch", "requires/finishing_info_entry_controller");
	}
	function fnc_dyeing_info_batch_load(type)//dyeing_info batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_dyeing_info_batch", "requires/dyeing_info_entry_controller");
	}	
	function fnc_dyeing_info_recipe_load(type)//batching batch no
	{
		var data=type+'_'+$('#txt_booking_no').val();
		get_php_form_data(data, "load_drop_down_for_recipe_dyeing", "requires/dyeing_info_entry_controller");
	}
</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
    <form name="samplechecklist_1" id="samplechecklist_1"> 
        <fieldset style="width:650px;margin-bottom: 10px;" id="checklistMst">
        <legend>Booking Information</legend>
            <table cellpadding="2" cellspacing="2" width="650" align="center"> 
                <tr>
                    <td colspan="3" align="right"><b>Booking No</b></td>
					<input type="hidden" name="txt_booking_id" id="txt_booking_id" value="">
                    <input type="hidden" name="company_id" id="company_id" value="">
                     <input type="hidden" name="req_id" id="req_id" value="">
                    <td colspan="3"><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width: 120px;" placeholder="Browse" readonly onDblClick="openmypage_booking()" ></td>
                </tr>
                <tr>
                    <td width="" class="">Style/Article</td>
                        <input type="hidden" name="txt_style_ref_hid" id="txt_style_ref_hid">
                    <td width="140"><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px;" value="" readonly placeholder="Display" ></td>
                    <td width="" class="">Buyer Name</td>
                        <input type="hidden" name="txt_buyer_name_hid" id="txt_buyer_name_hid">
                    <td width="140"><input type="text" name="txt_buyer_name" id="txt_buyer_name" class="text_boxes" style="width:120px;" value="" readonly placeholder="Display" ></td>
                    <td width="" class="">Sample Type</td>
                        <input type="hidden" name="txt_sample_type_hid" id="txt_sample_type_hid">
                    <td width="140"><input type="text" name="txt_sample_type" id="txt_sample_type" class="text_boxes" style="width:120px;" value="" readonly placeholder="Display" ></td>
                </tr>
                <tr>
                    <td colspan="6" align="center" height="15">
                        <span id="cutting_approved_msg" style="color:crimson;font-weight: bold;font-size: 19px;"></span>
                        <input type="hidden" name="update_id" id="update_id" value="">
                    </td>		 
                </tr>
            </table>
        </fieldset>
    </form>
	</div>
    <div style="width:1280px; margin-left:-50px" align="center">
			<div id="examples" style="width:880px;"></div>
				<div id="example" style="width:820px; margin-top:5px;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 0px;"  align="center">
					<ul class="tabs">
						<li><a href="#basic_info_entry" onClick="showData();">Basic Info</a></li>
						
						<li><a href="#yarn_info_entry" onClick="showResult();" id="graph">Yarn</a></li>
						<li><a href="#knitting_info_entry" onClick="showResult2();" id="graph2">Knitting</a></li>
						<li><a href="#batchting_info_entry" onClick="showResultBatching();">Batching</a></li>
						<li><a href="#dyeing_info_entry" onClick="showResultDyeinging();">Dyeing</a></li>
						<li><a href="#finishing_info_entry" onClick="showResultFinishing();">D. Finishing</a></li>
                        <li><a href="#merce_sunfo_rized_info_entry" onClick="showResultMerceSunfoRized();">Mercerized/ Sunforized</a></li>
                        <li><a href="#physical_test_Info_entry" onClick="showResultPhysicalTest();">Physical Test</a></li>
                        <li><a href="#fabric_wash_Info_entry" onClick="showResultWashing();">Fabric Wash</a></li>
                        <li><a href="#approval_consumption" onClick="showResult7();">Approval</a></li>
					</ul>
					<div id="basic_info_entry"><?php include('basic_info_entry.php'); ?></div>
					 
					<div id="yarn_info_entry"><?php include('yarn_info_entry.php'); ?></div>
					<div id="knitting_info_entry"><?php include('knitting_info_entry.php'); ?></div>
					<div id="batchting_info_entry"><?php include('batchting_info_entry.php'); ?></div>
					<div id="dyeing_info_entry"><?php include('dyeing_info_entry.php'); ?></div>
					<div id="finishing_info_entry"><?php include('finishing_info_entry.php'); ?></div>
                    <div id="merce_sunfo_rized_info_entry"><?php include('merce_sunfo_rized_info_entry.php'); ?></div>
					<div id="physical_test_Info_entry"><?php include('physical_test_Info_entry.php'); ?></div>
					<div id="fabric_wash_Info_entry"><?php include('fabric_wash_Info_entry.php'); ?></div>
					<div id="approval_consumption"><?php include('approval_consumption.php'); ?></div>
				</div>
			</div> 
				<div style="width:10px; overflow:auto; float:left; padding-top:1px; margin-top:1px; position:relative;"></div>
			<div style="float:left; padding-top:1px; margin-top:1px; position:relative;">
			<div id="list_operation_container"></div>
			<div id="reArrange_seqNo" style="margin-top:5px;overflow:auto;"></div>
			<div id="list_operation_container_thread" style="margin-top:5px;overflow:auto; "></div>
		</div>
    <script src="../includes/functions_bottom.js" type="text/javascript"></script>
</body>
</html>