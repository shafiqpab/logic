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
Report	By		:	Aziz

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Work Order","../../", 1, 1, $unicode,1,'');

$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
} 
$delivery_address     = create_drop_down( "delivery_address", 165, "select a.id,a.address from lib_location a, lib_company b where b.id=a.company_id and a.address is not null  and   a.status_active =1 and a.is_deleted=0  and   b.status_active =1 and b.is_deleted=0 order by a.id","id,address", 0, "-- select --", 0, "",0 );

?>

<script>

var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

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
		necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/dyes_and_chemical_work_order_controller');
		$("#hid_approval_necessity_setup").val(necessity_setup);

	}
	else
	{
		$("#hid_approval_necessity_setup").val('');
		$("#txt_req_numbers").val('');
 		$("#txt_req_numbers").attr("disabled",true);
	}
}


// for buyer po
function openmypage()
{

	var company = $("#cbo_company_name").val();
	//var category = $("#cbo_item_category").val();
	var garments_nature = $("#garments_nature").val();
	var txt_req_dtls_id = $("#txt_req_dtls_id").val(); // if value has then it will be selected
	var req_numbers		= $("#txt_req_numbers").val();
	var req_numbers_id  = $("#txt_req_numbers_id").val();

 	var page_link = 'requires/dyes_and_chemical_work_order_controller.php?action=requitision_popup&company='+company+'&garments_nature='+garments_nature+'&txt_req_dtls_id='+txt_req_dtls_id+'&req_numbers='+req_numbers+'&req_numbers_id='+req_numbers_id;
	var title = "Requisition No Search";

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
 		var requisition_id=this.contentDoc.getElementById("txt_selected_ids").value; // Requisition ID
		var requisition_number=this.contentDoc.getElementById("txt_selected_numbers").value; // Requisition Number
		var req_dtlsID=this.contentDoc.getElementById("txt_selected_dtls_id").value; // item id

		var existing_req_dtlsID 	= ( $("#txt_req_dtls_id").val() ).split(",");
		var additional_req_dtlsID 	= req_dtlsID.split(",");
		if(existing_req_dtlsID!="")
			var unique_req_dtlsID		= additional_req_dtlsID.diff(existing_req_dtlsID);
		else
			var unique_req_dtlsID		= req_dtlsID;

 		$("#txt_req_numbers").val(requisition_number);
		$("#txt_req_numbers_id").val(requisition_id);
		$("#txt_req_dtls_id").val(req_dtlsID);
		var update_id=$("#update_id").val();
		//if($("#txt_req_numbers").val()!="") $("#txt_req_numbers").val(","+requisition_number); else $("#txt_req_numbers").val(requisition_number);
		//if($("#txt_req_numbers_id").val()!="") $("#txt_req_numbers_id").val(","+requisition_id); else $("#txt_req_numbers_id").val(requisition_id);
		//if($("#txt_req_dtls_id").val()!="") $("#txt_req_dtls_id").val(","+req_dtlsID); else $("#txt_req_dtls_id").val(req_dtlsID);

		if(requisition_number!="")
		{
			freeze_window(5);
			//show_list_view(requisition_id+'**'+req_dtlsID,'show_dtls_listview','details_container','requires/dyes_and_chemical_work_order_controller','');
			/*var row = $("#tbl_details tr:last").attr('id');
			var responseHtml = return_ajax_request_value(requisition_id+'**'+unique_req_dtlsID+'**'+row, 'show_dtls_listview', 'requires/dyes_and_chemical_work_order_controller');
			$("#tbl_details").append(responseHtml); */

			var row = 0;
			var responseHtml = return_ajax_request_value(requisition_id+'**'+req_dtlsID+'**'+row+'**'+update_id, 'show_dtls_listview', 'requires/dyes_and_chemical_work_order_controller');
			$('#tbl_details tr:not(:first)').remove();
			$("#tbl_details").append(responseHtml);
			release_freezing();
			calculate_total_amount(1);

		}
		else
		{
			$("#details_container").html('');
		}
	}
}


