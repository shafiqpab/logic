<?
/*-- ------------------------------------------ Comments
Purpose			: 	This form will create Finish Fabric Delivery Roll Wise
Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	12-07-2018
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
echo load_html_head_contents("Finish Fabric Delivery Entry","../../", 1, 1, $unicode,'','');
?>
<script>
	function openmypage_fso() 
	{
		var title = 'Fabric Sale Order Form';
		var cbo_company_id = $('#cbo_company_id').val();
		var page_link = 'requires/finish_feb_delivery_to_garments_controller.php?cbo_company_id='+cbo_company_id+'&action=fabric_sales_order_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');		
		emailwindow.onclose = function () 
		{
			var theform=this.contentDoc.forms[0];
			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
			var booking_data = hidden_booking_data.split("**");
			var fso_id = booking_data[0];
			var sales_booking_no = booking_data[1];
			var companyId = booking_data[2]; 
			var withinGroup = booking_data[3];
			var buyer_id = booking_data[4];			
			var fso_no =  booking_data[5];
			var batch_id = booking_data[6];
			var batch_no = booking_data[7];
			var po_job_no = booking_data[8];
			var po_company_id = booking_data[9];
			var po_company_name = booking_data[10];

			$("#txt_booking_no").val(sales_booking_no);
			$("#hdn_fso_id").val(fso_id);
			$("#hdn_buyer_id").val(buyer_id);
			$("#txt_fso_no").val(fso_no);
			$('#hdn_batch_id').val(batch_id);
			$('#txt_batch_no').val(batch_no);
			$('#txt_po_job').val(po_job_no);
			$('#po_company_id').val(po_company_id);
			$('#txt_party').val(po_company_name).attr('disabled','disabled');
			$('#txt_booking_no').attr('disabled','disabled');
			$('#txt_batch_no').attr('disabled','disabled');

			show_list_view(fso_id+"**"+sales_booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_feb_delivery_to_garments_controller','');
		}
	}

	function set_form_data(data){
		var formData = data.split("**");
		$('#cbo_body_part').val(formData[0]);
		var desc = formData[1] + ", " + formData[2] + ", " + formData[3];
		$('#txt_fabric_description').val(desc);		
		$('#txt_fabric_description_id').val(formData[3]);
		$('#txt_gsm').val(formData[2]);
		$('#txt_dia').val(formData[3]);
		$('#txt_color').val(formData[5]);
		$('#txt_color_id').val(formData[12]);
		$('#txt_dia_width_type').val(formData[8]);

		$('#hidden_receive_id').val(formData[7]);
		$('#hidden_receive_number').val(formData[15]);
		$('#hidden_receive_dtls_id').val(formData[16]);
		$('#hidden_product_id').val(formData[17]);

		$('#txt_sales_order_no').val($("#txt_fso_no").val());
		$('#txt_fabric_receive').val((formData[13]*1).toFixed(2));
		$('#txt_cumulative_delivery').val((formData[14]*1).toFixed(2));
		$('#txt_yet_delivery').val((formData[13]*1 - formData[14]*1).toFixed(2));

		$('#cbo_body_part').attr('disabled','disabled');
	}

	function fnc_finish_delivery_entry( operation )
	{
		if( form_validation('cbo_company_id*cbo_location*txt_delivery_date*txt_fso_no*txt_party*txt_booking_no*txt_Delivery_qnty','Company*Location*Delivery Date*FSO No*Party* Booking No*Delivery Qnty')==false )
		{
			return;
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location*txt_system_id*txt_delivery_date*txt_fso_no*hdn_fso_id*hdn_buyer_id*txt_po_job*po_company_id*txt_fabric_description*txt_fabric_description_id*txt_gsm*txt_dia*hdn_batch_id*cbo_body_part*txt_color*txt_color_id*txt_Delivery_qnty*hidden_receive_id*hidden_receive_number*hidden_receive_dtls_id*hidden_product_id*txt_dia_width_type*txt_party*update_mst_id',"../../");

		freeze_window(operation);
		http.open("POST","requires/finish_feb_delivery_to_garments_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_finish_delivery_entry_reponse;
	}

	function fnc_finish_delivery_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			release_freezing();
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:930px; float:left;" align="center">   
				<fieldset style="width:920px;">
					<legend>Finish Fabric Delivery Entry</legend>
					<fieldset>
						<table cellpadding="0" cellspacing="2" width="810" border="0" style="margin-bottom: 20px">
							<tr>
								<td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6"></td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company Name</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"load_drop_down('requires/knit_finish_fabric_receive_controller', this.value, 'load_drop_down_location','location_td');" );
									?>
								</td>
								<td class="must_entry_caption">Location</td>
								<td id="location_td">
									<?
									echo create_drop_down("cbo_location", 162, $blank_array,"", 1,"-- Select Location --", 0,"");
									?>
								</td>
								<td class="must_entry_caption">Delivery Date</td>
								<td>
									<input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:150px;" value="<? echo date("d-m-Y"); ?>" readonly>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">FSO No</td>
								<td>
									<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_fso();" readonly="readonly" />
									<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
									<input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id" class="text_boxes" value="" />
									<input type="hidden" name="txt_po_job" id="txt_po_job" class="text_boxes" readonly="readonly" />
								</td>
								<td>Party</td>
								<td>
									<input type="text" name="txt_party" id="txt_party" class="text_boxes" style="width:150px" placeholder="Party" readonly="readonly" />
									<input type="hidden" name="po_company_id" id="po_company_id" class="text_boxes" readonly="readonly" />
								</td>
								<td class="must_entry_caption"> Booking No </td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px" placeholder="Booking No" readonly="readonly" />
								</td>
							</tr>		
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="910" border="0">
						<tr>
							<td width="60%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td id="fabric_desc">
												<input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:300px;" placeholder="Fabric Description" disabled="disabled" />
												<input type="hidden" name="txt_fabric_description_id" id="txt_fabric_description_id" class="text_boxes"/>
												<input type="hidden" name="txt_gsm" id="txt_gsm" class="text_boxes"/>
												<input type="hidden" name="txt_dia" id="txt_dia" class="text_boxes"/>
												<input type="hidden" name="txt_dia_width_type" id="txt_dia_width_type" class="text_boxes"/>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Batch No</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" placeholder="Batch No" readonly="readonly" />
												<input type="hidden" name="hdn_batch_id" id="hdn_batch_id" class="text_boxes" value="" />
											</td>
										</tr>
										<tr>
											<td>Body Part</td>
											<td>
												<? echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:170px;" placeholder="Color" readonly="readonly" disabled="disabled" />
												<input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" readonly="readonly" />
											</td>
										</tr>
										<tr>
											<td>Delivery Qnty</td>
											<td>
												<input type="text" name="txt_Delivery_qnty" id="txt_Delivery_qnty" class="text_boxes text_boxes_numeric" placeholder="Write" style="width:170px;" placeholder="Delivery Qnty"/>
											</td>
										</tr>

									</table>
								</fieldset>
							</td>

							<td width="1%" valign="top">&nbsp;</td>
							<td width="38%" valign="top">
								<div id="roll_details_list_view"></div>
								<fieldset>
									<legend>Display</legend>
									<table>
										<tr>
											<td>FSO No</td>
											<td>
												<input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Fabric Production</td>
											<td>
												<input type="text" name="txt_fabric_production" id="txt_fabric_production" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Fabric Received</td>
											<td>
												<input type="text" name="txt_fabric_receive" id="txt_fabric_receive" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td width="120">Cumulative Delivery</td>
											<td>
												<input type="text" name="txt_cumulative_delivery" id="txt_cumulative_delivery" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Yet to Delivery</td>
											<td>
												<input type="text" name="txt_yet_delivery" id="txt_yet_delivery" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly" />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="4" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_finish_delivery_entry", 0,1,"reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container*roll_details_list_view','','','')",1);
								?>
								<input type="button" id="show_button" class="formbutton" style="width:80px" value="Print 2" onClick="fn_report_generated(2)" />
								<input type="hidden" id="update_mst_id" name="update_mst_id" value="" />
								<input type="hidden" id="hidden_receive_id" name="hidden_receive_id" value="" />
								<input type="hidden" id="hidden_receive_number" name="hidden_receive_number" value="" />
								<input type="hidden" id="hidden_receive_dtls_id" name="hidden_receive_dtls_id" value="" />
								<input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" />
							</td>
						</tr>
					</table>
					<div style="width:820px;" id="list_container_finishing"></div>
				</fieldset>
			</div>
			<div id="list_fabric_desc_container" style="width:380px; margin-left:5px;float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			<br clear="all" />
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
