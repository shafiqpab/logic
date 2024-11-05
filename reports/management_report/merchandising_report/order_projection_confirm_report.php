<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create Order Projection VS Confirm Report
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08/10/2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 
-----------------------------------------------------------------------*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
//echo load_html_head_contents("Order Projection VS Confirm","../../../", 1, 1, $unicode,'',1);
echo load_html_head_contents("Order Projection VS Confirm","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id','Company Name*')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year_id*cbo_end_year_name*cbo_from_month*cbo_to_month',"../../../")+'&report_title='+report_title;
		}
		else
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_brand_id*cbo_year_id*cbo_end_year_name*cbo_from_month*cbo_to_month',"../../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/order_projection_confirm_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[0]);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
		
        <form name="orderProjectionVSConfirm_1" id="orderProjectionVSConfirm_1" autocomplete="off" > 
        <? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:910px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:910px" >      
			<fieldset>
                <table class="rpt_table" width="910" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Company</th>
                            <th>Buyer</th>
							<th>Brand</th>
                            <th>From Year</th>
                            <th class="must_entry_caption">From Month</th>
							<th>To Year</th>
                            <th class="must_entry_caption">To Month</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:90px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down("cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/order_projection_confirm_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                            ?> 
                        </td>
                        <td id="buyer_td"><? echo create_drop_down("cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", $selected, ""); ?></td>
						<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        <td><? echo create_drop_down("cbo_year_id", 60, $year,"", 1,"-- All --", date("Y",time()), "",0,""); ?></td>
                        <td><? echo create_drop_down("cbo_from_month", 100, $months,"", 1, "--From Month--", $selected, "",""); ?></td>
						<td><? echo create_drop_down("cbo_end_year_name", 60, $year,"", 1,"-- All --", date("Y",time()), "",0,""); ?></td>
                        <td><? echo create_drop_down("cbo_to_month", 100, $months,"", 1, "--To Month--", $selected,"",""); ?></td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1)" style="width:90px" class="formbutton" />
							<input type="button" name="search2" id="search2" value="Show 2" onClick="fn_report_generated(2)" style="width:90px" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </form>
 </div> 
 <div id="report_container" align="center"></div>
 <div id="report_container2"></div>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>