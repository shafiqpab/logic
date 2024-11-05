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
Report	By		:	Aziz
Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Work Order","../../", 1, 1, $unicode,'','');

$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}

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
	$("#cbo_pay_mode").val('');
	if(str==1)
	{
 		$("#txt_req_numbers").attr("disabled",false);
 		var company = $("#cbo_company_name").val();
 		var necessity_setup="";
		necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/stationary_work_order_controller');
		$("#hid_approval_necessity_setup").val(necessity_setup);
		
		//$("#cbo_pay_mode").attr("disabled",true);
	}
	else
	{
		$("#hid_approval_necessity_setup").val('');
		$("#txt_req_numbers").val('');
 		$("#txt_req_numbers").attr("disabled",true);
		//$("#cbo_pay_mode").attr("disabled",false);
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

 	var page_link = 'requires/stationary_work_order_controller.php?action=requitision_popup&company='+company+'&garments_nature='+garments_nature+'&txt_req_dtls_id='+txt_req_dtls_id+'&req_numbers='+req_numbers+'&req_numbers_id='+req_numbers_id;
	var title = "Requisition No Search";

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
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
 		if(requisition_number!="")
		{
			//freeze_window(5);
 			/*var row = $("#tbl_details tr:last").attr('id');
			var responseHtml = return_ajax_request_value(requisition_id+'**'+unique_req_dtlsID+'**'+row, 'show_dtls_listview', 'requires/stationary_work_order_controller');
			$("#tbl_details").append(responseHtml); */
			get_php_form_data(requisition_id, "populate_pay_mode_data", "requires/stationary_work_order_controller" );
			var row = 0;
			var responseHtml = return_ajax_request_value(requisition_id+'**'+req_dtlsID+'**'+row+'**'+update_id+'**'+company, 'show_dtls_listview', 'requires/stationary_work_order_controller');
			$('#tbl_details tr:not(:first)').remove();
			$("#tbl_details").append(responseHtml);

			calculate_total_amount(1);
 			release_freezing();
		}
		else
		{
			$("#details_container").html('');
		}
	}
}


