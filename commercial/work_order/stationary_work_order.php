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
$permission="0_0_0_1";
//echo $permission;
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

	<?
	if($_SESSION['logic_erp']['data_arr'][146]!="")
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][146] );
		echo "var field_level_data= ". $data_arr . ";\n";
	}
	?>

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
 		if(requisition_number!="")
		{
			//freeze_window(5);
 			/*var row = $("#tbl_details tr:last").attr('id');
			var responseHtml = return_ajax_request_value(requisition_id+'**'+unique_req_dtlsID+'**'+row, 'show_dtls_listview', 'requires/stationary_work_order_controller');
			$("#tbl_details").append(responseHtml); */
			// get_php_form_data(requisition_id, "populate_pay_mode_data", "requires/stationary_work_order_controller" );
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
		{
			return;
		}
	}
	calculate_total_amount(1);
}


function fnc_chemical_order_entry(operation)
{
	if(operation==0 || operation==1 || operation==2)
	{
		alert("Save Update Delete Not Allow");return;
	}
	
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

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][146]);?>') {
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][146]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][146]);?>')==false) {
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
				var txt_item_brand=$(this).find('input[name="txt_item_brand[]"]').val();
				var cboorigin=$(this).find('select[name="cboorigin[]"]').val();
				var txt_item_model=$(this).find('input[name="txt_item_model[]"]').val();
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
				if( $("#cbo_wo_basis").val()==1 && (app_necessity_setup == 2 || app_necessity_setup == 0) && operation !=2 )
				{
					if( txt_req_qnty*1 < txt_quantity*1 )
					{
						alert("Work Order Qty Can't over than Requisition Qty");
						$("#txt_quantity_"+i).focus();
						necessity_setup_chk=1;
						return;
					}
				}
				
				
				if(txt_quantity>0 && txt_rate>0){
					j++;
					dataString+='&txt_req_dtls_id_' + j + '=' + txt_req_dtls_id + '&txt_item_id_' + j + '=' + txt_item_id  +'&txt_req_no_id_' + j + '=' + txt_req_no_id + '&txt_req_no_' + j + '=' + txt_req_no+ '&txt_item_acct_' + j + '=' + txt_item_acct + '&txt_item_desc_' + j + '=' + txt_item_desc  + '&cbo_item_category_' + j + '=' + cbo_item_category + '&txt_item_size_' + j + '=' + txt_item_size+ '&txt_item_brand_' + j + '=' + txt_item_brand+ '&cboorigin_' + j + '=' + cboorigin+ '&txt_item_model_' + j + '=' + txt_item_model + '&cbogroup_' + j + '=' + cbogroup+ '&cbouom_' + j + '=' + cbouom + '&txt_req_qnty_' + j + '=' + txt_req_qnty + '&txt_quantity_' + j + '=' + txt_quantity + '&txt_rate_' + j + '=' + txt_rate + '&txt_amount_' + j + '=' + txt_amount + '&txt_row_id_' + j + '=' + txt_row_id+ '&txt_remarks_' + j + '=' + txt_remarks+ '&cbo_buyer_' + j + '=' + cbo_buyer+ '&cbo_season_' + j + '=' + cbo_season;

				}
				i++;
			});

			//alert(dataString+"__");return;
			if(necessity_setup_chk != 0 )
			{
				alert('necessity_setup_chk'+ necessity_setup_chk);
				return;
			} 
			else if((j*1)<1 )
			{
				alert('No data found');return;
			} 
			else 
			{
				var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
				if(is_approved==1){
					alert("This Order is Approved. So Change Not Allowed");
					return;
				}

				var data="action=save_update_delete&operation="+operation+'&total_row='+j+get_submitted_data_string('garments_nature*txt_wo_number*cbo_company_name*cbo_supplier*cbo_location*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_req_numbers*txt_req_numbers_id*txt_delivery_place*hidden_delivery_info_dtls*txt_delete_row*update_id*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*txt_up_remarks*txt_discount_remarks*cbo_deal_merchant*cbo_ready_to_approved*cbo_inco_term*hid_approval_necessity_setup*txt_tenor*txt_contact*cbo_payterm_id*cbo_wo_type*txt_remarks_mst*txt_reference*cbo_lc_type',"../../")+dataString;
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
		//alert(reponse)
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
			/*if(reponse[2]>0)
			{
				show_list_view(reponse[2]+'__'+$("#cbo_wo_basis").val(),'show_dtls_listview_update','details_container','requires/stationary_work_order_controller','');
			}*/
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','../')
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
		disable_enable_fields( 'cbo_company_name*cbo_currency*cbo_wo_basis', 1, '', '' );
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
			$("#cbo_company_name").attr("disabled",true);
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

function fn_report_generated(type,mail_data='')
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
			if(type==2){
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+$('#txt_contact').val()+'*'+mail_data, "stationary_work_order_print", "requires/stationary_work_order_controller" ) ;
				show_msg("3");
			}
			if(type==3){
				if(confirm('Press  OK to open with Size/MSR and Narration\n Press Cancel to open without Size/MSR and Narration'))
				{ show=1;} 
				else{ show=0; }
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+show+'*'+mail_data, "stationary_work_order_po_print", "requires/stationary_work_order_controller" ) ;
				show_msg("3");
			}
			if(type==4){
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val()+'*'+$('#hidden_delivery_info_dtls').val()+'*'+mail_data, "stationary_work_order_print3", "requires/stationary_work_order_controller" ) ;
				show_msg("3");
			}
			if(type==5){
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$("#cbo_location").val()+'*'+$("#cbo_payterm_id").val()+'*'+$("#txt_remarks_mst").val()+'*'+$("#txt_contact").val()+'*'+$("#txt_tenor").val()+'*'+$("#cbo_template_id").val()+'*'+mail_data, "stationary_work_order_print4", "requires/stationary_work_order_controller" ) ;
				show_msg("3");
			}
			if(type==6){
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_wo_number').val()+'*'+$('#cbo_item_category').val()+'*'+$('#cbo_supplier').val()+'*'+$('#txt_wo_date').val()+'*'+$('#cbo_currency').val()+'*'+$('#cbo_wo_basis').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#cbo_source').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#txt_attention').val()+'*'+$('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$("#cbo_location").val()+'*'+$("#cbo_payterm_id").val()+'*'+$("#txt_remarks_mst").val()+'*'+$("#txt_contact").val()+'*'+$("#txt_tenor").val()+'*'+$("#cbo_template_id").val()+'*'+$("#cbo_inco_term").val()+'*'+$("#cbo_wo_type").val()+'*'+$("#txt_reference").val()+'*'+mail_data, "stationary_work_order_print5", "requires/stationary_work_order_controller" ) ;
				show_msg("3");
			}
            if(type==8){
                print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_req_numbers_id').val()+'*'+$('#cbo_template_id').val()+'*'+mail_data, "stationary_work_order_po_print2", "requires/stationary_work_order_controller" );
                show_msg("3");
            }
            if(type==9){
                print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val()+'*'+mail_data, "stationary_work_order_print6", "requires/stationary_work_order_controller" );
                show_msg("3");
            }
		}
	}
