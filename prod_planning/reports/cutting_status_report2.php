<?php
/*********************************************** Comments *************************************
*	Purpose			: 	
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Aziz 
*	Creation date 	: 	26-02-2017
*	Updated by 		: 	Shafiq	
*	Update date		: 	28-05-2019	   
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
echo load_html_head_contents("Cutting Status Report", "../../", 1, 1,'', '', '');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_show_report(type)
{
	//alert("su..re"); return;
	// if (form_validation('cbo_company_name*txt_ref_no','Company Name*Reference No')==false)
	// {
	// 	return;
	// }
	
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
	
	if($('#txt_ref_no').val()=="")
	{
		if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}
	if($('#txt_date_from').val() =="" && $('#txt_date_to').val() =="")
	{
		if(form_validation('txt_ref_no','Style Ref No')==false)
		{
			return;
		}
	}
	
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_brand*cbo_season*cbo_season_year*cbo_working_company_name*txt_ref_no*txt_job_no*txt_date_from*txt_date_to*txt_job_no_hidden*cbo_gmts_item*txt_order_no*hide_order_id',"../../");
		freeze_window('3');
		http.open("POST","requires/cutting_status_report_controller2.php",true);
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
	var sytle_ref_no = $("#txt_ref_no").val();
	var page_link='requires/cutting_status_report_controller2.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&sytle_ref_no='+sytle_ref_no;
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

//cutting search popup
/*function open_cutting_popup()
{ 
	if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	} 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cutting_status_report_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var cutting_no = this.contentDoc.getElementById("hdn_cut_no").value;
		$('#txt_cutting_no').val(cutting_no); 
 	}
}*/

//order search popup	


function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/cutting_status_report_controller2.php?action=order_no_search_popup&companyID='+companyID+'&job_no='+job_no;
	var title='Order No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		
		$('#txt_order_no').val(order_no);
		$('#hide_order_id').val(order_id);	 
	}
}

// for report lay chart
function generate_report_lay_chart(data)
{
	var action	= 'cut_lay_entry_report_print';
	window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller.php?data=" + data+'&action='+action, true );
}

// for report lay chart
function fnc_print_bundle(param,action,controller_path)
{
   
	// var action	= 'cut_lay_entry_report_print';
	window.open(controller_path+"?data=" + param+'&action='+action, true );
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

function openmypage_style()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		//var job_year = $("#cbo_job_year").val();
		//var txt_style_ref_no = $("#txt_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/cutting_status_report_controller2.php?action=style_search_popup&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			data=style_no.split("_");
			fnc_gmt_item(data[1]);
			$('#txt_style_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);
			$("#txt_job_no_hidden").val(data[1]); 
	  		$('#txt_ref_no').val(data[2]);
	  		$('#txt_job_no').attr('disabled','true'); 
		}
}
function fnc_gmt_item(job_no)
{
		//alert(job_no);
		load_drop_down( 'requires/cutting_status_report_controller2',job_no,'load_drop_down_gmts_item','gmt_td' );	
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
         <h3 style="width:1490px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1490px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                   	<th> Working Company</th>
                    <th>Buyer Name</th>
                    <th>Brand Name</th>
                    <th>Season Name</th>
                    <th>Season Year</th>
                  
                    <th class="must_entry_caption">Style Ref.</th>
                    <th>Job No</th>
                    <th>PO NO.</th>
                    <th>Gmts Item</th>
                    
                    <th colspan="2" id="cap_cut_date">Cutting Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_status_report_controller2',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cutting_status_report_controller2');" ); 
							
							//get_php_form_data(this.value,'size_wise_repeat_cut_no','../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller' );
							?> 
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                        </td>
                        <td>
                        <? 
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Working Company --", $selected, "" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
                        <td width="110" id="brand_td"><!-- fpr show3 button -->
                            <? 
                                echo create_drop_down( "cbo_brand", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="110" id="season_td"><!-- fpr show3 button -->
                            <? 
                                echo create_drop_down( "cbo_season", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="60" id="season_year_td"><!-- fpr show3 button -->
                            <? 
                                echo create_drop_down( "cbo_season_year", 60, $year,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        
                       
                        <td>
                       <!-- <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" />-->
                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_style();" onChange="fnc_gmt_item(this.value)" readonly  /> 
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>                       </td>
                        <td>                        
                            <!--<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px;" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />-->
                          <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();"  disabled  />
                            <input type="hidden" name="update_id"  id="update_id" readonly />
                            <input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                         <td id="gmt_td"><? echo create_drop_down( "cbo_gmts_item", 100, $blank_array,"", 1, "-- Select Item --", $selected, "","","" ); ?></td>
                        
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" readonly></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="PO Wise" onClick="fn_show_report(2)" /><input type="button" id="show_button" class="formbutton" style="width:70px" value="Country" onClick="fn_show_report(3)" /></td>
                    </tr>
                    <tr>
                	<td colspan="10"><? echo load_month_buttons(1); ?></td>
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