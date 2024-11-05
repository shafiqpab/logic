<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Month Wise Order Booking Report
Functionality	         :	
JS Functions	         :
Created by		         :	Kausar
Creation date 	         :  05-02-2020
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
echo load_html_head_contents("Month Wise Order Booking Report", "../../../", "", $popup, 1);
?>	
<script>
	var permission='<? echo $permission; ?>';
 
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name*cbo_date_category*cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Company Name*Date Category*Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_date_category*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name',"../../../")+'&report_title='+report_title+'&type='+type;
			$("#report_type").val(type);
			freeze_window(3);
			http.open("POST","requires/month_wise_order_booking_report_controller.php",true);
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
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			//append_report_checkbox('table_header_1',1);
			
	 		show_msg('3');
			release_freezing();
		}
	}


	function new_window()
	{
		const el = document.querySelector('#scroll_body');
		const table_body = document.querySelector('#table_body');
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();

		}
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		 if (el) {
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$("#scroll_body tr:first").show();

		}
		
		
		//$(".flt").show();
	}

	function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	$("#report1").hide();
	$("#report2").hide();
	$("#report3").hide();
	$("#report4").hide();
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==266)
			{
				$("#report1").show();
			}
			if(report_id[k]==256)
			{
				$("#report2").show();
			}
			if(report_id[k]==267)
			{
				$("#report3").show();
			}
			if(report_id[k]==264)
			{
				$("#report4").show();
			}
		}
}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:850px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <form id="monthwiseorderbookingstatus_1" name="monthwiseorderbookingstatus_1">
                <div style="width:850px">
                    <fieldset>  
                        <table cellpadding="0" cellspacing="0" width="850" class="rpt_table" border="1" rules="all">

                            <thead>  
                                <th width="150" class="must_entry_caption">Company</th>
                                <th width="120" class="must_entry_caption">Date Category</th>
                                <th width="100" class="must_entry_caption">Start Year</th>
                                <th width="100" class="must_entry_caption">Start Month</th>
                                <th width="100" class="must_entry_caption">End Year</th>
                                <th width="100" class="must_entry_caption">End Month</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </thead>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected,"" );"get_php_form_data( this.value, 'get_company_config', 'requires/buyer_inquery_qote_submit_controller');" ?></td>
                                <td><? 
								$dateCategoryArr=array(1=>"Pub. Ship Date", 2=>"Actual Ship Date");
								echo create_drop_down( "cbo_date_category", 120,$dateCategoryArr,"", 0, "-Select Category-", 1,"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-Select Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                <td><? echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                <td>
                                	<input type="button" name="report1" id="report1" value="Show" onClick="fn_report_generated(1);" style="width:80px" class="formbutton" />
                                	<input type="button" name="report2" id="report2" value="Show2" onClick="fn_report_generated(2);" style="width:80px" class="formbutton" />
                                	<input type="hidden" name="report_type" id="report_type">
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>