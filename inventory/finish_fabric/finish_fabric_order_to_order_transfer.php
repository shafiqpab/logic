<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	26-06-2013
Updated by 		: 	Kausar (Creating Report)	
Update date		: 	15-12-2013	   
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
echo load_html_head_contents("Finish Fabric Order To Order Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/finish_fabric_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','');
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/finish_fabric_order_to_order_transfer_controller" );
		load_drop_down( 'requires/finish_fabric_order_to_order_transfer_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_order_to_order_transfer_controller','');
		show_list_view($('#txt_from_order_id').val(),'show_dtls_list_view','list_fabric_desc_container','requires/finish_fabric_order_to_order_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
    var cbo_company_id = $('#cbo_company_id').val();
	var cbo_company_to_id = $('#cbo_company_to_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/finish_fabric_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&cbo_company_to_id='+cbo_company_to_id+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/finish_fabric_order_to_order_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/finish_fabric_order_to_order_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id,'show_dtls_list_view','list_fabric_desc_container','requires/finish_fabric_order_to_order_transfer_controller','');
		}
	}
}


function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_order_to_order_transfer_print", "requires/finish_fabric_order_to_order_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*cbo_item_desc*txt_transfer_qnty','Company*Transfer Date*From Order No*To Order No*Item Description*Transfered Qnty')==false )
		{
			return;
		}	
		
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*cbo_item_category*cbo_item_desc*txt_transfer_qnty*txt_batch_id*cbo_uom*txt_torack*txt_toshelf*txt_rack*txt_shelf*update_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*txt_roll_no";

        //alert(get_submitted_data_string(dataString,"../../")); return;

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_order_to_order_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_reponse;
	}
}

function fnc_yarn_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		
                if(reponse[0]*1 == 20)
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
			$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('','','txt_batch_id*txt_batch_no*txt_torack*txt_toshelf*txt_rack*txt_shelf*update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id*cbo_uom','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_order_to_order_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}	
		release_freezing();
	}
}

function reset_dropDown()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
}

function set_form_data(data)
{
	var data=data.split("**");
	$("#cbo_item_desc").val(data[0]);
	$("#txt_batch_id").val(data[1]);
	$("#txt_batch_no").val(data[2]);
	$("#txt_rack").val(data[3]);
	$("#txt_shelf").val(data[4]);
	$("#txt_torack").val(data[3]);
	$("#txt_toshelf").val(data[4]);
	$("#cbo_uom").val(data[5]);

    $("#show_only_item_desc").val(data[7]+" , "+data[6]);

    $('#cbo_item_desc').hide();
    $('#show_only_item_desc').show();
}

function fnc_company_onchang(id){
    $("#cbo_company_to_id").val(id);
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:760px; float:left" align="center">   
            <fieldset style="width:760px;">
            <legend>Finish Fabric Order To Order Transfer Entry</legend>
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
                            <td class="must_entry_caption">From Company</td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "fnc_company_onchang(this.value);" );
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
                                <table id="from_order_info"  cellpadding="0" cellspacing="1" width="100%">										
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
                                        <td>Gmts Item</td>
                                        <td>
                                            <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                    <tr>
                                        <td>Shipment Date</td>						
                                        <td>
                                            <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>File No</td>						
                                        <td>
                                            <input type="text" name="txt_from_file_no" id="txt_from_file_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ref. No</td>						
                                        <td>
                                            <input type="text" name="txt_from_ref_no" id="txt_from_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
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
                                    <td class="must_entry_caption">To Company</td>
                                    <td>
                                        <? 
                                            echo create_drop_down( "cbo_company_to_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                        ?>
                                    </td>
                                    <tr>
                                        <td width="30%" class="must_entry_caption">Order No</td>
                                        <td>
                                            <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                            <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
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
                                        <td>Gmts Item</td>
                                        <td>
                                            <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                    <tr>
                                        <td>Shipment Date</td>						
                                        <td>
                                            <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>File No</td>						
                                        <td>
                                            <input type="text" name="txt_to_file_no" id="txt_to_file_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ref. No</td>						
                                        <td>
                                            <input type="text" name="txt_to_ref_no" id="txt_to_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>											
                                </table>                  
                           </fieldset>	
                        </td>
                    </tr>	
                    <tr>
                        <td colspan="3">
                            <fieldset style="margin-top:10px">
                            <legend>Item Info</legend>
                                <table id="tbl_item_info" cellpadding="0" cellspacing="2" width="75%">										
                                    <tr>
                                        <td width="130">Item Category</td>
                                        <td>
                                            <?
                                                echo create_drop_down( "cbo_item_category", 152, $item_category,'', 0, '', '', '','1',2 );
                                            ?>
                                        </td>
                                        <td width="80"  class="must_entry_caption">UOM</td>
                                        <td>
                                            <?
                                                echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,'', 1, '-Uom-', 12, "",1,"1,12,23,27" );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Description</td>
                                        <td id="itemDescTd" colspan="3">
                                            <?
                                                echo create_drop_down( "cbo_item_desc", 403, $blank_array,'', 1, "--Select Item Description--", 0, "",1 );
                                            ?>	

                                            <input type="text" name="show_only_item_desc" id="show_only_item_desc" class="text_boxes" style="width:392px; display:none;" placeholder="Item Description" disabled />

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Transfered Qnty</td>
                                        <td><input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:140px;" /></td>
                                        <td>Batch No</td>
                                        <td>
                                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Display" disabled />
                                            <input type="hidden" name="txt_batch_id" id="txt_batch_id"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>From Rack</td>
                                        <td>
                                            <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:140px" placeholder="Display" disabled />
                                        </td>
                                        <td >To Rack</td>
                                        <td>
                                            <input type="text" name="txt_torack" id="txt_torack" class="text_boxes" style="width:140px" />
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>From Shelf</td>
                                        <td>
                                            <input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes_numeric" style="width:140px" placeholder="Display" disabled />
                                        </td>
                                        <td >To Shelf</td>
                                        <td>
                                            <input type="text" name="txt_toshelf" id="txt_toshelf" class="text_boxes_numeric" style="width:140px" />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                    	<td>Number of Roll</td>
                                        <td>
                                            <input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:140px"  />
                                        </td>
                                        <td > &nbsp; </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr> 	
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id\');reset_dropDown();')",1);
                            ?>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                            <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                        </td>
                    </tr>
                </table>
                <div style="width:740px;" id="div_transfer_item_list"></div>
            </fieldset>
        </div>
        <div id="list_fabric_desc_container" style="width:480px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	</form>
</div> 
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
