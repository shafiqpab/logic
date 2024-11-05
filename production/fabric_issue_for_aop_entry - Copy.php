<?
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :
JS Functions	 :
Created by		 : Ashraful
Creation date 	 : 27-02-2015
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../", 1, 1,$unicode,'','');
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

var permission='<? echo $permission; ?>';
function openmypage_order(page_link,title)
{

	page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_supplier_name*cbo_booking_month*cbo_booking_year','../');
	//page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var id=this.contentDoc.getElementById("po_number_id");
		var po=this.contentDoc.getElementById("po_number");
		if (id.value!="")
		{
			reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_issue_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*txt_booking_no','txt_issue_date,<? echo date("d-m-Y"); ?>');
			freeze_window(5);
			document.getElementById('txt_order_no_id').value=id.value;
			document.getElementById('txt_order_no').value=po.value;
			get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/fabric_issue_for_aop_entry_controller" );
			set_button_status(0, permission, 'fnc_trims_booking',1);
			release_freezing();
		}
	}
}

function set_process(fabric_desription_id,type)
{
	$("#booking_list_view").text('');
	fabric_desription_id=$("#cbo_fabric_description").val();

	if(type=='set_process')
	{
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	if(type=="colorsizesensitive")
	{

	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	$("#hide_fabric_description").val(fabric_desription_id);
}

function fnc_fabric_description_id(color_id, button_status, type)
{
	var hide_color_id='';
	if(type==1)
	{
		hide_color_id=document.getElementById('hide_fabric_description').value;
		//document.getElementById('copy_val').checked=true;
	}
	else
	{
		hide_color_id=parseInt(document.getElementById('hide_fabric_description').value);
		//document.getElementById('copy_val').checked=false;
	}

	if(color_id==hide_color_id)
	{
		document.getElementById('hide_fabric_description').value='';
		set_button_status(0, permission, 'fnc_trims_booking',1);
	}
	else
	{
		document.getElementById('hide_fabric_description').value=color_id;
		set_button_status(button_status, permission, 'fnc_trims_booking',1);
	}
}
function setmaster_value(process, sensitivity)
{
	document.getElementById('cbo_process').value=process;
	document.getElementById('cbo_colorsizesensitive').value=sensitivity;
}

function calculate_amount(i)
{
	var is_short = $('#cbo_is_short').val();
	var txt_woqnty=(document.getElementById('txtWoqnty_'+i).value)*1;
	//alert(is_short);
	if(is_short==2)
	{
		var balance = (document.getElementById('txtWoqnty_'+i).placeholder)*1;
		//alert(balance);
		if(txt_woqnty>balance){
			alert("No Balance Quantity"); 
			document.getElementById('txtAmount_'+i).value='';
			document.getElementById('txtWoqnty_'+i).value='';
			calculate_total_amount();
			return;
		}
	}
	var txt_rate=(document.getElementById('txtRate_'+i).value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txtAmount_'+i).value=txt_amount.toFixed(4);
	calculate_total_amount();
}

function calculate_total_amount()
{
	//alert(1);
	var total_qty=0; var total_amount=0; 
	$('table#tbl_dtls_emb tbody tr').each(function()
	{
		var txtWoqnty 		= $(this).find('input[name="txtWoqnty[]"]').val()*1;
		var txtAmount 		= $(this).find('input[name="txtAmount[]"]').val()*1;
		total_qty += txtWoqnty;
		total_amount += txtAmount;
		//alert(2);
	});
	//alert(total_qty);
	document.getElementById('total_qty').value=total_qty;
	document.getElementById('total_amount').value=total_amount.toFixed(4);

}

function copy_value(i,type)
{
	var copy_val=document.getElementById('copy_val').checked;
	var rowCount=$('#tbl_table tbody tr').length;
	if(copy_val==true)
	{
		for(var j=i; j<=rowCount; j++)
		{
			if(type=='txt_rate')
			{
				var txt_woqnty=(document.getElementById('txt_woqnty_'+j).value)*1;
				var txt_rate=(document.getElementById('txt_rate_'+j).value)*1;
				var txt_amount=txt_woqnty*txt_rate;
				document.getElementById('txt_rate_'+j).value=txt_rate;
				document.getElementById('txt_amount_'+j).value=txt_amount;
			}

			if(type=='txt_woqnty')
			{
				var txt_woqnty=(document.getElementById('txt_woqnty_'+j).value)*1;
				var txt_rate=(document.getElementById('txt_rate_'+j).value)*1;
				var txt_amount=txt_woqnty*txt_rate;
				document.getElementById('txt_woqnty_'+j).value=txt_woqnty;
				document.getElementById('txt_amount_'+j).value=txt_amount;
			}
			if(type=='uom')
			{
				var uom=(document.getElementById('uom_'+ii).value)*1;
				document.getElementById('uom_'+j).value=uom;
			}
		}
	}
}

function fnc_generate_booking()
{
	if (form_validation('txt_order_no_id','Order No*Fabric Nature*Fabric Source')==false)
	{
		return;
	}
	else
	{
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_order_no_id',"../");
		http.open("POST","requires/fabric_issue_for_aop_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	}
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4)
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	}
}

function open_consumption_popup(page_link,title,po_id,i)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var po_id =document.getElementById(po_id).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}

	else
	{
		var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&cbo_supplier_name='+cbo_supplier_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("cons_sum");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
		}
	}
}

