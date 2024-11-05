<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	26-06-2013
Updated by 		: 	Kausar (Creating Report), Md Didar (order info pop up search pannel=>booking no)	   	
Update date		: 	14-12-2013,06-03-2018 
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
echo load_html_head_contents("Grey Fabric Order To Order Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var tableFilters = {
			col_0: "none", 
			col_operation: {
				id: ["value_total_roll_qnty"],
				col: [16],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/grey_fabric_order_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_fabric_order_to_order_roll_transfer_controller" );
		show_list_view(transfer_id+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/grey_fabric_order_to_order_roll_transfer_controller','');

		setFilterGrid('scanning_tbl',-1,tableFilters);
		disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	//var cbo_company_to_id = $('#cbo_company_to_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}


	if(type=="to")
	{
		if( $("#txt_from_order_no").val() == "") 
		{
			if (form_validation('txt_from_order_no','From Order')==false)
			{
				return;
			}
		}
	}
	var colorType="";
	var txt_from_order_id = $("#txt_from_order_id").val();
	var row_num=$('#scanning_tbl tbody tr').length-1;	
	for (var j=1; j<=row_num; j++)
	{
		if (row_num==j) {
			colorType+=$('#colorType_'+j).val();
		}
		else{
			colorType+=$('#colorType_'+j).val()+',';
		}
	}
	
	//alert(colorType);

	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_order_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup&txt_from_order_id='+txt_from_order_id+'&colorType='+colorType;

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=420px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var orderId_colorType=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		var data=orderId_colorType.split('**');
		var order_id=data[0];
		var color_type=data[1];
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/grey_fabric_order_to_order_roll_transfer_controller" );
		if(type=='from')
		{
			show_list_view(order_id,'show_dtls_list_view','tbl_details','requires/grey_fabric_order_to_order_roll_transfer_controller','');
			setFilterGrid('scanning_tbl',-1,tableFilters);
		}
		if(type=='to')
		{
			// alert(color_type);
			$("#txt_to_color_type").val(color_type);
		}
		
	}
}


function fnc_grey_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print", "requires/grey_fabric_order_to_order_roll_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no','Company*Transfer Date*From Order No*To Order No')==false )
		{
			return;
		}
                
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
		var to_color_type = $('#txt_to_color_type').val();
		var row_num=$('#scanning_tbl tbody tr').length-1;
		var txt_deleted_id=''; var selected_row=0; var i=0; var data_all=''; var txt_deleted_barcode_no = '';
		
		for (var j=1; j<=row_num; j++)
		{
			var updateIdDtls=$('#dtlsId_'+j).val(); 


			if(updateIdDtls!="" && $('#tbl_'+j).is(':not(:checked)'))
			{
				var transIdFrom=$('#transIdFrom_'+j).val();
				var transIdTo=$('#transIdTo_'+j).val();
				var rolltableId=$('#rolltableId_'+j).val();
				var rollId=$('#rollId_'+j).val();
				var delBarcodeNo=$('#barcodeNo_'+j).val();
				
				selected_row++;
				if(txt_deleted_id=="") txt_deleted_id=updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;  
				else txt_deleted_id+=","+updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;  

				if(txt_deleted_barcode_no=="") txt_deleted_barcode_no = $('#barcodeNo_'+j).val();
				else txt_deleted_barcode_no += "," + $('#barcodeNo_'+j).val();
			}
			
			if($('#tbl_'+j).is(':checked'))
			{
				var from_color_Type=$('#colorType_'+j).val();			
				if (from_color_Type!=to_color_type) 
				{
					alert('Fabric Color Type No Match With To Order');return;
				}

				i++;
				data_all+="&barcodeNo_" + i + "='" + $('#barcodeNo_'+j).val()+"'"+"&rollNo_" + i + "='" + $('#rollNo_'+j).val()+"'"+"&progId_" + i + "='" + $('#progId_'+j).val()+"'"+"&productId_" + i + "='" + $('#productId_'+j).val()+"'"+"&rollId_" + i + "='" + $('#rollId_'+j).val()+"'"+"&rollWgt_" + i + "='" + $('#rollWgt_'+j).val()+"'"+"&yarnLot_" + i + "='" + $('#yarnLot_'+j).val()+"'"+"&yarnCount_" + i + "='" + $('#yarnCount_'+j).val()+"'"+"&stichLn_" + i + "='" + $('#stichLn_'+j).val()+"'"+"&brandId_" + i + "='" + $('#brandId_'+j).val()+"'"+"&rack_" + i + "='" + $('#rack_'+j).val()+"'"+"&shelf_" + i + "='" + $('#shelf_'+j).val()+"'"+"&dtlsId_" + i + "='" + $('#dtlsId_'+j).val()+"'"+"&transIdFrom_" + i + "='" + $('#transIdFrom_'+j).val()+"'"+"&transIdTo_" + i + "='" + $('#transIdTo_'+j).val()+"'"+"&rolltableId_" + i + "='" + $('#rolltableId_'+j).val()+"'"+"&transRollId_" + i + "='" + $('#transRollId_'+j).val()+"'" + "&colorName_" + i + "='" + $('#colorName_'+j).val() + "'" + "&storeId_" + i + "='" + $('#storeId_'+j).val() + "'" + "&rollAmount_" + i + "='" + $('#rollAmount_'+j).val() + "'";
						
				selected_row++;
			}
		} 
		//alert(selected_row);
		if(selected_row<1 || selected_row==0)
		{
			alert("Please Select Barcode No.");
			return;
		}
		
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*txt_from_order_no*txt_to_order_no*update_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+'&txt_deleted_barcode_no='+txt_deleted_barcode_no+data_all;
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_order_to_order_roll_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_grey_transfer_entry_reponse;
	}
}

