<?
/*-------------------------------------------- Comments

Purpose			: 	Yarn Work order entry

Functionality	:

JS Functions	:

Created by		:	Bilas
Creation date 	: 	22-04-13
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
echo load_html_head_contents("Spare Parts Order","../../", 1, 1, $unicode,'','');

$user_id = $_SESSION['logic_erp']['user_id'];
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = " and lib_location.id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip($general_item_category)).",101";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========

$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}
//print_r($_SESSION['logic_erp']['mandatory_field'][728]);die;
?>

<script>

var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var contact = [<? echo substr(return_library_autocomplete( "select contact  from wo_non_order_info_mst where  status_active=1 and is_deleted=0 group by contact", "contact" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
        $("#txt_contact").autocomplete({
			source: contact
		});
    });

<?
if($_SESSION['logic_erp']['data_arr'][147]!="")
{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][147] );
	$data_arr = 90;
	echo "var field_level_data= ". $data_arr . ";\n";
} 
 
?>

<?php
// if($_SESSION['logic_erp']['mandatory_field'][728]!="")
// {
// 	$mandatory_field_arr = json_encode($_SESSION['logic_erp']['mandatory_field'][728]);
// 	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	// condition for txt internal File No
	// $temp_mandatory_field_arr = $_SESSION['logic_erp']['mandatory_field'][728];
	// unset($temp_mandatory_field_arr[3]);
	// $temp_mandatory_message_arr = $_SESSION['logic_erp']['mandatory_message'][728];
	// unset($temp_mandatory_message_arr[3]);
// }
?>
function fn_disable_enable(str)
{
	
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		$("#cbo_wo_basis").val(0);
		return;
	}
	if(str==1)
	{
 		$("#txt_req_numbers").attr("disabled",false);
 		var company = $("#cbo_company_name").val();
 		var necessity_setup="";
		necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/spare_parts_work_order_controller');
		$("#hid_approval_necessity_setup").val(necessity_setup);
		
	}
	else
	{
		$("#hid_approval_necessity_setup").val('');
		$("#txt_req_numbers").val('');
 		$("#txt_req_numbers").attr("disabled",true);
	}
}

// $(function(){
// 	$("#Print4").attr("disabled", "disabled");
// 	$("#Print4").addClass('formbutton_disabled');
// })


// for buyer po
function openmypage()
{

	var company = $("#cbo_company_name").val();
	//var category = $("#cbo_item_category").val();
	var garments_nature = $("#garments_nature").val();
	var txt_req_dtls_id = $("#txt_req_dtls_id").val(); // if value has then it will be selected
	var req_numbers		= $("#txt_req_numbers").val();
	var req_numbers_id  = $("#txt_req_numbers_id").val();

 	var page_link = 'requires/spare_parts_work_order_controller.php?action=requitision_popup&company='+company+'&garments_nature='+garments_nature+'&txt_req_dtls_id='+txt_req_dtls_id+'&req_numbers='+req_numbers+'&req_numbers_id='+req_numbers_id;
	var title = "Requisition No Search";

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
 		var requisition_id=this.contentDoc.getElementById("txt_selected_ids").value; // Requisition ID
		var requisition_number=this.contentDoc.getElementById("txt_selected_numbers").value; // Requisition Number
		var req_dtlsID=this.contentDoc.getElementById("txt_selected_dtls_id").value; // item id
 		//alert(requisition_number+"=="+requisition_id);
		var existing_req_dtlsID 	= ( $("#txt_req_dtls_id").val() ).split(",");
		var additional_req_dtlsID 	= req_dtlsID.split(",");

		if(existing_req_dtlsID!="")
		{
			var unique_req_dtlsID	= additional_req_dtlsID.diff(existing_req_dtlsID);
		}
		else
		{
			var unique_req_dtlsID	= req_dtlsID;
		}

 		$("#txt_req_numbers").val(requisition_number);
		$("#txt_req_numbers_id").val(requisition_id);
		$("#txt_req_dtls_id").val(req_dtlsID);
		var update_id=$("#update_id").val();

 		if(requisition_number!="")
		{
			freeze_window(5);
 			/*var row = $("#tbl_details tr:last").attr('id');
			var responseHtml = return_ajax_request_value(requisition_id+'**'+unique_req_dtlsID+'**'+row, 'show_dtls_listview', 'requires/spare_parts_work_order_controller');*/
			var row = 0;
			var responseHtml = return_ajax_request_value(requisition_id+'**'+req_dtlsID+'**'+row+'**'+update_id, 'show_dtls_listview', 'requires/spare_parts_work_order_controller');
			// alert(responseHtml);return;
			$('#tbl_details tbody tr').remove();
			$("#tbl_details tbody").append(responseHtml);
			calculate_total_amount(1);
 			release_freezing();
		}
		else
		{
			$("#details_container").html('');
		}
	}
}

$(function(){
	$("#cbo_company_name").change(function(){
		// alert('ok');
		$("#tbl_details tbody").empty();
		// $("#tbl_details tfoot").empty();
		$("#txt_total_amount").val('');
		$("#txt_total_amount_net").val('');
		$("#txt_req_numbers").val('');
	});
})

