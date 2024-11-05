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
	function print_report_button_setting(report_ids) {
		$("#show_button").hide();
		$("#show_button1").hide();
		var report_id = report_ids.split(",");
		var report_length=report_id.length;
		if(report_length>0){
			for (var k = 0; k < report_id.length; k++) {
				if (report_id[k] == 147) {
					$("#show_button").show();
				}
				else if (report_id[k] == 195) {
					$("#show_button1").show();
				}
			}
		}		 
		else{
			$("#show_button").hide();
			$("#show_button1").hide();
		}		
	}

	function fn_report_generated(action)
	{
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var budget_type=document.getElementById('cbo_budgettype').value;
		var job_id=document.getElementById('hidd_job_id').value;

		
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
		
		if(cbo_buyer_id==0 && cbo_company==0 && action=='report_generate')
		{
			if(form_validation(divData,msgData)==false){
				return;
			}
		}
		else if(action=='report_generate2')
		{	
			if(form_validation("cbo_company_id*txt_internal_file","Company Name*File NO.")==false){
					return;
			}
			else{
				var report_title=$( "div.form_caption" ).html();
				var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*hidd_job_id*cbo_appstatus*cbo_marginupto*txt_marginvalue*cbo_datetype*txt_date_from*txt_date_to*txt_internal_file',"../../")+'&report_title='+report_title+'&garments_nature='+garments_nature+'&budget_type='+budget_type;
				freeze_window(3);
				http.open("POST","requires/bom_list_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}		
		}	
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*hidd_job_id*cbo_appstatus*cbo_marginupto*txt_marginvalue*cbo_datetype*txt_date_from*txt_date_to*txt_internal_file',"../../")+'&report_title='+report_title+'&garments_nature='+garments_nature+'&budget_type='+budget_type;
			freeze_window(3);
			http.open("POST","requires/bom_list_report_controller.php",true);
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
			var totRow=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+totRow+');" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setc()
	 		show_msg('3');
			if(totRow>1)
			{
				var tableFilters = {
					col_operation: {
					  id: ["value_tdpo","value_tdbom","value_tdmargin"],
					  col: [6,7,9],
					  
					   operation: ["sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML"]
					}	
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			
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
	
	function new_window(totRow)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		if(totRow*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		if(totRow*1>1) $("#table_body tr:first").show();
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/bom_list_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				//var response=theemailid.value.split('_');
				if ( theemailid!="" )
				{
					$('#txt_internal_file').prop("disabled", true);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid);
					$("#txt_job_no").val(theemailjob);
					release_freezing();
				}
			}
		}
	}
	function openmypage_internalfile(){
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/bom_list_report_controller.php?data='+data+'&action=internal_no_popup', 'Internal Ref.', 'width=700px,height=220px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("hide_buyer_id").value;
				var theemailfileno=this.contentDoc.getElementById("hide_file_no").value;
				if ( theemailid!="" )
				{
					freeze_window(5);
					$('#txt_job_no').prop("disabled", true);
					$('#cbo_buyer_id').prop("disabled", true);
					$("#cbo_buyer_id").val(theemailid);
					console.log(theemailfileno);
					$("#txt_internal_file").val(theemailfileno);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

		}
	}
	
	function generate_report_v3(company_name, job_no, style_ref_no, buyer_name, costing_date, po_ids, type) {
		freeze_window(3);
		if (type == "summary" || type == "budget3_details" || type == "budget_4") {
			if (type == 'summary') {
				var rpt_type = 3;
				var comments_head = 0;
			} else if (type == 'budget3_details') {
				var rpt_type = 4;
				var comments_head = 1;
			} else if (type == 'budget_4') {
				var rpt_type = 7;
				comments_head = 1;
			}

			var report_title = "Budget/Cost Sheet";
			//	var comments_head=0;
			var cbo_company_name = company_name;
			var cbo_buyer_name = buyer_name;
			var txt_style_ref = style_ref_no;
			var txt_style_ref_id = "";
			var txt_quotation_id = "";
			var sign = 0;
			var txt_order = "";
			var txt_order_id = "";
			var txt_season_id = "";
			var txt_season = "";
			var txt_file_no = "";
			var data = "action=report_generate&reporttype=" + rpt_type +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&cbo_buyer_name=' + "'" + cbo_buyer_name + "'" +
				'&txt_style_ref=' + "'" + txt_style_ref + "'" +
				'&txt_style_ref_id=' + "'" + txt_style_ref_id + "'" +
				'&txt_order=' + "'" + txt_order + "'" +
				'&txt_order_id=' + "'" + txt_order_id + "'" +
				'&txt_season=' + "'" + txt_season + "'" +

				'&txt_season_id=' + "'" + txt_season_id + "'" +
				'&txt_file_no=' + "'" + txt_file_no + "'" +
				'&txt_quotation_id=' + "'" + txt_quotation_id + "'" +
				'&txt_hidden_quot_id=' + "'" + txt_quotation_id + "'" +
				'&comments_head=' + "'" + comments_head + "'" +
				'&sign=' + "'" + sign + "'" +
				'&report_title=' + "'" + report_title + "'" +
				'&path=../../../';

			http.open("POST", "../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php", true);

			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = function() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "_blank");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>'); //<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
					d.close();
					release_freezing();
				}
			}
		} 
		else {
			var rate_amt = 2;
			var zero_val = '';
			if (type != 'mo_sheet' && type != 'budgetsheet' && type != 'materialSheet' && type != 'materialSheet2' && type != 'mo_sheet_3') {
				var r = confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			}

			if (type == 'materialSheet' || type == 'materialSheet2') {
				var r = confirm("Press \"OK\" to show Qty  Excluding Allowance.\nPress \"Cancel\" to show Qty Including Allowance.");
			}

			var excess_per_val = "";

			if (type == 'mo_sheet') {
				excess_per_val = prompt("Please enter your Excess %", "0");
				if (excess_per_val == null) excess_per_val = 0;
				else excess_per_val = excess_per_val;
			}

			if (type == 'budgetsheet') {
				var r = confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
			}

			if (type == 'mo_sheet_3') {
				excess_per_val = prompt("Please enter your Excess %", "0");
				if (excess_per_val == null) excess_per_val = 0;
				else excess_per_val = excess_per_val;
			}

			if (r == true) zero_val = "1";
			else zero_val = "0";
			var print_option_id = "";
			var data = "action=" + type + "&zero_value=" + zero_val + "&rate_amt=" + rate_amt + "&excess_per_val=" + excess_per_val + "&txt_job_no='" + job_no + "'&cbo_company_name=" + company_name + "&cbo_buyer_name=" + buyer_name + "&txt_style_ref='" + style_ref_no + "'&cbo_costing_per=" + costing_date + "&print_option_id=" + print_option_id + "&txt_po_breack_down_id=" + po_ids;
			if(type == 'budgetsheet3'){
				freeze_window(3);
				http.open("POST", "../woven_order/requires/pre_cost_entry_report_controller_v2.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_generate_report_v3_reponse;
			}
			else{
				freeze_window(3);
				http.open("POST", "../woven_order/requires/pre_cost_entry_controller_v3.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_generate_report_v3_reponse;
			}

		
		}
	}

function fnc_generate_report_v3_reponse() {
if (http.readyState == 4) {
	var w = window.open("Surprise", "_blank");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>' + http.responseText + '</body</html>');
	d.close();
	release_freezing();
}
}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1090px; text-align:center" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1000px" > 		 
            <fieldset style="width:1000px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="120">Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="80">Job No.</th>
                    <th width="80">File NO.</th>
                    <th width="100">Approve Status</th>
                    <th width="90">Margin Value $ % Get Up To</th>
                    <th width="90">Margin Value %</th>
                    <th width="90">Date Criteria</th>
                    <th width="90">Budget Type</th>
                    <th width="130" colspan="2"> Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','disable_enable_fields(\'txt_job_no*txt_internal_file\',0)')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/bom_list_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value, 'set_print_button', 'requires/bom_list_report_controller' );" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                        	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:68px"  placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();" />
                        	<input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:60px" />
                        </td>
						<td>
                        	<input type="text" name="txt_internal_file" id="txt_internal_file" class="text_boxes" style="width:68px"  placeholder="Browse" onDblClick="openmypage_internalfile();" readonly />
                        </td>
                        <td>
							<?  
								 $appStatusArr = array(1=>'Approve',2=>'Unapprove');
                                 echo create_drop_down( "cbo_appstatus", 90, $appStatusArr,"",1, "-All-", 0,"",0 );
                            ?>
                        </td>
                        <td>
							<?  
								 $marginUpto = array(1=>'Greater Than',2=>'Less Than',3=>'Greater Equal',4=>'Less Equal',5=>'Equal');
                                 echo create_drop_down( "cbo_marginupto", 90, $marginUpto,"",1, "-All-", 0,"",0 );
                            ?>
                        </td>
                        <td><input type="text" name="txt_marginvalue" id="txt_marginvalue" class="text_boxes_numeric" style="width:78px" placeholder="Write" /></td>
                        <td>
							<?  
								 $dateCondArr = array(1=>'Costing/Budget Date',2=>'Shipment Date');
                                 echo create_drop_down( "cbo_datetype", 90, $dateCondArr,"",0, "-All-", 1,"",0 );
                            ?>
                        </td>
						<td>
							<?  
								 $budget_type = array(2=>'Pre cost V2',3=>'Pre cost V3');
                                 echo create_drop_down( "cbo_budgettype", 90, $budget_type,"","", "-All-", 0,"",0 );
                            ?>
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
						<td></td>
                    </tr>
					<tr>						
						<td align="center" colspan="11">
							<input type="button" id="show_button" class="formbutton" value="Show" style="width:70px; display:none;" onClick="fn_report_generated('report_generate')" />
							<input type="button" id="show_button1" class="formbutton" value="Show 2" style="width:70px; display:none;" onClick="fn_report_generated('report_generate2')" />
						</td>
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
