<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create AOP production
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	24-03-2019
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

	function openmypage_finishing_id()
	{ 
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var cbo_company_id=$('#cbo_company_id').val()
			var cbo_party_name=$('#cbo_party_name').val()
			var data=cbo_company_id+"_"+cbo_party_name;

			emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/aop_production_controller.php?action=finishing_id_popup&data='+data,'AOP Production Popup Info', 'width=800px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("finishing_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					//var data=data.split("**");
					get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/aop_production_controller" );
					show_list_view(theemail.value,'fabric_finishing_list_view','fabric_finishing_list_view','requires/aop_production_controller','setFilterGrid("list_view",-1)');

					//show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value+"_"+document.getElementById('txt_batch_id').value+"_"+document.getElementById('cbo_company_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_production_controller','');
					reset_form('','list_fabric_desc_container','txt_batch_no*txt_batch_ext_no*order_no_id*txt_process_id*txt_description*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id*txt_buyer_po*txt_buyer_po_id*txt_buyer_style*txt_remarks*txt_aop_ref*cbo_body_part*cboShift');
					//document.getElementById('cbo_receive_basis').disabled=true;
					//document.getElementById('cbo_company_id').disabled=true;
					//document.getElementById('cbo_party_name').disabled=true;
					//$('#txt_process_id').focus();
					set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
					release_freezing();
				}
			}
		}
	}

	function openmypage_batchno()
	{ 
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('txt_process_id').value;
			emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/aop_production_controller.php?action=batch_numbers_popup&data='+data,'Batch Numbers Popup', 'width=900px, height=420px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("selected_batch_id");
				var data=theemail.value.split("_");
				if (data[0]!="")
				{
					freeze_window(5);
					reset_form('','','txt_process_id*txt_description*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id');
					get_php_form_data( data[0]+"_"+data[3]+"_"+data[4]+"_"+data[5]+"_"+document.getElementById('cbo_company_id').value, "load_php_data_to_form_batch", "requires/aop_production_controller" );
					$('#txt_aop_ref').val(data[1]);
					var hidden_control_chemical_issue=$('#hidden_control_chemical_issue').val();
					if (hidden_control_chemical_issue==20) 
					{
						alert('Batch Not Found In Dyes and Chemical Issue');
						//fnResetForm();
						release_freezing(); return;
					}
					
					show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value+"_"+document.getElementById('txt_batch_id').value+"_"+document.getElementById('cbo_company_id').value+"_"+data[3],'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_production_controller','');
					//document.getElementById('txt_receive_qnty').value="";
					//document.getElementById('txt_product_qnty').value="";
					release_freezing();
				}
			}
		}
	}

	function openmypage_qnty(order_no_id)
	{
		var data=order_no_id+"_"+document.getElementById('update_id_dtl').value+"_"+document.getElementById('update_check').value+"_"+document.getElementById('txt_receive_qnty').value+"_"+document.getElementById('txt_batch_qty').value+"_"+document.getElementById('is_sales_contract').value;
		//1997_67747__100_21424_
		//alert (data);//return;
		var title = 'Order Qnty Info';	
		var page_link = 'requires/aop_production_controller.php?action=order_qnty_popup&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
		var receive_qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot"); 
		var receive_qnty=this.contentDoc.getElementById("hidden_qnty"); 
		var receive_tbl_id=this.contentDoc.getElementById("hidd_qnty_tbl_id"); 
		//alert (receive_tbl_id.value);return;
		$('#txt_product_qnty').val(receive_qnty_tot.value);
		$('#txt_receive_qnty').val(receive_qnty.value);
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
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_finishing_id').val()+'*'+report_title, "subcon_fabric_finishing_print", "requires/aop_production_controller" ) 
			 return;
		}
		else if(operation==2)
		{
			show_msg('13');
			return;
		}
		else if(operation==0 || operation==1)
		{
			if( form_validation('cbo_company_id*cbo_party_name*txt_production_date*txt_dia_width*cbo_uom','Company Name*Party Name*Production Date*Dia/Width*UOM')==false )
			{
				return;
			}
			else if ($('#txt_product_qnty').val()==0)
			{
				alert ("Production Qty Not Zero or Less.");
				return;
			}
			else
			{
				if ($('#cbo_receive_basis').val()!=4)
				{
					if ( form_validation('txt_batch_no','Batch No')==false )
					{
						return;
					}
				}
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_finishing_id*cbo_receive_basis*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_chal_no*txt_remarks*update_id*txt_batch_id*txt_batch_no*txt_batch_ext_no*hidden_dia_type*txt_process_id*order_no_id*item_order_id*txt_description*comp_id*txt_color*hidden_color_id*txt_gsm*txt_dia_width*txt_product_qnty*txt_receive_qnty*txt_reject_qty*txt_roll_no*update_id_qnty*cbo_floor_name*cbo_machine_id*cboShift*cbo_uom*update_id_dtl*txt_buyer_po_id*cbo_body_part*txt_design_number*txt_coverage*txt_print_type',"../../");
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/aop_production_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = subcon_fabric_finishing_reponse;
			}
		}
	}

	function subcon_fabric_finishing_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			/*if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();return;
			}
			else*/ 
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_finishing_id').value = reponse[2];
				document.getElementById('update_id_dtl').value = reponse[3];
				var cbo_company_id = $('#cbo_company_id').val();
				show_list_view(reponse[1]+'_'+cbo_company_id,'fabric_finishing_list_view','fabric_finishing_list_view','requires/aop_production_controller','setFilterGrid("list_view",-1)');
				reset_form('fabricfinishing_1','','','txt_production_date,<? echo date("d-m-Y"); ?>','','txt_finishing_id*cbo_receive_basis*cbo_company_id*cbo_location_name*cbo_party_name*txt_chal_no*txt_remarks*update_id*txt_batch_no*txt_batch_ext_no*txt_batch_id*order_no_id*txt_process_id');
				//$('#txt_process_id').val('');
				show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value+'_'+document.getElementById('txt_batch_id').value+"_"+document.getElementById('cbo_company_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_production_controller','');
				document.getElementById('txt_batch_no').disabled=true;
				//$('#txt_batch_no').focus();
				//$('#list_fabric_desc_container').html('');
				set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
			}
			release_freezing();
		}
	}
	
	function set_form_data(data)
	{
		// alert(data);
		var data=data.split("**");
		//var gsm_dia=data[1].split(",");
		$('#comp_id').val(data[0]);
		$('#txt_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
	    $('#txt_dia_width').val(data[3]);
		$('#hidden_color_id').val(data[4]);
		$('#txt_color').val(data[5]);
		$('#item_order_id').val(data[6]);
		$('#cbo_body_part').val(data[7]);
		$('#txt_buyer_po').val(data[8]);
		$('#txt_buyer_style').val(data[9]);
		$('#txt_buyer_po_id').val(data[10]);
		$('#txt_roll_no').val(data[11]);
		$('#cbo_uom').val(data[12]);
		$('#txt_batch_qty').val(data[13]);
		$('#is_sales_contract').val(data[14]);
		$('#order_no_id').val(data[6]);
		$('#update_id_dtl').val('');
		$('#txt_product_qnty').val('');
		$('#txt_print_type').val(data[15]);
		$('#txt_design_number').val(data[16]);
		$('#txt_coverage').val(data[17]);
		set_button_status(0, permission, 'subcon_fabric_finishing',1,0);
		openmypage_qnty(data[6]);
		
				
	}
	
	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/aop_production_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			//$('#txt_process_name').val(process_name);
		}
	}
	
