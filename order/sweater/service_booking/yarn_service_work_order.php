<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for sweater Yarn Serviec Work Order
Functionality	:
JS Functions	:
Created by		:	Md. Zakaria Joy
Creation date 	: 	21-07-2019
Updated by 		:
Update date		:
QC Performed BY	:   Ashique
QC Date			:	21-07-2019
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Service Work Order", "../../../", 1, 1,$unicode,'','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission='<? echo $permission; ?>';


//-------------------------------function---------------------------------------------------------------

function fnc_yarn_service_wo(operation)
{
	if( form_validation('cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*cbo_pay_mode*txt_delivery_date','Company Name *Service Type*Factory*Booking Date*Pay Mode*Delivery Date')==false )
	{
		return;
	}
	else
	{
		var j=0; var dtlsDataString='';
		var cbo_with_order = $("#cbo_with_order").val();
		var cbo_service_type = $("#cbo_service_type").val();
		var validation=true;
		$("#dtls_container tr").each(function()
		{
			var txt_job_no			= trim($("#txt_job_no_"+j).val());
			var txt_job_id			= $("#txt_job_id_"+j).val();
			var txt_lot 			= $("#txt_lot_"+j).val();
			var txt_pro_id 	 		= $("#txt_pro_id_"+j).val();
			var cbo_count 			= $("#cbo_count_"+j).val();
			var cbo_color 			= $("#cbo_color_"+j).val();
			var txt_item_des 		= $("#txt_item_des_"+j).val();
			var cbo_uom 	 		= $("#cbo_uom_"+j).val();
			var txt_wo_qty 			= $("#txt_wo_qty_"+j).val();
			var txt_rate 			= $("#txt_rate_"+j).val();
			var txt_amount 			= $("#txt_amount_"+j).val();
			var txt_bag 			= $("#txt_bag_"+j).val();
			var txt_cone 			= $("#txt_cone_"+j).val();
			var txt_min_req_cone 	= $("#txt_min_req_cone_"+j).val();
			var txt_remarks 		= $("#txt_remarks_"+j).val();
			var dtls_update_id 		= $("#dtls_update_id_"+j).val();

			if((cbo_with_order==1 && txt_job_no=="") || txt_lot=="" || txt_wo_qty=="" || txt_rate==""){
				validation=false;
				return;
			}

			dtlsDataString+='&txt_job_no_' + j + '=' + txt_job_no + '&txt_job_id_' + j + '=' + txt_job_id + '&txt_lot_' + j + '=' + txt_lot + '&txt_pro_id_' + j + '=' + txt_pro_id + '&txt_item_des_' + j + '=' + txt_item_des + '&cbo_uom_' + j + '=' + cbo_uom + '&txt_wo_qty_' + j +'='+txt_wo_qty + '&cbo_color_' + j + '=' + cbo_color + '&cbo_count_' + j + '=' + cbo_count + '&txt_rate_' + j + '=' + txt_rate + '&txt_amount_' + j + '=' + txt_amount + '&txt_bag_' + j + '=' + txt_bag + '&txt_cone_' + j + '=' + txt_cone + '&txt_min_req_cone_' + j + '=' + txt_min_req_cone + '&txt_remarks_' + j + '=' + txt_remarks + '&dtls_update_id_' + j + '=' + dtls_update_id;
			j++;
		});

		if(cbo_service_type==15 || cbo_service_type==50 || cbo_service_type==51){
			if( form_validation('cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color','Count*Composition*%*Type*Color')==false )
			{
				return;
			}
		}

		if(validation==false){
			alert("Required fields can not be empty");
			return;
		}else{
			var twistingDataStr="*cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color*hdn_fin_update_id";
			var mstDataString = "cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*txt_attention*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*txt_delivery_date*cbo_is_sales_order*update_id*txt_booking_no*cbo_with_order*update_dtls_ids*txt_ref_no"+twistingDataStr;

			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string(mstDataString,"../../")+dtlsDataString;
			freeze_window(operation);
			http.open("POST","requires/yarn_service_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_service_wo_response;
		}
	}
}

function fnc_yarn_service_wo_response()
{
	if(http.readyState == 4)
	{
		var response=trim(http.responseText).split('**');
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			show_msg(trim(response[0]));
			release_freezing();

		}
		else if(response[0]==10||response[0]==11)
		{
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}else if(response[0]==22)
		{
			alert(response[1]);
			release_freezing();
			return;
		}

		show_msg(trim(response[0]));
		release_freezing();
		$("#update_id").val(response[2]);
		$("#txt_booking_no").val(response[1]);
		$('#cbo_is_sales_order').attr('disabled','disabled');
		var cbo_service_type = $("#cbo_service_type").val();
		show_list_view(response[2]+"_"+cbo_service_type,'show_dtls_list_view','list_container','requires/yarn_service_work_order_controller','');
		if(response[0]==0)
		{
			set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
		}
		$('#txt_wo_qty').attr("placeholder","");
		var clone_tr = $('#dtls_container tr:first').clone();
		$('#dtls_container').html('').append(clone_tr);
		$reset_fin_prod_dtls="";
		if(cbo_service_type==15 || cbo_service_type==50 || cbo_service_type==51){
			$reset_fin_prod_dtls = "*cbo_fin_count*cbo_fin_composition*txt_fin_perc*cbo_fin_type*txt_fin_color";
		}
		reset_form('','','txt_job_no_0*txt_job_id_0*cbo_count_0*txt_item_des_0*txt_lot_0*txt_pro_id_0*txt_wo_qty_0*txt_rate_0*txt_amount_0*txt_bag_0*txt_cone_0*txt_min_req_cone_0*txt_remarks_0*dtls_update_id_0*update_dtls_ids'+$reset_fin_prod_dtls,'','','update_id*txt_booking_no*cbo_is_sales_order*');

		$("#txt_wo_qty_0").removeAttr("placeholder");

		show_msg(trim(response[0]));
		release_freezing();
	}
}