function calculate_total_amount(type)
{
	if(type==1)
	{   
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_details tbody tr').length; 
		math_operation_byName( "txt_total_amount", "txt_amount", "+", "tbl_details",ddd );
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

function math_operation_byName( target_fld, value_fld, operator, fld_range, dec_point)
{
	//number_format_common( number, dec_type, comma, path, currency )
	//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
	//	math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	if (!dec_point) var dec_point=0;
	var tot=0;
	$("#"+fld_range).find('tbody tr').each(function()
	{
		tot=(tot*1) + ($(this).find('input[name="'+value_fld+'[]"]').val()*1);
	});
	document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma,dec_point.currency);
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

		if(report_id[k]==66)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print 2"  name="Print2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==85)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print 3"  name="Print3"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
		}
        if(report_id[k]==129)
        {
            $('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print 5"  name="Print5"  onClick="fn_report_generated(6)" />&nbsp;&nbsp;&nbsp;' );
        }
        if(report_id[k]==134)
        {
            $('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print"  name="Print"  onClick="fnc_chemical_order_entry(4)" />&nbsp;&nbsp;&nbsp;' );
        }
		if(report_id[k]==137)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print 4"  name="Print4"  onClick="fn_report_generated(5)" />&nbsp;&nbsp;&nbsp;' );
		}
        if(report_id[k]==430)
        {
            $('#button_data_panel').append( '<input type="button"  id="print_with_rate" class="formbutton" style="width:80px; text-align:center;" value="PO Print 2"  name="po_print2"  onClick="fn_report_generated(8)" />&nbsp;&nbsp;&nbsp;' );
        }
		if(report_id[k]==732)
		{
			$('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="PO Print"  name="Po_print"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;&nbsp;' );
		}
		if(report_id[k]==72)
		{
			$('#button_data_panel').append( '<input type="button"  id="print6" class="formbutton" style="width:100px; text-align:center;" value="Print 6"  name="print6"  onClick="fn_report_generated(9)" />&nbsp;&nbsp;&nbsp;' );
		}
	}
}