function calculate_yarn_consumption_ratio(i)
{
	var cbocount=$('#txt_quantity_'+i).val();
	var cbocompone=$('#txt_rate_'+i).val();
	var amount =  cbocount*1*cbocompone*1;
	$('#txt_amount_'+i).val(amount.toFixed(4));
	calculate_total_amount(1);
}

function open_terms_condition_popup(page_link,title)
{
	var txt_wo_number=document.getElementById('update_id').value;
	if (txt_wo_number=="")
	{
		alert("Save The Yarn Work Order First");
		return;
	}
	else
	{
	    page_link=page_link+get_submitted_data_string('update_id','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){};
	}
}

function fn_inc_decr_row(rowid,type)
{

	if(type=="increase")
	{
		var row = $("#tbl_details tbody tr:last").attr('id');
		var valuesLastRow = $("#tbl_details tbody tr:last").find('input[name=txt_item_desc_'+row+']').val();
		if(valuesLastRow!="")
		{
			row = row*1+1;
			if( form_validation('cbo_company_name','Company Name')==false )	return;
			var company = $("#cbo_company_name").val();
			//var itemCategory = $("#cbo_item_category").val();
			var responseHtml = return_ajax_request_value(row+'**'+company, 'append_load_details_container', 'requires/spare_parts_work_order_controller');
			$("#tbl_details tbody").append(responseHtml);
		}
	}
	else if(type=="decrease")
	{
		var row = $("#tbl_details tbody tr").length;
		//alert(row*1+"##"+rowid*1); && row*1==rowid*1
		if(rowid*1!="" && row*1>1)
		{
			var vals = $("#txt_delete_row").val();
			var delID = $("#txt_row_id_"+rowid).val();
 			if(vals!="")
 				$("#txt_delete_row").val(vals+','+delID);
			else
				$("#txt_delete_row").val(delID);

			//------------------remove--------------//
			var dtlsID = $("#txt_req_dtls_id_"+rowid).val();
			var dtldArr = ($("#txt_req_dtls_id").val()).split(',');
			$("#txt_req_dtls_id").val(dtldArr.remove(dtlsID));
			$("#tbl_details tbody tr#"+rowid).remove();
  		}
		else
			return;
	}
	calculate_total_amount(1);
}

function fnc_chemical_order_entry(operation)
{
	if(operation==6)
	{
		//var company = $('#cbo_company_name').val();
		var wo_number = $('#txt_wo_number').val();
		//alert(wo_number);
		if (wo_number=='') {
			alert('Please Fill up WO Number field Value');
			return false;
		}
		else {
			var form_caption=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+operation+'*'+$('#cbo_template_id').val(), "spare_parts_work_print", "requires/spare_parts_work_order_controller" )
			 return;
		}
	}
	else if(operation==5){
		//var company = $('#cbo_company_name').val();
		var wo_number = $('#txt_wo_number').val();
		//alert(wo_number);
		if (wo_number=='') {
			alert('Please Fill up WO Number field Value');
			return false;
		}
		else {
			var form_caption=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+operation+'*'+$('#cbo_template_id').val(), "spare_parts_work_print", "requires/spare_parts_work_order_controller" )
			 return;
		}
	}
	else if(operation==4) //operation==4 || operation==5
	{
	 	var form_caption=$( "div.form_caption" ).html();
	 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+operation+'*'+$('#cbo_template_id').val(), "spare_parts_work_print", "requires/spare_parts_work_order_controller" )
	 	return;
	}
	else if(operation==8) //operation==4 || operation==5
	{
		var wo_number = $('#txt_wo_number').val();
		if (wo_number=='') {
			alert('Please Fill up WO Number field Value');
			return;
		}
	 	var form_caption=$( "div.form_caption" ).html();
	 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+operation+'*'+$('#cbo_template_id').val(), "spare_parts_work_print_8", "requires/spare_parts_work_order_controller" )
	 	return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if( form_validation('cbo_company_name*cbo_supplier*txt_wo_date*cbo_currency*cbo_location*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date','Company Name*Supplier Name*WO Date*Currency*Location*WO Basis*Pay Mode*Source*Delivery Date')==false )
		{
			return;
		}

		if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][728]);?>'){
			var mandatory_field = '<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][728]);?>';
			var mandatory_field_arr = mandatory_field.split('*');

			var mandatoryFieldArr = Array(); 
			var mandatoryMessageArr = Array();
			$.each( mandatory_field_arr, function( key, value ) {
				var valueArr = value.split('_');
				var totalRows = $("#tbl_details tbody tr").length;
				for(var i = 1;i<= totalRows;i++){
					mandatoryFieldArr.push(valueArr[0]+'_'+i);
					mandatoryMessageArr.push(valueArr[0]);
				}
			});
			//console.log(mandatoryFieldArr);
			if (form_validation(mandatoryFieldArr.join('*'),mandatoryMessageArr.join('*'))==false)
			{
				return;
			}
		}  

		if($("#cbo_wo_basis").val()==1 && form_validation('txt_req_numbers','Requisition NO')==false ) //buyer po basis
		{
			return;
		}
		else
		{
			try
			{
				//var row = $("#tbl_details tbody tr:last").attr('id'); mysql problem occured
				var row = $("#tbl_details tbody tr").length;
		 		if(row<=0) throw "Save Not Possible!!Input Item Details For Save";
			}
			catch(err)
			{
		 		alert("Error : "+err);
				return;
			}

			var j=0; var i=1; var dataString=''; var necessity_setup_chk=0;var budget_chk=0;
			var app_necessity_setup=$('#hid_approval_necessity_setup').val();
			$("#tbl_details").find('tbody tr').each(function()
			{

				var txt_req_dtls_id=$(this).find('input[name="txt_req_dtls_id[]"]').val();
				var txt_item_id=$(this).find('input[name="txt_item_id[]"]').val();
				var txt_req_no_id=$(this).find('input[name="txt_req_no_id[]"]').val();
				var txt_req_no=$(this).find('input[name="txt_req_no[]"]').val();

				var txt_item_acct=$(this).find('input[name="txt_item_acct[]"]').val();
				var txt_item_desc=$(this).find('input[name="txt_item_desc[]"]').val();
				var cbo_item_category=$(this).find('select[name="cbo_item_category[]"]').val();
				var txt_item_number=$(this).find('input[name="txt_item_number[]"]').val();
				var txt_item_size=$(this).find('input[name="txt_item_size[]"]').val();
				var txt_item_brand=$(this).find('input[name="txt_item_brand[]"]').val();
				var cboorigin=$(this).find('select[name="cboorigin[]"]').val();
				var cbonature=$(this).find('select[name="cbonature[]"]').val();
				var cboProfitCanter=$(this).find('select[name="cboProfitCanter[]"]').val();
				var txt_item_model=$(this).find('input[name="txt_item_model[]"]').val();
				var cbogroup=$(this).find('select[name="cbogroup[]"]').val();
				var cbouom=$(this).find('select[name="cbouom[]"]').val();
				var txt_req_qnty=$(this).find('input[name="txt_req_qnty[]"]').val();

				var txt_quantity=$(this).find('input[name="txt_quantity[]"]').val();
				var txt_rate=$(this).find('input[name="txt_rate[]"]').val();
				var txt_amount=$(this).find('input[name="txt_amount[]"]').val()*1;
				var txt_row_id=$(this).find('input[name="txt_row_id[]"]').val();

				var txt_remarks=$(this).find('input[name="txt_remarks[]"]').val();
				var cbo_buyer=$(this).find('select[name="cbo_buyer[]"]').val();
				var cbo_season=$(this).find('select[name="cbo_season[]"]').val();
				var txt_avail_badget=$(this).find('input[name="txt_avail_badget[]"]').val()*1;
				
				if(cbonature==2 && txt_amount>0)
				{
					if(txt_amount>txt_avail_badget)
					{
						alert("Work Order Amount Not Allow Over Budget Amount Of This Month");
						budget_chk=1;
						$("#txt_amount_"+i).focus().css('border-color', 'red');
						return;
					}
				}

				if(cbo_item_category !=114 &&( txt_quantity*1 <= 0 || txt_rate*1 <= 0 || txt_item_desc=="" || cbo_item_category==0 || cbogroup==0 || cbouom==0))
				{
					alert("Please Fill Up Qnty or Rate or Item Description Or Item Category Or Item Group Or UOM");
					$("#txt_quantity_"+i).focus();
					return;
				}
				
				if( $("#cbo_wo_basis").val()==1 && app_necessity_setup != 1 )
				{
					if( txt_req_qnty*1 < txt_quantity*1 ){
						alert("Work Order Qty Can't over than Requisition Qty");
						$("#txt_quantity_"+i).focus();
						necessity_setup_chk=1;
						return;
					}
				}
				//alert(txt_quantity+"="+txt_rate);
				if(txt_quantity>0 && txt_rate>0)
				{
					j++;
					dataString+='&txt_req_dtls_id_' + j + '=' + txt_req_dtls_id + '&txt_item_id_' + j + '=' + txt_item_id  +'&txt_req_no_id_' + j + '=' + txt_req_no_id + '&txt_req_no_' + j + '=' + txt_req_no+ '&txt_item_acct_' + j + '=' + txt_item_acct + '&txt_item_desc_' + j + '=' + txt_item_desc  + '&cbo_item_category_' + j + '=' + cbo_item_category+ '&txt_item_size_' + j + '=' + txt_item_size+ '&txt_item_brand_' + j + '=' + txt_item_brand+ '&cboorigin_' + j + '=' + cboorigin+ '&txt_item_model_' + j + '=' + txt_item_model + '&cbogroup_' + j + '=' + cbogroup+ '&cbouom_' + j + '=' + cbouom + '&txt_req_qnty_' + j + '=' + txt_req_qnty + '&txt_quantity_' + j + '=' + txt_quantity + '&txt_rate_' + j + '=' + txt_rate + '&txt_amount_' + j + '=' + txt_amount + '&txt_row_id_' + j + '=' + txt_row_id+ '&txt_remarks_' + j + '=' + txt_remarks+ '&cbonature_' + j + '=' + cbonature+ '&cboProfitCanter_' + j + '=' + cboProfitCanter+ '&txt_item_number_' + j + '=' + txt_item_number+ '&cbo_buyer_' + j + '=' + cbo_buyer+ '&cbo_season_' + j + '=' + cbo_season;

				}
				i++;
			});

			// alert(dataString);return;
			if(budget_chk == 1 )
			{
				return;
			}
			
			if(necessity_setup_chk != 0 ){
				return;
			} else if((j*1)<1 ){
				alert('No data found');return;
			} 
			else 
			{
				var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
				if(is_approved==1){
					alert("This Order is Approved. So Change Not Allowed");
					return;
				}
				var data="action=save_update_delete&operation="+operation+'&total_row='+row+get_submitted_data_string('garments_nature*txt_wo_number*cbo_company_name*cbo_supplier*txt_wo_date*cbo_location*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_req_numbers*txt_req_numbers_id*txt_delete_row*txt_delivery_place*hidden_delivery_info_dtls*update_id*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*txt_up_remarks*txt_dis_remarks*cbo_ready_to_approved*cbo_inco_term*cbo_payterm_id*txt_tenor*txt_port_of_loading*cbo_pi_issue_to*hid_approval_necessity_setup*txt_reference*txt_contact*txt_contact_no*cbo_wo_type*txt_remarks_mst*cbo_lc_type',"../../")+dataString;
				//alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/spare_parts_work_order_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_chemical_order_entry_reponse;
			}
		}
	}
}

