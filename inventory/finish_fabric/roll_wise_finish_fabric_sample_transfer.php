<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll Wise Finish Fabric Sample Transfer
				
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	11-10-2021
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
echo load_html_head_contents("Roll Wise Grey Fabric Sample Transfer","../../", 1, 1, '','',''); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	// Existing Data bring for updating 
	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

		if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
		{
			return;
		}
		
		var title = 'Item Transfer Info';	
		var page_link = 'requires/roll_wise_finish_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=sampleToOrderTransfer_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
			
			get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/roll_wise_finish_fabric_sample_transfer_controller" );
			show_list_view(transfer_id+"**"+$('#txt_from_order_book_id').val()+"**"+cbo_transfer_criteria,'show_transfer_listview','tbl_details','requires/roll_wise_finish_fabric_sample_transfer_controller','setFilterGrid(\'tbl_details\',-1)');
		}
	} 

	// Sample browsing 
	function from_openmypage(type)
	{
		var cbo_company_id = $('#cbo_company_id').val();

		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var cbo_store_name=$('#cbo_store_name').val();

		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name','Transfer Criteria*Company*From Store')==false)
		{
			return;
		}
		
		if(cbo_transfer_criteria==6) // Order
		{
			var title = 'Order Information';
		}
		else // Sample
		{ 
			var title = 'Sample Information';
		}	
		var page_link = 'requires/roll_wise_finish_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&action=from_order_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function() // return_id is booking or order no
		{
			var theform=this.contentDoc.forms[0];	//("search_order_frm"); //Access the form inside the modal window
			var return_id=this.contentDoc.getElementById("return_id").value; //Access form field with id="emailfield"
			var return_arr =  return_id.split("_");
			var return_booking_order_id = return_arr[0];
			var return_booking_order_dtls_id = return_arr[1];
			var body_part_id = return_arr[2];
			var batch_id = return_arr[3];

			//alert(batch_id);

			/*var sample_id=this.contentDoc.getElementById("sample_id").value;
			var sample_arr =  sample_id.split("_");
			var samp_booking_id = sample_arr[0];
			var samp_booking_dtls_id = sample_arr[1];

			get_php_form_data(samp_booking_dtls_id+"**"+type, "populate_data_to_sample_transfer_from", "requires/grey_fabric_sample_to_sample_roll_transfer_controller" );
			if(type=='from')
			{
				show_list_view(samp_booking_id,'show_dtls_list_view','tbl_details','requires/grey_fabric_sample_to_sample_roll_transfer_controller','');
			}*/

			get_php_form_data(return_booking_order_dtls_id+"**"+type+"**"+cbo_transfer_criteria+"**"+body_part_id+"**"+batch_id, "populate_data_from_sample", "requires/roll_wise_finish_fabric_sample_transfer_controller" ); // return_id
			if(type=='from')
			{
				show_list_view(return_booking_order_id+"**"+cbo_transfer_criteria+"**"+body_part_id+"**"+batch_id+"**"+cbo_store_name,'show_dtls_list_view','tbl_details','requires/roll_wise_finish_fabric_sample_transfer_controller','setFilterGrid(\'tbl_details\',-1)'); // return_id
			}
		}
	}

	// Order browsing 
	function to_openmypage()
	{
		var cbo_company_id_to = $('#cbo_company_id_to').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var txt_from_order_book_no = $('#txt_from_order_book_no').val();

		if (form_validation('cbo_transfer_criteria*cbo_company_id_to*txt_from_order_book_no','Transfer Criteria*To Company*From Order/Sample')==false)
		{
			return;
		}
		
		if(cbo_transfer_criteria==7) // Order
		{
			var title = 'Order Information';
		}
		else // Sample
		{ 
			var title = 'Sample Information';
		}
		var page_link = 'requires/roll_wise_finish_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id_to+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=to_order_popup'+"&txt_from_order_book_no='"+txt_from_order_book_no+"'";
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=955px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var order_id_str=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"

			var order_id_arr =  order_id_str.split("_");
			var order_id = order_id_arr[0];
			var body_part_id = order_id_arr[1];
			var batch_id = order_id_arr[2];
			
			get_php_form_data(order_id+"**"+cbo_transfer_criteria+"**"+body_part_id+"**"+batch_id, "populate_data_to_order", "requires/roll_wise_finish_fabric_sample_transfer_controller" );
			
		}
	}

	// Save/update/print operation 
	function fnc_finish_transfer_entry(operation)
	{
		

		if(operation==4) // print Operation
		{
			// var report_title=$( "div.form_caption" ).html();
			// print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_transfer_criteria').val(), "finish_fabric_order_to_order_transfer_print", "requires/roll_wise_finish_fabric_sample_transfer_controller" ) 
			// return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			
			if(operation==2)
			{
				show_msg('13');
				return;
			}

			if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_book_id*txt_to_order_book_id*cbo_store_name_to*cbo_from_body_part*cbo_to_body_part','Company*Transfer Date*Sample Booking No*To Order No*To Store*From Body Part*To Body Part')==false )
			{
				return;
			}
			
			var txt_from_order_book_no=$('#txt_from_order_book_no').val();
			var txt_to_order_book_no=$('#txt_to_order_book_no').val();
			var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
			
			if(cbo_transfer_criteria == 8 && txt_from_order_book_no ==txt_to_order_book_no)
			{
				alert("From Booking number and To Booking number can not allow to be same.");
				return;
			}
			
	        var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_transfer_date').val(), current_date)==false)
			{
				alert("Transfer Date Can not Be Greater Than Current Date");
				return;
			}

			var store_update_upto=$('#store_update_upto').val()*1;
			var toFloor=$('#cbo_floor_to').val();
			var toRoom=$('#cbo_room_to').val();
			var toRack=$('#txt_rack_to').val();
			var toShelf=$('#txt_shelf_to').val();
			if(store_update_upto > 1)
			{
				if(store_update_upto==5 && (toFloor==0 || toRoom==0 || toRack==0 || toShelf==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (toFloor==0 || toRoom==0 || toRack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && (toFloor==0 || toRoom==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && toFloor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
	                
			var row_num=$('#scanning_tbl tbody tr').length;
			var txt_deleted_id=''; var selected_row=0; var i=0; var data_all=''; var txt_deleted_prod_qty ='';
			
			for (var j=1; j<=row_num; j++)
			{
				var updateIdDtls=$('#dtlsId_'+j).val();
				
				if(updateIdDtls!="" && $('#tbl_'+j).is(':not(:checked)'))
				{
					var transIdFrom=$('#transIdFrom_'+j).val();
					var transIdTo=$('#transIdTo_'+j).val();
					var rolltableId=$('#rolltableId_'+j).val();
					var rollId=$('#rollId_'+j).val();
					var delBarcodeNo=$('#barcodeNo_'+j).val();

					var productId=$('#productId_'+j).val(); 
					var rollWgt=$('#rollWgt_'+j).val();
					var fromProductUp=$('#fromProductUp_'+j).val();

					selected_row++;
					if(txt_deleted_id=="") txt_deleted_id=updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;
					else txt_deleted_id+=","+updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;


					if(txt_deleted_prod_qty=='') txt_deleted_prod_qty=productId+'='+rollWgt+'='+fromProductUp; 
					else txt_deleted_prod_qty =txt_deleted_prod_qty+','+productId+'='+rollWgt+'='+fromProductUp;
				}

				
				
				if($('#tbl_'+j).is(':checked'))
				{
					i++;
					data_all+="&barcodeNo_" + i + "='" + $('#barcodeNo_'+j).val()+"'"+"&rollNo_" + i + "='" + $('#rollNo_'+j).val()+"'"+"&productId_" + i + "='" + $('#productId_'+j).val()+"'"+"&rollId_" + i + "='" + $('#rollId_'+j).val()+"'"+"&rollWgt_" + i + "='" + $('#rollWgt_'+j).val()+"'"+"&hiddenTransferqnty_" + i + "='" + $('#hiddenTransferqnty_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&floor_" + i + "='" + $('#floor_'+j).val()+"'"+"&room_" + i + "='" + $('#room_'+j).val()+"'"+"&rack_" + i + "='" + $('#rack_'+j).val()+"'"+"&shelf_" + i + "='" + $('#shelf_'+j).val()+"'"+"&dtlsId_" + i + "='" + $('#dtlsId_'+j).val()+"'"+"&transIdFrom_" + i + "='" + $('#transIdFrom_'+j).val()+"'"+"&transIdTo_" + i + "='" + $('#transIdTo_'+j).val()+"'"+"&rolltableId_" + i + "='" + $('#rolltableId_'+j).val()+"'"+"&transRollId_" + i + "='" + $('#transRollId_'+j).val()+"'"+"&storeId_" + i + "='" + $('#storeId_'+j).val()+"'"+"&requiDtlsId_" + i + "='" + $('#requiDtlsId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diaWidth_" + i + "='" + $('#diaWidth_'+j).val()+"'"+"&febDescripId_" + i + "='" + $('#febDescripId_'+j).val()+"'"+"&constructCompo_" + i + "='" + encodeURIComponent($('#constructCompo_'+j).val())+"'"+"&fromProductUp_" + i + "='" + $('#fromProductUp_'+j).val()+"'";
							
					selected_row++;
				}
			}
			
			if(selected_row<1)     
			{
				alert("Please Select Barcode No.");
				return;
			}

			var dataString = "cbo_transfer_criteria*txt_system_id*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*txt_from_order_book_no*txt_from_batch_no*txt_from_batch_id*txt_from_order_book_id*hidden_book_no*txt_from_order_book_dtls_id*txt_to_order_book_no*txt_to_batch_no*txt_to_batch_id*txt_to_order_book_id*txt_to_order_book_dtls_id*update_id*txt_requisition_no*txt_requisition_id*cbo_store_name*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_from_body_part*cbo_to_body_part"; 
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+'&txt_deleted_prod_qty='+txt_deleted_prod_qty+data_all;

			// alert(data);return;
			
			freeze_window(operation);
			http.open("POST","requires/roll_wise_finish_fabric_sample_transfer_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finish_transfer_entry_reponse;
		}
	}

	// Ajax Respons 
	function fnc_finish_transfer_entry_reponse()
	{	
		if(http.readyState == 4) 
		{	  		
			var reponse=trim(http.responseText).split('**');		
			//alert(http.responseText);release_freezing();return;
            if (reponse[0] * 1 == 20 * 1) 
            {
                alert(reponse[1]);
                release_freezing();
                return;
            }
			show_msg(reponse[0]); 	
				
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#update_id").val(reponse[1]);
				$("#txt_system_id").val(reponse[2]);
				$('#cbo_company_id').attr('disabled','disabled');
				$('#txt_from_order_book_no').attr('disabled','disabled');
				$('#txt_to_order_book_no').attr('disabled','disabled');
				$('#txt_requisition_no').attr('disabled','disabled');
				
				show_list_view(reponse[1]+"**"+$('#txt_from_order_book_id').val()+"**"+$('#cbo_transfer_criteria').val(),'show_transfer_listview','tbl_details','requires/roll_wise_finish_fabric_sample_transfer_controller','setFilterGrid(\'tbl_details\',-1)');
				set_button_status(1, permission, 'fnc_finish_transfer_entry',1,1);
			}	
			release_freezing();
		}
	}

	// Order view 
	function openmypage_orderInfo(type)
	{
		var txt_to_order_book_no = $('#txt_'+type+'_order_no').val();
		var txt_to_order_book_id = $('#txt_'+type+'_order_id').val();

		if (form_validation('txt_'+type+'_order_no','Order No')==false)
		{
			alert("Please Select Order No.");
			return;
		}
		
		var title = 'Order Info';	
		var page_link = 'requires/roll_wise_finish_fabric_sample_transfer_controller.php?txt_to_order_book_no='+txt_to_order_book_no+'&txt_to_order_book_id='+txt_to_order_book_id+'&type='+type+'&action=orderInfo_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
	}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {

				if(!$(this).find('[type=checkbox]').is(':disabled'))
				{
					$(this).find('[type=checkbox]').attr('checked', false);
				}

				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}

	//  Transfer Criteria wise onchange caption
	function active_inactive(type)
	{
		
		if (type==8) // sammple to sammple
		{
			$("#showHideFrom").html('From Sample');
			$("#showHideTo").html('To Sample');

			$("#showHideToNo").html('SBWO No').css('color', 'blue');
			$("#showHideFromNo").html('SBWO No').css('color', 'blue');

			$("#showHideFromQty").html('Booking Qnty');
			$("#showHideToQty").html('Booking Qnty');

			// var txt_system_id = $("#txt_from_order_book_id").val();
			// var txt_system_id = $("#txt_system_id").val();


		}
		else if(type==7) // sammple to Order
		{
			$("#showHideFrom").html('From Sample');
			$("#showHideTo").html('To Order');

			$("#showHideFromNo").html('SBWO No').css('color', 'blue');
			$("#showHideToNo").html('Order No').css('color', 'blue');

			$("#showHideFromQty").html('Booking Qnty');
			$("#showHideToQty").html('Order Qnty');
		}
		else // Order to sammple
		{			
			$("#showHideFrom").html('From Order');
			$("#showHideTo").html('To Sample');

			$("#showHideFromNo").html('Order No').css('color', 'blue');
			$("#showHideToNo").html('SBWO No').css('color', 'blue');

			$("#showHideFromQty").html('Order Qnty');
			$("#showHideToQty").html('Booking Qnty.');

		}

		$("#txt_from_order_book_no").val('');
		$("#txt_from_order_book_id").val('');
		$("#txt_from_qnty").val('');
		$("#cbo_from_buyer_name").val('');
		$("#txt_from_style_ref").val('');
		$("#cbo_from_body_part").val('');
		$("#txt_from_job_no").val('');
		$("#txt_from_gmts_item").val('');
		$("#txt_from_shipment_date").val('');
		$("#txt_from_batch_no").val('');
		$("#txt_from_batch_id").val('');

		$("#txt_to_order_book_no").val('');
		$("#txt_to_order_book_id").val('');
		$("#txt_to_order_book_dtls_id").val('');
		$("#txt_to_qnty").val('');
		$("#cbo_to_buyer_name").val('');
		$("#txt_to_style_ref").val('');
		$("#cbo_to_body_part").val('');
		$("#txt_to_job_no").val('');
		$("#txt_to_gmts_item").val('');		
		$("#txt_to_shipment_date").val('');
		$("#txt_to_batch_no").val('');
		$("#txt_to_batch_id").val('');

		
		$("#tbl_details").text('');
	}

	

	function company_on_change(company)
	{
		var txt_system_id = $("#txt_system_id").val();

		if(txt_system_id !="")
		{	
			var pre_cbo_company_id = $("#pre_cbo_company_id").val();
			alert("Company change not allowed");
			$("#cbo_company_id").val(pre_cbo_company_id);
			return;
		}

		var cbo_transfer_criteria = $("#cbo_transfer_criteria").val();
		active_inactive(cbo_transfer_criteria);
		

		page_link = 'cbo_company_id='+company+'&action=requ_variable_settings';

		$.ajax({
			url: 'requires/roll_wise_finish_fabric_sample_transfer_controller.php',
			type: 'POST',
			data: page_link,
			success: function (response)
			{				
				var variable_settings = response.split("**");
				//alert(variable_settings[0]+'='+variable_settings[1]);
				if (variable_settings[0]==1) 
				{
					// $('#txt_requisition_no').attr('disabled',false);
					// $('#txt_from_order_book_no').attr('disabled','disabled');
					// $('#txt_to_order_book_no').attr('disabled','disabled');
				}
				else
				{
					$('#txt_requisition_no').attr('disabled','disabled');
					$('#txt_from_order_book_no').attr('disabled',false);
					$('#txt_to_order_book_no').attr('disabled',false);
				}
				$('#store_update_upto').val(variable_settings[1]);
			}
		});
	}

	function from_company_on_change(from_company)
	{
		var txt_system_id = $("#txt_system_id").val();

		if(txt_system_id !="")
		{	
			var pre_cbo_company_id = $("#pre_cbo_company_id").val();
			alert("Company change not allowed");
			$("#cbo_company_id").val(pre_cbo_company_id);
			return;
		}

		load_drop_down( 'requires/roll_wise_finish_fabric_sample_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+from_company, 'load_drop_store_from', 'from_store_td' );

	}

	function to_company_on_change()
	{
		var txt_system_id = $("#txt_system_id").val();

		if(txt_system_id !="")
		{	
			var pre_cbo_company_id_to = $("#pre_cbo_company_id_to").val();
			alert("Company change not allowed");
			$("#cbo_company_id_to").val(pre_cbo_company_id_to);
			return;
		}
		load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*cbo_store_name_to', 'store','to_store_td', $("#cbo_company_id_to").val(),'','','','','','','','store_load_cond()');

	}



	function openmypage_requisition_no()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

		if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
		{
			return;
		}
		
		var title = 'Item Requisition Info';	
		var page_link = 'requires/roll_wise_finish_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=sampleRequisitionTransfer_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
			
			get_php_form_data(transfer_id, "populate_data_from_sample_requisition_master", "requires/roll_wise_finish_fabric_sample_transfer_controller" );
			show_list_view(transfer_id+"**"+$('#txt_from_order_book_id').val()+"**"+cbo_transfer_criteria,'show_sample_requisition_transfer_listview','tbl_details','requires/roll_wise_finish_fabric_sample_transfer_controller','');
		}
	}

	function store_load_cond()
	{
		if($('#txt_to_order_book_id').val()=='')
		{
			$('#cbo_store_name_to').val(0)
		}

		var cbo_transfer_criteria  = $('#cbo_transfer_criteria').val(); 
		var txt_from_order_book_id = $('#txt_from_order_book_id').val(); 
		var txt_to_order_book_id   = $('#txt_to_order_book_id').val(); 
		var txt_to_order_book_id   = $('#txt_to_order_book_id').val(); 
		var cbo_store_name         = $('#cbo_store_name').val(); 
		var cbo_store_name_to      = $('#cbo_store_name_to').val(); 

		if (cbo_transfer_criteria == 8) 
		{
			if(txt_from_order_book_id == txt_to_order_book_id)
			{
				if(cbo_store_name == cbo_store_name_to)
				{
					$('#cbo_store_name_to').val(0);
				}
			}
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        <legend>Roll Wise Finish Fabric Sample Transfer Entry</legend>
        <br>
            <fieldset style="width:750px;">
                <table width="740" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <? 
                            	//array( 1 => 'Sample To Sample', 2 => 'Sample To Order', 3 => 'Order To Sample' );
                            	$trans_criteria = array( 8 => 'Sample To Sample', 7 => 'Sample To Order', 6 => 'Order To Sample' );
                                echo create_drop_down( "cbo_transfer_criteria", 160, $trans_criteria,"", 1, "--Select--", 0, "active_inactive(this.value);" );
                            ?>
                        </td>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);from_company_on_change(this.value);" );
                            ?>
                            <input type="hidden" name="pre_cbo_company_id" id="pre_cbo_company_id" />
                        </td>
                        <td class="must_entry_caption">To Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change();" );
                            ?>
                            <input type="hidden" name="pre_cbo_company_id_to" id="pre_cbo_company_id_to" />
                        </td>
                    </tr>
                    <tr>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td>Requisition Basis</td>
                        <td>
                            <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:148px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_requisition_no();"  disabled="disabled"/>
                        	<input type="hidden" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width:148px" />
                        	<input type="hidden" name="store_update_upto" id="store_update_upto">
                        </td>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend id="showHideFrom">From Sample</legend>
                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >	
								<tr>
									<td width="100" class="must_entry_caption">From Store</td>
									
									<td id="from_store_td">
										<?
											echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
										?>	
									</td>
								
								</tr>
                            	<tr>
                                    <td width="30%" class="must_entry_caption" id="showHideFromNo">SBWO No</td>
                                    <td>
                                        <input type="text" name="txt_from_order_book_no" id="txt_from_order_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="from_openmypage('from');" readonly />
                                        <input type="hidden" name="txt_from_order_book_id" id="txt_from_order_book_id" readonly>
                    					<input type="hidden" name="txt_from_order_book_dtls_id" id="txt_from_order_book_dtls_id" readonly>
                                    </td>
                                </tr>
								<tr>
                                    <td>Batch No</td>
                                    <td>
                                        <input type="text" name="txt_from_batch_no" id="txt_from_batch_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
										<input type="hidden" name="txt_from_batch_id" id="txt_from_batch_id"   /></td>
                                </tr>
                                <tr>
                                    <td id="showHideFromQty">Booking Qnty.</td>
                                    <td>
                                        <input type="text" name="txt_from_qnty" id="txt_from_qnty" class="text_boxes_numeric" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                
                                <tr>
                                    <td height="19">Body Part</td>
                                    <td>
                                        <?  echo create_drop_down( "cbo_from_body_part", 162, $body_part,"", 1, "--Select--", $selected, "",1 ); ?>	
                                    </td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo();">
                                    </td>
                                </tr>								
                            </table>                  
                       </fieldset>	
                    </td>

                    <td width="2%" valign="top"></td>

                    <td width="49%" valign="top">
                        <fieldset>
                        <legend id="showHideTo">To Sample</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                    <td width="30%" class="must_entry_caption" id="showHideToNo">SBWO No</td>
                                    <td>
                                        <input type="text" name="txt_to_order_book_no" id="txt_to_order_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="to_openmypage('to');" readonly />
                                        <input type="hidden" name="hidden_book_no" id="hidden_book_no" readonly>
                                        <input type="hidden" name="txt_to_order_book_id" id="txt_to_order_book_id" readonly>
                                        <input type="hidden" name="txt_to_order_book_dtls_id" id="txt_to_order_book_dtls_id" readonly>
                                    </td>
                                </tr>
								<tr style="display: none;">
                                    <td>Batch No</td>
                                    <td>
                                        <input type="text" name="txt_to_batch_no" id="txt_to_batch_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                        <input type="hidden" name="txt_to_batch_id" id="txt_to_batch_id"/></td>
                                </tr>
                                 <tr>
                                    <td id="showHideToQty">Booking Qnty.</td>
                                    <td>
                                        <input type="text" name="txt_to_qnty" id="txt_to_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
								<tr>
									<td width="100" class="must_entry_caption">Body Part</td>
									<td id="to_body_part_td">
									<?
									echo create_drop_down( "cbo_to_body_part", 160, $blank_array,"", 1, "--Select Body Part--", 0, "" );
									?>	
									</td>
								</tr>
                                <!-- <tr>
                                    <td height="19">Body Part</td>
                                    <td>
                                        <? // echo create_drop_down( "cbo_to_body_part", 162, $body_part,"", 1, "--Select--", $selected, "",0 ); ?>	
                                    </td>
                                </tr> -->
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo();">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100" class="must_entry_caption">To Store</td>
                                    <td id="to_store_td">
                                    <?
                                    echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "" );
                                    ?>	
                                    </td>
                                </tr>	
                                <tr>
                                    <td>Floor</td>
                                    <td id="floor_td_to">
                                    <? echo create_drop_down( "cbo_floor_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Room</td>
                                    <td id="room_td_to">
                                    <? echo create_drop_down( "cbo_room_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rack</td>
                                    <td id="rack_td_to">
                                    <? echo create_drop_down( "txt_rack_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shelf</td>
                                    <td id="shelf_td_to">
                                    <? echo create_drop_down( "txt_shelf_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
			</table>	
            <fieldset style="width:1150px;text-align:left">
				<table cellpadding="0" width="1130" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    	<th width="40">SL</th>
                        <th width="100">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="150">Batch No</th>
                        <th width="60">Product Id</th>
                        <th width="200">Fabric Description</th>
                        <th width="120">order/ sample booking</th>
                        <th width="70">Color</th>
                        <th width="55">Floor</th>
                		<th width="55">Room</th>
                        <th width="55">Rack</th>
                        <th width="55">Shelf</th>
                        <th>Roll Wgt.</th>
                    </thead>
                 </table>
                 <div style="width:1130px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1130" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_details">
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1130" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                     			echo load_submit_buttons($permission, "fnc_finish_transfer_entry",0,1,"reset_form('transferEntry_1','tbl_details','','','disable_enable_fields(\'cbo_company_id\');')",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </fieldset>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
