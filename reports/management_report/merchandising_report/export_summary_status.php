<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Export Summary Status
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	27-12-2021
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
echo load_html_head_contents("Export Summary Status","../../../", 1, 1, $unicode,'','');
?>	
<style>
	@media print {
		body * {
			visibility: hidden;
		}
		#report_container2 * {
			visibility: visible;
		}
		#report_container2 {
			position: absolute;
			left: 0;
			top: 0;
		}
	}
</style>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date From*Date To')==false)
		{
			return;
		}				
		else
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/export_summary_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="window.print();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}
	
	function new_window()
	{		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}

	 
</script>
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<form id="export_summary_status" name="export_summary_status">
		    <div style="width:780px;" align="center">
		        <? echo load_freeze_divs ("../../../"); ?>
		        <h3 align="left" id="accordion_h1" style="width:780" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		        <div id="content_search_panel"> 
		            <fieldset style="width:770px;">
		                <table class="rpt_table" width="700" cellpadding="1" cellspacing="2" align="center">
		                	<thead>
		                    	<tr>                   
		                            <th width="150" class="must_entry_caption">Company</th>
		                            <th width="150">Buyer</th>
		                            <th width="200" class="must_entry_caption">Ex-Factory Date</th>
		                            <th ><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
		                        </tr>
		                    </thead>
		                    <tbody>
			                    <tr class="general">
			                        <td> 
			                            <?
			                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/export_summary_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
			                            ?>
			                        </td>
			                        <td id="buyer_td">
										<? 
				                            echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select --", $selected, "" );
				                        ?>	  
				                    </td>
			                        <td>
			                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
			                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
			                        </td>
			                        <td>
			                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1);" />
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
		    </div>
	    </form>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2"></div>
 	</div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
