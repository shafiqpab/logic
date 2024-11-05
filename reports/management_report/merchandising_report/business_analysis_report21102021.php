<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Business Analysis Report.
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	03-10-2019
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

echo load_html_head_contents("Business Analysis Report","../../../", 1, 1, $unicode,1,1);
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
		freeze_window(3);
		if(form_validation('cbo_company_id*cbo_from_year*cbo_to_year','Company*From Fiscal Year*To Fiscal Year')==false)
		{
			release_freezing();
			return;
		}
		
		$('#report_container_summary').html('');
		$('#report_container_month_details').html('');
		$('#report_container_buyer_details').html('');
		$('#report_container_po_details').html('');
		
		var report_title=$( "div.form_caption" ).html();
		if($('#cbo_location_id').val()!=0)
		{
			$("#cbo_location_id").attr("disabled",true);
		}
		else
		{
			$("#cbo_location_id").attr("disabled",false);
		}

		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_order_status*cbo_from_year*cbo_to_year',"../../../")+'&report_title='+report_title;
		
		http.open("POST","requires/business_analysis_report_controller21102021.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
			//setFilterGrid("table_body",-1,tableFilters);
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
	
	function fncexcesscost(action,data,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/business_analysis_report_controller21102021.php?action='+action+'&data='+data, 'Excess Mat. Cost', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function fncpanalty(action,data,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/business_analysis_report_controller21102021.php?action='+action+'&data='+data, 'Penalty', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_poremarkspopup(data,action)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/business_analysis_report_controller21102021.php?action='+action+'&data='+data, 'PO Remarks Popup', 'width=400px,height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_report(company,year,month,type)
	{
		var location_id=$('#cbo_location_id').val();
		var order_status=$('#cbo_order_status').val();
		if(type==3 || type==4) var buyer_id=month;
		if(type==1)//Month & Buyer Wise Summary
		{
			$('#report_container_details').html('');
			show_list_view(company+'***'+year+'***'+type+'***'+location_id+'***'+order_status,'month_buyer_summary_list_view','report_container_summary','requires/business_analysis_report_controller21102021', '');
		}
		else if(type==2)//Month Wise Details
		{
			show_list_view(company+'***'+year+'***'+month+'***'+type+'***'+location_id+'***'+order_status,'month_details_list_view','report_container_month_details','requires/business_analysis_report_controller21102021', '');
		}
		else if(type==3)//Buyer Wise Details
		{
			show_list_view(company+'***'+year+'***'+buyer_id+'***'+type+'***'+location_id+'***'+order_status,'buyer_details_list_view','report_container_buyer_details','requires/business_analysis_report_controller21102021', '');
		}
		else if(type==4)//Po Wise Details
		{
			show_list_view(company+'***'+year+'***'+buyer_id+'***'+type+'***'+location_id+'***'+order_status,'po_details_list_view','report_container_po_details','requires/business_analysis_report_controller21102021', '');
		}
	}
	
	function generate_preCostReport(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var rate_amt=2;
		var data="action="+type
			+"&rate_amt="+rate_amt
			+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id="+po_id
			+"&cbo_costing_per="+costing_per
		;
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}
	
	function toggleSigns(span) {
		//alert(span)
		if($("#"+span).text()=='+'){
			$("#"+span).text("-")
		}else{
			$("#"+span).text("+")
		}
	}
	
	function yearT(span,selector){
		//alert(span+'_'+selector)
		$( selector ).toggle( "fast", function() {
		});
		toggleSigns(span)
	}
</script>

<style>
	.year,.month,.buyer,.monthdetails,.buyerdetails,.podtls{
		display:none;
	}
	
	.adl-signs{
		font-weight:bold;
		font-size:18px;
		cursor:pointer
	}
</style>
</head>
<body onLoad="set_hotkey();">
<form id="businessAnalysisReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:740px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:740px;" id="content_search_panel">
            <table class="rpt_table" width="740" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="140">Location</th>
                    <th width="120">Status</th>
                    <th width="120" class="must_entry_caption">From Fiscal Year</th>
                    <th width="120" class="must_entry_caption">To Fiscal Year</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('businessAnalysisReport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/business_analysis_report_controller21102021', this.value, 'load_drop_down_location', 'location_td');" ); ?></td>
                        <td id="location_td"><?=create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-All-", $selected, "" ); ?></td>
                        <td>
                        <? $statusArray=array(1 => "Open Order", 2 => "Close Order");
						echo create_drop_down( "cbo_order_status", 120, $statusArray, "",  1, "All Order", 0, "",0 ); ?></td>
                        <td>
						<?php
                        $starting_year  =date('Y', strtotime('-5 year'));
                        $ending_year = date('Y', strtotime('+4 year'));
						$fiscal_year_arr=array();
                        for($starting_year; $starting_year <= $ending_year; $starting_year++)
						{
							$fiscal_year='';
							$conYear=1;
							$fiscal_year=$starting_year.'-'.($starting_year+$conYear);
							//echo $starting_year.'-'.($starting_year+$conYear).'<br>';
							$fiscal_year_arr[$fiscal_year]=$fiscal_year;
                        }
						echo create_drop_down( "cbo_from_year",  120, $fiscal_year_arr, "",  1, "--Select--", $selected, "",0 ); ?></td>
                        <td><?=create_drop_down( "cbo_to_year",  120, $fiscal_year_arr, "",  1, "--Select--", $selected, "",0 ); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1);" /></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div id="report_container3"></div>
    <br>
    <div id="report_container_summary"></div>
    <br>
    <div id="report_container_month_details"></div>
    <br>
    <div id="report_container_buyer_details"></div>
    <br>
    <div id="report_container_po_details"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>