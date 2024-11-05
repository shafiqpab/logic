<?php
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Purpose			         :  This form will create Trims Booking
Functionality	         :	
JS Functions	         :
Created by		         :  MONZU 
Creation date 	         :  27-12-2012
Requirment Client        :  Fakir Apperels
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
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Trims Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][43]) ;
	echo "var field_level_data= ". $data_arr . ";\n";
?>

var permission='<? echo $permission; ?>';

function openmypage(page_link,title){
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if(txt_booking_no==""){
		alert("Save Booking First");
		return;
	}
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	var trim_type=document.getElementById('cbo_trim_type').value;
	var cbo_budget_version=document.getElementById('cbo_budget_version').value;
	
	var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
	page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_budget_version='+cbo_budget_version;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1190px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function(){
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		var theemail_po_id=this.contentDoc.getElementById("po_id");
		var theemail_po_no=this.contentDoc.getElementById("po_no");
		if (theemail.value!=""){
			freeze_window(5);
			reset_form('','booking_list_view','txt_po_no*txt_po_id*txt_trim_group_id*cbo_trim_precost_id*cbo_colorsizesensitive*cbo_uom*txt_country*txt_country_name*txt_quantity*txt_avg_price*txt_amount*txt_req_amt*txt_update_dtls_id','','','')
			document.getElementById('txt_job_no').value=theemail.value;
			document.getElementById('txt_po_id').value=theemail_po_id.value;
			document.getElementById('txt_po_no').value=theemail_po_no.value;
			get_php_form_data(theemail_po_id.value+"_save_"+cbo_company_name+"_"+cbo_pay_mode+"_"+trim_type, "set_delivery_date_from_tna", "requires/trims_booking_controller" );
			var tna_date=$('#txt_tna_date').val();
            compare_date(2);
			if(cbo_item_from_precost==1){
				load_drop_down( 'requires/trims_booking_controller', theemail.value+'_'+document.getElementById('cbo_supplier_name').value+'_'+theemail_po_id.value, 'load_drop_down_trim_precost_id', 'trim_group_td' )
			}
			load_drop_down( 'requires/trims_booking_controller', theemail.value, 'load_drop_down_gmt_item', 'gmt_item_td' );
			set_button_status(0, permission, 'fnc_trims_booking_dtls',2);
			release_freezing();
		}
	}
}

function set_precost_data(trim_precost_id,type){
   if(type==1){
   reset_form('','','cbo_colorsizesensitive*cbo_uom*txt_quantity*txt_avg_price*txt_amount*txt_req_amt*txt_update_dtls_id','','','')//txt_delevary_date
   }
    var cbo_item_from_precost = document.getElementById('cbo_item_from_precost').value;
	var txt_po_id = document.getElementById('txt_po_id').value;
	var txt_country=document.getElementById('txt_country').value;
	var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	var cbo_currency=document.getElementById('cbo_currency').value;
	var budget_version=document.getElementById('cbo_budget_version').value;
	if(txt_po_id==""){
		alert("Insert Po NO");
		document.getElementById('cbo_trim_precost_id').value=0;
		return;
	}
	if(txt_country==""){
		alert("Insert Country");
		document.getElementById('cbo_trim_precost_id').value=0;
		return;
	}
	if(cbo_gmt_item_id==0){
		alert("Select Gmt Item");
		document.getElementById('cbo_trim_precost_id').value=0;
		return;
	}
	//alert(budget_version);
	if(budget_version==1)
	{
		get_php_form_data(trim_precost_id+"_"+txt_po_id+"_"+txt_country+"_"+cbo_gmt_item_id+"_"+cbo_currency+"_"+type+"_"+cbo_item_from_precost, "set_precost_data", "requires/trims_booking_controller" );
	}
	else
	{
		get_php_form_data(trim_precost_id+"_"+txt_po_id+"_"+txt_country+"_"+cbo_gmt_item_id+"_"+cbo_currency+"_"+type+"_"+cbo_item_from_precost, "set_precost_data", "requires/trims_booking_controller2" );
	}
}

function open_country_popup(page_link,title){
	var txt_po_id=document.getElementById('txt_po_id').value;
	var txt_country=document.getElementById('txt_country').value
	var txt_country_name=document.getElementById('txt_country_name').value
	page_link=page_link+'&txt_po_id='+txt_po_id+'&txt_country='+txt_country+'&txt_country_name='+txt_country_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=350px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function(){
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("txt_selected_id");
		var theemailname=this.contentDoc.getElementById("txt_selected_name");
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById('txt_country').value=theemail.value;
			document.getElementById('txt_country_name').value=theemailname.value;
			release_freezing();
		}
	}
}

