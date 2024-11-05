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
		page_link='requires/embellishment_order_entry_controller.php?action=job_popup&data='+data;
		title='Job No Pop-up';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[1], "load_php_data_to_form", "requires/embellishment_order_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/embellishment_order_entry_controller','setFilterGrid("list_view",-1)');
				show_list_view(2+'_'+ex_data[1]+'_'+within_group+'_'+$("#update_id").val(),'order_dtls_list_view','emb_details_container','requires/embellishment_order_entry_controller','setFilterGrid(\'list_view\',-1)');
				
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

		/*for (var j=1; j<=row_num; j++)
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
			//alert($('#hdnDtlsdata_'+j).val());
			data_all+="&txtbuyerPoId_" + i + "='" + $('#txtbuyerPoId_'+j).val()+"'"+"&txtbuyerPo_" + i + "='" + $('#txtbuyerPo_'+j).val()+"'"+"&txtstyleRef_" + i + "='" + $('#txtstyleRef_'+j).val()+"'"+"&txtbuyer_" + i + "='" + $('#txtbuyer_'+j).val()+"'"+"&cboGmtsItem_" + i + "='" + $('#cboGmtsItem_'+j).val()+"'"+"&cboProcessName_" + i + "='" + $('#cboProcessName_'+j).val()+"'"+"&cboembtype_" + i + "='" + $('#cboembtype_'+j).val()+"'"+"&cboBodyPart_" + i + "='" + $('#cboBodyPart_'+j).val()+"'"+"&txtOrderQuantity_" + i + "='" + $('#txtOrderQuantity_'+j).val()+"'"+"&cboUom_" + i + "='" + $('#cboUom_'+j).val()+"'"+"&txtRate_" + i + "='" + $('#txtRate_'+j).val()+"'"+"&txtAmount_" + i + "='" + $('#txtAmount_'+j).val()+"'"+"&txtSmv_" + i + "='" + $('#txtSmv_'+j).val()+"'"+"&txtOrderDeliveryDate_" + i + "='" + $('#txtOrderDeliveryDate_'+j).val()+"'"+"&txtWastage_" + i + "='" + $('#txtWastage_'+j).val()+"'"+"&hdnDtlsdata_" + i + "='" + $('#hdnDtlsdata_'+j).val()+"'"+"&hdnDtlsUpdateId_" + i + "='" + $('#hdnDtlsUpdateId_'+j).val()+"'"+"&hdnbookingDtlsId_" + i + "='" + $('#hdnbookingDtlsId_'+j).val()+"'";
		}*/

		var j=0; var check_field=0; data_all="";
		$("#tbl_dtls_emb tbody tr").each(function()
		{
			var txtbuyerPoId 		= $(this).find('input[name="txtbuyerPoId[]"]').val();
			var txtbuyerPo 			= $(this).find('input[name="txtbuyerPo[]"]').val();
			var txtstyleRef 		= $(this).find('input[name="txtstyleRef[]"]').val();
			if(cbo_within_group==1)
			{
				var txtbuyer 		= $(this).find('select[name="txtbuyer[]"]').val();
			}
			else
			{
				var txtbuyer 		= $(this).find('input[name="txtbuyer[]"]').val();
			}
			var cboGmtsItem 		= $(this).find('select[name="cboGmtsItem[]"]').val();
			var cboProcessName 		= $(this).find('select[name="cboProcessName[]"]').val();
			var cboembtype 			= $(this).find('select[name="cboembtype[]"]').val();
			var cboBodyPart 		= $(this).find('select[name="cboBodyPart[]"]').val();
			var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
			var txtOrderQuantity 	= $(this).find('input[name="txtOrderQuantity[]"]').val();
			var txtRate 			= $(this).find('input[name="txtRate[]"]').val();
			var txtAmount 			= $(this).find('input[name="txtAmount[]"]').val();
			var txtSmv 				= $(this).find('input[name="txtSmv[]"]').val();
			var txtOrderDeliveryDate 	= $(this).find('input[name="txtOrderDeliveryDate[]"]').val();
			var txtWastage 			= $(this).find('input[name="txtWastage[]"]').val();
			var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
			var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
			var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
			var txtDelBreakId 		= $(this).find('input[name="txtDelBreakId[]"]').val();
			//var txtIsWithOrder 		= $(this).find('input[name="txtIsWithOrder[]"]').val();
			//txt_total_amount 		+= $(this).find('input[name="amount[]"]').val()*1;
			//alert(cboSection);
			j++;
			
			if(cboGmtsItem==0 || cboProcessName==0 || cboembtype==0 ||  cboUom==0 || txtOrderQuantity==''|| txtRate==0 || txtRate=='' ||  txtAmount==''|| txtAmount==0 || txtOrderDeliveryDate=='')
			{	 				
				if(cboGmtsItem==0)
				{
					alert('Please Select Gmts. Item');
					check_field=1 ; return;
				}
				else if(cboProcessName==0)
				{
					alert('Please Select Process /Embl. Name');
					check_field=1 ; return;
				}
				else if(cboembtype==0)
				{
					alert('Please Select Embl. Type');
					check_field=1 ; return;
				}
				else if(txtOrderQuantity=='')
				{
					alert('Please Fill up Order Qty ');
					check_field=1 ; return;
				}
				else if(cboUom==0)
				{
					alert('Please Select Order UOM ');
					check_field=1 ; return;
				}
				else if(txtRate=='' || txtRate==0)
				{
					alert('Please Fill up Rate ');
					check_field=1 ; return;
				}
				else if(txtAmount=='' || txtAmount==0)
				{
					alert('Please Fill up Amount');
					check_field=1 ; return;
				}
				else
				{
					alert('Please Select Delivery Date');
					check_field=1 ; return;
				}
				return;
			}

			i++;
			data_all += "&txtbuyerPoId_" + j + "='" + txtbuyerPoId + "'&txtbuyerPo_" + j + "='" + txtbuyerPo + "'&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtbuyer_" + j + "='" + txtbuyer + "'&cboGmtsItem_" + j + "='" + cboGmtsItem  + "'&cboProcessName_" + j + "='" + cboProcessName + "'&cboembtype_" + j + "='" + cboembtype + "'&cboBodyPart_" + j + "='" + cboBodyPart + "'&txtOrderQuantity_" + j + "='" + txtOrderQuantity + "'&cboUom_" + j + "='" + cboUom + "'&txtSmv_" + j + "='" + txtSmv + "'&txtRate_" + j + "='" + txtRate + "'&txtAmount_" + j + "='" + txtAmount +"'&txtWastage_" + j + "='" + txtWastage +"'&txtOrderDeliveryDate_" + j + "='" + txtOrderDeliveryDate +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId +"'&txtDelBreakId_" + j + "='" + txtDelBreakId + "'";
		});

		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+data_all+'&total_row='+i+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_rec_start_date*txt_rec_end_date*txt_order_no*hid_order_id*update_id*txt_deleted_id_dtls',"../../");
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/embellishment_order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_order_entry_response;
		}
		else
		{
			return;
		}	
	}

	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if(trim(response[0])=='emblRec'){
				alert("Receive Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			 
			 if(trim(response[0])=='emblRecipe'){
				alert("Recipe Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				
				
				var within_group = $('#cbo_within_group').val();
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				
				show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2],'order_dtls_list_view','emb_details_container','requires/embellishment_order_entry_controller','setFilterGrid(\'list_view\',-1)');
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
		load_drop_down( 'requires/embellishment_order_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
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
			load_drop_down( 'requires/embellishment_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			$("#cboUom_1").val(2);
			$('#cboUom_1').attr('disabled',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$("#txtbuyerPo_1").val("");

			$('#txtstyleRef_1').attr('readonly',true);
			$('#txtbuyer_1').attr('readonly',true);
			$('#txtstyleRef_1').attr('placeholder','Display');
			$('#txtbuyer_1').attr('placeholder','Display');
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/embellishment_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			$('#cboUom_1').attr('disabled',false);

			$('#txtstyleRef_1').attr('readonly',false);
			$('#txtbuyer_1').attr('readonly',false);
			$('#txtstyleRef_1').attr('placeholder','Write');
			$('#txtbuyer_1').attr('placeholder','Write');
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			
			$('#txtbuyerPo_1').attr('readonly',false);
			$('#txtbuyerPo_1').attr('placeholder','Write');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/embellishment_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
			$('#cboUom_1').attr('disabled',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$("#txtbuyerPo_1").val("");
			//$('#txtstyleRef_1').attr('readonly',true);
			//$('#txtbuyer_1').attr('readonly',true);
		} 
		
				if(within_group==2)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
				}
				else if(within_group==1)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
					
				}
	}

	function openmypage_order()
	{
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company*Within Group*Party')==false )
		{
			return;
		}
		else
		{
			var title = 'Order No. Pop-up';
			var page_link = 'requires/embellishment_order_entry_controller.php?company='+company+'&party_name='+party_name+'&action=order_popup';
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
					//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/embellishment_order_entry_controller" );
					show_list_view(1+'_'+ex_data[1]+'_'+1,'order_dtls_list_view','emb_details_container','requires/embellishment_order_entry_controller','setFilterGrid(\'list_view\',-1)');
					release_freezing();
				}
			}
		}
	}

	function openmypage_order_qnty(type,booking_dtls_id,row)
	{
		var company 	= $('#cbo_company_name').val();
		var job_no 	= $('#txt_job_no').val();
		var party_name 	= $('#cbo_party_name').val();
		var order_mst_id = $('#hid_order_id').val();
		var order_no 	= $('#txt_order_no').val();
		
		var within_group = $('#cbo_within_group').val();
		var process_id = $('#cboProcessName_'+row).val();
		
		var booking_po_id = $('#txtbuyerPoId_'+row).val();
		
		var data_break=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		
		var page_link = 'requires/embellishment_order_entry_controller.php?within_group='+within_group+'&booking_dtls_id='+booking_dtls_id+'&order_no='+order_no+'&process_id='+process_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&job_no='+job_no+'&action=order_qty_popup';
		
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
			$('#txtDelBreakId_'+row).val(break_delete_id.value);
			
			$('#txtOrderQuantity_'+row).val(receive_total_order_qnty.value);
			$('#txtRate_'+row).val(receive_average_rate.value);
			$('#txtAmount_'+row).val(receive_total_order_amount.value);
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

	function fnc_addRow( i, table_id, tr_id )
	{ 
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			return;
		}
		else
		{
			var prefix=tr_id.substr(0, tr_id.length-1);
			var row_num = $('#tbl_dtls_emb tbody tr').length; 
			//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
			row_num++;
			var cboGmtsItem= $("#"+'cboGmtsItem_'+i).val();
			var cboProcessName= $("#"+'cboProcessName_'+i).val();
			var cboProcessName= $("#"+'cboProcessName_'+i).val();
			var cboUom= $("#"+'cboUom_'+i).val();
			var clone= $("#"+tr_id+i).clone();
			clone.attr({
				id: tr_id + row_num,
			});
			
			clone.find("input,select").each(function(){

				$(this).attr({ 
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					//'name': function(_, name) { var name=name.split("_"); return name[0] },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }
				});
			}).end();
			$("#"+tr_id+i).after(clone);
			//$('#rate_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+row_num+")");

			$('#cboGmtsItem_'+row_num).removeAttr("value").attr("value",cboGmtsItem);
			$('#cboProcessName_'+row_num).removeAttr("value").attr("value",cboProcessName);
			$('#cboUom_'+row_num).removeAttr("value").attr("value",cboUom);
			$('#txtbuyerPoId_'+row_num).removeAttr("value").attr("value",'');
			$('#txtbuyerPo_'+row_num).removeAttr("value").attr("value",'');
			$('#txtstyleRef_'+row_num).removeAttr("value").attr("value",'');
			$('#txtbuyer_'+row_num).removeAttr("value").attr("value",'');
			$('#cboembtype_'+row_num).removeAttr("value").attr("value",0);
			$('#cboBodyPart_'+row_num).removeAttr("value").attr("value",0);
			$('#txtOrderQuantity_'+row_num).removeAttr("value").attr("value",'');
			$('#txtRate_'+row_num).removeAttr("value").attr("value",'');
			$('#txtAmount_'+row_num).removeAttr("value").attr("value",'');
			$('#txtSmv_'+row_num).removeAttr("value").attr("value",'');
			$('#txtOrderDeliveryDate_'+row_num).removeAttr("value").attr("value",'');
			$('#txtWastage_'+row_num).removeAttr("value").attr("value",'');
			$('#hdnDtlsdata_'+row_num).removeAttr("value").attr("value",'');
			$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value",'');
			$('#hdnbookingDtlsId_'+row_num).removeAttr("value").attr("value",'');
			$('#txtDelBreakId_'+row_num).removeAttr("value").attr("value",'');
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#cboembtype_'+row_num).removeAttr("disabled").attr('disabled',false);
			$('#cboBodyPart_'+row_num).removeAttr("disabled").attr('disabled',false);

			$('#txtOrderQuantity_'+row_num).removeAttr("onClick").attr("onClick","openmypage_order_qnty("+1+","+'0'+","+row_num+")");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#txtOrderDeliveryDate_'+row_num).removeAttr("class").attr("class","datepicker");
			//add_auto_complete(row_num);
			//fnc_comm_basis();
			set_all_onclick();
		}
	}

	function fnc_deleteRow(rowNo,table_id,tr_id) 
	{ 
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			return;
		}
		else
		{
			var numRow = $('#'+table_id+' tbody tr').length; 
			var prefix=tr_id.substr(0, tr_id.length-1);
			var total_row=$('#'+prefix+'_tot_row').val();
			
			var numRow = $('table#tbl_dtls_emb tbody tr').length; 
			if(numRow!=1)
			{
				var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
				var txt_deleted_id_dtls=$('#txt_deleted_id_dtls').val();
				var selected_id='';
				
				if(updateIdDtls!='')
				{
					if(txt_deleted_id_dtls=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id_dtls+','+updateIdDtls;
					$('#txt_deleted_id_dtls').val( selected_id );
				}
				
				$("#"+tr_id+rowNo).remove();
				$('#'+prefix+'_tot_row').val(total_row-1);
				//calculate_total_amount(1);
			}
			else
			{
				return false;
			}
		}
	}
	
