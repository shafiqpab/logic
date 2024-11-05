<?
/*-------------------------------------------- Comments 

Purpose			: 	This form will create Yarn Dyeing Charge Booking
					
Functionality	:	

JS Functions	:

Created by		:	Jahid
Creation date 	: 	04-03-2013
Updated by 		: 		
Update date		: 		   

QC Performed BY	:		

QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function openmypage_job(title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}	
	else
	{
	
		var company = $("#cbo_company_name").val();
		//alert(company);
		page_link='requires/yarn_dyeing_booking_without_lot_controller.php?action=order_search_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=390px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_tbl_id").value.split("_");
			//alert(data[0]);
			freeze_window(5);
			document.getElementById('txt_job_id').value=data[0];
			document.getElementById('txt_job_no').value=data[1];
			document.getElementById('txt_file_no').value=data[2];
			document.getElementById('txt_int_ref_no').value=data[3];
			load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller', data[1], 'load_drop_down_color', 'color_td' );
			load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller', data[1], 'load_drop_down_compisition', 'composition_td' );
			load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller', data[1], 'load_drop_down_yarn_type', 'yarn_type_td' );
			release_freezing();
	
		}
	}
}

function fnc_yarn_dyeing(operation)
{
	
	if( form_validation('cbo_company_name*cbo_supplier_name*txt_booking_date*cbo_source*txt_job_no*cbo_count*cbo_composition*txt_pacent*cbo_yarn_type*txt_yern_color*txt_wo_qty*cbo_pay_mode','Company Name *Supplier Name*Booking Date*Source Name*Job Number*Count*Composition*Percent*Yarn Type*Color*Order Quanty*Pay Mode')==false )
	{
		return;
	}
	else
	{
		var exchange_rate=$('#txt_exchange_rate').val()*1;
		if(exchange_rate<=0)
		{
			alert("Exchange Rate Must be Greater then 0");
			return;
		}
		
		var dataString = "cbo_company_name*cbo_supplier_name*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_attention*txt_delivery_date*txt_delivery_end*dy_delevery_start*dy_delevery_end*cbo_item_category_id*txt_job_no*cbo_count*txt_item_des*txt_yern_color*cbo_color_range*cbo_uom*txt_wo_qty*txt_dyeing_charge*txt_amount*txt_bag*txt_cone*update_id*txt_booking_no*txt_job_id*dtls_update_id*txt_min_req_cone*cbo_source*txt_ref_no*txt_file_no*txt_int_ref_no*txt_remarks*cbo_is_short*cbo_composition*txt_pacent*cbo_yarn_type*txt_app_batch_no";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//alert(data);
		freeze_window(operation);
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_dyeing_response;
	}
	
}

function fnc_yarn_dyeing_response()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		//alert (response);
		if(response[0]==40)
		{
			alert(response[1]);release_freezing();return;
		}
		
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			show_msg(trim(response[0]));
			$('#cbo_company_name').attr('disabled',true);
			release_freezing();

		}
		else if(response[0]==10||response[0]==11)
		{
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}
		$("#update_id").val(response[2]);
		$("#txt_booking_no").val(response[1]);
		show_list_view(response[2],'show_dtls_list_view','list_container','requires/yarn_dyeing_booking_without_lot_controller','');
		set_button_status(0, permission, 'fnc_yarn_dyeing',1,1);
		reset_form('','','txt_yern_color*cbo_color_range*txt_wo_qty*txt_dyeing_charge*txt_amount*txt_bag*txt_cone*txt_min_req_cone*txt_ref_no*txt_file_no*txt_int_ref_no*txt_remarks*txt_budget_wo_qty*dtls_update_id*txt_app_batch_no','','','');
 	}
}


function fnc_calculate()
{
	var is_short=$('#cbo_is_short').val();
	var wo_qty=$('#txt_wo_qty').val();
	var budget_wo_qty=$('#txt_budget_wo_qty').val();
	
	if(is_short==2)
	{
		if((wo_qty*1)>(budget_wo_qty*1))
		{
			alert("Work Order Quantity Does Not Allow More Then Fabric Required.");
			$('#txt_wo_qty').val("");
			$('#txt_wo_qty').focus();
			return;
		}
	}
	
	var dyeing_charge=$('#txt_dyeing_charge').val();
	//alert(dyeing_charge);
	var amount=(wo_qty*1)*(dyeing_charge*1);
	$('#txt_amount').val(number_format_common( amount, 2));
}

function chack_variable(id)
{
	//alert(id);
	var data="action=variable_chack&company="+id;
	http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange =  fnc_variable_response;
}

function fnc_variable_response()
{
	if(http.readyState == 4) 
	{
		
		var response=trim(http.responseText).split('**');
		if(response==1)
		{
			$('#dyeing_charge_td').html('<input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric" placeholder="Browse" onDblClick="openmypage_charge()" readonly />');
		}
		else
		{
			$('#dyeing_charge_td').html('<input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric"  onKeyUp="fnc_calculate()"/>');
		}

 	}
}


function openmypage_booking()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	page_link='requires/yarn_dyeing_booking_without_lot_controller.php?action=yern_dyeing_booking_popup&company='+company;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Yarn Dyeing Booking Search', 'width=950px, height=450px, center=1, resize=0, scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_number=this.contentDoc.getElementById("hidden_sys_number").value.split("_");
		
		if(sys_number!="")
		{	
		//alert(b_date);
			freeze_window(5);
			
			/*$("#txt_booking_no").val(mst_id[1]);
			$("#cbo_company_name").val(mst_id[2]);
			$("#cbo_supplier_name").val(mst_id[3]);
			$("#txt_booking_date").val(change_date_format(mst_id[4]));
			$("#txt_delivery_date").val(change_date_format(mst_id[5]));
			$("#cbo_currency").val(mst_id[6]);
			$("#txt_exchange_rate").val(mst_id[7]);
			$("#cbo_pay_mode").val(mst_id[8]);
			$("#txt_attention").val(mst_id[9]);
			$("#cbo_source").val(mst_id[10]);
			$("#txt_delivery_end").val(change_date_format(mst_id[11]));
			$("#dy_delevery_start").val(change_date_format(mst_id[12]));
			$("#dy_delevery_end").val(change_date_format(mst_id[13]));
			
			$("#update_id").val(mst_id[0]);*/
			//reset_form('','','txt_item_description*txt_quantity*cbo_uom*txt_rate*txt_amount','','','');
			
			get_php_form_data(sys_number[0], "populate_master_from_data", "requires/yarn_dyeing_booking_without_lot_controller" );
			show_list_view(sys_number[0],'show_dtls_list_view','list_container','requires/yarn_dyeing_booking_without_lot_controller','');
			$('#cbo_company_name').attr('disabled',true);
			set_button_status(0, permission, 'fnc_yarn_dyeing',1,1);
			release_freezing();
		}
	}
}



