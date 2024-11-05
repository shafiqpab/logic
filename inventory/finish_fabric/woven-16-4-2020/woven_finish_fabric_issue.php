<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Issue Entry

Functionality	:
JS Functions	:
Created by		:	zakaria
Creation date 	: 	06-12-2019
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
echo load_html_head_contents("Woven Finish Fabric Issue Info","../../", 1, 1, '','','');

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str,roll_field_reset)
{
	$('#cbo_sample_type').val(0);
	$('#cbo_sewing_source').val(0);
	$('#cbo_sewing_company').val(0);
	$('#cbo_buyer_name').val(0);
	$('#txt_issue_qnty').val('');
	$('#hidden_prod_id').val('');
	$('#all_po_id').val('');
	$('#save_data').val('');
	$('#save_string').val('');
	$('#txt_order_numbers').val('');
	$('#txt_fabric_received').val('');
	$('#txt_cumulative_issued').val('');
	$('#txt_yet_to_issue').val('');
	$('#previous_prod_id').val('');
	$('#txt_global_stock').val('');

	if(roll_field_reset==1)
	{
		$('#txt_no_of_roll').val('');
		$('#txt_no_of_roll').attr('disabled','disabled');
		$('#txt_no_of_roll').attr('placeholder','Display');
	}

	if(str==3 || str==10)
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').attr('disabled','disabled');
		$('#cbo_sewing_company').attr('disabled','disabled');
		$('#cbo_buyer_name').removeAttr('disabled','disabled');

		$('#txt_issue_qnty').removeAttr('readonly');
		$('#txt_issue_qnty').removeAttr('onDblClick');
		$('#txt_issue_qnty').removeAttr('placeholder');
	}
	else if(str==4 || str==8)
	{
		$('#cbo_sample_type').removeAttr('disabled','disabled');
		$('#cbo_sewing_source').removeAttr('disabled','disabled');
		$('#cbo_sewing_company').removeAttr('disabled','disabled');

		if(str==4)
		{
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_issue_qnty').attr('readonly','readonly');
			$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
			$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
		}
		else
		{
			$('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#txt_issue_qnty').removeAttr('readonly');
			$('#txt_issue_qnty').removeAttr('onDblClick');
			$('#txt_issue_qnty').removeAttr('placeholder');
		}
	}
	else
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').removeAttr('disabled','disabled');
		$('#cbo_sewing_company').removeAttr('disabled','disabled');
		$('#cbo_buyer_name').attr('disabled','disabled');
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
		$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
	}
}

function requisition_enable(basis){
	$('#txt_requisition_no').val('');
	$('#txt_requisition_id').val('');
	$('#hidden_job').val('');
	$('#txt_batch_lot').val('');
	$('#txt_batch_id').val('');
	$('#txt_fabric_desc').val('');
	$('#list_fabric_desc_container').html('');
	if (typeof basis == 'undefined'){
		basis = $('#cbo_issue_basis').val();
	}
	if(basis == 2){
		$('#txt_batch_lot').attr("placeholder", "Browse");
		$('#txt_batch_lot').attr('readonly','readonly');
		$('#txt_requisition_no').removeAttr('disabled','disabled');
	}
	else{
		$('#txt_batch_lot').attr("placeholder", "Write/Browse");
		$('#txt_batch_lot').removeAttr('readonly','readonly');
		$('#txt_requisition_no').attr('disabled','disabled');
	}
}

