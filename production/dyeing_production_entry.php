<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Garments Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	19-12-2015
Updated by 		: 	Shafiq
Update date		: 	05-10-2022
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
echo load_html_head_contents("Dyeing Production Entry","../", 1, 1, $unicode,'','');

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function fnc_transfer_entry(operation)
{
	if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_id*txt_transfer_qty*txt_from_order_no*txt_to_order_no','Company Name*Transfer Quantity*From Order No*To Order No')==false )
		{
			return;
		}		
		else
		{
			var from_buyer=$("#cbo_from_buyer_name").val();
			var from_item=$("#cbo_from_gmts_item").val();
			var to_buyer=$("#cbo_to_buyer_name").val();
			var to_item=$("#cbo_to_gmts_item").val();
			if( (from_buyer!=to_buyer)  || (from_item!=to_item) )
			{
				alert("Please select buyer and same item");
				return;
			}

			var current_date='<? echo date("d-m-Y"); ?>';
			
			var sewing_production_variable = $("#sewing_production_variable").val();
			if(sewing_production_variable=='0')
			{
				sewing_production_variable = 3;
			}
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			
			var i=0;var colorIDvalue='';
			if(sewing_production_variable==2)//color level
			{
 				$("input[name=txt_color]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
						}
					}	
					i++;				
				});
			}
			else if(sewing_production_variable==3)//color and size level
			{
 				$("input[name=colorSize]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
						}
					}
					i++;
 					
				});
			}
			// alert(colorIDvalue);
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('update_id*cbo_company_id*sewing_production_variable*styleOrOrderWisw*txt_from_order_id*cbo_from_gmts_item*cbo_from_country_name*txt_to_order_id*cbo_to_gmts_item*cbo_to_country_name*txt_transfer_qty*txt_remark*hidden_break_down_html*hidden_colorSizeID*update_dtls_id*update_dtls_issue_id*update_dtls_recv_id*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*garments_nature',"../");
 			freeze_window(operation);
 			http.open("POST","requires/dyeing_production_entry_controller",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_transfer_entry_Reply_info;
		}
	}
}
  
function fnc_transfer_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		//alert(http.responseText);return;

		var response=http.responseText.split('**');
		if(response[0]==15) 
		{ 
			 setTimeout('fnc_transfer_entry('+ response[1]+')',8000); 
		}
		else if(response[0]==30) 
		{ 
			alert(response[1]); 
		}
		else if(response[0]==11) 
		{ 
			alert(response[1]); 
		}
		else if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			show_msg(trim(response[0]));

			reset_form('transferEntry_1','breakdown_td_id','','','','update_id*cbo_company_id*sewing_production_variable*styleOrOrderWisw');
			$("#update_id").val(response[1]);
			$("#txt_cumul_quantity").attr('placeholder','');
			$("#txt_yet_quantity").attr('placeholder','');
			show_list_view(response[1],'show_dtls_listview','transfer_list_view','requires/finish_gmts_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1)');		
		} 
		
		release_freezing();
 	}
} 

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+tableName+index).val('');
 		}
	}
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_transfer_qty").val(totalVal);
}

function fn_colorlevel_total(index) //for color level
{
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+index).val('');
 		}
	}
	
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_transfer_qty").val( $("#total_color").val() );
} 

function fn_poin_sum(row_id){
	
	var totalRow = $("#penalty_poin_id tr").length;
	var total= 0;
	for(var i=1; i<=totalRow; i++){
		total += $("#txt_poin_"+i).val()*1;
	}
	$("#total_reject_qty").val(total);
	document.getElementById("total_sum").innerHTML=total;
}



