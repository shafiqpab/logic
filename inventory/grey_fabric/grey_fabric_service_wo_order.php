<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Grey Fabric Service Wo Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	028-04-2019
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 	


function order_browse_active_inactive()
{
	var issueBasis=$("#cbo_basis").val();
	if(issueBasis==2)
	{
		$("#txt_order_no").attr("placeholder","Double Click"); 
		$('#txt_order_no').attr('onDblClick','openmypage_order();');	
	}
	else
	{
		$("#txt_order_no").attr("placeholder","Display"); 
		$("#txt_order_no").removeAttr("onDblClick");
	}
}

//function for field enable disable
function enable_disable()
{
	var issuePurpose	=$("#cbo_issue_purpose").val();
	var issueBasis		=$("#cbo_basis").val();
	var isBatch			=$("#hidden_is_batch_maintain").val();
	
	$("#txt_booking_no").val(""); 
	$("#txt_booking_id").val("");
	$("#txt_program_no").val("");
	//fabric booking
	if(issueBasis==2)	
	{
		$("#txt_booking_no").attr("disabled",true);	
		$("#txt_program_no").attr("disabled",true);	
		$("#txt_order_no").attr("placeholder","Double Click"); 
		$('#txt_order_no').attr('onDblClick','openmypage_order();');	
		$("#txtIssueQnty").attr("placeholder","Double Click"); 
		$("#txtIssueQnty").attr("readonly","readonly");	
	}
	else
	{	
		$("#txt_booking_no").removeAttr("disabled");
		$("#txt_program_no").attr("disabled",true);	
		$("#txt_order_no").attr("placeholder","Display"); 
		$("#txt_order_no").removeAttr("onDblClick");
		
		if(issuePurpose==11 || issuePurpose==4)
		{
			$("#txtIssueQnty").attr("placeholder","Double Click"); 
			$("#txtIssueQnty").attr("readonly","readonly");	
		}
		else
		{
			$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Wirte"); 
			$("#txtIssueQnty").removeAttr("readonly");	
		}
	}

	//function call for item list enable disable
	//new_item_controll();
}




function generate_report_file(data,action,page)
{
	window.open("requires/grey_fabric_service_wo_order_controller.php?data=" + data+'&action='+action, true );
}

