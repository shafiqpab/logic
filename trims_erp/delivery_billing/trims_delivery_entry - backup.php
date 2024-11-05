<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	28-01-2018
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
echo load_html_head_contents("Trims Order Receive Info", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_devivery_workorder()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_delivery_entry_controller.php?action=devivery_workorder_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[1], "load_php_data_to_form", "requires/trims_delivery_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				var received_id =$("#received_id").val();
				//alert(received_id);
				show_list_view(1+'_'+ex_data[1]+'_'+within_group+'_'+received_id,'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(0, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

	function fnc_job_order_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title, "challan_print", "requires/trims_delivery_entry_controller") 
			//return;
			show_msg("3");
		}
		else
		{
			var delete_master_info=0; var i=0;
			//var process = $("#cbo_process_name").val();
			var cbo_within_group = $("#cbo_within_group").val();
			/*if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_delivery_date*txt_order_no*cbo_party_location','Company*Within Group*Party*Currency*Order No*Order Delivery Date*Order No.*Party Location')==false )
				{
					return;
				}*/
			
			var txt_dalivery_no 	= $('#txt_dalivery_no').val();
			var cbo_company_name 	= $('#cbo_company_name').val();
			var cbo_location_name 	= $('#cbo_location_name').val();
			var cbo_within_group 	= $('#cbo_within_group').val();
			var cbo_party_name 		= $('#cbo_party_name').val();
			var cbo_party_location 	= $('#cbo_party_location').val();
			var cbo_currency 		= $('#cbo_currency').val();
			var txt_challan_no 		= $('#txt_challan_no').val();
			var txt_delivery_date 	= $('#txt_delivery_date').val();
			var txt_gate_pass_no 	= $('#txt_gate_pass_no').val();
			var txt_order_no 		= $('#txt_order_no').val();
			var hid_order_id 		= $('#hid_order_id').val();
			var update_id 			= $('#update_id').val();
			var received_id 		= $('#received_id').val();
			var cboshipingStatus 	= $('#cboshipingStatus').val();
			var txt_remarks 		= $('#txt_remarks').val();
			//var txt_deleted_id 	= $('#txt_deleted_id').val();
			
			var j=0; var check_field=0; data_all="";
				
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				var txtWorkOrder 		= $(this).find('input[name="txtWorkOrder[]"]').val();
				var txtWorkOrderID 		= $(this).find('input[name="txtWorkOrderID[]"]').val();
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
				var cboSection 			= $(this).find('select[name="cboSection[]"]').val();
				var cboItemGroup 		= $(this).find('select[name="cboItemGroup[]"]').val();
				var txtOrderQuantity 	= $(this).find('input[name="txtOrderQuantity[]"]').val();
				var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
				var txtPrevQty 			= $(this).find('input[name="txtPrevQty[]"]').val();
				var txtCurQty 			= $(this).find('input[name="txtCurQty[]"]').val();
				var txtClaimQty 		= $(this).find('input[name="txtClaimQty[]"]').val();
				var txtRemarksDtls 		= $(this).find('input[name="txtRemarksDtls[]"]').val();
				
				var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
				var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
				var hdnReceiveDtlsId 	= $(this).find('input[name="hdnReceiveDtlsId[]"]').val();
				var hdnJobDtlsId 		= $(this).find('input[name="hdnJobDtlsId[]"]').val();
				var hdnProductionDtlsId = $(this).find('input[name="hdnProductionDtlsId[]"]').val();
				//var hdnBreakIDs 		= $(this).find('input[name="hdnBreakIDs[]"]').val();
				//txt_total_amount 	+= $(this).find('input[name="amount[]"]').val()*1;
				//alert(cboSection);
				j++;
				i++;
				data_all += "&txtbuyerPoId_" + j + "='" + txtbuyerPoId + "'&txtWorkOrder_" + j + "='" + txtWorkOrder + "'&txtWorkOrderID_" + j + "='" + txtWorkOrderID + "'&txtbuyerPo_" + j + "='" + txtbuyerPo + "'&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtbuyer_" + j + "='" + txtbuyer + "'&cboSection_" + j + "='" + cboSection + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&txtOrderQuantity_" + j + "='" + txtOrderQuantity + "'&cboUom_" + j + "='" + cboUom + "'&txtPrevQty_" + j + "='" + txtPrevQty + "'&txtCurQty_" + j + "='" + txtCurQty +"'&txtClaimQty_" + j + "='" + txtClaimQty +"'&txtRemarksDtls_" + j + "='" + txtRemarksDtls +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId +"'&hdnReceiveDtlsId_" + j + "='" + hdnReceiveDtlsId+"'&hdnJobDtlsId_" + j + "='" + hdnJobDtlsId+"'&hdnProductionDtlsId_" + j + "='" + hdnProductionDtlsId + "'";
			});	
		}

		var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_dalivery_no='+txt_dalivery_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_currency='+cbo_currency+'&txt_challan_no='+txt_challan_no+'&txt_delivery_date='+txt_delivery_date+'&txt_gate_pass_no='+txt_gate_pass_no+'&received_id='+received_id+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&update_id='+update_id+'&txt_deleted_id='+txt_deleted_id+'&cboshipingStatus='+cboshipingStatus+'&txt_remarks='+txt_remarks+data_all;
		
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","requires/trims_delivery_entry_controller.php",true);
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
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_dalivery_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				var within_group = $('#cbo_within_group').val();
				if(within_group==2)
				{
					document.getElementById('txt_order_no').value = response[3];
				}
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				//var received_id = $('#received_id').val();
				var company_name = $('#cbo_company_name').val();

				show_list_view(2+'_'+response[2]+'_'+within_group+'_'+company_name+'_'+response[1],'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');

				//show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2],'order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');
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
		load_drop_down( 'requires/trims_delivery_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
	}

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		//load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+1, 'load_drop_down_group', 'group_td' );
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			//$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			//$('#txt_order_no').attr('readonly',true);
			//$('#txt_order_no').attr('placeholder','Browse');
			
			//$("#cbo_party_location").val(0);
			//$('#cbo_party_location').attr('disabled',false);
			//$('#cbo_currency').attr('disabled',true);
			
			/*$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');*/
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			//$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			//$('#txt_order_no').attr('readonly',false);
			//$('#txt_order_no').attr('placeholder','Write');
			
			//$("#cbo_party_location").val(0); 
			//$('#cbo_party_location').attr('disabled',true);
			//$('#cbo_currency').attr('disabled',false);
			
			/*$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');*/
		}
		else if(within_group==1 && type==2)
		{
			//alert(party_name);
			load_drop_down( 'requires/trims_delivery_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			//$('#td_party_location').css('color','blue');
			//$('#cbo_currency').attr('disabled',true);
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
			var page_link = 'requires/trims_delivery_entry_controller.php?company='+company+'&party_name='+party_name+'&action=delivery_order_popup';
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
					fnc_exchange_rate();
					$('#cbo_company_name').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);
					$('#cbo_party_name').attr('disabled',true);
					$('#cbo_currency').attr('disabled',true);
					$('#txt_exchange_rate').attr('disabled',true);
					var exchange_rate = $('#txt_exchange_rate').val();
					//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/trims_delivery_entry_controller" );
					show_list_view(1+'_'+ex_data[1]+'_'+1+'_'+exchange_rate,'order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');
					release_freezing();
				}
			}
		}
	}
	
	function fnc_exchange_rate()
	{
		//var rcv_date=$('#txt_rcv_date').val();
		var currency_id=$('#cbo_currency').val();

		var response=return_global_ajax_value(currency_id, 'check_conversion_rate', '', 'requires/trims_delivery_entry_controller');
		$('#txt_exchange_rate').val(response);
		calculate_domestic();

		/*if(rcv_date!='')
		{
			var response=return_global_ajax_value(currency_id, 'check_conversion_rate', '', 'requires/trims_delivery_entry_controller');
			$('#txt_exchange_rate').val(response);
		}
		else
		{
			return;
		}*/
	}

	function calculate_domestic()
	{
		var exchange_rate=$('#txt_exchange_rate').val();
		var numRow = $('table#tbl_dtls_emb tbody tr').length;
		//alert(numRow); return;
		for (var i=1;i<=numRow; i++)
		{
			var domRate=0; var domAmount=0;
			var rate=$('#txtRate_'+i).val()*1;
			var amount=$('#txtAmount_'+i).val()*1;
			domRate=exchange_rate*rate;
			domAmount=exchange_rate*amount;
			$('#txtDomRate_'+i).val(domRate.toFixed(4));
			$('#txtDomamount_'+i).val(domAmount.toFixed(4));
		}
	}
	function load_uom(i)
	{
		var itemGroup=$('#cboItemGroup_'+i).val();
		var response=return_global_ajax_value(itemGroup, 'check_uom', '', 'requires/trims_delivery_entry_controller');
		$('#cboUom_'+i).val(response);

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

	function calculate_amount(i)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation( 'txtAmount_'+i, 'txtOrderQuantity_'+i+'*txtRate_'+i, '*','',ddd); 

		//calculate_total_amount(1);
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
			
			$('#cboItemGroup_'+row_num).removeAttr("onChange").attr("onChange","load_uom("+row_num+")");
			$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");
			$('#hdnbookingDtlsId_'+row_num).removeAttr("value").attr("value","");
			$('#txtOrderQuantity_'+row_num).removeAttr("onClick").attr("onClick","openmypage_order_qnty(1,0,"+row_num+")");

			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
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
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';
				
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
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

	function openmypage_delivery()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_delivery_entry_controller.php?action=delivery_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[0], "load_delivery_data_to_form", "requires/trims_delivery_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				var company_name = $('#cbo_company_name').val();
				show_list_view(2+'_'+ex_data[0]+'_'+within_group+'_'+company_name+'_'+ex_data[1],'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

	function openmypage_order_qnty(type,receive_dtls_id,row)
	{
		var company 		= $('#cbo_company_name').val();
		var party_name 		= $('#cbo_party_name').val();
		var within_group 	= $('#cbo_within_group').val();
		var data_break		=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId =$('#hdnDtlsUpdateId_'+row).val();
		//var job_no 			= $('#txt_job_no').val();
		//var order_no 			= $('#txt_order_no').val();
		//var booking_po_id 	= $('#txtbuyerPoId_'+row).val();
		//alert(data_break);
		
		
		var page_link = 'requires/trims_delivery_entry_controller.php?within_group='+within_group+'&receive_dtls_id='+receive_dtls_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&type='+type+'&action=order_qty_popup';
		//alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Popup', 'width=670px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
			var receive_total_order_qnty=this.contentDoc.getElementById("txt_total_order_qnty");
			//var receive_average_rate=this.contentDoc.getElementById("txt_average_rate");
			//var receive_total_order_amount=this.contentDoc.getElementById("txt_total_order_amount"); 
			
			$('#hdnDtlsdata_'+row).val(break_data.value);
			$('#txtDeletedId_'+row).val(break_delete_id.value);
			
			$('#txtOrderQuantity_'+row).val(receive_total_order_qnty.value);
			//$('#txtRate_'+row).val(receive_average_rate.value);
			//$('#txtAmount_'+row).val(receive_total_order_amount.value);
		}		
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:950px;">
			<legend>Trims Order Receive</legend>
                <table width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_dalivery_no" id="txt_dalivery_no" onDblClick="openmypage_delivery();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_delivery_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
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
                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" /></td>
                    </tr> 
                    <tr>
                        <td class="must_entry_caption" ><strong>Work Order</strong></td>
                        <td ><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_devivery_workorder();" readonly />
                        </td>
                        <td >Challan No</td>
                        <td ><input name="txt_challan_no" id="txt_challan_no" type="text"  class="text_boxes" style="width:140px" />
                        </td>
                        <td >Gate Pass No</td>
                        <td ><input name="txt_gate_pass_no" id="txt_gate_pass_no" type="text"  class="text_boxes" style="width:140px" />
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"fnc_exchange_rate()", 1,"" ); ?></td>
                        <td >Remarks</td>
                        <td ><input name="txt_remarks" id="txt_remarks" type="text"  class="text_boxes" style="width:140px" />
                        </td>
                        <td style="display: none;">Delivery Status</td>
                        <td style="display: none;"><?php echo create_drop_down( "cboshipingStatus", 150, $shipment_status,"", 0, "--  --", 0, "" ); ?></td>
                    </tr> 
                </table>
        </fieldset> 			
        <fieldset style="width:1120px;">
           <legend>Trims Order Receive Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<th width="90">Work Order</th>
                        <th width="110" id="buyerpo_td">Buyer's PO</th>
                        <th width="110" id="buyerstyle_td">Buyer's Style Ref.</th>
                        <th width="110" id="buyerbuyer_td">Buyer's Buyer </th>
                        <th width="90">Section</th>
                        <th width="90">Trims Group</th>
                        <th width="60">Order UOM</th>
                        <th width="70" class="must_entry_caption">Quantity</th>
                        <th width="70">Previous Delv Qty</th>
                        <th width="80">Curr. Delv Qnty</th>
                        <th width="70">Claim Qnty</th>
                        <th width="90">Remarks</th>
                        <th style="display: none;"></th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                        	<td><input id="txtWorkOrder_1" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                        		<input id="txtWorkOrderID_1" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                        	</td>
                            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
                            </td>
                            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
                            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
                            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "load_uom(1)",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
                            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly="readonly" /></td>
                            <td><input id="txtCurQty_1" name="txtCurQty[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td><input id="txtClaimQty_1" name="txtClaimQty[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly="readonly" /></td> 
                            <td><input id="txtRemarksDtls_1" name="txtRemarksDtls[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly="readonly" />
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_1">
                                <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_1">
                                <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_1">
                                
                            </td> 
                            <td width="65" style="display: none;">
							<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
							<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
						</td>
                        </tr>
                    </tbody>
                </table>
                <table width="1030" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="13" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,1,"fnResetForm();",1); ?>
                        	<input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                            <input type="hidden" name="received_id" id="received_id">
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>