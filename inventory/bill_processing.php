<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bill Processing
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	27-5-2017
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
echo load_html_head_contents("Bill Processing","../", 1, 1, $unicode,0,0);

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openmypage_bill_number()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bill_processing_controller.php?action=bill_popup&cbo_company_id='+cbo_company_id,'System No Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var bill_ids=this.contentDoc.getElementById("hidden_bill_id").value;	 //Requisition Id and Number
			var bill_id=bill_ids.split('_');
			if(bill_id[0]!="")
			{
				freeze_window(5);
				reset_form('billprocessingEntry_1','','','','','');
				get_php_form_data(bill_id[0]+'*'+bill_id[1], "populate_data_from_bill", "requires/bill_processing_controller" );
				$('#hidden_reference_ids').val(bill_id[3]);
				//var list_view = trim(return_global_ajax_value(bill_id[0]+'_'+bill_id[1]+'_'+bill_id[2]+'_'+bill_id[3]+'_'+bill_id[4]+'_'+bill_id[5], 'populate_list_view', '', 'requires/bill_processing_controller'));
				//$("#scanning_tbl tbody").html(list_view);	
				//set_all_onclick();
				var list_view2 = trim(return_global_ajax_value(bill_id[0]+'_'+bill_id[1]+'_'+bill_id[2]+'_'+bill_id[3], 'short_list_view', '', 'requires/bill_processing_controller'));
				$("#search_container").html(list_view2);
				reset_form('','','','','$(\'#scanning_tbl tbody tr\').remove();');	
				set_button_status(1, permission, 'fnc_bill_processing',1);
				release_freezing();
			}
		}
	}
	
	function openpopup_party()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bill_processing_controller.php?action=party_popup&company_id='+cbo_company_id,'Party Popup', 'width=355px,height=380px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var party_datas=this.contentDoc.getElementById("party_id").value;	 //Requisition Id and Number
			party_data=party_datas.split('_');
			var party_id=party_data[0];
			var party_name=party_data[1];
			$('#hidden_party_id').val(party_id);
			$('#txt_party').val(party_name);
			release_freezing();
			
		}
	}
	
	function populate_list_view_send(data)
	{
		// alert(data);
		var bill_dtls_id=data.split('_');
		//$('#hidden_bill_dtls_id').val(bill_dtls_id[6]);
		
		var list_view = trim(return_global_ajax_value(data, 'populate_list_view', '', 'requires/bill_processing_controller'));
		$("#scanning_tbl tbody").html(list_view);	
		set_all_onclick();
		sumTotalMrrAmnt();
		TotalMrrRecvIDS();
		$("#sumTotalMrr").css("display", "block");
	}
	
	function openmypage_mrr()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var party_id = $('#hidden_party_id').val();
		var wo_po_id = $('#hidden_reference_id').val();
		var wo_po_no = $('#txt_reference_no').val();
		var item_cat_id = $('#hidden_item_cat').val();
		// var hidden_wo_nonwo_type = $('#hidden_wo_nonwo_type').val();
		var hidden_reference_ids = $('#hidden_reference_ids').val();
		
		if (form_validation('cbo_company_id*txt_reference_no','Company*WO')==false)
		{
			return;
		}
		else
		{
			var num_row=$('#scanning_tbl tbody tr').length*1;
			if(num_row){
				var allprodIds=''; var p = 1;
				for(var k=0; k<num_row; k++)
				{
					if(allprodIds =='') allprodIds=$("#prodIds"+p).val();
					else allprodIds+=","+$("#prodIds"+p).val();
					p++;
				}
			}
			 	
			// alert(wo_po_no);
			var title = 'MRR or Receive Ref Number';	
			var page_link ='requires/bill_processing_controller.php?cbo_company_id='+cbo_company_id+'&wo_po_id='+wo_po_id+'&wo_po_no='+wo_po_no+'&party_id='+party_id+'&item_cat_id='+item_cat_id+'&hidden_reference_ids='+hidden_reference_ids+'&allprodIds='+allprodIds+'&action=mrr_popup';
			var popup_width="1050px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=300px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_data=this.contentDoc.getElementById("hidden_data").value; 
				if(hidden_data==''){
					alert("No data select."); return;
				}
				
				var data=hidden_data.split("_");
				var html=''; var num_row=$('#scanning_tbl tbody tr').length+1;
				var totalMrr=0;
				for(var k=0; k<data.length; k++)
				{
					if(num_row%2==0) var bgcolor="#E9F3FF"; else var bgcolor="#FFFFFF";

					var row_data=data[k].split("**");
					var program_no=''; var booking_no=row_data[9]; 
					if(row_data[7]==1) 
					{
						program_no=row_data[9];
						booking_no=row_data[21]; 
					}
						
					var html=html
					+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="30">'+num_row
					+'</td><td width="150" style="word-break:break-all;">'+row_data[2]
					+'</td><td width="70" style="word-break:break-all;">'+row_data[5]
					+'</td><td width="100" style="word-break:break-all;">'+row_data[10]
					+'</td><td width="60" style="word-break:break-all;">'+row_data[6]
					+'</td><td width="130" id="job'+num_row+'">'+row_data[11]
					+'</td><td width="75" style="word-break:break-all;">'+row_data[19]
					+'</td><td width="100" style="word-break:break-all;">'+row_data[20]
					+'</td><td width="125" style="word-break:break-all;">'+row_data[28]
					+'</td><td width="100" id="gsm'+num_row+'">'+row_data[24]
					+'</td><td width="100" id="dia'+num_row+'">'+row_data[14]
					+'</td><td width="70" align="center" style="word-break:break-all;">'+row_data[21]
					+'</td><td width="70" align="right">'+row_data[22]
					+'</td><td width="120" align="right">'+row_data[15] 
					+'</td><td width="80" align="right"><input type="text" value="'+number_format(row_data[17],4)+'" class="text_boxes_numeric" style="width:55px" id="rateID'+num_row+'" name="rateID[]" readonly/>'
					+'</td><td width="90" align="right"><input type="text" value="'+row_data[16]+'" class="text_boxes_numeric" style="width:55px" id="paymentOverrecv'+num_row+'" name="paymentOverrecv[]" onKeyUp="fnc_chng_mrr_amnt('+num_row+')"/>'
					+'</td><td width="90" align="right"><input type="text" value="'+number_format(row_data[18],4,'.','')+'" class="text_boxes_numeric" style="width:70px" id="mrr_amnt_acpt'+num_row+'" name="mrr_amnt_acpt[]" readonly/>'
					+'</td><td width="100" align="center">'+row_data[8]
					+'<input type="hidden" value="'+row_data[13]+'" class="text_boxes_numeric" style="width:55px" id="recvId'+num_row+'" name="recvId[]"/><input type="hidden" value="'+row_data[10]+'" class="text_boxes_numeric" style="width:55px" id="recvNo'+num_row+'" name="recvNo[]"/><input type="hidden" value="'+row_data[25]+'" class="text_boxes_numeric" style="width:55px" id="trimsDtlsId'+num_row+'" name="trimsDtlsId[]"/><input type="hidden" value="'+row_data[26]+'" class="text_boxes_numeric" style="width:55px" id="woID'+num_row+'" name="woID[]"/><input type="hidden" value="'+row_data[27]+'" class="text_boxes_numeric" style="width:55px" id="prodIds'+num_row+'" name="prodIds[]"/><input type="hidden" value="'+row_data[2]+'" class="text_boxes_numeric" style="width:55px" id="woNumber'+num_row+'" name="woNumber[]"/><input type="hidden" value="'+row_data[29]+'" class="text_boxes_numeric" style="width:55px" id="po_id'+num_row+'" name="po_id[]"/><input type="hidden" value="'+row_data[12]+'" class="text_boxes_numeric" style="width:55px" id="mrrAmount'+num_row+'" name="mrrAmount[]"/><input type="hidden" value="'+row_data[30]+'" class="text_boxes_numeric" style="width:55px" id="currencyIDs'+num_row+'" name="currencyIDs[]"/></td>'
					+'" id="programBookingId'+num_row+'" name="programBookingId[]"/><input type="hidden" value="'+row_data[0]
					+'" id="receiveBasis'+num_row+'" name="receiveBasis[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/></td><td  align="left">'
					+'<input type="button" value="-" class="text_boxes_numeric" style="width:30px;text-align:center;" id="decrease_'+num_row+'" onClick="javascript:fn_deletebreak_down_tr('+num_row+')" /></td></tr>';
					num_row++;
					
				}
				$("#scanning_tbl tbody:last").append(html);	
				set_all_onclick();
				
				sumTotalMrrAmnt();
				//TotalMrrRecvIDS();
				
			}
			// call-- create_mrr_search_list_view
			//<input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
			$("#sumTotalMrr").css("display", "block");
			
		}
	}
	function fn_deletebreak_down_tr(rowNo) 
	{

		/*var numRow = $('table#scanning_tbl tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#scanning_tbl tbody tr:last').remove();
		}*/

		var numRow = $('table#scanning_tbl tbody tr').length; 
		$("#tr_"+rowNo).remove();

		

		/*var index=rowNo-1
		$("#scanning_tbl tbody tr:eq("+index+")").remove();
		var numRow=$('#scanning_tbl tbody tr').length;
		for(i = rowNo;i <= numRow;i++)
		{
			$("#scanning_tbl tr:eq("+i+")").find("input,select").each(function() 
			{
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
				  'value': function(_, value) { return value }              
				}); 
				
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$("#scanning_tbl tr:eq("+i+") td:eq(0)").text(i);
			})

		}*/

		sumTotalMrrAmnt(numRow);

	}
	
	function fnc_bill_processing( operation )
	{
		if(operation==4)
		{
			
			var recvIDs=$('#hidden_reference_ids').val();
			var report_title=$( "div.form_caption" ).html();  
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#hidden_party_id').val()+'*'+$('#txt_reference_no').val()+'*'+$('#hidden_reference_id').val()+'*'+recvIDs+'*'+$('#txt_party').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val()+'*'+report_title+'*'+$('#cbo_buyer_name').val(),'print_bill_processing_action','requires/bill_processing_controller');
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_party*txt_bill_no*txt_bill_date','Company*Party*Bill No*Bill Date')==false)
		{
			return; 
		}

		/*var row_num=$('#scanning_tbl tbody tr').length;
		var dataString=""; var j=0; //var mrrss="";
		for (var i=1; i<=row_num; i++)
		{
			var receiveId=$('#recvId'+i).val();
			var receiveNo=$('#recvNo'+i).val();
			var acceptedQnty=$('#paymentOverrecv'+i).val();
			var trimsDtlsId=$('#trimsDtlsId'+i).val();
			
			var woNumber=$('#woNumber'+i).val();
			var woID=$('#woID'+i).val(); 
			var prodIds=$('#prodIds'+i).val(); 
			var po_id=$('#po_id'+i).val();
			var mrrAmount=$('#mrrAmount'+i).val(); 
			var currencyIDs=$('#currencyIDs'+i).val(); 
						
			
			j++;
			//mrrss+=',' + mrrAmount;
		  	dataString+='&receiveId' + j + '=' + receiveId + '&receiveNo' + j + '=' + receiveNo + '&acceptedQnty' + j + '=' + acceptedQnty + '&trimsDtlsId' + j + '=' + trimsDtlsId + '&woNumber' + j + '=' + woNumber + '&woID' + j + '=' + woID  + '&prodIds' + j + '=' + prodIds + '&po_id' + j + '=' + po_id + '&mrrAmount' + j + '=' + mrrAmount + '&currencyIDs' + j + '=' + currencyIDs;
		}*/

		var j=0; var dataString=''; 
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var receiveId=$(this).find('input[name="recvId[]"]').val();
			var receiveNo=$(this).find('input[name="recvNo[]"]').val();
			var acceptedQnty=$(this).find('input[name="paymentOverrecv[]"]').val();
			var trimsDtlsId=$(this).find('input[name="trimsDtlsId[]"]').val();
			var woNumber=$(this).find('input[name="woNumber[]"]').val();
			var woID=$(this).find('input[name="woID[]"]').val();
			var prodIds=$(this).find('input[name="prodIds[]"]').val();
			var po_id=$(this).find('input[name="po_id[]"]').val();
			var mrrAmount=$(this).find('input[name="mrrAmount[]"]').val();
			var currencyIDs=$(this).find('input[name="currencyIDs[]"]').val();

			j++;
			dataString+='&receiveId' + j + '=' + receiveId + '&receiveNo' + j + '=' + receiveNo + '&acceptedQnty' + j + '=' + acceptedQnty + '&trimsDtlsId' + j + '=' + trimsDtlsId + '&woNumber' + j + '=' + woNumber + '&woID' + j + '=' + woID  + '&prodIds' + j + '=' + prodIds + '&po_id' + j + '=' + po_id + '&mrrAmount' + j + '=' + mrrAmount + '&currencyIDs' + j + '=' + currencyIDs;

		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		// alert(recvIDsHidenID);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('cbo_company_id*hidden_party_id*txt_bill_no*txt_bill_date*txt_remarks*txt_reference_no*hidden_reference_id*txt_system_no*update_id*hidden_bill_dtls_id*recvIDsHidenID*cbo_buyer_name',"../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/bill_processing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_bill_processing_Reply_info;
	}

	function fnc_bill_processing_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				$( "#txt_mrr_no" ).focus();
				//var list_view = trim(return_global_ajax_value(response[1], 'populate_list_view', '', 'requires/bill_processing_controller'));
				//$("#scanning_tbl tbody").html(list_view);
				var cbo_company_id=$('#cbo_company_id').val();
				var hidden_party_id=$('#hidden_party_id').val();
				//reset_form('','','','','$(\'#scanning_tbl tbody tr\').remove();');
				show_list_view(response[1]+'_'+cbo_company_id+'_'+hidden_party_id,'short_list_view','search_container','requires/bill_processing_controller','setFilterGrid("list_view",-1)');
  				reset_form('','','txt_reference_no*hidden_reference_id*hidden_reference_ids*hidden_bill_dtls_id*txt_mrr_no*hidden_item_cat','','$(\'#scanning_tbl tbody tr\').remove();');
				//"reset_form('billprocessingEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();')"
				set_button_status(1, permission, 'fnc_bill_processing',1);
			}
			var cbo_company_id=$('#cbo_company_id').val();
			var hidden_party_id=$('#hidden_party_id').val();
			show_list_view(response[1]+'_'+cbo_company_id+'_'+hidden_party_id,'short_list_view','search_container','requires/bill_processing_controller','setFilterGrid("list_view",-1)');
			reset_form('','','txt_reference_no*hidden_reference_id*hidden_reference_ids*hidden_bill_dtls_id*txt_mrr_no','','$(\'#scanning_tbl tbody tr\').remove();');
			release_freezing();
		}
		release_freezing();
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/bill_processing_controller.php?data=" + data+'&action='+action, true );
	}
	
	// new wopo
	function open_wopopopup(page_link,title)
	{
		if( form_validation('cbo_company_id*txt_party','Company Name*Party')==false )
		{
			return;
		}
		var title = "WO Popup";
		var company = $("#cbo_company_id").val();
		var party = $("#hidden_party_id").val();
		var buyer_id = $("#cbo_buyer_name").val();
		page_link='requires/bill_processing_controller.php?action=wo_po_popup&company='+company +'&party='+party+'&buyer_id='+buyer_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split("_"); // wo/po number
			var sysNumber=(this.contentDoc.getElementById("txt_selected_no").value);
			var sysId=(this.contentDoc.getElementById("txt_selected_id").value);
			var sysCat=(this.contentDoc.getElementById("txt_selected_cat").value);
			var sysNumber_arr = $.unique(sysNumber.split(','));
			var sysId_arr = $.unique(sysId.split(','));
			// alert(sysNumber_arr);
			if (sysNumber!="")
			{
				//$("#update_id").val(sysNumber[0]);
				//get_php_form_data(sysNumber[0], "populate_master_from_data", "requires/get_in_entry_controller" );
				//show_list_view(sysNumber[0],'show_dtls_list_view','list_container','requires/get_in_entry_controller','');
				//disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company', 1, "", "" );
				//show_list_view(sysNumber[0]+'*'+sysNumber[1]+'*'+sysNumber[2]+'*'+sysNumber[3],'items_list_view_action','items_list_view','requires/get_in_entry_controller',''); //new for item list view
				//get_php_form_data(sysNumber[0], "data_populate_from_side_list", "requires/get_in_entry_controller" );
				$("#txt_reference_no").val("");
				$("#hidden_reference_id").val("");
				$("#hidden_item_cat").val("");
				
				$("#txt_reference_no").val(sysNumber_arr);
				$("#hidden_reference_id").val(sysId_arr);
				$("#hidden_item_cat").val(sysCat);
				// $("#hidden_wo_nonwo_type").val("");
			}
		}		
	}
	
	function adjustment_fnc()
	{
		var cbo_company_id = $('#cbo_company_id').val();
			
			if (form_validation('cbo_company_id*txt_system_no','Company*System No')==false)
			{
				return;
			}
			var bill_id =$('#update_id').val();
			var party_id=$('#hidden_party_id').val();
			var receive_id=$('#hidden_reference_ids').val();
			//id,company_id,party_id,wo_po_no,wo_po_id,receive_id
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bill_processing_controller.php?action=adjustment_popup&company_id='+cbo_company_id+'&bill_id='+bill_id+'&party_id='+party_id+'&receive_id='+receive_id,'Adjustment Popup', 'width=555px,height=365px,center=1,resize=1,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var party_datas=this.contentDoc.getElementById("party_id").value;	 //Requisition Id and Number
				party_data=party_datas.split('_');
				var party_id=party_data[0];
				var party_name=party_data[1];
				$('#hidden_party_id').val(party_id);
				$('#txt_party').val(party_name);
				release_freezing();
				
			}
	}
	

	/*function sumTotalMrrAmnt________()
	{
		var num_rows=$('#scanning_tbl tbody tr').length;
		var mrrAmountTotal=0; //var recvIDsTotal=0;
		for(var k=1; k<=num_rows; k++ )
		{
			mrrAmountTotal+=$("#mrrAmount"+k).val()*1;
			//recvIDsTotal+=$("#recvId"+k).val()+'**';
		}
		//alert(mrrAmountTotal);
		$("#sumTotalMrr").val(number_format(mrrAmountTotal,4));
		//$("#recvIDsHidenID").val(recvIDsTotal);
	}*/

	function sumTotalMrrAmnt()
	{
		var mrrAmountTotal=0;
		//var total_roll_weight='';
		$("table#scanning_tbl").find('tbody tr').each(function()
		{
			var acptamount=$(this).find('input[name="mrr_amnt_acpt[]"]').val();
			//alert(acptamount);
			mrrAmountTotal=mrrAmountTotal*1+acptamount*1;
		});	
		$("#sumTotalMrr").val(number_format(mrrAmountTotal,4));
	}
			
	function TotalMrrRecvIDS()
	{
		var num_rows=$('#scanning_tbl tbody tr').length;
		var recvIDsTotal=0;
		for(var k=1; k<=num_rows; k++ )
		{
			recvIDsTotal+=$("#recvId"+k).val()+'**';
		}
		$("#recvIDsHidenID").val(recvIDsTotal);
	}

	function fnc_chng_mrr_amnt(row_ids)
	{
		var acpt_amount=$('#paymentOverrecv'+row_ids).val()*1;
		var rate=$('#rateID'+row_ids).val()*1;
		var total_mrr_qty_row=acpt_amount*rate;

		//var noCommas = total_mrr_qty_row.replace(/,/g , '');
		$('#mrr_amnt_acpt'+row_ids).val(number_format(total_mrr_qty_row,4)); // total_mrr_qty_row.toFixed(4)
		$('#mrrAmount'+row_ids).val(total_mrr_qty_row);
		totMrrAcpt=0;
		var num_rows=$('#scanning_tbl tbody tr').length;
		for(var k=1; k<=num_rows; k++ )
		{
			totMrrAcpt+=$("#mrrAmount"+k).val()*1;
		}
		$('#sumTotalMrr').val(number_format(totMrrAcpt,4));
	}
	function anable_field()
	{
		document.getElementById('cbo_company_id').disabled = false;
		document.getElementById('txt_party').disabled = false;
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="billprocessingEntry_1" id="billprocessingEntry_1"> 
		<div align="center" style="width:100%;">
            <fieldset style="width:910px;">
				<legend>Bill Processing</legend>
                <table cellpadding="0" cellspacing="2" width="1150">
                    <tr>
                        <td align="right" colspan="4"><b>System No</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_bill_number()" placeholder="Browse For System No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption" align="right">Company Name</td>
                        <td width="140">
                            <? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/bill_processing_controller', this.value, 'load_drop_down_buyer', 'buyer_td');",0 );
                            ?>
                        </td>
                        <td width="80" align="right">Buyer</td>                 
                        <td width="140" id="buyer_td">
                           <? 
                       			echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);    
                           ?>
                        </td>
                        <td width="80" class="must_entry_caption" align="right">Party</td>                                              
                        <td width="140">
                           <input type="text" name="txt_party" id="txt_party" class="text_boxes" style="width:140px;" placeholder="Browse For Party" onDblClick="openpopup_party()" readonly/>
                           <input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" style="width:140px;"/>
                        </td>
                        <td width="100" align="right" class="must_entry_caption">Bill No</td>
                        <td width="80"><input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes"  style="width:120px;"/> 
  						</td>
                        
                        <td width="100" align="right" class="must_entry_caption">Bill Date</td>
                        <td width="80"><input type="text" name="txt_bill_date" id="txt_bill_date" class="datepicker" style="width:80px;" readonly /></td>
                    </tr>
                    <tr>
                    	<td width="80"  align="right">Remarks</td>        
                        <td colspan="9"	><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:1045px;" placeholder="Remarks" /></td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1730px;text-align:left">
            	<table cellpadding="0" cellspacing="2" width="900">
                 <tr>
                    <td align="right"><b>WO Number</b></td>
                   <td width="150px">
                        <input type="text" name="txt_reference_no" id="txt_reference_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_wopopopup();"/>
  						<input type="hidden" id="hidden_reference_id" name="hidden_reference_id" value="" />
                        <input type="hidden" id="hidden_reference_ids" name="hidden_reference_ids" value="" />
                        <input type="hidden" id="hidden_bill_dtls_id" name="hidden_bill_dtls_id" value="" />
                        <input type="hidden" id="hidden_item_cat" name="hidden_item_cat" value="" /> 
                        <!-- <input type="hidden" id="hidden_wo_nonwo_type" name="hidden_wo_nonwo_type" value="" />  -->
                       <!-- <input type="text" id="recvIDsHidenID" name="recvIDsHidenID" /> -->
                        
                         
                     <!--/*    <input class="text_boxes"  type="text" name="txt_wo_po" id="txt_wo_pi" onDblClick="openmypage('xx','Order Search')"  placeholder="Double Click" style="width:158px;" readonly />
                        <input type="text" id="txt_wo_po" name="txt_wo_po" value="" /></td> 
*/-->
                    </td>
                    <td align="right"><b>Receive Ref Number</b></td>
                    <td>
                        <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:140px;" placeholder="Browse For MRR No" onDblClick="openmypage_mrr()" readonly/>
                    </td>
                 </tr>
                </table>
				<table cellpadding="0" width="1710" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="150">WO</th>
                        <th width="70">Receive Date</th>
                        <th width="100">MRR/System ID</th>
                        <th width="60">Challan No</th>
                        <th width="130">Receive Basis</th>
                        <th width="75">Job No</th>
                        <th width="100">Style No</th>
                        <th width="125">Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Des.</th>
                        <th width="70">UOM</th>
                        <th width="70">W/O Qty.</th>
                        <th width="120">Full Receive Qty.</th>
                        <th width="80">Rate</th>
						<th width="90">Accepted Qty</th>
                        <th width="90">MRR Amount</th>
                        <th width="100">WO Currency</th>
                        <th></th>
                    </thead>
                </table>
                <div style="width:1730px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1710" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        </tbody>
                        <tfoot>
                        	<tr>
	                            <td width="30" colspan="16" >&nbsp </td>
	                            <td width="90" align="right"><input type="text" class="text_boxes" style="width:75px; display:none; text-align:right;" disabled id="sumTotalMrr"></td>
                                <td width="50"><input type="hidden" class="text_boxes" style="width:50px;  text-align:right;" disabled id="recvIDsHidenID"></td>
                            </tr>
                        </tfoot>
                	</table>
                    <input  name="adjusment_btn" id="adjusment_btn" class="formbutton" value="Adjustment" style=" width:100px; margin-left:800px;" onClick="adjustment_fnc();" type="button">
                </div>
                <br>
                <table width="1725" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_bill_processing",0,1,"reset_form('billprocessingEntry_1','search_container','','','$(\'#scanning_tbl tbody tr\').remove();anable_field();')",1);
								//function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids ) 
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
    	</div>
       <div style="width:100%; float:left; margin:auto" align="center" id="search_container">
		
		</div>
	</form>
  	
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
