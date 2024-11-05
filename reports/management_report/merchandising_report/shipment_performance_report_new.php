<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Consolidated Order Summary Report.
Functionality	:	
JS Functions	:
Created by		:	Shariar Ahmed
Creation date 	: 	18-04-2022
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
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_brand_id*cbo_team_leader*cbo_year_selection*cbo_month*cbo_end_year_name*cbo_month_end',"../../../")+'&report_title='+report_title;
			//alert(data); return;
			freeze_window(3);
			http.open("POST","requires/shipment_performance_report_new_controller.php",true);
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
	
	function calculate_date(str)
	{		
		if(str==0){
			var thisDate=($('#txt_date_from').val()).split('-');
			var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
			
			//alert(last);return;
			var last_date = last.getDate();
			var month = last.getMonth()+1;
			var year = last.getFullYear();
			
			if(month<10)
			{
				var months='0'+month;
			}
			else
			{
				var months=month;
			}
			
			var last_full_date=last_date+'-'+months+'-'+year;
			var first_full_date='01'+'-'+months+'-'+year;
			
			$('#txt_date_from').val(first_full_date);
			$('#txt_date_to').val(last_full_date);
		}
		else
		{
			
			var thisDate=($('#txt_date_to').val()).split('-');
			var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
			
			//alert(last);return;
			var last_date = last.getDate();
			var month = last.getMonth()+1;
			var year = last.getFullYear();
			
			if(month<10)
			{
				var months='0'+month;
			}
			else
			{
				var months=month;
			}
			var last_full_date=last_date+'-'+months+'-'+year;
			$('#txt_date_to').val(last_full_date);
			
		}
	}
	
</script>
</head>
    <body onLoad="set_hotkey();">
        <div style="100%" align="center">
            <form id="consolid_order_1" name="consolid_order_1">
                <div style="width:1130px;">
					<? echo load_freeze_divs ("../../../"); ?>
                    <h3 align="left" id="accordion_h1" style="width:1130px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                    <div id="content_search_panel"> 
                        <fieldset style="width:1130px;">
                            <table class="rpt_table" width="100%" cellpadding="1" cellspacing="2" align="center" rules="all">
                                <thead>
                                    <tr>                   
                                    <th width="140" class="must_entry_caption">Company Name</th>
                                    <th width="140">Buyer Name</th>
									<th width="140">Brand</th>
                                    <th width="150">Team Leader</th>
                                    <th width="140" class="must_entry_caption">Start Pub. Ship/ S.Forecast Year</th>
                                	<th width="100" class="must_entry_caption">Start Month</th>
                                	<th width="140" class="must_entry_caption">End Pub. Ship/ S.Forecast Year</th>
                                	<th width="100" class="must_entry_caption">End Month</th>
                                    <th width="80"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90%" onClick="reset_form('consolid_order_1','report_container*report_container2','','','')" /></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="general">
                                    <td><?=create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/shipment_performance_report_new_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                                    <td id="buyer_td" align="center"><?=create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
									<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 140, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                                    <td align="center"><?=create_drop_down( "cbo_team_leader", 140, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", 0, "",0 ); ?></td>
                                    <td align="center"><?=create_drop_down( "cbo_year_selection", 140, $year,"", 1, "-- Select --", date('Y'), "",0 );	?></td>
									<td><? echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                	<td><? echo create_drop_down( "cbo_end_year_name", 140,$year,"id,year", 1, "-- Select Year --", date('Y'),"" ); ?></td>
                                	<td><? echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                    <td align="center"><input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1);" /></td>
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
