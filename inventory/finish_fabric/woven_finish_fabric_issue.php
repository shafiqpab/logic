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
$user_id=$_SESSION['logic_erp']['user_id'];

//========== user credential start ========
	$user_id = $_SESSION['logic_erp']['user_id'];
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id,working_unit_id FROM user_passwd where id=$user_id");
	$company_id = $userCredential[0][csf('company_id')];
	//$working_company_id = $userCredential[0][csf('working_unit_id')];
	//$store_location_id = $userCredential[0][csf('store_location_id')];
	//$item_cate_id = $userCredential[0][csf('item_cate_id')];
	//$location_id = $userCredential[0][csf('location_id')];

	$company_credential_cond = "";

	if ($company_id >0) {
	    $company_credential_cond = " and comp.id in($company_id)";
	}




//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Finish Fabric Issue Info","../../", 1, 1, '','','');
//print_r($_SESSION['logic_erp']['data_arr'][19])."sdws"; die;
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";



    /*function rcv_basis_reset()
	{
		document.getElementById('cbo_receive_basis').value=0;
	} */

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][19] );


		//echo $_SESSION['logic_erp']['data_arr'][17]."sdws"; die;
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

function active_inactive(str,roll_field_reset)
{
	$('#cbo_sample_type').val(0);
	//$('#cbo_sewing_source').val(0);
	//$('#cbo_sewing_company').val(0);
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

	if(str==3)
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').attr('disabled','disabled');
		$('#cbo_sewing_company').attr('disabled','disabled');
		$('#cbo_buyer_name').removeAttr('disabled','disabled');

		$('#txt_issue_qnty').removeAttr('readonly');
		$('#txt_issue_qnty').removeAttr('onDblClick');
		$('#txt_issue_qnty').removeAttr('placeholder');
	}
	else if(str==10)
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').attr('disabled','disabled');
		$('#cbo_sewing_company').attr('disabled','disabled');
		$('#cbo_buyer_name').removeAttr('disabled','disabled');

		/*$('#txt_issue_qnty').removeAttr('readonly');
		$('#txt_issue_qnty').removeAttr('onDblClick');
		$('#txt_issue_qnty').removeAttr('placeholder');*/
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
		$('#txt_issue_qnty').attr('placeholder','Double Click To Open');
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
			$('#txt_issue_qnty').attr('placeholder','Double Click To Open');
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
		$('#txt_issue_qnty').attr('placeholder','Double Click To Open');
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
		$('#txt_batch_lot').attr("placeholder", "Browse");
		//$('#txt_batch_lot').removeAttr('readonly','readonly');
		$('#txt_requisition_no').attr('disabled','disabled');
		$('#txt_batch_lot').removeAttr('disabled','disabled');
		$('#cbo_body_part').removeAttr('disabled','disabled');
		$('#cbo_store_name').removeAttr('disabled','disabled');
	}
}

