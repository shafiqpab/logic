<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	23-01-2019 
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
echo load_html_head_contents("Trims Job Card Preparation", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_production_entry( operation )
	{
		var delete_master_info=0; var i=0;
		if ( form_validation('txt_prod_date*cbo_mc_group','Production Date*M/C Group')==false )
			{
				return;
			}
		var txt_production_no 	= $('#txt_production_no').val();
		var cbo_company_name 	= $('#cbo_company_name').val();
		var cbo_location_name 	= $('#cbo_location_name').val();
		var cbo_within_group 	= $('#cbo_within_group').val();
		var cbo_party_name 		= $('#cbo_party_name').val();
		var cbo_party_location 	= $('#cbo_party_location').val();
		var txt_prod_date 		= $('#txt_prod_date').val();
		var cbo_section 		= $('#cbo_section').val();
		var txt_item 			= $('#txt_item').val();
		var txt_order_qty 		= $('#txt_order_qty').val();
		var cbo_mc_group 		= $('#cbo_mc_group').val();
		var cbo_floor_id 		= $('#cbo_floor_id').val();
		var hid_order_id 		= $('#hid_order_id').val();
		var hid_recv_id 		= $('#hid_recv_id').val();
		var hid_job_id 			= $('#hid_job_id').val();
		var update_id 			= $('#update_id').val();
		var txt_variable_status 			= $('#txt_variable_status').val();
		
		var j=0; var check_field=0; data_all="";
		$("#tbl_dtls_emb tbody tr").each(function()
		{
			var txtItem 			= $(this).find('input[name="txtItem[]"]').val();
			var cboMachineName		= $(this).find('select[name="cboMachineName[]"]').val();
			var txtMcNo 			= $(this).find('input[name="txtMcNo[]"]').val();
			var txtMcNoID 			= $(this).find('input[name="txtMcNoID[]"]').val();
			var cbosubProcess 		= $(this).find('select[name="cbosubProcess[]"]').val();
			var txtcolor 			= $(this).find('input[name="txtcolorID[]"]').val();
			var txtsize 			= $(this).find('input[name="txtsizeID[]"]').val();
			var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
			var txtImpression 		= $(this).find('input[name="txtImpression[]"]').val();
			var cborawColor 		= $(this).find('select[name="cborawColor[]"]').val();
			var txtQtyReel 			= $(this).find('input[name="txtQtyReel[]"]').val();
			var txtTotalHead 		= $(this).find('input[name="txtTotalHead[]"]').val();
			var txtProdQty 			= $(this).find('input[name="txtProdQty[]"]').val();
			var txtQcPassQty 		= $(this).find('input[name="txtQcPassQty[]"]').val();
			var txtCompProd 		= $(this).find('input[name="txtCompProd[]"]').val();
			var hdnCumDelQty 		= $(this).find('input[name="hdnCumDelQty[]"]').val();
			var txtRejectQty 		= $(this).find('input[name="txtRejectQty[]"]').val();
			var cboProdTime 		= $(this).find('select[name="cboProdTime[]"]').val();
			var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
			var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
			var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
			var hdnjobDtlsId 		= $(this).find('input[name="hdnjobDtlsId[]"]').val();
			var hdnRcvDtlsId 		= $(this).find('input[name="hdnRcvDtlsId[]"]').val();
			var hdnBreakIDs 		= $(this).find('input[name="hdnBreakIDs[]"]').val();
			var txtBalQty 			= $(this).find('input[name="txtBalQty[]"]').val();
			var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
			var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
			var txtDeletedId 		= $(this).find('input[name="txtDeletedId[]"]').val();

			if(hdnDtlsUpdateId!='' || (hdnDtlsUpdateId=='' && txtProdQty*1>0))
			{
				if((txtProdQty*1)>(txtBalQty*1))
				if((txtBalQty*1)<0)
				{
					alert("Production Qty can't exceed Production Balance Qty");
					check_field=1 ; 
					return;
				}
				if(hdnDtlsUpdateId!='')
				{

					if(txtCompProd*1<hdnCumDelQty*1)
					{
						alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
						check_field=1 ;
						return;
					}
				}
				
				

				j++;  i++;
				//alert(j);
				data_all += "&cboMachineName_" + j + "='" + cboMachineName + "'&txtMcNo_" + j + "='" + txtMcNo+ "'&txtMcNoID_" + j + "='" + txtMcNoID + "'&txtItem_" + j + "='" + txtItem  + "'&cbosubProcess_" + j + "='" + cbosubProcess + "'&txtcolor_" + j + "='" + txtcolor + "'&txtsize_" + j + "='" + txtsize + "'&cboUom_" + j + "='" + cboUom + "'&txtImpression_" + j + "='" + txtImpression + "'&cborawColor_" + j + "='" + cborawColor + "'&txtQtyReel_" + j + "='" + txtQtyReel + "'&txtTotalHead_" + j + "='" + txtTotalHead+ "'&txtProdQty_" + j + "='" + txtProdQty+ "'&txtCompProd_" + j + "='" + txtCompProd+ "'&hdnCumDelQty_" + j + "='" + hdnCumDelQty+ "'&txtRejectQty_" + j + "='" + txtRejectQty+ "'&txtQcPassQty_" + j + "='" + txtQcPassQty+ "'&cboProdTime_" + j + "='" + cboProdTime + "'&txtRemarks_" + j + "='" + txtRemarks +"'&hdnjobDtlsId_" + j + "='" + hdnjobDtlsId +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId+"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId+"'&hdnRcvDtlsId_" + j + "='" + hdnRcvDtlsId+"'&hdnBreakIDs_" + j + "='" + hdnBreakIDs +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&txtDeletedId_" + j + "='" + txtDeletedId + "'";
			}
		});
		//alert(j+'=');
		if(j==0)
		{
			alert('Please Insert Qty At Least One Row.');
			return;
		}
		//alert(check_field); return;
		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_production_no='+txt_production_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_prod_date='+txt_prod_date+'&cbo_section='+cbo_section+'&txt_item='+txt_item+'&txt_order_qty='+txt_order_qty+'&cbo_mc_group='+cbo_mc_group+'&cbo_floor_id='+cbo_floor_id+'&hid_order_id='+hid_order_id+'&hid_recv_id='+hid_recv_id+'&hid_job_id='+hid_job_id+'&txt_variable_status='+txt_variable_status+'&update_id='+update_id+data_all;
			
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/trims_production_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_entry_response;
		}
	}
	
	function fnc_job_entry_response()
	{
		
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if(response[0]==555)
			{
				alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
				return;
			}
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_production_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				var within_group 	= $('#cbo_within_group').val();
				var company_name 	= $('#cbo_company_name').val();
				var location_name 	= $('#cbo_location_name').val();
				var mc_group 		= $('#cbo_mc_group').val();
				var floor_id 		= $('#cbo_floor_id').val();
				
				//$('#txt_order_no').attr('disabled',true);
				//$('#cbo_within_group').attr('disabled',true);
				
				var variable_status = $('#txt_variable_status').val()*1;
				if(variable_status==2){
					show_list_view(2+'**'+response[4]+'**'+within_group+'**'+company_name+'**'+response[3]+'**'+response[2]+'**'+location_name+'**'+mc_group+'**'+floor_id,'po_order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');
				}else{
					show_list_view(2+'**'+response[4]+'**'+within_group+'**'+company_name+'**'+response[3]+'**'+response[2],'order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');
				}
				set_button_status(1, permission, 'fnc_production_entry',1);

			}else if(response[0]==2){
				location.reload();
			}
			show_msg(response[0]);
			release_freezing();
		}
	}

	function openmypage_work_order()
	{
		if ( form_validation('cbo_company_name*cbo_section','Company*Section')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_section').value;
		page_link='requires/trims_production_entry_controller.php?action=work_order_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemailjob=this.contentDoc.getElementById("all_subcon_job").value;
			var theemaildtls=this.contentDoc.getElementById("all_sub_dtls_id").value;
			var theemailbreak=this.contentDoc.getElementById("all_sub_break_id").value;
			var theemailqty=this.contentDoc.getElementById("total_order_qty").value;
			$('#txt_order_qty').val(theemailqty);
			if (theemailbreak!="")
			{
				//alert(theemailbreak);
				freeze_window(5);
				get_php_form_data( theemailjob, "load_php_data_to_form", "requires/trims_production_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				show_list_view(1+'**'+theemailbreak+'**'+within_group,'order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(0, permission, 'fnc_production_entry',1);
				release_freezing();
			}
		}
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
			load_drop_down( 'requires/trims_production_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			//$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			//$('#txt_order_no').attr('readonly',true);
			//$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/trims_production_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			//$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			//$('#txt_order_no').attr('readonly',false);
			//$('#txt_order_no').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/trims_production_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
		} 
	}
	
	
	
	/*function openmypage_qc_qnty(row)
	{
		if ( form_validation('cbo_company_name*txt_job_no','Company*JOB NO.')==false ){
			return;
		}
		var company 	= $('#cbo_company_name').val();
		var job_no 		= $('#txt_job_no').val();
		var party_name 	= $('#cbo_party_name').val();
		var order_mst_id = $('#hid_order_id').val();
		var order_no 	= $('#txt_order_no').val();
		var within_group = $('#cbo_within_group').val();
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		var colorID 	=$('#txtcolorID_'+row).val();
		var sizeID 	 	=$('#txtsizeID_'+row).val();
		var txtItem 	=$('#txtItem_'+row).val();
		var subSection 	=$('#cboSubSection_'+row).val();
		var data_break 	=$('#hdnDtlsdata_'+row).val();
		var recv_id 	=$('#hid_recv_id').val();
		var order_id 	=$('#hid_order_id').val();
		var job_id 		=$('#hid_job_id').val();
		var page_link 	= 'requires/trims_production_entry_controller.php?within_group='+within_group+'&order_no='+order_no+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&colorID='+colorID+'&sizeID='+sizeID+'&txtItem='+txtItem+'&subSection='+subSection+'&recv_id='+recv_id+'&order_id='+order_id+'&job_id='+job_id+'&action=qc_qty_popup';
		//alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Pop-up', 'width=1170px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row");
			var breakDatas=trim(break_data.value).split('*******');
			var summaryDatas=trim(breakDatas[1]).split('_');
			$('#txtQcPassQty_'+row).val(summaryDatas[0]);
			$('#txtRejectQty_'+row).val(summaryDatas[1]);
			$('#txtProdQty_'+row).val(summaryDatas[2]);
			$('#txtCompProd_'+row).val(summaryDatas[3]);
			$('#txtBalQty_'+row).val(summaryDatas[4]);
			$('#txtQcPassQty_'+row).attr('readonly',true);
			$('#txtRejectQty_'+row).attr('readonly',true);
			$('#txtProdQty_'+row).attr('readonly',true);
			$('#txtCompProd_'+row).attr('readonly',true);
			$('#txtBalQty_'+row).attr('readonly',true);
			$('#hdnDtlsdata_'+row).val(break_data.value);
			$('#txtDeletedId_'+row).val(break_delete_id.value);
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 			
		}		
	}*/
	
	function openmypage_qc_qnty(row,jobDtlsId)
	{
		if ( form_validation('cbo_company_name*txt_job_no','Company*JOB NO.')==false ){
			return;
		}
		var company 	= $('#cbo_company_name').val();
		var job_no 		= $('#txt_job_no').val();
		var party_name 	= $('#cbo_party_name').val();
		var order_mst_id = $('#hid_order_id').val();
		var order_no 	= $('#txt_order_no').val();
		var within_group = $('#cbo_within_group').val();
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		var colorID 	=$('#txtcolorID_'+row).val();
		var sizeID 	 	=$('#txtsizeID_'+row).val();
		var txtItem 	= $('#txtItem_'+row).val();
		var subSection 	=$('#cboSubSection_'+row).val();
		var data_break 	=$('#hdnDtlsdata_'+row).val();
		var recv_id 	=$('#hid_recv_id').val();
		var order_id 	=$('#hid_order_id').val();
		var job_id 		=$('#hid_job_id').val();
		txtItem= "<pre>'"+txtItem+"'</pre>";
		var page_link 	= 'requires/trims_production_entry_controller.php?within_group='+within_group+'&order_no='+order_no+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&colorID='+colorID+'&sizeID='+sizeID+'&txtItem='+txtItem+'&subSection='+subSection+'&recv_id='+recv_id+'&order_id='+order_id+'&job_id='+job_id+'&jobDtlsId='+jobDtlsId+'&action=qc_qty_popup';
		//alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Pop-up', 'width=1170px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row");
			var breakDatas=trim(break_data.value).split('*******');
			var summaryDatas=trim(breakDatas[1]).split('_');
			$('#txtQcPassQty_'+row).val(summaryDatas[0]);
			$('#txtRejectQty_'+row).val(summaryDatas[1]);
			$('#txtProdQty_'+row).val(summaryDatas[2]);
			$('#txtCompProd_'+row).val(summaryDatas[3]);
			$('#txtBalQty_'+row).val(summaryDatas[4]);
			$('#txtQcPassQty_'+row).attr('readonly',true);
			$('#txtRejectQty_'+row).attr('readonly',true);
			$('#txtProdQty_'+row).attr('readonly',true);
			$('#txtCompProd_'+row).attr('readonly',true);
			$('#txtBalQty_'+row).attr('readonly',true);
			$('#hdnDtlsdata_'+row).val(break_data.value);
			$('#txtDeletedId_'+row).val(break_delete_id.value);
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 			
		}		
	}
	
	function fnc_variable_qc_qty(comid,within_group)
	{
		var variable_status = $('#txt_variable_status').val()*1;
		if(variable_status==2)
		{
			$('#txtQcPassQty_1').removeAttr("onDblClick").attr("onDblClick","openmypage_qc_qnty(1,'0',1)");
			$('#txtQcPassQty_1').attr('readonly',true);
			$('#txtQcPassQty_1').attr('placeholder','Browse');
		}
	}

	function openmypage_row_metarial(type,booking_dtls_id,row)
	{
		var company 	= $('#cbo_company_name').val();
		var within_group = $('#cbo_within_group').val();
		var data_break=$('#hdnDtlsdata_'+row).val();
		var order_qty=$('#txt_order_qty').val();
		//var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		
		//var page_link = 'requires/trims_production_entry_controller.php?within_group='+within_group+'&booking_dtls_id='+booking_dtls_id+'&order_no='+order_no+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&job_no='+job_no+'&action=order_qty_popup';
		var page_link = 'requires/trims_production_entry_controller.php?within_group='+within_group+'&data_break='+data_break+'&order_qty='+order_qty+'&action=row_metarial_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Raw Materials Details Popup', 'width=950px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
			
			$('#txtRawMat_'+row).val(break_data.value);
			$('#hdnDtlsdata_'+row).val(break_data.value);
			//$('#txt_deleted_id'+row).val(break_delete_id.value);
			/*$('#txtOrderQuantity_'+row).val(receive_total_order_qnty.value);
			$('#txtRate_'+row).val(receive_average_rate.value);
			$('#txtAmount_'+row).val(receive_total_order_amount.value);
			if(within_group==2)
			{
				var exchange_rate 	= $('#txt_exchange_rate').val()*1;
				$('#txtDomRate_'+row).val(receive_average_rate.value*exchange_rate);
				$('#txtDomamount_'+row).val(receive_total_order_amount.value*exchange_rate);
			}*/
		}
	}

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name*cbo_within_group','Company*Within Group')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_production_entry_controller.php?action=job_popup&data='+data;
		title='Trims JOB Search';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(theemail.value);
				freeze_window(5);
				get_php_form_data( ex_data[0], "load_mst_php_data_to_form", "requires/trims_production_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				var company_name = $('#cbo_company_name').val();
				//alert(variable_status);
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/trims_production_entry_controller','setFilterGrid("list_view",-1)');
				
				
				var variable_status = $('#txt_variable_status').val()*1;
				if(variable_status==2)
				{
					show_list_view(1+'**'+ex_data[0]+'**'+within_group+'**'+company_name+'**'+ex_data[2],'po_order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');
				}
				else
				{
					show_list_view(1+'**'+ex_data[0]+'**'+within_group+'**'+company_name+'**'+ex_data[2],'order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');
				}
				//$('#cboMachineName_1').val("")
				set_button_status(0, permission, 'fnc_production_entry',1);
				release_freezing();
			}
		}
		
				
		
		
	}
	
	function openmypage_production()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_production_entry_controller.php?action=production_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(ex_data[2]);

				freeze_window(5);
				get_php_form_data( ex_data[0], "load_production_data_to_form", "requires/trims_production_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				var company_name = $('#cbo_company_name').val();
				var location_name = $('#cbo_location_name').val();
				var mc_group = $('#cbo_mc_group').val();
				var floor_id = $('#cbo_floor_id').val();
				var variable_status = $('#txt_variable_status').val()*1;
				if(variable_status==2)
				{
					show_list_view(2+'**'+ex_data[3]+'**'+within_group+'**'+company_name+'**'+ex_data[2]+'**'+ex_data[0]+'**'+location_name+'**'+mc_group+'**'+floor_id,'po_order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');	
				}
				else
				{
					show_list_view(2+'**'+ex_data[3]+'**'+within_group+'**'+company_name+'**'+ex_data[2]+'**'+ex_data[0],'order_dtls_list_view','emb_details_container','requires/trims_production_entry_controller','setFilterGrid(\'list_view\',-1)');
				}
				
				//fnc_load_drop(company_name,location_name,mc_group,floor_id,0,0);
				
				set_button_status(1, permission, 'fnc_production_entry',1);
				release_freezing();
			}
		}
	}

	function copy_values()
	{
		var copy_val=document.getElementById('copy_basis').checked;
		var row_num=$('#tbl_dtls_emb tbody tr').length;
		if(copy_val==true)
		{
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtOrdQty_'+j).val()*1;
				var txtQcBalQty 	= $('#txtQcBalQty_'+j).val()*1;
				//alert(txtOrdQty+"**"+j);
				$('#txtQcPassQty_'+j).val((txtQcBalQty).toFixed(4));
				cal_values(j);
			}
		}
		else
		{
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtOrdQty_'+j).val()*1;
				$('#txtQcPassQty_'+j).val('');
				cal_values(j);
			}
		}
		//alert(copy_val);
		//var copy_basis=$('input[name="copy_basis"]:checked').val()
	}

	function cal_values(rowNo)
	{
		var balance='';
		var txtOrdQty 		= $('#txtOrdQty_'+rowNo).val()*1;
		var txtQcPassQty 	= $('#txtQcPassQty_'+rowNo).val()*1;
		var txtQcBalQty 	= $('#txtQcBalQty_'+rowNo).val()*1;
		var txtRejectQty 	= $('#txtRejectQty_'+rowNo).val()*1;
		
		var txtBalQty 		= $('#txtBalQty_'+rowNo).val()*1;
		var hidBalQty 		= $('#hidBalQty_'+rowNo).val()*1;
		var hidCompProd 	= $('#hidCompProd_'+rowNo).val()*1;
		var hdnCumDelQty 	= $('#hdnCumDelQty_'+rowNo).val()*1;
		var hdnDtlsUpdateId = $('#hdnDtlsUpdateId_'+rowNo).val()*1;

		
		$('#txtProdQty_'+rowNo).val((txtQcPassQty+txtRejectQty).toFixed(4));
		//var txtProdQty 		= $('#txtProdQty_'+rowNo).val()*1;

		balance=(hidBalQty-txtQcPassQty);
		//alert(balance);
		if(balance<0)
		{
			alert("Production Qty can't exceed Production Balance Qty");
			$('#txtProdQty_'+rowNo).val('');
			$('#txtQcPassQty_'+rowNo).val('');
			$('#txtBalQty_'+rowNo).val((hidBalQty).toFixed(4));

			$('#txtCompProd_'+rowNo).val((txtOrdQty-hidBalQty).toFixed(4));
			return;
		}
		else
		{
			$('#txtBalQty_'+rowNo).val((balance).toFixed(4));
			$('#txtCompProd_'+rowNo).val((hidCompProd+txtQcPassQty).toFixed(4));
		}

		var txtCompProd 	= $('#txtCompProd_'+rowNo).val()*1;
		//alert(hdnDtlsUpdateId);
		if(hdnDtlsUpdateId!='')
		{//alert(txtCompProd+'='+hdnCumDelQty);
			if(txtCompProd<hdnCumDelQty) 
			{
				alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
				$('#txtProdQty_'+rowNo).val('');
				$('#txtQcPassQty_'+rowNo).val('');
				$('#txtRejectQty_'+rowNo).val('');
				$('#txtCompProd_'+rowNo).val((hidCompProd).toFixed(4));
				return;
				//cal_values(rowNo);
			}
		}
	}


	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_production_entry', 1);
        reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);

		//reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1*2',"disable_enable_fields('txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
		//$('#txt_order_no').attr('disabled',false);
		//$('#cboGmtsItem_1').attr('disabled',false);
		//$('#cboProcessName_1').attr('disabled',false);
		//$('#cboembtype_1').attr('disabled',false);
		//$('#cboBodyPart_1').attr('disabled',false);
    }
	
	