function fnc_generate_booking(){
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var txt_job_no=document.getElementById('txt_job_no').value;
	var txt_po_id=document.getElementById('txt_po_id').value;
	var cbo_trim_precost_id=document.getElementById('cbo_trim_precost_id').value;
	var txt_trim_group_id=document.getElementById('txt_trim_group_id').value;
	var txt_update_dtls_id=document.getElementById('txt_update_dtls_id').value;
	var cbo_colorsizesensitive=document.getElementById('cbo_colorsizesensitive').value;
	var txt_req_quantity=document.getElementById('txt_req_quantity').value;
	var txt_avg_price=document.getElementById('txt_avg_price').value;
	var txt_country=document.getElementById('txt_country').value;
	var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	var txt_pre_des=document.getElementById('txt_pre_des').value;
	var txt_pre_brand_sup=document.getElementById('txt_pre_brand_sup').value;
	var txt_exchange_rate_dtls=document.getElementById('txt_exchange_rate_dtls').value;	
	var cbo_isshort=document.getElementById('cbo_isshort').value;

	if(txt_po_id==""){
	alert("Insert Po NO");
	return;
	}
	if(txt_country==""){
	alert("Insert Country");
	return;
	}
	if(cbo_gmt_item_id==0){
	alert("Select Gmt Item");
	return;
	}
	if(cbo_trim_precost_id==0){
	alert("Select Item Group");
	return;
	}
	var data="action=generate_fabric_booking&garments_nature="+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&cbo_gmt_item_id='+cbo_gmt_item_id+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+'&txt_exchange_rate_dtls='+txt_exchange_rate_dtls+'&cbo_isshort='+cbo_isshort;
	http.open("POST","requires/trims_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_booking_reponse;
}

function fnc_generate_booking_reponse(){
	if(http.readyState == 4) {
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_sum_value( 'qty_sum', 'qty_' );
		set_sum_value( 'woqty_sum', 'woqny_' );
		set_sum_value( 'amount_sum', 'amount_' );
		set_sum_value( 'pcs_sum', 'pcs_' );
		set_all_onclick();
	}
}

function openmypage_booking(page_link,title){
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var page_link=page_link+'&company_id='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("txt_booking");
		if (theemail.value!="")
		{
			reset_form('trimsbooking_1*trimsbooking_2','listview_dtls*booking_list_view','id_approved_id','','','copy_val');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/trims_booking_controller" );
			$('#cbo_isshort').attr('disabled',true);
			set_button_status(1, permission, 'fnc_trims_booking',1);
			fnc_show_booking();
			
			var company_id = $("#cbo_company_name").val();
			get_php_form_data( company_id, 'populate_field_level_access_data', 'requires/trims_booking_controller' );
		}
	}
}

Date.prototype.yyyymmdd = function() {         
        var yyyy = this.getFullYear().toString();                                    
        var mm = (this.getMonth()+1).toString();//getMonth() is zero-based         
        var dd  = this.getDate().toString();             
        //return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
		return (dd[1]?dd:"0"+dd[0])+ '-' + (mm[1]?mm:"0"+mm[0])+ '-' + yyyy ;
   };  

function open_terms_condition_popup(page_link,title){
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no==""){
		alert("Save The Booking First")
		return;
	}	
	else{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}
function fnc_trims_booking( operation ){
	
	if(document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		return;
	}
	var delete_type=0;
	if(operation==2)
	{
		var al_magg="Press OK to delete master and details part.\n Press CANCEL to delete only details part.";
		var r=confirm(al_magg);
		
		if(r==true)
		{
			delete_type=1;
		}
		else
		{
			delete_type=0;
		}
		var q=confirm("Press OK to Delete Or Press Cancel");
		if(q==false){
			release_freezing();
			return;
		}
	}
	
	var data_all="";
	if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*cbo_pay_mode*txt_exchange_rate*cbo_trim_type','Company Name*Buyer Name*Booking Date*Pay Mode*Exchange Rate*Trim Type')==false){
		return;
	}	
	else{
		 data_all=data_all+get_submitted_data_string('txt_booking_no*txt_job_no*cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*cbo_isshort*txt_remarks*cbo_ready_to_approved*cbo_trim_type*cbo_item_from_precost*cbo_budget_version*update_id',"../../")+"&delete_type="+delete_type;
	}
	var data="action=save_update_delete&operation="+operation+data_all;
	freeze_window(operation);
	http.open("POST","requires/trims_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}
	 
function fnc_trims_booking_reponse(){
	if(http.readyState == 4){
		 var reponse=trim(http.responseText).split('**');
		 if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}
		 if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='delQtyExeed'){
			alert("Quantity Exeed Delivery Quantity. Delivery ID:"+trim(reponse[1])+"\n So Update/Delete Not Possible");
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='orderFound'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Delete Not Allowed");
		    release_freezing();
		    return;
		}
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==3){
			document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
			$('#cbo_isshort').attr('disabled',true);
			$('#cbo_item_from_precost').attr('disabled',true);
			$('#cbo_budget_version').attr('disabled',true);
			set_button_status(1, permission, 'fnc_trims_booking',1);
		}
		if(trim(reponse[0])==2){
			location.reload();
		}
		release_freezing();
		var company_id = $("#cbo_company_name").val();
		get_php_form_data( company_id, 'populate_field_level_access_data', 'requires/trims_booking_controller' );
	}
}