function openmypage_requisition(){
	var cbo_company_id = $('#cbo_company_id').val();
	var hdn_variable_setting_status = $('#hdn_variable_setting_status').val();
	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_controller.php?action=requisition_popup&company_id='+cbo_company_id+'&hdn_variable_setting_status='+hdn_variable_setting_status,'Requisition Popup', 'width=770px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		//var product_details=this.contentDoc.getElementById("hidden_prod_details").value;
		var req_no=this.contentDoc.getElementById("hidden_reqn_no").value;
		var req_id=this.contentDoc.getElementById("hidden_reqn_id").value;
		var req_poid=this.contentDoc.getElementById("hidden_reqn_po_id").value;
		/*var job_no=this.contentDoc.getElementById("hidden_job").value;
		$('#txt_fabric_desc').val( product_details );
		$('#hidden_job').val( job_no );*/
		$('#txt_requisition_id').val( req_id );
		$('#txt_requisition_no').val( req_no );
		$('#txt_requisition_poid').val( req_poid );
		$('#list_fabric_desc_container').html('');

		show_list_view(req_id+'_'+req_poid+'_'+hdn_variable_setting_status,'populate_list_view','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','');
		reset_form('','list_fabric_desc_container_rquisition','','','','');


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
	var cbo_company_id 		= $('#cbo_company_id').val();
	var cbo_issue_purpose 	= $('#cbo_issue_purpose').val();
	var roll_maintained 	= $('#roll_maintained').val();
	var save_data 			= $('#save_data').val();
	var all_po_id 			= $('#all_po_id').val();
	var txt_issue_qnty 		= $('#txt_issue_qnty').val();
	var distribution_method = $('#distribution_method_id').val();
	var txt_batch_lot 		= $('#txt_batch_lot').val();
	var txt_batch_id 		= $('#txt_batch_id').val();
	var dtls_tbl_id 		= $('#update_dtls_id').val();
	var hidden_prod_id 		= $('#hidden_prod_id').val();
	var hidden_bodypart_id 	= $('#hidden_bodypart_id').val();
	var hidden_color_id 	= $('#hidden_color_id').val();
	var hidden_dia_width 	= $('#hidden_dia_width').val();
	var hidden_gsm_weight 	= $('#hidden_gsm_weight').val();
	var issue_basis 		= $('#cbo_issue_basis').val();
	var requisition_id 		= $('#txt_requisition_id').val();

	var cbo_store_name 		= $("#cbo_store_name").val();
	var txt_floor 			= $("#cbo_floor").val();
	var txt_room 			= $("#cbo_room").val();
	var txt_rack 			= $("#txt_rack").val();
	var txt_shelf 			= $("#txt_shelf").val();
	var txt_bin 			= $("#cbo_bin").val();
	var cbouom				= $("#hidden_uom").val();
	var cbo_body_part		= $("#cbo_body_part").val();
	var txt_rate			= $("#txt_hdn_rate").val();
	var fabric_desc_id		= $("#hidden_detarmination_id").val();
	var hidden_width_original= $("#hidden_width_original").val();
	var hidden_weight_original= $("#hidden_weight_original").val();
	var txt_fabric_ref 		= $("#txt_fabric_ref").val();
	var txt_rd_no 			= $("#txt_rd_no").val();
	var cbo_weight_type 	= $("#cbo_weight_type").val();
	var txt_cutable_width 	= $("#txt_cutable_width").val();
	var hidden_selected_po_ids 	= $("#hidden_selected_po_ids").val();
	var update_dtls_id		= $("#update_dtls_id").val();
	var po_popup_patern_variable		= $("#hdn_variable_setting_status").val();

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
	//var po_popup_patern_variable=2;
	if (po_popup_patern_variable==1) {var actionName="po_popup_booking_wise";var title = 'Style Info';}else{var actionName="po_popup";var title = 'PO Info';}


	var page_link = 'requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_issue_purpose='+cbo_issue_purpose+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_issue_qnty='+txt_issue_qnty+'&prev_distribution_method='+distribution_method+'&hidden_prod_id='+hidden_prod_id+'&txt_batch_lot='+encodeURIComponent(txt_batch_lot) +'&txt_batch_id='+ txt_batch_id +'&hidden_bodypart_id='+hidden_bodypart_id+'&hidden_color_id='+hidden_color_id+'&hidden_dia_width='+hidden_dia_width+'&hidden_gsm_weight='+hidden_gsm_weight+'&requisition_id='+requisition_id +'&cbo_store_name='+cbo_store_name+'&txt_floor='+txt_floor+'&txt_room='+txt_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&txt_bin='+txt_bin+'&cbouom='+cbouom+'&cbo_body_part='+cbo_body_part+'&action='+actionName+'&issue_basis='+issue_basis+'&fabric_desc_id='+fabric_desc_id+'&txt_rate='+txt_rate+'&hidden_weight_original='+hidden_weight_original+'&hidden_width_original='+hidden_width_original+'&txt_fabric_ref='+txt_fabric_ref+'&txt_rd_no='+txt_rd_no+'&cbo_weight_type='+cbo_weight_type+'&txt_cutable_width='+txt_cutable_width+'&hidden_selected_po_ids='+hidden_selected_po_ids+'&update_dtls_id='+update_dtls_id;
	
	$('#cbo_issue_purpose').attr('disabled','disabled');

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{

		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_data=this.contentDoc.getElementById("save_data").value;	 //Access form field with id="emailfield"
		var tot_issue_qnty=this.contentDoc.getElementById("tot_issue_qnty").value; //Access form field with id="emailfield"
		var tot_rollNo=this.contentDoc.getElementById("tot_rollNo").value; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
		//alert(tot_issue_qnty+'Tipu');
		$('#cbo_issue_purpose').attr('disabled',false);
		$('#save_data').val(save_data);
		if(tot_issue_qnty==""){tot_issue_qnty=0;}
		$('#txt_issue_qnty').val(parseFloat(tot_issue_qnty).toFixed(2)); //tot_issue_qnty.toFixed(2)
		$('#cbo_buyer_name').val(buyer_id);
		$('#all_po_id').val(all_po_id);
		$('#txt_order_numbers').val(all_po_no);
		$('#distribution_method_id').val(distribution_method);
		$('#txt_no_of_roll').val(tot_rollNo);
		if(all_po_id!="")
		{
			get_php_form_data(all_po_id+"**"+hidden_prod_id+"**"+cbo_store_name+"**"+txt_floor+"**"+txt_room+"**"+txt_rack+"**"+txt_shelf+"**"+txt_bin+"**"+cbo_body_part+"**"+txt_batch_id, "populate_data_about_order", "requires/woven_finish_fabric_issue_controller" );
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

		reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','','roll_maintained*hdn_variable_setting_status');
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
			$('#txt_batch_lot').attr("placeholder", "Browse");
		 	$('#txt_batch_lot').removeAttr('readonly','readonly');
		}
		$('#hidden_bookingNo').val("");
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
		/* if(operation==2)
		{
			show_msg('13');
			return;
		} */

		var is_posted_account_status=$('#hidden_is_posted_account_id').val()*1;
		if(is_posted_account_status==1)
		{
			alert("Save/Update Restriction, Already Posted In Accounting");
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

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][19]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][19]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][19]);?>')==false)
			{
				return;
			}
		}

		var dataString = "txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*cbo_cutting_floor*txt_remarks*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_batch_lot*txt_batch_id*txt_fabric_desc*txt_issue_qnty*txt_no_of_roll*hidden_prod_id*previous_prod_id*update_id*save_data*save_string*update_dtls_id*update_trans_id*hidden_issue_qnty*txt_issue_qnty*all_po_id*roll_maintained*cbo_item_name*hidden_bodypart_id*cbo_issue_basis*txt_requisition_id*hidden_job*txt_requisition_no*hidden_uom*txt_fabric_ref*txt_rd_no*txt_weight*cbo_weight_type*txt_cutable_width*hidden_detarmination_id*txt_hdn_rate*txt_hdn_cons_amount*hidden_weight_original*hidden_width_original*cbo_extra_status*cbo_sewing_location";
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
		show_msg(trim(reponse[0]));

		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[2]);
			$("#txt_system_id").val(reponse[3]);
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_issue_purpose').attr('disabled','disabled');
			$('#cbo_issue_basis').attr('disabled','disabled');
			$('#txt_requisition_no').attr('disabled','disabled');
			if(issue_basis==2)
			{
				$('#txt_batch_lot').attr('disabled','disabled');
			}
			else
			{
				$('#txt_batch_lot').removeAttr('disabled','disabled');
				$('#cbo_store_name').removeAttr('disabled','disabled');
			}

			var company_id=$('#cbo_company_id').val();
			var batch_id=$('#txt_batch_id').val();
			var store_id=$('#cbo_store_name').val();
			var booking_no=$('#hidden_bookingNo').val();
			var batchLot_no=$('#txt_batch_lot').val();
			var requisition_id=$('#txt_requisition_id').val();


			reset_form('finishFabricEntry_1','','cbo_store_name*txt_batch_lot*txt_batch_id','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot*cbo_issue_basis*txt_requisition_no*txt_requisition_id*hidden_job*hdn_variable_setting_status*cbo_extra_status*cbo_sewing_location');

			show_list_view(reponse[2],'show_finish_fabric_issue_listview','div_details_list_view','requires/woven_finish_fabric_issue_controller','');
			$('#txt_fabric_desc').focus();
			var issue_basis=$('#cbo_issue_basis').val();
			if(issue_basis==2)
			{
				var hdn_variable_setting_status=$('#hdn_variable_setting_status').val();
				get_php_form_data(reponse[2]+"**"+roll_maintained+"**"+reponse[4]+"**"+hdn_variable_setting_status,'populate_issue_balanc_list_requi_basis', 'requires/woven_finish_fabric_issue_controller');
				show_list_view(requisition_id+"_"+reponse[4],'populate_list_view','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','');

				//show_list_view(batch_id+'_'+company_id+'_'+store_id+'_'+booking_no+'_'+batchLot_no,'show_fabric_desc_listview','list_fabric_desc_container_rquisition','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
			}
			else
			{
				$("#list_fabric_desc_container").html("");
				//show_list_view(batch_id+'_'+company_id+'_'+store_id+'_'+booking_no+'_'+batchLot_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
			}

			set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
		}
		else if(reponse[0]==2)
		{
			if(reponse[3]==1)
			{
				release_freezing();
				//location.reload();
				reset_form('finishFabricEntry_1','','cbo_store_name*txt_batch_lot*txt_batch_id*update_dtls_id*update_trans_id*txt_batch_id*txt_batch_lot*cbo_store_name*cbo_body_part*txt_fabric_desc*hidden_detarmination_id*txt_color*hidden_color_id*save_data*save_string','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*roll_maintained*cbo_issue_basis*txt_requisition_no*txt_requisition_id*hidden_job*hdn_variable_setting_status*cbo_extra_status*cbo_sewing_location');

				$("#div_details_list_view").html("");
				$("#list_fabric_desc_container").html("");

			}
			if(reponse[3]==2)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				show_list_view(reponse[1],'show_finish_fabric_issue_listview','div_details_list_view','requires/woven_finish_fabric_issue_controller','');
				set_button_status(reponse[3], permission, 'fnc_fabric_issue_entry',1,1);

 				reset_form('finishFabricEntry_1','','cbo_store_name*txt_batch_lot*txt_batch_id*update_dtls_id*update_trans_id*txt_batch_id*txt_batch_lot*cbo_store_name*cbo_body_part*txt_fabric_desc*hidden_detarmination_id*txt_color*hidden_color_id*save_data*save_string','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*roll_maintained*cbo_issue_basis*txt_requisition_no*txt_requisition_id*hidden_job*hdn_variable_setting_status*cbo_extra_status*cbo_sewing_location');
				//reset_form('','','txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_batch_lot*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*cbo_body_part*txt_roll*txt_remarks*hdn_booking_no*hdn_booking_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width*update_finish_fabric_id*txt_weight_edit*txt_width_edit','','','');
				release_freezing();
			}
		}
		else if(reponse[0]*1==20)
		{
			alert(reponse[1]);
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}

		release_freezing();
	}
}

