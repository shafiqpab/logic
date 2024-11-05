<?
/*-------------------------------------------- Comments
Purpose			: 	This Report will create TNA Task Summary Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	06-10-2020
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
echo load_html_head_contents("TNA Task Summary Report","../../", 1, 1, $unicode,'','');

?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';



function fnc_generate_report( operation)
{
	
	if(operation==3){

		if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
	{
		return;
	}
	var data="action=generate_report&"+get_submitted_data_string('cbo_company_name*tna_task_id*cbo_buyer_name*txt_job_no*txt_date_from*txt_date_to',"../../");
	}else if(operation==4){

		if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
	{
		return;
	}

	if($("#cbo_date_type").val()==2 ){var data="action=generate_report_2&"+get_submitted_data_string('cbo_company_name*tna_task_id*cbo_buyer_name*txt_job_no*cbo_date_type*txt_date_from*txt_date_to',"../../");}

	else{
		alert("Pls Select Shipment Date")
		return;	
	}
   
	}
	
	freeze_window(operation);
	http.open("POST","requires/tna_task_summary_report_controller.php",true);
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
		document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="360px";
	
}

 
function fn_tna_popup(po_id,action,width,title,date,task_id)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_task_summary_report_controller.php?action='+action+'&po_id='+po_id+'&date='+date+'&task_id='+task_id,title, 'width='+width+'px,height=360px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
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
	var page_link='requires/tna_task_summary_report_controller.php?action=task_name_list&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;  
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		$("#txt_taks_name").val(style_des);
		$("#tna_task_id").val(style_id); 
		$("#tna_task_id_no").val(style_des_no);
	}
}


</script>
<body  onLoad="set_hotkey();">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1160px;">
    	<table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="150" class="must_entry_caption"> Company Name</th>
                    <th width="150" class="" id="buyer_caption" >Buyer Name</th>
                    <th width="150" >Job No</th>
                    <th width="150" class="must_entry_caption">Task</th>
                    <th width="100" class="must_entry_caption">Date Type</th>
                    <th class="must_entry_caption" colspan="2"> Date Range</th>
                    <th><input type="reset" id="btn_rest" value="Reset" onClick="reset_form('','report_container*print_button','cbo_company_name*cbo_task_name*cbo_buyer_name*cbo_job_year*txt_job_no*txt_order_no*cbo_shipment_status*cbo_order_status*txt_date_from*txt_date_to','','','');" style="width:100px" class="formbutton" /></th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                <?
                	echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/tna_task_summary_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); " );
                ?> 
                </td>
                <td id="buyer_td" align="center">
                <? 
                	echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                ?>	
                </td>
             
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px" placeholder="Write" /></td>
                <td align="center">
                <input style="width:150px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                <input type="hidden" name="tna_task_id" id="tna_task_id"/> 
                <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>              
                </td>
				<td>
					<?
						$search_by_date=array(2=>"Ship Date",1=>"Plan Finished Date");
						echo create_drop_down( "cbo_date_type", 100, $search_by_date,"",0, "", "",'',0 );
					?>
				</td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:100px" readonly placeholder="From Date" /></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:100px" readonly placeholder="To Date" /></td>
                <td align="center"><input type="button" class="formbutton" style="width:100px" value="Show" onClick="fnc_generate_report(3)" id="btn_report_generate"/>

				<input type="button" class="formbutton" style="width:100px" value="Show 2" onClick="fnc_generate_report(4)" id="btn_report_generate"/>
			</td>
           </tr>
           <tr>
            	<td colspan="7" align="center" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
        </table>
        <div id="print_button"></div>
        <div style="margin-top:5px" id="report_container"></div>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
 
 
 

 
 

