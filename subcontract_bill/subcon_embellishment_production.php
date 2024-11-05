<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create subcontract embellishment entry
Functionality	:	
JS Functions	:
Created by		:	Md. Reaz Uddin
Creation date 	: 	23-11-2017
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Entry Info", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_batch_color").autocomplete({
			 source: str_color
		  });
     });
	 var scanned_barcode=new Array();
	 var batch_against_arr=[];
	<?
		$jsbatch_against= json_encode($batch_against);
		echo "batch_against_arr = ". $jsbatch_against . ";\n";
	?>
	// Start:  For Save / Update / Delete //
	function fnc_embel_entry(operation)
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		
		//if( form_validation('cbo_company_id*cbo_location_name*txt_batch_date*cbo_source*cbo_company_supplier*cbo_location_name_s','Company*Location*Batch Date*Source*cbo_company_supplier*cbo_location_name_s')==false )
		if( form_validation('cbo_company_id','Company')==false )
		{
			return;
		}
		
		/*
		var row_num=$('#embellishment_details_container tr').length;
		var data_all="";
		//alert (row_num); return;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txtQcPassQty_'+i,'QC Pass Qty')==false)
			{
				return;
			}
			
			data_all+=get_submitted_data_string('txtBatchNo_'+i+'*hiddenBatchId_'+i+'*updateIdDtls_'+i+'*txtBatchColorId_'+i+'*txtPoNo_'+i+'*txtPoId_'+i+'*cboJobParty_'+i+'*txtGmtsItem_'+i+'*txtProcessId_'+i+'*txtBatchQty_'+i+'*txtRejectQty_'+i+'*txtNeedReProcQty_'+i+'*txtQcPassQty_'+i+'*txtOperatorName_'+i+'*txtOperatorId_'+i+'*cboShift_'+i,"../",i);
		}
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_batch_date*cbo_source*cbo_company_supplier*cbo_location_name_s*update_id*hiddenBatchAgainst',"../")+data_all+'&total_row='+row_num;
		*/
		
		var j=0; var dataString=''; //var all_barcodes='';
		$("#embellishment_details_container").find('tr').each(function()
		{
			var hiddenBatchId=$(this).find('input[name="hiddenBatchId[]"]').val();
			var hiddenBatchDtlsId=$(this).find('input[name="hiddenBatchDtlsId[]"]').val();
			var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
			
			var BatchNo=$(this).find('input[name="txtBatchNo[]"]').val();
			var BatchColorId=$(this).find('input[name="txtBatchColorId[]"]').val();
			var PoNo=$(this).find('input[name="txtPoNo[]"]').val();
			var PoId=$(this).find('input[name="txtPoId[]"]').val();
			var cboJobParty=$(this).find('input[name="cboJobParty[]"]').val();
			var GmtsItem=$(this).find('input[name="txtGmtsItem[]"]').val();
			var ProcessId=$(this).find('input[name="txtProcessId[]"]').val();
			var BatchQty=$(this).find('input[name="txtBatchQty[]"]').val();
			var RejectQty=$(this).find('input[name="txtRejectQty[]"]').val();
			var NeedReProcQty=$(this).find('input[name="txtNeedReProcQty[]"]').val();
			var QcPassQty=$(this).find('input[name="txtQcPassQty[]"]').val();
			var OperatorName=$(this).find('input[name="txtOperatorName[]"]').val();
			var OperatorId=$(this).find('input[name="txtOperatorId[]"]').val();
			var Shift=$(this).find('select[name="cboShift[]"]').val();
			
			j++;
			
			dataString += '&txtBatchNo_' + j + '=' + BatchNo + '&hiddenBatchId_' + j + '=' + hiddenBatchId+ '&hiddenBatchDtlsId_' + j + '=' + hiddenBatchDtlsId + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtBatchColorId_' + j + '=' + BatchColorId + '&txtPoNo_' + j + '=' + PoNo + '&txtPoId_' + j + '=' + PoId + '&cboJobParty_' + j + '=' + cboJobParty + '&txtGmtsItem_' + j + '=' + GmtsItem + '&txtProcessId_' + j + '=' + ProcessId + '&txtBatchQty_' + j + '=' + BatchQty + '&txtRejectQty_' + j + '=' + RejectQty + '&txtNeedReProcQty_' + j + '=' + NeedReProcQty + '&txtQcPassQty_' + j + '=' + QcPassQty + '&txtOperatorName_' + j + '=' + OperatorName + '&txtOperatorId_' + j + '=' + OperatorId + '&cboShift_' + j + '=' + Shift;
			//all_barcodes +=+ barcodeNo+',';
			
		});
		if(j<1)
		{
			alert('No data');
			return;
		}
		//alert(dataString);return;
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_batch_date*cbo_source*cbo_company_supplier*cbo_location_name_s*update_id*hiddenBatchAgainst*txt_deleted_dtls_id*txtSubConEmbDtlsIds',"../")+dataString+'&total_row='+j;
		//alert (data);return;
		freeze_window(operation);
		
		http.open("POST","requires/subcon_embellishment_production_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_embel_entry_response;
	}	 
	 
	function fnc_embel_entry_response()
	{
		if(http.readyState == 4) 
		{
			//release_freezing(); 
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');	
				
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				refresh_data(); // Refress all form data;
				
				document.getElementById('txtEmbelProductionSerch').value = response[2];
				document.getElementById('update_id').value = response[1];
				document.getElementById('hiddenBatchAgainst').value = response[3];
				//alert("Save is Complite. Now Decision for save after ?");
				$('#lbl_batch_Against').text(batch_against_arr[response[3]]);
				
				get_php_form_data(response[1]+'_'+response[3], "populate_data_from_embellishment_mst", "requires/subcon_embellishment_production_controller" );
				show_list_view(response[1]+'**'+response[3],'embellishment_details','embellishment_details_container','requires/subcon_embellishment_production_controller','');
				//set_button_status(1, permission, 'fnc_embel_entry',1,1);
				auto_complete(); // Employee Or Operator name auto Complete
				fnc_tr_count();	 // Tr counter Function
							
			}
			else if( response[0]=="11" )
			{
				alert("Duplicate Data Save Not Allowed!!");
				scanned_barcode = [];
				$('#embellishment_details_container').children( 'tr:not(:last)' ).remove();
				reset_form('embellishmentEntry_1','','','','');
				set_button_status(0, permission, 'fnc_embel_entry',1,1);
				
				
				release_freezing();
				return;	
			}
			release_freezing();	
		}
	}
	 // End:  For Save / Update / Delete //
	 
	function batch_search_popup()
	{
				
			if (form_validation('cbo_company_id','Company')==false)  return;
			
			var cbo_company_id = $('#cbo_company_id').val();
			var deleted_ids = $('#txt_deleted_dtls_id').val();
			var hiddenBatchAgainst = $('#hiddenBatchAgainst').val();
			
			var prevBatchIds='';
			$("#tbl_item_details").find('tbody tr').each(function()
			{
				var batch_id=$(this).find('input[name="hiddenBatchId[]"]').val();
				
				if(batch_id!="")
				{
					if(prevBatchIds=="") prevBatchIds=batch_id; else prevBatchIds+=","+batch_id;
				}
			});
			
			
			var prevBatchDtlsIds='';
			$("#tbl_item_details").find('tbody tr').each(function()
			{
				var batch_dtls_id=$(this).find('input[name="hiddenBatchDtlsId[]"]').val();
				
				if(batch_dtls_id!="")
				{
					if(prevBatchDtlsIds=="") prevBatchDtlsIds=batch_dtls_id; else prevBatchDtlsIds+=","+batch_dtls_id;
				}
			});
			
			//alert(prevBatchIds+"__"+prevBatchDtlsIds);
			
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/subcon_embellishment_production_controller.php?cbo_company_id='+cbo_company_id+'&prevBatchIds='+prevBatchIds+'&prevBatchDtlsIds='+prevBatchDtlsIds+'&hiddenBatchAgainst='+hiddenBatchAgainst+'&action=batch_search_popup&txt_deleted_dtls_id='+deleted_ids;
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=420px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
				var theform=this.contentDoc.forms[0] 	//("search_order_frm"); //Access the form inside the modal window
				
				var popupCompany=this.contentDoc.getElementById("cbo_company_name").value;
				$('#cbo_company_id').val(popupCompany);
				var batch_ids=this.contentDoc.getElementById("hidden_batch_ids").value;
				var batch_against=this.contentDoc.getElementById("hidden_Batch_Against").value;
				
				/*var popBIdArr = batch_ids.split(",")
				var i=0;
				for ( i in popBIdArr ) {
					
					if( jQuery.inArray( popBIdArr[i], scanned_barcode )>-1) //Duplicate Batch Scan check
					{ 
						alert('Sorry! Barcode Already Scanned.'); 
						$('#txtBatchNumberSerch').val('');
						return; 
					}
					scanned_barcode.push(popBIdArr[i]);
				}*/
				
				$('#lbl_batch_Against').text(batch_against_arr[batch_against]); // For Showing Batch Against
				
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
				
				$("#hiddenBatchAgainst").val(batch_against);
				
				var data=batch_ids+"**"+tot_row+"**"+batch_against;
				//alert(data);
				
				var list_view_barcode =return_global_ajax_value( data, 'populate_batch_data', '', 'requires/subcon_embellishment_production_controller');
				
				$("#embellishment_details_container").prepend(list_view_barcode);	
				
				auto_complete();
				
				fnc_tr_count();
				
			}
	}	
	 
	function search_embel_production()
	{
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_id').val();
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/subcon_embellishment_production_controller.php?cbo_company_id='+company_id+'&action=search_embel_production';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var emblishment_id_data=this.contentDoc.getElementById("hidden_embl_against_id").value;
				//alert(emblishment_id_data);return;
				var emb_data = emblishment_id_data.split("_");
				if(emb_data[0]!="")
				{
					freeze_window(5);
					
					$('#lbl_batch_Against').text(batch_against_arr[emb_data[1]]); // For Showing Batch Against
					
					get_php_form_data(emb_data[0]+'_'+emb_data[1], "populate_data_from_embellishment_mst", "requires/subcon_embellishment_production_controller" );
					show_list_view(emb_data[0]+'**'+emb_data[1],'embellishment_details','embellishment_details_container','requires/subcon_embellishment_production_controller','');
					
					release_freezing();
					
					//calculate_batch_qnty();
				}
				
				auto_complete();
				
				fnc_tr_count();
			}
		}
	}
	 
	 
	 
	function auto_complete()
	{
		var company_id = $("#cbo_company_id").val();
		var employee = trim(return_global_ajax_value( company_id, 'employee_name', '', 'requires/subcon_embellishment_production_controller'));
		empCode = eval(employee);
		//var employee_ref=employee[1];
		//employee_arr = JSON.parse(employee_ref); 
		var prevBatchIds='';
		
		$("#embellishment_details_container").find('tr').each(function()
		{
				$('input[name="txtOperatorName[]"]').autocomplete({
					source : empCode
				});
		});
	}
	
	function emp_code_onkeypress( str,row )
	{
		var testStr =  $('#txtOperatorName_'+row).val();
		var inputValue = testStr.search(":");
		if( inputValue == -1) return;
		
		var emp_name_data = str.split(":");
		if(trim(emp_name_data[1])!="")
		{
			$('#txtOperatorName_'+row).val(emp_name_data[0]);
			$('#txtOperatorId_'+row).val(emp_name_data[1]);
		}
		
		/*var emp_name = emp_name_data[0];
		var data=emp_name+"__"+ $('#cbo_company_name').val();
		var d = return_global_ajax_value( data, "emp_code_onkeypress", "", 'requires/subcon_embellishment_production_controller');
		//alert(d);
		 var tmp_coa_id=d.split("_");
		 if( tmp_coa_id[0] != "" ){
			 $('#txt_custody_of_id').val(tmp_coa_id[1]);
			 $('#txt_custody_of').val(tmp_coa_id[2]);
		 }*/
		
	}	 
	 
	 
	 
	 
	function calculate_batch_qnty(i,fild)
	{
		var batchQty = $('#txtBatchQty_'+i).val()*1;
		var rejectQty = $('#txtRejectQty_'+i).val()*1;
		var needRePorcQty = $('#txtNeedReProcQty_'+i).val()*1;
		var resultValue = (batchQty - (rejectQty+needRePorcQty));
		
		if( resultValue >= 0 ){
			
			$('#txtQcPassQty_'+i).val(resultValue);
			
		}else{
			
			if(fild==1){
				$('#txtRejectQty_'+i).val('');
			}else{
				$('#txtNeedReProcQty_'+i).val('');
			}
			
			var batchQty = $('#txtBatchQty_'+i).val()*1;
			var rejectQty = $('#txtRejectQty_'+i).val()*1;
			var needRePorcQty = $('#txtNeedReProcQty_'+i).val()*1;
			var resultValue = (batchQty - (rejectQty+needRePorcQty));
			
			$('#txtQcPassQty_'+i).val(resultValue);
			alert("QC Pass Quantity not more then Batch Quantity !!");
			
		}
	}
	 	
	
	
	//==Start==Bar Code Scan/Right// 
	function barCodeScanFunction(e) 
	{
		if (e.which === 13 || e.charCode === 13 || e.keyCode === 13)
		{
			if (form_validation('cbo_company_id','Company')==false){ 
				$("#txtBatchNumberSerch").val('');
				return;
			}
		    search_batch_by_barcode_scanner();
		}
		
	}	 
		 
	function search_batch_by_barcode_scanner()
	{
		var scanValue = trim($("#txtBatchNumberSerch").val());
		if(scanValue == "") return;
		
		var BatchAgainst = trim($("#hiddenBatchAgainst").val());
		
		var prevBatchIds='';
		$("#tbl_item_details").find('tbody tr').each(function()
		{
			var batch_id=$(this).find('input[name="hiddenBatchId[]"]').val();
			
			if(batch_id!="")
			{
				if(prevBatchIds=="") prevBatchIds=batch_id; else prevBatchIds+=","+batch_id;
			}
		});
		
		var scanDatas = scanValue+"_"+BatchAgainst+"_"+prevBatchIds;
		//alert(scanDatas);
		var responseDatas = return_global_ajax_value( scanDatas, 'check_embl_production', '', 'requires/subcon_embellishment_production_controller');
		var dataArr = responseDatas.split("_");
		
		if( jQuery.inArray( dataArr[1], scanned_barcode )>-1) //Duplicate Batch Scan check
		{ 
			alert('Sorry! Barcode Already Scanned.'); 
			$('#txtBatchNumberSerch').val('');
			return; 
		}
		
			
		if(dataArr[0] == 1)
		{
			
			var hiddenBatchAga = $("#hiddenBatchAgainst").val()*1;
			var companyID=$("#cbo_company_id").val();
			var batchIdNo=$('#txtBatchNo_1').val();
			
			/*var batchIdNo='';
			$("#embellishment_details_container").find('tr').each(function()
			{
				var batchNo=trmi($(this).find('input[name="txtBatchNo[]"]').val());
				
				if(batch_id!="")
				{
					if(batchIdNo=="") batchIdNo=batchNo; else batchIdNo+=","+batchNo;
				}
			});*/
			
			
			scanned_barcode.push(dataArr[1]); //Duplicate Batch Scan check
			//alert(batchIdNo);
			if(batchIdNo=="")
			{
				var tot_row=0;
				$("#embellishment_details_container tr").remove();
			}
			else
			{
				var tot_row=$('#txt_tot_row').val();
			}
			
			/* Batch No Mix privention Validation check*/
			if( hiddenBatchAga != 0 &&  hiddenBatchAga != dataArr[2] )
			{
				$("#txtBatchNumberSerch").val('');
				alert("Batch Number Mix not Allowed !");
				return;
			}
			
			/* Company Mix Validation check*/
			if( companyID != 0 &&  companyID != dataArr[3] )
			{
				$("#txtBatchNumberSerch").val('');
				alert("Company Mix not Allowed !");
				return;
			}
			
			
			$('#lbl_batch_Against').text(batch_against_arr[dataArr[2]]);
			$("#hiddenBatchAgainst").val(dataArr[2]);
			if(companyID==0)
			{
				$("#cbo_company_id").val(dataArr[3]);
				load_drop_down( 'requires/subcon_embellishment_production_controller', dataArr[3], 'load_drop_down_location', 'location_td');
			}
			
			
			var data=dataArr[1]+"**"+tot_row+"**"+dataArr[2];
			var list_view_barcode =return_global_ajax_value( data, 'populate_batch_data', '', 'requires/subcon_embellishment_production_controller');
			$("#embellishment_details_container").prepend(list_view_barcode);
			$("#txtBatchNumberSerch").val('');
			auto_complete();
		}
		else
		{
			$("#txtBatchNumberSerch").val('');
			alert("Batch Number("+scanValue+") not found ... !"); return;
		}
		
		fnc_tr_count();
	}
	//==End==Bar Code Scan/Right// 
	
	
	 function process_popup( process_id ) // Show Process Data in PopUp
	 {
		var hiddenBatchAgainst = $('#hiddenBatchAgainst').val();
		//alert(hiddenBatchAgainst);return;
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/subcon_embellishment_production_controller.php?hiddenBatchAgainst='+hiddenBatchAgainst+'&process_id='+process_id+'&action=process_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=360px,center=1,resize=1,scrolling=0','');
		/*emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txtProcessName_1').val(process_name);
		}*/
	 }
	

	function fn_deleteRow( rid ) // Delete tr from the data
	{
		
		var num_row =$('#embellishment_details_container tr').length;
		
		//var hidBatchId =$("#hiddenBatchId_"+rid).val(); 
		var hidBatchDtlsId =$("#hiddenBatchDtlsId_"+rid).val(); 
		
		var txt_deleted_dtls_id=$('#txt_deleted_dtls_id').val();
		var txtSubConEmbDtlsIds=$('#txtSubConEmbDtlsIds').val();
		
		var updateIdDtls =$("#updateIdDtls_"+rid).val(); 
		
		var bar_code =$("#txtBatchNumberSerch").val();
		
		if(num_row ==1)
		{
			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{	
			$("#tr_"+rid).remove();
		}
		
		
		var selected_id='';
		if(hidBatchDtlsId!='')
		{
			if(txt_deleted_dtls_id=='') selected_id=hidBatchDtlsId; else selected_id=txt_deleted_dtls_id+','+hidBatchDtlsId;
			$('#txt_deleted_dtls_id').val( selected_id );
		}
		
		var selected_dtls_id='';
		if(updateIdDtls!='')
		{
			if(txtSubConEmbDtlsIds=='') selected_dtls_id=updateIdDtls; else selected_dtls_id=txtSubConEmbDtlsIds+','+updateIdDtls;
			$('#txtSubConEmbDtlsIds').val( selected_dtls_id );
		}
		//var index = scanned_barcode.indexOf(bar_code);
		//scanned_barcode.splice(index,1);
	}
	
	
	function refresh_data()//Form Data Refre
	{	
		scanned_barcode = [];
		$('#cbo_company_id').removeAttr('disabled');
		$('#cbo_location_name').removeAttr('disabled');
		$('#cbo_source').removeAttr('disabled');
		$('#cbo_company_supplier').removeAttr('disabled');
		$('#cbo_location_name_s').removeAttr('disabled');
		$('#lbl_batch_Against').text('');
		//$('#embellishment_details_container').children( 'tr:not(:last)' ).remove();
		fnc_reset_form();
		reset_form('embellishmentEntry_1','','','','');
		set_button_status(0, permission, 'fnc_embel_entry',1,1);
	}
	
	function fnc_reset_form()
	{
		$('#embellishment_details_container tr').remove();
		
		var html='<tr class="general" name="tr[]" id="tr_1"><td> <input type="text" name="txtSl[]" id="txtSl_1" class="text_boxes_numeric" style="width:30px" disabled /></td><td> <input type="text" name="txtBatchNo[]" id="txtBatchNo_1" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled /> <input type="hidden" name="hiddenBatchId[]" id="hiddenBatchId_1" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled /> <input type="hidden" name="hiddenBatchDtlsId[]" id="hiddenBatchDtlsId_1" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled /> <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" value="" class="text_boxes_numeric" style="width:50px" placeholder="updateIDDtls"disabled /></td><td> <input type="text" name="txtBatchcolor[]" id="txtBatchcolor_1" class="text_boxes" style="width:80px" placeholder="Display"disabled /></td><td id=""> <input type="text" name="txtPoNo[]" id="txtPoNo_1" class="text_boxes" style="width:100px" placeholder="Display" disabled /><input type="hidden" name="txtPoId[]" id="txtPoId_1" style="width:50px" class="text_boxes" readonly /> </td><td id="party_td"> <input type="text" name="cboJobPartyName[]" id="cboJobPartyName_1" value="" class="text_boxes" style="width:150px" placeholder="Display" readonly disabled /> <input type="hidden" name="cboJobParty[]" id="cboJobParty_1" value="" readonly /></td><td> <input type="text" name="txtStyle[]" id="txtStyle_1" class="text_boxes" style="width:80px" placeholder="Display" disabled /></td><td id=""> <input type="text" name="cboGmtsItem_[]" id="cboGmtsItem_1" value="" class="text_boxes" style="width:80px" placeholder="Display" readonly disabled/> <input type="hidden" name="txtGmtsItem[]" id="txtGmtsItem_1" value="" class="text_boxes" style="width:50px" /></td><td>	<input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:50px;" tabindex="12"  placeholder="Dbl.Click" readonly onDblClick="process_popup();" title="Bbl. Click" /> <input type="hidden" name="txtProcessId[]" id="txtProcessId_1" /></td><td> <input type="text" name="txtBatchQty[]" id="txtBatchQty_1" class="text_boxes_numeric" style="width:50px"  placeholder="Display" readonly /></td><td> <input type="text" name="txtRejectQty[]" id="txtRejectQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:50px" placeholder="Write" /></td><td> <input type="text" name="txtNeedReProcQty[]" id="txtNeedReProcQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();"  placeholder="Write" style="width:50px" /></td><td> <input type="text" name="txtQcPassQty[]" id="txtQcPassQty_1" class="text_boxes_numeric" style="width:50px" readonly /></td><td><input type="text" name="txtOperatorName[]" id="txtOperatorName_1" class="text_boxes" style="width:100px;" onKeyPress="Javascript: if (event.keyCode==40){ emp_code_onkeypress(this.value,1)}" onBlur="emp_code_onkeypress( this.value,1)" placeholder="Write"  tabindex="4" /><input type="hidden" name="txtOperatorId[]" id="txtOperatorId_1" style="width:60px"/></td><td> <? echo create_drop_down( "cboShift_1", 80, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','','cboShift[]'); ?></td> <td> <input type="button" name="btnRowDelete[]" id="btnRowDelete_1" value="-" class="formbutton" onClick="fn_deleteRow(1)" style="width:30px" /></td></tr>';
		
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
	
	
	
	function fnc_tr_count() // All tr count from the form
	{
		/*var lastTrId = $('#embellishment_details_container tr:first').attr('id').split('_');
		var numRow=lastTrId[1];
		$('#txt_tot_row').val(numRow);*/
		
		var numRow = $('#embellishment_details_container tr').length;
		$('#txt_tot_row').val(numRow);
	} 

	 
 </script>
</head>

<body onLoad="set_hotkey();">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../",$permission); ?>
    <div>
    <form name="embellishmentEntry_1" id="embellishmentEntry_1">
         <fieldset style="width:840px;   margin-bottom:10px;">
            <legend>Embellishment Production</legend> 
                <table width="100%" border="0" id="tblEmbellishmentMst"> 
                	<tr>
                        <td colspan="2" align="right">System ID </td>
                        <td colspan="4">
                            <input type="text" name="txtEmbelProductionSerch" id="txtEmbelProductionSerch" class="text_boxes" style="width:150px;" placeholder="Browse" onDblClick="search_embel_production()" tabindex="" />
                             <input type="hidden" name="update_id" id="update_id"/>
                             <input type="hidden" name="txt_deleted_dtls_id" id="txt_deleted_dtls_id"/>
                             <input type="hidden" name="txtSubConEmbDtlsIds" id="txtSubConEmbDtlsIds"/>
                        </td>
                    </tr>
                	<tr>
                    	<th class="must_entry_caption">Company</th>
                        <th>Location</th>
                        <th>Prod. Date</th>
                        <th>Source</th>
                        <th>Serving Company</th>
                        <th>Location</th>
                    </tr>
                    
                    <tr>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"load_drop_down( 'requires/subcon_embellishment_production_controller', this.value, 'load_drop_down_location', 'location_td');",'','','','','',3);
								// load_drop_down( 'requires/subcon_embellishment_production_controller', this.value, 'load_drop_down_party_name', 'party_td' );
                            ?>                              
                        </td>
                        <td width="150" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:100px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" />
                        </td>
                        <td>
                        <?
                       		echo create_drop_down( "cbo_source", 140, $knitting_source,"", 1, "-- Select Source --", 0, "load_drop_down( 'requires/subcon_embellishment_production_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_location_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );load_drop_down( 'requires/subcon_embellishment_production_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_location_name').val(), 'load_drop_down_company_supplier_location', 'issue_to_location_td' );",0,'1,3' );
                    	 ?> 
                            
                        </td>
                        <td id="issue_to_td">
                        <?
                        	echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "-- Select Company --", $selected, "",0 );	
                     	?>                               
                        </td>
                        <td id="issue_to_location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name_s", 140, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                            ?>
                        </td>
                    </tr>
                    
                    <tr > <td colspan="6" align="right">&nbsp;</td> </tr>
                  
                    <tr>
                        <td width="110" colspan="2" class="must_entry_caption" align="right">Search Batch Number </td>
                        <td colspan="4">
                            <input type="text" name="txtBatchNumberSerch" id="txtBatchNumberSerch" class="text_boxes" style="width:150px;" placeholder="Write/Browse/Scan" onDblClick="batch_search_popup()" onKeyDown="barCodeScanFunction(event)"  tabindex="" /> 
                            <label id="lbl_batch_Against" style="font-size:15px;font-weight:bold"></label>
                            
                            
                            <input type="hidden" name="hiddenBatchAgainst" id="hiddenBatchAgainst" style="width:150px;" placeholder="batch Against"  tabindex="" />
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row"  value="1">
                        </td>
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:840px;" >
            	<legend>Batch Details Information</legend>
                <table cellpadding="0" cellspacing="0" width="" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                    	<th>SL</th>
                        <th>Batch Number</th>
                        <th>Batch Color</th>
                        <th class="">Order No / PO No.</th>
                        <th class="">Party Name</th>
                        <th class="">Style</th>
                        <th class="">Gmts Item</th>
                        <th class="">Process</th>
                        <th class="">Batch Qty-Pcs</th>
                        <th>Reject Qty-Pcs</th>
                        <th>Need Re - Process - Pcs</th>
                        <th class="must_entry_caption">QC Pass Qty-Pcs</th>
                        <th>Operator</th>
                        <th>Shift</th>
                        <th>Actin</th>
                    </thead>
                    <tbody id="embellishment_details_container">
                        <tr class="general" name="tr[]" id="tr_1">
                        	<td>
                                <input type="text" name="txtSl[]"  id="txtSl_1" class="text_boxes_numeric" style="width:30px" disabled />
                            </td>
                        	<td>
                                <input type="text" name="txtBatchNo[]"  id="txtBatchNo_1" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                                 <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_1" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                                 <input type="hidden" name="hiddenBatchDtlsId[]"  id="hiddenBatchDtlsId_1" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                                 <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_1" value="" class="text_boxes_numeric" style="width:50px" placeholder="updateIDDtls"disabled />
                            </td>
                            <td>
                                <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_1" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                            </td>
                            
                        	<td id="">	
                            	<input type="text" name="txtPoNo[]"  id="txtPoNo_1" class="text_boxes" style="width:100px"  placeholder="Display" disabled  />
                                <input type="hidden" name="txtPoId[]" id="txtPoId_1" style="width:50px" class="text_boxes" readonly />
                                
                            </td>
                        	<td id="party_td">
                            	<?
                                   //echo create_drop_down( "cboJobParty_1", 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0","id,buyer_name",1, "-- Select Party --",0,"", "1","","","","","","","cboJobParty[]");
                                ?>
                                 <input type="text" name="cboJobPartyName[]"  id="cboJobPartyName_<? echo $tblRow; ?>" value="" class="text_boxes" style="width:150px"  placeholder="Display"  readonly disabled />
                    			<input type="hidden" name="cboJobParty[]"  id="cboJobParty_<? echo $tblRow; ?>" value=""   readonly />
                            </td>
                        	
                            <td>
                            	<input type="text" name="txtStyle[]"  id="txtStyle_1" class="text_boxes" style="width:80px" placeholder="Display" disabled  />
                                <?
                                   // echo create_drop_down( "cboStyle_1", 100, $color_range,"",1, "-- Select --", 0, "" );
                                ?>
                            </td>
                            <td id="">
                                <? //echo create_drop_down( "cboGmtsItem_1", 80, $blank,"", 1, "-- Select Item --", "", "",'1','','','','','','','cboGmtsItem[]'); ?>
                                <input type="text" name="cboGmtsItem_[]"  id="cboGmtsItem_1" value="" class="text_boxes"  style="width:80px" placeholder="Display" readonly disabled/>
                                 <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_1" value="" class="text_boxes"  style="width:50px" />
                            </td>
                            <td>
                                <input type="text" name="txtProcessName[]" id="txtProcessName_1" class="text_boxes" style="width:50px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup();" title="Bbl. Click" />
                                <input type="hidden" name="txtProcessId[]" id="txtProcessId_1" />
								
                        	</td>
                            <td>
                                <input type="text" name="txtBatchQty[]"  id="txtBatchQty_1" class="text_boxes_numeric"  style="width:50px"   placeholder="Display" readonly  />
                            </td>
                            <td>
                                <input type="text" name="txtRejectQty[]"  id="txtRejectQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:50px"   placeholder="Write"  />
                            </td>
                            <td>
                                <input type="text" name="txtNeedReProcQty[]"  id="txtNeedReProcQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();"   placeholder="Write"  style="width:50px" />
                            </td>
                            <td>
                                <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_1" class="text_boxes_numeric"  style="width:50px" readonly />
                            </td>
                            <td>
                            <input type="text" name="txtOperatorName[]" id="txtOperatorName_1" class="text_boxes" style="width:100px;" onKeyPress="Javascript: if (event.keyCode==40){ emp_code_onkeypress(this.value,1)}" onBlur="emp_code_onkeypress( this.value,'1')"   placeholder="Write"   tabindex="4" />
                            <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_1" style="width:60px"/>

                        	</td>
                            <td>
                                <?
									echo create_drop_down( "cboShift_1", 80, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','','cboShift[]');
                                ?>
                            </td>
                             <td>
                                <input type="button" name="btnRowDelete[]"  id="btnRowDelete_1" value="-" class="formbutton" onClick="fn_deleteRow(1)"  style="width:30px"  />
                            </td>
                        </tr>
                    </tbody>
                </table>
             </fieldset>
            
            <table cellpadding="0" cellspacing="1" width="100%">
            	<tr>  <td colspan="6" align="center"></td>	 </tr>
                <tr>
                     <td align="center" colspan="6" valign="middle" class="button_container">
                        <? 
							echo load_submit_buttons($permission, "fnc_embel_entry", 0,0,"refresh_data()",1);
                        ?> 
                    </td>	  
                </tr>
            </table>
        

    </form>
    </div>
<!--    <div id="list_color" style="width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
-->
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>