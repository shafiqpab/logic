<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Cost Comparison.
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	13-05-2017
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
	echo load_html_head_contents("Style Wise Cost Comparison","../../../", 1, 1, $unicode,1,1);
	?>	
	<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
		function fn_report_generated(report){
			var budget_version = $("#cbo_budget_version").val();
			var costcontrol_source = $("#cbo_costcontrol_source").val();
			
			if(budget_version==1 && costcontrol_source==1)
			{
				alert("Quick Costing is not Applicable for Budget Version Pre-Cost 1."); 
				return;
			}
			
			if(form_validation('cbo_company_name*cbo_year*txt_job_no','Company Name*Year*Job No')==false){
				return;
			}
			else{	
				if(report==1){
					var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_job_no*cbo_year*g_exchange_rate*cbo_budget_version*cbo_costcontrol_source',"../../../");
				}
				freeze_window(3);
				if(budget_version==1)
				{
					http.open("POST","requires/style_wise_cost_comparison_report_controller_ets.php",true);
				}
				else
				{
					http.open("POST","requires/style_wise_cost_comparison_report_controller2_ets.php",true);
				}
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
		}
		
		function fn_report_generated_reponse()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split("****");
				$('#report_container2').html(response[0]);
				document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
				show_msg('3');
				release_freezing();
			}
		}
		
		function openmypage(page_link,title){
			var garments_nature=document.getElementById('garments_nature').value;
			var company_name=document.getElementById('cbo_company_name').value;
				var cbo_budget_version = $("#cbo_budget_version").val();
			//alert(cbo_budget_version);
			page_link=page_link+'&garments_nature='+garments_nature+'&company_name='+company_name+'&cbo_budget_version='+cbo_budget_version;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job");
				var theemail1=this.contentDoc.getElementById("selected_year");
				var theemail2=this.contentDoc.getElementById("selected_company");
				var theemail3=this.contentDoc.getElementById("selected_exchange_rate");
				//alert(theemail3.value);
				if (theemail.value!=""){
					document.getElementById('txt_job_no').value=theemail.value;
					document.getElementById('cbo_year').value=theemail1.value;
					document.getElementById('cbo_company_name').value=theemail2.value;
					document.getElementById('g_exchange_rate').value=theemail3.value;
					$("#g_exchange_rate").attr('disabled',true);
					freeze_window(5);
					release_freezing();
				}
			}
		}
		
	function openmypage_style(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style").val();
		var cbo_budget_version = $("#cbo_budget_version").val();
		//alert(cbo_budget_version);
		var page_link='requires/style_wise_cost_comparison_report_controller_ets.php?action=style_refarence_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year+'&cbo_budget_version='+cbo_budget_version+'&type='+type;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(type);
			if(type==1)
			{
				$("#txt_style").val(style_des);
				$("#txt_style_ref_id").val(style_id); 
				$("#txt_style_ref_no").val(style_no);
			}
			else
			{
				$("#txt_job_no").val(style_des);
				$("#txt_style_ref_id").val(style_id); 
			}
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#div_buyer').hide();
		$('#div_summary').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#div_buyer').show();
		$('#div_summary').show();
	}
	
	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
	
	function openmypage_issue(po_ids,type)
	{
		var budget_version = $("#cbo_budget_version").val();
		var g_exchange_rate = $("#g_exchange_rate").val();
		if(type==1)
		{
			if(budget_version==1)
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?action=issue_popup&po_id='+po_ids, "Issue Details", 'width=680px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
			else
			{
			    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller2_ets.php?action=issue_popup&po_id='+po_ids, "Issue Details", 'width=680px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
		}
		else if(type==2)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?action=show_transfer_popup&po_id='+po_ids+'&g_exchange_rate='+g_exchange_rate, "Transfer Details", 'width=850px, height=400px, center=1, resize=0, scrolling=0', '../../');
		}
		else if(type==3)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?action=show_finish_trans_popup&po_id='+po_ids+'&g_exchange_rate='+g_exchange_rate, "Transfer Details", 'width=850px, height=400px, center=1, resize=0, scrolling=0', '../../');
		}
	}
	
	/*function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}*/
	
	function openmypage_mkt_cm_popup(job_no,type,tittle,popup_type,popup_width)
	{
		var company = $("#cbo_company_name").val();
		var exchange_rate = $("#g_exchange_rate").val();	
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?job_no='+job_no+'&action='+type+'&popup_type='+popup_type+'&company='+company+'&exchange_rate='+exchange_rate+'&popup_width='+popup_width, tittle, 'width='+popup_width+', height=420px, center=1, resize=0, scrolling=0', '../../');
	}
	
	
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_actual(po_id,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function generate_po_report(company_name,po_id,job_no,action,type)
	{
		popup_width='940px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cost_comparison_report_controller_ets.php?action='+action+'&po_id='+po_id+'&job_no='+job_no+'&company_name='+company_name, 'PO Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function toggleSigns(span) {
	//alert(span)
	if($("#"+span).text()=='+'){
		$("#"+span).text("-")
	}else{
		$("#"+span).text("+")
	}
}
function yarnT(span,selector){
	$( selector ).toggle( "fast", function() {
	});
	toggleSigns(span)
}

function exchange_check(job_no)
{
	var cbo_year=$("#cbo_year").val();
	var budget_version = $("#cbo_budget_version").val();
	if(cbo_year==0)
	{
		$("#txt_job_no").val('');
		$("#g_exchange_rate").val('');
	}
	if( form_validation('cbo_company_name*cbo_year','Company*Year')==false )
	{
			return;
	}
	var company_name=$("#cbo_company_name").val();
	
	
	var data=company_name+'_'+cbo_year+'_'+job_no+'_'+budget_version;
	if(job_no!='')
	{
		var exchange_rate = return_global_ajax_value( data, 'load_exchange_rate', '', 'requires/style_wise_cost_comparison_report_controller_ets');
		exchange_rate=trim(exchange_rate);
	}
	//alert(exchange_rate);
	$("#g_exchange_rate").val(exchange_rate);
	$("#g_exchange_rate").attr('disabled',true);
	if(exchange_rate=='')
	{
		 $("#txt_job_no").val('');
	}
}

</script>
<style>
.yarn,.fbpur,.knit,.dyfi,.transc,.trims{
	display:none;
}

.adl-signs{
	font-weight:bold;
	font-size:18px;
	cursor:pointer
}
</style>
</head>

<body onLoad="set_hotkey();">
	
	<form id="cost_breakdown_rpt">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../../"); ?>
			<h3 align="left" id="accordion_h1" style="width:700px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel"> 
				<fieldset style="width:700px;">
					<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>                   
								<th width="150" class="must_entry_caption">Company Name</th>
								<th width="65" class="must_entry_caption">Job Year</th>
								<th width="80" class="must_entry_caption">Job No</th>
								<th width="80"class="must_entry_caption">Exchange Rate</th>
								<th width="100">Budget Version</th>
                                <th width="100">Cost Cont. source</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>
								<td><? echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", 1, "",0,"" ); ?></td>
								<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" onChange="exchange_check(this.value);" placeholder="Browse Or Write"  onDblClick="openmypage('requires/style_wise_cost_comparison_report_controller_ets.php?action=order_popup','Job/Order Selection Form')" /></td>
							    <td><input type="text" name="g_exchange_rate" id="g_exchange_rate" class="text_boxes" style="width:70px" readonly  /> </td>
                                <td>
									<?  
                                    $pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2',3=>'Pre Cost 3');
                                    $bom_cost_control_source_arr=array(1=>"Quick Costing",2=>"Price Quotation",3=>"Quotation/Buyer Costing");
                                    //$dd="search_populate(this.value)";
                                    echo create_drop_down( "cbo_budget_version", 100, $pre_cost_class_arr,"",0, "--Select--", 2,"",0 );
                                    ?>
                        		</td>
                                <td><? echo create_drop_down( "cbo_costcontrol_source", 100, $bom_cost_control_source_arr,"",0, "--Select--", 2,"",0 ); ?></td>
							<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>
	<div id="report_container" align="center"></div>
	<div id="report_container2"></div>
</form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
