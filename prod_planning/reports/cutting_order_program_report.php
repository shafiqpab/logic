<?php
/*********************************************** Comments *************************************
*	Purpose			: 	
*	Functionality	:	
*	JS Functions	:
*	Created by		:	md mamun ahmed sagor 
*	Creation date 	: 	07-08-2022
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
echo load_html_head_contents("Cutting Status Report V2", "../../", 1, 1,'', '', '');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_show_report(type)
{
	
	
		if( form_validation('cbo_company_name*txt_job_no*txt_gmts_color','Company Name*Job No*Gmts Color')==false )
		{
			return;
		}
		
		
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_ref_no*txt_style_ref_id*txt_job_no*txt_job_no_hidden*txt_gmts_color*hidd_gmts_color*hidden_order_ids',"../../");
		freeze_window('3');
		http.open("POST","requires/cutting_order_program_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		//alert(http.responseText);
		show_msg('3');
		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

		release_freezing();
 	}
}

function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();
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
	var sytle_ref_no = $("#txt_ref_no").val();
	var page_link='requires/cutting_order_program_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&sytle_ref_no='+sytle_ref_no;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		fnc_gmt_item(job_no);
		$('#txt_job_no').val(job_no);
	}
}

// for report lay chart


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




function openmypage_style()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var page_link='requires/cutting_order_program_report_controller.php?action=style_search_popup&company='+company+'&buyer='+buyer;
		var title="Style Search Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var style_no=this.contentDoc.getElementById("hide_style_no").value;
			var order_ids=this.contentDoc.getElementById("hide_order_id").value;
			
			
			$('#txt_style_ref_id').val(job_id);
			$('#txt_job_no').val(job_no);
			$("#txt_job_no_hidden").val(job_id); 
	  		$('#txt_ref_no').val(style_no);
			$("#hidden_order_ids").val(order_ids); 
	  		// $('#txt_job_no').attr('disabled','true'); 
		}
}



function openmypage_color()
{
	if( form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var job_no = $("#txt_job_no").val();
		var page_link='requires/cutting_order_program_report_controller.php?action=color_popup&company='+company+'&buyer='+buyer+'&job_no='+job_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("txt_selected_no").value;
			var color_id=this.contentDoc.getElementById("txt_selected_id").value;
			
			$('#txt_gmts_color').val(color_name);
			$('#hidd_gmts_color').val(color_id);
	
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
         <h3 style="width:610px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:610px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th class="must_entry_caption">Job No</th>                 
                    <th class="must_entry_caption">Color</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_order_program_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cutting_order_program_report_controller');" ); 
							
							?> 
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
							<input type="hidden" name="txt_ref_no" id="txt_ref_no" class="text_boxes"/> 
								<input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    
								<input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>   
								<input type="hidden" name="hidden_order_ids" id="hidden_order_ids"/>    
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
						<td>                        
                         	 <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_style();" placeholder="Browse"  readonly />
                            <input type="hidden" name="update_id"  id="update_id" readonly />
                            <input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
                        </td>
                     
						 <td>
                         	<input type="text" name="txt_gmts_color"  id="txt_gmts_color" class="text_boxes" onDblClick="openmypage_color()" placeholder="Browse" readonly="" />
							 <input type="hidden" name="hidd_gmts_color" id="hidd_gmts_color"/>  
                         </td>
                        
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(1)" /></td>
                    </tr>
                </tbody>

            </table>
           
        </fieldset>
    	</div>
         <div style="display:none" id="data_panel"></div>   
    	<div id="report_container" align="center"></div>
    	<div id="report_container2" align="left"></div>
    </div>
   
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>