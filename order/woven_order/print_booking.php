<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
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
Report Created BY: Aziz
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
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

<? $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][161] );
echo "var field_level_data= ". $data_arr . ";\n";
?>


function openmypage(page_link,title)
{
	//var date= new Date();
	//alert(date.getDate()  + '-' + (date.getMonth() + 1) + '-' +  date.getFullYear());
	var company = $("#cbo_company_name").val();
	var budget_version = $("#cbo_budget_version").val();
		
	page_link+='&company='+company+'&budget_version='+budget_version;
	
	var d = new Date();
    var date=d.yyyymmdd();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		var theemail2=this.contentDoc.getElementById("selected_budget_id") //Access form field with id="emailfield"
		if (theemail.value!="") 
		{
			freeze_window(5);
			reset_form('printbooking_1','booking_list_view*booking_list_view2','','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_booking_month,1','','cbo_budget_version');
			get_php_form_data( theemail.value, "populate_order_data_from_search_popup", "requires/print_booking_controller" );
			document.getElementById('txt_booking_date').value=date;
			//document.getElementById('cbo_budget_version').value=theemail2.value;
			
			single_select()
			
			release_freezing();
		}
	}
}

function single_select()
{
if($('#txt_order_no_id option').length==2)
	{
		if($('#txt_order_no_id option:first').val()==0)
		{
			$('#txt_order_no_id').val($('#txt_order_no_id option:last').val());
			eval($('#txt_order_no_id').attr('onchange')); 
		}
	}
	else if($('#txt_order_no_id option').length==1)
	{
		$('#txt_order_no_id').val($('#txt_order_no_id option:last').val());
		eval($('#txt_order_no_id').attr('onchange'));
	}	
	
	if($('#cbo_gmt_item option').length==2)
	{
		if($('#cbo_gmt_item option:first').val()==0)
		{
			$('#cbo_gmt_item').val($('#cbo_gmt_item option:last').val());
			eval($('#cbo_gmt_item').attr('onchange')); 
		}
	}
	else if($('#cbo_gmt_item option').length==1)
	{
		$('#cbo_gmt_item').val($('#cbo_gmt_item option:last').val());
		eval($('#cbo_gmt_item').attr('onchange'));
	}	
}

Date.prototype.yyyymmdd = function() {         
                                
        var yyyy = this.getFullYear().toString();                                    
        var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based         
        var dd  = this.getDate().toString();             
                            
        //return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
		return (dd[1]?dd:"0"+dd[0])+ '-' + (mm[1]?mm:"0"+mm[0])+ '-' + yyyy ;

   };  



function fnc_generate_booking()
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var cbo_booking_natu=document.getElementById('cbo_booking_natu').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	//alert(cbo_booking_natu);
	var txt_job_no=document.getElementById('txt_job_no').value;
	var txt_order_no_id=document.getElementById('txt_order_no_id').value;
	var cbo_gmt_item=document.getElementById('cbo_gmt_item').value
	var cbo_booking_natu=document.getElementById('cbo_booking_natu').value;
	var budget_version=document.getElementById('cbo_budget_version').value;
	var txt_booking_date=document.getElementById('txt_booking_date').value;
	var booking=return_global_ajax_value(txt_booking_no+"_"+txt_job_no+"_"+txt_order_no_id+"_"+cbo_gmt_item+"_"+cbo_booking_natu, 'delete_row_fabric_cost', '', 'requires/print_booking_controller');
	
	if(budget_version==2)
	{
	var emb_vari_seting=return_global_ajax_value(cbo_company_name+"_"+cbo_booking_natu, 'check_row_embl_setting', '', 'requires/print_booking_controller');
	var emb_vari_seting_id=trim(emb_vari_seting);
		if(emb_vari_seting_id!=0)
		{
		 $("#calculation_basis").val(emb_vari_seting_id);
		  	$('#calculation_basis').attr('disabled','disabled');
			
		 }
		 else
		 {
		 	$('#calculation_basis').removeAttr('disabled','disabled');
		 }
	}
	
	if (form_validation('txt_order_no_id*cbo_booking_natu*cbo_gmt_item','Order No*Fabric Nature*Gmt Item')==false)
	{
		return;
	}
	
	else
	{
		
		var data="action=generate_print_booking"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*txt_booking_no*txt_booking_date*txt_delivery_date*calculation_basis',"../../");
		if(budget_version==1)
		{
			http.open("POST","requires/print_booking_controller.php",true);
		}
		else
		{
			http.open("POST","requires/print_booking_controller2.php",true);
		}
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
		set_all_onclick();
		//set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
	}
}

