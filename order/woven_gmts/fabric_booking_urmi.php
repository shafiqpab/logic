﻿<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :	Monzu
Creation date 	         : 	27-12-2012
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
							Date 08-08-15, Merchandizing > Main Fabric booking > Fabric booking booking GR > Cuff - Color Size Breakdown in Pcs > Contrast color is not showing. Issue id=5749 update by jahid
-----------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';
<?

/*$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][86] );
echo "var field_level_data= ". $data_arr . ";\n";*/
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][118] );
echo "var field_level_data= ". $data_arr . ";\n";
?>

	/*var stri = '{ 0:{ "id": "John" , "val":"test"},1:{ "id": "John" , "val":"test"} }';
	var obj = jQuery.parseJSON( stri );
	alert( obj.id +"=="+obj.val );*/

function openmypage_booking(page_link,title)
{
	var company=$("#cbo_company_name").val()*1;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1400px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_booking_urmi_controller" );
			check_month_setting();
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			fnc_show_booking(1)
		}
	}
}
function openmypage_order(page_link,title)
{
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	var month_check=$('#month_id').val();
	//alert(month_check);
	if(month_check==1)
	{
		if (form_validation('cbo_booking_month*cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Year*Fabric Nature*Fabric Source')==false)
		{
			return;
		}
	}
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/fabric_booking_urmi_controller');
	//alert(check_is_booking_used_id);
	if(trim(check_is_booking_used_id) !="")
	{
		alert("This booking used in PI Table. So Adding or removing order is not allowed")
		return;
	}
	else
	{
		if(txt_booking_no=="")
		{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=420px,center=1,resize=1,scrolling=0','../')
		}
		else
		{
			var r=confirm("Existing Item against these Order  Will be Deleted")
			if(r==true)
			{
				var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/fabric_booking_urmi_controller');
				page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=470px,center=1,resize=1,scrolling=0','../')
			}
			else
			{
				return;
			}
		}
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			if (id.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/fabric_booking_urmi_controller" );
				check_month_setting();
				release_freezing();
				fnc_generate_booking();
			}
		}
	}
}

function fnc_generate_booking()
{
	if (form_validation('txt_order_no_id*cbo_fabric_natu*cbo_fabric_source','Order No*Fabric Nature*Fabric Source')==false)
	{
		return;
	}
	else
	{
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_job_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*txt_booking_percent*cbo_company_name*cbo_buyer_name*cbouom*txt_colar_excess_percent*txt_cuff_excess_percent',"../../");
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
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
		$('#cbo_fabric_natu').attr('disabled','disabled');
		$('#cbo_fabric_source').attr('disabled','disabled');
		$('#cbouom').attr('disabled','disabled');
		setFilterGrid("tbl_fabric_booking",-1);
		//copy_colarculfpercent(0);
	}
}

function auto_mail_send(){
		var data="action=booking_app_mail&operation="+get_submitted_data_string('txt_booking_no*cbo_ready_to_approved*txt_un_appv_request*cbo_company_name',"../../");
		http.open("POST","../../booking_approval_auto_mail.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=function(){
			if(http.readyState == 4)
			{
			//alert(data);
			}
		}

}



function fnc_fabric_booking( operation )
{
	freeze_window(operation);
	if(operation==2)
	{
		alert("Delete Restricted")
		release_freezing();
		return;
	}
	else if(operation==1)
	{
		auto_mail_send();
	}
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		release_freezing();
		return;
	}
	var month_set_id=$('#month_id').val();
	if(month_set_id==1)
	{
		if (form_validation('cbo_booking_month','Booking Month')==false)
		{
			release_freezing();
			return;
		}
	}
		var delivery_date=$('#txt_delivery_date').val();

		if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
		{
			alert("Delivery Date Not Allowed Less than Booking Date.");
			release_freezing();
			return;
		}
	//alert(month_set_id);
	if (form_validation('txt_order_no_id*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*cbo_buyer_name','Order No*Booking Date*Delivery Date*Pay Mode*Supplier Name*Buyer Name')==false)
	{
		release_freezing();
		return;
	}
	if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0)
	{
		alert("Select Supplier Name")
		release_freezing();
		return;
	}
	if(document.getElementById('full_booked').innerHTML=="Full Booked")
	{
		alert ("No Item Found");
		release_freezing();
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*txt_booking_percent*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_ready_to_approved*processloss_breck_down*txt_fabriccomposition*txt_intarnal_ref*txt_file_no*cbouom*txt_remark*update_id',"../../");
		//freeze_window(operation);
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_reponse;
	}
}

