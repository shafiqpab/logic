<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	28-10-2020
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
echo load_html_head_contents("Sub Con Work Order Entry", "../../", 1,1, $unicode,1,'');
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][450] );
//print_r($data_arr);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	
	echo "var field_level_data= ". $data_arr . ";\n";

	//echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][255]) . "';\n";
	//echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][255]) . "';\n";

	?>
	
	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+0+"_"+2;
		page_link='requires/sub_con_wo_entry_controller.php?action=work_order_popup&data='+data;
		title='WO Pop-up';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("all_subcon_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{
				freeze_window(5);
				//get_php_form_data( ex_data[0], "load_php_data_to_form", "requires/sub_con_wo_entry_controller" );
				var subcon_jobs=return_global_ajax_value(ex_data[0], 'return_subcon_jobs', '', 'requires/sub_con_wo_entry_controller');
				$('#txt_ord_rec_no').val(subcon_jobs);
				$('#txt_ord_rec_id').val(ex_data[0]);
				var currency 		= $('#cbo_currency').val();

				show_list_view(1+'_'+ex_data[0]+'_'+currency,'order_dtls_list_view','tbl_dtls_emb','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(0, permission, 'fnc_job_order_entry',1);
				set_all_onclick();
				//tr_disabled();
				release_freezing();
			}
		}
	}

	function openmypage_order_qnty(row,i)
	{
		var company 		= $('#cbo_company_name').val();
		var hdnWoId 		= $('#hdnWoId_'+i).val();
		var hdnRcvBrkIds 	= $('#hdnRcvBrkIds_'+row).val();
		var cboSection 		= $('#cboSection_'+row).val();
		var cboSubSection 	= $('#cboSubSection_'+row).val();
		var cboItemGroup 	= $('#cboItemGroup_'+row).val();
		var cboUom 			= $('#cboUom_'+row).val();
		//var breakDtlsDatas 	= $('#hdnBreakDtlsDatas_'+row).val();
		var breakDtlsDatas=encodeURIComponent($('#hdnBreakDtlsDatas_'+row).val());


		//var data_break=encodeURIComponent($('#hdnDtlsdata_'+row).val());
		
		//alert(hdnBreakDtlsDatas);
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		//alert(hdnDtlsUpdateId+'='+txtConvFactor)
		//var page_link = 'requires/sub_con_wo_entry_controller.php?company='+company+'&hdnWoId='+hdnWoId+'&cboSection='+cboSection+'&cboSubSection='+cboSubSection+'&cboItemGroup='+cboItemGroup+'&cboUom'+cboUom+'&hdnBreakDtlsDatas'+hdnBreakDtlsDatas+'&action=order_qty_popup';
		var page_link = 'requires/sub_con_wo_entry_controller.php?company='+company+'&hdnWoId='+hdnWoId+'&cboSection='+cboSection+'&cboSubSection='+cboSubSection+'&cboItemGroup='+cboItemGroup+'&cboUom='+cboUom+'&breakDtlsDatas='+breakDtlsDatas+'&hdnRcvBrkIds='+hdnRcvBrkIds+'&action=order_qty_popup';
		//alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Pop-up', 'width=860px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			//$('#cboSection_'+row).attr('disabled',true);
			//$('#cboSubSection_'+row).attr('disabled',true);
			//$('#txtConvFactor_'+row).attr('disabled',true);
			$('#hdnBreakDtlsDatas_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
			var receive_total_order_qnty=this.contentDoc.getElementById("txt_total_order_qnty");
			var receive_average_rate=this.contentDoc.getElementById("txt_average_rate");
			var receive_total_order_amount=this.contentDoc.getElementById("txt_total_order_amount"); 
			
			$('#hdnBreakDtlsDatas_'+row).val(break_data.value);
			//$('#txtDeletedId_'+row).val(break_delete_id.value);
			
			$('#txtOrderQuantity_'+row).val(receive_total_order_qnty.value);
			$('#txtRate_'+row).val(receive_average_rate.value);
			$('#txtAmount_'+row).val(receive_total_order_amount.value);
			/*if(within_group==2)
			{
				var exchange_rate 	= $('#txt_exchange_rate').val()*1;
				$('#txtDomRate_'+row).val(receive_average_rate.value*exchange_rate);
				$('#txtDomamount_'+row).val(receive_total_order_amount.value*exchange_rate);
			}
			cal_booked_qty(row);*/
		}		
	}

	function fnc_job_order_entry( operation )
	{
		var delete_master_info=0; var i=0;
		if ( form_validation('cbo_company_name*cbo_location_name*cbo_paymode_id*cbo_supplier_id*cbo_currency*txt_exchange_rate*txt_wo_date*cbo_source_id*txt_delivery_date*txt_ord_rec_no','Company*location name*Pay Mode*Supplier*Currency*Exchange Rate*WO Date*Source*Delivery Date*Order Received NO')==false )
		{
			return;
		}
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_subcon_wo_print", "requires/sub_con_wo_entry_controller") 
			//return;
			show_msg("3"); return;
		}
		else
		{
			if(operation==2)
			{
				if(confirm("Do You Want To Delete")==false)
				{
					return;
				}
			}

			//txt_wo_no*cbo_company_name*cbo_location_name*cbo_paymode_id*cbo_supplier_id*cbo_currency*txt_wo_date*cbo_source_id*txt_delivery_date*txt_attention*txt_ord_rec_id*update_id  
			var txt_wo_no 			= $('#txt_wo_no').val();
			var cbo_company_name 	= $('#cbo_company_name').val();
			var cbo_location_name 	= $('#cbo_location_name').val();
			var cbo_paymode_id 		= $('#cbo_paymode_id').val();
			var cbo_supplier_id 	= $('#cbo_supplier_id').val();
			var cbo_currency 		= $('#cbo_currency').val();
			var txt_exchange_rate 	= $('#txt_exchange_rate').val();
			var txt_wo_date 		= $('#txt_wo_date').val();
			var cbo_source_id 		= $('#cbo_source_id').val();
			var txt_delivery_date 	= $('#txt_delivery_date').val();
			var txt_attention 		= $('#txt_attention').val();
			var txt_delivery_to 	= $('#txt_delivery_to').val();
			var txt_remarks 		= $('#txt_remarks').val();
			var txt_ord_rec_id 		= $('#txt_ord_rec_id').val();
			var txt_prev_ord_rec_id = $('#txt_prev_ord_rec_id').val();
			var txt_ord_rec_no 		= $('#txt_ord_rec_no').val();
			var update_id 			= $('#update_id').val();
			var j=0; var check_field=0; data_all=""; qty_chk=0;
				
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				var cboSection 			= $(this).find('select[name="cboSection[]"]').val();
				var cboSubSection 		= $(this).find('select[name="cboSubSection[]"]').val();
				var cboItemGroup 		= $(this).find('select[name="cboItemGroup[]"]').val();
				var txtOrderQuantity 	= $(this).find('input[name="txtOrderQuantity[]"]').val();
				var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
				var txtRate 			= $(this).find('input[name="txtRate[]"]').val();
				var txtAmount 			= $(this).find('input[name="txtAmount[]"]').val();
				var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
				var hdnDtlsdata 		= encodeURIComponent($(this).find('input[name="hdnBreakDtlsDatas[]"]').val());
				var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				var hdnWoDtlsIds 		= $(this).find('input[name="hdnWoDtlsIds[]"]').val();
				var hdnRcvId 			= $(this).find('input[name="hdnRcvId[]"]').val();
				var hdnBalance 			= $(this).find('input[name="hdnBalance[]"]').val();
				//alert(txtOrderQuantity +'=='+ hdnBalance);
				
				//2.0000==1199.0000
				if( hdnBalance*1 < txtOrderQuantity*1  ){
					alert("No Balance Quantity"); 
					check_field=1 ;return;
					
				}
				
				if(check_field !=1 )
				{
					if((txtOrderQuantity!=0 && txtOrderQuantity!='') && (txtRate!=0 && txtRate!='')){
					qty_chk=1; check_field=2;
					}else if(((txtOrderQuantity!=0 && txtOrderQuantity!='') && (txtRate==0 || txtRate=='')) || ((txtOrderQuantity==0 && txtOrderQuantity=='') && (txtRate!=0 || txtRate!=''))) {
						alert('Must Fill Up Order Quantity And Rate Value');
						check_field=1; return;
					}else {
						check_field=3;
					}
					//txt_total_amount 	+= $(this).find('input[name="amount[]"]').val()*1;
					
					if(check_field==2 || check_field==3)
					{
						j++;
						data_all += "'&cboSection_" + j + "='" + cboSection  + "'&cboSubSection_" + j + "='" + cboSubSection + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&txtOrderQuantity_" + j + "='" + txtOrderQuantity + "'&cboUom_" + j + "='" + cboUom  + "'&txtRate_" + j + "='" + txtRate + "'&txtAmount_" + j + "='" + txtAmount+ "'&txtRemarks_" + j + "='" + txtRemarks  +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&hdnWoDtlsIds_" + j + "='" + hdnWoDtlsIds +"'&hdnRcvId_" + j + "='" + hdnRcvId + "'";
						i++;
					}
				}
			});
		}
		
		if((check_field==2 || check_field==3) && qty_chk==1 )
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_wo_no='+txt_wo_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_paymode_id='+cbo_paymode_id+'&cbo_supplier_id='+cbo_supplier_id+'&cbo_currency='+cbo_currency+'&txt_exchange_rate='+txt_exchange_rate+'&txt_wo_date='+txt_wo_date+'&txt_delivery_date='+txt_delivery_date+'&cbo_source_id='+cbo_source_id+'&txt_attention='+txt_attention+'&txt_delivery_to='+txt_delivery_to+'&txt_remarks='+txt_remarks+'&txt_ord_rec_id='+txt_ord_rec_id+'&txt_prev_ord_rec_id='+txt_prev_ord_rec_id+'&txt_ord_rec_no='+txt_ord_rec_no+'&update_id='+update_id+data_all;
			//alert (data); //return; 
			//freeze_window(operation);
			http.open("POST","requires/sub_con_wo_entry_controller.php",true);
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
			
			/*if(trim(response[0])=='20')
			{
				alert("Bill Found ."+"\n So Update/Delete Not Possible");
				//alert("Job Found ."+"\n So Update/Delete Not Possible"+"\n Job Card No.:"+response[4]);
				release_freezing();
				return;
			}
			else if(trim(response[0])=='26')
			{
				alert ("Delivery Date not allowed less than Order Receive Date");
				release_freezing();
				return;
			}
			else if(trim(response[0])=='25')
			{
				alert ("Receive Date Must be Current Date");
				release_freezing();
				return;
			}
			else if(trim(response[0])=='27')
			{
				alert ("Order Quantity Can't Less Than Delivary Quantity");
				release_freezing();
				return;
			}*/

			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_wo_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				$('#txt_ord_rec_no').attr('disabled',true);
				/*var within_group = $('#cbo_within_group').val();
				if(within_group==2)
				{
					document.getElementById('txt_order_no').value = response[3];
				}
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				*/
				//show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2],'order_dtls_list_view','emb_details_container','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');

				show_list_view(2+'_'+response[2]+'_'+response[1],'order_dtls_list_view_update','tbl_dtls_emb','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				var currency=$('#cbo_currency').val();
				change_rate_caption(currency);
				//btn_load_change_bookings();
				set_all_onclick();


			}
			else if(response[0]==2)
			{
				location.reload();
			}
			show_msg(response[0]);
			release_freezing();
		}
	}

	function openmypage_wo()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		//var company=document.getElementById('cbo_company_name').value;
		//var supplier=document.getElementById('cbo_supplier_id').value;
		//var page_link = 'requires/sub_con_wo_entry_controller.php?company='+company+'&supplier='+supplier+'&action=wo_popup';

		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_supplier_id').value;
		page_link='requires/sub_con_wo_entry_controller.php?action=wo_popup&data='+data;
		//page_link='requires/sub_con_wo_entry_controller.php?action=wo_popup&data='+data;
		title='WO Pop-up';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=830px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			//alert(theemaildata);
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{
				freeze_window(5);
				get_php_form_data( ex_data[0], "load_php_data_to_form", "requires/sub_con_wo_entry_controller" );
				//var subcon_jobs=return_global_ajax_value(ex_data[0], 'return_subcon_jobs', '', 'requires/sub_con_wo_entry_controller');
				//$('#txt_ord_rec_no').val(subcon_jobs);
				//$('#txt_ord_rec_id').val(ex_data[0]);
				
				show_list_view(2+'_'+ex_data[0],'order_dtls_list_view_update','tbl_dtls_emb','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');	
				var currency=$('#cbo_currency').val();
				change_rate_caption(currency);			
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				set_all_onclick();
				//tr_disabled();
				release_freezing();
			}
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
		load_drop_down( 'requires/sub_con_wo_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
	}

	function fnc_load_party(type,within_group)
	{
		if(type!=3) // type=3 for refresh only
		{
			if ( form_validation('cbo_company_name','Company')==false )
			{
				$('#cbo_within_group').val(1);
				return;
			}
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		if(within_group==1 && (type==1 || type==3 ))
		{
			load_drop_down( 'requires/sub_con_wo_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$('#txtstyleRef_1').attr('placeholder','Display');
			$('#txtbuyer_1').attr('placeholder','Display');
			if(type==3)
			{
				$('#txtbuyerPo_1').attr('disabled',false);
				$('#txtstyleRef_1').attr('disabled',false);
				$('#txtbuyer_1').attr('disabled',false);
				var x = document.createElement("INPUT");
			  	x.setAttribute("type", "text");
			  	x.setAttribute("value", "");
			  	x.setAttribute("placeholder", "Display");
			  	x.setAttribute("id", "txtbuyer_1");
			  	x.setAttribute("name", "txtbuyer[]");
			  	x.setAttribute("class", "text_boxes");
			  	$("#buyerbuyerTd_1").html('');
			  	$("#buyerbuyerTd_1").append(x);
			}
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');

			$("#txt_delivery_date").val(''); 
			$('#txt_delivery_date').attr('disabled',true);
			$('#txt_delivery_date').attr('placeholder','Display');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/sub_con_wo_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			$('#txtbuyerPo_1').attr('placeholder','Write');
			$('#txtstyleRef_1').attr('placeholder','Write');
			$('#txtbuyer_1').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			$('#txt_delivery_date').attr('disabled',false);
			$('#txt_delivery_date').attr('placeholder','Select Date');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/sub_con_wo_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
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
			var title = 'Work Order Pop-up';
			var page_link = 'requires/sub_con_wo_entry_controller.php?company='+company+'&party_name='+party_name+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				//alert(theemaildata);
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					$('#txt_order_no').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					$('#cbo_currency').val(ex_data[2]);
					$('#txt_is_sample').val(ex_data[3]);
					fnc_exchange_rate();
					$('#cbo_company_name').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);
					$('#cbo_party_name').attr('disabled',true);
					$('#cbo_currency').attr('disabled',true);
					$('#txt_exchange_rate').attr('disabled',true);
					var exchange_rate = $('#txt_exchange_rate').val();
					var within_group = $('#cbo_within_group').val();
					//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/sub_con_wo_entry_controller" );
					show_list_view(1+'_'+ex_data[1]+'_'+within_group+'_'+exchange_rate+'_'+ex_data[3],'order_dtls_list_view','emb_details_container','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');
					//var min_dates=$("#min_date_id").val();
					//alert(min_dates);
					$("#txt_delivery_date").val(min_dates);
					set_all_onclick();
					tr_disabled();
					release_freezing();
				}
			}
		}
	}
	
	function fnc_exchange_rate(val)
	{
		//var rcv_date=$('#txt_rcv_date').val();
		//var currency_id=$('#cbo_currency').val();
		//var within_group = $('#cbo_within_group').val();
		//alert(1);
		var response=return_global_ajax_value(val, 'check_conversion_rate', '', 'requires/sub_con_wo_entry_controller');
		$('#txt_exchange_rate').val(response);
		$('#txt_exchange_rate').attr('readonly',true);
		change_rate_caption (val);
		//alert(3);
		//$('.rateTd').text('blah') ;
		//alert($('.rateTd').text());
	}

	function change_rate_caption(val)
	{
		if(val==2){
			$('.rateTd').text('Rate (USD)') ;
		}else if(val==3){
			$('.rateTd').text('Rate (EURO)') ;
		}else if(val==4){
			$('.rateTd').text('Rate (CHF)') ;
		}else if(val==5){
			$('.rateTd').text('Rate (SGD)') ;
		}else if(val==6){
			$('.rateTd').text('Rate (Pound)') ;
		}else if(val==7){
			$('.rateTd').text('Rate (Yen)') ;
		}else{
			$('.rateTd').text('Rate (Taka)') ;
		}
		$('.rateTd').css('color','blue');
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
		var response=return_global_ajax_value(itemGroup, 'check_uom', '', 'requires/sub_con_wo_entry_controller');
		$('#cboUom_'+i).val(trim(response));

		var cboBookUom=$('#cboBookUom_'+i).val()*1;
		if(cboBookUom==response)
		{
			$('#txtConvFactor_'+i).val(1);
		}
		else
		{
			$('#txtConvFactor_'+i).val('');
		}
	}

	function load_delivery_date()
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==2)
		{
			var delivery_date=$('#txt_delivery_date').val();
			var row_num = $('#tbl_dtls_emb tbody tr').length;
			var i=''; 
			for(i=1;i<=row_num;i++)
			{
				$('#txtOrderDeliveryDate_'+i).val(trim(delivery_date));
			} 
		}
		else
		{
			return;
		}
	}

	function chk_min_del_date(rowNo)
	{
		var mstDelDate=$('#txt_delivery_date').val();
		var dtlsDelDate=$('#txtOrderDeliveryDate_'+rowNo).val();
		if(mstDelDate=='')
		{
			$('#txt_delivery_date').val(dtlsDelDate);
		}
		else
		{
			var i=''; var otherDtlsDelDate=''; 
			$('#txt_delivery_date').val($('#txtOrderDeliveryDate_1').val());
			for(i=1;i<=$('#tbl_dtls_emb tbody tr').length;i++)
			{
				otherDtlsDelDate=$('#txtOrderDeliveryDate_'+i).val();
				if(otherDtlsDelDate!='')
				{
					if(date_compare( $('#txt_delivery_date').val() , otherDtlsDelDate )==false)
					{
						$('#txt_delivery_date').val(otherDtlsDelDate);
					}
				}
			} 
		}
	}
	
	
	
	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_job_order_entry', 1);
		//reset_form('trims_order_receive','','','cbo_within_group,1*cbo_currency,1*cboUom_1*2',"disable_enable_fields('txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
		reset_form('trims_order_receive','','','cbo_within_group,1*cbo_currency,1*txt_exchange_rate,1*cboUom_1,2*cboBookUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		fnc_load_party(3,1);
		var current_date='<? echo date("d-m-Y"); ?>';
		$('#txt_wo_date').val(current_date);
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#txt_order_no').attr('disabled',false);
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
			var cboSection= $("#"+'cboSection_'+i).val();
			var cboSubSection= $("#"+'cboSubSection_'+i).val();
			var cboItemGroup= $("#"+'cboItemGroup_'+i).val();
			var cboUom= $("#"+'cboUom_'+i).val();
			var cboBookUom= $("#"+'cboBookUom_'+i).val();
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
			
			$('#cboSection_'+row_num).removeAttr("value").attr("value",cboSection);
			$('#cboSubSection_'+row_num).removeAttr("value").attr("value",cboSubSection);
			$('#cboItemGroup_'+row_num).removeAttr("value").attr("value",cboItemGroup);
			$('#cboUom_'+row_num).removeAttr("value").attr("value",cboUom);
			$('#cboBookUom_'+row_num).removeAttr("value").attr("value",cboBookUom);
			
			$('table #row_'+row_num+' #subSectionTd_'+i).removeAttr("id").attr('id','subSectionTd_'+row_num);
			//$( "td:last" ).removeAttr("id").attr('id','subSectionTd_'+row_num);
			$('#txtOrderQuantity_'+row_num).removeAttr("value").attr("value","");
			$('#txtRate_'+row_num).removeAttr("value").attr("value","");
			$('#txtAmount_'+row_num).removeAttr("value").attr("value","");
			$('#hdnDtlsdata_'+row_num).removeAttr("value").attr("value","");
			
			$('#cboItemGroup_'+row_num).removeAttr("onChange").attr("onChange","load_uom("+row_num+")");
			$('#cboSection_'+row_num).removeAttr("onChange").attr("onChange","load_sub_section("+row_num+")");
			$('#cboSubSection_'+row_num).removeAttr("onChange").attr("onChange","load_sub_section_value("+row_num+")");
			$('#txtConvFactor_'+row_num).removeAttr("onkeyup").attr("onkeyup","cal_booked_qty("+row_num+")");
			$('#txtOrderDeliveryDate_'+row_num).removeAttr("onChange").attr("onChange","chk_min_del_date("+row_num+")");

			$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");
			$('#hdnbookingDtlsId_'+row_num).removeAttr("value").attr("value","");
			$('#txtOrderQuantity_'+row_num).removeAttr("onClick").attr("onClick","openmypage_order_qnty("+row_num+")");

			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
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
	
	function load_sub_section(rowNo)
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			var section=$('#cboSection_'+rowNo).val();
			var row_num = $('#tbl_dtls_emb tbody tr').length;
			for(i=rowNo;i<=row_num;i++)
			{
				$('#cboSection_'+i).val(section);
				load_drop_down( 'requires/sub_con_wo_entry_controller',section+'_'+i , 'load_drop_down_subsection', 'subSectionTd_'+i );
			}
		}
		else
		{
			var section=$('#cboSection_'+rowNo).val();
			load_drop_down( 'requires/sub_con_wo_entry_controller',section+'_'+rowNo , 'load_drop_down_subsection', 'subSectionTd_'+rowNo );
		}
		load_sub_section_value(rowNo);
	}

	function load_sub_section_value(rowNo)
	{
		var within_group = $('#cbo_within_group').val();
		var section=$('#cboSection_'+rowNo).val();
		var sub_section=$('#cboSubSection_'+rowNo).val();
		var company=$('#cbo_company_name').val();
		var order_uom=$('#cboUom_'+rowNo).val()*1;
		var response=return_global_ajax_value(company+'_'+section+'_'+sub_section, 'check_booked_uom', '', 'requires/sub_con_wo_entry_controller');
		response=trim(response);
		if(response=='')
		{
			response=0;
		}
		$('#cboBookUom_'+rowNo).val(response);

		var cboUom=$('#cboUom_'+rowNo).val()*1;
		if(cboUom==response)
		{
			$('#txtConvFactor_'+rowNo).val(1);
			$('#txtConvFactor_').attr('disabled',true);
		}
		else
		{
			$('#txtConvFactor_'+rowNo).val('');
			$('#txtConvFactor_').attr('disabled',false);
		}

		if(within_group==1)
		{
			var row_num = $('#tbl_dtls_emb tbody tr').length;
			for(i=rowNo;i<=row_num;i++)
			{
				$('#cboSubSection_'+i).val(sub_section);
				$('#cboBookUom_'+i).val(response);
			}
			cal_booked_qty(rowNo);
		}
	}

	function cal_booked_qty(rowNo)
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			var order_uom=0; var cboBookUom=0; var txtOrderQuantity=0;
			var row_num = $('#tbl_dtls_emb tbody tr').length;
			var txtConvFactor=$('#txtConvFactor_'+rowNo).val()*1;
			for(i=rowNo;i<=row_num;i++)
			{
				order_uom=$('#cboUom_'+i).val()*1;
				cboBookUom=$('#cboBookUom_'+i).val()*1;
				txtOrderQuantity=$('#txtOrderQuantity_'+i).val()*1;
				if(order_uom==cboBookUom)
				{
					$('#txtConvFactor_'+i).val(1);
					$('#txtBookQty_'+i).val(txtOrderQuantity.toFixed(4));
				}
				else
				{
					$('#txtConvFactor_'+i).val(txtConvFactor);
					$('#txtBookQty_'+i).val((txtOrderQuantity*txtConvFactor).toFixed(4));
				}
			}
		}
		else
		{
			var order_uom=$('#cboUom_'+rowNo).val()*1;
			var cboBookUom=$('#cboBookUom_'+rowNo).val()*1;
			var txtOrderQuantity=$('#txtOrderQuantity_'+rowNo).val()*1;
			if(order_uom==cboBookUom)
			{
				$('#txtConvFactor_'+rowNo).val(1);
				$('#txtBookQty_'+rowNo).val(txtOrderQuantity.toFixed(4));
			}
			else
			{
				var txtConvFactor=$('#txtConvFactor_'+rowNo).val()*1;
				$('#txtBookQty_'+rowNo).val((txtOrderQuantity*txtConvFactor).toFixed(4));
			}
		}
	}

	function btn_load_change_bookings2()
	{
		var count = trim(return_global_ajax_value("", 'btn_load_change_bookings', '', 'requires/sub_con_wo_entry_controller'));
		if(count > 0){
			$("#list_change_booking_nos").html("<span id='btn_span' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_bookings()' type='button' class='formbutton' value='&nbsp;&nbsp;Revised Bookings&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Revised Booking List'></span>");
		}else
		{
			$("#list_change_booking_nos").html("<span id='btn_span_disabled' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' type='button' class='formbutton_disabled' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#ccc !important; background-image:none !important;border-color: #ccc;' title='Revised Booking List'></span>");
		}
		(function blink() {
			$('#btn_span').fadeOut(900).fadeIn(900, blink);
		})();
	}

	function show_change_bookings() {
		show_list_view('', 'show_change_bookings', 'list_change_booking_nos', 'requires/sub_con_wo_entry_controller', 'setFilterGrid(\'tbl_list_search_revised\',-1);');
	}
	function set_form_data(data) 
	{
		var data = data.split("**");
		var id = data[0];
		var company_id = data[1];
		var subcon_job = data[2];
		var order_no = data[3];
		load_drop_down( 'requires/sub_con_wo_entry_controller', company_id+'_'+1, 'load_drop_down_location', 'location_td' );
		freeze_window(5);
		get_php_form_data( subcon_job, "load_php_data_to_form", "requires/sub_con_wo_entry_controller" );
		var within_group = $('#cbo_within_group').val();
		show_list_view(2+'_'+order_no+'_'+within_group+'_'+id,'order_dtls_list_view','emb_details_container','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');				
		set_button_status(1, permission, 'fnc_job_order_entry',1);
		$("#last_update").css("visibility", "visible");
		set_all_onclick();
		tr_disabled();
		release_freezing();
	}

	function apply_last_update()
	{
		//freeze_window(5);
		var txt_order_no = $('#txt_order_no').val();
		var hid_order_id = $('#hid_order_id').val();
		var cbo_currency = $('#cbo_currency').val();
		var txt_is_sample = $('#txt_is_sample').val();
		var update_id = $('#update_id').val();
		
		/*$('#txt_order_no').val(ex_data[1]);
		$('#hid_order_id').val(ex_data[0]);
		$('#cbo_currency').val(ex_data[2]);
		$('#txt_is_sample').val(ex_data[3]);*/
		fnc_exchange_rate();
		/*$('#cbo_company_name').attr('disabled',true);
		$('#cbo_within_group').attr('disabled',true);
		$('#cbo_party_name').attr('disabled',true);
		$('#cbo_currency').attr('disabled',true);
		$('#txt_exchange_rate').attr('disabled',true);*/
		var exchange_rate = $('#txt_exchange_rate').val();
		var within_group = $('#cbo_within_group').val();
		//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/sub_con_wo_entry_controller" );
		show_list_view(hid_order_id+'_'+txt_order_no+'_'+cbo_currency+'_'+exchange_rate+'_'+txt_is_sample+'_'+update_id,'order_dtls_list_view_last_update','emb_details_container','requires/sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)');
		var min_dates=$("#min_date_id").val();
		//alert(min_dates);
		$("#txt_delivery_date").val(min_dates);
		$("#is_apply_last_update").val(1);
		$("#last_update").attr('disabled',true);
		tr_disabled();
		set_all_onclick();
	}
	
	function tr_disabled()
	{
		var numRow = $('table#tbl_dtls_emb tbody tr').length;
		var is_revised='';
		for (var i=0;i<numRow; i++)
		{
			is_revised = $("#txtIsRevised_"+i).val();
			if(is_revised==2)
			{
				$("#row_"+i).find("input,button,textarea,select").attr("disabled", true);
			}
		} 
		//$("tr.statuscheck input, tr.statuscheck select").prop('disabled', true);
		//$("tr.statuscheck input, tr.statuscheck select, tr.statuscheck textarea").prop('disabled', false);
	}
	