function fnc_grey_fabric_service_entry(operation)
{
	
	if(operation==4)
	{
		var show_item="";
			var r=confirm("Press  \"OK\"  to Without Rate\nPress  \"Cancel\"  to open Rate");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
		
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+show_item, "grey_fab_service_wo_order_print", "requires/grey_fabric_service_wo_order_controller" ) ;
			//return;
			show_msg("3");
	}
			
	if(operation==2)
	{
		show_msg('13');
		return;
	}
	else if(operation==0 || operation==1)
	{
		
		if( form_validation('cbo_company_id*cbo_service_type*txt_booking_date*cbo_dyeing_company','Company Name*Service Type*Booking Date*Factory')==false )
		{
			return;
		}
		var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_fso_item tbody tr').length;
		var data_all="";
		
		var j=0; var breakOut = true; var error=0; 
		$("#tbl_fso_item tbody").find('tr').each(function()
		{

			if(breakOut==false || error==1)
			{
				return;
			}
			var trId = $(this).attr('id').split('_');
			var i=trId[1];
				var wo_qty=$('#txtwoqty_'+i).val();
			//	alert(wo_qty);
			/*if (form_validation('cboItemDesc_'+i+'*txtBatchQnty_'+i+'*cboDiaWidthType_'+i,'Item Description*Batch Qnty*Dia/ W. Type')==false)
			{
				breakOut = false;
				return false;//cboDiaWidthType_1
			}*/
			if(wo_qty!="")
			{
			
			if (form_validation('txtsalesno_'+i+'*txtcolor_'+i+'*txtcolor_'+i+'*txtwoqty_'+i,'Sales No*Color*Wo Qnty')==false)
			{
				breakOut = false;
				return false;//cboDiaWidthType_1
			}
			//var fso_field='';
			var fso_field='txtsalesno_';

			j++;
			data_all+="&"+fso_field + j + "='" + $('#'+fso_field+i).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+i).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+i).val()+"'"+"&cbobuyer_" + j + "='" + $('#cbobuyer_'+i).val()+"'"+"&txtfabricbookingno_" + j + "='" + $('#txtfabricbookingno_'+i).val()+"'"+"&txtFabricDesc_" + j + "='" + $('#txtFabricDesc_'+i).val()+"'"+"&fabricDescId_" + j + "='" + $('#fabricDescId_'+i).val()+"'"+"&cboDiaWidthType_" + j + "='" + $('#cboDiaWidthType_'+i).val()+"'"+"&cbouom_" + j + "='" + $('#cbouom_'+i).val()+"'"+"&colorId_" + j + "='" + $('#colorId_'+i).val()+"'"+"&cbocolorrange_" + j + "='" + $('#cbocolorrange_'+i).val()+"'"+"&txtwoqty_" + j + "='" + $('#txtwoqty_'+i).val()+"'"+"&txtrate_" + j + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + j + "='" + $('#txtamount_'+i).val()+"'"+"&txtnoofroll_" + j + "='" + $('#txtnoofroll_'+i).val()+"'"+"&txtremark_" + j + "='" + $('#txtremark_'+i).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+i).val()+"'";
			//data_all+="&"+fso_field + j + "='" + $('#'+fso_field+i).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+i).val()+"'";
			//alert (data_all);
			}

		});
		
		var dataString = "txt_system_no*hidden_system_id*cbo_company_id*txt_challan_no*cbo_service_type*cbo_pay_mode*txt_booking_date*txt_attention*cbo_currency*txt_exchange_rate*cbo_dyeing_source*cbo_dyeing_company*txt_delivery_date*txt_vehical_no*txt_driver*txt_dl_no*txt_transport_no*txt_mobile_no*txt_remarks";
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;
		
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_service_wo_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_grey_fabric_service_entry_reponse;
	}
}

function fnc_grey_fabric_service_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');	
 		
		show_msg(reponse[0]);
		if(reponse[0]==0 || reponse[0]==1) //insert
		{
 			//show_msg(reponse[0]);
			$("#txt_system_no").val(reponse[1]); 
			$('#hidden_system_id').val(reponse[2]);	

			show_list_view(reponse[2],'show_dtls_list_view','fso_details_container','requires/grey_fabric_service_wo_order_controller','');
			set_button_status(1, permission, 'fnc_grey_fabric_service_entry',1,1);
			
			//after save reset child form
						
		}	
			 	
		release_freezing();
	}
}

function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
	var page_link='requires/grey_fabric_service_wo_order_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var sysNumber=this.contentDoc.getElementById("hidden_sys_number").value; // system number
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_account").value; // posted in accounce
 		var hidden_id=this.contentDoc.getElementById("hidden_id").value; // posted in accounce
		
		$("#txt_system_no").val(sysNumber);		
		// master part call here
		
		
		get_php_form_data(hidden_id, "populate_data_from_data", "requires/grey_fabric_service_wo_order_controller");	 
		//list view call here
		show_list_view($("#hidden_system_id").val(),'show_dtls_list_view','fso_details_container','requires/grey_fabric_service_wo_order_controller','');
 		//$("#child_tbl").find('input,select').val('');
		//$("#display").find('input,select').val('');
		
		
		set_button_status(1, permission, 'fnc_grey_fabric_service_entry',1,1);
		//enable_disable();
  	}
}

//form reset/refresh function here
function fnResetForm()
{ 
	//disable_enable_fields( 'cbo_company_id*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
 	set_button_status(0, permission, 'fnc_grey_fabric_service_entry',1,0);
	reset_form('grey_issue_1','fso_details_container','','','','');
	disable_enable_fields('cbo_company_id',0);
	//enable_disable();
}

