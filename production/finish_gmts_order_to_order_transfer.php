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

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	if (form_validation('cbo_company_id*cbo_transfer_criteria','Company*Transfer Criteria')==false)
	{
		return;
	}

	var title = 'Order Info';
	var page_link = 'requires/finish_gmts_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_data=this.contentDoc.getElementById("order_data").value; //Access form field with id="emailfield"
		var data=order_data.split("_");

		$('#txt_'+type+'_order_id').val(data[0]);
		$('#txt_'+type+'_order_no').val(data[1]);
		$('#cbo_'+type+'_buyer_name').val(data[2]);
		$('#txt_'+type+'_style_ref').val(data[3]);
		$('#txt_'+type+'_job_no').val(data[4]);
		$('#txt_'+type+'_po_qnty').val(data[5]);
		$('#cbo_'+type+'_gmts_item').val(data[6]);
		$('#txt_'+type+'_shipment_date').val(data[7]);
		$('#cbo_'+type+'_country_name').val(data[8]);
		$('#txt_'+type+'_plan_cut_qty').val(data[9]);

		if(type=='from')
		{
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();

			reset_form('','breakdown_td_id','txt_transfer_qty*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity','','');
			get_php_form_data(data[0]+'**'+data[6]+'**'+data[8]+'**'+cbo_company_id, "populate_data_from_search_popup", "requires/finish_gmts_order_to_order_transfer_controller" );

			if(variableSettings!=1)
			{
				get_php_form_data(data[0]+'**'+data[6]+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+data[8]+'**'+cbo_company_id, "color_and_size_level", "requires/finish_gmts_order_to_order_transfer_controller" );
			}
			else
			{
				$("#txt_transfer_qty").removeAttr("readonly");
			}
		}
	}
}

function openmypage_reqNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var transfer_criteria = $('#cbo_transfer_criteria').val();
	if (form_validation('cbo_company_id*cbo_transfer_criteria','Company*Transfer Criteria')==false)
	{
		return;
	}

	var title = 'Sample Requision Info';
	var page_link = 'requires/finish_gmts_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=requisition_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_data=this.contentDoc.getElementById("order_data").value; //Access form field with id="emailfield"
		//alert(order_data);
		var data=order_data.split("_");
		//alert('#cbo_'+type+'sample_name');

		$('#txt_'+type+'_order_id').val(data[0]);
		$('#txt_'+type+'_order_no').val(data[1]);
		$('#cbo_'+type+'_buyer_name').val(data[2]);
		$('#txt_'+type+'_style_ref').val(data[3]);
		$('#txt_'+type+'_job_no').val(data[4]);
		$('#txt_'+type+'_po_qnty').val(data[6]);
		$('#cbo_'+type+'_gmts_item').val(data[5]);
		$('#txt_'+type+'_shipment_date').val(data[9]);
		$('#cbo_'+type+'_country_name').val(0);
		$('#txt_'+type+'_plan_cut_qty').val(data[10]);
		$('#cbo_'+type+'_sample_name').val(data[10]);
		//alert(data[11]);


		if(type=='from')
		{
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();

			reset_form('','breakdown_td_id','txt_transfer_qty*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity','','');
			get_php_form_data(data[0]+'**'+data[5], "populate_sample_data_from_search_popup", "requires/finish_gmts_order_to_order_transfer_controller" );

			if(variableSettings!=1)
			{
				get_php_form_data(data[0]+'**'+data[5]+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level_sample", "requires/finish_gmts_order_to_order_transfer_controller" );
			}
			else
			{
				$("#txt_transfer_qty").removeAttr("readonly");
			}
		}
	}
}

function fnc_transfer_entry(operation)
{
	if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_id*txt_transfer_date*cbo_transfer_criteria*txt_transfer_qty*txt_from_order_no*txt_to_order_no','Company Name*Transfer Date*Transfer Criteria*Transfer Quantity*From Order No*To Order No')==false )
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
			if(date_compare($('#txt_transfer_date').val(), current_date)==false)
			{
				alert("Transfer Date Can not Be Greater Than Current Date");
				return;
			}

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

			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('txt_system_id*update_id*cbo_company_id*sewing_production_variable*styleOrOrderWisw*txt_transfer_date*cbo_transfer_criteria*txt_challan_no*txt_from_order_id*cbo_from_gmts_item*cbo_from_country_name*txt_to_order_id*cbo_to_gmts_item*cbo_to_country_name*txt_transfer_qty*txt_remark*hidden_break_down_html*hidden_colorSizeID*update_dtls_id*update_dtls_issue_id*update_dtls_recv_id*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*garments_nature',"../");
 			freeze_window(operation);
 			http.open("POST","requires/finish_gmts_order_to_order_transfer_controller.php",true);
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

			reset_form('transferEntry_1','breakdown_td_id','','','','txt_system_id*update_id*cbo_company_id*sewing_production_variable*styleOrOrderWisw*txt_transfer_date*txt_challan_no');
			$("#update_id").val(response[1]);
			$("#txt_system_id").val(response[2]);
			$("#txt_cumul_quantity").attr('placeholder','');
			$("#txt_yet_quantity").attr('placeholder','');
			show_list_view(response[1]+'**'+response[3],'show_dtls_listview','transfer_list_view','requires/finish_gmts_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1)');
		}

		release_freezing();
 	}
}