function fnc_trims_booking_dtls( operation ){
	if(document.getElementById('id_approved_id').value==1){
		alert("This booking is approved")
		return;
	}
	
	var txt_po_id=document.getElementById('txt_po_id').value;
	var wo_quantity=document.getElementById('txt_quantity').value*1;
	var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	var txt_trim_group_id=document.getElementById('txt_trim_group_id').value;
	var cbo_trim_precost_id=document.getElementById('cbo_trim_precost_id').value;
	var txt_exchange_rate_dtls=document.getElementById('txt_exchange_rate_dtls').value*1;
	var txt_quantity=document.getElementById('txt_quantity').value*1;
	var saved_quantity=$('#txt_quantity').attr('saved_quantity')*1;
	
	var txt_amount=document.getElementById('txt_amount').value*1;
	var saved_amount=$('#txt_amount').attr('saved_amount')*1;
	
	var txt_req_quantity=document.getElementById('txt_req_quantity').value*1;
	var conversion_factor=$('#txt_req_quantity').attr('conversion_factor')*1;
	var req_qnty_cons_uom=$('#txt_req_quantity').attr('req_qnty_cons_uom')*1;
	var req_amount_cons_uom=$('#txt_req_quantity').attr('req_amount_cons_uom')*1;
	
	var exeed_budge_qty_percent=document.getElementById('exeed_budge_qty').value*1;
	var exeed_budge_amount_percent=document.getElementById('exeed_budge_amount').value*1;
	var amount_exceed_level=document.getElementById('amount_exceed_level').value*1;
	var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value*1;
	
	
	var booking_qnty_and_amount_trim_group=return_global_ajax_value(txt_po_id+"_"+txt_trim_group_id+"_"+cbo_trim_precost_id+"_"+cbo_gmt_item_id, 'booking_qnty_and_amount_trim_group', '', 'requires/trims_booking_controller');
	
	var booking_qnty_and_amount_trim_group_data=booking_qnty_and_amount_trim_group.split('_');
	var booking_qnty=booking_qnty_and_amount_trim_group_data[0]*1;
	var booking_amount=booking_qnty_and_amount_trim_group_data[1]*1;
	
	var txt_req_quantity_with_percent=number_format_common(((exeed_budge_qty_percent/100)*req_qnty_cons_uom)+req_qnty_cons_uom,5,0);
	var txt_req_amount_with_percent=number_format_common(((exeed_budge_amount_percent/100)*req_amount_cons_uom)+req_amount_cons_uom,5,0);
	
	
	
	if(operation==0 && cbo_item_from_precost==1){
	   if(((booking_qnty+txt_quantity)*conversion_factor)>txt_req_quantity_with_percent*1)
		{
			alert("Budget Qty Over");
			return;
		}	
	}

	if(operation==1 && cbo_item_from_precost==1){
		if((((booking_qnty-saved_quantity)+txt_quantity)*conversion_factor)>txt_req_quantity_with_percent*1)
		{
			alert("Budget Qty Over");
			return;
		}
	}
	//wo_quantity
	if(wo_quantity=="" || wo_quantity==0)
	{
		alert("WO Qty");
		//txt_quantity
		$('#txt_quantity').focus();
			return;
	}
	
	/*var txt_delivery_date=$('#txt_delevary_date').val();
	
		if(date_compare($('#txt_booking_date').val(), txt_delivery_date)==false)
		{
			alert("Delivery Date Can not Be Greater Than Booking Date");
			return;
		}*/

	if(amount_exceed_level==2 && cbo_item_from_precost==1){
		if(operation==0){
			booking_amount=((booking_amount+txt_amount)/conversion_factor);
			booking_amount=number_format_common(booking_amount/txt_exchange_rate_dtls,5,0);
		   if(booking_amount*1>txt_req_amount_with_percent*1)
			{
				alert("Budget Amount Over");
				return;
			}	
		}
	
		if(operation==1){
			booking_amount=(((booking_amount-saved_amount)+txt_amount)/conversion_factor);
			booking_amount=number_format_common(booking_amount/txt_exchange_rate_dtls,5,0);
			if(booking_amount*1>txt_req_amount_with_percent*1)
			{
				alert("Booking Amount "+booking_amount+"\n Budget Amount \n"+txt_req_amount_with_percent+"\ n Budget Amount Over");
				return;
			}
		}
	}
	
	if(amount_exceed_level==1){
		
		var budget_version_id=$('#cbo_budget_version').val();
		if(budget_version_id==1)
		{	
		var booking_amount_and_budget_amount_po_level=return_global_ajax_value(txt_po_id+"_"+txt_trim_group_id+"_"+cbo_trim_precost_id, 'booking_amount_and_budget_amount_po_level', '', 'requires/trims_booking_controller');
		}
		else
		{
			var booking_amount_and_budget_amount_po_level=return_global_ajax_value(txt_po_id+"_"+txt_trim_group_id+"_"+cbo_trim_precost_id, 'booking_amount_and_budget_amount_po_level', '', 'requires/trims_booking_controller2');
		}
		
		//alert(booking_amount_and_budget_amount_po_level);
		var booking_amount_and_budget_amount_po_level=booking_amount_and_budget_amount_po_level.split('_');
		var budget_amount_po_level=booking_amount_and_budget_amount_po_level[0]*1;
		var booking_amount_po_level=booking_amount_and_budget_amount_po_level[1]*1;
		
		var budget_amount_po_level_with_percent=number_format_common(((exeed_budge_amount_percent/100)*budget_amount_po_level)+budget_amount_po_level,5,0);
		if(operation==0)
		{
			booking_amount_po_level=((booking_amount_po_level+txt_amount)/conversion_factor);
			booking_amount_po_level=number_format_common(booking_amount_po_level/txt_exchange_rate_dtls,5,0);
			//alert(booking_amount_po_level)
			//alert(booking_amount_po_level+'=='+budget_amount_po_level_with_percent);
			
		   if(booking_amount_po_level*1 > budget_amount_po_level_with_percent*1)
			{
				alert("Budget Amount Over");
				return;
			}	
		}
	
		if(operation==1)
		{
			
			booking_amount_po_level=(((booking_amount_po_level-saved_amount)+txt_amount)/conversion_factor);
			booking_amount_po_level=number_format_common(booking_amount_po_level/txt_exchange_rate_dtls,5,0);
			//alert(booking_amount_po_level+"=="+budget_amount_po_level_with_percent);
			if(booking_amount_po_level*1>budget_amount_po_level_with_percent*1)
			{
				alert("Budget Amount Over");
				return;
			}
		}
	}
	var data_all="";
	if (form_validation('txt_booking_no*txt_po_no*cbo_trim_precost_id*cbo_supplier_name*txt_delevary_date','Booking no*Po No*Trims Group*Supplier Name*Delivery Date')==false)
	{
		return;
	}
	else
	{
		 data_all=data_all+get_submitted_data_string('txt_booking_no*txt_job_no*cbo_isshort*txt_po_no*txt_po_id*txt_trim_group_id*cbo_gmt_item_id*cbo_trim_precost_id*cbo_colorsizesensitive*cbo_uom*txt_delevary_date*txt_country*txt_quantity*txt_avg_price*txt_amount*txt_update_dtls_id*cbo_supplier_name*txt_exchange_rate_dtls*txt_delivery_date*cbo_item_from_precost*txt_req_amt*cbo_company_name*update_id',"../../");
	}
	
	var row_num=$('#tbl_consmption_cost tbody tr').length;
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('des_'+i,'Description')==false )
		{
			return;
		}
		else
		{
			data_all=data_all+get_submitted_data_string('pocolorid_'+i+'*gmtssizesid_'+i+'*des_'+i+'*brndsup_'+i+'*itemcolor_'+i+'*itemsizes_'+i+'*qty_'+i+'*excess_'+i+'*woqny_'+i+'*rate_'+i+'*amount_'+i+'*colorsizetableid_'+i+'*pcs_'+i+'*updateid_'+i,"../../",i);
		}
	}
	//alert(row_num);
	var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","requires/trims_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_dtls_reponse;
}
	 
