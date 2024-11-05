<?
/*-------------------------------------------- Comments
Purpose			: This form will create Grey Fabric Roll Issue To Subcon
				
Functionality	:	
JS Functions	:
Created by		: Fuad
Creation date 	: 23-02-2015
Updated by 		: Zaman		
Update date		: 18.12.2019   
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
echo load_html_head_contents("Grey Fabric Issue Roll Wise","../../", 1, 1, $unicode,'',''); 
$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1)
	{
		window.location.href = "../../logout.php";
	}
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	
	//save update
	function fnc_grey_roll_issue_to_subcon( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		/*if(operation==4)
		{
			
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subcon_issue_print','requires/aop_roll_receive_entry_controller');
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print');
			return;
		}*/
		
	 	if(form_validation('cbo_company_id*cbo_service_source*cbo_service_company*txt_issue_date*cbo_process*txt_wo_no','Company*Service Source*Service Company*Issue Date*Process*WO No')==false)
		{
			return; 
		}
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();

			var gsm=$(this).find('input[name="hiddenGsm[]"]').val();
			var diaWidth=$(this).find('input[name="hiddenDiaWidth[]"]').val();
			var jobNo=$(this).find('input[name="hiddenJob[]"]').val();

			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWeightInput[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
		
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();

			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var widthDiaType=$(this).find('input[name="widthDiaType[]"]').val();
			var serviceCompany=$(this).find('input[name="serviceCompany[]"]').val();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var bookingNo=$(this).find('input[name="bookingNo[]"]').val();
			var determinationId=$(this).find('input[name="determinationId[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var dtlsIsSales=$(this).find('input[name="dtlsIsSales[]"]').val();

			j++;
			dataString+='&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&colorId_' + j + '=' + colorId + '&dtlsId_' + j + '=' + dtlsId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&batchId_' + j + '=' + batchId+ '&hiddenQtyInPcs_' + j + '=' + hiddenQtyInPcs+ '&bodyPartId_' + j + '=' + bodyPartId+ '&widthDiaType_' + j + '=' + widthDiaType+ '&serviceCompany_' + j + '=' + serviceCompany+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&jobNo_' + j + '=' + jobNo+ '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder+ '&bookingNo_' + j + '=' + bookingNo+ '&determinationId_' + j + '=' + determinationId+ '&buyerId_' + j + '=' + buyerId+ '&dtlsIsSales_' + j + '=' + dtlsIsSales;
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_issue_date*cbo_process*txt_batch_no*txt_batch_id*update_id*txt_deleted_id*txt_wo_no*txt_attention*txt_remarks*hdn_is_sales*hidden_wo_entry_form',"../../")+dataString;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_roll_issue_to_subcon_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_roll_issue_to_subcon_Reply_info;
	}

	function fnc_grey_roll_issue_to_subcon_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if(response[0]*1==20*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_issue_no').value = response[2];
				var cbo_company_id = $('#cbo_company_id').val();
				//show_list_view(response[1]+"_"+cbo_company_id, 'update_greyRollIssueToProcess_details', 'scanning_tbody','requires/grey_fabric_roll_issue_to_subcon_controller', '' );	
				$('#txt_deleted_id').val( '' );
				$('#txt_wo_no').attr('disabled',true);
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);
			}
			$("#cbo_service_company").attr('disabled','disabled');
			$("#cbo_process").attr('disabled','disabled');
			$("#cbo_service_source").attr('disabled','disabled');
			release_freezing();
		}
	}
	
	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		var textile_sales_maintain = $('#textile_sales_maintain').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_fabric_roll_issue_to_subcon_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup', 'Issue Popup', 'width=890px, height=350px, center=1, resize=1, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/grey_fabric_roll_issue_to_subcon_controller");

				var hdn_is_sales = $('#hdn_is_sales').val()*1; // Master part is sales flag which may differ from details part
				var hidden_wo_entry_form = $('#hidden_wo_entry_form').val()*1; // fso wise fabric service work order in sub-contact
				if((hdn_is_sales==2 && textile_sales_maintain==1) || hidden_wo_entry_form == 418 || hidden_wo_entry_form == 696)
				{
					show_list_view(issue_id+"_"+cbo_company_id, 'update_greyRollIssueToProcess_detailsAop_sales', 'scanning_tbody','requires/grey_fabric_roll_issue_to_subcon_controller', '' );	
				}
				else
				{
					show_list_view(issue_id+"_"+cbo_company_id, 'update_greyRollIssueToProcess_details', 'scanning_tbody','requires/grey_fabric_roll_issue_to_subcon_controller', '' );	
				}
				

				set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);
			}
			  	var rollWgtTotal = 0;
                $("#scanning_tbl").find('tbody tr').each(function () {
               
                    rollWgtTotal += $(this).find('input[name="rollWeightInput[]"]').val() * 1;
                });
                $("#total_rollWgt").html(number_format(rollWgtTotal, 2));
				
				$("#cbo_service_company").attr('disabled','disabled');
				$("#cbo_process").attr('disabled','disabled');
				$("#cbo_service_source").attr('disabled','disabled');
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id = $('#cbo_company_id').val();
		var po_ids = $("#txt_po_ids").val();
		var batch_id = $("#txt_batch_id").val();
		var cbo_service_source = $("#cbo_service_source").val();
		var txt_wo_no = $("#txt_wo_no").val();
		var cbo_process = $("#cbo_process").val();
		var cbo_service_company = $("#cbo_service_company").val();
		var hdn_is_sales = $("#hdn_is_sales").val();
		var textile_sales_maintain = $("#textile_sales_maintain").val();
		var hidden_wo_entry_form = $('#hidden_wo_entry_form').val();
		//alert(po_ids);
		if(batch_id=="")
		{
			batch_id=0;
		}
		else
		{
			batch_id=batch_id;
		}
			
		if (form_validation('cbo_company_id*txt_wo_no*cbo_service_source','Company*WO No*Service Source')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_roll_issue_to_subcon_controller.php?company_id='+company_id+'&po_ids='+po_ids+'&batch_id='+batch_id+'&txt_wo_no='+txt_wo_no+'&cbo_service_source='+cbo_service_source+'&cbo_process='+cbo_process+'&cbo_service_company='+cbo_service_company+'&hdn_is_sales='+hdn_is_sales+'&textile_sales_maintain='+textile_sales_maintain+'&hidden_wo_entry_form='+hidden_wo_entry_form+'&action=barcode_popup','Barcode Popup', 'width=850px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				fnc_create_row(barcode_nos);
			}
		}
	}
	
	$('#txt_bar_code_num').live('keydown', function(e){
		if (e.keyCode === 13) 
		{
			if(form_validation('txt_wo_no*cbo_service_source*cbo_process','WO No*Service Source*Process')==false)
			{
				$('#txt_bar_code_num').val('');
				return; 
			}
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			fnc_create_row(bar_code);
		}
	});

	function fnc_create_row(barcode_no)
	{
		var rollWgtTotal = 0;
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		var cbo_service_company = $('#cbo_service_company').val();
		var txt_po_ids = $('#txt_po_ids').val();
		var txt_wo_no = $('#txt_wo_no').val();
		var cbo_process = $('#cbo_process').val();
		var hdn_is_sales = $('#hdn_is_sales').val();
		var textile_sales_maintain = $('#textile_sales_maintain').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var barcodeNo = trim(barcode_no);
		var row_num = $('#txt_tot_row').val();
		var num_row = $('#scanning_tbl tbody tr').length;
		
		
		var newBarcode='';
		var msg=0;
		var barcode_da=barcodeNo.split(',');
		 $("#scanning_tbl").find('tbody tr').each(function() {
            for (var kk = 0; kk < barcode_da.length; kk++) {
                var barcodeNos = $(this).find('input[name="barcodeNo[]"]').val();
                if(trim(barcodeNos) == barcode_da[kk]){
                    msg++;
                    return;
                }
                else
				{
					if(newBarcode !='')
					{
						newBarcode += ',';
					}
					newBarcode += barcode_da[kk];
				}
            }
        });
        if(msg>0){
            alert("Barcode already scanned");
            return;
        }

		if(newBarcode == '')
		{
			return;
		}
		
		if($('#hidden_wo_entry_form').val() ==696)
		{
			//fso wise fabric service work order
			//var barcode_data=trim(return_global_ajax_value_post(newBarcode+"__"+cbo_company_id+"__"+txt_wo_no+"__"+txt_po_ids+"__"+cbo_service_company+"__"+hdn_is_sales, 'populateBarcode_Data_FabFsoServiceWO', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));

			var barcode_data=trim(return_global_ajax_value_post(cbo_company_id+"__"+$("#txt_wo_no").val()+"__"+$("#cbo_service_source").val()+"__"+$("#cbo_service_company").val()+"__"+newBarcode+"__"+cbo_process, 'populateBarcode_Data_FabFsoServiceWO', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));
		}

		else if(textile_sales_maintain ==1 && hdn_is_sales ==2)
		{
			var barcode_data=trim(return_global_ajax_value_post(newBarcode+"__"+cbo_company_id+"__"+txt_wo_no+"__"+txt_po_ids+"__"+cbo_service_company+"__"+hdn_is_sales, 'populateBarcodeDataAopSales', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));
		}

		else if(cbo_service_source ==3 && cbo_process ==31)
		{
			//Outbound data Here...
			var barcode_data=trim(return_global_ajax_value_post(newBarcode+"__"+cbo_company_id+"__"+txt_wo_no+"__"+txt_po_ids+"__"+cbo_service_company+"__"+hdn_is_sales, 'populateBarcodeDataOutbound', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));
		}
		else
		{
			var barcode_data=trim(return_global_ajax_value(newBarcode+"__"+cbo_company_id, 'populateBarcodeData', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));
		}

		var spltBarcode=trim(barcode_data).split('__');

		if(spltBarcode[0]==0)
		{
			alert('Barcode is Not Valid...');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}

		if(spltBarcode[0]==1)
		{
			alert(spltBarcode[1]);
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$('#messagebox_main', window.parent.document).html(spltBarcode[1]).removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}		
		
		if(spltBarcode[0]==101)
		{
			alert("Sorry! Barcode Already Scanned.");
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}

		for(var k = 0; k<spltBarcode.length; k++)
		{
			var spltData = spltBarcode[k].split("**");
			var barCode = spltData[0];
			var rollNo = spltData[1];
			var batchNo = spltData[2];
			var prodId = spltData[3];
			var bodyPart = spltData[4];
			var construction = spltData[5];
			var gsm = spltData[6];
			var width = spltData[7];
			var colorName = spltData[8];
			var diaType = spltData[9];
			var rollWeight = spltData[10];
			var qtyInPcs = spltData[11];
			var buyerName = spltData[12];
			var jobNo = spltData[13];
			var orderNo = spltData[14];
			var knittingCompany = spltData[15];
			var receiveBasisDtls = spltData[16];
			var bookingNo = spltData[17];
			var rollMstId = spltData[18];
			var rollDtlsId = spltData[19];
			var colorId = spltData[20];
			var companyId = spltData[21];
			var receiveBasisId = spltData[22];
			var orderId = spltData[23];
			var batchId = spltData[24];
			var bookingId = spltData[25];

			var body_part_id = spltData[26];
			var width_dia_type = spltData[27];
			var service_company = spltData[28];
			var bookingWithoutOrder = spltData[29];
			var determinationId = spltData[30];
			var buyerId = spltData[31];
			var rollId = spltData[32];
			var dtlsIsSales = spltData[33];
		
			//company checking
			/*if(cbo_company_id != companyId)
			{
				alert('Multiple Company Not Allowed');
				return;	
			}*/
			
			//new tr creating here
			if($('#barcodeNo_'+row_num).val() != '')
			{
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
			
			//value assigning here
			$("#sl_"+row_num).text(row_num);
			$("#barcode_"+row_num).text(barCode);
			$("#roll_"+row_num).text(rollNo);
			$("#batchNo_"+row_num).text(batchNo);
			$("#prodId_"+row_num).text(prodId);
			$("#bodyPart_"+row_num).text(bodyPart);
			$("#cons_"+row_num).text(construction);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#color_"+row_num).text(colorName);
			$("#diaType_"+row_num).text(diaType);
			//$("#rollWeight_"+row_num).text(rollWeight);
			$("#rollWeightInput_"+row_num).val(rollWeight);
			$('#rollWeightInput_'+row_num).removeAttr("onBlur").attr("onBlur","fnc_qnty_check("+row_num+");");
			if($('#hidden_wo_entry_form').val()==696)
			{
				$('#rollWeightInput_'+row_num).attr("disabled","disabled");
			}
			$("#qtyInPcs_"+row_num).text(qtyInPcs);
			$("#buyer_"+row_num).text(buyerName);
			$("#job_"+row_num).text(jobNo);
			$("#order_"+row_num).text(orderNo);
			$("#basis_"+row_num).text(receiveBasisDtls);
			$("#progBookPiNo_"+row_num).text(bookingNo);
			
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			$("#barcodeNo_"+row_num).val(barCode);
			$("#progBookPiId_"+row_num).val(bookingId);
			$("#productId_"+row_num).val(prodId);
			$("#orderId_"+row_num).val(orderId);
			$("#batchId_"+row_num).val(batchId);
			$("#rollNo_"+row_num).val(rollNo);
			$("#rollId_"+row_num).val(rollId);
			$("#rollWgt_"+row_num).val(rollWeight);
			$("#colorId_"+row_num).val(colorId);
			$("#dtlsId_"+row_num).val(rollDtlsId);
			$("#rolltableId_"+row_num).val(rollMstId);
			$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
			
			$("#bodyPartId_"+row_num).val(body_part_id);
			$("#widthDiaType_"+row_num).val(width_dia_type);
			$("#serviceCompany_"+row_num).val(service_company);
			$("#hiddenGsm_"+row_num).val(gsm);
			$("#hiddenDiaWidth_"+row_num).val(width);
			$("#hiddenJob_"+row_num).val(jobNo);
			$("#bookingWithoutOrder_"+row_num).val(bookingWithoutOrder);
			$("#bookingNo_"+row_num).val(bookingNo);
			$("#determinationId_"+row_num).val(determinationId);
			$("#buyerId_"+row_num).val(buyerId);
			$("#dtlsIsSales_"+row_num).val(dtlsIsSales);

			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();

			rollWgtTotal += rollWeight * 1;
		}
		$("#total_rollWgt").html($("#total_rollWgt").text() * 1 + rollWgtTotal);

	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/grey_fabric_roll_issue_to_subcon_controller.php?data=" + data+'&action='+action, true );
	}
	
	function add_dtls_data( data )
	{
		var barcode_dtlsId_array=new Array();
		var barcode_rollTableId_array=new Array();
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var roll_table_id=datas[2];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_rollTableId_array[barcode_no] = roll_table_id;
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="" || dtlsId==0) 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}
	
	function fn_deleteRow( rid )
	{
		var num_row =$('#scanning_tbl tbody tr').length;
		var bar_code =$("#barcodeNo_"+rid).val();
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		
		if(num_row==1)
		{
			return;
			$('#tr_'+rid+' td:not(:last-child)').each(function(index, element){
				$(this).html('');
			});
			
			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}
		
		/*var selected_id='';
		if(rolltableId!='')
		{
			if(selected_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );
		}*/

		var selected_id='';
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );
		}

		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);

		var rollWgtTotal = 0;
        $("#scanning_tbl").find('tbody tr').each(function () {
       
            rollWgtTotal += $(this).find('input[name="rollWeightInput[]"]').val() * 1;
        });
        $("#total_rollWgt").html(number_format(rollWgtTotal, 2));      
	}
	
	function openmypage_batchNo()
	{
		var po_ids = $("#txt_po_ids").val();
		//alert(po_ids);
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/grey_fabric_roll_issue_to_subcon_controller.php?cbo_company_id='+cbo_company_id+'&po_ids='+po_ids+'&action=batch_number_popup';
			var title='Batch Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"
				
				if(batch_id!="")
				{
					$('#txt_batch_no').val(batch_no);
					$('#txt_batch_id').val(batch_id);
				}
			}
		}
	}
	
	function check_batch(data)
	{
		if(data=="")
		{
			$('#txt_batch_id').val('');
			return;	
		}
		var cbo_company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
		var batch_id=return_global_ajax_value( data+"**"+cbo_company_id, 'check_batch_no', '', 'requires/grey_fabric_roll_issue_to_subcon_controller');
		if(batch_id==0)
		{
			alert("Batch No Found");
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
	}

	function fnc_reset_dtls_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="70" id="batchNo_1"></td><td width="60" id="prodId_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="diaType_1"></td><td width="70" id="rollWeight_1" align="right"><input class="text_boxes_numeric" style="width:60px; text-align:right;" onBlur="fnc_qnty_check(1);" type="text" name="rollWeightInput[]" id="rollWeightInput_1"/></td><td width="70" id="qtyInPcs_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/></td><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="widthDiaType[]" id="widthDiaType_1"/><input type="hidden" name="serviceCompany[]" id="serviceCompany_1"/><input type="hidden" name="hiddenDiaWidth[]" id="hiddenDiaWidth_1"/><input type="hidden" name="hiddenGsm[]" id="hiddenGsm_1"/><input type="hidden" name="hiddenJob[]" id="hiddenJob_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/><input type="hidden" name="bookingNo[]" id="bookingNo_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="rollNo[]" id="rollNo_1"/><input type="hidden" name="dtlsIsSales[]" id="dtlsIsSales_1"/></td></tr>';
		$("#scanning_tbl tbody").html(html);
		$("#total_rollWgt").html("");
	}
	
	function fnc_reset_form()
	{
		fnc_reset_dtls_form();
		
		$('#txt_wo_no').val("");
		$('#txt_po_ids').val("");
		$('#hdn_is_sales').val("");
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_service_source').val(0);
		$('#cbo_service_company').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_process').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		
	}
	
	function wo_no_popup()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var cbo_company_id = $('#cbo_company_id').val();
		var page_link='requires/grey_fabric_roll_issue_to_subcon_controller.php?cbo_company_id='+cbo_company_id+'&action=wo_no_issue_popup';
		var title='Service WO Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_ids=this.contentDoc.getElementById("hidden_challan_no").value;
			var process_id=this.contentDoc.getElementById("hidden_challan_id").value;
			var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
			var hidden_comp_id=this.contentDoc.getElementById("hidden_comp_id").value;
			var hidden_source=this.contentDoc.getElementById("hidden_source").value;
			var hidden_suppplier=this.contentDoc.getElementById("hidden_suppplier").value;
			var hidden_is_sales=this.contentDoc.getElementById("hidden_is_sales").value;
			var hidden_wo_entry_form=this.contentDoc.getElementById("hidden_wo_entry_form").value;
			
			$("#txt_wo_no").val(hidden_booking_no);
			$("#txt_po_ids").val(po_ids);
			$("#cbo_company_id").val(hidden_comp_id);
			$("#cbo_service_source").val(hidden_source);
			$("#hdn_is_sales").val(hidden_is_sales);

			if(hidden_source){
				$("#cbo_service_source").attr('disabled','disabled');
			}
			//alert(hidden_comp_id);
			var datas=hidden_source+'**'+hidden_suppplier;
			load_drop_down( 'requires/grey_fabric_roll_issue_to_subcon_controller', datas, 'load_drop_down_knitting_com', 'dyeing_company_td' );
			if(trim(process_id)!="")
			{
				load_drop_down( 'requires/grey_fabric_roll_issue_to_subcon_controller', process_id, 'load_process', 'process_td' );
			}

			var process_id_arr=process_id.split(",");
			if(process_id_arr.length ==1)
			{
				$("#cbo_process").val(process_id);
				$("#cbo_process").attr('disabled','disabled');
			}

			if(hidden_suppplier){
				$("#cbo_service_company").val(hidden_suppplier);
				$("#cbo_service_company").attr('disabled','disabled');
			}
			fnc_reset_dtls_form();

			$("#hidden_wo_entry_form").val(hidden_wo_entry_form);
			if(hidden_wo_entry_form==696)
			{
				//fso wise fabric service wo data here
				/*var barcode_data=trim(return_global_ajax_value_post(cbo_company_id+"__"+$("#txt_wo_no").val()+"__"+$("#cbo_service_source").val()+"__"+$("#cbo_service_company").val()+"__"+$("#cbo_process").val(), 'populate_barcode_fab_fso_service_booking_wo', '', 'requires/grey_fabric_roll_issue_to_subcon_controller'));

				$("#scanning_tbody").html(barcode_data);

				var total_rollWgt=0;
				$("#scanning_tbl").find('tbody tr').each(function() {
					var rollWeightInput = $(this).find('input[name="rollWeightInput[]"]').val()*1;
					total_rollWgt = total_rollWgt + rollWeightInput;
				});

				$("#total_rollWgt").text(total_rollWgt);
				*/
			}
		}
	}
	function fnc_qnty_check(id)
	{
		var numRow =$('#scanning_tbl tbody tr').length;
		var rollQnty =$("#rollWgt_"+id).val()*1;
		var editableRollQnty =$("#rollWeightInput_"+id).val()*1;

		if(isNaN($("#rollWeightInput_"+id).val()) === false)
		{
			if(rollQnty<editableRollQnty)
			{
				alert("Not allowed quantity more than roll Wgt.");
				$("#rollWeightInput_"+id).val(rollQnty);
				return;
			}
		}
		else
		{
			$("#rollWeightInput_"+id).val(rollQnty);
			return;
		}
	}

	function company_wise_load(company_id)
	{
		$("#txt_po_ids").val("");
		$("#txt_wo_no").val("").removeAttr('disabled', 'disabled');
		$("#hdn_is_sales").val("");
		$("#textile_sales_maintain").val("");
		$("#txt_issue_no").val("");
		$("#cbo_service_source").val(0);
		$("#cbo_service_company").val(0);
		$("#cbo_process").val(0);

		get_php_form_data( company_id,'company_wise_load' ,'requires/grey_fabric_roll_issue_to_subcon_controller');
		
		fnc_reset_dtls_form();
		set_button_status(0, permission, 'fnc_grey_roll_issue_to_subcon',1);
	}

	function print_report() // print 1
	{
		if ($('#update_id').val()=="") 
		{
			alert("Please Save First.");return;
		}
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subcon_issue_print','requires/aop_roll_receive_entry_controller');
		return;		
	}
	function gray_fabric_no_of_copy() // print 2
	{
		if ($('#update_id').val()=="") 
		{
			alert("Please Save First.");return;
		}

		// Press OK to show Summery and Press Cancel to Show Barcode wise
		var show_report_format = "0";
        var r = confirm("Press \"OK\" to show Summery.\nPress \"Cancel\" to Show Barcode wise.");
        if (r == true)
		{
            show_report_format = "1";
        }
        else
		{
            show_report_format = "0";
        }
		var report_title = $("div.form_caption").html();
		generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_issue_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_service_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#cbo_service_company").val() + '*' + show_report_format, 'roll_issue_no_of_copy_print');
        return;
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<?php echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="2"></td>
                        <td align="center"><b>Subcon Issue No</b></td>
                        <td>
                        	<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                      <tr>
                        <td align="right" class="must_entry_caption">WO No</td>
                        <td>
                        	<input type="hidden" name="txt_po_ids" id="txt_po_ids"/>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:140px;" onDblClick="wo_no_popup()" placeholder="Browse" readonly />
							<input type="hidden" name="hdn_is_sales" id="hdn_is_sales"/>
							<input type="hidden" name="textile_sales_maintain" id="textile_sales_maintain"/>
							<input type="hidden" name="hidden_wo_entry_form" id="hidden_wo_entry_form"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "company_wise_load(this.value)",0 );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_roll_issue_to_subcon_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Issue Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right" class="must_entry_caption" width="100">Process</td>
                        <td id="process_td">
							<? 
								echo create_drop_down( "cbo_process", 152, $conversion_cost_head_array,"", 1, "-- Select Process --", 11, "","","" ); 
							?>
                        </td>
                        <td align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Write / Browse" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                        </td>
                    </tr>
                    <!--<tr>
                    	<td height="5" colspan="6"></td>
                    </tr>-->
                    <tr>
                    	<td align="right">Attention</td>
                        <td>
                        	<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:140px;" placeholder="Write" />
                        </td>
                        <!-- <td colspan="2"></td> -->
                        <td align="right"><strong>Roll Number</strong></td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right">Remarks</td>
                        <td>
                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;" placeholder="Write" />
                        </td>
                        <!-- <td colspan="2"></td> -->
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1330px;text-align:left">
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
				<table cellpadding="0" width="1330" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="70">Batch No</th>
                        <th width="60">Product Id</th>
                        <th width="80">Body Part</th>
                        <th width="150">Construction/ Composition</th>
                        <th width="50">GSM</th>
                        <th width="50">Dia</th>
                        <th width="70">Color</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="70">Roll Wgt.</th>
                        <th width="70">Qty. In Pcs</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="100">Program/ Booking/PI No</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1330px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1310" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="30" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="50" id="roll_1"></td>
                                <td width="70" id="batchNo_1"></td>
                                <td width="60" id="prodId_1"></td>
                                <td width="80" style="word-break:break-all;" id="bodyPart_1"></td>
                                <td width="150" style="word-break:break-all;" id="cons_1" align="left"></td>
                                <td width="50" style="word-break:break-all;" id="gsm_1"></td>
                                <td width="50" style="word-break:break-all;" id="dia_1"></td>
                                <td width="70" style="word-break:break-all;" id="color_1"></td>
                                <td width="70" style="word-break:break-all;" id="diaType_1"></td>
                                <td width="70" id="rollWeight_1" align="right">
                                	<input style="width: 60px;text-align: right;" onBlur="fnc_qnty_check(1);" class="text_boxes_numeric" type="text" name="rollWeightInput[]" id="rollWeightInput_1"/>
                                </td>
                                <td width="70" align="right" id="qtyInPcs_1"></td>
                                <td width="60" style="word-break:break-all;" id="buyer_1"></td>
                                <td width="80" style="word-break:break-all;" id="job_1"></td>
                                <td width="80" style="word-break:break-all;" id="order_1" align="left"></td>
                                
                                <td width="100" style="word-break:break-all;" id="progBookPiNo_1"></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="widthDiaType[]" id="widthDiaType_1"/>
                                    <input type="hidden" name="serviceCompany[]" id="serviceCompany_1"/>
                                    <input type="hidden" name="hiddenGsm[]" id="hiddenGsm_1"/>
                                    <input type="hidden" name="hiddenDiaWidth[]" id="hiddenDiaWidth_1"/>
                                    <input type="hidden" name="hiddenJob[]" id="hiddenJob_1"/>
                                    <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                                    <input type="hidden" name="bookingNo[]" id="bookingNo_1"/>
                                    <input type="hidden" name="determinationId[]" id="determinationId_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="rollNo[]" id="rollNo_1"/>
                                    <input type="hidden" name="dtlsIsSales[]" id="dtlsIsSales_1"/>
                                    <input type="hidden" name="dtlsIsSales[]" id="dtlsIsSales_1"/>
                                </td>
                            </tr>
                        </tbody>
                	</table>
					<table cellpadding="0" cellspacing="0" width="1310" border="1" id="scanning_tbl" rules="all" class="rpt_table">
						<tfoot>
						    <tr>
				                <th width="30"></th>
		                        <th width="80"></th>
		                        <th width="50"></th>
		                        <th width="70"></th>
		                        <th width="60"></th>
		                        <th width="80"></th>
		                        <th width="150"></th>
		                        <th width="50"></th>
		                        <th width="50"></th>
		                        <th width="70"></th>
		                        <th width="70">Total</th>
		                        <th width="70" id="total_rollWgt"></th>
		                        <th width="70"></th>
		                        <th width="60"></th>
		                        <th width="80"></th>
		                        <th width="80"></th>
		                        <th width="100"></th>
		                        <th></th>
						    </tr>
						</tfoot>
					</table>
                </div>
               



                <br>
                <table width="1310" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <?php echo load_submit_buttons($permission,"fnc_grey_roll_issue_to_subcon",0,0,"fnc_reset_form()",1);?>
                            
                            <input type="button" class="formbutton" name="print1" id="print1" style=" width:80px; display:none" value="Print 1" onClick="print_report()"/>
                            <input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;"/>
                			<input type="button" class="formbutton" name="print2" id="print2" style=" width:80px; display:none" value="Print 2" onClick="gray_fabric_no_of_copy()"/>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