function calculate_yarn_consumption_ratio(i)
{
	var cbocount=$('#txt_quantity_'+i).val()*1;
	var cbocompone=$('#txt_rate_'+i).val()*1;
	var ddd={ dec_type:2, comma:0, currency:''}
	math_operation( 'txt_amount_'+i, 'txt_quantity_'+i+'*txt_rate_'+i, '*','',ddd);
	
	
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
			var responseHtml = return_ajax_request_value(row+'**'+company, 'append_load_details_container', 'requires/stationary_work_order_controller');
			//alert(responseHtml);return;
			$("#tbl_details tbody").append(responseHtml);
		}
	}
	else if(type=="decrease")
	{
		var row = $("#tbl_details tbody tr").length;
		//alert(row*1+"##"+rowid*1); //&& row*1==rowid*1
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
	if(operation==4)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('WO Number Not Save.');
			return;
		}
	 var report_title=$( "div.form_caption" ).html();
	 print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$("#cbo_location").val()+'*'+$("#cbo_template_id").val(), "stationary_work_print", "requires/stationary_work_order_controller" )
	 return;
	}
	else if(operation==5)
	{
	 var report_title=$( "div.form_caption" ).html();
	 print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$("#cbo_location").val()+'*'+$("#cbo_template_id").val(), "stationary_work_print", "requires/stationary_work_order_controller" )
	 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
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
			/*var detailsData="";
			for(var i=1;i<=row;i++)
			{
				try{
					if( form_validation('txt_item_desc_'+i+'*cbo_item_category_'+i+'*cbogroup_'+i+'*cbouom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i,'Item Account*Item Description*Item Category*Item Size*Item Group*UOM*Work Order Quantity*Rate*Amount')==false)
					{
						return;
					}
					if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0)
					{
						alert("Quantity OR Rate Can not be 0 or less than 0");
						$("#txt_quantity_"+i).focus();
						return;
					}
					detailsData+='*txt_req_dtls_id_'+i+'*txt_item_id_'+i+'*txt_req_no_id_'+i+'*txt_req_no_'+i+'*txt_item_acct_'+i+'*txt_item_desc_'+i+'*cbo_item_category_'+i+'*txt_item_size_'+i+'*cbogroup_'+i+'*cbouom_'+i+'*txt_req_qnty_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i+'*cbo_buyer_'+i+'*cbo_season_'+i;
				}
				catch(err){}
			}*/
			//alert(detailsData);return;
			var app_necessity_setup=$('#hid_approval_necessity_setup').val();
			
			var j=0; var i=1; var dataString=''; var necessity_setup_chk=0;
			$("#tbl_details").find('tbody tr').each(function()
			{

				var txt_req_dtls_id=$(this).find('input[name="txt_req_dtls_id[]"]').val();
				var txt_item_id=$(this).find('input[name="txt_item_id[]"]').val();
				var txt_req_no_id=$(this).find('input[name="txt_req_no_id[]"]').val();
				var txt_req_no=$(this).find('input[name="txt_req_no[]"]').val();

				var txt_item_acct=$(this).find('input[name="txt_item_acct[]"]').val();
				var txt_item_desc=$(this).find('input[name="txt_item_desc[]"]').val();
				var cbo_item_category=$(this).find('select[name="cbo_item_category[]"]').val();

				var txt_item_size=$(this).find('input[name="txt_item_size[]"]').val();
				var cbogroup=$(this).find('select[name="cbogroup[]"]').val();
				var cbouom=$(this).find('select[name="cbouom[]"]').val();
				var txt_req_qnty=$(this).find('input[name="txt_req_qnty[]"]').val();

				var txt_quantity=$(this).find('input[name="txt_quantity[]"]').val()*1;
				var txt_rate=$(this).find('input[name="txt_rate[]"]').val()*1;
				var txt_amount=$(this).find('input[name="txt_amount[]"]').val();
				var txt_row_id=$(this).find('input[name="txt_row_id[]"]').val();

				var txt_remarks=$(this).find('input[name="txt_remarks[]"]').val();
				var cbo_buyer=$(this).find('select[name="cbo_buyer[]"]').val();
				var cbo_season=$(this).find('select[name="cbo_season[]"]').val();
				//alert(txt_rate);return;
				/*if( txt_quantity*1 <= 0 || txt_rate*1 <= 0 || txt_item_desc=="" || cbo_item_category==0 || cbogroup==0 || cbouom==0)
				{
					alert("Quantity Or Rate Can not be 0 or less than 0");
					$("#txt_quantity_"+i).focus();
					return;
				}*/
				//alert(cbo_wo_basis+'='+app_necessity_setup+'='+txt_req_qnty+'='+txt_quantity);
				if( $("#cbo_wo_basis").val()==1 && app_necessity_setup == 2 ){
					if( txt_req_qnty*1 < txt_quantity*1 ){
						alert("Work Order Qty Can't over than Requisition Qty");
						$("#txt_quantity_"+i).focus();
						necessity_setup_chk=1;
						return;
					}
				}
				
				
				if(txt_quantity>0 && txt_rate>0){
					j++;
					dataString+='&txt_req_dtls_id_' + j + '=' + txt_req_dtls_id + '&txt_item_id_' + j + '=' + txt_item_id  +'&txt_req_no_id_' + j + '=' + txt_req_no_id + '&txt_req_no_' + j + '=' + txt_req_no+ '&txt_item_acct_' + j + '=' + txt_item_acct + '&txt_item_desc_' + j + '=' + txt_item_desc  + '&cbo_item_category_' + j + '=' + cbo_item_category+ '&txt_item_size_' + j + '=' + txt_item_size + '&cbogroup_' + j + '=' + cbogroup+ '&cbouom_' + j + '=' + cbouom + '&txt_req_qnty_' + j + '=' + txt_req_qnty + '&txt_quantity_' + j + '=' + txt_quantity + '&txt_rate_' + j + '=' + txt_rate + '&txt_amount_' + j + '=' + txt_amount + '&txt_row_id_' + j + '=' + txt_row_id+ '&txt_remarks_' + j + '=' + txt_remarks+ '&cbo_buyer_' + j + '=' + cbo_buyer+ '&cbo_season_' + j + '=' + cbo_season;

				}
				i++;
			});

			//alert(dataString+"__");//return;
			if(necessity_setup_chk != 0 ){
				return;
			} else if((j*1)<1 ){
				alert('No data found');return;
			} else {
				var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
				if(is_approved==1){
					alert("This Order is Approved. So Change Not Allowed");
					return;
				}

				var data="action=save_update_delete&operation="+operation+'&total_row='+j+get_submitted_data_string('garments_nature*txt_wo_number*cbo_company_name*cbo_supplier*cbo_location*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_req_numbers*txt_req_numbers_id*txt_delivery_place*txt_delete_row*update_id*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*txt_up_remarks*cbo_deal_merchant*cbo_ready_to_approved*cbo_inco_term*hid_approval_necessity_setup',"../../")+dataString;
				//alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/stationary_work_order_controller.php",true);
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
		show_msg(trim(reponse[0]));
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_wo_number").val(reponse[1]);
			$("#update_id").val(reponse[2]);
			disable_enable_fields( 'cbo_company_name*cbo_item_category*cbo_supplier*cbo_currency*cbo_wo_basis', 1, '', '' );
			show_list_view(reponse[2]+'__'+$("#cbo_wo_basis").val(),'show_dtls_listview_update','details_container','requires/stationary_work_order_controller','');
			set_button_status(1, permission, 'fnc_chemical_order_entry',1);
		}
		else if(reponse[0]==2)
		{
			reset_form('chemicalWorkOrder_1','details_container','','','','');
			release_freezing(); return;
		}
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
			if(reponse[2]>0)
			{
				show_list_view(reponse[2]+'__'+$("#cbo_wo_basis").val(),'show_dtls_listview_update','details_container','requires/stationary_work_order_controller','');
			}
			release_freezing(); return;
		}

		else if(reponse[0]==15)
		{
			alert(reponse[1]);release_freezing(); return;
		}
		else if(reponse[0]==14)
		{
			alert(reponse[1]);release_freezing(); return;
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
	var page_link = 'requires/stationary_work_order_controller.php?action=wo_popup&company='+company+'&garments_nature='+garments_nature;
	var title = "Order Search";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		//freeze_window(5);
		var theform=this.contentDoc.forms[0];
		var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");
		//alert(hidden_wo_number[0]);return;
		reset_form('chemicalWorkOrder_1','details_container','','','','cbo_currency');
		$("#txt_wo_number").val(hidden_wo_number[0]);
		$("#update_id").val(hidden_wo_number[1]);
		get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/stationary_work_order_controller" );
		disable_enable_fields( 'cbo_company_name*cbo_supplier*cbo_currency*cbo_wo_basis', 1, '', '' );
		show_list_view(hidden_wo_number[1]+'__'+$("#cbo_wo_basis").val(),'show_dtls_listview_update','details_container','requires/stationary_work_order_controller','');
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		if(cbo_wo_basis==1){
			var necessity_setup="";
			necessity_setup=return_global_ajax_value( company, 'necessity_setup_variable_form_lib', '', 'requires/stationary_work_order_controller');
			$("#hid_approval_necessity_setup").val(necessity_setup);
		}
		set_button_status(1, permission, 'fnc_chemical_order_entry',1);

		//release_freezing();
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
	var page_link = 'requires/stationary_work_order_controller.php?action=account_order_popup&company='+company;
	var title = 'Search Item Details';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("item_1").value;

 		if(theemail!="")
		{

 			var data=theemail+"**"+tot_row+"**"+company;
			var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form_asd', '', 'requires/stationary_work_order_controller');
  			 //alert (list_view_orders);
			/*$("#tbl_details tbody tr").remove();
 			$("#tbl_details tbody").append(list_view_orders);*/
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
		   var report_title=$( "div.form_caption" ).html();
		  print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val(), "stationary_work_order_print", "requires/stationary_work_order_controller" ) ;
			show_msg("3");
		}
	}
