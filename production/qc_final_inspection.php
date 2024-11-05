<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------
$user_id=$_SESSION['logic_erp']['user_id'];
$user_name = return_field_value("user_name", "user_passwd", "id ='$user_id' and valid=1");
echo load_html_head_contents("QC Screen Final Inspection","../", 1, 1, $unicode,1,'');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function set_data(data){

		var value = trim(data).split('_');
		get_php_form_data( value[1], 'populate_php_data_full_form', 'requires/qc_final_inspection_controller');
		$('#txt_batch_no').attr('readonly', true);
		$('#txt_system_id').attr('readonly', true);
		show_list_view(value[0],'show_qcfinal_prog_quantity_listview','list_container_program','requires/qc_final_inspection_controller','');
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/qc_final_inspection_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_qc_final_inspection_entry(operation)
	{
		if (operation == 4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'**'+$('#update_id').val()+'**'+report_title,'qc_final_inspection_print','requires/qc_final_inspection_controller');
		}else if (operation == 5)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'**'+$('#update_id').val()+'**'+report_title,'qc_final_inspection_print2','requires/qc_final_inspection_controller');
		}
		else
		{
			if(form_validation('cbo_company_id*txt_batch_no','Company Name*Batch No')==false)
			{
				return;
			}
			var update_id = document.getElementById('update_id').value;
			var batch_id = document.getElementById('hidden_batch_id').value;
			var color_id = document.getElementById('hidden_color_id').value;
			var dye_mc_id = document.getElementById('hidden_dye_mc_id').value;
			var knit_mc_id = document.getElementById('hidden_knit_mc_id').value;
			var po_id = document.getElementById('hidden_po_id').value;

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_id*txt_batch_no*hidden_batch_id*txt_shift_name*hidden_color_id*hidden_booking_no*hidden_body_part_id*hidden_width_dia_type*txt_prog_id*txt_tube_ref*txt_item_description*txt_prog_qty*txt_actual_qty*txt_finalized_date*txt_customer_order_id*hidden_po_id*hidden_knit_mc_id*hidden_dye_mc_id*txt_finalized_id*txt_req_qty_bulk*cbo_handfeel*txt_wash_fastness*txt_shrinkageL*txt_shrinkageL_pct*cbo_sewability*txt_water_fastness*txt_shrinkageW*txt_shrinkageW_pct*cbo_ph_for*txt_rub_fastness_wet*txt_twist*txt_twist_pct*cbo_bale_to_bale*txt_rub_fastness_dry*txt_skewness_warp*txt_skewness_warp_degree*cbo_job_to_job*txt_pilling*txt_skewness_weft*txt_skewness_weft_degree*cbo_shading_test_same_bale*txt_shade_deE*txt_shade_deL*txt_shade_deC*txt_shade_deH*txt_required_width_bulk*txt_required_width_bulk_inches*txt_required_width_acc1*txt_required_width_acc1_inches*txt_required_width_acc2*txt_required_width_acc2_inches*txt_actual_width_bulk*txt_actual_width_bulk_inches*txt_actual_width_acc1*txt_actual_width_acc1_inches*txt_actual_width_acc2*txt_actual_width_acc2_inches*txt_required_dencity_bulk*txt_required_dencity_bulk_gm2*txt_required_dencity_acc1*txt_required_dencity_acc1_gm2*txt_required_dencity_acc2*txt_required_dencity_acc2_gm2*txt_actual_dencity_bulk*txt_actual_dencity_bulk_gm2*txt_actual_dencity_acc1*txt_actual_dencity_acc1_gm2*txt_actual_dencity_acc2*txt_actual_dencity_acc2_gm2*txt_phenolic_yellowing*txt_ext_rec_l*txt_ext_rec_l_pct*txt_ext_rec_w*txt_ext_rec_w_pct*txt_carcasse_assessed*txt_symmetry*cbo_hydrophility*cbo_print_durability*txt_width_print*txt_width_print_inches*cbo_shading_selvedge*cbo_pattern_height*txt_width_selvedge*txt_width_selvedge_inches*cbo_shedding_fibers*txt_remarks*cbo_flammability_test*cbo_orientation_finished_orders*txt_steam_test*txt_bursting_strength_delicate_fabrics*txt_bursting_strength_delicate_fabrics_kpa*txt_tear_strength*txt_orientation_delicate_fabrics*cbo_pass_fail',"../")+'&update_id='+update_id;
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/qc_final_inspection_controller.php",true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_qc_final_inspection_entry_response;
		}		
	}

	function fnc_qc_final_inspection_entry_response()
	{
		if (http.readyState == 4)
		{
			var response = trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_system_id').value = response[1];			

		
			if (response[0]==0)
			{
				// $('#list_programme_desc_container').hide();
				reset_form('qcScreenFinalInspection_1','','','','','cbo_company_id*txt_system_id');
				// set_button_status(1, permission, 'fnc_qc_final_inspection_entry', 1, 1);
				set_button_status(0, permission, 'fnc_qc_final_inspection_entry', 1, 0);
			}
			if (response[0]==1)
			{		
				reset_form('qcScreenFinalInspection_1','','','','','cbo_company_id*txt_system_id');				
				set_button_status(0, permission, 'fnc_qc_final_inspection_entry', 1, 0);
			}
						
			show_list_view(response[1],'create_qc_final_inspection_list_view','list_container','requires/qc_final_inspection_controller','setFilterGrid("list_view_container",-1)');
			show_list_view(response[2]+'_'+response[3]+'_'+response[4],'show_programme_desc_listview','list_programme_desc_container','requires/qc_final_inspection_controller','');
			
			release_freezing();		
		}			
	}
	
	function check_batch()
	{
		var batch_no=$('#txt_batch_no').val();
		var cbo_company_id = $('#cbo_company_id').val();

		$('#txt_system_no').val('');
		$('#cbo_company_id').removeAttr('disabled','disabled');
		if(batch_no!="")
		{
			if (form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/qc_final_inspection_controller');
			var response=response.split("_");
			var batch_id  = response[1];
			var cbo_company_id  = response[2];
			var booking_no  = response[3];

			$('#cbo_company_id').val(cbo_company_id);
			if(response[0]==0)
			{
				alert('Batch Not found !!');
				$('#txt_batch_no').val('');
				$('#hidden_batch_id').val('');
				$('#txt_color_id').val('');
				$('#txt_prog_id').val('');
				$('#txt_knit_mc_id').val('');
				$('#txt_dye_mc_id').val('');
			}
			else
			{
				$("#txt_batch_no").val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_booking_no').val(booking_no);

				get_php_form_data(batch_id+'_'+batch_no+'_'+cbo_company_id+'_'+booking_no, "populate_data_from_batch", "requires/qc_final_inspection_controller");
				$('#cbo_company_id').attr('disabled','disabled');
				$('#txt_color_id').attr('disabled','disabled');
				$('#txt_dye_mc_id').attr('disabled','disabled');
				$('#txt_knit_mc_id').attr('disabled','disabled');
				$('#txt_customer_order_id').attr('disabled','disabled');

				show_list_view(batch_id+'_'+batch_no+'_'+cbo_company_id,'show_programme_desc_listview','list_programme_desc_container','requires/qc_final_inspection_controller','');
				$('#txt_prog_id').attr('disabled','disabled');			
			}
		}
	}

	$('#txt_batch_no').live('keydown', function(e) {
    	if (e.keyCode === 13) {
       	    e.preventDefault();
			var batch_no=$('#txt_batch_no').val();
			$('#txt_batch_no').removeAttr('onChange','onChange');
			$('#cbo_company_id').focus();
		 	check_batch();
        }
    });

	function programme_val(val)
	{
		//alert(val);
		var row_data   = val.split("_");
		var program_no = row_data[0];
		var batch_id   = row_data[1];
		var booking_no   = row_data[2];
		var batch_no   = row_data[3];
		var tube_ref   = row_data[4];
		var cbo_company_id = $('#cbo_company_id').val();
		$('#txt_prog_id').val(program_no);
		$('#txt_batch_no').val(batch_no);
		$('#hidden_batch_id').val(batch_id);
		$('#hidden_booking_no').val(booking_no);

		show_list_view(batch_id+'_'+program_no+'_'+cbo_company_id+'_'+booking_no+'_'+tube_ref,'show_programme_quantity_listview','list_container_program','requires/qc_final_inspection_controller','');
		set_button_status(0, permission, 'fnc_qc_final_inspection_entry', 1, 0);
	}
	 
	function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var batch_no = $('#txt_batch_no').val();

		page_link='requires/qc_final_inspection_controller.php?action=batch_number_popup&cbo_company_id='+cbo_company_id+'&batch_no='+batch_no;
		var title='Batch Number Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var batch_datas=this.contentDoc.getElementById("hid_batch_id").value;
			//alert(batch_datas);
			if(batch_datas!="")
			{
				var batch_data = batch_datas.split("_");
				var batch_id   = batch_data[0];
				var batch_no   = batch_data[1];
				var booking_no = batch_data[2];

				$("#txt_batch_no").val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_booking_no').val(booking_no);

				get_php_form_data(batch_id+'_'+batch_no+'_'+cbo_company_id+'_'+booking_no, "populate_data_from_batch", "requires/qc_final_inspection_controller");
				$('#cbo_company_id').attr('disabled','disabled');
				$('#txt_color_id').attr('disabled','disabled');
				$('#txt_dye_mc_id').attr('disabled','disabled');
				$('#txt_knit_mc_id').attr('disabled','disabled');
				$('#txt_customer_order_id').attr('disabled','disabled');

				show_list_view(batch_id+'_'+batch_no+'_'+cbo_company_id,'show_programme_desc_listview','list_programme_desc_container','requires/qc_final_inspection_controller','');
				$('#txt_prog_id').attr('disabled','disabled');
			}
		}
	}

	function openmypage_sysnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}

		page_link='requires/qc_final_inspection_controller.php?action=sys_number_popup&cbo_company_id='+cbo_company_id;
		var title='System Number Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sys_datas=this.contentDoc.getElementById("hid_sys_id").value;
			//alert(batch_datas);
			if(sys_datas!="")
			{
				var system_data  = sys_datas.split("_");
				var id = system_data[0];
				var program_no  = system_data[1];
				var system_no   = system_data[2];	
				var batch_id   = system_data[3];		
				var batch_no   = system_data[4];									
				$("#update_id").val(id);
				$("#txt_system_id").val(system_no);
				get_php_form_data(id, "populate_php_data_full_form", "requires/qc_final_inspection_controller");
				$('#cbo_company_id').attr('disabled','disabled');
				$('#txt_color_id').attr('disabled','disabled');
				$('#txt_dye_mc_id').attr('disabled','disabled');
				$('#txt_knit_mc_id').attr('disabled','disabled');
				$('#txt_customer_order_id').attr('disabled','disabled');
				var cbo_company_id=$('#cbo_company_id').val();
				// alert(cbo_company_id);
				show_list_view(id,'create_qc_final_inspection_list_view','list_container','requires/qc_final_inspection_controller','setFilterGrid("list_view_container",-1)');
				show_list_view(batch_id+'_'+batch_no+'_'+cbo_company_id,'show_programme_desc_listview','list_programme_desc_container','requires/qc_final_inspection_controller','');
				$('#txt_prog_id').attr('disabled','disabled');
				$('#txt_batch_no').attr('readonly', true);
				$('#txt_system_id').attr('readonly', true);
				show_list_view(id,'show_qcfinal_prog_quantity_listview','list_container_program','requires/qc_final_inspection_controller','');
				set_button_status(1, permission, 'fnc_qc_final_inspection_entry', 1, 1);
			}
		}
	}
