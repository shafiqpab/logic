<?
/*-------------------------------------------- Comments 

Purpose			: 	This form will create Yarn Serviec Work Order
					
Functionality	:	

JS Functions	:

Created by		:	Md. Saidul Islam
Creation date 	: 	04-05-2016
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
echo load_html_head_contents("Yarn Service Work Order", "../../", 1, 1,$unicode,'','');
?>	
<script> 
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

//-------------------------------function---------------------------------------------------------------
/*$( document ).ready(function()
{
	get_php_form_data(94, "populate_field_level_access_data", "requires/yarn_service_work_order_controller" );
});*/


function fnc_yarn_service_wo(operation)
{
	
	if( form_validation('cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*cbo_pay_mode*txt_delivery_date*txt_lot*txt_wo_qty*txt_rate','Company Name *Service Type*Factory*Booking Date*Pay Mode*Delivery Date*Lot*Order Quanty*Rate')==false )
	{
		return;
	}
	else
	{
	var dataString = "cbo_company_name*cbo_service_type*cbo_supplier_name*txt_booking_date*txt_attention*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*txt_delivery_date*cbo_is_sales_order*txt_job_no*txt_job_id*txt_lot*txt_pro_id*cbo_count*txt_item_des*cbo_uom*txt_wo_qty*txt_rate*txt_amount*txt_bag*txt_cone*txt_min_req_cone*txt_remarks*update_id*dtls_update_id*txt_booking_no";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//alert(data);
	freeze_window(operation);
	http.open("POST","requires/yarn_service_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_yarn_service_wo_response;
	}
	
}

function fnc_yarn_service_wo_response()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{	//alert('sdfsdf');
			show_msg(trim(response[0]));
			release_freezing();

		}
		else if(response[0]==10||response[0]==11)
		{
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}
			
			show_msg(trim(response[0]));
			release_freezing();
			$("#update_id").val(response[2]);
			$("#txt_booking_no").val(response[1]);
			$('#cbo_is_sales_order').attr('disabled','disabled');
			show_list_view(response[2],'show_dtls_list_view','list_container','requires/yarn_service_work_order_controller','');
			if(response[0]==0)
			{
				set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
			}
			else
			{
				set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
			}
			//reset_form('','','txt_job_no*txt_job_id*txt_lot*txt_wo_qty*txt_rate*txt_amount*txt_bag*txt_cone*txt_min_req_cone*txt_ref_no*txt_remarks','','','update_id*txt_booking_no*cbo_is_sales_order');
			$('#txt_wo_qty').attr("placeholder","");
			reset_form('','','cbo_count*txt_item_des*txt_lot*txt_wo_qty*txt_rate*txt_amount*txt_bag*txt_cone*txt_min_req_cone*txt_ref_no*txt_remarks','','','update_id*txt_booking_no*cbo_is_sales_order');

		show_msg(trim(response[0]));
		release_freezing();
	
	
	}
}

function fnc_calculate()
{
	var wo_qty=$('#txt_wo_qty').val();
	var place_val=$('#txt_wo_qty').attr("placeholder")*1;
	if(place_val<wo_qty)
	{
		$('#txt_wo_qty').val("");
		return;
	}
	var dyeing_charge=$('#txt_rate').val();
	var amount=(wo_qty*1)*(dyeing_charge*1);
	$('#txt_amount').val(number_format_common(amount,2));

}

function openmypage_job(title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}	
	else
	{
		var is_sales_order = $("#cbo_is_sales_order").val();
		var company = $("#cbo_company_name").val();
		var width = "";
		if(is_sales_order == 1)
		{
			width = "720px";
		}else
		{
			width = "620px";
		}
		page_link='requires/yarn_service_work_order_controller.php?action=job_search_popup&company='+company+'&is_sales_order='+is_sales_order;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("hidden_job_no").value;
			var dataArr=data.split(',');
			freeze_window(5);
			document.getElementById('txt_job_id').value=dataArr[0];
			document.getElementById('txt_job_no').value=dataArr[1];
			release_freezing();
	
		}
	}
}

function openmypage_lot()
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
		page_link='requires/yarn_service_work_order_controller.php?action=lot_search_popup&company='+company+'&job_no='+job_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Lot Number Search', 'width=1120px,height=380px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("hidden_product").value.split("*");
			//alert(data[0]);return;
			freeze_window(5);
			$('#txt_item_des').val(data[0]).attr('disabled',true);
			$('#cbo_count').val(data[1]).attr('disabled',true);
			$('#txt_lot').val(data[2]);
			$('#txt_pro_id').val(data[3]);
			$('#txt_wo_qty').attr("placeholder",data[4]);
			//$('#txt_wo_qty').attr("placeholder","text");
			release_freezing();

		}
	}
}


function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/yarn_service_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
		
}

