<?
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Trims Booking
Functionality	 :	
JS Functions	 :
Created by		 : MONZU 
Creation date 	 : 
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
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Booking Non Order", "../../", 1, 1,$unicode,'','');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';


function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/trims_sample_booking_without_order2_controller" );
			reset_form('orderdetailsentry_2','booking_list_view','','','')
			show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/trims_sample_booking_without_order2_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			release_freezing();
		}
	}
}

function color_from_library(company_id)
{
	var color_from_library=return_global_ajax_value(company_id, 'color_from_library', '', 'requires/trims_sample_booking_without_order2_controller');
	if(color_from_library==1)
	{
		$('#txt_gmt_color').attr('readonly',true);
		$('#txt_gmt_color').attr('placeholder','Click');
		$('#txt_gmt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
		
		$('#txt_color').attr('readonly',true);
		$('#txt_color').attr('placeholder','Click');
		$('#txt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
		
	}
	else
	{
		$('#txt_gmt_color').attr('readonly',false);
		$('#txt_gmt_color').removeAttr('placeholder','Click');
		$('#txt_gmt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
		
		$('#txt_color').attr('readonly',false);
		$('#txt_color').removeAttr('placeholder','Click');
		$('#txt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
	}
}

function color_select_popup(buyer_name,text_box)
{
	//var page_link='requires/trims_sample_booking_without_order2_controller.php?action=color_popup'
	//alert(page_link)
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_sample_booking_without_order2_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#'+text_box).val(color_name.value);
		}
	}
}



	


function fnc_fabric_booking( operation )
{
	/*if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}*/
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	

 if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*cbo_pay_mode','Company Name*Buyer Name*Fabric Nature*Fabric Source*Booking Date*Pay Mode')==false)
	{
		return;
	}	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_source*cbo_ready_to_approved',"../../");
		freeze_window(operation);
		http.open("POST","requires/trims_sample_booking_without_order2_controller.php",true);
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
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		 show_msg(trim(reponse[0]));
		 if(trim(reponse[0])==0 || trim(reponse[0])==1){
			 document.getElementById('txt_booking_no').value=reponse[1];
			 set_button_status(1, permission, 'fnc_fabric_booking',1);
			 release_freezing();
		 }
		 if(trim(reponse[0])==2) { location.reload(); }
	}
}

function open_sample_popup()
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	if(cbo_company_name==0)
	{
		alert("Select Company")
		return;
	}
	if(cbo_buyer_name==0)
	{
		alert("Select Buyer")
		return;
	}
	var page_link='requires/trims_sample_booking_without_order2_controller.php?action=sample_description_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sample Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var style_id=this.contentDoc.getElementById("style_id");
		var style_no=this.contentDoc.getElementById("style_no");
		var sample_id=this.contentDoc.getElementById("sample_id");
		//alert(sample_id.value)
		document.getElementById('txt_style').value=style_id.value;
		document.getElementById('txt_style_no').value=style_no.value;
		document.getElementById('cbo_sample_type').value=sample_id.value;
		// alert(style_id.value)
		load_drop_down( 'requires/trims_sample_booking_without_order2_controller', cbo_buyer_name+'_'+style_id.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );
		
		load_drop_down( 'requires/trims_sample_booking_without_order2_controller', style_id.value+'_'+sample_id.value, 'load_drop_down_trim_group', 'tgroup_td' );
		
		$('#cbo_trim_group').removeAttr('disabled','disabled');
	}
	
}


