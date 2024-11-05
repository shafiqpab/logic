<?

/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Sample To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	25-08-2020
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
echo load_html_head_contents("Grey Fabric Sample Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

	if (form_validation('cbo_company_id*cbo_transfer_criteria','Company*Transfer Criteria')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/grey_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*txt_rack_to*txt_shelf_to*txt_rack*txt_shelf*update_trans_issue_id*update_trans_recv_id','','','');
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_fabric_sample_transfer_controller" );
		load_drop_down( 'requires/grey_fabric_sample_transfer_controller', $('#txt_from_order_book_id').val()+"**"+$('#cbo_transfer_criteria').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view($('#txt_from_order_book_id').val()+"**"+$('#cbo_transfer_criteria').val()+"**"+$('#cbo_company_id').val(),'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_sample_transfer_controller','');
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_sample_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		disable_enable_fields("txt_from_order_book_no*txt_to_order_book_no",1);
	}
}

function from_openmypage(type)
{
	var cbo_company_id = $('#cbo_company_id').val();	
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	if(cbo_transfer_criteria==6) // Order
	{
		var title = 'Order Info';
	}
	else // Sample
	{ 
		var title = 'Sample Info';
	}
	var page_link = 'requires/grey_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=from_sample_order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var return_id=this.contentDoc.getElementById("return_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(return_id+"**"+type+"**"+cbo_transfer_criteria, "populate_data_from_sample", "requires/grey_fabric_sample_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/grey_fabric_sample_transfer_controller', return_id+"**"+cbo_transfer_criteria, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(return_id+"**"+cbo_transfer_criteria+"**"+cbo_company_id,'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_sample_transfer_controller','');
		}
	}
}

function to_openmypage(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	if(cbo_transfer_criteria==7) // To Order
	{
		var title = 'Order Info';
	}
	else // To Sample
	{ 
		var title = 'Sample Info';
	}	
	var page_link = 'requires/grey_fabric_sample_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_booking_id").value; //Access form field with id="emailfield"
		get_php_form_data(order_id+"**"+type+"**"+cbo_transfer_criteria, "populate_data_to_order", "requires/grey_fabric_sample_transfer_controller" );
	}
}

function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print", "requires/grey_fabric_sample_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_book_no*txt_to_order_book_no*cbo_item_desc*txt_transfer_qnty*cbo_store_name*cbo_store_name_to','Company*Transfer Date*From Order No*To Order No*Item Description*Transfered Qnty*From Store*To Store')==false )
		{
			return;
		}
                
		var current_date='<? echo date("d-m-Y"); ?>';
        if(date_compare($('#txt_transfer_date').val(), current_date)==false)
        {
            alert("Transfer Date Can not Be Greater Than Current Date");
            return;
        }
                
		// if(($('#txt_transfer_qnty').val()*1)>($('#txt_current_stock').val()*1))
		if(($("#txt_transfer_qnty").val()*1 > $("#txt_current_stock").val()*1+$("#previous_trans_qnty").val()*1)) 
		{
			alert("Transfer Quantity Not Allow More Then Stock Quantity");
			$('#txt_transfer_qnty').val("");
			$('#txt_transfer_qnty').focus();
			return;
		}	

		var store_update_upto=$('#store_update_upto').val()*1;
		var floor=$("#cbo_floor").val();
		var room=$("#cbo_room").val();
		var rack=$("#txt_rack").val();
		var shelf=$("#txt_shelf").val();
		var floorTo=$("#cbo_floor_to").val();
		var roomTo=$("#cbo_room_to").val();
		var rackTo=$("#txt_rack_to").val();
		var shelfTo=$("#txt_shelf_to").val();
		if(store_update_upto > 1)
		{
			if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");
				$("#txt_shelf").focus();return;
			}
			else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");
				$("#txt_rack").focus();return;
			}
			else if(store_update_upto==3 && (floor==0 || room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");
				$("#cbo_room").focus();return;
			}
			else if(store_update_upto==2 && floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");
				$("#cbo_floor").focus();return;
			}

			if(store_update_upto==5 && (floorTo==0 || roomTo==0 || rackTo==0 || shelfTo==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");
				$("#txt_shelf_to").focus();return;
			}
			else if(store_update_upto==4 && (floorTo==0 || roomTo==0 || rackTo==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");
				$("#txt_rack_to").focus();return;
			}
			else if(store_update_upto==3 && (floorTo==0 || roomTo==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");
				$("#cbo_room_to").focus();return;
			}
			else if(store_update_upto==2 && floorTo==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");
				$("#cbo_floor_to").focus();return;
			}
		}
		
		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_book_id*txt_to_order_book_id*cbo_item_category*cbo_item_desc*txt_transfer_qnty*txt_roll*cbo_uom*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*txt_form_prog*txt_to_prog*update_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_trans_qnty*txt_prod_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_sample_transfer_controller.php",true);
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
        if (reponse[0] * 1 == 20 * 1)
        {
            alert(reponse[1]);
            release_freezing();
            return;
        }
		if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(reponse[0]);
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*update_trans_issue_id*update_trans_recv_id*txt_form_prog*txt_to_prog*txt_current_stock','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_sample_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
			disable_enable_fields("cbo_transfer_criteria*cbo_company_id*txt_from_order_book_no*txt_to_order_book_no",1,"","");
			release_freezing();
		}
		else if(reponse[0]==40)	
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==5)
		{
			alert(reponse[1]);
			$('#txt_transfer_qnty').val("");
			$('#txt_transfer_qnty').focus();
			release_freezing();return;
		}
		else
		{
			show_msg(reponse[0]);
			release_freezing();
		}
		
	}
}

