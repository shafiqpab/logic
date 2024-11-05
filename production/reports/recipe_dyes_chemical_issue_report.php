<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	07-04-2017
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
//echo load_html_head_contents("Daily Batch Recipe Report", "../../", 1, 1,'','','');
echo load_html_head_contents("Batch Recipe Report", "../../", 1, 1,'',1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

var tableFilters =
	{
		col_operation: {
		id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
		col: [26,27,28,29],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters_2 =
	{
		col_operation: {
		id: ["value_sbatch_qnty","value_stotal_trims_weight","value_sbatch_weight","value_s_roll_total"],
		col: [15,16,17,18],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

function fn_report_generated(operation)
{
	var txt_batch_no=document.getElementById('txt_batch_no').value;
	var txt_batch_id=document.getElementById('txt_batch_id').value;
	var txt_fso_no=document.getElementById('txt_fso_no').value;
	var txt_fso_id=document.getElementById('txt_fso_id').value;
	
	if(txt_batch_no!="" || txt_fso_no!="")
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}
	}
	var report_title=$( "div.form_caption" ).html();
	freeze_window(5);
 	var data="action=recipe_batch_report&operation="+operation+get_submitted_data_string('cbo_company_name*txt_batch_no*txt_batch_id*txt_fso_no*txt_fso_id*cbo_base_on*txt_lot_no*cbo_year*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;

	http.open("POST","requires/recipe_dyes_chemical_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_batch_report;
}
function fnc_show_batch_report()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split("**");
		$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';


		show_msg('3');
		release_freezing();

	}
}
function new_window()
	{
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
	}

function Batch_number_popup()
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var txt_batch_no=document.getElementById('txt_batch_no').value;
	var page_link="requires/recipe_dyes_chemical_issue_report_controller.php?action=batch_number_popup&company_name="+company_name;
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1190px,height=480px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("hidden_batch_id").value;
		var batch=theemail.split("_");
		document.getElementById('txt_batch_id').value=batch[0];
		document.getElementById('txt_batch_no').value=batch[1];
		release_freezing();
	}
}


	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}

	function fsoNumber() 
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/recipe_dyes_chemical_issue_report_controller.php?cbo_company_id=' + cbo_company_id + '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

 			emailwindow.onclose = function ()
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				//alert (job_no);
				$('#txt_fso_no').val(fso_no);
				$('#txt_fso_id').val(fso_id);
 			}
 		}
 	}

 	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_year = $("#cbo_year").val();

		var page_link='requires/recipe_dyes_chemical_issue_report_controller.php?action=booking_no_popup&companyID='+companyID+ '&cbo_year='+cbo_year;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var booking_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_booking_no_show').val(booking_no);
			$('#txt_booking_no').val(booking_no);
		}
	}
	function clear_hidden(src)
	{
		if(src == 'book')
		{
			$('#txt_booking_no').val("");
		}
	}
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Batch Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Dyeing Date";
			$('#search_by_th_up').css('color','blue');
		}
		
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:910px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:910px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" style="margin-left:5px;">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Year</th>
                            <th>FSO No</th>
                            <th>Batch No</th>
                            <th>Lot no</th>
                            <th>Based On</th>
                            <th id="search_by_th_up" class="must_entry_caption">Batch Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                                    ?>
                                </td>
                                <td>
									<?
                                       echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", "", "",0,"" );
									?>
                                </td>
                               
                                <td>
                                     <input type="text"  name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Browse" onDblClick="fsoNumber();" readonly>
                                     <input type="hidden" name="txt_fso_id" id="txt_fso_id">
                                </td>
                                <td>
                                    <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" tabindex="2" placeholder="Write/Browse" onDblClick="Batch_number_popup();">
                                     <input type="hidden" name="txt_batch_id" id="txt_batch_id">
                                </td>
                                <td>
                                     <input type="text"  name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px;" tabindex="3" >
                                     
                                </td>
                                <td>
                                    <?
									/*$base_no_array=array(1=>"Batch Date",2=>"Dyeing Date");
									$dd="search_populate(this.value)";
                                       echo create_drop_down( "cbo_base_on", 100, $base_no_array,"", 0, '--- Select ---', "1", $dd,0);*/
									   
									   $search_by = array(1=>'Batch Date',2=>'Dyeing Date');
									$dd="search_populate(this.value)";
									echo create_drop_down( "cbo_base_on", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );
									?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="12">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="report_container" align="center"></div>
    		<div id="report_container2" align="center"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>