function js_set_value(id)
{
	var roll_maintained=$('#roll_maintained').val();
	var hdn_variable_setting_status=$('#hdn_variable_setting_status').val();
	get_php_form_data(id+"**"+roll_maintained+"**"+hdn_variable_setting_status,'populate_issue_details_form_data', 'requires/woven_finish_fabric_issue_controller')
}

function openmypage_batchLot()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var cbo_issue_basis = $('#cbo_issue_basis').val();
	var cbo_issue_purpose = $('#cbo_issue_purpose').val();
	var cbo_fabric_desc = $('#txt_fabric_desc').val();
	var hdn_variable_setting_status = $('#hdn_variable_setting_status').val();
	var job_no = $('#hidden_job').val();
	if(cbo_issue_basis == 2)
	{
		if (form_validation('cbo_company_id*cbo_store_name*cbo_issue_basis*txt_requisition_no*txt_fabric_desc','Company*Store*Issue Basis*Requisition NO.*Fabric Description')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_fabric_desc='+cbo_fabric_desc+'&job_no='+job_no+'&hdn_variable_setting_status='+hdn_variable_setting_status+'&action=requisition_batch_lot_popup';
			var title='Batch/Lot Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=200px,center=1,resize=1,scrolling=0','../');
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
					$('#txt_issue_qnty').attr('placeholder','Double Click To Open');
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
			var page_link='requires/woven_finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_fabric_desc='+cbo_fabric_desc+'&job_no='+job_no+'&hdn_variable_setting_status='+hdn_variable_setting_status+'&cbo_issue_purpose='+cbo_issue_purpose+'&action=batch_lot_popup';
			var title='Batch/Lot Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batchLot_no=this.contentDoc.getElementById("hidden_batchLot_no").value; //Access form field with id="emailfield"
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var company_id=this.contentDoc.getElementById("txt_company_id").value;
				var store_id=this.contentDoc.getElementById("hidden_store_id").value;
				var booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
				var jobNo=this.contentDoc.getElementById("hidden_job_no").value;
				var poIds=this.contentDoc.getElementById("hidden_poIds").value;
				var colorID=this.contentDoc.getElementById("hidden_colorId").value;

				reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot*cbo_issue_basis*hdn_variable_setting_status*cbo_extra_status*cbo_sewing_location');

				$('#txt_batch_lot').val(batchLot_no);
				$('#txt_batch_id').val(batch_id);
				$('#hidden_bookingNo').val(booking_no);

				if(hdn_variable_setting_status==1)
				{
					show_list_view(batch_id+'_'+company_id+'_'+store_id+'_'+booking_no+'_'+batchLot_no+'_'+jobNo+'_'+poIds+'_'+hdn_variable_setting_status+'_'+cbo_issue_purpose+'_'+colorID,'show_fabric_desc_listview_style_wise','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
				}
				else
				{
					show_list_view(batch_id+'_'+company_id+'_'+store_id+'_'+booking_no+'_'+batchLot_no+'_'+hdn_variable_setting_status+'_'+colorID,'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
				}


			}
		}
	}

}

