<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Delivery Roll Wise

Functionality	:
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	27-11-2015
Updated by 		: 	Jahid Hasan
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

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_batch_entry_controller_2.php?action=requisition_popup&company_id='+cbo_company_id,'Requisition Popup', 'width=1080px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value;	 //Requisition Id and Number

			if(reqn_id!="")
			{
				freeze_window(5);
				reset_form('requisitionEntry_1','','','','','');
				get_php_form_data(reqn_id, "populate_data_from_requisition", "requires/fabric_requisition_for_batch_entry_controller_2" );
				var list_view = trim(return_global_ajax_value(reqn_id, 'populate_list_view', '', 'requires/fabric_requisition_for_batch_entry_controller_2'));
				$("#scanning_tbl tbody").html(list_view);
				//set_all_onclick();
				$('#cbo_company_id').attr('disabled',true);
				fnc_count_total_other_qty();
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch_2',1);
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
			var page_link ='requires/fabric_requisition_for_batch_entry_controller_2.php?company_id='+cbo_company_id+'&action=po_popup';
			var popup_width="1200px";

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_data=this.contentDoc.getElementById("hidden_data").value;
				var data=hidden_data.split("####");
				//alert(data);
				var html=''; var html2=''; var num_row=$('#scanning_tbl tbody tr').length+1;
				// alert(num_row);
				var total_prog_booking_qty=0; var total_req_qty=0; var total_balance_qty=0;
				for(var k=0; k<data.length; k++)
				{
					if(num_row%2==0) var bgcolor="#E9F3FF"; else var bgcolor="#FFFFFF";

					var row_data=data[k].split("**");
					// alert(row_data[30]+'='+row_data[31]);
					var program_no='';
					var booking_no=row_data[9];

					total_prog_booking_qty+=row_data[6]*1;
					total_req_qty+=row_data[10]*1;
					total_balance_qty+=row_data[11]*1;

					// html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="30">'+num_row+'</td><td width="60" style="word-break:break-all;text-align:center;">'+row_data[12]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="cbuyerId'+num_row+'">'+row_data[28]+'</td><td width="120" style="word-break:break-all;text-align:center;" id="job'+num_row+'">'+row_data[22]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="styleRef'+num_row+'">'+row_data[29]+'</td><td width="80" style="word-break:break-all;" id="fileNo'+num_row+'">'+row_data[24]+'</td><td width="80" style="word-break:break-all;" id="grouping'+num_row+'">'+row_data[13]+'</td><td width="100" style="word-break:break-all;text-align:center;">'+row_data[2]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="bookingNo'+num_row+'">'+row_data[14]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="bookingType'+num_row+'">'+row_data[31]+'</td><td width="150" style="word-break:break-all;text-align:center;" id="bodyPart'+num_row+'">'+row_data[15]+'</td><td width="100" style="word-break:break-all;text-align:center;">'+row_data[16]+'</td><td width="100" style="word-break:break-all;" id="constraction'+num_row+'">'+row_data[17]+'</td><td width="100" style="word-break:break-all;" id="composition'+num_row+'">'+row_data[18]+'</td><td width="50" style="word-break:break-all;text-align:center;" align="right" id="gsm'+num_row+'">'+row_data[19]+'</td><td width="50" style="word-break:break-all;text-align:center;" align="right" id="dia'+num_row+'">'+row_data[20]+'</td><td width="100" style="word-break:break-all;">'+row_data[21]+'</td><td width="80" style="word-break:break-all;" align="right" id="bookQty'+num_row+'">'+row_data[6]+'</td><td width="80" style="word-break:break-all;" align="right" id="totReqQty'+num_row+'">'+row_data[10]+'</td><td width="80" style="word-break:break-all;" align="right" id="totBalQty'+num_row+'">'+row_data[11]+'</td><td width="80"><input type="text" value="" class="text_boxes_numeric" style="width:65px" id="reqsnQty'+num_row+'" name="reqsnQty[]" onkeyup="fnc_count_total_qty();fnc_check_balance_qty('+num_row+');" /></td><td><input type="text" value="" class="text_boxes" style="width:90%" id="remarks'+num_row+'" name="remarks[]"/><input type="hidden" value="'+row_data[0]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[1]+'" id="poId'+num_row+'" name="poId[]"/><input type="hidden" value="'+row_data[9]+'" id="colorTypeId'+num_row+'" name="colorTypeId[]"/><input type="hidden" value="'+row_data[5]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[4]+'" id="bodyPartId'+num_row+'" name="bodyPartId[]"/><input type="hidden" value="'+row_data[6]+'" id="bookintQty'+num_row+'" name="bookintQty[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/><input type="hidden" value="'+row_data[26]+'" id="isSales'+num_row+'" name="isSales[]"/><input type="hidden" value="'+row_data[27]+'" id="cbuyerId'+num_row+'" name="cbuyerId[]"/></td><td><input type="button" value="-" class="formbuttonplasminus" style="" id="decrease'+num_row+'" name="decrease[]"  onClick="fn_deleteRow('+num_row+');"/></td></tr>';
					// num_row++;

					html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="30">'+num_row+'</td><td width="60" style="word-break:break-all;text-align:center;">'+row_data[12]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="cbuyerId'+num_row+'">'+row_data[28]+'</td><td width="120" style="word-break:break-all;text-align:center;" id="job'+num_row+'">'+row_data[22]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="styleRef'+num_row+'">'+row_data[29]+'</td><td width="80" style="word-break:break-all;" id="fileNo'+num_row+'">'+row_data[24]+'</td><td width="80" style="word-break:break-all;" id="grouping'+num_row+'">'+row_data[13]+'</td><td width="100" style="word-break:break-all;text-align:center;">'+row_data[2]+'</td><td width="100" style="word-break:break-all;text-align:center;" id="bookingNo'+num_row+'">'+row_data[14]+'</td><td width="150" style="word-break:break-all;text-align:center;" id="bodyPart'+num_row+'">'+row_data[15]+'</td><td width="100" style="word-break:break-all;text-align:center;">'+row_data[16]+'</td><td width="100" style="word-break:break-all;" id="constraction'+num_row+'">'+row_data[17]+'</td><td width="100" style="word-break:break-all;" id="composition'+num_row+'">'+row_data[18]+'</td><td width="50" style="word-break:break-all;text-align:center;" align="right" id="gsm'+num_row+'">'+row_data[19]+'</td><td width="50" style="word-break:break-all;text-align:center;" align="right" id="dia'+num_row+'">'+row_data[20]+'</td><td width="100" style="word-break:break-all;">'+row_data[21]+'</td><td width="80" style="word-break:break-all;" align="right" id="bookQty'+num_row+'">'+row_data[6]+'</td><td width="80" style="word-break:break-all;" align="right" id="totReqQty'+num_row+'">'+row_data[10]+'</td><td width="80" style="word-break:break-all;" align="right" id="totBalQty'+num_row+'">'+row_data[11]+'</td><td width="80"><input type="text" value="" class="text_boxes_numeric" style="width:65px" id="reqsnQty'+num_row+'" name="reqsnQty[]" onkeyup="fnc_count_total_qty();fnc_check_balance_qty('+num_row+');" /></td><td><input type="text" value="" class="text_boxes" style="width:90%" id="remarks'+num_row+'" name="remarks[]"/><input type="hidden" value="'+row_data[0]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[1]+'" id="poId'+num_row+'" name="poId[]"/><input type="hidden" value="'+row_data[9]+'" id="colorTypeId'+num_row+'" name="colorTypeId[]"/><input type="hidden" value="'+row_data[5]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[4]+'" id="bodyPartId'+num_row+'" name="bodyPartId[]"/><input type="hidden" value="'+row_data[6]+'" id="bookintQty'+num_row+'" name="bookintQty[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/><input type="hidden" value="'+row_data[26]+'" id="isSales'+num_row+'" name="isSales[]"/><input type="hidden" value="'+row_data[27]+'" id="cbuyerId'+num_row+'" name="cbuyerId[]"/></td><td><input type="button" value="-" class="formbuttonplasminus" style="30px" id="decrease'+num_row+'" name="decrease[]"  onClick="fn_deleteRow('+num_row+');"/></td></tr>';
					num_row++;

				}
				//var html2='<tr></td><td colspan="14" style="text-align:right; font-weight:bold;">'+'Total'+'</td></td><td style="text-align:right; font-weight:bold;">'+number_format(total_prog_booking_qty,2)+'</td></td><td style="text-align:right; font-weight:bold;">'+number_format(total_req_qty,2)+'</td></td><td style="text-align:right; font-weight:bold;">'+number_format(total_balance_qty,2)+'</td></td><td width="80" align="center"><input type="text" class="text_boxes_numeric" style="width:65px" id="total_blnc_qty_td_id" name="" readonly/></td></tr>';
				$("#scanning_tbl tbody:last").append(html);
				$("#scanning_tbl tbody:last").append(html2);
				fnc_count_total_other_qty();
			}
		}
	}

	function fn_deleteRow(rid) // folling Roll Wise Grey Fabric Delivery to Store
    {
    	// alert(rid);
        var num_row = $('#scanning_tbl tbody tr').length;
        var dtlsId = $("#dtlsId" + rid).val();
        var update_id = $('#update_id').val();
        if(dtlsId!="")
        {
        	alert('Updated data remove restricted.');return;
        }

        if (num_row == 1)
        {
        	alert('At least one data field must be selected');return;
        }
        else
        {
        	//alert('remove');
            $("#tr_" + rid).remove();
        }
        var total_book_qty = 0;
        var total_reqn_qty = 0;
        var total_balance = 0;
        var total_current_reqn = 0;
        $("#scanning_tbl").find('tbody tr').each(function () {
            total_book_qty += $(this).find('td:nth-child(16)').html() * 1;
            total_reqn_qty += $(this).find('td:nth-child(17)').html() * 1;
            total_balance += $(this).find('td:nth-child(18)').html() * 1;
            total_current_reqn += $(this).find('input[name="reqsnQty[]"]').val() * 1;
        });
        $("#total_book_qty_td_id").html(number_format(total_book_qty, 2));
        $("#total_req_qty_td_id").html(number_format(total_reqn_qty, 2));
        $("#balance_td_id").html(number_format(total_balance, 2));
        // alert(total_current_reqn);
        $("#total_blnc_qty_td_id").val(number_format(total_current_reqn, 2));
    }

	function fnc_count_total_qty()
	{
		var total_current_reqn = 0;
        $("#scanning_tbl").find('tbody tr').each(function () {

            total_current_reqn += $(this).find('input[name="reqsnQty[]"]').val() * 1;

        });
        // alert(total_current_reqn);
        $("#total_blnc_qty_td_id").val(number_format(total_current_reqn, 2));

		/*var tot_count_qty=0;
		var num_row_total=$('#scanning_tbl tbody tr').length;
		//alert(num_row_total);
		for(var kk=1; kk<=num_row_total; kk++)
		{
			tot_count_qty+=$('#reqsnQty'+kk).val()*1;
		}
		$('#total_blnc_qty_td_id').val(number_format(tot_count_qty,2));*/
	}


	function fnc_count_total_other_qty()
	{
		var tot_book_qty=0; var tot_req_qty=0; var tot_balance_qty=0;var tot_count_qty=0;
		var num_row_total=$('#scanning_tbl tbody tr').length;
		for(var kk=1; kk<=num_row_total; kk++)
		{
			tot_book_qty+=$('#bookQty'+kk).text()*1;
			tot_req_qty+=$('#totReqQty'+kk).text()*1;
			tot_balance_qty+=$('#totBalQty'+kk).text()*1;
			tot_count_qty+=$('#reqsnQty'+kk).val()*1;
		}
		$('#total_book_qty_td_id').html(number_format(tot_book_qty,2));
		$('#total_req_qty_td_id').html(number_format(tot_req_qty,2));
		$('#balance_td_id').html(number_format(tot_balance_qty,2));
		$('#total_blnc_qty_td_id').val(number_format(tot_count_qty,2));
	}

	// checking blance qty with requision qty
	function fnc_check_balance_qty(rowsl)
	{

		var update_id = $("#update_id").val();


		//var booking_qty = $("#bookQty"+rowsl).text()*1;
		var balance_qty = $("#totBalQty"+rowsl).text()*1;
		var reqsn_qty = $("#reqsnQty"+rowsl).val()*1;

		if(update_id!="")
		{
			var previous_reqsnQty = $("#previous_reqsnQty"+rowsl).val()*1;
			balance_qty = (previous_reqsnQty+balance_qty);
		}

		if(reqsn_qty>balance_qty)
		{
			alert("Requisition quantity can not greater than balance quantity");
			$("#reqsnQty"+rowsl).val('');
			$("#reqsnQty"+rowsl).focus();
		}
	}


	function fnc_fabric_requisition_for_batch_2( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}
		if(operation==6)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title+
				'*print2','print_fab_req_for_batch','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}
		if(operation==7)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title+
				'*print3','print_fab_req_for_batch_3','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}
		if(operation==8)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch_4','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}
		if(operation==9)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch_tg','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}
		if(operation==10)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_requisition_no').val()+'*'+report_title,'print_fab_req_for_batch_5','requires/fabric_requisition_for_batch_entry_controller_2');
			return;
		}

	 	if(form_validation('cbo_company_id*txt_requisition_date','Company*Requisition Date')==false)
		{
			return;
		}

		var row_num=$('#scanning_tbl tbody tr').length;
		//alert (row_num); return;
		var j=0;
		var dataString="";
		for (var i=1; i<=row_num; i++)
		{
			var reqsnQty=$('#reqsnQty'+i).val()*1;
			if(reqsnQty == ""){
				alert('Current Reqn. Qty Not Found');
				return;
			}
		}
		/*for (var i=1; i<=row_num; i++)
		{
			var job=$('#job'+i).text();
			var bookingNo=$('#bookingNo'+i).text();
			var fileNo=$('#fileNo'+i).text();
			var grouping=$('#grouping'+i).text();
			var constraction=$('#constraction'+i).text();
			var composition=$('#composition'+i).text();
			var gsm=$('#gsm'+i).text();
			var dia=$('#dia'+i).text();
			var poId=$('#poId'+i).val();
			var buyerId=$('#buyerId'+i).val();
			var colorId=$('#colorId'+i).val();
			var colorTypeId=$('#colorTypeId'+i).val();
			var reqsnQty=$('#reqsnQty'+i).val()*1;
			var remarks=$('#remarks'+i).val();
			var dtlsId=$('#dtlsId'+i).val();
			var isSales=$('#isSales'+i).val();
			var bodyPartId=$('#bodyPartId'+i).val();
			var bookintQty=$('#bookintQty'+i).val();

			if(reqsnQty>0 || dtlsId!="")
			{
				j++;

				dataString += '&job' + j + '=' + job + '&bookingNo' + j + '=' + bookingNo + '&fileNo' + j + '=' + fileNo + '&grouping' + j + '=' + grouping + '&constraction' + j + '=' + constraction + '&composition' + j + '=' + composition + '&gsm' + j + '=' + gsm + '&dia' + j + '=' + dia + '&poId' + j + '=' + poId + '&buyerId' + j + '=' + buyerId + '&colorId' + j + '=' + colorId + '&colorTypeId' + j + '=' + colorTypeId + '&reqsnQty' + j + '=' + reqsnQty + '&bodyPartId' + j + '=' + bodyPartId + '&bookintQty' + j + '=' + bookintQty + '&remarks' + j + '=' + remarks + '&dtlsId' + j + '=' + dtlsId + '&isSales' + j + '=' + isSales;
			}
		}*/
		// ==============Start
		var j = 0;
        var dataString = '';
        $("#scanning_tbl").find('tbody tr').each(function () {
            var job = $(this).find("td:eq(3)").text();
            var fileNo = $(this).find("td:eq(5)").text();
            var grouping = $(this).find("td:eq(6)").text();
            var bookingNo = $(this).find("td:eq(8)").text();
            var constraction = $(this).find("td:eq(11)").text();
            var composition = $(this).find("td:eq(12)").text();
            var gsm = $(this).find("td:eq(13)").text();
            var dia = $(this).find("td:eq(14)").text();



            var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
            var reqsnQty = $(this).find('input[name="reqsnQty[]"]').val();
            var remarks = $(this).find('input[name="remarks[]"]').val();
            var buyerId = $(this).find('input[name="buyerId[]"]').val();
            var cbuyerId = $(this).find('input[name="cbuyerId[]"]').val();
            var poId = $(this).find('input[name="poId[]"]').val();
            var colorTypeId = $(this).find('input[name="colorTypeId[]"]').val();
            var colorId = $(this).find('input[name="colorId[]"]').val();
            var bookintQty = $(this).find('input[name="bookintQty[]"]').val();
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
            var isSales = $(this).find('input[name="isSales[]"]').val();

            try {
                /*if (currentDelivery < 0.1) {
                    alert("Please Insert Roll Qty.");
                    return;
                }*/

                j++;

                dataString += '&job' + j + '=' + job + '&bookingNo' + j + '=' + bookingNo + '&fileNo' + j + '=' + fileNo + '&grouping' + j + '=' + grouping + '&constraction' + j + '=' + constraction + '&composition' + j + '=' + composition + '&gsm' + j + '=' + gsm + '&dia' + j + '=' + dia + '&poId' + j + '=' + poId + '&buyerId' + j + '=' + buyerId + '&colorId' + j + '=' + colorId + '&colorTypeId' + j + '=' + colorTypeId + '&reqsnQty' + j + '=' + reqsnQty + '&bodyPartId' + j + '=' + bodyPartId + '&bookintQty' + j + '=' + bookintQty + '&remarks' + j + '=' + remarks + '&dtlsId' + j + '=' + dtlsId + '&isSales' + j + '=' + isSales + '&cbuyerId' + j + '=' + cbuyerId;
            }
            catch (e) {
                //got error no operation
            }
        });

        if (j < 1) {
            alert('No data');
            return;
        }
        // =================end

		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_requisition_date*txt_requisition_no*update_id',"../")+dataString;

		//alert(dataString);
		//alert(data);//return;

		freeze_window(operation);

		http.open("POST","requires/fabric_requisition_for_batch_entry_controller_2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_fabric_requisition_for_batch_2_Reply_info;
	}

	function fnc_fabric_requisition_for_batch_2_Reply_info()
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
				$('#cbo_company_id').attr('disabled',true);
				var list_view = trim(return_global_ajax_value(response[1], 'populate_list_view', '', 'requires/fabric_requisition_for_batch_entry_controller_2'));
				$("#scanning_tbl tbody").html(list_view);
				set_button_status(1, permission, 'fnc_fabric_requisition_for_batch_2',1);
			}
			release_freezing();
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/fabric_requisition_for_batch_entry_controller_2.php?data=" + data+'&action='+action, true );
	}




