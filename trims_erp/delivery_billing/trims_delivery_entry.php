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
echo load_html_head_contents("Trims Delivery Info", "../../", 1,1, $unicode,1,'');
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][208] );
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	
	echo "var field_level_data= ". $data_arr . ";\n";

	//echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][208]) . "';\n";
	//echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][208]) . "';\n";

	?>
	var cust_location = [<? echo substr(return_library_autocomplete( "select cust_location  from trims_delivery_mst where  status_active=1 and is_deleted=0 group by cust_location", "cust_location" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
        $("#txt_cust_location").autocomplete({
			source: cust_location
		});
    });	
	function openmypage_devivery_workorder()
	{
		if ( form_validation('cbo_company_name*cbo_source','Company*Source')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_source').value;
		page_link='requires/trims_delivery_entry_controller.php?action=devivery_workorder_popup&data='+data;
		title='Work Order';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				if(ex_data[2]==1) // Check Finish Trims 
				{
					get_php_form_data( ex_data[0]+'_'+ex_data[3], "load_php_rcv_data_to_form", "requires/trims_delivery_entry_controller" );  //Finish Trims
				}else{
					get_php_form_data( ex_data[1], "load_php_data_to_form", "requires/trims_delivery_entry_controller" ); 
				}
				if(ex_data[3]==2 || ex_data[3]==7){
					document.getElementById('is_fabric_trims').value = (ex_data[2]);
				}
				document.getElementById('is_transfer_trims').value = (ex_data[4]);
				var within_group = $('#cbo_within_group').val();
				var received_id =$("#received_id").val();
				var company_name =$("#cbo_company_name").val();
				//alert(received_id);
				show_list_view(1+'_'+ex_data[1]+'_'+within_group+'_'+received_id+'_'+company_name+'_'+ex_data[2]+'_'+ex_data[3]+'_'+ex_data[4],'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');	

				set_button_status(0, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

function show_print_report(type)
{
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var action='';
		var show_color=0;
		if(type==1){
			action='challan_print2';
		}
		else if(type==2){
			action='challan_print3';
		}
		else if(type==3){
			action='challan_print4';
		}
		else if(type==4)
		{
			if(confirm("Show With Color"))
			{
				show_color=1;
			}
			action='challan_print5';
		}
		else if(type==5){
            action='challan_print6';
        }
		else if(type==6)
		{
			if(confirm("Show With Color"))
			{
				show_color=1;
			}
			action='challan_print7';
		}
		else if(type==7)
		{
			if(confirm("Show With Color"))
			{
				show_color=1;
			}
			action='challan_print8';
		}
		else if(type==8)
		{
			action='challan_print_9';
		}
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_dalivery_no').val()+'*'+$('#cbo_template_id').val()+'*'+$('#is_fabric_trims').val()+'*'+show_color, action, "requires/trims_delivery_entry_controller") 
		return;
	}
}
	function fnc_job_order_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_dalivery_no').val()+'*'+$('#cbo_template_id').val()+'*'+$('#no_copy').val(), "challan_print", "requires/trims_delivery_entry_controller") 
			//return;
			show_msg("3");
		}
		else
		{
			var txt_wo_type 			= $('#txt_wo_type').val();
			if(txt_wo_type!=1)
			{
				if ( form_validation('cbo_company_name*cbo_location_name*cbo_within_group*txt_delivery_date*txt_order_no*cbo_currency','Company*location name*Within Group*Party*Delivery Date*Work Order')==false )
				{
					return;
				}
			}else{
				if ( form_validation('cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*txt_delivery_date*txt_order_no*cbo_currency','Company*location name*Within Group*Party*Delivery Date*Work Order')==false )
				{
					return;
				}
			}

			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][208]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][208]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][208]);?>')==false)
				{

					return;
				}
		    }
			
			
			var delete_master_info=0; var i=0;
			//var process = $("#cbo_process_name").val();
			var cbo_within_group = $("#cbo_within_group").val();
			var txt_dalivery_no 	= $('#txt_dalivery_no').val();
			var cbo_company_name 	= $('#cbo_company_name').val();
			var cbo_location_name 	= $('#cbo_location_name').val();
			var cbo_within_group 	= $('#cbo_within_group').val();
			var cbo_party_name 		= $('#cbo_party_name').val();
			var cbo_party_location 	= $('#cbo_party_location').val();
			var cbo_deli_party_name = $('#cbo_deli_party_name').val();
			var cbo_deli_party_location = $('#cbo_deli_party_location').val();
			var cbo_currency 		= $('#cbo_currency').val();
			var txt_challan_no 		= $('#txt_challan_no').val();
			var txt_cust_location 		= $('#txt_cust_location').val();
			var txt_delivery_date 	= $('#txt_delivery_date').val();
			var txt_gate_pass_no 	= $('#txt_gate_pass_no').val();
			var txt_order_no 		= $('#txt_order_no').val();
			var hid_order_id 		= $('#hid_order_id').val();
			var update_id 			= $('#update_id').val();
			
			var received_id 		= $('#received_id').val();
			var order_received_id 		= $('#order_received_id').val();
			var txt_receive_basis 		= $('#txt_receive_basis').val();
			var is_fabric_trims 		= $('#is_fabric_trims').val();
			var is_transfer_trims 		= $('#is_transfer_trims').val();
			//var cboshipingStatus 	= $('#cboshipingStatus').val();
			var txt_remarks 		= $('#txt_remarks').val();
			//var txt_wo_type 			= $('#txt_wo_type').val();
			//var txt_deleted_id 	= $('#txt_deleted_id').val();
			
			var j=1; var i=1; var total_row=0; var check_field=0; data_all="";
			//$("#tbl_dtls_emb tbody tr").each(function()
			$("#tbl_dtls_emb").find('tbody tr').each(function() 
			{
				var txtCurQty 				= $(this).find('input[name="txtCurQty[]"]').val();
				var noOfRollBag 			= $(this).find('input[name="noOfRollBag[]"]').val();
				var txtRemarksDtls 			= $(this).find('input[name="txtRemarksDtls[]"]').val();
				var cboStatus 				= $(this).find('select[name="cboStatus[]"]').val();
				var hdnDtlsUpdateId 		= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				var hdn_break_down_id 		= $(this).find('input[name="hdn_break_down_id[]"]').val();
				var cboshipingStatus 		= $('#cboshipingStatus_'+i).attr('title');
				var txtWorkOrderQuantity 	= $('#txtWorkOrderQuantity_'+i).attr('title');
				var txtJobQuantity     	= $('#txtJobQuantity_'+i).attr('title');
				var txtOrderQuantity 		= $('#txtOrderQuantity_'+i).attr('title');
				var txtPrevQty				= $('#txtPrevQty_'+i).attr('title');
				var txtDelvBalance 			= $('#txtDelvBalance_'+i).attr('title');

			
					
				if(txt_wo_type==1){
					var txtBuyerPO 		= $('#txtBuyerPO_'+i).attr('title');
					var txtBuyerStyle 	= $('#txtBuyerStyle_'+i).attr('title');
					var txtSection 		= $('#txtSection_'+i).attr('title');
					var txtTrimGroup 	= $('#txtTrimGroup_'+i).attr('title');
					var txtUom 			= $('#txtUom_'+i).attr('title');
					var txtStyle 		= $('#txtStyle_'+i).attr('title');
					var txtDesc 		= $('#txtDesc_'+i).attr('title');
					var txtColor 		= $('#txtColor_'+i).attr('title');
					var txtSize 		= $('#txtSize_'+i).attr('title');
					var hdn_rcv_basis 	= $(this).find('input[name="hdn_rcv_basis[]"]').val();
					if((txtCurQty!='' && txtCurQty!=0) || hdnDtlsUpdateId !='')
					{
						data_all += "&txtCurQty_" + j + "='" + txtCurQty+"'&noOfRollBag_" + j + "='" + noOfRollBag+"'&txtRemarksDtls_" + j + "='" + txtRemarksDtls +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&cboStatus_" + j + "='" + cboStatus+"'&hdn_break_down_id_" + j + "='" + hdn_break_down_id +"'&cboshipingStatus_" + j + "='" + cboshipingStatus  +"'&txtWorkOrderQuantity_" + j + "='" + txtWorkOrderQuantity  +"'&txtOrderQuantity_" + j + "='" + txtOrderQuantity  +"'&txtPrevQty_" + j + "='" + txtPrevQty  +"'&txtDelvBalance_" + j + "='" + txtDelvBalance  +"'&txtBuyerPO_" + j + "='" + txtBuyerPO  +"'&txtBuyerStyle_" + j + "='" + txtBuyerStyle  +"'&txtSection_" + j + "='" + txtSection  +"'&txtTrimGroup_" + j + "='" + txtTrimGroup  +"'&txtStyle_" + j + "='" + txtStyle  +"'&txtDesc_" + j + "='" + txtDesc+"'&txtUom_" + j + "='" + txtUom  +"'&txtColor_" + j + "='" + txtColor  +"'&txtSize_" + j + "='" + txtSize  +"'&hdn_rcv_basis_" + j + "='" + hdn_rcv_basis+"'&txtJobQuantity_" + j + "='" + txtJobQuantity+ "'";
						j++; total_row++;
					}
				}
				 else{

					if((txtCurQty!='' && txtCurQty!=0) || hdnDtlsUpdateId !='')
					{
						data_all += "&txtCurQty_" + j + "='" + txtCurQty+"'&noOfRollBag_" + j + "='" + noOfRollBag+"'&txtRemarksDtls_" + j + "='" + txtRemarksDtls +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&cboStatus_" + j + "='" + cboStatus+"'&hdn_break_down_id_" + j + "='" + hdn_break_down_id +"'&cboshipingStatus_" + j + "='" + cboshipingStatus  +"'&txtWorkOrderQuantity_" + j + "='" + txtWorkOrderQuantity  +"'&txtOrderQuantity_" + j + "='" + txtOrderQuantity  +"'&txtPrevQty_" + j + "='" + txtPrevQty  +"'&txtDelvBalance_" + j + "='" + txtDelvBalance+"'&txtJobQuantity_" + j + "='" + txtJobQuantity+ "'"; 
						j++; total_row++;
					}
				}
			   
				i++;
			});	
			
		}

		if(check_field==0) 
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+'&txt_dalivery_no='+txt_dalivery_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_deli_party_name='+cbo_deli_party_name+'&cbo_deli_party_location='+cbo_deli_party_location+'&cbo_currency='+cbo_currency+'&txt_challan_no='+txt_challan_no+'&txt_delivery_date='+txt_delivery_date+'&txt_gate_pass_no='+txt_gate_pass_no+'&received_id='+received_id+'&order_received_id='+order_received_id+'&txt_receive_basis='+txt_receive_basis+'&is_fabric_trims='+is_fabric_trims+'&is_transfer_trims='+is_transfer_trims+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&update_id='+update_id+'&txt_remarks='+txt_remarks+'&txt_cust_location='+txt_cust_location+'&txt_wo_type='+txt_wo_type+data_all;
			//alert (data_all); //return;  
			freeze_window(operation);
			http.open("POST","requires/trims_delivery_entry_controller.php",true);
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
			//alert(response[1]);
			if(response[0]*1==40*1){
				alert(response[1]);
				release_freezing(); return;	
			}else if(response[0]*1==18*1){
				alert(response[1]);
				release_freezing(); return;
			}

			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_dalivery_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				var within_group = $('#cbo_within_group').val();
				if(within_group==2){
					document.getElementById('txt_order_no').value = response[3];
				}
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				//var received_id = $('#received_id').val();
				var company_name = $('#cbo_company_name').val();
				var txt_receive_basis 		= $('#txt_receive_basis').val();
				var is_fabric_trims 		= $('#is_fabric_trims').val();
				var is_transfer_trims 		= $('#is_transfer_trims').val();
				show_list_view(2+'_'+response[2]+'_'+within_group+'_'+response[4]+'_'+company_name+'_'+is_fabric_trims+'_'+txt_receive_basis+'_'+is_transfer_trims,'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');
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
		if(process == 2 || process == 3 || process == 4){
			//$("#cbo_uom").val(12);
		}else{
			//$("#cbo_uom").val(2);
		}
		$('#cboUom_'+inc).attr('disabled',true);
		load_drop_down( 'requires/trims_delivery_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
	}

	function fnc_load_delivery_com(company,within_group){

		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}

		load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_delivery_com', 'delivery_td' );

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
		var deli_party_name = $('#cbo_deli_party_name').val();
		
		if(within_group==1 && type==1){
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}else if(within_group==2 && type==1){
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}else if(within_group==1 && type==2){
			load_drop_down( 'requires/trims_delivery_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
		else if(within_group==1 && type==3)
		{
			load_drop_down( 'requires/trims_delivery_entry_controller', deli_party_name+'_'+3, 'load_drop_down_location', 'dparty_location_td' ); 
			//$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
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
	}

	function openmypage_delivery()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_delivery_entry_controller.php?action=delivery_popup&data='+data;
		title='Trims Delivery Pop-up';
		

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
				var receive_basis = $('#txt_receive_basis').val();
				var is_fabric_trims = $('#is_fabric_trims').val();
				var is_transfer_trims = $('#is_transfer_trims').val();
				//alert(ex_data[2]);
				show_list_view(2+'_'+ex_data[0]+'_'+within_group+'_'+ex_data[2]+'_'+company_name+'_'+is_fabric_trims+'_'+receive_basis+'_'+is_transfer_trims,'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				//cal_values(0);
				release_freezing();
			}
		}
	}

	function fn_trims_del_print()
	{
		if( form_validation('cbo_company_name*cbo_party_name*txt_dalivery_no','Company Name*Party*System ID')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var title='Trims Delivery Pop-up';
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/trims_delivery_entry_controller.php?action=del_multi_number_popup&data='+data; 
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var delNumber=this.contentDoc.getElementById("hidden_del_number").value; // mrr number
			var delId=this.contentDoc.getElementById("hidden_del_id").value; // mrr number
			var ordId=this.contentDoc.getElementById("hidden_ord_number").value; // order number
			var report_title=$( "div.form_caption" ).html();
			delId=delId+','+$("#update_id").val();
			//alert(delId);
			print_report( $('#cbo_company_name').val()+'*'+delId+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+ordId+'*'+delNumber+'*'+$('#cbo_template_id').val(), "multi_del_print", "requires/trims_delivery_entry_controller") ;
			return;
		}
	}

	function cal_values(rowNo)
	{
		var balance 		='';
		var update_id 		= $('#update_id').val()*1;
		//alert(rowNo);
		if(rowNo==0)
		{
			var numRow = $('table#tbl_dtls_emb tbody tr').length;
			//alert(numRow); //return;
			for (var i=1;i<=numRow; i++)
			{
				//alert(i);
				var txtCurQty 				= $('#txtCurQty_'+i).val()*1;
				
				
				if (txtCurQty =='')
				{
						txtCurQty=0;
				}
				  
				var txtWoRate 	= $('#txtWoRate_'+i).attr('title'); 
				$('#txtWoAmaount_'+i).html(number_format_common((txtCurQty*txtWoRate),"","",1));
				//var txtWorkOrderQuantity 	= $('#txtWorkOrderQuantity_'+i).html()*1;
				var txtPrevQty 				= $('#txtPrevQty_'+i).html()*1;
				if (txtPrevQty ==''){
						txtPrevQty=0;
					}
				var txtOrderQuantity 		= $('#txtOrderQuantity_'+i).html()*1;
				var txtWorkOrderQuantity 	= $('#txtOverWOquantity_'+i).val()*1;
				var txtActualWOQuantity 	= $('#txtWorkOrderQuantity_'+i).html()*1;
				//var txtdeliverableQuantity 		= $('#txtdeliverableQuantity_'+rowNo).html()*1;
				var txtdeliverableQuantity 	= $('#txtdeliverableQuantity_'+rowNo).attr('title');
				var overQty=txtActualWOQuantity-txtWorkOrderQuantity;
				
				//alert(txtWorkOrderQuantity+'=='+txtCurQty+'=='+txtPrevQty);
				//$('#txtDelvBalance_'+i).html((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)).toFixed(2));
				$('#txtDelvBalance_'+i).html(number_format_common((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)),"","",1));
				var txtDelvBalance 	= $('#txtDelvBalance_'+i).html()*1;

				var totaldoqnty 	=(txtCurQty+txtPrevQty);
				//balance 			=(txtOrderQuantity-totaldoqnty);
				balance 			=(txtdeliverableQuantity-totaldoqnty);
				//alert(balance+'=balance');
				if(balance<0){
					//alert("Delv. Qty not more then prod. Qty. Balance="+ txtOrderQuantity+'=='+totaldoqnty+'=='+txtCurQty+'=='+txtPrevQty);
					//Delv. Qty not more then prod. Qty. Balance=27.5==54==54==0
					alert("Delv. Qty can not be greater then Prod. Qty");
					$('#txtCurQty_'+i).val('');
					//$('#txtDelvBalance_'+i).val((txtWorkOrderQuantity-txtPrevQty).toFixed(2));
					$('#txtDelvBalance_'+i).html(number_format_common((txtWorkOrderQuantity-txtPrevQty),"","",1));
					return;
				}

				if(txtDelvBalance==0 || (txtDelvBalance <= overQty)){
					$('#cboshipingStatus_'+i).html('Full Deliverd');
					$('#cboshipingStatus_'+i).attr('title','3');
				}else if(txtDelvBalance>0 && txtDelvBalance!=totaldoqnty){
					$('#cboshipingStatus_'+i).html('Partial Deliverd');
					$('#cboshipingStatus_'+i).attr('title','2');
				}else if(txtDelvBalance>0 && txtDelvBalance==txtActualWOQuantity){
					$('#cboshipingStatus_'+i).html('Full Pending');
					$('#cboshipingStatus_'+i).attr('title','1');
				}else if(txtDelvBalance<0 && txtCurQty>txtActualWOQuantity){
					$('#cboshipingStatus_'+i).html('Full Pending');
					$('#cboshipingStatus_'+i).attr('title','1');
				} 
			}
		}
		else
		{
			//alert(1111);
			var txtCurQty 				= $('#txtCurQty_'+rowNo).val()*1;
		    var txtWoRate 	= $('#txtWoRate_'+rowNo).attr('title'); 
			$('#txtWoAmaount_'+rowNo).html(number_format_common((txtCurQty*txtWoRate),"","",1));
			//var txtWorkOrderQuantity 	= $('#txtWorkOrderQuantity_'+rowNo).html()*1;
			var txtPrevQty 				= $('#txtPrevQty_'+rowNo).html()*1;
			var txtOrderQuantity 		= $('#txtOrderQuantity_'+rowNo).html()*1;
			//var txtdeliverableQuantity 	= $('#txtdeliverableQuantity_'+rowNo).html()*1;
			
			var txtdeliverableQuantity 	= $('#txtdeliverableQuantity_'+rowNo).attr('title');
			
			//alert(txtdeliverableQuantity);
			var txtWorkOrderQuantity 	= $('#txtOverWOquantity_'+rowNo).val()*1;
			var txtActualWOQuantity 	= $('#txtWorkOrderQuantity_'+rowNo).html()*1;
			//var txtDelvBalance 	= $('#txtDelvBalance_'+rowNo).html()*1;
			var overQty=txtActualWOQuantity-txtWorkOrderQuantity;
			//alert(txtCurQty+'=='+txtPrevQty+'=='+txtOrderQuantity+'=='+txtWorkOrderQuantity+'=='+txtActualWOQuantity);
			//alert(txtWorkOrderQuantity+'=='+txtCurQty+'=='+txtPrevQty);
			//$('#txtDelvBalance_'+rowNo).html((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)).toFixed(2));
			//alert(txtPrevQty);
			var delvBalance= txtActualWOQuantity - (txtCurQty+txtPrevQty);
			$('#txtDelvBalance_'+rowNo).html(delvBalance);
			//$('#txtDelvBalance_'+rowNo).html(number_format_common((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)),"","",1).toFixed(4));
			//alert(2+'delvBalance'+delvBalance);
			var txtDelvBalance 	= $('#txtDelvBalance_'+rowNo).html()*1;
			//alert(txtDelvBalance);

			var totaldoqnty 	=(txtCurQty+txtPrevQty);
			//balance 			=(txtOrderQuantity-totaldoqnty);
			balance 			=(txtdeliverableQuantity-totaldoqnty);
			//alert(txtCurQty+'=='+txtPrevQty+'=='+totaldoqnty+'=='+txtOrderQuantity+'=='+balance);
			if(balance<0){
				alert("Delv. Qty can not be greater then Prod. Qty");
				$('#txtCurQty_'+rowNo).val('');
				$('#txtDelvBalance_'+rowNo).html(txtDelvBalance);
				//$('#txtDelvBalance_'+rowNo).val((txtWorkOrderQuantity-txtPrevQty).toFixed(2));
				//$('#txtDelvBalance_'+rowNo).html(number_format_common((txtWorkOrderQuantity-txtPrevQty),"","",1).toFixed(4));
				return;
			}

			if(txtDelvBalance==0 || (txtDelvBalance <= overQty)){
				$('#cboshipingStatus_'+rowNo).html('Full Deliverd');
				$('#cboshipingStatus_'+rowNo).attr('title','3');
			}else if(txtDelvBalance>0 && txtDelvBalance!=totaldoqnty){
				$('#cboshipingStatus_'+rowNo).html('Partial Deliverd');
				$('#cboshipingStatus_'+rowNo).attr('title','2');
			}else if(txtDelvBalance>0 && txtDelvBalance==txtActualWOQuantity){
				$('#cboshipingStatus_'+rowNo).html('Full Pending');
				$('#cboshipingStatus_'+rowNo).attr('title','1');
			}else if(txtDelvBalance<0 && txtCurQty>txtActualWOQuantity){
				$('#cboshipingStatus_'+rowNo).html('Full Pending');
				$('#cboshipingStatus_'+rowNo).attr('title','1');
			} 
		}
		
	}

    function val_roundup(){
        if($('#round_down').is(':checked')){
            $( "input[name='txtCurQty[]']" ).each(function (index){
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val(octal[0]);
            });
        }else{
            $( "input[name='txtCurQty[]']" ).each(function (index){
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }

    function remove_del_val()
    {
    	if($('#remove_del_value').is(':checked'))
    	{
            $( "input[name='txtCurQty[]']" ).each(function (index)
            {
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val('');
            });
        }
        else
        {
            $( "input[name='txtCurQty[]']" ).each(function (index)
            {
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:950px;">
			<legend>Trims Delivery</legend>
                <table width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_dalivery_no" id="txt_dalivery_no" onDblClick="openmypage_delivery();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1,document.getElementById('cbo_within_group').value); load_drop_down( 'requires/trims_delivery_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td');get_php_form_data( this.value, 'company_wise_report_button_setting','requires/trims_delivery_entry_controller');"); ?>
                        </td>
                        <td width="110" class="must_entry_caption">Location Name</td>
                        <td width="160" id="location_td"><? 
                        	echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                         //echo create_drop_down( "cbo_location_name", 150, "select id, location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);fnc_load_delivery_com(document.getElementById('cbo_company_name').value,this.value);" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>

                        <td id="td_deli_party">Delivery Company</td>
                        <td id="delivery_td"><? echo create_drop_down( "cbo_deli_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Del.Company--", $selected, "load_drop_down( 'requires/trims_delivery_entry_controller', this.value+'_'+3, 'load_drop_down_location', 'dparty_location_td'); fnc_load_party(3,document.getElementById('cbo_within_group').value);"); ?></td>
                        
                    </tr> 
                    <tr>
                    	<td id="td_dparty_location">Del.Party Location</td>
                        <td id="dparty_location_td"><? echo create_drop_down( "cbo_deli_party_location", 150, $blank_array,"", 1, "--Select Location--", $selected, "",1 ); ?></td>
                    	<td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:138px"  class="datepicker" value="<? echo Date('d-m-Y'); ?>"  /></td>
                        <td class="must_entry_caption" ><strong>Source</strong></td>
                    	<td><? $source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
                            echo create_drop_down( "cbo_source", 150, $source_for_order,"", 0, "-- Select --",1,1, 0,'','','','','','',"cboSource[]"); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" ><strong>Work Order</strong></td>
                        <td ><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_devivery_workorder();" readonly />
                        </td>
                        <td >Challan No</td>
                        <td ><input name="txt_challan_no" id="txt_challan_no" type="text"  class="text_boxes" style="width:140px" />
                        </td>

                    	<td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"fnc_exchange_rate()", 1,"" ); ?></td>
                    </tr> 
                    <tr>
                    	<td style="display:none">Gate Pass No</td>
                        <td style="display:none"><input name="txt_gate_pass_no" id="txt_gate_pass_no" type="text"  class="text_boxes" style="width:140px" /> </td>
                    	<td >Remarks</td>
                        <td><input name="txt_remarks" id="txt_remarks" type="text"  class="text_boxes" style="width:140px" />
                        </td>
                        <td>Terms and Condition</td>
                        <td>
	                        <? 
	                        include("../../terms_condition/terms_condition.php");
	                        terms_condition(208,'update_id','../../');
	                        ?>
                        </td>
						<td class="must_entry_caption">Cust. Location</td>
                        <td ><input name="txt_cust_location" id="txt_cust_location" type="text"  class="text_boxes" style="width:140px" />
                        </td>
                        <!-- <td style="display: none;">Delivery Status</td>
                        <td style="display: none;"><?php echo create_drop_down( "cboshipingStatus", 150, $delivery_status,"", 0, "--  --", 0, "" ); ?></td> -->
                    </tr>
                </table>
        </fieldset> 			
        <fieldset style="width:1680px;">
           <legend>Trims Delivery Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<th width="90" style="display:none">Work Order</th>
                        <th width="110" id="buyerpo_td">Buyer's PO</th>
                        <th width="110" id="buyerstyle_td">Buyer's Style Ref.</th>
                        <th width="90">Section</th>
                        <th width="110" id="buyerbuyer_td" style="display:none">Buyer's Buyer </th>
                        <th width="90">Trims Group</th>
                        <th width="200">Style</th>
                        <th width="100">Item Description</th>
						<th width="70">Gmts Color</th>
						<th width="60">Gmts Size</th>
                    	<th width="70">Item Color</th>
						<th width="60">Item Size</th>
                    	<th width="70">Ply</th>
                        <th width="60">Order UOM</th>
                        <th width="70">WO Qty</th>
                        <th width="70">Job Qty</th>
                        <th width="70" class="must_entry_caption">Prod. Qty</th>
                        <th width="70">Deliverable Qty</th>
                        <th width="70">Cum. Delv Qty</th>
                        <th width="80">
                            <input type="checkbox" name="round_down" onClick="val_roundup();" id="round_down" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <input type="checkbox" name="remove_del_value" onClick="remove_del_val();" id="remove_del_value" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <hr style="padding: 2px 0px;">
                            Curr. Delv Qnty
                        </th>
                        <th width="80">WO Rate</th>
                        <th width="80">Amount</th>
                        <th width="80">No of Roll/Bag</th>
                        <th width="70" style="display:none">Claim Qnty</th>
                        <th width="70">Delv Balance</th>
                        <th width="80">Delivery Status</th>
                        <th width="90">Remarks</th>
                        <th width="60">Status</th>
                        <th style="display: none;"></th>
                    </thead>
                    <tbody id="emb_details_container">
                        
                    </tbody>
                </table>
                <table width="1410" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="15" valign="middle" class="button_container">
                        	<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,1,"fnResetForm();",1); ?>
                        	<input type="hidden" name="hid_order_id" id="hid_order_id">
                        	<input type="hidden" name="txt_wo_type" id="txt_wo_type">
                            <input type="hidden" name="update_id" id="update_id">
                            <input type="hidden" name="txt_receive_basis" id="txt_receive_basis">
                            <input type="hidden" name="received_id" id="received_id">
                            <input type="hidden" name="order_received_id" id="order_received_id">
                            <input type="hidden" name="is_fabric_trims" id="is_fabric_trims" value="0">
                            <input type="hidden" name="is_transfer_trims" id="is_transfer_trims" value="0">
							<input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:60px;"/>
                            <input type="button" id="btn_print2" value="Print2" class="formbutton" style="width:80px;" onClick="show_print_report(1);" >
                            <input type="button" id="btn_print3" value="Print3" class="formbutton" style="width:80px;" onClick="show_print_report(2);" >
                            <input type="button" id="btn_print4" value="Print4" class="formbutton" style="width:80px;" onClick="show_print_report(3);" >
                            <input type="button" id="btn_print5" value="Print5" class="formbutton" style="width:80px;" onClick="show_print_report(4);" >
                            <input type="button" id="btn_print7" value="Print6" class="formbutton" style="width:80px;" onClick="show_print_report(5);" >
                            <input type="button" id="btn_print8" value="Print7" class="formbutton" style="width:80px;" onClick="show_print_report(6);" >
                            <input type="button" id="btn_print9" value="Print8" class="formbutton" style="width:80px;" onClick="show_print_report(7);" >
                            <input type="button" id="btn_print10" value="Print 9" class="formbutton" style="width:80px;" onClick="show_print_report(8);" >
                            <input type="button" name="print6" value="Print Multi Delivery Challan Number " id="btn_print6" class="formbutton" style="width: 220px;" onClick="fn_trims_del_print()" />
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>