<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Sample Fabric Booking
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
Updated by 		         : Md. Didarul Alam		
Update date		         : 250/08/2016	   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Booking", "../../", 1, 1,$unicode,'','');
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

<?
					 
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][89] );
echo "var field_level_data= ". $data_arr . ";\n";

?>
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
		if(form_validation('cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Year*Fabric Nature*Fabric Source')==false)
		return;
	}
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/sample_booking_controller');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=470px,center=1,resize=1,scrolling=0','../')
		}
		else
		{
			var r=confirm("Existing Item against these Order  Will be Deleted")
			if(r==true)
			{
			var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/sample_booking_controller');
			show_list_view(txt_booking_no,'show_fabric_booking','booking_list_view','requires/sample_booking_controller','setFilterGrid(\'list_view\',-1)');
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=470px,center=1,resize=1,scrolling=0','../')
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
				reset_form('orderdetailsentry_2','booking_list_view','','','')
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
				
				
				
				get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/sample_booking_controller" );
				check_month_setting();
				load_drop_down( 'requires/sample_booking_controller', id.value, 'load_drop_down_po_number', 'order_drop_down_td' )
                load_drop_down( 'requires/sample_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_fabric_description', 'fabricdescription_id_td' )
				load_drop_down( 'requires/sample_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_fabric_color', 'fabriccolor_id_id_td' )
				
				var buyer_id=$("#cbo_buyer_name").val();
				load_drop_down( 'requires/sample_booking_controller', buyer_id, 'load_drop_down_buyer_tag_sample', 'sample_td' )
				
				load_drop_down( 'requires/sample_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_gmts_color', 'garmentscolor_id_id_td' )
				
				load_drop_down( 'requires/sample_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_item_size', 'itemsize_id_td' )
				load_drop_down( 'requires/sample_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_gmts_size', 'garmentssize_id_td' )
				release_freezing();
				//fnc_generate_booking()
			}
		}
	}
}

function openmypage_booking(page_link,title)
{

	var company=$("#cbo_company_name").val()*1;
	//alert(company);
	//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1400px,height=450px,center=1,resize=1,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_booking_controller" );
			print_button_setting();
			check_month_setting();
			reset_form('orderdetailsentry_2','booking_list_view','','','')
			
			var txt_order_no_id=document.getElementById('txt_order_no_id').value
			var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
			var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
			
			var buyer_id=$("#cbo_buyer_name").val();
			
			load_drop_down( 'requires/sample_booking_controller', txt_order_no_id, 'load_drop_down_po_number', 'order_drop_down_td' )
            load_drop_down( 'requires/sample_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_fabric_description', 'fabricdescription_id_td' )
		    load_drop_down( 'requires/sample_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_fabric_color', 'fabriccolor_id_id_td' )
			load_drop_down( 'requires/sample_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_gmts_color', 'garmentscolor_id_id_td' )
			
			load_drop_down( 'requires/sample_booking_controller', buyer_id, 'load_drop_down_buyer_tag_sample', 'sample_td' )
			
			load_drop_down( 'requires/sample_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_item_size', 'itemsize_id_td' )
			load_drop_down( 'requires/sample_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source, 'load_drop_down_gmts_size', 'garmentssize_id_td' )
			show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/sample_booking_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			
			release_freezing();
		}
	}
}


function openmypage_fabric_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
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