function openmypage_requisition(){
	var cbo_company_id = $('#cbo_company_id').val();
	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_controller.php?action=requisition_popup&company_id='+cbo_company_id,'Requisition Popup', 'width=770px,height=400px,center=1,resize=1,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		//var product_details=this.contentDoc.getElementById("hidden_prod_details").value;
		var req_no=this.contentDoc.getElementById("hidden_reqn_no").value;
		var req_id=this.contentDoc.getElementById("hidden_reqn_id").value;
		/*var job_no=this.contentDoc.getElementById("hidden_job").value;
		$('#txt_fabric_desc').val( product_details );
		$('#hidden_job').val( job_no );*/
		$('#txt_requisition_id').val( req_id );
		$('#txt_requisition_no').val( req_no );
		$('#list_fabric_desc_container').html('');
		show_list_view(req_id,'populate_list_view','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','');

	}

}
function change_color(v_id,e_color)
{
	if( $('#req_tr_'+v_id).attr('bgcolor')=='#FF9900')
		$('#req_tr_'+v_id).attr('bgcolor',e_color)
	else
		$('#req_tr_'+v_id).attr('bgcolor','#FF9900')
}



function openmypage_fabricDescription(roll)
{
	//alert(roll)
	var cbo_company_id = $('#cbo_company_id').val();
	var save_string = $('#save_string').val();
	var hidden_prod_id = $('#hidden_prod_id').val();
	var txt_fabric_desc = $('#txt_fabric_desc').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Fabric Description Info';
	var page_link = 'requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&save_string='+save_string+'&hidden_prod_id='+hidden_prod_id+'&txt_fabric_desc='+txt_fabric_desc+'&action=fabricDescription_popup_'+roll;

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		var product_details=this.contentDoc.getElementById("product_details").value; //Access form field with id="emailfield"
		var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
		var hidden_roll_issue_qnty=this.contentDoc.getElementById("hidden_roll_issue_qnty").value; //Access form field with id="emailfield"
		var save_string=this.contentDoc.getElementById("save_string").value; //Access form field with id="emailfield"

		//alert(product_id);

		$('#save_string').val( save_string );
		$('#txt_issue_qnty').val(hidden_roll_issue_qnty);
		$('#txt_no_of_roll').val( number_of_roll );
		$('#hidden_prod_id').val(product_id);
		$('#txt_fabric_desc').val(product_details);
	}
}

function openmypage_po()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var roll_maintained = $('#roll_maintained').val();
	var save_data = $('#save_data').val();
	var all_po_id = $('#all_po_id').val();
	var txt_issue_qnty = $('#txt_issue_qnty').val();
	var distribution_method = $('#distribution_method_id').val();
	var txt_batch_lot = $('#txt_batch_lot').val();
	var txt_batch_id = $('#txt_batch_id').val();
	var dtls_tbl_id = $('#update_dtls_id').val();
	var hidden_prod_id = $('#hidden_prod_id').val();
	var hidden_bodypart_id = $('#hidden_bodypart_id').val();
	var hidden_color_id = $('#hidden_color_id').val();
	var hidden_dia_width = $('#hidden_dia_width').val();
	var hidden_gsm_weight = $('#hidden_gsm_weight').val();
	var issue_basis = $('#cbo_issue_basis').val();
	var requisition_id = $('#txt_requisition_id').val();

	if(issue_basis == 2){
		if (form_validation('cbo_company_id*txt_fabric_desc*txt_batch_lot','Company*Fabric Description*Batch')==false)
		{
			return;
		}
	}
	if(issue_basis == 1){
		if (form_validation('cbo_company_id*txt_fabric_desc','Company*Fabric Description')==false)
		{
			return;
		}
	}


	var title = 'PO Info';
	var page_link = 'requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_issue_qnty='+txt_issue_qnty+'&prev_distribution_method='+distribution_method+'&hidden_prod_id='+hidden_prod_id+'&txt_batch_lot='+txt_batch_lot +'&txt_batch_id='+ txt_batch_id +'&hidden_bodypart_id='+hidden_bodypart_id+'&hidden_color_id='+hidden_color_id+'&hidden_dia_width='+hidden_dia_width+'&hidden_gsm_weight='+hidden_gsm_weight+'&action=po_popup&issue_basis='+issue_basis+'&requisition_id='+requisition_id;

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_data=this.contentDoc.getElementById("save_data").value;	 //Access form field with id="emailfield"
		var tot_issue_qnty=this.contentDoc.getElementById("tot_issue_qnty").value; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
		//alert(tot_issue_qnty+'Tipu');
		$('#save_data').val(save_data);
		$('#txt_issue_qnty').val(parseFloat(tot_issue_qnty).toFixed(2)); //tot_issue_qnty.toFixed(2)
		$('#cbo_buyer_name').val(buyer_id);
		$('#all_po_id').val(all_po_id);
		$('#txt_order_numbers').val(all_po_no);
		$('#distribution_method_id').val(distribution_method);

		if(all_po_id!="")
		{
			get_php_form_data(all_po_id+"**"+hidden_prod_id, "populate_data_about_order", "requires/woven_finish_fabric_issue_controller" );
			load_drop_down( 'requires/woven_finish_fabric_issue_controller',all_po_id, 'load_drop_down_gmt_item', 'gmt_item_td' );
		}
	}
}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Finish Fabric Issue Info';
	var page_link = 'requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=finishFabricIssue_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=370px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var finish_fabric_issue_id=this.contentDoc.getElementById("finish_fabric_issue_id").value; //Access form field with id="emailfield"

		reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','','roll_maintained');
		get_php_form_data(finish_fabric_issue_id, "populate_data_from_issue_master", "requires/woven_finish_fabric_issue_controller" );
		show_list_view(finish_fabric_issue_id,'show_finish_fabric_issue_listview','div_details_list_view','requires/woven_finish_fabric_issue_controller','');
		set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
		var issueBasis=$('#cbo_issue_basis').val();
		if(issueBasis == 2)
		{
			$('#txt_batch_lot').attr("placeholder", "Browse");
			$('#txt_batch_lot').attr('readonly','readonly');
		} 
		else
		{
			$('#txt_batch_lot').attr("placeholder", "Write/Browse");
		 	$('#txt_batch_lot').removeAttr('readonly','readonly');
		}
	}
}

