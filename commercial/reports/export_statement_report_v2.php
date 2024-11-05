<?

/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export Statement Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	18-01-2014
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
		col_30: "none",
		col_operation: {
		id: ["value_gt_total_val","total_inv_qty","value_total_invoice_val","value_total_sub_collectin","value_total_sub_pur","value_all_purchase","value_total_realize","value_total_deduction","value_total_advice_amt","value_total_freight","value_total_paid","value_total_panding","total_parcentage"],
	   col: [4,7,8,9,10,11,12,13,14,15,16,17,19],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	 var tableFilters2 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_buyer_total_lc_val","buyer_total_inv_qty","value_buy_total_invoice_val","value_buy_total_sub_collectin","value_buy_total_sub_pur","value_buy_all_purchase","value_buy_total_realize","value_buy_total_deduction","value_buy_total_in_hand","value_buy_total_freight","value_buy_total_paid","value_buy_total_panding","buy_total_parcentage"],
	   col: [2,3,4,5,6,7,9,10,11,13,14,15,17],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	 var tableFilters3 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["unsbmit_inv_qty","value_unsbmit_invoice_val"],
	   col: [3,4],
	   operation: ["sum","sum"],
	 write_method: ["innerHTML","innerHTML"]
		}
	 }
	 
	 var tableFilters4 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_no_invoice_val"],
	   col: [4],
	   operation: ["sum"],
	 write_method: ["innerHTML"]
		}
	 }
	 
	 var tableFilters5 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_bank_sub_qnty","value_total_bank_sub_value","value_total_bank_sub_collect_value","value_total_bank_sub_nago_value","value_total_bank_sub_purchase_value"],
	   col: [4,5,6,7,8],
	   operation: ["sum","sum","sum","sum","sum"],
	 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	 var tableFilters6 = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_sub_qnty","value_total_buyer_sub_value"],
	   col: [5,6],
	   operation: ["sum","sum"],
	 write_method: ["innerHTML","innerHTML"]
		}
	 }
 
	var permission = '<? echo $permission; ?>';
	
function generate_report()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_lien_bank*cbo_search_by*txt_exchange_rate*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/export_statement_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body2",-1,tableFilters2);
			setFilterGrid("table_body3",-1,tableFilters3);
			setFilterGrid("table_body4",-1,tableFilters4);
			setFilterGrid("table_body5",-1,tableFilters5);
			setFilterGrid("table_body6",-1,tableFilters6);

			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
		
		document.getElementById('scroll_body4').style.overflow="auto";
		document.getElementById('scroll_body4').style.maxHeight="none";
		document.getElementById('scroll_body5').style.overflow="auto";
		document.getElementById('scroll_body5').style.maxHeight="none";
		document.getElementById('scroll_body6').style.overflow="auto";
		document.getElementById('scroll_body6').style.maxHeight="none";
		
		
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();
		$('#table_body4 tr:first').hide();
		$('#table_body5 tr:first').hide();
		$('#table_body6 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
		$('#table_body4 tr:first').show();
		$('#table_body5 tr:first').show();
		$('#table_body6 tr:first').show();
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="300px";
		
		document.getElementById('scroll_body3').style.overflowY="scroll";
		document.getElementById('scroll_body3').style.maxHeight="300px";
		
		document.getElementById('scroll_body4').style.overflowY="scroll";
		document.getElementById('scroll_body4').style.maxHeight="300px";
		
		document.getElementById('scroll_body5').style.overflowY="scroll";
		document.getElementById('scroll_body5').style.maxHeight="300px";
		
		document.getElementById('scroll_body6').style.overflowY="scroll";
		document.getElementById('scroll_body6').style.maxHeight="300px";
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:1100px;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_export_statement" name="frm_export_statement">
    <div style="width:100%;" align="center">
    <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:1000px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="1000">
            <thead>
                <th width="130" class="must_entry_caption">Company</th>
                <th width="140">Buyer</th>
                <th width="130">Lien Bank</th>
                <th width="130">LC Include</th>
                <th width="100">Echg. Rate</th>
                <th width="150">LC/SC Date Range</th>
                <th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('frm_export_statement','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr >
                    <td>
                    <?
                    echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_statement_report_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                    ?>
                    </td>
                    <td id="buyer_td"><? 
                    echo create_drop_down( "cbo_buyer_name", 155, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                    ?></td>
                    <td>
                    <?
                    echo create_drop_down( "cbo_lien_bank", 130, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
                    ?>
                    </td>
                    <td>	
                    <?
						$lc_include_arr=array(1=>"All LC",2=>"Exclude Fully Realized",3=>"Only Fully Realized");
						echo create_drop_down( "cbo_search_by", 145, $lc_include_arr,"", 1, "-- Select --", 1,"",0,"" );
                    ?>
                    </td>
                    <td>
                    <input type="text" id="txt_exchange_rate" name="txt_exchange_rate" style="width:110px" class="text_boxes_numeric">
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                            To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                    </td>
                    <td align="center">
                    <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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