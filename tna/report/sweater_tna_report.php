<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------;
echo load_html_head_contents("TNA Process","../../", 1, 1, $unicode,1,'');

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';



var tableFilters = 
 {
	col_operation: {
	id: ["total_po_qty"],
	col: [4],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
 }

function fnc_generate_report_main( operation,rpt_type)
{
	var task_name_ref=$('#txt_taks_name').val();
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	var style_no=$('#txt_style_ref_no').val();
	var file_no=$('#txt_file_no').val();
	var int_ref_no=$('#txt_int_ref_no').val();
	if(task_name_ref!="")
	{
		if(rpt_type==1)
		{
			alert("Please Click On Task Wise Button");
			return;
		}
	}
	
	var action_type="";
	
	
	if(rpt_type==4)
	{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			action_type="generate_multi_style_report_with_graph";
	}	
	else if(rpt_type==3)
	{
			if ( form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false )
			{
				return;
			}
			action_type="generate_style_report_with_graph";
	}
	else if(rpt_type==1)
	{
		if(job_no!="" || order_no!="" || style_no!="" || file_no!="" || int_ref_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{ 
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_tna_report";
	} 
	else if(rpt_type==2)
	{
		if(job_no!="" || order_no!="" || style_no!="" || file_no!="" || int_ref_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_style_report";
	}
	else
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_penalty_report";
	}
	
	var data="action="+action_type+"&"+get_submitted_data_string('cbo_company_name*txt_taks_name*tna_task_id*cbo_buyer_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_order_no*txt_style_ref_no*cbo_search_type*cbo_shipment_status*cbo_order_status*txt_file_no*txt_int_ref_no',"../../"); 
	freeze_window(operation);
	http.open("POST","requires/sweater_tna_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_report_main_reponse;
}

function fnc_generate_report_main_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('****');
		$("#report_container").html(reponse[0]);  
		
		
		if(reponse[1]==1){
			document.getElementById('print_button').innerHTML='<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		}
		else{
			
			document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
		}
		release_freezing();
		
		
		
		
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide(); 
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
	
	$('#table_body tr:first').show();
	
}



function new_window2()
{
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close();
}


function update_tna_process(type,id,po_id)
{  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action=edit_update_tna'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("auto_field_data_str").value;
		var reponse=theemail.split('**');
  
		if(reponse[2]==1){
  
			if(trim($('#plan_1'+reponse[3]+reponse[4]).text()) != reponse[7]){
				$('#plan_1'+reponse[3]+reponse[4]).text(reponse[7]).css('background-color','#0000FF');
			}
			if(trim($('#plan_2'+reponse[3]+reponse[4]).text()) != reponse[8]){
				$('#plan_2'+reponse[3]+reponse[4]).text(reponse[8]).css('background-color','#0000FF');
			}
		}
		else if(reponse[2]==2){
			 
			if(trim($('#actual_1'+reponse[3]+reponse[4]).text()) != reponse[5]){
				$('#actual_1'+reponse[3]+reponse[4]).text(reponse[5]).css('background-color','#0000FF');
			}
			if(trim($('#actual_2'+reponse[3]+reponse[4]).text()) != reponse[6]){
				$('#actual_2'+reponse[3]+reponse[4]).text(reponse[6]).css('background-color','#0000FF');
			}
		}
 

		if (theemail.value!="")
		{ 
			freeze_window(5);
			//get_submitted_data_string('',"../../"); 
			release_freezing();
		}
	}
}


function update_tna_process_style(type,id,po_id,task_id)
{ 

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&task_id='+task_id+'&action=edit_update_tna_style'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			//get_submitted_data_string('',"../../"); 
			release_freezing();
		}
	}
}



function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}

function progress_comment_popup_style(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment_style'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}




function openmypage_task()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var tna_task = $("#txt_taks_name").val();
	var tna_task_id = $("#tna_task_id").val();
	var tna_task_id_no = $("#tna_task_id_no").val();
	var page_link='requires/sweater_tna_report_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;  
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		//alert(style_des_no);
		$("#txt_taks_name").val(style_des);
		$("#tna_task_id").val(style_id); 
		$("#tna_task_id_no").val(style_des_no);
		if(style_des!="")
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('TNA From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('TNA To Date');
			
		}
		else
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('Ship From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('Ship To Date');
		}
	}
}

function fn_change_caption(str)
{
	
	var tna_task_id = $("#tna_task_id").val();
	if(tna_task_id){alert('Task ID Selected.');return;}
	
	if(str==1)
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Ship From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Ship To Date');
	}
	else if(str==3)
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Cun.Ship From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Cun.Ship To Date');
	}
	else
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Recv. From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Recv. To Date');
	}
	
}

function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==54)
			{
				$("#btn_buyer_task_report_generate").show();	 
			}
			if(report_id[k]==55)
			{
				$("#btn_task_report_generate").show();	 
			}
			if(report_id[k]==56)
			{
				$("#btn_report_generate").show();	 
			}
			if(report_id[k]==57)
			{
				$("#btn_due_task").show();	 
			}
			if(report_id[k]==58)
			{
				$("#btn_penalty").show();	 
			}
			
			if(report_id[k]==198)
			{
				$("#btn_commitment").show();	 
			}
			
			
			
		}
}


function fn_photo_view(photo_location)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?photo_location='+photo_location+'&action=photo_view'+'&permission='+permission, "Photo View", 'width=240px,height=300px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}



