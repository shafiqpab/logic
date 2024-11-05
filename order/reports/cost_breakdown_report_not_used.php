<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cost Breakdown Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	10-02-2013
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
//echo load_html_head_contents("Cost Breakdown Report","../../", 1, 1, $unicode,1,1);
echo load_html_head_contents("Cost Breakdown Report", "../../", 1, 1,$unicode,'','');
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to',"../../");
			freeze_window(3);
			http.open("POST","requires/cost_breakdown_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(tot_rows*1>1)
			{
				 var tableFilters = 
				 {
					 /* col_0: "none",
					  col_12: "none",
					  col_20: "none",
					display_all_text: " ---Show All---",*/
					col_operation: {
					    id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
				   col: [5,7,21,22,25,26,27,28],
				   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				}
				/*col_operation: {
					    id: ["total_order_qnty","total_order_qnty_in_pcs","value_knit_fab_cons","value_woven_fab_cons","value_fabric_cost_per_dzn","value_trims_cost_per_dzn","value_embell_cost_per_dzn","value_cm_cost_per_dzn","value_commission_cost","value_other_cost","value_tot_cost_per_dzn","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
				   col: [5,7,10,12,14,15,16,17,18,19,20,21,22,25,26,27,28],
				   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]*/
				setFilterGrid("table_body",-1,tableFilters);
			}
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../", '',500,900 );
			show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		if(html_filter_print*1>1) $("#table_body tr:first").remove();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		if(html_filter_print*1>1)
		{
			 var tableFilters = 
			 {
				 /* col_0: "none",
				  col_12: "none",
				  col_20: "none",
				display_all_text: " ---Show All---",*/
				col_operation: {
					    id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
				   col: [5,7,21,22,25,26,27,28],
				   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}	
			}
			
			setFilterGrid("table_body",-1,tableFilters);
		}
	}	
	
</script>

</head>
 
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
       
         <fieldset style="width:800px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="700" cellpadding="1" cellspacing="2" align="center">
               <thead>                    
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cost_breakdown_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
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
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
