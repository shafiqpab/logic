<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Short Booking Analysis Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	18-03-2019
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Short Booking Analysis Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
/*		else 
		{	
*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val()+"_"+$("#txt_order_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_booking_analysis_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		//}
	}
	
	function openmypage_po()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
/*		else
		{	
*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_job_no").val()+"_"+$("#cbo_year").val()+"_"+$("#txt_order_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_booking_analysis_report_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid.value);
					$("#txt_po_no").val(theemailval.value);
					release_freezing();
				}
			}
		//}
	}
	
	function fn_report_generated(operation)
	{
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		
		if(txt_date_from=="" && txt_date_to=="" && cbo_company_id==0){
			var divData="cbo_company_id*txt_date_from*txt_date_to";	
			var msgData="Company Name*From Date*To Date";	
		}
		else if(txt_date_from=="" && txt_date_to=="" && cbo_company_id!=0){
			var divData="txt_date_from*txt_date_to";	
			var msgData="From Date*To Date";	
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";	
		}
		
		if(cbo_buyer_id==0 && cbo_company==0)
		{
			if(form_validation(divData,msgData)==false){
				return;
			}
		}		
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			if(operation==0){
				var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_styleref*txt_internalref*txt_main_booking*txt_short_booking*cbo_cause_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			}
			else{
				var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_styleref*txt_internalref*txt_main_booking*txt_short_booking*cbo_cause_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			}
			freeze_window(3);
			http.open("POST","requires/short_booking_analysis_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="none";
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body1').style.overflowY="scroll";
		document.getElementById('scroll_body1').style.maxHeight="200px";
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1170px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1170px" > 		 
            <fieldset style="width:1170px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="120">Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="80">Job No.</th>
                    <th width="100">Style Ref.</th>
					<th width="100">IR/IB</th>
                    <th width="90">Main Booking</th>
                    <th width="90">Short Booking</th>
                    <th width="90">Causes Type</th>
                    <th width="130" colspan="2">Short Booking Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:150px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/short_booking_analysis_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:68px" placeholder="Write" /></td>
                        <td><input type="text" name="txt_styleref" id="txt_styleref" class="text_boxes" style="width:88px" placeholder="Write" /></td>
						<td><input type="text" name="txt_internalref" id="txt_internalref" class="text_boxes" style="width:88px" placeholder="Write" /></td>
                        <td><input type="text" name="txt_main_booking" id="txt_main_booking" class="text_boxes" style="width:78px" placeholder="Write" /></td>
                        <td><input type="text" name="txt_short_booking" id="txt_short_booking" class="text_boxes" style="width:78px" placeholder="Write" /></td>
                        <td><?=create_drop_down( "cbo_cause_type", 90, $short_booking_cause_arr,"", 1, "-- Select --", 0, "",0,"","" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /><input type="button" id="show_button2" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(2)" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="11"><?=load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
