<?
/*-------------------------------------------- Comments
Purpose			: This form will create Knit Grey Fabric Issue Entry
				
Functionality	:	
JS Functions	:
Created by		: Ashraful 
Creation date 	: 19-02-2015
Updated by 		: Zaman
Update date		: 13.01.2020
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	<? 
    $company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	?>
	function fnc_wo_popup()
	{
		var title='AOP Receive Form';
		var page_link='requires/aop_roll_receive_entry_controller.php?action=action_wo_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hiddenBookingNo=this.contentDoc.getElementById("hiddenBookingNo").value;
			var hiddenChallanNo=this.contentDoc.getElementById("hiddenChallanNo").value;
			var hiddenChallanId=this.contentDoc.getElementById("hiddenChallanId").value;
			var hiddenCompanyId=this.contentDoc.getElementById("hiddenCompanyId").value;
			var hiddenKnittingSource=this.contentDoc.getElementById("hiddenKnittingSource").value;
			var hiddenKnittingCompany=this.contentDoc.getElementById("hiddenKnittingCompany").value;
			var hiddenBookingMstId=this.contentDoc.getElementById("hiddenBookingMstId").value;
			var hiddenProcessId=this.contentDoc.getElementById("hiddenProcessId").value;
			var hiddenWOEntryForm=this.contentDoc.getElementById("hiddenWOEntryForm").value;

			$("#txt_wo_no").val(hiddenBookingNo);
			$("#txt_wo_id").val(hiddenBookingMstId);
			$('#cbo_company_id').val(hiddenCompanyId);
			$('#cbo_knitting_source').val(hiddenKnittingSource);
			load_drop_down( 'requires/aop_roll_receive_entry_controller', hiddenKnittingSource+'_'+hiddenCompanyId, 'load_drop_down_knitting_com', 'knitting_com'); 
			$('#cbo_knitting_company').val(hiddenKnittingCompany);
			$("#hidden_challan_id").val(hiddenChallanId);
			$("#hiddenSelectedChallanId").val(hiddenChallanId);
			$("#hiddenProcessId").val(hiddenProcessId);
			$("#hidden_wo_entry_form").val(hiddenWOEntryForm);

			$("#txt_challan_no").val("");
			$("#scanning_tbl").html("");
		}
	}

	function fnc_issue_challan_popup()
	{
		if(form_validation('txt_wo_no','WO No')==false)
		{
			return; 
		}
		
		var txt_wo_no= $("#txt_wo_no").val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var hidden_challan_id = $('#hidden_challan_id').val();
		var update_id = $('#update_id').val();
		if(update_id!= '')
		{
			//alert('Issue Challan No Re select no need,all are loaded in bellow list view plz checked it.');
			//return;
		}

		if(hidden_challan_id == '')
		{
			alert('Challan No Are Not Avaible For This Booking No.');
			return;
		}

		var title='AOP Receive Form';
		var page_link='requires/aop_roll_receive_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_knitting_source='+cbo_knitting_source+'&cbo_knitting_company='+cbo_knitting_company+'&hidden_challan_id='+hidden_challan_id+'&action=action_issue_challan_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_recv_nos=this.contentDoc.getElementById("hidden_recv_nos").value;
			var hiddenChallanId=this.contentDoc.getElementById("hiddenChallanId").value;

			var selectedChallanId='';
			/* if( $("#hiddenSelectedChallanId").val() != '' )
			{
				$("#hiddenSelectedChallanId").val($("#hiddenSelectedChallanId").val()+','+hiddenChallanId);
				selectedChallanId = $("#hiddenSelectedChallanId").val();
			}
			else
			{
				$("#hiddenSelectedChallanId").val(hiddenChallanId);
				selectedChallanId = $("#hiddenSelectedChallanId").val();
			} */

			$("#hiddenSelectedChallanId").val(hiddenChallanId);
			selectedChallanId = $("#hiddenSelectedChallanId").val();

			//alert(hiddenChallanId);

			var updateId = $("#update_id").val();
			if(updateId != '')
			{
				$("#txt_challan_no").val($("#txt_challan_no").val()+','+hidden_recv_nos);
				show_list_view(updateId+'_'+selectedChallanId, 'grey_item_details_both', 'scanning_tbl','requires/aop_roll_receive_entry_controller', '' );
				set_button_status(1, permission, 'fnc_aop_roll_wise',1);
			}
			else
			{
				/* if($("#txt_challan_no").val() != '')
				{
					$("#txt_challan_no").val($("#txt_challan_no").val()+','+hidden_recv_nos);
				}
				else
				{
					$("#txt_challan_no").val(hidden_recv_nos);
				} */
				$("#txt_challan_no").val(hidden_recv_nos);

				show_list_view(selectedChallanId+'_'+cbo_company_id, 'grey_item_details', 'scanning_tbl','requires/aop_roll_receive_entry_controller', '' );
				set_button_status(0, permission, 'fnc_aop_roll_wise',1);
			}
		}
	}
	
	function open_mrrpopup()
	{
		var page_link='requires/aop_roll_receive_entry_controller.php?action=update_system_popup';
		var title='AOP Receive Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hiddenReceiveNo=this.contentDoc.getElementById("hiddenReceiveNo").value;
			var hiddenUpdateId=this.contentDoc.getElementById("hiddenUpdateId").value;
			var hiddenChallanNo=this.contentDoc.getElementById("hiddenChallanNo").value;
			var hiddenChallanId=this.contentDoc.getElementById("hiddenChallanId").value;

			$("#txt_system_no").val(hiddenReceiveNo);
			$("#txt_challan_no").val(hiddenChallanNo);
			$("#update_id").val(hiddenUpdateId);

			if(trim(hiddenUpdateId)!="")
			{
				show_list_view(hiddenUpdateId, 'grey_item_details_update', 'scanning_tbl','requires/aop_roll_receive_entry_controller', '' );
				get_php_form_data(hiddenUpdateId, "load_php_form_update", "requires/aop_roll_receive_entry_controller" );
				set_button_status(1, permission, 'fnc_aop_roll_wise',1);
			}
		}
	}

	function fnc_aop_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_delivery_print');
			return;
		}
		
	 	if(form_validation('txt_delivery_date*cbo_company_id*cbo_knitting_source*cbo_knitting_company','Receive Date*Company*Serving Source*Serving Company')==false)
		{
			return; 
		}
		var cbo_knitting_source = $('#cbo_knitting_source').val()
		var j=0; var dataString=''; var batchChk=0;var colorChk=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
				var activeId=1; 
			}
			else
			{
				var activeId=0; 	
			}
			var updateDetailsId=$(this).find('input[name="updateDetaisId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var challanNo=$(this).find('input[name="challanNo[]"]').val();
			var batchNo=$(this).find('input[name="batchNo[]"]').val();
			var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
			var hdnProcessNo=$(this).find('input[name="hdnProcessNo[]"]').val();
			var hdnProductionEntryFrom=$(this).find('input[name="hdnProductionEntryFrom[]"]').val();
			var isSalesId=$(this).find('input[name="isSalesId[]"]').val();

			if( cbo_knitting_source==3 && hdnProcessNo ==31 ){
				var colorId=$(this).find('select[name="colorId[]"]').val();
			}
			else{
				var colorId=$(this).find('input[name="colorId[]"]').val();
			}
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var orderNo=$(this).find('input[name="orderNo[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var rollwgt=$(this).find('input[name="rolWgt[]"]').val();
			var rolldia=$(this).find('input[name="rollDia[]"]').val();
			var rollGsm=$(this).find('input[name="rollGsm[]"]').val();
			var fabricId=$(this).find('input[name="fabricId[]"]').val();
			var receiveBasis=$(this).find('input[name="receiveBasis[]"]').val();
			var knittingSource=$(this).find('input[name="knittingSource[]"]').val();
			var knittingComp=$(this).find('input[name="knittingComp[]"]').val();
			var job_no=$(this).find('input[name="jobNo[]"]').val();
			var bookingNo=$(this).find('input[name="bookingNo[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodNumber[]"]').val();
			var rollNo=$(this).find('input[name="rollNo[]"]').val();
			
			var rate=$(this).find('input[name="rate[]"]').val();
			var amount=$(this).find('input[name="amount[]"]').val();
			var currency=$(this).find('select[name="currencyId[]"]').val();
			var exchangeRate=$(this).find('input[name="exchangeRate[]"]').val();
			
			var issueRollId=$(this).find('input[name="issueRollId[]"]').val();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var salesBookingNO=$(this).find('input[name="salesBookingNO[]"]').val();
			var salesBookingID=$(this).find('input[name="salesBookingID[]"]').val();

			var txtBatchNo=$(this).find('input[name="txtBatchNo[]"]').val();
			

			if(txtBatchNo == "undefined"){
				txtBatchNo = "";
			}

			//without Heat settings other process should have batch no
			if(activeId ==1 && txtBatchNo =="" && hdnProcessNo !=33 && hdnProcessNo !=100 && hdnProcessNo !=476){
				batchChk += 1;
			}
			if(activeId ==1 && colorId ==0 && hdnProcessNo !=33 && hdnProcessNo !=100 && hdnProcessNo !=476){
				colorChk += 1;
			}

			j++;
			dataString+='&rollId_' + j + '=' + rollId + '&buyerId_' + j + '=' + buyerId + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '=' + colorId  + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&knittingSource_' + j + '=' + knittingSource + '&knittingComp_' + j + '=' + knittingComp+ '&deterId_' + j + '=' + deterId + '&fabricId_' + j + '=' + fabricId + '&receiveBasis_' + j + '=' + receiveBasis+ '&job_no_' + j + '=' + job_no+ '&rollwgt_' + j + '=' + rollwgt + '&rolldia_' + j + '=' + rolldia + '&bookingNo_' + j + '=' + bookingNo+ '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' + activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+ '&rate_' + j + '=' + rate+ '&amount_' + j + '=' + amount+ '&currency_' + j + '=' + currency+ '&exchangeRate_' + j + '=' + exchangeRate+ '&batchId_' + j + '=' + batchId+ '&challanNo_' + j + '=' + challanNo+ '&batchNo_' + j + '=' + batchNo+ '&hdnProcessNo_' + j + '=' + hdnProcessNo+ '&txtBatchNo_' + j + '=' + txtBatchNo+ '&issueRollId_' + j + '=' + issueRollId+ '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder+ '&hdnProductionEntryFrom_' + j + '=' + hdnProductionEntryFrom+ '&isSalesId_' + j + '=' + isSalesId+ '&orderNo_' + j + '=' + orderNo+ '&salesBookingNO_' + j + '=' + salesBookingNO+ '&salesBookingID_' + j + '=' + salesBookingID;
			
		});
		if(j<1)
		{
			alert('No data found');
			return;
		}

		if(batchChk > 0){
			alert("please type batch no of selected roll")
			return;
		}
		if(colorChk > 0){
			alert("please select color name of selected roll")
			return;
		}
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_knitting_source*cbo_knitting_company*update_id*txt_system_no*txt_batch_no*txt_wo_no*txt_wo_id*txt_delivery_challan*hidden_wo_entry_form',"../../")+dataString;
		freeze_window(operation);
		//alert(data);
		http.open("POST","requires/aop_roll_receive_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');

			if(response[0]*1==20){
				alert(response[1]);
				release_freezing();
				return;
			}

			show_msg(response[0]);
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				
				if(response[3]!="")
				{
				 var update_details_arr=response[3].split('##')	
				 for(var i=0;i<update_details_arr.length;i++)
					{
						var update_tr=update_details_arr[i].split('#');
						document.getElementById('updateDetaisId_'+update_tr[0]).value=update_tr[1];	
					}
					if(trim(response[1])!="")
					{
						show_list_view(response[1], 'grey_item_details_update', 'scanning_tbl','requires/aop_roll_receive_entry_controller', '' );
					}
					
				}
				set_button_status(1, permission, 'fnc_aop_roll_wise',1);
			}
			release_freezing();
		}
	}
	
	/*
	$('#txt_challan_no').live('keydown', function(e){
		if (e.keyCode === 13)
		{
			if(form_validation('txt_wo_no','WO No')==false)
			{
				return; 
			}
			e.preventDefault();
		   scan_challan_no(this.value); 
		}
	});	

	function scan_challan_no(str)
	{
		show_list_view(str, 'grey_item_details', 'scanning_tbl','requires/aop_roll_receive_entry_controller', '' );
		get_php_form_data(str, "load_php_form", "requires/aop_roll_receive_entry_controller" );
	}
	*/

	function rate_form_workorder(deterId, bodyPart, jobNo, id)
	{
		var page_link='requires/aop_roll_receive_entry_controller.php?action=woorder_rate_popup&determinationId='+deterId+'&bodyPart='+bodyPart+'&job_no='+jobNo;
		var title='Rate From Wo Order';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var rate=this.contentDoc.getElementById("hidden_rate").value;
			var currency_id=this.contentDoc.getElementById("hidden_currency_id").value;
			var exchange_rate=this.contentDoc.getElementById("hidden_exchange_rate").value;
			
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var body_part=$(this).find('input[id="bodyPartId_'+id+'"]').val();
				var deter_id=$(this).find('input[id="deterId_'+id+'"]').val();
				
				if(bodyPart==body_part && deterId==deter_id)
				{
					var rollWgt=$(this).find('input[id="rolWgt_'+id+'"]').val()*1;
					var amount=rollWgt*rate;
					$(this).find('input[id="rate_'+id+'"]').val(rate);
					$(this).find('input[id="amount_'+id+'"]').val(amount);
					$(this).find('select[id="currencyId_'+id+'"]').val(currency_id);
					$(this).find('input[id="exchangeRate_'+id+'"]').val(exchange_rate);
				}
			});
		}
	}
	
	function Calculate_amount(rate,id)
	{
		var selected_bodyPart=$("#bodyPartId_"+id).val();
		var selected_deterId=$("#deterId_"+id).val();

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var bodyPart=$(this).find('input[id="bodyPartId_'+id+'"]').val();
			var deterId=$(this).find('input[id="deterId_'+id+'"]').val();

			if(bodyPart==selected_bodyPart && deterId==selected_deterId)
			{
				var rollWgt=$(this).find('input[id="rolWgt_'+id+'"]').val()*1;
				var amount=rollWgt*rate;
				$(this).find('input[id="rate_'+id+'"]').val(rate);
				$(this).find('input[id="amount_'+id+'"]').val(amount);
			}
		});
	}
	 
	function fnc_total_sum(id)
	{
		//var selected_bodyPart=$("#bodyPartId_"+id).val();
	//	var selected_deterId=$("#deterId_"+id).val();
			var total_roll_weight='';
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if($(this).find('input[name="checkRow[]"]').is(':checked'))
				{
					var activeId=1; 
				}
				else
				{
					var activeId=0; 	
				}
				//alert(activeId);
				if(activeId==1)
				{
					var rollwgt=$(this).find('input[name="rolWgt[]"]').val()*1;
					if(rollwgt!='')
					{
					  total_roll_weight=total_roll_weight*1+rollwgt*1;
					  // alert(total_roll_weight+'='+rollwgt);
					}
				}
				
			});
				$("#tot_sum").val(total_roll_weight.toFixed(2));	
	}
	
	function fnc_roll_wgt_check(id)
	{
		var aop_over_qty=$("#aop_over_qty").val()*1;
		 
		 $("#scanning_tbl").find('tbody tr').each(function()
		{
			
			var rollWgt=$(this).find('input[name="rolWgt[]"]').val()*1;
			var chkrollwgt=$(this).find('input[name="chkrollwgt[]"]').val()*1;
			var over_wgt_qty=chkrollwgt+((aop_over_qty*chkrollwgt)/100);
			//alert(over_wgt_qty+'='+aop_over_qty+'='+chkrollwgt+'='+rollWgt);
			if(aop_over_qty>0)
			{
				if(rollWgt>over_wgt_qty)
				{
					alert('Over qty not allowed');
					//$(this).find('input[name="rollWgt[]"]').val(chkrollwgt);
					$(this).find('input[name="rolWgt[]"]').val(chkrollwgt);
					return;
				}
			}
		});
	}

	function fnc_copy_batch_no(i) 
	{
    	var colorId = document.getElementById('colorId_' + i).value;
    	var txtBatchNo = document.getElementById('txtBatchNo_' + i).value;
    	var copy_all_batch=$("#copy_all_batch").is(":checked");
		var copy_row=$("#checkRow_"+i).is(":checked");

    	if(copy_all_batch && copy_row)
		{
	        $("#scanning_tbl").find('tbody tr').each(function () {
	            var batchNoIdArr = $(this).find('input[name="txtBatchNo[]"]').attr('id').split('_');
	            var row_num = batchNoIdArr[1];
				var check_row_curr=$("#checkRow_"+row_num).is(":checked");
				if(check_row_curr)
				{
					if(row_num >= i)
					{
						var colorIdChk = document.getElementById('colorId_' + row_num).value;
						if (colorId == colorIdChk) {
							$('#txtBatchNo_' + row_num).val(txtBatchNo);
						}
					}
				}
	        });
	    }
    }

	function fnc_copy_color_no(i) 
	{
    	var colorId = document.getElementById('colorId_' + i).value;
    	var txtBatchNo = document.getElementById('txtBatchNo_' + i).value;
    	var copy_all_color=$("#copy_all_color").is(":checked");
    	var check_row=$("#checkRow_"+i).is(":checked");

		//alert(copy_all_color +'&&'+ check_row);
    	if(copy_all_color && check_row)
		{
	        $("#scanning_tbl").find('tbody tr').each(function () {
	            var batchNoIdArr = $(this).find('input[name="txtBatchNo[]"]').attr('id').split('_');
	            var row_num = batchNoIdArr[1];
				var check_row_curr=$("#checkRow_"+row_num).is(":checked");
				if(check_row_curr)
				{//alert(check_row_curr +' kk');
					if(row_num >= i)
					{
						var batchNoChk = document.getElementById('txtBatchNo_' + row_num).value;
						if (txtBatchNo == batchNoChk) {
							$('#colorId_' + row_num).val(colorId);
						}
					}
				}
	        });
	    }
    }

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="checkRow[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="checkRow[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="rollscanning_1" id="rollscanning_1" autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Issue Challan Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td align="right" colspan="3" width="100">AOP Receive No</td>
                        <td colspan="3" align="left"><input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="open_mrrpopup()" placeholder="Browse For System No" /></td>
                    </tr>
                    <tr>
                        <td align="right"  width="100">WO No</td>
                        <td  align="left">
                        	<input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:140px;" onDblClick="fnc_wo_popup()" placeholder="Browse" readonly />
                        	<input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" style="width:140px;" />
                        	<input type="hidden" name="hiddenProcessId" id="hiddenProcessId" class="text_boxes" style="width:140px;" />
                        	<input type="hidden" name="hidden_wo_entry_form" id="hidden_wo_entry_form" class="text_boxes" style="width:140px;" />
                        </td>
                        <td align="right">Company</td>
                        <td><? echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Display--", 0, "",1 ); ?></td>
                        <td align="right">Serving Source </td>
                        <td><? echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); ?></td>
                    </tr>
                    <tr>
                    <td align="right">Serving Company</td>
                    <td id="knitting_com"><? echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 ); ?></td>
                    <td align="right" class="must_entry_caption" width="100">Receive Date</td>
                    <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;"  /></td>
                    <td align="right" style="display:none" >Batch No&nbsp;&nbsp;</td>
                    <td style="display:none"><input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" onDblClick="" placeholder="Display"/></td>
                    </tr>
                    <tr>
                        <td align="right">Delivery Challan&nbsp;&nbsp;</td>
                        <td><input type="text" name="txt_delivery_challan" id="txt_delivery_challan" class="text_boxes" style="width:140px;" /></td>
                        <td align="right">Issue Challan No&nbsp;&nbsp;</td>
                        <td><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="fnc_issue_challan_popup()" placeholder="Browse" readonly/></td>
                        <td colspan="2">
                            <input type="hidden" name="update_id" id="update_id" class="text_boxes" />
                            <input type="hidden" name="aop_over_qty" id="aop_over_qty" class="text_boxes" />
                        	<!--<input type="text" name="knit_company_id" id="knit_company_id" class="text_boxes" />-->
                            <input type="hidden" name="hidden_challan_id" id="hidden_challan_id" class="text_boxes" />
                            <input type="hidden" name="hiddenSelectedChallanId" id="hiddenSelectedChallanId" value="" class="text_boxes" />
                            <!--<input type="text" name="txt_tot_row" id="txt_tot_row" class="text_boxes" />-->
                        </td>
                    </tr>
                </table>
			</fieldset> 
            <br/>
            <fieldset style="width:1260px;text-align:left">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
                <table cellpadding="0" width="1675" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                        <th width="45"><input type="checkbox" id="all_check" name="all_check" onClick="check_all('all_check')"> SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="100">Challan No</th>
                        <th width="80" class="must_entry_caption">Batch No <input type="checkbox" id="copy_all_batch" name="copy_all_batch">
                        </th>
                        <th width="100" class="must_entry_caption">Batch Color <input type="checkbox" id="copy_all_color" name="copy_all_color"></th>
                        <th width="90">Body Part</th>
                        <th width="100">Const./ Composition</th>
                        <th width="60">Gsm</th>
                        <th width="60">Dia</th>
                        <th width="70">Color</th>
                        <th width="70">Roll Wgt.</th>
                        <th width="70">Rate</th>
                        <th width="70">Amount</th>
                        <th width="70">Currency</th>
                        <th width="50">Job No</th>
                        <th width="50">Year</th>
                        <th width="65">Buyer</th>
                        <th width="80">Order No</th>
                        <th width="80">Kniting Com</th>
                        <th width="100">Program/ Booking /Pi No</th>
                        <th width="">Opening / Independent</th>
                    </thead>
                 </table>
                <div style="width:1700px; max-height:250px; overflow-y:scroll" align="left">
                    <table cellpadding="0" cellspacing="0" width="1675" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                        <!--<tbody>
                            <tr id="tr_1" align="center" valign="middle">
                                <td width="45" id="sl_1"></td>
                                <td width="80"></td>
                                <td width="50"></td>
                                <td width="100"></td>
                                <td width="80"></td>
                                <td width="90"></td>
                                <td width="100"></td>
                                <td width="60"></td>
                                <td width="60"></td>
                                <td width="70" style="word-break:break-all;"></td>
                                <td width="70"><input type="text"  class="text_boxes_numeric" id="rollWgt_1" name="rollWgt[]"  style="width:45px;" /></td>
                                <td width="70"><input type="text"  class="text_boxes_numeric" id="rate_1" name="rate[]"  style="width:45px;"  placeholder="write/Browse"/></td>
                                <td width="70"><input type="text"  class="text_boxes_numeric" id="amount_1" name="amount[]"  style="width:45px;"  readonly/></td>
                                <td width="70"><?php echo create_drop_down( "currencyId_1", 70, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" ); ?></td>
                                <td width="50" id="job_1"></td>
                                <td width="50" id="year_1" align="center"></td>
                                <td width="65" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" style="word-break:break-all;" align="left"></td>
                                <td width="100" style="word-break:break-all;" align="left"></td>
                                <td width=""></td>
                                <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                <input type="hidden" name="productionId[]" id="productionId_1"/>
                                <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                                <input type="hidden" name="deterId[]" id="deterId_1"/>
                                <input type="hidden" name="productId[]" id="productId_1"/>
                                <input type="hidden" name="orderId[]" id="orderId_1"/>
                                <input type="hidden" name="rollId[]" id="rollId_1"/>
                                <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                <input type="hidden" name="exchangeRate[]" id="exchangeRate_1"/>
                            </tr>
                        </tbody>-->
                    </table>
                    <table width="1675" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:10px;">
                        <tr>
                            <td align="center" class="button_container"><? echo load_submit_buttons($permission,"fnc_aop_roll_wise",0,0,"",1); ?></td>
                        </tr>  
                    </table>
                </div>
            </fieldset>
            <!-- ========================== Child table end ============================ -->   
            <div style="width:990px; margin-top:5px" id="list_view_container"></div>
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>