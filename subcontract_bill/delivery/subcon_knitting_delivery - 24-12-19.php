<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Knitting Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	01-11-2014
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
echo load_html_head_contents("Knitting Delivery Info","../../", 1, 1, '','','');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_order()
	{
		if( form_validation('cbo_company_name*cbo_party_name','Company Name*Party')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value;
			var page_link = 'requires/subcon_knitting_delivery_controller.php?data='+data+'&action=order_number_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link,'Order Number Form', 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidden_order_value");//po id
				var ret_value=theemail.value.split("_");
				if (ret_value[0]!="")
				{
					//freeze_window(5);
					get_php_form_data(ret_value[0], "populate_data_from_search_popup", "requires/subcon_knitting_delivery_controller" );

					//load_drop_down( 'requires/subcon_delivery_entry_controller', document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value, 'load_drop_down_item', 'item_td' );
					show_list_view(document.getElementById('txt_order_id').value+"_"+document.getElementById('cbo_process_name').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_knitting_delivery_controller','');
					reset_form('','','txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_remarks*txt_delivery_pcs*txt_reject_qty');
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

		var isDisabled = $('#txt_collar_cuff_mgt').prop('disabled');
		if(isDisabled==false)
		{
			if( form_validation('txt_collar_cuff_mgt*txt_delivery_pcs','Collar Cuff Measurement*Delivery Qty Pcs')==false )
			{
				return;
			}
		}

		if( form_validation('cbo_company_name*cbo_party_name*txt_order_no*txt_delivery_date*txt_dalivery_item*txt_delivery_qnty','Company Name*Party*Order Number*Delivery Date*Dalivery Item*Delivery Qty')==false )
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
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sys_id*cbo_company_name*cbo_location*txt_order_id*cbo_party_name*cbo_process_name*txt_delivery_date*txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_challan_no*txt_transport_company*txt_vehical_no*cbo_forwarder*txt_collar_cuff_mgt*txt_remarks*update_id*update_id_dtls*txt_gsm*txt_dia*txt_dia_type*txt_color_id*txt_lot*txt_delivery_pcs*txt_reject_qty*txt_grey_used',"../");
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/subcon_knitting_delivery_controller.php",true);
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
			release_freezing();		
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				return;
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(reponse[0]);
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[2];
				document.getElementById('txt_challan_no').value = reponse[3];
				show_list_view(reponse[1],'delivery_entry_list_view','delivery_entry_list_view','requires/subcon_knitting_delivery_controller','setFilterGrid("list_view",-1)');
				reset_form('','','txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_collar_cuff_mgt*txt_remarks*update_id_dtls*txt_production_qnty*txt_cal_order_qnty*txt_color*txt_color_id*txt_gsm*txt_dia*txt_dia_type*bill_info*txt_dalivery_item*txt_pre_delivery_qnty*txt_matchine_neme*txt_count*txt_stitch_length*txt_lot*txt_delivery_pcs*txt_reject_qty*txt_grey_used');
				//$('#list_fabric_desc_container').html('');
				$('#cbo_party_name').attr('disabled','disabled');
				show_list_view(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value, 'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_knitting_delivery_controller','');
			}
			else
			{
				return;
			}
			set_button_status(0, permission, 'fnc_subcon_delivery_entry',1,0);	
		}
	}

	function openpage_delivery_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value;
		var page_link='requires/subcon_knitting_delivery_controller.php?action=delivery_id_popup&data='+data
		var title='Subcontract Delivery';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=780px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_delivery_id");
			//alert (theemail.value);return;
			if (theemail.value!="")
			{
				//var ret_value=theemail.value.split("_");
				
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/subcon_knitting_delivery_controller" );
				show_list_view(theemail.value,'delivery_entry_list_view','delivery_entry_list_view','requires/subcon_knitting_delivery_controller','setFilterGrid("list_view",-1)');
				reset_form('','','txt_order_id*txt_order_no*cbo_process_name*txt_order_date*txt_ordr_qnty*txt_cal_order_qnty*txt_uom*txt_style*txt_item_id*txt_delivery_qnty*txt_carton_roll_no*txt_collar_cuff_mgt*txt_remarks*update_id_dtls*txt_gsm*txt_dia*txt_dia_type*txt_color_id*txt_production_qnty*txt_color*txt_color_id*bill_info*txt_dalivery_item*txt_matchine_neme*txt_count*txt_stitch_length*txt_lot*txt_delivery_pcs*txt_reject_qty');
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
		var response=return_global_ajax_value(data, 'delivery_qty_check', '', 'requires/subcon_knitting_delivery_controller');
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
		var response=return_global_ajax_value( document.getElementById('txt_order_id').value+"_"+document.getElementById('cbo_process_name').value+"_"+val, 'populate_data_production_qty', '', 'requires/subcon_knitting_delivery_controller');

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
	
	/*function set_form_data(data)
	{
		var data=data.split("**");
		$('#txt_item_id').val(data[0]);
		$('#txt_dalivery_item').val(data[1]);
		$('#txt_production_qnty').val(data[2]);
		$('#txt_gsm').val(data[3]);
		$('#txt_dia').val(data[4]);
		$('#txt_matchine_neme').val(data[5]);
		$('#txt_count').val(data[6]);
		$('#txt_color_id').val(data[7]);
		$('#txt_color').val(data[8]);
		$('#txt_stitch_length').val(data[9]);
		$('#txt_lot').val(data[10]);
		//openmypage_qnty();txt_gsm*txt_dia
	}*/
	
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
			$('#txt_vehical_no').attr('readOnly','readOnly');
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
			$('#txt_vehical_no').removeAttr('readOnly','readOnly');
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
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type, "subcon_delivery_entry_print", "requires/subcon_knitting_delivery_controller" ) 
			//return;
			show_msg("3");
		}
	}
	
	function generate_report_short(type)
	{
		if ( $('#txt_sys_id').val()=='')
		{
			alert ('Delivery Not Save.');
			return;
		}
		else
		{		
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type, "knitting_delivery_print", "requires/subcon_knitting_delivery_controller" ) 
			//return;
			show_msg("3");
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
        <legend>Knitting Delivery Entry</legend>
        <form name="deliveryentry_1" id="deliveryentry_1" action=""  autocomplete="off">
            <fieldset>
                <table width="100%">
                    <tr>
                        <td align="right" colspan="3"><strong>System ID</strong></td>
                        <td width="140" align="justify">
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openpage_delivery_id();" readonly >
                        </td>
                    </tr>
                     <tr>
                        <td width="100" class="must_entry_caption">Company </td>
                        <td width="140">
                            <?
                                echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_knitting_delivery_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/subcon_knitting_delivery_controller', this.value, 'load_drop_down_party_name', 'party_td' );",0 );	
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                 </table>
            </fieldset>
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
                                	<td class="must_entry_caption">Delivery Qty Wgt / Pcs</td>
                                    <td>
                                    	<input type="text" name="txt_delivery_qnty" id="txt_delivery_qnty" class="text_boxes_numeric" style="width:52px;" placeholder="wgt"/>

                                    	<input type="text" name="txt_delivery_pcs" id="txt_delivery_pcs" class="text_boxes_numeric" style="width:52px;" placeholder="pcs"/>

                                        <input type="hidden" name="txt_pre_delivery_qnty" id="txt_pre_delivery_qnty" class="text_boxes_numeric" style="width:60px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order Date</td>
                                    <td>
                                        <input type="text" name="txt_order_date" id="txt_order_date" class="datepicker" style="width:120px;" disabled />
                                    </td>
                                	<td >No of Carton/Roll</td>
                                    <td>
                                    	<input type="text" name="txt_carton_roll_no" id="txt_carton_roll_no" class="text_boxes_numeric" style="width:52px;" />
                                    	<input type="text" name="txt_grey_used" id="txt_grey_used" class="text_boxes_numeric" style="width:52px;" placeholder="Yarn Used">
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
                                    <td>Collar Cuff Measurement</td>
                                    <td>
                                        <input type="text" name="txt_collar_cuff_mgt" id="txt_collar_cuff_mgt" class="text_boxes" style="width:120px;" />
                                    </td>
                                    <td>Reject Qty</td>
                                    <td>
                                        <input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:120px;" />
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
                                	<td width="120" colspan="2">Production Qty</td>
                                    <td width="100" colspan="2">  <input type="hidden" name="bill_info" id="bill_info" class="text_boxes" style="width:30px;" />
                                    	<input type="text" name="txt_production_qnty" id="txt_production_qnty" class="text_boxes" style="width:100px;" disabled />
                                    </td>
                                </tr>
                                <tr>
                                	<td width="120" colspan="2">Bal. Order Qty</td>
                                    <td width="100" colspan="2">
                                    	<input type="text" name="txt_cal_order_qnty" id="txt_cal_order_qnty" class="text_boxes" style="width:100px;" disabled />
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="2">Fab. Color</td>
                                    <td colspan="2">
                                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px;" disabled />
                                        <input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" style="width:30px;" />
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
                                        <input type="hidden" name="txt_dia_type" id="txt_dia_type" class="text_boxes" style="width:37px;" />
                                    </td>
                                </tr>
                            	<tr>
                                	<td>Machine</td>
                                    <td>
                                    	<input type="text" name="txt_matchine_neme" id="txt_matchine_neme" class="text_boxes" style="width:60px;" disabled />
                                    </td>
                                	<td>Y.Count</td>
                                    <td>
                                    	<input type="text" name="txt_count" id="txt_count" class="text_boxes" style="width:77px;" disabled />
                                    </td>
                                </tr>
                            	<tr>
                                	<td>S.Length</td>
                                    <td>
                                    	<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:60px;" disabled />
                                    </td>
                                	<td>Lot</td>
                                    <td>
                                    	<input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:77px;" disabled />
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
						<?
                        echo load_submit_buttons( $permission, "fnc_subcon_delivery_entry", 0,0 ,"reset_form('deliveryentry_1','delivery_entry_list_view','','','')",1); 
                        ?><input type="hidden" id="update_id_dtls">
                        <input type="button" name="search" id="search" value="With Gate Pass" onClick="generate_report(1)" style="width:100px" class="formbuttonplasminus" />
                        <input type="button" name="search" id="search" value="WithOut G.Pass" onClick="generate_report(2)" style="width:100px" class="formbuttonplasminus" />
                        <input type="button" name="search" id="search" value="Print Short" onClick="generate_report_short(3)" style="width:100px" class="formbuttonplasminus" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4">&nbsp;</td>
                </tr>
            </table>
        </form>
        </fieldset>
        <br>
            <div style="width:800px;" id="delivery_entry_list_view" ></div>
		</div>   
		<div id="list_fabric_desc_container" style="max-height:500px; overflow:auto; float:left; padding-top:5px; margin-top:5px; margin-left:20px; position:relative;"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