function openmypage_booking(page_link,title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	else
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var page_link=page_link+'&cbo_company_id='+cbo_company_id+'&cbo_supplier_name='+cbo_supplier_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail_booking=this.contentDoc.getElementById("selected_booking");
			var theemail_job=this.contentDoc.getElementById("selected_job");
			var theemail_within_group=this.contentDoc.getElementById("within_group");
			//alert(theemail_within_group.value);
			document.getElementById('within_group').value=theemail_within_group.value;

			if (theemail_booking.value!="")
			{
				//alert(theemail.value);
			 	get_php_form_data( theemail_booking.value, "populate_data_from_search_popup", "requires/fabric_issue_for_aop_entry_controller" );
				//alert("mmm");
		   		//set_button_status(1, permission, 'fnc_trims_booking',1);
			    //show_list_view(document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/fabric_issue_for_aop_entry_controller','');

			    //get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/fabric_issue_for_aop_entry_controller' );
			}
		}
	}
}

function openmypage_issue(page_link,title)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
	var page_link=page_link+'&cbo_company_id='+cbo_company_id+'&cbo_supplier_name='+cbo_supplier_name;
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail_booking=this.contentDoc.getElementById("selected_booking");
		var theemail_job=this.contentDoc.getElementById("selected_job");

		if (theemail_job.value!="")
		{
			//alert(theemail.value);
			//reset_form('servicebooking_1','booking_list_view','','txt_issue_date,<? echo date("d-m-Y"); ?>');
		 	get_php_form_data( theemail_job.value, "populate_issue_data_from_search_popup", "requires/fabric_issue_for_aop_entry_controller" );
			//alert("mmm");
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		   
		    $('#booking_list_view').text('');
			var update_id = $("#update_id").val();
			var data='2'+'**'+update_id;
			show_list_view(data, 'generate_aop_booking','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
			var numRow = $('table#tbl_dtls_emb tbody tr').length;
			if(numRow>0){
				calculate_total_amount();
				set_button_status(1, permission, 'fnc_service_booking_dtls',2);
			}
			else{
				set_button_status(0, permission, 'fnc_service_booking_dtls',2);
			}
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
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}



function fnc_trims_booking( operation ){
	//freeze_window(operation);
	var data_all="";
	//alert(111);
	/*if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}*/

	/*if(operation==2){
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}*/

	/*var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}*/

	if (form_validation('cbo_company_name*txt_order_no*cbo_supplier_name','Company Name*Work Order*Service Company')==false){
		release_freezing();
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_name*txt_issue_date*cbo_issue_purpose*cbo_source*cbo_supplier_name*txt_party_location*txt_order_no*hid_order_id*txt_gate_pass_no*cbo_is_short*txt_remarks*update_id*txt_attention*txt_delivery_date*txt_vehical_no*txt_driver_name*txt_dl_no*txt_transport*txt_cell_no*buyer_id*within_group*cbo_lc_company_name*hid_fab_booking',"../");
	}
	
	http.open("POST","requires/fabric_issue_for_aop_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}

function fnc_trims_booking_reponse()
{
	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 
		 if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Aop Order Entry. Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}

		if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(trim(reponse[0]));
			document.getElementById('txt_system_id').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_trims_booking',1);
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_supplier_name").attr("disabled",true);
			//$("#cbo_supplier_name").attr("disabled",true);
			$("#cbo_level").attr("disabled",true);
			$("#cbo_is_short").attr("disabled",true);
			$("#txt_order_no").attr("disabled",true);
			//$("#cbo_buyer_name").attr("disabled",true);
			//$("#cbo_level").attr("disabled",true);
			$("#cbo_is_short").attr("disabled",true);
		}
		release_freezing();
	}
}

