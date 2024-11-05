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
				cal_values();
				release_freezing();
			}
		}
	}

	function fnc_job_order_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_dalivery_no').val()+'*'+$('#cbo_template_id').val(), "challan_print", "requires/trims_delivery_entry_controller") 
			//return;
			show_msg("3");
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_delivery_date*txt_order_no*cbo_currency','Company*Within Group*Party*Delivery Date*Work Order')==false )
			{
				return;
			}
			else
			{
				var delete_master_info=0; var i=0;
				//var process = $("#cbo_process_name").val();
				var cbo_within_group = $("#cbo_within_group").val();
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
				
				var j=1; var check_field=0; data_all="";
					
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
					/*if(txtOrderQuantity==0 || txtOrderQuantity=='' )
					{
						alert('Please fill-up Prod. Qty');
						check_field=1 ; return;
					}*/
					var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
					var txtPrevQty 			= $(this).find('input[name="txtPrevQty[]"]').val();
					var txtCurQty 			= $(this).find('input[name="txtCurQty[]"]').val();
					var txtClaimQty 		= $(this).find('input[name="txtClaimQty[]"]').val();
					var txtRemarksDtls 		= $(this).find('input[name="txtRemarksDtls[]"]').val();
					
					var txtItem 				= $(this).find('input[name="txtItem[]"]').val();
					var txtcolor 				= $(this).find('input[name="txtcolor[]"]').val();
					var txtsize 				= $(this).find('input[name="txtsize[]"]').val();
					var txtWorkOrderQuantity 	= $(this).find('input[name="txtWorkOrderQuantity[]"]').val();
					var txtDelvBalance 			= $(this).find('input[name="txtDelvBalance[]"]').val();
					var cboStatus 				= $(this).find('select[name="cboStatus[]"]').val();
					var cboshipingStatus 		= $(this).find('select[name="cboshipingStatus[]"]').val();

					var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
					var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
					var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
					var hdnReceiveDtlsId 	= $(this).find('input[name="hdnReceiveDtlsId[]"]').val();
					var hdnJobDtlsId 		= $(this).find('input[name="hdnJobDtlsId[]"]').val();
					var hdnProductionDtlsId = $(this).find('input[name="hdnProductionDtlsId[]"]').val();
					var hdn_break_down_rate 	= $(this).find('input[name="hdn_break_down_rate[]"]').val();
					var hdn_break_down_id 		= $(this).find('input[name="hdn_break_down_id[]"]').val();
					
					var txtcolorID 		= $(this).find('input[name="txtcolorID[]"]').val();
					var txtsizeID 		= $(this).find('input[name="txtsizeID[]"]').val();
					
					//alert(txtCurQty);
					if(txtCurQty!='' && txtCurQty!=0)
					{
						data_all += "&txtbuyerPoId_" + j + "='" + txtbuyerPoId + "'&txtWorkOrder_" + j + "='" + txtWorkOrder + "'&txtWorkOrderID_" + j + "='" + txtWorkOrderID + "'&txtbuyerPo_" + j + "='" + txtbuyerPo + "'&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtbuyer_" + j + "='" + txtbuyer + "'&cboSection_" + j + "='" + cboSection + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&txtOrderQuantity_" + j + "='" + txtOrderQuantity + "'&cboUom_" + j + "='" + cboUom + "'&txtPrevQty_" + j + "='" + txtPrevQty + "'&txtCurQty_" + j + "='" + txtCurQty +"'&txtClaimQty_" + j + "='" + txtClaimQty +"'&txtRemarksDtls_" + j + "='" + txtRemarksDtls +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId +"'&hdnReceiveDtlsId_" + j + "='" + hdnReceiveDtlsId+"'&hdnJobDtlsId_" + j + "='" + hdnJobDtlsId+"'&hdnProductionDtlsId_" + j + "='" + hdnProductionDtlsId+"'&txtItem_" + j + "='" + txtItem+"'&txtcolor_" + j + "='" + txtcolor+"'&txtsize_" + j + "='" + txtsize+"'&txtWorkOrderQuantity_" + j + "='" + txtWorkOrderQuantity+"'&txtDelvBalance_" + j + "='" + txtDelvBalance+"'&cboStatus_" + j + "='" + cboStatus+"'&cboshipingStatus_" + j + "='" + cboshipingStatus +"'&txtcolorID_" + j + "='" + txtcolorID+"'&txtsizeID_" + j + "='" + txtsizeID+"'&hdn_break_down_rate_" + j + "='" + hdn_break_down_rate+"'&hdn_break_down_id_" + j + "='" + hdn_break_down_id+ "'";
						j++; i++;
					}
				});	
			}
			
		}

		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_dalivery_no='+txt_dalivery_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_currency='+cbo_currency+'&txt_challan_no='+txt_challan_no+'&txt_delivery_date='+txt_delivery_date+'&txt_gate_pass_no='+txt_gate_pass_no+'&received_id='+received_id+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&update_id='+update_id+'&txt_deleted_id='+txt_deleted_id+'&cboshipingStatus='+cboshipingStatus+'&txt_remarks='+txt_remarks+data_all;
			//alert (data);// return;
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
				release_freezing();
				return;	
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
				show_list_view(2+'_'+response[2]+'_'+within_group+'_'+response[4],'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');
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
		
		if(within_group==1 && type==1){
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}else if(within_group==2 && type==1){
			load_drop_down( 'requires/trims_delivery_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}else if(within_group==1 && type==2){
			load_drop_down( 'requires/trims_delivery_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
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
				show_list_view(2+'_'+ex_data[0]+'_'+within_group+'_'+ex_data[2],'dalivery_order_dtls_list_view','emb_details_container','requires/trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				cal_values();
				release_freezing();
			}
		}
	}


	function cal_values(rowNo)
	{
		var balance='';
		var update_id 		= $('#update_id').val()*1;
		var txtCurQty 		= $('#txtCurQty_'+rowNo).val()*1;
		var txtWorkOrderQuantity = $('#txtWorkOrderQuantity_'+rowNo).val()*1;
		var txtPrevQty 	= $('#txtPrevQty_'+rowNo).val()*1;
		var txtOrderQuantity 	= $('#txtOrderQuantity_'+rowNo).val()*1;
		$('#txtDelvBalance_'+rowNo).val((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)).toFixed(2));
		$('#txtDelvBalance_'+rowNo).val(number_format_common((txtWorkOrderQuantity-(txtCurQty+txtPrevQty)),"","",1));
		var txtDelvBalance 	= $('#txtDelvBalance_'+rowNo).val()*1;
		var totaldoqnty=(txtCurQty+txtPrevQty);
		balance=(txtOrderQuantity-totaldoqnty);
		//alert(balance);
		
		if(txtDelvBalance<=1){
			$('#cboshipingStatus_'+rowNo).val(3);
		}
		if(txtDelvBalance>1 && txtDelvBalance!=totaldoqnty)
		{
			$('#cboshipingStatus_'+rowNo).val(2);
		}if(txtDelvBalance>1 && txtDelvBalance==txtWorkOrderQuantity)
		{
			$('#cboshipingStatus_'+rowNo).val(1);
		}if(txtDelvBalance<0 && txtCurQty>txtWorkOrderQuantity){
			$('#cboshipingStatus_'+rowNo).val(1);
		} 
		
		if(balance<0){
			alert("Delv. Qty not more then prod. Qty");
			$('#txtCurQty_'+rowNo).val('');
			//$('#txtDelvBalance_'+rowNo).val((txtWorkOrderQuantity-txtPrevQty).toFixed(2));
			$('#txtDelvBalance_'+rowNo).val(number_format_common((txtWorkOrderQuantity-txtPrevQty),"","",1));
			return;
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
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:138px"  class="datepicker" value="<? echo Date('d-m-Y'); ?>" disabled /></td>
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
                        
                        <td style="display:none">Gate Pass No</td>
                        <td style="display:none"><input name="txt_gate_pass_no" id="txt_gate_pass_no" type="text"  class="text_boxes" style="width:140px" /> </td>
                    </tr>
                    <tr>
                    	<td >Remarks</td>
                        <td  colspan="5"><input name="txt_remarks" id="txt_remarks" type="text"  class="text_boxes" style="width:686px" />
                        </td>
                        <td style="display: none;">Delivery Status</td>
                        <td style="display: none;"><?php echo create_drop_down( "cboshipingStatus", 150, $delivery_status,"", 0, "--  --", 0, "" ); ?></td>
                    </tr> 
                </table>
        </fieldset> 			
        <fieldset style="width:1170px;">
           <legend>Trims Order Receive Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<th width="90" style="display:none">Work Order</th>
                        <th width="110" id="buyerpo_td">Buyer's PO</th>
                        <th width="90">Section</th>
                        <th width="110" id="buyerstyle_td" style="display:none">Buyer's Style Ref.</th>
                        <th width="110" id="buyerbuyer_td" style="display:none">Buyer's Buyer </th>
                        
                        <th width="90">Trims Group</th>
                        <th width="100">Item Description</th>
                    	<th width="70">Gmts Color</th>
                    	<th width="60">Gmts Size</th>
                        
                        <th width="60">Order UOM</th>
                        <th width="70">WO Qty</th>
                        <th width="70" class="must_entry_caption">Prod. Qty</th>
                        <th width="70">Cum. Delv Qty</th>
                        <th width="80">Curr. Delv Qnty</th>
                        <th width="70" style="display:none">Claim Qnty</th>
                        <th width="70">Delv Balance</th>
                        <th width="80">Delivery Status</th>
                        <th width="90">Remarks</th>
                        <th width="60">Status</th>
                        <th style="display: none;"></th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                        	<td style="display:none">
                            
                            
                            <input id="txtWorkOrder_1" name="txtWorkOrder[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                        	<input id="txtWorkOrderID_1" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                        	</td>
                            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
                            </td>
                            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
                            <td style="display:none"><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                             <td style="display:none"><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
                            
                            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "load_uom(1)",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
                            
                            <td><input id="txtItem_1" name="txtItem[]" type="text" class="text_boxes" style="width:87px" placeholder="Display" readonly disabled/>
                            <td><input id="txtcolor_1" name="txtcolor[]" type="text" class="text_boxes" style="width:57px" placeholder="Display" readonly disabled/>
                            <input id="txtcolorID_1" name="txtcolorID[]" type="hidden" value="" class="text_boxes" style="width:57px" placeholder="Display" readonly disabled /></td>
                            <td><input id="txtsize_1" name="txtsize[]" type="text" class="text_boxes" style="width:57px" placeholder="Display" readonly/>
                            <input id="txtsizeID_1" name="txtsizeID[]" type="hidden" value=""  class="text_boxes" style="width:57px" placeholder="Display" readonly disabled/></td>
                            
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                            <td><input id="txtWorkOrderQuantity_1" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" placeholder="" readonly /></td>
                            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px"  placeholder="" readonly /></td>
                            
                            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly="readonly" /></td>
                            <td><input id="txtCurQty_1" name="txtCurQty[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td style="display:none"><input id="txtClaimQty_1" name="txtClaimQty[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly="readonly" /></td> 
                            <td><input id="txtDelvBalance_1" name="txtDelvBalance[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td>
                            <td>
	                            <? echo create_drop_down( "cboshipingStatus_1", 80, $delivery_status,"", 0, "--  --", 0,"", 1,'','','','','','',"cboshipingStatus[]"); ?>
	                        </td>
                            <td><input id="txtRemarksDtls_1" name="txtRemarksDtls[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly="readonly" />
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_1">
                                <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_1">
                                <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_1">
                                <input type="hidden" name="hdn_break_down_rate[]" id="hdn_break_down_rate_1">
                                <input type="hidden" name="hdn_break_down_id[]" id="hdn_break_down_id_1">
                                
                            </td> 
                            <td><?   echo create_drop_down( "cboStatus_1", 60, $row_status,"",0, $selected,0,'','','','','','','','',"cboStatus[]");?>	</td>
                              
                            <td width="65" style="display: none;">
							<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
							<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
						</td>
                        </tr>
                    </tbody>
                </table>
                <table width="1210" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="13" valign="middle" class="button_container">
                        	<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
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