<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Order To Order Transfer Info
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	
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
	var page_link = 'requires/finish_fabric_fso_to_fso_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/finish_fabric_fso_to_fso_transfer_controller" );
		show_list_view(transfer_id+"**"+$('#txt_from_order_id').val()+"**"+cbo_company_id,'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_fso_to_fso_transfer_controller','');
		setFilterGrid('scanning_tbl',-1);
		disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
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
	var page_link = 'requires/finish_fabric_fso_to_fso_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/finish_fabric_fso_to_fso_transfer_controller" );
		if(type=='from')
		{
			show_list_view(order_id+"_"+cbo_company_id,'show_dtls_list_view','list_fabric_desc_container','requires/finish_fabric_fso_to_fso_transfer_controller','');
		}
		
	}
}


function fnc_finish_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_from_order_id').val()+'*'+$('#txt_to_order_id').val(), "finish_fabric_order_to_order_transfer_print", "requires/finish_fabric_fso_to_fso_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*txt_transfer_qnty*cbo_store_name','Company*Transfer Date*From Order No*To Order No*Transfer Qnty*Store Name')==false )
		{
			return;
		}
		
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		//var row_num=$('#scanning_tbl tbody tr').length-1;
		var txt_deleted_id=''; var selected_row=0; var i=0; var data_all=''; 
		
		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;
		
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
			else if(store_update_upto==3 && cbo_floor==0 || cbo_room==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
	
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*txt_product_id*fabric_desc_id*txt_batch_id*txt_gsm*txt_width*txt_dia_width_type*txt_machine_id*txt_color_id*cbo_fabric_shade*cbo_uom*txt_body_part_id*txt_rate*txt_cons_rate*from_store_id*from_floor_id*from_room_id*from_rack_id*from_shelf_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_transfer_qnty*hidden_transfer_qnty*update_dtls_id*update_trans_from*update_trans_to*update_id*txt_batch_no*txt_to_order_no*txt_to_booking_no*txt_item_desc*previous_to_batch_id*previous_from_batch_id*store_update_upto*txt_aop_rate";

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+data_all;
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_fso_to_fso_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_finish_transfer_entry_reponse;
	}
}