function fnc_service_booking_dtls( operation )
{
	//freeze_window(operation);
	if (form_validation('update_id','Save First')==false)
	{
		alert('Please Save Master Part First');
		release_freezing();
		return;
	}
	/*if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}*/
	/*	if(operation==2){
	=======
	/*	if(operation==2){
	>>>>>>> 3a9cc0cc133490344742c8a455a1668dec128a34
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}*/

	/*var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}*/
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "issue_print", "requires/fabric_issue_for_aop_entry_controller") 
		//return;
		show_msg("3");
	}
	else
	{
		var txt_order_no 			= $('#txt_order_no').val();
		var hid_order_id 			= $('#hid_order_id').val();
		var update_id 				= $('#update_id').val();
		var txtDeletedId 			= $('#txtDeletedId').val();
		var cbo_lc_company_name 	= $('#cbo_lc_company_name').val();

		var j=0; var i=0; var check_field=0; data_all="";
		var numRow = $('table#tbl_dtls_emb tbody tr').length;
		//alert(numRow); 		
		$('table#tbl_dtls_emb tbody tr').each(function()
		{
			var cboProcessType 	= $(this).find('select[name="cboProcessType[]"]').val();
			var txtWoqnty 		= $(this).find('input[name="txtWoqnty[]"]').val();
			var txtRate 		= $(this).find('input[name="txtRate[]"]').val();
			var txtAmount 		= $(this).find('input[name="txtAmount[]"]').val();
			var txtNumberRoll 	= $(this).find('input[name="txtNumberRoll[]"]').val();
			var txtRemarks 		= $(this).find('input[name="txtRemarks[]"]').val();
			var soDtlsId 		= $(this).find('input[name="soDtlsId[]"]').val();
			var batchDtlsId 	= $(this).find('input[name="batchDtlsId[]"]').val();
			var batchId 		= $(this).find('input[name="batchId[]"]').val();
			var batchDtlsId 		= $(this).find('input[name="batchDtlsId[]"]').val();
			var hiddenid 		= $(this).find('input[name="hiddenid[]"]').val();
			
			if(txtWoqnty==0 || txtWoqnty=='')
			{	 
				alert('Please Fill up Issue Qty ');	
				check_field=1 ; return;			
			}
			
			if(check_field==0)
			{
				//alert(check_field);
				j++;
				data_all += "&cboProcessType_" + j + "='" + cboProcessType + "'&txtWoqnty_" + j + "='" + txtWoqnty + "'&txtRate_" + j + "='" + txtRate + "'&txtAmount_" + j + "='" + txtAmount + "'&txtNumberRoll_" + j + "='" + txtNumberRoll  + "'&txtRemarks_" + j + "='" + txtRemarks + "'&soDtlsId_" + j + "='" + soDtlsId+ "'&batchDtlsId_" + j + "='" + batchDtlsId+ "'&batchId_" + j + "='" + batchId + "'&hiddenid_" + j + "='" + hiddenid + "'";

				i++;
				//alert(data_all);
			}
		});
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+i+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&update_id='+update_id+'&txtDeletedId='+txtDeletedId+'&cbo_lc_company_name='+cbo_lc_company_name+data_all;

		//alert(data);
		http.open("POST","requires/fabric_issue_for_aop_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_service_booking_dtls_reponse;
	}
}

