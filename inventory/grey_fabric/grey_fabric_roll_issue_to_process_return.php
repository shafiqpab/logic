<?
/*-------------------------------------------- Comments
Purpose			: This form will create Knit Grey Fabric Issue Entry
Functionality	:	
JS Functions	:
Created by		: Ashraful 
Creation date 	: 219-02-2015
Updated by 		: Zaman		
Update date		: 11.12.2019
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 <?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][62] );
echo "var field_level_data= ". $data_arr . ";\n";
?>

	function generate_report_file(data,action)
	{
		window.open("requires/grey_fabric_roll_issue_to_process_return_controller.php?data=" + data+'&action='+action, true );
	}
 
	function fnc_grey_receive_roll_wise( operation )
	{
		if(operation==2)
		{
			alert("Delete not possible");
			return;
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_delivery_print');
			return;
		}
		
		if(form_validation('txt_return_date*txt_issue_challan_no','Return Date*Issue Challan No')==false)
		{
			return; 
		}
		
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
				var activeId=1; 
			}
			else
			{
				var activeId=0; 	
			}
			
			if($(this).find('input[name="checkRow[]"]').is(':disabled'))
			{
				var disabled=1; 
			}
			else
			{
				var disabled=0; 	
			}
			
			
			var updateDetailsId=$(this).find('input[name="updateDetaisId[]"]').val();
			var updateRollId=$(this).find('input[name="updateRollId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var rollwgt=$(this).find('input[name="rolWgt[]"]').val();
			var rolldia=$(this).find('input[name="rollDia[]"]').val();
			var rollGsm=$(this).find('input[name="rollGsm[]"]').val();
			//var fabricId=$(this).find('input[name="fabricId[]"]').val();
			//var receiveBasis=$(this).find('input[name="receiveBasis[]"]').val();
			var knittingSource=$(this).find('input[name="knittingSource[]"]').val();
			var knittingComp=$(this).find('input[name="knittingComp[]"]').val();
			var job_no=$(this).find('input[name="jobNo[]"]').val();
			var bookingNo=$(this).find('input[name="bookingNo[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodNumber[]"]').val();
			var rollNo=$(this).find('input[name="rollNo[]"]').val();
			var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
			var isSales=$(this).find('input[name="isSales[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			var issueRollId=$(this).find('input[name="issueRollId[]"]').val();
			j++;
			
			dataString+='&rollId_' + j + '=' + rollId + '&buyerId_' + j + '=' + buyerId + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '=' + colorId  + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&knittingSource_' + j + '=' + knittingSource + '&knittingComp_' + j + '=' + knittingComp+ '&deterId_' + j + '=' + deterId + '&job_no_' + j + '=' + job_no+ '&rollwgt_' + j + '=' + rollwgt + '&rolldia_' + j + '=' + rolldia + '&bookingNo_' + j + '=' + bookingNo+ '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' + activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+ '&updateRollId_' + j + '=' + updateRollId+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder+ '&disabledStatus_' + j + '=' + disabled+ '&isSales_' +j + '=' + isSales+ '&hiddenQtyInPcs_' +j + '=' + hiddenQtyInPcs+ '&issueRollId_' +j + '=' + issueRollId;

			//+ '&fabricId_' + j + '=' + fabricId + '&receiveBasis_' + j + '=' + receiveBasis
		});
		if(j<1)
		{
			alert('No data found');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_return_date*txt_wo_no*txt_issue_id*txt_issue_challan_no*cbo_company_id*cbo_knitting_source*cbo_knitting_company*update_id*txt_system_no*cbo_process*txt_return_challan*txt_remarks',"../../")+dataString;
		freeze_window(5);
		http.open("POST","requires/grey_fabric_roll_issue_to_process_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				add_dtls_data( response[3]);
				set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');

			    var grey_challan_no = $("#txt_issue_challan_no").val();
			    
			    show_list_view(response[1]+"_"+grey_challan_no, 'grey_item_details_update', 'scanning_tbl','requires/grey_fabric_roll_issue_to_process_return_controller', '' );
			}
			else if(response[0]==2)
			{
				set_button_status(0, permission, 'fnc_grey_receive_roll_wise',1);
				fnc_reset_form();	
				
			}
			else if(response[0]==7)
			{
				batch_data=	response[3].split(",");
				var undeleted_barcode='Delete Restricted. Because Following \n Barcode Are Inserted in Batch Creation Page:\n';
				for(var i=0; i<batch_data.length; i++)
				{
					batch_data_row=batch_data[i].split("_");
					undeleted_barcode+=" Barcode no: "+batch_data_row[0]+" Batch No: "+batch_data_row[1]+"\n";
				}
				
				alert(undeleted_barcode);
			}
			else if(response[0]==20)
			{
				alert(response[1]);

			}
			release_freezing();
		}
	}
	
	function add_dtls_data( data )
	{
		var barcode_dtlsId_array=new Array(); var barcode_rollTableId_array=new Array();
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
			var barcodeNo=$(this).find('input[name="barcodNumber[]"]').val();
			var dtlsId=$(this).find('input[name="updateDetaisId[]"]').val()*1;
			
			if(dtlsId=="" || dtlsId==0) 
			{
				$(this).find('input[name="updateDetaisId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="updateRollId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}
	
	function grey_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		var update_id = $('#update_id').val();
		if(update_id != "")
		{
			alert("Multiple challan not allowed in same system number.");
			$("#txt_challan_no").val("");
			return;
		}

		var garments_nature =2;

		var page_link='requires/grey_fabric_roll_issue_to_process_return_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=challan_popup';
		var title='Issue to process WO Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_no=this.contentDoc.getElementById("hidden_challan_no").value;
			var grey_recv_id=this.contentDoc.getElementById("hidden_challan_id").value;
			
			$("#txt_challan_no").val(grey_recv_no);
			if(trim(grey_recv_id)!="")
			{
				show_list_view(grey_recv_no, 'grey_item_details', 'scanning_tbl','requires/grey_fabric_roll_issue_to_process_return_controller', '' );
				get_php_form_data(grey_recv_no, "load_php_form", "requires/grey_fabric_roll_issue_to_process_return_controller" );
			}
			set_field_level_access( $('#cbo_company_id').val() );
			var delDate='<? echo date('d-m-Y'); ?>';
			$('#txt_delivery_date').val(delDate);
		}
	}

	function openmypage_challan()
	{
		var company=1;
		var page_link='requires/grey_fabric_roll_issue_to_process_return_controller.php?action=mrr_popup&company='+company; 
		var title="Search Challan Popup";
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			$("#txt_system_no").val(sysNumber);		
			get_php_form_data(sysNumber, "populate_data_from_data", "requires/grey_fabric_issue_controller");	 
			show_list_view($("#hidden_system_id").val(),'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
			$("#child_tbl").find('input,select').val('');
			$("#display").find('input,select').val('');
			
			var issuePurpose=$("#cbo_issue_purpose").val();
			if(issuePurpose!=8)
			{
				$("#color_td").html('<? echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
			}
		}
	}

	$('#txt_issue_challan_no_show').live('keydown', function(e) {
		   
		if (e.keyCode === 13) {
			e.preventDefault();
		    scan_challan_no(this.value); 
		}
	});	

	function scan_challan_no(str)
	{
		var update_id = $('#update_id').val();

		if(update_id != "")
		{
			alert("Multiple challan not allowed in same system number.");
			$("#txt_challan_no").val("");
			return;
		}

		var response=return_global_ajax_value(str, 'check_challan_no', '', 'requires/grey_fabric_roll_issue_to_process_return_controller');
		if(response==2)
		{
			alert("Invalid Challan No.");	
			$("#txt_challan_no").val('');
		}
		else if(response==0)
		{
			alert("All Barcode In This Challan Are Saved");	
			$("#txt_challan_no").val('');
		}
		else
		{
			show_list_view(str, 'grey_item_details', 'scanning_tbl','requires/grey_fabric_roll_issue_to_process_return_controller', '' );
			get_php_form_data(str, "load_php_form", "requires/grey_fabric_roll_issue_to_process_return_controller" );
		}

		
	}
	function totalCalculation(rid)
	{
		var total_checked_value=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
				var activeId=1; 
				total_checked_value+=$(this).find('input[name="rolWgt[]"]').val()*1;
			}
		
		});
		$("#total_calculate_qty_id").html(number_format(total_checked_value,2));
	}
	function open_mrrpopup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var garments_nature =2;
	
		var page_link='requires/grey_fabric_roll_issue_to_process_return_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=update_system_popup';
		var title='Grey Receive Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
			var theform=this.contentDoc.forms[0];
			
			var grey_recv_no=this.contentDoc.getElementById("hidden_receive_no").value;
			var grey_recv_id=this.contentDoc.getElementById("hidden_update_id").value;
			var grey_challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
			var hidden_rec_date=this.contentDoc.getElementById("hidden_rec_date").value;
			$("#txt_system_no").val(grey_recv_no);
			$("#txt_challan_no").val('');
			$("#txt_issue_challan_no").val(grey_challan_no);
			if(trim(grey_recv_id)!="")
			{
				show_list_view(grey_recv_id+"_"+grey_challan_no, 'grey_item_details_update', 'scanning_tbl','requires/grey_fabric_roll_issue_to_process_return_controller', '' );
				get_php_form_data(grey_recv_id, "load_php_form_update", "requires/grey_fabric_roll_issue_to_process_return_controller" );
				set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			}
			set_field_level_access( $('#cbo_company_id').val() );
			//var delDate='<? //echo date('d-m-Y'); ?>';
			$('#txt_delivery_date').val(hidden_rec_date);
		}
	}
	
    function change_cursor()
	{
		$("#txt_challan_no").focus();
	}
	
	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="50" id="sl_1"></td><td width="40" id="rollId_1"></td><td width="70" id="barcode_1"></td><td width="80" id="systemId_1"></td><td width="120" id="progBookId_1"></td> <td width="50" id="basis_1"></td><td width="50" id="knitSource_1"></td><td width="100" id="prodDate_1"></td><td width="60" id="prodId_1"></td><td width="60" id="rollWgt_1" name="rollWgt[]" align="center"></td><td width="100" id="job_1"></td><td width="50" id="year_1" align="center"></td><td width="65" id="buyer_1"></td><td width="80" id="order_1" style="word-break:break-all;" align="left"></td><td width="70" id="file_1" style="word-break:break-all;" align="left"></td><td width="90" id="cons_1" style="word-break:break-all;" align="left"></td><td width="60" id="mc_1" style="word-break:break-all;" align="left"></td><td width="90" id="comps_1" style="word-break:break-all;" align="left"></td><td id="gsm_1"></td><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="productionId[]" id="productionId_1"/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="updateRollId[]" id="updateRollId_1" value="0" /><input type="hidden" name="issueRollId[]" id="issueRollId_1" value="0" /></tr>';
		
		$('#cbo_company_id').val(0);
		$('#txt_system_no').val('');
		$('#cbo_knitting_source').val(0);
		$('#cbo_knitting_company').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_challan_no').val('');
		$('#txt_delivery_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_issue_purpose').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		$('#cbo_basis').val(0);
		$("#scanning_tbl tbody").html(html);	
	}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="checkRow[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="checkRow[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		}
	}
</script>
</head>

<body onLoad="set_hotkey(); change_cursor();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:1150px;">
				<legend>Issue Challan Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="1150">
                    <tr>
     
                        <td align="right" colspan="4" ><b>Issue Return No</b></td>
                        <td colspan="6" align="left">
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="open_mrrpopup()" placeholder="Browse For System No" readonly />
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
					<tr><td colspan="10">&nbsp;</td></tr>
                    <tr>
						<td align="left" class="must_entry_caption" style="width:90px;">Issue Challan No</td>             
                       	<td align="left">
							<input type="text" name="txt_issue_challan_no_show" id="txt_issue_challan_no_show" class="text_boxes" style="width:140px" onDblClick="grey_receive_popup()" placeholder="Scan/Browse/Write" />
							<input type="hidden" name="txt_issue_challan_no" id="txt_issue_challan_no" class="text_boxes" style="width:140px" />
							<input type="hidden" name="txt_issue_id" id="txt_issue_id" class="text_boxes" style="width:140px;" />
						</td>

                        <td align="right" style="width:70px;">WO No</td>
                        <td  align="left">
                        	<input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;"  placeholder="Display"   disabled/></td>                      	
                        <td align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Display--", 0, "",1 );//$company_cond 
                            ?>
                        </td>
                        <td align="right">Service Source </td>
                        <td>
							<? 
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); 
							?>
                        </td>
                    	<td align="right">Service Company</td>
                        <td id="knitting_com">
                        	<?
							 echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
							 
							 ?>
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>
                        
                    </tr>
                    <tr>
						<td align="right" >Process</td>
                        <td id="process_td">
							<? 
								echo create_drop_down( "cbo_process", 152, $conversion_cost_head_array,"", 1, "-- Select Process --", 11, "","1","" ); 
							?>
                        </td>
                      	<td align="right" class="must_entry_caption" >Return Date</td>
                        <td ><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:122px;" value="<?php echo date('d-m-Y');?>" /></td>
						
						<td align="right" >Return Challan</td>
                        <td>
                            <input type="text" name="txt_return_challan" id="txt_return_challan" class="text_boxes" style="width:140px;" />
                        </td>
						<td align="right" >Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;" />
                        </td>
                    </tr>

                </table>
			</fieldset> 
            <br/>
              <fieldset style="width:1525px;text-align:left">
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
				<table cellpadding="0" width="1550" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="50"><input type="checkbox" id="all_check" name="all_check" onClick="check_all('all_check')"> SL</th>
                        <th width="40">Roll No</th>
                        <th width="70">Bar Code</th>
                        <th width="80">Body Part</th>
                        <th width="120">Const./ Composition</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="100">Color</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Qty. In Pcs</th>
                        <th width="100">Job No</th>
                        <th width="65">Buyer</th>
                        <th width="80">Order</th>
                        <th width="90">Program/ Booking /Pi No</th>
                    </thead>
                 </table>
                 <div style="width:1570px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1550" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="50" id="sl_1"></td>
                                <td width="40" id="rollId_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="80" id="systemId_1"></td>
                                <td width="120" id="progBookId_1"></td>
                                <td width="50" id="basis_1"></td>
                                <td width="50" id="knitSource_1"></td>
                                <td width="100" id="color_1"></td>
                                <td width="60" id="rollWgt_1" name="rollWgt[]" align="center"></td>
                                <td width="60" id="qtyInPcs_1" name="qtyInPcs[]" align="center"></td>
                                <td width="100" id="job_1"></td>
                                <td width="65" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="90" id="comps_1" style="word-break:break-all;" align="left"></td>
                                <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                <input type="hidden" name="productionId[]" id="productionId_1"/>
                                <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                                <input type="hidden" name="deterId[]" id="deterId_1"/>
                                <input type="hidden" name="productId[]" id="productId_1"/>
                                <input type="hidden" name="orderId[]" id="orderId_1"/>
                                <input type="hidden" name="rollId[]" id="rollId_1"/>
                                <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                <input type="hidden" name="colorId[]" id="colorId_1"/>
                                <input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="0" /> 
                            </tr>
                        </tbody>
                	</table>
                    <table width="1550" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                        <tr>
                            <td align="center" class="button_container">
                                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                                <? 
                                   echo load_submit_buttons($permission,"fnc_grey_receive_roll_wise",0,1,"",1);
                                ?>
                                <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
                            </td>
                        </tr>  
                    </table>
                </div>
              </fieldset>  
                  <!-- ========================== Child table end ============================ -->   
    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>
		</form>
	</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
