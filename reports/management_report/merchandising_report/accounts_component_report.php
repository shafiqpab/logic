<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Accounts Component Report.
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	17-07-2019
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Accounts Component Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
	{
		col_7: "none",
		col_operation: 
		{
			id: ["td_fabAmt","td_aopAmt","td_accAmt","td_embAmt","td_labAmt","td_ttlAmt","td_wipQty","td_wipValue"],
			col: [17,18,20,22,24,26,27,28],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(type)
	{
		var wcompany_name=$('#cbo_wcompany_name').val();
		var production_process=$('#cbo_production_process').val();
		var style_ref=$('#txt_style_ref').val();
		var date_from=$('#txt_date_from').val();
		var date_to=$('#txt_date_to').val();
		if(wcompany_name =="" && production_process =="" ){
			if(form_validation('cbo_wcompany_name*cbo_production_process','Working Company*Production Process')==false){
				return;
			}
		}
		 if( style_ref =="" && date_from ==""){
			if(form_validation('cbo_wcompany_name*cbo_production_process*txt_date_from*txt_date_to','Working Company*Production Process*From Date*To Date')==false)
				{
					return;
				}
		}
		
	
		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_wcompany_name*cbo_location*cbo_production_process*cbo_floor*cbo_buyer_name*cbo_year*txt_date_from*txt_date_to*txt_style_ref*hidden_style_ref_id',"../../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/accounts_component_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			//alert(reponse[1]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);

			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			show_msg('3');
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
	}
	
	function fnc_date_check(todate)
	{
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
		if(from_date!="" && to_date!="")
		{
			var to_dates=to_date.split('-');
			var from_dates=from_date.split('-');
			var to_mon_year=to_dates[1]+'-'+to_dates[2];
			var from_mon_year=from_dates[1]+'-'+from_dates[2];
			//alert(from_mon_year);
			if(from_mon_year==to_mon_year)
			{
				$('#txt_date_from').val(from_date);
				$('#txt_date_to').val(to_date);
			}
			else
			{
				alert('Month Mixed Not Allow');
				$('#txt_date_to').val('');
			}
		}
	}
	function open_style_popup()
	{
		if(form_validation('cbo_wcompany_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_wcompany_name").val();
		var page_link='requires/accounts_component_report_controller.php?action=style_search_popup&companyID='+companyID;
		var title='Style Ref';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=410px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_ref=this.contentDoc.getElementById("hide_style_ref").value;
			var style_id=this.contentDoc.getElementById("hide_style_id").value;
			
			$('#txt_style_ref').val(style_ref);
			$('#hidden_style_ref_id').val(style_id);	 
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="componentReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1050px;" id="content_search_panel">
            <table class="rpt_table" width="1050px" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">Working Company</th>
                    <th width="120">Location</th>
                    <th width="80" class="must_entry_caption">Production Process</th>
                    <th width="120">Floor</th>
                    <th width="140">Buyer Name</th>
                    <th width="60">Year</th>
                    <th width="150">Style Ref</th>
                    <th width="140" colspan="2" id="search_by_th_up" class="must_entry_caption">Production Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('componentReport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_wcompany_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--All Company--", $selected, "load_drop_down( 'requires/accounts_component_report_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/accounts_component_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location",  120, $blank_array, "",  1, "--Select Location--", $selected, "",1 ); ?></td>
                        <td><?
								$production_process_arr=array(1=>"Sewing Output",2=>"Poly Output");
								echo create_drop_down( "cbo_production_process", 80, $production_process_arr,"", 1, "--Select--", $selected, "load_drop_down('requires/accounts_component_report_controller', $('#cbo_location').val()+'_'+this.value, 'load_drop_down_floor', 'floor_td' );" );
                            ?>
                        </td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 120, $blank_array,"", 1,"--Select Floor--", $selected, "",1 ); ?></td>
                        
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td>
                        	<input type="text" class="text_boxes" name="txt_style_ref" id="txt_style_ref" placeholder="Browse" onclick="open_style_popup();" readonly>
                        	<input type="hidden" name="hidden_style_ref_id" id="hidden_style_ref_id">
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" onChange="fnc_date_check();" ></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date" onChange="fnc_date_check(this.value);" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1)" /></td>
                    </tr>
                    <tr align="center" class="general">
                        <td colspan="10"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div id="report_container3"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>