function fnc_finish_transfer_entry_reponse()
{	
	var cbo_company_id = $('#cbo_company_id').val();
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		// alert(http.responseText);release_freezing();
        if(reponse[0]*1 == 20)
        {
            alert(reponse[1]);
            release_freezing();
            return;
        } 
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if (reponse[0]==2 && reponse[4]==1) // is mst delete reset form
			{
				disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
				reset_form('transferEntry_1','div_transfer_item_list*list_fabric_desc_container','','','');
			}
			else
			{
				$("#update_id").val(reponse[1]);
				$("#txt_system_id").val(reponse[2]);
				
				//show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_fso_to_fso_transfer_controller','');
				show_list_view(reponse[1]+"**"+$('#txt_from_order_id').val()+"**"+cbo_company_id,'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_fso_to_fso_transfer_controller','');

				show_list_view($('#txt_from_order_id').val()+"_"+cbo_company_id,'show_dtls_list_view','list_fabric_desc_container','requires/finish_fabric_fso_to_fso_transfer_controller','');
				
				setFilterGrid('scanning_tbl',-1);
				disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
			}
			
			set_button_status(1, permission, 'fnc_finish_transfer_entry',1,1);
			
		}	
		release_freezing();
	}
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
	var page_link = 'requires/finish_fabric_fso_to_fso_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="check[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="check[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
	
	function reset_form_all()
	{
		disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
		reset_form('transferEntry_1','tbl_details','','','');
	}

	function validate_available()
	{
		var available_qnty = $('#txt_current_stock').val()*1;
		var transferQnty = $('#txt_transfer_qnty').val()*1;
		var hidden_trans_qnty = $('#hidden_transfer_qnty').val()*1;
		if(transferQnty > available_qnty)
		{
			alert("Transfer Qnty Exceeds Available Qnty.\n Transfer qnty="+transferQnty+"\n Available qnty="+available_qnty);
			$('#txt_transfer_qnty').val(hidden_trans_qnty);
			return;
		}

	}

	function set_form_data(data_ref)
	{
		var data=data_ref.split("_");

		$('#txt_product_id').val(data[0]);
		$('#txt_item_desc').val(data[1]);
		$('#fabric_desc_id').val(data[2]);
		$('#txt_batch_id').val(data[3]);
		$('#txt_batch_no').val(data[4]);
		$('#txt_gsm').val(data[5]);
		$('#txt_width').val(data[6]);
		$('#txt_dia_width_type').val(data[7]);
		$('#txt_machine_id').val(data[8]);

		$('#txt_color_id').val(data[9]);
		$('#txt_color').val(data[10]);
		$('#cbo_fabric_shade').val(data[11]);
		$('#cbo_uom').val(data[12]);

		$('#txt_body_part_id').val(data[13]);
		$('#txt_body_part').val(data[14]);
		$('#txt_rate').val(data[15]);
		$('#txt_cons_rate').val(data[16]);

		$('#from_store_id').val(data[17]);
		$('#from_store_name').val(data[18]);

		$('#from_floor_id').val(data[19]);
		$('#from_floor').val(data[20]);

		$('#from_room_id').val(data[21]);
		$('#from_room').val(data[22]);
		
		$('#from_rack_id').val(data[23]);
		$('#from_rack').val(data[24]);
		
		$('#from_shelf_id').val(data[25]);
		$('#from_shelf').val(data[26]);
		

		$('#txt_current_stock').val(data[27]);
		$('#txt_aop_rate').val(data[28]);

		$('#update_dtls_id').val("");
		$('#update_trans_from').val("");
		$('#update_trans_to').val("");
		$('#previous_from_batch_id').val("");
		$('#previous_to_batch_id').val("");
		$('#txt_transfer_qnty').val("");
		$('#hidden_transfer_qnty').val("");

		$('#cbo_floor').val(0);
		$('#cbo_room').val(0);
		$('#txt_rack').val(0);
		$('#txt_shelf').val(0);

		set_button_status(0, permission, 'fnc_finish_transfer_entry',1,1);
	}

	function company_on_change(company)
	{
	    var data='cbo_company_id='+company+'&action=upto_variable_settings';    

	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;
	        }
	    }
	    xmlhttp.open("POST", "requires/finish_fabric_fso_to_fso_transfer_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

	function store_update_upto_disable() 
	{
		var store_update_upto=$('#store_update_upto').val()*1;	 
		if(store_update_upto==4)
		{
			$('#txt_shelf').prop("disabled", true);
		}
		else if(store_update_upto==3)
		{
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);
		}
		else if(store_update_upto==2)
		{	
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);	
		}
		else if(store_update_upto==1)
		{
			$('#cbo_floor').prop("disabled", true);
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);	
		}          
        
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
    	<div style="width:760px; float: left;">
    	<table width="760" cellpadding="0" cellspacing="2" align="left">
	     	<tr>
	        	<td width="760" align="center" valign="top">  
			        <fieldset style="width:760px; float:left;">
			        	<legend>Finish Fabric FSO To FSO Transfer Entry</legend>
			        	<br>
			            <fieldset style="width:750px; float: left;">
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
			                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/finish_fabric_fso_to_fso_transfer_controller*2', 'store','store_td', $('#cbo_company_id').val(), '');company_on_change(this.value);" );
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
			            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls" style="float: left;">
			                <tr>
			                    <td width="65%" valign="top">
			                    	<div style="float: left; width:49%">
				                        <fieldset>
				                        	<legend>From Order</legend>
				                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
				                                <tr>
				                                    <td width="30%" class="must_entry_caption">Sales Order No</td>
				                                    <td>
				                                        <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
				                                        <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
				                                    </td>
				                                </tr>

				                                <tr>
				                                    <td>Fab. Booking No</td>
				                                    <td><input type="text" name="txt_from_booking_no" id="txt_from_booking_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>
				                                <tr>	
				                                    <td>Po Company</td>
				                                    <td><? echo create_drop_down( "cbo_from_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", '', "" ,1); ?>	  	
				                                    </td>
				                                </tr>	
				                                <tr>	
				                                    <td>Po Buyer</td>
				                                    <td><? echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>	  	
				                                    </td>
				                                </tr>						
				                                <tr>
				                                    <td>Style Ref.</td>
				                                    <td><input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>
				                                <tr>
				                                    <td>Gmts Item</td>
				                                    <td><input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>
				                                <tr>
				                                    <td>Fabric Desc.</td>
				                                    <td>
				                                    	<input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" />
				                                    	<input type="hidden" name="fabric_desc_id" id="fabric_desc_id"  />
				                                    	<input type="hidden" name="txt_product_id" id="txt_product_id"  />
				                                    	<input type="hidden" name="txt_gsm" id="txt_gsm"  />
				                                    	<input type="hidden" name="txt_width" id="txt_width"  />
				                                    	<input type="hidden" name="txt_dia_width_type" id="txt_dia_width_type"  />
				                                    	<input type="hidden" name="txt_machine_id" id="txt_machine_id"  />
				                                    </td>
				                                </tr>
				                                <tr>
				                                    <td>Body Part</td>
				                                    <td>
				                                    	<input type="text" name="txt_body_part" id="txt_body_part" class="text_boxes" style="width:150px;"  disabled="disabled" placeholder="Display" readonly/>
				                                    	<input type="hidden" name="txt_body_part_id" id="txt_body_part_id"  />
				                                    </td>
				                                </tr> 
				                                <tr>
				                                    <td class="must_entry_caption">Transfer Qnty</td>
				                                    <td>
				                                    	<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;"  onkeyup="validate_available()" />
				                                    	<input type="hidden" name="hidden_transfer_qnty" id="hidden_transfer_qnty" class="text_boxes_numeric" style="width:150px;"   />
				                                    </td>
				                                </tr>
				                                <tr>
				                                    <td>From Store</td>
				                                    <td>
				                                    	<input type="text" name="from_store_name" id="from_store_name" class="text_boxes" style="width:150px;"  disabled="disabled" placeholder="Display" readonly/>
				                                    	<input type="hidden" name="from_store_id" id="from_store_id"  />
				                                    </td>
				                                </tr>
				                                <tr>
		                                        <td>Floor</td>
		                                        <td>
		                                         <input type="text" name="from_floor" id="from_floor" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" readonly="" />
		                                         <input type="hidden" name="from_floor_id" id="from_floor_id" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" readonly="" />
		                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>Room</td>
			                                        <td>
			                                         <input type="text" name="from_room" id="from_room" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" readonly="" />
			                                          <input type="hidden" name="from_room_id" id="from_room_id" class="text_boxes" disabled="disabled" placeholder="Display" readonly="" />
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>Rack</td>
			                                        <td>
			                                        <input type="text" name="from_rack" id="from_rack" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" readonly="" />
			                                          <input type="hidden" name="from_rack_id" id="from_rack_id" class="text_boxes" disabled="disabled" placeholder="Display" readonly="" />
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>Shelf</td>
			                                        <td>
			                                        <input type="text" name="from_shelf" id="from_shelf" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" readonly="" />
			                                          <input type="hidden" name="from_shelf_id" id="from_shelf_id" class="text_boxes" disabled="disabled" placeholder="Display" readonly="" />
			                                        </td>
			                                    </tr>
				                                
				                                <tr>
				                                    <td>Color</td>
				                                    <td>
				                                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:150px;"   disabled="disabled" placeholder="Display" readonly/>
				                                    	<input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes"  />
				                                    </td>
				                                </tr>
				                                <tr>
				                                    <td>Fabric Shade</td>
				                                    <td>
														<?
														echo create_drop_down( "cbo_fabric_shade", 160, $fabric_shade,"",1, "-- Select --", 0, "",1 );
														?>
													</td>
				                                </tr>
				                                <tr>
				                                    <td>Batch No</td>
				                                    <td>
														<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:150px;"  disabled="disabled" placeholder="Display" readonly/>
				                                    	<input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes"  />
													</td>
				                                </tr>
				                            </table>
				                        </fieldset>
			                    	</div>
			                    	<div style="float: right; width:49%">
				                        <fieldset>
				                        	<legend>To Order</legend>					
				                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
				                                <tr>
				                                    <td width="30%" class="must_entry_caption">Sales Order No</td>
				                                    <td>
				                                        <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
				                                        <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
				                                    </td>
				                                </tr>
				                                <tr>
				                                    <td width="30%" class="must_entry_caption">To Store </td>
				                                    <td id="store_td">
				                                        <? 
				                                        	echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" ); 
				                                        ?>
				                                    </td>
				                                </tr>
				                                <tr>
				                                	<td width="30%" >To Floor</td>
					                                <td id="floor_td">
														<? 
															echo create_drop_down( "cbo_floor", 160, $blank_array,"",1, "--Select Floor--", 1, "" );
														?>
													</td>
												</tr>
				                                <tr>
				                                	<td width="30%" >To Room</td>
													<td id="room_td">
														<? 
															echo create_drop_down( "cbo_room", 160, $blank_array,"",1, "--Select Room--", 1, "" );
														?>
													</td>
												</tr>
				                                <tr>
				                                	<td width="30%" >To Rack</td>
													<td id="rack_td">
														<? 
															echo create_drop_down( "txt_rack", 160, $blank_array,"",1, "--Select Rack--", 1, "" );
														?>
													</td>
												</tr>
				                                <tr>
				                                	<td width="30%" >To Shelf</td>
													<td width="" id="shelf_td">
														<? 
														echo create_drop_down( "txt_shelf", 160, $blank_array,"",1, "--Select Shelf--", 1, "" );
														?>
														
													</td>
												</tr>
				                                <tr>
				                                    <td>Fab. Booking No</td>
				                                    <td><input type="text" name="txt_to_booking_no" id="txt_to_booking_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>
				                                <tr>	
				                                    <td>Po Company</td>
				                                    <td><? echo create_drop_down( "cbo_to_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", '', "" ,1); ?>	  	
				                                    </td>
				                                </tr>
				                                <tr>	
				                                    <td>Po Buyer</td>
				                                    <td><? echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>	  	
				                                    </td>
				                                </tr>						
				                                <tr>
				                                    <td>Style Ref.</td>
				                                    <td><input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>
				                                <tr>
				                                    <td>Gmts Item</td>
				                                    <td><input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
				                                </tr>										
				                            </table>                  
				                       </fieldset>	
			                   		</div>
			                    </td>
			                    <td width="1%" valign="top"></td>
			                    <td width="40%" valign="top">
									<fieldset>
			                        	<legend>Display</legend>					
			                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
			                                <tr>
			                                    <td>Current Stock</td>						
			                                    <td>
			                                    <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px" disabled />
			                                    <input type="hidden" name="hidden_current_stock" id="hidden_current_stock" readonly>
			                                    </td>
			                                </tr>
			                                <tr>
			                                    <td>Avg. Rate</td>						
			                                    <td>
			                                    	<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:150px" disabled />
			                                    	<input type="hidden" name="txt_cons_rate" id="txt_cons_rate"  />
			                                    	<input type="hidden" name="txt_aop_rate" id="txt_aop_rate"  />
			                                    </td>
			                                </tr>
			                                <tr>
			                                    <td>Transfer Value </td>
			                                    <td><input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:150px" disabled /></td>
			                                </tr>	
			                                <tr>
			                                	<td>Item Category</td>
						                        <td>
													<?
						                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',2 );
						                            ?>
						                        </td>
			                                </tr>				
			                                <tr>
			                                    <td>UOM</td>
			                                    <td>
			                                    <?
			                                    echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "-UOM-", 12, "",1,"" );
			                                    
			                                    ?>
			                                    </td>
			                                </tr>											
			                            </table>
			                       </fieldset>	
			              		</td>
			                </tr>
						</table>
			            <br>
			            <table width="700" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
			                <tr>
			                    <td align="center" class="button_container">
			                        <? 
			                        	echo load_submit_buttons($permission, "fnc_finish_transfer_entry", 0,1,"reset_form_all()",1);
			                        ?>
			                        <input type="hidden" id="update_dtls_id" name="update_dtls_id">
			                        <input type="hidden" id="update_trans_from" name="update_trans_from">
			                        <input type="hidden" id="update_trans_to" name="update_trans_to">
			                        <input type="hidden" id="previous_from_batch_id" name="previous_from_batch_id">
			                        <input type="hidden" id="previous_to_batch_id" name="previous_to_batch_id">
			                        <input type="hidden" name="store_update_upto" id="store_update_upto">
			                    </td>
			                </tr>  
			            </table>
		            </fieldset>
		            <fieldset>
		             	<div style="width:800px; float: left;" id="div_transfer_item_list"></div>
			        </fieldset>
			    </td>
	        </tr>
    	</table>
        </div>

        <div style="width:20px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		<div id="list_fabric_desc_container" style="width:635px; float:left; padding-top:5px; margin-top:5px; position:relative; overflow:auto;"></div>
	
	</form>
</div>   
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
