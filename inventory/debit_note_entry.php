<?
/*-------------------------------------------- Comments
Purpose         :   This form will create Debit Note Entry

Functionality   :
JS Functions    :
Created by      :   Md. Abu Sayed
Creation date   :   17-07-2023
Updated by      :   
Update date     :   
QC Performed BY :
QC Date         :
Comments        :
*/


session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Debit Note Entry", "../", 1, 1, $unicode, 1, 1);
?>

<script>

	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
    


    function fnc_debit_note_entry(operation)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		if (form_validation('cbo_company_id*cbo_debit_note_against*cbo_service_goods_name*cbo_basis*txt_item_description', 'Company Name*Basis*Debit Note Against*Goods Name*Basis*Item Description ') == false) 
		{
			return;
		}	

		var dataString = 'txt_mst_id*txt_system_no*cbo_company_id*cbo_location_name*cbo_debit_note_against*cbo_service_goods_name*cbo_within_group*cbo_working_company*cbo_basis*txt_wo_issue_adj*txt_wo_issue_adj_id*txt_issue_challan*txt_goods_rcv_challan*cbo_debit_note_to*txt_debit_note_date*txt_rcvissue_id*txt_prod_id*txt_fin_dia*txt_finish_gsm*txt_sl*txt_process_name*txt_fab_fault*txt_knit_charge*txt_plodq*txt_knit_qnty*txt_debit_note_qnty*txt_dying_charge*txt_debit_amount*txt_dyeing_qnty*txt_remarks*txt_yarn_receive_qty*txt_yarn_issue_qty*txt_net_used_qty*txt_returnable_qnty*txt_return_qnty*txt_fabric_receive*txt_receive_balance*txt_returnable_balance_qnty*txt_yarn_rate*txt_returnable_bl_value*txt_process_loss_value*txt_debit_note_value*txt_total_knitting_cost*txt_total_dyeing_cost*update_id*txt_item_description*txt_fab_prod_id*txt_yarn_lot*txt_yarn_brand*txt_yarn_brand_id*txt_yarn_count*txt_yarn_count_id*txt_item_description_id*txt_color*txt_color_id*cbo_uom*txt_requisition_no';
		//alert(dataString);return;
		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST", "requires/debit_note_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_issue_entry_reponse;
    }

    function fnc_yarn_issue_entry_reponse() 
    {
    	if (http.readyState == 4) 
        {
			//release_freezing(); return;	
			var reponse=trim(http.responseText).split('**');		
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			} 

			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1 )
			{
				$("#txt_mst_id").val(reponse[1]);
				$("#txt_system_no").val(reponse[2]);
	 			disable_enable_fields( 'cbo_company_id*cbo_debit_note_against*cbo_service_goods_name*cbo_basis*txt_wo_issue_adj', 1, "", "" ); // disable true
	 			show_list_view(reponse[1],'show_dtls_list_view','list_container_yarn','requires/debit_note_entry_controller','');
				//child form reset here after save data-------------//
				$("#txt_item_description").attr("disabled", false);
				$("#tbl_child").find('input,select').val('');
				
				reset_form('','','update_id','','','');
				
				set_button_status(0, permission, 'fnc_debit_note_entry',1,1);
			}

			release_freezing();	
    	}
    }

	function fnResetForm() 
    {
    	$("#tbl_master").find('input').attr("disabled", false);
    	$("#tbl_master").find('input,select').attr("disabled", false);
    	set_button_status(0, permission, 'fnc_debit_note_entry', 1);
    	reset_form('debit_note_1', 'list_container_yarn', '', '', '', 'cbo_uom');
    }

	function open_mrrpopup()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var page_link='requires/debit_note_entry_controller.php?action=debit_number_popup&company='+company;
		var title="Search Return Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=400px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hiddenDebitId=this.contentDoc.getElementById("hidden_debit_id").value; // mrr number
	  		// master part call here
	  		get_php_form_data(hiddenDebitId, "populate_master_from_data", "requires/debit_note_entry_controller");
			//list view call here
			show_list_view(hiddenDebitId,'show_dtls_list_view','list_container_yarn','requires/debit_note_entry_controller','');
			disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable true
			set_button_status(0, permission, 'fnc_debit_note_entry',1,1);
		}
	}

 // popup for WO/Issue----------------------
 function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_debit_note_against*cbo_service_goods_name*cbo_basis','Company Name*Debit Note Against*Service Goods Name*Basis Name')==false )
	{
		return;
	}

	var company = $("#cbo_company_id").val();
	var debit_note_against = $("#cbo_debit_note_against").val();
	var service_goods_name = $("#cbo_service_goods_name").val();
	var debit_basis = $("#cbo_basis").val();

	page_link='requires/debit_note_entry_controller.php?action=woissueadj_popup&company='+company+'&debit_basis='+debit_basis+'&debit_note_against='+debit_note_against+'&service_goods_name='+service_goods_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px, height=400px, center=1, resize=0, scrolling=0','')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
		var woIssueNumber=this.contentDoc.getElementById("hidden_woissue_number").value; // wo/Issue number			
		//alert(woIssueNumber);
		
		if (rowID!="")
		{
			freeze_window(5);
			$("#txt_wo_issue_adj_id").val(rowID);
			$("#txt_wo_issue_adj").val(woIssueNumber);
			disable_enable_fields( 'cbo_company_id*cbo_debit_note_against*cbo_service_goods_name*cbo_basis', 1, "", "" ); // disable true
			release_freezing();
		}
	}
}

