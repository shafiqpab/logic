<?
/*-------------------------------------------- Comments

Purpose         :   This form will Create KPI Dashboard Report
Functionality   :  
JS Functions    :
Created by      :   Shafiq
Creation date   :   8-11-2023
Updated by      :       
Update date     :
QC Performed BY :  
QC Date         : 
Comments        :

*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("KPI Dashboard Report", "../../", 1, 1,$unicode,1,1);
//echo load_html_head_contents("Supplier Info", "../../", 1, 1, $unicode,1,'');

?>
<script>
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [8],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 

	function fn_report_generated()
	{   
		if( form_validation('cbo_company_name*txt_date','Company Name*Date')==false )
		{
			return;
		}
	
		var report_title=$( "div.form_caption" ).html(); 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_date',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/kpi_dashboard_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
    function new_window()
    {
        // document.getElementById('scroll_body').style.overflow="auto";
        // document.getElementById('scroll_body').style.maxHeight="none"; 
        // $(".flt").css('display','none');
           
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        // document.getElementById('scroll_body').style.overflowY="auto"; 
        // document.getElementById('scroll_body').style.maxHeight="400px";
        // $(".flt").css('display','block');
    }

</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
         <form name="kpiDashBoard_1" id="kpiDashBoard_1"> 
         <h3 style="width:380px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:380px;">
                 <table class="rpt_table" width="380" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="170">Working Compuny</th>
                            <th  id="search_text_td" class="must_entry_caption">Production Date</th>
                            <th>
								<input type="reset" name="res" id="res" value="Reset" onClick="reset_form('kpiDashBoard_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" />
							</th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>								
                                <td>
                                    <input name="txt_date" id="txt_date" class="datepicker" style="width:100px" placeholder="Production Date" >
                                </td> 
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </form>
    </div>
    <div id="report_container" align="center" style="margin: 5px 0;"></div>
    <div id="report_container2" align="center"></div>
</body>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>