function openmypage_charge()
{
	if (form_validation('cbo_company_name*txt_job_no','Company Name*Job Number')==false)
	{
		return;
	}	
	else
	{
		var company = $("#cbo_company_name").val();
		//alert(company);
		page_link='requires/yarn_dyeing_booking_without_lot_controller.php?action=dyeing_search_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Charge', 'width=600px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_rate").value;
			//alert(data);
			freeze_window(5);
			document.getElementById('txt_dyeing_charge').value=data;
			release_freezing();
			fnc_calculate();

		}
	}
}


/*function openmypage_lot()
{
	if (form_validation('cbo_company_name','Company Number')==false)
	{
		return;
	}	
	else
	{
	
		var company = $("#cbo_company_name").val();
		var job_no = $("#txt_job_no").val();
		//alert(job_no);
		page_link='requires/yarn_dyeing_booking_without_lot_controller.php?action=lot_search_popup&company='+company+'&job_no='+job_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Lot Number Search', 'width=630px,height=380px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_product").value.split(",");
			//alert(data[0]);return;
			freeze_window(5);
			//document.getElementById('txt_item_des').value=data[0];
			//document.getElementById('cbo_count').value=data[1];
			//document.getElementById('txt_lot').value=data[2];
			//document.getElementById('txt_pro_id').value=data[3];
			$('#txt_item_des').val(data[0]).attr('disabled',true);
			$('#cbo_count').val(data[1]).attr('disabled',true);
			$('#txt_lot').val(data[2]);
			$('#txt_pro_id').val(data[3]);
			release_freezing();

		}
	}
}*/

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