</script>
<body onLoad="set_hotkey();set_auto_complete('color_return');">
<div style="width:100%;">   
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<div style="width:780px; float: left;">
	    <fieldset>
		    <legend >AOP Production</legend>
		    <form name="fabricfinishing_1" id="fabricfinishing_1">
		        <table cellpadding="0" cellspacing="1" width="100%">
		            <tr>
		                <td colspan="3">
		                <fieldset>
		                    <table cellpadding="0" cellspacing="2" width="100%">
		                        <tr>
		                            <td align="right" colspan="3"><strong>Production ID </strong></td>
		                            <td width="140" align="justify">
		                                <input type="hidden" name="update_id" id="update_id" />
		                                <input type="text" name="txt_finishing_id" id="txt_finishing_id" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_finishing_id();" readonly tabindex="1" >
		                            </td>
		                        </tr>
		                        <tr>
		                            <td width="120" class="must_entry_caption">Company Name</td>
		                            <td width="140">
										<?php 
											echo create_drop_down( "cbo_company_id",140,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/aop_production_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",3);	
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
		                            <td class="must_entry_caption">Production Date</td>
		                            <td>
		                                <input class="datepicker" type="text" style="width:130px" name="txt_production_date" value="<? echo date('d-m-Y'); ?>" id="txt_production_date" tabindex="6" readonly />
		                            </td>
		                            <td>Prod. Floor</td>
		                            <td id="floor_td">
		                                <? echo create_drop_down( "cbo_floor_name", 140, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
		                            </td>
		                            <td >Machine No</td>
		                            <td  id="machine_td">
		                                <? echo create_drop_down( "cbo_machine_id", 140, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
		                            </td>
		                            
		                        </tr>
		                        <tr style="display: none;">
		                            <td>Remarks </td>                                              
		                            <td colspan="3"> 
		                                <input type="text" name="txt_remarkss" id="txt_remarkss" class="text_boxes" style="width:390px" maxlength="150" title="Maximum 150 Character" tabindex="8" >
		                            </td> 
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
		                            <td width="120">Batch No</td>
		                            <td width="140" id="batch_no">
		                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_batchno();" tabindex="10" />
		                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" style="width:20px"/>
		                                <input type="hidden" name="txt_batch_qty" id="txt_batch_qty" class="text_boxes_numeric" style="width:20px"/> 
		                                <input type="hidden" name="is_sales_contract" id="is_sales_contract" class="text_boxes_numeric" style="width:20px"/>
		                                <input type="hidden" name="hidden_control_chemical_issue" id="hidden_control_chemical_issue" /> 
		                                <input type="text" name="txt_batch_ext_no" id="txt_batch_ext_no" class="text_boxes" style="width:53px" placeholder="Ext." readonly />
		                            </td>
		                            <td class="must_entry_caption">Product Qty</td>
		                            <td><input type="hidden" name="txt_receive_qnty" id="txt_receive_qnty" />
		                                <input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" style="width:130px;" onClick="openmypage_qnty(document.getElementById('item_order_id').value)" placeholder="Single Click" readonly tabindex="16" />	
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td>Body Part</td>
									<td>
										<? echo create_drop_down( "cbo_body_part", 140, $body_part,"", 1, "--Select--",0,"", 1 ); ?>
									</td>
									<td>Reject Qty.</td>
		                            <td><input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:130px;" /></td>
		                        </tr>
		                        <tr>
		                        	<td>Fabric Description</td>
		                        	<td align="center">
		                        		<Input name="txt_description" ID="txt_description"  style="width:130px" class="text_boxes" placeholder="Write" disabled >
		                        	</td>
		                        	<td>No. Of Roll</td>
		                            <td><input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:130px;" /></td>
			                    </tr>
			                    <tr>
		                            <td  style="width:120px ;">GSM</td>
		                            <td>
		                                <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:130px ;text-align:right" tabindex="14" />
		                            </td>
		                            <td  style="width:120px ;" class="must_entry_caption">UOM</td>
		                            <td>
		                                <? echo create_drop_down( "cbo_uom", 140, $unit_of_measurement,'', 1, '-Select-', $uom, "","1","1,12,15,23,27" ); ?>
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
		                        	<td  style="width:120px ;" >Shift Name</td>
		                            <td>
		                                <? echo create_drop_down( "cboShift", 140, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','',''); ?>
		                            </td>
		                        	<td>Process Name</td>
			                        <td>
			                        	<? echo create_drop_down( "txt_process_id", 140, $conversion_cost_head_array,"", 1, "-- Select Location --", $selected, "" );
			                        	//echo create_drop_down( "txt_process_id", 140, $conversion_cost_head_array,"", 0, " Select Process", $selected, "" ,"","35,133,148,150,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237"); ?>
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
		                        	<td>
		                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:130px" tabindex="11"/>
		                            </td>
		                            <td >AOP Ref.</td>
		                        	<td>
		                                <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:130px" readonly tabindex="13"/>
		                            </td>
	                        	</tr>
	                        	<tr>
	                        		<td>Design Number</td>
		                        	<td>
		                                <input type="text" name="txt_design_number" id="txt_design_number" class="text_boxes" style="width:130px" readonly tabindex="13"/>
		                            </td>
		                            <td>Coverage%</td>
		                        	<td>
		                                <input type="text" name="txt_coverage" id="txt_coverage" class="text_boxes" style="width:130px" readonly tabindex="13"/>
		                            </td>
	                        	</tr>
	                        	<tr>
	                        		<td>Print Type</td>
		                        	<td>
		                                <input type="text" name="txt_print_type" id="txt_print_type" class="text_boxes" style="width:130px" readonly tabindex="11"/>
		                            </td>
	                        	</tr>
		                        <tr style="display: none;">
		                            <td class="must_entry_caption">Const. Compo.</td>
		                        </tr>
		                    </table>
		                </fieldset>
		                </td>
		            </tr>
		            <tr>
		                <td colspan="6" align="center" class="button_container">
							<? 
								$date=date('d-m-Y');
								echo load_submit_buttons($permission, "subcon_fabric_finishing", 0,1,"reset_form('fabricfinishing_1','fabric_finishing_list_view','','txt_production_date,".$date."','disable_enable_fields(\'cbo_receive_basis*cbo_company_id*cbo_party_name\',0)')",1);
		                    ?> 
		                    <input type="hidden" name="update_id_dtl" id="update_id_dtl" />
		                    <input type="hidden" name="hidden_dia_type" id="hidden_dia_type" />
		                    <input type="hidden" name="order_no_id" id="order_no_id" />
		                    <input type="hidden" name="item_order_id" id="item_order_id" />
		                    <input type="hidden" name="update_id_qnty" id="update_id_qnty" />
		                    <input type="hidden" name="update_check" id="update_check" />
		                    <input type="hidden" name="process_id" id="process_id" />
		                    <input type="hidden" name="comp_id" id="comp_id" />
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