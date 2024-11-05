<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sub-Contruct Embellishment Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	MD. REAZ UDDIN
Creation date 	: 	02.01.2018
Purpose			:
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
echo load_html_head_contents("Embellishment Delivery Info","../../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<?php echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	function fnc_embl_delivery(operation)
	{
		
		/*if(operation==2)
		{
			alert("Delete Not Allowed");
			return;
		}*/
		//if( form_validation('cbo_company_id*txt_delivery_date*cbo_party_name*cbo_party_id','Company Name*Delivery Date*Party Name*Party ID')==false )
		if( form_validation('cbo_company_id*txt_delivery_date','Company Name*Delivery Date')==false )
		{
			return;
		}
		
		var j=0; 
		var dataString=''; //var all_barcodes='';
		//alert(dataString);
		$("#embellishment_details_container").find('tr').each(function()
		{
			
			var EmblTypeId=$(this).find('input[name="txtEmblTypeId[]"]').val();
			var OrderId=$(this).find('input[name="txtOrderId[]"]').val();
			var ProdDtlsId=$(this).find('input[name="emblProdDtlsId[]"]').val();
			var BatchId=$(this).find('input[name="hiddenBatchId[]"]').val();
			var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
			var BatchColorId=$(this).find('input[name="txtBatchColorId[]"]').val();
			var GmtsItem=$(this).find('input[name="txtGmtsItem[]"]').val();
			var ProcessId=$(this).find('input[name="txtProcessId[]"]').val();
			var QcPassQty=$(this).find('input[name="txtQcPassQty[]"]').val();
			var CurrentDelivery=$(this).find('input[name="txtCurrentDelivery[]"]').val();
			var OperatorName=$(this).find('input[name="txtOperatorName[]"]').val();
			var OperatorId=$(this).find('input[name="txtOperatorId[]"]').val();
			var ShiftId=$(this).find('select[name="cboShift[]"]').val();
			//alert(CurrentDelivery);
			if(CurrentDelivery != "")
			{
				j++;
				
				dataString += '&txtEmblTypeId_' + j + '=' + EmblTypeId
				+ '&txtOrderId_' + j + '=' + OrderId 
				+ '&emblProdDtlsId_' + j + '=' + ProdDtlsId 
				+ '&hiddenBatchId_' + j + '=' + BatchId 
				+ '&updateIdDtls_' + j + '=' + updateIdDtls 
				+ '&txtBatchColorId_' + j + '=' + BatchColorId 
				+ '&txtGmtsItem_' + j + '=' + GmtsItem 
				+ '&txtProcessId_' + j + '=' + ProcessId 
				+ '&txtQcPassQty_' + j + '=' + QcPassQty 
				+ '&txtCurrentDelivery_' + j + '=' + CurrentDelivery
				+ '&txtOperatorName_' + j + '=' + OperatorName 
				+ '&txtOperatorId_' + j + '=' + OperatorId
				+ '&cboShift_' + j + '=' + ShiftId;
			}
		});
		
		if(dataString == "")
		{
			if( form_validation('txtCurrentDelivery_1','Current Delivery')==false )
			{
				return;
			}	
		}
		
		$('#txt_tot_row').val(j);
		
		var data="action=save_update_delete&operation="+operation+"&dataString="+dataString+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_party_id*txt_challan_no*txt_delivery_date*txt_transport_company*txt_update_id*hiddenBatchAgainst*txt_sys_id*txtPoId*txt_tot_row',"../../");
		//alert (data); //return;
		freeze_window(operation);
		http.open("POST","requires/subcon_embellishment_delivery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_embl_delivery_Reply;
	}
	
	function fnc_embl_delivery_Reply()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();
			//alert(http.responseText);return;
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			
			var response=http.responseText.split('**');
			show_msg(trim(response[0]));
			//release_freezing();
			
			if( response[0]==0 || response[0]==1)// Save
			{
				$("#txt_update_id").val(response[1]);
				$("#txt_sys_id").val(response[2]);
				$("#txt_challan_no").val(response[3]);
				
				fnc_reset_form();
				
				//reset_form('embelDelv_1','delivery_list_view','','','childFormReset()');
				
				show_list_view(response[1],'delivery_list_view','delivery_list_view','requires/subcon_embellishment_delivery_controller','');
				setFilterGrid("details_table",-1);
				//reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*sewing_production_variable*txt_total_carton_qnty*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				//$('#cbo_party_name').attr('disabled','disabled');
				
				set_button_status(0, permission, 'fnc_embl_delivery',1,1);
				
			} 
			else if(response[0]==2)
			{
				reset_form('embelDelv_1','delivery_list_view','','','childFormReset();fnc_reset_form();');
			}
				release_freezing();
		}
	}
	
	
	function SearchOroderNo() // Order No Pop Up
	{
		//alert (page_link); return;
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_id").val();
		var update_id = $("#txt_update_id").val();
		
		var previousPoId = $("#txtPoId").val();
		
		//alert(previousPoId);
		var page_link = 'requires/subcon_embellishment_delivery_controller.php?action=order_popup&company='+document.getElementById('cbo_company_id').value+'&cbo_party_name='+document.getElementById('cbo_party_id').value+'&Batch_Against='+document.getElementById('hiddenBatchAgainst').value+'&txtPoId='+previousPoId;
		var title = 'Order Search'
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_id=this.contentDoc.getElementById("hidden_order_id").value;//po id
			var batch_against=this.contentDoc.getElementById("BatchAgainst").value;//po id
			//var item_id=this.contentDoc.getElementById("hidden_item_id").value; 
			//var po_qnty=this.contentDoc.getElementById("hidden_order_qty").value;
			//alert (order_id);
			//$("#txt_order_no").val(order_id);
			
			
			if (order_id!="")
			{
				//freeze_window(5);
				//childFormReset();//child from reset
				var batchIdNo=$('#txtBatchNo_1').val();
				if(batchIdNo=="")
				{
					var tot_row=0;
					$("#embellishment_details_container tr").remove();
				}
				else
				{
					var tot_row=$('#txt_tot_row').val();
				}
				
				
				if(previousPoId != "")
				{
					previousPoId += ","+order_id;
					$("#txtPoId").val(previousPoId);
				}
				else
				{
					$("#txtPoId").val(order_id);
				}
				$("#hiddenBatchAgainst").val(batch_against);
				
				var data = order_id+"**"+company+"**"+batch_against+"**"+tot_row;
				//alert(data);
				var list_view_order_data =return_global_ajax_value( data, 'populate_order_data', '', 'requires/subcon_embellishment_delivery_controller');
				$("#embellishment_details_container").prepend(list_view_order_data);	
				/*
				//get_php_form_data(order_id+'**'+item_id, "populate_data_from_search_popup", "requires/subcon_embellishment_delivery_controller" );
				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var cbo_process_name=$('#cbo_process_name').val();
				if(variableSettings!=1) 
				{ 
					get_php_form_data(order_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+cbo_process_name, "color_and_size_level", "requires/subcon_embellishment_delivery_controller" ); 
				}
				else
				{
					$("#txt_delivery_qty").removeAttr("readonly");
				}
				
				//show_list_view(order_id+'**'+item_id,'show_dtls_listview','delivery_list_view','requires/subcon_embellishment_delivery_controller','');
				//show_list_view(order_id,'show_country_listview','list_view_country','requires/subcon_embellishment_delivery_controller','');
				*/
				
				//if(update_id !=""){
				//	set_button_status(1, permission, 'fnc_embl_delivery',1,0);
				//}else{
				//	set_button_status(0, permission, 'fnc_embl_delivery',1,0);
				//}
				
				fnc_tr_count();
				release_freezing();
			}
		}
	}
	
	function SearchSystemId() // System ID Pop Up
	{ 
		var data=document.getElementById('cbo_company_id').value;
		
		var page_link='requires/subcon_embellishment_delivery_controller.php?action=delivery_id_popup&data='+data
		var title='Subcontract Delivery';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=700px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_delivery_id");
			//alert (theemail.value);return;
			//alert(theemail.value);
			if (theemail.value!="")
			{
				//var ret_value=theemail.value.split("_");
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/subcon_embellishment_delivery_controller" );
				show_list_view(theemail.value,'delivery_list_view','delivery_list_view','requires/subcon_embellishment_delivery_controller','');
				setFilterGrid("details_table",-1)
				
				//reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*sewing_production_variable*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				
				set_button_status(0, permission, 'fnc_embl_delivery',1,0);
				
				release_freezing();
			}
		}
	}
	
	
	function auto_complete(company_id,scopid) // Auto Complite Party/Transport Com
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		
		if(scopid == 1)
		{
			var party = return_global_ajax_value( company_id, 'party_name', '', 'requires/subcon_embellishment_delivery_controller');
			partyInfo = eval(party);
			$("#cbo_party_name").autocomplete({
			 source: partyInfo,	
			 search: function( event, ui ) {
				$("#cbo_party_id").val("");
				$("#hidden_party_name").val("");
			 },	 
			 select: function (e, ui) {
				$(this).val(ui.item.label);
				$("#hidden_party_name").val(ui.item.label);
				$("#cbo_party_id").val(ui.item.id);
				}
			 });
			 
			 $(".party_name").live("blur",function(){
				  if($(this).siblings(".hdn_party_name").val() == ""){
					  $(this).val("");
				 }
			  });
		}
		else if(scopid == 2){
			var transportComp = return_global_ajax_value( company_id, 'transport_company', '', 'requires/subcon_embellishment_delivery_controller');
			transportCompany = eval(transportComp);
			$("#txt_transport_company").autocomplete({
				source : transportCompany
			});
		}
		else
		{
			alert("Problem is here !!");return;	
		}
		
	}
	
	
	 function process_popup( process_id ) // Show Process Data in PopUp
	 {
		var hiddenBatchAgainst = $('#hiddenBatchAgainst').val();
		//alert(hiddenBatchAgainst+"**"+process_id);return;
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/subcon_embellishment_delivery_controller.php?hiddenBatchAgainst='+hiddenBatchAgainst+'&process_id='+process_id+'&action=process_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=360px,center=1,resize=1,scrolling=0','');
	 }
	 
	 function fnc_tr_count() // All tr count from the form
	{
		var numRow = $('#embellishment_details_container tr').length;
		$('#txt_tot_row').val(numRow);
	}
	
	
	function fnc_embl_delivery_dtls(id) 
	{
		$("#txt_tot_row").val('');
		fnc_reset_form();
		
		var MstDtlsId = id.split("_");
		//alert(MstDtlsId[0]);
		//reset_form('embelDelv_1','','','','childFormReset()');
		var company=$("#cbo_company_id").val();
		var batch_against=$("#hiddenBatchAgainst").val();
		var tot_row=$("#txt_tot_row").val();
		
		//get_php_form_data( MstDtlsId[0], "load_php_data_to_form", "requires/subcon_embellishment_delivery_controller" );
		
		var data = id+"**"+company+"**"+batch_against+"**"+tot_row;
		//alert(data);
		var list_view_order_data =return_global_ajax_value( data, 'populate_delivery_dtls_data', '', 'requires/subcon_embellishment_delivery_controller');
		//alert(list_view_order_data);
		$("#embellishment_details_container").html('');
		$("#embellishment_details_container").prepend(list_view_order_data);
		fnc_tr_count();
		
		var preOrderIDs="";
		$("#embellishment_details_container").find('tr').each(function()
		{
			if(preOrderIDs == "")
			{
				preOrderIDs = $(this).find('input[name="txtOrderId[]"]').val();
				
			}else{
				var OrderId = $(this).find('input[name="txtOrderId[]"]').val();
				preOrderIDs += ","+OrderId
			}
		});	
		//alert(preOrderIDs);	
		$("#txtPoId").val(preOrderIDs);
		set_button_status(1, permission, 'fnc_embl_delivery',1,0);
	}
	
	function calculate_amount(i)
	{
		var QcPassQty	=$("#txtQcPassQty_"+i).val()*1;
		var PreDelivQty	=$("#txtPreDelivQty_"+i).val()*1;
		//var Balance		=$("#txtBalance_"+i).val()*1;
		var CurrentDelivery=$("#txtCurrentDelivery_"+i).val()*1;
		var hiddCurrentDelivery=$("#hiddenCurrentDelivery_"+i).val();
		var cBalance = QcPassQty-PreDelivQty;
		if( cBalance < CurrentDelivery )
		{	
			alert("Now over QC Pass Quantity("+QcPassQty+")");
			$("#txtCurrentDelivery_"+i).val(hiddCurrentDelivery);
		}
		
		/*
		var QcQty=0;
		var PreQty=0;
		var txtBal=0;
		var CDelivery=0;
		$("#embellishment_details_container").find('tr').each(function()
		{
			var trcheck = $(this).find('input[name="txtQcPassQty[]"]').val();
			if (trcheck != "NaN")
			{
				alert(trcheck);
				QcQty +=  $(this).find('input[name="txtQcPassQty[]"]').val();
				PreQty += $(this).find('input[name="txtPreDelivQty[]"]').val();
				txtBal	+= $(this).find('input[name="txtBalance[]"]').val();
				CDelivery	+= $(this).find('input[name="txtCurrentDelivery[]"]').val();
			}
		});
		
		$("#txtSumQcPassQty").val(QcQty*1);
		$("#txtSumPreDelivQty").val(PreQty*1);
		$("#txtSumBalance").val(txtBal*1);
		$("#txtSumCurrentDelivery").val(CDelivery*1);
		*/
		
	}
	
	
	function childFormReset()
	{
		$("#breakdown_td_id").html('');
		$('#cbo_party_name').removeAttr('disabled');
		$('#embellishment_details_container').children( 'tr:not(:last)' ).remove();
		
		//$('#cbo_company_id').removeAttr('disabled');
		//$('#cbo_location_name').removeAttr('disabled');
		//$('#cbo_company_supplier').removeAttr('disabled');
		//$('#cbo_location_name_s').removeAttr('disabled');
		//$('#lbl_batch_Against').text('');
		//reset_form('embellishmentEntry_1','','','','');
		//set_button_status(0, permission, 'fnc_embel_entry',1,1);
	}
	
	function fnc_reset_form()
	{
		//$('#scanning_tbl tbody tr').remove();
		$('#embellishment_details_container tr').remove();
		
		var html='<tr class="general" id="tr_1" name="tr[]" ><td><input type="text" name="txtSl[]"  id="txtSl_1" value="1" class="text_boxes_numeric" style="text-align:center; width:30px" disabled /><input type="hidden" name="emblProdDtlsId[]"  id="emblProdDtlsId_1" value="" style="width:50px" /><input type="hidden" name="txtOrderId[]"  id="txtOrderId_1" value="" class="" style="width:50px" /><input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_1" value="" class="" style="width:50px" /></td><td><input type="text" name="EmblType[]"  id="EmblType_1" value="" class="text_boxes" style="width:80px" placeholder="Display"disabled /><input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_1" value="" class="text_boxes_numeric" style="width:50px" disabled /></td><td><input type="text" name="txtBatchNo[]"  id="txtBatchNo_1"  value="" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled /><input type="hidden" name="hiddenBatchId[]" id="hiddenBatchId_1" value="" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled /></td><td><input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_1" value="" class="text_boxes" style="width:80px" placeholder="Display"disabled /><input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_1" value="" style="width:30px" disabled /></td><td id=""><input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_1" value="" class="text_boxes"  style="width:80px" readonly disabled /><input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_1" value="" class="text_boxes"  style="width:50px" /></td><td><input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:80px;"  tabindex="12" placeholder="Dbl.Click" readonly onDblClick="" title="Bbl. Click" /><input type="hidden" name="txtProcessId[]" id="txtProcessId_1" value="" /></td><td><input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_1" value="" class="text_boxes_numeric" style="width:50px" readonly disabled /></td><td><input type="text" name="txtPreDelivQty[]"  id="txtPreDelivQty_1" class="text_boxes_numeric" style="width:50px"   placeholder="Display"  /></td><td><input type="text" name="txtBalance[]"  id="txtBalance_1" class="text_boxes_numeric"  style="width:50px" placeholder="Display" readonly /></td><td><input type="text" name="txtCurrentDelivery[]"  id="txtCurrentDelivery_1" value="" class="text_boxes_numeric" placeholder="Write"  style="width:50px" /><input type="hidden" name="hiddenCurrentDelivery[]"  id="hiddenCurrentDelivery_1" value="" class="text_boxes_numeric" placeholder="Write"  style="width:50px" /></td><td><input type="text" name="txtCustBuyer[]"  id="txtCustBuyer_1" value="" class="text_boxes" style="width:80px" placeholder="Display" disabled  /></td><td><input type="text" name="txtStyle[]"  id="txtStyle_1" value="" class="text_boxes" style="width:80px" placeholder="Display" disabled /></td><td><input type="text" name="txtOperatorName[]" id="txtOperatorName_1" value="" class="text_boxes" style="width:100px;" placeholder="Display" disabled  tabindex="4" /><input type="hidden" name="txtOperatorId[]" id="txtOperatorId_1" value="" style="width:60px"/></td><td><? echo create_drop_down( "cboShift_1", 80, $shift_name,"", 1, '- Select -', 0,"",'1','','','','','','','cboShift[]'); ?></td></tr>';
		
		$("#embellishment_details_container").html(html);
		
		/*
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_dyeing_source').val(0);
		$('#cbo_dyeing_company').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_issue_purpose').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		$('#roll_weight_total').text('');
		document.getElementById("accounting_posted_status").innerHTML="";*/
	}
	

