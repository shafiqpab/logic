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


if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][485] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/trims_production_order_to_order_transfer_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list*list_fabric_desc_container','','');
		
		/*var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";*/
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/trims_production_order_to_order_transfer_entry_controller" );
		load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/trims_production_order_to_order_transfer_entry_controller','');
		//show_list_view($('#txt_from_order_id').val()+"__"+$('#cbo_store_name').val(),'show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		disable_enable_fields( 'cbo_company_id*cbo_store_name*txt_from_order_no*cbo_store_name_to*txt_to_order_no', 1, '', '' );
		set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
	}
}

function fnc_load_party(type,within_group,dropdown_type)
{
	var company = $('#cbo_company_id').val();
	if(within_group==1)
	{
		if(type==1)
		{
		load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', company+'_'+1+'_'+dropdown_type, 'load_drop_down_buyer', 'buyer_td_mst' );
		}
		else
		{
 			load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', company+'_'+1+'_'+dropdown_type, 'load_drop_down_buyer', 'buyer_td_mst_to' );
			
		}
		
	}
	else if(within_group==2 && type==1)
	{
		
		
		if(type==1)
		{
			load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', company+'_'+2+'_'+dropdown_type, 'load_drop_down_buyer', 'buyer_td_mst' );
		}
		else
		{
 			load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', company+'_'+2+'_'+dropdown_type, 'load_drop_down_buyer', 'buyer_td_mst_to' );
			
		}
		
		//load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', company+'_'+2+'_'+dropdown_type, 'load_drop_down_buyer', 'buyer_td_mst' );
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	if(type=='from')
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+type;
	}
	else
	{
		if (form_validation('cbo_company_id*txt_from_order_no*txt_item_description','Company*From Work Order*Item Description')==false)
		{
			return;
		}
		var cboSection 		= $('#cboSection').val();
		var cboSubSection 	= $('#cboSubSection').val();
		var cboItemGroup 	= $('#cboItemGroup').val();
		var cbo_uom 		= $('#cbo_uom').val();
		var hid_color_id 	= $('#hid_color_id').val();
		var hid_size_id 	= $('#hid_size_id').val();
		var item_description = encodeURIComponent($('#txt_item_description').val());
		var data=document.getElementById('cbo_company_id').value+"_"+type+"_"+cboSection+"_"+cboSubSection+"_"+cboItemGroup+"_"+cbo_uom+"_"+hid_color_id+"_"+hid_size_id+"_"+item_description;
	}
	var title = 'Order Info';	
	var	page_link='requires/trims_production_order_to_order_transfer_entry_controller.php?action=order_popup&data='+data;
	//var page_link = 'requires/trims_production_order_to_order_transfer_entry_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	//alert(cbo_company_id)
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]
		var theemaildata=this.contentDoc.getElementById("selected_job").value;
		var ex_data=theemaildata.split('_');
		if (ex_data[0]!="")
		{
			if(type=='from')
			{
				//load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
				get_php_form_data( ex_data[0], "load_mst_php_data_to_form", "requires/trims_production_order_to_order_transfer_entry_controller" );
				show_list_view(ex_data[0],'show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
			}
			else
			{
				get_php_form_data( ex_data[0], "load_mst_php_data_to_form_to", "requires/trims_production_order_to_order_transfer_entry_controller" );
			}

		}
		/*var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		//get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/trims_production_order_to_order_transfer_entry_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id+"__"+cbo_store_name,'show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		}*/
	}
}


function set_form_data(data)
{		 
	$('#cboSection').val(0);
	$('#cboSubSection').val(0);
	$('#cboItemGroup').val(0);
	$('#cbo_uom').val(0);
	$('#txt_item_color').val('');
	$('#txt_item_size').val('');
	$('#txt_item_description').val('');
	$('#hid_job_dtls_id').val('');
	$('#hid_color_id').val('');
	$('#hid_size_id').val('');
	$('#txt_transfer_qnty').val('');
	$('#txt_remark').val('');
	var data=data.split("**");
	$('#cboSection').val(data[1]);
	$('#cboSubSection').val(data[2]);
	$('#cboItemGroup').val(data[3]);
	$('#txt_item_description').val(data[4]);
	$('#hid_color_id').val(data[5]);
	$('#hid_size_id').val(data[6]);
	$('#txt_item_color').val(data[7]);
	$('#txt_item_size').val(data[8]);
	$('#cbo_uom').val(data[9]);
	$('#hid_job_dtls_id').val(data[0]);
	$('#hid_production_qty').val(data[10]);
	
	$('#txt_prod_qty').val(data[10]);
	$('#txt_cum_prod_qty').val(data[11]);
	$('#txt_yet_transfer_qty').val(data[12]);
	set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
}


