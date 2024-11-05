<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Issue Return Entry

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-02-2015
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
echo load_html_head_contents("Finish Fabric Issue Return Info","../../", 1, 1, '','','');

?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function open_issuemrr()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id=$('#cbo_company_id').val();
			var page_link='requires/finish_fabric_issue_return_controller.php?action=mrr_popup&cbo_company_id='+cbo_company_id;
			var title="Search MRR Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_");
				$("#txt_issue_id").val(mrrNumber[0]);
				$("#txt_issue_no").val(mrrNumber[1]);
				$("#cbo_return_purpose").val(mrrNumber[2]);
				$("#txt_challan_no").val(mrrNumber[3]);
				return_qnty_basis(mrrNumber[2]);
				$("#tbl_item_info").find('input,select').val('');
				show_list_view(mrrNumber[0],'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_return_controller','');
				return;

			}
		}
	}

	function return_qnty_basis(purpose)
	{
		$("#txt_return_qnty").val('');

		if(purpose==8)
		{
			$("#txt_return_qnty").attr('onDblClick','');
			$("#txt_return_qnty").attr('placeholder','');
			$("#txt_return_qnty").attr("readonly",false);
			//$("#txt_no_of_roll").attr("readonly",false);
			$("#txt_return_qnty").attr('onkeyup','fnc_calculate_amount(this.value);');
		}
		else
		{
			$("#txt_return_qnty").attr('onDblClick','openmypage_po();');
			$("#txt_return_qnty").attr('placeholder','Double Click To Search');
			$("#txt_return_qnty").attr("readonly",true);
			//$("#txt_no_of_roll").attr("readonly",true);
			$("#txt_return_qnty").attr('ondblclick','openmypage_rtn_qty()');
			$("#txt_return_qnty").removeAttr('onkeyup');
		}
	}

	function fnc_calculate_amount(val)
	{
		var txt_rate=$('#txt_rate').val();
		$('#txt_amount').val((val*txt_rate).toFixed(2)).attr('disabled','disabled');
	}

	function set_form_data(data)
	{
		var data_ref=data.split('**');
		reset_form('','','cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*before_prod_id*txt_prod_id*txt_color*txt_return_qnty*txt_break_qnty*txt_break_roll*txt_order_id_all*prev_return_qnty*txt_rack*txt_shelf*cbo_bin*txt_remarks*txt_order_numbers*txt_tot_issue*txt_total_return_display*txt_total_return*txt_net_used*hide_net_used*txt_global_stock*update_id*hdn_recv_dtls_id','','','');
		$("#txt_prod_id").val(data_ref[2]);
		$("#txt_fabric_desc").val(data_ref[3]);
		//$("#txt_rack").val(data_ref[4]);
		//$("#txt_shelf").val(data_ref[5]);
		$("#txt_tot_issue").val(data_ref[6]);
		$("#txt_total_return_display").val(data_ref[7]);
		var balance=data_ref[6]-data_ref[7];
		$("#txt_net_used").val(balance);
		$("#txt_global_stock").val(data_ref[8]);
		$("#txt_color").val(data_ref[9]);
		$("#cbouom").val(data_ref[11]);
		var issue_purpose=$('#cbo_return_purpose').val();
		var order_type=data_ref[10];
		//$("#txt_floor").val(data_ref[13]);
		//$("#txt_room").val(data_ref[14]);
		$("#hdn_color_id").val(data_ref[15]);
		$("#cbo_fabric_type").val(data_ref[16]);
		$("#hdn_issue_dtls_id").val(data_ref[17]);
		$("#cbo_body_part").val(data_ref[18]);
		$("#cbo_item_name").val(data_ref[19]);
		$("#txt_rate").val(data_ref[20]);

		get_php_form_data(data_ref[0]+'**'+data_ref[1]+'**'+data_ref[12]+'**'+data_ref[13]+'**'+data_ref[14]+'**'+data_ref[4]+'**'+data_ref[5]+'**'+data_ref[21], "floor_room_rack_shelf", "requires/finish_fabric_issue_return_controller");
		get_php_form_data(data_ref[0]+'**'+data_ref[1]+'**'+data_ref[17], "populate_details_from_data", "requires/finish_fabric_issue_return_controller");

		if(order_type=="")
		{
			$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');
			//$('#txt_no_of_roll').removeAttr('readonly');
		}
		else
		{
			$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');
			//$('#txt_no_of_roll').removeAttr('readonly').attr('readonly','');
			openmypage_rtn_qty();
		}

		set_button_status(0, permission, 'fnc_fabric_issue_rtn',1,1);
	}

	function openmypage_rtn_qty()
	{
		var cbo_company_name = $('#cbo_company_id').val();
		var txt_issue_id = $('#txt_issue_id').val();
		var txt_prod_id = $('#txt_prod_id').val();
		var update_id = $('#update_id').val();
		var txt_return_qnty = $('#txt_return_qnty').val();
		var roll_maintained = $('#roll_maintained').val();
		var break_roll = $('#txt_break_roll').val();
		var break_qnty = $('#txt_break_qnty').val();
		var distribution_method = $('#distribution_method_id').val();
		var cbo_body_part = $('#cbo_body_part').val();
		var cbo_fabric_type = $('#cbo_fabric_type').val();
		var hidden_batch_id = $('#hidden_batch_id').val();
		
		var cbo_store_name = $('#cbo_store_name').val();
		var cbo_floor = $('#cbo_floor').val();
		var cbo_room = $('#cbo_room').val();
		var txt_rack = $('#txt_rack').val();
		var txt_shelf = $('#txt_shelf').val();
		var cbo_bin = $('#cbo_bin').val();


		if (form_validation('cbo_company_id*txt_issue_id*txt_prod_id','Company*Issue*Item Description')==false)
		{
			return;
		}
		var title = 'Issue Return Info';
		var page_link = 'requires/finish_fabric_issue_return_controller.php?cbo_company_name='+cbo_company_name+'&txt_issue_id='+txt_issue_id+'&txt_prod_id='+txt_prod_id+'&txt_return_qnty='+txt_return_qnty+'&prev_distribution_method='+distribution_method+'&update_id='+update_id+'&roll_maintained='+roll_maintained+'&break_roll='+break_roll+'&break_qnty='+break_qnty+'&cbo_body_part='+cbo_body_part+'&batch_id='+hidden_batch_id+'&cbo_fabric_type='+cbo_fabric_type+'&cbo_store_name='+cbo_store_name+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&cbo_bin='+cbo_bin+'&action=return_po_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var tot_qnty=this.contentDoc.getElementById("tot_qnty").value;
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value;
			var break_qnty=this.contentDoc.getElementById("break_qnty").value;
			var break_roll=this.contentDoc.getElementById("break_roll").value;
			var break_order_id=this.contentDoc.getElementById("break_order_id").value;
			var tot_roll=this.contentDoc.getElementById("tot_roll").value;

			var distribution_method=this.contentDoc.getElementById("distribution_method").value;

			$('#txt_return_qnty').val(tot_qnty);
			$('#txt_reject_return_qnty').val(tot_reject_qnty);
			$('#txt_break_qnty').val(break_qnty);
			$('#txt_break_roll').val(break_roll);
			$('#txt_order_id_all').val(break_order_id);
			$('#distribution_method_id').val(distribution_method);

			var txt_rate = $('#txt_rate').val();
			$('#txt_amount').val((tot_qnty*txt_rate).toFixed(2));
		}
	}

	function fnc_fabric_issue_rtn(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$('#issue_mst_id').val(),'issue_return_print','requires/finish_fabric_issue_return_controller');
			return;
		}
		/*else if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		//else
		//{
		if( form_validation('cbo_company_id*txt_issue_date*txt_issue_no*cbo_store_name*txt_batch_no*txt_fabric_desc*txt_return_qnty','Company Name*Issue Date*Issue No*Store Name*Batch No*Item Description*Return Qnty')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Return Date Can not Be Greater Than Current Date");
			return;
		}
		if($("#txt_return_qnty").val()*1<=0)
		{
			alert("Return Quantity Should be Greater Than Zero(0).");
			return;
		}

		if(operation==0){
			if($("#txt_return_qnty").val()*1>$("#txt_net_used").val()*1)
			{
				alert("Return Quantity Not Over Issue Quantity.");
				return;
			}
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][52]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][52]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][52]);?>')==false)
			{					
				return;
			}
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var txt_floor=$('#cbo_floor').val()*1;
		var txt_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (txt_floor==0 || txt_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && txt_floor==0 || txt_room==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && txt_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End

		var dataString = "txt_system_id*issue_mst_id*cbo_company_id*txt_issue_date*txt_issue_no*txt_issue_id*txt_challan_no*cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*before_prod_id*txt_prod_id*txt_color*txt_return_qnty*txt_break_qnty*txt_break_roll*txt_order_id_all*prev_return_qnty*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*roll_maintained*cbo_return_purpose*update_id*txt_remarks*cbouom*cbo_fabric_type*hdn_color_id*hdn_issue_dtls_id*cbo_body_part*cbo_item_name*txt_rate*txt_amount*txt_reject_return_qnty*hdn_recv_dtls_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_issue_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_issue_rtn_reponse;
		//}
	}

	function fnc_fabric_issue_rtn_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				if (reponse[0]==2 && reponse[4]==1) // is mst delete reset form
				{
					reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_return_purpose,9','disable_enable_fields(\'cbo_company_id*txt_issue_date*txt_issue_no*cbo_return_purpose\');');
				}
				else
				{
					$("#txt_system_id").val(reponse[1]);
					$("#issue_mst_id").val(reponse[2]);
					var issue_id = $("#txt_issue_id").val();
					disable_enable_fields( 'cbo_company_name', 1, "", "" );

					show_list_view(reponse[2]+'**'+reponse[3],'show_dtls_list_view','div_details_list_view','requires/finish_fabric_issue_return_controller','');
					show_list_view(issue_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_return_controller','');

					$('#cbo_company_id').attr('disabled','disabled');
					$('#txt_issue_no').attr('disabled','disabled');
					$('#txt_issue_date').attr('disabled','disabled');
					//child form reset here after save data-------------//
					$("#tbl_item_info").find('input,select').val('');
					$("#tbl_display_info").find('input,select').val('');
				}				
				set_button_status(0, permission, 'fnc_fabric_issue_rtn',1,1);
				release_freezing();
			}
			release_freezing();
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/finish_fabric_issue_return_controller.php?data=" + data+'&action='+action, true );
	}

	function open_returnpopup()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var company = $("#cbo_company_id").val();
		var roll_maintained = $("#roll_maintained").val();
		var page_link='requires/finish_fabric_issue_return_controller.php?action=return_number_popup&company='+company;
		var title="Search Issue Return Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split('_');
			// master part call here
			reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id*roll_maintained*store_update_upto');

			get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/finish_fabric_issue_return_controller");
			show_list_view(returnNumber[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_return_controller','');
			show_list_view(returnNumber[0]+'**'+ roll_maintained,'show_dtls_list_view','div_details_list_view','requires/finish_fabric_issue_return_controller','');

			$('#cbo_company_id').attr('disabled','disabled');
			$('#txt_issue_no').attr('disabled','disabled');
			$('#txt_issue_date').attr('disabled','disabled');
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:740px; float:left;" align="center">
				<fieldset style="width:730px;">
					<legend>Finish Fabric Issue Return Entry</legend>
					<fieldset style="width:730px;">
						<table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
							<tr>
								<td colspan="3" align="right"><strong>Issue Rtn No</strong></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="open_returnpopup();" readonly />
									<input type="hidden" id="issue_mst_id" name="issue_mst_id" >
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "get_php_form_data(this.value,'roll_maintained','requires/finish_fabric_issue_return_controller' );load_drop_down( 'requires/finish_fabric_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );" );
									?>
								</td>
								<td class="must_entry_caption">Return Date</td>
								<td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:138px;" readonly placeholder="Select Date" /></td>
								<td class="must_entry_caption">Issue No</td>
								<td>
									<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:138px;" onDblClick="open_issuemrr();" placeholder="Double Click To Search" readonly />
									<input type="hidden" id="txt_issue_id" name="txt_issue_id" >
								</td>
							</tr>
							<tr>
								<td >Challan No.</td>
								<td><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:138px;" maxlength="20" title="Maximum 20 Character" /></td>
								<td>Return Purpose</td>
								<td>
									<?
									$return_purpose=array('1' => 'Cutting Closed & Return to Store','2' => 'Return to Re-Process' );
									echo create_drop_down("cbo_return_purpose", 150,$return_purpose,"", 1, "--Select--", 0, "");
									?>
								</td>
								<td >&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</fieldset>
					<table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
						<tr>
							<td width="68%" valign="top">
								<fieldset>
									<legend>Details
									</legend><table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">
										<tr>
											<td class="must_entry_caption" width="30%">Store Name</td>
											<td id="store_td">
												<?
												echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "-- Select store --", 0, "",1 );
												?>
											</td>
										</tr>
										<tr>
											<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 170,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 170,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 170,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 170,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td>Bin Box</td>
											<td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 170,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Batch No.</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:158px;" placeholder="Display" readonly disabled/>
												<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
											</td>
										</tr>
										<tr>
											<td>Body Part</td>
											<td id="body_part_td">
												<?
												echo create_drop_down( "cbo_body_part", 170, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
												?>
											</td>
										</tr>
										<tr>
											<td>Garments Item</td>
											<td id="gmt_item_td">
												<?
												echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Gmt. Item --", "", "",1,0 );	
												?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td id="fabricDesc_td">
												<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:200px;" placeholder="Display" disabled />
												<input type="hidden" name="before_prod_id" id="before_prod_id" readonly>
												<input type="hidden" id="txt_prod_id" name="txt_prod_id" />
												<span class="must_entry_caption">UOM</span>
												<? echo create_drop_down( "cbouom", 70, $unit_of_measurement,'', 1, '-Uom-', 12, "",1,"1,12,23,27" ); ?>
											</td>
										</tr>
										<tr>
											<td>Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:158px" placeholder="Display" disabled />
												<input type="hidden" id="hdn_color_id" name="hdn_color_id" >
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Issue Qnty</td>
											<td>
												<input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:158px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_rtn_qty();"   />
												<input type="hidden" id="txt_break_qnty" name="txt_break_qnty" >
												<input type="hidden" id="txt_break_roll" name="txt_break_roll" >
												<input type="hidden" id="txt_order_id_all" name="txt_order_id_all" >
												<input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >
												<input type="hidden" name="distribution_method_id" id="distribution_method_id" />
											</td>
										</tr>
										<tr>
											<td>Reject Qnty</td>
											<td>
												<input class="text_boxes_numeric" type="text" name="txt_reject_return_qnty" id="txt_reject_return_qnty" style="width:158px;" readonly   />
											</td>
										</tr>
										<tr>
											<td>Rate</td>
											<td>
												<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:158px;" disabled readonly /> 
											</td>
										</tr>
										<tr>
											<td>Amount</td>
											<td>
												<input type="text" name="txt_amount" class="text_boxes_numeric" id="txt_amount" disabled style="width:158px;" readonly /> 
											</td>
										</tr>
										
										<tr>
											<td>Fabric Shade</td>
											<td>
												<? echo create_drop_down( "cbo_fabric_type", 170, $fabric_shade,"",1, "-- Select --", 0, "",1 ); ?>
											</td>
										</tr>
										<tr>
											<td>Remarks</td>
											<td>
												<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px" />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
							<td width="2%" valign="top"></td>
							<td width="30%" valign="top">
								<fieldset>
									<legend>Display</legend>
									<table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >
										<tr style="display:none;">
											<td>Order Numbers</td>
											<td>
												<input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:100px" placeholder="Display" readonly disabled />
											</td>
										</tr>
										<tr>
											<td>Fabric Issue</td>
											<td><input class="text_boxes_numeric" type="text" name="txt_tot_issue" id="txt_tot_issue" style="width:100px;" placeholder="Display" readonly disabled /></td>
										</tr>
										<tr>
											<td>Cumulative Return</td>
											<td>
												<input class="text_boxes_numeric" type="hidden" name="txt_total_return" id="txt_total_return" style="width:100px;" placeholder="Display" readonly disabled />
												<input class="text_boxes_numeric" type="text" name="txt_total_return_display" id="txt_total_return_display" style="width:100px;" placeholder="Display" readonly disabled />
											</td>
										</tr>
										<tr>
											<td>Yet to Return</td>
											<td>
												<input class="text_boxes_numeric" type="text" name="txt_net_used" id="txt_net_used" style="width:100px;" placeholder="Display" readonly disabled />
												<input class="text_boxes_numeric" type="hidden" name="hide_net_used" id="hide_net_used" readonly />
											</td>
										</tr>
										<tr>
											<td>Global Stock</td>
											<td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes_numeric" style="width:100px" readonly disabled /></td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="3" class="button_container" width="100%">
								<?
								echo load_submit_buttons($permission, "fnc_fabric_issue_rtn", 0,1,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_return_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_return_purpose\');active_inactive(9,1);')",1);
								?>
								<input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
								<input type="hidden" id="update_id" name="update_id" value="" />
								<input type="hidden" id="hdn_issue_dtls_id" name="hdn_issue_dtls_id" value="" />
								<input type="hidden" id="hdn_recv_dtls_id" name="hdn_recv_dtls_id" value="" />
								<input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
							</td>
						</tr>
					</table>
					<div style="width:730px;" id="div_details_list_view"></div>
				</fieldset>
			</div>
			<div id="list_fabric_desc_container" style="width:580px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