function calculate_yarn_consumption_ratio(i)
{
	var cbocount=$('#txt_quantity_'+i).val();
	var cbocompone=$('#txt_rate_'+i).val();
	var amount =  cbocount*1*cbocompone*1;
	$('#txt_amount_'+i).val(amount.toFixed(2));
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
			var responseHtml = return_ajax_request_value(row+'**'+company, 'append_load_details_container', 'requires/dyes_and_chemical_work_order_controller');
			//alert(responseHtml);return;
			$("#tbl_details tbody").append(responseHtml);
		}
	}
	else if(type=="decrease")
	{
		var row = $("#tbl_details tbody tr").length;
		//alert(row*1+"##"+rowid*1);
		if(rowid*1!="" && row*1>1 && row*1==rowid*1)
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

	if(operation==4)
	 {
		 /*print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val(), "dyes_chemical_work_print", "requires/dyes_and_chemical_work_order_controller" ) */
		  var form_caption=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val(), "dyes_chemical_work_print", "requires/dyes_and_chemical_work_order_controller" )

		 return;
	 }
	 else if(operation==0 || operation==1 || operation==2)
	 {
		// if(operation==2)
		// {
		// 	show_msg('13');
		// 	return;
		// }
        //
		if( form_validation('cbo_company_name*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date','Company Name*Supplier Name*WO Date*Currency*WO Basis*Pay Mode*Source*Delivery Date')==false )
		{
			return;
		}

		if($("#cbo_wo_basis").val()==1 && form_validation('txt_req_numbers','Requisition NO')==false ) //buyer po basis
		{
			return;
		}
		else
		{
			try
			{
				var row = $("#tbl_details tbody tr:last").attr('id');
				if(row<=0) throw "Save Not Possible!!Input Item Details For Save";
			}
			catch(err)
			{
				alert("Error : "+err);
				return;
			}

			// save data here
			var detailsData=""; necessity_setup_chk=0;
			var app_necessity_setup=$('#hid_approval_necessity_setup').val();
			for(var i=1;i<=row;i++)
			{
				try{
					if( form_validation('txt_item_desc_'+i+'*cbo_item_category_'+i+'*cbogroup_'+i+'*cbouom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i,'Item Account*Item Description*Item Size*Item Group*UOM*Work Order Quantity*Rate*Amount')==false)
					{
						return;
					}
					if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0)
					{
						alert("Quantity OR Rate Can not be 0 or less than 0");
						$("#txt_quantity_"+i).focus();
						return;
					}

					if( $("#cbo_wo_basis").val()==1 && app_necessity_setup != 1 ){
						if( $("#txt_req_qnty_"+i).val()*1 < $("#txt_quantity_"+i).val()*1 ){
							alert("Work Order Qty Can't over than Requisition Qty");
							$("#txt_quantity_"+i).focus();
							necessity_setup_chk=1;
							return;
						}
					}
					//detailsData+='*txt_req_dtls_id_'+i+'*txt_item_id_'+i+'*txt_req_no_id_'+i+'*txt_req_no_'+i+'*txt_item_acct_'+i+'*txt_item_desc_'+i+'*txt_item_size_'+i+'*cbogroup_'+i+'*txt_remarks_'+i+'*cbouom_'+i+'*txt_req_qnty_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_row_id_'+i;
					detailsData+='*txt_req_dtls_id_'+i+'*txt_item_id_'+i+'*txt_req_no_id_'+i+'*txt_req_no_'+i+'*txt_item_acct_'+i+'*txt_item_desc_'+i+'*cbo_item_category_'+i+'*txt_item_size_'+i+'*cbogroup_'+i+'*cbouom_'+i+'*txt_req_qnty_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i;
				}
				catch(err){}
			}

			//alert(detailsData);return;
			if(necessity_setup_chk != 0 ){
				return;
			}

			var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
			if(is_approved==1){
				alert("This Order is Approved. So Change Not Allowed");
				return;
			}
			// alert(document.getElementById("hidden_delivery_info_dtls").value);
			//alert(row);return;txt_up_remarks
			var data="action=save_update_delete&operation="+operation+'&total_row='+row+get_submitted_data_string('txt_contact*cbo_wo_type*txt_remarks_mst*garments_nature*txt_wo_number*cbo_company_name*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_req_numbers*txt_req_numbers_id*txt_delivery_place*txt_delete_row*update_id*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*txt_up_remarks*txt_discount_remarks*txt_ref*txt_port_of_loading*txt_tenor*cbo_inco_term*cbo_payterm_id*cbo_ready_to_approved*hid_approval_necessity_setup*hidden_delivery_info_dtls*cbo_lc_type*txt_place_of_delivery*delivery_address'+detailsData,"../../");
			//alert(data);return
			freeze_window(operation);
			if(operation==1 || operation==2)
			{
				var wo_is_approved=return_global_ajax_value( document.getElementById("cbo_company_name").value+"***"+document.getElementById("txt_wo_number").value, 'check_wo_is_approved', '', 'requires/dyes_and_chemical_work_order_controller');
				var approved_data=wo_is_approved.split("***");
				if(approved_data[0]==1)
				{
					release_freezing();
					alert("Work Order is partially or fully approved . Update or Delete not allowed");
					return;
				}
			}
			http.open("POST","requires/dyes_and_chemical_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);

			http.onreadystatechange = fnc_chemical_order_entry_reponse;
		}

	 }

}

function wo_date_genearte(){
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var output = (day<10 ? '0' : '') + day + '-' +
        (month<10 ? '0' : '') + month + '-' + d.getFullYear();
    $('#txt_wo_date').val(output);
}

function fnc_chemical_order_entry_reponse()
{

	if(http.readyState == 4)
	{


	    var reponse=trim(http.responseText).split('**');
		//alert(http.responseText);
		show_msg(trim(reponse[0]));
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_wo_number").val(reponse[1]);
			$("#update_id").val(reponse[2]);
			show_list_view(reponse[2],'show_dtls_listview_update','details_container','requires/dyes_and_chemical_work_order_controller','');
			set_button_status(1, permission, 'fnc_chemical_order_entry',1);
		}else if(reponse[0]==2){
            reset_form('chemicalWorkOrder_1', 'details_container', 'txt_wo_number*cbo_company_name*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_req_numbers*txt_req_numbers_id*txt_delivery_place*update_id*txt_ref*txt_port_of_loading*txt_tenor*cbo_inco_term*cbo_payterm_id*cbo_ready_to_approved');
            wo_date_genearte();
        }
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
			if(reponse[2]>0)
			{
				show_list_view(reponse[2],'show_dtls_listview_update','details_container','requires/dyes_and_chemical_work_order_controller','');
			}
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
	var page_link = 'requires/dyes_and_chemical_work_order_controller.php?action=wo_popup&company='+company+'&garments_nature='+garments_nature;
	var title = "Order Search";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=380px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		freeze_window(5);
		var theform=this.contentDoc.forms[0];
		var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");

 		$("#txt_wo_number").val(hidden_wo_number[0]);
		$('#update_id').val(hidden_wo_number[1]);
		//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
		//alert("x");
		get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/dyes_and_chemical_work_order_controller" );
		show_list_view(hidden_wo_number[1],'show_dtls_listview_update','details_container','requires/dyes_and_chemical_work_order_controller','');
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		$("#mstId").val(hidden_wo_number[1]);
		if(cbo_wo_basis==1){
			var necessity_setup="";
			necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/dyes_and_chemical_work_order_controller');
			$("#hid_approval_necessity_setup").val(necessity_setup);
		}
		set_button_status(1, permission, 'fnc_chemical_order_entry',1);
		release_freezing();
	}

}