function fnc_chemical_order_entry_reponse()
{

	if(http.readyState == 4)
	{
		//alert(http.responseText);release_freezing();return;
	    var reponse=trim(http.responseText).split('**');

		if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(trim(reponse[0]));
			$("#txt_wo_number").val(reponse[1]);
			$("#update_id").val(reponse[2]);
			var company_id=$('#cbo_company_name').val();
			var wo_date=$('#txt_wo_date').val();
			show_list_view(reponse[2]+"**"+company_id+"**"+wo_date,'show_dtls_listview_update','details_container','requires/spare_parts_work_order_controller','');
			$('#cbo_company_name').attr('disabled',true);
			$('#cbo_supplier').attr('disabled',true);
			//$('#txt_req_numbers').attr('disabled',true);
			set_button_status(1, permission, 'fnc_chemical_order_entry',1);
		}
		else if(reponse[0]==2)
		{
			show_msg(trim(reponse[0]));
			reset_form('chemicalWorkOrder_1','details_container','','','','');
			$('#cbo_company_name').attr('disabled',false);
			set_button_status(0, permission, 'fnc_chemical_order_entry',1);
			release_freezing();
		}
		else if(reponse[0]==60)
		{
			alert("This WO Already Received. \n Receive number : "+reponse[1]);
			release_freezing();return;
		}
		else if(reponse[0]==24)
		{
			alert("This WO Already Bill Update/Delete Not Allow");
			release_freezing();return;
		}
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
			/*if(reponse[2]>0)
			{
				show_list_view(reponse[2],'show_dtls_listview_update','details_container','requires/spare_parts_work_order_controller','');
			}*/
			release_freezing(); return;
		}
		else if(reponse[0]==13)
		{
			alert(reponse[1]);

			release_freezing(); return;
		}
 		release_freezing();
		//reset_form('chemicalWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
	}
}

