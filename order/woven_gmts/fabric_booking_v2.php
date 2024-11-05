<?php
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This Form will create Dia Wise Fabric Booking
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	23-12-2015
Requirment Client        :  AKH
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
Comments		         : 
-----------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dia Wise Fabric Booking", "../../", 1, 1,$unicode,'','');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
function openmypage_booking(page_link,title){
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1400px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!=""){
			reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_booking_controller_v2" );
			check_month_setting();
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			show_list_view(document.getElementById('txt_booking_no').value+"_"+document.getElementById('txt_order_no_id').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbo_fabric_source').value,'show_listview','list_view','requires/fabric_booking_controller_v2','');
		}
	}
}

function openmypage_order(page_link,title){
	if(document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		return;
	}
	var month_check=$('#month_id').val();
	if(month_check==1){
		if (form_validation('cbo_booking_month*cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false){
			return;
		}	
	}
	else{
		if (form_validation('cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Year*Fabric Nature*Fabric Source')==false){
			return;
		}
	}
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/fabric_booking_controller_v2');
	if(trim(check_is_booking_used_id) !=""){
		alert("This booking used in PI Table. So Adding or removing order is not allowed")
		return;
	}
	else{
		if(txt_booking_no==""){
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=470px,center=1,resize=1,scrolling=0','../')
		}
		else{
			var r=confirm("Existing Item against these Order  Will be Deleted")
			if(r==true){
				var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/fabric_booking_controller_v2');
				page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=470px,center=1,resize=1,scrolling=0','../')
			}
			else{
				return;
			}
		}
		emailwindow.onclose=function(){
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			if (id.value!=""){																																																																																								
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				get_php_form_data( id.value+"_"+document.getElementById('cbo_fabric_natu').value+"_"+document.getElementById('cbo_fabric_source').value, "populate_order_data_from_search_popup", "requires/fabric_booking_controller_v2" );
				check_month_setting();
				loadmatrix()
				release_freezing();
			}
		}
	}
}

function fnc_fabric_booking( operation )
{
	freeze_window(operation);
	if(operation==2){
		alert("Delete Restricted")
		release_freezing();
		return;
	}
	if(document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		release_freezing();
		return;
	}
	var month_set_id=$('#month_id').val();
	if(month_set_id==1){
		if (form_validation('cbo_booking_month','Booking Month')==false){
			release_freezing();
			return;
		}	
	}
	if (form_validation('txt_order_no_id*txt_booking_date','Order No*Booking Date')==false){
		release_freezing();
		return;
	}
	if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0){
		alert("Select Supplier Name")
		release_freezing();
		return;
	}
	else{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*txt_booking_percent*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_ready_to_approved*processloss_breck_down*txt_fabriccomposition*txt_intarnal_ref*txt_file_no',"../../");
		http.open("POST","requires/fabric_booking_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_reponse;
	}
}
	 
function fnc_fabric_booking_reponse(){
	if(http.readyState == 4){
		 var reponse=trim(http.responseText).split('**');
		 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1){
			document.getElementById('txt_booking_no').value=reponse[1];
			set_button_status(1, permission, 'fnc_fabric_booking',1);
		 }
		 show_msg(trim(reponse[0]));
		 release_freezing();
	}
}

function loadcolorsize(){
	var colorsize=return_global_ajax_value(document.getElementById('cbo_order_id').value+"_"+document.getElementById('cbo_fabricdescription_id').value+"_"+document.getElementById('cbo_gmt_item_id').value, 'load_color_size', '', 'requires/fabric_booking_controller_v2');
	document.getElementById('listview_dtls').innerHTML=data;
}

function loadmatrix(){
	var cbo_order_id=document.getElementById('cbo_order_id').value;
	var cbo_fabricdescription_id=document.getElementById('cbo_fabricdescription_id').value;
	var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	var newdia=document.getElementById('newdia').value;
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
	if (form_validation('cbo_order_id*cbo_fabricdescription_id*cbo_gmt_item_id','PO No*Fabric Description*Gmt. Item')==false){
	return;
	}
	var colorsize=return_global_ajax_value(cbo_order_id+"_"+cbo_fabricdescription_id+"_"+cbo_gmt_item_id+"_"+newdia+"_"+txt_booking_no+"_"+cbo_company_name+"_"+cbo_fabric_natu, 'load_color_size', '', 'requires/fabric_booking_controller_v2');
    document.getElementById('breackdown_form').innerHTML=colorsize;
	
}

