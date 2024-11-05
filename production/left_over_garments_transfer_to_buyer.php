<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Garments Order To Order Transfer Entry

Functionality	:
JS Functions	:
Created by		:	Arnab
Creation date 	: 	17-1-2024
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
echo load_html_head_contents("Finish Garments Order To Order Transfer Entry","../", 1, 1, $unicode,'','');

?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	let cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
	let txt_transfer_date=$('#txt_transfer_date').val();
	let cbo_order_type=$('#cbo_order_type').val();
	let cbo_goods_type=$('#cbo_goods_type').val();

	if (form_validation('cbo_company_id*txt_transfer_date*cbo_transfer_criteria*cbo_order_type*cbo_goods_type','Company*Transfer Date*Transfer Criteria*Order Type*Goods Type')==false)
	{
		return;
	}

	var title = 'Order Info';
	var page_link = 'requires/left_over_garments_transfer_to_buyer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_data=this.contentDoc.getElementById("order_data").value; //Access form field with id="emailfield"
		var data=order_data.split("_");
		// alert(data);

		$('#txt_'+type+'_order_id').val(data[0]);
		$('#txt_'+type+'_order_no').val(data[1]);
		$('#cbo_'+type+'_buyer_name').val(data[2]);
		$('#txt_'+type+'_style_ref').val(data[3]);
		$('#txt_'+type+'_job_no').val(data[4]);
		$('#txt_'+type+'_po_qnty').val(data[5]);
		$('#cbo_'+type+'_gmts_item').val(data[6]);
		$('#txt_'+type+'_shipment_date').val(data[7]);
		$('#cbo_'+type+'_country_name').val(data[8]);

		if(type=='from')
		{
            var variableSettings=3;

			get_php_form_data(data[0]+'**'+data[6]+'**'+cbo_company_id, "populate_data_from_search_popup", "requires/left_over_garments_transfer_to_buyer_controller");


			if(variableSettings!=1)
			{
				get_php_form_data(data[0]+'**'+data[6]+'**'+variableSettings, "color_and_size_level", "requires/left_over_garments_transfer_to_buyer_controller");

			}

		}
	}
}

function openmypage_reqNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	if (form_validation('cbo_company_id*cbo_transfer_criteria','Company*Transfer Criteria')==false)
	{
		return;
	}

	var title = 'Sample Requision Info';
	var page_link = 'requires/left_over_garments_transfer_to_buyer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=requisition_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_data=this.contentDoc.getElementById("order_data").value; //Access form field with id="emailfield"
		var data=order_data.split("_");
		// alert(data);

		$('#txt_'+type+'_order_id').val(data[0]);
		$('#txt_'+type+'_order_no').val(data[1]);
		$('#cbo_'+type+'_buyer_name').val(data[2]);
		$('#txt_'+type+'_style_ref').val(data[3]);
		$('#txt_'+type+'_job_no').val(data[4]);
		$('#txt_'+type+'_po_qnty').val(data[5]);
		$('#cbo_'+type+'_gmts_item').val(data[6]);
		$('#txt_'+type+'_shipment_date').val(data[7]);
		$('#cbo_'+type+'_country_name').val(data[8]);


		if(type=='from')
		{
			var variableSettings=3;

			reset_form('','breakdown_td_id','txt_leftover_quantity*txt_cumul_quantity*txt_yet_quantity','','');
			get_php_form_data(data[0]+'**'+data[6], "populate_sample_data_from_search_popup", "requires/left_over_garments_transfer_to_buyer_controller" );

			if(variableSettings!=1)
			{
				get_php_form_data(data[0]+'**'+data[6]+'**'+variableSettings, "color_and_size_level_sample", "requires/left_over_garments_transfer_to_buyer_controller" );
			}
		}
	}
}

