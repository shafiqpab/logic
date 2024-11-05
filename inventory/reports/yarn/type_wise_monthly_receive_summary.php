<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Type Wise Monthly Receive Summary Report
Functionality	         :	
JS Functions	         :
Created by		         :	Md.mahbubur Rahman
Creation date 	         :  01-08-2018
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 	   
QC Performed BY	         :		
QC Date			         :	
Comments		         : 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Type Wise Monthly Receive Summary","../../../", 1, 1, $unicode,1,1); 
?>	


<script>
var permission='<? echo $permission; ?>';


 
 	function fn_report_generated(rptType)
	{
		
		//alert(rptType); 
		if(form_validation('cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name*cbo_dyed_type*cbo_yarn_type',"../../../")+'&rptType='+rptType;
			freeze_window(3);
			http.open("POST","requires/type_wise_monthly_receive_summary_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("##");
			var tot_rows=reponse[2];
			
			$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			//append_report_checkbox('table_header_1',1);
			
			//setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}
	
function new_window()
{
	//document.getElementById('caption').style.visibility='visible';
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	//document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}
</script>





</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:1250px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <form id="monthly_capacity_order_qnty" name="monthly_capacity_order_qnty">
                <div style="width:1250px">
                    <fieldset>  
                    <legend id="caption">Type Wise Monthly Receive Summary</legend>
                        <table cellpadding="0" cellspacing="2" width="1200" class="rpt_table" border="1" rules="all">
                          <thead>  
                            <tr>
                                <th width="200">Company</th>
                                <th>Dyed Type</th>
                            	<th>Yarn Type</th>
                                <th width="120">Start Year</th>
                                <th width="120">Start Month</th>
                                <th width="120">End Year</th>
                                <th width="120">End Month</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:280px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </tr>
                            <tr>
                                <td align="center">
                                <? 
                                echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"" );
                                ?>
                                </td>
                                <td align="center">
                                <?   
                                	$dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                                	echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", 2, "", "","");
                                ?>              
                                </td>
                                <td> 
                                <?
                               	 //echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "--Select--", 0, "",0 );
                               	 echo create_drop_down("cbo_yarn_type",130,$yarn_type,"",0, "-- Select --", $selected, "");
                                ?>
                                </td>
                                <td align="center">
                                <? 
                                echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                                </td>
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
                                <td align="center">
                                <?
                                echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                                </td>
                                <td align="center">
                                <input type="button" name="search" id="search" value="Receive" onClick="fn_report_generated(1)" style="width:80px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Issue" onClick="fn_report_generated(2)" style="width:80px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Receive and Issue" onClick="fn_report_generated(3)" style="width:120px" class="formbutton" />
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
<script>
	set_multiselect('cbo_yarn_type','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>