function fnc_load_report_format(data)
{
	var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/fabric_requisition_for_batch_entry_controller_2');
	print_report_button_setting(report_ids);
}

function print_report_button_setting(report_ids)
{
	if(trim(report_ids)=="")
	{
		$("#Print1").show();
		$("#Print2").show();
		$("#Print3").show();
		$("#Print_tg").show();
		$("#Print5").show();
 	}
	else
	{
		var report_id=report_ids.split(",");
		$("#Print1").hide();
		$("#Print2").hide();
		$("#Print3").hide();
		$("#Print_tg").hide();
		$("#Print5").hide();
 		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==86)
			{
				$("#Print1").show();
			}
			else if(report_id[k]==84)
			{
				$("#Print2").show();
			}
			else if(report_id[k]==88)
			{
				$("#Print3").show();
			}
			else if(report_id[k]==839)
			{
				$("#Print_tg").show();
			}
			else if(report_id[k]==129)
			{
				$("#Print5").show();
			}
		}
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
                        <td align="right" colspan="3"><strong>Requisition No</strong></td>
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
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/fabric_requisition_for_batch_entry_controller_2', this.value, 'load_drop_down_location', 'location_td' );fnc_load_report_format(this.value);",0 );
                            ?>
                        </td>
                        <td width="80" align="right">Location</td>
                        <td width="140" id="location_td">
                            <?
                                echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
                        <td width="130" align="right" class="must_entry_caption">Requisition Date</td>
                        <td width="80"><input type="text" name="txt_requisition_date" id="txt_requisition_date" value="<?php echo date('d-m-Y');?>" class="datepicker" style="width:80px;" readonly /></td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3"><strong>Select Fabric</strong></td>
                        <td>
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px;" placeholder="Browse For Order No" onDblClick="openmypage_po()" readonly/>
                        </td>
                        <td align="right"></td>
                        <td colspan="2">
                            <?
                            include("../terms_condition/terms_condition.php");
                            terms_condition(123,'txt_requisition_no','../');
                            ?>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:2080px;text-align:left">
				<table cellpadding="0" width="2060" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
					<!-- if you add new column please check fn_deleteRow(); and fnc_fabric_requisition_for_batch_2(); function for td:nth-child() -->
                    <thead>
                    	<th width="30">Sl</th>
                        <th width="60">Buyer</th>
                        <th width="100">Cust Buyer</th>
                        <th width="120">Job No</th>
                        <th width="100">Style No</th>
                        <th width="80">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Order/FSO No</th>
                        <th width="100">Booking No</th>
                        <th width="150">Body Part</th>
                        <th width="100">Color TYPE</th>
                        <th width="100">Construction</th>
                        <th width="100">Composition</th>
                        <th width="50">F. GSM</th>
                        <th width="50">F. Dia</th>
                        <th width="100">Color/ Code</th>
                        <th width="80">Book Qty.</th>
                        <th width="80">Total Reqn. Qty.</th>
                        <th width="80">Balance</th>
                        <th width="80">Current Reqn. Qty.</th>
                        <th width="100">Remarks</th>
                        <th width="30" ></th>
                    </thead>
                </table>
                <div style="width:2080px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="2060" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        </tbody>
                        <tfoot>
                			<tr>
                				<td width="30"></td>
		                        <td width="60"></td>
		                        <td width="100"></td>
		                        <td width="120"></td>
		                        <td width="100"></td>
		                        <td width="80"></td>
		                        <td width="80"></td>
		                        <td width="100"></td>
		                        <td width="100"></td>
		                        <td width="150"></td>
		                        <td width="100"></td>
		                        <td width="100"></td>
		                        <td width="100"></td>
		                        <td width="50"></td>
		                        <td width="50"></td>
	                			<td width="100" style="text-align:right; font-weight:bold;">Total</td>
	                			<td width="80" style="text-align:right; font-weight:bold;" id="total_book_qty_td_id" name=""></td>
	                			<td width="80" style="text-align:right; font-weight:bold;" id="total_req_qty_td_id"></td>
	            				<td width="80" style="text-align:right; font-weight:bold;" id="balance_td_id"></td>
	        					<td width="80" align="center"><input type="text" class="text_boxes_numeric" style="width:65px" id="total_blnc_qty_td_id" name="" readonly/></td>
	        					<td width="100"></td>
	        					<td width="30" ></td>
    						</tr>
                		</tfoot>
                	</table>
                </div>
                <br>
                <table width="1810" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <?
                            	echo load_submit_buttons($permission,"fnc_fabric_requisition_for_batch_2",0,1,"reset_form('requisitionEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();')",1);
                            ?>
                            <input type="button" class="formbutton" id="Print2" style="width:120px;" value="Print 2" onClick="fnc_fabric_requisition_for_batch_2(6)" />
                            <input type="button" class="formbutton" id="Print3" style="width:120px;" value="Print 3" onClick="fnc_fabric_requisition_for_batch_2(7)" />
							<input type="button" class="formbutton" id="Print4" style="width:120px;" value="Print 4" onClick="fnc_fabric_requisition_for_batch_2(8)" />
							<input type="button" class="formbutton" id="Print_tg" style="width:120px;" value="TG-1" onClick="fnc_fabric_requisition_for_batch_2(9)" />
							<input type="button" class="formbutton" id="Print5" style="width:120px;" value="Print 5" onClick="fnc_fabric_requisition_for_batch_2(10)" />
                        </td>
                    </tr>
                </table>
			</fieldset>
    	</div>
	</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>