function fnc_fabric_booking_reponse()
{

	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
		 {
		   document.getElementById('txt_booking_no').value=reponse[1];
		    document.getElementById('update_id').value=reponse[2];
		   set_button_status(1, permission, 'fnc_fabric_booking',1);
		   show_msg(trim(reponse[0]));
		 }
		 if(trim(reponse[0])=='approved')
		 {
			 alert("This booking is approved");
			 release_freezing();
		     return;
		 }
		 if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		 }
		 if(trim(reponse[0])=='sal1'){
			alert("Sales Order Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		 }
		 if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='parb1'){
			alert("Partial Booking Found :"+trim(reponse[2])+"\n")
		    release_freezing();
		    return;
		}
		 //show_msg(trim(reponse[0]));
		 release_freezing();
	}
}

function fnc_fabric_booking_dtls( operation )
{
	freeze_window(operation);
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		release_freezing()
		return;

	}

	var row_num=$('#tbl_fabric_booking tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('txt_order_no_id*txt_booking_date*txt_booking_no','Order No*Booking Date*Booking No')==false)
		{
		release_freezing()
		return;
		}
		data_all=data_all+get_submitted_data_string('txt_booking_no*update_id*txt_order_no_id*txt_job_no*selected_id_for_delete*cbo_pay_mode*pobreakdownid_'+i+'*precostfabriccostdtlsid_'+i+'*cotaid_'+i+'*preconskg_'+i+'*colorid_'+i+'*finscons_'+i+'*greycons_'+i+'*rate_'+i+'*amount_'+i+'*colortype_'+i+'*construction_'+i+'*composition_'+i+'*gsmweight_'+i+'*diawidth_'+i+'*processlosspercent_'+i+'*colarculfpercent_'+i+'*updateid_'+i+'*gmtscolorid_'+i+'*remarks_'+i,"../../",i);
	}
	var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all;
	//freeze_window(operation);
	if(operation==2)
	{
		if(document.getElementById('selected_id_for_delete').value=="")
		{
			var r=confirm("All item will be deleted");
			if(r==true)
			{
				http.open("POST","requires/fabric_booking_urmi_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
			}
			else
			{
				release_freezing();
				return;
			}
		}

		if(document.getElementById('selected_id_for_delete').value!="")
		{
			var r=confirm("Selected item will be deleted");
			if(r==true)
			{
				http.open("POST","requires/fabric_booking_urmi_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
			}
			else
			{
				release_freezing();
				return;
			}
		}
	}
	else
	{
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}
}

function fnc_fabric_booking_dtls_reponse(){
	if(http.readyState == 4) {
		 var reponse=trim(http.responseText).split('**');
		 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1){
			 set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
			 release_freezing();
			 fnc_show_booking(1)
		 }
		 if(trim(reponse[0])=='approved'){
			 alert("This booking is approved");
			 release_freezing();
		     return;
		 }
		 if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		 }
		 if(trim(reponse[0])=='sal1'){
			alert("Sales Order Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		 }
		if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='parb1'){
			alert("Partial Booking Found :"+trim(reponse[2])+"\n")
		    release_freezing();
		    return;
		}
	}
}


