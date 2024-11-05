<?
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
Comments		         :  From this version oracle conversion is start
-----------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;


//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,'','');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1400px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			reset_form('fabricbooking_1','booking_list_view','');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_booking_controller" );
			check_month_setting();
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			fnc_show_booking(1)
		}
	}
}


function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/fabric_booking_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
	
}

function check_month_setting()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/fabric_booking_controller');
	
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
	
	/*if (form_validation('cbo_booking_month*cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false)
	{
		return;
	}*/
	
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/fabric_booking_controller');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=470px,center=1,resize=1,scrolling=0','../')
		}
		else
		{
			var r=confirm("Existing Item against these Order  Will be Deleted")
			if(r==true)
			{
			var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/fabric_booking_controller');
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
				get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/fabric_booking_controller" );
				check_month_setting();
				release_freezing();
				fnc_generate_booking()
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
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_job_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*txt_booking_percent',"../../");
		http.open("POST","requires/fabric_booking_controller.php",true);
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
	var month_set_id=$('#month_id').val();
	if(month_set_id==1)
	{
		if (form_validation('cbo_booking_month','Booking Month')==false)
		{
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
		
	if (form_validation('txt_order_no_id*txt_booking_date','Order No*Booking Date')==false)
	{
		return;
	}
	if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0)
	{
		alert("Select Supplier Name")
		return;
	}
	if(document.getElementById('full_booked').innerHTML=="Full Booked")
	{
		alert ("No Item Found");
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*txt_booking_percent*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_ready_to_approved*processloss_breck_down*txt_fabriccomposition',"../../");
		freeze_window(operation);
		http.open("POST","requires/fabric_booking_controller.php",true);
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
		 set_button_status(1, permission, 'fnc_fabric_booking',1);
		 }
		 show_msg(trim(reponse[0]));
		 release_freezing();
	}
}

function fnc_fabric_booking_dtls( operation )
{
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}
	var row_num=$('#tbl_fabric_booking tr').length;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('txt_order_no_id*txt_booking_date*txt_booking_no','Order No*Booking Date*Booking No')==false)
		{
		return;
		}	
		data_all=data_all+get_submitted_data_string('txt_booking_no*txt_job_no*selected_id_for_delete*pobreakdownid_'+i+'*precostfabriccostdtlsid_'+i+'*cotaid_'+i+'*colorid_'+i+'*finscons_'+i+'*greycons_'+i+'*rate_'+i+'*amount_'+i+'*colortype_'+i+'*construction_'+i+'*composition_'+i+'*gsmweight_'+i+'*diawidth_'+i+'*processlosspercent_'+i+'*colarculfpercent_'+i+'*updateid_'+i,"../../",i);
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
				http.open("POST","requires/fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
			}
			else
			{
				//release_freezing();
				return;
			}
		}
		
		if(document.getElementById('selected_id_for_delete').value!="")
		{
			var r=confirm("Selected item will be deleted");
			if(r==true)
			{
				http.open("POST","requires/fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
			}
			else
			{
				//release_freezing();
				return;
			}
		}
	}
	else
	{
		http.open("POST","requires/fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}
}
	 
function fnc_fabric_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		 set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
		 fnc_show_booking(1)
		// release_freezing();
	}
}


var selected_id = new Array;
function select_id_for_delete_item(str)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/fabric_booking_controller');
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
	if(type==2 && document.getElementById('id_approved_id').value==1)
	{
	
		alert("This booking is approved")
		return;
	
	}
	
	if(type==2)
	{
	
		document.getElementById('app_sms3').innerHTML = ''
	
	}
	
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		//get_submitted_data_string('txt_job_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source',"../../")
		freeze_window(5);
		var data="action=show_fabric_booking"+get_submitted_data_string('txt_job_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*txt_booking_no*txt_booking_percent',"../../")+"&type="+type;
		http.open("POST","requires/fabric_booking_controller.php",true);
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
		set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
		selected_id=[]
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
		 get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/fabric_booking_controller' );
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
		if(user_given> real_value)
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
if (form_validation('txt_booking_no','Booking No')==false)
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
		//freeze_window(5);
		http.open("POST","requires/fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}	
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel').html( http.responseText );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;
		//alert(content)
		//var conversion_qnty=return_global_ajax_value(content, 'create_file', '', 'requires/fabric_booking_controller');
		
		/*$.post(rel_path+"requires/fabric_booking_controller.php",
			  { path: rel_path, action: "generate_report_file", htm_doc: tto },
			  function(data){
				window.open(rel_path+data, "#");
			  }
			);*/
		
		$.post("requires/fabric_booking_controller.php", { action: "create_file", data: content } );
		//$("#print_booking2").click();
		
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
		http.open("POST","requires/fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report2_reponse;
	}	
}