function fnc_fabric_booking_dtls( operation )
{
	var trim_req_qty=$("#txt_trim_req_qty").val()*1;
	var wo_req_qty=$("#txt_trim_qty").val()*1;
	// if(wo_req_qty>trim_req_qty)
	// {
	// 	alert('Exceed Qty  not allowed than Sample Req Qty');
	// 	return;
	// }
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	if (form_validation('txt_booking_no*cbo_trim_group*txt_trim_qty*cbo_sample_type*txt_description','Booking No*Trims Group*Finish Fabric*Sample type*Fabric Description')==false)
	{
		return;
	}	
	var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('txt_booking_no*cbo_trim_group*cbo_uom*txt_style*txt_style_des*cbo_sample_type*txt_description*txt_barnd_sup_ref*txt_gmt_color*txt_color*txt_gmts_size*txt_size*txt_trim_qty*txt_trim_req_qty*txt_rate*txt_amount*update_id_details',"../../");
	//alert(data);
	//return;
	freeze_window(operation);
	http.open("POST","requires/trims_sample_booking_without_order2_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
}
	 
function fnc_fabric_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
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
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		if(trim(reponse[0])==17){
			alert(trim(reponse[1])+"\n So Save/Update Not Possible")
			release_freezing();
			return;
		}
		if(trim(reponse[0])==13){
			alert(trim(reponse[1])+"\n So Save/Update Not Possible")
			release_freezing();
			return;
		}
		if(trim(reponse[0])==0 || trim(reponse[0])==1 ){ 
			$("#txt_style_no").attr('disabled',false);
			$("#cbo_trim_group").attr('disabled',false);
			$("#cbo_sample_type").attr('disabled',false);
		}
		
		show_msg(trim(reponse[0]));
		reset_form('orderdetailsentry_2','booking_list_view','','','','txt_style_no*txt_style*cbo_sample_type*cbo_trim_group')
		set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
		show_list_view(reponse[1],'show_fabric_booking','booking_list_view','requires/trims_sample_booking_without_order2_controller','setFilterGrid(\'list_view\',-1)');
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

function calculate_amount()
{
var trim_qty=document.getElementById('txt_trim_qty').value;
var txt_rate=document.getElementById('txt_rate').value;	
var amount=number_format_common((trim_qty*txt_rate),5,0);
document.getElementById('txt_amount').value=amount
}

function generate_fabric_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		$report_title=$( "div.form_caption" ).html();
		var data="action=show_fabric_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+$report_title;
		//freeze_window(5);
		http.open("POST","requires/trims_sample_booking_without_order2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}	
}

function set_trim_cons_uom(trim_group_id){
	var txt_style_id=document.getElementById('txt_style').value;
	var cbo_sample_type=document.getElementById('cbo_sample_type').value;
	var response=return_global_ajax_value(trim_group_id+'_'+txt_style_id+'_'+cbo_sample_type, 'set_cons_uom', '', 'requires/trims_sample_booking_without_order2_controller');
	var response=response.split("_");
	var cbo_cons_uom=response[0];
	var req_qty_ra=response[1]*1;
	var description_ra=response[2];
	var prev_qty=response[3]*1;
	
	var trim_qty_balance=(req_qty_ra-prev_qty);
	//alert(description_ra+'='+req_qty_ra+'='+prev_qty);
	//alert(prev_qty);
	
  	document.getElementById('cbo_uom').value = cbo_cons_uom;
	document.getElementById('txt_trim_qty').value = trim_qty_balance;
	document.getElementById('txt_trim_req_qty').value = req_qty_ra;
	//document.getElementById('txt_description').value = trim(description_ra);
	$("#txt_description").val(trim(description_ra));
	if(description_ra!="")
	{
	$("#txt_description").attr('disabled',true);
	} 
	else  { $("#txt_description").attr('',false);}
}disabled

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}
function fnc_trimGroup(sam_type)
{
	var txt_style_id=document.getElementById('txt_style').value;
	var cbo_sample_type=document.getElementById('cbo_sample_type').value;
	
	load_drop_down( 'requires/trims_sample_booking_without_order2_controller', txt_style_id+'_'+cbo_sample_type, 'load_drop_down_trim_group', 'tgroup_td' );
}
function fnc_desc_popup()
{
	var txt_style_id=document.getElementById('txt_style').value;
	var cbo_sample_type=document.getElementById('cbo_sample_type').value;
	var cbo_trim_group=document.getElementById('cbo_trim_group').value;
	if (form_validation('cbo_trim_group','Trim Group')==false)
	{
		return;
	}
	
	var page_link='requires/trims_sample_booking_without_order2_controller.php?action=trim_description_popup&cbo_trim_group='+cbo_trim_group+'&txt_style_id='+txt_style_id+'&cbo_sample_type='+cbo_sample_type;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sample Description', 'width=700px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var description=this.contentDoc.getElementById("description");
		var req_qty=this.contentDoc.getElementById("req_qty");
		var uom_id=this.contentDoc.getElementById("uom_id");
		var previous_qty=this.contentDoc.getElementById("prev_qty");
		var prev_qty=previous_qty*1;
		var trim_qty_balance=(req_qty.value-prev_qty);
		//alert(sample_id.value)
		document.getElementById('txt_description').value=description.value;
		document.getElementById('txt_trim_req_qty').value=req_qty.value;
		document.getElementById('txt_trim_qty').value=trim_qty_balance;
		document.getElementById('cbo_uom').value=uom_id.value;
		
	

		// alert(style_id.value)
		//load_drop_down( 'requires/trims_sample_booking_without_order2_controller', cbo_buyer_name+'_'+style_id.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );
		
		//load_drop_down( 'requires/trims_sample_booking_without_order2_controller', style_id.value+'_'+sample_id.value, 'load_drop_down_trim_group', 'tgroup_td' );
		
		$('#cbo_trim_group').removeAttr('disabled','disabled');
		$('#txt_description').attr('disabled','disabled');
	}
}
function fnc_disable()
{
	$('#txt_description').removeAttr('disabled','disabled');
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            	<fieldset style="width:950px;">
                <legend>Sample Booking (Without Order) </legend>
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                                <td  width="130" height="" align="right" > Booking No </td>             
                                <td  width="170" >
                                	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/trims_sample_booking_without_order2_controller.php?action=trims_booking_popup','Trims Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                                </td>
                              <td  align="right" class="must_entry_caption">Company Name</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_sample_booking_without_order2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/trims_sample_booking_without_order2_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );color_from_library( this.value )","","" );
								?>	  
                              </td>
                         <td align="right" class="must_entry_caption">Buyer Name</td>   
   						 <td id="buyer_td"> 
                             <?  
							  	echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" );
								?>
                         </td>
                        </tr>
                       <tr>
                        <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                                <td width="170">
                                    <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo date("d-m-Y")?>" disabled />	
                                </td>
                       <td align="right">Delivery Date</td>
                        	<td>
                            	 <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/>	  	
                            </td>
                            
                                 <td  align="right" class="must_entry_caption">Pay Mode</td>
                                <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "load_drop_down( 'requires/trims_sample_booking_without_order2_controller', this.value, 'load_drop_down_supplier', 'supplier_td' )","" );
							   ?> 
                                 </td>
                        </tr>
                        <tr>
                        <td align="right">Currency</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "",0 );		
							  ?>	
                               
                              </td>
                        	<td align="right">Exchange Rate</td>
                              <td>
                             <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate"  />  
                              </td>
                             <td  align="right">Supplier Name</td>
                                <td id="supplier_td">
                               <?
							   		echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_without_order2_controller');",0 );
							   ?> 
                                 </td> 
                              </tr>
                        <tr>
                             <td  width="130" height="" align="right"> Source </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", 3, "","" );
							   ?>
                                </td>
                               <td align="right">Ready To Approved</td>  
                        	<td align="center" height="10">
                              <?
							   		echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
							  ?>
                            </td>
                             <td align="right">Attention</td>   
                        	<td align="left" height="10">
                            	<input class="text_boxes" type="text" style="width:160px;"  name="txt_attention" id="txt_attention"/>
                            	
                                </td>                                
                        </tr>
                        <tr>
                        </tr>
                        <tr>
							<td align="right">Tenor</td>
                            <td><input style="width:160px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                        	<td align="center" height="10" colspan="3">
                            <? 
								include("../../terms_condition/terms_condition.php");
								terms_condition(143,'txt_booking_no','../../');
							?>
                            <!--<input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/trims_sample_booking_without_order2_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                            
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                            	
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
                              <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/trims_sample_booking_without_order2_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                                <input type="hidden" id="id_approved_id">
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" height="10">
                           
                           </td>
                        </tr>
                    </table>
              </fieldset>
              </form>
              
              <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
            	<fieldset style="width:950px;">
                <legend>Sample Booking (Without Order) </legend>
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                                <td  width="130" height="" align="right" > Style ref </td>              
                                <td  width="170" >
                                	<input type="text" id="txt_style_no"    name="txt_style_no"  class="text_boxes" style="width:160px" onDblClick="open_sample_popup()" placeholder="Double Click to Search" readonly/>
                                  <input type="hidden" id="txt_style"    name="txt_style"  class="text_boxes" style="width:100px"   readonly/>
                                </td>
                              <td  align="right">Style Des.</td>
                              <td>
                             <input type="text" id="txt_style_des"    name="txt_style_des"  class="text_boxes" style="width:160px"/> 
                              </td>
                        
						 <td align="right" class="must_entry_caption">Sample type</td>   
							<td id="sampletd"><? echo create_drop_down( "cbo_sample_type", 172, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "", 0,'' ); 
							//echo create_drop_down( "cbo_sample_type", 172, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "","",'' );
							 //echo create_drop_down( "cbo_sample_type", 172, $blank_array,"", 1, "-- Select --", $selected, "",0 );
							?>
							</td>
                        </tr>
                       <tr>
                       <td align="right" class="must_entry_caption">Trims Group</td>
                        	<td id="tgrouptd">
                            	   <? echo create_drop_down( "cbo_trim_group", 172, "select id, item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0","id,item_name", '1', "--Select--", '', "",0,'' ); 
								   //$trim_group= return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0",'id','item_name');

								   //echo create_drop_down( "cbo_trim_group", 172, $trim_group,"", 1, "--Select--", $selected, "",1); 
								   //set_trim_cons_uom(this.value)
								   ?>	
                            </td>
                               <td align="right" width="130" >
                               UOM
                  </td>
                                <td>	
                                     <?  echo create_drop_down( "cbo_uom", 172, $unit_of_measurement,"", 1, "--Select--", $selected, "",0 ); ?>
                                </td>
                       <td  width="130" align="right" class="must_entry_caption"> Description</td>
                                <td  width="170"> 
							  <input type="text" id="txt_description"    name="txt_description"    class="text_boxes" style="width:160px" />
                              </td>
                                                          
                        </tr>
                      
                        
                        <tr>
                        <td align="right">Brand/ Supp. Ref</td>
                              <td>
                               <input name="txt_barnd_sup_ref" id="txt_barnd_sup_ref" class="text_boxes" type="text" value=""  style="width:160px "/>	
                               
                              </td>
                              <td align="right">Gmts Color</td>
                              <td>
                               <input name="txt_gmt_color" id="txt_gmt_color" class="text_boxes" type="text" value=""  style="width:160px "/>
                              </td>
                        	<td align="right">Item Color</td>
                              <td id="fabriccolor_id_id_td" >
                               <input name="txt_color" id="txt_color" class="text_boxes" type="text" value=""  style="width:160px "/>
                              </td>
                              </tr>
                        <tr>
                        <td  align="right">Gmts size</td>
                                <td>
                              <input name="txt_gmts_size" id="txt_gmts_size" class="text_boxes" type="text" value=""  style="width:160px "/>
                              </td>
                        <td  align="right">Item size</td>
                                <td id="itemsize_id_td">
                              <input name="txt_size" id="txt_size" class="text_boxes" type="text" value=""  style="width:160px "/>
                              </td>
                              <td  align="right" class="must_entry_caption">Qty</td>
                                <td>
                               <input name="txt_trim_qty" id="txt_trim_qty" class="text_boxes_numeric" placeholder="Write" type="text" onChange="calculate_amount()" value=""  style="width:160px "/>
                               <input name="txt_trim_req_qty" id="txt_trim_req_qty" class="text_boxes_numeric" type="hidden"  style="width:20px"/>
                                 </td>
                        </tr>
                        <tr>
                        <td align="right">Rate</td>  
                        	<td align="center" height="10">
                             <input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" value=""  style="width:160px " onChange="calculate_amount()" />
                            </td>
                        <td align="right">Amount</td>  
                        	<td align="center" height="10">
                             <input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:160px " readonly/>
                              <input type="hidden" id="update_id_details">
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <?
									
									echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2) ; 
									?>
                                    <div id="pdf_file_name"></div>
                                    
                                    <input type="button" value="Print Booking" onClick="generate_fabric_report()"  style="width:100px" name="print" id="print" class="formbutton" />
                            </td>
                        </tr>
                    </table>
              </fieldset>
              </form>
              <fieldset style="width:1300px;">
                <legend>Booking Entry</legend>
                    <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                            <tr align="center">
                                <td colspan="12" id="booking_list_view">
                                </td>	
                        	</tr>
                       </table>
                </fieldset>
	</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>