function fn_total(tableName, index) // for color and size level
	{
		var filed_value = $("#colSize_" + tableName + index).val();
		var placeholder_value = $("#colSize_" + tableName + index).attr('placeholder');
		
		if (filed_value * 1 > placeholder_value * 1) 
		{
			  alert("Qnty Excceded by" + (placeholder_value - filed_value));
				$("#colSize_" + tableName + index).val('');
				$("#txt_transfer_qty").val('');
		
				if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
					void(0);
				else {
					$("#colSize_" + tableName + index).val('');
				}
		}
		var totalRow = $("#table_" + tableName + " tr").length;
		//alert(tableName);
		math_operation("total_" + tableName, "colSize_" + tableName, "+", totalRow);
		if ($("#total_" + tableName).val() * 1 != 0) {
			$("#total_" + tableName).html($("#total_" + tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
	
		let max_transfer_amount  = $("#txt_to_plan_cut_qty").val();

		if(totalVal > max_transfer_amount)
		 {
			alert("Qnty Excceded by" + (max_transfer_amount - totalVal));
			$("#colSize_" + tableName + index).val('');
			$("#txt_transfer_qty").val('');

			if (confirm("Qnty Excceded by" + (max_transfer_amount - totalVal)))
				void(0);
			else
			{
				$("#colSize_" + tableName + index).val('');
			}
	   }
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

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Item Transfer Info';
	var page_link = 'requires/finish_gmts_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_data=this.contentDoc.getElementById("transfer_data").value; //Access form field with id="emailfield"
		var data=transfer_data.split("_");
		var transfer_id=data[0];
		var system_no=data[1];
		var transfer_date=data[2];
		var challan=data[3];
		var transfer_criteria=data[4];

		reset_form('transferEntry_1','transfer_list_view*breakdown_td_id','','','','cbo_company_id*sewing_production_variable*styleOrOrderWisw');

		$('#update_id').val(transfer_id);
		$('#txt_system_id').val(system_no);
		$('#txt_transfer_date').val(transfer_date);
		$('#txt_challan_no').val(challan);
		$('#cbo_transfer_criteria').val(transfer_criteria);

		fn_change_design(transfer_criteria);

		show_list_view(transfer_id+'**'+transfer_criteria,'show_dtls_listview','transfer_list_view','requires/finish_gmts_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1)');
		set_button_status(0, permission, 'fnc_transfer_entry',1,1);
	}
}

function fn_check_qty()
{
	// alert('ok');
	var placeholderVal = document.getElementById("txt_transfer_qty").getAttribute("placeholder")*1;
	var isUpdateMood = $("#isUpdateMood").val()*1;
	var finQty = $("#txt_finish_quantity").val()*1;
	var cumulQty = $("#txt_cumul_quantity").val()*1;
	var trnfQty = $("#txt_transfer_qty").val()*1;
	if(isUpdateMood){yetToTransQty = (finQty-cumulQty)+placeholderVal;}else{yetToTransQty=finQty-cumulQty;}
	if(yetToTransQty < trnfQty)
	{
		alert('Transfer qty is not bigger than '+yetToTransQty);
		$("#txt_transfer_qty").val('');
		return;
	}
}

function fn_change_design(type)
{
	show_list_view(type,'populate_design_view','design_view','requires/finish_gmts_order_to_order_transfer_controller','');
	$("#breakdown_td_id").html('');
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:930px;">
            <legend>Finish Garments Transfer Entry</legend>
            <br>
            <fieldset style="width:920px;">
                <table width="100%" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                        <td colspan="4" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8"><hr class="style-one"></td>
                    </tr>
                    <tr>
                        <td width="8%" class="must_entry_caption">Company</td>
                        <td width="12%">
                            <?
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "get_php_form_data(this.value,'load_variable_settings','requires/finish_gmts_order_to_order_transfer_controller');" );
                            ?>
                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="isUpdateMood" value="" />
                        </td>
                        <td width="8%" class="must_entry_caption">Transfer Date</td>
                        <td width="12%">
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:100px;" readonly placeholder="Select Date" />
                        </td>
                        <td width="8%">Challan No.</td>
                        <td width="12%">
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:100px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td width="8%" class="must_entry_caption">Transfer Criteria</td>
                        <td width="16%">
                            <?
                                echo create_drop_down( "cbo_transfer_criteria", 130, $fin_gmts_transfer_criteria_array,"", 1, "-- Select --", 0, "fn_change_design(this.value);" );
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
			<div id="design_view"></div>


            <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order">
            	<tr>
          			<td width="40%" valign="top">
               			<fieldset>
                  			<legend>New Entry</legend>
                  			<table cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
                                    <td class="must_entry_caption">Transfer Qnty</td>
                                    <td>
                                        <input type="text" name="txt_transfer_qty" id="txt_transfer_qty" class="text_boxes_numeric"  style="width:100px" readonly onkeyup="fn_check_qty()" >
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:240px" title="450 Characters" /></td>
                                </tr>
                            </table>
						</fieldset>
                    </td>
                 	<td width="1%" valign="top"></td>
                  	<td width="15%" valign="top">
                        <fieldset>
                            <legend>Display</legend>
                            <table cellpadding="0" cellspacing="2" width="200px" >
                                <tr>
                                    <td width="100">Finish Qnty</td>
                                    <td width="80"><input type="text" name="txt_finish_quantity" id="txt_finish_quantity" class="text_boxes_numeric" style="width:70px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Cumul. Transfer Qnty</td>
                                    <td><input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity" class="text_boxes_numeric" style="width:70px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Yet to Transfer </td>
                                    <td><input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric" style="width:70px" disabled /></td>
                                </tr>
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