function load_dtls_data(po_break_down_id,item_number_id,pre_cost_fabric_cost_dtls_id,dia_width){
	document.getElementById('cbo_order_id').value=po_break_down_id;
	document.getElementById('cbo_fabricdescription_id').value=pre_cost_fabric_cost_dtls_id;
	document.getElementById('cbo_gmt_item_id').value=item_number_id;
	document.getElementById('newdia').value=dia_width;
	document.getElementById('saveddia').value=dia_width;
	document.getElementById('cbo_order_id').disabled=true
	document.getElementById('cbo_fabricdescription_id').disabled=true
	document.getElementById('cbo_gmt_item_id').disabled=true
	loadmatrix();
	set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
}

function fnc_fabric_booking_dtls( operation )
{
	freeze_window(operation);
	if(document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		release_freezing();	
		return;
	}
	if (form_validation('txt_order_no_id*txt_booking_date*txt_booking_no*cbo_order_id*cbo_gmt_item_id*cbo_fabricdescription_id*newdia','Order No*Booking Date*Booking No*PO No*Gmt. Item*Fabric Description*Dia')==false){
		release_freezing();	
		return;
	}

	var colnum=0;
	$("#table_1  thead tr").find("th").each(function() {
		colnum++;
	});	
	colnum=colnum-4;
	var data_size='';
	for(var i=1; i<=colnum; i++){
		data_size+=get_submitted_data_string('gmtssize_'+i,"../../",1);
	}
	var data_color='';
	var data_breakdown='';
	var row_num=$('#table_1 tbody tr').length/11;
	for(i =1;i <= row_num;i++){
		data_color+=get_submitted_data_string('gmtscolor_'+i+'*fabcolor_'+i,"../../",i);
		for(var m=1; m<=colnum; m++){
			data_breakdown+=get_submitted_data_string('gmtsqty_'+i+'_'+m+'*newcons_'+i+'_'+m+'*greyconsdzn_'+i+'_'+m+'*totcons_'+i+'_'+m+'*totfincons_'+i+'_'+m+'*processloss_'+i+'_'+m+'*itemsize_'+i+'_'+m+'*dia_'+i+'_'+m+'*cons_'+i+'_'+m+'*colorsizetableid_'+i+'_'+m+'*updateid_'+i+'_'+m,"../../",m);
		}
	}
	var mstdata=get_submitted_data_string('txt_booking_no*txt_job_no*cbo_order_id*cbo_gmt_item_id*cbo_fabricdescription_id*newdia*saveddia*colortype*construction*composition*gsm_weight',"../../",i);
	var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+'&total_col='+colnum+data_size+data_color+data_breakdown+mstdata;
	http.open("POST","requires/fabric_booking_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
}
	 
function fnc_fabric_booking_dtls_reponse(){
	if(http.readyState == 4){
		 var reponse=http.responseText.split('**');
		 show_msg(trim(reponse[0]));
		 if(trim(reponse[0])==11){
			 alert("Duplicate Data found")
			 release_freezing();
			 return
		 }
		 set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
		 show_list_view(document.getElementById('txt_booking_no').value+"_"+document.getElementById('txt_order_no_id').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbo_fabric_source').value,'show_listview','list_view','requires/fabric_booking_controller_v2','');
		 document.getElementById('cbo_order_id').disabled=false
		 document.getElementById('cbo_fabricdescription_id').disabled=false
		 document.getElementById('cbo_gmt_item_id').disabled=false
		 document.getElementById('newdia').value=''
		 release_freezing();
	}
}

function open_terms_condition_popup(page_link,title){
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no==""){
		alert("Save The Booking First")
		return;
	}	
	else{
		page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
		}
	}
}

function open_rmg_process_loss_popup(page_link,title){
	var processloss_breck_down=document.getElementById('processloss_breck_down').value
	page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("processloss_breck_down");
		if (theemail.value!=""){
			document.getElementById('processloss_breck_down').value=theemail.value;
		}
	}
}


function open_size_wise_cuff_popup(page_link,title){
	var processloss_breck_down=document.getElementById('processloss_breck_down').value
	page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("processloss_breck_down");
		if (theemail.value!=""){
			document.getElementById('processloss_breck_down').value=theemail.value;
		}
	}
}

function open_size_wise_colur_popup(page_link,title){
	var processloss_breck_down=document.getElementById('processloss_breck_down').value
	page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("processloss_breck_down");
		if (theemail.value!=""){
			document.getElementById('processloss_breck_down').value=theemail.value;
		}
	}
}
	
function openmypage_unapprove_request(){
	if (form_validation('txt_booking_no','Booking Number')==false){
		return;
	}
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
	var data=txt_booking_no+"_"+txt_un_appv_request;
	var title = 'Un Approval Request';	
	var page_link = 'requires/fabric_booking_controller_v2.php?data='+data+'&action=unapp_request_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function(){
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
		$('#txt_un_appv_request').val(unappv_request.value);
	}
}