$(document).ready(function(e) {
    $("#cbo_issue_purpose").val(11); //default set issue purpose fabric dyeing 
	enable_disable();
});

 function openmypage_wo(row_num_id) {
 	var cbo_company_id = $('#cbo_company_id').val();
 

 	if (form_validation('cbo_company_id', 'Company') == false) {
 		return;
 	}
 	else {
		var prev_wo_ids=''; var prev_wo_feb_datas='';
		var row_num=$('#tbl_fso_item tbody tr').length;
		//alert(row_num);
		for (var j=1; j<=row_num; j++)
		{
			var hideWoDtlsId=$('#hideWoDtlsId_'+j).val();
			if(hideWoDtlsId!="")
			{
				if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;
			}
			
				var hideWoId=$('#hideWoId_'+j).val();
				var workOrderNo=$('#workOrderNo_'+j).val();
				
		}	
		
		
 		var title = 'Job Selection Form';
 		var page_link = 'requires/grey_fabric_service_wo_order_controller.php?cbo_company_id=' + cbo_company_id+'&prev_wo_ids='+prev_wo_ids+ '&action=sales_no_popup';

 		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0', '../');

 		emailwindow.onclose = function () {
 			var theform = this.contentDoc.forms[0];
 			var theemail=this.contentDoc.getElementById("txt_selected_wo_id"); //Access form field with id="emailfield"
			var theemail_mst=this.contentDoc.getElementById("txt_selected_wo_mst_id");

 			if(theemail.value!="")
			{


           		freeze_window(5);
				var numRow = $('table#tbl_fso_item tbody tr').length; 
				var wo_no=$('#txtsalesno_'+row_num_id).val(); 
				
				if(wo_no=="")
				{
					numRow--;
				}
				var data=theemail.value+"**"+row_num_id+"**"+theemail_mst.value;
				//229,253,320**0**28,30,35**1
				//alert (data);
				//alert(numRow);
				var list_view_wo =return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/grey_fabric_service_wo_order_controller');
				if(list_view_wo!="")
				{
					//alert(row_num);
					$("#slTd_"+row_num_id).remove();
				}
				
				$("#tbl_fso_item tbody").append(list_view_wo);	
				//$("#tbl_fso_item tbody:last").append(list_view_wo);	
				//calculate_total_amount(1);
				set_all_onclick();
				release_freezing();
			}
        }
    }
}


	function add_break_down_tr( i )
		{
			
			if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}

			var cbo_company_id= $('#cbo_company_id').val();
			

		
			//var row_num=$('#tbl_item_details tbody tr').length;
			var lastTrId = $('#tbl_fso_item tbody tr:last').attr('id').split('_');
			var row_num=lastTrId[1];
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_fso_item tbody tr:last").clone().find("input,select").each(function(){

					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name },
						'value': function(_, value) { return value }
					});

				}).end().appendTo("#tbl_fso_item");

				$("#tbl_fso_item tbody tr:last").removeAttr('id').attr('id','tr_'+i);
				$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
				$('#tr_' + i).find("td:eq(0)").text(i);
				
    			//$('#txtFinishQty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");calculate_grey_qty(" + i + ");");
				//$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','txt_sales_no_'+i);
				/*$('#tr_' + i).find("td:eq(1)").removeAttr('id').attr('id','cbo_buyer_'+i);
				$('#tr_' + i).find("td:eq(2)").removeAttr('id').attr('id','txt_fabric_booking_no_'+i);
				$('#tr_' + i).find("td:eq(3)").removeAttr('id').attr('id','cboItemDesc_'+i);
				$('#tr_' + i).find("td:eq(4)").removeAttr('id').attr('id','cboDiaWidthType_'+i);
				$('#tr_' + i).find("td:eq(5)").removeAttr('id').attr('id','cbo_uom_'+i);*/
				//ondblclick
				$('#updateIdDtls_' + i).val('');
				$('#hideWoDtlsId_' + i).val('');
				$('#hideWoId_' + i).val('');
				$('#txtsalesno_' + i).val('');
				
				$('#txtsalesno_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_wo(" + i + ");");
				//$('#updateIdDtls_'+i).removeAttr("value").attr("value","");
				//$('#hideWoDtlsId_'+i).removeAttr("value").attr("value","");
				//('#hideWoId_'+i).removeAttr("value").attr("value","");
				$('#txtamount_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
				//$('#txtwoqty_'+i).removeAttr("value").attr("value","");
				$('#txtwoqty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
				//$('#txtrate_'+i).removeAttr("value").attr("value","");
				$('#txtrate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_amount(" + i + ");");
				//$('#txtamount_'+i).removeAttr("value").attr("value","");
				$('#txtnoofroll_'+i).removeAttr("value").attr("value","");
				$('#txtremark_'+i).removeAttr("value").attr("value","");

				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			}

			set_all_onclick();
			calculate_amount(i);
		
	}
	function fn_deleteRow(rowNo)
	{
			var numRow = $('#tbl_fso_item tbody tr').length;
			//if(numRow==rowNo && rowNo!=1)
			//alert(rowNo);
			if( numRow==1)
			{
				return false;
			}
			if(rowNo!=0)
			{
				var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id ); 
				}
			//	var bar_code =$("#barcodeNo_"+rowNo).val();
				//var index = scanned_barcode.indexOf(bar_code);
				//scanned_barcode.splice(index,1);
				//$('#tbl_item_details tbody tr:last').remove();
				$('#slTd_'+rowNo).remove();
			}
			else
			{
				return false;
			}

			calculate_amount(numRow);
		
	}
	