function fnc_fabric_issue_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "woven_finish_fabric_issue_print", "requires/woven_finish_fabric_issue_controller" )
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}

		var cbo_issue_purpose=$('#cbo_issue_purpose').val();

		if( form_validation('cbo_company_id*txt_issue_date*txt_challan_no*cbo_issue_basis','Company*Issue Date*Challan No*Issue Basis')==false )
		{
			return;
		}

		if(cbo_issue_purpose==4 || cbo_issue_purpose==8)
		{
			if( form_validation('cbo_sample_type','Sample Type')==false )
			{
				return;
			}
		}

		if(cbo_issue_purpose==4 || cbo_issue_purpose==8 || cbo_issue_purpose==9)
		{
			if(form_validation('cbo_sewing_source*cbo_sewing_company','Sewing Source*Sewing Company')==false )
			{
				return;
			}
		}

		if(form_validation('cbo_store_name*txt_batch_lot*txt_fabric_desc*txt_issue_qnty','Store Name*Batch/Lot*Batch No*Fabric Description*Issue Qnty')==false )
		{
			return;
		}

		var dataString = "txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*cbo_cutting_floor*txt_remarks*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_batch_lot*txt_batch_id*txt_fabric_desc*txt_issue_qnty*txt_no_of_roll*hidden_prod_id*previous_prod_id*update_id*save_data*save_string*update_dtls_id*update_trans_id*hidden_issue_qnty*txt_issue_qnty*all_po_id*roll_maintained*cbo_item_name*hidden_bodypart_id*cbo_issue_basis*txt_requisition_id*hidden_job*txt_requisition_no";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");

		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/woven_finish_fabric_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_issue_entry_reponse;
	}
}