function fnc_trims_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
		var reponse=trim(http.responseText).split('**');
		if(trim(reponse[0])=='appNSet')
		{
			alert("Budget is not approved of this Order. Please approve the budget and try again.")
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='delQtyExeed'){
			alert("Quantity Exeed Delivery Quantity. Delivery ID:"+trim(reponse[1])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		/*if(trim(reponse[0])=='orderFound'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Delete Not Allowed");
		    release_freezing();
		    return;
		}*/
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==3)
		{
			reset_form('','booking_list_view','cbo_trim_precost_id*txt_trim_group_id*cbo_colorsizesensitive*cbo_uom*txt_req_quantity*txt_exchange_rate_dtls*txt_quantity*txt_avg_price*txt_amount*txt_req_amt*txt_update_dtls_id','','','')
			//  reset_form('','booking_list_view','txt_po_no*txt_po_id*txt_trim_group_id*cbo_trim_precost_id*cbo_gmt_item_id*cbo_colorsizesensitive*cbo_uom*txt_delevary_date*txt_req_quantity*txt_exchange_rate_dtls*txt_country*txt_country_name*txt_quantity*txt_avg_price*txt_amount*txt_update_dtls_id','','','')
			set_button_status(0, permission, 'fnc_trims_booking_dtls',2);
			fnc_show_booking()
			document.getElementById('txt_update_dtls_id').value="";
			$("#cbo_colorsizesensitive").attr("disabled",false);
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_buyer_name").attr("disabled",true);
			$("#cbo_supplier_name").attr("disabled",true);
		}
		if(trim(reponse[0])==2)
		{
			reset_form('','booking_list_view','txt_po_no*txt_po_id*txt_trim_group_id*cbo_trim_precost_id*cbo_colorsizesensitive*cbo_uom*txt_delevary_date*txt_country*txt_country_name*txt_quantity*txt_avg_price*txt_amount*txt_req_amt*txt_update_dtls_id','','','')
			fnc_show_booking()
			$("#cbo_colorsizesensitive").attr("disabled",false);
			set_button_status(0, permission, 'fnc_trims_booking_dtls',2);
		}
		release_freezing();
	}
}

