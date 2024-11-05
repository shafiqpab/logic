<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PO Wise Contract Attach Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	06/04/2019
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
echo load_html_head_contents("PO Wise Contract Attach Report","../../", 1, 1, $unicode,'',''); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(operation)
	{
		var order_no=trim($( "#txt_order_no" ).val());
		if(order_no!="")
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*txt_order_no*txt_date_from*txt_date_to*txt_job_no*txt_style_no*txt_int_ref_no',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/po_wise_contact_attach_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} 
		else
		{
			
		}
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert(http.responseText);	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tr:first').hide(); 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
			$('#table_body tr:first').show();
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1050px;" align="center" id="content_search_panel">
        <fieldset style="width:1050px;">
        <legend>Search Panel</legend> 
            <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="200" class="must_entry_caption">Company</th>                                
                        <th width="180">Order</th>
                        <th width="150">Job</th>
                        <th width="150">Style</th>
                        <th width="100">Int. Ref. No</th>
                        <th width="220" colspan="2" class="must_entry_caption">Shipment Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/po_wise_contact_attach_rpt_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td>
                        <input style="width:160px;"  name="txt_order_no" id="txt_order_no"  ondblclick="openmypage_style()" class="text_boxes" placeholder="Write"/>   
                    </td>
                    <td>
                   		 <input type="text" name="txt_job_no" id="txt_job_no" style="width:140px" class="text_boxes"/>
                    </td>
                    <td>
                   		 <input type="text" name="txt_style_no" id="txt_style_no" style="width:140px" class="text_boxes"/>
                    </td>
                    <td>
                    	<input type="text" id="txt_int_ref_no" name="txt_int_ref_no" class="text_boxes" style="width:90px;" placeholder="Write" >
                    </td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>         
                    </td>
                    <td>
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>         
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:90px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    <!-- Result Contain Start-------------------------------------------------------------------->
    <fieldset style="width:1000px;">
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </fieldset>
    <!-- Result Contain END-------------------------------------------------------------------->
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