function openmypage_wo()
{

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}

	var company = $("#cbo_company_name").val();
	//var itemCategory = $("#cbo_item_category").val();
	var garments_nature = $("#garments_nature").val();
	var page_link = 'requires/spare_parts_work_order_controller.php?action=wo_popup&company='+company+'&garments_nature='+garments_nature;
	var title = "Order Search";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		freeze_window(5);
		var theform=this.contentDoc.forms[0];
		var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");
		//alert(hidden_wo_number[1]);
 		$("#txt_wo_number").val(hidden_wo_number[0]);
		$("#update_id").val(hidden_wo_number[1]);
		//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
		get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/spare_parts_work_order_controller" );
		var company_id=$('#cbo_company_name').val();
		var wo_date=$('#txt_wo_date').val();
		show_list_view(hidden_wo_number[1]+"**"+company_id+"**"+wo_date,'show_dtls_listview_update','details_container','requires/spare_parts_work_order_controller','');
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		if(cbo_wo_basis==1){
			var necessity_setup="";
			necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/spare_parts_work_order_controller');
			$("#hid_approval_necessity_setup").val(necessity_setup);
		}
 		//$('#txt_req_numbers').attr('disabled',true);
		set_button_status(1, permission, 'fnc_chemical_order_entry',1);
		release_freezing();
	}

}


function itemDetailsPopup()
{
    if( form_validation('cbo_company_name*txt_wo_date','Company Name*Wo Date')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	var txt_wo_date = $("#txt_wo_date").val();
	var tot_row = $("#tbl_details tbody tr:last").attr('id');
	//alert(tot_row);
	var itemIDS="";
	for(var i=1;i<=tot_row;i++)
	{
 		try
		{
			if(itemIDS=="") itemIDS = $("#txt_item_id_"+i).val();
			else
			{
				if($("#txt_item_id_"+i).val() != "")
					itemIDS +=","+$("#txt_item_id_"+i).val();
			}
 		}
		catch(err){}
 	}
	var page_link = 'requires/spare_parts_work_order_controller.php?action=account_order_popup&company='+company;
	var title = 'Search Item Details';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("item_1").value;
 		if(theemail!="")
		{

 			var data=theemail+"**"+tot_row+"**"+company;
			var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/spare_parts_work_order_controller');

			$("#tbl_details tbody tr:last").remove();
 			$("#tbl_details tbody:last").append(list_view_orders);
			set_all_onclick();
		}

		release_freezing();
	}

}