function fnc_transfer_entry(operation)
{
	if(operation==0 || operation==1 || operation==2)
	{
		if (form_validation('cbo_company_id*txt_transfer_date*cbo_transfer_criteria','Company Name*Transfer Date*Transfer Criteria')==false )
		{
			return;
		}
		else
		{
			// var from_buyer=$("#cbo_from_buyer_name").val();
			// var from_item=$("#cbo_from_gmts_item").val();
			// var to_buyer=$("#cbo_to_buyer_name").val();
			// var to_item=$("#cbo_to_gmts_item").val();
			// if( (from_buyer!=to_buyer)  || (from_item!=to_item) )
			// {
			// 	alert("Please select buyer and same item");
			// 	return;
			// }

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_transfer_date').val(), current_date)==false)
			{
				alert("Transfer Date Can not Be Greater Than Current Date");
				return;
			}

		    var sewing_production_variable = $("#sewing_production_variable").val();

		     var sewing_production_variable = 3;


			var colorList = ($('#hidden_colorSizeID').val()).split(",");

			var i=0;var colorIDvalue='';

		     if(sewing_production_variable==3)//color and size level
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
							// alert(colorIDvalue);
						}
					}
					i++;

				});
			}
			// alert(colorIDvalue);

			var data="action=save_update_delete&operation=" + operation +" &colorIDvalue="+ colorIDvalue +get_submitted_data_string('txt_system_id*update_id*cbo_company_id*sewing_production_variable*txt_transfer_date*cbo_transfer_criteria*txt_challan_no*cbo_order_type*cbo_store_name*cbo_del_company*cbo_goods_type*cbo_delivery_location*cbo_delivery_floor*txt_from_order_no*txt_from_po_qnty*cbo_from_buyer_name*txt_from_job_no*txt_from_style_ref*cbo_from_gmts_item*txt_from_shipment_date*cbo_from_country_name*txt_to_order_no*txt_to_po_qnty*cbo_to_buyer_name*txt_to_job_no*txt_to_style_ref*cbo_to_gmts_item*txt_to_shipment_date*cbo_to_country_name*txt_leftover_quantity*txt_cumul_quantity*txt_yet_quantity*txt_from_order_id*txt_to_order_id*update_dtls_id*update_dtls_issue_id*update_dtls_recv_id',"../");
 			freeze_window(operation);
 			http.open("POST","requires/left_over_garments_transfer_to_buyer_controller.php",true);
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

			reset_form('leftoverEntry_1','breakdown_td_id','','','','txt_system_id*update_id*cbo_company_id*sewing_production_variable*txt_transfer_date*txt_challan_no');
			$("#update_id").val(response[1]);
			$("#txt_system_id").val(response[2]);
			$("#txt_cumul_quantity").attr('placeholder','');
			$("#txt_yet_quantity").attr('placeholder','');
			show_list_view(response[1],'show_dtls_listview','transfer_list_view','requires/left_over_garments_transfer_to_buyer_controller','setFilterGrid(\'tbl_list_search\',-1)');
		}

		release_freezing();
 	}
}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
    // if(filed_value*1 > placeholder_value*1)
	// {
	// 	if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
	// 	{
	// 		// void(0);
	// 		$("#colSize_"+tableName+index).val(placeholder_value*1);
	// 	}
	// }

	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);

	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });

	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html(totalVal);
	}
	$("#txt_leftover_quantity").val(totalVal);
}



function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Item Transfer Info';
	var page_link = 'requires/left_over_garments_transfer_to_buyer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_data=this.contentDoc.getElementById("transfer_data").value; //Access form field with id="emailfield"
		var data=transfer_data.split("_");
		// alert(data);
		var transfer_id=data[0];
		var system_no=data[1];
		var transfer_date=data[2];
		var challan=data[3];
		var transfer_criteria=data[4];
		var store_id=data[5];
		var purpose_id=data[6];




		reset_form('leftoverEntry_1','transfer_list_view*breakdown_td_id','','','','cbo_company_id*sewing_production_variable');

		$('#update_id').val(transfer_id);
		$('#txt_system_id').val(system_no);
		$('#txt_transfer_date').val(transfer_date);
		$('#txt_challan_no').val(challan);
		$('#cbo_transfer_criteria').val(transfer_criteria);
		$('#cbo_store_name').val(store_id);
		$('#cbo_goods_type').val(purpose_id);






		show_list_view(transfer_id,'show_dtls_listview','transfer_list_view','requires/left_over_garments_transfer_to_buyer_controller','setFilterGrid(\'tbl_list_search\',-1)');
		set_button_status(0, permission, 'fnc_transfer_entry',1,1);
	}
}








