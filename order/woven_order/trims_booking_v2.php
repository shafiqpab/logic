<?
/*-------------------------------------------- Comments ----------------------------------------
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

//----------------------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Trims Booking", "../../", 1, 1,$unicode,'','');
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
function openmypage(page_link,title)
{
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1310px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		if (theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/trims_booking_controller_v2" );
			set_button_status(0, permission, 'fnc_trims_booking',1);
			release_freezing();
		}
	}
}

function fnc_po_select()
{
if (form_validation('txt_job_no*cbo_company_name','Job NO*Company')==false)
	{
		return;
	}	
	else
	{
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var page_link='requires/trims_booking_controller_v2.php?action=fnc_po_select_data';
		var title='Trim Booking Search';
		page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemail_name=this.contentDoc.getElementById("txt_selected_name");
			if (theemail.value!="")
			{
				document.getElementById('txt_select_po').value=theemail.value;
				document.getElementById('txt_select_po_num').value=theemail_name.value;
				//fnc_generate_booking(theemail.value,cbo_company_name)
			}
		}
	}	
}

function fnc_process_data()
{
	if (form_validation('txt_job_no*cbo_company_name','Job NO*Company')==false)
	{
		return;
	}	
	else
	{
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_currency_job=document.getElementById('cbo_currency_job').value;
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		if(cbo_item_from_precost==1)
		{
			var page_link='requires/trims_booking_controller_v2.php?action=fnc_process_data';
		}
		if(cbo_item_from_precost==2)
		{
			var page_link='requires/trims_booking_controller_v2.php?action=fnc_process_data_item_from_library';
		}
		var title='Trim Booking Search';
		page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job+'&cbo_supplier_name='+cbo_supplier_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1570px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			//var tot_req_amount=this.contentDoc.getElementById("txt_tot_req_amount");
			//var tot_cu_amount=this.contentDoc.getElementById("txt_tot_cu_amount");
			
			if (theemail.value!="")
			{
				document.getElementById('txt_select_item').value=theemail.value;
				//document.getElementById('txt_tot_req_amount').value=tot_req_amount.value;
				//document.getElementById('txt_tot_cu_amount').value=tot_cu_amount.value;
				fnc_generate_booking(theemail.value,cbo_company_name)
			}
		}
	}
}

function fnc_generate_booking(param,cbo_company_name)
{
	    freeze_window(operation);
		var garments_nature=document.getElementById('garments_nature').value;
	    var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	    var txt_delivery_date= document.getElementById('txt_delivery_date').value
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var txt_select_po=document.getElementById('txt_select_po').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_currency_job=document.getElementById('cbo_currency_job').value;
		
	    var data="'"+param+"'"
		if(cbo_item_from_precost==1)
		{
			var data="action=generate_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost+'&txt_select_po='+txt_select_po+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job;
		}
		
		if(cbo_item_from_precost==2)
		{
			var data="action=generate_fabric_booking_without_precost&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost+'&txt_select_po='+txt_select_po+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job;
		}
		http.open("POST","requires/trims_booking_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	    $("#cbo_currency").attr("disabled",true);
		set_all_onclick();
		release_freezing();
	}
}


function copy_value(value,field_id,i)
{
	var copy_val=document.getElementById('copy_val').checked;
	var txttrimgroup=document.getElementById('txttrimgroup_'+i).value;
	var rowCount = $('#tbl_list_search tr').length;
	
	if(copy_val==true)
	{
		freeze_window(operation);
		for(var j=i; j<=rowCount; j++)
		{
			if(field_id=='txtdescription_')
			{
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value)
				{
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='txtbrandsupref_')
			{
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value)
				{
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='cbocolorsizesensitive_')
			{
				
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value)
				{
					document.getElementById(field_id+j).value=value;
					//set_cons_break_down(i);
				}
			}
		}
		release_freezing();
	}
}

 
function open_consumption_popup(page_link,title,po_id,i)
{
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txt_job_no').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var txt_po_id =document.getElementById(po_id).value;
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txt_req_amount=document.getElementById('txtreqamount_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	//var cbo_gmt_item_id=document.getElementById('cbo_gmt_item_id').value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtwoq=document.getElementById('txtbalwoq_'+i).value;
	var txtcuwoq=document.getElementById('txtcuwoq_'+i).value;
	var txtcuamount=document.getElementById('txtcuamount_'+i).value*1;
	
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}
	
	else
	{
		var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("woqty_sum");
			var rate=this.contentDoc.getElementById("rate_sum");
			var amount=this.contentDoc.getElementById("amount_sum");
			
			//=========================
			if(document.getElementById('amount_exceed_level').value==2)
			{
			
				var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
				var woq_qty=woq.value*1;
				var amount_qty=amount.value*1;
				var exeed_budge_qty_percent=document.getElementById('exeed_budge_qty').value*1;
				var exeed_budge_amount_percent=document.getElementById('exeed_budge_amount').value*1;
				var txt_req_quantity_with_percent=number_format_common(((exeed_budge_qty_percent/100)*txt_req_quantity*1)+txt_req_quantity*1,5,0);
				
				var txt_req_amount_with_percent=number_format_common(((exeed_budge_amount_percent/100)*txt_req_amount*1)+txt_req_amount*1,5,0);
				
				if(txt_req_quantity_with_percent <((txtcuwoq*1)+woq_qty) && cbo_item_from_precost==1)
				{
					alert("Budget Qty Over");
					return;	
				}
				
				if(txt_req_amount_with_percent <((txtcuamount*1)+amount_qty) && cbo_item_from_precost==1)
				{
					alert("Budget Amount Over");
					return;	
				}
			}
			//==========================

			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtrate_'+i).value=rate.value;
			document.getElementById('txtamount_'+i).value=amount.value;
			//document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
			calculate_amount(i)
		}	
	}
}

function set_exchange_rate(currency_id)
{
	var cbo_currency_job=document.getElementById('cbo_currency_job').value;
	if(currency_id==cbo_currency_job)
	{
		document.getElementById('txt_exchange_rate').value=1;	
	}
	//document.getElementById('booking_list_view').innerHTML="";
}

function set_cons_break_down(i)
{
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txt_job_no').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
	var txt_select_po=document.getElementById('txt_select_po').value;
	
	var txt_po_id =document.getElementById('txtpoid_'+i).value;
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtwoq=document.getElementById('txtbalwoq_'+i).value;
	
	if(cbo_item_from_precost==2)
	{
		return;
	}
	var cons_breack_down=return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+cbo_buyer_name+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_country+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+txtwoq, 'set_cons_break_down', '', 'requires/trims_booking_controller_v2');
	
   document.getElementById('consbreckdown_'+i).value=cons_breack_down;
}

	function openmypage_booking(page_link,title)
	{
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1155px,height=455px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking");
			if (theemail.value!="")
			{
				reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
				document.getElementById('copy_val').checked=true;
				$("#cbo_currency").attr("disabled",true);
				get_php_form_data( theemail.value, "populate_data_from_search_popup_booking", "requires/trims_booking_controller_v2" );
				set_button_status(1, permission, 'fnc_trims_booking',1);
				fnc_show_booking();
			}
		}
	}

function calculate_amount(i)
{
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
	var txtrate=(document.getElementById('txtrate_'+i).value)*1
	var txtexchrate=(document.getElementById('txtexchrate_'+i).value)*1
	
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
	if(txtrate>txtrate_precost && cbo_item_from_precost==1)
	{
		alert("Rate Exceeds Pre-Cost Rate");
		document.getElementById('txtrate_'+i).value=number_format_common(txtrate_precost,5,0)
		document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtwoq),5,0)
		return
	}
	document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
	var tot_amount=0
	var row_num=$('#tbl_list_search tr').length;
	for (var j=1; j<=row_num; j++)
	{
		var amount=document.getElementById('txtamount_'+j).value*1
		tot_amount+=amount;
	}
	//alert (tot_amount);
	document.getElementById('tot_amount').value=number_format_common(tot_amount,5,0);
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


function fnc_trims_booking( operation )
{
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	var amount_exceed_level=document.getElementById('amount_exceed_level').value*1;
	if(amount_exceed_level==1)
	{
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var exeed_budge_amount=document.getElementById('exeed_budge_amount').value*1;
		var txt_tot_req_amount=document.getElementById('txt_tot_req_amount').value*1;
		var txt_tot_req_amount_with_percent=((txt_tot_req_amount*exeed_budge_amount)/100)+txt_tot_req_amount;
		var txt_tot_cu_amount=document.getElementById('txt_tot_cu_amount').value*1;
		var tot_amount=document.getElementById('tot_amount').value*1;
		var saved_tot_amount=document.getElementById('saved_tot_amount').value*1;
		if(((txt_tot_cu_amount-saved_tot_amount)+tot_amount)>txt_tot_req_amount_with_percent && cbo_item_from_precost==1)
		{
			alert("Budget amount Over");
			return;
		}
	}
	var data_all="";
	if (form_validation('cbo_booking_month*cbo_company_name*cbo_supplier_name*txt_booking_date*cbo_pay_mode*cbo_source*txt_delivery_date','Booking Month*Company Name*Supplier Name*Booking Date*Pay Mode*source*Delivery Date')==false)
	{
		return;
	}	
	else
	{
		 data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*txt_job_no*cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_remarks*cbo_ready_to_approved*txt_tenor*cbo_item_from_precost',"../../");
	}
	var row_num=$('#tbl_list_search tr').length;
	if(row_num <1)
	{
		alert("Select Item");
		return;
	}
	for (var i=1; i<=row_num; i++)
	{
		var txtrate=document.getElementById('txtrate_'+i).value
		if(txtrate=="" || txtrate==0)
		{
		alert("Insert Rate")	
		//return; issue id 3512 AKH
		}
		var consbreckdown=document.getElementById('consbreckdown_'+i).value
		
		if (consbreckdown=="" )
		{
			set_cons_break_down(i)
		}
		//else
		//{
			data_all=data_all+get_submitted_data_string('txtbookingid_'+i+'*txtpoid_'+i+'*txtcountry_'+i+'*txttrimcostid_'+i+'*txttrimgroup_'+i+'*txtuom_'+i+'*cbocolorsizesensitive_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*consbreckdown_'+i+'*txtexchrate_'+i+'*preconsamt_'+i,"../../");
		//}
	}
	var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","requires/trims_booking_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}
	 
function fnc_trims_booking_reponse()
{
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		 if(trim(reponse[0])=='app1'){
			alert("This booking is approved")
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
			alert("PI Number Found :"+number_format(trim(reponse[2]),2,'.','' )+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		 if (reponse[0].length>2) reponse[0]=10;
		   release_freezing();
		 show_msg(trim(reponse[0]));
		  
		 if(trim(reponse[0])==0 || trim(reponse[0])==1 ){
		 document.getElementById('txt_booking_no').value=reponse[1];
		 $("#cbo_supplier_name").attr("disabled",true);
		 set_button_status(1, permission, 'fnc_trims_booking',1);
		 fnc_show_booking()
		 //show_msg(trim(reponse[0]));
		 release_freezing();
		 }
		if(trim(reponse[0])==2) { location.reload(); }
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
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_item_from_precost=document.getElementById('cbo_item_from_precost').value;
		var txt_select_po=document.getElementById('txt_select_po').value;
		if(cbo_item_from_precost==1)
		{
		var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature;
		}
		
		if(cbo_item_from_precost==2)
		{
		var data="action=show_trim_booking_without_precost"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_item_from_precost='+cbo_item_from_precost+'&txt_select_po='+txt_select_po;
		}
		//freeze_window(5);
		http.open("POST","requires/trims_booking_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
}

function fnc_show_booking_reponse()
{
	if(http.readyState == 4) 
	{
			//document.getElementById('cbo_currency_job').disabled=true;
			
        $("#cbo_currency").attr("disabled",true);
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_button_status(1, permission, 'fnc_trims_booking',2);
		/*var tableFilters = {
				col_operation: {
								   id: ["value_total_amount"],
								   col: [16],
								   operation: ["sum"],
								   write_method: ["innerHTML"]
								} 
								}
		setFilterGrid('tbl_list_search',-1,tableFilters)*/
		//document.getElementById('tot_amount').value;
		//document.getElementById('txt_tot_cu_amount').value=(document.getElementById('txt_tot_cu_amount').value*1)-(document.getElementById('tot_amount').value*1);
		set_all_onclick();
		//release_freezing();
	}
}