</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:930px;">
            <legend>Dyeing Production Entry </legend>
            <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order">
            	<tr>
                    <table cellpadding="0" cellspacing="2" width="100%">
                        <tr>
                            <td class="">Dyeing Type</td>
                            <td> 
                                <input type="text" name="dyeing_type" id="dyeing_type" class="text_boxes"
                                style="width:150px;">
                            </td>
                            <td class="must_entry_caption">Ref. No</td> 
                            <td> 
                                <input type="text" name="ref_no" id="ref_no" class="text_boxes_numeric"  style="width:150px">
                            </td>
                            <td class="must_entry_caption">Company</td> 
                            <td> 
                                <input type="text" name="cbo_company_id" id="cbo_company_id" class="text_boxes"
                                style="width:150px;">
                            </td>
                        </tr>
                      
                        <tr>
                            <td class="">Issue Challan</td> 
                            <td> 
                                <input type="text" name="challan_no" id="challan_no" class="text_boxes"  style="width:150px">
                            </td>
                            <td class="must_entry_caption">Service Source</td> 
                            <td> 
                                <input type="text" name="txt_service_source" id="txt_service_source" class="text_boxes" style="width:150px">
                            </td>
                            <td class="must_entry_caption">Service Company</td> 
                            <td> 
                                <input type="text" name="txt_service_company" id="txt_service_company" class="text_boxes"  style="width:150px">
                            </td>
                        </tr>
                        <tr>
                            <td class="">Received Challan</td> 
                            <td> 
                                <input type="text" name="txt_receive_challan" id="txt_receive_challan" class="text_boxes"   style="width:150px">
                            </td>
                            <td class="must_entry_caption">Process Name</td> 
                            <td> 
                                <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes"   style="width:150px">
                            </td>
                            <td class="">Service Booking</td> 
                            <td> 
                                <input type="text" name="txt_service_booking" id="txt_service_booking" class="text_boxes"  style="width:150px">
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">BTB LTB</td> 
                            <td> 
                                <input type="text" name="txt_btb_ltb" id="txt_btb_ltb" class="text_boxes"  style="width:150px">
                            </td>
                            <td class="">Hour Unload Meter</td> 
                            <td> 
                                <input type="text" name="txt_transfer_qty" id="txt_transfer_qty" class="text_boxes_numeric"  style="width:150px">
                            </td>
                            <td class="">Water Flow</td> 
                            <td> 
                                <input type="text" name="txt_water_flow" id="txt_water_flow" class="text_boxes"  style="width:150px">
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Productuon Date</td> 
                            <td> 
                                <input class="datepicker" type="text" style="width:150px;" value="<? echo date("d-m-Y")?>" name="txt_productuon_date" id="txt_productuon_date" />
                            </td>
                            <td class="must_entry_caption">Process End Time</td> 
                            <td> 
                                <input type="text" name="process_end_time" id="process_end_time" class="text_boxes" style="width:150px">
                            </td>
                            <td class="must_entry_caption">Floor</td> 
                            <td> 
                                <input type="text" name="txt_floor" id="txt_floor" class="text_boxes" style="width:150px">
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Machine Name</td> 
                            <td> 
                                <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:150px">
                            </td>
                            <td class="must_entry_caption">Result</td> 
                            <td> 
                                <input type="text" name="txt_result" id="txt_result" class="text_boxes" style="width:150px">
                            </td>
                            <td class="">Fabric Type</td> 
                            <td> 
                                <input type="text" name="txt_fabric" id="txt_fabric" class="text_boxes_numeric"  style="width:150px">
                            </td>
                        </tr>
                        <tr>
                            <td class="">Shift Name</td> 
                            <td> 
                                <input type="text" name="txt_shift_name" id="txt_shift_name"  class="text_boxes"  style="width:150px">
                            </td>
                            <td class="">Responsibility Dept</td> 
                            <td> 
                                <input type="text" name="txt_responsibility_dept" id="txt_responsibility_dept"  class="text_boxes"  style="width:150px">
                            </td>
                        </tr>
                    </table>
                </tr>
            </table>    
            <table width="930">
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_transfer_entry", 0,0,"reset_form('transferEntry_1','transfer_list_view*breakdown_td_id','','','disable_enable_fields(\'cbo_company_id\');')",1);
                        ?>
                        <input type="hidden" name="update_dtls_issue_id" id="update_dtls_issue_id" readonly>
                        <input type="hidden" name="update_dtls_recv_id" id="update_dtls_recv_id" readonly>
                        <input type="hidden" name="update_dtls_recv_id" id="update_dtls_id" readonly>
                    </td>
                </tr>
            </table> 
        </fieldset>

        <fieldset style="width:800px;text-align:left">
            <legend>Fabric Details</legend>
				<table cellpadding="0" width="780" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="120">Const/Composition</th>
                        <th width="60">GSM</th>
                        <th width="100">Dia/Width</th>
                        <th width="80">D/W Type</th>
						<th width="80" class="must_entry_caption">Roll</th>
						<th width="70">Ref. Qty</th>
						<th width="80" class="must_entry_caption">Prod. Qty</th>
                        <th width="80">Rate</th>
                        <th width="80">Amount</th>
                    </thead>
                 </table>
                 <div style="width:800px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="780" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_tbody">
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="30">1</td>
								<td width="120">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="70">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
                                <td width="80">&nbsp;</td>
							</tr>
                        </tbody>
                	</table>
                </div>
				<table cellpadding="0" width="780" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <tfoot>
                    	<th width="30">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                    </tfoot>
                 </table>
			</fieldset>
        
	</form>
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>    
</html>
