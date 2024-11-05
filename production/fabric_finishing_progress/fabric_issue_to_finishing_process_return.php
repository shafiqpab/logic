<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	30-08-2022
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
echo load_html_head_contents("Grey Fabric Issue Roll Wise","../../", 1, 1, $unicode,'',''); 
$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_booking_arr=new Array();
	var scanned_barcode=new Array();
	var scanned_batch_arr=new Array();

 	<? 
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_rollTableId_array=array();
	?>

	function openmypage_challan_no()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_issue_to_finishing_process_return_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_data_from_issue", "requires/fabric_issue_to_finishing_process_return_controller");
				show_list_view(issue_id+"_"+cbo_company_id+"_"+cbo_service_source, 'grey_item_details_from_issue', 'scanning_tbody','requires/fabric_issue_to_finishing_process_return_controller', '' );	
				//set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);
			}
		}
	}
		
	function openmypage_return()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_issue_to_finishing_process_return_controller.php?cbo_company_id='+cbo_company_id+'&action=return_popup','Recv. Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var return_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var order_type=this.contentDoc.getElementById("hidden_order_type").value;
			
			if(return_id!="")
			{
				fnc_reset_form();
				get_php_form_data(return_id, "populate_data_from_data", "requires/fabric_issue_to_finishing_process_return_controller");
				show_list_view(return_id+"_"+cbo_company_id+"_"+cbo_service_source, 'grey_item_details_update', 'scanning_tbody','requires/fabric_issue_to_finishing_process_return_controller', '' );	
				set_button_status(1, permission, 'fnc_grey_roll_receive_from_subcon',1);
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/fabric_issue_to_finishing_process_return_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_grey_roll_receive_from_subcon( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title,'fabric_receive_print','requires/aop_roll_receive_entry_controller');
			return;
		}
		
	 	if(form_validation('txt_issue_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_return_date','Challan No*Company*Service Source*Service Company*Return Date')==false)
		{
			return; 
		}
		
		var j=0; var dataString=''; var error=0;var privCurrentQty=batchQty=0; 
		$("#scanning_tbl").find('tbody tr').each(function()
		{				
			var prifix=$(this).attr("id").split("_");
			var id=prifix[1];
			var txtReturnQty=$(this).find('input[name="txtReturnQty[]"]').val()*1;
			if(txtReturnQty>0)
			{
				var txtBatchNo=$(this).find("td:eq(1)").text();
				var cboProcess=$(this).find('select[name="cboProcess[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var bodypartId=$(this).find('input[name="bodypartId[]"]').val();
				var determinationId=$(this).find('input[name="determinationId[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var woRate=$(this).find('input[name="woRate[]"]').val();
				var bookingNo=$(this).find('input[name="txtBookingNo[]"]').val();
				var finDia=$(this).find('input[name="finDia[]"]').val();
				var finGsm=$(this).find('input[name="finGsm[]"]').val();
				var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
				;
				var batchQty=$(this).find('input[name="batchWgt[]"]').val()*1;
				var batchId=$(this).find('input[name="batchId[]"]').val()*1;
				var jobNo=$(this).find('input[name="hiddnJobNo[]"]').val();
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var bookingDtlsId=$(this).find('input[name="bookingDtlsId[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
				if(operation==0)
				{
					var privCurrentQty=$(this).find('input[name="privCurrentQty[]"]').val()*1;
					var batchQty=$(this).find('input[name="batchWgt[]"]').val()*1;
					//$prev_recv_balance_qty<= $po_batch_qty) 
					if(privCurrentQty>=batchQty || batchQty==0)
					{
						//alert('G');
						//alert('Recv Qty is over then Batch Qty.\n'+'RecvQty:'+privCurrentQty+'\n BatchQty:'+batchQty);
						//error=1;
						//return; 
					}
				}

				j++;
				dataString+='&cboProcess_' + j + '=' + cboProcess + '&determinationId_' + j + '=' + determinationId + '&buyerId_' + j + '=' + buyerId + '&orderId_' + j + '=' + orderId + '&txtBatchNo_' + j + '=' + txtBatchNo + '&txtbatchId_' + j + '=' + batchId + '&jobNo_' + j + '=' + jobNo + '&colorId_' + j + '=' + colorId + '&dtlsId_' + j + '=' + dtlsId + '&bodypartId_' + j + '=' + bodypartId + '&txtReturnQty_' + j + '=' + txtReturnQty+ '&tr_' + j + '=' + id+ '&woRate_' + j + '=' + woRate+ '&bookingNo_' + j + '=' + bookingNo+ '&bookingDtlsId_' + j + '=' + bookingDtlsId+ '&batchQty_' + j + '=' + batchQty+ '&finDia_' + j + '=' + finDia+ '&finGsm_' + j + '=' + finGsm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder+ '&txtRemarks_' + j + '=' + txtRemarks;
			}
		});
		
		//alert(dataString);
		if(error==1)
		{
			return;
		}
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*txt_issue_id*txt_woorder_no*txt_return_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_return_date*txt_return_challan*txt_remarks*update_id',"../../")+dataString;
		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/fabric_issue_to_finishing_process_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_roll_receive_from_subcon_Reply_info;
	}

	function fnc_grey_roll_receive_from_subcon_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if (response[0]==20) 
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_return_no').value = response[2];
				$('#txt_issue_no').attr('disabled','true');
				add_dtls_data(response[3]);
				var cbo_service_source = $('#cbo_service_source').val();
				show_list_view($('#update_id').val()+"_"+$('#cbo_company_id').val()+"_"+cbo_service_source, 'grey_item_details_update', 'scanning_tbody','requires/fabric_issue_to_finishing_process_return_controller', '' );
				set_button_status(1, permission, 'fnc_grey_roll_receive_from_subcon',1);
			}
			release_freezing();
		}
	}

	function add_dtls_data( data )
	{
		var batch_datas=data.split(",");
		for(var k=0; k<batch_datas.length; k++)
		{
			var datas=batch_datas[k].split("__");
			var tr_no=datas[0];
			var dtls_id=datas[1];
			$("#dtlsId_"+tr_no).val(dtls_id);
		}
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="25" id="sl_1"></td><td width="60" id="txtBatchNo_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="120" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="fabColor_1"></td><td style="word-break:break-all;" width="60" id="diaType_1"></td><td width="120" align="right" id=""><? echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" );?></td><td style="word-break:break-all;" width="60" id="batchWeight_1"></td><td style="word-break:break-all;" width="60" id="txtRollNo_1"></td><td width="60" align="center" id=""><input type="text" id="txtReturnQty_1" name="txtReturnQty[]"  style=" width:40px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="internalRef_1" align="left"></td><td style="word-break:break-all;" width="" align="left"><input type="text" id="txtRemarks_1" name="txtRemarks[]"  class="text_boxes" value=""><input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_<? echo $i; ?>" value=""/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="bodypartId[]" id="bodypartId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="currencyId[]" id="currencyId_1"/><input type="hidden" name="woRate[]" id="woRate_1"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/><input type="hidden" name="finDia[]" id="finDia_1"/><input type="hidden" name="finGsm[]" id="finGsm_1"/><input type="hidden" name="batchWgt[]" id="batchWgt_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/><input type="hidden" name="txtReturnQtyHidden[]" id="txtReturnQtyHidden_1"/></td></tr>';


		$('#txt_issue_no').val('');
		$('#txt_issue_id').val('');
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_service_source').val(0);
		$('#cbo_service_company').val(0);
		$('#update_id').val('');
		$('#txt_return_no').val('');
		$('#txt_return_challan').val('');
		$('#txt_return_date').val('');
		$('#txt_woorder_no').val('');
		$('#txt_woorder_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}

	function calculate_amount(id)
	{
		var issue_qnty = $("#txtReturnQty_"+id).attr("placeholder")*1;//50
		var tot_rcv = $("#totalReturnQty_"+id).val()*1;//30
		var curr_rcv = $("#txtReturnQty_"+id).val()*1;
		var hidden_curr_rcv = $("#txtReturnQtyHidden_"+id).val()*1;

		if(hidden_curr_rcv != 0) // update event
		{
			// alert(tot_rcv +'+'+ curr_rcv +'-'+ hidden_curr_rcv);
			// var totRcv = (tot_rcv + curr_rcv ) - hidden_curr_rcv ;
			var totRcv = curr_rcv;
		}
		else // save event
		{
			// alert(curr_rcv);
			var totRcv = curr_rcv;
		}
		
		if(totRcv > issue_qnty)
		{
			$("#txtReturnQty_"+id).val($("#txtReturnQtyHidden_"+id).val()*1);
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:1010px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="1000">
                    <tr>
                        <td colspan="3" align="right"><b>Issue Return&nbsp;</b>
                        </td>
                         <td colspan="3" align="left">
                        	<input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_return()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">Challan No</td>
                        <td>
                        	<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:130px;" placeholder="Browse" onDblClick="openmypage_challan_no();" readonly="" />
                            <input type="hidden" id="txt_issue_id" />
                        </td>

                    	<td align="right">Work Order No</td>
                        <td>
                        	<input type="text" name="txt_woorder_no" id="txt_woorder_no" class="text_boxes" style="width:130px;" placeholder="Display" readonly="" disabled="" />
                            <input type="hidden" id="txt_woorder_id" />
                        </td>

                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );
                            ?>
                        </td>

                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 142, $knitting_source, "", 1, "-- Select --", 0, "load_drop_down( 'requires/fabric_issue_to_finishing_process_return_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                    </tr>
                    <tr>                    	
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 142, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>

                    	<td align="right" class="must_entry_caption" width="100">Return Date</td>
                        <td><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:130px;"  placeholder="Return Date" readonly /></td>
                        
                        <td align="right"  width="">Return Challan No</td>
                        <td><input type="text" name="txt_return_challan" id="txt_return_challan" class="text_boxes" style="width:140px;" placeholder="Return Challan No" />
                        </td>

                        <td align="right">Remarks</td>
                        <td>
                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:130px;" placeholder="Remarks"/>
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
				<table cellpadding="0" width="1480" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="25">SL</th>
                        <th width="60">Batch No</th>
                        <th width="80">Body Part</th>
                        <th width="120">Construction/ Composition</th>
                        <th width="50">Fin. gsm</th>
                        <th width="50">Fin. Dia</th>
                        <th width="70">Gmts.Color</th>
                        <th width="70">Fab. Color</th>
                        <th width="60">Dia/Width Type</th>
                        <th width="120">Process</th>
                        <th width="60">Batch wgt/Wo Qty</th>
                        <th width="60">Roll No</th>
                        <th width="60">Issue Return Qty</th>
                        
                        <th width="90">Booking No</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="100">Order No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="">Remarks</th>
                    </thead>
                 </table>
                 <div style="width:1500px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1480" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="25" id="sl_1"></td>
                                <td style="word-break:break-all;" width="60" id="txtBatchNo_1" name="txtBatchNo[]"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="120" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td style="word-break:break-all;" width="70" id="fabColor_1"></td>
                                <td style="word-break:break-all;" width="60" id="diaType_1"></td>
                                <td width="120" align="right" id="">
                                	<? 
										echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" ); 
									?>
                              	</td>
                                <td style="word-break:break-all;" width="60" id="batchWeight_1"></td>
                                <td style="word-break:break-all;" width="60" id="txtRollNo_1"></td>
                             	<td width="60" align="center" id="txtReturnQty_1"><input type="text" id="txtReturnQty_1" name="txtReturnQty[]" style=" width:40px" class="text_boxes_numeric" placeholder="" /></td>                                
                                <td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="internalRef_1" align="left"></td>
                                <td width="" align="center" id="txtRemarks_1"><input type="text" id="txtRemarks_1" name="txtRemarks[]" style=" width:40px" class="text_boxes" placeholder="" />
                                	<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_1" value=""/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="bodypartId[]" id="bodypartId_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="determinationId[]" id="determinationId_1"/>
                                    <input type="hidden" name="woRate[]" id="woRate_1"/>
                                    <input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/>
                                    <input type="hidden" name="finDia[]" id="finDia_1"/>
                                    <input type="hidden" name="finGsm[]" id="finGsm_1"/>
                                    <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_1"/>
                                    <!-- <input type="hidden" name="privCurrentQty[]" id="privCurrentQty_1"/> -->
                                    <input type="hidden" name="batchWgt[]" id="batchWgt_1"/>

                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/>
                                    <input type="hidden" name="txtReturnQtyHidden[]" id="txtReturnQtyHidden_1"/>
                                </td>
                            </tr>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1200" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_roll_receive_from_subcon",0,1,"fnc_reset_form()",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
