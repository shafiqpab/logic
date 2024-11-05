<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Order To Sample Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	20-06-2015
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
echo load_html_head_contents("Grey Fabric Order To Sample Transfer Info","../../", 1, 1, '','',''); 

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
	var page_link = 'requires/grey_fabric_order_to_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*txt_torack*txt_toshelf*txt_rack*txt_shelf*update_trans_issue_id*update_trans_recv_id','','','');
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_fabric_order_to_sample_transfer_controller" );
		load_drop_down( 'requires/grey_fabric_order_to_sample_transfer_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view($('#txt_from_order_id').val(),'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_order_to_sample_transfer_controller','');
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_order_to_sample_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_order_to_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/grey_fabric_order_to_sample_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/grey_fabric_order_to_sample_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id,'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_order_to_sample_transfer_controller','');
		}
	}
}

function fn_open_semple()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Sample Info';	
	var page_link = 'requires/grey_fabric_order_to_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=sample_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var sample_id=this.contentDoc.getElementById("sample_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(sample_id, "populate_data_from_sample", "requires/grey_fabric_order_to_sample_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/grey_fabric_order_to_sample_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id,'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_order_to_sample_transfer_controller','');
		}
	}
}



function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print", "requires/grey_fabric_order_to_sample_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_sam_book_no*cbo_item_desc*txt_transfer_qnty','Company*Transfer Date*From Order No*To Order No*Item Description*Transfered Qnty')==false )
		{
			return;
		}
		
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		if(($('#txt_transfer_qnty').val()*1)>($('#txt_current_stock').val()*1))
		{
			alert("Transfer Quantity Not Allow More Then Order Stock");
			$('#txt_transfer_qnty').val("");
			$('#txt_transfer_qnty').focus();
			return;
		}
		
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_sam_book_id*cbo_item_category*cbo_item_desc*txt_transfer_qnty*txt_roll*cbo_uom*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*txt_torack*txt_toshelf*txt_rack*txt_shelf*txt_form_prog*txt_to_prog*update_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_trans_qnty*txt_prod_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_order_to_sample_transfer_controller.php",true);
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
		//alert(http.responseText);release_freezing();return;
                if (reponse[0] * 1 == 20 * 1) {
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
			
			reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*txt_torack*txt_toshelf*txt_rack*txt_shelf*update_trans_issue_id*update_trans_recv_id*txt_form_prog*txt_to_prog*txt_current_stock','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_order_to_sample_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
			disable_enable_fields("cbo_company_id*txt_from_order_no*txt_sam_book_no",1,'','');
		}
		if(reponse[0]==5)
		{
			alert(reponse[1]);
			$('#txt_transfer_qnty').val("");
			$('#txt_transfer_qnty').focus();
			release_freezing();return;
		}
		release_freezing();
	}
}

function reset_dropDown()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
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
	var page_link = 'requires/grey_fabric_order_to_sample_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