function calculate_amount(i) {
//alert(i);
 	var wo_qty = $('#txtwoqty_' + i).val() * 1;
 	var avgRate = $('#txtrate_' + i).val() * 1;
 	var amount = 0;
 	if (wo_qty <= 0 || avgRate <= 0) {
 		amount = '';
 	}
 	else {
 		amount = wo_qty * avgRate;
 		amount = amount.toFixed(2);
 	}
 	$('#txtamount_' + i).val(amount);
 	total_amount_cal();
 }
 function total_amount_cal(){
 	var total_amnt = 0;
	
 	$("#tbl_fso_item").find('tbody tr').each(function () {
 		var txtAmount = $(this).find('input[name="txtamount[]"]').val();
		//alert(txtAmount);
 		total_amnt += txtAmount*1;
 	});
 	total_amnt = total_amnt.toFixed(4);

 	$('#txt_total_amount').val(total_amnt);
 }
 function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var txt_booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+txt_booking_date, 'check_conversion_rate', '', 'requires/grey_fabric_service_wo_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
</script>
</head>

<body onLoad="set_hotkey();check_exchange_rate();">
	<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="grey_issue_1" id="grey_issue_1" autocomplete="off" > 
    	<div style="width:100%;" align="center">  
            <fieldset style="width:1100px;">
                <legend>Grey Fabric Service Wo Order</legend>
                   <!-- ========================== Master table start ============================ -->     
                       <fieldset style="width:950px;">                                       
                            <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                <tr>
                                    <td colspan="6" align="center"><b>System No&nbsp;</b>
                                        <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                    	<input type="hidden" id="hidden_system_id"  name="hidden_system_id" /> 
                                    </td>
                               </tr>
                               <tr>
                                    <td  width="120" align="right" class="must_entry_caption">Company Name </td>
                                    <td width="170">
                                        <?  		 
                                         echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                         //load_room_rack_self_bin('requires/grey_fabric_service_wo_order_controller*1', 'store','store_td', this.value);
                                        
                                        ?>
                                    </td>
                                    <td width="120" align="right" class="must_entry_caption">Service Type</td>
									 <td width="" id="">
									 <?
									
                                     echo create_drop_down( "cbo_service_type", 170, $fabric_service_type,"", 1, "-- Select Service --", $selected, "", "", "1,2,3,4"); ?>
									 </td>
                                    
                                    <td width="120" align="right" class="must_entry_caption">Pay Mode</td>
                                    <td width="" id="issue_purpose_td">
										<? 
											 echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "","","" ); 
                                        ?>
                                    </td>
                                </tr>
                                <tr>                           
                                  <td width="120" align="right" class="must_entry_caption">Booking Date </td>
                                  <td width="160"><input type="text" name="txt_booking_date" id="txt_booking_date" class="datepicker" style="width:160px;" value="<? echo date('d-m-Y')?>" placeholder="Select Date" onChange="check_exchange_rate();" readonly /></td>
								   <td width="120" align="right" class="">Attention</td>
								   <td>
								    <input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:160px"/>
									</td>
									 <td width="120" align="right">Currency</td>
                    				<td><? echo create_drop_down( "cbo_currency", 170, $currency,"",1, "-- Select --", 2, "check_exchange_rate()",0 ); ?></td>
									
                                    
                                </tr>
                                <tr>                          
                                    <td width="120" align="right" id="knit_source">Exchange Rate</td>
                                    <td width="170">
                                        <input name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px" disabled="disabled" readonly="" />
                                        
                                    </td>
                                   <td width="120" align="right" >Source</td>
                                    <td width="160"><?
                                        echo create_drop_down( "cbo_dyeing_source", 172, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_service_wo_order_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_knit_com', 'dyeing_company_td' );","","1,3" );
                                    ?></td>
                                    <td width="120" class="must_entry_caption" align="right">Factory</td>
                                    <td width="" id="dyeing_company_td">
										<?
                                        	echo create_drop_down( "cbo_dyeing_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                                    	?>
                                	</td>
                                    
                                </tr>
                                <tr>
                                   <td width="120" align="right" >Delivery Date</td>
                                   
                                  <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:160px;" placeholder="Select Date" readonly /></td>
								   <td  width="120" align="right" >Challan No</td>
                                   <td width="170">
                                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" >
                                   </td>
                                  
                                   <td width="120" align="right">Vehical No</td>
                                   <td width=""><input  type="text" name="txt_vehical_no" id="txt_vehical_no" class="text_boxes" style="width:160px"  /></td>                                   
                              	</tr>
                                <tr>
                                    <td width="120" align="right">Driver Name</td>
                                    <td>
                                        <input type="text" name="txt_driver" id="txt_driver" class="text_boxes" style="width:160px"  />
                                        
                                    </td>
									<td width="120" align="right">DL No</td>
                                    <td>
                                        <input type="text" name="txt_dl_no" id="txt_dl_no" class="text_boxes" style="width:160px"  />
                                        
                                    </td>
									<td width="120"  align="right">Transport No</td>
                                    <td>
                                        <input type="text" name="txt_transport_no" id="txt_transport_no" class="text_boxes" style="width:160px"  />
                                       
                                    </td>
                                   
                                 </tr>
								 <tr>
                                    <td width="120" align="right">Mobile No</td>
                                    <td>
                                        <input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:160px" />
                                        
                                    </td>
									<td width="120" align="right">Remarks</td>
                                    <td colspan="5">
                                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:460px"  />
                                        
                                    </td>
                                 </tr>
								 <tr>
                                    <td width="120" align="right">Terms and Condition</td>
                                    <td>
                                        <? 
									include("../../terms_condition/terms_condition.php");
									terms_condition(309,'txt_system_no','../../');
									?>
                                        
                                    </td>
									
                                 </tr>
                            </table>
                        </fieldset> 
				
			<form name="greymasterform_2" id="greymasterform_2" autocomplete="off">
             <fieldset style="float:left; margin-left:30px">  
                <legend>Display</legend>                                     
                      <table class="rpt_table" width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fso_item">
                        	<thead>
							<th width="20">SL</th>
								<th>Sales Order No</th>
                                <th>Buyer</th>
                                <th>Fabric Booking NO</th>
                                <th>Item Description</th>					
                                <th>Dia Type</th>
                                <th>UOM</th>
                                <th>Color</th>
								<th>Color Range</th>
                                <th class="must_entry_caption">WO Qnty</th>
                                <th>Rate</th>
                                <th>Amount</th>
								<th>No of Roll</th>
								<th>Remarks</th>
								<th>&nbsp;</th>
                            </thead>
                            <tbody id="fso_details_container">
                            	<tr class="general" id="slTd_1">
								<td  width="20">1</td>
                                    <td>
                                        <input type="text" name="txtsalesno[]" id="txtsalesno_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                                        <input type="hidden" name="hideWoId[]" id="hideWoId_1" readonly />
										<input type="hidden" name="hideWoDtlsId[]" id="hideWoDtlsId_1" readonly />
                                        
                                    </td>
                                    <td> 
                                       <?
									   echo create_drop_down( "cbobuyer_1", 70, $blank_array,"", 1, "-- Select Buyer --", 0, "", "", "", "", "", "","","","cbobuyer[]");
									   ?>	
									   
                                    </td>
                                    <td>
                                        <input type="text" name="txtfabricbookingno[]" id="txtfabricbookingno_1" class="text_boxes" value="" style="width:100px" disabled="disabled"/> 
                                    </td> 
                                        <td>
											
											<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes" style="width:100px" readonly/>
											<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes">
										</td>
                                    <td>
                                        <?
											echo create_drop_down( "cboDiaWidthType_1", 70, $fabric_typee,"",1, "-- Select --", 0, "", "", "", "", "", "", "", "", "cboDiaWidthType[]" );
											?>
                                    </td>
                                    <td>
                                       <?
										  echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "-Uom-", 0, "", "", "", "", "", "","","","cbouom[]");
										?>	
                                    </td>
                                     <td>
                                     <input type="text" name="txtcolor[]" id="txtcolor_<? echo $i; ?>" class="text_boxes" style="width:60px" placeholder="Display" readonly/> 
									<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes">					 
                                    </td>
									 <td id="">
                                        <? 
										 echo create_drop_down( "cbocolorrange_1", 70, $color_range,"", 1, "-- Color --", 0, "", "", "", "", "", "","","","cbocolor_range[]");
										 ?>						 
                                    </td>
                                    <td>
                                        <input type="text" name="txtwoqty[]" id="txtwoqty_1" class="text_boxes_numeric" value="" style="width:60px;"  placeholder="Write" onKeyUp="calculate_amount(1)"/>
                                    </td>
                                    <td>
                                        <input type="text" name="txtrate[]" id="txtrate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                                    </td>
                                    <td>
                                        <input type="text" name="txtamount[]" id="txtamount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                    </td>
									<td>
                                        <input type="text" name="txtnoofroll[]" id="txtnoofroll_1"  value="" class="text_boxes_numeric"  style="width:60px;" readonly/>
                                    </td>
									<td>
                                        <input type="text" name="txtremark[]" id="txtremark_1" class="text_boxes" value="" style="width:75px;" readonly/>
                                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" readonly/>
                                    </td>	
									<td width="90">
											<input type="hidden" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
											<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
										</td>
                                </tr>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <tr>
                                    <td>&nbsp;</td>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Total</td>
									 <td style="text-align:center">
                                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
										<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" readonly />
                                    </td>
                                    <td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									
                                </tr>
						</tfoot>
					</table>
                </fieldset>  
				</form> 
                <div style="clear:both"></div>
                   <!-- ========================== Master table end ============================ -->     
                   <!-- ========================== Child table start ============================ -->                                      
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr> 
                           <td colspan="6" align="center"></td>				
                        </tr>
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                                 <!-- details table id for update -->                             
                                 <input type="hidden" id="update_id" name="update_id" readonly />
                                 <!-- -->
                                 <? echo load_submit_buttons( $permission, "fnc_grey_fabric_service_entry", 0,1,"fnResetForm()",1);?>
                            </td>
                       </tr> 
                    </table>                 
                    </fieldset>              	
                  <!-- ========================== Child table end ============================ -->   

    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>

    		</div>
		</form>
	</div>    
</body>  
<script>
	set_multiselect('cbo_yarn_count*cbo_color_id','0*0','0*0','','0*0');
	disable_enable_fields('show_textcbo_yarn_count','1','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