var selected_id = new Array;
function select_id_for_delete_item(str)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/fabric_booking_urmi_controller');
	if(check_is_booking_used_id !="")
	{
		alert("This booking used in PI Table. So Delete  is not allowed")
		return;
	}
	else
	{

	if( jQuery.inArray( $('#updateid_' + str).val(), selected_id ) == -1 ) {
		//alert(str)
				selected_id.push( $('#updateid_' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#updateid_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}

			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#selected_id_for_delete').val( id );
	}
}
function fnc_show_booking(type)
{
	freeze_window(5);
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	var txt_job_no=document.getElementById('txt_job_no').value;
	if(type==2){
		var description=return_global_ajax_value(txt_booking_no+"_"+cbo_pay_mode+"_"+txt_job_no, 'check_SO_PI_APP', '', 'requires/fabric_booking_urmi_controller');
		var reponse=trim(description).split('**');
		if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='parb1'){
			alert("Partial Booking Found :"+trim(reponse[2])+"\n So Last Update  Not Possible")
		    release_freezing();
		    return;
		}

	}

	if(type==2 && document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		release_freezing();
		return;
	}

	if(type==2){
		document.getElementById('app_sms3').innerHTML = ''
	}

	if (form_validation('txt_booking_no','Booking No')==false){
		release_freezing();
		return;
	}
	else{
		var data="action=show_fabric_booking"+get_submitted_data_string('txt_job_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*txt_booking_no*txt_booking_percent*cbouom*txt_colar_excess_percent*txt_cuff_excess_percent',"../../")+"&type="+type;
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
}

function fnc_show_booking_reponse()
{
	if(http.readyState == 4){
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		$('#cbo_fabric_natu').attr('disabled','disabled');
		$('#cbo_fabric_source').attr('disabled','disabled');
		$('#cbouom').attr('disabled','disabled');
        setFilterGrid("tbl_fabric_booking",-1);
		set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
		selected_id=[];
		release_freezing();
	}
}

function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First");
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

function open_rmg_process_loss_popup(page_link,title)
{
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
	    page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!="")
			{
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}

}
function open_adjust_qty_popup(page_link,title)
{
	    var txt_booking_no=document.getElementById('txt_booking_no').value;
		 var txt_job_no=document.getElementById('txt_job_no').value;
		if(txt_booking_no==""){
			alert("Save The booking First");
			return;
		}
		var adjust_qty_breck_down=document.getElementById('adjust_qty_breck_down').value
	    page_link=page_link+'&txt_booking_no='+txt_booking_no+'&txt_job_no='+txt_job_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}

}


function open_size_wise_cuff_popup(page_link,title)
{
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
	    page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!="")
			{
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}
}


function open_size_wise_colur_popup(page_link,title)
{
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
	    page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!="")
			{
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}

}
function dtm_popup(page_link,title)
{
	var job_no=$('#txt_job_no').val();
	var booking_no=$('#txt_booking_no').val();
	var selected_no=$('#txt_order_no_id').val();

	if(booking_no=='')
	{
		alert('Booking  Not Found.');
		$('#txt_booking_no').focus();
		return;
	}

	page_link=page_link+'&job_no='+job_no+'&booking_no='+booking_no+'&selected_no='+selected_no;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../')

}

/*function show_hide_content(row, id)
{
	$('#row_'+row).toggle('fast', function() {
		 get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/fabric_booking_urmi_controller' );
	});
}*/

function validate_value( i , type)
{
	if(type=='finish')
	{
		var real_value =document.getElementById('finscons_'+i).placeholder;
		var user_given =document.getElementById('finscons_'+i).value;
		if(user_given> real_value)
		{
			alert("Over booking than budget not allowed");
			document.getElementById('finscons_'+i).value=real_value;
			document.getElementById('finscons_'+i).focus();
		}
    }
	if(type=='grey')
	{
		var real_value =document.getElementById('greycons_'+i).placeholder;
		var user_given =document.getElementById('greycons_'+i).value;
		document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*user_given;
		if(user_given > real_value )
		{
			alert("Over booking than budget not allowed");
			document.getElementById('greycons_'+i).value=real_value;
			document.getElementById('greycons_'+i).focus();
			document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*real_value;
		}
    }

	if(type=='rate')
	{
		document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*(document.getElementById('greycons_'+i).value)*1;
    }
}

function generate_fabric_report(type)
{

	if ( form_validation('txt_booking_no','Booking No')==false )
	{
		return;
	}
	else
	{
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true)
		{
			show_yarn_rate="1";
		}
		else
		{
			show_yarn_rate="0";
		}
		$report_title=$( "div.form_caption" ).html();
		var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';


		 //copyToClipboard( "asdasdasd_asdasdasd", 1 ) ;
		//alert( fname )
		freeze_window(5);
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );


		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;

		release_freezing();



		//$.post("requires/fabric_booking_urmi_controller.php", { action: "create_file", data: content } );
	}
}