function openmypage_booking()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	page_link='requires/yarn_service_work_order_controller.php?action=yern_service_wo_popup&company='+company;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Yarn Dyeing Booking Search', 'width=885px, height=450px, center=1, resize=0, scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_number=this.contentDoc.getElementById("hidden_sys_number").value.split("_");
		
		if(sys_number!="")
		{	
		//alert(b_date);
			freeze_window(5);
			get_php_form_data(sys_number[1], "populate_master_from_data", "requires/yarn_service_work_order_controller" );
			show_list_view(sys_number[0],'show_dtls_list_view','list_container','requires/yarn_service_work_order_controller','');
			set_button_status(0, permission, 'fnc_yarn_service_wo',1,1);
			release_freezing();
		}
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

function generate_trim_report()
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_trim_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type',"../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
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
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
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
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_without_rate_booking_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type',"../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
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

function sales_order_report()
{
	var update_id =  $('#update_id').val();
	var is_sales = $('#cbo_is_sales_order').val();
	if(update_id == "" || is_sales == 2)
	{
		alert("This Report Is For Sales Order Only");
		return;
	}
	var show_rate_column = "";
	var r=confirm("Press \"OK\" to open with Rate column\nPress \"Cancel\" to open without Rate column");
	if (r==true)
	{
		show_rate_column="1";
	}
	else
	{
		show_rate_column="0";
	}

	/*if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{*/
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=sales_order_report"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_service_type',"../../")+"&show_val_column="+show_rate_column;
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = sales_order_report_reponse;
	//}
}

function sales_order_report_reponse()
{
	if(http.readyState == 4) 
	{
		freeze_window(5);
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
		release_freezing();
	}
}
//=======================================================================





function openmypage_charge()
{
	if (form_validation('cbo_company_name','Company Name*Job Number')==false)
	{
		return;
	}	
	else
	{
		var company = $("#cbo_company_name").val();
		//alert(company);
		page_link='requires/yarn_service_work_order_controller.php?action=dyeing_search_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Charge', 'width=600px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_rate").value;
			//alert(data);
			freeze_window(5);
			document.getElementById('txt_rate').value=data;
			release_freezing();
			fnc_calculate();

		}
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
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_with_multiple_job"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		//var data="action=show_with_multiple_job"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
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
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
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
		var form_name="yarn_dyeing_wo_booking_without_order";
		var data="action=show_with_multiple_job_without_rate"+'&form_name='+form_name+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		//var data="action=show_with_multiple_job_without_rate"+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id',"../../");
		http.open("POST","requires/yarn_service_work_order_controller.php",true);
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
	reset_form('yarn_dyeing_wo_booking_without_order','list_container','','txt_booking_date,<? echo date("d-m-Y"); ?>','disable_enable_fields("txt_item_des*txt_lot*cbo_count",0)','cbo_uom');
	set_button_status(0, permission, 'fnc_yarn_service_wo',1,0);
}

function change_job_title(val)
{
	if(val == 1){
		$("#job_title").text("Sales Order No");
	}else{
		$("#job_title").text("Job No");
	}
}


</script>
 
