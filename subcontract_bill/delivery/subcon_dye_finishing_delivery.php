<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Dye & Finishing Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	11-11-2014
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
echo load_html_head_contents("Dye & Finishing Delivery Info","../../", 1, 1, '','','');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_order()
	{
		if( form_validation('cbo_company_name*cbo_party_name','Company Name*Party')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value;
			var page_link = 'requires/subcon_dye_finishing_delivery_controller.php?data='+data+'&action=order_number_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link,'Order Number Form', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidden_order_value");//po id
				var ret_value=theemail.value.split("_");
				if (ret_value[0]!="")
				{
					//freeze_window(5);
					get_php_form_data(ret_value[0], "populate_data_from_search_popup", "requires/subcon_dye_finishing_delivery_controller" );

					//load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value, 'load_drop_down_item', 'item_td' );
					show_list_view(document.getElementById('txt_order_id').value+"_"+document.getElementById('cbo_process_name').value+"_"+document.getElementById('cbo_company_name').value+"_"+'2','show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dye_finishing_delivery_controller','');
					reset_form('','','txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_remarks*txt_reject_qty');
					release_freezing();
				}
			}
		}
	}
	
	function fnc_subcon_delivery_entry(operation)
	{
		var prod_qty=($("#txt_production_qnty").val()*1)+$("#txt_pre_delivery_qnty").val()*1;
		var delivery_qty=$("#txt_delivery_qnty").val()*1;
		//alert (prod_qty);
		
		if( form_validation('cbo_company_name*cbo_party_name*txt_order_no*txt_delivery_date*txt_dalivery_item*txt_delivery_qnty*txt_gray_qnty','Company Name*Party*Order Number*Delivery Date*Dalivery Item*Delivery Qnty*Gray Used Qnty')==false )
		{
			return;
		} 
		var bill_info=$("#bill_info").val();
		var bill_status=bill_info.split('**');
		
		if (bill_status[0]!=0 || bill_status[0]!='')
		{
			alert ('This Delivery item already Bill Issue. Bill No is ='+bill_status[1]);
			return;
		}
		
		if (delivery_qty>prod_qty)
		{
			alert ('Delivery Qty is over from production.');
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sys_id*cbo_company_name*cbo_location*txt_order_id*cbo_party_name*cbo_process_name*txt_delivery_date*txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_challan_no*txt_transport_company*cbo_forwarder*txt_remarks*update_id*update_id_dtls*txt_gsm*txt_dia*txt_color_id*txt_batch_id*hidden_dia_type*hid_sub_process*txt_gray_qnty*txt_reject_qty*txt_moisture_gain*txt_vehical_no*txt_driver_name*txt_mobile_no*txt_remark*collarAndCuffStr',"../");
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/subcon_dye_finishing_delivery_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_subcon_delivery_entry_respone;
		}
	}
	
	function fnc_subcon_delivery_entry_respone()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]==11)
			{
				release_freezing();
				alert(reponse[1]);return;
			}	
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[2];
				document.getElementById('txt_challan_no').value = reponse[3];
				show_list_view(reponse[1],'delivery_entry_list_view','delivery_entry_list_view','requires/subcon_dye_finishing_delivery_controller','setFilterGrid("list_view",-1)');
				reset_form('','','txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_remarks*update_id_dtls*txt_production_qnty*txt_color*txt_color_id*txt_batch_no*txt_ext_no*txt_batch_id*txt_gsm*txt_sub_process*hid_sub_process*txt_dia*hidden_dia_type*bill_info*txt_dalivery_item*txt_pre_delivery_qnty*txt_gray_qnty*txt_reject_qty*txt_moisture_gain');
				//$('#list_fabric_desc_container').html('');
				$('#cbo_party_name').attr('disabled','disabled');
				show_list_view(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value+"_"+document.getElementById('cbo_company_name').value+"_"+'2', 'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dye_finishing_delivery_controller','');
			}
			set_button_status(0, permission, 'fnc_subcon_delivery_entry',1,0);	
			release_freezing();	
		}
	}
	
	function openpage_delivery_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value;
		var page_link='requires/subcon_dye_finishing_delivery_controller.php?action=delivery_id_popup&data='+data
		var title='Subcontract Delivery';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=850px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_delivery_id");
			//alert (theemail.value);return;
			if (theemail.value!="")
			{
				//var ret_value=theemail.value.split("_");
				
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/subcon_dye_finishing_delivery_controller" );
				show_list_view(theemail.value,'delivery_entry_list_view','delivery_entry_list_view','requires/subcon_dye_finishing_delivery_controller','setFilterGrid("list_view",-1)');
				reset_form('','','txt_order_id*txt_order_no*cbo_process_name*txt_order_date*txt_ordr_qnty*txt_uom*txt_style*txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_remarks*update_id_dtls*txt_gsm*txt_dia*txt_color_id*txt_batch_no*txt_ext_no*txt_batch_id*hid_sub_process*hidden_dia_type*txt_production_qnty*txt_color*txt_color_id*txt_sub_process*hid_sub_process*bill_info*txt_dalivery_item*txt_reject_qty*txt_moisture_gain');
				$('#list_fabric_desc_container').html('');
				set_button_status(0, permission, 'fnc_subcon_delivery_entry',1,0);
				
				release_freezing();
			}
		}
	}
	
	function qty_validation(val,field_id)
	{
		var order_id=document.getElementById('txt_order_id').value;
		var process_id=document.getElementById('cbo_process_name').value;
		var item_id=document.getElementById('delivery_item_id').value;
		var data=order_id+'**'+process_id+'**'+item_id+'**'+val;
		var response=return_global_ajax_value(data, 'delivery_qty_check', '', 'requires/subcon_dye_finishing_delivery_controller');
		var response_value=response.split('_');
		//alert (field_id)
		var tot_qty=response_value[0]-response_value[1];
		if (response_value[0]<val && val>tot_qty )
		{
			alert ("Qnty Excceded From Production.");
			//$("#txt_delivery_qnty").val('');
			$('#'+field_id).val('');
			$('#'+field_id).select();
			$('#'+field_id).focus();
			return;
		}
	}
	
	function fnc_production_qty(val)
	{
		//alert (val)
		var response=return_global_ajax_value( document.getElementById('txt_order_id').value+"_"+document.getElementById('cbo_process_name').value+"_"+val, 'populate_data_production_qty', '', 'requires/subcon_dye_finishing_delivery_controller');

		var response=response.split("_");
		if(response[0]==1)
		{
			$("#txt_production_qnty").val( response[1] );
		}
		else
		{
			$("#txt_production_qnty").val('');
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#txt_item_id').val(data[0]);
		$('#txt_dalivery_item').val(data[1]);
		$('#txt_production_qnty').val(data[2]);
		$('#txt_gsm').val(data[3]);
		$('#txt_dia').val(data[4]);
		$('#txt_batch_no').val(data[5]);
		$('#txt_ext_no').val(data[6]);
		$('#txt_color_id').val(data[7]);
		$('#txt_color').val(data[8]);
		$('#hidden_dia_type').val(data[9]);
		$('#hid_sub_process').val(data[10]);
		//openmypage_qnty();txt_gsm*txt_dia
	}
	
	function active_inactive(val)
	{
		if(val!='' || val!=0)
		{
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_location').attr('disabled','disabled');
			$('#txt_challan_no').attr('readOnly','readOnly');
			$('#txt_delivery_date').attr('disabled','disabled');
			$('#cbo_forwarder').attr('disabled','disabled');
			$('#txt_transport_company').attr('readOnly','readOnly');
			$('#txt_order_no').attr('disabled','disabled');
			$('#txt_delivery_qnty').attr('readOnly','readOnly');
			$('#txt_carton_roll_no').attr('readOnly','readOnly');
			$('#txt_remarks').attr('readOnly','readOnly');
		}
		else
		{
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$('#cbo_location').removeAttr('disabled','disabled');
			$('#txt_challan_no').removeAttr('readOnly','readOnly');
			$('#txt_delivery_date').removeAttr('disabled','disabled');
			$('#cbo_forwarder').removeAttr('disabled','disabled');
			$('#txt_transport_company').removeAttr('readOnly','readOnly');
			$('#txt_order_no').removeAttr('disabled','disabled');
			$('#txt_delivery_qnty').removeAttr('readOnly','readOnly');
			$('#txt_carton_roll_no').removeAttr('readOnly','readOnly');
			$('#txt_remarks').removeAttr('readOnly','readOnly');
		}
	}
	
	function generate_report(type)
	{
		if ( $('#txt_sys_id').val()=='')
		{
			alert ('Delivery Not Save.');
			return;
		}
		else
		{		
			var report_title=$( "div.form_caption" ).html();
			 
			if(type == 3)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_location').val(), "delivery_entry_without_gp2_print", "requires/subcon_dye_finishing_delivery_controller" ) 
			}
			else if(type == 5)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val(), "subcon_delivery_entry_print5", "requires/subcon_dye_finishing_delivery_controller" ) 
			}
			else
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_location').val(), "subcon_delivery_entry_print", "requires/subcon_dye_finishing_delivery_controller" ) 
			}
			//return;
			show_msg("3");
		}
	}
	function validate_gray_qty()
	{
		var txt_delivery_qnty=$('#txt_delivery_qnty').val()*1;
		var txt_gray_qnty=$('#txt_gray_qnty').val()*1;

		if($('#txt_cumullative_gray_qnty').val()=="")
		{
			if(txt_gray_qnty>($('#txt_batch_weight').val()*1))
			{
				alert("Gray Used Quantity can't be more than Batch Quantity");
				$('#txt_gray_qnty').val("");
			}

			validate_delivery_qty();
			return;
		}

		var valide_qty = ($('#txt_batch_weight').val()*1)-($('#txt_cumullative_gray_qnty').val()*1);
		if(((txt_gray_qnty <= valide_qty) && (txt_gray_qnty >= txt_delivery_qnty))==false)
		{
			alert("Gray Used Quantity Can be Given maximum = "+ valide_qty+ " and not less than Delivery Quantity");
			$('#txt_gray_qnty').val("");
			$('#txt_delivery_qnty').val("");
		}

	}

	function validate_delivery_qty()
	{
		var txt_delivery_qnty=$('#txt_delivery_qnty').val()*1;
		var txt_gray_qnty=$('#txt_gray_qnty').val()*1;
		
		if($('#txt_gray_qnty').val() != "")
		{
			if(txt_delivery_qnty > txt_gray_qnty)
			{
				alert("Delivery Quantity can't be more than Gray Used Quantity");
				$('#txt_delivery_qnty').val("");
			}
		}
	}
	function openmypage_collar_and_cuff()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		 
		collarAndCuffStr = $('#collarAndCuffStr').val();
		hidden_serial_id = $('#update_id').val();
		hiddin_job_no= $('#txt_batch_id').val();
		var title = 'Coller & Cuff Mesurement Info';	
	/* 	var page_link = 'requires/subcon_dye_finishing_delivery_controller.php?hiddin_job_no='+hiddin_job_no+'&hidden_serial_id='+hidden_serial_id+'action=collar_and_cuff_popup&collarAndCuffStr='+collarAndCuffStr; */
		var page_link = 'requires/subcon_dye_finishing_delivery_controller.php?hiddin_job_no='+hiddin_job_no+'&hidden_serial_id='+hidden_serial_id+'&action=collar_and_cuff_popup&collarAndCuffStr='+collarAndCuffStr;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window 
			var data=this.contentDoc.getElementById("hide_data").value; //Access form field with id="emailfield"
			$('#collarAndCuffStr').val(data);
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;">
        <div style="width:810px;" align="center">
         <? echo load_freeze_divs ("../../",$permission);  ?>
        </div>
        <div style="width:800px; float:left;" align="center">  
        <fieldset style="width:800px"> 
        <legend>Dye & Finishing Delivery Entry</legend>
        <form name="deliveryentry_1" id="deliveryentry_1" action=""  autocomplete="off">
            <fieldset>
                <table width="100%">
                    <tr>
                        <td align="right" colspan="3"><strong> System ID </strong></td>
                        <td width="140" align="justify">
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openpage_delivery_id();" readonly >
                        </td>
                    </tr>
                     <tr>
                        <td width="100" class="must_entry_caption">Company </td>
                        <td width="140">
                            <?
                                echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3)  order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', this.value, 'load_drop_down_party_name', 'party_td' );load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', this.value, 'print_report_setting', 'button_list');",0 );	

                            ?>
                        </td>
                        <td width="100">Location</td>
                        <td width="140" id="location_td">
                             <?
                            echo create_drop_down( "cbo_location", 152, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );	
                             ?> 
                        </td>
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="party_td">
                            <?
                            echo create_drop_down( "cbo_party_name", 152,  $blank_array,"", 1, "-- Select Party --", $selected, "",'' ); 
                            ?>
                        </td>
                     </tr>
                    <tr>
                        <td>Challan No</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes_numeric" style="width:140px;" placeholder="Write Or Auto Create" />
                        </td>
                        <td class="must_entry_caption">Delivery Date</td>
                        <td>
                            <input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" placeholder="Date" readonly />
                        </td>
                        <td>Forwarder</td>
                        <td>
                             <?
                                  echo create_drop_down( "cbo_forwarder", 152, "select b.id,b.supplier_name from lib_supplier_party_type a, lib_supplier b where b.id=a.supplier_id and a.party_type in (30,32) group by b.id,b.supplier_name","id,supplier_name", 1, "-- Select Forwarder --", $selected, "",0 );	
                             ?> 
                        </td>
                    </tr>
                    <tr>
                        <td>Transport Company</td>
                        <td>
                            <input type="text" name="txt_transport_company" id="txt_transport_company" class="text_boxes" placeholder="Transport Company" style="width:140px;" />
                        </td>
						<td>Vehicle No</td>
						<td>
							<input type="text" name="txt_vehical_no" id="txt_vehical_no" class="text_boxes" placeholder="Vehicle No" style="width:140px;" />
						</td>
						<td>Driver Name</td>
                        <td>
                        	<input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" placeholder="Driver Name" style="width:140px;" />
                        </td> 
					</tr>                       
					<tr>   
						<td> Mobile No.</td>
                        <td>
                        	<input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" placeholder="Mobile No." style="width:140px;" />
                        </td>
						<td> Remarks</td>
                        <td>
                        	<input type="text" name="txt_remark" id="txt_remark" class="text_boxes" placeholder="write" style="width:140px;" />
                        </td>
                    </tr>

                 </table>
            </fieldset>
            &nbsp;
            <table cellpadding="0" cellspacing="1" width="800" align="left">
            	<tr><td colspan="4">&nbsp;</td></tr>
            	<tr>
                	<td width="60%" valign="top" >
                    	<fieldset>
                        <legend>New Entry</legend>
                        	 <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td class="must_entry_caption"  width="100">Order No.</td>
                                    <td width="120"><input type="hidden" id="txt_order_id"> 
                                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px;" placeholder="Click to search" onDblClick="openmypage_order();" readonly />
                                    </td>
                                    
                                	<td width="100" class="must_entry_caption">Delivery Item</td><input type="hidden" id="delivery_item_id">
                                	<td width="120" id="item_td">
                                        <input type="text" name="txt_dalivery_item" id="txt_dalivery_item" class="text_boxes" style="width:120px;" readonly />
                                        <input type="hidden" id="txt_item_id"> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>Process</td>
                                    <td>
                                        <? echo create_drop_down( "cbo_process_name", 130, $production_process,"", 1, "--Select Process--",$selected,"", "1","" );?>
                                    </td>
                                	<td class="must_entry_caption">Delivery Qty</td>
                                    <td>
                                    	<input type="text" name="txt_delivery_qnty" id="txt_delivery_qnty" class="text_boxes_numeric" style="width:120px;" onKeyUp="validate_delivery_qty();"/>
                                        <input type="hidden" name="txt_pre_delivery_qnty" id="txt_pre_delivery_qnty" class="text_boxes_numeric" style="width:60px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order Date</td>
                                    <td>
                                        <input type="text" name="txt_order_date" id="txt_order_date" class="datepicker" style="width:120px;" disabled />
                                    </td>

                                    <td class="must_entry_caption">Gray Used Qty</td>
                                    <td>
                                    	<input type="text" name="txt_gray_qnty" id="txt_gray_qnty" class="text_boxes_numeric" style="width:120px;" onBlur="validate_gray_qty();"/>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Order Qty</td>
                                    <td>
                                        <input type="text" name="txt_ordr_qnty" id="txt_ordr_qnty" class="text_boxes_numeric" style="width:70px;" disabled />
                                        <input type="text" name="txt_uom" id="txt_uom" class="text_boxes" style="width:30px;" disabled />
                                    </td>
                                    <td>Style</td>
                                    <td>
                                        <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:120px;" disabled />
                                    </td>
                                 </tr>

                                 <tr>
                                 	<td >No of Carton/Roll</td>
                                    <td>
                                    	<input type="text" name="txt_carton_roll_no" id="txt_carton_roll_no" class="text_boxes_numeric" style="width:120px;" />
                                    </td>
                                    <td>Reject Qty</td>
                                    <td>
                                        <input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:120px;" />
                                    </td>
                                 </tr>
                                <tr>
                                    <td>Moisture Gain</td>
                                    <td><input type="text" name="txt_moisture_gain" id="txt_moisture_gain" class="text_boxes_numeric" style="width:120px;" /></td>
									<td>Collar and Cuff</td>
                                    <td><input type="button" class="formbutton" style="width:120px" value="Browse" onClick="openmypage_collar_and_cuff()"><input type="hidden" name="collarAndCuffStr" id="collarAndCuffStr">
									<input type="hidden" name="collarAndCuffStr" id="collarAndCuffStr">
									<input type="hidden" name="hidden_serial_id" id="hidden_serial_id" value=""  class="text_boxes">
									<input type="hidden" name="hiddin_job_no" id="hiddin_job_no" value=""  class="text_boxes">
									</td>
                                </tr>
                                <tr>
                                	<td>Remarks</td>
                                    <td colspan="3">
                                    	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:355px;" maxlength="150" title="Maximum 150 Character" />
                                    </td>
                                </tr>
                             </table>
                        </fieldset>
                    </td>
                    <td width="2%" valign="top">&nbsp;</td>
                    <td width="35%" valign="top">
                    	<fieldset>
                        <legend>Display</legend>
                        	<table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                	<td width="120" colspan="2">Balance Qty</td>
                                    <td width="100" colspan="2">  <input type="hidden" name="bill_info" id="bill_info" class="text_boxes" style="width:30px;" />
                                    	<input type="text" name="txt_production_qnty" id="txt_production_qnty" class="text_boxes" style="" disabled />
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="2">Color</td>
                                    <td colspan="2">
                                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="" disabled />
                                        <input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" style="width:30px;" />
                                    </td>
                                </tr>
                            	<tr>
                                	<td>Batch</td>
                                    <td>
                                    	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px;" disabled />
                                        <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" style="width:40px;" />
                                    </td>
                                	<td>Ext:</td>
                                    <td>
                                    	<input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes" style="width:77px;" disabled />
                                    </td>
                                </tr>
                                <tr>
                                	<td>Batch Weight</td>
                                	<td>
                                		<input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:60px;" disabled/>
                                	</td>
                                	<td>Total Gray</td>
                                	<td>
                                		<input type="text" name="txt_cumullative_gray_qnty" id="txt_cumullative_gray_qnty" class="text_boxes_numeric" style="width:77px;" disabled/>
                                	</td>
                                </tr>
                                <tr>
                                	<td>GSM</td>
                                    <td>
                                    	<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes" style="width:60px;" disabled />
                                    </td>
                                	<td>Dia</td>
                                    <td>
                                    	<input type="text" name="txt_dia" id="txt_dia" class="text_boxes" style="width:77px;" disabled />
                                        <input type="hidden" name="hidden_dia_type" id="hidden_dia_type" class="text_boxes" style="width:30px;" />
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="1">Sub-Pro.</td>
                                    <td colspan="3">
                                    	<input type="text" name="txt_sub_process" id="txt_sub_process" class="text_boxes" style="width:200px;" disabled />
                                        <input type="hidden" name="hid_sub_process" id="hid_sub_process" class="text_boxes" style="width:40px;" />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="3%" valign="top">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="4" valign="middle" class="button_container">
                		
                		<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
						<?
                        echo load_submit_buttons( $permission, "fnc_subcon_delivery_entry", 0,0 ,"reset_form('deliveryentry_1','delivery_entry_list_view','','','')",1); 
                        ?><input type="hidden" id="update_id_dtls">
						<p id="button_list"></p>
                       
                    </td>

                    
                </tr>
                <tr>
                	<td colspan="4">&nbsp;</td>
                </tr>
            </table>
            <fieldset><div style="width:800px;" id="delivery_entry_list_view" ></div></fieldset>
        </form>
        </fieldset>
		</div>   
		<div id="list_fabric_desc_container" style="max-height:600px; overflow:auto; float:left; padding-top:5px; margin-top:5px; margin-left:20px; position:relative;"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>