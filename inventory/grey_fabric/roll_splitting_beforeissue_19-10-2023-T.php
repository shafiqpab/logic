<?
/*-------------------------------------------- Comments
Purpose			: This form will create Grey Fabric Delivery Roll Wise
				
Functionality	:	
JS Functions	:
Created by		: Ashraful
Creation date 	: 28-04-2015
Updated by 		: Zaman		
Update date		: 10.12.2019   
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
echo load_html_head_contents("Roll Splitting Before Issue","../../", 1, 1, $unicode,'','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
    <? 	
	//*****************************************************************************************************
	$variable_data=sql_select("select company_name,smv_source from variable_settings_production where variable_list in(27) and is_deleted=0 and status_active=1");
	
	$barcode_generation=array();
	foreach($variable_data as $row)
	{
		$barcode_generation[$row[csf('company_name')]]=$row[csf('smv_source')];
	}
	$jsbarcode_generation= json_encode($barcode_generation);
	echo "var barcode_generation = ".$jsbarcode_generation. ";\n";	
    ?>

	function openmypage_mrr()
	{ 
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_splitting_beforeissue_controller.php?action=mrr_popup','Receive Popup', 'width=800px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_data=this.contentDoc.getElementById("hidden_system_id").value;//Barcode Nos
			barcode_data=barcode_data.split("_");
			var system_no=barcode_data[1];
			var barcode_nos=barcode_data[2];
			var sales_order=barcode_data[5];
			var barcodeSels= barcode_nos+"_"+sales_order+"_"+system_no;
			
			$("#txt_system_no").val(barcode_data[0]);
			$("#update_id").val(barcode_data[1]);
			$("#hdn_productionDtlsId").val(barcode_data[7]); //this filed for getting dtlsID for entry from 2 for Direct Print sticker

			$("#deleted_all_id").val('');
			
			if(barcode_nos!="")
			{
				get_php_form_data( barcodeSels, "load_barcode_mst_form", "requires/roll_splitting_beforeissue_controller" );
				$("#txt_original_wgt").val(barcode_data[4]);
				$("#hidden_roll_wgt").val(barcode_data[4]);
				
				$("#txt_original_pcs").val(barcode_data[6]);
				$("#hidden_original_pcs").val(barcode_data[6]);
				$("#hidden_table_id").val(barcode_data[3]);
				
				var company=$("#hidden_company_id").val();
				if(barcode_generation[company]==2)
				{
					 $("#barcode_generation").val("Send To Printer");
					 $('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_send_printer_text();");
					// onclick="fnc_send_printer_text()	
				}
				else
				{
					$("#barcode_generation").val("Barcode Generation");
					$('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_bundle_report(1);");
					$("#barcode_generation_128").val("Barcode 128 v2");
					$('#barcode_generation_128').removeAttr("onclick").attr("onclick","fnc_bundle_report(2);");
				}
				
				set_button_status(1, permission, 'fnc_roll_split_entry',1);
				show_list_view(barcode_data[3], 'roll_details_update', 'scanning_tbody','requires/roll_splitting_beforeissue_controller', '' );
			}
		}
	}

	function openmypage_barcode()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_splitting_beforeissue_controller.php?&action=barcode_popup','Barcode Popup', 'width=1080px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			//alert(barcode_nos);
			//hidden_company_id
			if(barcode_nos!="")
			{
				get_php_form_data( barcode_nos, "load_barcode_mst_form", "requires/roll_splitting_beforeissue_controller" );
				var company=$("#hidden_company_id").val();
				if(barcode_generation[company]==2)
				{
				    $("#barcode_generation").val("Send To Printer");
				    $('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_send_printer_text();");
				}
				else
				{
					$("#barcode_generation").val("Barcode Generation");
					$('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_bundle_report(1);");
					$("#barcode_generation_128").val("Barcode 128 v2");
					$('#barcode_generation_128').removeAttr("onclick").attr("onclick","fnc_bundle_report(2);");	
				}
			}
		}
	}
	
	function barcode_scan_dtls(is_update,barcode_no)
	{
		
		var response=trim(return_global_ajax_value( barcode_no, 'check_barcode_no', '', 'requires/roll_splitting_beforeissue_controller')).split('**');
		var bar_code=trim(barcode_no);
		if(response[0]==1) 
		{ 
			alert('Sorry! Barcode Already Scanned.\nFor more split or change roll weight brows below system no.\nSystem Number = ' + response[1]); 
			return; 
		}
		if(response[0]==0) 
		{ 
			alert('Barcode Already Used in Grey Roll Issue.\nIssue Number = ' + response[1]); 
			$('#txt_bar_code_num').val('');
			return; 
		}
		if(barcode_no!="" || response[0]==2)
		{
			var response_sales = return_global_ajax_value( barcode_no, 'check_sales_order', '', 'requires/roll_splitting_beforeissue_controller');
			var barcodeSels= barcode_no+"_"+response_sales;
			get_php_form_data( barcodeSels, "load_barcode_mst_form", "requires/roll_splitting_beforeissue_controller" );
			
			var company=$("#hidden_company_id").val();
			if(barcode_generation[company]==2)
			{
			 	$("#barcode_generation").val("Send To Printer");
				$('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_send_printer_text();");	
			}
			else
			{
			 	$("#barcode_generation").val("barcode_generation");
				$('#barcode_generation').removeAttr("onclick").attr("onclick","fnc_bundle_report(1);");
				$("#barcode_generation_128").val("Barcode 128 v2");
				$('#barcode_generation_128').removeAttr("onclick").attr("onclick","fnc_bundle_report(2);");	
			}
		}
		scanned_barcode.push(barcode_no);	
	}	
	
	function generate_report_file(data,action)
	{
		window.open("requires/roll_splitting_beforeissue_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_roll_split_entry( operation )
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
	 
		var j=0; var dataString=''; var prev_batch=''; var prev_color=''; var breakOut = true; var new_batch_no=''; var new_batch_id='';
		var rfid_array = new Array();
		var diplicat_rfid = 0;
		var rfId_flag=0;
		$("#scanning_tbl_top").find('tbody tr').each(function()
		{
			var roll_no=$(this).find('input[name="roll_no[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var rfidNo=$(this).find('input[name="rfidNo[]"]').val();
			var update_roll_id=$(this).find('input[name="updateRollId[]"]').val();
			var update_dtls_id=$(this).find('input[name="updateDtlsId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var hideRollWgt=$(this).find('input[name="hideRollWgt[]"]').val();
			
			var qtyInPcs=$(this).find('input[name="qtyInPcs[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			
			if(rfidNo!=undefined && rfidNo!=""){
				var txtRFId_length=rfidNo.length; 
				if(txtRFId_length!=9)
				{
					rfId_flag=1;
				}
				if (jQuery.inArray(rfidNo, rfid_array) == -1) {
					rfid_array.push(rfidNo);
				}else{
					diplicat_rfid = 1;
				}
			}
			j++;
			
			dataString+='&roll_no_' + j + '=' + roll_no + '&barcodeNo_' + j + '=' + barcodeNo + '&rfidNo_' + j + '=' + rfidNo + '&update_roll_id_' + j + '=' + update_roll_id +
			'&rollWgt_' + j + '=' + rollWgt + '&hideRollWgt_' + j + '=' + hideRollWgt + '&qtyInPcs_' + j + '=' + qtyInPcs + '&hiddenQtyInPcs_' + j + '=' + hiddenQtyInPcs + '&update_dtls_id_' + j + '='+update_dtls_id ;
		});
	
		
		if(j<1)
		{
			alert('No data');
			return;
		}

		for(var p=1;p<=j;p++)
		{
			if(form_validation('rollWgt_'+p,'Roll Wgt.')==false)
		   	{
				return;
		   	}	
		}
		if(diplicat_rfid == 1)
		{
			alert("Same RFID is not Allowed Multiple Times.");
			return;
		}
		if(rfId_flag==1)
		{
			alert("RFID must be 9 digits long");
			return;
		}
		//alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('hidden_company_id*hidden_po_breakdown_id*hidden_rollId*hidden_dtls_id*hidden_mst_id*hidden_barcode*hidden_roll_wgt*hidden_entry_form*hidden_table_id*update_id*txt_system_no*hidden_roll_mst*txt_original_wgt*deleted_all_id*txt_bar_code_num*booking_without_order*txt_order_no*hidden_transfer_mother_roll*hidden_is_sales*hidden_program_no*txt_remarks*hidden_mother_rf_id*txt_original_pcs*hidden_original_pcs',"../../")+dataString;
		//alert(data); return;
		freeze_window(operation);
		
		http.open("POST","requires/roll_splitting_beforeissue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_roll_split_entry_info;
	}

	function fnc_roll_split_entry_info()
	{
		if(http.readyState == 4) 
		{			
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				document.getElementById('hdn_productionDtlsId').value = response[5];

				show_list_view(response[4], 'roll_details_update', 'scanning_tbody','requires/roll_splitting_beforeissue_controller', '' );
				set_button_status(1, permission, 'fnc_roll_split_entry',1);
				
			}
			else if(response[0]==30)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			
			release_freezing();
		}
	}
	
	function add_break_down_tr( i )
	{		
		var row_num=$('#scanning_tbody tr').length;
		var maximum_roll=$("#hidden_roll_name").val();
		
		row_num++;
		var clone= $("#tr_"+i).clone();
		clone.attr({
			id: "tr_" + row_num,
		});
		
		clone.find("input,select").each(function(){
			  
		$(this).attr({ 
		  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
		  'name': function(_, name) { return name },
		  'value': function(_, value) { return value }              
		});
		 
		}).end();
		
		$("#tr_"+i).after(clone);
		
		
		$('#tr_'+ row_num).find("td:eq(0)").removeAttr('id').attr('id','txtSl_'+row_num);
		
		$('#rollno_'+row_num).removeAttr("value").attr("value","");
	
		$('#increase_'+row_num).removeAttr("value").attr("value","+");
		$('#txtSl_'+row_num).text(row_num);	
		$('#increase_'+row_num).removeAttr("value").attr("value","+");
		$('#decrease_'+row_num).removeAttr("value").attr("value","-");
		$('#rollWgt_'+row_num).removeAttr("value").attr("value","");
		$('#rollWgt_'+row_num).removeAttr("disabled");
		
		$('#qtyInPcs_'+row_num).removeAttr("value").attr("value","");
		$('#qtyInPcs_'+row_num).removeAttr("disabled");
		
		$('#barcodeNo_'+row_num).removeAttr("value").attr("value","");
		$('#rfidNo_'+row_num).removeAttr("value").attr("value","");
		$('#updateDtlsId_'+row_num).removeAttr("value").attr("value","");
		$('#updateRollId_'+row_num).removeAttr("value").attr("value","");
		
		$('#rollno_'+row_num).removeAttr("onBlur").attr("onBlur","check_roll_no("+row_num+");");
		$('#rollWgt_'+row_num).removeAttr("onBlur").attr("onBlur","check_qty("+row_num+");");
		$('#qtyInPcs_'+row_num).removeAttr("onBlur").attr("onBlur","check_qty_in_pcs("+row_num+");");
		$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
		$('#rollWgt_'+row_num).removeAttr('readonly');
		//$('#txt_tot_row').val(row_num);
		//set_all_onclick();		
	}
	
	function fn_deleteRow(rowNo) 
	{ 
		if(rowNo!=1)
		{
			var deleted_id=$("#deleted_all_id").val();
			var roll_id=$("#updateRollId_"+rowNo).val();
			var dtls_id=$("#updateDtlsId_"+rowNo).val();
			var roll_wgt=$("#rollWgt_"+rowNo).val();
			
			if(trim(deleted_id)!="") { deleted_id=deleted_id+","+roll_id+"**"+dtls_id+"**"+roll_wgt;}
			else  deleted_id=roll_id+"**"+dtls_id;
			$("#deleted_all_id").val(deleted_id);	
				
			$("#tr_"+rowNo).remove();
		}
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			barcode_scan_dtls(0,bar_code);
			//$('#txt_bar_code_num').val('');
			//set_all_onclick();
		}
	});

	function check_qty(id)
	{		
		var	roll_weight=$("#rollWgt_"+id).val()*1;
		var	original_wgt=$("#txt_original_wgt").val()*1;
		var total_row=$("#scanning_tbody tr").length;
		if(roll_weight>=original_wgt)
		{
			alert("Total weight of all splitted rolls must be less than original roll");
			$("#rollWgt_"+id).val('');
			return;
		}
	    var total_wight=0;
		var j=0;
		for(j=1;j<=total_row;j++)
		{
			total_wight=total_wight+$("#rollWgt_"+j).val()*1;
		}		
		if(total_wight>=original_wgt)
		{
			alert("Total weight of all splitted rolls must be less than original roll");
			$("#rollWgt_"+id).val('');
		}		
	}    
    
    //check_qty_in_pcs
	function check_qty_in_pcs(id)
	{		
		var	original_qtyInPcs=$("#txt_original_pcs").val()*1;
		var	splited_qtyInPcs=$("#qtyInPcs_"+id).val()*1;
		
		var total_row=$("#scanning_tbody tr").length;
		if(splited_qtyInPcs>=original_qtyInPcs)
		{
			alert("Splitted qty. in pcs must be less than original qty. in pcs");
			$("#qtyInPcs_"+id).val('');
			return;
		}
		
	    var totalSplited_qtyInPcs=0;
		var j=0;
		for(j=1;j<=total_row;j++)
		{
			totalSplited_qtyInPcs=totalSplited_qtyInPcs+$("#qtyInPcs_"+j).val()*1;
		}
				
		if(totalSplited_qtyInPcs>=original_qtyInPcs)
		{
			alert("Total splitted qty. in pcs must be less than original qty. in pcs");
			$("#qtyInPcs_"+id).val('');
		}		
	}    

	function check_roll_no(id)
	{
		roll_no=parseInt($("#rollno_"+id).val());
		var maximum_roll=parseInt($("#hidden_roll_name").val());
		total_row=$("#scanning_tbody tr").length;
		
		if(roll_no<=maximum_roll)
		{
			alert("Roll No must be getter than "+maximum_roll);
			$("#rollno_"+id).val('');
		}
	
		var j=0;
		for(j=1;j<=total_row;j++)
		{
			
			if(id!=j)
			{
				if(parseInt($("#rollno_"+j).val())==roll_no)
				{
					alert("Duplicate entry");
					$("#rollno_"+id).val('');
				}
			}			
		}
	}
	
	function coursor_focus()
	{
		$("#txt_bar_code_num").focus();
	}	
	
	function fnc_bundle_report(type)
	{
		// alert(type);
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#updateRollId_'+idd[1] ).val(); else data=data+","+$('#updateRollId_'+idd[1] ).val();
				$('#hiddenid_'+idd[2] ).val();
			}
			
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		
		if (type==4)
		{
			var productionDtlsId=$("#hdn_productionDtlsId").val();
			var txt_bar_code_num=$("#txt_bar_code_num").val();
			data=data+"***"+productionDtlsId+"***"+txt_bar_code_num;
			var url=return_ajax_request_value(data, "direct_print_barcode", "requires/roll_splitting_beforeissue_controller");
			window.open(url,"##");	return;
		}
		if (type==1)
		{
			window.open("requires/roll_splitting_beforeissue_controller.php?data=" + data+'&action=report_barcode_generation', true );
		}
		if (type==2)
		{
			//window.open("requires/roll_splitting_beforeissue_controller.php?data=" + data+'&action=report_barcode_generation_128', true );
			var url=return_ajax_request_value(data, "report_barcode_generation_128", "requires/roll_splitting_beforeissue_controller");
		}

		if (type==3) // Barcode N
		{
			
			//window.open("requires/roll_splitting_beforeissue_controller.php?data=" + data+'&action=print_barcode_one_128_v3', true );
			var url=return_ajax_request_value(data, "print_barcode_one_128_v3", "requires/roll_splitting_beforeissue_controller");
		
		}

		if (type==5) // Barcode 128 v3
		{
			var productionDtlsId=$("#hdn_productionDtlsId").val();
			var txt_bar_code_num=$("#txt_bar_code_num").val();
			data=data+"***"+productionDtlsId+"***"+txt_bar_code_num;
			var url=return_ajax_request_value(data, "print_barcode_one_128_v4", "requires/roll_splitting_beforeissue_controller");
		}

		if (type==6) //Direct Print 6
		{
			var productionDtlsId=$("#hdn_productionDtlsId").val();
			var txt_bar_code_num=$("#txt_bar_code_num").val();
			data=data+"***"+productionDtlsId+"***"+txt_bar_code_num;
			var url=return_ajax_request_value(data, "direct_print_barcode_6", "requires/roll_splitting_beforeissue_controller");
		}

		if (type==7) //Barcode CCL
		{
			var productionDtlsId=$("#hdn_productionDtlsId").val();
			var txt_bar_code_num=$("#txt_bar_code_num").val();
			data=data+"***"+productionDtlsId+"***"+txt_bar_code_num;
			var url=return_ajax_request_value(data, "print_barcode_ccl", "requires/roll_splitting_beforeissue_controller");
		}

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "requires/roll_splitting_beforeissue_controller");
		window.open(url,"##");		
	}
	
	function fnc_send_printer_text()
	{
		var data="";
		var error=1;
		var is_sales=$('#hidden_is_sales').val();
		//alert(is_sales);
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#updateRollId_'+idd[1] ).val(); else data=data+","+$('#updateRollId_'+idd[1] ).val();
				//$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		/*if(is_sales==1){
			var url=return_ajax_request_value(data, "report_barcode_text_file_sales", "requires/roll_splitting_beforeissue_controller");
		}else{
			var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/roll_splitting_beforeissue_controller");
		}*/
		
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/roll_splitting_beforeissue_controller");		
		window.open('requires/'+url.trim()+".zip","_blank");
	}
	
