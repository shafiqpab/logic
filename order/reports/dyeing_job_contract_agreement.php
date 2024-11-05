<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sample Development Status Report.
Functionality	:	
JS Functions	:
Created by		:	Tofazzal Al Hoque
Creation date 	: 	
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
echo load_html_head_contents("Sample Development Status Report", "../../", 1, 1,$unicode,'1','');

?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		
			var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
			if(cbo_supplier_name!=0)
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_supplier_name*txt_date_from*txt_date_to',"../../");
			freeze_window(3);
			http.open("POST","requires/dyeing_job_contract_agreement_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window('+tot_rows+',1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(tot_rows*1>1)
			{
				 var tableFilters = 
				 {
					 /* col_0: "none",
					  col_12: "none",
					  col_20: "none",
					display_all_text: " ---Show All---",*/
					col_operation: {
					//id: ["total_order_qnty","total_order_qnty_in_pcs"],
				    //col: [5,7],
				    //operation: ["sum","sum"],
				   // write_method: ["innerHTML","innerHTML"]
					}	
				}
				
				setFilterGrid("table_body",-1,tableFilters);
			}
			show_msg('3');
			release_freezing();
		}
	}
	 

	function new_window(html_filter_print,type)
	{
		if(type==1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			if(html_filter_print*1>1) $("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="600px";
			if(html_filter_print*1>1) $("#table_body tr:first").show();
		}
		else if(type==2)
		{
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('lapdib_approval_div').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
		}
	}
	
</script>

</head>

<body onLoad="set_hotkey();">
<form id="sample_development_status_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:800px;">
        	<legend>Search Panel</legend>
                <table class="rpt_table" width="800" cellpadding="1" cellspacing="2">
                   <thead>                    
                        <th>Company Name</th>
                        <th>Service Company</th>
                        <th>Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                               echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td>
                            <?
                            	echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=21) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, 
                             "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knitting_controller');",0 );
                            ?> 
                        </td> 
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >&nbsp; To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" >
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="1" cellspacing="2">
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>     
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
