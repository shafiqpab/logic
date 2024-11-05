<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Ship Date Extention Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	21-05-2015
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

//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Ship Date Extention Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value"],
	   col: [2,3,4,5,6,7,8],
	   operation: ["sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Shipmet Date From*Shipmet Date To')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+'&report_title='+report_title+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to',"../../../");
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/ship_date_extention_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		
	}
		
		
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		setFilterGrid("table_body",-1);
			
	 		show_msg('3');
			release_freezing();
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();
	}
	
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="100%" align="center">
<form id="consolid_order_1" name="consolid_order_1">
    <div style="width:1000px;">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:750px;">
                <table class="rpt_table" width="750" cellpadding="1" cellspacing="2" align="center" rules="all">
                	<thead>
                    	<tr>                   
                            <th width="200" class="must_entry_caption">Company Name</th>
                             <th width="200">Buyer Name</th>
                            <th colspan="2" class="must_entry_caption">Shipment Date</th>
                            <th width="100"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90%" onClick="reset_form('consolid_order_1','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td align="center"> 
                            <? 
                              echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/ship_date_extention_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                         <td id="buyer_td" align="center">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 180, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td align="center">
                             <input id="txt_date_from" name="txt_date_from" class="datepicker" style="width:100px;"  placeholder="Select Date" readonly >
                         </td>
                         <td align="center">
                             <input id="txt_date_to" name="txt_date_to" class="datepicker" style="width:100px;"  placeholder="Select Date" readonly>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90%" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
