<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	28-11-2013
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
echo load_html_head_contents("Production Incentive Payment Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		}
		
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
		var datediff = date_diff( 'd', from_date, to_date )+1;
		//alert (datediff);
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_line*txt_date_from*txt_date_to*txt_emp_code',"../../")+'&report_title='+report_title+'&datediff='+datediff;
		freeze_window(3);
		http.open("POST","requires/prod_incentive_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	

	function fn_add_date_field()
	{
		$("#txt_date_to").val(add_days($('#txt_date_from').val(),'6'));
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
        <form name="incentivepayment_1" id="incentivepayment_1" autocomplete="off" > 
         <h3 style="width:770px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:770px" align="center" >      
            <fieldset>  
                <table class="rpt_table" width="765" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="130" class="must_entry_caption">Location</th>
                        <th width="100" >Line</th>
                        <th class="must_entry_caption">Date Range</th>
                        <th width="100" >Employee Code</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('incentivepayment_1','report_container','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 130, "select company_id,company_name from  lib_employee comp where status_active=1 and is_deleted=0  and company_name!='' $company_cond group by company_name order by company_name  ","company_id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/prod_incentive_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/prod_incentive_report_controller', this.value, 'load_drop_down_line', 'line_td' );" );
                                ?>                            
                            </td>
                            <td id="location_td">
                                <? 
                                    echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "--Select Location--", "", "" );
                                ?>                            
                            </td>
                            <td id="line_td">
                                <? 
                                    echo create_drop_down( "cbo_line", 100, $blank_array,"", 1, "--Select Line--", "", "" );
                                ?>                            
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px;" placeholder="From Date" onChange="fn_add_date_field();" readonly/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:75px;" readonly />
                            </td>
                            <td>
                                <input type="text" name="txt_emp_code" id="txt_emp_code"  class="text_boxes" style="width:100px;"/>                    							
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
            <br /> 
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
        </form>    
    </div>
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
