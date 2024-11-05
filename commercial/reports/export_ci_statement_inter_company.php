<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export CI Statement Inter Company Report.
Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	04-04-2019
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
echo load_html_head_contents("Monthly Bank Submission/Export Status", "../../", 1, 1,'','','');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var tableFilters =
	{
		col_61: "none",
		col_operation: {
		id: ["value_total_grs_value","value_total_discount_value","value_total_net_invo_value","value_total_rlz_amt"],
	   col: [13,14,17,30],
	   operation: ["sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var permission = '<? echo $permission; ?>';

	function generate_report(operation)
	{
		var cbo_based_on=$('#cbo_based_on').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_location=$('#cbo_location').val();
		var txt_invoice_no=$('#txt_invoice_no').val();
		var txt_lc_sc_no=$('#txt_lc_sc_no').val();
		if(cbo_buyer_name>0 || txt_invoice_no!="" || txt_lc_sc_no!="")
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
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_location*cbo_based_on*txt_date_from*txt_date_to*txt_invoice_no*txt_lc_sc_no","../../")+'&report_title='+report_title+'&operation='+operation;
		freeze_window(3);
		http.open("POST","requires/export_ci_statement_inter_company_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			//alert(http.responseText);//return;
			$('#report_container2').html(response[0]);
			release_freezing();
			//alert(response[1]);			
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();		
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body_short').style.overflow="auto";
		document.getElementById('scroll_body_short').style.maxHeight="none";
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body_short').style.overflowY="auto";
		document.getElementById('scroll_body_short').style.maxHeight="400px";
		$("#table_body tr:first").show();
	}

	function openmypage_pi_date(wo_po_break_down,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_ci_statement_inter_company_controller.php?wo_po_break_down='+wo_po_break_down+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}

	function lc_sc_no_auto_com()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var company_id=$('#cbo_company_name').val();
			var all_code=trim(return_global_ajax_value( company_id, 'lc_sc_no_auto_com', '', 'requires/export_ci_statement_inter_company_controller'));
			str_code =eval(all_code);
			$("#txt_lc_sc_no").autocomplete({
				source: str_code
			});
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:990px;">
    <h3 align="left" id="accordion_h1" style="width:990px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
    <fieldset style="width:990px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="980" border="1" rules="all">
            <thead>
                <th width="140" class="must_entry_caption">Company</th>
                <th width="140">Buyer</th>
                <th width="130">Location</th>
                <th width="70">Invoice No</th>
                <th width="80">LC/SC No</th>
                <th width="120">Based On</th>
                <th width="150" class="must_entry_caption">Date Range</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_ci_statement_inter_company_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                    ?>
                    </td>
                    <td id="buyer_td"><?
                        echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                    ?></td>
                    <td>
                    <?
                        echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location","id,location_name", 1, "-- Select Location --", $selected,"",0,"" );
                    ?>
                    </td>
                    <td>
                    <input type="text" id="txt_invoice_no" name="txt_invoice_no" class="text_boxes" style="width:70px;" placeholder="Write" >
                    </td>
                    <td>
                    <input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:70px;" onFocus="lc_sc_no_auto_com()" placeholder="Write" >
                    </td>
                    <td>
                    <?
                    $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date",4=>"BL/Cargo Date",5=>"Shipping Bill Date",6=>"Realization Date",7=>"Insert Date",8=>"Without Realization");
                        echo create_drop_down( "cbo_based_on", 120, $based_on_arr,"", 1, "------ Select ------", 0);
                    ?>
                    </td>
                    <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" readonly>
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" readonly>
                    </td>
                    <td align="center">
                    <input type="button" name="search" id="search_1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>
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