function itemDetailsPopup()
{
	var company = $("#cbo_company_name").val();
	//var itemCategory = $("#cbo_item_category").val();
	var tot_row = $("#tbl_details tbody tr:last").attr('id');
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

	var page_link = 'requires/dyes_and_chemical_work_order_controller.php?action=account_order_popup&company='+company+'&itemIDS='+itemIDS;
	var title = 'Search Item Details';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("item_1").value;
 		if(theemail!="")
		{

 			var data=theemail+"**"+tot_row;
			var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/dyes_and_chemical_work_order_controller');

			$("#tbl_details tbody tr:last").remove();

			$("#tbl_details tbody:last").append(list_view_orders);
			set_all_onclick();
		}

		release_freezing();
	}

}


// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] === val) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
}

// two array diffrence
Array.prototype.diff = function(a) {
    return this.filter(function(i) {return !(a.indexOf(i) > -1);});
};

function fn_report_generated(type)
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
		if(type==2){
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val(), "dyes_chemical_work_print2", "requires/dyes_and_chemical_work_order_controller" );
			show_msg( "3" );
		}
		if(type==3){
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_lc_type').val(), "dyes_chemical_work_po_print", "requires/dyes_and_chemical_work_order_controller" );
			show_msg( "3" );
		}
		if(type==4){
		 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val(), "dyes_chemical_work_print3", "requires/dyes_and_chemical_work_order_controller" )
			show_msg( "3" );
		}
		if(type==5){
		 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val(), "dyes_chemical_work_po_print2", "requires/dyes_and_chemical_work_order_controller" )
			show_msg( "3" );
		}
		if(type==6){
		 	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_req_numbers_id').val()+'*'+form_caption+'*'+$('#cbo_template_id').val(), "dyes_chemical_work_print4", "requires/dyes_and_chemical_work_order_controller" )
			show_msg( "3" );
		}

	}
}

