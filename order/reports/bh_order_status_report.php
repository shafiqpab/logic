<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create  BH Order Status Report
Functionality	:	
JS Functions	:
Created by		:	SHARIAR AHMED
Creation date 	: 	18/12/2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 
-----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
echo load_html_head_contents("BH Order Status Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';


	
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name*cbo_working_factory*txt_date_from*txt_date_to','Company Name*Working Factory*From Date*To Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_factory*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*cbo_order_status*cbo_category_by',"../../")+'&report_title='+report_title;
		}
		else
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_working_factory*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*cbo_order_status*cbo_category_by',"../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/bh_order_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[0]);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
	}
	
	
	function fnc_get_company_config(company_id)
	{
		$('#cbo_company_name').val( company_id );

		get_php_form_data(company_id,'get_company_config','requires/bh_order_status_report_controller' );
		set_multiselect('cbo_buyer_name','0','0','0','0'); 
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <form name="capacityOrderBooking_1" id="capacityOrderBooking_1" autocomplete="off" > 
        <h3 style="width:1115px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1110px" >      
			<fieldset>
                <table align="center" class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Company</th>
							<th class="must_entry_caption">Working Factory</th>
                            <th>Buyer</th>
                            <th>Job No</th>
                            <th>Style Ref</th>
                            <th>Order Status</th>
							<th>Date Category</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:110px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_get_company_config(this.value);" );
                            ?> 
                        </td>
						<td>
							<?=create_drop_down( "cbo_working_factory", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Working Company", $selected, ""); ?>  
						</td>  
						<td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 0, "-- Select Buyer --", $selected, "" );
                            ?>	
                        </td>
						<td align="center">
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" />
                        </td>
						<td align="center">
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" />
                        </td>
						<td>
							<? 
								echo create_drop_down( "cbo_order_status", 100, $order_status,"", 1, "ALL", "", "" );
							?>	
						</td>
                        <td>
                        <? 
								$report_po_date = array(1 => "Publish Ship Date", 2 => "Original Ship Date");
                                echo create_drop_down( "cbo_category_by", 80, $report_po_date,"", 0, "--Select--", $selected,"" );
                            ?>	
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To Date"></td>
                        <td>
							<input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1)" style="width:90px" class="formbutton" />
							<input type="button" name="search2" id="search2" value="Show 2" onClick="fn_report_generated(2)" style="width:90px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
						
                    </tr>
                </table>

            </fieldset>
        </div>
        <div id="report_container" align="center" style="padding:5px 0;"></div>
        <div id="report_container2"></div>
        </form>
    </div>
</body>
<script type="text/javascript">
	set_multiselect('cbo_buyer_name','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