function fnTaskPopup(taskDataStr)
{ 

	var dataArr=taskDataStr.split('*');
	if(dataArr[0]==47){
		var width=605;
		var action='graph_task_poup_yarn_in_house';
	}
	else if(dataArr[0]==46){
		var width=605;
		var action='graph_task_poup_yarn_work_order';
	}
	else{
		var width=305;
		var action='graph_task_poup';
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?task_data_str='+taskDataStr+'&action='+action+'&permission='+permission, "TNA Task Dtls", 'width='+width+'px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}

function fnMultiStyueTaskPopup(taskDataStr)
{ 

 
	var width=950;
	var action='multi_style_graph_task_poup';

	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?task_data_str='+taskDataStr+'&action='+action+'&permission='+permission, "TNA Task Dtls", 'width='+width+'px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}

	//sweater Precost.................................
	function generate_bom(type,job_no,company_name,buyer_name,style_ref,costing_date)
	{

		var rate_amt=2; var zero_val='';
		if(type!='mo_sheet')
		{
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		}

		if (r==true) zero_val="1"; else zero_val="0";
		var data="action=preCostRpt2&zero_value=1&rate_amt=2&txt_job_no='"+job_no+"'&cbo_company_name='"+company_name+"'&cbo_buyer_name='"+buyer_name+"'&txt_style_ref='"+style_ref+"'&txt_costing_date='"+costing_date+"'&txt_po_breack_down_id=''&cbo_costing_per='1'";

		freeze_window(3);
		http.open("POST","../../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_bom_reponse;
		
	}

	function generate_bom_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
		}
	}


	function report_generate_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&cbo_company_name='+$('#cbo_company_name').val()+'&action=report_generate_popup'+'&permission='+permission, "Report View", 'width=1040px,height=400px,center=1,resize=1,scrolling=0','../');
	}


	function task_his_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=task_his_dtls'+'&permission='+permission, "TNA Task History", 'width=1040px,height=400px,center=1,resize=1,scrolling=0','../');
	}


</script>
<div style="display:none;" id="data_panel"></div>
<body  onLoad="set_hotkey()">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1330px; text-align:left">
    	<table width="1330" class="rpt_table" border="1" rules="all" cellpadding="3" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="110">Task</th>
                    <th width="120" class="" id="buyer_caption">Buyer Name</th>
                    <th width="120">Merchant</th>
                    <th width="80">Search type</th>
                    <th width="80">Shipment Status</th>
                    <th width="80">Order Status</th>
                    <th width="70" id="from_date_html">Ship From Date</th>
                    <th width="70" id="to_date_html">Ship To Date</th>
                    <th width="90">Job No</th>
                    <th width="90">Order No</th>
                    <th width="90">Style Ref. No</th>
                    <th width="90">File No</th>
                    <th>Internal Ref. No</th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                	<?
					   echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/sweater_tna_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value, 'set_print_button', 'requires/sweater_tna_report_controller' ); " );
						?> 
                </td>
                <td align="center">
                        <input style="width:100px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                        <input type="hidden" name="tna_task_id" id="tna_task_id"/> <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>               
                 </td>
                <td id="buyer_td">
						 <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                         ?>	
                 </td>
                <td id="team_td">
                                    
					<? 
						echo create_drop_down( "cbo_team_member", 120, "select id,team_member_name from  lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
                        //echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                     ?>	
                                    
                </td>
                <td align="center">
					<? 
						$search_type=array(1=>'Pub-Ship Date',2=>'PO Recv. Date',3=>'Country Ship Date',4=>'PO Insert Date');
						echo create_drop_down( "cbo_search_type", 80, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
                     ?>	
                </td>
                <td align="center">
					<? 
                    	$shipment_status_tna = array(0=>"ALL (Pending+Partial)",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                        echo create_drop_down( "cbo_shipment_status", 80, $shipment_status_tna,"", 1, "-- Select --", $selected, "",0,"0,3" );
						
					
					 ?>
                </td>
                <td align="center">
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 80, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off"  placeholder="13,35,109" class="text_boxes" style="width:80px" /></td>
                <td align="center"><input type="text" name="txt_order_no" id="txt_order_no" autocomplete="off" class="text_boxes" style="width:80px" ></td>
                <td  align="center"><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
                <td align="center"><input type="text" name="txt_file_no" id="txt_file_no" autocomplete="off" class="text_boxes" style="width:80px" ></td>
                <td  align="center"><input type="text" name="txt_int_ref_no" id="txt_int_ref_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
                 
           </tr>
           
           <tr>
            	<td colspan="14" align="center">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
           <tr>
           		<td colspan="14" valign="middle" align="center">
                	<span id="print_button"></span>
                    <input type="button" class="formbutton" style="width:120px;" value="Generate Report" onClick="fnc_generate_report_main(3,1)" id="btn_report_generate"/>
					<input type="button" class="formbutton" style="width:100px;" value="Style Wise" onClick="fnc_generate_report_main(3,2)" id="btn_style_wise" />
					<input type="button" class="formbutton" style="width:130px;" value="Style Follow Up" onClick="fnc_generate_report_main(3,3)" id="btn_style_wise" />
					<input type="button" class="formbutton" style="width:150px;" value="Multi Style Follow Up" onClick="fnc_generate_report_main(3,4)" id="btn_multi_style_wise" />                 
                </td>
           </tr>

        </table>
        
        <div style="margin-top:5px" id="report_container"></div>
    </fieldset>
	 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
 
 
 

 
 