function fnc_grey_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);release_freezing();return;
        if (reponse[0] * 1 == 20 * 1) 
        {
            alert(reponse[1]);
            release_freezing();
            return;
        }
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			
			//show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_order_to_order_roll_transfer_controller','');
			show_list_view(reponse[1]+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/grey_fabric_order_to_order_roll_transfer_controller','');
			setFilterGrid('scanning_tbl',-1,tableFilters);
			set_button_status(1, permission, 'fnc_grey_transfer_entry',1,1);
			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
		}	
		release_freezing();
	}
}

function openmypage_orderInfo(type)
{
	var txt_order_no = $('#txt_'+type+'_order_no').val();
	var txt_order_id = $('#txt_'+type+'_order_id').val();

	if (form_validation('txt_'+type+'_order_no','Order No')==false)
	{
		alert("Please Select Order No.");
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_order_to_order_roll_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="check[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="check[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		}
		
		var row_num=$('#scanning_tbl tbody tr').length;
		var selected_roll_wgt=0;
		for (var j=1; j<=row_num; j++)
		{	

			if($('#tbl_'+j).is(':checked'))
			{
				selected_roll_wgt += $('#rollWgt_'+j).val()*1;
			}
		}
		$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));
	}
	
	function reset_form_all()
	{
		disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
		reset_form('transferEntry_1','tbl_details','','','');
	}

	/*function fnc_company_onchang(id){
		$("#cbo_company_to_id").val(id);
	}*/
	function fnc_company_onchang_reset(){
		//alert();
		$('#txt_from_order_no').val('');
		$('#txt_from_order_id').val('');
		$('#txt_from_po_qnty').val('');
		$('#cbo_from_buyer_name').val('');
		$('#txt_from_style_ref').val('');
		$('#txt_from_job_no').val('');
		$('#txt_from_booking_no').val('');
		$('#txt_from_internal_ref_no').val('');
		$('#txt_from_gmts_item').val('');
		$('#txt_from_shipment_date').val('');

		$('#txt_to_order_no').val('');
		$('#txt_to_order_id').val('');
		$('#txt_to_po_qnty').val('');
		$('#cbo_to_buyer_name').val('');
		$('#txt_to_style_ref').val('');
		$('#txt_to_job_no').val('');
		$('#txt_to_booking_no').val('');
		$('#txt_to_internal_ref_no').val('');
		$('#txt_to_gmts_item').val('');
		$('#txt_to_shipment_date').val('');
		
		$("#tbl_details").html("");
	}


	function show_selected_total(str)
	{

		var roll_wgt=0; var pre_wgt = 0;
		roll_wgt =$('#rollWgt_'+str).val()*1;
		pre_wgt = $("#selected_roll_wgt_show").text()*1;
		if($('#tbl_'+str).is(":checked"))
		{
			$("#selected_roll_wgt_show").text(pre_wgt+roll_wgt);
		}
		else
		{
			$("#selected_roll_wgt_show").text(pre_wgt-roll_wgt);
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        <legend>Roll Wise Grey Fabric Order To Order Transfer Entry</legend>
        <br>
            <fieldset style="width:750px;">
                <table width="740" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "fnc_company_onchang_reset();"); //fnc_company_onchang(this.value);
                            ?>
                        </td>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td> 
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>From Order</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                    <td width="30%" class="must_entry_caption">Order No</td>
                                    <td>
                                        <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
                                        <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Order Qnty</td>
                                    <td>
                                        <input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Fabric Booking No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_booking_no" id="txt_from_booking_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Internal Ref. No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_internal_ref_no" id="txt_from_internal_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo('from');">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="2%" valign="top"></td>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>To Order</legend>					
                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <!-- <td class="must_entry_caption">To Company</td>
		                        <td>
		                            <? 
		                                //echo create_drop_down( "cbo_company_to_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
		                            ?>
		                        </td> -->
                                <tr>
                                    <td width="30%" class="must_entry_caption">Order No</td>
                                    <td>
                                        <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                        <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
                                        <input type="hidden" name="txt_to_color_type" id="txt_to_color_type" readonly>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Order Qnty</td>
                                    <td>
                                        <input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Fabric Booking No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_booking_no" id="txt_to_booking_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Internal Ref. No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_internal_ref_no" id="txt_to_internal_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo('to');">
                                    </td>
                                </tr>											
                            </table>                  
                       </fieldset>	
                    </td>
                </tr>
			</table>	
            <fieldset style="width:1300px;text-align:left">
				<table cellpadding="0" width="1280" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    	<th width="40">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="70">Program No</th>
                        <th width="60">Product Id</th>
                        <th width="180">Fabric Description</th>
                        <th width="80">Y/Count</th>
                        <th width="70">Y/Brand</th>
                        <th width="80">Y/Lot</th>
                        <th width="80">Color</th>
                        <th width="80">Color Type</th>
                        <th width="100">Body Part</th>
                        <th width="55">Rack</th>
                        <th width="55">Shelf</th>
                        <th width="80">Stitch Length</th>
                        <th width="80">Roll Wgt.</th>
                    </thead>
                 </table>
                 <div style="width:1300px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1280" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_details">
                        </tbody>
                	</table>
                </div>
                <table cellpadding="0" cellspacing="0" width="1280" border="1" rules="all" class="rpt_table">
                	<tfoot>
                		<tr>
                        	<th width="40"></th>
	                    	<th width="40"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="60"></th>
	                        <th width="180"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="55"></th>
	                        <th width="55"></th>
	                        <th width="80">Total</th>
	                        <th width="80" id="value_total_roll_qnty"></th>
                    	</tr>
                    	<tr>
                        	<th width="40"></th>
	                    	<th width="40"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="60"></th>
	                        <th width="180"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="55"></th>
	                        <th width="55"></th>
	                        <th width="80">Selected Total</th>
	                        <th width="80" id="selected_roll_wgt_show">&nbsp;</th>
                    	</tr>

                    </tfoot>
                </table>
                <br>
                <table width="1280" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	 echo load_submit_buttons($permission, "fnc_grey_transfer_entry", 0,1,"reset_form_all()",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </fieldset>
	</form>
</div>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