function calculate_total_amount(type)
{
	if(type==1)
	{   
		var ddd={ dec_type:2, comma:0, currency:''}
		var numRow = $('table#tbl_details tbody tr').length; 
		math_operation( "txt_total_amount", "txt_amount_", "+", numRow,ddd );
	}

	var txt_total_amount=$('#txt_total_amount').val()*1;
	var txt_upcharge=$('#txt_upcharge').val();
	var txt_discount=$('#txt_discount').val();
	if(txt_total_amount>0)
	{
		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(number_format_common(net_tot_amnt,2,"",""));
	}
}
function print_button_setting(company)
{
	$('#button_data_panel').html('');
	//alert(company);
	get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/stationary_work_order_controller' );
}

function print_report_button_setting(report_ids)
{
	//alert(report_ids);
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{

		if(report_id[k]==134)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:150px; text-align:center;" value="Print"  name="Print"  onClick="fnc_chemical_order_entry(4)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==66)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:150px; text-align:center;" value="Print2"  name="Print2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
		}
	}
}

function fnc_load_supplier(pay_mode)
{
	var company=$('#cbo_company_name').val();
	load_drop_down( 'requires/stationary_work_order_controller', company+'_'+pay_mode, 'load_drop_down_supplier', 'supplier_td' );
}
</script>

