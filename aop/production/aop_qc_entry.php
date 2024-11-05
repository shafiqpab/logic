<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create AOP QC Entry
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	27-03-2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:			
Convert date	: 		   
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
echo load_html_head_contents("AOP production", "../../",1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$("#txt_color").autocomplete({
			source: str_color
			});
		}
	}

	function openmypage_production()
	{ 
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var data=$('#cbo_company_id').val()+"_"+$('#cbo_party_name').val();
			emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/aop_qc_entry_controller.php?action=production_popup&data='+data,'Production Popup', 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("production_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					//alert(theemail.value);
					get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/aop_qc_entry_controller" );
					show_list_view(theemail.value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');
					//show_list_view(theemail.value,'fabric_finishing_list_view','fabric_finishing_list_view','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');
					
					//document.getElementById('cbo_receive_basis').disabled=true;
					//document.getElementById('cbo_company_id').disabled=true;
					//document.getElementById('cbo_party_name').disabled=true;
					reset_form('','','txt_batch_no*txt_batch_ext_no*order_no_id*txt_process_id*txt_description*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id*txt_qc_qnty*txt_buyer_po*txt_buyer_po_id*txt_buyer_style*txt_remarks*cbo_body_part*cboShift');
					$('#txt_process_id').focus();
					set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
					release_freezing();
				}
			}
		}
	}
	function openmypage_qc()
	{ 
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var cbo_company_id=$('#cbo_company_id').val();
			var cbo_party_name=$('#cbo_party_name').val();
			var data=cbo_company_id+"_"+cbo_party_name;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/aop_qc_entry_controller.php?action=qc_popup&data='+data,'QC Popup Info', 'width=800px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("qc_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					//var datas=theemail.value.split('_');
					//$('#txt_production_id').val(theemail.value);
					//document.getElementById('txt_production_id').val(theemail.value);
					get_php_form_data(theemail.value, "load_qc_data_to_form_mst", "requires/aop_qc_entry_controller" );
					//calculate_gain_loss();
					//alert(theemail.value);
					show_list_view(theemail.value,'fabric_finishing_list_view','fabric_finishing_list_view','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');
					//show_list_view(theemail.value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');

					/*var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_qc_id*cbo_receive_basis*cbo_company_id*cbo_location_name*cbo_party_name*txt_qc_date*txt_chal_no*txt_remarks*update_id*txt_batch_id*txt_production_id*txt_batch_no*txt_batch_ext_no*hidden_dia_type*txt_process_id*order_no_id*item_order_id*txt_description*comp_id*txt_color*hidden_color_id*txt_gsm*txt_dia_width*txt_product_qnty*cbo_floor_name*cbo_machine_id*cboShift*cbo_uom*update_id_dtl*txt_qc_qnty*txt_reject_qty*txt_roll_no*update_id_qnty*cbo_body_part*txt_buyer_po_id',"../../");*/

					reset_form('','list_fabric_desc_container','txt_batch_no*txt_batch_ext_no*order_no_id*txt_process_id*txt_description*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id*txt_qc_qnty*txt_buyer_po*txt_buyer_po_id*txt_buyer_style*txt_remarks*cbo_body_part*cboShift');
					document.getElementById('cbo_receive_basis').disabled=true;
					document.getElementById('cbo_company_id').disabled=true;
					document.getElementById('cbo_party_name').disabled=true;
					$('#txt_process_id').focus();
					set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
					release_freezing();
				}
			}
		}
	}

	/*function openmypage_batchno()
	{ 
		if ( form_validation('cbo_company_id*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('txt_process_id').value;
			emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/aop_qc_entry_controller.php?action=batch_numbers_popup&data='+data,'Order Numbers Popup', 'width=800px, height=420px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("selected_batch_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					reset_form('','','txt_process_id*txt_description*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id');
					get_php_form_data( theemail.value, "load_php_data_to_form_batch", "requires/aop_qc_entry_controller" );
					
					show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value+"_"+document.getElementById('txt_batch_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','');
					//document.getElementById('txt_receive_qnty').value="";
					//document.getElementById('txt_product_qnty').value="";
					release_freezing();
				}
			}
		}
	}*/

	function openmypage_qnty(order_no_id)
	{
		var data=order_no_id+"_"+document.getElementById('update_id_dtl').value+"_"+document.getElementById('update_check').value;
		//alert (data);return;
		var title = 'Order Qnty Info';	
		var page_link = 'requires/aop_qc_entry_controller.php?action=order_qnty_popup&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
		var receive_qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot"); 
		var receive_qnty=this.contentDoc.getElementById("hidden_qnty"); 
		var receive_tbl_id=this.contentDoc.getElementById("hidd_qnty_tbl_id"); 
		//alert (receive_tbl_id.value);return;
		$('#txt_product_qnty').val(receive_qnty_tot.value);
		//$('#txt_receive_qnty').val(receive_qnty.value);
		$('#update_id_qnty').val(receive_tbl_id.value);
		
			if(document.getElementById('update_check').value==1)
			{
				document.getElementById('update_id_qnty').value="";
			}
		}
	}

	function subcon_fabric_finishing(operation)
	{
		//alert (operation)
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_qc_id').val()+'*'+report_title, "subcon_fabric_finishing_print", "requires/aop_qc_entry_controller" ) 
			 return;
		}
		else if(operation==2)
		{
			show_msg('13');
			return;
		}
		else if(operation==0 || operation==1)
		{
			//if( form_validation('cbo_company_id*cbo_party_name*txt_qc_date*txt_gsm*txt_dia_width','Company Name*Party Name*Production Date*GSM*Dia/Width')==false )
			var cbo_company_id=$('#cbo_company_id').val();
			var response=return_global_ajax_value(cbo_company_id, 'check_qty_is_mandatory', '', 'requires/aop_qc_entry_controller');
			
				if(response==1)
				{
					if( form_validation('cbo_company_id*txt_qc_qnty','Company Name*QC Qty')==false )
					{
						return;
					}
				}
				else
				{
					if( form_validation('cbo_company_id','Company Name')==false )
					{
						return;
					}
				}
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_qc_id*cbo_receive_basis*cbo_company_id*cbo_location_name*cbo_party_name*txt_qc_date*txt_chal_no*txt_remarks*update_id*txt_batch_id*txt_production_id*txt_batch_no*txt_batch_ext_no*hidden_dia_type*txt_process_id*order_no_id*item_order_id*txt_description*comp_id*txt_color*hidden_color_id*txt_gsm*txt_dia_width*txt_product_qnty*cbo_floor_name*cbo_machine_id*cboShift*cbo_uom*update_id_dtl*txt_qc_qnty*txt_reject_qty*txt_roll_no*update_id_qnty*cbo_body_part*txt_buyer_po_id*hid_within_group',"../../");
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/aop_qc_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = subcon_fabric_finishing_reponse;
			
			/*else if ($('#txt_qc_qnty').val()==0)
			{
				alert ("QC Qty Not Zero or Less.");
				return;
			}
			else
			{*/
				/*if ($('#cbo_receive_basis').val()!=4)
				{
					if ( form_validation('txt_batch_no','Batch No')==false )
					{
						return;
					}
				}*/
				
		}
	}

	function subcon_fabric_finishing_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			release_freezing();
			//if (reponse[0].length>2) reponse[0]=10;
			/*if (reponse[0] == 11) 
			{
    			alert(reponse[1]);
    			return;
    		}*/ 
			/*if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();return;
			}
			else */if((reponse[0]==0 || reponse[0]==1))
			{
				show_msg(reponse[0]);
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_qc_id').value = reponse[2];
				document.getElementById('update_id_dtl').value = reponse[3];
				var company_id=$('#cbo_company_id').val();
				var within_group=$('#hid_within_group').val();
				show_list_view(reponse[1]+'_'+company_id,'fabric_finishing_list_view','fabric_finishing_list_view','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');
				show_list_view(reponse[4]+'_'+within_group+'_'+company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','setFilterGrid("list_view",-1)');
				document.getElementById('txt_production_no').disabled=true;
				set_button_status(1, permission, 'subcon_fabric_finishing',1,0);
			}
			release_freezing();
		}
	}
	
	function set_form_data(data)
	{
		 
		
		var data=data.split("**");
		//alert(data[2]);
		//var gsm_dia=data[1].split(",");
		$('#comp_id').val(data[0]);
		$('#txt_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
	    $('#txt_dia_width').val(data[3]);
		$('#hidden_color_id').val(data[4]);
		$('#txt_color').val(data[5]);
		$('#txt_process_id').val(data[6]);

		$('#txt_roll_no').val(data[7]);
		$('#cboShift').val(data[8]);
		$('#cbo_uom').val(data[9]);
		$('#txt_buyer_po').val(data[10]);
		$('#txt_buyer_style').val(data[11]);
		$('#txt_buyer_po_id').val(data[12]);
		$('#order_no_id').val(data[13]);
		$('#txt_batch_id').val(data[14]);
		$('#cbo_body_part').val(data[15]);
		$('#cbo_floor_name').val(data[16]);
		$('#txt_qc_qnty').val(data[17]);
		$('#txt_product_qnty').val(data[17]);
		$('#hid_within_group').val(data[18]);
		$('#txt_remarks').val('');
		$('#update_id_dtl').val('');
		set_multiselect('txt_process_id','0','1',data[6],'0')
		set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
		//openmypage_qnty(data[9]);
	}
	
	function openmypage_reject_type(update_id_dtl)
	{
		if(update_id_dtl=='')
		{
			alert("Please Save First"); return;
		}
		else
		{
			var update_id = $('#update_id').val();
			var data=document.getElementById('update_id_dtl').value+"_"+document.getElementById('update_id').value;
			var txt_reject_id = $('#txt_reject_id').val();
			var title = 'Process Name Selection Form';
			var page_link = 'requires/aop_qc_entry_controller.php?data='+data+'&action=reject_type_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				//var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				//var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
				var qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot").value;
				$('#txt_reject_qty').val(qnty_tot);
				calculate_gain_loss()
			}
		}
	}

	function calculate_gain_loss()
	{
		var product_qnty=$('#txt_product_qnty').val()*1;
		var qc_qnty=$('#txt_qc_qnty').val()*1;
		var reject_qty=$('#txt_reject_qty').val()*1;
		//var gain_loss=product_qnty-(qc_qnty+reject_qty);
		var gain_loss=(qc_qnty+reject_qty)-product_qnty;
		if(gain_loss<0)
		{
			gain_loss=0;
		}
		$('#txt_gain_loss').val(gain_loss.toFixed(2));
	}

