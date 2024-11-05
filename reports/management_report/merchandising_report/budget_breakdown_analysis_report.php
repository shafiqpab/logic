<?

/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cost Break Down Report.
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	14-12-2019
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

echo load_html_head_contents("Cost Break Down Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
		{
				col_47: "none",
				col_operation: {
				id: ["tot_order_qnty","tot_order_price","tot_cons_dzn","tot_all_fab_cost","tot_fab_purchase","tot_yarn_costing","tot_knit_cost","tot_fabric_dyeing_cost","tot_yarn_dyeing_cost","tot_all_over_print_cost","tot_gmt_wash_cost","tot_fabric_cost","total_trim_cost","total_print_amount","total_embroidery_amount","total_others_amount","total_wash_cost","total_commercial_cost","total_testing_cost","total_freight_cost","tot_other_cost","tot_merchandise_cost","tot_cm_cost","grand_total_cost","tot_order_amount","total_margin"],
				col: [7,9,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

	function fn_report_generated(type)
	{
		var job_no=document.getElementById('txt_job_no').value;
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('txt_season').value;
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var file_no=document.getElementById('txt_file_no').value;
		var ref_no=document.getElementById('txt_ref_no').value;
		//var cbo_search_type_id=document.getElementById('cbo_search_type').value;


		if(job_no!="" || order_no!="" || season!="" || file_no =="" || ref_no=="")
		{
			/*if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}*/
		}
		else
		{
			//if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
			{
				return;
			}
		}


		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_file_no*txt_ref_no*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*txt_season*cbo_status*cbo_approved_status*cbo_search_type',"../../../");
		freeze_window(3);
		if(pre_cost_class==1)
		{
			http.open("POST","requires/budget_breakdown_analysis_report_controller.php",true);
		}
		else
		{
			http.open("POST","requires/budget_breakdown_analysis_report_controller_v2.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			//alert(reponse[1]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);

			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
			//alert(document.getElementById('graph_data').value);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/budget_breakdown_analysis_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			/*var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;*/
			var job_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var job_no=this.contentDoc.getElementById("txt_selected").value; // product Description
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function openmypage_file(){
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/budget_breakdown_analysis_report_controller.php?action=file_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='File Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			/*var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;*/
			var file_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var file_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			$('#txt_file_no').val(file_no);
			$('#txt_file_id').val(file_id);
		}
	}
	function openmypage_ref(){
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/budget_breakdown_analysis_report_controller.php?action=ref_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Ref No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			/*var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;*/
			var ref_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var ref_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			$('#txt_ref_no').val(ref_no);
			$('#txt_ref_id').val(ref_id);
		}
	}

	function openmypage_order_old()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/budget_breakdown_analysis_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/budget_breakdown_analysis_report_controller.php?action=order_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			/*var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;*/
			var order_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var order_no=this.contentDoc.getElementById("txt_selected").value; // product Description
			$('#txt_order_no').val(order_no);
			$('#txt_order_id').val(order_id);
			

		}
	}

	function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_fab_purchase_detail(po_id,job_no,company_id,buyer_id,fabric_source,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pre_cost_knit_popup(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,action)
	{
		var popup_width='750px';
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_washing_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_trim_cost_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	//Pre cost end

	function generate_pricecost_yarnavg_popup(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{ //alert(quotation_id);
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricecost_purchase_popup(po_id,job_no,company_id,buyer_id,fabric_source,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pri_cost_knit_popup(po_id,job_no,company_id,buyer_id,cons_process,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_trim_cost_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_precost_embell_cost_detail(po_id,job_no,company_id,buyer_id,style,action)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_pricost_embell_cost_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{   //alert(quotation_id);
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action+'&pre_cost_class='+pre_cost_class, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function new_window1(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="yarn_summary";
			//scroll_div='scroll_body';
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
	}

	function precost_bom_pop(type,po_id,job_no,company_id,buyer_id,style_ref,costing_date)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		//alert(po_id);
		/*var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/budget_breakdown_analysis_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');*/

/*		var data="&action=bomRpt"+
					'&txt_po_breack_down_id='+"'"+po_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'";
					//alert(data);
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller.php",true);

*/
					var zero_val='';
					var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
					if (r==true)
					{
						zero_val="1";
					}
					else
					{
						zero_val="0";
					}
					var data="action="+type+
					'&txt_po_breack_down_id='+"'"+po_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&txt_costing_date='+"'"+costing_date+"'"+
					'&zero_value='+zero_val+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&presentation_type='+"'"+2+"'";

				if(pre_cost_class==1)
				{
					http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
				}
				else
				{
					http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
				}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
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
	}
	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,quotation_id,txt_order_id,txt_order)
	{
		
			
		//var ref=$("#txt_style_ref").val(style_ref);
		//var style_ref_no=encodeURIComponent(""+$("#txt_style_ref").val()+"");
		// alert(style_ref+'='+style_ref_no);
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		if( type=="budget5")
		{
			
			var rpt_type=8;var comments_head=1;
			var report_title="Budget/Cost Sheet";
			//var comments_head=0;
			var txt_style_ref_id='';
			var sign=0;

			var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action=report_generate&reporttype="+rpt_type+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
				'&txt_order='+"'"+txt_order+"'"+
				'&txt_order_id='+"'"+txt_order_id+"'"+
				'&txt_season='+"'"+txt_season+"'"+

				'&txt_season_id='+"'"+txt_season_id+"'"+
				'&txt_file_no='+"'"+txt_file_no+"'"+
				'&txt_quotation_id='+"'"+quotation_id+"'"+
				'&txt_hidden_quot_id='+"'"+quotation_id+"'"+
				'&comments_head='+"'"+comments_head+"'"+
				'&sign='+"'"+sign+"'"+
				'&report_title='+"'"+report_title+"'"+
				'&path=../';
				//alert(data);
			http.open("POST","../merchandising_report/requires/cost_breakup_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}

	}
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4)
		{
			
			var reponse=trim(http.responseText).split('****');
			
			if(reponse[0]!=="mail_sent"){

				$('#data_panel').html( http.responseText );
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
				d.close();
				show_msg('3');
				// release_freezing();

			}
		}
	}

	
		

	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==4)
		{
			document.getElementById('search_by_th_up').innerHTML="Cancelled Date";
			$('#search_by_th_up').css('color','blue');
		}
	}

	function openmypage_season()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var job_no = $("#txt_job_no").val();
		var page_link='requires/budget_breakdown_analysis_report_controller.php?action=season_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_season=this.contentDoc.getElementById("hide_season").value;

			$('#txt_season').val(hide_season);
		}
	}

	function fn_report_excel_generated(type) //Excel Convert Only
	{
		var excel_type=10;
		var job_no=document.getElementById('txt_job_no').value;
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('txt_season').value;
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
		if (excel_type==10)
		{

			var grnerate=1;
			if(job_no=="" && order_no=="" && season=="")
			{
				if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
				{
					var grnerate=0;
					return;
				}
				else
				{
					var grnerate=1;
				}
			}


			if(grnerate==1)
			{

				var data="action=report_generate_excel&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*txt_season*cbo_status*cbo_approved_status',"../../../")+'&excel_type='+excel_type;


				//alert(data);//return;
				freeze_window(3);
				if(pre_cost_class==1)
				{
					http.open("POST","requires/budget_breakdown_analysis_report_controller.php",true);
				}
				else
				{
					http.open("POST","requires/budget_breakdown_analysis_report_controller_v2.php",true);
				}

				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse2;
			}
		}

	}
	function fn_report_generated_reponse2()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			//alert(response[1]);
			//$('#report_container3').html(response[0]);
			if(response!='')
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+response[1]);
				 document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
	}
	function fnc_field_disabled()
	{
		
		var search_type=$("#cbo_search_type").val();
		//alert(search_type);
		if(search_type==1)
		{
			$("#txt_file_no").attr("disabled","disabled");
			$("#txt_ref_no").attr("disabled","disabled");
			$("#txt_order_no").attr("disabled","disabled");
			$("#txt_order_no").val("");$("#txt_file_no").val("");$("#txt_ref_no").val("");
			
		}
		else {
			$("#txt_file_no").removeAttr("disabled","disabled");
			$("#txt_ref_no").removeAttr("disabled","disabled");
			$("#txt_order_no").removeAttr("disabled","disabled");
		}
	}
</script>
</head>
<body onLoad="set_hotkey();fnc_field_disabled();">
<form id="costbreakdownReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1540px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1500px;" id="content_search_panel">
            <table class="rpt_table" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th class="must_entry_caption">Search Type</th>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No.</th>
                    <th>File No.</th>
                    <th>Ref. No.</th>
                    <th>Order Status</th>
                    <th>Active Status</th>
                    <th>Approved Status</th>
                    <th>Order</th>
                    <th>Season</th>
                    <th>Search By</th>
                    <th>Pre Cost By</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Shipment Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<?
								$search_type_arr=array(2=>"PO Wise");
                           		echo create_drop_down( "cbo_search_type", 100, $search_type_arr,"", 0, "-- Select --", 2, "fnc_field_disabled();" );
                            ?>
                        </td>
                         <td>
							<?
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/budget_breakdown_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/budget_breakdown_analysis_report_controller' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<?
                            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" placeholder="Wr.  File No "  />
                           
                        </td>
                        <td>
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Wr. Ref No."  />
                           
                        </td>
                        <td>
							<?
								$order_status=array(0=>"All",1=>"Confirmed",2=>"Projected");
								echo create_drop_down( "cbo_order_status", 80, $order_status,"", 0, "", 0, "" );
                            ?>
                        </td>
                        <td >
							  <?php echo create_drop_down("cbo_status", 85, $row_status, "", 0, "", "", "", "");?>
                        </td>
                        <td>
							<?
								$approved_status_array=array(1=>'Not Submit',2=>'Ready For Approval',3=>'Partial Approved',4=>'Full Approved');
                                echo create_drop_down( "cbo_approved_status", 100, $approved_status_array,"", 1, "-- All -- ", $selected, "",0,"" );
                            ?>

                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Wr./Br. Order"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                        <td align="center">
                        	<input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_season();" readonly/>
                        </td>
                         <td width="" align="center">
							<?
								$search_by = array(1=>'Shipment Date',2=>'Po Received Date',3=>'Po Insert Date',4=>'Cancelled Date');
								$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );
                            ?>
                        </td>
                        <td width="" align="center">
							<?
								$pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
								echo create_drop_down( "cbo_pre_cost_class", 100, $pre_cost_class_arr,"",0, "--Select--", 2,"",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date" >
                        </td>
                    </tr>
                    <tr align="center"  class="general">
                        <td colspan="16">
                        	<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                    <tr >
                    	<td colspan="16" align="center">

                            <input type="button"  id="show_button" class="formbutton" style="width:90px; display:none" value="Group" onClick="fn_report_generated(2)" />
                            
                            <input type="button"  id="show_button" class="formbutton" style="width:120px; display:none" value="Convert to Excel" onClick="fn_report_excel_generated(1)" /><a id="aa1" href="" style="text-decoration:none;"></a>
                            
                            
                            <input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('costbreakdownReport_1','report_container*report_container2','','','')" />

                            <span id="button_data_panel"></span>
                            
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
        <div id="report_container3"></div>
		<div style="display:none;" id="data_panel"></div>
 </form>
</body>
<!--<script>set_multiselect('cbo_approved_status','0','0','','');</script> -->
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>