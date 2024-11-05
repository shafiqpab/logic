<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Issue Return Entry

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	07-05-2013
Updated by 		: 	Kausar	(Creating Report)
Update date		: 	13-01-2014
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Issue Return Info","../", 1, 1, $unicode,1,1);

//$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1");
$issue_basis_requisition_or_demand_variable = 2;
if($issue_basis_requisition_or_demand_variable==2){
	$issue_basis = array(1 => "Booking", 2 => "Independent", 8 => "Demand", 4 => "Sales Order");
}else{
	$issue_basis = array(1 => "Booking", 2 => "Independent", 3 => "Requisition", 4 => "Sales Order");
}
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

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
		
		if(str==2)
		{
			$("#txt_return_qnty").attr('placeholder', 'Entry');
        	$("#txt_return_qnty").removeAttr('ondblclick');
        	$("#txt_return_qnty").removeAttr('readOnly');
        }
        else
		{
        	$("#txt_return_qnty").attr('placeholder', 'Double Click');
        	$("#txt_return_qnty").attr('ondblclick', 'openmypage_po()');
        	$("#txt_return_qnty").attr('readOnly', true);
		}
	}

	function return_qnty_basis(purpose)
	{
		var basis = parseInt($("#cbo_basis").val());
		var booking_without_order = parseInt($("#booking_without_order").val());

		$("#save_data").val('');
		$("#all_po_id").val('');
		$("#txt_return_qnty").val('');
		$("#txt_reject_qnty").val('');
		$("#distribution_method").val('');

		if((basis==2 && (purpose==3 || purpose==5 || purpose==6 || purpose==7 || purpose==10 || purpose==12 || purpose==15 || purpose==30 || purpose==44)) || (basis==1 && purpose==8) || (basis==1 && booking_without_order==3) || (basis==1 && booking_without_order==114))
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
	}

		// popup for booking no ----------------------
		function popuppage_fabbook()
		{
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}

			$('#txt_issue_qnty,#txt_total_return_display,#txt_net_used,#txt_returnable_qnty,#txt_rate,#txt_amount,#txt_issue_challan_no,#txt_returnable_bl_qnty').val('');
			
			var receive_basis=$('#cbo_basis').val();
			var company = $("#cbo_company_id").val();
			reset_form('','','txt_item_description*txt_yarn_lot*txt_prod_id*txt_supplier_id*txt_issue_id*cbo_store_name*txt_return_qnty*txt_reject_qnty','','','');
			var page_link='requires/yarn_issue_return_controller.php?action=fabbook_popup&company='+company+'&receive_basis='+receive_basis;
			var title="K&D Information";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1260px, height=400px, center=1, resize=0, scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value;	
				var wo_entry_form = 0;

				if (bookingNumber!="")
				{
					freeze_window(5);
					bookingNumberArr = bookingNumber.split("_");

					dataArr = bookingNumber.split("***");
					fabricBookingNo=dataArr[1].split("-");
					bookingExtension = fabricBookingNo[1];

					
					if(bookingExtension=='SMN')
					{
						$('#txt_return_qnty').val('');
						$('#txt_return_qnty').removeAttr('readonly','readonly');
						$('#txt_return_qnty').removeAttr('onDblClick','openmypage_po();');
						$('#txt_return_qnty').attr('placeholder','Entry');

						if(bookingNumberArr[6]!=1) // Not sales order
						{
							$('#booking_without_order').val(1);
						}
					}

					if(bookingNumberArr[0]==8)
					{
						var issue_ref=bookingNumberArr[7].split("***");
						var issue_id =  issue_ref[0];
						$("#txt_issue_id").val(issue_id);
						$("#txt_booking_id").val(bookingNumberArr[4]);
						$("#txt_booking_no").val(bookingNumberArr[3]);
						$("#hdn_req_no").val(bookingNumberArr[1]);
						$("#txt_buyer_name").val(bookingNumberArr[5]); 
					}
					else
					{
						$("#txt_booking_id").val(bookingNumberArr[1]);
						$("#txt_booking_no").val(bookingNumberArr[2]);
						$("#hdn_req_no").val(bookingNumberArr[1]);						

						if(bookingNumberArr[0]==1)
						{
							$("#txt_buyer_name").val(bookingNumberArr[9]);
							$("#txt_issue_id").val(bookingNumberArr[4]);
							var issue_id =  bookingNumberArr[4];
						}
						else if(bookingNumberArr[0]==4)
						{
							var issue_id =  bookingNumberArr[3];
							$("#txt_issue_id").val(bookingNumberArr[3]);
							$("#txt_buyer_name").val(bookingNumberArr[4]); 
						}
						else
						{
							var issue_ref=bookingNumberArr[7].split("***");
							var issue_id =  issue_ref[0];
							$("#txt_issue_id").val(issue_id);
							$("#txt_buyer_name").val(bookingNumberArr[5]); 
						}
					}

					if(bookingNumberArr[0]==1)
					{
						wo_entry_form = bookingNumberArr[10];
					}

					$("#txt_wo_entry_form").val(wo_entry_form);					

					get_php_form_data(bookingNumberArr[1]+'**'+company+'**'+bookingNumberArr[0]+'**'+issue_id, "populate_knitting_source", "requires/yarn_issue_return_controller");

					load_drop_down( 'requires/yarn_issue_return_controller', issue_id +'**'+ company, 'load_drop_down_purpose', 'issue_purpose_td' );
					
					$('#cbo_company_id').attr('disabled',true);
					$('#cbo_knitting_company').attr('disabled',true);

					release_freezing();
				}				
			}
		}


		function openmypage_po()
		{
			var receive_basis=$('#cbo_basis').val();
			var issue_purpose=$('#cbo_issue_purpose').val();
			var booking_no=$('#txt_booking_no').val();
			var booking_id=$('#txt_booking_id').val();
			var dyeing_color_id = $('#txt_dyeing_color_id').val();
			var booking_type=$('#booking_without_order').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var save_data = $('#save_data').val();
			var all_po_id = $('#all_po_id').val();
			var issueQnty = $('#txt_return_qnty').val();
			var reject_qnty = $('#txt_reject_qnty').val();
			var processloss_qnty = $('#txt_processloss_qnty').val();
			var txt_prod_id = $('#txt_prod_id').val();
			var txt_issue_id = $('#txt_issue_id').val();

			var distribution_method =  $('#distribution_method').val();
			var hdn_req_no =  $('#hdn_req_no').val();

			if(form_validation('cbo_company_id*cbo_basis*txt_item_description','Company Name*Basis*Item Description')==false)
			{
				return;
			}

			if($('#cbo_basis').val()==1 || $('#cbo_basis').val()==3)
			{
				if(form_validation('txt_booking_no','F. Booking/Reqsn. No')==false)
				{
					return;
				}
			}

			var title = 'PO Info';
			var page_link = 'requires/yarn_issue_return_controller.php?receive_basis='+receive_basis+'&issue_purpose='+issue_purpose+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&booking_id='+booking_id+'&dyeing_color_id='+dyeing_color_id+'&all_po_id='+all_po_id+'&save_data='+save_data+'&issueQnty='+issueQnty+'&process_loss_qnty='+processloss_qnty+'&distribution_method='+distribution_method+'&booking_type='+booking_type+'&reject_qnty='+reject_qnty+'&txt_prod_id='+txt_prod_id+'&txt_issue_id='+txt_issue_id+'&hdn_req_no='+hdn_req_no+'&action=po_popup';


			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var save_string=this.contentDoc.getElementById("save_string").value;
				var tot_issue_qnty=this.contentDoc.getElementById("tot_grey_qnty").value;
				var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value;
				var tot_processloss_qnty=this.contentDoc.getElementById("tot_processloss_qnty").value;
				var all_po_id=this.contentDoc.getElementById("all_po_id").value;
				var distribution_method = this.contentDoc.getElementById("distribution_method").value;

				$('#save_data').val(save_string);
				$('#txt_return_qnty').val( tot_issue_qnty );
				$('#txt_reject_qnty').val( tot_reject_qnty );
				$('#txt_processloss_qnty').val(tot_processloss_qnty );
				$('#all_po_id').val(all_po_id);
				$('#distribution_method').val(distribution_method);

				fn_calculateAmount(tot_issue_qnty);
			}
		}

		function open_itemdesc()
		{
			if( form_validation('cbo_company_id*cbo_basis','Company Name*Basis')==false )
			{
				return;
			}
			else if( ($("#cbo_basis").val()==1 ||  $("#cbo_basis").val()==3) && form_validation('txt_booking_no','Booking No')==false  )
			{
				return;
			}
			var company = $("#cbo_company_id").val();
			var location = $("#cbo_location").val();
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
				get_php_form_data(mrrNumber + "_" + company + "_" + location + "_" + $("#booking_without_order").val(), "populate_data_from_data", "requires/yarn_issue_return_controller");
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
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#cbo_basis').val()+'*'+$('#txt_booking_id').val()+'*'+$('#txt_mst_id').val()+'*'+$('#cbo_location').val()+'*'+$('#txt_buyer_name').val(),"yarn_issue_return_print", "requires/yarn_issue_return_controller")
			return;
		}else if(operation==5)
        {
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#cbo_basis').val()+'*'+$('#txt_booking_id').val()+'*'+$('#txt_mst_id').val()+'*'+$('#cbo_location').val()+'*'+$('#txt_buyer_name').val(),"yarn_issue_return_print2", "requires/yarn_issue_return_controller")
            return;
        }else if(operation==6)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#cbo_basis').val()+'*'+$('#txt_booking_id').val()+'*'+$('#txt_mst_id').val()+'*'+$('#cbo_location').val()+'*'+$('#txt_buyer_name').val(),"yarn_issue_return_print3", "requires/yarn_issue_return_controller")
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if($("#is_posted_account").val()==1)
			{
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			if( form_validation('cbo_company_id*cbo_basis*txt_return_date*txt_return_challan_no*txt_item_description*cbo_store_name','Company Name*Basis*Return Date*Challan No*Item Description*Store Name')==false )
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

			if($("#txt_return_qnty").val()*1<=0 && $("#txt_reject_qnty").val()*1<=0)
			{
				alert("Return Quantity Should be Greater Than Zero(0).");
				return;
			}

			if($('#cbo_basis').val()==1 && $('#txt_booking_no').val()=="")
			{
				alert("Please Select Booking No");
				$('#txt_booking_no').focus();
				return;
			}
			else if($('#cbo_basis').val()==3 && $('#txt_booking_no').val()=="")
			{
				alert("Please Select Reqsn. No");
				$('#txt_booking_no').focus();
				return;
			}

			// Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor=$('#cbo_floor').val()*1;
			var cbo_room=$('#cbo_room').val()*1;
			var txt_rack=$('#txt_rack').val()*1;
			var txt_shelf=$('#txt_shelf').val()*1;
			var cbo_bin=$('#cbo_bin').val()*1;
			var txt_bag=$('#txt_bag').val();
			var txt_cone=$('#txt_cone').val();
			
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
			
			var dataString = "txt_mst_id*txt_return_no*cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*txt_booking_id*cbo_location*cbo_knitting_source*cbo_knitting_company*txt_return_date*txt_return_challan_no*txt_item_description*txt_prod_id*txt_supplier_id*txt_issue_id*txt_yarn_lot*cbo_uom*txt_return_qnty*txt_reject_qnty*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_remarks*txt_issue_qnty*txt_rate*txt_total_return*txt_amount*txt_net_used*txt_issue_challan_no*before_prod_id*update_id*save_data*all_po_id*booking_without_order*save_data_pre*hdn_req_no*cbo_bin*txt_wo_entry_form*txt_dyeing_color_id*txt_bag*txt_cone*txt_processloss_qnty";

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../");
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
				$("#tbl_child").find('input,select').val('');
				reset_form('','','save_data*all_po_id*distribution_method*before_prod_id*update_id','','','');
				set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);
			}
			release_freezing();
		}
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
			var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
			$("#is_posted_account").val(posted_in_account);
			if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
			else 					 document.getElementById("accounting_posted_status").innerHTML="";
	  		// master part call here
	  		get_php_form_data(returnNumberID, "populate_master_from_data", "requires/yarn_issue_return_controller");
			//list view call here
			show_list_view(returnNumberID,'show_dtls_list_view','list_container_yarn','requires/yarn_issue_return_controller','');
			disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable true
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

	function fn_yarn_issue_return_print()
	{
	
		if( form_validation('cbo_company_id*txt_return_no','Company Name*Return Number')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var working_company  = $("#cbo_knitting_company").val();
		var return_source =  $("#cbo_knitting_source").val();

		var page_link='requires/yarn_issue_return_controller.php?action=return_multy_number_popup&company='+company+'&working_company='+working_company+'&return_source='+return_source; 

		var title="Search Return Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
			var returnid=this.contentDoc.getElementById("hidden_return_id").value; // mrr number
			var report_title=$( "div.form_caption" ).html();

			print_report( $('#cbo_company_id').val()+'*'+returnNumber+'*'+returnid+'*'+report_title+'*'+working_company+'*'+return_source, "yarn_issue_multy_return_print", "requires/yarn_issue_return_controller" );
			return;
		}
	}

	function company_onchange(company)
	{
	   	var status = return_global_ajax_value(company, 'upto_variable_settings', '', 'requires/yarn_issue_return_controller').trim();
		$('#store_update_upto').val(status);
	}

	// ==============End Floor Room Rack Shelf Bin disable============
	function storeUpdateUptoDisable() 
	{	
		$('#cbo_store_name').prop("disabled", true);
		$('#cbo_floor').prop("disabled", true);
		$('#cbo_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_shelf').prop("disabled", true);	
		$('#cbo_bin').prop("disabled", true);
	}
	// ==============End Floor Room Rack Shelf Bin disable============
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../",$permission);  ?><br />
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
											<td align="right"><b>Return Number</b></td>
											<td  align="left">
												<input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
												<input type="hidden" id="txt_mst_id" name="txt_mst_id" style="width:100px;" />
											</td>

											<td width="120" align="right">Issue Purpose</td>
											<td id="issue_purpose_td">
												<?
												echo create_drop_down("cbo_issue_purpose", 170, $blank_array, "", 1, "-- Select Purpose --", $selected, "", 1, "");
												?>
											</td>

											<td width="120" align="right">Requisition No</td>
											<td>
												<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:160px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td  width="120" align="right" class="must_entry_caption">Company Name </td>
											<td width="170">
												<?
												echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' );load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', this.value);load_drop_down( 'requires/yarn_issue_return_controller', this.value, 'load_drop_down_basis', 'receive_baisis_td' );storeUpdateUptoDisable();company_onchange(this.value);" );
												?>
											</td>
											<td width="120" align="right" class="must_entry_caption">Basis</td>
											<td width="160" id="receive_baisis_td">
												<?
												echo create_drop_down( "cbo_basis", 170, $issue_basis,"", 1, "-- Select Basis --", $selected, "active_inactive(this.value);", "", "");
												?>
											</td>
											<td width="120" align="right" id="booking_reqsn_fso_label">F. Booking/Reqsn. No</td>
											<td width="170">
												<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly disabled />
												<input type="hidden" name="txt_booking_id" id="txt_booking_id" />
												<input type="hidden" name="txt_wo_entry_form" id="txt_wo_entry_form" />
												<input type="hidden" name="booking_without_order" id="booking_without_order"/>
												<input type="hidden" name="hdn_req_no" id="hdn_req_no"/>
											</td>
										</tr>
										<tr>
											<td width="130" align="right">Location</td>
											<td width="170" id="location_td"><?
											echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Location --", "", "" );
											?></td>
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
								<table cellpadding="0" cellspacing="1" width="100%" id="tbl_child">
									<tr>
										<td width="60%" valign="top" align="center">
											<fieldset style="width:480px; float:left">
												<legend>Return Item Info</legend>
												<table  width="450" cellspacing="2" cellpadding="0" border="0">
													<tr>
														<td width="110" align="right" class="must_entry_caption">Description&nbsp;</td>
														<td colspan="3">
															<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:400px;" placeholder="Double Click To Search" onDblClick="open_itemdesc()" readonly  />
															<input type="hidden" id="txt_prod_id" name="txt_prod_id" />
															<input type="hidden" id="txt_dyeing_color_id" name="txt_dyeing_color_id" readonly />
															<input type="hidden" id="txt_supplier_id" name="txt_supplier_id" />
															<input type="hidden" id="txt_issue_id" name="txt_issue_id" />
														</td>
													</tr>
													<tr>
														<td width="110" align="right">Yarn Lot&nbsp;</td>
														<td width="158"><input class="text_boxes" type="text" name="txt_yarn_lot" id="txt_yarn_lot" style="width:150px;" placeholder="Display" readonly  /></td>
														
														<td align="right" width="41" class="must_entry_caption">Store</td>
														<td id="store_td"><? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select --", $storeName, "",1 ); ?></td>
													</tr>
													<tr>
														<td width="41" align="right">UOM</td>
														<td width="131"><? echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?></td>
														<td align="right" width="41" >Floor</td>
														<td id="floor_td">
															<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
														</td>
													</tr>
													<tr>
														<td width="110" align="right" class="must_entry_caption">Ret.Qnty&nbsp;</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:150px;" placeholder="Double Click To Search" onKeyUp="fn_calculateAmount(this.value)" readonly onDblClick="openmypage_po()" /></td>
														<td align="right" width="41">Room</td>
														<td id="room_td">
															<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
														</td>
													</tr>
													<tr>
														<td width="110" align="right">Reject Qty&nbsp;</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_reject_qnty" id="txt_reject_qnty" style="width:150px;" readonly  /></td>
														<td align="right" width="41">Rack</td>
														<td id="rack_td">
															<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
														</td>
													</tr>
													<tr>
														<td width="110" align="right">Process Loss Qty&nbsp;</td>
														<td>
															<input class="text_boxes_numeric" type="text" name="txt_processloss_qnty" id="txt_processloss_qnty" style="width:150px;" readonly  />
														</td>
														<td align="right" width="41">Bin/Box</td>
														<td id="bin_td">
															<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
														</td>
													</tr>

													<tr>
														<td align="right" width="41">Shelf&nbsp;</td>
														<td id="shelf_td">
															<? echo create_drop_down( "txt_shelf", 162,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
														</td>
														<td width="110" align="right">No Of Bag&nbsp;</td>
														<td ><input class="text_boxes_numeric" type="text" name="txt_bag" id="txt_bag" style="width:140px;" placeholder="Entry"  /></td>
													</tr>
													
													<tr>
														<td width="110" align="right">No Of Cone&nbsp;</td>
														<td ><input class="text_boxes_numeric" type="text" name="txt_cone" id="txt_cone" style="width:150px;" placeholder="Entry"  /></td>

														<td width="110" align="right">BTB Selection&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_btb" id="txt_btb" style="width:150px;" readonly /></td>
													</tr>
													<tr>
														<td width="110" align="right">Remarks&nbsp;</td>
														<td colspan="3"><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:400px;" placeholder="Entry"  /></td>
													</tr>
												</table>
											</fieldset>

											<fieldset style="width:450px; float:left; margin-left:5px">
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
														<td align="right">Issue MRR No&nbsp;</td>
														<td><input class="text_boxes" type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" style="width:100px;" placeholder="Display" readonly  /></td>
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
											<input type="hidden" name="save_data" id="save_data" readonly  />
											<input type="hidden" name="save_data_pre" id="save_data_pre" readonly  />
											<input type="hidden" name="save_data_adjust_po" id="save_data_adjust_po" value="" readonly />
											<input type="hidden" name="all_po_id" id="all_po_id" readonly />
											<input type="hidden" id="distribution_method" readonly />

											<input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
											<input type="hidden" id="update_id" name="update_id" value="" />
											<input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
											<input type="hidden" name="store_update_upto" id="store_update_upto">
											

											<? echo load_submit_buttons( $permission, "fnc_yarn_issue_return_entry", 0,1,"fnResetForm()",1);?>

											<input type="button" name="print2" value="Print 2" id="print2" class="formbutton" style="width: 80px;" onClick="fnc_yarn_issue_return_entry(5)" />
											<input type="button" name="print3" value="Print 3" id="print3" class="formbutton" style="width: 80px;" onClick="fnc_yarn_issue_return_entry(6)" />
											<input type="button" name="print3" value="Print Multi Return Number" id="print3" class="formbutton" style="width: 180px;" onClick="fn_yarn_issue_return_print()" />

											<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
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
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
