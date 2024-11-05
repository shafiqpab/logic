<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Order Forecast Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	03-07-2019
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
echo load_html_head_contents("Sales Forecast Vs Booked report","../../../", 1, 1, $unicode,1,1);
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
		if(form_validation('cbo_company_name*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to','Company Name*From Month*From Year*To Month*To Year')==false)
		{
			return;
		}
		else
		{
			
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_section_id*cbo_sub_section_id*cbo_team_member*cbo_team_leader*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to',"../../../");
			
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sales_forecast_vs_booked_report_controller.php",true);
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
	
	
	function change_colors(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			document.getElementById(v_id+'1').bgColor=e_color;
			document.getElementById(v_id+'2').bgColor=e_color;
			document.getElementById(v_id+'3').bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			document.getElementById(v_id+'1').bgColor="#33CC00";
			document.getElementById(v_id+'2').bgColor="#33CC00";
			document.getElementById(v_id+'3').bgColor="#33CC00";
		}
	}
	
	
	
	
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:1050px" align="left">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1000px;">
                <table class="rpt_table" width="990" cellpadding="1" cellspacing="2" align="center" rules="all">
                	<thead>
                    	<tr>                   
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="100">Section</th>
                            <th width="100">Sub Section</th>
                            <th width="150">Team Leader</th>
                            <th width="150">Team Leader</th>
                            <th width="250" colspan="5" class="must_entry_caption">Month Range</th>
                            <th ><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td> 
                            <?
                              echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                         <td>
                            <? 
                                echo create_drop_down( "cbo_section_id", 100, $trims_section,"", 1, "--All--", "", "" );
                            ?>
                        </td>
                         <td>
                            <? 
                               echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"", 1, "--All--", "", "" );
                            ?>
                        </td>
                         <td>
                            <?  
							    echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=3","id,team_leader_name", 1, "-- Select Leader --", $selected, "load_drop_down( 'requires/sales_forecast_vs_booked_report_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');"); 
							     
                            ?>
                        </td>
                        <td id="member_td">
                                <? 
                                     echo create_drop_down( "cbo_team_member", 150,  $blank_array,"", 1, "-- Select Member --", $selected, "load_drop_down( 'requires/sales_forecast_vs_booked_report_controller',this.value+'_'+1, 'load_drop_down_member', 'member_td');");
                                ?>
                           </td>
                        
                        <td> 
                            <?
								$selected_month=date("m");
                                echo create_drop_down( "cbo_month_from", 80, $months,"", 1, "--Month--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year_from", 60, $year,"", 1, "--Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                           <td> 
                            <?
								$selected_month=date("m");
                                echo create_drop_down( "cbo_month_to", 80, $months,"", 1, "--Month--", 0, "",0 );
                            ?>
                        </td>
                         <td>
                             <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year_to", 60, $year,"", 1, "--Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
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