function chk_po_level_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_po_level_variable', '', 'requires/trims_production_entry_controller').trim();
   status = status.split("**");
   //alert(status);
   if(status[0]==2)
   {
	   $('#txt_variable_status').val(status[0]);
	   //$('#copyvalues').html('');
	   $('#copyvalues').css('display','none');

   }
   else
   {
	   $('#copyvalues').css('display','block');
   }
   
}


function fnc_load_drop(companyid,locationid,mcgroup,floorid)
{
	var row_num=$('#tbl_dtls_emb tbody tr').length;
	var machine_loadData = return_global_ajax_value(companyid+'_'+locationid+'_'+mcgroup+'_'+floorid+'_'+row_num, 'machine_load', '', 'requires/trims_production_entry_controller');
	var exMachineLoadData = machine_loadData.split("&!&!");
	for (var j=1; j<exMachineLoadData.length; j++)
	{
		document.getElementById( 'machinetd_'+j ).innerHTML = exMachineLoadData[j];
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
                        <td colspan="3" align="right"><strong>Production ID</strong></td>
                        <td colspan="3">
                            <input class="text_boxes"  type="text" name="txt_production_no" id="txt_production_no" placeholder="Double Click" style="width:140px;" onDblClick="openmypage_production();"  readonly />
                            
                             <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                            </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td> 
                        <td width="160"><? 
						echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_production_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/trims_production_entry_controller', this.value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_machine_group', 'group_td');  fnc_load_party(1,document.getElementById('cbo_within_group').value);fnc_variable_qc_qty(this.value,document.getElementById('cbo_within_group').value);chk_po_level_variabe(this.value)"); ?>
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
                       <td class="must_entry_caption">Production Date</td>
                       <td><input type="text" name="txt_prod_date" id="txt_prod_date"  style="width:140px"  class="datepicker" value="<? echo date("d-m-Y"); ?>" disabled /></td>
                    </tr> 
                    <tr>
                    	<td><strong>JOB NO.</strong></td>
                        <td>
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    	<td>Work Order</td>
                        <td ><input name="txt_order_no" id="txt_order_no" type="text" placeholder="Dispaly"  class="text_boxes" style="width:140px" readonly />
                        </td>
                        <td class="must_entry_caption">Section</td>
                        <td><? echo create_drop_down( "cbo_section", 150, $trims_section,"", 1, "-- Select Section --","",'',1,'','','','','','',"cboSection[]"); ?></td>
                    </tr>
                    <tr>
                        
                        <td class="must_entry_caption">M/C Group</td>
                        <td id="group_td"><? echo create_drop_down( "cbo_mc_group", 150, $blank_array,"", 1, "-- Select Group --", $selected, "",1 );?></td>
                        <td>Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "",1 ); ?></td>
                        <td style="display: none" class="must_entry_caption" >Item</td>
                        <td style="display: none"><input name="txt_item" id="txt_item" type="text"  class="text_boxes" style="width:140px"  readonly />
                        </td>
                        <td style="display: none">Quantity</td>
                        <td style="display: none"><input name="txt_order_qty" id="txt_order_qty" type="text"  class="text_boxes" style="width:140px" readonly />
                        </td>
                    </tr>
                </table>
        	</fieldset> 
        	<fieldset style="width:1220px;">
           	<legend>Trims Order Receive Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<th width="100">Item Description</th>
                    	<th width="70">Item Color</th>
                    	<th width="60">Item Size</th>
                    	<th width="70">Sub Section</th>
                    	<th width="60">UOM</th>
                        <th width="80">M/C No.</th>
                        <th width="60">Impression</th>
                        <th width="60">Imp. Color</th>
                        <th width="60">Qty/ Reel</th>
                        <th width="60">Total Head</th>
                        <th width="60">Job Qty</th>
                        <th width="60">QC Pass<span id="copyvalues"><input id="copy_basis" name="copy_basis" type="checkbox" onChange="copy_values()" /></span></th>
						<th width="60">Reject Qty.</th>
						<th width="60">Current Prod. Qty.</th>
						<th width="60">Cum. Prod. Qty</th>
						<th width="60">Prod. Balance</th>

                        <th width="70">Prod. Time</th>
                        <th>Remarks</th>
                        <th width="110"  style="display: none;">Sub Process</th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                        	<td><input id="txtItem_1" name="txtItem[]" type="text" class="text_boxes" style="width:87px" placeholder="Display"/>
                        	<td><input id="txtcolor_1" name="txtcolor[]" type="text" class="text_boxes" style="width:57px" placeholder="Display"/>
                        		<input id="txtcolorID_1" name="txtcolorID[]" type="hidden" value="" class="text_boxes" style="width:57px" placeholder="Display"/></td>
                            <td><input id="txtsize_1" name="txtsize[]" type="text" class="text_boxes" style="width:57px" placeholder="Display"/>
                            	<input id="txtsizeID_1" name="txtsizeID[]" type="hidden" value=""  class="text_boxes" style="width:57px" placeholder="Display"/></td>
                            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 70, $trims_sub_section,"", 1, "-- Select Sub-Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>	
                            <td id="machinetd_1">
                            	<? echo create_drop_down( "cboMachineName_1", 80, $blank_array,"", 1, "-- Select --", $selected,"", "",'','','','','','',"cboMachineName[]"); ?>	</td>
                            </td>
                            <td><input id="txtImpression_1" name="txtImpression[]" type="text" class="text_boxes" style="width:47px" placeholder="Display"/></td>
							<td><? echo create_drop_down( "cborawColor_1", 60, $blank_array,"", 1, "-- Select --",0,1, 1,'','','','','','',"cborawColor[]"); ?>
                            <td><input id="txtQtyReel_1" name="txtQtyReel[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>
                            <td><input id="txtTotalHead_1" name="txtTotalHead[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>

                            <td><input id="txtOrdQty_1" name="txtOrdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>

                            <td><input id="txtQcPassQty_1" name="txtQcPassQty[]" class="text_boxes_numeric" type="text"  style="width:47px"  placeholder="Write/Click To Search"  /></td>
							<td><input id="txtRejectQty_1" name="txtRejectQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Write" /></td>
                            <td><input id="txtProdQty_1" name="txtProdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>

                            <td><input id="txtCompProd_1" name="txtCompProd[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>
                            <td><input id="txtBalQty_1" name="txtBalQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  /></td>
							<td><?
								$production_time=array(1=>"G.Hour",2=>"OT.Hour");
							 	echo create_drop_down( "cboProdTime_1", 70, $production_time,"", 1, "-- Select --",0,1,0,'','','','','','',"cboProdTime[]"); ?>	</td>
							<td><input id="txtRemarks_1" name="txtRemarks[]" class="text_boxes" type="text"  style="width:60px"/></td>
							
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="hdnjobDtlsId[]" id="hdnjobDtlsId_1">
                                <input type="hidden" name="hdnRcvDtlsId[]" id="hdnRcvDtlsId_1">
                            </td>
                            <td style="display: none;">
                            	<input id="txtMcNo_1" name="txtMcNo[]" type="text" class="text_boxes" style="width:100px;" placeholder="Display" />
                            	<input id="txtMcNoID_1" name="txtMcNoID[]" type="hidden" class="text_boxes" style="width:70px" readonly />
                            </td>
                            <td style="display: none;"><? echo create_drop_down( "cbosubProcess_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cbosubProcess[]"); ?>	</td>
                        </tr>                     
                    </tbody>
                </table>
                <table width="1210" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="13" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_production_entry", 0,0,"fnResetForm();",1); ?>
                        	<input type="hidden" name="hid_order_id" id="hid_order_id">
                        	<input type="hidden" name="hid_recv_id" id="hid_recv_id">
                        	<input type="hidden" name="hid_job_id" id="hid_job_id">
                        	<input type="hidden" name="update_id" id="update_id">
                             <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                              <input type="hidden" name="txtDeletedId[]" id="txtDeletedId_1">
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>