function reset_dropDown()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
}

function openmypage_orderInfo(type)
{
	var txt_order_no = $('#txt_to_order_book_no').val();
	var txt_order_id = $('#txt_to_order_book_id').val();

	if (form_validation('txt_to_order_book_no','Order No')==false)
	{
		alert("Please Select Order No.");
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_sample_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

function set_form_data(data)
{
	set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*update_trans_issue_id*update_trans_recv_id*txt_current_stock*previous_trans_qnty*txt_prod_id','','','');

	var data=data.split("**");
	$("#cbo_item_desc").val(data[0]);
	$("#txt_prod_id").val(data[0]);
	$("#txt_ycount").val(data[1]);
	$("#hid_ycount").val(data[2]);
	$("#txt_ybrand").val(data[3]);
	$("#hid_ybrand").val(data[4]);
	$("#txt_ylot").val(data[5]);
	//$("#txt_rack_to").val(data[6]);
	//$("#txt_shelf_to").val(data[7]);
	$("#txt_form_prog").val(data[8]);
	$("#txt_to_prog").val(data[8]);
	$("#stitch_length").val(data[9]);

	var store_id = data[12];
	var  floor_id = data[13];
	var  room_id = data[14];
	var  rack_id = data[6];
	var  shelf_id = data[7];

	var company_id = $('#cbo_company_id').val();
	get_php_form_data(company_id+"_"+store_id+"_"+floor_id+"_"+room_id+"_"+rack_id+"_"+shelf_id, "populate_store_floor_room_rack_self_data", "requires/grey_fabric_sample_transfer_controller" );

	populate_stock();

	/*var transfer_criteria = $("#cbo_transfer_criteria").val();
	get_php_form_data(data[0]+'**'+data[10]+'**'+transfer_criteria, "populate_stock_data", "requires/grey_fabric_sample_transfer_controller" );*/
}

function populate_stock()
{
	var txt_order_id = $('#txt_from_order_book_id').val();
	var prod_id = $('#cbo_item_desc').val();
	var from_program_no = $('#txt_form_prog').val();
	var company_id = $('#cbo_company_id').val();
	var transfer_criteria = $("#cbo_transfer_criteria").val();
	var store = $("#cbo_store_name").val();
	var floor = $("#cbo_floor").val();
	var room = $("#cbo_room").val();
	var rack = $("#txt_rack").val();
	var shelf = $("#txt_shelf").val();
	if( form_validation('cbo_store_name','From Store')==false )
	{
		$("#txt_current_stock").val('');
		return;
	}
	get_php_form_data(txt_order_id+"**"+prod_id+"**"+from_program_no+"**"+company_id+"**"+transfer_criteria+"**"+store+"**"+floor+"**"+room+"**"+rack+"**"+shelf, "populate_data_about_order", "requires/grey_fabric_sample_transfer_controller" );
}

//  Transfer Criteria wise onchange caption
function active_inactive(type)
{
	if (type==8) // sammple to sammple
	{
		$("#showHideFrom").html('From Sample');
		$("#showHideTo").html('To Sample');

		$("#showHideToNo").html('SBWO No').css('color', 'blue');
		$("#showHideFromNo").html('SBWO No').css('color', 'blue');

		$("#showHideFromQty").html('Booking Qnty');
		$("#showHideToQty").html('Booking Qnty');

		$("#cbo_garments_item_show").show();
		$("#txt_garments_item_hide").css("display","none");
		
	}
	else if(type==6) // Order to sammple
	{
		$("#showHideFrom").html('From Order');
		$("#showHideTo").html('To Sample');

		$("#showHideFromNo").html('Order No').css('color', 'blue');
		$("#showHideToNo").html('SBWO No').css('color', 'blue');

		$("#showHideFromQty").html('Order Qnty');
		$("#showHideToQty").html('Booking Qnty.');

		$("#cbo_garments_item_show").css("display","none");
		$("#txt_garments_item_hide").show();
	}
	else // sammple to Order
	{
		$("#showHideFrom").html('From Sample');
		$("#showHideTo").html('To Order');

		$("#showHideFromNo").html('SBWO No').css('color', 'blue');
		$("#showHideToNo").html('Order No').css('color', 'blue');

		$("#showHideFromQty").html('Booking Qnty');
		$("#showHideToQty").html('Order Qnty');

		$("#cbo_garments_item_show").show();
		$("#txt_garments_item_hide").hide();
	}

	reset_form('','','cbo_company_id*txt_from_order_book_no*txt_from_order_book_id*txt_from_qnty*cbo_from_buyer_name*txt_from_style_ref*txt_from_job_no*cbo_from_garments_item*txt_from_shipment_date*txt_to_order_book_no*txt_to_order_book_id*txt_to_qnty*cbo_to_buyer_name*txt_to_style_ref*txt_to_job_no*txt_to_gmts_item*txt_to_shipment_date*update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*txt_rack_to*txt_shelf_to*txt_rack*txt_shelf*update_trans_issue_id*update_trans_recv_id*txt_form_prog*txt_to_prog*txt_current_stock*cbo_store_name*cbo_floor*cbo_room','','','');
	$("#list_fabric_desc_container").text('');
}

function reset_func(value)
{
	// alert(value);
	if (value=='com_fn') 
	{
		reset_form('','','cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to','','','');	
	}
	if (value=='store_fn') 
	{
		reset_form('','','cbo_room_to*txt_rack_to*txt_shelf_to','','','');		
	}
	else if(value=='floor_fn')
	{
		reset_form('','','txt_rack_to*txt_shelf_to','','','');	
	}
	else if(value=='room_fn')
	{
		reset_form('','','txt_shelf_to','','','');	
	}
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:760px; float:left" align="center">   
            <fieldset style="width:760px;">
            <legend>Grey Fabric Sample To Order Transfer Entry</legend>
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
                        	<td class="must_entry_caption">Transfer Criteria</td>
	                        <td>
	                            <?
	                            	$trans_criteria = array( 8 => 'Sample To Sample', 7 => 'Sample To Order', 6 => 'Order To Sample' );
	                                echo create_drop_down( "cbo_transfer_criteria", 160, $trans_criteria,"", 1, "--Select--", 0, "active_inactive(this.value);" );
	                            ?>
	                        </td>
                            <td class="must_entry_caption">Company</td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/grey_fabric_sample_transfer_controller',this.value, 'load_drop_down_store_to', 'to_store_td' );reset_func('com_fn');get_php_form_data(this.value,'varible_inventory','requires/grey_fabric_sample_transfer_controller' );" );
                                ?>
                            </td>
                            <td class="must_entry_caption">Transfer Date</td>
                            <td>
                                <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                            </td> 
                        </tr>
                        <tr>                        	
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
                            <legend id="showHideFrom">From Sample</legend>
                                <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
                                    <tr>
                                        <td width="30%" class="must_entry_caption" id="showHideFromNo">SBWO No</td>
                                        <td>
                                            <input type="text" name="txt_from_order_book_no" id="txt_from_order_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="from_openmypage('from');" readonly />
                                            <input type="hidden" name="txt_from_order_book_id" id="txt_from_order_book_id" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="showHideFromQty">Booking Qnty.</td>
                                        <td>
                                            <input type="text" name="txt_from_qnty" id="txt_from_qnty" class="text_boxes_numeric" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
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
                                            <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gmts Item</td>
                                        <td id="cbo_garments_item_show">
                                            <?  echo create_drop_down( "cbo_from_garments_item", 162, $body_part,"", 1, "--Select--", $selected, "",1 ); ?>	
                                        </td>
                                        <td id="txt_garments_item_hide" style="display: none;">
                                            <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" />
                                        </td>
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
                            <legend id="showHideTo">To Order</legend>					
                                <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                    <tr>
                                        <td width="30%" class="must_entry_caption" id="showHideToNo">Order No</td>
                                        <td>
                                            <input type="text" name="txt_to_order_book_no" id="txt_to_order_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="to_openmypage('to');" readonly />
                                            <input type="hidden" name="txt_to_order_book_id" id="txt_to_order_book_id" readonly>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td id="showHideToQty">Order Qnty</td>
                                        <td>
                                            <input type="text" name="txt_to_qnty" id="txt_to_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
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
                                        <td id="cbo_garments_item_show">
                                            <?  echo create_drop_down( "cbo_from_garments_item", 162, $body_part,"", 1, "--Select--", $selected, "",1 ); ?>	
                                        </td>
                                        <td id="txt_garments_item_hide" style="display: none;">
                                            <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" />
                                        </td>
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
                        <td width="49%" valign="top">
                            <fieldset>
								<legend>From Store</legend>
								<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%" style="float: left;">
									<tr>
										<td width="100" class="must_entry_caption">From Store</td>
										<td id="from_store_td">
											<?
											echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
											?>
										</td>
									</tr>
									<tr>
										<td>Floor</td>
										<td id="floor_td">
											<? echo create_drop_down( "cbo_floor", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Room</td>
										<td id="room_td">
											<? echo create_drop_down( "cbo_room", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Rack</td>
										<td id="rack_td">
											<? echo create_drop_down( "txt_rack", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Shelf</td>
										<td id="shelf_td">
											<? echo create_drop_down( "txt_shelf", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
								</table>
							</fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
								<legend>To Store</legend>
								<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%" style="float: left;">
									<tr>
										<td width="100" class="must_entry_caption">To Store</td>
										<td id="to_store_td">
											<?
											echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "" );
											?>
										</td>
									</tr>
									<tr>
										<td>Floor</td>
										<td id="floor_td_to">
											<? echo create_drop_down( "cbo_floor_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Room</td>
										<td id="room_td_to">
											<? echo create_drop_down( "cbo_room_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Rack</td>
										<td id="rack_td_to">
											<? echo create_drop_down( "txt_rack_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Shelf</td>
										<td id="shelf_td_to">
											<? echo create_drop_down( "txt_shelf_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
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
                                            ?>	
                                        </td>
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
                                        <td>From Prog</td>
                                        <td>
                                           <input type="text" name="txt_form_prog" id="txt_form_prog" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                        </td>
                                        <td>To Prog</td>
                                        <td>
                                            <input type="text" name="txt_to_prog" id="txt_to_prog" class="text_boxes" style="width:125px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Current Stock</td>
                                        <td>
                                           <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                        </td>
                                        <td>Roll</td>
                                        <td>
                                           <input type="text" name="txt_roll" id="txt_roll" class="text_boxes_numeric" style="width:125px"   />
                                        </td>
                                        <td>Stitch Length</td>
                                        <td><input type="text" name="stitch_length" id="stitch_length" class="text_boxes" style="width:125px" placeholder="Display" readonly /></td>                                        
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
                            <input type="hidden" name="store_update_upto" id="store_update_upto"/>
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