</script>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
		<form name="qcScreenFinalInspection_1" id="qcScreenFinalInspection_1">
			<div style="width:810px; float:left; padding-left: 20px;" align="center">
				<fieldset style="width:810px">
					<legend>Order Details</legend>
					<fieldset style="width:810px">
						<table cellpadding="0" cellspacing="2" width="100%" id="master_table">
							<tr>
								<td align="right" colspan="3"><strong>System ID</strong></td>
								<td>									
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="openmypage_sysnum();"/>
									<input type="hidden" name="update_id" id="update_id" />
									<input type="hidden" name="hidden_batch_id" id="hidden_batch_id"/>
									<input type="hidden" name="hidden_color_id" id="hidden_color_id"/>
									<input type="hidden" name="hidden_dye_mc_id" id="hidden_dye_mc_id"/>
									<input type="hidden" name="hidden_knit_mc_id" id="hidden_knit_mc_id"/>
									<input type="hidden" name="hidden_po_id" id="hidden_po_id"/>					
									<input type="hidden" name="hidden_booking_no" id="hidden_booking_no"/>					
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company</td>
		                        <td>
									<?
									echo create_drop_down("cbo_company_id", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $selected);
									?>
		                        </td>								
								<td class="must_entry_caption">Batch</td>
								<td>
									<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:145px" placeholder="Write/Browse/Scan" onDblClick="openmypage_batchnum();" onChange="check_batch();"/>																		
								</td>
								<td>Shift</td>
								<td>
									<?
									echo create_drop_down( "txt_shift_name", 155, $shift_name,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
							</tr>
							<tr>
								<td>Colour</td>
								<td>
									<input type="text" name="txt_color_id" id="txt_color_id" class="text_boxes" style="width:145px" />
								</td>
							
								<td>Date Finalized</td>
								<td>
									<input class="datepicker" type="text" style="width:145px" name="txt_finalized_date" id="txt_finalized_date" value="<? echo date("d-m-Y");?>"/>
								</td>
								<td>Customer Order</td>
								<td>
									<input type="text" name="txt_customer_order_id" id="txt_customer_order_id" class="text_boxes" style="width:145px"/>
								</td>	
							</tr>
							<tr>
															
								<td>Knit MC</td>
								<td>
									<input type="text" name="txt_knit_mc_id" id="txt_knit_mc_id" class="text_boxes" style="width:145px"/>
								</td>
								<td>Dye MC</td>
								<td>
									<input type="text" name="txt_dye_mc_id" id="txt_dye_mc_id" class="text_boxes" style="width:145px"/>
								</td>
							</tr>
							<tr>
								<td>Data Input By</td>
								<td>
									<input type="text" name="txt_data_input_id" id="txt_data_input_id" class="text_boxes" style="width:145px" value="<? echo $user_name; ?>"  readonly/>
								</td>
								<td>Finalized By</td>
								<td>
									<input type="text" name="txt_finalized_id" id="txt_finalized_id" class="text_boxes" style="width:145px"/>
								</td>
							</tr>
						</table>												
					</fieldset>
					<fieldset>
						<div style="width:810px;" id="list_container_program"></div>
					</fieldset>
					<fieldset>
						<legend>Test Results</legend>
						<div style="text-align: center; background-color: #aaa; margin: 5px 0px 5px 0px;">Applicable To All Orders</div>
						<table cellpadding="0" cellspacing="2" width="820">	
							<tr>
								<td width="120">Handfeel</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_handfeel", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Wash Fastness</td>
								<td>
									<input type="text" name="txt_wash_fastness" id="txt_wash_fastness" class="text_boxes" style="width:80px"/>
								</td>
								<td width="120">Shrinkage-L</td>
								<td>
									<input type="text" name="txt_shrinkageL" id="txt_shrinkageL" class="text_boxes" style="width:80px"/>
									<input type="text" name="txt_shrinkageL_pct" id="txt_shrinkageL_pct" class="text_boxes" style="width:40px;"  placeholder="%"/>
								</td>
							</tr>
							<tr>
								<td width="120">Sewability</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_sewability", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Water Fastness</td>
								<td>
									<input type="text" name="txt_water_fastness" id="txt_water_fastness" class="text_boxes" style="width:80px"/>
								</td>
								<td width="120">Shrinkage-W</td>
								<td>
									<input type="text" name="txt_shrinkageW" id="txt_shrinkageW" class="text_boxes" style="width:80px"/>
									<input type="text" name="txt_shrinkageW_pct" id="txt_shrinkageW_pct" class="text_boxes" style="width:40px;"  placeholder="%"/>
								</td>
							</tr>
							<tr>
								<td width="120">PH For White/Pastel Clr</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_ph_for", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Rub Fastness (Wet)</td>
								<td>
									<input type="text" name="txt_rub_fastness_wet" id="txt_rub_fastness_wet" class="text_boxes" style="width:80px"/>
								</td>
								<td width="120">Twist</td>
								<td>
									<input type="text" name="txt_twist" id="txt_twist" class="text_boxes" style="width:80px"/>
									<input type="text" name="txt_twist_pct" id="txt_twist_pct" class="text_boxes" style="width:40px;"  placeholder="%" />
								</td>
							</tr>
							<tr>
								<td width="120">Shading Test Bale To Bale</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_bale_to_bale", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Rub Fastness (Dry)</td>
								<td>
									<input type="text" name="txt_rub_fastness_dry" id="txt_rub_fastness_dry" class="text_boxes" style="width:80px"/>
								</td>
								<td width="120">Skewness-Warp</td>
								<td>
									<input type="text" name="txt_skewness_warp" id="txt_skewness_warp" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_skewness_warp_degree" id="txt_skewness_warp_degree" class="text_boxes_numeric" style="width:40px;"  placeholder="degree" />
								</td>
							</tr>
							<tr>
								<td width="120">Shading Test Job To Job</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_job_to_job", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Pilling</td>
								<td>
									<input type="text" name="txt_pilling" id="txt_pilling" class="text_boxes" style="width:80px"/>
								</td>
								<td width="120">Skewness-Weft</td>
								<td>
									<input type="text" name="txt_skewness_weft" id="txt_skewness_weft" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_skewness_weft_degree" id="txt_skewness_weft_degree" class="text_boxes_numeric" style="width:40px;"  placeholder="degree" />
								</td>
							</tr>
							<tr>
								<td width="120">Shading Test Within Same Bale</td>
								<td>
									<?
									$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
									echo create_drop_down( "cbo_shading_test_same_bale", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
								<td width="120">Shade (DE)</td>
								<td colspan="3">
									<input type="text" name="txt_shade_deE" id="txt_shade_deE" class="text_boxes_numeric" style="width:80px" placeholder="E">
									<input type="text" name="txt_shade_deL" id="txt_shade_deL" class="text_boxes_numeric" style="width:80px" placeholder="L"/>
									<input type="text" name="txt_shade_deC" id="txt_shade_deC" class="text_boxes_numeric" style="width:80px" placeholder="C"/>
									<input type="text" name="txt_shade_deH" id="txt_shade_deH" class="text_boxes_numeric" style="width:80px" placeholder="H"/>
								</td>
							</tr>
							<tr>
								<td width="120">Required Width-Bulk</td>
								<td>
									<input type="text" name="txt_required_width_bulk" id="txt_required_width_bulk" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_width_bulk_inches" id="txt_required_width_bulk_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
								<td width="120">Required Width-ACC 1</td>
								<td>
									<input type="text" name="txt_required_width_acc1" id="txt_required_width_acc1" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_width_acc1_inches" id="txt_required_width_acc1_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
								<td width="120">Required Width-ACC 2</td>
								<td>
									<input type="text" name="txt_required_width_acc2" id="txt_required_width_acc2" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_width_acc2_inches" id="txt_required_width_acc2_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
							</tr>
							<tr>
								<td width="120">Actual Width-Bulk</td>
								<td>
									<input type="text" name="txt_actual_width_bulk" id="txt_actual_width_bulk" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_width_bulk_inches" id="txt_actual_width_bulk_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
								<td width="120">Actual Width-ACC 1</td>
								<td>
									<input type="text" name="txt_actual_width_acc1" id="txt_actual_width_acc1" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_width_acc1_inches" id="txt_actual_width_acc1_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
								<td width="120">Actual Width-ACC 2</td>
								<td>
									<input type="text" name="txt_actual_width_acc2" id="txt_actual_width_acc2" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_width_acc2_inches" id="txt_actual_width_acc2_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
							</tr>
							<tr>
								<td width="120">Required Density-Bulk</td>
								<td>
									<input type="text" name="txt_required_dencity_bulk" id="txt_required_dencity_bulk" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_dencity_bulk_gm2" id="txt_required_dencity_bulk_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
								<td width="120">Required Density-ACC 1</td>
								<td>
									<input type="text" name="txt_required_dencity_acc1" id="txt_required_dencity_acc1" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_dencity_acc1_gm2" id="txt_required_dencity_acc1_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
								<td width="120">Required Density-ACC 2</td>
								<td>
									<input type="text" name="txt_required_dencity_acc2" id="txt_required_dencity_acc2" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_required_dencity_acc2_gm2" id="txt_required_dencity_acc2_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
							</tr>
							<tr>
								<td width="120">Actual Density-Bulk(R/M/L)</td>
								<td>
									<input type="text" name="txt_actual_dencity_bulk" id="txt_actual_dencity_bulk" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_dencity_bulk_gm2" id="txt_actual_dencity_bulk_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
								<td width="120">Actual Density-ACC 1</td>
								<td>
									<input type="text" name="txt_actual_dencity_acc1" id="txt_actual_dencity_acc1" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_dencity_acc1_gm2" id="txt_actual_dencity_acc1_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
								<td width="120">Actual Density-ACC 2</td>
								<td>
									<input type="text" name="txt_actual_dencity_acc2" id="txt_actual_dencity_acc2" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_actual_dencity_acc2_gm2" id="txt_actual_dencity_acc2_gm2" class="text_boxes_numeric" style="width:40px;"  placeholder="G/M2" />
								</td>
							</tr>
							<tr>
								<td width="120">Phenolic Yellowing (White)</td>
								<td>
									<input type="text" name="txt_phenolic_yellowing" id="txt_phenolic_yellowing" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120">Ext & Rec (Lycra)</td>
								<td width="240" colspan="3">
									<input type="text" name="txt_ext_rec_l" id="txt_ext_rec_l" class="text_boxes_numeric" style="width:80px"  placeholder="L-">
									<input type="text" name="txt_ext_rec_l_pct" id="txt_ext_rec_l_pct" class="text_boxes_numeric" style="width:40px;"  placeholder="%" />
									<input type="text" name="txt_ext_rec_w" id="txt_ext_rec_w" class="text_boxes_numeric" style="width:80px"  placeholder="W-">
									<input type="text" name="txt_ext_rec_w_pct" id="txt_ext_rec_w_pct" class="text_boxes_numeric" style="width:40px;"  placeholder="%" />
								</td>
							</tr>
							<tr>
								<td width="120">Carcasse Assessed</td>
								<td>
									<input type="text" name="txt_carcasse_assessed" id="txt_carcasse_assessed" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120">Symmetry</td>
								<td>
									<input type="text" name="txt_symmetry" id="txt_symmetry" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120"></td>
								<td width=""></td>
							</tr>
						</table>
						<div style="text-align: center; background-color: #aaa; margin: 5px 0px 5px 0px;">Applicable To AOP Orders</div>
						<table cellpadding="0" cellspacing="2" width="820">	
							<tr>
								<td width="120">Hydrophility</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_hydrophility", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="txt_hydrophility" id="txt_hydrophility" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Print Durability</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_print_durability", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="txt_print_durability" id="txt_print_durability" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Width Print To Print</td>
								<td>
									<input type="text" name="txt_width_print" id="txt_width_print" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_width_print_inches" id="txt_width_print_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
							</tr>
							<tr>
								<td width="120">Shading-Selvedge To Selvedge</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_shading_selvedge", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="cbo_shading_selvedge" id="cbo_shading_selvedge" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Pattern Height</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_pattern_height", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="cbo_pattern_height" id="cbo_pattern_height" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Width Selvedge To Selvedge</td>
								<td>
									<input type="text" name="txt_width_selvedge" id="txt_width_selvedge" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_width_selvedge_inches" id="txt_width_selvedge_inches" class="text_boxes_numeric" style="width:40px;"  placeholder="inches" />
								</td>
							</tr>												
						</table>
						<div style="text-align: center; background-color: #aaa; margin: 5px 0px 5px 0px;">Applicable To Special Finished Orders</div>
						<table cellpadding="0" cellspacing="2" width="820">								
							<tr>
								<td width="120">Shedding Fibers</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_shedding_fibers", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="cbo_shedding_fibers" id="cbo_shedding_fibers" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Remarks</td>
								<td>
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:125px"/>
								</td>
								<td width="120"></td>
								<td width="135"></td>
							</tr>
							<tr>
								<td width="120">Flammability Test</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_flammability_test", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="cbo_flammability_test" id="cbo_flammability_test" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120">Orientation</td>
								<td>
									<?
										$ok_notok = array(1 => "Ok", 2 => "Not Ok"); 
										echo create_drop_down( "cbo_orientation_finished_orders", 135, $ok_notok,"", 1, "-- Select --", 0, "",'' );
									?>
									<!-- <input type="text" name="cbo_orientation_finished_orders" id="cbo_orientation_finished_orders" class="text_boxes_numeric" style="width:135px"/> -->
								</td>
								<td width="120"></td>
								<td width=""></td>
							</tr>							
						</table>
						<div style="text-align: center; background-color: #aaa; margin: 5px 0px 5px 0px;">Applicable To Delicate Fabrics</div>
						<table cellpadding="0" cellspacing="2" width="820">								
							<tr>
								<td width="120">Steam Test</td>
								<td>
									<input type="text" name="txt_steam_test" id="txt_steam_test" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120">Bursting Strength</td>
								<td>
									<input type="text" name="txt_bursting_strength_delicate_fabrics" id="txt_bursting_strength_delicate_fabrics" class="text_boxes_numeric" style="width:80px"/>
									<input type="text" name="txt_bursting_strength_delicate_fabrics_kpa" id="txt_bursting_strength_delicate_fabrics_kpa" class="text_boxes_numeric" style="width:40px;"  placeholder="KPA" />
								</td>
							</tr>
							<tr>
								<td width="120">Tear Strength</td>
								<td>
									<input type="text" name="txt_tear_strength" id="txt_tear_strength" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120">Orientation</td>
								<td>
									<input type="text" name="txt_orientation_delicate_fabrics" id="txt_orientation_delicate_fabrics" class="text_boxes_numeric" style="width:135px"/>
								</td>
								<td width="120">Fabric Quality</td>
								<td>
									<?
									$pass_fail_arr = array(1=>'Pass',2=>'Fail');
									echo create_drop_down( "cbo_pass_fail", 135, $pass_fail_arr,"", 1, "-- Select --", 0, "",'' );
									?>
								</td>
							</tr>							
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="820">
						<tr> 
						   <td colspan="6" align="center"></td>
						</tr>
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
								<? echo load_submit_buttons( $permission, "fnc_qc_final_inspection_entry", 0, 1,"fnResetForm()",1);?>	
								<input type="button" value="Print 2" onClick="fnc_qc_final_inspection_entry(5)"  style="width:100px; " name="print_2" id="print_2" class="formbutton" />
							</td>
					    </tr>
					</table>			
				</div>
				<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
				<div id="list_programme_desc_container" style="max-height:500px; width:550px; overflow:auto; padding-top:5px; margin-top:5px; position:relative;padding-left: 20px;"></div>
				<!-- <div id="list_program_wise_fabric_desc_container" style="max-height:500px; width:340px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div> -->
			</form>
			<fieldset style="width:825px; margin: 20px 0px 0px 20px; ">
				<div id="list_container"></div>
			</fieldset>
		</div>
	</body>
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>