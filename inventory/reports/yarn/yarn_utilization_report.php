<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create BTB Lc Balance Statement
				
Functionality	:	
JS Functions	:
Created by		:	SAIDUL REZA
Creation date 	: 	22-01-2017
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
echo load_html_head_contents("Yarn Utilization Report","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


var tableFilters = { 
	col_35: "none", 
	col_operation: {
		id: ["total_rcv_qnty","value_total_rcv_amt","total_total_req_qnty","value_total_req_amt","total_total_issue_qnty","value_total_issue_amt","total_total_transfer_qnty","value_total_transfer_value","total_total_balance_qnty","value_total_balance_amt"],
		col: [11,12,17,18,21,22,27,28,29,30],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}


function generate_report()
{
	var btbLcNO = $('#txt_btbLc_no').val();
	var supplierID = $('#cbo_suppler_name').val();
	
	var txt_date_from = $('#txt_date_from').val();
	var txt_date_to = $('#txt_date_to').val();
	
	if(btbLcNO=='' && txt_date_from=='' && txt_date_to=='')
	{
		if( form_validation('txt_date_from*txt_date_to','Date Form*Date To')==false )
		{
			return;
		}
	}
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_suppler_name*btbLc_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_utilization_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
	}
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




function openmypage_btbLc()
{
	var companyID = $('#cbo_company_name').val();
	var supplierID = $('#cbo_suppler_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/yarn_utilization_report_controller.php?action=btbLc_popup&companyID='+companyID+'&supplierID='+supplierID;
	var title='Yarn Requisition Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var btbLc_id=this.contentDoc.getElementById("btbLc_id").value;
		var btbLc_no=this.contentDoc.getElementById("btbLc_no").value;
		
		$('#btbLc_id').val(btbLc_id);
		$('#txt_btbLc_no').val(btbLc_no);
		date_range_herder_color(btbLc_id);
	}
}



function new_window()
{
	document.getElementById('caption').style.visibility='visible';
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	$('#table_body').find('tr:first').hide();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	$('#table_body').find('tr:first').show();
	document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}

function date_range_herder_color(str)
{
	if(str>0) $('#date_reange_cullon').css("color", "black");
	else $('#date_reange_cullon').css("color", "blue");
}



/*function openmypage_popup(company_id,pi_id,page_title,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_utilization_report_controller.php?company_id='+company_id+'&pi_id='+pi_id+'&action='+action, page_title, 'width=720px,height=400px,center=1,resize=0,scrolling=0','../../');
}*/

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="btbLcBalanceStatement_1" id="btbLcBalanceStatement_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:750px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150">Company</th>                                
                            <th width="150">Supplier</th>
                            <th width="120">BTB LC No.</th>
                            <th width="230" style="color:#03F;" id="date_reange_cullon">BTB Last Shipment Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('btbLcBalanceStatement_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "reset_form('','','txt_btbLc_no*btbLc_id','','',''); load_drop_down( 'requires/yarn_utilization_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                            ?>                            
                        </td>
                        <td id="supplier_td"> 
                            <?
                                echo create_drop_down( "cbo_suppler_name", 150, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_btbLc_no" name="txt_btbLc_no" class="text_boxes" style="width:120px" placeholder="Double Click To Search" onDblClick="openmypage_btbLc()" readonly />
                            <input type="hidden" id="btbLc_id" readonly /> 
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:90px" placeholder="From Date" readonly>
                    		<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px"placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:90px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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
	//set_multiselect('cbo_suppler_name','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
