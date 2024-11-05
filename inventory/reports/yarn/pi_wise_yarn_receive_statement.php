<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PI Balance Statement
				
Functionality	:	
JS Functions	:
Created by		:	Didar
Creation date 	: 	20-04-2022
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
echo load_html_head_contents("PI Wise Yarn Received Statement","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

var tableFilters = { 
	col_20: "none", 
	col_operation: {
		id: ["value_total_pi_qnty","value_total_rcv_qnty","value_total_rtn_qnty","value_total_actual_rcv_qnty","value_total_yarn_balance"],
		col: [11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}

function openmypage_pi()
{
	var companyID = $('#cbo_company_name').val();
	var supplierID = $('#cbo_suppler_name').val();
	
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/pi_wise_yarn_receive_statement_controller.php?action=pi_searce_popup&companyID='+companyID+'&supplierID='+supplierID;
	var title='Yarn Requisition Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var pi_id=this.contentDoc.getElementById("pi_id").value;
		var pi_no=this.contentDoc.getElementById("pi_no").value;
		
		$('#pi_id').val(pi_id);
		$('#txt_pi_no').val(pi_no);
	}
}

function openmypage_pinumber()
{
	var companyID = $('#cbo_company_name').val();
	var supplierID = $('#cbo_suppler_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link="requires/pi_wise_yarn_receive_statement_controller.php?action=pinumber_popup&companyID='"+companyID+"'&supplierID='"+supplierID+"'";
	var title='PI Number Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var selected_name=this.contentDoc.getElementById("selected_name").value;
		var selected_id=this.contentDoc.getElementById("selected_id").value;
		
		$("#txt_pi_no").val(selected_name);
		$("#pi_id").val(selected_id);
	}
}

function generate_report()
{
	var supplierID = $('#cbo_suppler_name').val();
	var pi_id = $('#pi_id').val();

	if(supplierID==0 &&  pi_id=="")
	{
		var txt_date_from = $('#txt_date_from').val();
		var txt_date_to = $('#txt_date_to').val();

		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_suppler_name*pi_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
	//alert(data);return;
	freeze_window(3);
	http.open("POST","requires/pi_wise_yarn_receive_statement_controller.php",true);
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
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';			
		setFilterGrid("table_body",-1,tableFilters);
		show_msg('3');
		release_freezing();
	}
} 
 

function new_window()
{
	document.getElementById('caption').style.visibility='visible';
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}


function openmypage_mrr(company_id,pi_id,count_id,composition_id,type_id,color_id,received_ids,page_title,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_wise_yarn_receive_statement_controller.php?company_id='+company_id+'&pi_id='+pi_id+'&count_id='+count_id+'&composition_id='+composition_id+'&type_id='+type_id+'&color_id='+color_id+'&received_ids='+received_ids+'&action='+action, page_title, 'width=980px,height=400px,center=1,resize=0,scrolling=0','../../');
}

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
	    <form name="btbLcBalanceStatement_1" id="btbLcBalanceStatement_1" autocomplete="off" > 
	        <div style="width:100%;" align="center">
	            <fieldset style="width:800px;">
	            <legend>Search Panel</legend> 
	                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
	                    <thead>
	                        <tr> 	 	
	                            <th width="150" class="must_entry_caption">Company</th>                                
	                            <th width="150">Supplier</th>
	                            <th width="120">PI No.</th>
	                            <th width="160" class="must_entry_caption">PI Date Range</th>
	                            <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('btbLcBalanceStatement_1','report_container*report_container2','','','','');" /></th>
	                        </tr>
	                    </thead>
	                    <tr align="center">
	                        <td>
	                            <? 
	                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pi_wise_yarn_receive_statement_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
	                            ?>                            
	                        </td>
	                        <td id="supplier_td"> 
	                            <?
	                                echo create_drop_down( "cbo_suppler_name", 150, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
	                            ?>
	                        </td>
	                        <td>
	                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:120px" placeholder="Double Click To Search" onDblClick="openmypage_pinumber()" readonly />
	                            <input type="hidden" id="pi_id" name="pi_id" readonly /> 
	                        </td>

	                        <td>
	                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" readonly>
	                    		<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" readonly>
	                        </td>

	                        <td>
	                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:90px" class="formbutton" />
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                        </td>
	                    </tr>
	                </table> 
	            </fieldset>  
	            
	            <div id="report_container" align="center"></div>
	        	<div id="report_container2"></div>
	            
	        </div>
	    </form>    
	</div>    
</body>  
<script>
	set_multiselect('cbo_company_name','0','0','0','0');	
	$("#multi_select_cbo_company_name a").click(function(){load_supplier();});

	function load_supplier()
	{  
		var company=$("#cbo_company_name").val(); 		 
		load_drop_down( 'requires/pi_wise_yarn_receive_statement_controller', company, 'load_drop_down_supplier', 'supplier_td' );
		set_multiselect('cbo_suppler_name','0','0','0','0');
	}

	set_multiselect('cbo_suppler_name','0','0','0','0');

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