function fnc_load_uom(type,uom)
	{
		var cbo_within_group=$('#cbo_within_group').val();
		if(uom==1)
		{
			$('#order_uom_td').text('Rate/Pcs');
			$('#order_uom_td').css('color','blue');
		}
		if(uom==2)
		{
			$('#order_uom_td').text('Rate/Dzn');
			$('#order_uom_td').css('color','blue');
		}
		
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:850px;">
			<legend>Embellishment Order Entry</legend>
                <table width="830" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job No</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input type="hidden" name="txt_deleted_id_dtls" id="txt_deleted_id_dtls" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
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
                        <td><input type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" class="datepicker" value="<? echo date("d-m-Y")?>" /></td>
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" /></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"", 1,"" ); ?></td>
                        <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. Start Date" /></td>
                    </tr>
                    <tr>
                    	<td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date" /></td>
                        <td class="must_entry_caption" align="right"><strong>Work Order</strong></td>
                        <td colspan="3"><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_order();" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                        </td>
                    </tr> 
                </table>
        </fieldset> 
        <fieldset style="width:1210px;">
           <legend>Embellishment Order Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb" width="1210">
                    <thead class="form_table_header">
                        <th width="110" id="buyerpo_td">Buyer PO</th>
                        <th width="110" id="buyerstyle_td">Buyer Style Ref.</th>
                        <th width="110" id="buyerbuyer_td">Buyer's Buyer </th>
                        <th width="90" class="must_entry_caption">Gmts. Item</th>
                        <th width="80" class="must_entry_caption">Process /Embl. Name</th>
                        <th width="80" class="must_entry_caption">Embl. Type</th>
                        <th width="80">Body Part</th>
                        <th width="70" class="must_entry_caption">Order Qty</th>
                        <th width="60" class="must_entry_caption">Order UOM</th>
                        <th width="70" class="must_entry_caption" id="order_uom_td">Rate/Dzn</th>
                        <th width="80" class="must_entry_caption">Amount</th>
                        <th width="50">SMV</th>
                        <th width="60" class="must_entry_caption">Delivery Date</th>
                        <th width="60">Wastage %</th>
                        <th></th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                            <td><input name="txtbuyerPo[]" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly />
                            	<input name="txtbuyerPoId[]" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                            </td>
                            <td><input name="txtstyleRef[]" id="txtstyleRef_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly /></td>
                            <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
                            <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,'','','','','','',"cboGmtsItem[]"); ?>	</td>
                            <td><? echo create_drop_down( "cboProcessName_1", 80, $emblishment_name_array,"", 1, "--Select--",0,"change_caption_n_uom(1,this.value);", 1,1,'','','','','',"cboProcessName[]"); ?>	</td>
                            <td id="embltype_td_1"><? echo create_drop_down( "cboembtype_1", 80, $blank_array,"", 1, "-- Select --",$selected, "",0,'','','','','','',"cboembtype[]"); ?>	</td>
                            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "-- Select --",$selected, "",0,'','','','','','',"cboBodyPart[]"); ?>	</td>
                            <td><input name="txtOrderQuantity[]" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
                            <td><?
							echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"",1, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1",'','','','','',"cboUom[]" );
							// echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]");
							 ?>	</td>
                            <td><input name="txtRate[]" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:60px" /></td>
                            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td><input name="txtSmv[]" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
                            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" /></td>
                            <td>
                                <input name="txtWastage[]" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:47px" />
                                <input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="txtDelBreakId[]" id="txtDelBreakId_1">
                            </td>
                            <td width="65">
							<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
							<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
							</td>
                        </tr>                     
                    </tbody>
                </table>
            
                <table width="1210" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" valign="middle" class="button_container">
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