/*function fnc_load_supplier(pay_mode)
{
	var company=$('#cbo_company_name').val();
	load_drop_down( 'requires/stationary_work_order_controller', company+'_'+pay_mode, 'load_drop_down_supplier', 'supplier_td' );
}*/
function fn_delivery_info()
{
	var hidden_delivery_info_dtls=$('#hidden_delivery_info_dtls').val();
	var page_link='requires/stationary_work_order_controller.php?action=delivery_info_popup&hidden_delivery_info_dtls='+hidden_delivery_info_dtls;
	var title="Place Of Delivery Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=420px,height=250px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("hdn_delivery_info_dtls").value;
		document.getElementById('hidden_delivery_info_dtls').value=theemail;
	}
}

function openmypage_supplier()
{
	if( form_validation('cbo_company_name*cbo_pay_mode','Company Name*Pay Mode')==false )
	{
		return;
	}
	var cbo_company_name = $('#cbo_company_name').val();
	var cbo_pay_mode = $('#cbo_pay_mode').val();
	var title = 'Supplier Name';
	var mst_id = $("#update_id").val();	
	//alert(mst_id)
	var page_link = 'requires/stationary_work_order_controller.php?cbo_company_name='+cbo_company_name+'&cbo_pay_mode='+cbo_pay_mode+ '&mst_id='+ mst_id + '&action=supplier_name_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var supplier_info=this.contentDoc.getElementById("hidden_supplier_info").value.split("__");	
		$('#cbo_supplier').val(supplier_info[0]);
		$('#txt_supplier_name').val(supplier_info[1]);
		get_php_form_data( supplier_info[0], 'load_drop_down_attention', 'requires/stationary_work_order_controller');
	}
}
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txt_remarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/stationary_work_order_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=450px,height=320px,center=1,resize=1,scrolling=0','../')
		
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
		var firstButtonId=return_global_ajax_value( company, 'get_first_selected_print_button', '', 'requires/stationary_work_order_controller');

		if(firstButtonId==66){fn_report_generated(2,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==85){fn_report_generated(4,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==129){fn_report_generated(6,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==134){fn_report_generated(4,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==137){fn_report_generated(5,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==430){fn_report_generated(8,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==732){fn_report_generated(3,mail+'___1'+'___'+mail_body);}
		else if(firstButtonId==72){fn_report_generated(9,mail+'___1'+'___'+mail_body);}
		
	}

 
     




</script>

<body onLoad="set_hotkey()">
<div style="width:1340px;" align="left">
    <!-- <div style="width:1300px;"> -->
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    <!-- </div> -->
		<fieldset style="width:1340px;">
			<legend>Stationary Purchase Order</legend>
			<form name="chemicalWorkOrder_1" id="chemicalWorkOrder_1" style="width:1340px;" method="" >
				<table cellpadding="0" cellspacing="2" width="1340">
					<tr>
					  <td colspan="3">&nbsp;</td>
					  <td>&nbsp;</td><input type="hidden" name="update_id" id="update_id" value=""><input type="hidden" id="report_ids" >
					  <td>WO Number</td><input type="hidden" name="is_approved" id="is_approved" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:139px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                      </td>
					  <td>&nbsp;</td>
					  <td colspan="3">&nbsp;</td>
				  	</tr>
					<tr>
						<td width="80" class="must_entry_caption">Company</td>
						<td width="155">
                            <?
							   	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down('requires/stationary_work_order_controller', this.value, 'load_drop_down_location', 'location_td');print_button_setting(this.value);setFieldLevelAccess(this.value);");
 							?>
						</td>
                        <td width="80" class="must_entry_caption">Currency</td>
						<td width="155">
                         	<?
							   	echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 1, "",0 );
								// Default value was 2, now 0 as per Urmi Requirement
 							?>
                        </td>
						<td width="80">Dealing Merchant</td>
						<td width="155"><? echo create_drop_down( "cbo_deal_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --",0, "",0 );?></td>
						<td width="80">Contact To</td>
						<td width="150"><input type="text" name="txt_contact"  id="txt_contact"  style="width:159px" class="text_boxes" /></td>
						<td width="80">Refusing Cause</td>
						<td width="140" id="refusing_cause" style="color:#F00; font-size:16px; font-weight:bold;"></td>
					</tr>
					<tr>
                        <td width="80" class="must_entry_caption">WO Basis</td>
						<td width="150">
                        	<?
							   	//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
 								echo create_drop_down( "cbo_wo_basis", 150, $wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);load_drop_down( 'requires/stationary_work_order_controller', $('#cbo_wo_basis').val()+'**'+$('#cbo_company_name').val(), 'load_details_container', 'details_container' );",0,'','','','3' );
 							?>
                        </td>
                        <td width="90" class="must_entry_caption">WO Date</td>
						<td width="150">
							<input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" value="<? echo date("d-m-Y"); ?>" style="width:139px"  />
 						</td>
						<td width="90">Requisition No</td>
						<td width="150">
                        	<input type="text" name="txt_req_numbers"  id="txt_req_numbers" class="text_boxes" style="width:139px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly disabled />
                            <input type="hidden" name="txt_req_numbers_id"  id="txt_req_numbers_id" value="" />
                            <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                            <!-- DELETED ROW ID HERE------>
                            <input type="hidden" name="txt_delete_row"  id="txt_delete_row" readonly disabled />
                            <input type="hidden" name="hid_approval_necessity_setup"  id="hid_approval_necessity_setup" readonly disabled />
                            <!-- DELETED ROW END------>
                        </td>
						<td width="80">Pay Term</td>
                        <td width="150"><?php echo create_drop_down("cbo_payterm_id", 170, $pay_term, '', 1, '-Select-', 0, "", 0, ''); ?></td>
						<td width="90">Tenor</td>
                        <td width="130"><input style="width:128px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
					</tr>
					<tr>
                        <td width="80" class="must_entry_caption">Pay Mode</td>
						<td width="150">
                        	<?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
							// fnc_load_supplier(this.value);
							   	echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select --", 0, "",0,"","","","" );

								//    $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg="", $selected_index="", $onchange_func="", $is_disabled="", $array_index="", $fixed_options="", $fixed_values="", $not_show_array_index="", $tab_index="", $new_conn="", $field_name="", $additionalClass="", $additionalAttributes=""
 							?>
                        </td>
						<td width="90" class="must_entry_caption">Source</td>
						<td width="150">
                        	<?
							   	echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select --", 3, "",0 );
 							?>
                        </td>
						<td width="90">Place Of Delivery</td>
						<td width="150"><input type="text" name="txt_delivery_place"  id="txt_delivery_place"  style="width:139px" class="text_boxes" onDblClick="fn_delivery_info()" placeholder="Write or Browse"/>
						<input type="hidden" name="hidden_delivery_info_dtls" id="hidden_delivery_info_dtls" />
						</td>
						<td width="80">L/C Type</td>
                        <td width="150"><?php
						 $lc_type_arr= array( 4 => "TT/Pay Order", 5 => "FTT", 6 => "FDD/RTGS");
						 echo create_drop_down("cbo_lc_type", 170, $lc_type_arr, '', 1, '-Select-', 0, "", 0, ''); ?></td>
						<td width="80">WO Type</td>
                        <td width="150"><?php echo create_drop_down("cbo_wo_type", 170, $main_fabric_co_arr, '', 1, '-Select-', 0, "", 0, ''); ?></td>
						<td width="90">&nbsp;</td>
						
					</tr>
					<tr>
						<td width="80" class="must_entry_caption">Supplier</td>
						<td width="148" id="supplier_td">
						  	<?
							   	//echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (5,8) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/stationary_work_order_controller');",1 );
 							?>
							<input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:138px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
                            <input type="hidden" name="cbo_supplier" id="cbo_supplier" />
						</td>
						<td width="90" class="must_entry_caption">Delivery Date</td>
						<td width="150">
							<input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:139px" />
						</td>
						<td width="90">Ready To Approved</td>
						<td width="150">
							<?
							echo create_drop_down("cbo_ready_to_approved", 150, $yes_no, "", 1, "-- Select--", 2, "", "", "");
							?>
						</td>
						<td width="80">Remarks</td>
						<td width="430" colspan="3"><input type="text" name="txt_remarks_mst" style="width:438px" id="txt_remarks_mst" class="text_boxes" /></td>

					</tr>
					<tr>
						<td width="80">Location</td>
                        <td width="150" id="location_td">
							<?
							echo create_drop_down("cbo_location", 150, $blank_array, "", 1, "-- Select Location --", 0, "");
							?>
                        </td>
						<td width="90">Attention</td>
						<td width="150"><input type="text" name="txt_attention"  id="txt_attention" style="width:139px " class="text_boxes" /></td>
                        <td width="90">Incoterm</td>
						<td width="150">
							<?
							echo create_drop_down("cbo_inco_term", 150, $incoterm, "", 1, "-Select-", 0, "");
							?>
						</td>
						<td width="80">Reference</td>
                        <td width="150"><input type="text"  name="txt_reference" style="width:159px" id="txt_reference" class="text_boxes" /></td>
						<td width="90">Add Image</td>
						<td width="140">
							<input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'stationary_purchase_order', 0 ,1)"> 
						</td>
					</tr>
					<tr>
						<td colspan="7">&nbsp;</td>
						<td width="140" height="10">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(146,'txt_wo_number','../../');
                            ?>
                        </td>
						<td width="90">Add File</td>
						<td >
							<input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'stationary_purchase_order', 2 ,1)"> 
                        </td>
                    </tr>
                </table>
                <br />
                <div style="width:1494px" id="details_container" align="left">	</div>

				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="10" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<?
								//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
								//load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve )
								echo load_submit_buttons( $permission, "fnc_chemical_order_entry", 0,0 ,"reset_form('chemicalWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);",1);
								echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, "");
							?>
                            <!--<input type="button" id="show_button" class="formbutton" style="width:80px" value="Print2" onClick="fn_report_generated(2)" />-->
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
