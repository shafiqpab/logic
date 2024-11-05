<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Count Wise Yarn Requirement Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	28-07-2020
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
echo load_html_head_contents("Knitting Bill Report","../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	
	
	function generate_wo_order_report(company_id,knitting_wo_id)
	{
		
		
		print_report( company_id+'**'+knitting_wo_id,"work_order_print", "requires/knitting_bill_report_controller");
	}
	
	

	function generate_report()
	{
		var from=$("#txt_date_from").val();
		var to=$("#txt_date_to").val();
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
		else if(to!=from)
		{
			alert('Please Select Single Date');
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/mail_recipient_group_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			//setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="400px";
		
		//$("#table_body tr:first").show();
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:1200;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="mail_recipient_group_report" id="mail_recipient_group_report" autocomplete="off" > 
    <h3 style="width:1070px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1070px;">
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="200" class="must_entry_caption">Company</th>                                
                            <th width="300" class="must_entry_caption">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('mail_recipient_group_report','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report();" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
