<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	5-5-2018
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
	


		
	function openmypage_receive(sysType)
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_service_receive_return_controller.php?cbo_company_id='+cbo_company_id+'&popupType='+sysType+'&action=receive_and_return_popup','Issue Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var receive_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var order_type=this.contentDoc.getElementById("hidden_order_type").value;
			var popup_type=this.contentDoc.getElementById("hidden_popup_type").value;
			
			if(receive_id!="")
			{
				fnc_reset_form();
				get_php_form_data(receive_id+"_"+popup_type, "populate_data_from_data", "requires/fabric_service_receive_return_controller");
				show_list_view(receive_id+"_"+cbo_company_id+"_"+order_type+"_"+""+"_"+popup_type, 'grey_item_details_update', 'scanning_tbody','requires/fabric_service_receive_return_controller', '' );
				
				if(popup_type=='returnSys')
				{	
					set_button_status(1, permission, 'fnc_grey_roll_receive_from_subcon',1);
				}
			}
		}
	}
	
	
	
	
	

	function openmypage_booking()
	{
		var company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		var supplier_id = $('#cbo_service_company').val();
		if (form_validation('cbo_company_id*cbo_service_company','Company*Service Company')==false)
		{
			return;
		}
		
		//bookingNo_1
		var dataString="";
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					
					var booking_no  = $(this).children('td').eq(15).html();
					dataString+= booking_no+"_";
				});

			//alert(dataString);//return;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_service_receive_return_controller.php?company_id='+company_id+'&supplier_id='+supplier_id+'&cbo_service_source='+cbo_service_source+'&prebookingNos='+dataString+'&action=service_booking_popup', 'Service Booking Search', 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			
			if (theemail.value!="")
			{
				var booking_data=(theemail.value).split("_");
				$("#txt_woorder_no").val(booking_data[1]);
				$("#txt_woorder_id").val(booking_data[0]);
				if( jQuery.inArray( booking_data[1], scanned_booking_arr )>-1) 
				{ 
					alert('Sorry! Booking Already Generated.'); 
					$('#txt_woorder_no').val('');
					$('#txt_woorder_id').val('');
					return; 
				}
				
				if($("#cboProcess_1").val()==0)
				{
					var total_row=0;	
				}
				else
				{
					var total_row=$("#scanning_tbl tbody tr").length;
				}
				$('#cbo_service_company').attr('disabled',"disabled");
				
				var ddd=return_global_ajax_value(booking_data[1]+'**'+booking_data[2]+'**'+booking_data[0]+'**'+total_row+'**'+booking_data[3], "fabric_detls_list_view", "requires/", "fabric_service_receive_return_controller");
				
				if($("#cboProcess_1").val()==0)
				{
					$("#scanning_tbody").text('');	
				}
				
				if( ddd!="") $("#scanning_tbody").prepend(ddd);
				scanned_booking_arr.push(booking_data[1]);
				
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/fabric_service_receive_return_controller.php?data=" + data+'&action='+action, true );
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
		
	 	if(form_validation('cbo_company_id*cbo_service_source*cbo_service_company*txt_receive_date','Company*Service Source*Service Company*Receive Date')==false)
		{
			return; 
		}
		
		var j=0; var dataString=''; var error=1;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
				
				var prifix=$(this).attr("id").split("_");
				var id=prifix[1];
				var txtReceiveQty=$(this).find('input[name="txtReceiveQty[]"]').val()*1;
				if(txtReceiveQty>0)
				{
					var txtBatchNo=$(this).find('input[name="txtBatchNo[]"]').val();
					var txtInhouseBatchNo=$(this).find('input[name="txtinHouseBatchNo[]"]').val();
					var cboProcess=$(this).find('select[name="cboProcess[]"]').val();
					var colorId=$(this).find('input[name="colorId[]"]').val();
					var bodypartId=$(this).find('input[name="bodypartId[]"]').val();
					var determinationId=$(this).find('input[name="determinationId[]"]').val();
					var buyerId=$(this).find('input[name="buyerId[]"]').val();
					var orderId=$(this).find('input[name="orderId[]"]').val();
					var txtRate=$(this).find('input[name="txtRate[]"]').val();
					var txtAmount=$(this).find('input[name="txtAmount[]"]').val();
					var greyUsed=$(this).find('input[name="txtgreyUsed[]"]').val();
					var currencyId=$(this).find('input[name="currencyId[]"]').val();
					var exchangeRate=$(this).find('input[name="exchangeRate[]"]').val();
					var bookingId=$(this).find('input[name="progBookPiId[]"]').val();
					var bookingNo=$(this).find('input[name="txtBookingNo[]"]').val();
					var finDia=$(this).find('input[name="finDia[]"]').val();
					var finGsm=$(this).find('input[name="finGsm[]"]').val();
					var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
					var finDia=$(this).find('input[name="finDia[]"]').val();
					var finGsm=$(this).find('input[name="finGsm[]"]').val();
					
					var woorder_qty=$(this).find("td:eq(9)").text();
					var jobNo=$(this).find('input[name="hiddnJobNo[]"]').val();
					var dia=$(this).find("td:eq(5)").text();
					var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
					var bookingDtlsId=$(this).find('input[name="bookingDtlsId[]"]').val();
					var txtReturnQty=$(this).find('input[name="txtReturnQty[]"]').val();

					j++;
					dataString+='&cboProcess_' + j + '=' + cboProcess + '&determinationId_' + j + '=' + determinationId + '&buyerId_' + j + '=' + buyerId + '&txtRate_' + j + '=' + txtRate + '&orderId_' + j + '=' + orderId + '&txtBatchNo_' + j + '=' + txtBatchNo+ '&txtinHouseBatchNo_' + j + '=' + txtInhouseBatchNo + '&jobNo_' + j + '=' + jobNo + '&colorId_' + j + '=' + colorId + '&dtlsId_' + j + '=' + dtlsId + '&txtAmount_' + j + '=' + txtAmount + '&dia_' + j + '=' + dia + '&bodypartId_' + j + '=' + bodypartId + '&txtReceiveQty_' + j + '=' + txtReceiveQty+ '&tr_' + j + '=' + id+ '&currencyId_' + j + '=' + currencyId+ '&exchangeRate_' + j + '=' + exchangeRate+ '&bookingId_' + j + '=' + bookingId+ '&bookingNo_' + j + '=' + bookingNo+ '&workorderNo_' + j + '=' + woorder_qty+ '&finDia_' + j + '=' + finDia+ '&finGsm_' + j + '=' + finGsm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder+ '&greyUsed_' + j + '=' + greyUsed+ '&bookingDtlsId_' + j + '=' + bookingDtlsId+ '&txtReturnQty_' + j + '=' + txtReturnQty;
				}
		});
		
		//alert(dataString);
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_receive_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_receive_date*txt_receive_challan*txt_receive_no_id*update_id',"../../")+dataString;
		//alert(data)
		freeze_window(operation);
		http.open("POST","requires/fabric_service_receive_return_controller.php",true);
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
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_return_sys_no').value = response[2];
				add_dtls_data(response[3]);
				show_list_view($('#update_id').val()+"_"+$('#cbo_company_id').val()+"_"+""+"_"+response[0], 'grey_item_details_update', 'scanning_tbody','requires/fabric_service_receive_return_controller', '' );
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
	
	
	
	$('#txt_woorder_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var booking_no=$('#txt_woorder_no').val();
			check_woorder(booking_no);
		}
	});
	
	
	function check_woorder(data)
	{
		if(data=="")
		{
			$('#txt_woorder_no').val('');
			return;	
		}
		var cbo_company_id=$('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var booking_data=return_global_ajax_value( data+"**"+cbo_company_id, 'check_booking_no', '', 'requires/fabric_service_receive_return_controller');
		
		if(booking_data==0)
		{
			alert("Booking No Found");
			$('#txt_woorder_no').val('');
			$('#txt_woorder_id').val('');
			return;
		}
		else
		{
			booking_data=booking_data.split("**");
			booking_id=booking_data[0];
			if( jQuery.inArray( booking_id, scanned_booking_arr )>-1) 
			{ 
				alert('Sorry! Booking Already Scanned.'); 
				$('#txt_woorder_no').val('');
				$('#txt_woorder_id').val('');
				return; 
			}
			
			$("#txt_woorder_no").val(booking_data[1]);
			$("#txt_woorder_id").val(booking_data[0]);
			
			
			if($("#cboProcess_1").val()==0)
			{
				var total_row=0;	
			}
			else
			{
				var total_row=$("#scanning_tbl tbody tr").length;
			}
			
			var ddd=return_global_ajax_value(booking_data[1]+'**'+booking_data[2]+'**'+booking_data[0]+'**'+total_row, "fabric_detls_list_view", "requires/", "fabric_service_receive_return_controller");
			
			if($("#cboProcess_1").val()==0)
			{
				$("#scanning_tbody").text('');	
			}
			
			if( ddd!="") $("#scanning_tbody").prepend(ddd);
			scanned_booking_arr.push(booking_id);
		}
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="25" id="sl_1"></td><td width="70" id="batchNo_1"><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td width="70" id="inHouseBatchNo_1" style="display:none"><input type="text" id="txtinHouseBatchNo_1" name="txtinHouseBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="120" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="120" align="right" id=""><? echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" );?></td><td style="word-break:break-all;" width="60" id="woQty_1"></td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_1" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_1" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric"/></td><td width="60" align="center" id=""><input type="text" id="txtRate_1" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_1" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="order_1" align="left"></td><td style="word-break:break-all;" width="" id="currency_1" align="left"><? echo create_drop_down( "currencyId_1", 65, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" ); ?><input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_<? echo $i; ?>" value=""/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="bodypartId[]" id="bodypartId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="currencyId[]" id="currencyId_1"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_1"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/><input type="hidden" name="finDia[]" id="finDia_1"/><input type="hidden" name="finGsm[]" id="finGsm_1"/></td></tr>';
		
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_service_source').val(0);
		$('#cbo_service_company').val(0);
		$('#update_id').val('');
		$('#txt_receive_no').val('');
		$('#txt_receive_challan').val('');
		$('#txt_receive_date').val('');
		$('#txt_woorder_no').val('');
		$('#txt_woorder_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}
	
	function calculate_amount(id)
	{	

		var issue_qnty = $("#txtReceiveQty_"+id).attr("placeholder")*1;
		var tot_rcv = $("#totalreceiveQty_"+id).val()*1;
		var curr_rcv = $("#txtReceiveQty_"+id).val()*1;
		var hidden_curr_rcv = $("#txtReceiveQtyHidden_"+id).val()*1;

		if(hidden_curr_rcv != 0)
		{
			var totRcv = (tot_rcv + curr_rcv ) - hidden_curr_rcv ;
		}else
		{
			var totRcv =tot_rcv + curr_rcv;
		}
		
		if(totRcv > issue_qnty)
		{
			$("#txtReceiveQty_"+id).val($("#txtReceiveQtyHidden_"+id).val()*1);
		}
		var amount=($("#txtReceiveQty_"+id).val()*1)*($("#txtRate_"+id).val()*1);
		//$("#txtAmount_"+id).val(amount);
	}
	
	
	
	
	
	function calculate_total_retn(id)
	{

		var tot_rcv_qty = $("#totalreceiveQty_"+id).val()*1;
		var return_qty = $("#txtReturnQty_"+id).val()*1;
		var txtRate = $("#txtRate_"+id).val()*1;
		if(tot_rcv_qty>=return_qty)
		{				
			$("#txtAmount_"+id).val(return_qty*txtRate)*1; 
		}
		else
		{
			alert('Don not allow Return Quantity more than Receive Quantity');
			 $("#txtReturnQty_"+id).val('');
		}
		
	}
	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="3" align="right"><b>Return System Number&nbsp;</b>
                        </td>
                         <td colspan="3" align="left">
                        	<input type="text" name="txt_return_sys_no" id="txt_return_sys_no" class="text_boxes" style="width:130px;" onDblClick="openmypage_receive('returnSys')" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );

                                //load_drop_down( 'requires/fabric_service_receive_return_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 142, $knitting_source, "", 1, "-- Select --", 0, "load_drop_down( 'requires/fabric_service_receive_return_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
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
                    	<td align="right" class="must_entry_caption" width="100">Receive Date</td>
                        <td><input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right"  width="">Receive Challan No</td>
                        <td><input type="text" name="txt_receive_challan" id="txt_receive_challan" class="text_boxes" style="width:130px;" />
                        </td>
                         <td align="left"><b>Service Recv Number</b>
                        <td>
                        	<input type="text" name="txt_receive_no" id="txt_receive_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_receive('recChallan')" placeholder="Double Click To Search" readonly/>
                            
                       <input type="hidden" name="txt_receive_no_id" id="txt_receive_no_id"/>

                        </td>
                       
                        <!-- <td align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Browse/Write/scan" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                            
                        </td> -->
                    </tr>
                    <!-- <tr>
                    						<td>&nbsp;</td>
                    						<td align="right" colspan="2" autofocus="autofocus">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:130px;" placeholder="Browse/Write/scan" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                            
                        </td>
                    						<td>&nbsp;</td>
                    </tr> -->
                    <tr>
                     <!--<td align="right">Work Order No</td>-->
                        <td>
                        	<input type="hidden" name="txt_woorder_no" id="txt_woorder_no" class="text_boxes" style="width:130px;" placeholder="Browse/scan" onDblClick="openmypage_booking();"  />
                            <input type="hidden" id="txt_woorder_id" />
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1430px;text-align:left">
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
				<table cellpadding="0" width="1410" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="25">SL</th>
                        <th width="70">Subcon Batch</th>
                        <th width="100" style="display:none">Inhouse Batch</th>
                        <th width="80">Body Part</th>
                        <th width="120">Construction/ Composition</th>
                        <th width="50">Fin. Dia</th>
                        <th width="50">Fin. gsm</th>
                        <th width="70">Color</th>
                        <th width="80">Process</th>
                        <th width="60">WO. Qnty</th>
                        <th width="60">Total Rev. Qty.</th>
                        <th width="60">Current Rev. Qty</th>
                        <th width="60">Return Qty</th>
                        <th width="60">Rate</th>
                        <th width="70">Amount</th>
                        <th width="70">Grey Used</th>
                        <th width="90">Booking No</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="">Currency</th>
                    </thead>
                 </table>
                 <div style="width:1430px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1410" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="25" id="sl_1"></td>
                                <td width="70" id="batchNo_1"  ><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td>
                                <td width="100" id="inHouseBatchNo_1" style="display:none"><input type="text" id="txtinHouseBatchNo_1" name="txtinHouseBatchNo[]"  style=" width:90px" class="text_boxes"/></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="120" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="50" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td width="120" align="right" id="">
                                	<? 
										echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" ); 
									?>
                              	</td>
                                <td style="word-break:break-all;" width="60" id="batchWeight_"></td>
                             	<td width="60" align="center" id=""><input type="text" id="totalreceiveQty_1" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td>
                             	<td width="60" align="center" id=""><input type="text" id="txtReceiveQty_1" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" placeholder="" /> <input type="hidden" id="txtReceiveQtyHidden_1" name="txtReceiveQtyHidden[]"/></td> 
                                <td width="60" align="center" id=""><input type="text" id="txtRate_1" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td>
                                <td width="70" align="center" id=""><input type="text" id="txtAmount_1" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"/></td>
                                <td width="70" align="center" id=""><input type="text" id="txtgreyUsed_1" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric"/></td>
                                <td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="" id="currency_1" align="left">
									<? 
											echo create_drop_down( "currencyId_1", 65, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" );
									?>
                                	<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_1" value=""/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="bodypartId[]" id="bodypartId_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="determinationId[]" id="determinationId_1"/>
                                    <input type="hidden" name="exchangeRate[]" id="exchangeRate_1"/>
                                    <input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/>
                                    <input type="hidden" name="finDia[]" id="finDia_1"/>
                                    <input type="hidden" name="finGsm[]" id="finGsm_1"/>
                                    <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_1"/>
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