/*function check_batchLot(data)
{
	var cbo_company_id=$('#cbo_company_id').val();
	var txt_batch_id=$('#txt_batch_id').val();
	var cbo_issue_basis=$('#cbo_issue_basis').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var booking_no =$('#hidden_bookingNo').val();


	if(form_validation('cbo_company_id','Company')==false)
	{
		$('#txt_batch_lot').val('');
		return;
	}

	reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cutting_unit_no*cbo_store_name*roll_maintained*txt_batch_lot**cbo_issue_basis');

	show_list_view(txt_batch_id+'_'+cbo_company_id+'_'+cbo_store_name+'_'+booking_no+'_'+data,'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
}
*/
function requisition_set_data(data)
{
	var cbo_issue_purpose = $('#cbo_issue_purpose').val();
	var data=data.split("**");
	$('#txt_batch_lot').val('');
	$('#txt_batch_id').val('');
	$('#txt_issue_qnty').val('');
	$('#txt_uom').val('');
	$('#hidden_uom').val('');
	$('#txt_order_numbers').val('');
	$('#txt_fabric_received').val('');
	$('#txt_cumulative_issued').val('');
	$('#txt_yet_to_issue').val('');
	$('#txt_global_stock').val('');
	$('#cbo_body_part').val('');


	$('#hidden_bodypart_id').val();
	$('#hidden_color_id').val();
    $('#hidden_dia_width').val();
    $('#hidden_gsm_weight').val();
    var cbo_issue_basis=$('#cbo_issue_basis').val();

    $('#txt_rack_name').val('');
	$('#txt_shelf_name').val('');
	$('#txt_floor_name').val('');
	$('#txt_room_name').val('');
	$('#txt_bin_name').val('');

	$('#txt_rack').val('');
	$('#txt_shelf').val('');
	$('#txt_color').val('');
	$('#cbo_floor').val('');
	$('#cbo_room').val('');
	$('#cbo_store_name').val('');
	$('#cbo_bin').val('');
	$('#txt_batch_lot').attr('disabled','disabled');
	$('#cbo_body_part').attr('disabled','disabled');
	$('#cbo_store_name').attr('disabled','disabled');


	$('#hidden_job').val(data[0]);
	$('#txt_fabric_desc').val(data[1]);

	var jobNo=data[0];
	var fabDesc=data[1];
	var company_id=data[2];
	var body_part=data[3];
	var colorId=data[4];
	var determination_id=data[5];
	var gsm=data[6];
	var dia=data[7];
	var po_id=data[8];
	var requ_mst_id=data[9];

	var hdn_variable_setting_status = $('#hdn_variable_setting_status').val();

	show_list_view(jobNo+'_'+company_id+'_'+body_part+'_'+colorId+'_'+determination_id+'_'+gsm+'_'+dia+'_'+po_id+'_'+requ_mst_id+'_'+hdn_variable_setting_status+'_'+cbo_issue_purpose,'show_fabric_desc_listview_requ','list_fabric_desc_container_rquisition','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');

}