function fnc_calculate(thisVal,i)
{
	var cbo_service_type = $("#cbo_service_type").val();
	if(cbo_service_type==15 || cbo_service_type==50 || cbo_service_type==51){
		//var dyeing_charge = $('#txt_rate_'+i).val()*1;
		var dyeing_charge=$('#txt_rate_0').val();
		$('#dtls_container tr').each(function(j)
		{
			var wo_qty=$('#txt_wo_qty_'+j).val()*1;
			var place_val=$('#txt_wo_qty_'+j).attr("placeholder")*1;

			if(place_val<wo_qty)
			{
				$('#txt_wo_qty_'+j).val("");
				$('#txt_amount_'+j).val("");
				$('#txt_rate_'+j).val("");
				return;
			}
			$(".dc_rate").val(dyeing_charge);
			var amount=(wo_qty*1)*(dyeing_charge*1);
			$('#txt_amount_'+j).val(number_format_common(amount,2));
		});
	}else{
		var wo_qty=$('#txt_wo_qty_0').val();
		var place_val=$('#txt_wo_qty_0').attr("placeholder")*1;
		if(place_val<wo_qty)
		{
			$('#txt_wo_qty_0').val("");
			return;
		}
		var dyeing_charge=$('#txt_rate_0').val();
		var amount=(wo_qty*1)*(dyeing_charge*1);
		$('#txt_amount_0').val(number_format_common(amount,2));
	}

}

function openmypage_job(thisVal,title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	else
	{
		var is_sales_order = $("#cbo_is_sales_order").val();
		var company = $("#cbo_company_name").val();
		var width = "";
		if(is_sales_order == 1)
		{
			width = "720px";
		}else
		{
			width = "620px";
		}
		page_link='requires/yarn_service_work_order_controller.php?action=job_search_popup&company='+company+'&is_sales_order='+is_sales_order;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("hidden_job_no").value;
			var dataArr=data.split(',');
			freeze_window(5);

			$(thisVal).val(dataArr[1]);
			$(thisVal).siblings().val(dataArr[0]);
			$(thisVal).parent().parent().siblings().find("td:first input[type='text']").val(dataArr[1]);
			$(thisVal).parent().parent().siblings().find("td:first input[type='hidden']").val(dataArr[0]);
			release_freezing();

		}
	}
}