function fnc_trims_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#update_dtls_id').val(), "trims_store_order_to_order_transfer_print", "requires/trims_production_order_to_order_transfer_entry_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		/*if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}*/
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*txt_item_description*txt_transfer_qnty','Company*Transfer Date*From Order No*To Order No*Item Description*Transfered Qnty')==false )
		{
			return;
		}
                
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
				alert("Transfer Date Can not Be Greater Than Current Date");
				return;
		}
		
		var dataString = "update_id*txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_no*txt_from_order_id*cbo_from_buyer_name*txt_order_rcv_no*txt_from_job_no*txt_from_buyer_buyer*txt_to_order_no*txt_to_order_id*cbo_to_buyer_name*txt_to_order_rcv_no*txt_to_job_no*txt_to_buyer_buyer*cboSection*cboSubSection*cboItemGroup*cbo_uom*txt_item_color*txt_item_size*txt_item_description*txt_transfer_qnty*txt_remark*update_dtls_id*order_received_id*to_order_received_id*job_id*to_job_id*hid_job_dtls_id*hid_color_id*hid_size_id*hid_production_qty";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/trims_production_order_to_order_transfer_entry_controller.php",true);
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
                
		/*if (reponse[0] * 1 == 20 * 1) 
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
		}*/
		
		show_msg(reponse[0]);
		
		if (reponse[0] * 1 == 20 * 1) 
		{
			 alert(reponse[1]);
			 release_freezing();
			 return;
		} 
		if (reponse[0] * 1 == 30 * 1) 
		{
			 alert(reponse[1]);
			 release_freezing();
			 return;
		} 
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('','','cboSection*cboSubSection*cboItemGroup*cbo_uom*txt_item_color*txt_item_size*txt_item_description*txt_transfer_qnty*txt_remark*update_dtls_id','','','');
 			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/trims_production_order_to_order_transfer_entry_controller','');
			show_list_view(reponse[4],'show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
 			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
			set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
		}	
		
		if(reponse[0]==2)
		{
 			if(reponse[4]==1)
			{
  			  reset_form('transferEntry_1','div_transfer_item_list*list_fabric_desc_container','','','','');
 			  set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
 			}
			else
			{
				
				$("#update_id").val(reponse[1]);
				$("#txt_system_id").val(reponse[2]);
				$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('','','txt_to_order_no*txt_to_order_id*cbo_to_buyer_name*txt_to_order_rcv_no*txt_to_job_no*txt_to_buyer_buyer*cboSection*cboSubSection*cboItemGroup*cbo_uom*txt_item_color*txt_item_size*txt_item_description*txt_transfer_qnty*txt_remark*update_dtls_id','','','');
 			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/trims_production_order_to_order_transfer_entry_controller','');
			
			
			
			disable_enable_fields( 'cbo_company_id*txt_from_order_no', 1, '', '' );
			set_button_status(0, permission, 'fnc_trims_transfer_entry',1,1);
  				
				}
		}	
		release_freezing();
	}
}

