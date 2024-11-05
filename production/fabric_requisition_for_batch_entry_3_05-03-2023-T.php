<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Delivery Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Logic Software limited
Creation date 	: 	28-08-2022
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
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Requisition For Batch","../", 1, 1, $unicode,0,0); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openmypage_requisition()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_batch_entry_controller_3.php?action=requisition_popup&company_id='+cbo_company_id,'Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value;	 //Requisition Id and Number
			
			if(reqn_id!="")
			{
				freeze_window(5);
				reset_form('requisitionEntry_1','','','','','');
				get_php_form_data(reqn_id, "populate_data_from_requisition", "requires/fabric_requisition_for_batch_entry_controller_3" );
				var list_view = trim(return_global_ajax_value(reqn_id, 'populate_list_view', '', 'requires/fabric_requisition_for_batch_entry_controller_3'));
				$("#scanning_tbl tbody").html(list_view);
				fnc_count_total_qty();
				set_all_onclick();
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch',1);
				//$("#txt_order_no").attr("disabled");
				//$("#txt_order_no").removeAttr("ondblclick");
				release_freezing();
			}
		}
	}
	
	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Fabric Selection Form';	
			var page_link ='requires/fabric_requisition_for_batch_entry_controller_3.php?company_id='+cbo_company_id+'&action=po_popup';
			var popup_width="1090px";

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_data=this.contentDoc.getElementById("hidden_data").value;
				$("#hidden_data").val(hidden_data);
				var data="action=populate_details_data&type=1"+ get_submitted_data_string('cbo_company_id*cbo_location_name*hidden_data*update_id',"../");

				freeze_window(5);
				http.open("POST","requires/fabric_requisition_for_batch_entry_controller_3.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange =fnc_populate_details_data_Reply_info;
			}
		}
	}

	function fnc_populate_details_data_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			$("#scanning_tbl tbody").html(response);

			fnc_count_total_qty();
			release_freezing();
		}
	}

	function fnc_fabric_requisition_for_batch( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch','requires/fabric_requisition_for_batch_entry_controller_3');
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_requisition_date','Company*Requisition Date')==false)
		{
			return; 
		}
		
		var row_num=$('#scanning_tbl tbody tr').length;
		//alert (row_num); return;
		for (var i=1; i<=row_num; i++)
		{
			var reqsnQty=$('#reqsnQty'+i).val()*1;
			if(reqsnQty == ""){
				alert('Current Reqn. Qty Not Found');
				return;
			}
		}

		var dataString=""; var j=0; var validation_error =0;
		$("#scanning_tbl").find('tbody tr').each(function () 
		{
            var bookingNo = "";//$(this).find("td:eq(7)").text();

            var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
            var programBookingId = $(this).find('input[name="programBookingId[]"]').val();
            var jobNo = $(this).find('input[name="jobNo[]"]').val();
            var reqsnQty = $(this).find('input[name="reqsnQty[]"]').val()*1;
            var remarks = $(this).find('input[name="remarks[]"]').val();
            var buyerId = $(this).find('input[name="buyerId[]"]').val();

            var poId = $(this).find('input[name="poId[]"]').val();
            var fileNo = $(this).find('input[name="fileNo[]"]').val();
            var grouping = $(this).find('input[name="grouping[]"]').val();
            var prodId = $(this).find('input[name="prodId[]"]').val();
            var deterId = $(this).find('input[name="deterId[]"]').val();
            var gsm = $(this).find('input[name="gsm[]"]').val();
            var width = $(this).find('input[name="width[]"]').val();
            var colorId = $(this).find('input[name="colorId[]"]').val();
            var batchColorId = $(this).find('select[name="batchColorId[]"]').val();
			var bookintQty = $(this).find('input[name="bookintQty[]"]').val()*1;
			var reqnBalQty = $(this).find('input[name="reqnBalQty[]"]').val()*1;
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
            var isSales = $(this).find('input[name="isSales[]"]').val();
            var booking_without_order = $(this).find('input[name="booking_without_order[]"]').val();
            var yCountId = $(this).find('input[name="yCountId[]"]').val();
            var brandId = $(this).find('input[name="brandId[]"]').val();
            var yLotId = $(this).find('input[name="yLotId[]"]').val();

			if(reqsnQty > 0)
			{
				if(reqsnQty > reqnBalQty)
				{
					validation_error +=1;
				}
			}
            
            try 
			{
                j++;
                dataString += '&job' + j + '=' + jobNo + '&programBookingId' + j + '=' + programBookingId + '&fileNo' + j + '=' + fileNo + '&grouping' + j + '=' + grouping + '&deterId' + j + '=' + deterId+ '&gsm' + j + '=' + gsm + '&dia' + j + '=' + width+ '&prodId' + j + '=' + prodId + '&poId' + j + '=' + poId + '&buyerId' + j + '=' + buyerId + '&colorId' + j + '=' + colorId + '&batchColorId' + j + '=' + batchColorId + '&reqsnQty' + j + '=' + reqsnQty + '&bodyPartId' + j + '=' + bodyPartId + '&bookintQty' + j + '=' + bookintQty + '&remarks' + j + '=' + remarks + '&dtlsId' + j + '=' + dtlsId + '&isSales' + j + '=' + isSales + '&booking_without_order' + j + '=' + booking_without_order + '&yCountId' + j + '=' + yCountId + '&brandId' + j + '=' + brandId + '&yLotId' + j + '=' + yLotId;
            }
            catch (e) {
                //got error no operation
            }
        });

		if(j<1)
		{
			alert('No data');
			return;
		}

		if(validation_error >0)
		{
			alert('Reqn. Qty. can not greater than balance Qty.');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_requisition_date*txt_requisition_no*txt_deleted_id*update_id',"../")+dataString;
		//alert(operation);+dataString
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/fabric_requisition_for_batch_entry_controller_3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_fabric_requisition_for_batch_Reply_info;
	}

	function fnc_fabric_requisition_for_batch_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_requisition_no').value = response[2];
				var list_view = trim(return_global_ajax_value(response[1], 'populate_list_view', '', 'requires/fabric_requisition_for_batch_entry_controller_3'));
				$("#scanning_tbl tbody").html(list_view);
				fnc_count_total_qty();
				$("#txt_order_no").attr("disabled");
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch',1);
			}
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/fabric_requisition_for_batch_entry_controller_3.php?data=" + data+'&action='+action, true );
	}

	function fn_deleteRow(rid) 
    {	//alert(1);
        var num_row = $('#scanning_tbl tbody tr').length;
        var dtlsId = $("#dtlsId" + rid).val();
        var update_id = $('#update_id').val();
        /* if(dtlsId!="")
        {
        	alert('Updated data remove restricted.');return;
        } */

		var txt_deleted_id=$('#txt_deleted_id').val();
		var selected_id=''; 
		if(dtlsId!='')
		{
			if(txt_deleted_id=='') selected_id=dtlsId; else selected_id=txt_deleted_id+','+dtlsId;
			$('#txt_deleted_id').val( selected_id );
		}
		//alert('num_row'+num_row);
        if (num_row == 1)
        {
        	alert('At least one data field must be selected');return;
        }
        else
        {
            $("#tr_" + rid).remove();
        }

		var total_total_grey_stock_without_issue=0;
        var total_total_grey_stock = 0;
        var total_total_prev_reqn = 0;
        var total_total_balance_reqn = 0;
		var total_issueQty=0;
        var total_total_current_reqn = 0;

        $("#scanning_tbl").find('tbody tr').each(function () {
			total_total_grey_stock_without_issue += $(this).find('input[name="stockWithoutIssueQty[]"]').val() * 1;
			total_total_grey_stock += $(this).find('input[name="bookintQty[]"]').val() * 1;
            total_total_balance_reqn += $(this).find('input[name="reqnBalQty[]"]').val() * 1;
            total_total_prev_reqn += $(this).find('input[name="previous_reqsnQty[]"]').val() * 1;

            total_total_current_reqn += $(this).find('input[name="reqsnQty[]"]').val() * 1;
			total_issueQty += $(this).find('input[name="issueQty[]"]').val() * 1;
        });
        //alert(3);
		$("#total_total_grey_stock_without_issue").html(number_format(total_total_grey_stock_without_issue, 2));
        $("#total_total_grey_stock").html(number_format(total_total_grey_stock, 2));
        $("#total_total_balance_reqn").html(number_format(total_total_balance_reqn, 2));
		$("#total_total_prev_reqn").text(number_format(total_total_prev_reqn, 2));

        $("#total_total_current_reqn").text(number_format(total_total_current_reqn, 2));
		//alert(4);
        
    }

	function fnc_count_total_qty()
	{
		var total_total_grey_stock = 0;
		var total_total_prev_reqn = 0;
		var total_total_balance_reqn = 0;
		var total_current_reqn = 0;
		var total_stockWithoutIssueQty = 0;
		var total_issueQty = 0;
        $("#scanning_tbl").find('tbody tr').each(function () {
            total_total_grey_stock += $(this).find('input[name="bookintQty[]"]').val() * 1;
            total_total_prev_reqn += $(this).find('input[name="previous_reqsnQty[]"]').val() * 1;
            total_total_balance_reqn += $(this).find('input[name="reqnBalQty[]"]').val() * 1;
            total_current_reqn += $(this).find('input[name="reqsnQty[]"]').val() * 1;
            total_stockWithoutIssueQty += $(this).find('input[name="stockWithoutIssueQty[]"]').val() * 1;
            total_issueQty += $(this).find('input[name="issueQty[]"]').val() * 1;
        });
        $("#total_total_grey_stock").text(number_format(total_total_grey_stock, 2));
        $("#total_total_prev_reqn").text(number_format(total_total_prev_reqn, 2));
        $("#total_total_balance_reqn").text(number_format(total_total_balance_reqn, 2));
        $("#total_total_current_reqn").text(number_format(total_current_reqn, 2));
        $("#total_total_grey_stock_without_issue").text(number_format(total_stockWithoutIssueQty, 2));
        $("#total_total_issue").text(number_format(total_issueQty, 2));
	}

	function fnc_check_balance_qty(rowsl)
	{
		
		var update_id = $("#update_id").val();
		var balance_qty = $("#reqnBalQty"+rowsl).val()*1;	
		var reqsn_qty = $("#reqsnQty"+rowsl).val()*1;

		/* if(update_id!="")
		{
			var previous_reqsnQty = $("#previous_reqsnQty"+rowsl).val()*1;	
			balance_qty = (previous_reqsnQty+balance_qty);
		} */

		if(reqsn_qty>balance_qty)
		{
			alert("Requisition quantity can not greater than balance quantity =" + reqsn_qty+">"+balance_qty);
			$("#reqsnQty"+rowsl).val('');
			$("#reqsnQty"+rowsl).focus();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="requisitionEntry_1" id="requisitionEntry_1"> 
		<div align="center" style="width:100%;">
            <fieldset style="width:690px;">
				<legend>Fabric Requisition</legend>
                <table cellpadding="0" cellspacing="2" width="650">
                    <tr>
                        <td align="right" colspan="3"><b>Requisition No</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_requisition()" placeholder="Browse For Requisition No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td width="80" class="must_entry_caption" align="right">Company</td>
                        <td width="140">
                            <? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/fabric_requisition_for_batch_entry_controller_3', this.value, 'load_drop_down_location', 'location_td' );",0 );
                            ?>
                        </td>
                        <td width="80" align="right">Location</td>                                              
                        <td width="140" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
                        <td width="130" align="right">Requisition Date</td>
                        <td width="80"><input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:80px;" readonly /></td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3"><b>Select Fabric</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px;" placeholder="Browse For fabric" onDblClick="openmypage_po()" readonly/>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:2045px;text-align:left">
				<table cellpadding="0" width="2025" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="70">Ref. No</th>
                        <th width="60">File No</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job No</th>
						<th width="80">Order No</th>
						<th width="70">Prog. no</th>
						<th width="80">Body part</th>
                        <th width="175">Fabric Description</th>
                        <th width="150">Yarn Count & Brand</th>
                        <th width="150">Yarn Lot</th>
                        <th width="40">GSM</th>
                        <th width="40">F. Dia</th>
                        <th width="90">Color/ Code</th>
                        <th width="90">Batch Color</th>
						<th width="100">TTL Rcv + Transfer IN - Transfer Out</th>
                        
                        <th width="70">Grey Stock Qty (Kg) </th>
                        <th width="70">Previous Req. Qty. (Kg)</th>
                        <th width="80">Req. Balance (Kg)</th>
                        <th width="90">Reqn. Qty. (Kg)</th>
                        <th width="90">Issue</th>
                        <th width="90">Remarks</th>
                        <th width="60">Prod. Id</th>
                        <th width="50">&nbsp;</th>
                    </thead>
                 </table>
                 <div style="width:2045px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="2025" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_tbody">
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="30">1</td>
								<td width="70">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="70">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="175">&nbsp;</td>
								<td width="150">&nbsp;</td>
								<td width="150">&nbsp;</td>
								<td width="40">&nbsp;</td>
								<td width="40">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="100">&nbsp;</td>
								
								<td width="70">&nbsp;</td>
								<td width="70">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="50">&nbsp;</td>
							</tr>
                        </tbody>
                	</table>
                </div>
				<table cellpadding="0" width="2025" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <tfoot>
                    	<th width="30">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="175">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="40">&nbsp;</th>
                        <th width="40">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="90">Total</th>
                        <th width="100" id="total_total_grey_stock_without_issue">&nbsp;</th>
                        
                        <th width="70" id="total_total_grey_stock">&nbsp;</th>
                        <th width="70" id="total_total_prev_reqn">&nbsp;</th>
                        <th width="80" id="total_total_balance_reqn">&nbsp;</th>
                        <th width="90" id="total_total_current_reqn">&nbsp;</th>
                        <th width="90" id="total_total_issue">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                    </tfoot>
                 </table>
                <br>
                <table width="1825" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_fabric_requisition_for_batch",0,1,"reset_form('requisitionEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();')",1);
                            ?>
							<input type="hidden" id="hidden_data" name="hidden_data" />
							<input type="hidden" id="txt_deleted_id" name="txt_deleted_id" />
                        </td>
                    </tr>  
                </table>
			</fieldset>
    	</div>
	</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