function openmypage_lot()
{
	if (form_validation('cbo_company_name','Company Number')==false)
	{
		return;
	}
	else
	{
		var company = $("#cbo_company_name").val();
		var job_no = $("#txt_job_no_0").val();
		var is_sales_order = $("#cbo_is_sales_order").val();
		var cbo_service_type = $("#cbo_service_type").val();
		var cbo_with_order = $("#cbo_with_order").val();

		if(cbo_with_order==1){
			if (form_validation('txt_job_no_0','Job No')==false)
			{
				return;
			}
		}
		page_link='requires/yarn_service_work_order_controller.php?action=lot_search_popup&company='+company+'&job_no='+job_no+'&is_sales_order='+is_sales_order+'&cbo_service_type='+cbo_service_type+'&cbo_with_order='+cbo_with_order;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Lot Number Search', 'width=1120px,height=380px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);

			var theform=this.contentDoc.forms[0];
			var product_info = this.contentDoc.getElementById("hidden_product").value.split("#");
			var initial_val = (product_info.length > 1)?1:0;

			if(product_info.length > 1){

				$("#dtls_container").find("tr:gt(0)").remove();
				for( var i = 1; i < product_info.length; i++ ) {
					var job_no = $("#txt_job_no_0").val();
					var job_id = $("#txt_job_id_0").val();

					$('#dtls_container').append($('#dtls_container tr:last').clone());
					$('#dtls_container tr').each(function(i) {
						var data = product_info[i].split("*");
						var selectinput = $(this).find('select');
						var textinput = $(this).find('input');
						if(i===0){
							textinput.eq(0).attr('id', 'txt_job_no_' + i).val(job_no);
							textinput.eq(1).attr('id', 'txt_job_id_' + i).val(job_id);
						}else{
							textinput.eq(0).attr('id', 'txt_job_no_' + i).val(job_no).attr('disabled',true);
							textinput.eq(1).attr('id', 'txt_job_id_' + i).val(job_id).attr('disabled',true);
						}

						textinput.eq(2).attr('id', 'txt_lot_' + i).val(data[2]);
						textinput.eq(3).attr('id', 'txt_pro_id_' + i).val(data[3]);
						textinput.eq(4).attr('id', 'txt_item_des_' + i).val(data[0]).attr('disabled',true);
						textinput.eq(5).attr('id', 'txt_wo_qty_' + i).attr("placeholder",data[4]).attr("onKeyUp","fnc_calculate(this,"+i+")").val('');
						textinput.eq(6).attr('id', 'txt_rate_' + i).val('').attr("onKeyUp","fnc_calculate(this,"+i+")");
						textinput.eq(7).attr('id', 'txt_amount_' + i).val('');
						textinput.eq(8).attr('id', 'txt_bag_' + i).val('');
						textinput.eq(9).attr('id', 'txt_cone_' + i).val('');
						textinput.eq(10).attr('id', 'txt_min_req_cone_' + i).val('');
						textinput.eq(11).attr('id', 'txt_remarks_' + i).val('');
						//textinput.eq(12).attr('id', 'dtls_update_id_' + i);

						selectinput.eq(0).attr('id', 'cbo_count_' + i).val(data[1]).attr('disabled',true);
						selectinput.eq(1).attr('id', 'cbo_color_' + i).val(data[5]).attr('disabled',true);
						selectinput.eq(2).attr('id', 'cbo_uom_' + i);

					});
				}
			}else{
				var data = product_info[0].split("*");
				var selectinput = $('#dtls_container tr').find('select');
				var textinput = $('#dtls_container tr').find('input');

				//textinput.eq(0).attr('id', 'txt_job_no_0').val('');
				//textinput.eq(1).attr('id', 'txt_job_id_0').val('');
				textinput.eq(2).attr('id', 'txt_lot_0' ).val(data[2]);
				textinput.eq(3).attr('id', 'txt_pro_id_0' ).val(data[3]);
				textinput.eq(4).attr('id', 'txt_item_des_0' ).val(data[0]).attr('disabled',true);
				textinput.eq(5).attr('id', 'txt_wo_qty_0' ).attr("placeholder",data[4]).val('').attr("onKeyUp","fnc_calculate(this,1)");;
				textinput.eq(6).attr('id', 'txt_rate_0').val('').attr("onKeyUp","fnc_calculate(this,1)");
				textinput.eq(7).attr('id', 'txt_amount_0').val('');
				textinput.eq(8).attr('id', 'txt_bag_0').val('');
				textinput.eq(9).attr('id', 'txt_cone_0').val('');
				textinput.eq(10).attr('id', 'txt_min_req_cone_0').val('');
				textinput.eq(11).attr('id', 'txt_remarks_0').val('');
				//textinput.eq(12).attr('id', 'dtls_update_id_0');

				selectinput.eq(0).attr('id', 'cbo_count_0' ).val(data[1]).attr('disabled',true);
				selectinput.eq(1).attr('id', 'cbo_color_0').val(data[5]).attr('disabled',true);
				selectinput.eq(2).attr('id', 'cbo_uom_0' );
			}
			release_freezing();
		}
	}
}