</script>
<body onLoad="set_hotkey();set_auto_complete('color_return');">
<div style="width:100%;">   
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<div style="width:780px; float: left;">
	    <fieldset>
		    <legend >AOP QC Entry</legend>
		    <form name="fabricfinishing_1" id="fabricfinishing_1">
		        <table cellpadding="0" cellspacing="1" width="100%">
		            <tr>
		                <td colspan="3">
		                <fieldset>
		                    <table cellpadding="0" cellspacing="2" width="100%">
		                        <tr>
		                            <td align="right" colspan="3"><strong>QC ID </strong></td>
		                            <td width="140" align="justify">
		                                <input type="hidden" name="update_id" id="update_id" />
		                                <input type="hidden" name="hid_within_group" id="hid_within_group" />
		                                <input type="text" name="txt_qc_id" id="txt_qc_id" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_qc();" readonly tabindex="1" >
		                            </td>
		                        </tr>
		                        <tr>
		                            <td width="120" class="must_entry_caption">Company Name</td>
		                            <td width="140">
										<?php 
											echo create_drop_down( "cbo_company_id",140,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/aop_qc_entry_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",3);	
		                                ?>
		                            </td>
		                            <td width="120" class="must_entry_caption">Location </td>                                              
		                            <td width="140" id="location_td">
										<? 
											echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",4);
		                                ?>
		                            </td>
		                            <td>Party Name</td>
		                            <td id="buyer_td">
										<?
											echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"","","","",5);
		                                ?> 
		                            </td>
		                        </tr> 
		                        <tr>
		                            <td class="must_entry_caption">QC Date</td>
		                            <td>
		                                <input class="datepicker" type="text" style="width:130px" name="txt_qc_date" value="<? echo date('d-m-Y'); ?>" id="txt_qc_date" tabindex="6" />
		                            </td>
		                            <td>Prod. Floor</td>
		                            <td id="floor_td">
		                                <? echo create_drop_down( "cbo_floor_name", 140, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
		                            </td>
		                        </tr>
		                        <tr style="display: none;">
		                            
		                            <td width="120">Receive Basis</td>                                              
		                            <td width="140">
										<? 
											echo create_drop_down( "cbo_receive_basis", 140, $receive_basis_arr,"", 1,"-- Select Basis --", 5,"","","5","","","",2);
		                                ?>
		                            </td>
		                            <td>Challan No </td>
		                            <td>
		                                <input type="text" name="txt_chal_no" id="txt_chal_no" class="text_boxes" style="width:130px" tabindex="7" >
		                            </td>
		                            <td >Machine No</td>
		                            <td  id="machine_td">
		                                <? echo create_drop_down( "cbo_machine_id", 140, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
		                            </td>
		                        </tr>                                      
		                    </table>
		                </fieldset>
		                </td>
		            </tr>
		            <tr align="center">
		                <td width="75%" valign="top" style="margin-left:10px;">
		                <fieldset style="width:600px">
		                <legend>New Entry</legend>
		                    <table  cellpadding="0" cellspacing="2" width="100%" align="center">
		                        <tr>
		                            <td width="120">Production No</td>
		                            <td width="140" >
		                                <input type="text" name="txt_production_no" id="txt_production_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_production();" tabindex="10" /> 
		                                <input type="hidden" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:20px"/>
		                            </td>
		                            <td>Production Qty</td>
		                            <td>
		                                <input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" style="width:130px;"  placeholder="display" readonly tabindex="16" />	
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td>Body Part</td>
									<td>
										<? echo create_drop_down( "cbo_body_part", 140, $body_part,"", 1, "--Select--",0,"", 1 ); ?>
									</td>
									<td class="must_entry_caption">QC Qty</td>
		                            <td><input type="text" name="txt_qc_qnty" id="txt_qc_qnty" class="text_boxes_numeric"  placeholder="Write" style="width:130px;" onKeyUp="calculate_gain_loss()" />
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td>Fabric Description</td>
		                        	<td align="center">
		                        		<Input name="txt_description" ID="txt_description"  style="width:130px" class="text_boxes"  disabled >
		                        	</td>
		                        	<td>Reject Qty</td>
		                            <td>
										<input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" value="0" style="width:130px;" onClick="openmypage_reject_type(document.getElementById('update_id_dtl').value)" placeholder="Single Click" readonly tabindex="16" />
		                            	<input type="hidden" name="txt_reject_id" id="txt_reject_id" class="text_boxes" style="width:130px;" />
		                            </td>
			                    </tr>
			                    <tr>
		                            <td  style="width:120px ;"  class="must_entry_caption">GSM</td>
		                            <td>
		                                <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:130px ;text-align:right" tabindex="14" />
		                            </td>
									<td>Gain</td>
		                            <td><input type="text" name="txt_gain_loss" id="txt_gain_loss" class="text_boxes_numeric"  placeholder="Display" style="width:130px;" />
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td>Color</td>
			                        <td>
			                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" value="" style="width:130px;" readonly/>
			                            <input type="hidden" value="" id="hidden_color_id">
			                        </td>
		                            <td class="must_entry_caption">Dia/Width</td>
		                            <td>
		                                <input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes" style="width:130px;" tabindex="15" />	
		                            </td> 
		                        </tr>
		                        <tr>
		                        	<td>No. Of Roll</td>
		                            <td><input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:130px;" /></td>
		                            <td  style="width:120px ;" class="must_entry_caption">UOM</td>
		                            <td>
		                                <? echo create_drop_down( "cbo_uom", 140, $unit_of_measurement,'', 1, '-Select-', $uom, "","1","1,12,15,23,27" ); ?>
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td  style="width:120px ;" >Shift Name</td>
		                            <td>
		                                <? echo create_drop_down( "cboShift", 140, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','',''); ?>
		                            </td>
		                            <td>Process Name</td>
			                        <td>
			                        	<? echo create_drop_down( "txt_process_id", 140, $conversion_cost_head_array,"", 1, " Select Process", $selected, "" ,"","35,133,148,150,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237"); ?>
			                        </td>
		                        </tr>
		                        	<td >Buyer PO</td>
		                            <td id="order_numbers">
		                                <input type="text" name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:130px" readonly tabindex="11"/>
		                                <input type="hidden" name="txt_buyer_po_id" id="txt_buyer_po_id" class="text_boxes"/>

		                            </td>
		                        	<td >Buyer Style</td>
		                        	<td>
		                                <input type="text" name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:130px" readonly tabindex="11"/>
		                            </td>
		                        <tr>
		                        <tr>
		                        	<td >Remarks</td>
		                        	<td colspan="3">
		                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:395px" tabindex="11"/>
		                            </td>
		                        </tr>
		                        <tr style="display: none;">
		                            <td class="must_entry_caption">Const. Compo.</td>
		                            <td> <input type="hidden" name="comp_id" id="comp_id" /></td>
		                            
		                            <td width="120">Batch No</td>
		                            <td width="140" id="batch_no">
		                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_batchno();" tabindex="10" /> 
		                                <input type="text" name="txt_batch_ext_no" id="txt_batch_ext_no" class="text_boxes" style="width:53px" placeholder="Ext." readonly />
		                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" style="width:20px"/>
		                            </td>
		                        </tr>
		                    </table>
		                </fieldset>
		                </td>
		            </tr>
		            <tr>
		                <td colspan="6" align="center" class="button_container">
							<? 
								$date=date('d-m-Y');
								echo load_submit_buttons($permission, "subcon_fabric_finishing", 0,1,"reset_form('fabricfinishing_1','fabric_finishing_list_view','','txt_qc_date,".$date."','disable_enable_fields(\'cbo_receive_basis*cbo_company_id*cbo_party_name\',0)')",1);
		                    ?> 
		                    <input type="hidden" name="update_id_dtl" id="update_id_dtl" />
		                    <input type="hidden" name="hidden_dia_type" id="hidden_dia_type" />
		                    <input type="hidden" name="order_no_id" id="order_no_id" />
		                    <input type="hidden" name="item_order_id" id="item_order_id" />
		                    <input type="hidden" name="update_id_qnty" id="update_id_qnty" />
		                    <input type="hidden" name="update_check" id="update_check" />
		                    <input type="hidden" name="process_id" id="process_id" />
		                </td>
		            </tr>
		        </table> 
		    </form>
	    </fieldset>
	</div>
	<div id="list_fabric_desc_container" style="max-height:500px; width:470px; overflow:auto; float:left; margin:0px 5px 5px 15px;"> <!-- content would load here  --> </div>
	<div style="width:800px; margin-top:10px; float:left;" id="fabric_finishing_list_view" align="center"> <!-- content would load here --> </div>
</div>
</body>
<script>
	set_multiselect('txt_process_id','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>