function fnc_service_booking_dtls_reponse()
{
	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 
		 /*if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Aop Order Entry. Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}*/
		
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
		show_msg(trim(reponse[0]));
			$('#booking_list_view').text('');
			var update_id = $("#update_id").val();
			var data='2'+'**'+update_id;
			show_list_view(data, 'generate_aop_booking','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
		//show_list_view(document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/fabric_issue_for_aop_entry_controller','');
			set_button_status(1, permission, 'fnc_service_booking_dtls',2);
		}
		if(trim(reponse[0])==10){
			release_freezing();
			return;
		}
		release_freezing();
	}
}

function generate_trim_report(action)
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true) show_comment="1";
		else show_comment="0";
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name',"../")+'&show_comment='+show_comment+'&path=../';
		http.open("POST","requires/fabric_issue_for_aop_entry_controller.php",true);
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
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_issue_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/fabric_issue_for_aop_entry_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);

}
function fnc_file_upload(i)
{
		
		var update_id = $("#updateid_"+i).val();
		var gmts_color_id = $("#gmts_color_id_"+i).val();
		var dia_name = $("#dia_"+i).val();
		var po_id = $("#po_id_"+i).val();
		var pre_conv_id = $("#fabric_description_id_"+i).val();
			//alert(issue_id);
			file_uploader ( '../', update_id,'', 'aop_v2', 0,1);
		
	
}
	
function fnc_fab_booking(page_link,title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var company=$("#cbo_company_name").val()*1;
	//alert(company);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			//reset_form('fabricbooking_1','booking_list_view','','txt_issue_date,<? //echo date("d-m-Y"); ?>');
		//	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_issue_for_aop_entry_controller" );
			//check_month_setting();
			//var is_approved_id=$('#id_approved_id').val();
			//alert(is_approved_id);
		
			//$('#cbo_company_name').attr('disabled','true');
			//set_button_status(1, permission, 'fnc_fabric_booking',1);
			$("#txt_fab_booking").val(theemail.value);
		
			
		}
	}
}

function fnc_file_upload(i)
{
	var update_id = $("#updateid_"+i).val();
	var gmts_color_id = $("#gmts_color_id_"+i).val();
	var dia_name = $("#dia_"+i).val();
	var po_id = $("#po_id_"+i).val();
	var pre_conv_id = $("#fabric_description_id_"+i).val();
		//alert(issue_id);
		file_uploader ( '../', update_id,'', 'aop_v2', 0,1);
}
	
	function fnc_fab_booking(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				//reset_form('fabricbooking_1','booking_list_view','','txt_issue_date,<? //echo date("d-m-Y"); ?>');
			//	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_issue_for_aop_entry_controller" );
				//check_month_setting();
				//var is_approved_id=$('#id_approved_id').val();
				//alert(is_approved_id);
			
				//$('#cbo_company_name').attr('disabled','true');
				//set_button_status(1, permission, 'fnc_fabric_booking',1);
				$("#txt_fab_booking").val(theemail.value);
			}
		}
	}

