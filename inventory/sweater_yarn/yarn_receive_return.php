﻿<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Receive Return
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	14-03-2020
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
	echo load_html_head_contents("Yarn Receive Return Info","../../", 1, 1, $unicode,'',''); 
	?>	

	<script>
		var permission='<? echo $permission; ?>';
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


	function open_mrrpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/yarn_receive_return_controller.php?action=mrr_popup&company='+company; 
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1085px,height=370px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number
			//var ref_closing_status=this.contentDoc.getElementById("hidden_ref_closing_status").value; // mrr number
			//alert(ref_closing_status);
			// master part call here
			$("#pi_id").val('');
			$("#txt_pi_no").val('');
			
			get_php_form_data(mrrNumber[0]+"**"+mrrNumber[1]+"**"+mrrNumber[2]+"**"+mrrNumber[3], "populate_data_from_data", "requires/yarn_receive_return_controller");  
			$("#tbl_child").find('input,select').val('');
			$("#cbo_uom").val(15);			
		}
	}

	function fn_calculateAmount(qnty)
	{
		var rate = $("#txt_rate").val();
		var rcvQnty = $("#txt_receive_qnty").val();
		var ref_closing_status = $("#hidd_ref_closing_status").val();
		var returned_qnty = $("#hidd_returned_qnty").val();
		
		if(ref_closing_status==1)
		{
			alert("This reference is closed. No operation is allowed");
			$('#txt_return_qnty').val(returned_qnty);
			return;
		} 
		else
		{
			if(qnty=="" || rate=="" || rcvQnty*1<qnty)
			{
				$('#txt_return_qnty').val('');
				$('#txt_return_value').val('');
				return;
			}
			else
			{		
				var amount = rate*qnty;
				$('#txt_return_value').val(number_format_common(amount,"","",1));
			}
		}
		
	}

	function fnc_yarn_receive_return_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_return_no').val()+'*'+report_title, "yarn_receive_return_print", "requires/yarn_receive_return_controller" ) 
			return;
		}
		else
		{
			if($("#is_posted_account").val()==1)
			{
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			if( form_validation('cbo_company_name*cbo_return_to*txt_return_date*txt_mrr_no*cbo_buyer_name*txt_job_no*txt_style_no*txt_item_description*cbo_store_name*txt_return_qnty*txt_rate','Company Name*Return To*Return Date*MRR Number*Buyer*Job*Style*Item Description*Store Name*Return Quantity*Rate')==false )
			{
				return;
			}
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_return_date').val(), current_date)==false)
			{
				alert("Receive Return Date Can not Be Greater Than Current Date");
				return;
			}	
			if($("#txt_return_qnty").val()*1>$("#txt_receive_qnty").val()*1)
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
				return;
			}

			if($("#hidden_ref_closing_status").val()==1)
			{
				var returned_qnty = $("#hidd_returned_qnty").val();
				alert("This reference is closed. No operation is allowed");
				$('#txt_return_qnty').val(returned_qnty);
				return;
			} 

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
				else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && cbo_floor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
			// Store upto validation End

			var dataString = "txt_return_id*txt_return_no*cbo_company_name*cbo_return_to*txt_return_date*txt_received_id*hdn_issue_id*txt_mrr_no*cbo_buyer_name*txt_job_no*txt_style_no*txt_item_description*txt_prod_id*txt_no_of_bag*txt_no_of_cone*cbo_store_name*txt_return_qnty*txt_receive_qnty*cbo_uom*txt_rate*txt_return_value*before_prod_id*update_id*order_rate*order_ile_cost*pi_id*txt_remarks*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin";
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
			freeze_window(operation);
			http.open("POST","requires/yarn_receive_return_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_receive_return_entry_reponse;
		}
	}

	function fnc_yarn_receive_return_entry_reponse()
	{	
		if(http.readyState == 4) 
		{	  	
			//release_freezing(); return;	
			var reponse=trim(http.responseText).split('**');		
			if(reponse[0]==20 || reponse[0]==30 || reponse[0]==31)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			} 
			else if(reponse[0]==13)
			{
				alert(reponse[1]);
				show_msg(reponse[0]);
				release_freezing();
				return;
			} 
			
			show_msg(reponse[0]);
		
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#txt_return_no").val(reponse[1]);
				$("#txt_return_id").val(reponse[2]);
				disable_enable_fields( 'cbo_company_name*txt_return_no', 0, "", "" );
				show_list_view(reponse[2],'show_dtls_list_view','list_container_yarn','requires/yarn_receive_return_controller','');
		
				$("#cbo_company_name").attr("disabled","disabled");
				$("#txt_mrr_no").attr("disabled","disabled");
		
				set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
				//$("#yarn_receive_return_1").not(".formbutton,.formbutton_disabled").find('input,select').val('');
				$("#tbl_child").find('input,select').val('');
			}
			else if(reponse[0]==2)
			{
				show_list_view(reponse[2],'show_dtls_list_view','list_container_yarn','requires/yarn_receive_return_controller','');
				set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1);
				reset_form('yarn_receive_return_1','','','','','list_container_yarn*list_product_container*txt_return_no*txt_return_id*cbo_company_name*txt_mrr_no*txt_return_date*cbo_return_to*txt_pi_no*pi_id');
				//fnResetForm();
			}
			release_freezing();
		}
	}

	function company_onchange(company) 
	{		
	    var status = return_global_ajax_value(company, 'upto_variable_settings', '', 'requires/yarn_receive_return_controller').trim();
		$('#store_update_upto').val(status);	    
	}

	function open_returnpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/yarn_receive_return_controller.php?action=return_number_popup&company='+company; 
		var title="Search Return Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
			var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted account
			var returnNumberId=this.contentDoc.getElementById("hidden_return_id").value; // mrr number
			$("#is_posted_account").val(posted_in_account);
			
			if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
			else 					 document.getElementById("accounting_posted_status").innerHTML="";
			// master part call here
			get_php_form_data(returnNumberId, "populate_master_from_data", "requires/yarn_receive_return_controller");  		
			//list view call here
			show_list_view(returnNumberId,'show_dtls_list_view','list_container_yarn','requires/yarn_receive_return_controller','');
			set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
			//$("#tbl_master").find('input,select').attr("disabled", true);	
			$("#tbl_child").find('input,select').val('');
	
			//$('#txt_return_date').attr('disabled','disabled');
			disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
		}
	}
			
	function hidden_ref_closing(returned_qnty) {
		var ref_closing_status = $("#hidden_ref_closing_status").val();
		if(ref_closing_status ==1)
		{
			$("#txt_return_qnty").attr("disabled",true);
			$("#txt_return_qnty").attr("readonly",true);
			$("#txt_return_qnty").val(returned_qnty);
			$("#hidd_ref_closing_status").val(ref_closing_status);
			$("#hidd_returned_qnty").val(returned_qnty);
		}
	}

	//form reset/refresh function here
	function fnResetForm()
	{
		$("#tbl_master").find('input,select').attr("disabled", false);	
		set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1);
		reset_form('yarn_receive_return_1','list_container_yarn*list_product_container','','','','');
		document.getElementById("accounting_posted_status").innerHTML="";
	}

	// popup for PI----------------------	
	function openmypage_pi()
	{
		if( form_validation('cbo_company_name*txt_mrr_no','Company Name*MRR No')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_name").val();
	
		page_link='requires/yarn_receive_return_controller.php?action=pi_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'PI Search', 'width=850px, height=370px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var piID=this.contentDoc.getElementById("hidden_tbl_id").value; // pi table id
			var piNumber=this.contentDoc.getElementById("hidden_pi_number").value; // pi number
			
			$("#pi_id").val(piID);
			$("#txt_pi_no").val(piNumber);
		}		
	}
	

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="yarn_receive_return_1" id="yarn_receive_return_1" autocomplete="off" > 
			<div style="width:75%;">       
				<table width="80%" cellpadding="0" cellspacing="2" align="left">
					<tr>
						<td width="80%" align="center" valign="top">   
							<fieldset style="width:990px; float:left;">
								<legend>Yarn Receive Return</legend>
								<br />
								<fieldset style="width:900px;">                                       
									<table  width="800" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
										<tr>
											<td colspan="3" align="right"><b>Return Number</b></td>
											<td colspan="3" align="left"><input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
												<input type="hidden" id="txt_return_id" name="txt_return_id" value="" />
											</td>
										</tr>
										<tr>
											<td colspan="6" align="center">&nbsp;</td>
										</tr>
										<tr>
											<td  width="120" align="right" class="must_entry_caption">Company Name </td>
											<td width="170">
												<? 
												echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3)  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_receive_return_controller', this.value, 'load_drop_down_store', 'store_td');company_onchange(this.value);" );
												?>
											</td>
											<td width="120" align="right" class="must_entry_caption">MRR Number</td>
											<td width="160">
                                            <input class="text_boxes" type="text" name="txt_mrr_no" id="txt_mrr_no" style="width:160px;" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly  />
                                            <input type="hidden" name="txt_received_id" id="txt_received_id" readonly /> 
											<input type="hidden" name="hdn_issue_id" id="hdn_issue_id" readonly /> 
                                            </td>
											<td width="120" align="right" class="must_entry_caption">Return Date</td>
											<td width="170"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
										</tr>
										<tr>
											<td width="130" align="right" class="must_entry_caption">Returned To</td>
											<td width="170">
												<? 
												echo create_drop_down( "cbo_return_to", 170, "select id,supplier_name from lib_supplier order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 ); 
												?>
											</td>
											<td align="right">PI NO </td>
											<td>
												<input class="text_boxes" type="text" name="txt_pi_no" id="txt_pi_no" onDblClick="openmypage_pi()" placeholder="Double Click To Search" style="width:160px;" readonly />
												<input type="hidden" name="pi_id" id="pi_id"/>
												<input type="hidden" name="hidden_ref_closing_status" id="hidden_ref_closing_status"/>
											</td>
											<td align="right">Remarks</td>
											<td><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:160px;" /></td>
										</tr>
									</table>
								</fieldset>
								<br />
								<table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
									<tr>
										<td width="49%" valign="top">
											<fieldset style="width:1390px;">  
												<legend>Return Item</legend>                                     
												<table  width="1385" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
													<thead>
														<tr>
															<th width="80" class="must_entry_caption">Buyer</th>
                                                            <th width="70" class="must_entry_caption">Job No</th>
                                                            <th width="70" class="must_entry_caption">Style No</th>
                                                            <th width="130" class="must_entry_caption">Item Description</th>
															<th width="40">Lot</th>
                                                            <th width="40">No Of Bag</th>
															<th width="40">No Of Cone</th> 
															<th width="90" class="must_entry_caption">Store</th>
															<th width="80">Floor</th>
															<th width="80">Room</th>
															<th width="80">Rack</th>
															<th width="80">Shelf</th>
															<th width="80">Bin/Box</th>
															<th width="75" class="must_entry_caption">Returned Qnty</th>
															<th width="75">Inv. Recv. Qnty</th>
															<th width="50">UOM</th>   
															<th width="65" class="must_entry_caption">Rate </th>
															<th>Return Value</th>
														</tr>
													</thead>  
													<tr align="center">
														<td>
															<? 
															echo create_drop_down( "cbo_buyer_name", 80, "select id, buyer_name from lib_buyer order by buyer_name","id,buyer_name", 1, "Display", "", "",1 );
															?>
														</td>
                                                        <td>
															<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" readonly disabled />
														</td>
                                                        <td>
															<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:70px" readonly disabled  />
														</td>
                                                        <td>
															<input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:125px" placeholder="Display" readonly />
															<input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly disabled />
														</td>
														<td>
															<input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:35px" readonly disabled  />
														</td>
                                                        <td>
															<input type="text" name="txt_no_of_bag" id="txt_no_of_bag" class="text_boxes_numeric" style="width:35px"  />
														</td> 
														<td>
															<input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:35px"  />
														</td>                                    
														<td id="store_td">
															<? 
															echo create_drop_down( "cbo_store_name", 90, "select id, store_name from lib_store_location order by store_name","id,store_name", 1, "Display", "", "",1 );
															?>
														</td>
														<td id="floor_td">
															<? //echo create_drop_down( "cbo_floor", 80,"SELECT b.floor_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b WHERE  a.floor_room_rack_id = b.floor_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0","", 1, "--Select--", 0, "",1 ); 
															echo create_drop_down( "cbo_floor", "80", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "",1 );?>
														</td>
														<td id="room_td">
															<? //echo create_drop_down( "cbo_room", 80,"SELECT b.room_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b WHERE     a.floor_room_rack_id = b.room_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY b.room_id, a.floor_room_rack_name ORDER BY a.floor_room_rack_name","", 1, "--Select--", 0, "",1 ); 
															echo create_drop_down( "cbo_room", "80", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "",1 );?>
														</td>
														<td id="rack_td">
															<? //echo create_drop_down( "txt_rack", 80,$blank_array,"", 1, "--Select--", 0, "",1 ); 
															echo create_drop_down( "txt_rack", '80', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "",1 );
															?>
														</td>
														<td id="shelf_td">
															<? //echo create_drop_down( "txt_shelf", 80,$blank_array,"", 1, "--Select--", 0, "",1 );
															echo create_drop_down( "txt_shelf", '80', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "",1 ); ?>
														</td>
														<td id="bin_td">
															<? //echo create_drop_down( "cbo_bin", 80,$blank_array,"", 1, "--Select--", 0, "",1 );
															echo create_drop_down( "cbo_bin", '80', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "",1 ); ?>
														</td>
														<td>
															<input type="text" name="txt_return_qnty" id="txt_return_qnty" class="text_boxes_numeric" style="width:70px;" placeholder="Entry" onKeyUp="fn_calculateAmount(this.value)" />
															<input type="hidden" name="hidd_ref_closing_status" id="hidd_ref_closing_status">
															<input type="hidden" name="hidd_returned_qnty" id="hidd_returned_qnty">
														</td>
														<td>
															<input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly />
														</td>                                    
														<td>
															<?
															echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,"", 0, "--Select--", 15, "",1,'12,15' );
															?>
														</td>
														<td>
															<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:60px" placeholder="Display" readonly />
														</td>
														<td>
															<input type="text" name="txt_return_value" id="txt_return_value" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly/>
														</td> 
													</tr>
													<input type="hidden" name="order_rate" id="order_rate"  />
													<input type="hidden" name="order_ile_cost" id="order_ile_cost" />
												</table>
											</fieldset>
										</td>
									</tr>
								</table>                
								<table cellpadding="0" cellspacing="1" width="100%">
									<tr> 
										<td colspan="6" align="center"></td>				
									</tr>
									<tr>
										<td align="center" colspan="6" valign="middle" class="button_container">
											<!-- details table id for update -->
											<input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
											<input type="hidden" id="update_id" name="update_id" value="" />
											<input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
											<input type="hidden" name="store_update_upto" id="store_update_upto">
											<!-- -->
											<? echo load_submit_buttons( $permission, "fnc_yarn_receive_return_entry", 0,0,"fnResetForm()",1);?>
											
											<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
										</td>
									</tr> 
								</table>                 
							</fieldset>
							<fieldset>
								<div style="width:990px;" id="list_container_yarn"></div>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
			<div id="list_product_container" style="max-height:500px; width:24%; overflow:auto; float:left; padding-top:5px; margin-top:5px; margin-left:5px; position:relative;"></div>  
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>