</head>
<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
<form name="yarn_service_work_order"  autocomplete="off" id="yarn_service_work_order">
<fieldset style="width:800px; margin-bottom:5px;">
<legend>Yarn Service Work Order</legend>
<table cellspacing="4" cellpadding="8" border="0">
    <tr>
        <td colspan="6" align="center" height="30" valign="top"> Wo No 
            <input class="text_boxes" type="text" style="width:190px" onDblClick="openmypage_booking()" readonly placeholder="Double Click for Work Order" name="txt_booking_no" id="txt_booking_no" />
        </td>
    </tr>
    
    <tr>
        <td align="right" class="must_entry_caption" width="80">Company Name</td>
        <td>
        <? 
        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected,"load_drop_down( 'requires/yarn_service_work_order_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );get_php_form_data('94'+'_'+this.value, 'populate_field_level_access_data', 'requires/yarn_service_work_order_controller' );",0);
        ?>
        </td>
        <td  align="right" class="must_entry_caption" width="80">Service Type</td>
        <td>
        <?
            echo create_drop_down( "cbo_service_type", 160, $yarn_issue_purpose,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_service_work_order_controller',$('#cbo_company_name').val()+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' );",0,'15,38,46,7');
        ?>
        </td>
        <td  align="right" class="must_entry_caption" width="90">Factory</td>
        <td id="supplier_td">
        <?
        echo create_drop_down( "cbo_supplier_name", 160, $blank_array,"", 1, "-- Select Supplier --", $selected, "",0 );
        ?>
        </td>
        
        
    </tr>
    <tr>
        <td align="right" class="must_entry_caption">Booking Date</td>   
        <td><input class="datepicker" type="text" style="width:150px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" /></td>
        <td align="right">Attention</td>   
        <td ><input class="text_boxes" type="text" style="width:150px;"  name="txt_attention" id="txt_attention"/></td>
       <td align="right">Currency</td>
        <td><? 
        echo create_drop_down( "cbo_currency", 160, $currency,"", 1, "-- Select --", 2, "",0 );		
        ?></td>
        
    </tr>
    <tr>
        <td align="right">Exchange Rate</td>
        <td><input style="width:150px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly /></td>
        <td  align="right" class="must_entry_caption">Pay Mode</td>
        <td ><?
        echo create_drop_down( "cbo_pay_mode", 160, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" );
        ?></td>
        
        <td  align="right">Source</td>
        <td ><?
            echo create_drop_down( "cbo_source", 160, $source,"", 1, "-- Select --", 3, "",0 );
        ?></td>
    </tr>
    <tr>
        <td align="right" class="must_entry_caption">Delivery Date</td>   
        <td align="left"><input class="datepicker" type="text" style="width:150px" name="txt_delivery_date" id="txt_delivery_date"/></td>
   		<td align="right">Sales Order</td>   
        <td align="left">
       	<?
            echo create_drop_down( "cbo_is_sales_order", 160, $yes_no,"", 0, "-- Select --", 2, "change_job_title(this.value);",0 );
        ?>
        <td colspan="2" align="center">
        <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup( 'requires/yarn_service_work_order_controller.php?action=terms_condition_popup','Terms Condition'
)"/>
        </td>
    </tr>
</table>
</fieldset>

<fieldset style="width:1000px;">
<legend>Yarn Service Work Order Details</legend>
<table cellspacing="0" cellpadding="0" border="0" class="rpt_table">
    <thead>
        <tr>
            <th id="job_title">Job No</th>
            <th class="must_entry_caption">Lot No</th>
            <th>Count</th>
            <th>Yarn Description</th>
            <th>UOM</th>
            <th class="must_entry_caption">WO Qnty</th>
            <th class="must_entry_caption">Rate</th>
            <th>Amount</th>
            <th>No of Bag</th>
            <th>No of Cone</th>
            <th>Min Req. Cone</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td >
            <input type="text" id="txt_job_no" name="txt_job_no" placeholder="Doubole Click for Job" readonly style="width:100px;" class="text_boxes" onDblClick="openmypage_job('Job Search')"  />
            <input type="hidden" id="txt_job_id" name="txt_job_id" />
            </td>
            <td>
            <input type="text" id="txt_lot" name="txt_lot" style="width:70px;" class="text_boxes" placeholder="Browse" onDblClick="openmypage_lot()" readonly />
            <input type="hidden" id="txt_pro_id" name="txt_pro_id" />
            </td>
            <td>
             <?
                echo create_drop_down( "cbo_count", 70, "Select id, yarn_count from  lib_yarn_count where  status_active=1","id,yarn_count", 1, "-select-", $selected,"","0" );
            ?>
            
            </td>
            <td>
            <input type="text" id="txt_item_des" name="txt_item_des" style="width:150px;" class="text_boxes"   />
            </td>
            <td>
            <?
                echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,"", 1, "-- UOM--",12,"",1 );
            ?>
            </td>
            <td>
            <input type="text" id="txt_wo_qty" name="txt_wo_qty" style="width:55px;" class="text_boxes_numeric" onKeyUp="fnc_calculate()"   placeholder="" />
            </td>
            <td>
                <input type="text" id="txt_rate" name="txt_rate" style="width:55px;" class="text_boxes_numeric"  onKeyUp="fnc_calculate()" />
            </td>
            <td>
            <input type="text" id="txt_amount" name="txt_amount" style="width:65px;" class="text_boxes_numeric" readonly />
            </td>
            <td>
            <input type="text" id="txt_bag" name="txt_bag" style="width:40px;" class="text_boxes_numeric"   />
            </td>
             <td>
            <input type="text" id="txt_cone" name="txt_cone" style="width:40px;" class="text_boxes_numeric"   />
            </td>
            <td>
            <input type="text" id="txt_min_req_cone" name="txt_min_req_cone" style="width:40px;" class="text_boxes_numeric"   />
            </td>
             <td>
            <input type="text" id="txt_remarks" name="txt_remarks" style="width:100px;;" class="text_boxes"   />
            </td>
        </tr>
    </tbody>
</table>
<table width="100%">
    <tr>
        <td align="center" class="button_container">
          <? echo load_submit_buttons( $permission, "fnc_yarn_service_wo", 0,0 ,"fnResetForm()",1) ; ?>
          <input type="hidden" id="update_id" >
          <input type="hidden" id="dtls_update_id" >
       
       
            <div id="pdf_file_name"></div>
            <input type="button" value="Print With Rate" onClick="generate_trim_report()"  style="width:160px" name="print_booking" id="print_booking" class="formbutton" />   
            <input type="button" value="Print Without Rate" onClick="generate_without_rate_report()"  style="width:160px" name="print_booking2" id="print_booking2" class="formbutton" /> 
            <input type="button" value="Multiple Sample With Rate" onClick="generate_multiple_job_report()"  style="width:160px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
            <input type="button" value="Multiple Sample Without Rate" onClick="multiple_job_without_rate_report()"  style="width:170px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" /> 

            <input type="button" value="Print Report Sales" onClick="sales_order_report()"  style="width:170px;" name="print_booking5" id="print_booking5" class="formbutton" />                    
       
        </td>
    </tr>
</table>


</fieldset>
</form>
  <br>
  <div id="list_container"></div>
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>