function create_row(booking_id)
{
	var cbo_with_order = $("#cbo_with_order").val();

	var response=return_global_ajax_value( booking_id, 'child_form_input_row', '', 'requires/yarn_service_work_order_controller');
	var product_info=response.split("#");

	if(product_info.length > 1){

		$("#dtls_container").find("tr:gt(0)").remove();
		for( var i = 1; i < product_info.length; i++ ) {
			var job_no = $("#txt_job_no_0").val();
			var job_id = $("#txt_job_id_0").val();

			$('#dtls_container').append($('#dtls_container tr:last').clone());

			$('#dtls_container tr').each(function(i) {
				var data = product_info[i].split("*");
				var selectinput = $(this).find('select');
				var textinput = $(this).find('input');
				if(i===0){
					if(cbo_with_order==2){
						textinput.eq(0).attr('id', 'txt_job_no_' + i).val('').attr("disabled","disabled");
						textinput.eq(1).attr('id', 'txt_job_id_' + i).val('');
					}else{
						textinput.eq(0).attr('id', 'txt_job_no_' + i).val('');
						textinput.eq(1).attr('id', 'txt_job_id_' + i).val('');
					}

				}else{
					textinput.eq(0).attr('id', 'txt_job_no_' + i).val('').attr("disabled","disabled");
					textinput.eq(1).attr('id', 'txt_job_id_' + i).val('');
				}

				textinput.eq(2).attr('id', 'txt_lot_' + i).val('');
				textinput.eq(3).attr('id', 'txt_pro_id_' + i).val('');

				textinput.eq(4).attr('id', 'txt_item_des_' + i).val('');
				textinput.eq(5).attr('id', 'txt_wo_qty_' + i).attr("onKeyUp","fnc_calculate(this,"+i+")");
				textinput.eq(6).attr('id', 'txt_rate_' + i).val('').attr("onKeyUp","fnc_calculate(this,"+i+")");
				textinput.eq(7).attr('id', 'txt_amount_' + i).val('');
				textinput.eq(8).attr('id', 'txt_bag_' + i).val('');
				textinput.eq(9).attr('id', 'txt_cone_' + i).val('');
				textinput.eq(10).attr('id', 'txt_min_req_cone_' + i).val('');
				textinput.eq(11).attr('id', 'txt_remarks_' + i).val('');
				//textinput.eq(12).attr('id', 'dtls_update_id_' + i).val('');

				selectinput.eq(0).attr('id', 'cbo_count_' + i).val('');
				selectinput.eq(1).attr('id', 'cbo_color_' + i).val('');
				selectinput.eq(2).attr('id', 'cbo_uom_' + i);

			});
		}
	}else{

		//alert(555);
		$("#dtls_container").find("tr:gt(0)").remove();
		var data = product_info[0].split("*");
		var selectinput = $('#dtls_container tr').find('select');
		var textinput = $('#dtls_container tr').find('input');

		if(cbo_with_order==2){
			textinput.eq(0).attr('id', 'txt_job_no_0').val('').attr("disabled","disabled").removeAttr("placeholder");
		}else{
			textinput.eq(0).attr('id', 'txt_job_no_0').val('').removeAttr("disabled").attr("placeholder","Doubole Click for Job");
		}


		textinput.eq(1).attr('id', 'txt_job_id_0').val('');
		textinput.eq(2).attr('id', 'txt_lot_0' ).val(data[2]);
		textinput.eq(3).attr('id', 'txt_pro_id_0' ).val(data[3]);
		textinput.eq(4).attr('id', 'txt_item_des_0' ).val(data[0]).attr('disabled',true);
		textinput.eq(5).attr('id', 'txt_wo_qty_0' ).attr("placeholder",data[4]);
		textinput.eq(5).attr('id', 'txt_wo_qty_0').attr("onKeyUp","fnc_calculate(this,1)");
		textinput.eq(6).attr('id', 'txt_rate_0').val('').attr("onKeyUp","fnc_calculate(this,1)");
		textinput.eq(7).attr('id', 'txt_amount_0').val('');
		textinput.eq(8).attr('id', 'txt_bag_0').val('');
		textinput.eq(9).attr('id', 'txt_cone_0').val('');
		textinput.eq(10).attr('id', 'txt_min_req_cone_0').val('');
		textinput.eq(11).attr('id', 'txt_remarks_0').val('');
		//textinput.eq(12).attr('id', 'dtls_update_id_0').val('');

		selectinput.eq(0).attr('id', 'cbo_count_0' ).val(data[1]).attr('disabled',true);
		selectinput.eq(1).attr('id', 'cbo_color_0').val(data[5]);
		selectinput.eq(2).attr('id', 'cbo_uom_0' );
	}
}