function fnc_show_booking()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var data='action=show_trim_booking&txt_booking_no='+txt_booking_no+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost;
		//freeze_window(5);
		http.open("POST","requires/trims_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
}

function fnc_show_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('listview_dtls').innerHTML=http.responseText;
	}
}
function load_dtls_data(booking_dtls_id,req_qty)
{
	get_php_form_data( booking_dtls_id, "populate_data_dtls_from", "requires/trims_booking_controller" );
	document.getElementById('txt_req_quantity').value=req_qty;
	fnc_generate_booking()
	$("#cbo_colorsizesensitive").attr("disabled",true);
	set_button_status(1, permission, 'fnc_trims_booking_dtls',2);
}
//=====================================

function poportionate_qty(qty)
{
	var po_qty=document.getElementById('po_qty').value;
	var txtwoq_qty=document.getElementById('txtwoq_qty').value;
    var rowCount = $('#tbl_consmption_cost tr').length-2;
	for(var i=1; i<=rowCount; i++)
	{
	// var pcs=$('#pcs_'+i).val();
	 var pcs=$('#pcsset_'+i).val();
	 var txtwoq_cal =number_format_common((txtwoq_qty/po_qty) * (pcs),5,0);
	 $('#qty_'+i).val(txtwoq_cal);
	 calculate_requirement(i)
	}
	set_sum_value( 'qty_sum', 'qty_' )
}

function calculate_requirement(i)
{
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('qty_'+i).value)*1;
	var processloss=(document.getElementById('excess_'+i).value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('woqny_'+i).value= WastageQty;
		set_sum_value( 'woqty_sum', 'woqny_' )
		calculate_amount(i)
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='qty_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	if(des_fil_id=='excess_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	if(des_fil_id=='woqty_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	
	if(des_fil_id=='amount_sum')
	{
	var ddd={dec_type:6,comma:0,currency:0};
	}
	
	if(des_fil_id=='pcs_sum')
	{
	var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-2;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
}

function copy_value(value,field_id,i)
{
	  var copy_val=document.getElementById('copy_val').checked;
	  var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
	  var pocolorid=document.getElementById('pocolorid_'+i).value;
	  var rowCount = $('#tbl_consmption_cost tr').length-2;
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
		  
		  if(field_id=='des_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='brndsup_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='itemcolor_')
		  {
			if( pocolorid==document.getElementById('pocolorid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  
		  if(field_id=='itemsizes_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='qty_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j) 
			set_sum_value( 'qty_sum', 'qty_'  );
		  }
		  if(field_id=='excess_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j)  
		  }
		  if(field_id=='rate_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_amount(j)  
		  }
		}
	  }
}

function calculate_amount(i) 
{
	var rate=(document.getElementById('rate_'+i).value)*1;
	var woqny=(document.getElementById('woqny_'+i).value)*1;
	var amount=number_format_common((rate*woqny),5,0);
	document.getElementById('amount_'+i).value=amount;
	set_sum_value( 'amount_sum', 'amount_' );
	//var amount_sum=document.getElementById('amount_sum').value;
	calculate_avg_rate()
	
}
function calculate_avg_rate()
{
	var woqty_sum=document.getElementById('woqty_sum').value;
	var amount_sum=document.getElementById('amount_sum').value;
	var avg_rate=number_format_common((amount_sum/woqty_sum),6,0);
	//alert(avg_rate);
	document.getElementById('rate_sum').value=avg_rate;
	document.getElementById('txt_quantity').value=woqty_sum;
	document.getElementById('txt_avg_price').value=avg_rate;
	document.getElementById('txt_amount').value=amount_sum;
}

function set_delv_date()
{
	document.getElementById('txt_delevary_date').value=document.getElementById('txt_delivery_date').value
}
//===========================


function generate_trim_report(action)
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		}
		
		var budget_version_id=$('#cbo_budget_version').val();
		$report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_isshort',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment;
		//freeze_window(5);
		if(budget_version_id==1)
		{
			http.open("POST","requires/trims_booking_controller.php",true);
		}
		else
		{
			http.open("POST","requires/trims_booking_controller2.php",true);
		}
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
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

/*function popolate_job_data(data)
{
  var displayValueCompany=document.getElementById('displayValueCompany').value;
  var displayValueYear=document.getElementById('displayValueYear').value;
  $job_no=trim(displayValueCompany)+'-'+trim(displayValueYear)+'-'+padToFive(trim(data));
  get_php_form_data($job_no, "populate_data_from_search_popup", "requires/trims_booking_controller" );

}*/
function padToFive(number) {
  if (number<=99999) { number = ("0000"+number).slice(-5); }
  return number;
}

	function currency_populate(curr_id)
	{
		if(curr_id==1)
		{
			$('#txt_exchange_rate').attr('placeholder','Write');
			$('#txt_exchange_rate').removeAttr('disabled','disabled');
			document.getElementById('txt_exchange_rate').value='';
			
		}
		else if(curr_id==2)
		{
			
			document.getElementById('txt_exchange_rate').value=1;
			//$('#txt_exchange_rate').removeAttr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
			
		}
		else if(curr_id==3)
		{
			$('#txt_exchange_rate').attr('placeholder','Write');
			$('#txt_exchange_rate').removeAttr('disabled','disabled');
			document.getElementById('txt_exchange_rate').value='';
		}
		else if(curr_id==4)
		{
			
			$('#txt_exchange_rate').attr('placeholder','Write');
			$('#txt_exchange_rate').removeAttr('disabled','disabled');
			document.getElementById('txt_exchange_rate').value='';
		}
	}
	
//for print button
function print_report_button_setting(report_ids) 
{
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==13)
			{
			$("#print_booking1").show();	 

			}
			if(report_id[k]==14)
			{
			$("#print_booking2").show();	 
			}
			if(report_id[k]==15)
			{
			$("#print_booking3").show();	 

			}
			if(report_id[k]==16)
			{
			$("#print_booking4").show();	 
			}
			if(report_id[k]==17)
			{
			$("#print_booking1").show();	 
			}
			if(report_id[k]==18)
			{
			$("#print_booking2").show();	 
			}
			if(report_id[k]==19)
			{
			$("#print_booking3").show();	 

			}
		}
}

