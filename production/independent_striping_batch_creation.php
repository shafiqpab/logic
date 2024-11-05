<?
/*-------------------------------------------- Commentssss
Purpose			: 	This form will create Independent Striping Batch Creation
Functionality	:
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	16.07.2023
Updated by 		: 	
Update date		: 	
Report by		:	
Creation date 	: 	
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

echo load_html_head_contents("Batch Creation Info", "../", 1, 1,'','','');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();

	function openmypage_fabricBooking()
	{
		var cbo_company_id 		= $('#cbo_company_id').val();
		var batch_against 	= $('#hidden_batch_against').val();
		var sales_batch_flag 	= $('#hidden_is_sales_batch').val();// come form > Batch Number Is FSO Checked

		if (form_validation('cbo_company_id*txt_from_batch_number','Company*From Batch Number')==false)
		{
			return;
		}
		else
		{
			var title 		= 'Booking Selection Form';
			var page_link 	= 'requires/independent_striping_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&sales_batch_flag='+sales_batch_flag+'&action=fabricBooking_popup';

			emailwindow 	= dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform 				= this.contentDoc.forms[0];
				var theemail 				= this.contentDoc.getElementById("hidden_booking_id").value;
				var theename 				= this.contentDoc.getElementById("hidden_booking_no").value;
				var theecolor_id 			= this.contentDoc.getElementById("hidden_color_id").value;
				var theecolor 				= this.contentDoc.getElementById("hidden_color").value;
				var job_no 					= this.contentDoc.getElementById("hidden_job_no").value;
				var booking_without_order 	= this.contentDoc.getElementById("booking_without_order").value;
				var search_type 			= this.contentDoc.getElementById("hidden_search_type").value;
				var within_group 			= this.contentDoc.getElementById("hidden_within_group").value;
				var hidden_sales_id 		= this.contentDoc.getElementById("hidden_sales_id").value;
				var hidden_color_type 		= this.contentDoc.getElementById("hidden_color_type").value;
				var hidden_entry_form 		= this.contentDoc.getElementById("hidden_entry_form").value;
				var hidden_sales_remarks 	= this.contentDoc.getElementById("hidden_sales_remarks").value;
				var is_sales 	 			= this.contentDoc.getElementById("hidden_is_sales").value;

				if(search_type == 7)
				{
					var hidden_sales_booking_no=this.contentDoc.getElementById("hidden_sales_booking_no").value;
					$('#txt_sales_booking_no').val(hidden_sales_booking_no);
					get_php_form_data(hidden_sales_id,'load_process_loss_from_fso','requires/independent_striping_batch_creation_controller' );
				}

				$('#txt_booking_no_id').val(theemail);
				$('#txt_booking_no').val(theename);
				$('#txt_new_batch_color_id').val(theecolor_id);
				$('#txt_new_batch_color').val(theecolor);
				$('#booking_without_order').val(booking_without_order);
				$('#txt_within_group').val(within_group);
				$('#txt_search_type').val(search_type);
				$('#txt_sales_id').val(hidden_sales_id);
				$('#txt_color_type').val(hidden_color_type);
				$('#txt_remarks').val(hidden_sales_remarks);
				$('#hidden_booking_entry_form').val(hidden_entry_form);
			}
		}
	}

	function active_inactive_delete()
	{
		reset_form('','','txt_batch_weight*update_id*txt_batch_sl_no*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*cboDiaWidthType_1*txtRollNo_1*txtSize_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txt_deleted_id*txtQtyPcs_1*txt_total_qtyPcs*cbo_shift_name*txt_dyeing_pdo*txtRemarks_1','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		$('#booking_without_order').val(0);
	}

	function fn_deleteRow(rowNo)
	{
		var numRow = $('#tbl_item_details tbody tr').length;
		//if(numRow==rowNo && rowNo!=1)
		if( numRow==1)
		{
			return false;
		}
		if(rowNo!=0)
		{
			var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';

			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
			var bar_code =$("#barcodeNo_"+rowNo).val();
			var index = scanned_barcode.indexOf(bar_code);
			scanned_barcode.splice(index,1);
			//$('#tbl_item_details tbody tr:last').remove();
			$('#tr_'+rowNo).remove();
		}
		else
		{
			return false;
		}

		calculate_batch_qnty();
		calculate_qtyPcs();
	}

	function calculate_batch_qnty()
	{
		var total_batch_qnty='';
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			var batchQnty=$(this).find('input[name="txtBatchQnty[]"]').val();
			total_batch_qnty=total_batch_qnty*1+batchQnty*1;
		});

		$('#txt_total_batch_qnty').val(total_batch_qnty.toFixed(2));

		var txt_batch_weight = $('#txt_total_batch_qnty').val()*1 + $('#txt_tot_trims_weight').val()*1;
		$("#txt_batch_weight").val(txt_batch_weight.toFixed(2));
	}

	function calculate_qtyPcs()
	{
		var total_qtyPcs='';
		var total_collar_qtyPcs=0;
		var total_cuff_qtyPcs=0;

		$("#tbl_item_details tbody").find('tr').each(function(){
			var qtyPcs=$(this).find('input[name="txtQtyPcs[]"]').val();
			total_qtyPcs=total_qtyPcs*1+qtyPcs*1;

			//for cuff and collar qty pcs.
			var bodyPartId=$(this).find('select[name="cboBodyPart[]"]').val();
			if(body_part_arr[bodyPartId] == 40)
			{
				total_collar_qtyPcs=total_collar_qtyPcs*1+qtyPcs*1;
			}
			else if(body_part_arr[bodyPartId] == 50)
			{
				total_cuff_qtyPcs=total_cuff_qtyPcs*1+qtyPcs*1;
			}
		});

		$('#txt_total_qtyPcs').val(total_qtyPcs);
		$('#txt_collar_qty').val(total_collar_qtyPcs);
		$('#txt_cuff_qty').val(total_cuff_qtyPcs);
	}

	function roll_maintain()
	{
		reset_form('','list_color','txt_new_batch_number*txt_new_batch_color*txt_new_batch_color_id*txt_batch_weight*txt_booking_no*txt_booking_no_id*booking_without_order*txt_batch_color*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txtQtyPcs_1*txtSize_1*txt_total_qtyPcs*cbo_shift_name*txt_dyeing_pdo*txtRemarks_1','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		get_php_form_data($('#cbo_company_id').val(),'roll_maintained','requires/independent_striping_batch_creation_controller' );

		var roll_maintained=$('#roll_maintained').val();

		$('#cboProgramNo_1').text('');
		$('#cboPoNo_1').text('');
		$('#cboItemDesc_1').text('');
		$('#cboBodyPart_1').text('');
		$('#cboDiaWidthType_1').text('');
		$('#txtRollNo_1').text('');
		$('#barcodeNo_1').text('');
		$('#txtPoBatchNo_1').text('');
		$('#cboColorType_1').text('');
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/independent_striping_batch_creation_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_batch_creation(operation)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if(operation==4)
		{
			/*var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_from_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_working_company_id').val(),'batch_card_print','requires/independent_striping_batch_creation_controller');
			return;*/
		}
		
		if( form_validation('txt_from_batch_number','Batch Number')==false )
		{
			alert("Plesae Insert Batch No.");
			$('#txt_from_batch_number').focus();
			return;
		}

		if($('#txt_batch_weight').val()*1 < 0.1)
		{
			alert('Please Insert Batch Weight.');
			$('#txt_batch_weight').focus();
			return;
		}

		if( form_validation('txt_new_batch_number*txt_new_batch_color*cbo_batch_against*cbo_company_id*txt_batch_date*txt_batch_weight*cbo_working_company_id*txt_batch_color*txt_process_name',' New Batch Number*New Batch Color*Batch Against*Company*Batch Date*Batch Weight*Working Company*Batch Color*Process')==false )
		{
			return;
		}

		if($('#txt_booking_no').val()=="")
		{
			alert("Please Select Booking No");
			$('#txt_booking_no').focus();
			return;
		}
		var save_data=$('#save_data').val();

		var txt_batch_weight=$('#txt_batch_weight').val();
		var batch_qty=$('#txt_total_batch_qnty').val()*1+$('#txt_tot_trims_weight').val()*1;
		var tot_batch_weight=Math.round(batch_qty * 1e12) / 1e12;

		if(txt_batch_weight!=tot_batch_weight)
		{
			alert('Batch Weight and Total Batch Qnty+Trims Weight should be same.');
			return;
		}

		if( save_data != "" && $("#txt_tot_trims_weight").val() =="" )
		{
			alert('Total Trims Weight does not syncronized with Trims Weight popup.');
			return;
		}


		var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_item_details tbody tr').length;
		var data_all="";

		var j=0; var breakOut = true; var error=0; error_barcode=all_po_ids='';
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			if(breakOut==false || error==1)
			{
				return;
			}
			var trId = $(this).attr('id').split('_');
			var i=trId[1];

			var roll_maintained_id = $('#roll_maintained').val();
			/*if(roll_maintained_id==1)
			{
				if(operation!=2 && operation!=1)
				{
					if(($('#hiddenRollqty_'+i).val()*1)!=($('#txtBatchQnty_'+i).val()*1))
					{
						alert($('#hiddenRollqty_'+i).val()+"***"+$('#txtBatchQnty_'+i).val())
						error=1;
						error_barcode=$('#barcodeNo_'+i).val();
						return;
					}
				}
			}*/

			if (form_validation('cboItemDesc_'+i+'*cboBodyPart_'+i+'*txtBatchQnty_'+i+'*cboDiaWidthType_'+i,'Item Description*Body Part*Batch Qnty*Dia/ W. Type')==false)
			{
				breakOut = false;
				return false;
			}

			var programNo=$(this).find('input[name="programNo[]"]').val();
			var poId=$(this).find('input[name="poId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var widthDiaType=$(this).find('input[name="widthDiaType[]"]').val();
			var hideRollNo=$(this).find('input[name="hideRollNo[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var batchQty=$(this).find('input[name="batchQty[]"]').val();
			var batchQtyPcs=$(this).find('input[name="batchQtyPcs[]"]').val();
			var itemSize=$(this).find('input[name="itemSize[]"]').val();
			var poBatchNo=$(this).find('input[name="poBatchNo[]"]').val();
			var colorTypeId=$(this).find('input[name="colorTypeId[]"]').val();
			var isSalesOrder=$(this).find('input[name="isSalesOrder[]"]').val();
			var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
			var fromBatchDtlsId=$(this).find('input[name="fromBatchDtlsId[]"]').val();
			var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();

			j++;
			data_all+="&programNo_" + j + "=" +programNo+"&poId_" + j + "=" + poId+"&productId_" + j + "=" + productId+"&bodyPartId_" + j + "=" + bodyPartId+"&cboDiaWidthType_" + j + "=" + widthDiaType+"&txtRollNo_" + j + "=" + hideRollNo+"&rollId_" + j + "=" + rollId+"&barcodeNo_" + j + "='" + barcodeNo+"'"+"&txtBatchQnty_" + j + "='" + $('#txtBatchQnty_'+i).val()+"'"+"&txtQtyPcs_" + j + "='" + $('#txtQtyPcs_'+i).val()+"'"+"&txtSize_" + j + "='" + itemSize+"'"+"&txtPoBatchNo_" + j + "='" + poBatchNo+"'"+"&txtRemarks_" + j + "='" + $('#txtRemarks_'+i).val()+"'"+"&cboColorType_" + j + "=" + colorTypeId+"&updateIdDtls_" + j + "=" + updateIdDtls+"&isSalesOrder_" + j + "=" + isSalesOrder+"&fromBatchDtlsId_" + j + "=" + fromBatchDtlsId;
		});

		if(error==1)
		{
			alert("Roll Qty and Batch Qty not match in Barcode No "+error_barcode);
			return;
		}

		if(breakOut==false)
		{
			return;
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_company_id*batch_maintained*txt_from_batch_number*hiddden_from_batch_id*txt_new_batch_number*txt_batch_date*txt_batch_weight*txt_tot_trims_weight*txt_booking_no_id*txt_booking_no*txt_batch_color*txt_batch_color_id*txt_new_batch_color*txt_new_batch_color_id*cbo_color_range*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*booking_without_order*roll_maintained*txt_remarks*txt_cuff_qty*txt_collar_qty*cbo_machine_name*save_data*txt_sales_booking_no*txt_search_type*txt_sales_id*cbo_working_company_id*txt_process_seq*cbo_floor*unloaded_batch*ext_from*hidden_booking_entry_form*cbo_double_dyeing*cbo_shift_name*txt_dyeing_pdo',"../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;
		// alert(data);return;
		freeze_window(operation);

		http.open("POST","requires/independent_striping_batch_creation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_batch_creation_Reply_info;
	}

	function fnc_batch_creation_Reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_batch_sl_no').value = reponse[2];
				document.getElementById('txt_new_batch_number').value = reponse[3];
				$('#txt_booking_no').attr('disabled',true);
				$('#txt_from_batch_number').attr('disabled',true);
				$('#txt_new_batch_number').attr('readonly',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_working_company_id').attr('disabled',true);
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();

				show_list_view(reponse[1]+'**'+roll_maintained,'batch_details_update','batch_details_container','requires/independent_striping_batch_creation_controller','');

				$('#txt_deleted_id').val('');
				set_button_status(1, permission, 'fnc_batch_creation',1);
			}
			else if(reponse[0]==2)
			{
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				active_inactive_delete();
				set_button_status(0, permission, 'fnc_batch_creation',1);
			}
			release_freezing();
		}
	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Batch No Selection Form';
			var page_link = 'requires/independent_striping_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				var ext_from=this.contentDoc.getElementById("hidden_ext_from").value;
				var batch_against=this.contentDoc.getElementById("hidden_batch_against").value;
				var sales_batch=this.contentDoc.getElementById("hidden_sales_batch").value;
				$('#hidden_is_sales_batch').val(sales_batch);// come form > Batch Number Is FSO Checked
				$('#hidden_batch_against').val(sales_batch);
				$('#hiddden_from_batch_id').val(batch_id);
				//alert(unloaded_batch);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_id+'**'+batch_no+'**'+cbo_company_id+'**'+unloaded_batch+'**'+ext_from, "populate_data_from_search_popup", "requires/independent_striping_batch_creation_controller" );

					show_list_view(batch_id+'**'+roll_maintained,'batch_details','batch_details_container','requires/independent_striping_batch_creation_controller','');
					release_freezing();
					if(roll_maintained==1)
					{

					}
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
					calculate_qtyPcs();
				}
			}
		}
	}

	function openmypage_newBatchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Batch No Selection Form';
			var page_link = 'requires/independent_striping_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&action=new_batch_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				var ext_from=this.contentDoc.getElementById("hidden_ext_from").value;
				var batch_against=this.contentDoc.getElementById("hidden_batch_against").value;
				var sales_batch=this.contentDoc.getElementById("hidden_sales_batch").value;
				$('#hidden_is_sales_batch').val(sales_batch);// come form > Batch Number Is FSO Checked
				$('#hidden_batch_against').val(sales_batch);
				$('#hiddden_from_batch_id').val(batch_id);
				//alert(unloaded_batch);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_id+'**'+batch_no+'**'+cbo_company_id, "populate_data_from_search_popup_new_batch", "requires/independent_striping_batch_creation_controller" );

					show_list_view(batch_id+'**'+roll_maintained,'batch_details_update','batch_details_container','requires/independent_striping_batch_creation_controller','');
					release_freezing();
					if(roll_maintained==1)
					{

					}
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
					calculate_qtyPcs();
				}
			}
		}
	}

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var txt_process_seq = $('#txt_process_seq').val();


		var title = 'Process Name Selection Form';
		var page_link = 'requires/independent_striping_batch_creation_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;

			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
			$('#txt_process_seq').val(process_seq);
		}
	}
	
	function fnc_move_cursor(val,id, field_id,lnth,max_val) // Duration Req.
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}

		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}

	function openmypage_trims()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();

		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}

		var page_link='requires/independent_striping_batch_creation_controller.php?save_data='+save_data+'&action=trims_weight_popup';
		var title='Trims Weight ';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var save_data=this.contentDoc.getElementById("save_data").value;
			var tot_trims_wgt=this.contentDoc.getElementById("tot_trims_qnty").value;
			$('#save_data').val(save_data);
			$('#txt_tot_trims_weight').val( tot_trims_wgt );
			calculate_batch_qnty();
			calculate_qtyPcs();
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
		<div style="width:1070px; float:left" align="center">
			<fieldset style="width:1070px;">
				<legend>Batch Creation</legend>
				<form name="batchcreation_1" id="batchcreation_1">
					<fieldset style="width:1000px;">
						<table width="1269" align="center" border="0">
							<tr>
								<td width="110" colspan="4" align="right"><b>Batch Serial No</b></td>
								<td colspan="2">
									<input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
									<!-- <input type="hidden" name="is_approved_id" id="is_approved_id" value=""> -->
								</td>
							</tr>
							<tr><td></td></tr>
							<tr>								
								<td class="must_entry_caption">Booking Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 144, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/independent_striping_batch_creation_controller' );roll_maintain();",'','','','','',3);
									?>
								</td>
								<td width="100" class="must_entry_caption">From Batch Number</td>
								<td>
									<input type="text" name="txt_from_batch_number" id="txt_from_batch_number" class="text_boxes" style="width:132px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo()" tabindex="4" readonly="" />
                                <input type="hidden" name="hiddden_from_batch_id" id="hiddden_from_batch_id"/>
								</td>
								<td class="must_entry_caption">From Batch Color</td>
								<td>
									<input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:132px;" tabindex="10" disabled />
									<input type="hidden" name="txt_batch_color_id" id="txt_batch_color_id" class="text_boxes" />
								</td>
								<td  width="130" class="must_entry_caption">FSO/Book. No</td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:134px;" placeholder="Double Click To Search" onDblClick="openmypage_fabricBooking();" readonly tabindex="9"/>
									<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id"/>
									<input type="hidden" name="txt_sales_id" id="txt_sales_id"/>
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
									<input type="hidden" name="txt_sales_booking_no" id="txt_sales_booking_no"/>
									<input type="hidden" name="txt_color_type" id="txt_color_type"/>
									<input type="hidden" name="hidden_booking_entry_form" id="hidden_booking_entry_form"/>
								</td>
                                <td class="must_entry_caption">Working Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_working_company_id", 144, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Working--', 0,"load_drop_down('requires/independent_striping_batch_creation_controller',this.value, 'load_drop_down_floor', 'td_floor' );",'','','','','',3);
									?>
								</td>
							</tr>
							<tr>
								<td width="100" class="must_entry_caption">New Batch Number</td>
								<td>
									<input type="text" name="txt_new_batch_number" id="txt_new_batch_number" class="text_boxes" placeholder="Double Click To Edit" onDblClick="openmypage_newBatchNo()" style="width:132px;" />
								</td>
								<td class="must_entry_caption">New Batch Color</td>
								<td>
									<input type="text" name="txt_new_batch_color" id="txt_new_batch_color" class="text_boxes" value="" style="width:132px;" tabindex="10" disabled />
									<input type="hidden" name="txt_new_batch_color_id" id="txt_new_batch_color_id" />
								</td>
								<td>Color Range</td>
								<td>
									<?
									echo create_drop_down( "cbo_color_range", 144, $color_range,"",1, "-- Select --", 0, "" );
									?>
								</td>
								<td width="110" class="must_entry_caption">Batch Weight </td>
								<td>
									<input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:132px;" tabindex="7" disabled />
								</td>
								<td>Total Trims Weight</td>
								<td>
									<input type="text" name="txt_tot_trims_weight" id="txt_tot_trims_weight" class="text_boxes_numeric" style="width:132px;" tabindex="8" onDblClick="openmypage_trims()" placeholder="Double Click To Search" readonly />
									<input type="hidden" name="save_data" id="save_data" class="text_boxes" style="width:100px;">
								</td>
							</tr>
                            <tr>
								<td class="must_entry_caption">Batch Against</td>
								<td>
									<?
									echo create_drop_down( "cbo_batch_against", 144, $dyeing_re_process,"", 1, '--- Select ---', 1, "",1,'3','','','',1 );
									?>
								</td>
								<td>Collar Qty (Pcs)</td>
								<td>
									<input type="text" name="txt_collar_qty" id="txt_collar_qty" class="text_boxes_numeric" style="width:132px;"/>
								</td>
								<td>Cuff Qty (Pcs)</td>
								<td>
									<input type="text"  name="txt_cuff_qty" id="txt_cuff_qty" class="text_boxes_numeric" style="width:132px;"/>
								</td>
								<td> Multi Dyeing</td>
								<td>
									<?

									echo create_drop_down("cbo_double_dyeing", 144, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","");
									?>
								</td>
								<td>Duration Req.</td>
								<td>
									<input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:56px;" />&nbsp;
									<input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:56px;" />
								</td>
							</tr>
							<tr>
								<td>Floor</td>
								<td id="td_floor">
									<?
									echo create_drop_down("cbo_floor", 144, $blank_array,"", 1, "-- Select Floor--", 0, "",0,"","","","");
									?>
								</td>
								<td>Dyeing Machine</td>
								<td id="td_dyeing_machine"><?
									echo create_drop_down("cbo_machine_name", 144, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","","");?>
								</td>
								<td>Process Name</td>
								<td colspan="6">
									<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:630px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
									<input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
									<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="" />
								</td>
							</tr>
							<tr>
								<td>Shift Name</td>
								<td>
									<?

									echo create_drop_down("cbo_shift_name", 144, $shift_name,"", 1, "-- Select --", 0, "",0,"","","","");
									?>
								</td>
								<td>Dyeing PDO</td>
								<td><input type="text" name="txt_dyeing_pdo" id="txt_dyeing_pdo" class="text_boxes" style="width:132px;" /></td>
								<td class="must_entry_caption">Batch Date</td>
								<td>
									<input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:132px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" />
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td colspan="2">
									<?
									include("../terms_condition/terms_condition.php");
									terms_condition(64,'txt_batch_sl_no','../','txt_sales_id');
									?>
								</td>
							</tr>
							<tr>
								<td>Remarks</td>
								<td colspan="10"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:1140px;" /></td>
							</tr>
						</table>
                    </fieldset>
                    <fieldset style="width:1110px; margin-top:10px">
                         <legend>Item Details</legend>
                        <table cellpadding="0" cellspacing="0" width="1310" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                            <thead>
                                <th>SL</th>
                                <th>Program No</th>
                                <th class="must_entry_caption">PO No./FSO No</th>
                                <th class="must_entry_caption">Item Description</th>
                                <th class="must_entry_caption">Body Part</th>
                                <th class="must_entry_caption">Dia/ W. Type</th>
                                <th>Roll No.</th>
                                <th>Barcode No.</th>
                                <th class="must_entry_caption">Batch Qnty</th>
                                <th>Qty In Pcs</th>
                                <th>Item Size</th>
                                <th>PO Batch No</th>
                                <th>Color Type</th>
                                <th>Remarks</th>
                                <th></th>
                            </thead>
                            <tbody id="batch_details_container">
                                <tr id="tr_1">
                                    <td id="slTd_1" width="30">1</td>
                                    <td style="word-break:break-all;" width="80" id="cboProgramNo_1"></td>
                                    <td style="word-break:break-all;" width="130" id="cboPoNo_1"></td>
                                    <td style="word-break:break-all;" width="180" id="cboItemDesc_1"></td>
                                    <td style="word-break:break-all;" width="120" id="cboBodyPart_1"></td>
                                    <td style="word-break:break-all;" width="90" id="cboDiaWidthType_1"></td>
                                    <td style="word-break:break-all;" width="50" id="txtRollNo_1"></td>
                                    <td style="word-break:break-all;" width="70" id="barcodeNo_1"></td>
                                    <td>
                                        <input type="text" name="txtBatchQnty[]" id="txtBatchQnty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" />
                                    </td>
                                    <td>
                                        <input type="text" name="txtQtyPcs[]" id="txtQtyPcs_1" class="text_boxes_numeric" onKeyUp="calculate_qtyPcs();" style="width:60px;" disabled />
                                    </td>
                                    <td style="word-break:break-all;" width="60" id="txtSize_1"></td>
                                    <td style="word-break:break-all;" width="45" id="txtPoBatchNo_1"></td>
                                    <td style="word-break:break-all;" width="100" id="cboColorType_1"></td>
                                    <td>
                                        <input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:100px;" />
                                    </td>
                                    <td width="65">
                                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                        
                                        <!-- <input type="hidden" name="hiddenRollqty[]" id="hiddenRollqty_1"/> -->
                                        <input type="hidden" name="programNo[]" id="programNo_1"/>
										<input type="hidden" name="poId[]" id="poId_1"/>
										<input type="hidden" name="productId[]" id="productId_1"/>
										<input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
										<input type="hidden" name="widthDiaType[]" id="widthDiaType_1""/>
										<input type="hidden" name="hideRollNo[]" id="hideRollNo_1"/>
										<input type="hidden" name="rollId[]" id="rollId_1"/>
										<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
										<input type="hidden" name="batchQty[]" id="batchQty_1"/>
						                <input type="hidden" name="batchQtyPcs[]" id="batchQtyPcs_1"/>
						                <input type="hidden" name="itemSize[]" id="itemSize_1"/>
						                <input type="hidden" name="poBatchNo[]" id="poBatchNo_1"/>
										<input type="hidden" name="colorTypeId[]" id="colorTypeId_1"/>
										<input type="hidden" name="fromBatchDtlsId[]" id="fromBatchDtlsId_1"/>
										<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_1"/>
										<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1"/>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Sum</td>
                                <td><input type="text" name="txt_total_batch_qnty" id="txt_total_batch_qnty" class="text_boxes_numeric" style="width:60px" readonly /></td>
                                <td><input type="text" name="txt_total_qtyPcs" id="txt_total_qtyPcs" class="text_boxes_numeric" style="width:60px" readonly /></td>
                                <td><input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" readonly /></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tfoot>
                        </table>
                    </fieldset>
                    <table width="1140">
                        <tr>
                            <td colspan="5" align="center" class="button_container">
                                <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons($permission, "fnc_batch_creation",0,0,"reset_form('batchcreation_1','list_color','','txt_batch_date,".$date."','disable_enable_fields(\'txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no*cbo_shift_name*txt_dyeing_pdo\',0)');$('#txt_from_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();",1);
                                ?>
                                <input type="hidden" name="update_id" id="update_id"/>
                                <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                                <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                                <input type="hidden" name="batch_maintained" id="batch_maintained" readonly>
                                <input type="hidden" name="hide_job_no" id="hide_job_no" readonly><!--For Duplication Check-->
                                <input type="hidden" name="txt_within_group" id="txt_within_group" readonly><!--For Duplication Check-->
                                <input type="hidden" name="txt_search_type" id="txt_search_type" readonly><!--For Duplication Check-->
                                <input type="hidden" name="unloaded_batch" id="unloaded_batch" readonly>
                                <input type="hidden" name="ext_from" id="ext_from" readonly>
                                <!-- come form > Batch Number popup- Is FSO Checked hidden_is_sales_batch-->
                                <input type="hidden" name="hidden_is_sales_batch" id="hidden_is_sales_batch" readonly>
                                <input type="hidden" name="hidden_batch_against" id="hidden_batch_against" readonly>
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_color" style="width:330px; overflow:auto; float:left; margin-left:90px; padding-top:5px; margin-top:5px; position:relative;"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>