function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/yarn_service_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function openmypage_booking()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	page_link='requires/yarn_service_work_order_controller.php?action=yern_service_wo_popup&company='+company;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Yarn Dyeing Booking Search', 'width=885px, height=450px, center=1, resize=0, scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_number=this.contentDoc.getElementById("hidden_sys_number").value.split("_");

		if(sys_number!="")
		{
			freeze_window(5);

			get_php_form_data(sys_number[1], "populate_master_from_data", "requires/yarn_service_work_order_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_service_type*cbo_pay_mode*cbo_currency', 1, "", "" );
			show_list_view(sys_number[0]+"_"+sys_number[2],'show_dtls_list_view','list_container','requires/yarn_service_work_order_controller','');
			set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
			release_freezing();
		}
	}
}

function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}
	else
	{
		page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}

function generate_trim_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_trim_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type*cbo_supplier_name*cbo_pay_mode',"../../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function generate_without_rate_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_without_rate_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type*cbo_supplier_name*cbo_pay_mode',"../../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_without_rate_report_reponse;
	}
}

function generate_without_rate_report_reponse()
{
	if(http.readyState == 4)
	{

		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function sales_order_report()
{
	var update_id =  $('#update_id').val();
	var is_sales = $('#cbo_is_sales_order').val();
	if(update_id == "" || is_sales == 2)
	{
		alert("This Report Is For Sales Order Only");
		return;
	}
	var show_rate_column = "";
	var r=confirm("Press \"OK\" to open with Rate column\nPress \"Cancel\" to open without Rate column");
	if (r==true)
	{
		show_rate_column="1";
	}
	else
	{
		show_rate_column="0";
	}

	var form_name="yarn_dyeing_wo_booking_without_order";
	var data="action=sales_order_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type',"../../../")+"&show_val_column="+show_rate_column;
	http.open("POST","requires/yarn_service_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = sales_order_report_reponse;
}

function sales_order_report_reponse()
{
	if(http.readyState == 4)
	{
		freeze_window(5);

		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
		release_freezing();
	}
}

function openmypage_charge()
{
	if (form_validation('cbo_company_name','Company Name*Job Number')==false)
	{
		return;
	}
	else
	{
		var company = $("#cbo_company_name").val();

		page_link='requires/yarn_service_work_order_controller.php?action=dyeing_search_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Charge', 'width=600px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_rate").value;

			freeze_window(5);
			document.getElementById('txt_rate').value=data;
			release_freezing();
			fnc_calculate();
		}
	}
}

function generate_multiple_job_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_with_multiple_job"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");

		http.open("POST","requires/yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_multiple_job_report_reponse;
	}
}