function generate_trim_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		} 
		
		var form_name="yarn_dyeing_wo_booking";
		var data="action=show_trim_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../")+'&show_comment='+show_comment+'&path=../../';
		//var data="action=show_trim_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
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
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}


function generate_print2_report()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		/*var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		} */
		
		var form_name="yarn_dyeing_wo_booking";
		var data="action=show_print2_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../")+'&show_comment='+show_comment+'&path=../../';
		
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_print2_report_reponse;
	}	
}

function generate_print2_report_reponse()
{
	if(http.readyState == 4) 
	{
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}




function generate_without_rate_report()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking";
		var data="action=show_without_rate_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		//var data="action=show_without_rate_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_without_rate_report_reponse;
	}	
}

function generate_without_rate_report_reponse()
{
	if(http.readyState == 4) 
	{
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}


function generate_multiple_job_report()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		} 
		
		
		
		var form_name="yarn_dyeing_wo_booking";
		var data="action=show_with_multiple_job"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../")+'&show_comment='+show_comment;
		//var data="action=show_with_multiple_job"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_multiple_job_report_reponse;
	}	
}

function generate_multiple_job_report_reponse()
{
	if(http.readyState == 4) 
	{
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}


function multiple_job_without_rate_report()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking";
		var data="action=show_with_multiple_job_without_rate"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		//var data="action=show_with_multiple_job_without_rate"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = multiple_job_without_qty_report_reponse;
	}	
}

function print2()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking";
		var data="action=print2"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		//var data="action=show_with_multiple_job_without_rate"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_dyeing_booking_without_lot_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = multiple_job_without_qty_report_reponse;
	}	
}

