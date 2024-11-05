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
echo load_html_head_contents("Finish Garments Order To Order Transfer Entry","../", 1, 1, $unicode,'','');

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage_popup() {
 	var txt_job_no = $('#txt_job_no').val();
	var title = 'Job Selection Form';
	var page_link = 'requires/inspection_end_sewing_entry_controller?txt_job_no=' + txt_job_no + '&action=jobNo_popup';

	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '');

	emailwindow.onclose = function () {
		var theform = this.contentDoc.forms[0];
		var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
		var booking_data = hidden_booking_data.split("**");
		var job_id = booking_data[9];

		$('#txt_booking_no_id').val(booking_data[0]);
	}
}

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
 			http.open("POST","requires/inspection_end_sewing_entry_controller",true);
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
            <legend>Inspection And End Sewing Entry</legend>
            <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order">
            	<tr>
          			<td width="40%" valign="top">
               			<fieldset>
                  			
                  			<table cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
                                    <td class="">Sales Number</td> 
                                    <td> 
										<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes"
										style="width:150px;" placeholder="Double Click To Edit"
										onDblClick="openmypage_popup()" readonly/>
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Roll Number</td> 
                                    <td> 
                                        <input type="text" name="txt_roll_number" id="txt_roll_number" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">QC Date</td> 
                                    <td> 
                                        <input class="datepicker" type="text" style="width:150px;" value="<? echo date("d-m-Y")?>" name="txt_qc_date" id="txt_qc_date" />
                                    </td>
								</tr>
                                <tr>
                                    <td class="">QC Name</td> 
                                    <td> 
                                        <input type="text" name="txt_qc_name" id="txt_qc_name" class="text_boxes"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Roll Width (inch)</td> 
                                    <td> 
                                        <input type="text" name="txt_roll_width" id="txt_roll_width" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Roll Wgt. (Met)</td> 
                                    <td> 
                                        <input type="text" name="txt_roll_wgt" id="txt_roll_wgt" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Roll Length (Yds)</td> 
                                    <td> 
                                        <input type="text" name="txt_roll_length" id="txt_roll_length" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Reject Qty</td> 
                                    <td> 
                                        <input type="text" name="total_reject_qty" id="total_reject_qty" class="text_boxes_numeric"  style="width:150px" readonly>
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Construction & Composition</td> 
                                    <td> 
                                        <input type="text" name="txt_construction_composition" id="txt_construction_composition" class="text_boxes"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">GSM</td> 
                                    <td> 
                                        <input type="text" name="txt_transfer_qty" id="txt_transfer_qty" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Dia</td> 
                                    <td> 
                                        <input type="text" name="txt_transfer_qty" id="txt_transfer_qty" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">M/C Dia</td> 
                                    <td> 
                                        <input type="text" name="txt_transfer_qty" id="txt_transfer_qty" class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>
                                <tr>
                                    <td class="">Color</td> 
                                    <td> 
                                        <input type="text" name="txt_transfer_qty" class="text_boxes"  class="text_boxes_numeric"  style="width:150px">
                                    </td>
								</tr>

                            </table>
						</fieldset>
                    </td>
                 	<td width="1%" valign="top"></td>
                  	<td width="60%" valign="top">
                        <fieldset>
                            <legend>Reset Defect Counter</legend>
                            <table cellspacing="0" cellpadding="0"  border="1" rules="all" class="rpt_table"  align="center" width="100%" >
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Defect Name</th>
                                        <th>Defect Count</th>
                                        <th>Found in (Inch)</th>
                                        <th width="100">Penalty Poin</th>
                                    </tr>
                                </thead>
                                <tbody id="penalty_poin_id">
								<?php
									$defect_name = array("1"=>"Hole", "2"=>"Loop", "3"=>"Press Off", "4"=>"Lycra Out","5"=>"Lycra Drop", "6"=>"Lycra Out/Drop", "7"=>"Dust", "8"=>"Oil Spot","9"=>"Fly Conta", "10"=>"Slub", "11"=>"Patta", "12"=>"Needle Break","13"=>"Sinker Mark", "14"=>"Wheel Free", "15"=>"Count Mix", "16"=>"Yarn Contra","17"=>"NEPS", "18"=>"Black Spot", "19"=>"Oil/Ink Mark", "20"=>"Set up","21"=>"Pin Hole", "22"=>"Slub Hole", "23"=>"Needle Mark", "24"=>"Miss Yarn","25"=>"Color Contra [Yarn]", "26"=>"Color/dye spot", "27"=>"friction mark", "28"=>"Pin out","29"=>"softener spot", "30"=>"Dirty Spot", "31"=>"Rust Stain", "32"=>"Stop mark","33"=>"Compacting Broken", "34"=>"Insect Spot", "35"=>"Grease spot", "36"=>"Knot","37"=>"Tara","38"=>"Contamination","39"=>"Thick and Thin");
								?> 
								<?php
								foreach ($defect_name as $key => $value){
								?>
                                    <tr>
                                        <td align="center"><?php echo $key ?></td>
                                        <td align="center"><?php echo $value ?></td>
                                        <td align="center"></td>
                                        <td align="center"></td>
                                        <td align="center">
											<input type="text" name="txt_penalty_poin" id="txt_poin_<? echo $key; ?>" class="text_boxes_numeric" onchange="fn_poin_sum(this.id);"  style="width:100px">
										</td>
                                    </tr>
								<?php } ?>
								</tbody>
								<tfoot>
                                    <tr>
                                        <td colspan="4" align="right" style="background-color:#CCCCCC">Total Point:  </td>
                                        <td align="center" id="total_sum"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="right" style="background-color:#CCCCCC">Fabric Grade:  </td>
                                        <td align="center"></td>
                                    </tr>
                                    <tr>
                                        <td  colspan="2" align="left">Comments</td>
                                        <td colspan="3" align="center"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>	
                    </td>
                    <td width="40%" valign="top">
                    	<div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>
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
            <div style="width:930px; margin-top:5px;" id="transfer_list_view" align="center"></div>
        </fieldset>
	</form>
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>    
</html>
