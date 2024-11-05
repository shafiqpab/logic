<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create AOP Capacity Plan Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	18-09-2023
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
echo load_html_head_contents("AOP Capacity Plan Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	
function generate_report_main()
 {
	 //var report=return_global_ajax_value($('#cbo_company_name').val()+'*'+$('#cbo_year_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_month').val()+'*'+$('#cbo_location_id').val(), 'capacity_allocation_print', '', 'requires/pre_cost_entry_controller');
	 print_report( $('#cbo_company_name').val()+'*'+$('#cbo_year_name').val()+'*'+$('#cbo_month').val()+'*'+$('#cbo_month_end').val(), "capacity_allocation_print", "requires/aop_capacity_plan_report_controller" ) 
	 return; 
 }
 
 
 	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Company Name*Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name*cboMachineName*cboapacityType',"../../");
			freeze_window(3);
			http.open("POST","requires/aop_capacity_plan_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			
			//setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}
	
</script>
 
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:850px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <form id="monthly_capacity_order_qnty" name="monthly_capacity_order_qnty">
                <div style="width:850px">
                    <fieldset>  
                    <legend>AOP Capacity Plan</legend>
                        <table cellpadding="0" cellspacing="2" width="800" class="rpt_table" border="1" rules="all">
                          <thead>  
                            <tr>
                                <th width="200">Company</th>
                                <th width="100" >Machine No.</th>
                                  <th width="120">Start Year</th>
                                  <th width="120">Start Month</th>
                                  <th width="120">End Year</th>
                                   <th width="120">End Month</th>
                                 <th width="120">Capacity Type</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </tr>
                            <tr>
                                <td align="center">
                                <? 
                                echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/aop_capacity_plan_report_controller',this.value, 'load_drop_machine', 'machinetd');" );
                                ?>
                                </td>
                                <td id="machinetd"><?   
							 		 echo create_drop_down( "cboMachineName",100, $blank_array,'', 1, '-Select-',0,"","","","","","","","","cboMachineName[]");   
							 	?> 
                              </td>
                               <td align="center">
                                <? 
                                echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                                  <td align="center">
                                <?
                                echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                                </td>
                                 <td align="center">
                                <? 
                                echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                                </td>
                                </td>
                                <td align="center">
                                <?
                                echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                                </td>
                                
                                  <td><?  
								  
								  $CapacityType=array('1'=>"All",'2'=>"Over Booked",'3'=>"Under Booked"); 
							 		 echo create_drop_down( "cboapacityType",100, $CapacityType,'', 1, '-Select-',0,"","","","","","","","","cboapacityType[]");   
							 	?> 
                              </td>
                                <td align="center">
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated()" style="width:80px" class="formbutton" />
                                </td>
                            </tr>
                           </thead>
                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>