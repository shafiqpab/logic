﻿<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Purpose			 : This form will create Sammple Trims Booking
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
echo load_html_head_contents("Woven Trims Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';

function openmypage(page_link,title)
{
	var garments_nature=document.getElementById('garments_nature').value;
	//alert(garments_nature);
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		if (theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/trims_sample_booking_with_order_controller" );
			set_button_status(0, permission, 'fnc_trims_booking',1);
			release_freezing();
		}
	}
}

function fnc_process_data(page_link,title)
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
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		
		var page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_currency='+cbo_currency;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			if (theemail.value!="")
			{
				document.getElementById('txt_select_item').value=theemail.value;
				fnc_generate_booking(theemail.value,cbo_company_name)
			}
		}
	}
}

function fnc_generate_booking(param,cbo_company_name)
{
		var garments_nature=document.getElementById('garments_nature').value;
	    var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	    var txt_delivery_date= document.getElementById('txt_delivery_date').value
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;

	    var data="'"+param+"'"
		var data="action=generate_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency;;
		http.open("POST","requires/trims_sample_booking_with_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_all_onclick();
		
	}
}


function copy_value(value,field_id,i)
{
  var copy_val=document.getElementById('copy_val').checked;
  //var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
  var gmtssizesid=document.getElementById('txttrimgroup_'+i).value;
  var rowCount = $('#tbl_list_search tr').length;
 // if(hid_fab_cons_in_quotation_variable==1)
  //{
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
		  if(field_id=='txtdescription_')
		  {
			if( gmtssizesid==document.getElementById('txttrimgroup_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='txtbrandsupref_')
		  {
			if( gmtssizesid==document.getElementById('txttrimgroup_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		}
	  }
  //}
  
 /* if(hid_fab_cons_in_quotation_variable==2)
  {
	  if(copy_val==true)
	  {
		  for(var j=i; j<=rowCount; j++)
		  {
			  if(field_id=='diawidth_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				  document.getElementById(field_id+j).value=value;
				  calculate_measurement_top(j)
				}
			  }
			  else if(field_id=='itemsizes_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_measurement_top(j)
				}
			  }
			  else
			  {
			  document.getElementById(field_id+j).value=value;
			  calculate_measurement_top(j)
			  }
		  }
	  }
  }*/
}


function open_consumption_popup(page_link,title,po_id,i)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var po_id =document.getElementById(po_id).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value*1;
	var txtreqqnty=document.getElementById('txtreqqnty_'+i).value*1;
	//alert(txtreqqnty);
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}
	
	else
	{
		var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&txtwoq='+txtwoq+'&cons_breck_downn='+cons_breck_downn+'&cbocolorsizesensitive='+cbocolorsizesensitive+'&txtreqqnty='+txtreqqnty;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("cons_sum");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			//document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
			calculate_amount(i)
		}	
	}
}


function openmypage_booking(page_link,title)
{
	
	
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking");
			if (theemail.value!="")
			{
		        reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
				document.getElementById('copy_val').checked=true;
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/trims_sample_booking_with_order_controller" );
				set_button_status(1, permission, 'fnc_trims_booking',1);
				fnc_show_booking()
			}
		}
	
}

function calculate_amount(i)
{
	
	
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
	var txtrate=(document.getElementById('txtrate_'+i).value)*1
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	var cbo_currency=(document.getElementById('cbo_currency').value)*1;
	if(txtrate>txtrate_precost)
	{
		if(cbo_currency==1)
		{
			
			

			var cbo_currercy=$('#cbo_currency').val();
			var booking_date = $('#txt_booking_date').val();
			var cbo_company_name = $('#cbo_company_name').val();
			// var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/knitting_work_order_controller');
			// var response=response.split("_");
			// var exchange_rate=response[1]*1;
			var exchange_rate=set_conversion_rate(2, booking_date, '../../', '');

			var new_exchange_rate=exchange_rate*txtrate_precost;

			console.log('txtrate='+txtrate+' ; '+' new_exchange_date='+new_exchange_rate);

			if(txtrate>new_exchange_rate)
			{
				alert("Rate Exceeds Pre-Cost Rate");
				document.getElementById('txtrate_'+i).value=number_format_common(new_exchange_rate,5,0)
				document.getElementById('txtamount_'+i).value=number_format_common((new_exchange_rate*txtwoq),5,0)

				return ;
			}
		}
		else
		{
			
			alert("Rate Exceeds Pre-Cost Rate");
			document.getElementById('txtrate_'+i).value=number_format_common(txtrate_precost,5,0)
			document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtwoq),5,0)

			return
			

		}
		
	}
	//alert(txtrate*txtwoq)
	document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
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
	var data_all="";
	if (form_validation('cbo_booking_month*cbo_company_name*cbo_supplier_name*txt_booking_date*cbo_pay_mode*cbo_material_source','Booking Month*Company Name*Supplier Name*Booking Date*Pay Mode*Material Source')==false)
	{
		return;
	}	
	else
	{
		 data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*txt_job_no*cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_tenor*cbo_ready_to_approved*cbo_material_source',"../../");
	}
	var row_num=$('#tbl_list_search tr').length;
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('txtdescription_'+i,'Description')==false )
		{
			return;
		}
		if (form_validation('consbreckdown_'+i,'Consumption Brackdown is Empty')==false )
		{
			alert("Consumption Brackdown is Empty");
			$("#txtwoq_"+i).click();
			document.getElementById('txtwoq_'+i).focus();
			return;
		}
		else
		{
			data_all=data_all+get_submitted_data_string('txtjob_'+i+'*txtpoid_'+i+'*txttrimcostid_'+i+'*txttrimgroup_'+i+'*txtdescription_'+i+'*txtbrandsupref_'+i+'*txtuom_'+i+'*txtreqqnty_'+i+'*txtcuwoq_'+i+'*txtbalwoq_'+i+'*cbocolorsizesensitive_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*consbreckdown_'+i+'*txtbookingid_'+i,"../../");
		}
	}
	var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","requires/trims_sample_booking_with_order_controller.php",true);
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
		 show_msg(trim(reponse[0]));
		 if(trim(reponse[0])==0 || trim(reponse[0])==1)
		 {
			 document.getElementById('txt_booking_no').value=reponse[1];
			 $("#cbo_supplier_name").attr("disabled",true);
			 set_button_status(1, permission, 'fnc_trims_booking',1);
			 fnc_show_booking();
		 }
		 if(trim(reponse[0])==2) { location.reload(); }
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
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&txt_job_no='+txt_job_no+'&cbo_buyer_name='+cbo_buyer_name+'&garments_nature='+garments_nature;
		//freeze_window(5);
		http.open("POST","requires/trims_sample_booking_with_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
}

