<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Yarn Procurement Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-05-2015
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
echo load_html_head_contents("Yarn Procurement Progress Report V2", "../../", 1, 1, $unicode,1,'');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission = '<? echo $permission; ?>';
	
	/*var tableFilters = 
	 {
		col_55: "none",
		col_operation: {
		id: ["value_total_grs_value","value_total_discount_value","value_total_bonous_value","value_total_claim_value","value_total_commission_value","value_total_net_invo_value","total_invoice_qty","total_carton_qty"],
	   col: [11,12,13,14,15,16,20,21],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } */
 
	
	//generate_report_summary
	function generate_report_summary()
	{
		var txt_req_no=$('#txt_req_no').val();
		if(txt_req_no!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate_summary"+get_submitted_data_string("cbo_company_name*cbo_year*cbo_buyer_name*txt_req_no*txt_req_id*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_procurement_progress_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generated_summary_reponse;
	}
	
	function report_generated_summary_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);
			//alert(response[2]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_summeary()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
	function new_window_summeary()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tr:first').hide();
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="300px";
		//$('#table_body tr:first').show();
	}
	
	function openmypage_req()
	{
		/*if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
		  return;
		}*/
		var company = $("#cbo_company_name").val(); 
		var cbo_year = $("#cbo_year").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var txt_req_no = $("#txt_req_no").val();
		var txt_req_id = $("#txt_req_id").val();
		var txt_req_sl_no = $("#txt_req_sl_no").val();
		var page_link='requires/yarn_procurement_progress_report_v2_controller.php?action=req_such_popup&company='+company+'&cbo_year='+cbo_year+'&cbo_buyer_name='+cbo_buyer_name+'&txt_req_no='+txt_req_no+'&txt_req_id='+txt_req_id+'&txt_req_sl_no='+txt_req_sl_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=430px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var req_id=this.contentDoc.getElementById("txt_selected_id").value; 
			var req_no=this.contentDoc.getElementById("txt_selected").value; 
			var sl_no=this.contentDoc.getElementById("txt_selected_no").value; 
			$("#txt_req_no").val(req_no);
			$("#txt_req_id").val(req_id);
			$("#txt_req_sl_no").val(sl_no);
		}
	}
	
	function getCompanyId() 
	{  
		var company_id = document.getElementById('cbo_company_name').value;
		if(company_id !='') {
		load_drop_down( 'requires/yarn_procurement_progress_report_v2_controller', company_id, 'load_drop_down_buyer', 'buyer_td' );
		}
	}
		
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_procureument_rpt" name="frm_procureument_rpt">
    <div style="width:920px;">
    <h3 align="left" id="accordion_h1" style="width:920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:920px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="900" border="1" rules="all">
            <thead>
                <th width="150" class="must_entry_caption">Company</th>
                <th width="100">Year</th>
                <th width="150">Buyer</th>
                <th width="150" id="no_html">Requisition No</th>
                <th width="200" id="date_html">Requisition Date</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center" id="td_company">
                    <?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                    ?>
                    </td>
                    <td  align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_year", 80, $year,"", 1, "All",$year_current);
                    ?>
                    </td>
                    <td  align="center" id="buyer_td">
                    <?
                        echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --",$selected, "");
                    ?>
                    </td>
                    <td align="center">
                    <input type="text" id="txt_req_no" name="txt_req_no" style="width:120px;" class="text_boxes" onDblClick="openmypage_req()" placeholder="browse" readonly >
                    <input type="hidden" name="txt_req_id" id="txt_req_id"/>    
                    <input type="hidden" name="txt_req_sl_no" id="txt_req_sl_no"/>
                    </td>
                    <td  align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:65px">TO
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                    </td>
                    <td align="center">
                    <input type="button" name="search" id="search" value="Show" onClick="generate_report_summary()" style="width:100px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
set_multiselect('cbo_company_name','0','0','','0');
setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];  
</script> 
</html>