function calculate_requirement()
{
	var cbo_company_name= document.getElementById('cbo_company_name').value;
	var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
	var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/sample_booking_controller');
	var txt_finish_qnty=(document.getElementById('txt_finish_qnty').value)*1;
	var processloss=(document.getElementById('txt_process_loss').value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(txt_finish_qnty/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('txt_grey_qnty').value= WastageQty;
		document.getElementById('txt_amount').value=number_format_common((document.getElementById('txt_rate').value)*1*WastageQty,5,0)
}


function fnc_fabric_booking( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	
	var delivery_date=$('#txt_delivery_date').val();
	if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
	{
		alert("Delivery Date Not Allowed Less than Booking Date");
		return;
	}
	
	var month_set_id=$('#month_id').val();
	if(month_set_id==1)
	{
		if (form_validation('cbo_booking_month','Booking Month')==false)
		{
			return;
		}	
	}
	if (form_validation('txt_order_no_id*txt_booking_date*txt_delivery_date*cbo_pay_mode','Order No*Booking Date*Delivery Date*Pay mode')==false)
	{
		return;
	}	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*cbo_company_name*cbo_buyer_name*update_id*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*cbo_ready_to_approved*txt_fabriccomposition*txt_fabric_booking_no',"../../");
		freeze_window(operation);
		http.open("POST","requires/sample_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_reponse;
	}
}
	 
function fnc_fabric_booking_reponse(){
	
	if(http.readyState == 4){
		 var reponse=trim(http.responseText).split('**');
		 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
			 show_msg(trim(reponse[0]));
			 document.getElementById('txt_booking_no').value=reponse[1];
			 document.getElementById('update_id').value=reponse[2];
			 set_button_status(1, permission, 'fnc_fabric_booking',1);
		 }
		 if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		/*if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}*/
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		 release_freezing();
	}
}


function fnc_fabric_booking_dtls( operation )
{
	/*if(operation==2)
	{
		alert("Delete Restricted")cbo_order_id
		return;
	}*/
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	if (form_validation('txt_booking_no*cbo_sample_type*txt_booking_date*cbo_order_id*cbo_fabricdescription_id*cbo_garmentscolor_id*cbo_garmentssize_id','Booking No*cbo_sample_type*Booking Date*Order No*Fabric Description*Garments Color*Garments size')==false)
	{
		return;
	}
	if(document.getElementById('cbo_fabric_source').value==2)
		{
			if (form_validation('txt_rate','Rate')==false){
				return;
			}			
		}	
	var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('txt_booking_no*txt_job_no*update_id*cbo_order_id*cbo_fabricdescription_id*cbo_sample_type*cbo_fabriccolor_id*cbo_garmentscolor_id*cbo_itemsize_id*cbo_garmentssize_id*txt_dia_width*txt_finish_qnty*txt_process_loss*txt_grey_qnty*txt_rate*txt_amount*update_id_details*txt_bh_qty*txt_rf_qty*cbo_pay_mode*cbouom',"../../");
	freeze_window(operation);
	http.open("POST","requires/sample_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
}
	 
function fnc_fabric_booking_dtls_reponse(){
	if(http.readyState == 4){
		 var reponse=http.responseText.split('**');
		 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
			 show_msg(trim(reponse[0]));
			 reset_form('orderdetailsentry_2','booking_list_view','','','')
			 set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
			 show_list_view(reponse[1],'show_fabric_booking','booking_list_view','requires/sample_booking_controller','setFilterGrid(\'list_view\',-1)');
		 }
		 if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		/*if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}*/
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		 release_freezing();
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


function open_trims_acc_popup(title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var update_id=document.getElementById('update_id').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}	
	else
	{
		page_link='requires/sample_booking_controller.php?action=acc_popup'+get_submitted_data_string('txt_booking_no*update_id*txt_job_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=470px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			//var theform=this.contentDoc.forms[0];
			//var theemail=this.contentDoc.getElementById("selected_data").value;
			//document.getElementById('trims_acc_hidden_data').value=theemail;
		}
	}
}



function enable_disable(value)
{
	/*if(value==2)
	{
		document.getElementById('txt_rate').disabled=false;
	}
	else
	{
		document.getElementById('txt_rate').disabled=true;
	}*/
}

function generate_fabric_report(type)
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		if(type==1)
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
		}
			$report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
			var data="action=show_fabric_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate;
		}
		else if(type==2)
		{
			var data="action=show_fabric_booking_report2"+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title;	
		}
		else if(type==3)
		{
			var data="action=show_fabric_booking_report3"+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title;	
		}
		//freeze_window(5);
		http.open("POST","requires/sample_booking_controller.php",true);
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
'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function check_exchange_rate()
{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/sample_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
}
function check_month_setting()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/sample_booking_controller');
		
		var response=response.split("_");
		if(response[0]==1)
		{
			
			$('#month_id').val(1);
			$('#booking_td').css('color','blue');	
		}
		else
		{
			$('#month_id').val(2);
			$('#booking_td').css('color','black');
			$('#cbo_booking_month').val('');		
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
		
	}
	function print_button_setting()
	{
		$('#button_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sample_booking_controller' ); 
	}
	
	function print_report_button_setting(report_ids) 
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==38)
			{
				$('#button_panel').append( '<input type="button" value="Print Booking" onClick="generate_fabric_report(1)"  style="width:100px" name="print" id="print" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==39)
			{
				$('#button_panel').append( '<input type="button" value="Print Booking2" onClick="generate_fabric_report(2)"  style="width:100px" name="print2" id="print2" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==64)
			{
				$('#button_panel').append( '<input type="button" value="Print Metro" onClick="generate_fabric_report(3)"  style="width:100px" name="print3" id="print3" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			
			
		}
	}

    
</script>
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();check_month_setting();print_button_setting();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:950px;">
        <legend>Sample Booking </legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="3"> Booking No </td>              <!-- 11-00030  -->
                    <td colspan="3"><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/sample_booking_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    	<input type="hidden" id="update_id">
                        <input type="hidden" id="id_approved_id">
                		<input type="hidden" id="month_id" class="text_boxes" style="width:20px" >
                        <input type="hidden" id="report_ids" name="report_ids"/>
                    </td>
                </tr>
                <tr>
                    <td width="130" align="right">Company Name</td>
                    <td width="172"><? echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where comp.status_active=1 $company_cond and comp.is_deleted=0 order by comp.company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier();print_button_setting();",0,"" );
                    ?>	
                    </td>
                    <td width="130" align="right" >Buyer Name</td>   
                    <td width="172" id="buyer_td"><?  echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );",1,"" ); ?></td>
                    <td width="130" align="right">Job No.</td>
                    <td><input style="width:160px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled /></td>
                </tr>
                <tr>
                    <td align="right" id="booking_td">Booking Month</td>   
                    <td><? echo create_drop_down( "cbo_booking_month", 100, $months,"", 1, "-- Select --", "", "",0 ); ?>
                    	<? echo create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?>
                    </td>
                    <td align="right" class="must_entry_caption">Fabric Nature</td>
                    <td><? echo create_drop_down( "cbo_fabric_natu", 172, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");	?>	
                    </td>
                    <td align="right" width="130" class="must_entry_caption">Fabric Source</td>
                    <td><? echo create_drop_down( "cbo_fabric_source", 172, $fabric_source,"", 1, "-- Select --", "","enable_disable(this.value);", "", ""); ?></td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                    </td>
                    <td align="right" class="must_entry_caption">Selected Order No</td>   
                    <td colspan="3"><input class="text_boxes" type="text" style="width:97%;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/sample_booking_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                    <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                    </td>                                
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    <td align="right">Exchange Rate</td>
                    <td><input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                    <td align="right"> Source </td>              <!-- 11-00030  -->
                    <td><? echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption" align="right">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td align="right" class="must_entry_caption">Pay Mode</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --",3, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?> 
                    </td>
                    <td align="right">Supplier Name</td>
                    <td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_controller');",0 ); ?> 
                    </td> 
                </tr>
                <tr>
                    <td align="right">Attention</td>   
                    <td align="left" colspan="3">
                        <input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                        <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/sample_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                    <td align="right">Ready To Approved</td>  
                    <td align="center" height="10"><? echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                </tr>
                <tr>
                    <td align="right">Fabric Composition</td>   
                    <td align="left" colspan="5"><input class="text_boxes" type="text" maxlength="200" style="width:98%;"  name="txt_fabriccomposition" id="txt_fabriccomposition"/></td>
                </tr>
                <tr>
                    <td align="right">File No </td>   
                    <td align="left" colspan="2"><input class="text_boxes" type="text"  style="width:300px;"  name="txt_file" id="txt_file"/></td>
                    <td align="right">Ref. No </td>   
                    <td align="left" colspan="2"><input class="text_boxes" type="text"  style="width:290px;"  name="txt_ref" id="txt_ref"/></td>
                </tr>
                <tr>
                    <td width="130" align="right">Tag Booking No</td>                            
                    <td width="170"><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_fabric_booking('requires/sample_booking_controller.php?action=fabric_booking_no_popup','fabric Booking Search')" readonly placeholder="Double Click for Fabric Booking"  name="txt_fabric_booking_no" id="txt_fabric_booking_no"/>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="6">
                        <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Accessories" onClick="open_trims_acc_popup('Accessories Dtls')" />
                        <!-- <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/sample_booking_controller.php?action=terms_condition_popup','Terms Condition')" /> -->
                        <? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(89,'txt_booking_no','../../');
                        ?>
                    </td>
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
                    </td>
                </tr>
                <tr>
                	<td align="center" colspan="6" height="10"></td>
                </tr>
            </table>
        </fieldset>
    </form>
              
             
    <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
    <fieldset style="width:950px;">
    <legend>Sample Booking </legend>
        <table  width="900" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td align="right" class="must_entry_caption">PO No </td>   
                <td id="order_drop_down_td"><? echo create_drop_down( "cbo_order_id", 172, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                <td align="right" class="must_entry_caption">Fabric Description</td>
                <td id="fabricdescription_id_td" colspan="3"><? echo create_drop_down( "cbo_fabricdescription_id", 420, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
            </tr>
            <tr>
                <td width="130" align="right" class="must_entry_caption">Sample Type</td>
                <td id="sample_td"><? echo create_drop_down( "cbo_sample_type", 172, $blank_array,"", 1, "-- Select --", $selected, "",0 );	
                	//echo create_drop_down( "cbo_sample_type", 172, "select id, sample_name from lib_sample where is_deleted=0","id,sample_name", 1, "--Select--", $selected, "" ); ?>
                </td>
                <td width="130" align="right" class="must_entry_caption">Garments Color </td>
                <td id="garmentscolor_id_id_td"><?  echo create_drop_down( "cbo_garmentscolor_id", 172, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                <td width="130" align="right">Fabric Color </td>
                <td id="fabriccolor_id_id_td" >
                	<? echo create_drop_down( "cbo_fabriccolor_id", 172, $blank_array,"", 1, "--Select--", $selected, "" ); ?>
                </td>
            </tr>
            <tr>
                <td align="right" width="130" class="must_entry_caption">Garments size</td>   
                <td id="garmentssize_id_td"><?  echo create_drop_down( "cbo_garmentssize_id", 172, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                <td height="" align="right" width="130">Item size</td>   
                <td id="itemsize_id_td"><? echo create_drop_down( "cbo_itemsize_id", 172, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                <td align="right">Dia/ Width</td>   
                <td><input name="txt_dia_width" id="txt_dia_width" class="text_boxes" type="text" value=""  style="width:160px "/></td> 
            </tr>
            <tr>
                <td align="right">Finish Fabric</td>
                <td><input name="txt_finish_qnty" id="txt_finish_qnty" class="text_boxes_numeric" type="text" onChange="calculate_requirement()" style="width:160px "/></td> 
                <td align="right">Process loss</td>
                <td><input name="txt_process_loss" id="txt_process_loss" class="text_boxes_numeric" type="text" onChange="calculate_requirement()" style="width:160px "/></td>
                <td align="right">Gray Fabric</td>
                <td><input name="txt_grey_qnty" id="txt_grey_qnty" class="text_boxes_numeric" type="text" value=""  style="width:160px " readonly/></td> 
            </tr>
            <tr>
                <td align="right">File No</td>
                <td><input name="txt_file2" id="txt_file2" class="text_boxes_numeric" type="text"  style="width:160px "/></td> 
                <td align="right">Ref. No</td>
                <td><input name="txt_ref2" id="txt_ref2" class="text_boxes_numeric" type="text"  style="width:160px "/></td>
                <td width="130" align="right">Rate</td>
                <td width="170"><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" value="" onChange="calculate_requirement()" style="width:160px " /></td>
            </tr>
            <tr>
                <td align="right">Amount</td>
                <td><input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:160px " readonly/></td>
                <td width="130" height="" align="right"> GMT Qty  </td>              
                <td width="170" >
                    <input name="txt_bh_qty" id="txt_bh_qty" class="text_boxes_numeric" type="text" value=""  style="width:70px " placeholder="BH Qty"/>
                    <input name="txt_rf_qty" id="txt_rf_qty" class="text_boxes_numeric" type="text" value=""  style="width:70px; float:right;" placeholder="RF Qty"/>
                    <input type="hidden" id="update_id_details">
                </td>
                <td align="right">Uom</td>
                <td id="uom_td"><? echo create_drop_down( "cbouom", 172, $unit_of_measurement,'', 1, '-Uom-',"", "",1,"1,12,23,27" ); ?></td>
            </tr>
            <tr>
                <td align="center" colspan="6" valign="middle" class="button_container">
					<? echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2); ?>
                    <!--<input type="button" value="Print Booking" onClick="generate_fabric_report(1)"  style="width:100px" name="print" id="print" class="formbutton" /> 
                    <input type="button" value="Print Booking2" onClick="generate_fabric_report(2)"  style="width:100px" name="print2" id="print2" class="formbutton" /> 
                    <input type="button" value="Print Booking3" onClick="generate_fabric_report(3)"  style="width:100px" name="print3" id="print3" class="formbutton" />-->
                    <div id="pdf_file_name"></div>
                    <div id="button_panel"></div>
                </td>
            </tr>
        </table>
    </fieldset>
    </form>
              
    <fieldset style="width:1200px;">
    <legend>Booking Entry</legend>
        <table style="border:none" cellpadding="0" cellspacing="2" border="0"> 
            <tr align="center">
                <td id="booking_list_view"></td>	
            </tr>
        </table>
    </fieldset>
	</div>
    <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
load_drop_down( 'requires/sample_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
});
//set_multiselect( 'cbo_booking_gr', '1', '0', '0', '0' ); 
</script>
</html>