function check_exchange_rate(){
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/fabric_booking_controller_v2');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}
	
function copy_colarculfpercent(count){
	var rowCount = $('#tbl_fabric_booking tr').length;
	var bodypartid=document.getElementById('bodypartid_'+count).value;
	var gmtssizeid=document.getElementById('gmtssizeid_'+count).value;
	var colarculfpercent=document.getElementById('colarculfpercent_'+count).value;
	for(var j=count; j<=rowCount; j++){
		if(document.getElementById('bodypartid_'+j).value==2 || document.getElementById('bodypartid_'+j).value==3){
			if( gmtssizeid==document.getElementById('gmtssizeid_'+j).value){
				document.getElementById('colarculfpercent_'+j).value=colarculfpercent;
			}
		}
	}
}
	
function check_month_setting(){
	var cbo_company_name=$('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/fabric_booking_controller_v2');
	var response=response.split("_");
	if(response[0]==1){
		$('#month_id').val(1);
		$('#booking_td').css('color','blue');	
	}
	else{
		$('#month_id').val(2);
		$('#booking_td').css('color','black');
		$('#cbo_booking_month').val('');		
	}
}
	
function validate_suplier(){
	var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
	var company=document.getElementById('cbo_company_name').value;
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	if(company==cbo_supplier_name && cbo_pay_mode==5){
		alert("Same Company Not Allowed");
		document.getElementById('cbo_supplier_name').value=0;
		return;
	}
}
	
function compare_date(){
	var txt_delevary_date_data=document.getElementById('txt_delivery_date').value;
	txt_delevary_date_data= txt_delevary_date_data.split('-');
	var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
	
	var txt_tna_date_data=document.getElementById('txt_tna_date').value;
	txt_tna_date_data = txt_tna_date_data.split('-');
	var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];
	
	var txt_delevary_date = new Date(txt_delevary_date_inv);
    var txt_tna_date = new Date(txt_tna_date_inv);
	if(txt_tna_date_data !=''){
		if(txt_delevary_date > txt_tna_date){
			alert('Delivery Date is greater than TNA Date');
			document.getElementById('txt_delivery_date').value=document.getElementById('txt_tna_date').value;
		}
	}
}

function calculate_tot_cons(field_id){
	var field_id_arr=field_id.split("_");
	if(field_id_arr[0]=='gmtsqty'){
	    var gmtsqty=(document.getElementById(field_id).value)*1;
	    var newcons=(document.getElementById('newcons_'+field_id_arr[1]+"_"+field_id_arr[2]).value)*1;
		if(newcons>=0 && gmtsqty>=0){
		document.getElementById('totfincons_'+field_id_arr[1]+"_"+field_id_arr[2]).value=number_format_common((newcons/12)*gmtsqty,5,'','')
		}
		
	}
	
	if(field_id_arr[0]=='newcons'){
	    var newcons=(document.getElementById(field_id).value)*1;
	    var gmtsqty=(document.getElementById('gmtsqty_'+field_id_arr[1]+"_"+field_id_arr[2]).value)*1;
		if(newcons>=0 && gmtsqty>=0){
		document.getElementById('totfincons_'+field_id_arr[1]+"_"+field_id_arr[2]).value=number_format_common((newcons/12)*gmtsqty,5,'','')
		}
	}
}

function calculate_requirement(field_id){
	var field_id_arr=field_id.split("_");
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var gmtsqty=(document.getElementById('gmtsqty_'+field_id_arr[1]+"_"+field_id_arr[2]).value)*1;
	var cons=(document.getElementById('newcons_'+field_id_arr[1]+"_"+field_id_arr[2]).value)*1;
	var processloss=(document.getElementById('processloss_'+field_id_arr[1]+"_"+field_id_arr[2]).value)*1;
	var WastageQty='';
	if(process_loss_method_id==1){
		WastageQty=cons+cons*(processloss/100);
	}
	else if(process_loss_method_id==2){
		var devided_val = 1-(processloss/100);
		var WastageQty=parseFloat(cons/devided_val);
	}
	else{
		WastageQty=0;
	}
	WastageQty= number_format_common( WastageQty, 5, 0) ;	
	document.getElementById('greyconsdzn_'+field_id_arr[1]+"_"+field_id_arr[2]).value= WastageQty;
	document.getElementById('totcons_'+field_id_arr[1]+"_"+field_id_arr[2]).value=number_format_common((WastageQty/12)*gmtsqty,5,'','')
}