function fnc_show_booking_reponse()
{
	if(http.readyState == 4) 
	{
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
		set_all_onclick();
		//release_freezing();
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
		var data="action=show_trim_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment;
		//freeze_window(5);
		http.open("POST","requires/trims_sample_booking_with_order_controller.php",true);
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
'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function popolate_job_data(data)
{
  var displayValueCompany=document.getElementById('displayValueCompany').value;
  var displayValueYear=document.getElementById('displayValueYear').value;
  $job_no=trim(displayValueCompany)+'-'+trim(displayValueYear)+'-'+padToFive(trim(data));
  get_php_form_data($job_no, "populate_data_from_search_popup", "requires/trims_sample_booking_with_order_controller" );

}
function padToFive(number) {
  if (number<=99999) { number = ("0000"+number).slice(-5); }
  return number;
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
                    
                  <!-- <ul style="position:absolute;width:55px;height:20px;border:0;padding:0;margin:0;">
                    <select style="position:absolute;width:55px; height:20px;line-height:16px;margin:0;padding:0;border-radius: 3px;border-radius: .5em;padding:2px 3px 2px 5px;border:1px solid #676767;" onchange="document.getElementById('displayValue').value=this.options[this.selectedIndex].text; document.getElementById('idValue').value=this.options[this.selectedIndex].value;">
                    <option></option>
                    <option value="one">one</option>
                    <option value="two">two</option>
                    <option value="three">three</option>
                    </select>
                    <input name="displayValueCompany" placeholder="add/select a value" id="displayValueCompany" style="position:absolute;width:35px;width:32px\9;#width:35px;height:18px; height:16px\9;#height:18px;border:1px solid #676767; border-radius: 3px;border-radius: .5em;" onfocus="this.select()" type="text">
                    <input name="idValue" id="idValue" type="hidden">
                    </ul>
                    
                    <ul style=" position:relative; left:50px">
                    <select style="position:absolute;width:50px; height:20px;line-height:16px;margin:0;padding:0;border-radius: 3px;border-radius: .5em;padding:2px 3px 2px 5px;border:1px solid #676767;" onchange="document.getElementById('displayValue').value=this.options[this.selectedIndex].text; document.getElementById('idValue').value=this.options[this.selectedIndex].value;">
                    <option></option>
                    <option value="one">one</option>
                    <option value="two">two</option>
                    <option value="three">three</option>
                    </select>
                    <input name="displayValueYear" placeholder="add/select a value" id="displayValueYear" style="position:absolute;width:30px;width:27px\9;#width:30px;height:18px; height:16px\9;#height:18px;border:1px solid #676767; border-radius: 3px;border-radius: .5em;" onfocus="this.select()"  type="text">
                    <input name="idValue" id="idValue" type="hidden">
                   </ul>
                   </ul>
                    
                    <ul style=" position:relative; left:95px">
                    <input name="displayValueNum" placeholder="add/select a value" id="displayValueNum" style="position:absolute;width:55px;height:18px;border:1px solid #676767; border-radius: 3px;border-radius: .5em;" onfocus="this.select()" type="text" onChange="popolate_job_data(this.value)">
                    <input name="idValue" id="idValue" type="hidden">
                   </ul>-->
                    
                    </td>
                    <td  width="130" height="" align="right" class="must_entry_caption">Booking No </td>              
                    <td  width="170" >
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/trims_sample_booking_with_order_controller.php?action=trims_booking_popup','Trims Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                    <td align=""><input type="hidden" id="id_approved_id"></td>
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
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/trims_sample_booking_with_order_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Double Click for Job" name="txt_job_no" id="txt_job_no" readonly />                               
                                </td>
                              <td  align="right" class="must_entry_caption">Company Name</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_sample_booking_with_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );","","" );
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
                               <td  width="130" align="right">Booking Date</td>
                            <td width="170">
                                <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo date("d-m-Y")?>" disabled/>	
                            </td>
                               
                             <td  align="right" class="must_entry_caption">Pay Mode</td>
                                <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/trims_sample_booking_with_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' )","" );
							   ?> 
                                 </td> 
                               
                      </tr>
                        <tr>
                            <td  width="130" align="right">Delivery Date</td>
                                <td width="170">
                                    <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/>	
                                </td>
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
                        </tr>
                        <tr>
                        
                             <td  align="right" class="must_entry_caption">Supplier Name</td>
                              <td id="supplier_td">
                               <?
							   		echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_with_order_controller');",0 );//find_in_set(4,party_type) party_type in (4,5)
									
							   		//echo create_drop_down( "cbo_supplier_name", 172, "select sup.id,sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b where  sup.status_active=1 and sup.is_deleted=0 and sup.id in (Select supplier_id from  lib_supplier_party_type where party_type in (4,5)) order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );//find_in_set(4,party_type)
							   ?> 
                             </td>
                             <td  width="130" height="" align="right"> Source </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", 3, "","" );
							   ?>
                                </td>
                        	 
                            <td  width="130" height="" align="right"> Select Item </td>              <!-- 11-00030  -->
                            <td  width="170" >
                                <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data('requires/trims_sample_booking_with_order_controller.php?action=fnc_process_data','Trim Booking Search')" readonly placeholder="Double Click for Booking" name="txt_select_item" id="txt_select_item"/>
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">Attention</td>   
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                            	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/trims_sample_booking_with_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                            </td>
							<td align="right">Tenor</td>
                            <td><input style="width:160px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                        	
                        </tr>
                        <tr>
                        <td align="right">Ready To Approve</td>  
                        	<td align="center">
                              <?
							   		echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
							   ?>
                            </td> 
                            <td align="right" class="must_entry_caption">Material Source</td>
                            <td><? echo create_drop_down( "cbo_material_source", 172, $fabric_source,"", 1, "-- Select Source --", "2", "","","","","","1,4" );?></td>
							<td align="center" height="10" colspan="6">
    						<? 
								include("../../terms_condition/terms_condition.php");
								terms_condition(142,'txt_booking_no','../../');
							?>                        
                            <!--<input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/trims_sample_booking_with_order_controller.php?action=terms_condition_popup','Terms and Condition')" />-->
                            
                            
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,".$date."','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val')",1) ; ?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" height="10">
                           <div id="pdf_file_name"></div>
                            <input type="button" value="Print Booking" onClick="generate_trim_report()"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                            Copy:<input type="checkbox" id="copy_val"  name="copy_val" checked/>                        
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