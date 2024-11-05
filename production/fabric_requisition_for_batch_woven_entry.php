<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Grey Fabric Requisition for batch
				
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	28-09-2022
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
echo load_html_head_contents("Fabric Requisition For Batch","../", 1, 1, $unicode,0,0); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openmypage_requisition()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_batch_woven_entry_controller.php?action=requisition_popup&company_id='+cbo_company_id,'Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value;	 //Requisition Id and Number
			
			if(reqn_id!="")
			{
				freeze_window(5);
				reset_form('requisitionEntry_1','','','','','');
				get_php_form_data(reqn_id, "populate_data_from_requisition", "requires/fabric_requisition_for_batch_woven_entry_controller" );

				show_list_view($('#hidden_batch_id').val()+'**'+cbo_company_id+'**'+$('#txt_batch_no').val(),'show_color_listview','list_color','requires/fabric_requisition_for_batch_woven_entry_controller','');	
				show_list_view(reqn_id,'show_details_listview','detail_list','requires/fabric_requisition_for_batch_woven_entry_controller','');	
				set_all_onclick();
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch',1);
				release_freezing();
			}
		}
	}
	
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Batch No Selection Form';
			var page_link = 'requires/fabric_requisition_for_batch_woven_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var load_unload=this.contentDoc.getElementById("hidden_load_unload").value;
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				var ext_from=this.contentDoc.getElementById("hidden_ext_from").value;

				//alert(load_unload);return;
				if(batch_id!="")
				{
					$("#txt_batch_no").val(batch_no);
					$("#hidden_batch_id").val(batch_id);
					show_list_view(batch_id+'**'+cbo_company_id+'**'+batch_no,'show_color_listview','list_color','requires/fabric_requisition_for_batch_woven_entry_controller','')
				}
			}
		}
	}

	function fnc_count_total_qty()
	{
		var tot_count_qty=0;
		var num_row_total=$('#scanning_tbl tbody tr').length;
		for(var kk=1; kk<num_row_total; kk++)
		{
			tot_count_qty+=$('#reqsnQty'+kk).val()*1;
		}
		$('#total_blnc_qty_td_id').val(number_format(tot_count_qty,2));
	}
	function fnc_fabric_requisition_for_batch( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch','requires/fabric_requisition_for_batch_woven_entry_controller');
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_requisition_date*cbo_store_name*txt_batch_no','Company*Requisition Date*Store*Batch No')==false)
		{
			return; 
		}

		if($('#txt_req_qnty').val() * 1 <= 0)
		{
			alert('Enter Req Qnty');
			return;
		}
		
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_requisition_date*txt_requisition_no*update_id*cbo_body_part*cbo_uom*txt_gsm*txt_width*txt_req_qnty*cbo_color_range*color_id*txt_roll_no*hidden_batch_id*txt_batch_no*hidden_po_id*hidden_job_no*hidden_booking_no*txt_fabric_description*hidden_dtls_id*cbo_store_name',"../");
		//alert(operation);+dataString
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/fabric_requisition_for_batch_woven_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_fabric_requisition_for_batch_Reply_info;
	}

	function fnc_fabric_requisition_for_batch_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_requisition_no').value = response[2];
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch',1);
				show_list_view($('#hidden_batch_id').val()+'**'+$('#cbo_company_id').val()+'**'+$('#txt_batch_no').val(),'show_color_listview','list_color','requires/fabric_requisition_for_batch_woven_entry_controller','');	
				show_list_view(document.getElementById('update_id').value,'show_details_listview','detail_list','requires/fabric_requisition_for_batch_woven_entry_controller','');
			}
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/fabric_requisition_for_batch_woven_entry_controller.php?data=" + data+'&action='+action, true );
	}
	function put_batch_data(data,dtls_id='')
	{
		var data = data.split('*');
		var qnty = data[0];
		var po_id = data[1];
		var color_id = data[2];
		var color_range_id = data[3];
		var item_description = data[4];
		var booking_no = data[5];
		var sales_order_no = data[6];
		var batch_id = data[7];
		var width_dia_type = data[8];
		var gsm = data[9];
		var grey_dia = data[10];
		var fin_dia = data[11];
		var body_part_id = data[12];
		var fullwidth = data[13];
		var cutablewidth = data[14];
		var color = data[15];
		var balance_qnty = data[16];
		$("#txt_color").val(color);
		$("#color_id").val(color_id);
		$("#cbo_color_range").val(color_range_id);
		$("#cbo_body_part").val(body_part_id);
		$("#txt_fabric_description").val(item_description);
		$("#txt_gsm").val(gsm);
		$("#txt_width").val(fullwidth);
		$("#txt_req_qnty").val(qnty);
		$("#hidden_po_id").val(po_id);
		$("#hidden_job_no").val(sales_order_no);
		$("#hidden_booking_no").val(booking_no);
		$("#balance_qnty").val(balance_qnty);
		$("#hidden_dtls_id").val(dtls_id);
		if(data.length > 18 )
		{
			var cbo_uom = data[17];
			var txt_roll_no = data[18];
			$("#cbo_uom").val(cbo_uom);
			$("#txt_roll_no").val(txt_roll_no);
		}
		else{
			$("#txt_roll_no").val('');
		}
		calculate_amount();
	}
	

	function calculate_amount()
	{
		var qnty = $("#txt_req_qnty").val() * 1;
		var balance_qnty = $("#balance_qnty").val() * 1;
		if(balance_qnty < qnty )
		{
			alert('Over quantity not allowed');
			$("#txt_req_qnty").val(balance_qnty);
			qnty = balance_qnty;
		}
		
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="requisitionEntry_1" id="requisitionEntry_1"> 
		<div align="center" style="width:100%;">
            <fieldset style="width:690px;">
				<legend>Fabric Requisition</legend>
                <table cellpadding="0" cellspacing="2" width="650">
                    <tr>
                        <td align="right" colspan="3"><b>Requisition No</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_requisition()" placeholder="Browse For Requisition No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td width="80" class="must_entry_caption" align="right">Company</td>
                        <td width="140">
                            <? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/fabric_requisition_for_batch_woven_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );
                            ?>
                        </td>
                        <td width="80" align="right">Location</td>                                              
                        <td width="140" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
                        <td width="130" align="right">Requisition Date</td>
                        <td width="80"><input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:80px;" readonly /></td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption"> Store Name </td>
                    	<td id="store_td">
							<?
							echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
							?>
						</td>
						<td colspan="4"></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3"><b>Batch Number</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Browse For Batch No" onDblClick="openmypage_batchNo()" readonly/>
                        	<input type="hidden" name="hidden_batch_id" id="hidden_batch_id">
                        </td>
                    </tr>
                </table>
                
			</fieldset>
			<br>
			<table cellpadding="0" cellspacing="1" width="690" border="0">
				<tr>
					<td width="100%" valign="top">
						<fieldset>
							<legend>New Entry
								
							</legend>
							<table cellpadding="0" cellspacing="2" width="100%">
								<tr>
									<td class="must_entry_caption">Body Part</td>
									<td>
										<? 
										echo create_drop_down( "cbo_body_part", 130, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
										?>
									</td>
									<td>UOM</td>
									<td>
										<?
										echo create_drop_down( "cbo_uom", 132, $unit_of_measurement,"", 0, "", '12', "",0,12 );
										?>
									</td>
								</tr>
								<tr>
									<td class="must_entry_caption">Fabric Description </td>
									<td colspan="3">
										<input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:455px" onDblClick="openmypage_fabricDescription()" placeholder="Double Click To Search" disabled="disabled" readonly/>
										<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes">
									</td>
								</tr>
								<tr>
									<td class="must_entry_caption">Weight</td>
									<td>
										<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:120px;"  />	
									</td>
									<td class="must_entry_caption">Color Range</td>
									<td>
										<?
										echo create_drop_down( "cbo_color_range", 132, $color_range,"",1, "-- Select --", 0, "" );
										?>
									</td>
								</tr> 
								<tr>
									<td class="must_entry_caption">Dia / Width</td>
									<td>
										<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:120px;text-align:right;" />	
									</td>
									<td>No of Roll</td>
									<td>
										<input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:120px" />
									</td>
									
								</tr>
								
					
								<tr>
									<td class="must_entry_caption">Reqn. Qty.</td>
									<td>
										<input type="text" name="txt_req_qnty" id="txt_req_qnty" class="text_boxes_numeric" style="width:120px;"  placeholder="Reqn. Qty." onchange="calculate_amount()" />	
										<input type="hidden" id="balance_qnty">
									</td>
									<td>Fabric Color</td>
									<td>
										<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="openmypage_color();" readonly/>
										<input type="hidden" name="color_id" id="color_id" />
										<input type="hidden" name="hidden_prog_id" id="hidden_prog_id" />
									</td>
										
								</tr>
								
								
								
								<tr>
									<td></td>
									<td>
										<input type="hidden" id="hidden_po_id">
										<input type="hidden" id="hidden_job_no">
										<input type="hidden" id="hidden_booking_no">
										<input type="hidden" id="hidden_dtls_id">
									</td>										
									<td></td>										
								</tr>
								
								
							</table>
						</fieldset>
					</td>                    
					                     
				</tr>		
                <tr>
                	<td align="center" class="button_container">
                        <? 
                        	echo load_submit_buttons($permission,"fnc_fabric_requisition_for_batch",0,1,"reset_form('requisitionEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();')",1);
                        ?>
                    </td>
                </tr>
            </table>
            <div id="detail_list" style="width:500px; overflow:auto; float:left; margin-left:90px; padding-top:5px; margin-top:5px; position:relative;"></div>
            <div id="list_color" style="width:500px; overflow:auto; float:left; margin-left:90px; padding-top:5px; margin-top:5px; position:relative;"></div>
    	</div>
	</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