function copy_data(field_id){
	var colnum=0;
	$("#table_1  thead tr").find("th").each(function() {
	colnum++;
	});	
	var field_id_arr=field_id.split("_");
	colnum=colnum-4;
	for(var i=field_id_arr[2]; i<=colnum; i++){
		if(field_id_arr[0]=='gmtsqty'){
			if($('#gmtsqty_'+field_id_arr[1]+"_"+i).prop("disabled")==false){
				var gmtsqty=(document.getElementById(field_id).value)*1;
				//var gmtsqty_saved= $('#gmtsqty_'+field_id_arr[1]+"_"+i).prop("title");
				//var hidgmtsqty=document.getElementById('hidgmtsqty_'+field_id_arr[1]+"_"+i).value;
				document.getElementById('gmtsqty_'+field_id_arr[1]+"_"+i).value=gmtsqty;
				var field_id_new='gmtsqty_'+field_id_arr[1]+"_"+i;
				calculate_tot_cons(field_id_new);
				calculate_requirement(field_id_new)
			}
		}
		if(field_id_arr[0]=='newcons'){
			if($('#newcons_'+field_id_arr[1]+"_"+i).prop("disabled")==false){
				var newcons=(document.getElementById(field_id).value)*1;
				document.getElementById('newcons_'+field_id_arr[1]+"_"+i).value=newcons;
				var field_id_new='newcons_'+field_id_arr[1]+"_"+i;
				calculate_tot_cons(field_id_new);
				calculate_requirement(field_id_new)
			}
		}
		
		if(field_id_arr[0]=='processloss'){
			if($('#processloss_'+field_id_arr[1]+"_"+i).prop("disabled")==false){
				var processloss=(document.getElementById(field_id).value)*1;
				document.getElementById('processloss_'+field_id_arr[1]+"_"+i).value=processloss;
				var field_id_new='processloss_'+field_id_arr[1]+"_"+i;
				calculate_requirement(field_id_new)
			}
		}
	}
	var total_gmts_qty=0;
	var total_totcons_qty=0;
	for(var i=1; i<=colnum; i++){
		total_gmts_qty+=document.getElementById('gmtsqty_'+field_id_arr[1]+"_"+i).value*1
		total_totcons_qty+=document.getElementById('totcons_'+field_id_arr[1]+"_"+i).value*1
	}
	document.getElementById('totalgmtsqty_'+field_id_arr[1]).value=total_gmts_qty;
	document.getElementById('totaltotcons_'+field_id_arr[1]).value=number_format_common(total_totcons_qty,5,'','');
}
function fnc_check_po_size_qty(field_id,po_id,color_id,size_id,item_id){
	
	var colnum=0;
	$("#table_1  thead tr").find("th").each(function() {
	colnum++;
	});	
	var field_id_arr=field_id.split("_");
	colnum=colnum-4;
	//alert(colnum);
	
	
	var size_qty =document.getElementById(field_id).value*1;
	//alert(size_qty);
	var response=return_global_ajax_value( po_id+'_'+color_id+'_'+size_id+'_'+item_id, 'check_color_size_qty', '', 'requires/fabric_booking_controller_v2');
	var response=response.split("_");
	po_size_qty=response[0];
	if(response[0]!="" || response[0]!=0){
		if(size_qty>po_size_qty)
		{
			alert('Over Qty Not Allowed');
			//$('#'+field_id).val('');
			for(var i=field_id_arr[2]; i<=colnum; i++)
			{
				//$('#'+field_id).val('');
				$('#gmtsqty_'+field_id_arr[1]+"_"+i).val('');
				$('#totalgmtsqty_'+field_id_arr[1]).val('');
			}
		}
	}
}
function other_work(){
	document.getElementById('cbo_order_id').disabled=false
	document.getElementById('cbo_fabricdescription_id').disabled=false
	document.getElementById('cbo_gmt_item_id').disabled=false
}
function loadrelateddata(){
	var cbo_order_id=document.getElementById('cbo_order_id').value;
	var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
	var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
	var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	get_php_form_data( cbo_order_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbo_gmt_item_id, "load_drop_down_fabric", "requires/fabric_booking_controller_v2" );
	loadmatrix()
}
function generate_fabric_report(type){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else{
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true){
			show_yarn_rate="1";
		}
		else{
			show_yarn_rate="0";
		} 
		$report_title=$( "div.form_caption" ).html();
		var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
		freeze_window(5);
		http.open("POST","requires/fabric_booking_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}	
}

function generate_fabric_report_reponse(){
	if(http.readyState == 4){
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;
		release_freezing();
	}
}
</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate();check_month_setting();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:950px;">
            <legend>Fabric Booking </legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right"></td> 
                    <td align="right"></td>    
                    <td  width="130" height="" align="right" class="must_entry_caption"> Booking No </td>                            
                    <td  width="170" >
                    <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/fabric_booking_controller_v2.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                    <td align="right" width="130" >
                    <input type="hidden" id="id_approved_id">
                    <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                    </td>
                    <td> </td>
                </tr>
                <tr>
                    <td  align="right">Company Name</td>
                    <td>
                    <? 
                    echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/fabric_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier();",0,"" );
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
                    <input style="width:160px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  /> 
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
                    echo create_drop_down( "cbo_fabric_natu", 172, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");		
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
                    <input class="text_boxes" type="text" style="width:97%;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/fabric_booking_controller_v2.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                    <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                    </td>                                
                </tr>
                <tr>
                    <td  width="130" align="right">Delivery Date</td>
                    <td width="170">
                    <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" onChange="compare_date()"/>	
                    <input class="datepicker" type="hidden" style="width:160px" name="txt_tna_date" id="txt_tna_date"/>	
                    </td>
                    <td  align="right">Pay Mode</td>
                    <td>
                    <?
                    echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "load_drop_down( 'requires/fabric_booking_controller_v2', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" );
                    ?> 
                    </td>
                    <td  align="right">Supplier Name</td>
                    <td id="sup_td">
                    <?
                    echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
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
                    </td>
                    <td align="right">Booking Percent</td>   
                    <td>
                    <input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_booking_percent" id="txt_booking_percent" value="100"  />  
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
                    <td align="left" height="10" colspan="5">
                    <input class="text_boxes" type="text" maxlength="200" style="width:97%;"  name="txt_fabriccomposition" id="txt_fabriccomposition"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td align="left" height="10" colspan="6">
                    <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/fabric_booking_controller_v2.php?action=terms_condition_popup','Terms Condition')" />
                    <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/fabric_booking_controller_v2.php?action=rmg_process_loss_popup','Process Loss %')" />
                    <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" /> 
                    </td>
                </tr>
                
                <tr>
                    <td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="top" id="app_sms3" style="font-size:18px; color:#F00">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    <? 
					$date=date('d-m-Y'); 
					echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2016*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*txt_booking_date,".$date."')",1) ; 
					?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" height="10">
                    <input type="hidden" class="" style="width:200px" id="selected_id_for_delete">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
        <fieldset style="width:950px;">
            <legend>Details</legend>
            <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th  width="210">PO No</th>
                        <th  width="210">Gmt. Item</th>
                        <th  width="400">Fabric Description</th>
                        <th  width="100">Dia</th>
                    </tr> 
                </thead>
                    <tr>
                        <td height="22" id="order_drop_down_td" >
                        <? 
                        echo create_drop_down( "cbo_order_id", 210, $blank_array,"", 1, "--Select--", $selected, "" ); 
                        ?>
                        </td>
                        <td id="gmt_item_td">
                        <?  echo create_drop_down( "cbo_gmt_item_id", 210, $blank_array, "",1," -- Select Item --", "", "","","" );?>
                        </td>
                        <td id="fabricdescription_id_td">
                        <?  echo create_drop_down( "cbo_fabricdescription_id", 400, $blank_array,"", 1, "--Select--", $selected, "" ); ?>
                        </td>
                        <td>
                        <input class="text_boxes" type="text" name="newdia" id="newdia"  value=""  style="width:100px"/>
                        <input class="text_boxes" type="hidden" name="saveddia" id="saveddia"  value=""  style="width:100px"/>
                        </td>
                    </tr> 
                    <tr>
                        <td align="center" colspan="12" id="breackdown_form">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12" valign="middle" class="button_container">
                        <input class="text_boxes" name="txt_update_dtls_id" id="txt_update_dtls_id" type="hidden" value=""  style="width:100px"/>
                        <? 
                        echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','breackdown_form','','','other_work()','')",2) ; 
                        ?>
                        <input type="button" value="Print Booking" onClick="generate_fabric_report('show_fabric_booking_report')"  style="width:100px" name="print" id="print" class="formbutton" /> 
                        <div id="pdf_file_name"></div>
                        </td>
                        </tr>
                         <tr>
                        <td align="center" colspan="12" id="list_view">
                        </td>
                    </tr>
            </table>
        </fieldset>
    </form>
</div>
<div id="booking_list_view"></div>
<div style="display:none" id="data_panel"></div>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</body>
</html>