function reset_dropDown()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
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
    xmlhttp.open("POST", "requires/trims_production_order_to_order_transfer_entry_controller.php", true);
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
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                    //load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', this.value, 'load_drop_down_store', 'store_td_from' );load_drop_down( 'requires/trims_production_order_to_order_transfer_entry_controller', this.value, 'load_drop_down_store_to', 'store_td_to' ); fn_to_store(this.value);
                                ?>
                            </td>
                            <td class="must_entry_caption">Transfer Date</td>
                            <td>
                                <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;"  value="<? echo date("d-m-Y"); ?>"  readonly placeholder="Select Date" />
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
                            <legend>From Order Details</legend>
                                <table id="from_order_info"  cellpadding="0" cellspacing="1" width="100%">										
                                    <tr>
                                        <td width="30%" class="must_entry_caption">From Work Order</td>
                                        <td>
                                            <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
                                            <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td>Party</td>
                                        <td id="buyer_td_mst">
                                             <? 
                                                echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                            ?>	  	
                                        </td>
                                    </tr>						
                                    <tr>
                                        <td>Order Receive No.</td>
                                        <td>
                                            <input type="text" name="txt_order_rcv_no" id="txt_order_rcv_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                    <tr>
                                        <td>Job ID</td>						
                                        <td>                       
                                            <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr style="display:none">
                                        <td>Buyer's Buyer</td>
                                        <td>
                                            <input type="text" name="txt_from_buyer_buyer" id="txt_from_buyer_buyer" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>To Order Details</legend>					
                                <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                    <tr>
                                    <tr>
                                        <td width="30%" class="must_entry_caption">To Work Order</td>
                                        <td>
                                            <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                            <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
                                        </td>
                                    </tr>
                                    <tr>	
                                        <td>Party</td>
                                        <td id="buyer_td_mst_to">
                                             <? 
                                                echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                            ?>	  	
                                        </td>
                                    </tr>						
                                    <tr>
                                        <td>Order Receive No.</td>
                                        <td>
                                            <input type="text" name="txt_to_order_rcv_no" id="txt_to_order_rcv_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                    <tr>
                                        <td>Job ID</td>						
                                        <td>                       
                                            <input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                        </td>
                                    </tr>
                                    <tr style="display:none">
                                        <td>Buyer's Buyer</td>
                                        <td>
                                            <input type="text" name="txt_to_buyer_buyer" id="txt_to_buyer_buyer" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                    </tr>
                                </table>                  
                           </fieldset>	
                        </td>   
                    </tr>	
                    <tr>
                        <td colspan="3">
                            <fieldset style="margin-top:10px">
                            <legend>Item Info</legend>
                                <table id="tbl_item_info" cellpadding="0" cellspacing="2" width="100%">				
                                    <tr>
                                        <td width="130">Section</td>
                                        <td>
                                            <?
                                                echo create_drop_down( "cboSection", 152, $trims_section,"", 1, "-- Dispaly --","","",1,'','','','','','',"cboSection[]");
                                            ?>
                                        </td>
                                        <td width="130">Sub Section</td>
                                        <td>
                                            <?
                                                echo create_drop_down( "cboSubSection", 152, $trims_sub_section,"", 1, "-- Dispaly --","",'',1,'','','','','','',"cboSubSection[]");
                                            ?>
                                        </td>
                                        <td width="130">Prod. Qty</td>
                                       <td>
	                                            <input type="text" name="txt_prod_qty" id="txt_prod_qty" class="text_boxes" style="width:100px;" disabled="disabled" placeholder="Display" /></td>
                                        <tr>
                                        	<td width="130">Trim Group</td>
	                                        <td>
	                                            <?
	                                                echo create_drop_down( "cboItemGroup", 152, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Dispaly --",$selected, "",1,'','','','','','',"cboItemGroup[]");
	                                            ?>
	                                        </td>
	                                        <td width="80">Booked UOM</td>
	                                        <td>
	                                            <?
	                                                //echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,'', 1, "--Select--", '', "",1,"" );
													echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,"", 1, "-- Dispaly --", '0', "",1 );
	                                                
	                                            ?>
	                                        </td>
                                             <td width="130">Cum. Transfer Qty</td>
                                       <td>
	                                            <input type="text" name="txt_cum_prod_qty" id="txt_cum_prod_qty" class="text_boxes" style="width:100px;" disabled="disabled" placeholder="Display" /></td>
                                        </tr>
                                        <tr>
                                        	<td width="130">Item Color</td>
	                                        <td>
	                                            <input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:137px;" disabled="disabled" placeholder="Display" /></td>
		                                   	<td width="130">Item Size</td>
	                                        <td>
	                                            <input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:137px;" disabled="disabled" placeholder="Display" /></td>
                                                <td width="130">Yet to Transfer qty</td>
                                       <td>
	                                            <input type="text" name="txt_yet_transfer_qty" id="txt_yet_transfer_qty" class="text_boxes" style="width:100px;" disabled="disabled" placeholder="Display" /></td>
                                                
                                        </tr>
                                        <tr>
                                        	<td width="130">Item Description</td>
	                                        <td>
	                                            <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:137px;" disabled="disabled" placeholder="Display" /></td>
		                                   	<td class="must_entry_caption">Transfered Qnty</td>
	                                        <td><input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:140px;" />
	                                         	<input type="hidden" name="txt_current_stock" id="txt_current_stock" style="width:50px;" class="text_boxes_numeric" readonly/>
	                                            <input type="hidden" name="txt_item_id" id="txt_item_id"/>
	                                        </td>
                                        </tr>
                                        <tr>
                                        	<td width="130">Remarks</td>
	                                        <td colspan="3">
	                                            <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:425px;"  placeholder="Write" /></td>
                                        </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr> 	
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_trims_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');reset_dropDown();')",1);
                            ?>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="order_received_id" id="order_received_id" readonly>
                            <input type="hidden" name="to_order_received_id" id="to_order_received_id" readonly>
                            <input type="hidden" name="job_id" id="job_id" readonly>
                            <input type="hidden" name="to_job_id" id="to_job_id" readonly>
                            <input type="hidden" name="hid_job_dtls_id" id="hid_job_dtls_id" readonly>
                            <input type="hidden" name="hid_color_id" id="hid_color_id" readonly>
                            <input type="hidden" name="hid_size_id" id="hid_size_id" readonly>
                            <input type="hidden" name="hid_production_qty" id="hid_production_qty" readonly>
                        </td>
                    </tr>
                    <tr>
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
