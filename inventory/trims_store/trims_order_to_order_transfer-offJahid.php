<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	17-06-2015
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
echo load_html_head_contents("Trims Order To Order Transfer Info","../../", 1, 1, '','',''); 

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
	var page_link = 'requires/trims_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','');
		
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/trims_order_to_order_transfer_controller" );
		load_drop_down( 'requires/trims_order_to_order_transfer_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/trims_order_to_order_transfer_controller','');
		show_list_view($('#txt_from_order_id').val()+"__"+$('#cbo_store_name').val(),'show_dtls_list_view','list_fabric_desc_container','requires/trims_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1);');
		disable_enable_fields( 'cbo_company_id*cbo_store_name*txt_from_order_no*cbo_store_name_to*txt_to_order_no', 1, '', '' );
		set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	if(type=='from')
	{
		if (form_validation('cbo_company_id*cbo_store_name','Company*From Store')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
	}
	
	
	var title = 'Order Info';	
	var page_link = 'requires/trims_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&type='+type+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/trims_order_to_order_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/trims_order_to_order_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id+"__"+cbo_store_name,'show_dtls_list_view','list_fabric_desc_container','requires/trims_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1);');
		}
	}
}


function fnc_trims_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_store_order_to_order_transfer_print", "requires/trims_order_to_order_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
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
                
		var current_stock_qty=$("#txt_current_stock").val()*1;
		var transfer_qnty=$("#txt_transfer_qnty").val()*1;	
		if(transfer_qnty>current_stock_qty)
		{
			alert('Transfered Qnty Can not be Greater Than Current Stock Qty.');
			$("#txt_transfer_qnty").focus();
			return;	
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;

		var cbo_floor_to=$('#cbo_floor_to').val()*1;
		var cbo_room_to=$('#cbo_room_to').val()*1;
		var txt_rack_to=$('#txt_rack_to').val()*1;
		var txt_shelf_to=$('#txt_shelf_to').val()*1;
		var cbo_bin_to=$('#cbo_bin_to').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
			// ===============================================================================
			if(store_update_upto==6 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || cbo_bin_to==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && cbo_floor_to==0 || cbo_room_to==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor_to==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*cbo_item_category*cbo_item_desc*txt_item_id*txt_transfer_qnty*cbo_uom*update_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*store_update_upto*txt_rate";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/trims_order_to_order_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_transfer_entry_reponse;
	}
}

function fnc_trims_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);
                
		if (reponse[0] * 1 == 20 * 1) 
		{
			 alert(reponse[1]);
			 release_freezing();
			 return;
		} 
		show_msg(reponse[0]); 	
		if(reponse[0]==11)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/trims_order_to_order_transfer_controller','');
			show_list_view(reponse[3]+"__"+reponse[4],'show_dtls_list_view','list_fabric_desc_container','requires/trims_order_to_order_transfer_controller','setFilterGrid(\'tbl_list_search\',-1);');
			disable_enable_fields( 'cbo_company_id*cbo_store_name*txt_from_order_no*cbo_store_name_to*txt_to_order_no', 1, '', '' );
			set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
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
	//alert (data)
	var data=data.split("**");
	$("#cbo_item_desc").val(data[0]);
	$("#txt_prod_id").val(data[0]);
	$("#cbo_uom").val(data[3]);
	$("#txt_item_id").val(data[2]);
	$("#txt_current_stock").val(number_format(data[4],4));
	$("#txt_rate").val(data[5]);
	set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
	
	//get_php_form_data(data[0]+'_'+data[3], 'show_ile_load_uom', 'requires/trims_order_to_order_transfer_controller' );
	//$("#txt_batch_no").val(data[2]);
	//alert(data[4]);
}