</script>
</head>
<body onLoad="set_hotkey();coursor_focus()">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <br>
                <table cellpadding="0" cellspacing="2" width="800">
                	<tr>
                        <td align="right" colspan="3"><strong>System Number</strong>&nbsp;&nbsp;
                        </td>
                         <td colspan="3">
                            <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_mrr()" placeholder="Browse" readonly/>
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="hidden" name="deleted_all_id" id="deleted_all_id" />
                        </td>
                     </tr>
                    
                     <tr>
                        <td align="right"><strong>Barcode Number</strong>&nbsp;&nbsp;
                        </td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                         <td  align="right">Fabric Description</td>
                         <td colspan="3" align="left">
                         <input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:410px;"  placeholder="Display" disabled/>
                         </td>
                    </tr>
                    <tr>
                    	<td  align="right">Yarn Lot</td>
                        <td  align="left">
                             <input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right">Count</td>
                         <td  align="left">
                             <input type="text" name="txt_count" id="txt_count" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right">Knitting Company</td>
                         <td  align="left">
                             <input type="text" name="txt_knitting_com" id="txt_knitting_com" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                     </tr>
                     <tr>
                    	<td  align="right">Buyer</td>
                        <td  align="left">
                             <input type="text" name="txt_buyer" id="txt_buyer" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right">Job No</td>
                         <td  align="left">
                             <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right" id="po_booking_td">Order/FSO No</td>
                         <td  align="left">
                             <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                     </tr>
                     <tr>
                    	<td  align="right">Company</td>
                        <td  align="left">
                             <input type="text" name="txt_company_name" id="txt_company_name" class="text_boxes" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right">Original Wgt</td>
                         <td  align="left">
                             <input type="text" name="txt_original_wgt" id="txt_original_wgt" class="text_boxes_numeric" style="width:140px;"  placeholder="Display" disabled/>
                         </td>
                         <td  align="right">Original Roll</td>
                         <td  align="left">
      						   <input type="text" name="txt_original_roll" id="txt_original_roll" class="text_boxes_numeric" style="width:140px;"  placeholder="Display" disabled/>
                                <input type="hidden" name="hidden_company_id" id="hidden_company_id"/>
                                <input type="hidden" name="hidden_roll_name" id="hidden_roll_name"/>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id"/>
                                <input type="hidden" name="hidden_dtls_id" id="hidden_dtls_id"/>
                                <input type="hidden" name="hidden_po_breakdown_id" id="hidden_po_breakdown_id"/>
                                <input type="hidden" name="hidden_entry_form" id="hidden_entry_form"/>
                                <input type="hidden" name="hidden_transfer_mother_roll" id="hidden_transfer_mother_roll"/>
                                <input type="hidden" name="hidden_entry_form_last" id="hidden_entry_form_last"/>
                                <input type="hidden" name="hidden_rollId" id="hidden_rollId"/>
                                <input type="hidden" name="hidden_barcode" id="hidden_barcode"/>
                                <input type="hidden" name="hidden_roll_wgt" id="hidden_roll_wgt"/>
                                <input type="hidden" name="hidden_original_pcs" id="hidden_original_pcs"/>
                                <input type="hidden" name="hidden_table_id" id="hidden_table_id"/>
                                <input type="hidden" name="hidden_roll_mst" id="hidden_roll_mst"/>
                                <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                                <input type="hidden" name="hidden_is_sales" id="hidden_is_sales"/>
                                <input type="hidden" name="hidden_program_no" id="hidden_program_no"/>
                                <input type="hidden" name="hidden_mother_rf_id" id="hidden_mother_rf_id"/>
                               
                         </td>
                     </tr>
                     <tr>
                        <td  align="right">Original Pcs</td>
                        <td  align="left">
                        <input type="text" name="txt_original_pcs" id="txt_original_pcs" class="text_boxes_numeric" style="width:140px;"  placeholder="Display" disabled/>
                        </td>

                     </tr>
                     <tr>
                        <td align="right">Remarks</td>
                        <td colspan="5" align="left">
                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:660px;" placeholder="Remarks"/>
                        </td>
                    </tr>
                  
                  
                </table>
                <br>
                <table cellpadding="0" width="760" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">SL</th>
                        <th width="120">New Roll No</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Qty. In Pcs</th>
                        <th width="180">Barcode No</th>
                        <th width="100">RFID</th>
                        <th width="100"></th>
                        <th>Report &nbsp;</th>
                    </thead>
                    <tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="40" id="txtSl_1">1</td>
                                <td width="100" >
                                <input type="text" name="roll_no[]" id="rollno_1" style="width:80px" class="text_boxes_numeric" onBlur="check_roll_no(1)" disabled/>
                                </td>
                                <td width="60" >
                                <input type="text" name="rollWgt[]" id="rollWgt_1" style="width:50px" class="text_boxes_numeric" onBlur="check_qty(1)"/>
                                <input type="hidden" name="hideRollWgt[]" id="hideRollWgt_1" style="width:50px" class="text_boxes_numeric"  value="" />
                                </td>
                                
                                <td width="60">
                                <input type="text" name="qtyInPcs[]" id="qtyInPcs_1" style="width:50px" class="text_boxes_numeric" onBlur="check_qty_in_pcs(1)"/>
                                <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1" style="width:50px" class="text_boxes_numeric"  value="" />
                                </td>
                                
                                <td width="180" >
                                <input type="text" name="barcodeNo[]" id="barcodeNo_1" style="width:150px" class="text_boxes"  placeholder="Display" readonly />
                                </td>
                                <td width="100" >
                                <input type="text" name="rfidNo[]" id="rfidNo_1" style="width:100px" class="text_boxes"  placeholder="Write/Scan" />
                                </td>
                                <td id="button_1" align="center">
                                 <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                                 <input  type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px;display:none" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                 <input type="hidden" name="updateRollId[]" id="updateRollId_1"/>
                                 <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_1"/>
                                 
                                   
                                </td>
                                <td> <input id="chkBundle_1" type="checkbox" name="chkBundle" ></td>
                            </tr>
                           
                        </tbody>
                 </table>
                 <table cellpadding="0" width="760" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                	<tbody>
                    	<tr>
                    		<input id="hdn_productionDtlsId" type="hidden" name="hdn_productionDtlsId" >
                    		<!-- this filed for getting dtlsID for entry from 2 for Direct Print sticker -->


                            <td colspan="6" align="center" class="button_container">
							
                            <? 
                                echo load_submit_buttons($permission, "fnc_roll_split_entry", 0,0,"",1);//set_auto_complete(1);
                            ?>  

                            <!-- <input type="button" value="Barcode 128 v2" id="barcode_generation_128" class="formbutton" onClick="fnc_bundle_report(2)"/>
                            <input type="button" value="Barcode Generation" id="barcode_generation" class="formbutton" onClick="fnc_bundle_report(1)"/> -->
                            </td> 

						 </tr>     
						 <tr>
                            <td colspan="6" align="center" class="button_container" id="button_list">                        
                            </td> 
						 </tr>                 
                   </tbody>
                 </table>
			</fieldset>
			
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
