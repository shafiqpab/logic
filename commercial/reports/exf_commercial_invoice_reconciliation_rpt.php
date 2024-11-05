<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create ExFactory vs. Commercial Invoice Reconciliation Report
				
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed 
Creation date 	: 	06/09/2021
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

	var tableFilters = 
	{
		//col_30: "none",
		col_operation: {
		id: ["tot_po_qnty","tot_ex_factory_qnty","tot_ex_factory_value","tot_current_invoice_qnty","tot_current_invoice_value","tot_qty_var","tot_amt_var"],
		col: [4,6,7,8,9,10,11],
		operation: ["sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}


	function generate_report(operation)
	{
		var txt_int_ref_no=trim($( "#txt_int_ref_no" ).val());
		var txt_job_no=trim($( "#txt_job_no" ).val());
		if(txt_int_ref_no!="" || txt_job_no!="")
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

		if(operation==1){
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*txt_job_no*txt_int_ref_no',"../../")+'&report_title='+report_title;
		}else{
			var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report2"+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*txt_job_no*txt_int_ref_no',"../../")+'&report_title='+report_title;

		}
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/exf_commercial_invoice_reconciliation_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
		
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
			setFilterGrid("table_body",-1,tableFilters);
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

	function openmypage(job_no,po_number,ir_no,action)
	{ 
		var companyID = $("#cbo_company_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var popup_width='520px';
		var data_ref='requires/exf_commercial_invoice_reconciliation_rpt_controller.php?companyID='+companyID+'&job_no='+job_no+'&po_number='+po_number+'&ir_no='+ir_no+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&action='+action;
		//alert(data_ref);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', data_ref, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../');
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:850px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:850px;" align="center" id="content_search_panel">
        <fieldset style="width:850px;">
        <legend>Search Panel</legend> 
            <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="200" class="must_entry_caption">Company</th>                                
                        <th width="180">IR No</th>
                        <th width="150">Job No</th>
                        <th width="220" colspan="2" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/po_wise_contact_attach_rpt_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td>
                    	<input type="text" id="txt_int_ref_no" name="txt_int_ref_no" class="text_boxes" style="width:180px;" placeholder="Write" >
                    </td>
                    <td>
                   		 <input type="text" name="txt_job_no" id="txt_job_no" style="width:150px" class="text_boxes"/>
                    </td>
                   
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>         
                    </td>
                    <td>
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>         
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:90px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:90px" class="formbutton" />
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