function compare_date(str)
{
	var txt_delevary_date_data=document.getElementById('txt_delevary_date').value;
	var txt_tna_date_data=document.getElementById('txt_tna_date').value;
	var booking_date=document.getElementById('txt_booking_date').value;
	if(txt_delevary_date_data=='')
	{
		txt_delevary_date_data=document.getElementById('txt_tna_date').value;
	}
	txt_delevary_date_data= txt_delevary_date_data.split('-');
	var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
	txt_tna_date_data = txt_tna_date_data.split('-');
	var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];
	booking_date = booking_date.split('-');
	var booking_date_inv=booking_date[2]+"-"+booking_date[1]+"-"+booking_date[0];
	
	var txt_delevary_date = new Date(txt_delevary_date_inv);
    var txt_tna_date = new Date(txt_tna_date_inv);
	var txt_booking_date = new Date(booking_date_inv);
	var lib_tna_intregrate=$('#lib_tna_intregrate').val();
	
	var cbo_isshort=$('#cbo_isshort').val();
	if(cbo_isshort==1)
	{
		if(txt_delevary_date < txt_booking_date)
		{
			//salert('Delivery Date Not Allowed Less than Booking Date');
			document.getElementById('txt_delevary_date').value='';
		}
	}
	else
	{
		if(str==1)
		{
			if(txt_tna_date_data !='')
			{
				if( lib_tna_intregrate==1)
				{
					if(txt_delevary_date > txt_tna_date)
					{
						alert('Delivery Date Not Allowed Greater Than TNA Date');
						if(txt_tna_date>txt_booking_date)
						{
							document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
						}
						else
						{
							document.getElementById('txt_delevary_date').value='';
						}
						
						//return;
					}
					else if((txt_delevary_date < txt_booking_date) ||  (txt_booking_date > txt_tna_date))
					{
						alert('Delivery Date Not Allowed Less than Booking Date');
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
						document.getElementById('txt_delevary_date').value='';
					}
				}
				else
				{
					if((txt_delevary_date < txt_booking_date))
					{
						alert('Delivery Date Not Allowed Less than Booking Date');
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
						document.getElementById('txt_delevary_date').value='';
					}
				}
			}
			else
			{
				if(txt_delevary_date < txt_booking_date )
				{
					alert('Delivery Date Not Allowed Less than Booking Date');
					document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
				}
			}
		}
		if(str==2)
		{
			if(lib_tna_intregrate==1)
			{
				if(txt_tna_date !='')
				{
					if(txt_tna_date < txt_booking_date)
					{
						alert('TNA Date is Less than Booking Date');
						document.getElementById('txt_delevary_date').value='';
						//document.getElementById('txt_tna_date').value='';
						return;
					}
					else
					{
						document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;return;
					}
				}
			}
		}
	}
}
</script>
 
</head> 
 