function generate_fabric_report_gr(type)
{
	var booking_option = $("#booking_option").val();
	var booking_option_id = $("#booking_option_id").val();
	var booking_option_no = $("#booking_option_no").val();
	var page_link='requires/fabric_booking_urmi_controller.php?action=booking_surch_option&booking_option='+booking_option+'&booking_option_id='+booking_option_id+'&booking_option_no='+booking_option_no;
	var title="Booking Search Option";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var option_des=this.contentDoc.getElementById("txt_selected").value;
		var option_id=this.contentDoc.getElementById("txt_selected_id").value;
		var serial_no=this.contentDoc.getElementById("txt_selected_no").value;
		//alert(style_des_no);
		$("#booking_option").val(option_des);
		$("#booking_option_id").val(option_id);
		$("#booking_option_no").val(serial_no);


		if (form_validation('txt_booking_no*booking_option_id','Booking No*Report Option')==false)
		{
			var txt_booking_no=$('#booking_option_id').val();
			if(txt_booking_no=="")
			{
				alert("Please Select At Least One Report Option");
				$('#show_textcbo_booking_gr').focus();
			}
			return;
		}
		else
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
			$report_title=$( "div.form_caption" ).html();
			var cbo_booking_gr=$('#booking_option_id').val();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&cbo_booking_gr='+cbo_booking_gr+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/fabric_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_gr_reponse;
		}

	}

}

function generate_fabric_report_gr_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );


		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;
		release_freezing();
		//$.post("requires/fabric_booking_urmi_controller.php", { action: "create_file", data: content } );
	}
}


function generate_fabric_report2()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{

		$report_title=$( "div.form_caption" ).html();
		var data="action=show_fabric_booking_report2"+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../");
		//freeze_window(5);
		http.open("POST","requires/fabric_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report2_reponse;
	}
}

function generate_fabric_report2_reponse()
{
	if(http.readyState == 4)
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;
		release_freezing();
		//$.post("requires/fabric_booking_urmi_controller.php", { action: "create_file", data: content } );
	}
}


function openmypage_unapprove_request()
{
	if (form_validation('txt_booking_no','Booking Number')==false)
	{
		return;
	}

	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;

	var data=txt_booking_no+"_"+txt_un_appv_request;

	var title = 'Un Approval Request';
	var page_link = 'requires/fabric_booking_urmi_controller.php?data='+data+'&action=unapp_request_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");

		$('#txt_un_appv_request').val(unappv_request.value);
	}
}


function openmypage_fabric_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_fabric_booking_no");
		if (theemail.value!="")
		{
			document.getElementById('txt_fabric_booking_no').value=theemail.value;
			fnc_show_booking(1);
		}
	}
}