function open_itemdesc()
{
	var companyID = $("#cbo_company_id").val();
	var debitBasis = $("#cbo_basis").val();
	var woIssueAdj = $("#txt_wo_issue_adj").val();
	var woIissueAdjId = $("#txt_wo_issue_adj_id").val();
	var debit_note_against = $("#cbo_debit_note_against").val();
	var service_goods_name = $("#cbo_service_goods_name").val();
	
	if( form_validation('cbo_company_id*cbo_basis*txt_wo_issue_adj','Company Name*Wo Issue Id')==false )
	{
		return;
	}

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/debit_note_entry_controller.php?action=item_desc_popup&companyID='+companyID+'&debitBasis='+debitBasis+'&woIissueAdjId='+woIissueAdjId+'&debit_note_against='+debit_note_against+'&service_goods_name='+service_goods_name, 'Item Details', 'width=910px,height=420px,center=1,resize=0,scrolling=0','');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_rcv_id=this.contentDoc.getElementById("hidden_yarn_rcv_id").value; //Access form field with id="emailfield"
		var prod_id=this.contentDoc.getElementById("hidden_prod_id").value;
		var req_no=this.contentDoc.getElementById("hidden_req_no").value;

		//alert(req_no);
		if(req_no == ""){ req_no = 0;}else{req_no=req_no;}
		
		get_php_form_data(yarn_rcv_id + "_" + prod_id + "_" + debitBasis + "_" + companyID + "_" + service_goods_name + "_" + debit_note_against + "_" + req_no, "populate_data_from_data", "requires/debit_note_entry_controller");

	}
}


function fn_calDeducQnty(qnty)
{
	var rate = $("#txt_yarn_rate").val()*1;
	
	var pldval = rate*(qnty*1);
	$('#txt_process_loss_value').val(pldval.toFixed(2));
}

function fn_calDabitNQnty(qnty)
{
	var rate = $("#txt_yarn_rate").val()*1;
	
	var debitNVal = rate*(qnty*1);
	$('#txt_debit_note_value').val(debitNVal.toFixed(2));
	fn_calDabitAmount();
}

function fn_calKnitCost(qnty)
{
	var knit_charge = $("#txt_knit_charge").val()*1;
	var knit_qnty = $("#txt_knit_qnty").val()*1;
	
	var totKntingCost = knit_charge*knit_qnty;
	$('#txt_total_knitting_cost').val(totKntingCost.toFixed(2));
	fn_calDabitAmount();
}

function fn_caldyingCost(qnty)
{
	var dying_charge = $("#txt_dying_charge").val()*1;
	var dyeing_qnty = $("#txt_dyeing_qnty").val()*1;
	
	var totdyingCost = dying_charge*dyeing_qnty;
	//$('#txt_total_dyeing_cost').val(number_format_common(totdyingCost,"","",4));
	$('#txt_total_dyeing_cost').val(totdyingCost.toFixed(2));
	fn_calDabitAmount();
}

function fn_calDabitAmount()
{
	var rate = $("#txt_yarn_rate").val()*1;
	var debit_note_qnty = $("#txt_debit_note_qnty").val()*1;
	var total_knitting_cost = $("#txt_total_knitting_cost").val()*1;
	var total_dyeing_cost = $("#txt_total_dyeing_cost").val()*1;
	var debit_amount = (rate*debit_note_qnty)+(total_knitting_cost+total_dyeing_cost);
	$('#txt_debit_amount').val(debit_amount.toFixed(2));
}