function fn_empty_dtls()
{
	document.getElementById('booking_list_view').innerHTML="";
	set_all_onclick();
}

function fnc_show_booking()
{
	if (form_validation('txt_order_no_id*cbo_booking_natu*cbo_gmt_item','Order No*Fabric Nature*Gmt Item')==false)
	{
		return;
	}
	else
	{
		var data="action=show_print_booking"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*txt_booking_no',"../../");
		http.open("POST","requires/print_booking_controller.php",true);
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
		set_all_onclick();
		//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
	}
}

function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			reset_form('printbooking_1','booking_list_view*booking_list_view2','','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_booking_month,1');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/print_booking_controller" );
			
			get_php_form_data( document.getElementById('txt_job_no').value, "populate_order_data_from_search_popup", "requires/print_booking_controller" );
			show_list_view(theemail.value,'print_booking_list_view','booking_list_view2','requires/print_booking_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			release_freezing();
		}
	}
}

function fn_change_emb_name()
{
	get_php_form_data('', 'change_emb_name', 'requires/print_booking_controller')	
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
	if (form_validation('txt_job_no*txt_booking_date*txt_delivery_date','Job No*Booking Date*Delivery Date')==false)
	{
		return;
	}	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_booking_no*id_approved_id*txt_job_no*cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date*txt_delivery_date*cbo_currency*txt_exchange_rate*cbo_supplier_name*cbo_pay_mode*cbo_source*cbo_ready_to_approved*txt_attention*calculation_basis*cbo_budget_version*txt_tenor*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/print_booking_controller.php",true);
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
		show_msg(trim(reponse[0]));
		document.getElementById('txt_booking_no').value=reponse[1];
		$('#cbo_budget_version').attr('disabled',true);
		document.getElementById('update_id').value=reponse[2];
		set_button_status(1, permission, 'fnc_fabric_booking',1);
		release_freezing();
	}
}

function fnc_fabric_booking_dtls( operation )
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
	if (form_validation('txt_booking_no*txt_order_no_id*cbo_gmt_item*cbo_booking_natu','Booking No*Order No*Garments Item*Emblishment Name')==false)
	{
		return;
	}	
	var data_all=get_submitted_data_string('txt_booking_no*update_id*txt_job_no*txt_order_no_id*cbo_gmt_item*cbo_booking_natu',"../../");
	var row_num=$('#tbl_list_search tr').length;
	
	for (var i=1; i<=row_num; i++)
	{
		var amount=$('#txtamount_'+i).val()*1;
		var amount_precost=$('#txtamount_precost_'+i).val()*1;
		if(amount_precost<amount)
		{
			//alert("Amount Exceeds Pre Cost Amount");return;   // Validation stopped by CTO on request of Beeresh Vai for Group on 03-08-2015
		}//
		
	data_all=data_all+get_submitted_data_string('txtbookingdtlasid_'+i+'*txtpoid_'+i+'*txtitemnumberid_'+i+'*txtembcostid_'+i+'*txtcolorid_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*description_'+i+'*txtamount_precost_'+i+'*txtuomid_'+i,"../../",i);
	}
	var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","requires/print_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
}

