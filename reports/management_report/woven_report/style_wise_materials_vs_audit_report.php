<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Material Vs Audit Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	19-09-2021
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
	$menu_id=$_SESSION['menu_id'];

//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Job Wise Materials and Audit Report","../../../", 1, 1, $unicode,1,1,'','','');
	?>	
	<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
		var permission='<?=$permission; ?>'; 
		function fn_report_generated(report){
			 
			//txt_style
			 var txt_job_no=document.getElementById('txt_job_no').value;
       		 var txt_style=document.getElementById('txt_style_ref').value;
			 
			 if(txt_job_no!="" || txt_style!="")
            {
                if(form_validation('cbo_company_name*cbo_year','Company*Year')==false)
                {
                    return;
                }
            }
            else
            {
                if(form_validation('cbo_company_name*txt_job_no*txt_style_ref','Company*Job No*Style')==false)
                {
                    return;
                }
            }
			
				if(report==1){
					var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_job_id*txt_style_ref',"../../../");
				}
				freeze_window(3);
				 
				http.open("POST","requires/style_wise_materials_vs_audit_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			
		}
		
		function fn_report_generated_reponse()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split("****");
				$('#report_container2').html(response[0]);
				//document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
					document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				show_msg('3');
				release_freezing();
			}
		}
		
		function openmypage(page_link,title,type){
		
			var company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name =$("#cbo_buyer_name").val();
			var cbo_year =$("#cbo_year").val();
			//alert(cbo_budget_version);
			page_link=page_link+'&garments_nature='+garments_nature+'&company_name='+company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_year='+cbo_year+'&type='+type;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job");
				var theemail1=this.contentDoc.getElementById("selected_year");
				var theemail2=this.contentDoc.getElementById("selected_company");
				var theemail3=this.contentDoc.getElementById("txt_job_id");
				//alert(theemail3.value);
				if (theemail.value!=""){
					
					if(type==1)
					{
						document.getElementById('txt_job_no').value=theemail.value;
					}
					else
					{
						document.getElementById('txt_style_ref').value=theemail.value;
					}
					
					document.getElementById('cbo_year').value=theemail1.value;
					document.getElementById('cbo_company_name').value=theemail2.value;
					document.getElementById('txt_job_id').value=theemail3.value;
					 
					//$("#g_exchange_rate").attr('disabled',true);
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
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_budget_version = $("#cbo_budget_version").val();
		//alert(cbo_budget_version);
		var page_link='requires/style_wise_materials_vs_audit_report_controller.php?action=style_refarence_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year+'&cbo_budget_version='+cbo_budget_version+'&type='+type;
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
				$("#txt_style_ref").val(style_des);
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
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#div_buyer').hide();
		//$('#div_summary').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#div_buyer').show();
		//$('#div_summary').show();
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
		/*var cost_control = $("#cbo_costcontrol_source").val();*/
		var g_exchange_rate = $("#g_exchange_rate").val();
		if(type==1)
		{
			if(budget_version==1)
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action=issue_popup&po_id='+po_ids, "Issue Details", 'width=680px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
			else
			{
			    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action=issue_popup&po_id='+po_ids, "Issue Details", 'width=680px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
		}
		else if(type==2)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action=show_transfer_popup&po_id='+po_ids+'&g_exchange_rate='+g_exchange_rate, "Transfer Details", 'width=950px, height=400px, center=1, resize=0, scrolling=0', '../../');
		}
		else if(type==3)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action=show_finish_trans_popup&po_id='+po_ids+'&g_exchange_rate='+g_exchange_rate, "Transfer Details", 'width=950px, height=400px, center=1, resize=0, scrolling=0', '../../');
		}
	}	
	function openmypage_sample_fab(po_ids,job_no)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action=sample_fab_qnty_popup&po_id='+po_ids+'&job_no='+job_no, "Fabric QTY", 'width=650px, height=400px, center=1, resize=0, scrolling=0', '../../');
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
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}*/
	
	
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function report_generate_popup(company_name,job_no,wo_type,descrip,deter_min,booking_id,pi_dtls_id,action,type)
	{
		if(type==1)
		{
			popup_width='1020px';
		}
		else
		{
			popup_width='1250px';
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_vs_audit_report_controller.php?action='+action+'&job_no='+job_no+'&wo_type='+wo_type+'&company_name='+company_name+'&descrip='+descrip+'&type='+type+'&deter_min='+deter_min+'&booking_id='+booking_id+'&pi_dtls_id='+pi_dtls_id, 'Booking/PI Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
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

function generate_worder_report_pre_cost2(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id,garments_nature)
	{

			// alert(type+"**"+job_no+"**"+company_id+"**"+buyer_id+"**"+style_ref+"**"+txt_costing_date+"**"+entry_form+"**"+quotation_id+"**"+garments_nature);

		$("#txt_style_ref").val(style_ref);
		var zero_val='';
		var path='../../../';
		var img_show=1;
		/*var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		}*/
		 
			
			 
				//if(garments_nature==1)
				//{
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					
					'&path='+"'"+path+"'"+
					'&img_show='+img_show+
					'&txt_po_breack_down_id='+''+
					'&cbo_costing_per='+"'0'"+get_submitted_data_string('txt_style_ref',"../../../");
					'&txt_costing_date='+"'"+txt_costing_date+"'";//order\sweater
					http.open("POST","../../../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse2;
				//}
				
	}
		
	function generate_fabric_report_reponse2()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	function generate_worder_report_pre_cost(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id)
	{
		freeze_window(3);
		$("#txt_style_ref").val(style_ref);
		var zero_val='';
		//var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		//if (r==true) zero_val="1"; else zero_val="0";
		var path="../../../";
		var rate_amt=2;
		var data="action="+type+
			'&zero_value='+zero_val+
			'&rate_amt='+rate_amt+
			'&txt_job_no='+"'"+job_no+"'"+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_sourcing_date='+"'"+txt_costing_date+"'"+
			'&path='+"'"+path+"'"+
			'&txt_po_breack_down_id='+"''"+
			'&cbo_costing_per='+"'0'"+get_submitted_data_string('txt_style_ref',"../../../");
			//alert(data)
			http.open("POST","../../../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
	}
		
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
			release_freezing();
		}
	}
	

</script>

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
                                 <th>Buyer Name</th>
								<th width="65" class="must_entry_caption">Job Year</th>
								<th width="80" class="must_entry_caption">Job No</th>
								<th width="80"class="must_entry_caption">Style</th>
								 
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_materials_vs_audit_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td">
	                        <?
	                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
	                        ?>
	                   	   </td>
                        
								<td><? echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
								<td>
                                <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:30px" />
                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px"  placeholder="Browse Or Write"  onDblClick="openmypage('requires/style_wise_materials_vs_audit_report_controller.php?action=order_popup','Job Selection Form',1)" /></td>
							    <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px"  placeholder="Browse Or Write"  onDblClick="openmypage('requires/style_wise_materials_vs_audit_report_controller.php?action=order_popup','Style Selection Form',2)" /></td>
                               
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