<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1410px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1510px">
			<form name="chemicalWorkOrder_1" id="chemicalWorkOrder_1" method="" >
				<table cellpadding="0" cellspacing="2" width="900">
					<tr>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td><input type="hidden" name="update_id" id="update_id" value=""><input type="hidden" id="report_ids" >
					  <td>WO Number</td><input type="hidden" name="is_approved" id="is_approved" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                      </td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
				  	</tr>
					<tr>
						<td width="100" class="must_entry_caption">Company</td>
						<td width="170">
                            <?
							   	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down('requires/stationary_work_order_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down('requires/stationary_work_order_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting(this.value);");
 							?>
						</td>
                        <td width="" class="must_entry_caption">WO Basis</td>
						<td width="">
                        	<?
							   	//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
 								echo create_drop_down( "cbo_wo_basis", 170, $wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);load_drop_down( 'requires/stationary_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val(), 'load_details_container', 'details_container' );",0,'','','','3' );
 							?>
                        </td>
                        <td width="" class="must_entry_caption">Pay Mode</td>
						<td width="">
                        	<?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
							   	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 0, "fnc_load_supplier(this.value)",0,"","","","" );
 							?>
                        </td>
					</tr>
					<tr>
						<td width="130" class="must_entry_caption">Supplier</td>
						<td width="170" id="supplier_td">
						  	<?
							   	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (5,8) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
 							?>
						</td>
						<td>Location</td>
                        <td id="location_td">
							<?
							echo create_drop_down("cbo_location", 170, $blank_array, "", 1, "-- Select Location --", 0, "");
							?>
                        </td>
						<td width="" class="must_entry_caption">Currency</td>
						<td width="">
                         	<?
							   	echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select --", 1, "",0 );
								// Default value was 2, now 0 as per Urmi Requirement
 							?>
                        </td>
					</tr>
					<tr>
						<td width="" class="must_entry_caption">WO Date</td>
						<td width="">
							<input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" value="<? echo date("d-m-Y"); ?>" style="width:159px"  />
 						</td>
						<td width="" class="must_entry_caption">Source</td>
						<td width="">
                        	<?
							   	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", 3, "",0 );
 							?>
                        </td>
						<td width="" class="must_entry_caption">Delivery Date</td>
						<td width="">
							<input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:159px" />
						</td>
                        

					</tr>
					<tr>
						<!--<td width="130" class="must_entry_caption">Item Category</td>
						<td width="170">
                        	<?
							   	//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
								//echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/stationary_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val()+'**'+$('#cbo_item_category').val(), 'load_details_container', 'details_container' );load_drop_down('requires/stationary_work_order_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );",0,"4,11" );
 							?>
                        </td>-->
                        <td width="">Attention</td>
						<td width=""><input type="text" name="txt_attention"  id="txt_attention" style="width:159px " class="text_boxes" /></td>
                        <td width="">Dealing Merchant</td>
						<td width=""><? echo create_drop_down( "cbo_deal_merchant", 170, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --",0, "",0 );
 							?></td>
						<td width="">Requisition No</td>
						<td width="">
                        	<input type="text" name="txt_req_numbers"  id="txt_req_numbers" class="text_boxes" style="width:159px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly disabled />
                            <input type="hidden" name="txt_req_numbers_id"  id="txt_req_numbers_id" value="" />
                            <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                            <!-- DELETED ROW ID HERE------>
                            <input type="hidden" name="txt_delete_row"  id="txt_delete_row" readonly disabled />
                            <input type="hidden" name="hid_approval_necessity_setup"  id="hid_approval_necessity_setup" readonly disabled />
                            <!-- DELETED ROW END------>
                        </td>
                    </tr>
					<tr>
						<td width="">Place Of Delivery</td>
						<td width=""><input type="text" name="txt_delivery_place"  id="txt_delivery_place"  style="width:159px" class="text_boxes" />
						</td>

						<td align="">Ready To Approved</td>
						<td>
							<?
							echo create_drop_down("cbo_ready_to_approved", 170, $yes_no, "", 1, "-- Select--", 2, "", "", "");
							?>
						</td>

						<td align="">Inco Term</td>
						<td>
							<?
							echo create_drop_down("cbo_inco_term", 170, $incoterm, "", 0, "", 0, "");
							?>
						</td>
					</tr>
					<tr>
						<td>Refusing Cause</td>
						<td colspan="3" id="refusing_cause" style="color:#F00; font-size:16px; font-weight:bold;"></td>
                        <td align="center" height="10" colspan="2">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(146,'txt_wo_number','../../');
                            ?>

                            <!--<input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:25px; margin-top:5px" value="Terms & Condition" onClick="open_terms_condition_popup('requires/stationary_work_order_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                        </td>
					</tr>
                </table>
                <br />
                <div style="width:1500px" id="details_container" align="left">	</div>

				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<?
								//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
								//load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
								echo load_submit_buttons( $permission, "fnc_chemical_order_entry", 0,0 ,"reset_form('chemicalWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);",1);
								echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, "");
							?>
                            <!--<input type="button" id="show_button" class="formbutton" style="width:80px" value="Print2" onClick="fn_report_generated(2)" />-->

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
