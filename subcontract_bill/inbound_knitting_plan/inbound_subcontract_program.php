<?php
/*-------------------------------------------- Comments
Purpose			: This form will create planning Info Entry
Functionality	:	
JS Functions	:
Created by		: Zaman
Creation date 	: 28.01.2020
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
echo load_html_head_contents("Planning Info Entry", "../../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../../logout.php"; 
	var permission='<?php echo $permission; ?>';
	
	function fnc_order_no()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyId = $("#cbo_company_name").val();
		var partyId = $("#cbo_party_name").val();
		var title='Order No Search';
		var page_link='requires/inbound_subcontract_program_controller.php?action=actn_order_no&companyId='+companyId+'&partyId='+partyId;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hdnOrderId=this.contentDoc.getElementById("hdnOrderId").value;
			var hdnOrderNo=this.contentDoc.getElementById("hdnOrderNo").value;
			
			$('#txt_order_no').val(hdnOrderNo);
			$('#hdn_order_id').val(hdnOrderId);
		}
	}
	
	function fnc_show_details(type)
	{
		//if(form_validation('cbo_company_name*txt_order_no','Company*Order No')==false)
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		if(type==2)
		{
			if(form_validation('txt_order_no','Order No.')==false)
			{
				return;
			}
		}

		/*if(($('#txt_order_no').val() != "") || ($('#txt_style_ref').val() != "") || ($('#txt_internal_ref').val() != "") || ($('#txt_file_no').val() != "") || ($('#cbo_buyer_name').val() != 0) || ($('#txt_booking_no').val() != "") ||($('#txt_date_to').val() != "" && $('#txt_date_to').val() != ""))
		{
			var data="action=booking_item_details"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*hide_job_id*cbo_planning_status*txt_order_no*hdn_order_id*txt_booking_no*approval_needed_or_not*txt_internal_ref*txt_file_no*txt_date_from*txt_date_to*cbo_booking_type',"../../")+'&type='+type;
		}
		else
		{
			if(form_validation('txt_booking_no','Booking No.')==false)
			{
				return;
			}
		}*/
		
		var data='action=actn_show_details&type='+type+get_submitted_data_string('cbo_company_name*cbo_party_name*txt_order_no*hdn_order_id*txt_date_from*txt_date_to*cbo_planning_status*txt_barcode_no','../');
		freeze_window(5);
		http.open("POST","requires/inbound_subcontract_program_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_details_reponse;
	}

	function fnc_show_details_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			$('#container_show_details').html(response);
			set_all_onclick();
			show_msg('18');
			release_freezing();
		}
	}

    function fnc_selected_row(rowNo, status)
	{
		var color=document.getElementById('tr_'+rowNo ).style.backgroundColor;
		var hdnPartyId=$('#hdnPartyId_'+rowNo).val();
		var hdnOrderNo=$('#hdnOrderNo_'+rowNo).val();
		var hdnFabricId=$('#hdnFabricId_'+rowNo).val();
		var hdnGsm=$('#hdnGsm_'+rowNo).val();
		var hdnDia=$('#hdnDia_'+rowNo).val();
		var hdnDiaWidthType=$('#hdnDiaWidthType_'+rowNo).val();
		var hdnOrderQty=$('#hdnOrderQty_'+rowNo).val();
		var hdnPlanId=$('#hdnPlanId_'+rowNo).val();
		
		var rowColor='';
		if(color!='yellow')
		{
			var noOfRow=$('#tbl_list_search tbody tr').length;
			for(var i=1; i<=noOfRow; i++)
			{ 
				if(i!=rowNo)
				{
					check=$('#check_'+i).val();
					rowColor=document.getElementById('tr_'+i ).style.backgroundColor;
					if(rowColor=='yellow')
					{
						var partyId=$('#hdnPartyId_'+i).val();
						var orderNo=$('#hdnOrderNo_'+i).val();
						var fabricId=$('#hdnFabricId_'+i).val();
						var gsm=$('#hdnGsm_'+i).val();
						var dia=$('#hdnDia_'+i).val();
						var diaWidthType=$('#hdnDiaWidthType_'+i).val();
						var orderQty=$('#hdnOrderQty_'+i).val();
						var PlanId=$('#hdnPlanId_'+i).val();

						if(hdnPlanId == '' || PlanId == '')
						{
							if(!(hdnOrderNo == orderNo && hdnFabricId == fabricId && hdnGsm == gsm && hdnDia == dia))
							{
								alert("Please Select Same Description");
								return;
							}
						}
						else
						{
							if(!(hdnPlanId == PlanId && hdnOrderNo == orderNo && hdnFabricId == fabricId && hdnGsm == gsm && hdnDia == dia))
							{
								alert("Please Select Same Description and Same Plan ID");
								return;
							}
						}
					}
				}
			}

			$('#tr_'+rowNo).css('background-color','yellow');
		}
		else
		{
			var hdnIsRequisition=$('#hdnIsRequisition_'+rowNo).val();
			if(hdnIsRequisition == 0)
			{
				$('#tr_'+rowNo).css('background-color','#FFFFCC');
			}
			else
			{
				alert("Requisition Found Against This Planning. So Change Not Allowed");
				return;
			}
		}
    }

    function fnc_program()
	{
        //alert('su..re'); return;
		var hdnType = $('#hdnType').val();
        if (hdnType == 2)
		{
            alert("Not Allow");
            return;
        }
		
        var noOfRow = $('#tbl_list_search tbody tr').length;
        var companyId = $('#company_id').val();
        var data = '';
        var i = 0;
        var selectedRow = 0;
        var rowColor = '';
		var partyId = '';
		var orderNo = '';
		var fabricId = '';
		var fabricDtls = '';
		var gsm = '';
		var dia = '';
		var diaWidthType = '';
		var orderQty = 0;
		var colorId = '';
		var orderMstId = '';
		var orderDtlsId = '';
		var orderBrkDownId = '';
		var planId = '';
		var jobNo = '';
		
        for (var j = 1; j <= noOfRow; j++)
		{
            rowColor = document.getElementById('tr_'+j).style.backgroundColor;
            if (rowColor == 'yellow')
			{
                i++;
                selectedRow++;
				
				if (data != '')
				{
					data +=  "_";
				}
				
				data+= $('#hdnPartyId_'+j).val()+"**"
					+ $('#hdnOrderNo_'+j).val() + "**"
					+ $('#hdnFabricId_'+j).val() + "**"
					+ $('#hdnFabricDtls_'+j).val() + "**"
					+ $('#hdnGsm_'+j).val() + "**"
					+ $('#hdnDia_'+j).val() + "**"
					+ $('#hdnDiaWidthType_'+j).val() + "**"
					+ $('#hdnOrderQty_'+j).val() + "**"
					+ $('#hdnOrderMstId_'+j).val() + "**"
					+ $('#hdnOrderDtlsId_'+j).val() + "**"
					+ $('#hdnOrderBrkDownId_'+j).val() + "**"
					+ $('#hdnColorId_'+j).val();
				
				partyId = $('#hdnPartyId_'+j).val();
				orderNo = $('#hdnOrderNo_'+j).val();
				orderId = $('#hdnOrderId_'+j).val();
				fabricId = $('#hdnFabricId_'+j).val();
				fabricDtls = $('#hdnFabricDtls_'+j).val();
				gsm = $('#hdnGsm_'+j).val();
				dia = $('#hdnDia_'+j).val();
				diaWidthType = $('#hdnDiaWidthType_'+j).val();
				orderQty = $('#hdnOrderQty_'+j).val();
				colorId = $('#hdnColorId_'+j).val();
				orderMstId = $('#hdnOrderMstId_'+j).val();
				orderDtlsId = $('#hdnOrderDtlsId_'+j).val();
				orderBrkDownId = $('#hdnOrderBrkDownId_'+j).val();
				planId = $('#hdnPlanId_'+j).val();
				jobNo = $('#hdnJobNo_'+j).val();
            }
        }
        if (selectedRow < 1)
		{
            alert("Please Select At Least One Item");
            return;
        }

        var title = 'Program Qnty Info';
		var page_link = 'requires/inbound_subcontract_program_controller.php?action=actn_program'
			+ '&partyId='+partyId 
			+ '&orderNo='+orderNo 
			+ '&orderId='+orderId 
			+ '&fabricId='+fabricId 
			+ '&fabricDtls='+fabricDtls 
			+ '&gsm='+gsm 
			+ '&dia='+dia 
			+ '&diaWidthType='+diaWidthType 
			+ '&companyId='+companyId 
			+ '&orderQty='+orderQty 
			+ '&colorId='+colorId 
			+ '&orderMstId='+orderMstId 
			+ '&orderDtlsId='+orderDtlsId 
			+ '&orderBrkDownId='+orderBrkDownId 
			+ '&planId='+planId 
			+ '&jobNo='+jobNo 
			+ '&data='+data;
		
        //alert(page_link);
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=430px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose=function()
		{	
            //alert('su..re');
			fnc_show_details(1);
		}
    }
	
    function fnc_close()
	{
        var data = '';
        $('#selected_data').val(data);
        parent.emailwindow.hide();
    }
	
	function generate_report2(company_id, program_id, format_id="")
	{
		//action name= print
		var path = '../';
		if(format_id==273)
		{
			//print_report(company_id+'*'+program_id+'*'+path, "prog_info_print", "requires/planning_info_entry_for_sales_order_controller");
			print_report(company_id+'*'+program_id+'*'+path, "print", "requires/inbound_subcontract_program_controller");
		}
		else
		{
			//print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller");
			print_report(company_id+'*'+program_id+'*'+path, "print", "requires/inbound_subcontract_program_controller");
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
		<?php echo load_freeze_divs ("../../",$permission); ?>
        <form name="palnningEntry_1" id="palnningEntry_1"> 
            <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
            <div id="content_search_panel">      
                <fieldset style="width:1000px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Party Name</th>
                            <th>Order No</th>
                            <th>Order Receive Date</th>
                            <th>Planning Status</th>
                            <th>Barcode No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')" class="formbutton" style="width:60px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
									<?php echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/inbound_subcontract_program_controller',this.value, 'load_drop_down_party', 'party_td' );" ); ?>
                                </td>
                                <td id="party_td">
									<?php echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"" ); ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" placeholder="Browse Or Write" onDblClick="fnc_order_no();" onChange="$('#hdn_order_id').val('');" autocomplete="off">
                                    <input type="hidden" name="hdn_order_id" id="hdn_order_id" class="text_boxes" readonly>
                                </td>
                                <td>
                                    <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px;"/>			
                                    <input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:55px;"/>				
                                </td>
                                <td> 
									<?php echo create_drop_down( "cbo_planning_status", 130, $planning_status,"", 0, "", $selected,"","", "1,2" ); ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" style="width:120px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                                </td>
                                <td>
                                    <input type="button" value="Show" name="show" id="show" class="formbutton" style="width:60px" onClick="fnc_show_details(1)"/>
                                    <input type="button" value="Revised Order" name="show" id="show" class="formbutton" style="width:100px" onClick="fnc_show_details(2)"/>
                                    <input type="hidden" name="approval_needed_or_not" id="approval_needed_or_not" class="text_boxes" readonly>
                                </td>                	
                            </tr>
                            <tr>
                            	<td colspan="10" align="center" valign="middle"><?php echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:10px;">
            	<input type="button" value="Click For Program" name="generate" id="generate" class="formbutton" style="width:150px" onClick="fnc_program()"/>
                <!--<input type="button" value="Close" name="close" id="close" class="formbuttonplasminus" style="width:150px" onClick="fnc_close()"/>-->
                <input type="hidden" value="" id="selected_data" class="text_boxes"/>
            </div>
        </form>
        <div id="container_show_details" style="margin-left:10px;margin-top:10px"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>