</script>
</head>
<body onLoad="set_hotkey();"> <!--//btn_load_change_bookings();-->
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="trims_order_receive" id="trims_order_receive" autocomplete="off"> 
			<fieldset style="width:855px;">
			<legend>Trims Order Receive</legend>
			<div style="width:850px; float:left;" align="center">
                <table width="800" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>WO No.</strong></td>
                        <td colspan="3">
                            <input class="text_boxes"  type="text" name="txt_wo_no" id="txt_wo_no" onDblClick="openmypage_wo();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_con_wo_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/sub_con_wo_entry_controller',this.value, 'load_supplier_dropdown', 'supplier_td' );"); ?>
                        </td>
                        <td width="110" class="must_entry_caption">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Pay Mode</td>
                        <td><? echo create_drop_down("cbo_paymode_id", 151, $pay_mode, '', 1, '-Select-', 0, "", 0, ''); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Supplier</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" ); ?></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"fnc_exchange_rate(this.value)", 0,"" ); ?></td>
                        <td class="must_entry_caption">Exchange Rate</td>
                        <td><input name="txt_exchange_rate"  id="txt_exchange_rate" type="text"  class="text_boxes" value="1" style="width:140px" readonly /></td>
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">WO Date</td>
                        <td><input type="text" name="txt_wo_date"  style="width:140px"  id="txt_wo_date" class="datepicker" value="<? echo date("d-m-Y"); ?>" /></td>
                        <td  class="must_entry_caption">Source</td>
                        <td><? $source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
                        	echo create_drop_down( "cbo_source_id", 151, $source_for_order,'', 0, '',2,1, 1); ?></td>
                    	<td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" onChange="load_delivery_date()" /></td>
                    </tr>
                    <tr>	
                    	<td>Attention</td>
                    	<td ><input name="txt_attention" id="txt_attention" type="text"  class="text_boxes" style="width:140px" /></td>
                    	<td class="must_entry_caption"><strong>Order Received NO</strong></td>
                    	<td><input class="text_boxes"  type="text" name="txt_ord_rec_no" id="txt_ord_rec_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly />
                    		<input class="text_boxes"  type="hidden" name="txt_ord_rec_id" id="txt_ord_rec_id"  readonly /></td>
                    		<input class="text_boxes"  type="hidden" name="txt_prev_ord_rec_id" id="txt_prev_ord_rec_id"  readonly /></td>
                    	<td>Terms and Condition</td>
                        <td>
	                        <? 
	                        include("../../terms_condition/terms_condition.php");
	                        terms_condition(450,'update_id','../../');
	                        ?>
                        </td>
                    </tr> 
                    <tr>
                    	<td>Delivery To</td>
                    	<td><input name="txt_delivery_to" id="txt_delivery_to" type="text"  class="text_boxes" style="width:140px" /></td>
                    	<td>Remarks</td>
                    	<td colspan="5"><input name="txt_remarks" id="txt_remarks" type="text"  class="text_boxes" style="width:385px" /></td>
                    </tr> 
                </table>
            </div>
        </fieldset>
        <fieldset style="width:1150px;">
           <legend>Work Order Details</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<tr>
                    		<th width="150" class="must_entry_caption">Section</th>
	                        <th width="150" >Sub Section</th>
	                        <th width="150" class="must_entry_caption">Trims Group</th>
	                        <th width="100" class="must_entry_caption">Order UOM</th>
	                        <th width="110" class="must_entry_caption">Order Qty</th>
	                        <th width="110" class="must_entry_caption rateTd">Rate (Taka)</th>
	                        <th width="110" class="must_entry_caption">Amount</th>
	                        <th width="200" >Remarks</th>
                    	</tr>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                            <td><? echo create_drop_down( "cboSection_1", 150, $trims_section,"", 1, "-- Select Section --","","load_sub_section(1)",0,'','','','','','',"cboSection[]"); ?></td>
                            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 150, $trims_sub_section,"", 1, "-- Select Sub-Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
                            <td><? echo create_drop_down( "cboItemGroup_1", 150, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "load_uom(1)",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
                            <td><? echo create_drop_down( "cboUom_1", 100, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:97px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
                            
                            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:97px" readonly /></td>
                            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:97px"  class="text_boxes_numeric" readonly /></td> 
                            <td><input id="txtRemarks_1" name="txtRemarks[]" type="text" style="width:187px"  class="text_boxes" readonly /></td> 
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="txtDeletedId[]" id="txtDeletedId_1">
                                <input type="hidden" name="txtIsWithOrder[]" id="txtIsWithOrder_1" value="0">
                                <input type="hidden" id="hdnBalance_1" name="hdnBalance[]" value="">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table width="1100" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="14" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,1,"fnResetForm();",1); ?>
                        	<!--<input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();"
						style="visibility: hidden; width:120px"/>-->
							<input type="hidden" name="update_id" id="update_id" readonly>
							<input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" readonly />
                            <input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
                            <input type="hidden" name="txt_revise_no" id="txt_revise_no" value="0">
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>