function print_button_setting(company)
{
	$('#button_data_panel').html('');
	//alert(company);
	get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/dyes_and_chemical_work_order_controller' );
}

function print_report_button_setting(report_ids)
{
	//alert(report_ids);
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{

		if(report_id[k]==78)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:80px; text-align:center;" value="Print"  name="Print"  onClick="fnc_chemical_order_entry(4)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==84)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:80px; text-align:center;" value="Print 2"  name="Print2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==732)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:80px; text-align:center;" value="PO Print"  name="PO_Print"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==85)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:80px; text-align:center;" value="Print 3"  name="Print2"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==430)
		{
			$('#button_data_panel').append( '<input type="button"  id="po_print2" class="formbutton" style="width:80px; text-align:center;" value="PO Print 2"  name="po_print2"  onClick="fn_report_generated(5)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==137)
		{
			$('#button_data_panel').append( '<input type="button"  id="print4" class="formbutton" style="width:80px; text-align:center;" value="Print 4"  name="print4"  onClick="fn_report_generated(6)" />&nbsp;&nbsp;&nbsp;' );
		}
	}
}


function calculate_total_amount(type)
{
	if(type==1)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_details tbody tr').length;
		math_operation( "txt_total_amount", "txt_amount_", "+", numRow,ddd );
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

function calculate_yarn_consumption_ratio(i)
{
	var cbocount=$('#txt_quantity_'+i).val()*1;
	var cbocompone=$('#txt_rate_'+i).val()*1;
	var amount =  cbocount*cbocompone;
	$('#txt_amount_'+i).val(amount.toFixed(2));
	calculate_total_amount(1);
}

function openmypage_supplier()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var cbo_company_name = $('#cbo_company_name').val();
	var title = 'Supplier Name';
	var mst_id = $("#mstId").val();
	var page_link = 'requires/dyes_and_chemical_work_order_controller.php?cbo_company_name='+cbo_company_name+ '&mst_id='+mst_id+ '&action=supplier_name_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var supplier_info=this.contentDoc.getElementById("hidden_supplier_info").value.split("__");
		$('#cbo_supplier').val(supplier_info[0]);
		$('#txt_supplier_name').val(supplier_info[1]);
        $('#txt_attention').val(supplier_info[2])
	}
}

function fn_delivery_info()
{
	var hidden_delivery_info_dtls=$('#hidden_delivery_info_dtls').val();
	var page_link='requires/dyes_and_chemical_work_order_controller.php?action=delivery_info_popup&hidden_delivery_info_dtls='+hidden_delivery_info_dtls;
	var title="Place Of Delivery Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=420px,height=250px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("hdn_delivery_info_dtls").value;
		document.getElementById('hidden_delivery_info_dtls').value=theemail;
	}
}

