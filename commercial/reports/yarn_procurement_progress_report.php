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
echo load_html_head_contents("Yarn Procurement Progress Report", "../../", 1, 1,'','','');
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










	var tableFilters =
	{
		col_operation: {
			id: ['value_tot_req_qty_id','value_tot_req_amount_id','value_tot_wo_qty_id','value_tot_wo_amount_id','value_tot_pi_qnty_id','value_tot_pi_amt_id','value_tot_mrr_qnty_id','value_tot_mrr_value_id','value_tot_short_amt_id','value_tot_pipe_line_id'],
			col: [19,21,29,31,41,43,51,52,53,54],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	}

	var tableFiltersYarnAnalysis =
	{
		col_operation: {
			id: ['value_tot_req_qty_id','value_tot_req_amount_id','value_tot_wo_qty_id','value_tot_wo_amount_id','value_tot_pi_qnty_id','value_tot_pi_amt_id','value_tot_mrr_qnty_id','value_tot_rcvd_due','value_tot_issue_qnty','value_tot_Stock_Qty'],
			col: [19,21,29,31,41,43,51,52,53,54],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	}
 
	
	//generate_report_summary
	function generate_report_summary()
	{
		var cbo_based_on=$('#cbo_based_on').val();
		var txt_search_no=$('#txt_search_no').val();
		if(cbo_based_on!=1)
		{
			alert("Summary Button Only For Requisition Based");return;
		}
		if(txt_search_no!="")
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
		var data="action=report_generate_summary"+get_submitted_data_string("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to*cbo_receive_status","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_procurement_progress_report_controller.php",true);
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
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tr:first').hide();
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		//$('#table_body tr:first').show();
	}
	
	function generate_report(type)
	{
		var txt_search_no=$('#txt_search_no').val();
		// var txt_style_ref_no=$('#txt_style_ref_no').val();
		// var txt_job_id=$('#txt_job_id').val();
		if(txt_search_no!="")
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
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to*cbo_receive_status*txt_style_ref_no*txt_job_id","../../")+'&report_title='+report_title+'&type='+type;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_procurement_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);
			//alert(response[2]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+response[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(response[2]==1 || response[2]==3) setFilterGrid("table_body",-1,tableFilters);
			if(response[2]==2 || response[2]==3) setFilterGrid("table_body2",-1);
			if(response[2]==3) setFilterGrid("table_body3",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function generate_report_yarn_analysis(type)
	{
		var txt_search_no=$('#txt_search_no').val();
		if(txt_search_no!="")
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
		var data="action=report_generate_yarn_analysis"+get_submitted_data_string("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to*cbo_receive_status","../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_procurement_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_yarn_analysis_reponse;
	}

	function fn_report_generated_yarn_analysis_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);
			//alert(response[2]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+response[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(response[2]==1 || response[2]==3) setFilterGrid("table_body",-1,tableFiltersYarnAnalysis);
			if(response[2]==2 || response[2]==3) setFilterGrid("table_body2",-1);
			if(response[2]==3) setFilterGrid("table_body3",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(str)
	{
		if(str==1 || str==3)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tr:first').hide();
		}
		if(str==2 || str==3)
		{
			document.getElementById('scroll_body2').style.overflow="auto";
			document.getElementById('scroll_body2').style.maxHeight="none";
			$('#table_body2 tr:first').hide();
		}
		if(str==3)
		{
			document.getElementById('scroll_body3').style.overflow="auto";
			document.getElementById('scroll_body3').style.maxHeight="none";
			$('#table_body3 tr:first').hide();
		}
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		if(str==1 || str==3)
		{
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="300px";
			$('#table_body tr:first').show();
		}
		if(str==2 || str==3)
		{
			document.getElementById('scroll_body2').style.overflowY="scroll";
			document.getElementById('scroll_body2').style.maxHeight="300px";
			$('#table_body2 tr:first').show();
		}
		if(str==3)
		{
			document.getElementById('scroll_body3').style.overflowY="scroll";
			document.getElementById('scroll_body3').style.maxHeight="300px";
			$('#table_body3 tr:first').show();
		}
	}
	
	function fnc_html_change(id)
	{
		if(id==1)
		{
			$('#no_html').html("Requisition No");
			$('#date_html').html("Requisition Date");
		}
		else if(id==2)
		{
			$('#no_html').html("WO No");
			$('#date_html').html("WO Date");
		}
		else
		{
			$('#no_html').html("PI No");
			$('#date_html').html("PI Date");
		}
	}
	function fn_mrr_details(booking_id,book_basis,color_id,yarn_type,count_id,composition,action,piIds)
	{
		//alert(action);
		page_link='requires/yarn_procurement_progress_report_controller.php?action='+action+'&booking_id='+booking_id+'&book_basis='+book_basis+'&color_id='+color_id+'&yarn_type='+yarn_type+'&count_id='+count_id+'&composition='+composition+'&pi_ids='+piIds;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=624px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}

	const Fnc_Yd_HyperLink = function(ReportID,CompanyID,UpdateId,ReportTitle,RefCloseSts,CboPayMode,IsApprovedId,WoBasis,SuppylerId,type){
		var ReportTitle="Yarn Purchase Order";
		if(ReportID==78){
		   print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId,"yarn_work_order_print", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
	    }
		else if(ReportID==84){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId+'*'+WoBasis,"print_to_html_report", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==85){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId,"print_to_html_report2", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==72){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId,"print_to_html_report3", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==193){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId,"print_to_html_report4", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==129){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+RefCloseSts+'*'+CboPayMode+'*'+IsApprovedId,"yarn_work_order_print5", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==191){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+SuppylerId,"print_to_html_report7", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==227){
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle,"yarn_work_order_print8", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
		else if(ReportID==235){
			var r = confirm("Ok to print without Job No \n Cancel to print with Job No");
			if (r == true) {
				type = 1;
			} else {
				type = 2;
			}
			print_report( CompanyID+'*'+UpdateId+'*'+ReportTitle+'*'+type+'*'+SuppylerId+'*'+WoBasis,"print_to_html_report9", "../../commercial/work_order/requires/yarn_work_order_controller")
		   return;
		}
	}

	const Fnc_Pro_Farma_InvoiceV2_Print_hyperLink = function(ReportID,Importer_Id,UpdateId,EntryFrom,CatagoryID){
		if(ReportID==86){
			print_report( Importer_Id+'*'+UpdateId+'*'+EntryFrom+'*'+CatagoryID,"print", "../../commercial/import_details/requires/pi_print_urmi")

		}else{
			alert("1st button will Be ALwayas (Print)")
		}
	}
	function openmypage_job(search_type)
	{
		if( form_validation('cbo_company_name*cbo_year','Company Name*Year')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		
		
		var page_link='requires/yarn_procurement_progress_report_controller.php?action=job_no_popup&companyID='+companyID;
		if(search_type==1)
			var title='Style Ref No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_style_ref_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			if(search_type==1)
			{
				$('#txt_style_ref_no').val(style_no);
				$('#txt_job_id').val(job_id);
			}
		
		}
	}
		
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_procureument_rpt" name="frm_procureument_rpt">
    <div style="width:1180px;">
    <h3 align="left" id="accordion_h1" style="width:1180px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:1180px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="1160" border="1" rules="all">
            <thead>
                <th width="150" class="must_entry_caption">Company</th>
                <th width="90">Year</th>
                <th width="140">Based On</th>
                <th width="120" id="no_html">Requisition No</th>
				<th width="100" id="no_html">Style Ref No</th>
                <th width="200" id="date_html">Requisition Date</th>
                <th width="150">Receiving status</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center">
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
                    <td  align="center">
                    <?
                        $cbo_based_on_arr=array(1=>"Requisition Based",2=>"WO Based",3=>"PI Based");
                        echo create_drop_down( "cbo_based_on", 130, $cbo_based_on_arr,"", 0, "",$selected, "fnc_html_change(this.value);");
                    ?>
                    </td>
					
                    <td align="center"><input type="text" id="txt_search_no" name="txt_req_no" style="width:100px;" class="text_boxes" ></td>
					<td>
						<input type="text" id="txt_style_ref_no" name="txt_style_ref_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(1)"   placeholder="Browse/Write" />
						<input type="hidden" name="txt_job_id" id="txt_job_id"/>
                    </td>
					
                    <td  align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:65px">TO
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"></td>
                    <td>
                    <?
                    $receive_status=array(1=>"Full Pending",2=>"Partial Received",3=>"Fully Received",4=>"Full Pending And Partial Received",5=>"All");
                    echo create_drop_down( "cbo_receive_status", 140, $receive_status,"", 0, "-- Select Status --", 5, "" );
                    ?>
                  	</td>
                    <td align="center">
                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />                    
                    <input type="button" name="search" id="search" value="Summary" onClick="generate_report_summary()" style="width:70px" class="formbutton" />
                    <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    <td align="center"><input type="button" name="search" id="search" value="Yarn Analysis" onClick="generate_report_yarn_analysis(1)" style="width:90px" class="formbutton" /></td>
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
</html>