function fn_budget_amt(row_num,profit_center)
{
	//alert(profit_center);
	if( form_validation('cbo_company_name*txt_wo_date','Company Name*Wo Date')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	var txt_wo_date = $("#txt_wo_date").val();
	var cbo_item_category = $("#cbo_item_category_"+row_num).val();
	var data=company+"**"+txt_wo_date+"**"+cbo_item_category+"**"+profit_center;
	var cu_budget_amt = return_global_ajax_value( data, 'load_budget_data', '', 'requires/spare_parts_work_order_controller');
	$("#txt_avail_badget_"+row_num).val(cu_budget_amt);
}

function print_button_setting(company)
{
	$('#button_data_panel').html('');
	//alert(company);
	get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/spare_parts_work_order_controller' );
}

function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{

		if(report_id[k]==84)
		{
			$('#button_data_panel')
                .append( '<input type="button"  id="print_with_rate" class="formbutton" style="width:80px; text-align:center;" value="Print 2"  name="print2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==85)
		{
			$('#button_data_panel').append( '<input type="button"  id="print_without_rate" class="formbutton" style="width:80px; text-align:center;" value="Print 3"  name="print3"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==732)
		{
			$('#button_data_panel').append( '<input type="button"  id="Po_print" class="formbutton" style="width:80px; text-align:center;" value="PO Print"  name="Po_print"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==137)
		{
			$('#button_data_panel').append( '<input type="button"  id="Po_print" class="formbutton" style="width:80px; text-align:center;" value="Print 4"  name="print4"  onClick="fn_report_generated(5)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==129)
		{
			$('#button_data_panel').append( '<input type="button"  id="Po_print" class="formbutton" style="width:80px; text-align:center;" value="Print 5"  name="print5"  onClick="fn_report_generated(6)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==134)
		{
			$('#button_data_panel').append( '<input type="button"  id="print1" class="formbutton" style="width:80px; text-align:center;" value="Print"  name="print1"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==191)
		{
			$('#button_data_panel').append( '<input type="button"  id="print7" class="formbutton" style="width:80px; text-align:center;" value="Print 7"  name="print7"  onClick="fn_report_generated(7)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==227)
		{
			$('#button_data_panel').append( '<input type="button"  id="print7" class="formbutton" style="width:80px; text-align:center;" value="Print 8"  name="print8"  onClick="fn_report_generated(8)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==235)
		{
			$('#button_data_panel').append( '<input type="button"  id="print_without_rate_new" class="formbutton" style="width:80px; text-align:center;" value="Print 9"  name="print9"  onClick="fn_report_generated(9)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==274)
		{
			$('#button_data_panel').append( '<input type="button"  id="print_without_rate_new" class="formbutton" style="width:80px; text-align:center;" value="Print 10"  name="print10"  onClick="fn_report_generated(10)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==354)
		{
			$('#button_data_panel').append( '<input value="Print Wo" name="print_wo" onClick="fnc_chemical_order_entry(8)" style="width:80px" id="print_wo" class="formbutton" type="button">&nbsp;&nbsp;&nbsp;' );
		}
        if(report_id[k]==430)
        {
            $('#button_data_panel').append( '<input type="button"  id="print_with_rate" class="formbutton" style="width:80px; text-align:center;" value="PO Print 2"  name="po_print2"  onClick="fn_report_generated(11)" />&nbsp;&nbsp;&nbsp;' );
        }
		if(report_id[k]==241)
		{
			$('#button_data_panel').append( '<input type="button"  id="Print_11" class="formbutton" style="width:80px; text-align:center;" value="Print 11"  name="Print_11"  onClick="fn_report_generated(12)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==427)
		{
			$('#button_data_panel').append( '<input type="button"  id="Print_12" class="formbutton" style="width:80px; text-align:center;" value="Print 12"  name="Print_12"  onClick="fn_report_generated(13)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==72)
		{
			$('#button_data_panel').append( '<input type="button"  id="Print_6" class="formbutton" style="width:80px; text-align:center;" value="Print 6"  name="Print_6"  onClick="fn_report_generated(14)" />&nbsp;&nbsp;&nbsp;' );
		}

		if(report_id[k]==28)
		{
			$('#button_data_panel').append( '<input type="button"  id="Print_13" class="formbutton" style="width:80px; text-align:center;" value="Print 13"  name="Print_13"  onClick="fn_report_generated(15)" />&nbsp;&nbsp;&nbsp;' );
		}

	}
}



// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(val)
{
    for (var i = 0; i < this.length; i++)
	{
        if (this[i] === val)
		{
            this.splice(i, 1);
            i--;
        }
    }
    return this;
}

// two array diffrence
Array.prototype.diff = function(a)
{
    return this.filter(function(i) {return !(a.indexOf(i) > -1);});
};

/*function fn_report_generated(type)
{
	//alert($('#txt_req_numbers_id').val());
	if ( $('#txt_wo_number').val()=='')
	{
		alert ('WO Number Not Save.');
		return;
	}
	else
	{

	var form_caption=$( "div.form_caption" ).html();
	 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption, "spare_parts_work_order_print2", "requires/spare_parts_work_order_controller" )
		show_msg("3");
	}
}*/

function fn_report_generated(type,mail_data='')
{
	if(type==2)
	{
		//echo "type 2";
		//alert($('#txt_req_numbers_id').val());
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{

		var form_caption=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print2", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==3)
	{
		//echo "type 3"; die;
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{


		var form_caption=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print3", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==4)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{
            if(confirm('Press  OK to open with Size/MSR and Narration\n Press Cancel to open without Size/MSR and Narration')) {
                show=1;
            } else {
                show=0
            }
			var form_caption=$( "div.form_caption" ).html();
		 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+show+'*'+mail_data+'*'+$('#cbo_lc_type').val(), "spare_parts_work_order_po_print", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==5){
		//var company = $('#cbo_company_name').val();
		var wo_number = $('#txt_wo_number').val();
		//alert(wo_number);
		if (wo_number=='') {
			alert('Please Fill up WO Number field Value');
			return false;
		}
		else {
			var form_caption=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*5*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_print", "requires/spare_parts_work_order_controller" )
			 return;
		}
	}
	else if(type==6)
	{
		//var company = $('#cbo_company_name').val();
		var wo_number = $('#txt_wo_number').val();
		//alert(wo_number);
		if (wo_number=='') {
			alert('Please Fill up WO Number field Value');
			return false;
		}
		else {
			var form_caption=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*6*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_print", "requires/spare_parts_work_order_controller" )
			 return;
		}
	}
	else if(type==7)
	{
		//echo "type 3"; die;
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{	
		var form_caption=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+type+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_print_urmi", "requires/spare_parts_work_order_controller" )
		}
	}
	else if(type==8)
	{
		//echo "type 3"; die;
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{	
		var form_caption=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+type+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print8", "requires/spare_parts_work_order_controller" )
		}
	}
	else if(type==9)
	{
		//echo "type 3"; die;
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{


		var form_caption=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print9", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==10)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{
			var form_caption=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print10", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
    else if(type==11)
    {
        if ( $('#txt_wo_number').val()=='')
        {
            alert ('WO Number Not Save.');
            return;
        }
        else
        {
            var form_caption=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_po_print2", "requires/spare_parts_work_order_controller" );
            show_msg("3");
        }
    }
	else if(type==12)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{

		var form_caption=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_po_print_11", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==13)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{
			var form_caption=$( "div.form_caption" ).html();
		 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#txt_wo_number').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print12", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}
	else if(type==14) // print 6
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{
			var form_caption=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print6", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}

	else if(type==15) // print 13
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
		else
		{
			var form_caption=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "spare_parts_work_order_print13", "requires/spare_parts_work_order_controller" )
			show_msg("3");
		}
	}

}

function calculate_total_amount(type)
{
	if(type==1)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation_name( "txt_total_amount", "txt_amount", "+", "tbl_details" , ddd );
	}

	var txt_total_amount=$('#txt_total_amount').val()*1;
	var txt_upcharge=$('#txt_upcharge').val();
	var txt_discount=$('#txt_discount').val();
	if(txt_total_amount>0)
	{
		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
	}


}

function fn_delivery_info()
{
	var hidden_delivery_info_dtls=$('#hidden_delivery_info_dtls').val();
	var page_link='requires/spare_parts_work_order_controller.php?action=delivery_info_popup&hidden_delivery_info_dtls='+hidden_delivery_info_dtls;
	var title="Place Of Delivery Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=420px,height=250px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("hdn_delivery_info_dtls").value;
		document.getElementById('hidden_delivery_info_dtls').value=theemail;
	}
}
function fnc_necessity_setup(company)
{
	var company = $("#cbo_company_name").val();
	var wo_basis = $("#cbo_wo_basis").val();
	if(wo_basis==1)
	{
 		$("#txt_req_numbers").attr("disabled",false);
 		var necessity_setup="";
		necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/spare_parts_work_order_controller');
		$("#hid_approval_necessity_setup").val(necessity_setup);
		
	}
	else
	{
		$("#hid_approval_necessity_setup").val('');
		$("#txt_req_numbers").val('');
 		$("#txt_req_numbers").attr("disabled",true);
	}
	load_drop_down( 'requires/spare_parts_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val(), 'load_details_container', 'details_container' );
}

function openmypage_supplier()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var cbo_company_name = $('#cbo_company_name').val();
	var title = 'Supplier Name';	
	var page_link = 'requires/spare_parts_work_order_controller.php?cbo_company_name='+cbo_company_name+'&action=supplier_name_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var supplier_info=this.contentDoc.getElementById("hidden_supplier_info").value.split("__");	
		$('#cbo_supplier').val(supplier_info[0]);
		$('#txt_supplier_name').val(supplier_info[1]);
		get_php_form_data( supplier_info[0], 'load_drop_down_attention', 'requires/spare_parts_work_order_controller');
	}
}

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txt_remarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/spare_parts_work_order_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=450px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks").value;
			if (theemail!="")
			{
				$('#txt_remarks_'+id).val(theemail);
			}
		}
	}



	function call_print_button_for_mail(mail,mail_body,type){
		var company=document.getElementById('cbo_company_name').value;
		var firstButtonId=return_global_ajax_value( company, 'get_first_selected_print_button', '', 'requires/spare_parts_work_order_controller');

		if(firstButtonId==84)
		{
			fn_report_generated(2,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==85)
		{
			fn_report_generated(3,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==732)
		{
			fn_report_generated(4,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==137)
		{
			fn_report_generated(5,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==129)
		{
			fn_report_generated(6,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==134)
		{
			fn_report_generated(4,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==191)
		{
			fn_report_generated(7,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==227)
		{
			fn_report_generated(8,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==235)
		{
			fn_report_generated(9,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==274)
		{
			fn_report_generated(10,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==354)
		{
			fn_report_generated(8,mail+'___1'+'___'+mail_body);
		}
        else if(firstButtonId==430)
        {
            fn_report_generated(11,mail+'___1'+'___'+mail_body);
        }
		else if(firstButtonId==241)
		{
			fn_report_generated(12,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==427)
		{
			fn_report_generated(13,mail+'___1'+'___'+mail_body);
		}
		else if(firstButtonId==72)
		{
			fn_report_generated(14,mail+'___1'+'___'+mail_body);
		}
		
	}

</script>
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1450px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>

		<fieldset style="width:1650px">
			<legend>General Purchase Order</legend>
			<form name="chemicalWorkOrder_1" id="chemicalWorkOrder_1" method="" >
				<table cellpadding="0" cellspacing="2" width="100%">
					<tr>
					  <td colspan="3">&nbsp;</td>
					  <td>&nbsp;</td><input type="hidden" name="update_id" id="update_id" value="">
					  <td>WO Number</td><input type="hidden" name="is_approved" id="is_approved" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:158px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                      </td>
					  <td>&nbsp;</td>
					  <td colspan="3">&nbsp;</td>
				  	</tr>
					<tr>
						<td width="70" class="must_entry_caption">Company</td>
						<td width="170">
							<input type="hidden" id="report_ids" >
                        	<?
							   	echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/spare_parts_work_order_controller', this.value, 'load_drop_down_location', 'location_td' );print_button_setting(this.value);fnc_necessity_setup(this.value);",0 );
 							?>
						</td>
						<td width="" class="must_entry_caption">Currency</td>
						<td width="">
                         	<?
							   	echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select --", 1, "",0 );
 							?>
                        </td>
						<td width="">Attention</td>
						<td width=""><input type="text" name="txt_attention"  id="txt_attention" style="width:158px " class="text_boxes" /></td>
						<td>Tenor</td>
                        <td><input type="text"  name="txt_tenor" style="width:158px" id="txt_tenor" class="text_boxes_numeric" /></td>
						<td>WO Type</td>
                        <td width="165"><?php echo create_drop_down("cbo_wo_type", 165, $main_fabric_co_arr, '', 1, '-Select-', 0, "", 0, ''); ?></td>
					</tr>
					<tr>
                        <td width="70" class="must_entry_caption">WO Basis</td>
						<td width="">
                        	<?
							    // setFieldLevelAccess($('#cbo_company_name').val())
							   	//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
 								echo create_drop_down( "cbo_wo_basis", 160, $wo_basis,"", 1, "-- Select --", 1, "fn_disable_enable(this.value);load_drop_down( 'requires/spare_parts_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val(), 'load_details_container', 'details_container' );setFieldLevelAccess($('#cbo_company_name').val());",0,'','','','3' );
 							?>
                        </td>
						<td width="" class="must_entry_caption">Delivery Date</td>
						<td width="">
							<input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:158px" />
						</td>
                    	<td width="">Requisition No</td>
						<td width="">
                        	<input type="text" name="txt_req_numbers"  id="txt_req_numbers" class="text_boxes" style="width:158px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly />
                            <input type="hidden" name="txt_req_numbers_id"  id="txt_req_numbers_id" readonly disabled />
                            <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                            <input type="hidden" name="hid_approval_necessity_setup"  id="hid_approval_necessity_setup" readonly disabled />
                            <!-- DELETED ROW ID HERE------>
                            <input type="hidden" name="txt_delete_row"  id="txt_delete_row" readonly disabled />
                            <!-- DELETED ROW END------>
                        </td>
						<td>PI issue To</td>
						<td>
                            <?
                            echo create_drop_down( "cbo_pi_issue_to", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" );
                            ?>

                        </td>
						<td>Remarks</td>
					   	<td><input type="text" name="txt_remarks_mst" style="width:154px" id="txt_remarks_mst" class="text_boxes" /></td>
					</tr>
					<tr>
						<td width="70" class="must_entry_caption">Location</td>
						<td id="location_td">
							<?
								echo create_drop_down("cbo_location", 160, $blank_array, "", 1, "-- Select Location --", 0, "");
							?>
                        </td>
						<td width="" class="must_entry_caption">Pay Mode</td>
						<td width="">
                        	<?
							   	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 4, "",0 );
 							?>
                        </td>
						<td align="left">Ready To Approved</td>
						<td>
						    <?
						    echo create_drop_down("cbo_ready_to_approved", 170, $yes_no, "", 1, "-- Select--", 2, "", "", "");
						    ?>
						</td>
                        <td>Port of Loading</td>
                        <td><input type="text"  name="txt_port_of_loading" style="width:159px" id="txt_port_of_loading" class="text_boxes" /></td>
						<td>&nbsp;</td>
						<td align="center" height="10">
							<?
								include("../../terms_condition/terms_condition.php");
								terms_condition(147,'txt_wo_number','../../');
                            ?>

                        	<!--<input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:30px; margin-top:5px" value="Terms & Condition" onClick="open_terms_condition_popup('requires/spare_parts_work_order_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                        </td>
					</tr>
					<tr>
                        <td width="70" class="must_entry_caption">Supplier</td>
						<td id="supplier_td">
							<?
							// echo create_drop_down( "cbo_supplier", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(1,6,7,30,36,37,39,92) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/spare_parts_work_order_controller');","");
							// echo create_drop_down('cbo_supplier', 170, $blank_array, '', 1, '-- Select --', 0, '');
							?>
							<input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:148px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
							<input type="hidden" name="cbo_supplier" id="cbo_supplier" />
                        </td>
						<td width="" class="must_entry_caption">Source</td>
						<td width="">
                        	<?
							   	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", 3, "",0 );
 							?>
                        </td>
						<td align="">Incoterm</td>
                        <td>
                        <?
                       		echo create_drop_down("cbo_inco_term", 170, $incoterm, "", 1, "-- Select --", 0, "");
                        ?>
                        </td>
						<td>Reference</td>
                        <td><input type="text"  name="txt_reference" style="width:159px"  id="txt_reference" class="text_boxes" /></td>
						<td>Image</td>
						<td >
							<input type="button" class="image_uploader" style="width:165px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_wo_number').value,'', 'others_purchase_order', 0 ,1)"> 
						</td>
					</tr>
					<tr>
						<td width="70" class="must_entry_caption">WO Date</td>
						<td width="">
							<input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:148px" value="<? echo date("d-m-Y");?>"  />
 						</td>
						<td width="">Place Of Delivery</td>
						<td width=""><input type="text" name="commercial/work_order/requires/spare_parts_work_order_controller.php"  id="txt_delivery_place"  style="width:158px" class="text_boxes" onDblClick="fn_delivery_info()" placeholder="Write or Browse"/>
						<input type="hidden" name="hidden_delivery_info_dtls" id="hidden_delivery_info_dtls" /></td>
						<td>Pay Term</td>
                        <td><?php echo create_drop_down("cbo_payterm_id", 170, $pay_term, '', 1, '-Select-', 0, "", 0, ''); //set_port_loading_value(this.value)1,2   ?></td>
						<td>Contact To</td>
					   	<td><input type="text"  name="txt_contact" style="width:158px" id="txt_contact" class="text_boxes" /></td>
						<td >File</td>
						<td >
							<input type="button" class="image_uploader" style="width:165px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_wo_number').value,'', 'others_purchase_order', 2 ,1)"> 
                        </td>
					</tr>
					<tr>
						<td>Refusing Cause</td>
						<td colspan="3" id="refusing_cause" style="color:#F00; font-size:16px; font-weight:bold;"></td>
						<td align="left">L/C Type</td>
                        <td align="left"><?php $lc_type_arr=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT'];echo create_drop_down("cbo_lc_type", 170, $lc_type_arr, '', 1, '-Select-', 0, "", 0, ''); //set_port_loading_value(this.value)1,2   ?></td>

						<td>Contact No</td>
					   	<td><input type="text"  name="txt_contact_no" style="width:158px" id="txt_contact_no" class="text_boxes" /></td>
					</tr>
                </table>
                <br />
                <div style="width:1550px" id="details_container" align="left"></div>

				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
                        <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<?
								//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
								//load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
								echo load_submit_buttons( $permission, "fnc_chemical_order_entry", 0, 0, "reset_form('chemicalWorkOrder_1','approved*details_container','','','','');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#cbo_location').attr('disabled',false);",1);
								// echo load_submit_buttons( $permission, "fnc_chemical_order_entry", 0,1 ,"reset_form('chemicalWorkOrder_1','approved*details_container','','','','cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#cbo_item_category').attr('disabled',false);",1);
								
							?>
                            <!-- <input type="button" id="show_button" class="formbutton" style="width:80px" value="Print2" onClick="fn_report_generated(2)" />

                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="print4" onClick="fnc_chemical_order_entry(5)" /> -->
							<!-- <input value="Print4" name="print4" onClick="fnc_chemical_order_entry(5)" style="width:80px" id="Print4" class="formbutton" type="button">
                            <input value="Print5" name="print5" onClick="fnc_chemical_order_entry(6)" style="width:80px" id="Print4" class="formbutton" type="button">
                            <input value="Print7" name="print7" onClick="fnc_chemical_order_entry(7)" style="width:80px" id="Print7" class="formbutton" type="button">
                            <input value="Print Wo" name="print_wo" onClick="fnc_chemical_order_entry(8)" style="width:80px" id="print_wo" class="formbutton" type="button"> -->

							<input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
                            
						</td>

					</tr>
					<tr>
						<td id="button_data_panel" align="center"> </td>
					</tr>
				</table>
			</form>
		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