function fnc_fabric_issue_entry_reponse()
{
	if(http.readyState == 4)
	{
		//release_freezing();
		//alert(http.responseText);return;

		var reponse=trim(http.responseText).split('**');
                if(reponse[0]*1 == 20)
                {
                        alert(reponse[1]);
                        release_freezing();
                        return;
                }
		show_msg(trim(reponse[0]));

		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[2]);
			$("#txt_system_id").val(reponse[3]);
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_issue_purpose').attr('disabled','disabled');
			$('#cbo_issue_basis').attr('disabled','disabled');
			$('#txt_requisition_no').attr('disabled','disabled');

			reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot*cbo_issue_basis*txt_requisition_no*txt_requisition_id*hidden_job');

			show_list_view(reponse[2],'show_finish_fabric_issue_listview','div_details_list_view','requires/woven_finish_fabric_issue_controller','');
			$('#txt_fabric_desc').focus();
			set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
		}

		release_freezing();
	}
}

function js_set_value(id)
{
	var roll_maintained=$('#roll_maintained').val();
	get_php_form_data(id+"**"+roll_maintained,'populate_issue_details_form_data', 'requires/woven_finish_fabric_issue_controller')
}

function openmypage_batchLot()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var cbo_issue_basis = $('#cbo_issue_basis').val();
	var cbo_fabric_desc = $('#txt_fabric_desc').val();
	var job_no = $('#hidden_job').val();
	if(cbo_issue_basis == 2)
	{
		if (form_validation('cbo_company_id*cbo_store_name*cbo_issue_basis*txt_requisition_no*txt_fabric_desc','Company*Store*Issue Basis*Requisition NO.*Fabric Description')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_fabric_desc='+cbo_fabric_desc+'&job_no='+job_no+'&action=requisition_batch_lot_popup';
			var title='Batch/Lot Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=570px,height=200px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var data=this.contentDoc.getElementById("hidden_data").value;
				var data=data.split("**");
				$('#hidden_prod_id').val(data[0]);
				$('#txt_global_stock').val(data[1]);
				$('#hidden_bodypart_id').val(data[2]);
			    $('#txt_uom').val(data[3]);
			    $('#txt_batch_id').val(data[5]);
			    $('#hidden_color_id').val(data[6]);
			    $('#hidden_dia_width').val(data[7]);
			    $('#hidden_gsm_weight').val(data[8]);
			    $('#txt_batch_lot').val(data[9]);
			    $('#txt_order_numbers').val(data[10]);

			    if (data[4]==1)
			    {
					$('#txt_issue_qnty').removeAttr('readonly','readonly');
					$('#txt_issue_qnty').removeAttr('onClick','onClick');
					$('#txt_issue_qnty').removeAttr('placeholder','placeholder');

			    }
			    else
			    {
					$('#txt_issue_qnty').attr('readonly','readonly');
					$('#txt_issue_qnty').attr('placeholder','Single Click');
					//openmypage_po();
			    }
			    if(data[11]!="")
				{
					get_php_form_data(data[11]+"**"+data[0], "populate_data_about_order", "requires/woven_finish_fabric_issue_controller" );
					load_drop_down( 'requires/woven_finish_fabric_issue_controller',data[11], 'load_drop_down_gmt_item', 'gmt_item_td' );
				}
			}
		}
	}
	if(cbo_issue_basis == 1){
		if (form_validation('cbo_company_id*cbo_store_name*cbo_issue_basis','Company*Store*Issue Basis')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&action=batch_lot_popup';
			var title='Batch/Lot Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batchLot_no=this.contentDoc.getElementById("hidden_batchLot_no").value; //Access form field with id="emailfield"
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;

				reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot*cbo_issue_basis');

				$('#txt_batch_lot').val(batchLot_no);
				$('#txt_batch_id').val(batch_id);


				show_list_view(batchLot_no+'_'+cbo_company_id+'_'+batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
			}
		}
	}

}