/*function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	//alert(report_ids);
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==1)
			{
				//alert(report_id[k]);
			$("#print_gr").show();

			}
			if(report_id[k]==2)
			{
			$("#print").show();

			}
			if(report_id[k]==3)
			{
			$("#print_booking3").show();

			}
			if(report_id[k]==4)
			{
			$("#print_booking1").show();

			}
			if(report_id[k]==5)
			{
			$("#print_booking2").show();

			}
			if(report_id[k]==6)
			{
			$("#print_booking4").show();

			}
			if(report_id[k]==7)
			{
			$("#print_booking5").show();

			}
		}
}*/
function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/fabric_booking_urmi_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);

	}

	function copy_colarculfpercent(count)
	{
		var rowCount = $('#tbl_fabric_booking tr').length;
		//alert(rowCount)
		var collar_ex=document.getElementById('txt_colar_excess_percent').value;
		var culf_ex=document.getElementById('txt_cuff_excess_percent').value;

		var bodypartid=document.getElementById('bodypartid_'+count).value;
		var gmtssizeid=document.getElementById('gmtssizeid_'+count).value;
		var pocolorid=document.getElementById('gmtscolorid_'+count).value;
       // var cbogmtsitem=document.getElementById('cbogmtsitem_'+count).value;
	    var ponoid=document.getElementById('pobreakdownid_'+count).value;
		var colarculfpercent=document.getElementById('colarculfpercent_'+count).value;
		var copy_basis=$('input[name="copy_basis"]:checked').val()
		for(var j=count; j<=rowCount; j++)
		{

			if(document.getElementById('bodyparttype_'+j).value==40 || document.getElementById('bodyparttype_'+j).value==50)
			{
				if(document.getElementById('bodyparttype_'+j).value==40)
				{
					if(colarculfpercent=="" && collar_ex!="") var collar_culf_per=collar_ex;
					else var collar_culf_per=colarculfpercent;
				}
				else if(document.getElementById('bodyparttype_'+j).value==50)
				{
					if(colarculfpercent=="" && culf_ex!="") var collar_culf_per=culf_ex;
					else var collar_culf_per=colarculfpercent;
				}

				if(copy_basis==0){
				//document.getElementById('colarculfpercent_'+j).value=colarculfpercent;
					if( bodypartid==document.getElementById('bodypartid_'+j).value){
							document.getElementById('colarculfpercent_'+j).value=collar_culf_per;
					}
				}
				if(copy_basis==1){
					if( gmtssizeid==document.getElementById('gmtssizeid_'+j).value){
						document.getElementById('colarculfpercent_'+j).value=collar_culf_per;
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('gmtscolorid_'+j).value){
						document.getElementById('colarculfpercent_'+j).value=collar_culf_per;
					}
				}
				/*if(copy_basis==3){
					if( pocolorid==document.getElementById('gmtscolorid_'+j).value){
						document.getElementById('colarculfpercent_'+j).value=colarculfpercent;
					}
				}*/
				if(copy_basis==4){
					if( ponoid==document.getElementById('pobreakdownid_'+j).value){
						document.getElementById('colarculfpercent_'+j).value=collar_culf_per;
					}
				}
				if(copy_basis==5){
					if( ponoid==document.getElementById('pobreakdownid_'+j).value){
						//document.getElementById('colarculfpercent_'+j).value=collar_culf_per;
					}
				}

			}
		}
	}

	function check_month_setting()
	{

		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/fabric_booking_urmi_controller');

		var response=response.split("_");
		if(response[0]==1)
		{

			$('#month_id').val(1);
			$('#booking_td').css('color','blue');
			$('#lib_tna_intregrate').val(1);
		}
		else
		{
			$('#month_id').val(2);
			$('#booking_td').css('color','black');
			$('#cbo_booking_month').val('');
			$('#lib_tna_intregrate').val(0);

		}
	}

	function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		/*if(company==cbo_supplier_name && cbo_pay_mode==5){
			alert("Same Company Not Allowed");
			document.getElementById('cbo_supplier_name').value=0;
			return;
		}*/
		fill_attention(cbo_supplier_name)
	}

function fill_attention(supplier_id){
	if(supplier_id==0){
		document.getElementById('txt_attention').value='';
		return;
	}
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	var attention=return_global_ajax_value(supplier_id+"_"+cbo_pay_mode, 'get_attention_name', '', 'requires/fabric_booking_urmi_controller');
	document.getElementById('txt_attention').value=trim(attention);
}

	function compare_date()
	{

		var txt_delevary_date_data=document.getElementById('txt_delivery_date').value;
		txt_delevary_date_data= txt_delevary_date_data.split('-');
		var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
		var txt_tna_date_data=document.getElementById('txt_tna_date').value;
		txt_tna_date_data = txt_tna_date_data.split('-');
		var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];

		var txt_delevary_date = new Date(txt_delevary_date_inv);
		var txt_tna_date = new Date(txt_tna_date_inv);
		//var delivery_date_tna = new Date(txt_delivery_date_tna_data_inv);
		var tna_intregrate=document.getElementById('lib_tna_intregrate').value;
		if(txt_tna_date_data !='')
		{
			// less than, greater than is fine:
			/*x < y; => false
			x > y; => false
			x === y; => false, oops!*/

			// anything involving '=' should use the '+' prefix
			// it will then compare the dates' millisecond values
			/*+x <= +y;  => true
			+x >= +y;  => true
			+x === +y; => true*/
			//alert('mmmm');

			if(tna_intregrate==1)
			{
				if(txt_delevary_date > txt_tna_date)
				{
					alert('Delivery Date is greater than TNA Date');
					document.getElementById('txt_delivery_date').value=document.getElementById('txt_tna_date').value;
				}
			}

		}
		//return
	}
	function fnResetForm()
	{
		reset_form('','booking_list_view','','','');
	}
	function value_reset(){
		$('#txt_booking_no').val('');
		$('#txt_job_no').val('');
		$('#txt_order_no').val('');
		$('#txt_order_no_id').val('');
		reset_form('','booking_list_view','','','');
	}