</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="leftoverEntry_1" id="leftoverEntry_1" autocomplete="off" >
        <fieldset style="width:1222px;">
            <legend>Left Over Garments Transfer Entry</legend>
            <br>
            <fieldset style="width:1212px;">
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
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "get_php_form_data(this.value,'load_variable_settings','requires/left_over_garments_transfer_to_buyer_controller');load_drop_down( 'requires/left_over_garments_transfer_to_buyer_controller', this.value, 'load_drop_down_store', 'store_id');" );
                            ?>
                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
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
                                echo create_drop_down( "cbo_transfer_criteria", 130, $fin_gmts_transfer_criteria_array,"", 0, '', 1,"","",1);
                            ?>
                        </td>
                    </tr>
					<tr>
						<td width="8%" class="must_entry_caption">Order Type</td>
						<td width="12%">
						<?
						echo create_drop_down( "cbo_order_type", 130, $order_source, "", 0, "", 1, "", "", "1");
						?>
						</td>
						<td width="8%" >Store Name</td>
                        <td width="12%" id="store_id">
                          <? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select Store --", $selected, "" );?>
                        </td>
						<td width="8%">Working Company</td>
						<td width="12%" id="dev_company_td">
							<?=create_drop_down("cbo_del_company", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/left_over_garments_transfer_to_buyer_controller', this.value, 'load_drop_down_del_location', 'del_location_td' );", 0); ?>
					    </td>
					</tr>
					<tr>
						<td width="8%" class="must_entry_caption">Goods Type</td>
						<td width="12%">
						<?
						$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
						echo create_drop_down( "cbo_goods_type", 130, $goods_type_arr, "", 1, "-- Select Goods Type --", $selected, "", "", "", '', '');
						?>
						</td>
						<td width="8%">Working Location</td>
						<td width="12%" id="del_location_td"><?=create_drop_down("cbo_delivery_location", 130, $blank_array, "", 1, "--Select Working Location--", $selected, ""); ?></td>
						<td width="8%">Floor</td>
						<td width="12%" id="del_floor_td"><?=create_drop_down("cbo_delivery_floor", 130, $blank_array, "", 1, "-- Select Working Floor --", $selected, ""); ?></td>
					</tr>
                </table>
            </fieldset>
            <br>
			<div id="design_view"></div>


            <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order">
            	<tr>
          			<td width="40%" valign="top">
               			<fieldset>
                  			<legend>From Order</legend>
                  			<table cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
									<td width="80" class="must_entry_caption">Order No</td>
									<td width="140">
									<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
									<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
									<input type="hidden" name="txt_from_plan_cut_qty" id="txt_from_plan_cut_qty" readonly>
								</tr>
                                <tr>
								<td>Order Qnty</td>
									<td width="140">
									<input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									<input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
									<input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                </tr>
								<tr>
									<td>Buyer</td>
									<td>
									<?
										echo create_drop_down( "cbo_from_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
									?>
									</td>
								</tr>
								<tr>
								   <td>Job No</td>
									<td>
										<input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:120px" disabled="disabled" placeholder="Display" />
									</td>
								</tr>
								<tr>
									<td>Style Ref.</td>
									<td>
									<input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" />
									</td>
								</tr>
								<tr>
									<td>Item Name</td>
									<td>
										<?
											echo create_drop_down('cbo_from_gmts_item',132,$garments_item,'',1,'Display','','',1);
										?>
									</td>
								</tr>
							    <tr>
									<td>Shipment Date</td>
									<td>
										<input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
									</td>
                               </tr>
							    <tr>
									<td>Country</td>
									<td>
										<?
											echo create_drop_down('cbo_from_country_name',132,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
										?>
									</td>
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
                                    <td width="100">Left Over Stock</td>
                                    <td width="80"><input type="text" name="txt_leftover_quantity" id="txt_leftover_quantity" class="text_boxes_numeric" style="width:70px" readonly onkeyup="fn_check_qty()"/></td>
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
					 <legend>To Order</legend>
					 <fieldset>
						<table cellpadding="0" cellspacing="2" width="100%">
						<tr>
						<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >
							<tr>
								<td width="80" class="must_entry_caption">Order No</td>
								<td width="140">
								<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
								<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
								<input type="hidden" name="txt_from_plan_cut_qty" id="txt_from_plan_cut_qty" readonly>
							 </tr>
								<tr>
								<td>Order Qnty</td>
									<td width="140">
								    <input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
								</tr>
								<tr>
									<td>Buyer</td>
									<td>
									<?
									echo create_drop_down( "cbo_to_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
									?>
									</td>
								</tr>
								<tr>
								   <td>Job No</td>
									<td>
										<input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:120px" disabled="disabled" placeholder="Display"/>
									</td>
								</tr>
								<tr>
									<td>Style Ref.</td>
									<td>
										<input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</td>
								</tr>
								<tr>
									<td>Item Name</td>
									<td>
									<?
										echo create_drop_down('cbo_to_gmts_item',132,$garments_item,'',1,'Display','','',1);
									?>
									</td>
								</tr>
							    <tr>
									<td>Shipment Date</td>
									<td>
									<input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
									</td>
                               </tr>
							    <tr>
									<td>Country</td>
									<td>
									<?
									echo create_drop_down('cbo_to_country_name',132,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
									?>
									</td>
								</tr>
						</table>
                     </fieldset>
                    </td>

                    <td width="41%" valign="top">
                    	<div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>
                </tr>
            </table>
            <table width="930">
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_transfer_entry", 0,0,"reset_form('leftoverEntry_1','transfer_list_view*breakdown_td_id','','','disable_enable_fields(\'cbo_company_id\');')",1);
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