</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
</head>
<body onLoad="set_hotkey();check_exchange_rate();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",$permission);  ?>
        <!--<h3 style="width:950px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>-->
    <!--<div id="content_search_panel" >-->
        <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            <fieldset style="width:950px;">
                <legend>Fabric Issue For AOP</legend>
                <table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align=""></td>
                        <td align=""></td>
                        <td  width="130" height="" align="right" class="must_entry_caption">Issue ID</td>              <!-- 11-00030  -->
                        <td  width="170" >
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_issue('requires/fabric_issue_for_aop_entry_controller.php?action=issue_popup','Issue Search')" readonly placeholder="Double Click" name="txt_system_id" id="txt_system_id"/>
                        </td>
                        <td align=""></td>
                        <td align=""></td>
                    </tr>
                    <tr>
                        <td  align="right" class="must_entry_caption">Company Name</td>
                        <td><? 
                        //load_drop_down( 'requires/fabric_issue_for_aop_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                        echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "","","" ); ?></td>
                        <td width="130" align="right">Issue Date</td>
                        <td width="170"><input class="datepicker" type="text" style="width:160px" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" /></td>
                        <td width="130" align="right">Issue Purpose</td>              <!-- 11-00030  -->
                        <td width="170" ><? echo create_drop_down( "cbo_issue_purpose", 172, $yarn_issue_purpose,"", 1, "-- Select Source --", "52", "","",52); ?></td>
                    </tr>
                    <tr>
                    	<td width="130" align="right"> Source </td>              <!-- 11-00030  -->
                        <td width="170" ><? echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=25 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",1,"" ); ?></td>
                       
                        <td align="right">Service Company Address</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_party_location" id="txt_party_location" disabled="disabled" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption" align="right"><strong>Work Order</strong></td>
                        <td>
                    		<input class="text_boxes" type="text" style="width:160px" name="txt_order_no" id="txt_order_no" onDblClick="openmypage_booking('requires/fabric_issue_for_aop_entry_controller.php?action=service_booking_popup','WO Search')" readonly placeholder="Double Click" />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                            <input type="hidden" name="buyer_id" id="buyer_id">
                            <input type="hidden" name="within_group" id="within_group">
                            <input type="hidden" name="hid_booking_type" id="hid_booking_type">
                            <input type="hidden" name="cbo_lc_company_name" id="cbo_lc_company_name">
                            <input type="hidden" name="hid_fab_booking" id="hid_fab_booking">
                        </td>
                        <td align="right">Attention</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_attention" id="txt_attention"/></td>
                        <td width="130" align="right">Delivery Date</td>
                        <td width="170"><input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    </tr>
                    <tr>
                    	<td align="right">Vehical No</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_vehical_no" id="txt_vehical_no"/></td>
                        <td align="right">Driver Name</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_driver_name" id="txt_driver_name"/></td>
                        <td align="right">DL No</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_dl_no" id="txt_dl_no"/></td>
                    </tr>
                    <tr>
                    	<td align="right">Transport</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_transport" id="txt_transport"/></td>
                        <td align="right">Mobile No</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_cell_no" id="txt_cell_no"/></td>
                        <td align="right">Gate Pass No</td>
                        <td><input class="text_boxes" type="text" style="width:160px"  name="txt_gate_pass_no" id="txt_gate_pass_no"/></td>
                    </tr>
                    <tr>
                        <td  align="right">Is Short</td>
                        <td><? echo create_drop_down( "cbo_is_short", 172, $yes_no,'', 0, '',2,"");?></td>
                        <td align="right">Remark</td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:97%;"  name="txt_remarks" id="txt_remarks"/></td>
                    </tr>
                    <tr>
                    	<td width="100%" colspan="6">
	                    	<table width="100%" border="0">
	                    		<tr>
	                    			<td class="button_container" width="200"><div id="approved" style="float:left; font-size:20px; color:#FF0000;"></div></td>
			                        <td width="400" align="left"valign="middle" class="button_container">
			                        <?
			                        $endis = "disable_enable_fields( 'cbo_company_name*cbo_supplier_name', 0 )";
			                        echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','booking_list_view*data_panel*pdf_file_name','','txt_issue_date,".$date."',$endis,'cbo_source*txt_attention*txt_delivery_date*cbo_company_name*cbo_supplier_name')",1) ;
			                        
			                        ?>
			                        </td>
	                    		</tr>
	                    	</table>
                    	</td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <!--</div>-->
        <br/>
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">
            <fieldset style="width:950px;">
                <legend title="V3">Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data()" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
                <!--<b>Copy</b> :--><input type="checkbox" id="copy_val" name="copy_val" style="display:none" checked/>
                <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_order_no_id" id="txt_order_no_id"/>
                <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="cbo_fabric_description" id="cbo_fabric_description"/></legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<thead>
			                <th width="30">SL</th>
			                <th width="150">Buyer Name</th>
			                <th width="100">Style Ref. No</th>
			                <th width="100">FSO No.</th>
			                <th width="100">Fabric Booking No.</th>
			                <th width="100">Batch No</th>
			                <th width="70">Fab Color</th>
			                <th width="80">Bodypart</th>
			                <th width="200">Fabric Description</th>
			                <th width="70">GSM</th>
			                <th width="70">DIA</th>
			                <th width="120">Process Type</th>
			                <th width="70">Batch Wgt./WO Qnty</th>
			                <th width="70">Issue Qty.</th>
			                <th width="70">Rate</th>
			                <th width="70">Amount</th>
			                <th width="70">No. Of Roll</th>
			                <th width="70">Remarks</th>
			                <th>Remove</th>
			            </thead>
                    <tbody id="booking_list_view">
                    </tbody>
                    <tfoot>
				    	<th colspan="13" align="right">Total</td>
				    	<th align="right" ><input type="text" name="total_qty" id="total_qty" class="text_boxes_numeric" style="width:60px" readonly /></td>
				    	<th align="right">&nbsp;</td>
				    	<th align="right" ><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:60px" readonly /></td>
				    	<th align="right">&nbsp;</td>
				    	<th align="right">&nbsp;</td>
				    	<th align="right">&nbsp;
				    	<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
						<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" /></td>
				    </tfoot>
                </table>
                <table width="1480" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="14" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,1 ,"reset_form('','booking_list_view*data_panel','','','','')",2) ; ?>
                        </td>
                    </tr>   
                </table>
                
            </fieldset>
        </form>
        <div id="booking_list_view_list"></div>
        <div id="data_panel"></div>
        <div id="data_panel2" style="display:none"></div>
    </div>
    <input type="button" id="myBtn" value="OPen" style="display:none"/>