function active_inactive(str)
{
	var debit_note_against = $("#cbo_debit_note_against").val();
	var service_goods_name = $("#cbo_service_goods_name").val();
	
	/* if(str==2 || str==3 || str==5 )
	{
		disable_enable_fields( 'txt_wo_issue_adj', 0, "", "" );
		$("#txt_wo_issue_adj").attr('ondblclick', "openmypage('xx','WO/Issue/Adj/Prog')");
		if(str==1){
			$("#wo_issue_adj_label").html("Independent");
		}
		if(str==2){
			$("#wo_issue_adj_label").html("Issue ID");
		}else if (str==3){
			$("#wo_issue_adj_label").html("WO/Booking");
		}else if (str==5){
			$("#wo_issue_adj_label").html("Program No");
		}
	}
	else if(str==1)
	{
		$("#wo_issue_adj_label").html("Independent");
		disable_enable_fields( 'txt_wo_issue_adj', 1, "", "" );
		$("#txt_wo_issue_adj").removeAttr('ondblclick');
		$("#txt_item_description").removeAttr('ondblclick');
	    $("#txt_wo_issue_adj").val('');
		$("#txt_wo_issue_adj_id").val('');
	}
	else if(str==3)
	{
	    $("#txt_wo_issue_adj").val('');
		$("#txt_wo_issue_adj_id").val('');
	}
	else if(str==4)
	{
		$("#wo_issue_adj_label").html("Adjustment");
		disable_enable_fields( 'txt_wo_issue_adj', 1, "", "" );
		$("#txt_wo_issue_adj").removeAttr('ondblclick');
		$("#txt_wo_issue_adj").val('');
		$("#txt_wo_issue_adj_id").val('');
	} */
	
	if(debit_note_against==1)
	{
		if(service_goods_name == 16 && str==3)
		{
			$("#wo_issue_adj_label").html("WO/Booking");
			disable_enable_fields( 'txt_wo_issue_adj', 0, "", "" );
			$("#txt_wo_issue_adj").attr('ondblclick', "openmypage('xx','WO/Booking')");
			$("#txt_wo_issue_adj").val('');
			$("#txt_wo_issue_adj_id").val('');

			$("#txt_item_description").val('');
			$("#txt_item_description").attr('placeholder', 'Browse ');
			$("#txt_item_description").attr('ondblclick', 'open_itemdesc()');
			$("#txt_item_description").attr('readOnly', true);

			$("#txt_yarn_lot").attr('placeholder', 'Display');
			$("#txt_yarn_lot").attr('readOnly', true);
			$("#txt_yarn_lot").val('');

			$("#txt_fin_dia").removeAttr('placeholder', true);
			$("#txt_fin_dia").attr('readOnly', true);
			$("#txt_fin_dia").val('');
			

			$("#txt_yarn_brand").attr('placeholder', 'Display');
			$("#txt_yarn_brand").attr('readOnly', true);
			$("#txt_yarn_brand").val('');

			$("#txt_yarn_count").attr('placeholder', 'Display');
			$("#txt_yarn_count").attr('readOnly', true);
			$("#txt_yarn_count").val('');

			$("#txt_finish_gsm").removeAttr('placeholder', true);
			$("#txt_finish_gsm").attr('readOnly', true);
			$("#txt_finish_gsm").val('');

			$("#txt_sl").removeAttr('placeholder', true);
			$("#txt_sl").attr('readOnly', true);
			$("#txt_sl").val('');

			$("#txt_color").removeAttr('placeholder', true);
			$("#txt_color").attr('readOnly', true);
			$("#txt_color").val('');
			
			$("#cbo_uom").attr('disabled', true);
			$("#cbo_uom").val(12);

			$("#txt_fab_fault").attr('readOnly', true);
			$("#txt_fab_fault").val('');

			$("#txt_yarn_receive_qty").attr('placeholder', 'Display');
			$("#txt_yarn_receive_qty").attr('readOnly', true);
			$("#txt_yarn_receive_qty").val('');

			$("#txt_yarn_issue_qty").attr('readOnly', true);
			$("#txt_yarn_issue_qty").val('');

			$("#txt_net_used_qty").attr('readOnly', true);
			$("#txt_net_used_qty").val('');

			$("#txt_returnable_qnty").attr('readOnly', true);
			$("#txt_returnable_qnty").val('');

			$("#txt_return_qnty").attr('placeholder', 'Display');
			$("#txt_return_qnty").attr('readOnly', true);
			$("#txt_return_qnty").val('');

			$("#txt_fabric_receive").attr('readOnly', true);
			$("#txt_fabric_receive").val('');

			$("#txt_receive_balance").attr('readOnly', true);
			$("#txt_receive_balance").val('');

			$("#txt_returnable_balance_qnty").attr('readOnly', true);
			$("#txt_returnable_balance_qnty").val('');

		}
		else if(service_goods_name == 16 && str==1 )
		{
			$("#wo_issue_adj_label").html("Independent");
			disable_enable_fields( 'txt_wo_issue_adj', 1, "", "" );
			$("#txt_wo_issue_adj").removeAttr('ondblclick');
			$("#txt_item_description").removeAttr('ondblclick');
			$("#txt_wo_issue_adj").val('');
			$("#txt_wo_issue_adj_id").val('');

			$("#txt_item_description").removeAttr('ondblclick');
			$("#txt_item_description").removeAttr('readOnly');
			$("#txt_item_description").attr('placeholder', 'Entry');

			$("#txt_yarn_lot").attr('placeholder', 'Entry');
			$("#txt_yarn_lot").removeAttr('readOnly');

			$("#txt_fin_dia").attr('placeholder', 'Entry');
			$("#txt_fin_dia").removeAttr('readOnly');

			$("#txt_yarn_brand").attr('placeholder', 'Entry');
			$("#txt_yarn_brand").removeAttr('readOnly');

			$("#txt_yarn_count").attr('placeholder', 'Entry');
			$("#txt_yarn_count").removeAttr('readOnly');

			$("#txt_finish_gsm").attr('placeholder', 'Entry');
			$("#txt_finish_gsm").removeAttr('readOnly');

			$("#txt_sl").attr('placeholder', 'Entry');
			$("#txt_sl").removeAttr('readOnly');

			$("#txt_color").attr('placeholder', 'Entry');
			$("#txt_color").removeAttr('readOnly');

			$("#cbo_uom").attr('disabled',true);
			$("#cbo_uom").val(12);

			$("#txt_yarn_receive_qty").attr('placeholder', 'Entry');
			$("#txt_yarn_receive_qty").removeAttr('readOnly');
			$("#txt_yarn_issue_qty").attr('placeholder', 'Entry');
			$("#txt_yarn_issue_qty").removeAttr('readOnly');
			$("#txt_net_used_qty").attr('placeholder', 'Entry');
			$("#txt_net_used_qty").removeAttr('readOnly');
			$("#txt_returnable_qnty").attr('placeholder', 'Entry');
			$("#txt_returnable_qnty").removeAttr('readOnly');
			$("#txt_return_qnty").attr('placeholder', 'Entry');
			$("#txt_return_qnty").removeAttr('readOnly');
			$("#txt_fabric_receive").attr('placeholder', 'Entry');
			$("#txt_fabric_receive").removeAttr('readOnly');
			$("#txt_receive_balance").attr('placeholder', 'Entry');
			$("#txt_receive_balance").removeAttr('readOnly');
			$("#txt_returnable_balance_qnty").attr('placeholder', 'Entry');
			$("#txt_returnable_balance_qnty").removeAttr('readOnly');
		}
		else if(service_goods_name == 1 && str==5)
		{
			$("#wo_issue_adj_label").html("Program No");
			disable_enable_fields( 'txt_wo_issue_adj', 0, "", "" );
			$("#txt_wo_issue_adj").attr('ondblclick', "openmypage('xx','Program No')");
			$("#txt_wo_issue_adj").val('');
			$("#txt_wo_issue_adj_id").val('');

			$("#txt_item_description").val('');
			$("#txt_item_description").attr('placeholder', 'Browse ');
			$("#txt_item_description").attr('ondblclick', 'open_itemdesc()');
			$("#txt_item_description").attr('readOnly', true);

			$("#txt_yarn_lot").attr('placeholder', 'Display');
			$("#txt_yarn_lot").attr('readOnly', true);
			$("#txt_yarn_lot").val('');

			$("#txt_fin_dia").attr('placeholder', 'Display');
			$("#txt_fin_dia").attr('readOnly', true);
			$("#txt_fin_dia").val('');
			

			$("#txt_yarn_brand").attr('placeholder', 'Display');
			$("#txt_yarn_brand").attr('readOnly', true);
			$("#txt_yarn_brand").val('');

			$("#txt_yarn_count").attr('placeholder', 'Display');
			$("#txt_yarn_count").attr('readOnly', true);
			$("#txt_yarn_count").val('');

			$("#txt_finish_gsm").attr('placeholder', 'Display');
			$("#txt_finish_gsm").attr('readOnly', true);
			$("#txt_finish_gsm").val('');

			$("#txt_sl").attr('placeholder', 'Display');
			$("#txt_sl").attr('readOnly', true);
			$("#txt_sl").val('');

			$("#txt_color").attr('placeholder', 'Display');
			$("#txt_color").attr('readOnly', true);
			$("#txt_color").val('');
			
			$("#cbo_uom").attr('disabled', true);
			$("#cbo_uom").val(12);

			$("#txt_fab_fault").removeAttr('readOnly', true);
			$("#txt_fab_fault").val('');

			$("#txt_yarn_receive_qty").attr('placeholder', 'Display');
			$("#txt_yarn_receive_qty").attr('readOnly', true);
			$("#txt_yarn_receive_qty").val('');

			$("#txt_yarn_issue_qty").attr('readOnly', true);
			$("#txt_yarn_issue_qty").val('');

			$("#txt_net_used_qty").attr('readOnly', true);
			$("#txt_net_used_qty").val('');

			$("#txt_returnable_qnty").attr('readOnly', true);
			$("#txt_returnable_qnty").val('');

			$("#txt_return_qnty").attr('placeholder', 'Display');
			$("#txt_return_qnty").attr('readOnly', true);
			$("#txt_return_qnty").val('');

			$("#txt_fabric_receive").attr('readOnly', true);
			$("#txt_fabric_receive").val('');

			$("#txt_receive_balance").attr('readOnly', true);
			$("#txt_receive_balance").val('');

			$("#txt_returnable_balance_qnty").attr('readOnly', true);
			$("#txt_returnable_balance_qnty").val('');

		}
		else if(service_goods_name == 1 && str==2)
		{
			$("#wo_issue_adj_label").html("Issue ID");
			disable_enable_fields( 'txt_wo_issue_adj', 0, "", "" );
			$("#txt_wo_issue_adj").attr('ondblclick', "openmypage('xx','Issue ID')");
			$("#txt_wo_issue_adj").val('');
			$("#txt_wo_issue_adj_id").val('');

			$("#txt_item_description").val('');
			$("#txt_item_description").attr('placeholder', 'Browse ');
			$("#txt_item_description").attr('ondblclick', 'open_itemdesc()');
			$("#txt_item_description").attr('readOnly', true);

			$("#txt_yarn_lot").attr('placeholder', 'Display');
			$("#txt_yarn_lot").attr('readOnly', true);
			$("#txt_yarn_lot").val('');

			$("#txt_fin_dia").attr('placeholder', 'Display');
			$("#txt_fin_dia").attr('readOnly', true);
			$("#txt_fin_dia").val('');
			

			$("#txt_yarn_brand").attr('placeholder', 'Display');
			$("#txt_yarn_brand").attr('readOnly', true);
			$("#txt_yarn_brand").val('');

			$("#txt_yarn_count").attr('placeholder', 'Display');
			$("#txt_yarn_count").attr('readOnly', true);
			$("#txt_yarn_count").val('');

			$("#txt_finish_gsm").attr('placeholder', 'Display');
			$("#txt_finish_gsm").attr('readOnly', true);
			$("#txt_finish_gsm").val('');

			$("#txt_sl").attr('placeholder', 'Display');
			$("#txt_sl").attr('readOnly', true);
			$("#txt_sl").val('');

			$("#txt_color").attr('placeholder', 'Display');
			$("#txt_color").attr('readOnly', true);
			$("#txt_color").val('');
			
			$("#cbo_uom").attr('disabled', true);
			$("#cbo_uom").val(12);

			$("#txt_fab_fault").attr('readOnly', true);
			$("#txt_fab_fault").val('');

			$("#txt_yarn_receive_qty").attr('placeholder', 'Display');
			$("#txt_yarn_receive_qty").attr('readOnly', true);
			$("#txt_yarn_receive_qty").val('');

			$("#txt_yarn_issue_qty").attr('readOnly', true);
			$("#txt_yarn_issue_qty").val('');

			$("#txt_net_used_qty").attr('readOnly', true);
			$("#txt_net_used_qty").val('');

			$("#txt_returnable_qnty").attr('readOnly', true);
			$("#txt_returnable_qnty").val('');

			$("#txt_return_qnty").attr('placeholder', 'Display');
			$("#txt_return_qnty").attr('readOnly', true);
			$("#txt_return_qnty").val('');

			$("#txt_fabric_receive").attr('readOnly', true);
			$("#txt_fabric_receive").val('');

			$("#txt_receive_balance").attr('readOnly', true);
			$("#txt_receive_balance").val('');

			$("#txt_returnable_balance_qnty").attr('readOnly', true);
			$("#txt_returnable_balance_qnty").val('');

		}
	}
}