function check_val( val )
{
	if( val >100){
		$('#txt_booking_percent').val(100);
	}
}
</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate();check_month_setting();">
<div style="width:100%;" align="center">

     <? echo load_freeze_divs ("../../",$permission);?>
            	<form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            	<fieldset style="width:950px;">
                <legend>Fabric Booking </legend>

            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>

                            <td align="right"></td>
                            <td align="right"></td>
                            <td  width="130" height="" align="right" class="must_entry_caption"> Booking No </td>                            <td  width="170" >
                                	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/fabric_booking_urmi_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking"  name="txt_booking_no" id="txt_booking_no"/>
                            </td>
                            <td align="right" width="130" >
                               <input type="hidden" id="id_approved_id">
                               <input type="hidden" id="update_id">
                                <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                           </td>
                           <td> </td>
                    </tr>
                    <tr>
                    		<td  align="right">Company Name </td><!--onClick="copyToClipboard( $('#txt_job_no').val()+'_'+$('#txt_booking_no').val(), 2 )"-->
                              <td>
                              <?
							 echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "value_reset();load_drop_down( 'requires/fabric_booking_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier();",0,"" );

								?>

                              </td>
                         	<td align="right" >Buyer Name</td>
   							 <td id="buyer_td">
                             <?
							  	echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",1,"" );
							?>
                         	</td>
                            <td align="right">Job No.</td>
                        	<td>
                            	  <input style="width:160px;"  class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  />
                            </td>

                    </tr>
                    <tr>

                            <td align="right"  id="booking_td">Booking Month</td>
    						<td>
                            	<?
							  	echo create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
							  ?>
                              <?
							  	echo create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
							  ?>
                           </td>
                              <td align="right" class="must_entry_caption">Fabric Nature</td>
                        	<td>
                            	  <?
									echo create_drop_down( "cbo_fabric_natu", 110, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");
									echo create_drop_down( "cbouom", 60, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
								  ?>
                            </td>
                               <td align="right" width="130" class="must_entry_caption">
                               Fabric Source
                                </td>
                                <td>
                                    <?
									echo create_drop_down( "cbo_fabric_source", 172, $fabric_source,"", 1, "-- Select --", "","", "", "");
								  ?>
                                </td>
                        </tr>
                       <tr>
                       <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                                <td width="170">
                                    <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />
                                </td>
                            <td height="" align="right" class="must_entry_caption">Selected Order No</td>
                            <td colspan="3">
                                 <input class="text_boxes" type="text" style="width:97%;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/fabric_booking_urmi_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                                 <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                            </td>
                        </tr>
                        <tr>
                        <td  width="130" align="right" class="must_entry_caption">Delivery Date</td>
                                <td width="170"><input class="datepicker" type="hidden" style="width:160px" name="txt_tna_date" id="txt_tna_date"/>
                                <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" onChange="compare_date()"/></td>
                                 <td class="must_entry_caption"  align="right">Pay Mode</td>
                                <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 5, "load_drop_down( 'requires/fabric_booking_urmi_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" );
							   ?>
                                 </td>
                        	<td  align="right" class="must_entry_caption">Supplier Name</td>
                                <td id="sup_td">
                               <?
							   		//echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where find_in_set(9,party_type) and  status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
									echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
							   ?>
                                 </td>
                              </tr>
                        <tr>
                            <td align="right">Currency</td>
                              <td>
                              <?
							  	echo create_drop_down( "cbo_currency", 172, $currency,"",1, "-- Select --", 2, "",0 );
							  ?>

                              </td>
                        	<td align="right">Exchange Rate</td>
                              <td>
                             <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  />
                              </td>
                             <td  width="130" height="" align="right"> Source </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );
							   ?>
                                </td>
                        </tr>

                        <tr>
                        	<td align="right">Attention</td>
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                            	<!--<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/fabric_booking_urmi_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">-->
                            </td>
                            <td align="right">Booking Percent</td>
                            <td>
                            <input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_booking_percent" id="txt_booking_percent" value="100" onBlur="check_val( this.value)"   />
                            </td>

                        </tr>

                        <tr>
                            <td align="right">Colar Excess Cut %</td>
                            <td>
                            <input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_colar_excess_percent" id="txt_colar_excess_percent"/>
                            </td>
                            <td align="right">Cuff Excess Cut %</td>
                            <td>
                            <input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_cuff_excess_percent" id="txt_cuff_excess_percent"/>
                            </td>
                             <td align="right">Ready To Approved</td>
                        	<td align="center" height="10">
                              <?
							   		echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
							   ?>
                            </td>
                        </tr>
                        <tr>

                        	<td align="right">Internal Ref No</td>
                        	<td align="center">
                            	<Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:160px"  >
                            </td>
                            <td align="right">File no</td>
                        	<td align="center">
                            	<Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:160px" >
                            </td>
                            <td align="right">Un-approve request</td>
                        	<td align="center">
                            	<Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:160px"  onClick="openmypage_unapprove_request()">
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">Fabric Composition</td>
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" maxlength="200" style="width:99%;"  name="txt_fabriccomposition" id="txt_fabriccomposition"/>
                            </td>


                        </tr>
                        <tr>
                        	<td align="right">Remarks</td>
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" maxlength="200" style="width:99%;"  name="txt_remark" id="txt_remark"/>
                            </td>
                        </tr>
                         <tr>
                        	<td align="left" height="10" colspan="4">
                            <!--<input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/fabric_booking_urmi_controller.php?action=terms_condition_popup','Terms Condition')" />-->
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(118,'txt_booking_no','../../');
                            ?>


                            <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/fabric_booking_urmi_controller.php?action=rmg_process_loss_popup','Process Loss %')" />
                            <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" />
                            <input type="button" id="set_button" class="image_uploader" style="width:120px;" value="Trims Dye To Match" onClick="dtm_popup('requires/fabric_booking_urmi_controller.php?action=dtm_popup','DTM')"  />
                            <input type="button" id="set_button" class="image_uploader" style="width:160px; display:none" value="Adjust Qty" onClick="open_adjust_qty_popup('requires/fabric_booking_urmi_controller.php?action=open_adjust_qty_popup','Adjust Qty')" />
                            <input style="width:60px; display:none" type="hidden" class="text_boxes"  name="adjust_qty_breck_down" id="adjust_qty_breck_down" />


                            <!--<input type="button" id="cuf_button" class="image_uploader" style="width:160px;" value="Size Wise Cuff Excess %" onClick="open_size_wise_cuff_popup('requires/fabric_booking_urmi_controller.php?action=size_wise_cuff_popup','Size Wise Cuff Excess %')" />
                            <input type="button" id="cuf_button" class="image_uploader" style="width:160px;" value="Size Wise Cuff Excess %" onClick="open_size_wise_colur_popup('requires/fabric_booking_urmi_controller.php?action=size_wise_colar_popup','Size Wise Colar Excess %')" />-->

                            </td>
                            <td>&nbsp;

                            </td>
                            <td colspan="2">&nbsp;</td>
                        </tr>

                        <tr>
                        	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                        </tr>
                         <tr>
                        	<td align="center" colspan="6" valign="top" id="app_sms3" style="font-size:18px; color:#F00"></td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"fnResetForm();reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,5*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*txt_booking_date,".$date."','','cbo_booking_year*cbo_booking_month*cbo_fabric_natu*cbo_fabric_source*txt_booking_date*txt_delivery_date*cbo_supplier_name*cbo_pay_mode*cbo_source*cbo_currency')",1) ; ?>
                              <input class="text_boxes" name="lib_tna_intregrate" id="lib_tna_intregrate" type="hidden" value=""  style="width:100px"/>
                            <div id="pdf_file_name"></div>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" height="10">
                           <!--	<input type="button" class="formbutton" style="width:200px" value="Apply Last Update" onClick="fnc_show_booking(2)">-->
                            <input type="hidden" class="" style="width:200px" id="selected_id_for_delete">
                           </td>
                        </tr>
                    </table>

              </fieldset>
              </form>

	</div>
    <div id="booking_list_view">
    </div>
   <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
load_drop_down( 'requires/fabric_booking_urmi_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
});
//set_multiselect( 'cbo_booking_gr', '1', '0', '0', '0' );
</script>
</html>