<div id="myModal" class="modal">

  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>

  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
</body>
<script>
function fnc_process_data(){
	//if (form_validation('cbo_company_name*txt_system_id*txt_order_no','Company*Issue ID*Work Order')==false){
	if (form_validation('cbo_company_name*txt_system_id*txt_order_no','Company*Issue ID*Work Order')==false){
		return;
	}
	else{
		//alert(11);
		//var garments_nature=document.getElementById('garments_nature').value;
		//var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		//var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_lc_company_name').value;

		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;

		var txt_order_no=document.getElementById('txt_order_no').value;
		var hid_order_id=document.getElementById('hid_order_id').value;
		//var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_is_short=document.getElementById('cbo_is_short').value;
		var buyer_id=document.getElementById('buyer_id').value;
		var within_group=document.getElementById('within_group').value;
		
	    var page_link='requires/fabric_issue_for_aop_entry_controller.php?action=fabric_search_popup';
		var title='AOP Search';
		//alert(page_link); return;
		//page_link=page_link+'&garments_nature='+garments_nature+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_currency='+cbo_currency+'&cbo_is_short='+cbo_is_short;
		page_link=page_link+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&cbo_is_short='+cbo_is_short+'&cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+cbo_supplier_name+'&buyer_id='+buyer_id+'&within_group='+within_group;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1460px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txtSoDtlsId");
			var theemail2=this.contentDoc.getElementById("txtPreCostDtlsId");
			var theemail3=this.contentDoc.getElementById("txtBatchDtlsId");
			//alert(theemail.value);
			if (theemail.value!=""){
				//document.getElementById('txt_select_item').value=theemail.value;
				//document.getElementById('txt_order_no_id').value=theemail3.value;
				//document.getElementById('cbo_fabric_description').value=theemail2.value;
				var txtSoDtlsId=theemail.value;
				var txtPreCostDtlsId=theemail2.value;

				var txtBatchMstId=theemail3.value;
				var data='1'+'**'+txtSoDtlsId+'**'+txtPreCostDtlsId+'**'+txtBatchMstId+'**'+txt_order_no;
				//alert(txt_order_no);
				show_list_view(data, 'generate_aop_booking','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
				set_button_status(0, permission, 'fnc_service_booking_dtls',2);
			}
		}
	}
}