function change_service_goods(gs_value)
{
	if(gs_value == 1){
		$("#service_goods_title").text("Goods Name");
	}else{
		$("#service_goods_title").text("Service Name");
	}
}



</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs("../", $permission); ?><br/>
		<form name="debit_note_1" id="debit_note_1" autocomplete="off">
			<div style="width:100%; float:left; position:relative" align="center">
				<table width="80%;" cellpadding="0" cellspacing="2">
					<tr>
						<td width="100%" align="center" valign="top">
							<fieldset style="width:980px;">
								<legend>Debit Note Entry</legend>
								<br/>
								<fieldset style="width:1050px;">
									<table width="1050" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
										<tr>
											<td colspan="8" align="center"><b>Debit Note Number</b>
												<input type="text" name="txt_system_no" id="txt_system_no"
												class="text_boxes" style="width:160px"
												placeholder="Double Click To Search" onDblClick="open_mrrpopup()"
												readonly/>
												<input type="hidden" id="txt_mst_id" name="txt_mst_id" style="width:100px;" readonly/>
											</td>
										</tr>
										<tr>
											<td width="120" align="right" style="color: blue;" class="must_entry_caption">Company Name</td>
											<td width="170">
												<?
												echo create_drop_down("cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/debit_note_entry_controller', this.value, 'load_drop_down_com_location', 'location' );");
												?>
											</td>
											<td width="120" align="right" >Location:</td>
											<td width="160" id="location">
												<?=create_drop_down("cbo_location_name", 170, $blank_array,"", 1, "-- Select --", $selected, "" ); ?>
											</td>
											<td width="120" align="right" class="must_entry_caption" style="color:blue">Debit Note Against</td>
											<td>
												<?
												$debit_note_arr=[1=>'Goods',2=>'Service'];
												echo create_drop_down( "cbo_debit_note_against", 170, $debit_note_arr,"", 1, "-- Select --", $selected, "change_service_goods(this.value);load_drop_down( 'requires/debit_note_entry_controller', this.value, 'load_drop_down_goods_name', 'service_goods_placeholder_td' );", "", "");
												?>
											</td>
                                            <td width="120" align="right" style="color: blue" class="must_entry_caption" id="service_goods_title">Goods Name</td>
											<td id="service_goods_placeholder_td">
												<? echo create_drop_down("cbo_service_goods_name", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", ""); ?>
											</td>
										</tr>
										<tr>
											<td align="right" >Within Group</td>
											<td>
											<?
												echo create_drop_down("cbo_within_group", 170, $yes_no, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/debit_note_entry_controller', this.value, 'load_drop_down_working_com', 'working_company_td' );", "", "");
												?>
											</td>
											<td align="right">Working Company</td>
											<td id="working_company_td">
												<? echo create_drop_down("cbo_working_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", ""); ?>
											</td>
											<td align="right" style="color: blue">Basis</td>
											
											<td width="170" id="basis_td">
											<? echo create_drop_down("cbo_basis", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", ""); ?>
											</td>
                                            <td align="right" id="wo_issue_adj_label">Issue ID.</td>
											
											<td width="170"><input class="text_boxes"  type="text" name="txt_wo_issue_adj" id="txt_wo_issue_adj" onDblClick="openmypage('xx','WO/Issue/Adj')"  placeholder="Double Click" style="width:158px;" readonly disabled />
											<input type="hidden" id="txt_wo_issue_adj_id" name="txt_wo_issue_adj_id" value="" readonly/>
											
										</tr>
										<tr>
											<td align="right" id="knit_com">Issue Challan</td>
											<td>
                                                <input type="text" name="txt_issue_challan" id="txt_issue_challan" class="text_boxes" style="width:160px;" />
											</td>
											<td align="right">Goods Rcv Challan</td>
											<td>
                                                <input type="text" name="txt_goods_rcv_challan" id="txt_goods_rcv_challan" class="text_boxes" style="width:160px;" />
											</td>
											<td align="right" >Debit Note To</td>
											<td>
												<select name="cbo_debit_note_to" id="cbo_debit_note_to" style="width: 160px;" class="combo_boxes">
													<option value="0">Select</option>
													<option value="1">yarn supplier</option>
													<option value="2">Knitting Sub contract</option>
												</select>
											</td>
                                            <td align="right" >Debit Note Date</td>
											<td>
												<input type="text" name="txt_debit_note_date" id="txt_debit_note_date" class="datepicker" style="width:160px;" placeholder="Select Date" value="<? echo date('d-m-Y');?>" readonly/>
											</td>
										</tr>
										
									    <tr>
										<td align="right">Remarks</td>
										<td colspan="4"><input type="text" name="txt_remarks" id="txt_remarks"
											class="text_boxes" style="width:100%;"
											/></td>
										</tr>
										<tr>
											<td align="right">&nbsp;</td>
											<td colspan="4">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>

                                        <tr>
                                            <td style="width:130px;color: blue;" align="right" >Challan</td>
                                            <td ><input type="file" class="image_uploader" id="multiple_challan" name="multiple_challan" multiple="" style="width:170px"></td>
                                            <td style="width: 130px;" align="right">Other Docs</td>
                                            <td ><input type="file" class="image_uploader" id="multiple_other_docs" name="multiple_other_docs" multiple="" style="width:170px"></td>
                                            <td style="width:130px; " align="right">Image of Goods</td>
                                            <td ><input type="file" class="image_uploader" id="multiple_goods" name="multiple_goods" multiple="" style="width:170px"></td>
                                        </tr>
									</table>
								</fieldset>
								<br/>
                                <table id="tbl_child" width="96%" cellspacing="1" cellpadding="0">
									<tbody><tr>
										<td width="50%" valign="top" align="center">
											<fieldset style="width:460px; float:left">
												<legend>Debit Note Info</legend>
												<table width="530" cellspacing="2" cellpadding="0" border="0">
                                                    <tbody>
                                                        <tr>
															<td width="41" align="right" class="must_entry_caption">Item Description&nbsp;</td>
															<td  width="131">
																<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:148px;" placeholder="Double Click To Search" onDblClick="open_itemdesc()" readonly  />
																<input type="hidden" id="txt_rcvissue_id" name="txt_rcvissue_id" readonly/>
																<input type="hidden" id="txt_prod_id" name="txt_prod_id" readonly/>
																<input type="hidden" id="txt_fab_prod_id" name="txt_fab_prod_id" readonly/>
																<input type="hidden" id="txt_item_description_id" name="txt_item_description_id" readonly/>
																<input type="hidden" id="txt_requisition_no" name="txt_requisition_no" readonly/>
															</td>
                                                            <td width="41" align="right">Yarn Lot</td>
                                                            <td width="140"><input class="text_boxes" type="text" name="txt_yarn_lot" id="txt_yarn_lot" style="width:140px;" placeholder="Display" readonly  /></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="110" align="right">Finish Dia</td>
                                                            <td width="158"><input class="text_boxes" type="text" name="txt_fin_dia" id="txt_fin_dia" style="width:150px;" readonly></td>
                                                            
                                                            <td width="41" align="right">Yarn Brand</td>
                                                            <td>
																<input type="text" name="txt_yarn_brand" id="txt_yarn_brand" class="text_boxes" style="width:140px;" readonly/>
																<input type="hidden" id="txt_yarn_brand_id" name="txt_yarn_brand_id" readonly/>
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td width="41" align="right">Yarn Count</td>
                                                            <td width="131">
																<input type="text" name="txt_yarn_count" id="txt_yarn_count" class="text_boxes" style="width:150px;" readonly/>
																<input type="hidden" id="txt_yarn_count_id" name="txt_yarn_count_id" readonly/>
															</td>

                                                            <td width="41" align="right">Finish GSM</td>
                                                            <td >
																<input type="text" name="txt_finish_gsm" id="txt_finish_gsm" class="text_boxes" style="width:140px;" readonly/>
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td width="110" align="right">SL</td>
                                                            <td><input class="text_boxes" type="text" name="txt_sl" id="txt_sl" style="width:150px;" readonly></td>

                                                            <td width="41" align="right">Color</td>
                                                            <td>
																<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" readonly/>
																<input type="hidden" id="txt_color_id" name="txt_color_id" readonly/>
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td width="41" align="right">UOM</td>
                                                            <td><? echo create_drop_down("cbo_uom", 162, $unit_of_measurement, "", 1, "--Select--", $selected, "", 1); ?></td>
                                                            <td width="41" align="right">Process Name</td>
                                                            <td >
															<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:140px;" placeholder="Write/Browse" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="110" align="right">Fabric Fault</td>
                                                            <td><input class="text_boxes" type="text" name="txt_fab_fault" id="txt_fab_fault" style="width:150px;"  title="  Allowed Characters: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/<>?+[]{};: "></td>
                                                            <td width="110" align="right">Knitting Charge/Kg</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_knit_charge" id="txt_knit_charge" style="width:140px;" onKeyUp="fn_calKnitCost(this.value)"></td>
                                                        </tr>

                                                        <tr>
                                                            <td width="110" align="right">Process Loss Other Deduction Qty</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_plodq" id="txt_plodq" style="width:150px;" onKeyUp="fn_calDeducQnty(this.value)"></td>
                                                            <td width="110" align="right">Knitting QTY</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_knit_qnty" id="txt_knit_qnty" style="width:140px;" onKeyUp="fn_calKnitCost(this.value)"></td>
                                                        </tr>

                                                        <tr>
                                                            <td width="110" align="right">Debit Note QTY</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_debit_note_qnty" id="txt_debit_note_qnty" style="width:150px;" onKeyUp="fn_calDabitNQnty(this.value)"></td>
                                                            <td width="110" align="right">Dying Charge/Kg</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_dying_charge" id="txt_dying_charge" style="width:140px;" onKeyUp="fn_caldyingCost(this.value)"></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="110" align="right">Total Debit Amount</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_debit_amount" id="txt_debit_amount" style="width:150px;" readonly></td>
                                                            <td width="110" align="right">Dyeing Qty</td>
                                                            <td><input class="text_boxes_numeric" type="text" name="txt_dyeing_qnty" id="txt_dyeing_qnty" style="width:140px;" onKeyUp="fn_caldyingCost(this.value)"></td>
                                                        </tr>
                                                       
                                                    </tbody>
                                                </table>
											</fieldset>
											<fieldset style="width:460px; float:left; margin-left:5px">
												<legend>Display</legend>
												<table id="display_table" width="450" cellspacing="2" cellpadding="0" border="0">
													<tbody><tr>
														<td width="110" align="right">Yarn Receive Qty</td>
														<td width="100">
															<input class="text_boxes_numeric" type="text" name="txt_yarn_receive_qty" id="txt_yarn_receive_qty" style="width:100px;" readonly>
														</td>
														<td width="120" align="right">Yarn Rate&nbsp;</td>
														<td width="100"><input class="text_boxes_numeric" type="text" name="txt_yarn_rate" id="txt_yarn_rate" style="width:100px;" readonly></td>
													</tr>
													<tr>
														<td align="right">Yarn Issue Qty&nbsp;</td>
														<td>
															<input class="text_boxes_numeric" type="text" name="txt_yarn_issue_qty" id="txt_yarn_issue_qty" style="width:100px;" readonly >
														</td>
														<td align="right">Returnable BL. Value&nbsp;</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_returnable_bl_value" id="txt_returnable_bl_value" style="width:100px;" readonly></td>
													</tr>
													<tr>
														<td align="right">Net Used Quantity&nbsp;</td>
														<td>
															<input class="text_boxes_numeric" type="text" name="txt_net_used_qty" id="txt_net_used_qty" style="width:100px;"  readonly>
														</td>
														<td align="right">Process. Loss Deduc. value&nbsp;</td>
														<td>
															<input class="text_boxes_numeric" type="text" name="txt_process_loss_value" id="txt_process_loss_value" style="width:100px;" readonly>
														</td>
													</tr>
													<tr>
														<td align="right">Returnable Qty.</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_returnable_qnty" id="txt_returnable_qnty" style="width:100px;" readonly></td>
														<td align="right">Debit Note Value</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_debit_note_value" id="txt_debit_note_value" style="width:100px;"  readonly></td>
													</tr>
                                                    <tr>
														<td align="right">Yarn Returned Qty.</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:100px;" readonly ></td>
														<td align="right">Total Knitting Cost</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_knitting_cost" id="txt_total_knitting_cost" style="width:100px;" readonly></td>
													</tr>
                                                    <tr>
														<td align="right">Fabric Receive.</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_fabric_receive" id="txt_fabric_receive" style="width:100px;"  readonly></td>
														<td align="right">Total dyeing Cost</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_total_dyeing_cost" id="txt_total_dyeing_cost" style="width:100px;" readonly ></td>
													</tr>
                                                    <tr>
														<td align="right" style="width:110px;">Receive Balance</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_receive_balance" id="txt_receive_balance" style="width:100px;" readonly></td>
														
													</tr>
                                                    <tr>
														<td align="right" style="width:110px; color: blue;" class="must_entry_caption">Returnable Balance Qty</td>
														<td><input class="text_boxes_numeric" type="text" name="txt_returnable_balance_qnty" id="txt_returnable_balance_qnty" style="width:100px; " readonly ></td>
														
													</tr>
													<tr>
														<td align="right">&nbsp;</td>
														<td colspan="2">&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
												</tbody></table>
											</fieldset>
										</td>
									</tr>
								</tbody></table>
                            <table cellpadding="0" cellspacing="1" width="100%">
                            	<tr>
                            		<td colspan="6" align="center"></td>
                            	</tr>
                            	<tr>
                            		<td align="center" colspan="6" valign="middle" class="button_container">
                            			
										<input type="hidden" id="update_id" name="update_id" value="" readonly/>
										<? echo load_submit_buttons($permission, "fnc_debit_note_entry", 0, 0, "fnResetForm()", 1); ?>
                            			
                            		</td>
                            	</tr>
                            </table>
                        </fieldset>
                        <fieldset>
                        	<div style="width:970px;" id="list_container_yarn"></div>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>
       
    </form>
</div>
<script>
	// function change_service_goods(gs_value)
	// {
	// 	if(gs_value == 1){
	// 		$("#service_goods_title").text("Goods Name");
	// 	}else{
	// 		$("#service_goods_title").text("Service Name");
	// 	}
	// 	get_goods_services(gs_value);
 	// }

	// function get_goods_services(gs_value)
	// {
	// 	var http = createObject();
	// 	http.open("GET","requires/debit_note_entry_controller.php?action=get_goods_services&gs_value="+gs_value,true);
	// 	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	// 	http.send();
	// 	http.onreadystatechange = function() {
	// 		if (this.readyState == 4 && this.status == 200) {
	// 			$("#service_goods_placeholder_td").html(http.responseText);
	// 		}
	// 	};
	// }
</script>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>