<body onLoad="set_hotkey();currency_populate($('#cbo_currency').val());">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="trimsbooking_1"  autocomplete="off" id="trimsbooking_1">
            	<fieldset style="width:950px;">
                <legend title="V3">Trims Booking &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   <font id="app_sms" style="color:#F00"></font></legend>
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="" width="130" ></td>
                        <td align="" valign="top" width="170"></td>
                        <td  width="130" height="" align="right" class="must_entry_caption">Booking No </td>              
                        <td  width="170" >
                            <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/trims_booking_controller.php?action=trims_booking_popup','Trims Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                        </td>
                        <td align="" width="130" >
                        <input type="hidden" id="id_approved_id" >
                        <input type="hidden" id="exeed_budge_qty">
                        <input type="hidden" id="exeed_budge_amount">
                        <input type="hidden" id="amount_exceed_level">
                        <input type="hidden" id="update_id">
                        </td>
                        <td align=""></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company Name</td>              
                        <td>
                        <? 
						// load_drop_down( 'requires/trims_booking_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                        echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/trims_booking_controller' );load_drop_down( 'requires/trims_booking_controller', document.getElementById('cbo_pay_mode').value+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' );","","" );
                        ?>	
                        <input type="hidden" id="report_ids">  
                        <input style="width:160px;" type="hidden" class="text_boxes" placeholder="Display" name="txt_job_no" id="txt_job_no" readonly />                              
                        </td> 
                        <td align="right" class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" );
                        
                        ?>	  
                        </td>
                         <td  align="right" class="must_entry_caption">Pay Mode</td>
                        <td>
                        <?
                        echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", $selected, "load_drop_down( 'requires/trims_booking_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )","" );
                        ?> 
                        </td>
                         
                    </tr>
                    <tr>
                        <td  align="right">Booking Date</td>
                        <td>
                        <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo date("d-m-Y"); ?>" disabled/>	
                        </td>
                        <td align="right">Currency</td>
                        <td>
                        <? 
                        echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "currency_populate(this.value);",0 );		
                        ?>	
                        </td>
                        <td align="right" class="must_entry_caption">Exchange Rate</td>
                        <td>
                        <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate"  />  
                        </td>
                    </tr>
                    <tr>
                        
                        <td  align="right" class="">Supplier Name</td>
                        <td id="supplier_td">
                        <?
                        //echo create_drop_down( "cbo_supplier_name", 172, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );//find_in_set(4,party_type) party_type in (4,5) --"select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name"
                        
                     // echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
					  echo create_drop_down( "cbo_supplier_name", 172, $blank_array,"", 1, "-- Select Supplier --", $selected, "","" );
					  
					  //  echo create_drop_down( "cbo_supplier_name", 172, "select sup.id,sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b where  sup.status_active=1 and sup.is_deleted=0 and sup.id in (Select supplier_id from  lib_supplier_party_type where party_type in (4,5)) order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );//find_in_set(4,party_type)
                        ?> 
                        </td>
                        <td  height="" align="right"> Source </td>              
                        <td>
                        <?
                        echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", 3, "","" );
                        ?>
                        </td>
                        <td align="right">Is Short</td>  
                        <td >
                        <?
                        echo create_drop_down( "cbo_isshort", 172, $yes_no,"", 0, "", 2, "","","" );
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Attention</td>   
                        <td align="left" height="10">
                        <input class="text_boxes" type="text" style="width:160px;"  name="txt_attention" id="txt_attention"/>
                        </td>
                        <td align="right">Remarks</td>  
                        <td align="left" height="10" colspan="3">
                        <input class="text_boxes" type="text" style="width:97%;"  name="txt_remarks" id="txt_remarks"/>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption">Trims Type</td>
                        <td>
                        <?
							echo create_drop_down( "cbo_trim_type", 172, $trim_type,"", "1", "---- Select ----", 0, "" );
						?>
                        </td>
                        <td align="right">Item From Pre-cost</td>  
                        <td align="center" height="10">
						<?
                          echo create_drop_down( "cbo_item_from_precost", 172, $yes_no,"", 0, "", "", "select_po_enable_disable(this.value);","","" );
                        ?>
                        </td>
                        <td align="right">Ready To Approved</td>  
                        <td align="center" height="10">
                        <?
                        echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
                        ?>
                        <input class="datepicker" type="hidden" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" onChange="set_delv_date()"/>	
                        </td>                        
                    </tr>
                     <tr>
                        <td  align="right">Budget Version</td>
						<td>				
							<?
							$pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
							echo create_drop_down( "cbo_budget_version", 172, $pre_cost_class_arr,"", 0, "-- Select Version --",2);
							?>	
						</td> 
					
						<td align="right">&nbsp;</td>  
                        <td align="center" height="10">
						<? 
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(43,'txt_booking_no','../../');
                        ?>
                        
                       <!-- <input type="button" id="set_button" class="image_uploader" style="width:172px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/trims_booking_controller.php?action=terms_condition_popup','Terms and Condition')" />-->
                        
                        </td>
                                              
                    </tr>
                    <tr>
                    	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                        <? 
                        $enable_disable="disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_isshort',0,'','')";
                        $date=date("d-m-Y");
                        echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('trimsbooking_1*trimsbooking_2','listview_dtls*booking_list_view','id_approved_id','txt_booking_date,$date*cbo_currency,2*cbo_pay_mode,1*cbo_source,3*cbo_isshort,2',$enable_disable,'copy_val')",1) ; ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" height="10">
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:100px;display:none" name="print_booking1" id="print_booking1" class="formbutton"  />
                        <input type="button" value="Print Booking1" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:100px;display:none" name="print_booking2" id="print_booking2" class="formbutton" />
                        <input type="button" value="Print Booking2" onClick="generate_trim_report('show_trim_booking_report2')"  style="width:100px;display:none" name="print_booking3" id="print_booking3" class="formbutton" />
                        <input type="button" value="Print Booking3" onClick="generate_trim_report('show_trim_booking_report3')"  style="width:100px;display:none" name="print_booking4" id="print_booking4" class="formbutton" />
                        <input type="button" value="Print Booking GR" onClick="generate_trim_report('show_trim_booking_report4')"   name="print_booking5" id="print_booking5" class="formbutton" />
                        </td>
                    </tr>
                    </table>
              </fieldset>
              </form>
              
              
        <form name="trimsbooking_2"  autocomplete="off" id="trimsbooking_2">
            <fieldset style="width:1100px;">
            <legend title="V3">Trims Booking Dtls &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </legend>
            
                <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                    <thead class="form_table_header">
                        <tr align="center" >
                            <th width="100">PO No</th>
                            <th width="100">Country</th>
                            <th width="150">Gmt. Item</th>
                            <th width="155">Trims Group</th>
                            <th width="140" >Sensivity</th>
                            <th width="60">UOM</th>
                            <th width="70" class="must_entry_caption">Delivery Date</th>
                            <th width="65">Req.Quantity</th>
                            <th width="60">Quantity</th>
                            <th width="50">Exchange Rate</th>
                            <th width="50">Avg. Price</th>
                            <th width="65">Amount</th>
                        </tr> 
                    </thead>
                    <tr>
                        <td>
                            <input class="text_boxes" name="txt_po_no" id="txt_po_no" type="text" value=""  style="width:100px"  onDblClick="openmypage('requires/trims_booking_controller.php?action=order_popup','Job/Order Selection Form')" placeholder="Browse" readonly />
                            <input class="text_boxes" name="txt_po_id" id="txt_po_id" type="hidden" value=""  style="width:30px"/>
                            <input class="text_boxes" name="txt_trim_group_id" id="txt_trim_group_id" type="hidden" value="" style="width:30px"/>
                        </td>
                        <td>
                            <input name="txt_country_name" id="txt_country_name" onDblClick="open_country_popup('requires/trims_booking_controller.php?action=open_country_popup','Country')" class="text_boxes" type="text"  style="width:100px"  placeholder="Dbl. Click"/>
                            <input name="txt_country" id="txt_country" class="text_boxes" type="hidden"  style="width:100px" />
                        </td>
                        <td id="gmt_item_td"><? echo create_drop_down( "cbo_gmt_item_id", 150, $blank_array, "",1," -- Select Item --", "", "","","" );?></td>
                        
                        <td id="trim_group_td"><? echo create_drop_down( "cbo_trim_precost_id", 155, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and status_active=1 order by item_name", "id,item_name",1," -- Select Item --", "", "set_precost_data(this.value,1);fnc_generate_booking()","","" );?>
                        </td>
                        <td><? echo create_drop_down( "cbo_colorsizesensitive", 140, $size_color_sensitive,"", 1, "--Select--", "", "fnc_generate_booking()","","" ); ?></td>
                        <td><? echo create_drop_down( "cbo_uom", 60, $unit_of_measurement, 0, "", $selected, "","",1 ); ?></td>
                        <td>
                            <input name="txt_delevary_date" id="txt_delevary_date" class="datepicker" type="text" style="width:70px;"  onChange="compare_date(1)" readonly/>
                            <input name="txt_tna_date" id="txt_tna_date" class="datepicker" type="hidden" style="width:70px;"  readonly/>
                        </td>
                        <td>
                        	<input name="txt_req_quantity" id="txt_req_quantity" class="text_boxes_numeric" type="text" style="width:60px" readonly />
                            <input class="text_boxes" name="txt_req_amt" id="txt_req_amt" type="hidden" value="" style="width:30px"/>
                        </td>
                        <td><input name="txt_quantity" id="txt_quantity"  class="text_boxes_numeric" type="text"  style="width:60px"  readonly /></td>
                        <td><input name="txt_exchange_rate_dtls" id="txt_exchange_rate_dtls" class="text_boxes_numeric" type="text"   style="width:50px" readonly /></td>
                        <td><input name="txt_avg_price" id="txt_avg_price"   class="text_boxes_numeric" type="text" value=""  style="width:50px " onChange="copy_value(this.value,'rate_',1)" readonly /></td>
                        <td><input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value="" style="width:65px " readonly/></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12" valign="middle" class="button_container">
                            <div id="pdf_file_name"></div>
                            <input class="text_boxes" name="lib_tna_intregrate" id="lib_tna_intregrate" type="hidden" value=""  style="width:100px"/>
                            <input class="text_boxes" name="txt_pre_des" id="txt_pre_des" type="hidden" value=""  style="width:100px"/>
                            <input class="text_boxes" name="txt_pre_brand_sup" id="txt_pre_brand_sup" type="hidden" value=""  style="width:100px"/>
                            <input class="text_boxes" name="txt_update_dtls_id" id="txt_update_dtls_id" type="hidden" value=""  style="width:100px"/>
                            <? echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0 ,"reset_form('trimsbooking_2','','','','','')",2); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <div id="listview_dtls"></div>
        <div id="booking_list_view"></div>
        </div>
        <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>