function fnc_fabric_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));
		
		if(reponse[0]==0 || reponse[0]==1)
		{
			show_list_view(trim(reponse[1]),'print_booking_list_view','booking_list_view2','requires/print_booking_controller','setFilterGrid(\'list_view\',-1)');
			fnc_generate_booking();
			release_freezing();
		}
		else if(reponse[0]==2)
		{
			show_list_view(trim(reponse[1]),'print_booking_list_view','booking_list_view2','requires/print_booking_controller','setFilterGrid(\'list_view\',-1)');
			document.getElementById('booking_list_view').innerHTML="";
			set_all_onclick();
			release_freezing();
		}
		else if(trim(reponse[0])=='budgetOver'){
			alert("Budget Over :"+trim(reponse[2])+"\n So Save/Update Not Possible")
			release_freezing();
			return;
		}
		else
		{
			release_freezing();
		}
	}
}

function update_booking_data(data)
{
	var data=data.split("_");
	document.getElementById('txt_order_no_id').value=data[0];
	document.getElementById('cbo_gmt_item').value=data[1];
	document.getElementById('cbo_booking_natu').value=data[2];
	fnc_generate_booking()
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

function calculate_amount(i)
{
	
	//alert('nnn');
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
	var txtrate=(document.getElementById('txtrate_'+i).value)*1
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	if(txtrate>txtrate_precost)
	{
		alert("Rate Exceeds Pre-Cost Rate");
		document.getElementById('txtrate_'+i).value=number_format_common(txtrate_precost,5,0)
		document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtwoq),5,0)

		return
	}
	
	//alert(txtrate*txtwoq)
	document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
}

function calculate_amount2(i)
{
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1;
	var txtrate=(document.getElementById('txtrate_'+i).value)*1;
	var txtbalqty=(document.getElementById('txtbalqty_'+i).value)*1;
	
	var txtamount_precost=(document.getElementById('txtamount_precost_'+i).value)*1
	var txtamount=(document.getElementById('txtamount_'+i).value)*1
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	var bal_qty_precost=(document.getElementById('txt_bal_qty_precost_'+i).value)*1
	if(txtamount>txtamount_precost)
	{
		alert("Amount Exceeds Pre-Cost Amount");
		document.getElementById('txtwoq_'+i).value=number_format_common((txtbalqty),2,0)
		//document.getElementById('txtamount_'+i).value=number_format_common(txtamount_precost,5,0)
	//	document.getElementById('txtwoq_'+i).value=number_format_common(bal_qty_precost,5,0)
		//document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtamount_precost),5,0)

		return
	}
	//alert(txtrate*txtwoq)
	//document.getElementById('txtamount_'+i).value=number_format_common((txtamount_precost),5,0);
}

