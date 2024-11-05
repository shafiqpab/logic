<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Working company Wise Dyeing Production And Finishing Fabric Delivery Report.
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	04-01-2022
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
echo load_html_head_contents("Working Company Wise Dyeing Production And Finishing Fabric Delivery Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	{
		col_0: "none",
		col_operation: {
		id: ["total_dyeing_no_of_batch","total_prod_qty","total_subCon_prod_qty","total_tot_prod_qty","total_fin_no_of_batch","total_finish_qty","total_subCon_finish_qty","total_tot_fin_prod_qty","total_finish_trans_qty","total_delivery_qty","total_tot_trans_and_delivery"],
	    col: [3,4,5,6,7,8,9,10,11,12,13],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated()
	{
		
		if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_year*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/working_company_wise_dyeing_and_finish_delivery_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			// append_report_checkbox('table_header_1',1);
			
			setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:560px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:560px;">
                <table class="rpt_table" width="560" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150">Working company</th>
                            <th width="120">Location</th>
                            <th width="60">Year</th>
                            <th width="100" class="must_entry_caption" colspan="2">Date Range</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                            	<? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/working_company_wise_dyeing_and_finish_delivery_report_controller',this.value, 'load_drop_down_location', 'location_td' );" ); ?>
                            </td>
                            <td id="location_td"> 
                            	<? echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- All Location --", $selected, "",0,"" ); ?>	
                            </td>
                            <td>
                            	<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                            </td>
                            <td>
								<input type="text" name="txt_date_from" id="txt_date_from"  value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:50px" placeholder="From Date"/>
							</td>
							<td>
								<input type="text" name="txt_date_to" id="txt_date_to"  value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:50px" placeholder="To Date"/>
							</td>                         
                            <td>
                            	<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated()" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
