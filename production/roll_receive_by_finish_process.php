<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll Receive by Finish Process
Functionality	:	
JS Functions	:
Created by		:	Jahid Hasan	
Creation date 	: 	6.12.2016
Updated by 		: 	
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Roll Receive by Finish Process","../", 1, 1, $unicode,"","", 1); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	function open_mrrpopup()
	{
		var cbo_company_id = $("#cbo_company_id").val();
		var page_link='requires/roll_receive_by_finish_process_controller.php?cbo_company_id='+cbo_company_id+'&action=update_system_popup';
		var title='Roll Receive by Finish Process';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0];
			
			var roll_recv_no = this.contentDoc.getElementById("hidden_receive_no").value;
			var receive_data = roll_recv_no.split("**");
			var roll_recv_date = this.contentDoc.getElementById("hidden_receive_date").value;
			var batch_no = this.contentDoc.getElementById("hidden_batch_no").value;
			var company_id = this.contentDoc.getElementById("cbo_company_id").value;
			$("#txt_system_no").val(receive_data[0]);
			$("#txt_delivery_date").val(roll_recv_date);
			$("#txt_batch_no").val(batch_no);
			$("#txt_company_no").val(company_id);

			$("#cbo_knitting_source").val(receive_data[1]);
			$("#txt_knit_company").val(receive_data[2]);

			if(receive_data[0] != "")
			{
				show_list_view(receive_data[0] + '_' + company_id, 'populate_barcode_data_update', 'scanning_tbl','requires/roll_receive_by_finish_process_controller', '' );
				set_button_status(1, permission, 'fnc_role_receive_by_finish_process',1);
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe','requires/roll_receive_by_finish_process_controller.php?action=barcode_popup','Barcode Popup', 'width=930px,height=390px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			var batch_ids = this.contentDoc.getElementById("hidden_batch_id").value; //Batch Nos
			var selected_batch_id = this.contentDoc.getElementById("hiddenBatchId").value; //Batch Nos
			var company_id = this.contentDoc.getElementById("cbo_company_id").value; // Company ID
			$("#txt_company_no").val(company_id);
			var pre_batch = $("#txt_batch_no").val();
			var pre_batch_id = $("#txt_batch_id").val();
			if(pre_batch == ''){
				$("#txt_batch_no").val(batch_ids);
				$("#txt_batch_id").val(selected_batch_id);
			}else{
				if($("#txt_batch_no").val() == batch_ids){
					$("#txt_batch_no").val(batch_ids);
					$("#txt_batch_id").val(selected_batch_id);
				}
			}
			
			if(barcode_nos != "")
			{
				create_row(barcode_nos,batch_ids,selected_batch_id);
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/roll_receive_by_finish_process_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_role_receive_by_finish_process( operation )
	{
		// if(form_validation('txt_delivery_date') == false)
		//  {
		//  	return; 
		//  }
		var is_valid = false;
		if(operation==4) // print
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#txt_company_no').val()+'*'+$('#txt_batch_no').val()+'*'+$('#txt_system_no').val()+'*'+$('#txt_delivery_date').val() +'*'+$('#cbo_knitting_source').val()+'*'+$('#txt_knit_company').val(),'roll_receive_finish_print');
			return;
		}

		var companyNo = $('#companyNo').val();
		var productionId = $("#productionId").val();
		var productionDtlsId = $("#productionDtlsId").val();;
		var knitingSource = $("#knitingSource").val();
		var knitingCompany = $("#knitingCompany").val();
		var bookingNo = $("#bookingNo").val();
		var batchNo = $("#txt_batch_no").val();
		var batchID = $("#txt_batch_id").val();
		var delivery_date = $("#txt_delivery_date").val();
		var txt_system_no = $('#txt_system_no').val();
		var txt_deleted_id = $('#txt_deleted_id').val();
		var j = 0; var dataString = '';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
			if(barcodeNo != ''){
				is_valid = true;
			}
			var productId = $(this).find('input[name="productId[]"]').val();
			var orderId = $(this).find('input[name="orderId[]"]').val();			
			var rollId = $(this).find('input[name="rollId[]"]').val();
			var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
			var rollNo = $(this).find("td:eq(1)").text();
			var gsm = $(this).find("td:eq(5)").text();
			var dia = $(this).find("td:eq(6)").text();
			var const_comp = $(this).find("td:eq(4)").text();
			var roll_weight = $(this).find("td:eq(8)").text();
			var sub_dtls_id = $(this).find('input[name="sub_dtls_id[]"]').val();
			
			dataString += '&barcodeNo' + j + '=' + barcodeNo + '&productId' + j + '=' + productId + '&orderId' + j + '=' + orderId 
			+ '&rollId' + j + '=' + rollId + '&rollNo' + j + '=' + rollNo + '&gsm' + j + '=' + gsm + '&dia' + j + '=' + dia 
			+ '&const_comp' + j + '=' + const_comp + '&roll_weight' + j + '=' + roll_weight + '&sub_dtls_id' + j + '=' + sub_dtls_id;

			j++;
		});
		if(is_valid == true){
			var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('txt_delivery_date',"../")
			+ '&companyNo=' + companyNo + '&productionId=' + productionId + '&productionDtlsId=' + productionDtlsId + '&knitingSource=' + knitingSource 
			+ '&knitingCompany=' + knitingCompany + '&bookingNo=' + bookingNo + '&batchID=' + batchID + '&batchNo=' + batchNo + '&delivery_date=' + delivery_date + '&txt_system_no=' + txt_system_no + '&txt_deleted_id=' + txt_deleted_id + dataString;


			freeze_window(operation);

			http.open("POST","requires/roll_receive_by_finish_process_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_role_receive_by_finish_process_reply_info;
		}else{
			alert("Invalid Barcode!");
			return;
		}
	}

	function fnc_role_receive_by_finish_process_reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			 if((response[0]==0 || response[0]==1))
			{
				$('#txt_deleted_id').val( '' );
				$('#txt_system_no').val(response[1]);
				set_button_status(1, permission, 'fnc_role_receive_by_finish_process',1);
			}
			release_freezing();
		}
	}

	function fn_deleteRow( rid )
	{
		var bar_code =$("#barcodeNo_"+rid).val();
		var sub_dtls_id =$("#sub_dtls_id_"+rid).val();

		var num_row =$('#scanning_tbl tbody tr').length;
		var txt_deleted_id=$('#txt_deleted_id').val();
		
		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:last-child)').each(function(index, element) {
				$(this).html('');
			});
			
			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}
		
		calculate_total();
		
		var selected_id='';
		if(sub_dtls_id!='')
		{
			if(txt_deleted_id=='') selected_id=sub_dtls_id; else selected_id=txt_deleted_id+','+sub_dtls_id;
			$('#txt_deleted_id').val( selected_id );
		}
	}

	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		document.getElementById("rollscanning_1").reset();
		$('form#rollscanning_1 input[type=hidden]').val('');
		set_button_status(0, permission, 'fnc_role_receive_by_finish_process',1);
	}

	function create_row(barcode_no, batch_no, batch_id)
	{
		var row_num = $('#txt_tot_row').val();
		var barcode_nos = trim(barcode_no);
		var batch_nos = trim(batch_no).split(",");
		var batch_ids = trim(batch_id).split(",");
		
		var barcode_data = trim(return_global_ajax_value(barcode_nos, 'populate_barcode_data', '', 'requires/roll_receive_by_finish_process_controller'));

		if(barcode_data == 0)
		{
			alert('Barcode is Not Valid');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}
		
		var barcode_datas = barcode_data.split("_");
		for(var k = 0; k<barcode_datas.length; k++)
		{
			var data 				= barcode_datas[k].split("**");
			var bar_code 			= data[0];
			var mst_id 				= data[1];
			var company_id 			= data[2];
			var body_part 			= data[3];
			var booking_no 			= data[4];
			var knitting_source 	= data[5];
			var knitting_company_id	= data[6];
			var knitting_company 	= data[7];
			var location_id 	 	= data[8];
			var dtls_id 			= data[9];
			var prod_id 			= data[10];
			var gsm 				= data[11];
			var width 				= data[12];
			var roll_id 			= data[13];
			var roll_no 			= data[14];
			var po_breakdown_id 	= data[15];
			var color_name 			= data[16];
			var qnty 				= data[17];
			var prodQnty 			= data[18];
			var mechine_id 			= data[19];
			var composition 		= data[20];
			var po_id 				= data[21];
			var buyer_id 			= data[22];
			var buyer_name 			= data[23];
			var po_no 				= data[24];
			var job_no				= data[25];
			var year 				= data[26];
			
			var batchNo = $('#txt_batch_no').val();
			if(batchNo == "")
			{
				$('#txt_batch_no').val(batch_nos);
				$('#txt_batch_id').val(batch_ids);

			}
			else
			{
				var batch_id_prev = $('#txt_batch_no').val();
				if(batch_id_prev != batch_nos)
				{
					alert("Multiple Batch Not Allowed");
					return;	
				}				

				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({ 
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }              
					});
				}).end().prependTo("#scanning_tbl");
				
				$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
			}
			$("#sl_"+row_num).text(row_num);
			$("#batch_"+row_num).text(batch_nos[k]);
			$("#batchId_"+row_num).val(batch_ids[k]);
			$("#barcode_"+row_num).text(bar_code);
			$("#program_"+row_num).text(booking_no);
			$("#bodypart_"+row_num).text(body_part);
			$("#knitSource_"+row_num).text(knitting_source);
			$("#knitcomp_"+row_num).text(knitting_company);
			$("#prodId_"+row_num).text(prod_id);
			$("#year_"+row_num).text(year);
			$("#job_"+row_num).text(job_no);
			$("#buyer_"+row_num).text(buyer_name);
			$("#order_"+row_num).text(po_no);
			$("#composition_"+row_num).text(composition);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#roll_"+row_num).text(roll_no);
			$("#prodQty_"+row_num).text(prodQnty);
			$("#mc_"+row_num).text(mechine_id);
			$("#rollweight_"+row_num).text(qnty);
			$("#color_"+row_num).text(color_name);			
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#productId_"+row_num).val(prod_id);
			$("#orderId_"+row_num).val(po_breakdown_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#sub_"+row_num).val('');

			$("#productionId").val(mst_id);
			$("#productionDtlsId").val(dtls_id);			
			$("#knitingSource").val(knitting_source);
			$("#knitingCompany").val(knitting_company_id);
			$("#companyNo").val(company_id);
			$("#dtlsId").val(dtls_id);
			$("#bookingNo").val(booking_no);

			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			$('#currentDelivery_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","check_qty("+row_num+");");

			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
		}
	}

	$(document).on('keydown','#txt_bar_code_num', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code = $(this).val();
			var batch_no = trim(return_global_ajax_value( bar_code, 'get_barcode_batch_no', '', 'requires/roll_receive_by_finish_process_controller'));			
			create_row(bar_code,batch_no);
		}
	});

	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
		$("#roll_weight_total").text(total_roll_weight.toFixed(2));
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
		<form name="rollscanning_1" id="rollscanning_1"  autocomplete="off">
			<fieldset style="width:810px;">
				<legend>Barcode Scanning</legend>
				<table cellpadding="0" cellspacing="2" width="800">
					<tr>
						<td align="center" colspan="5"  width="100">System ID 
							<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="open_mrrpopup()" placeholder="Browse For System No" />
						</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption" width="100">Delivery Date</td>
						<td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" readonly /></td> 
						<td align="right">Dyeing Source </td>
						<td><? echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); ?></td>
						<td align="right">Dyeing Company</td>
						<td id="knitting_com">
							<input type="text" name="txt_knit_company" id="txt_knit_company" class="text_boxes" style="width:140px;" placeholder="Display" disabled/>
							<input type="hidden" name="knit_company_id" id="knit_company_id"/>
						</td>
					</tr>
					<tr>
						<td height="5" colspan="6"></td>
					</tr>
					<tr>
						<td align="center" colspan="6"><strong>Barcode Number</strong>&nbsp;&nbsp;
							<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/Scan"/>
							<input type="hidden" name="txt_batch_no" id="txt_batch_no" class="text_boxes_numeric" style="width:170px;"/>
							<input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes_numeric" style="width:170px;"/>
							<input type="hidden" name="txt_company_no" id="txt_company_no" class="text_boxes_numeric" style="width:170px;"/>
						</td>
					</tr>
				</table>
			</fieldset>
			<br>
			<fieldset style="width:1500px;text-align:left">
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
				<input type="hidden" name="companyNo" id="companyNo"/>
				<input type="hidden" name="bookingNo" id="bookingNo"/>
				<input type="hidden" name="knitingSource" id="knitingSource"/>
				<input type="hidden" name="knitingCompany" id="knitingCompany"/>
				<input type="hidden" name="batchNo" id="batchNo"/>
				<input type="hidden" name="productionId" id="productionId"/>
				<input type="hidden" name="productionDtlsId" id="productionDtlsId"/>
				<table cellpadding="0" width="1390" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Roll No</th>
						<th width="80">Barcode No</th>
						<th width="100">Body Part</th>
						<th width="130">Const./ Composition</th>
						<th width="40">GSM</th>
						<th width="45">Dia</th>
						<th width="100">Color</th>
						<th width="40">Roll Wgt.</th>
						<th width="80">Job No</th>
						<th width="40">Year</th>
						<th width="55">Buyer</th>
						<th width="130">Order No</th>
						<th width="40">File No</th>
						<th width="75">Knitting Company</th>
						<th width="75">M/C No</th>                  
						<th width="85">Booking/ Programm No</th>
						<th width="50">Batch No.</th>
						<th></th>
					</thead>
				</table>
				<div style="width:1420px; max-height:250px; overflow-y:scroll" align="left">
					<table cellpadding="0" cellspacing="0" width="1390" border="1" id="scanning_tbl" rules="all" class="rpt_table">
						<tbody>
							<tr id="tr_1" align="center" valign="middle">
								<td width="30" id="sl_1"></td>
								<td width="50" id="roll_1"></td>
								<td width="80" id="barcode_1"></td>
								<td width="100" id="bodypart_1"></td>
								<td width="130" id="composition_1"></td>
								<td width="40" id="gsm_1"></td>
								<td width="45" id="dia_1"></td>
								<td width="100" id="color_1"></td>
								<td width="40" id="rollweight_1"></td>
								<td width="80" id="job_1"></td>
								<td width="40" id="year_1"></td>
								<td width="55" id="buyer_1"></td>
								<td width="130" id="order_1"></td>
								<td width="40" id="file_1"></td>
								<td width="75" id="knitcomp_1"></td>
								<td width="75" id="mc_1"></td>
								<td width="85" id="program_1" align="right"></td>
								<td width="50" id="batch_1" align="right"></td>
								<td id="button_1" align="center">
									<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
									<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
									<input type="hidden" name="productId[]" id="productId_1"/>
									<input type="hidden" name="rollId[]" id="rollId_1"/>
									<input type="hidden" name="orderId[]" id="orderId_1"/>
									<input type="hidden" name="sub_dtls_id[]" id="sub_dtls_id_1" />
									<input type="hidden" name="batchId[]" id="batchId_1" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table width="1320" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
					<tr>
						<td align="center" class="button_container">
							<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
							<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
							<input type="hidden" name="txt_deleted_roll_id" id="txt_deleted_roll_id" class="text_boxes" value="">
							<? 
							echo load_submit_buttons($permission,"fnc_role_receive_by_finish_process",0,1,"fnc_reset_form()",1);
							?>
						</td>
					</tr>  
				</table>
			</fieldset>
		</form>	 
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>