function generate_fabric_report(type)
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		$report_title=$( "div.form_caption" ).html();
		var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title;
		//freeze_window(5);
		http.open("POST","requires/print_booking_controller.php",true);
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
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function generate_trim_report(action)// Report here
{ 
	var budget_version=document.getElementById('cbo_budget_version').value;
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
		
		//var data="action="+action+get_submitted_data_string('txt_booking_no*txt_job_no*cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_currency*cbo_supplier_name*cbo_pay_mode*txt_exchange_rate*cbo_source*cbo_booking_natu',"../../");
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_job_no*cbo_supplier_name*cbo_buyer_name*txt_delivery_date*txt_booking_date*cbo_source*cbo_pay_mode*cbo_currency*calculation_basis',"../../")+'&show_comment='+show_comment;
		if(budget_version==1)
		{
			http.open("POST","requires/print_booking_controller.php",true);
		}
		else
		{
			http.open("POST","requires/print_booking_controller2.php",true);
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
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/print_booking_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
	
}
</script>

</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form name="printbooking_1"  autocomplete="off" id="printbooking_1">
    <fieldset style="width:1000px;">
    <legend>Print Booking</legend>
        <table width="1000" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td align="right" class="must_entry_caption" colspan="4">Wo No</td>              
                <td colspan="4">
                    <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking('requires/print_booking_controller.php?action=fabric_booking_popup','Print Booking Search');" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    <input type="hidden" id="id_approved_id">
                </td>
            </tr>
            <tr>
                <td width="110">Job No.</td>
                <td width="140"><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" onDblClick="openmypage('requires/print_booking_controller.php?action=order_popup','Job/Order Selection Form');" placeholder="Double Click" readonly/></td>
                <td width="110">Company Name</td>
                <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/print_booking_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1,"" ); ?></td>
                <td width="110">Buyer Name</td>   
                <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                <td width="110">WO Month</td>   
                <td> 
					<? 
                    echo create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", 1, "",0 );		
                    echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
                </td>
            </tr>
            <tr>
                <td class="must_entry_caption">WO Date</td>
                <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" value="<?=date("d-m-Y");?>" disabled/></td>
                <td class="must_entry_caption">Delivery Date</td>
                <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                <td>Currency</td>
                <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 );	?></td>
                <td>Exchange Rate</td>
                <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
            </tr>
            <tr>
            	<td>Pay Mode</td>
                <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 1, "","" ); ?></td>
                <td>Source</td>              <!-- 11-00030  -->
                <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", 3, "","" ); ?></td>
                <td>Supplier Name</td>
                <td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/print_booking_controller');",0 ); ?></td> 
                <td>Tenor</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
            </tr>
            <tr>
                <td>Attention</td>   
                <td colspan="3"><input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention" /></td>
                <td>Calculation Basis</td>   
                <td><?=create_drop_down( "calculation_basis", 130, $calculation_basis,"", 0, "", "", "","","" ); ?></td>
                <td>Budget Version</td>
                <td>				
                    <?
                    $pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
                    echo create_drop_down( "cbo_budget_version", 130, $pre_cost_class_arr,"", 0, "-- Select Version --",2);
                    ?>	
                </td> 
            </tr>
            <tr>
                <td>Ready To Approved</td>  
                <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
				<td>&nbsp;</td>   
                <td>
					<?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(161,'txt_booking_no','../../');
                    ?>
                     <input type="hidden" id="update_id" >
                     <input type="hidden" id="dtls_update_id" >
                </td>
            </tr>
            <tr>
                <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                </td>
            </tr>
            <tr>
                <td align="center" colspan="8" valign="middle" class="button_container">
                <? 
				$date=date("d-m-Y");
				echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('printbooking_1','booking_list_view','','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*txt_booking_date,$date*cbo_booking_month,1')",1) ; 
				?>
                </td>
            </tr>
            <tr>
         		<td align="center"  valign="middle" colspan="8">
           <!--<div id="pdf_file_name"></div>-->
           <input type="button" value="Print" onClick="generate_trim_report('show_trim_booking_report')"  style="width:80px" name="print_booking" id="print_booking" class="formbutton" /> 
           <input type="button" value="Print Actual" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:80px" name="print_booking" id="print_booking" class="formbutton" /> 
           
            <input type="button" value="Print 3" onClick="generate_trim_report('show_trim_booking_report2')"  style="width:80px" name="print_booking" id="print_booking" class="formbutton" />   
         		</td>
  		 </tr>
        </table>
    </fieldset>
</form>


    <form id="printbooking_2" name="printbooking_2" autocomplete="off">
        <fieldset style="width:1247px;">
        <legend>Details</legend>
                <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td height="" align="right" class="must_entry_caption">Order No</td>   
                        <td id="po_id_td">
                        <? echo create_drop_down( "txt_order_no_id", 172, $blank_array,"", 1, "-- Select PO --", "", "fn_empty_dtls()","",""); ?>
                        </td> 
                        <td align="right" width="130" class="must_entry_caption">
                        Gmt Item
                        </td>
                        <td id="gmt_item_td">	
                        <? 
                        echo create_drop_down( "cbo_gmt_item", 172, $blank_array,"", 1, "-- Select --", "","", "", "");		
                        ?>
                        </td>
                        <td align="right" class="must_entry_caption">Embl Name</td>
                        <td id="booking_natu_td">
                        <? 
                        echo create_drop_down( "cbo_booking_natu", 172, $blank_array,"", 1, "-- Select --", "","","","");		
                        ?>	
                        </td>
                    </tr>
                    <tr align="center">
                        <td colspan="6" id="booking_list_view"></td>	
                    </tr>
                    <tr align="center">
                        <td colspan="6" id="booking_list_view2"></td>	
                    </tr>
                </table>
        </fieldset>
    </form>


</div>
<div style="display:none" id="data_panel"></div>


</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>