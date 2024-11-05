<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Lc Wise Trims Receive
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	28-04-2018
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
echo load_html_head_contents("LC Wise Trims Receive","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

var tableFilters = {
	col_30: "none",
	col_operation: {
	id: ["tot_pi_qnty","tot_pi_amnt","tot_pi_net_amnt"],
	col: [5,7,8],
	operation: ["sum","sum","sum"],
	write_method: ["innerHTML","innerHTML","innerHTML"]
	}
}
var tableFilters1 = {
	col_30: "none",
	col_operation: {
	id: ["tot_recv_qnty","tot_recv_amnt"],
	col: [6,8],
	operation: ["sum","sum"],
	write_method: ["innerHTML","innerHTML"]
	}
}
var tableFilters2 = {
	col_30: "none",
	col_operation: {
	id: ["tot_return_qnty","tot_return_amnt"],
	col: [5,7],
	operation: ["sum","sum"],
	write_method: ["innerHTML","innerHTML"]
	}
}
var tableFilters3 = {
	col_30: "none",
	col_operation: {
	id: ["total_receive_value","total_return_value","tot_payble_value","tot_accept_value","tot_yet_to_accept"],
	col: [2,3,4,6,7],
	operation: ["sum","sum","sum","sum","sum"],
	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}

function generate_report()
{
	var btbLc_id = $('#btbLc_id').val();
	var txt_pi_no = $('#txt_pi_no').val();
	if(btbLc_id=="" && txt_pi_no=="")
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
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
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_pi_no*btbLc_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/lc_wise_trims_receive_controller.php",true);
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

		setFilterGrid("tbl_btb_lc_details",-1);
		setFilterGrid("tbl_pi_details",-1,tableFilters);
		setFilterGrid("tbl_trims_store_receive",-1,tableFilters1);
		setFilterGrid("tbl_trims_store_receive_return",-1,tableFilters2);
		setFilterGrid("tbl_acceptance_details",-1,tableFilters3);
		show_msg('3');
		release_freezing();

	}
} 
 
function openmypage_btbLc()
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/lc_wise_trims_receive_controller.php?action=btbLc_popup&companyID='+companyID;
	var title='BTB LC NO';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var btbLc_id=this.contentDoc.getElementById("btbLc_id").value;
		var btbLc_no=this.contentDoc.getElementById("btbLc_no").value;
		
		$('#btbLc_id').val(btbLc_id);
		$('#txt_btbLc_no').val(btbLc_no);
	}
}

function openmypage_pinumber()
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/lc_wise_trims_receive_controller.php?action=pinumber_popup&companyID='+companyID;
	var title='PI Number Info';
	
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

function new_window()
{
	document.getElementById('caption').style.visibility='visible';
	$("#tbl_btb_lc_details tr:first").hide();
	$("#tbl_pi_details tr:first").hide();
	$("#tbl_trims_store_receive tr:first").hide();
	$("#tbl_trims_store_receive_return tr:first").hide();
	$("#tbl_acceptance_details tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	
	document.getElementById('caption').style.visibility='hidden';
	$("#tbl_btb_lc_details tr:first").show();
	$("#tbl_pi_details tr:first").show();
	$("#tbl_trims_store_receive tr:first").show();
	$("#tbl_trims_store_receive_return tr:first").show();
	$("#tbl_acceptance_details tr:first").show();
	
}

function openmypage_pinumber_details(pi_ids)
{
	var page_link='requires/lc_wise_trims_receive_controller.php?action=pi_dtls_popup&pi_ids='+pi_ids;
	var title='PI Number Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		// var theform=this.contentDoc.forms[0];
		// var pi_id=this.contentDoc.getElementById("pi_id").value;
		// var pi_no=this.contentDoc.getElementById("pi_no").value;
		
		// $('#pi_id').val(pi_id);
		// $('#txt_pi_no').val(pi_no);
	}
}



</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="piWiseYarnReceive_1" id="piWiseYarnReceive_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:800px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="200" class="must_entry_caption">Company</th>  
							<th width="100">PI Number</th>                              
                            <th width="180">LC No</th>
                            <th width="220"  class="must_entry_caption">LC Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('piWiseYarnReceive_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
						<td>
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write Or Browse" onDblClick="openmypage_pinumber()"  />
                            <input type="hidden" id="pi_id" readonly /> 
                        </td>
                        <td>
                            <input type="text" id="txt_btbLc_no" name="txt_btbLc_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage_btbLc()" readonly />
                            <input type="hidden" id="btbLc_id" readonly /> 
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" readonly/>&nbsp;To&nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="5"> <? echo load_month_buttons(1); ?></td>
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
	//set_multiselect('cbo_store_name','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