function check_batchLot(data)
{
	var cbo_company_id=$('#cbo_company_id').val();
	var txt_batch_id=$('#txt_batch_id').val();
	if(form_validation('cbo_company_id','Company')==false)
	{
		$('#txt_batch_lot').val('');
		return;
	}

	reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot**cbo_issue_basis');

	show_list_view(data+'_'+cbo_company_id+'_'+txt_batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
}

function requisition_set_data(data)
{
	var data=data.split("**");
	$('#txt_batch_lot').val('');
	$('#txt_batch_id').val('');
	$('#txt_issue_qnty').val('');
	$('#txt_uom').val('');
	$('#txt_order_numbers').val('');
	$('#txt_fabric_received').val('');
	$('#txt_cumulative_issued').val('');
	$('#txt_yet_to_issue').val('');
	$('#txt_global_stock').val('');
	$('#txt_issue_qnty').val('');
	$('#txt_issue_qnty').val('');
	$('#hidden_bodypart_id').val();
	$('#hidden_color_id').val();
    $('#hidden_dia_width').val();
    $('#hidden_gsm_weight').val();

	$('#hidden_job').val(data[0]);
	$('#txt_fabric_desc').val(data[1]);

}

function set_form_data(data)
{
	var data=data.split("**");
	$('#hidden_prod_id').val(data[0]);
	$('#txt_fabric_desc').val(data[1]);
	$('#txt_global_stock').val(data[2]);
	$('#hidden_bodypart_id').val(data[3]);
    $('#txt_uom').val(data[4]);
    $('#txt_batch_id').val(data[6]);
    $('#hidden_color_id').val(data[7]);
    $('#hidden_dia_width').val(data[8]);
    $('#hidden_gsm_weight').val(data[9]);
    if (data[5]==1)
    {
		$('#txt_issue_qnty').removeAttr('readonly','readonly');
		$('#txt_issue_qnty').removeAttr('onClick','onClick');
		$('#txt_issue_qnty').removeAttr('placeholder','placeholder');

    }
    else
    {
		$('#txt_issue_qnty').attr('readonly','readonly');
		//$('#txt_issue_qnty').attr('onClick','openmypage_po();');
		$('#txt_issue_qnty').attr('placeholder','Single Click');
		openmypage_po();
    }
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
    <div style="width:840px; float:left;" align="center">
        <fieldset style="width:840px;">
        <legend>Woven Finish Fabric Entry</legend>
        <br>
        	<fieldset style="width:820px;">
                <table width="800" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                    <td colspan="3" align="right"><strong>Issue No</strong></td>
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
								echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller*3', 'store','store_td', this.value);requisition_enable(document.getElementById('cbo_issue_basis').value)" );
							?>
                        </td>
                        <td>Issue Purpose</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_issue_purpose", 170,$yarn_issue_purpose,"", 0,"",'9',"active_inactive(this.value,0);",'','3,4,8,9,10');
                            ?>
                        </td>
                        <td>Sample Type</td>
                        <td>
                            <?
								echo create_drop_down( "cbo_sample_type", 170, "select id, sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name","id,sample_name", 1, "--Select Sample Type--", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Issue Date</td>
                        <td>
                            <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:158px;" value="<? echo date("d-m-Y"); ?>" readonly placeholder="Select Date" disabled />
                        </td>
                        <td class="must_entry_caption">Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:158px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td class="must_entry_caption">Sewing Source</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 170, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewingcom_td');","","","","","2");
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Sewing Company</td>
                        <td id="sewingcom_td">
                            <?
                                echo create_drop_down("cbo_sewing_company", 170, $blank_array,"", 1,"-- Select Sewing Company --", 0,"");
                            ?>
                        </td>
                        <td>Issue Basis</td>
                        <td><? $woven_issue_basis = array(1=>'Batch Basis',2=>'Requisition Basis');
							   echo create_drop_down( "cbo_issue_basis", 170, $woven_issue_basis,"", 1, "-- Select Basis --", $selected, "requisition_enable(this.value)",0);
 							?></td>
 						<td>Requisition No.</td>
 						<td><input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:160px;" placeholder="Browse Requisition" onDblClick="openmypage_requisition()" disabled=""/>
 							<input type="hidden" name="txt_requisition_id" id="txt_requisition_id">
 							<input type="hidden" name="hidden_job" id="hidden_job">

 						</td>
                    </tr>
                    <tr>
                        <td style="visibility:hidden" class="must_entry_caption">Buyer Name</td>
                        <td style="visibility:hidden" id="buyer_td_id">
                            <?
							   echo create_drop_down( "cbo_buyer_name", 170, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1 );
 							?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="820" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="60%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                	<td width="30%" class="must_entry_caption">Store Name</td>
                                    <td id="store_td">
                                        <?
                                            echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "--Select store--", 0, "" );
                                        ?>
                                    </td>
                                    <td></td><td></td>
                                </tr>
								<tr>
                                   <td >Floor</td>
                                   <td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                             	<tr>
                                   <td>Room</td>
                                   <td id="room_td">
										<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                    <td>Rack</td>
                                     <td id="rack_td">
										<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                    <td>Shelf</td>
                                     <td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                    <td>Bin/Box</td>
                                     <td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                 <tr>
                                    <td>Roll Qty</td>
                                    <td>
                                    	<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:158px" />
                                    </td>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Batch/Lot</td>
                                    <td>
                                    	<input type="text" name="txt_batch_lot" id="txt_batch_lot" class="text_boxes" style="width:158px;" placeholder="Write/Browse" onDblClick="openmypage_batchLot();" onChange="check_batchLot(this.value);" />
										<input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" >
                                    </td>
                                <td></td><td></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Fabric Description</td>
                                    <td id="fabricDesc_td" colspan="3">
                                    	<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:300px;" placeholder="Display" disabled /> <!--onDblClick="openmypage_fabricDescription();"--></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                    	<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:158px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_po();" /></td>
                                    <td>UOM</td>
                                    <td>
                                        <input type="text" name="txt_uom" id="txt_uom" class="text_boxes" style="width: 50px;" readonly disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Garments Item</td>
                                    <td id="gmt_item_td">
                                         <?
										 echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Gmt. Item --", "", "",0,0 );
										 ?>
                                    </td>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                	<td>Cutting Unit No.</td>
			                        <td id="cutting_unit_no">
			                            <?
			                            echo create_drop_down( "cbo_cutting_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "",0 );
										?>
			                        </td>
			                        <td></td><td></td>
                                </tr>
                                <tr>
                                	<td>Remarks</td>
                                  	<td colspan="3"> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width: 300px;"></td>
                                </tr>
							</table>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="40%" valign="top">
						<fieldset>
                        <legend>Display</legend>
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >
                                <tr>
                                    <td>Order Numbers</td>
                                	<td>
                                    	<input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:160px" disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Fabric Received</td>
                                    <td><input type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes_numeric" style="width:160px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Cumulative Issued</td>
                                    <td><input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" style="width:160px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Yet to Issue</td>
                                    <td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:160px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Global Stock</td>
                                    <td><input type="text" name="txt_global_stock" id="txt_global_stock" class="text_boxes_numeric" style="width:160px" disabled /></td>
                                </tr>
                            </table>
                       </fieldset>
              		</td>
				</tr>
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_fabric_issue_entry", 0,1,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose\');active_inactive(9,1);')",1);
                        ?>
                        <input type="hidden" id="update_id" name="update_id" value="" >
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="save_string" id="save_string" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" readonly>
                        <input type="hidden" name="hidden_bodypart_id" id="hidden_bodypart_id" readonly>
                        <input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly>
                        <input type="hidden" name="hidden_dia_width" id="hidden_dia_width" readonly>
                        <input type="hidden" name="hidden_gsm_weight" id="hidden_gsm_weight" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                        <input type="hidden" name="txt_issue_qnty" id="txt_issue_qnty" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                    </td>
                </tr>
            </table>
            <div style="width:820px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:450px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <!-- <div id="list_requisition_desc" style="width:400px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div> -->
	</form>
</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