/*
	function fnc_embl_delivery(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title, "gmts_delivery_print", "requires/subcon_embellishment_delivery_controller" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_id*txt_order_no*txt_delivery_qty*txt_delivery_date','Company Name*Order No*Delivery Quantity*Delivery Date')==false )
				{
				return;
				}		
			
				var sewing_production_variable = $("#sewing_production_variable").val();
				//alert(sewing_production_variable);return;
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				//alert(colorList);
				var i=0;var colorIDvalue='';
				if(sewing_production_variable==2)//color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
				}
				else if(sewing_production_variable==3)//color and size level
				{	
					$("input[name=colorSize]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
							}
							//alert( $(this).val() );return;
							
						}
						i++;
					});
				}
				
				//var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_id*cbo_location',"../");
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_id*sewing_production_variable*cbo_location_name*cbo_party_name*cbo_process_name*cbo_item_name*hidden_po_break_down_id*hidden_colorSizeID*txt_delivery_date*txt_delivery_qty*txt_total_carton_qnty*txt_challan_no*txt_ctn_qnty*txt_transport_company*txt_vehical_no*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_update_id*txt_sys_id*cbo_forwarder*txt_mst_id*txt_dtls_id',"../../");
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/subcon_embellishment_delivery_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_embl_delivery_Reply;
			//}
		}
	}
	function fn_qnty_per_ctn()
	{
		 var exQnty = $('#txt_delivery_qty').val();
		 var ctnQnty = $('#txt_total_carton_qnty').val();
		  
		 if(exQnty!="" && ctnQnty!="")
		 {
			 var ctn_per_qnty = parseInt( Number( exQnty/ctnQnty ) );
			 $('#txt_ctn_qnty').val(ctn_per_qnty);
		 }
	 }
  
	function fn_total(tableName,index) // for color and size level
	{
	 
		var filed_value = $("#colSize_"+tableName+index).val();
		// alert(filed_value);
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			 alert("Qnty Excceded by"+(placeholder_value-filed_value))	
				
				$("#colSize_"+tableName+index).val('');
				return;
			
		}
		
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_delivery_qty").val(totalVal);
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value)) 	
			$("#colSize_"+index).val('');
			return;
		}
		
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_delivery_qty").val( $("#total_color").val() );
	} 

	function SearchSystemId()
	{ 
		var data=document.getElementById('cbo_company_id').value;
		var page_link='requires/subcon_embellishment_delivery_controller.php?action=delivery_id_popup&data='+data
		var title='Subcontract Delivery';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=700px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_delivery_id");
			//alert (theemail.value);return;
			if (theemail.value!="")
			{
				//var ret_value=theemail.value.split("_");
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/subcon_embellishment_delivery_controller" );
				show_list_view(theemail.value,'delivery_list_view','delivery_list_view','requires/subcon_embellishment_delivery_controller','');
				setFilterGrid("details_table",-1);
				reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*sewing_production_variable*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				set_button_status(0, permission, 'fnc_embl_delivery',1,0);
				
				release_freezing();
			}
		}
	}
*/
	
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<?php  echo load_freeze_divs ("../../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
        <form name="embelDelv_1" id="embelDelv_1" autocomplete="off" >     
       			<fieldset style="width:840px;   margin-bottom:10px;"">
            	<legend>Embellishment Delivery</legend>                                    
                 <table width="100%">
                    <tr>
                        <td align="right" colspan="3"><strong>System ID</strong></td>
                        <td width="140" align="justify">
                            <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="SearchSystemId();" readonly >
                            <input type="hidden" name="txt_update_id" id="txt_update_id" />
                        </td>
                    </tr>
                     <tr>
                        <td width="100" class="must_entry_caption">Company </td>
                        <td width="140">
                            <?php
                                echo create_drop_down( "cbo_company_id", 152, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_embellishment_delivery_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );
								//auto_complete(this.value);load_drop_down( 'requires/subcon_embellishment_delivery_controller', this.value, 'load_drop_down_party_name', 'party_td' );	
                            ?>
                        </td>
                        <td width="100">Location</td>
                        <td width="140" id="location_td">
                             <?php
                           		echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );	
                             ?> 
                        </td>
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="party_td">
                        	 <input type="text" name="cbo_party_name" id="cbo_party_name" class="text_boxes party_name" onFocus="auto_complete($('#cbo_company_id').val(),'1')"   style="width:140px;" placeholder="Write" />
                             <input type="hidden" class="hdn_party_name" id="hidden_party_name" name="cbo_party_name" />
                             
                             <input type="hidden" name="cbo_party_id" id="cbo_party_id" class="text_boxes" style="width:140px;" />
                             
                            <?php
                            //echo create_drop_down( "cbo_party_name", 152,  $blank_array,"", 1, "-- Select Party --", $selected, "",'' ); 
                            ?>
                        </td>
                     </tr>
                    <tr>
                        <td>Challan No</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes_numeric" style="width:140px;" placeholder="Write Or Auto Create" />
                        </td>
                        <td class="must_entry_caption">Delivery Date</td>
                        <td>
                            <input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" placeholder="Date" readonly />
                        </td>
                       <td>Transport Com.</td>
                        <td>
                            <input type="text" name="txt_transport_company" id="txt_transport_company" onFocus="auto_complete($('#cbo_company_id').val(),'2')" class="text_boxes" placeholder="Write" style="width:140px;" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="3" style="text-align:right;">Order No</td>
                        <td colspan="3">
                            <input name="txt_order_no" id="txt_order_no"  placeholder="Double Click" onDblClick="SearchOroderNo()" class="text_boxes" style="width:140px " readonly />
                            
                            <input type="hidden" name="txtPoId"  id="txtPoId" value="" class=""/>
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row"  value="">
                            <input type="hidden" name="hiddenBatchAgainst" id="hiddenBatchAgainst"  tabindex="" />
                            
                            <!--<input type="hidden" id="hidden_po_break_down_id" />
                            <input type="hidden" name="txt_dtls_id" id="txt_dtls_id" readonly >-->
                            
                            
                        </td>
                        
                    </tr>
                 </table>
               </fieldset>
                <fieldset style="width:840px;" >
                    <legend>Embellishment Delivery Information</legend>
                    <table cellpadding="0" cellspacing="0" width="" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                        <thead>
                            <th>SL</th>
                            <th>Embellishment Type</th>
                            <th>Batch Number</th>
                            <th>Batch Color</th>
                            
                            <th class="">Gmts Item</th>
                            <th class="">Process</th>
                            <th>QC Pass Qty-Pcs</th>
                            
                            <th>Previous Delv </th>
                            <th>Balance</th>
                            <th class="must_entry_caption" >Current Delv</th>
                            <th>Cust. Buyer</th>
                            <th>Cust. Style</th>
                            <th>Operator</th>
                            <th>Shift</th>
                        </thead>
                        <tbody id="embellishment_details_container">
                            <tr class="general" id="tr_1" name="tr[]" >
                            	<td>
                                   <input type="text" name="txtSl[]"  id="txtSl_1" value="1" class="text_boxes_numeric" style="text-align:center; width:30px" disabled />
                                   <input type="hidden" name="emblProdDtlsId[]"  id="emblProdDtlsId_1" value="" style="width:50px" />
                                   <input type="hidden" name="txtOrderId[]"  id="txtOrderId_1" value="" class="" style="width:50px" />
                                   <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_1" value="" class="" style="width:50px" />
                                </td>
                                <td>
                                    <input type="text" name="EmblType[]"  id="EmblType_1" value="" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                                    <input type="hidden" name="txtEmblTypeId[]"  id="txtEmblTypeId_1" value="" class="text_boxes_numeric" style="width:50px" disabled />
                                </td>
                                <td>
                                     <input type="text" name="txtBatchNo[]"  id="txtBatchNo_1"  value="" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                                     <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_1" value="" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                                </td>
                                <td>
                                   <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_1" value="" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                                    <input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_1" value="" style="width:30px" disabled />
                                </td>
                                <td id="">
                                    <input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_1" value="" class="text_boxes"  style="width:80px" readonly disabled />
                                    <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_1" value="" class="text_boxes"  style="width:50px" />
                                </td>
                                <td>
                                     <input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:80px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup('');" title="Bbl. Click" />
                                    <input type="hidden" name="txtProcessId[]" id="txtProcessId_1" value="" />
                                </td>
                                
                                <td>
                                   <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_1" value="" class="text_boxes_numeric"  style="width:50px" readonly disabled />
                                </td>
                                <td>
                                    <input type="text" name="txtPreDelivQty[]"  id="txtPreDelivQty_1" class="text_boxes_numeric" style="width:50px"   placeholder="Display"  />
                                </td>
                                <td>
                                    <input type="text" name="txtBalance[]"  id="txtBalance_1" class="text_boxes_numeric"  style="width:50px" placeholder="Display" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txtCurrentDelivery[]"  id="txtCurrentDelivery_1" value="" class="text_boxes_numeric"    placeholder="Write"  style="width:50px" />
                                    <input type="hidden" name="hiddenCurrentDelivery[]"  id="hiddenCurrentDelivery_1" value="" class="text_boxes_numeric"   placeholder="Write"  style="width:50px" />
                                </td>
                                <td>
                                    <input type="text" name="txtCustBuyer[]"  id="txtCustBuyer_1" value="" class="text_boxes" style="width:80px" placeholder="Display" disabled  />
                                    
                                </td>
                                <td>
                                    <input type="text" name="txtStyle[]"  id="txtStyle_1" value="" class="text_boxes" style="width:80px"   placeholder="Display" disabled  />
                                </td>
                                <td>
                                <input type="text" name="txtOperatorName[]" id="txtOperatorName_1" value="" class="text_boxes" style="width:100px;"   placeholder="Display"  disabled  tabindex="4" />
                                <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_1" value="" style="width:60px"/>
                    
                                </td>
                                 <td>
                                <?
									echo create_drop_down( "cboShift_1", 80, $shift_name,"", 1, '- Select -', 0,"",'1','','','','','','','cboShift[]');
                                ?>
                            </td>
                            </tr>
                        </tbody>
                    </table>
            	 </fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                	
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <?php 
                                echo load_submit_buttons( $permission, "fnc_embl_delivery", 0,0,"reset_form('embelDelv_1','delivery_list_view','','','childFormReset()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        </td>
                    </tr> 
                </table>
               
           
        </form>
         <div style="width:830px; margin-top:5px;" id="delivery_list_view" align="center"></div>
    </div>  
</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>