function generate_multiple_job_report_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function multiple_job_without_rate_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_with_multiple_job_without_rate"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = multiple_job_without_qty_report_reponse;
	}
}

function multiple_job_without_qty_report_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function fnResetForm()
{
	reset_form('yarn_service_work_order','list_container','','txt_booking_date,<? echo date("d-m-Y"); ?>','disable_enable_fields("txt_item_des*txt_lot*cbo_count",0)','cbo_uom');
	set_button_status(0, permission, 'fnc_yarn_service_wo',1,0);
}

function change_job_title(val)
{
	if(val == 1){
		$("#job_title").text("Sales Order No");
		$("#cbo_with_order").attr("disabled","disabled");
	}else{
		$("#job_title").text("Job No");
	}
}

function change_job_priority(val)
{
	if(val == 2){
		$("#job_title").css("color","blue").addClass("must_entry_caption").attr("title","Must Entry Field");
		$(".job_field").attr("disabled","disabled").removeAttr("placeholder");
	}else{
		$("#job_title").css("color","#444").removeClass("must_entry_caption").removeAttr("title").attr("disabled");
		$(".job_field").removeAttr("disabled").attr("placeholder","Doubole Click for Job");
	}
}

function set_fin_visibility(val)
{
	if(val == 15 || val == 50 || val == 51){
		$("#is_twisting").css("display","block");
	}else{
		$("#is_twisting").css("display","none");
	}
}
function fnc_variable_settings_check(company_id)
{
	var color_from_lib=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/yarn_service_work_order_controller');

	if(color_from_lib==1)
	{
		//$('#hidd_color_from_lib').val( color_from_lib );
		$('#txt_fin_color').attr('readonly',true);
		$('#txt_fin_color').attr('placeholder','Browse');
		$('#txt_fin_color').removeAttr("onDblClick").attr("onDblClick","color_select_popup("+1+")");
	}
	else
	{
		//$('#hidd_color_from_lib').val( 2 );
		$('#txt_fin_color').attr('readonly',false);
		$('#txt_fin_color').attr('placeholder','Write');
		$('#txt_fin_color').removeAttr('onDblClick','onDblClick');
	}
}
function color_select_popup(id)
{

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_service_work_order_controller.php?action=color_popup', 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#txt_fin_color').val(color_name.value);
		}
	}
}

</script>