function set_form_data(data)
{

	/*var chck_btn_disble =$("#update_dtls_id").val();
	if (chck_btn_disble) {
      alert("This item can not select in update mode");
       return;
    } */
    var hdn_variable_setting_status=$('#hdn_variable_setting_status').val()*1;

	var cbo_issue_basis=$('#cbo_issue_basis').val();
	var data=data.split("**");

	//restricted duplicate item in save mrr no
	var updateID=$('#update_id').val();
    var system_id=$('#txt_system_id').val();
    get_php_form_data(updateID+"**"+data[0]+"**"+data[6]+"**"+data[3], "check_same_item_found_inSameMRR", "requires/woven_finish_fabric_issue_controller" );
    var hidden_chk_saved_item=$('#hidden_chk_saved_item').val();
    if(hidden_chk_saved_item==updateID && system_id!="")
    {
		alert("This item already saved in this MRR");
		$('#hidden_chk_saved_item').val('');
       	return;
    }
    else
    {
	    $('#save_data').val('');
	    $('#hidden_prod_id').val('');
	    $('#txt_hdn_rate').val('');
	    $('#txt_hdn_cons_amount').val('');
	    $('#hidden_color_id').val('');
	    $('#hidden_bodypart_id').val('');
	    $('#hidden_dia_width').val('');
	    $('#hidden_gsm_weight').val('');
	    //$('#previous_prod_id').val('');
	    //$('#hidden_issue_qnty').val('');
	    $('#txt_issue_qnty').val('');
	    $('#all_po_id').val('');
	    $('#hidden_selected_po_ids').val('');
	    $('#hidden_bookingNo').val('');


	    $('#txt_order_numbers').val('');
	    $('#txt_fabric_received').val('');
	    $('#txt_cumulative_issued').val('');
	    $('#txt_yet_to_issue').val('');
	    $('#txt_global_stock').val('');
	    if (cbo_issue_basis==1)
		{
	   		set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
	   	}
    }


	if (cbo_issue_basis==2)
	{
		if(data[33]<=0)
		{
			alert("Requisition Qnty is not available");
			return;
		}
		$('#txt_batch_lot').val(data[19]);
	}

	if (hdn_variable_setting_status==1)
	{
		$('#hidden_prod_id').val(data[0]);
		$('#txt_fabric_desc').val(data[1]);
		$('#txt_global_stock').val(data[2]);
		$('#hidden_bodypart_id').val(data[3]);
	    $('#txt_uom').val(data[4]);
	    $('#txt_batch_id').val(data[6]);
	    $('#hidden_color_id').val(data[7]);
	    $('#txt_width').val(data[8]);
	    $('#txt_weight').val(data[9]);
	    $('#hidden_dia_width').val(data[8]);
	    $('#hidden_gsm_weight').val(data[9]);
	    $('#txt_rack').val(data[10]);
		$('#txt_shelf').val(data[11]);
		$('#txt_color').val(data[12]);
		$('#txt_floor_name').val(data[13]);
		$('#txt_room_name').val(data[14]);
		$('#txt_rack_name').val(data[15]);
		$('#txt_shelf_name').val(data[16]);
		$('#cbo_floor').val(data[17]);
		$('#cbo_room').val(data[18]);
		$('#cbo_store_name').val(data[20]);
		$('#txt_hdn_rate').val(data[21]);
		$('#hidden_detarmination_id').val(data[22]);
		$('#cbo_bin').val(data[23]);
		$('#txt_bin_name').val(data[24]);
		$('#hidden_uom').val(data[25]);
		$('#txt_fabric_ref').val(data[26]);
		$('#txt_rd_no').val(data[27]);
		$('#cbo_weight_type').val(data[28]);
		$('#txt_cutable_width').val(data[29]);
		$('#hidden_weight_original').val(data[30]);
		$('#hidden_width_original').val(data[31]);
		$('#hidden_selected_po_ids').val(data[32]);

		var bodyPart=data[3];
	}
	else
	{
		$('#hidden_prod_id').val(data[0]);
		$('#txt_fabric_desc').val(data[1]);
		$('#txt_global_stock').val(data[2]);
		$('#hidden_bodypart_id').val(data[3]);
	    $('#txt_uom').val(data[4]);
	    $('#txt_batch_id').val(data[6]);
	    $('#hidden_color_id').val(data[7]);
	    $('#txt_width').val(data[8]);
	    $('#txt_weight').val(data[9]);
	    $('#hidden_dia_width').val(data[8]);
	    $('#hidden_gsm_weight').val(data[9]);
	    $('#txt_rack').val(data[10]);
		$('#txt_shelf').val(data[11]);
		$('#txt_color').val(data[12]);
		$('#txt_floor_name').val(data[13]);
		$('#txt_room_name').val(data[14]);
		$('#txt_rack_name').val(data[15]);
		$('#txt_shelf_name').val(data[16]);
		$('#cbo_floor').val(data[17]);
		$('#cbo_room').val(data[18]);
		$('#cbo_store_name').val(data[20]);
		$('#txt_hdn_rate').val(data[21]);
		$('#hidden_detarmination_id').val(data[22]);
		$('#cbo_bin').val(data[23]);
		$('#txt_bin_name').val(data[24]);
		$('#hidden_uom').val(data[25]);
		$('#txt_fabric_ref').val(data[26]);
		$('#txt_rd_no').val(data[27]);
		$('#cbo_weight_type').val(data[28]);
		$('#txt_cutable_width').val(data[29]);
		$('#hidden_weight_original').val(data[30]);
		$('#hidden_width_original').val(data[31]);
		$('#hidden_selected_po_ids').val(data[32]);

		var bodyPart=data[3];
	}


	if (bodyPart>0)
	{
		$('#cbo_body_part').val(data[3]);
		$('#cbo_body_part').attr('disabled','disabled');
	}

    if (data[5]==111) // booking without order popup not open
    {
		$('#txt_issue_qnty').removeAttr('readonly','readonly');
		$('#txt_issue_qnty').removeAttr('onClick','onClick');
		$('#txt_issue_qnty').removeAttr('placeholder','placeholder');

    }
    else
    {
		$('#txt_issue_qnty').attr('readonly','readonly');
		//$('#txt_issue_qnty').attr('onClick','openmypage_po();');
		$('#txt_issue_qnty').attr('placeholder','Double Click To Open');
		openmypage_po();
    }
}
function details_reset()
{
	$("#list_fabric_desc_container").html("");
	//$("#list_fabric_desc_container_rquisition").html("");
	reset_form('finishFabricEntry_1','','','','','txt_system_id*cbo_company_id*cbo_issue_purpose*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*txt_batch_id*cbo_store_name');
}

