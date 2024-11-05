<?php
/*********************************************** Comments *************************************
*	Purpose			:
*	Functionality	:
*	JS Functions	:
*	Created by		:	Arnab
*	Creation date 	: 	17-11-2022
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
echo load_html_head_contents("Table Wise Cutting  Report", "../../", 1, 1, $unicode,1,1);
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function fn_show_report(type)
{
	if($('#cbo_working_company_name').val()=="")
	{
		if(form_validation('cbo_working_company_name','Working Company')==false)
		{
			return;
		}
	}
	if($('#txt_job_no').val()=="")
	{
		if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}
	if($('#txt_date_from').val() =="" && $('#txt_date_to').val() =="")
	{
		if(form_validation('txt_job_no','Job No')==false)
		{
			return;
		}
	}
	var data="action=report_generate&type="+type+get_submitted_data_string('cbo_buyer_name*cbo_working_company_name*cbo_location_name*cbo_shift_name*cbo_season_year*txt_job_no*txt_date_from*txt_date_to*cbo_floor_id',"../../");
	freeze_window('3');
	http.open("POST","requires/table_wise_cutting_report_controller.php",true);
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


function openmypage_job()
{
	// alert('ok');
	if( form_validation('cbo_working_company_name','Company Name')==false )
	{
		return;
	}

	var companyID = $("#cbo_working_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_season_year = $("#cbo_season_year").val();

	var page_link='requires/table_wise_cutting_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_season_year;

	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var data_arr=this.contentDoc.getElementById("hide_job_no").value.split("_");
		$('#txt_job_no').val(data_arr[1]);
		$('#txt_job_id').val(data_arr[0]);
	}
}



function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/table_wise_cutting_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&job_no='+job_no;
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

function openmypage_color()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/table_wise_cutting_report_controller.php?action=color_search_popup&companyID='+companyID+'&job_no='+job_no;
	var title='Color Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;

		$('#txt_color').val(order_no);
		$('#hide_color_id').val(order_id);
	}
}

// for report lay chart
function generate_report_lay_chart(data)
{
	var action	= 'table_wise_cutting_report_print';
	window.open("../../prod_planning/cutting_plan/requires/table_wise_cutting_report_controller.php?data=" + data+'&action='+action, true );
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


function fnc_gmt_item(job_no)
{
		//alert(job_no);
		load_drop_down( 'requires/table_wise_cutting_report_controller',job_no,'load_drop_down_gmts_item','gmt_td' );
}

function generate_report_bundle_list(cut_no,entry_form){
    var action = "cut_lay_bundle_print";
    var data = return_global_ajax_value( cut_no, 'str_data_from_cut_no', '', 'requires/table_wise_cutting_report_controller');
    var data = data.trim();
    //alert (data);
	if(entry_form==99)
	{
		window.open("../cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php?data=" + data+'&action='+action, true );
	}
	else if(entry_form==289)
	{
		window.open("../cutting_plan/requires/woven_cut_and_lay_ratio_wise_entry_controller.php?data=" + data+'&action='+action, true );
	}
}

function generate_report_qc(company_id,source_id,working_com,qc_id,cut_no){
	var report_title="Cutting QC V2";
	var data=working_com+'*'+company_id+'*'+qc_id+'*'+source_id+'*'+report_title+'*'+cut_no;
	var action="print4_reject_report";
	window.open("../../production/requires/table_wise_cutting_controller_urmi.php?data=" + data+'&action='+action, true );
}

function print_report_button_setting(report_ids)
    {

        $('#show_button').hide();
        $('#show_button1').hide();
        $('#show_button2').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==260){$('#show_button1').show();}
            else if(items==265){$('#show_button2').show();}
            });
    }

	function load_floor(id)
	{
		var companyID = document.getElementById('cbo_working_company_name').value;
		var locationID = document.getElementById('cbo_location_name').value;
	  	load_drop_down('requires/table_wise_cutting_report_controller',companyID+'_'+locationID, 'load_drop_down_floor', 'floor_td' );
		set_multiselect('cbo_floor_id','0','0','','0');
	}

	function load_location()
	{
		// alert('ok');
		var companyID = document.getElementById('cbo_working_company_name').value;
		get_php_form_data(companyID,'print_button_variable_setting','requires/table_wise_cutting_report_controller');

		load_drop_down('requires/table_wise_cutting_report_controller',companyID, 'load_drop_down_location', 'location_td' );
		// set_multiselect('cbo_location_name','0','0','','0',);
		set_multiselect('cbo_location_name','0','0','','0','load_floor()');
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
         <h3 style="width:1190px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1190px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>

                   	<th>Working Company</th>
                   	<th>Location</th>
                   	<th>Cutting Floor</th>
                   	<th title="Apply in Show2 Button">Shift</th>
					<th class="must_entry_caption">Job Year</th>
                    <th>Buyer Name</th>

                    <th class="must_entry_caption">Job No</th>
                    <th colspan="2" id="cap_cut_date">Cutting Date Range</th>
					<th></th>
                    <!-- <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th> -->

                </thead>
                </thead>
                <tbody>
                    <tr class="general">

                        <td>
                        <?
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select  --", $selected, "load_drop_down( 'requires/table_wise_cutting_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/table_wise_cutting_report_controller');" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
                        <td id="location_td">
                        <?
	                       echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location comp where status_active=1 and is_deleted=0  order by location_name","id,location_name", 0, "-- Select  --", $selected, "" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
						<td id="floor_td">
							<?
								echo create_drop_down( "cbo_floor_id", 142, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
						<td>
					    <?
						echo create_drop_down( "cbo_shift_name", 120, $shift_name,"", 1, "--ALL--", 0, "",0 );
						?>
                       </td>
						<td width="60" id="season_year_td"><!-- fpr show3 button -->
									<?
										echo create_drop_down( "cbo_season_year", 60, $year,"", 1, "-- All --", date('Y'), "",0,"" );
									?>
						</td>
                        <td id="buyer_td"> <?
								echo create_drop_down( "cbo_buyer_name", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
					    </td>
                        <td>
                       <!-- <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" /> -->
                         <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_job();" readonly  />
                        <input type="hidden" name="txt_job_no" id="txt_job_no"/>    <input type="hidden" name="txt_job_no" id="txt_job_no"/></td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"></td>
						<td>
						<input type="button" id="show_button__" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(3)" />
						<input type="button" id="show_button__" class="formbutton" style="width:70px" value="Show2" onClick="fn_show_report(4)" />
						</td>
                    </tr>
                    <tr>
                	<td colspan="13"><? echo load_month_buttons(1); ?></td>


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
<script>
	set_multiselect('cbo_working_company_name','0','0','','0','load_location()');
	set_multiselect('cbo_location_name','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>