</head>
<body onLoad="set_hotkey(); check_exchange_rate();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
		<form name="yarn_service_work_order"  autocomplete="off" id="yarn_service_work_order">
			<fieldset style="width:800px; margin-bottom:5px;">
				<legend>Yarn Service Work Order</legend>
				<table cellspacing="4" cellpadding="8" border="0">
					<tr>
						<td colspan="6" align="center" height="30" valign="top"> Wo No
							<input class="text_boxes" type="text" style="width:190px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Work Order" name="txt_booking_no" id="txt_booking_no" />
						</td>
					</tr>

					<tr>
						<td align="right" class="must_entry_caption" width="80">Company</td>
						<td>
							<?
							echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected,"get_php_form_data('94'+'_'+this.value, 'populate_field_level_access_data', 'requires/yarn_service_work_order_controller' );fnc_variable_settings_check(this.value);",0);
							?>
						</td>
						<td  align="right" class="must_entry_caption" width="80">Service Type</td>
						<td>
							<?
							echo create_drop_down( "cbo_service_type", 160, $yarn_issue_purpose,"", 1, "-- Select --", $selected, "set_fin_visibility(this.value);",0,'12,15,38,46,7,50,51');
							?>
						</td>
						<td  align="right" class="must_entry_caption">Pay Mode</td>
						<td ><?
						echo create_drop_down( "cbo_pay_mode", 160, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/yarn_service_work_order_controller',$('#cbo_company_name').val()+'_'+$('#cbo_service_type').val()+'_'+this.value, 'load_drop_down_inhouse_company', 'supplier_td' )","" );
						?></td>


					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Booking Date</td>
						<td><input class="datepicker" type="text" style="width:150px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" /></td>
						<td align="right">Attention</td>
						<td ><input class="text_boxes" type="text" style="width:150px;"  name="txt_attention" id="txt_attention"/></td>
						<td align="right">Currency</td>
						<td><?
						echo create_drop_down( "cbo_currency", 160, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 );
						?></td>

					</tr>
					<tr>
						<td align="right">ExchangeRate</td>
						<td><input style="width:150px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly /></td>
						<td  align="right" class="must_entry_caption" width="90">Factory</td>
						<td id="supplier_td">
							<?
							echo create_drop_down( "cbo_supplier_name", 160, $blank_array,"", 1, "-- Select Supplier --", $selected, "",0 );
							?>
						</td>


						<td  align="right">Source</td>
						<td ><?
						echo create_drop_down( "cbo_source", 160, $source,"", 1, "-- Select --", 3, "",0 );
						?></td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Delivery Date</td>
						<td align="left"><input class="datepicker" type="text" style="width:150px;" name="txt_delivery_date" id="txt_delivery_date"/></td>
						<td align="right">Sales Order</td>
						<td align="left">
							<? echo create_drop_down( "cbo_is_sales_order", 160, $yes_no,"", 0, "-- Select --", 2, "change_job_title(this.value);",0 ); ?>
						</td>
						<td colspan="2" align="center">
							<?
							include("../../../terms_condition/terms_condition.php");
							terms_condition(335,'txt_booking_no','../../../');
							?>
						</td>
					</tr>
					<tr>
						<td align="right">With Order</td>
						<td align="left">
							<? echo create_drop_down( "cbo_with_order", 160, $yes_no,"", 0, "-- Select --", 1, "change_job_priority(this.value);",1 ); ?>
						</td>
						<td align="right">Ref No</td>
						<td align="left">
							<input class="text_boxes" type="text" style="width:150px;"  name="txt_ref_no" id="txt_ref_no"/>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset style="width:1000px;">
				<legend>Yarn Service Work Order Details</legend>
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
					<thead id="dtls_container_head">
						<tr>
							<th id="job_title">Job No</th>
							<th class="must_entry_caption">Lot No</th>
							<th>Count</th>
							<th>Color</th>
							<th>Yarn Description</th>
							<th>UOM</th>
							<th class="must_entry_caption">WO Qnty</th>
							<th class="must_entry_caption">Rate</th>
							<th>Amount</th>
							<th>No of Bag</th>
							<th>No of Cone</th>
							<th>Min Req. Cone</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<tbody id="dtls_container">
						<tr>
							<td>
								<input type="text" id="txt_job_no_0" name="txt_job_no_0" placeholder="Doubole Click for Job" readonly style="width:100px;" class="text_boxes job_field" onDblClick="openmypage_job(this,'Job Search');"  />
								<input type="hidden" id="txt_job_id_0" name="txt_job_id_0" />
							</td>
							<td>
								<input type="text" id="txt_lot_0" name="txt_lot_0" style="width:70px;" class="text_boxes" placeholder="Browse" onDblClick="openmypage_lot()" readonly />
								<input type="hidden" id="txt_pro_id_0" name="txt_pro_id_0" />
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_count_0", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1","id,yarn_count", 1, "-select-", $selected,"","0" );
								?>

							</td>
							<td id="color_td_id">
                                <?
									if($db_type==0) $color_cond=" and color_name!=''"; else $color_cond=" and color_name IS NOT NULL";
                                    echo create_drop_down( "cbo_color_0", 110, "select id,color_name from lib_color where status_active=1 $color_cond order by color_name","id,color_name", 1, "--Select--", 0, "",1 );
                                ?>
                            </td>
							<td>
								<input type="text" id="txt_item_des_0" name="txt_item_des_0" style="width:150px;" class="text_boxes"   />
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_uom_0", 50, $unit_of_measurement,"", 1, "-- UOM--",15,"",1 );
								?>
							</td>
							<td>
								<input type="text" id="txt_wo_qty_0" name="txt_wo_qty_0" style="width:55px;" class="text_boxes_numeric" onKeyUp="fnc_calculate(this,1)" placeholder="" />
							</td>
							<td>
								<input type="text" id="txt_rate_0" name="txt_rate_0" style="width:55px;" class="text_boxes_numeric dc_rate"  onKeyUp="fnc_calculate(this,1)" />
							</td>
							<td>
								<input type="text" id="txt_amount_0" name="txt_amount_0" style="width:65px;" class="text_boxes_numeric" readonly />
							</td>
							<td>
								<input type="text" id="txt_bag_0" name="txt_bag_0" style="width:40px;" class="text_boxes_numeric"   />
							</td>
							<td>
								<input type="text" id="txt_cone_0" name="txt_cone_0" style="width:40px;" class="text_boxes_numeric"   />
							</td>
							<td>
								<input type="text" id="txt_min_req_cone_0" name="txt_min_req_cone_0" style="width:40px;" class="text_boxes_numeric"   />
							</td>
							<td>
								<input type="text" id="txt_remarks_0" name="txt_remarks_0" style="width:100px;;" class="text_boxes"   />
								<input type="hidden" id="dtls_update_id_0">
							</td>
						</tr>
					</tbody>
				</table>
				<fieldset style="width:50%; margin:5px auto;display:none;" id="is_twisting">
					<legend>Finish Product Details</legend>
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" style="width:100%;">
						<tr>
							<th class="must_entry_caption">Count</th>
							<th class="must_entry_caption">Composition</th>
							<th class="must_entry_caption">%</th>
							<th class="must_entry_caption">Type</th>
							<th class="must_entry_caption">Color</th>
						</tr>
						<tr>
							<td>
								<? echo create_drop_down( "cbo_fin_count", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1","id,yarn_count", 1, "-select-", $selected,"","0" );?>
							</td>
							<td>
								<? echo create_drop_down( "cbo_fin_composition", 150, "select id, composition_name from  lib_composition_array where  status_active=1","id,composition_name", 1, "-select-", $selected,"","0" );?>
							</td>
							<td>
								<input type="text" name="txt_fin_perc" id="txt_fin_perc" class="text_boxes_numeric" style="width:45px" value="100" />
							</td>
							<td>
								<? echo create_drop_down( "cbo_fin_type", 100, $yarn_type, 1, "-select-", $selected,"","0" );?>
							</td>
							<td>
								<input type="text" id="txt_fin_color" name="txt_fin_color" placeholder="Write" style="width:100px;" class="text_boxes" />
								<input type="hidden" id="hdn_fin_update_id" name="hdn_fin_update_id" />
							</td>
						</tr>
					</table>
				</fieldset>
				<table width="100%">
					<tr>
						<td align="center" class="button_container">
							<? echo load_submit_buttons( $permission, "fnc_yarn_service_wo", 0,0 ,"fnResetForm()",1) ; ?>
							<input type="hidden" id="update_id" >
							<input type="hidden" id="update_dtls_ids" >

							<div id="pdf_file_name"></div>
							<input type="button" value="Print With Rate" onClick="generate_trim_report()"  style="width:160px" name="print_booking" id="print_booking" class="formbutton" />
							<input type="button" value="Print Without Rate" onClick="generate_without_rate_report()"  style="width:160px" name="print_booking2" id="print_booking2" class="formbutton" />
							<input type="button" value="Multiple Sample With Rate" onClick="generate_multiple_job_report()"  style="width:160px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
							<input type="button" value="Multiple Sample Without Rate" onClick="multiple_job_without_rate_report()"  style="width:170px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
							<input type="button" value="Print Report Sales" onClick="sales_order_report()"  style="width:170px; display:none;" name="print_booking5" id="print_booking5" class="formbutton" />

						</td>
					</tr>
				</table>
			</fieldset>

		</form>
		<br>
		<div id="list_container"></div>
	</div>
	<div style="display:none" id="data_panel"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>