<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Shipment Wise Performance Report .
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	30-07-2019
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

echo load_html_head_contents("Shipment Wise Performance Report ","../../../", 1, 1, $unicode,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
	{
			col_operation: {
			id: ["td_poQty","td_shipQty","td_totshipQty","td_shipValue","td_fabricLocal","td_fabricImport","td_commImportValue","td_accAmt","td_aopAmt","td_printAmt","td_embAmt","td_otherAmt","td_airPcs","td_airAmt","td_shortValue","td_ttlCost","td_prodPcs","td_prodHour","td_cm"],
			col: [13,14,15,17,18,19,20,21,22,23,24,25,26,27,29,30,31,33,39],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','LC Company*From Date*To Date')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_style_ref*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/shipment_performance_report_controller.php",true);
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
	
	function fnc_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/shipment_performance_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			$('#txt_job_no').val(job_no);
		}
	}
	
	function getBuyerId() 
	{
	    var company_name = document.getElementById('cbo_company_name').value;
		//alert(company_name)
	    if(company_name !='') {
		  var data="action=load_drop_down_buyer&data="+company_name;
		  //alert(data);die;
		  http.open("POST","requires/shipment_performance_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
	          }			 
	      };
	    }         
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
</script>
</head>
<body onLoad="set_hotkey();">
<form id="shipmentPerformance_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:800px;" id="content_search_panel">
            <table class="rpt_table" width="800" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">LC Company</th>
                    <th width="140">Buyer Name</th>
                    <th width="60">Year</th>
                    <th width="90">Job No</th>
                    <th width="100">Style Ref.</th>
                    <th width="140" colspan="2" id="search_by_th_up" class="must_entry_caption">Ex-Factory Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('shipmentPerformance_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="lccompany_td"><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--All Company--", $selected, "load_drop_down( 'requires/shipment_performance_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td');" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-All-", "", "",0,"" ); ?></td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="fnc_job_no();" readonly ></td>
                        <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px" placeholder="Write"></td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" onChange="fnc_date_check();" ></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" placeholder="To Date" onChange="fnc_date_check(this.value);"></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1)" /></td>
                    </tr>
                    <tr align="center">
                        <td colspan="8"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>
</body>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
	setTimeout[($("#lccompany_td a").attr("onclick","disappear_list(cbo_company_name,'0'); getBuyerId();"),3000)]; 
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>