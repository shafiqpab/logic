<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Dyes and Chemical Procurement Progress Report.
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	26-01-2022
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
echo load_html_head_contents("Dyes and Chemical Procurement Progress Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission = '<? echo $permission; ?>';
	

	var tableFilters =
	{
		col_operation: {
			id: ['value_tot_req_qty_id','value_tot_req_amount_id','value_tot_wo_qty_id','value_tot_wo_amount_id','value_tot_pi_qnty_id','value_tot_pi_amt_id','value_tot_mrr_qnty_id','value_tot_mrr_value_id','value_tot_short_amt_id','value_tot_pipe_line_id'],
			col: [19,21,29,31,41,43,51,52,53,54],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	}

	var tableFiltersYarnAnalysis =
	{
		col_operation: {
			id: ['value_tot_req_qty_id','value_tot_req_amount_id','value_tot_wo_qty_id','value_tot_wo_amount_id','value_tot_pi_qnty_id','value_tot_pi_amt_id','value_tot_mrr_qnty_id','value_tot_rcvd_due','value_tot_issue_qnty','value_tot_Stock_Qty'],
			col: [19,21,29,31,41,43,51,52,53,54],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	}
 
	function generate_report(type)
	{
		var txt_search_no=$('#txt_search_no').val();
		if(txt_search_no!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_year*txt_search_no*txt_date_from*txt_date_to","../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/dyes_chemical_procurement_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);
			//alert(response[2]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+response[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(response[2]==1 || response[2]==3) setFilterGrid("table_body",-1,tableFilters);
			if(response[2]==2 || response[2]==3) setFilterGrid("table_body2",-1);
			if(response[2]==3) setFilterGrid("table_body3",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window(str)
	{
		if(str==1 || str==3)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tr:first').hide();
		}
		if(str==2 || str==3)
		{
			document.getElementById('scroll_body2').style.overflow="auto";
			document.getElementById('scroll_body2').style.maxHeight="none";
			$('#table_body2 tr:first').hide();
		}
		if(str==3)
		{
			document.getElementById('scroll_body3').style.overflow="auto";
			document.getElementById('scroll_body3').style.maxHeight="none";
			$('#table_body3 tr:first').hide();
		}
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		if(str==1 || str==3)
		{
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="300px";
			$('#table_body tr:first').show();
		}
		if(str==2 || str==3)
		{
			document.getElementById('scroll_body2').style.overflowY="scroll";
			document.getElementById('scroll_body2').style.maxHeight="300px";
			$('#table_body2 tr:first').show();
		}
		if(str==3)
		{
			document.getElementById('scroll_body3').style.overflowY="scroll";
			document.getElementById('scroll_body3').style.maxHeight="300px";
			$('#table_body3 tr:first').show();
		}
	}
	
	function fnc_html_change(id)
	{
		if(id==1)
		{
			$('#no_html').html("Requisition No");
			$('#date_html').html("Requisition Date");
		}
		else if(id==2)
		{
			$('#no_html').html("WO No");
			$('#date_html').html("WO Date");
		}
		else
		{
			$('#no_html').html("PI No");
			$('#date_html').html("PI Date");
		}
	}
	function fn_mrr_details(pi_id,item_description,receive_basis,action)
	{
		//alert(action);
		//page_link='requires/dyes_chemical_procurement_progress_report_controller.php?action='+action+'&booking_id='+booking_id+'&book_basis='+book_basis+'&color_id='+color_id+'&yarn_type='+yarn_type+'&count_id='+count_id+'&composition='+composition+'&pi_ids='+piIds;
		page_link='requires/dyes_chemical_procurement_progress_report_controller.php?action='+action+'&pi_id='+pi_id+'&item_description='+item_description+'&receive_basis='+receive_basis;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=624px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
		
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_procureument_rpt" name="frm_procureument_rpt">
    <div style="width:760px;">
    <h3 align="left" id="accordion_h1" style="width:678px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:760px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="760" border="1" rules="all">
            <thead>
                <th width="150" class="must_entry_caption">Company</th>
                <th width="90">Year</th>
                <th width="120" id="no_html">PI No</th>
                <th width="200" id="date_html">PI Date</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:160px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center">
                    <?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                    ?>
                    </td>
                    <td  align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_year", 80, $year,"", 1, "All",$year_current);
                    ?>
                    </td>
                    <td align="center"><input type="text" id="txt_search_no" name="txt_req_no" style="width:100px;" class="text_boxes" ></td>
                    <td  align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:65px">TO
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"></td>
                    <td align="center">
                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:160px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>