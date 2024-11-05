<?php
/*********************************************** Comments *************************************
*	Purpose			: 	This Form Will Create Requisition  against demand status Report
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Saidul Islam REZA
*	Creation date 	: 	7-06-2016
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		: derict copy gst report
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Requisition  against demand status Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

	function fnc_generate_report()
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_ref_no=document.getElementById('txt_ref_no').value;
		var txt_orer_no=document.getElementById('txt_orer_id').value;
		if(form_validation('cbo_company_name*cbo_job_year','Company*Job Year')==false)
		{
			return;
		}
		if(txt_job_no=='' && txt_ref_no=='' && txt_orer_no==''){
			if(form_validation('txt_job_no','Job No or Style Ref or Order No')==false)
			{
				return;
			}
		}
		
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_job_year*txt_job_no*txt_ref_no*txt_orer_id',"../../");
		freeze_window('3');
		http.open("POST","requires/gst_print_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			show_msg('3');
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
		}
	}


	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}

function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/gst_print_report_controller.php?action=order_no_search_popup&companyID='+companyID;
	var title='Order No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		order_no=order_no.split("_");
		$('#txt_orer_id').val(order_no[1]);
		$('#txt_orer_no').val(order_no[0]);
	}
}

function openmypage_style()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/gst_print_report_controller.php?action=style_search_popup&companyID='+companyID;
	var title='Order No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var data=this.contentDoc.getElementById("hide_style_no").value;
		data=data.split("_");
		$('#txt_job_no').val(data[1]);
		$('#txt_ref_no').val(data[0]);
	}
}

</script>
</head>
 
<body onLoad="set_hotkey();">
  <div style="width:850px; margin:5px auto;"> 
    <form id="requsitionDemandnReport_1">
       
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset>
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style Ref</th>
                    <th>Order No</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requsitionDemandnReport_1','','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/gst_print_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?> 
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                       
                        <td><? 
								$selected_year=date("Y");
								echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "--All--", $selected_year, "",0,"","" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" placeholder="Brows" readonly  onDblClick="openmypage_style();" /></td>
                        <td>
                      
                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Brows" readonly  onDblClick="openmypage_style();"  /></td>
                        <td>                        
                           
                            <input type="text" name="txt_orer_no" id="txt_orer_no" class="text_boxes" style="width:100px" placeholder="Brows" readonly  onDblClick="openmypage_order();" />
                            <input type="hidden" name="txt_orer_id" id="txt_orer_id"/>
                            
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_generate_report()" /></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
     </form>   
   </div>
    
    <div id="report_container" align="center"></div><br>
    <div id="report_container2" align="center"></div>  
    
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>