<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Shipment Performence Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-07-2023
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
echo load_html_head_contents("Shipment Performence Report","../../../", 1, 1, $unicode,1,1);
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
	 
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name*cbo_year_selection','Company Name*Year')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_brand_id*cbo_team_leader*cbo_report_type*cbo_year_selection*cbo_value_type',"../../../")+'&report_title='+report_title;
			//alert(data); return;
			freeze_window(3);
			http.open("POST","requires/shipment_performance_report_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		//setFilterGrid("table_body",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();*/
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();*/
	}
</script>
</head>
    <body onLoad="set_hotkey();">
        <div style="100%" align="center">
            <form id="consolidorder_1" name="consolidorder_1">
                <div style="width:950px;">
					<? echo load_freeze_divs ("../../../"); ?>
                    <h3 align="left" id="accordion_h1" style="width:950px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                    <div id="content_search_panel"> 
                        <fieldset style="width:950px;">
                            <table class="rpt_table" width="100%" cellpadding="1" cellspacing="2" align="center" rules="all">
                                <thead>
                                    <tr>                   
                                    <th width="140" class="must_entry_caption">Company Name</th>
                                    <th width="140">Buyer Name</th>
									<th width="140">Brand</th>
                                    <th width="150">Team Leader</th>
                                    <th width="100">Report Type</th>
                                    <th width="70" class="must_entry_caption">Year</th>
                                	<th width="80" class="must_entry_caption">Value Level</th>
                                    <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('consolidorder_1','report_container*report_container2','','','')" /></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="general">
                                        <td><?=create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/shipment_performance_report_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                                        <td id="buyer_td" align="center"><?=create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" ); ?></td>
                                        <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 140, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                                        <td align="center"><?=create_drop_down( "cbo_team_leader", 140, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", 0, "",0 ); ?></td>
                                        <td><? 
                                        $rpt_type_arr=array(1 => "Buyer Wise", 2 => "Brand Wise", 3 => "Team Wise", 4 => "Month Wise");
                                        $rpt_value_typeArr=array(1 => "Qty", 2 => "Value", 3 => "Minute");
                                        
                                        echo create_drop_down( "cbo_report_type", 100,$rpt_type_arr,"", 0, "-- Select --", "1","" ); ?></td>
                                        <td align="center"><?=create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );	?></td>
                                        <td><? echo create_drop_down( "cbo_value_type", 80,$rpt_value_typeArr,"", 1, "-ALL-", "","" ); ?></td>
                                        <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </form>
        
            <!--<div id="report_container" align="center"></div>-->
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>
            <div style="display:none" id="data_panel"></div>  
        </div>    
    </body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
