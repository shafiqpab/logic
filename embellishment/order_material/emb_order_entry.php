<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	22-09-2018
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Order Info", "../../", 1,1, $unicode,1,'');

?>
<script> 
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/emb_order_entry_controller.php?action=job_popup&data='+data;
		title='Embellishment Order Entry';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[0], "load_php_data_to_form", "requires/emb_order_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/emb_order_entry_controller','setFilterGrid("list_view",-1)');
				show_list_view(2+'_'+ex_data[1]+'_'+within_group+'_'+$("#update_id").val(),'order_dtls_list_view','emb_details_container','requires/emb_order_entry_controller','setFilterGrid(\'list_view\',-1)');	
				if(within_group==2)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
				}
				else if(within_group==1)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
					//$('#txtOrderQuantity_1').attr('disabled',true);
				}	
				
				$('#txt_exchange_rate').val(ex_data[2]);	
				calculate_total();	
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

	function fnc_job_order_entry( operation )
	{
		var delete_master_info=0;
		//var process = $("#cbo_process_name").val();
		var cbo_within_group = $("#cbo_within_group").val();
		if(cbo_within_group==1)
		{	
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*cbo_party_location','Company*Within Group*Party*Currency*Order No*Order Receive Date*Order Delivery Date*Order No.*Party Location')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no','Company*Within Group*Party*Currency*Order Receive Date*Order Delivery Date*Order No.')==false )
			{
				return;
			}
		}
		if(operation==0)
		{
			var r=confirm("Are you sure?");	
			if(r==true)
			{
			}
			else
			{
				release_freezing();	
				return;
			}	
		}
		var row_num=$('#tbl_dtls_emb tbody tr').length;
		var data_all=""; var i=0; var selected_row=0;
		var data_delete="";  var a=0;

		for (var j=1; j<=row_num; j++)
		{
			//var updateIdDtls=$('#updateIdDtls_'+j).val();  
			//alert(updateIdDtls);
			if(cbo_within_group==1)
			{
				if (form_validation('cboProcessName_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*txtRate_'+j+'*txtOrderDeliveryDate_'+j,'Process/Embl. Name*Order Qty*Order UOM*Rate/Unit*Delivery Date')==false)
				{
					return;
				}
			}
			else
			{
				if (form_validation('cboProcessName_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*txtRate_'+j+'*txtOrderDeliveryDate_'+j+'*cboGmtsItem_'+j+'*cboembtype_'+j,'Process/Embl. Name*Order Qty*Order UOM*Rate/Unit*Delivery Date*Garments Item*Embellishment Type')==false)
				{
					return;
				}
			}
			i++;
			
			data_all+="&txtbuyerPoId_" + i + "='" + $('#txtbuyerPoId_'+j).val()+"'"+"&cboGmtsItem_" + i + "='" + $('#cboGmtsItem_'+j).val()+"'"+"&txtbuyerPo_" + i + "='" + $('#txtbuyerPo_'+j).val()+"'"+"&txtstyleRef_" + i + "='" + $('#txtstyleRef_'+j).val()+"'"+"&cboProcessName_" + i + "='" + $('#cboProcessName_'+j).val()+"'"+"&cboembtype_" + i + "='" + $('#cboembtype_'+j).val()+"'"+"&cboBodyPart_" + i + "='" + $('#cboBodyPart_'+j).val()+"'"+"&txtOrderQuantity_" + i + "='" + $('#txtOrderQuantity_'+j).val()+"'"+"&cboUom_" + i + "='" + $('#cboUom_'+j).val()+"'"+"&txtRate_" + i + "='" + $('#txtRate_'+j).val()+"'"+"&txtAmount_" + i + "='" + $('#txtAmount_'+j).val()+"'"+"&txtSmv_" + i + "='" + $('#txtSmv_'+j).val()+"'"+"&txtOrderDeliveryDate_" + i + "='" + $('#txtOrderDeliveryDate_'+j).val()+"'"+"&txtWastage_" + i + "='" + $('#txtWastage_'+j).val()+"'"+"&hdnDtlsdata_" + i + "='" + $('#hdnDtlsdata_'+j).val()+"'"+"&hdnDtlsUpdateId_" + i + "='" + $('#hdnDtlsUpdateId_'+j).val()+"'"+"&hdnbookingDtlsId_" + i + "='" + $('#hdnbookingDtlsId_'+j).val()+"'"+"&txtdomisticamount_" + i + "='" + $('#txtdomisticamount_'+j).val()+"'"+"&txtbuyer_" + i + "='" + $('#txtbuyer_'+j).val()+"'";
		}
		var data="action=save_update_delete&operation="+operation+data_all+'&total_row='+i+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_rec_start_date*txt_rec_end_date*txt_order_no*hid_order_id*update_id*txt_exchange_rate',"../../");
		//alert (data); //return;
		freeze_window(operation);
		http.open("POST","requires/emb_order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_job_order_entry_response;
	}

	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if(trim(response[0])=='emblRec'){
				alert("Receive Found :"+trim(response[2])+"\n So Delete Not Possible")
				release_freezing();
				return;
			}
			else if(trim(response[0])=='emblBill'){
				alert("Bill Found :"+"\n Rate Update Not Possible")
				release_freezing();
				return;
			}
			else if(trim(response[0])=='emblRecQty'){
				alert("Receive Found :"+trim(response[2])+"\n Order Quantity Cannot Be Less Than Received Quantity")
				release_freezing();
				return;
			}
			 
			/* if(trim(response[0])=='emblRecipe'){
				alert("Recipe Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }*/
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				
				
				var within_group = $('#cbo_within_group').val();
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				
				show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2],'order_dtls_list_view','emb_details_container','requires/emb_order_entry_controller','setFilterGrid(\'list_view\',-1)');
				calculate_total();
				set_button_status(1, permission, 'fnc_job_order_entry',1);

			}
			else if(response[0]==2)
			{
				location.reload();
			}
			show_msg(response[0]);
			release_freezing();
		}
	}

	function change_caption_n_uom(inc,process)
	{
		if(process == 2 || process == 3 || process == 4)
		{
			//$("#cbo_uom").val(12);
		}else{
			//$("#cbo_uom").val(2);
		}
		$('#cboUom_'+inc).attr('disabled',true);
		load_drop_down( 'requires/emb_order_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
	}

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/emb_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
			$('#txtbuyer_1').attr('readonly',true);
			$('#txtbuyer_1').attr('placeholder','Display');
			
			$("#cboUom_1").val(2);
			$('#cboUom_1').attr('disabled',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtstyleRef_1').attr('readonly',true);
			
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$('#txtstyleRef_1').attr('placeholder','Display');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/emb_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			load_drop_down( 'requires/emb_order_entry_controller', company+'_'+2, 'load_drop_down_embroidery', 'embltype_td_1' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			
			$('#txtbuyer_1').attr('readonly',false);
			$('#txtbuyer_1').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			$('#txtbuyerPo_1').attr('placeholder','Write');
			$('#txtstyleRef_1').attr('placeholder','Write');
			$('#txtbuyerPo_1').attr('readonly',false);
			$('#txtstyleRef_1').attr('readonly',false)
			$('#cboUom_1').attr('disabled',false);
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/emb_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
		} 
		
				if(within_group==2)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
					$('#cbo_currency').attr('disabled',false);
					$('#cbo_currency').val(0);
				}
				else if(within_group==1)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
					$('#cbo_currency').attr('disabled',true);
					
				}
	}

	function openmypage_order()
	{
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var cbo_within_group = $('#cbo_within_group').val();
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_exchange_rate','Company*Within Group*Party*exchange rate')==false )
		{
			return;
		}
		else
		{
			var title = 'Order No. Pop-up';
			var page_link = 'requires/emb_order_entry_controller.php?company='+company+'&party_name='+party_name+'&cbo_within_group='+cbo_within_group+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1070px,height=420px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					
					$('#txt_order_no').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					$('#cbo_currency').val(ex_data[2]);
					
					$('#cbo_company_name').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);
					$('#cbo_party_name').attr('disabled',true);
					$('#cbo_currency').attr('disabled',true);
					//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/emb_order_entry_controller" );
					exchange_rate(ex_data[2]);
					show_list_view(1+'_'+ex_data[1]+'_'+1+'_'+$('#txt_exchange_rate').val(),'order_dtls_list_view','emb_details_container','requires/emb_order_entry_controller','setFilterGrid(\'list_view\',-1)');
					calculate_total();
					
					release_freezing();
				}
			}
		}
	}

	function openmypage_order_qnty(type,booking_dtls_id,row)
	{
		
		if ( form_validation('txt_exchange_rate','Exchange Rate')==false )
		{
			return;
		}
		var company 	= $('#cbo_company_name').val();
		var job_no 	= $('#txt_job_no').val();
		var party_name 	= $('#cbo_party_name').val();
		var order_mst_id = $('#hid_order_id').val();
		var order_no 	= $('#txt_order_no').val();
		var exchange_rate 	= $('#txt_exchange_rate').val();
		
		var within_group = $('#cbo_within_group').val();
		var process_id = $('#cboProcessName_'+row).val();
		
		var booking_po_id = $('#txtbuyerPoId_'+row).val();
		
		var data_break=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		
		var page_link = 'requires/emb_order_entry_controller.php?within_group='+within_group+'&booking_dtls_id='+booking_dtls_id+'&order_no='+order_no+'&process_id='+process_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&job_no='+job_no+'&exchange_rate='+exchange_rate+'&action=order_qty_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Popup', 'width=670px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
			var receive_total_order_qnty=this.contentDoc.getElementById("txt_total_order_qnty");
			var receive_average_rate=this.contentDoc.getElementById("txt_average_rate");
			var receive_total_order_amount=this.contentDoc.getElementById("txt_total_order_amount"); 
			$('#hdnDtlsdata_'+row).val(break_data.value);
			$('#txt_deleted_id'+row).val(break_delete_id.value);
			$('#txtOrderQuantity_'+row).val(receive_total_order_qnty.value);
			$('#txtRate_'+row).val(receive_average_rate.value);
			$('#txtAmount_'+row).val(receive_total_order_amount.value);
			//var currency 	= $('#cbo_currency').val();
			//exchange_rate(currency);
			var exchange_rate = $('#txt_exchange_rate').val();
			var amount=receive_total_order_amount.value;
			var domisticamount=amount*exchange_rate;
			$("#txtdomisticamount_"+row).val(domisticamount);
			var cboUom = $('#cboUom_'+row).val();
			if(cboUom==2){
				var qty_pcs=(receive_total_order_qnty.value*1)*12;
			}else if(cboUom==1){
				var qty_pcs=receive_total_order_qnty.value*1;
			}else{
				var qty_pcs=0;
			}
			$("#txtQtyPcs_"+row).val(qty_pcs);
			$('#cbo_currency').attr('disabled',true);
			calculate_total();
			
		}		
	}
	
	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_job_order_entry', 1);
		//reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1*2',"disable_enable_fields('txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
		reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#txt_order_no').attr('disabled',false);
		
		$('#cboGmtsItem_1').attr('disabled',false);
		$('#cboProcessName_1').attr('disabled',false);
		$('#cboembtype_1').attr('disabled',false);
		$('#cboBodyPart_1').attr('disabled',false);
    }
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	function fnc_load_uom(type,uom)
	{
		
		if (form_validation('cbo_currency','Currency')==false)
		{
			return;
		}
		var cbo_within_group=$('#cbo_within_group').val();
		var orderQuantity=0;
		if(uom==1)
		{
			$('#order_uom_td').text('Rate/Pcs');
			$('#order_uom_td').css('color','blue');

			$("#tbl_dtls_emb tbody tr").each(function()
			{
				orderQuantity 	= $(this).find('input[name="txtOrderQuantity[]"]').val()*1;
				$(this).find('input[name="txtQtyPcs[]"]').val(orderQuantity);
				
			});
		}
		if(uom==2)
		{
			$('#order_uom_td').text('Rate/Dzn');
			$('#order_uom_td').css('color','blue');
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				orderQuantity 	= ( $(this).find('input[name="txtOrderQuantity[]"]').val()*1*12);
				$(this).find('input[name="txtQtyPcs[]"]').val(orderQuantity);
				
			});
		}
		$('#cbo_currency').attr('disabled',true);
		calculate_total();
	}
	
	function exchange_rate(val)
	{
		if(val==0)
		{
			$('#txt_order_receive_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			$('#txt_order_receive_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_order_receive_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/emb_order_entry_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_order_receive_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
	}
	function add_dtls_tr(i) 
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var row_num=$('#tbl_dtls_emb tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_dtls_emb tbody tr:last").clone().find("input,select").each(function() 
				{
					$(this).attr(
					{
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name },
						'value': function(_, value) { return value } 
					}); 
				}).end().appendTo("#tbl_dtls_emb tbody");
				$("#tbl_dtls_emb tbody tr:last").removeAttr('id').attr('id','row_'+i);
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_dtls_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fnc_delet_dtls_tr("+i+");");
				//$('#txtRate_' + i).removeAttr("onClick").attr("onClick", "openmypage_order_rate(1,0,"+i+");");
				$('#txtOrderQuantity_'+i).removeAttr("onClick").attr("onClick","openmypage_order_qnty("+1+","+'0'+","+i+")");
				
				$('#txtOrderDeliveryDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtOrderQuantity_' + i).val( '' );
				$('#txtRate_' + i).val( '' );
				$('#txtAmount_' + i).val( '' );
				/*$('#txtDomAmount_' + i).val( '' );*/
				$('#hdnDtlsUpdateId_' + i).val( '' );
				$('#hdnDtlsdata_' + i).val( '' );
				$('#hdnbookingDtlsId_' + i).val( '' );
				set_all_onclick();
			}
		}
	}
	function fnc_delet_dtls_tr(i)
	{ 
		var selected_delete_id = new Array();
		/*var templatedata=$('#txt_deleted_id_dtls').val();*/
		var within_group = $('#cbo_within_group').val();
		var details_update_id = $('#hdnDtlsUpdateId_'+i).val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var numRow = $('#tbl_dtls_emb tbody tr').length;
			if(numRow==i && i!=1)
			{
				if(details_update_id!='')
				{
					if(templatedata=='') templatedata=details_update_id; else templatedata=templatedata+','+details_update_id;
					/*$('#txt_deleted_id_dtls').val( templatedata );*/
				}				
				$('#tbl_dtls_emb tbody tr:last').remove();
			}			
		}
		/*
		for( var i = 0; i < selected_delete_id.length; i++ ) {
			templatedata += selected_delete_id[i] + ',';
		}
		templatedata = templatedata.substr( 0, templatedata.length - 1 );*/
		//$('#txt_deleted_id_dtls').val( templatedata );
	}

	function calculate_total()
	{
		var totOrderQuantity=totAmount=totdomisticamount=totQtyPcs=0;
		//math_operation( 'txtAmount_'+i, 'txtOrderQuantity_'+i+'*txtRate_'+i, '*','',ddd); 
		$("#tbl_dtls_emb tbody tr").each(function()
		{
			totOrderQuantity 	+= $(this).find('input[name="txtOrderQuantity[]"]').val()*1;
			
			totAmount 			+= $(this).find('input[name="txtAmount[]"]').val()*1;
			totdomisticamount 	+= $(this).find('input[name="txtdomisticamount[]"]').val()*1;
			totQtyPcs 			+= $(this).find('input[name="txtQtyPcs[]"]').val()*1;
		});
		$('#txtTotOrderQuantity').val(totOrderQuantity.toFixed(4));
		$('#txtTotAmount').val(totAmount.toFixed(4));
		$('#txtTotDomAmount').val(totdomisticamount.toFixed(4));
		$('#txtTotQtyPcs').val(totQtyPcs.toFixed(4));
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:1100px;">
			<legend>Embroidery Order Entry</legend>
                <table width="1100" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job No</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/emb_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);exchange_rate(document.getElementById('cbo_currency').value);"); ?>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Ord. Receive Date</td>
                        <td><input type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" class="datepicker" value="<? echo date("d-m-Y")?>" onChange="exchange_rate(document.getElementById('cbo_currency').value);" /></td>
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" /></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><?
						 echo create_drop_down("cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"exchange_rate(this.value)",1,"","","","");
						 ?></td>
                        <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. Start Date" /></td>
                    </tr>
                    <tr>
                    	<td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date" /></td>
                        <td class="must_entry_caption" align="right"><strong>Work Order</strong></td>
                        <td><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_order();" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                        </td>
                        <td class="must_entry_caption">Exchange Rate</td>
               			 <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric"  value=""  readonly/></td>
                    </tr> 
                </table>
        </fieldset> 
        <fieldset style="width:1400px;">
           <legend>Embroidery Order Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                        <th width="110" id="buyerpo_td">Buyer PO</th>
                        <th width="110" id="buyerstyle_td">Buyer Style Ref.</th>
                        <th width="110" id="buyerbuyer_td">Buyer's Buyer </th>
                        <th width="90" class="must_entry_caption">Gmts. Item</th>
                        <th width="80" class="must_entry_caption">Process /Emb Name</th>
                        <th width="80" class="must_entry_caption">Emb Type</th>
                        <th width="80">Body Part</th>
                        <th width="70" class="must_entry_caption">Order Qty</th>
                        <th width="60" class="must_entry_caption">Order UOM</th>
                        <th width="70" class="must_entry_caption" id="order_uom_td">Rate/Dzn</th>
                        <th width="80" class="must_entry_caption">Amount</th>
                        <th width="100">Domestic Amount</th>
                        <th width="80" class="must_entry_caption">Quantity (Pcs)</th>
                        <th width="50">SMV</th>
                        <th width="60" class="must_entry_caption">Delivery Date</th>
                        <th width="50">Wastage %</th>
                        <th></th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr>
                            <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly />
                            	<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                            </td>
                            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly /></td>
                            <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
                            <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cboProcessName_1", 80, $emblishment_name_array,"", 1, "--Select--",2,1,1 ); ?></td>
                            <td id="embltype_td_1"><? echo create_drop_down( "cboembtype_1", 80, $blank_array,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
                            <td><input name="txtOrderQuantity[]" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
                            <td><? //echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,"" );
							echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 0, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1" );
							
							 ?></td>
                            <td><input name="txtRate_1" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:60px" /></td>
                            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td align="center"><input type="text" name="txtdomisticamount[]" id="txtdomisticamount_1" class="text_boxes_numeric" style="width:90px;" readonly /></td>
                            <td><input name="txtQtyPcs[]" id="txtQtyPcs_1" type="text"  class="text_boxes_numeric" style="width:67px" readonly /></td> 
                            <td><input name="txtSmv_1" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
                            <td><input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" /></td>
                            <td>
                                <input name="txtWastage_1" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:40px" />
                                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata_1" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
                            </td>
                            <td width="65">
								<input type="button" id="increase_1" name="increase[]" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
							</td>
                        </tr>                     
                    </tbody>
                    <tfoot>
                    	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
		                    <td colspan="7" align="right">Total:</td>
		                    <td><input name="txtTotOrderQuantity[]" id="txtTotOrderQuantity" type="text"  class="text_boxes_numeric" style="width:60px"  readonly /></td>
		                    <td colspan="2">&nbsp;</td>
		                    <td><input name="txtTotAmount[]" id="txtTotAmount" type="text"  class="text_boxes_numeric" style="width:70px"  readonly /></td>
		                    <td><input name="txtTotDomAmount[]" id="txtTotDomAmount" type="text"  class="text_boxes_numeric" style="width:90px"  readonly /></td>
		                    <td><input name="txtTotQtyPcs[]" id="txtTotQtyPcs" type="text"  class="text_boxes_numeric" style="width:67px"  readonly /></td>
		                    <td colspan="4">&nbsp;</td>
		                </tr>
                    </tfoot>
                </table>
            
                <table width="1400" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="10" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>