function company_on_change(company)
{
	$("#cbo_company_id_to").val(company);
    var data='cbo_company_id='+company+'&action=upto_variable_settings';    

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;
        }
    }
    xmlhttp.open("POST", "requires/trims_order_to_order_transfer_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:760px; float:left" align="center">   
            <fieldset style="width:760px;">
            <legend>Trims Store Order To Order Transfer Entry</legend>
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
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'store','from_store_td', this.value);load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*cbo_store_name_to', 'store','to_store_td', this.value);company_on_change(this.value);" );
                                    //load_drop_down( 'requires/trims_order_to_order_transfer_controller', this.value, 'load_drop_down_store', 'store_td_from' );load_drop_down( 'requires/trims_order_to_order_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td_to' ); fn_to_store(this.value);
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
	                                	<td width="100" class="must_entry_caption">From Store</td>
	                                    <td id="from_store_td">
	                                        <?
	                                           echo create_drop_down( "cbo_store_name", 162, $blank_array,"", 1, "--Select store--", 0, "" );
	                                        ?>	
	                                    </td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Floor</td>
										<td id="floor_td">
											<? echo create_drop_down( "cbo_floor", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Room</td>
										<td id="room_td">
											<? echo create_drop_down( "cbo_room", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Rack</td>
										<td id="rack_td">
											<? echo create_drop_down( "txt_rack", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Shelf</td>
										<td id="shelf_td">
											<? echo create_drop_down( "txt_shelf", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Bin</td>
										<td id="bin_td">
											<? echo create_drop_down( "cbo_bin", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
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
                                </table>
                            </fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>To Order</legend>					
                                <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                    <tr>
	                                	<td width="100" class="must_entry_caption">To Store</td>
	                                    <td id="to_store_td">
	                                        <?
	                                           echo create_drop_down( "cbo_store_name_to", 162, $blank_array,"", 1, "--Select store--", 0, "" );
	                                        ?>	
	                                    </td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Floor</td>
										<td id="floor_td_to">
											<? echo create_drop_down( "cbo_floor_to", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Room</td>
										<td id="room_td_to">
											<? echo create_drop_down( "cbo_room_to", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Rack</td>
										<td id="rack_td_to">
											<? echo create_drop_down( "txt_rack_to", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Shelf</td>
										<td id="shelf_td_to">
											<? echo create_drop_down( "txt_shelf_to", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Bin</td>
										<td id="bin_td_to">
											<? echo create_drop_down( "cbo_bin_to", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
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
                                                echo create_drop_down( "cbo_item_category", 152, $item_category,'', 0, '', '', '','1',4 );
                                            ?>
                                        </td>
                                        <td width="80">UOM</td>
                                        <td>
                                            <?
                                                //echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,'', 1, "--Select--", '', "",1,"" );
												echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,"", 1, "-- Select UOM --", '0', "",1 );
                                                
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Description</td>
                                        <td id="itemDescTd" colspan="3">
                                            <?
                                                echo create_drop_down( "cbo_item_desc", 403, $blank_array,'', 1, "--Select Item Description--", 0, "",1 );
                                            ?>	
                                            <input type="hidden" name="txt_prod_id" id="txt_prod_id"/>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Transfered Qnty</td>
                                        <td><input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:140px;" />
                                         	<input type="hidden" name="txt_current_stock" id="txt_current_stock" style="width:50px;" class="text_boxes_numeric" readonly/>
                                            <input type="hidden" name="txt_item_id" id="txt_item_id"/>
                                            <input type="hidden" name="txt_rate" id="txt_rate"/>
                                        </td>
                                        <td></td>
                                        <td>
                                        	
                                        </td>
                                    </tr>
                                   
                                </table>
                            </fieldset>
                        </td>
                    </tr> 	
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_trims_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id\');reset_dropDown();')",1);
                            ?>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                            <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                            <input type="hidden" name="is_posted_account" id="is_posted_account" readonly>
                            <input type="hidden" name="store_update_upto" id="store_update_upto">
                            <input type="hidden" name="cbo_company_id_to" id="cbo_company_id_to">
                        </td>
                    </tr>
                    <tr>
                    <td colspan="6" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
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