function fn_deletebreak_down_tr(rowNo,table_id,tr_id) 
{ 
	var numRow = $('#'+table_id+' tbody tr').length; 
	var prefix=tr_id.substr(0, tr_id.length-1);
	var total_row=$('#'+prefix+'_tot_row').val();
	
	//var numRow = $('table#rpt_table_aop tbody tr').length; 

	if(numRow!=1)
	{
		var updateIdDtls=$('#hiddenid_'+rowNo).val();
		var txt_deleted_id=$('#txtDeletedId').val();
		var selected_id='';
		if(updateIdDtls!='')
		{
			if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
			$('#txtDeletedId').val( selected_id );
		}
		
		$("#"+tr_id+rowNo).remove();
		$('#'+prefix+'_tot_row').val(total_row-1);

		set_all_onclick();
		//sum_total_qnty(numRow);
		calculate_total_amount();
	}
}

function set_data(po_id,fabric_cost_id,precost_conver_id,booking_id){
	    document.getElementById('txt_select_item').value=precost_conver_id;
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=fabric_cost_id;
		var cbo_is_short=document.getElementById('cbo_is_short').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		//alert(cbo_is_short);
		show_list_view(fabric_cost_id+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_level').value+"**"+precost_conver_id+"**"+cbo_is_short+"**"+cbo_currency, 'show_aop_booking','booking_list_view','requires/fabric_issue_for_aop_entry_controller','$(\'#hide_fabric_description\').val(\'\')');
		set_button_status(1, permission, 'fnc_service_booking_dtls',2);
}

function deletedata(po_id,fabric_cost_id,precost_conver_id,booking_id){
	var operation=2;
	freeze_window(operation);
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		alert('Please  Save Master Part First');
		release_freezing();
		return;
	}


	var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}

	var row_num=1;
	var i=1;
	var data_all=get_submitted_data_string('txt_booking_no*cbo_is_short*cbo_pay_mode',"../");
        data_all+="&txtpoid_1="+po_id+"&txtpre_cost_fabric_cost_dtls_id_1="+fabric_cost_id+"&updateid_1="+booking_id+"&fabric_description_id_1="+fabric_cost_id;

	var cbo_level=document.getElementById('cbo_level').value;
	if(cbo_level==1){

	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&row_num='+row_num+'&delete_cause='+delete_cause;
	}
	if(cbo_level==2){
	var data="action=save_update_delete_dtls_job_level&operation="+operation+data_all+'&row_num='+row_num+'&delete_cause='+delete_cause;
	}

	http.open("POST","requires/fabric_issue_for_aop_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;

}

//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata(data){

	document.getElementById('ccc').innerHTML=data;
	document.getElementById('myBtn').click();
}
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>