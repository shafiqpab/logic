<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Issue Return Entry

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
echo load_html_head_contents("Yarn Issue Return Info","../../", 1, 1, $unicode,'','');

//$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1");
/*$issue_basis_requisition_or_demand_variable = 2;
if($issue_basis_requisition_or_demand_variable==2){
	$issue_basis = array(1 => "Booking", 2 => "Independent", 8 => "Demand", 4 => "Sales Order");
}else{
	$issue_basis = array(1 => "Booking", 2 => "Independent", 3 => "Requisition", 4 => "Sales Order");
}*/
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function active_inactive(str)
	{
		$("#txt_booking_no").val('');
		$("#txt_booking_id").val('');
		$("#txt_item_description").val('');
		$("#txt_prod_id").val('');
		$("#txt_supplier_id").val('');
		$("#txt_issue_id").val('');
		$("#txt_return_qnty").val('');
		$("#booking_without_order").val('');
		$("#tbl_child").find('select,input').val('');

		if(str==1 || str==3 || str==4 || str==8)
		{
			disable_enable_fields( 'txt_booking_no', 0, "", "" );
			if(str==1){
				$("#booking_reqsn_fso_label").html("F. Booking No");
			}else if (str==3){
				$("#booking_reqsn_fso_label").html("Requisition No");
			}else if (str==8){
				$("#booking_reqsn_fso_label").html("Demand No");
			}else if(str==4){
				$("#booking_reqsn_fso_label").html("Sales Order No");
			}
		}
		else
		{
			disable_enable_fields( 'txt_booking_no', 1, "", "" );
		}
	}

	/*function return_qnty_basis(purpose)
	{
		var basis = parseInt($("#cbo_basis").val());
		var booking_without_order = parseInt($("#booking_without_order").val());

		$("#save_data").val('');
		$("#all_po_id").val('');
		$("#txt_return_qnty").val('');
		$("#txt_reject_qnty").val('');
		$("#distribution_method").val('');

		if((basis==2 && (purpose==3 || purpose==5 || purpose==6 || purpose==7 || purpose==10 || purpose==12 || purpose==15 || purpose==30)) || (basis==1 && purpose==8) || (basis==1 && booking_without_order==3) || (basis==1 && booking_without_order==114))
		{
			$("#txt_return_qnty").attr('placeholder','Entry');
			$("#txt_return_qnty").removeAttr('ondblclick');
			$("#txt_return_qnty").removeAttr('readOnly');
			$("#txt_reject_qnty").removeAttr('readOnly');
		}
		else
		{
			$("#txt_return_qnty").attr('placeholder','Double Click');
			$("#txt_return_qnty").attr('ondblclick','openmypage_po()');
			$("#txt_return_qnty").attr('readOnly',true);
			$("#txt_reject_qnty").attr('readOnly',true);
		}
	}*/

	// popup for booking no ----------------------
	function popuppage_fabbook()
	{
		if( form_validation('cbo_company_id*cbo_basis','Company Name*Basis')==false )
		{
			return;
		}

		$('#txt_issue_qnty,#txt_total_return_display,#txt_net_used,#txt_returnable_qnty,#txt_rate,#txt_amount,#txt_issue_challan_no,#txt_returnable_bl_qnty').val('');
		
		var receive_basis=$('#cbo_basis').val();
		var company = $("#cbo_company_id").val();
		reset_form('','','txt_item_description*txt_yarn_lot*txt_prod_id*txt_supplier_id*txt_issue_id*cbo_store_name*txt_return_qnty*txt_reject_qnty','','','');
		var page_link='requires/yarn_issue_return_controller.php?action=fabbook_popup&company='+company+'&receive_basis='+receive_basis;
		var title="Book/Job/Lot No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/yarn_issue_return_controller',company, 'load_drop_down_floor', 'floor_td' );
			load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_room', 'room_td' );
			load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_rack', 'rack_td' );
			load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_shelf', 'shelf_td' );
			load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_bin', 'bin_td' );

			var theform=this.contentDoc.forms[0];
			var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value;
			bookingNumber = bookingNumber.split("_");	
			$("#txt_booking_id").val(bookingNumber[1]);
			$("#txt_booking_no").val(bookingNumber[2]);
			$("#txt_buyer_name").val(bookingNumber[7]);
			$("#txt_buyer_name").attr('buyer_id',bookingNumber[6]);
			$("#txt_issue_id").val(bookingNumber[3]);
			$("#txt_issue_challan_no").val(bookingNumber[4]);
			$("#txt_issue_qnty").val(bookingNumber[5]);
			$('#cbo_company_id').attr('disabled',true);
			$('#cbo_basis').attr('disabled',true);
		}
	}

	function open_itemdesc()
	{
		if( form_validation('cbo_company_id*cbo_basis*txt_booking_no*txt_issue_challan_no','Company Name*Basis*Book/lot/job*Issue Challan No')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var basis = $("#cbo_basis").val();
		var booking_no = $("#txt_booking_no").val();
		var booking_id = $("#txt_booking_id").val();
		var issue_id = $("#txt_issue_id").val();
		var page_link='requires/yarn_issue_return_controller.php?action=itemdesc_popup&company='+company+'&booking_no='+booking_no+'&basis='+basis+'&issue_id='+issue_id+'&booking_id='+booking_id;
		var title="Search Item Description";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1170px,height=400px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value;
			get_php_form_data(mrrNumber, "populate_data_from_data", "requires/yarn_issue_return_controller");
		}
	}

	function fn_calculateAmount(qnty)
	{
		var without_order=$("#booking_without_order").val()*1;

		var rate = $("#txt_rate").val()*1;
		var rcvQnty = $("#txt_total_return").val()*1+qnty*1+$("#txt_reject_qnty").val()*1;

		if(qnty=="")
		{
			return;
		}
		else if(rcvQnty*1>$("#txt_issue_qnty").val()*1)
		{
			alert("Returned Quantity can not be greater than Issue Quantity.\nIssue Quantity = " + $("#txt_issue_qnty").val()*1 );
			$('#txt_return_qnty').val('');
			$('#txt_reject_qnty').val('');
			$('#save_data').val('');
			$('#txt_amount').val(0);
			$("#txt_total_return_display").val( $("#txt_total_return").val());
		}
		else
		{
			var amount = rate*(qnty*1);
			$('#txt_amount').val(number_format_common(amount,"","",4));
		}
		//--------------

		var totalReturn = $("#txt_total_return").val()*1+$("#txt_return_qnty").val()*1+$("#txt_reject_qnty").val()*1;
		var balanceQnty = $("#txt_issue_qnty").val()*1-totalReturn;
		$("#txt_total_return_display").val( number_format_common(totalReturn,"","",1) );
		$("#txt_net_used").val( number_format_common(balanceQnty,"","",1) );
	//}
	}


	function fnc_yarn_issue_return_entry(operation)
	{
		if(operation==2)
		{
			show_msg(13);return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report($('#txt_mst_id').val(),"yarn_issue_return_print", "requires/yarn_issue_return_controller");
			return;
		}
		else if(operation==0 || operation==1)
		{
			if( form_validation('cbo_company_id*cbo_basis*txt_booking_no*txt_return_date*txt_item_description*cbo_store_name*txt_return_qnty','Company Name*Basis*Book/lot/Job*Return Date*Item Description*Store Name*Qnty')==false )
			{
				return;
			}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_return_date').val(), current_date)==false)
			{
				alert("Issue Return Date Can not Be Greater Than Current Date");
				return;
			}

			if(date_compare($('#hide_issue_date').val(), $('#txt_return_date').val())==false)
			{
				alert("Issue Return Date Can not Be Less Than Issue Date");
				return;
			}

			if($("#txt_return_qnty").val()*1<=0)
			{
				alert("Return Quantity Should be Greater Than Zero(0).");
				return;
			}

			// Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor=$('#cbo_floor').val()*1;
			var cbo_room=$('#cbo_room').val()*1;
			var txt_rack=$('#txt_rack').val()*1;
			var txt_shelf=$('#txt_shelf').val()*1;
			var cbo_bin = 0;
			// var cbo_bin=$('#cbo_bin').val()*1;
			// var txt_bag=$('#txt_bag').val();
			// var txt_cone=$('#txt_cone').val();
			
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
			//*save_data*all_po_id*booking_without_order*cbo_adjust_to*txt_adjust_po*save_data_adjust_po*hdn_adjust_po*pre_cbo_adjust_to*save_data_pre*hdn_req_no
			var dataString = "txt_mst_id*txt_return_no*cbo_company_id*cbo_basis*txt_booking_no*txt_booking_id*cbo_location*cbo_knitting_source*cbo_knitting_company*txt_return_date*txt_return_challan_no*txt_item_description*txt_prod_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_supplier_id*txt_issue_id*txt_yarn_lot*cbo_uom*txt_return_qnty*txt_reject_qnty*txt_remarks*txt_issue_qnty*txt_rate*txt_total_return*txt_amount*txt_net_used*txt_issue_challan_no*before_prod_id*update_id*cbo_color*txt_style_no*txt_job_no*cbo_buyer_name*txt_westage_qnty*txt_westage_dtls";
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/yarn_issue_return_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_issue_return_entry_reponse;
		}
	}

	function fnc_yarn_issue_return_entry_reponse()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==20 || reponse[0]==31)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(reponse[0]==13)
			{
				show_msg(reponse[0]);
				alert(reponse[1]);
				release_freezing();
				return;
			}

			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				$("#txt_mst_id").val(reponse[1]);
				$("#txt_return_no").val(reponse[2]);
	 			disable_enable_fields( 'cbo_company_id*cbo_basis*txt_booking_no*cbo_knitting_source*cbo_knitting_company', 1, "", "" ); // disable true
	 			show_list_view(reponse[1],'show_dtls_list_view','list_container_yarn','requires/yarn_issue_return_controller','');
				//child form reset here after save data-------------//
				//.not('#txt_issue_challan_no').not('#txt_issue_id')
				$("#tbl_child").find('input[id!="txt_issue_challan_no"],select').val('');
				reset_form('','','before_prod_id*update_id','','','');
				set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);
			}
			release_freezing();
		}
	}

	function company_onchange(company)
	{
	   	var status = return_global_ajax_value(company, 'upto_variable_settings', '', 'requires/yarn_issue_return_controller').trim();
		$('#store_update_upto').val(status);
	}

	function open_returnpopup()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var page_link='requires/yarn_issue_return_controller.php?action=return_number_popup&company='+company;
		var title="Search Return Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=400px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var returnNumberID=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
			//alert(returnNumberID);
	  		// master part call here
	  		get_php_form_data(returnNumberID, "populate_master_from_data", "requires/yarn_issue_return_controller");
			show_list_view(returnNumberID,'show_dtls_list_view','list_container_yarn','requires/yarn_issue_return_controller','');
			//disable_enable_fields( 'cbo_company_id*cbo_basis', 1, "", "" ); // disable true
			set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);
		}
	}

	//form reset/refresh function here
	function fnResetForm()
	{
		$("#tbl_master").find('input,select').attr("disabled", false);
		set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,0);
		reset_form('yarn_issue_return_1','list_container_yarn','','','','');
		document.getElementById("accounting_posted_status").innerHTML="";
	}
	
	function fn_westage_qnty()
	{
		if( form_validation('cbo_company_id*cbo_basis*txt_job_no','Company Name*Basis*Job No')==false )
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var westage_qnty=$("#txt_westage_qnty").val();
		var westage_dtls=$("#txt_westage_dtls").val();
		var page_link="requires/yarn_issue_return_controller.php?action=westage_popup&job_no="+job_no+"&westage_qnty="+westage_qnty+"&westage_dtls="+westage_dtls;
		var title="Westage Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=480px,height=300px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var dtls_data=this.contentDoc.getElementById("hdn_dtls_data").value;
			var hdn_weight=this.contentDoc.getElementById("hdn_weight").value;
			$("#txt_westage_dtls").val(dtls_data);
			$("#txt_westage_qnty").val(hdn_weight);
		}
	}

	function load_all_dropdowns(company)
	{
		alert(company);
		// var company = $("#cbo_company_id").val();
		// load_drop_down( 'requires/yarn_issue_return_controller',company, 'load_drop_down_floor', 'floor_td' );
        // load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_room', 'room_td' );
        // load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_rack', 'rack_td' );
        // load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_shelf', 'shelf_td' );
        // load_drop_down( 'requires/yarn_issue_return_controller', company, 'load_drop_down_bin', 'bin_td' );
	}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />
		<form name="yarn_issue_return_1" id="yarn_issue_return_1" autocomplete="off" >
			<div style="width:100%;">
				<table width="100%" cellpadding="0" cellspacing="2" align="center">
					<tr>
						<td width="80%" align="center" valign="top">
							<fieldset style="width:1000px;">
								<legend>Yarn Issue Return</legend>
								<br />
								<fieldset style="width:900px;">
									<table  width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
										<tr>
											<td colspan="3" align="right"><b>Return Number</b></td>
											<td colspan="3" align="left">
												<input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
												<input type="hidden" id="txt_mst_id" name="txt_mst_id" style="width:100px;" />
											</td>
										</tr>
										<tr>
											<td  width="120" align="right" class="must_entry_caption">Company Name </td>
											<td width="170">
												<?
												//load_drop_down( 'requires/yarn_issue_return_controller', this.value, 'load_drop_down_basis', 'receive_baisis_td' );
												echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' );load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', this.value);company_onchange(this.value);set_all_onclick();" );
												?>
											</td>
											<td width="120" align="right" class="must_entry_caption">Basis</td>
											<td width="160" id="receive_baisis_td">
												<?
												//active_inactive(this.value);
												echo create_drop_down( "cbo_basis", 170, $issue_basis,"", 1, "-- Select Basis --", $selected, "", "", "5,6,9,10");
												?>
											</td>
											<td width="120" align="right" id="booking_reqsn_fso_label">Job/Booking/lot</td>
											<td width="170">
												<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly />
												<input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                                                <input type="hidden" id="txt_issue_id" name="txt_issue_id" />
											</td>
										</tr>
										<tr>
											<td width="130" align="right">Location</td>
											<td width="170" id="location_td">
											<?
											echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Location --", "", "" );
											?>
                                            </td>
											<td width="94" align="right" >Return Source</td>
											<td width="160">
												<?
												echo create_drop_down( "cbo_knitting_source", 170, $knitting_source,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_issue_return_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_knit_com', 'knitting_company_td' );","","1,3" );
												?>
											</td>
											<td width="130" align="right">Working Company</td>
											<td width="" id="knitting_company_td">
												<?
												echo create_drop_down( "cbo_knitting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
												?>
											</td>
										</tr>
										<tr>
											<td align="right" class="must_entry_caption">Return Date</td>
											<td><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
											<td align="right" class="must_entry_caption">Return Challan</td>
											<td><input type="text" name="txt_return_challan_no" id="txt_return_challan_no" class="text_boxes" style="width:160px" /></td>
											<td align="right">
												Buyer Name
											</td>
											<td><input class="text_boxes" type="text" name="txt_buyer_name" id="txt_buyer_name" style="width:160px;" placeholder="Display" readonly /></td>
										</tr>
										<tr>
											<td align="right">&nbsp;</td>
											<td>&nbsp;</td>
											<td align="right" >&nbsp;</td>
											<td>&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
									<tr>
										<td width="50%" valign="top" align="center">
											<fieldset style="width:460px; float:left">
												<legend>Return Item Info</legend>
												<table  width="450" cellspacing="2" cellpadding="0" border="0">
													<tr>
														<td align="right" class="must_entry_caption">Item Description&nbsp;</td>
														<td colspan="3">
															<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:300px;" placeholder="Double Click To Search" onDblClick="open_itemdesc()" readonly  />
															<input type="hidden" id="txt_prod_id" name="txt_prod_id" />
															<input type="hidden" id="txt_supplier_id" name="txt_supplier_id" />
														</td>
													</tr>
													<tr>
														<td width="110" align="right">Color&nbsp;</td>
														<td width="158">
                                                        <?
                                                        echo create_drop_down( "cbo_color", 162,"select id,color_name from lib_color where status_active=1","id,color_name", 1, "--Select--", 0, "",1 );
														?>
                                                        </td>
														<td align="right" width="41" class="must_entry_caption">Store</td>
														<td id="store_td"><? echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select --", $storeName, "" ); ?></td>
													</tr>
													<tr>
                                                    	<td align="right">Yarn Lot&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_yarn_lot" id="txt_yarn_lot" style="width:150px;" placeholder="Display" readonly  /></td>
														<td align="right">Floor</td>
														<td id="floor_td">
															<? echo create_drop_down( "cbo_floor", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td align="right">Style&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_style_no" id="txt_style_no" style="width:150px;" placeholder="Display" readonly /></td>
														<td align="right" width="41">Room</td>
														<td id="room_td">
															<? echo create_drop_down( "cbo_room", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
                                                    	<td align="right">Job&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" style="width:150px;" placeholder="Display" readonly /></td>
														<td align="right" width="41">Rack</td>
														<td id="rack_td">
															<? echo create_drop_down( "txt_rack", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td width="110" align="right">Buyer</td>
														<td>
															<? 
															//echo create_drop_down( "cbo_adjust_to", 162,array(1=>"Allocation Quantity"),"", 1, "--Select--", 0, "active_inactive_adjust_po(this.value)",0 ); 
															echo create_drop_down( "cbo_buyer_name", 162,"select id,buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "--Select--", 0, "",1 );
															?>
															<input type="hidden" name="pre_cbo_adjust_to" id="pre_cbo_adjust_to" />
														</td>
														<td align="right" width="41">Shelf</td>
														<td id="shelf_td">
															<? echo create_drop_down( "txt_shelf", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
                                                    	<td align="right" class="must_entry_caption">Returned Qnty&nbsp;</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:150px;" onKeyUp="fn_calculateAmount(this.value)" /></td>
														<td align="right">UOM</td>
														<td><? echo create_drop_down( "cbo_uom", 100, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?></td>
													</tr>
													<tr>
                                                    	<td align="right">Reject Qty&nbsp;</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_reject_qnty" id="txt_reject_qnty" style="width:150px;"  /></td>
														<td align="right">Remarks&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:90px;" placeholder="Entry"  /></td>
													</tr>
                                                    <tr>
                                                    	<td align="right">Westage Qty&nbsp;</td>
														<td>
                                                        <input class="text_boxes_numeric" type="text" name="txt_westage_qnty" id="txt_westage_qnty" style="width:150px;" onDblClick="fn_westage_qnty()" readonly placeholder="Browse" />
                                                        <input class="text_boxes" type="hidden" name="txt_westage_dtls" id="txt_westage_dtls" />
                                                        </td>
														<td align="right">&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
												</table>
											</fieldset>
											<fieldset style="width:460px; float:left; margin-left:5px">
												<legend>Display</legend>
												<table  width="450" cellspacing="2" cellpadding="0" border="0" id="display_table" >
													<tr>
														<td width="110" align="right">Issue Qnty&nbsp;</td>
														<td width="100">
															<input class="text_boxes" type="text" name="txt_issue_qnty" id="txt_issue_qnty" style="width:100px;" placeholder="Display" readonly />
														</td>
														<td width="120" align="right">Rate&nbsp;</td>
														<td width="100"><input class="text_boxes" type="text" name="txt_rate" id="txt_rate" style="width:100px;" placeholder="Display" readonly  /></td>
													</tr>
													<tr>
														<td align="right">Total Return&nbsp;</td>
														<td>
															<input class="text_boxes" type="hidden" name="txt_total_return" id="txt_total_return" style="width:100px;" placeholder="Display" readonly  />
															<input class="text_boxes" type="text" name="txt_total_return_display" id="txt_total_return_display" style="width:100px;" placeholder="Display" readonly  />
														</td>
														<td align="right">Amount&nbsp;&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_amount" id="txt_amount" style="width:100px;" placeholder="Display" readonly /></td>
													</tr>
													<tr>
														<td align="right">Net Used&nbsp;</td>
														<td>
															<input class="text_boxes" type="text" name="txt_net_used" id="txt_net_used" style="width:100px;" placeholder="Display" readonly />
															<input class="text_boxes" type="hidden" name="hide_net_used" id="hide_net_used" readonly />
															<input class="text_boxes" type="hidden" name="hide_issue_date" id="hide_issue_date" readonly />
														</td>
														<td align="right">Issue Challan No&nbsp;</td>
														<td>
                                                        <input class="text_boxes" type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" style="width:100px;" placeholder="Display" readonly  />
                                                        </td>
													</tr>
													<tr>
														<td align="right">Returnable Qty.</td>
														<td><input class="text_boxes" type="text" name="txt_returnable_qnty" id="txt_returnable_qnty" style="width:100px;" placeholder="Display" readonly /></td>
														<td align="right">Returnable Bl. Qty.</td>
														<td><input class="text_boxes" type="text" name="txt_returnable_bl_qnty" id="txt_returnable_bl_qnty" style="width:100px;" placeholder="Display" readonly /></td>
													</tr>
													<tr>
														<td align="right">&nbsp;</td>
														<td colspan="2">&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
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
											<input type="hidden" name="store_update_upto" id="store_update_upto">
											<input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
											<input type="hidden" id="update_id" name="update_id" value="" />
											<? echo load_submit_buttons( $permission, "fnc_yarn_issue_return_entry", 0,1,"fnResetForm()",1);?>
										</td>
									</tr>
								</table>
							</fieldset>
							<fieldset style="width:1000px;">
								<div style="width:990px;" id="list_container_yarn"></div>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