function select_po_enable_disable(value)
{
	if(value==2)
	{
	$("#txt_select_po_num").attr("disabled",false);	
	$('#txt_select_po_num').removeAttr('placeholder','No Need');
	$('#txt_select_po_num').attr('placeholder','Double Click');
	}
	if(value==1)
	{
	$("#txt_select_po_num").attr("disabled",true);	
	$('#txt_select_po_num').removeAttr('placeholder','Double Click');
	$('#txt_select_po_num').attr('placeholder','No Need');
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
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			$report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment;
			//freeze_window(5);
			http.open("POST","requires/trims_booking_controller_v2.php",true);
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
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

function popolate_job_data(data)
{
  var displayValueCompany=document.getElementById('displayValueCompany').value;
  var displayValueYear=document.getElementById('displayValueYear').value;
  $job_no=trim(displayValueCompany)+'-'+trim(displayValueYear)+'-'+padToFive(trim(data));
  get_php_form_data($job_no, "populate_data_from_search_popup", "requires/trims_booking_controller_v2" );

}
function padToFive(number) {
  if (number<=99999) { number = ("0000"+number).slice(-5); }
  return number;
}
function fill_attention(supplier_id){
	if(supplier_id==0){
		document.getElementById('txt_attention').value='';
		return;
	}
	var attention=return_global_ajax_value(supplier_id, 'get_attention_name', '', 'requires/trims_booking_controller_v2');
	document.getElementById('txt_attention').value=trim(attention);
}
</script>
 
</head> 
 
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="trimsbooking_1"  autocomplete="off" id="trimsbooking_1">
            	<fieldset style="width:950px;">
                <legend title="V3">Trims Booking &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   <font id="app_sms" style="color:#F00"></font></legend>
               
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    <td align=""></td>
                    <td align="" valign="top" >
                    </td>
                    <td  width="130" height="" align="right" class="must_entry_caption">Booking No </td>              
                    <td  width="170" >
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/trims_booking_controller_v2.php?action=trims_booking_popup','Trims Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                    <td align="">
                    <input type="hidden" id="id_approved_id">
                     <input type="hidden" id="exeed_budge_qty">
                    <input type="hidden" id="exeed_budge_amount">
                    <input type="hidden" id="amount_exceed_level">
                    </td>
                    <td align=""></td>
                    </tr>
                    <tr>
                            
                                <td align="right" class="must_entry_caption">Booking Month</td>   
    						<td> 
                            	<? 
							  	echo create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );		
							  ?>
                              <? 
							  	echo create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
							  ?>
                           </td>
                            <td  width="130"  align="right" class="must_entry_caption">Job No</td>              
                                <td  width="170" >
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/trims_booking_controller_v2.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />                               
                                </td>
                              <td  align="right" class="must_entry_caption">Company Name</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/trims_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/trims_booking_controller_v2' );","","" );
								?>	  
                              </td>
                             
                        </tr>
                        <tr>
                        <td align="right" class="must_entry_caption">Buyer Name</td>
                              <td id="buyer_td">
                              <? 
                                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" );
                               ?>	  
                              </td>
                              
                                <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                            <td width="170">
                                <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo date("d-m-Y")?>" disabled />	
                            </td>
                            <td  align="right" class="must_entry_caption">Pay Mode</td>
                                <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/trims_booking_controller_v2', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )","" );
							   ?> 
                         </td>
                              
                      </tr>
                        <tr>
                            <td  width="130" align="right" class="must_entry_caption">Delivery Date</td>
                                <td width="170">
                                    <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/>	
                                </td>
                                 <td align="right">Currency</td>
                          <td>
                          <? 
                            echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "set_exchange_rate(this.value)",0 );		
                          ?>
                          <input style="width:160px;" type="hidden" class="text_boxes"  name="cbo_currency_job" id="cbo_currency_job"  /> 	
                          </td>
                        	<td align="right">Exchange Rate</td>
                            <td>
                             <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  />  
                              </td>
                        </tr>
                        <tr>
                        
                           <td  align="right" class="must_entry_caption" >Supplier Name</td>
                              <td id="supplier_td">
                               <?
							   	echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
							   ?> 
                             </td>
                             <td  width="130" height="" align="right" class="must_entry_caption"> Source </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );
							   ?>
                                </td>
                        	 <td align="right">Item From Pre-cost</td>  
                        	<td align="center" height="10"> 
								<?
                                  echo create_drop_down( "cbo_item_from_precost", 172, $yes_no,"", 0, "", "", "select_po_enable_disable(this.value);","","" );
                                ?>
                            </td>
                            
                        </tr>
                        <tr>
                        <td align="right">Attention</td>   
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                            </td>
                            <td  width="130" height="" align="right"> Select Po </td>              <!-- 11-00030  -->
                            <td  width="170" >
                            <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_po_select()"  placeholder="No Need" name="txt_select_po_num" id="txt_select_po_num" disabled/>
                                <input class="text_boxes" type="hidden" style="width:160px" onDblClick="fnc_po_select()" readonly placeholder="Double Click" name="txt_select_po" id="txt_select_po"/>
                                
                            </td> 
                        	
                        </tr>
                        
                        <tr>
                       
                            <td align="right">Remarks</td>  
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" style="width:97%;"  name="txt_remarks" id="txt_remarks"/>
                            </td>
                           <td  width="130"  align="right" class="must_entry_caption"> Select Item </td>              <!-- 11-00030  -->
                            <td  width="170" >
                                <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data()" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
                            </td> 
                           
                        </tr>
                         <tr>
                         <td align="right"></td> 
                           <td align="center" height="10">
                            <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/trims_booking_controller_v2.php?action=terms_condition_popup','Terms and Condition')" /></td>
                        	<td align="right">Ready To Approved</td>  
                        	<td align="center" height="10">
                              <?
							   		echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
							   ?>
                            </td>   
							<td align="right">Tenor</td>
                            <td><input style="width:160px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <?
							  $date=date('d-m-Y'); 
							  $dd="disable_enable_fields( 'cbo_currency*cbo_supplier_name', 0 )";
							  echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,".$date."',$dd,'cbo_currency*cbo_booking_year*cbo_booking_month*copy_val')",1) ; ?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" height="10">
                             <div id="pdf_file_name"></div>
                             <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                            <input type="button" value="Print Booking1" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                             <input type="button" value="Print Booking2" onClick="generate_trim_report('show_trim_booking_report2')"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                            Copy:<input type="checkbox" id="copy_val"  name="copy_val" checked/> 
                            <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_req_amount" id="txt_tot_req_amount"/>
                            <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_cu_amount" id="txt_tot_cu_amount"/>                       
                            </td>
                        </tr>
                    </table>
              </fieldset>
              </form>
           <div id="booking_list_view">
    </div>
	</div>
   <div style="display:none" id="data_panel"></div>
   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>