function fnReset()
{
	reset_form('','div_details_list_view*list_fabric_desc_container','txt_system_id*cbo_sample_type*txt_challan_no*txt_requisition_no*txt_requisition_id*hidden_job*cbo_buyer_name*txt_batch_lot*txt_batch_id*txt_fabric_desc*hidden_detarmination_id*cbo_body_part*txt_uom*hidden_uom*txt_color*txt_issue_qnty*txt_floor_name*cbo_floor*txt_room_name*cbo_room*txt_rack_name*txt_rack*txt_shelf_name*txt_shelf*txt_bin_name*cbo_bin*txt_no_of_roll*cbo_item_name*cbo_cutting_floor*txt_remarks*txt_order_numbers*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock*update_id*save_data*save_string*update_dtls_id*update_trans_id*hidden_prod_id*hidden_color_id*hidden_dia_width*hidden_gsm_weight*previous_prod_id*hidden_issue_qnty*txt_issue_qnty*all_po_id*roll_maintained*distribution_method_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width*hidden_weight_original*hidden_width_original','cbo_issue_purpose,9*cbo_company_id,0*cbo_store_name,0','');

	set_button_status(0, permission, 'fnc_fabric_issue_entry',1);
	disable_enable_fields('cbo_company_id*cbo_issue_purpose*cbo_store_name*txt_batch_lot');
	active_inactive(9,1);
}
//print 2
function fn_report_generated(type)
{
	if(type==2)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "woven_finish_fabric_issue_print_2", "requires/woven_finish_fabric_issue_controller" )
		return;
	}
	else if(type==3)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "woven_finish_fabric_issue_print_3", "requires/woven_finish_fabric_issue_controller" )
		return;
	}

}
function wvn_finish_fabric_po_to_style_wise_fnc(data)
{
	var varible_data=return_global_ajax_value( data, 'varible_setting_wvn_style_wise', '', 'requires/woven_finish_fabric_issue_controller');
	if(varible_data==1)
	{
		$("#hdn_variable_setting_status").val(varible_data);
		$("#captionName").html("Style Number");
	}
	else
	{
		//varible_data=0;
		$("#hdn_variable_setting_status").val(varible_data);
		$("#captionName").html("Order Number");
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
								echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller*3', 'store','store_td', this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/woven_finish_fabric_issue_controller' );requisition_enable(document.getElementById('cbo_issue_basis').value);wvn_finish_fabric_po_to_style_wise_fnc(this.value);" );
							?>
							<input type="hidden" name="hdn_variable_setting_status" id="hdn_variable_setting_status" class="text_boxes"/>
                        </td>
                        <td>Issue Purpose</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_issue_purpose", 170,$yarn_issue_purpose,"", 0,"",'9',"active_inactive(this.value,0);",'','3,4,8,9,10,26,29,30,31,64');
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
                            <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:158px;" value="<? echo date("d-m-Y"); ?>" readonly placeholder="Select Date"  />
                        </td>
                        <td class="must_entry_caption">Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:158px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td class="must_entry_caption">Sewing Source</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 170, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewingcom_td');load_drop_down( 'requires/woven_finish_fabric_issue_controller',document.getElementById('cbo_company_id').value, 'load_drop_down_cutting','cutting_unit_no');","","","","","2");
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
 							<input type="hidden" name="txt_requisition_poid" id="txt_requisition_poid">
 							<input type="hidden" name="hidden_job" id="hidden_job">

 						</td>
                    </tr>
                    <tr>
                		<td>Additional/Extra</td>
						<td>
							<?
								echo create_drop_down("cbo_extra_status", 170, $yes_no,"", 1,"-- Select --", 2,"");
							?>
						</td>
						<td>Sewing Location</td>
                        <td id="sewinglocation_td">
                            <?
                                echo create_drop_down("cbo_sewing_location", 170, $blank_array,"", 1,"-- Select Sewing Location --", 0,"");
                            ?>
                        </td>
						<td></td>
						<td></td>
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
                                            echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where  item_category_id in('3,14') and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "--Select store--", 0, "details_reset();" );
                                        ?>
                                    </td>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Batch/Lot</td>
                                    <td>
                                    	<input type="text" name="txt_batch_lot" id="txt_batch_lot" class="text_boxes" style="width:158px;" readonly="readonly" placeholder="Browse" onDblClick="openmypage_batchLot();" />
										<input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" >
                                    </td>
                                <td></td><td></td>
                                </tr>
                                <tr>
									<td>Body Part</td>
									<td id="body_part_td">
										<?
										echo create_drop_down( "cbo_body_part", 170, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
										?>
										<span style="width: 20px;">UOM</span>
										<span style="width: 60px;">
											<input type="text" name="txt_uom" id="txt_uom" class="text_boxes" style="width:60px;" readonly disabled />
											<input type="hidden" name="hidden_uom" id="hidden_uom" class="text_boxes" style="width: 50px;" readonly disabled>
										</span>
									</td>
								</tr>
                                <tr>
                                    <td class="must_entry_caption">Fabric Description</td>
                                    <td id="fabricDesc_td" colspan="3">
                                    	<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:260px;" placeholder="Display" disabled /> <!--onDblClick="openmypage_fabricDescription();"--></td>
										<input type="hidden" name="hidden_detarmination_id" id="hidden_detarmination_id" readonly>
                                </tr>
                              	<tr>
									<td align="left">Fabric Ref</td>
									<td>
										<input type="text" name="txt_fabric_ref" id="txt_fabric_ref" class="text_boxes" style="width:50px;" disabled />
										<span style="width: 20px;">RD No</span>
										<span style="width: 60px;"><input type="text" name="txt_rd_no" id="txt_rd_no" class="text_boxes" style="width:60px;" disabled /></span>
									</td>
								</tr>
								<tr>
									<td>Color</td>
									<td>
										<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:158px" placeholder="Display" disabled />
									</td>
								</tr>
								<tr>
									<td align="left">Weight</td>
									<td>
										<input type="text" name="txt_weight" id="txt_weight" class="text_boxes_numeric" style="width:40px;" disabled="disabled" readonly="readonly" />
										<input type="hidden" name="hidden_weight_original" id="hidden_weight_original" class="text_boxes_numeric" style="width:40px;" />
										<span style="width: 10px;">Type</span>
										<span style="width: 90px;">
										<?
										echo create_drop_down( "cbo_weight_type", 90, $fabric_weight_type,"", 1, "-Select-", 0, "",1 );
										?>
									</span>
									</td>
								</tr>
								<tr>
									<td align="left">Full Width</td>
									<td>
										<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:40px;" disabled="disabled" readonly="readonly"/>
										<input type="hidden" name="hidden_width_original" id="hidden_width_original" class="text_boxes" style="width:40px;"/>
										<span style="width: 30px;">Cutable Width</span>
										<span style="width: 35px;">
										<input type="text" name="txt_cutable_width" id="txt_cutable_width" class="text_boxes" style="width:35px;" disabled />
										</span>
									</td>
								</tr>
                                <tr>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                    	<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:158px;" readonly placeholder="Double Click To Open" onDblClick="openmypage_po();" /></td>
                                </tr>
								<tr>
                                   <td >Floor</td>
                                   <td>
                                   		<input type="text" name="txt_floor_name" id="txt_floor_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                   		<input type="hidden" name="cbo_floor" id="cbo_floor" class="text_boxes" style="width:158px"/>
                                   </td>
                                 <!--   <td id="floor_td">
										<? //echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> -->
                                </tr>
                             	<tr>
                                   <td>Room</td>
                                   <td>
                                  		<input type="text" name="txt_room_name" id="txt_room_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
										<input type="hidden" name="cbo_room" id="cbo_room" class="text_boxes" style="width:158px"/>
									</td>
                                   <!-- <td id="room_td">
										<? //echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> -->
                                </tr>
                                <tr>
                                    <td>Rack</td>
                                    <td>
                                    	<input type="text" name="txt_rack_name" id="txt_rack_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
										<input type="hidden" name="txt_rack" id="txt_rack" class="text_boxes" style="width:158px"/>
									</td>
                                     <!-- <td id="rack_td">
										<? //echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> -->
                                </tr>
                                <tr>
                                    <td>Shelf</td>
                                    <td>
                                    	<input type="text" name="txt_shelf_name" id="txt_shelf_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
										<input type="hidden" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:158px" />
									</td>
                                    <!--  <td id="shelf_td">
										<? //echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> -->
                                </tr>
                                <tr>
                                    <td>Bin/Box</td>
                                    <td>
                                    	<input type="text" name="txt_bin_name" id="txt_bin_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
										<input type="hidden" name="cbo_bin" id="cbo_bin" class="text_boxes" style="width:158px" />
									</td>
                                     <!-- <td id="bin_td">
										<? //echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> -->
                                </tr>
                                 <tr>
                                    <td>Roll Qty</td>
                                    <td>
                                    	<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:158px" disabled="disabled"/>
                                    </td>
                                    <td></td><td></td>
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
                       <input type="hidden" name="hidden_is_posted_account_id" id="hidden_is_posted_account_id" class="text_boxes_numeric" style="width:160px" disabled />
                       <p style="font-size: 20px;color:red;" id="is_posted_account_mst"></p>
              		</td>
				</tr>
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                           /*
                            echo load_submit_buttons($permission, "fnc_fabric_issue_entry", 0,0,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose\');active_inactive(9,1);')",1);
                            */

                             echo load_submit_buttons($permission, "fnc_fabric_issue_entry", 0,0,"fnReset();",1);
                        ?>
                        <input type="button" name="print" id="print" value="Print" onClick="fnc_fabric_issue_entry(4)" style="width: 80px; display:none;" class="formbutton">
						<input type="button" id="show_button" class="formbutton" style="width: 80px;" value="Print 2" onClick="fn_report_generated(2)" />
						<input type="button" name="print3" id="print3" value="Print 3" onClick="fn_report_generated(3)" style="width: 80px; display:none;" class="formbutton">
                        <input type="hidden" id="update_id" name="update_id" value="" >
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="save_string" id="save_string" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" readonly>
                        <input type="hidden" name="txt_hdn_rate" id="txt_hdn_rate" readonly>
                        <input type="hidden" name="txt_hdn_cons_amount" id="txt_hdn_cons_amount" readonly>
                        <input type="hidden" name="hidden_bodypart_id" id="hidden_bodypart_id" readonly>
                        <input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly>
                        <input type="hidden" name="hidden_dia_width" id="hidden_dia_width" readonly>
                        <input type="hidden" name="hidden_gsm_weight" id="hidden_gsm_weight" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                        <input type="hidden" name="txt_issue_qnty" id="txt_issue_qnty" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="hidden_selected_po_ids" id="hidden_selected_po_ids" readonly>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                        <input type="hidden" name="hidden_bookingNo" id="hidden_bookingNo" readonly />
                        <input type="hidden" name="hidden_chk_saved_item" id="hidden_chk_saved_item" readonly />
                    </td>
                </tr>
            </table>
            <div style="width:820px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:450px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div id="list_fabric_desc_container_rquisition" style="width:450px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <!-- <div id="list_requisition_desc" style="width:400px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div> -->
	</form>
</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
