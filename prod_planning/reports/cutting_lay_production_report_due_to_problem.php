<?php
/*********************************************** Comments *************************************
*	Purpose			: 	This Form Will Create Job/Order Wise Cutting Lay and Production Report
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Nuruzzaman 
*	Creation date 	: 	14-09-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Lay Production Report", "../../", 1, 1,'', '', '');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_show_report()
{
	//alert("su..re"); return;
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	else
	{
		if( ($('#cbo_buyer_name').val()==0 && $('#txt_job_no').val()=="" && $('#txt_file_no').val()=="" && $('#txt_order_no').val()=="" && $('#txt_cutting_no').val()=="" && $('#txt_table_no').val()=="") || ($('#cbo_buyer_name').val()!=0 && $('#txt_job_no').val()=="" && $('#txt_file_no').val()=="" && $('#txt_order_no').val()=="" && $('#txt_cutting_no').val()=="" && $('#txt_table_no').val()=="") )
		{
			if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_file_no*txt_order_no*txt_cutting_no*txt_table_no*txt_date_from*txt_date_to',"../../");
		freeze_window('3');
		http.open("POST","requires/cutting_lay_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		//alert(http.responseText);
		show_msg('3');
		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='300px';
}	 

//job search popup
function openmypage_job()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var page_link='requires/cutting_lay_production_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		$('#txt_job_no').val(job_no);
	}
}

//cutting search popup
function open_cutting_popup()
{ 
	if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	} 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cutting_lay_production_report_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var cutting_no = this.contentDoc.getElementById("hdn_cut_no").value;
		$('#txt_cutting_no').val(cutting_no); 
 	}
}

//order search popup	
function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/cutting_lay_production_report_controller.php?action=order_no_search_popup&companyID='+companyID;
	var title='Order No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		$('#txt_order_no').val(order_no);
	}
}

// for report lay chart
function generate_report_lay_chart(data)
{
	var data	= data+'*'+$('#size_wise_repeat_cut_no').val();
	var action	= 'cut_lay_entry_report_print';
	window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller.php?data=" + data+'&action='+action, true );
}

function onchange_buyer()
{
	if($('#cbo_buyer_name').val() !=0)
	{
		document.getElementById("cap_cut_date").style.color = "blue";
	}
	else 
	{
		document.getElementById("cap_cut_date").style.color = "";
	}
}
</script>
<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">
<form id="cuttingLayProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1050px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:950px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>File No</th>
                    <th>Order No</th>
                    <th>Cutting No</th>
                    <th>Tabel No</th>
                    <th colspan="2" id="cap_cut_date">Cutting Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'size_wise_repeat_cut_no','../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller' );" ); ?> 
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                        <td>
                        	<!--<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" />-->
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                            <!--<input type="hidden" id="txt_job_id" name="txt_job_id"/>-->
                        </td>
                        <td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
                        <td>
                       <!-- <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" />-->
                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Wr./Br. Order" onDblClick="openmypage_order();" />                        </td>
                        <td>                        
                            <!--<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px;" />-->
                            <input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px" placeholder="Wr./Br. Cutting" onDblClick="open_cutting_popup()" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                            <input type="hidden" name="update_id"  id="update_id"  />
                        </td>
                        <td><input type="text" name="txt_table_no" id="txt_table_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" readonly></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report()" /></td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td><? echo load_month_buttons(1); ?></td>
                </tr>
            </table> 
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <fieldset><div id="report_container2" align="left"></div></fieldset>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>