function multiple_job_without_qty_report_reponse()
{
	if(http.readyState == 4) 
	{
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}


function fnResetForm()
{
	reset_form('yarn_dyeing_wo_booking','list_container','','txt_booking_date,<? echo date("d-m-Y"); ?>','disable_enable_fields("txt_item_des*cbo_count",0)','cbo_uom');
	set_button_status(0, permission, 'fnc_yarn_dyeing',1,0);
}


function set_exchang(id)
{
	if(id==1)
	{
		$('#txt_exchange_rate').val(id).attr('disabled',true);
	}
	else
	{
		$('#txt_exchange_rate').val("").attr('disabled',false);
	}
}
function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/yarn_dyeing_booking_without_lot_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
		
	}
	
	//for print button
	function print_button_setting()
	{
		$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/yarn_dyeing_booking_without_lot_controller' ); 
	}
	 
	function print_report_button_setting(report_ids) 
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{

			if(report_id[k]==117)
			{
				$('#button_data_panel').append( '<input type="button"  id="print2_with_rate" class="formbutton" style="width:150px;" value="Print 2 With Rate"  name="print2_with_rate"  onClick="generate_print2_report()" />&nbsp;&nbsp;&nbsp;' );
			}

			if(report_id[k]==74)
			{
				$('#button_data_panel').append( '<input type="button"  id="print_with_rate" class="formbutton" style="width:150px;" value="Print Order With Rate"  name="print_with_rate"  onClick="generate_trim_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==75)
			{
				$('#button_data_panel').append( '<input type="button"  id="print_without_rate" class="formbutton" style="width:150px;" value="Print Order Without Rate"  name="print_without_rate"  onClick="generate_without_rate_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==76)
			{
				$('#button_data_panel').append( '<input type="button"  id="multiple_sample_with_rate" class="formbutton" style="width:150px;" value="Print With Multiple Job"  name="multiple_sample_with_rate"  onClick="generate_multiple_job_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==77)
			{
				$('#button_data_panel').append( '<input type="button"  id="multiple_sample_without_rate" class="formbutton" style="width:150px;" value="Multiple Job Without Rate"  name="multiple_sample_without_rate"  onClick="multiple_job_without_rate_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==78)
			{
				$('#button_data_panel').append( '<input type="button"  id="print_id" class="formbutton" style="width:80px;" value="Print"  name="print_id"  onClick="print2()" />&nbsp;&nbsp;&nbsp;' );
			}
			
		}
	}
	
</script>
 
</head>
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="yarn_dyeing_wo_booking"  autocomplete="off" id="yarn_dyeing_wo_booking">
    <fieldset style="width:1200px;">
    <legend>Yarn Dyeing Wo</legend>
        <table  width="1200" cellspacing="2" cellpadding="0" border="0">
            	<tr>
                    <td align="" width="120">&nbsp;</td>
                    <td align="" width="130">&nbsp;</td>
                    <td  width="120" height="" align="right" class="must_entry_caption"> Yarn Dyeing Wo No </td>              <!-- 11-00030  -->
                    <td  width="130" >
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking()" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" />
                    </td>
                    <td align="" width="120">&nbsp;</td>
                    <td align="" width="130">&nbsp;</td>
                    <td align="" width="120">&nbsp;</td>
                    <td align="" width="130">&nbsp;</td>
                </tr>
                
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                
                <tr>
                    <td align="right"><span class="must_entry_caption">Company Name</span></td>
                    <td><? 
                    //function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                    echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected,"chack_variable(this.value);load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting();",0);
                    ?></td>
                    <td  align="right" class="must_entry_caption">Yarn Dyeing Factory</td>
                    <td id="supplier_td">
                   
					<?
                    echo create_drop_down( "cbo_supplier_name", 140, $blank_array,"", 1, "-- Select Supplier --", $selected, "",0 );
                    ?>
                    </td>
                    <td align="right" class="must_entry_caption">Booking Date</td>   
                    <td  ><input class="datepicker" type="text" style="width:130px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                    <td align="right">Attention</td>   
                    <td ><input class="text_boxes" type="text" style="width:130px;"  name="txt_attention" id="txt_attention"/></td>
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td><? //set_exchang(this.value)
                    echo create_drop_down( "cbo_currency", 140, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 );		
                    ?></td>
                    <td align="right">Exchange Rate</td>
                    <td><input style="width:130px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                    
                    <td  align="right" class="must_entry_caption">Pay Mode</td>
                    <td ><?
                    echo create_drop_down( "cbo_pay_mode", 140, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" );
                    ?></td>
                    <td  align="right" class="must_entry_caption">Source</td>
                    <td ><?
                    	echo create_drop_down( "cbo_source", 140, $source,"", 1, "-- Select --", 3, "",0 );
                    ?></td>
                </tr>
                <tr>
                    <td  align="right">G/Y Issue Start</td>
                    <td><input class="datepicker" type="text" style="width:130px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td align="right" >G/Y Issue End</td>   
                    <td  height="10"><input class="datepicker" type="text" style="width:130px" name="txt_delivery_end" id="txt_delivery_end"/></td> 
                    <td align="right">D/Y Delivery Start</td>   
                    <td  height="10"><input class="datepicker" type="text" style="width:130px" name="dy_delevery_start" id="dy_delevery_start"/></td>
                    <td align="right">D/Y Delivery End</td>   
                    <td  height="10"><input class="datepicker" type="text" style="width:130px" name="dy_delevery_end" id="dy_delevery_end"/></td>
                </tr>
                 <tr>
                    <td  align="right">Item Category</td>
                    <td>
                     <?
					 
					 //function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
					 
					  echo create_drop_down( "cbo_item_category_id", 141, $item_category,'', 0, '',24,"",0,24); 
					 ?>  
                    </td>
                    <td  align="right">Is Short</td>
                    <td>
                     <?
					 
					 
					  echo create_drop_down( "cbo_is_short", 141, $yes_no,'', 0, '',2,""); 
					 ?>  
                    </td>
                </tr>
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td height="25" align="right" colspan="2">
                    
                    	<input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'yarn_dyeing_wo_booking', 0 ,1)">
                    </td>
                    <td align="center" height="10" colspan="2">
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(125,'txt_booking_no','../../');
                        ?>                    
                    <!--<input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup( 'requires/yarn_dyeing_booking_without_lot_controller.php?action=terms_condition_popup','Terms Condition'
)"/>-->
 			<input type="hidden"  class="text_boxes" id="report_ids" name="report_ids"  style="width:30px" />
					
                    </td>
                </tr>
                
               <tr>
                    <td align="center" height="10" colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td  colspan="8"> 
                        <table width="1300" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="dtls_part">
                            <thead>
                                <tr>
                                    <th width="80" class="must_entry_caption">Job No</th>
                                    <th width="70" style="display:none;">Lot No</th>
                                    <th width="70" class="must_entry_caption">Count</th>
                                    <th width="100" class="must_entry_caption">Composition</th>
                                    <th width="50" class="must_entry_caption">%</th>
                                    <th width="80" class="must_entry_caption">Yarn Type</th>
                                    <th width="90" class="must_entry_caption">Yarn Color</th>
                                    <th width="90">Color Range</th>
                                    <th width="60">Ref. No</th>
                                    <th width="50">UOM</th>
                                    <th width="50">Booking Balance</th>
                                    <th width="50" class="must_entry_caption">Yarn Wo. Qnty</th>
                                    <th width="50">Dyeing Charge</th>
                                    <th width="60">Amount</th>
                                    <th width="60">App. Batch No</th>
                                    <th width="40">No of Bag</th>
                                    <th width="40">No of Cone</th>
                                    <th width="40">Min Req. Cone</th>
                                    <th width="40">File No</th>
                                    <th width="40">Internal Ref. No</th>
                                    <th >Remarks/Shade</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td >
                                    <input type="text" id="txt_job_no" name="txt_job_no" placeholder="Double Click for Job" readonly style="width:80px;" class="text_boxes" onDblClick="openmypage_job('job Search')"  />
                                    <input type="hidden" id="txt_job_id" name="txt_job_id" style="width:100px;">
                                    </td>
                                    <td  style="display:none;">
                                    <input type="text" id="txt_lot" name="txt_lot" style="width:70px;" class="text_boxes" placeholder="Browse" onDblClick="openmypage_lot()" readonly />
                                    <input type="hidden" id="txt_pro_id" name="txt_pro_id" style="width:70px;"    />
                                    </td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_count", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1","id,yarn_count", 1, "-select-", $selected,"","0" );
                                    ?>
                                    </td>
                                    <td style="display:none">
                                    <input type="text" id="txt_item_des" name="txt_item_des" style="width:140px;" class="text_boxes"   />
                                    </td>
                                    <td align="center" id="composition_td">
									<?  
                                        echo create_drop_down( "cbo_composition", 100, $composition,"", 1, "-- Select --", 0, "",0,"" ); 
                                    ?>
                                    </td> 
                                    <td><input type="text" name="txt_pacent" id="txt_pacent" class="text_boxes" value="100" style="width:30px;" readonly /></td>
                                    <td id="yarn_type_td">
                                    <?  
                                        echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "-- Select --", 0, "",0,"" ); 
                                    ?>
                                    </td>
                                    <td id="color_td">
                                    <!--<input type="text" id="txt_yern_color" name="txt_yern_color" style="width:90px;" class="text_boxes"   />-->
                                    <?
                                        echo create_drop_down( "txt_yern_color", 90, $blank_array,"", 1, "-- Select--",$selected );
                                    ?>
                                    </td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_color_range", 90, $color_range,"", 1, "-- Select--",$selected );
                                    ?>
                                    </td>
                                    <td>
                                    <input type="text" id="txt_ref_no" name="txt_ref_no" style="width:55px;" class="text_boxes"   />
                                    </td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,"", 1, "-- UOM--",12,"",1 );
                                    ?>
                                    </td>
                                    <td>
                                    <input type="text" id="txt_budget_wo_qty" name="txt_budget_wo_qty" style="width:50px;" class="text_boxes_numeric" readonly />
                                    
                                    </td>
                                    <td>
                                    <input type="text" id="txt_wo_qty" name="txt_wo_qty" style="width:50px;" class="text_boxes_numeric" onKeyUp="fnc_calculate()"   />
                                    
                                    </td>
                                    <td id="dyeing_charge_td">
                                        <input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric"  onKeyUp="fnc_calculate()" />
                                    </td>
                                    <td>
                                    <input type="text" id="txt_amount" name="txt_amount" style="width:55px;" class="text_boxes_numeric" readonly />
                                    </td>

                                    <td>
                                    <input type="text" id="txt_app_batch_no" name="txt_app_batch_no" style="width:60px;" class="text_boxes"/>
                                    </td>

                                    <td>
                                    <input type="text" id="txt_bag" name="txt_bag" style="width:35px;" class="text_boxes_numeric"   />
                                    </td>
                                    <td>
                                    <input type="text" id="txt_cone" name="txt_cone" style="width:35px;" class="text_boxes_numeric"   />
                                    </td>
                                    <td>
                                    <input type="text" id="txt_min_req_cone" name="txt_min_req_cone" style="width:35px;" class="text_boxes_numeric"   />
                                    </td>
                                    <td>
                                    <input type="text" id="txt_file_no" name="txt_file_no" style="width:35px;" class="text_boxes" readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="txt_int_ref_no" name="txt_int_ref_no" style="width:35px;" class="text_boxes" readonly  />
                                    </td>
                                     <td>
                                    <input type="text" id="txt_remarks" name="txt_remarks" style="width:80px;;" class="text_boxes"   />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                     </td>
                </tr>
                <br>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                      <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_yarn_dyeing", 0,0 ,"fnResetForm()",1) ; ?>
                      <input type="hidden" id="update_id" >
                      <input type="hidden" id="dtls_update_id" >
                    </td>
                </tr>
                  
                    
                <tr>
                    <td align="center" colspan="8" height="10" id="button_data_panel">
                    	 <div id="pdf_file_name"></div>
                        <!--<input type="button" value="Print Order With Rate" onClick="generate_trim_report()"  style="width:160px" name="print_booking" id="print_booking" class="formbutton" />   
                        <input type="button" value="Print Order Without Rate" onClick="generate_without_rate_report()"  style="width:160px" name="print_booking2" id="print_booking2" class="formbutton" /> 
                        <input type="button" value="Print With Multiple Job" onClick="generate_multiple_job_report()"  style="width:160px" name="print_booking3" id="print_booking3" class="formbutton" />
                        <input type="button" value="Multiple Job Without Rate" onClick="multiple_job_without_rate_report()"  style="width:170px" name="print_booking4" id="print_booking4" class="formbutton" />

                         <input type="button" value="Print" onClick="print2()"  style="width:170px" name="print_booking5" id="print_booking5" class="formbutton" />-->

                    </td>
                </tr>
            </table>
  </fieldset>
  </form>
  <br>
  <fieldset style="width:1200px;">
           <div id="list_container"></div>
  </fieldset> 
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>