</script>



<body onLoad="set_hotkey()">
	<div align="center">
		<div style="width:1300px;">
			<? echo load_freeze_divs ("../../",$permission);  ?><br />
		</div>
		<fieldset style="width:1320px">
			<legend>Dyes And Chemical Purchase Order</legend>
			<form name="chemicalWorkOrder_1" id="chemicalWorkOrder_1" method="" >
				<table cellpadding="0" cellspacing="2" width="100%">
					<tr>
						<td colspan="3">&nbsp;</td>
						<td>&nbsp;</td><input type="hidden" name="update_id" id="update_id" value="">
						<input type="hidden" id="report_ids" >
						<td>WO Number</td><input type="hidden" name="is_approved" id="is_approved" value="">
						<td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                    	</td>
						<td>&nbsp;</td>
						<td colspan="3">&nbsp;</td>
				  	</tr>
					<tr>
						<td width="70" class="must_entry_caption">Company</td>
						<input type="hidden" id="mstId">
						<td width="170">
                        	<?
								echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "print_button_setting(this.value);" );
 							?>
						</td>
						<td width="" >Requisition No</td>
						<td width="">
                        	<input type="text" name="txt_req_numbers"  id="txt_req_numbers" class="text_boxes" style="width:159px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly disabled />
                            <input type="hidden" name="txt_req_numbers_id"  id="txt_req_numbers_id" readonly disabled />
                            <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                            <input type="hidden" name="hid_approval_necessity_setup"  id="hid_approval_necessity_setup" readonly disabled />
                            <!-- DELETED ROW ID HERE------>
                            <input type="hidden" name="txt_delete_row"  id="txt_delete_row" readonly disabled />
                            <!-- DELETED ROW END------>
                        </td>
						<td width="">Port of Discharge</td>
						<td width=""><input type="text" name="txt_delivery_place"  id="txt_delivery_place"  style="width:159px" class="text_boxes" /></td>
                        <td align="left">Ready To Approved</td>
						<td width="180">
						    <?
						    echo create_drop_down("cbo_ready_to_approved", 170, $yes_no, "", 1, "-- Select--", 2, "", "", "");
						    ?>
						</td>
						<td width="70">Add Image</td>
						<td width="170">
							<input type="button" class="image_uploader" style="width:165px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'dyes_n_chemical_purchase_order', 0 ,1)">
						</td>
					</tr>
					<tr>
                        <td width="70" class="must_entry_caption">WO Basis</td>
						<td>
                        	<?
							   	//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
 								echo create_drop_down( "cbo_wo_basis", 160, $wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);load_drop_down( 'requires/dyes_and_chemical_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val(), 'load_details_container', 'details_container' );",0,'','','','3' );
 							?>
                        </td>
						<td width="" class="must_entry_caption">Pay Mode</td>
						<td width="">
                        	<?
							   	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 0, "",0 ,"","","","4");
 							?>
                        </td>
						<td>Port of Loading</td>
                        <td><input type="text"  name="txt_port_of_loading" style="width:159px" id="txt_port_of_loading" class="text_boxes" /></td>
						<td align="left">Contact To</td>
                        <td><input type="text"  name="txt_contact" style="width:159px" id="txt_contact" class="text_boxes" /></td>
						<td >Add File</td>
						<td>
							<input type="button" class="image_uploader" style="width:165px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'dyes_n_chemical_purchase_order', 2 ,1)">
                        </td>
					</tr>
					<tr>
						<!--<td width="130" class="must_entry_caption">Item Category</td>
						<td width="170">
                        	<?
							   	//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
								//echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/dyes_and_chemical_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val()+'**'+$('#cbo_item_category').val(), 'load_details_container', 'details_container' );",0,"5,6,7,23" );
 							?>
                        </td>-->
						<td width="70" class="must_entry_caption">Supplier</td>
						<td width="170" id="supplier_td">
						  	<?
							   //	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and b.party_type in(3) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/spare_parts_work_order_controller');",0 );
 							?>
							<input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:150px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
                            <input type="hidden" name="cbo_supplier" id="cbo_supplier" />
						</td>
						<td width="" class="must_entry_caption">Source</td>
						<td width="">
                        	<?
							   	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", 0, "",0 );
 							?>
                        </td>
						<td align="left">Tenor</td>
                        <td><input type="text"  name="txt_tenor" style="width:159px" id="txt_tenor" class="text_boxes_numeric" /></td>
						<td align="left">WO Type</td>
						<td>
						    <?
						    echo create_drop_down("cbo_wo_type", 170, $wo_type_array, "", 1, "-- Select --", 0, "", "", "");
						    ?>
						</td>						 

						<td>Delivery Address</td>
                        <td><?=$delivery_address; ?></td>
						 
						<!--<td align="left" width="">
							<input type="text" name="txt_delivery_address"  id="txt_delivery_address"  style="width:149px" class="text_boxes"/>
							 
						</td>-->
						
					</tr>
					<tr>
						<td width="70" class="must_entry_caption">WO Date</td>
						<td width="">
							<input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:149px" value="<?=date('d-m-Y')?>" />
 						</td>
						<td width="" class="must_entry_caption">Delivery Date</td>
						<td width="">
							<input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:159px" />
						</td>
						<td align="">Incoterm</td>
                        <td>
                        <?
                       		 echo create_drop_down("cbo_inco_term", 170, $incoterm, "", 1, "-- Select --", 0, "");
                        ?>
                        </td>
						<td align="left">Reference</td>
						<td >
							<input type="text" name="txt_ref" id="txt_ref"  style="width:160px;" class="text_boxes" />
						</td>
						<td width="80">Place Of Delivery</td>
						<td width="">
							<input type="text" name="txt_place_of_delivery"  id="txt_place_of_delivery"  style="width:149px" class="text_boxes" onDblClick="fn_delivery_info()" placeholder="Write or Browse"/>
							<input type="hidden" name="hidden_delivery_info_dtls" id="hidden_delivery_info_dtls" />
						</td>


					</tr>
					<tr>
						<td width="70">Currency</td>
						<td width="">
                         	<?
							   	echo create_drop_down( "cbo_currency", 160, $currency,"", 1, "-- Select --", 2, "",0 );
 							?>
                        </td>
                        <td width="">Attention</td>
						<td width=""><input type="text" name="txt_attention"  id="txt_attention" style="width:159px " class="text_boxes" /></td>
						<td>Pay Term</td>
                        <td><?php echo create_drop_down("cbo_payterm_id", 170, $pay_term, '', 1, '-- Select --', 0, "", 0, ''); //set_port_loading_value(this.value)1,2  ?></td>
						<td>L/C Type</td>
                        <td><?php $lc_type_arr=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT'];echo create_drop_down("cbo_lc_type", 170, $lc_type_arr, '', 1, '-- Select --', 0, "", 0, ''); //set_port_loading_value(this.value)1,2  ?></td>
						<td align="left">Remarks</td>
						<td width="210" colspan='3'>
							<input type="text" name="txt_remarks_mst" id="txt_remarks_mst"  style="width:150px;" class="text_boxes" />
						</td>
					</tr>
					<tr>
						 
						<td style=" padding-right:70px;padding-top:3px;" align="right"  colspan='10'>
							<?
							include("../../terms_condition/terms_condition.php");
							terms_condition(145,'txt_wo_number','../../');
							?>
						</td>
					</tr>
                </table>
                <br />
                <div style="width:1100px" id="details_container" align="left">	</div>

				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<?
								//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
								//load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
								echo load_submit_buttons( $permission, "fnc_chemical_order_entry", 0,0,"reset_form('chemicalWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_item_category').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);wo_date_genearte();",1);
							?>
                             <!-- <input type="button" id="show_button" class="formbutton" style="width:80px" value="Print2" onClick="fn_report_generated(2)" /> -->
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
<script type="text/javascript">
    set_multiselect('delivery_address','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>