function generate_fabric_report2_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel').html( http.responseText );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;
		//alert(content)
		//var conversion_qnty=return_global_ajax_value(content, 'create_file', '', 'requires/fabric_booking_controller');
		
		/*$.post(rel_path+"requires/fabric_booking_controller.php",
			  { path: rel_path, action: "generate_report_file", htm_doc: tto },
			  function(data){
				window.open(rel_path+data, "#");
			  }
			);*/
		
		$.post("requires/fabric_booking_controller.php", { action: "create_file", data: content } );
		
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
	var page_link = 'requires/fabric_booking_controller.php?data='+data+'&action=unapp_request_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
		
		$('#txt_un_appv_request').val(unappv_request.value);
	}
}

	
	function copy_colarculfpercent(count)
	{
		var rowCount = $('#tbl_fabric_booking tr').length;
		//alert(rowCount)
		var bodypartid=document.getElementById('bodypartid_'+count).value;
		var gmtssizeid=document.getElementById('gmtssizeid_'+count).value;
		var colarculfpercent=document.getElementById('colarculfpercent_'+count).value;
		for(var j=count; j<=rowCount; j++)
		{
			
			if(document.getElementById('bodypartid_'+j).value==2 || document.getElementById('bodypartid_'+j).value==3)
			{
				if( gmtssizeid==document.getElementById('gmtssizeid_'+j).value)
				{
			        //alert(colarculfpercent)
					document.getElementById('colarculfpercent_'+j).value=colarculfpercent;
				}
			}
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
	//var delivery_date_tna = new Date(txt_delivery_date_tna_data_inv);
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
		if(txt_delevary_date > txt_tna_date){
			alert('Delivery Date is greater than TNA Date');
			document.getElementById('txt_delivery_date').value=document.getElementById('txt_tna_date').value;
			//return;
		}
	}
	//return
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
                                	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/fabric_booking_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                                
                                 
                                </td>
                               <td align="right" width="130" >
                               <input type="hidden" id="id_approved_id">
                                <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                                </td>
                                <td>	
                                    
                                </td>
							
                        </tr>
                        <tr>
                            
                    <td  align="right">Company Name</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier()",1,"" );
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
                            
                                <td align="right" id="booking_td">Booking Month</td>   
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
                                    <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" />	
                                </td>
                            <td height="" align="right" class="must_entry_caption">Selected Order No</td>   
                            <td colspan="3">
                                 <input class="text_boxes" type="text" style="width:97%;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/fabric_booking_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
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
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "load_drop_down( 'requires/fabric_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" );
							   ?> 
                                 </td>
                        
                       
                        	<td  align="right">Supplier Name</td>
                                <td>
                               <?
							   		//echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where find_in_set(9,party_type) and  status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
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
                             <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly />  
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
                            	<!--<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/fabric_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">-->
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
                        	<td align="left" height="10" colspan="3">
                            <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/fabric_booking_controller.php?action=terms_condition_popup','Terms Condition')" />
                            <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/fabric_booking_controller.php?action=rmg_process_loss_popup','Process Loss %')" />
                            <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" /> 
                            <input type="button" id="set_button" class="text_boxes" style="width:120px;" value="Trims Dyes To Match" onClick="dtm_popup('requires/fabric_booking_controller.php?action=dtm_popup','DTM')"  />
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
                              <? echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100')",1) ; ?>
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
</html>