function set_form_data(data)
{
	var data=data.split("**");
	$("#cbo_item_desc").val(data[0]);
	$("#txt_prod_id").val(data[0]);
	$("#txt_ycount").val(data[1]);
	$("#hid_ycount").val(data[2]);
	$("#txt_ybrand").val(data[3]);
	$("#hid_ybrand").val(data[4]);
	$("#txt_ylot").val(data[5]);
	$("#txt_rack").val(data[6]);
	$("#txt_shelf").val(data[7]);
	$("#txt_torack").val(data[6]);
	$("#txt_toshelf").val(data[7]);
	$("#txt_form_prog").val(data[8]);
	$("#txt_to_prog").val(data[8]);
	$("#stitch_length").val(data[9]);
	get_php_form_data(data[0]+'**'+data[10], "populate_stock_data", "requires/grey_fabric_order_to_sample_transfer_controller" );
	
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:760px; float:left" align="center">   
            <fieldset style="width:760px;">
            <legend>Grey Fabric Order To Sample Transfer Entry</legend>
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
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
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
                                        <td>Gmts Item</td>
                                        <td>
                                            <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                    <tr>
                                        <td>Shipment Date</td>						
                                        <td>
                                            <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                            <input type="button" class="formbutton" style="width:80px" value="View" onClick="openmypage_orderInfo('from');">
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>To Sample
                                </legend><table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                    <tr>
                                        <td width="30%" class="must_entry_caption">SBWO No</td>
                                        <td>
                                            <input type="text" name="txt_sam_book_no" id="txt_sam_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="fn_open_semple();" readonly />
                                            <input type="hidden" name="txt_sam_book_id" id="txt_sam_book_id" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Booking Qnty.</td>
                                        <td>
                                            <input type="text" name="txt_sam_booking_qnty" id="txt_sam_booking_qnty" class="text_boxes_numeric" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
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
                                        <td height="19">Gmts Item</td>
                                        <td>
                                            <?  echo create_drop_down( "cbo_garments_item", 162, $body_part,"", 1, "--Select--", $selected, "",1 ); ?>	
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
                                <table id="tbl_item_info" cellpadding="0" cellspacing="1" border="0" width="100%">										
                                    <tr>
                                        <td width="110">Item Category</td>
                                        <td >
                                            <?
                                                echo create_drop_down( "cbo_item_category", 135, $item_category,'', 0, '', '', '','1',13 );
                                            ?>
                                        </td>
                                        <td width="80">Y/Count</td>
                                        <td>
                                            <input type="text" name="txt_ycount" id="txt_ycount" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                            <input type="hidden" name="hid_ycount" id="hid_ycount" class="text_boxes" style="width:80px"/>
                                        </td>
                                        <td width="60">Y/Brand</td>
                                        <td>
                                            <input type="text" name="txt_ybrand" id="txt_ybrand" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                            <input type="hidden" name="hid_ybrand" id="hid_ybrand" class="text_boxes" style="width:80px"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Description</td>
                                        <td colspan="3" id="itemDescTd">
                                        <?
                                                echo create_drop_down( "cbo_item_desc", 368, $blank_array,'', 1, "--Select Item Description--", 0, "",1 );
                                            ?></td>
                                        <td >Y/Lot</td>
                                        <td>
                                            <input type="text" name="txt_ylot" id="txt_ylot" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Transfered Qty</td>
                                        <td width="150">
                                        <input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:60px;" />
                                        <input type="hidden" name="previous_trans_qnty" id="previous_trans_qnty" class="text_boxes_numeric"  />
                                        <? echo create_drop_down( "cbo_uom", 60, $unit_of_measurement,'', 0, "", '', "",1,12 ); ?>
                                        </td>
                                        <td >From Rack</td>
                                        <td>
                                            <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:125px" placeholder="Display" readonly >
                                        </td>
                                        <td >To Rack</td>
                                        <td>
                                            <input type="text" name="txt_torack" id="txt_torack" class="text_boxes" style="width:125px"   />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Roll</td>
                                        <td>
                                           <input type="text" name="txt_roll" id="txt_roll" class="text_boxes_numeric" style="width:125px"   />
                                        </td>
                                        <td>From Shelf</td>
                                        <td>
                                            <input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes_numeric" style="width:125px" placeholder="Display" readonly />
                                        </td>
                                        <td >To Shelf</td>
                                        <td>
                                            <input type="text" name="txt_toshelf" id="txt_toshelf" class="text_boxes_numeric" style="width:125px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>From Prog</td>
                                        <td>
                                           <input type="text" name="txt_form_prog" id="txt_form_prog" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                        </td>
                                        <td>To Prog</td>
                                        <td>
                                            <input type="text" name="txt_to_prog" id="txt_to_prog" class="text_boxes" style="width:125px" />
                                        </td>
                                        <td>Sth. Length</td>
                                        <td><input type="text" name="stitch_length" id="stitch_length" class="text_boxes" style="width:125px" placeholder="Display" readonly /></td>
                                    </tr>
                                    <tr>
                                        <td>Current Stock</td>
                                        <td>
                                           <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
                        	<input type="hidden" id="txt_prod_id" >
                        </td>
                    </tr>
                </table>
                <div style="width:740px;" id="div_transfer_item_list"></div>
            </fieldset>
        </div>
        <div id="list_fabric_desc_container" style="width:550px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
