<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
?>

<?
echo load_html_head_contents("TNA Process","../../", 1, 1, $unicode,1,'');

/* $dataPoints = array( 
    	array("y" => 7,"label" => "March" ),
    	array("y" => 12,"label" => "April" ),
    	array("y" => 28,"label" => "May" ),
    	array("y" => 18,"label" => "June" ),
    	array("y" => 41,"label" => "July" )
    );*/
 // echo json_encode($dataPoints);

?>
<!--<script src="../../canvasjs-3.2.11/canvasjs.min.js"></script>-->



<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';

var Dataset = '';
var tableFilters = 
 {
	col_operation: {
	id: ["total_po_qty","total_po_qty_pcs"],
	col: [4,5],
	operation: ["sum","sum"],
	write_method: ["innerHTML","innerHTML"]
	}
 }

function fnc_generate_report_main(operation, rpt_type)
{
	var task_name_ref=$('#txt_taks_name').val();
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	var style_no=$('#txt_style_ref_no').val();
	var file_no=$('#txt_file_no').val();
	var int_ref_no=$('#txt_int_ref_no').val();
	if(task_name_ref!="")
	{
		if(rpt_type==1)
		{
			alert("Please Click On Task Wise Button");
			return;
		}
	}
	
	var action_type="";
	if(rpt_type==9)
	{
		$('#report_chk_id').val(9);
	}
	else 
	{
		$('#report_chk_id').val(0);
	}
	if(rpt_type==3)
	{
			
		//reset_form('printbooking_1','booking_list_view*booking_list_view_list','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3');
			
		if(job_no!="" || order_no!="" || style_no!="" || file_no!="" || int_ref_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			
			if ( form_validation('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to','Company Name*Buyer Name*Date From*Date To')==false )
			{
				return;
			}
		}
			
		action_type="generate_buyer_task_wise_report";
	}
	else if(rpt_type==1)
	{
		if(job_no!="" || order_no!="" || style_no!="" || file_no!="" || int_ref_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_tna_report";
	} 
	else if(rpt_type==2)
	{
		if ( form_validation('cbo_company_name*txt_taks_name','Company Name*Task')==false )
		{
			return;
		}
		action_type="generate_task_wise_report";
	}
	else if(rpt_type==4)
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_overdew_task_wise_report";
	}
	else if(rpt_type==6)
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_tna_with_commitment_report";
	}
	else if(rpt_type==7)
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_style_wise_report";
	}

	else if(rpt_type==8)
	{
			if ( form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false )
			{
				return;
			}
			action_type="generate_style_report_with_graph";
	}
	else if(rpt_type==9)
	{
			if ( form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false )
			{
				return;
			}
			action_type="generate_style_report_with_graph_wvn";
	}
	else if(rpt_type==10)
	{
		if(job_no!="" || order_no!="" || style_no!="" || file_no!="" || int_ref_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_tna_report_v2";
	}
	else if(rpt_type==11)
	{
			if ( form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false )
			{
				return;
			}
			action_type="generate_tna_style_follow_up_woven_short";
	}
	else if(rpt_type==12)
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_style_wise_report_by_first_po_wise";
	}


	else
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_penalty_report";
	}
	
	var data="action="+action_type+"&"+get_submitted_data_string('cbo_company_name*txt_taks_name*tna_task_id*cbo_buyer_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_order_no*txt_style_ref_no*cbo_search_type*cbo_shipment_status*cbo_order_status*txt_file_no*txt_int_ref_no*cbo_task_group',"../../")+'&rpt_type='+rpt_type; 
	freeze_window(operation);
	http.open("POST","requires/tna_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_report_main_reponse;
}

function fnc_generate_report_main_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('****');
		//alert(reponse[0]);
		if(reponse[3]==9) //Wvn
		{
			hs_chart_mm(reponse[2]);
			$('#on_style').show();
		}
		else
		{
			//hs_chart_mm(reponse[2],0);
			
			$('#chartContainer').html('');
			$('#container_dhu').html('');
			$('#container_ratio').html('');
			$('#on_style').hide();
		}
		 
		$("#report_container").html(reponse[0]);  
		document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body",-1,tableFilters);
		release_freezing();
	}
}
// alert(Dataset);
function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide(); 
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
	
	$('#table_body tr:first').show();
	
}


function update_tna_process_style(type,id,po_id)
{ 
	var po_id_arr=po_id.split(',');
	$.each( po_id_arr, function( key, po_id ) {
		update_tna_process(type,id,po_id);
	});
}

function update_tna_process_style_first_ship(type,id,po_id)
{ 
	//var po_id_arr=po_id.split(',');
	//$.each( po_id_arr, function( key, po_id ) {
		update_tna_process(type,id,po_id,'edit_update_tna_first_ship');
	//});
}

function update_tna_process(type,id,po_id,action='edit_update_tna')
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action='+action+'&permission='+permission+'&cbo_company_name='+document.getElementById("cbo_company_name").value, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("auto_field_data_str").value;
		var reponse=theemail.split('**');
		 
		if(reponse[2]==1){
			if(trim($('#plan_1'+reponse[3]+reponse[4]).text()) != reponse[7]){
				$('#plan_1'+reponse[3]+reponse[4]).text(reponse[7]).css('color','blue');
			}
			if(trim($('#plan_2'+reponse[3]+reponse[4]).text()) != reponse[8]){
				$('#plan_2'+reponse[3]+reponse[4]).text(reponse[8]).css('color','blue');
			}

		}
		else if(reponse[2]==2){
			if(trim($('#actual_1'+reponse[3]+reponse[4]).text()) != reponse[5]){
				$('#actual_1'+reponse[3]+reponse[4]).text(reponse[5]).css('color','blue');
			}
			if(trim($('#actual_2'+reponse[3]+reponse[4]).text()) != reponse[6]){
				$('#actual_2'+reponse[3]+reponse[4]).text(reponse[6]).css('color','blue');
			}
			
			
		}
		//alert('#plan_1'+reponse[3]+reponse[4]+'=='+reponse[7]);		
		
		
		if (theemail!="")
		{
			freeze_window(5);
			//get_submitted_data_string('',"../../"); 
			release_freezing();
		}
	}
}

function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			freeze_window(5);
			
			release_freezing();
		}
	}
}

function order_update_log_popup(job_no,po_id)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&action=po_update_history'+'&permission='+permission, "PO Update History", 'width=640px,height=300px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			freeze_window(5);
			
			release_freezing();
		}
	}
}

function openmypage_task()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var tna_task = $("#txt_taks_name").val();
	var tna_task_id = $("#tna_task_id").val();
	var tna_task_id_no = $("#tna_task_id_no").val();
	var cbo_task_group = $("#cbo_task_group").val();
	
	
	var page_link='requires/tna_report_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no+'&cbo_task_group='+cbo_task_group;   
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		//alert(style_des_no);
		$("#txt_taks_name").val(style_des);
		$("#tna_task_id").val(style_id); 
		$("#tna_task_id_no").val(style_des_no);
		if(style_des!="")
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('TNA From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('TNA To Date');
			
		}
		else
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('Ship From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('Ship To Date');
		}
	}
}

function fn_change_caption(str)
{
	
	var tna_task_id = $("#tna_task_id").val();
	if(tna_task_id){alert('Task ID Selected.');return;}
	
	if(str==1)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('From Ship Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('To Ship Date');
	}
	else if(str==3)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('From Cun.Ship Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('To Cun.Ship Date');
	}
	
	else if(str==4)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('From Insert Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('To Insert Date');
	}
	else if(str==5)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('From Ship Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('To Ship Date');
	}
	else
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('From Recv. Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('To Recv. Date');
	}
	
}

function print_report_button_setting(report_ids)
{
	$("#btn_buyer_task_report_generate").hide();
	$("#btn_task_report_generate").hide();
	$("#btn_report_generate").hide();
	$("#btn_due_task").hide();
	$("#btn_penalty").hide();
	$("#btn_commitment").hide();
	$("#btn_report_generate_7").hide();
	$("#btn_report_generate_8").hide();
	$("#btn_report_generate_9").hide();
	$("#btn_report_generate_10").hide();
	$("#btn_report_generate_11").hide();
	$("#btn_report_generate_12").hide();
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		if(report_id[k]==54){$("#btn_buyer_task_report_generate").show();}
		if(report_id[k]==55){$("#btn_task_report_generate").show();}
		if(report_id[k]==56){$("#btn_report_generate").show();}
		if(report_id[k]==57){$("#btn_due_task").show();}
		if(report_id[k]==58){$("#btn_penalty").show();}
		if(report_id[k]==198){$("#btn_commitment").show();}
		if(report_id[k]==801){ $("#btn_report_generate_7").show();}
		if(report_id[k]==802){ $("#btn_report_generate_8").show();}
		if(report_id[k]==803){ $("#btn_report_generate_9").show();}
		if(report_id[k]==804){ $("#btn_report_generate_11").show();}
		if(report_id[k]==805){ $("#btn_report_generate_12").show();}
		if(report_id[k]==506){ $("#btn_report_generate_10").show();}

	}
}


function progress_comment_popup_style(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment_style'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}


function progress_comment_popup_style_first_ship(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment_style_first_ship'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}


function setReadyToApp(po_id,app_status,approved){
	if(approved == 1 || approved == 3){alert('Sorry,This order is approved');return;}
	
	if(!confirm("Are you Sure?")){return false;}
	
	var data="action=set_ready_to_app&po_id="+po_id+"&app_status="+app_status; 
	freeze_window(operation);
	http.open("POST","requires/tna_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = setReadyToAppReponse;
}

function setReadyToAppReponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		//$("#approval_status_"+reponse[1]).html("<span style='color:#D00;'>Approve</span>");
		release_freezing();
	}
}

function approvedAlertMessage()
{
	alert("This Order Is Approved. Any change not allow.");
}


function fnTaskPopup(taskDataStr)
{ 

	var dataArr=taskDataStr.split('*');
	if(dataArr[0]==47){
		var width=605;
		var action='graph_task_poup_yarn_in_house';
	}
	else if(dataArr[0]==46){
		var width=605;
		var action='graph_task_poup_yarn_work_order';
	}
	else{
		var width=305;
		var action='graph_task_poup';
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?task_data_str='+taskDataStr+'&action='+action+'&permission='+permission, "TNA Task Dtls", 'width='+width+'px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}

function fnTaskPopupWvn(taskDataStr)
{ 

	var dataArr=taskDataStr.split('*');
	if(dataArr[0]==47){
		var width=1200;
		var action='graph_task_poup_yarn_in_house';
	}
	else if(dataArr[0]==46){
		var width=1200;
		var action='graph_task_poup_yarn_work_order';
	}
	else{
		var width=1200;
		var action='graph_task_poup_wvn';
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?task_data_str='+taskDataStr+'&action='+action+'&permission='+permission, "TNA Task Dtls", 'width='+width+'px,height=470px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			release_freezing();
		}
	}
}
	//sweater Precost.................................
	function generate_bom(type,job_no,company_name,buyer_name,style_ref,costing_date)
	{

		var rate_amt=2; var zero_val='';
		if(type!='mo_sheet')
		{
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		}

		if (r==true) zero_val="1"; else zero_val="0";
		var data="action=basic_cost&zero_value=1&rate_amt=2&txt_job_no='"+job_no+"'&cbo_company_name="+company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref='"+style_ref+"'&txt_costing_date='"+costing_date+"'&txt_po_breack_down_id=''&cbo_costing_per=''";
		//alert(data);
		
		freeze_window(3);
		http.open("POST","../../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_bom_reponse;
		
	}

	function generate_bom_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
		}
	}


	function task_his_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=task_his_dtls'+'&permission='+permission, "TNA Task History", 'width=1040px,height=400px,center=1,resize=1,scrolling=0','../');
	}

	function report_generate_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&cbo_company_name='+$('#cbo_company_name').val()+'&action=report_generate_popup'+'&permission='+permission, "Report View", 'width=1040px,height=400px,center=1,resize=1,scrolling=0','../');
	}

function fn_tna_target_entry(type,tna_mst_id,po_id,task_id,action='tna_target_save_update_delete_popup')
{ 

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_report_controller.php?type='+type+'&tna_mst_id='+tna_mst_id+'&po_id='+po_id+'&task_id='+task_id+'&action='+action+'&permission='+permission, "TNA Update", 'width=1200px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var theemail=this.contentDoc.getElementById("auto_field_data_str").value;
		var reponse=theemail.split('**');
		
		if(reponse[2]==1){
			if(trim($('#plan_1'+reponse[3]+reponse[4]).text()) != reponse[7]){
				$('#plan_1'+reponse[3]+reponse[4]).text(reponse[7]).css('color','blue');
			}
			if(trim($('#plan_2'+reponse[3]+reponse[4]).text()) != reponse[8]){
				$('#plan_2'+reponse[3]+reponse[4]).text(reponse[8]).css('color','blue');
			}

		}
		else if(reponse[2]==2){
			if(trim($('#actual_1'+reponse[3]+reponse[4]).text()) != reponse[5]){
				$('#actual_1'+reponse[3]+reponse[4]).text(reponse[5]).css('color','blue');
			}
			if(trim($('#actual_2'+reponse[3]+reponse[4]).text()) != reponse[6]){
				$('#actual_2'+reponse[3]+reponse[4]).text(reponse[6]).css('color','blue');
			}
			
			
		}
		//alert('#plan_1'+reponse[3]+reponse[4]+'=='+reponse[7]);		
		
		
		if (theemail!="")
		{
			freeze_window(5);
			//get_submitted_data_string('',"../../"); 
			release_freezing();
		}
	}
}

</script>

<body  onLoad="set_hotkey()">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1330px; text-align:left">
    	<table width="1330" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="110">Task Group</th>
                    <th width="110">Task</th>
                    <th width="120" class="" id="buyer_caption">Buyer Name</th>
                    <th width="120">Merchant</th>
                    <th width="80">Search type</th>
                    <th width="80">Shipment Status</th>
                    <th width="80">Order Status</th>
                    <th width="70" id="from_date_html">Ship From Date</th>
                    <th width="70" id="to_date_html">Ship To Date</th>
                    <th width="90">Job No</th>
                    <th width="90">Order No</th>
                    <th width="90">Style Ref. No</th>
                    <th width="90">File No</th>
                    <th>Internal Ref. No</th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                	<?
					echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/tna_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value, 'set_print_button', 'requires/tna_report_controller' ); " );
					?> 
                </td>
                 <td>
                	<?
					if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
					echo create_drop_down( "cbo_task_group", 130, "select task_group from lib_tna_task where is_deleted = 0 and status_active=1 $task_group_con group by task_group order by task_group","task_group,task_group", 1, "-- Select --", $selected, "" );
					?> 
                </td>

                <td align="center">
                    <input style="width:100px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                    <input type="hidden" name="tna_task_id" id="tna_task_id"/> <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>   
                     <input type="hidden" name="report_chk_id" id="report_chk_id"/>              
                 </td>
                <td id="buyer_td">
					<? 
                    echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                    ?>	
                 </td>
                <td id="team_td">         
					<? 
					echo create_drop_down( "cbo_team_member", 120, "select id,team_member_name from  lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
                    //echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                    ?>             
                </td>
                <td align="center">
					<? 
					$search_type=array(1=>'Pub-Ship Date',2=>'PO Recv. Date',3=>'Country Ship Date',4=>'PO Insert Date',5=>'Ship Date');
					echo create_drop_down( "cbo_search_type", 80, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
                    ?>	
                </td>
                <td align="center">
					<?
                    	//$shipment_status_tna = array(0=>"ALL (Pending+Partial)",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                    	$shipment_status_tna = array(1=>"ALL (Pending+Partial)",2=>"Full Pending",3=>"Partial Shipment",4=>"Full Shipment/Closed");
                        echo create_drop_down( "cbo_shipment_status", 80, $shipment_status_tna,"", 1, "-- Select --", $selected, "",0,"1,4" );
					 ?>
                </td>
                <td align="center">
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 80, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
                <td align="center"><input type="text" name="txt_order_no" id="txt_order_no" autocomplete="off" class="text_boxes" style="width:80px" ></td>
                <td  align="center"><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
                <td align="center"><input type="text" name="txt_file_no" id="txt_file_no" autocomplete="off" class="text_boxes" style="width:80px" ></td>
                <td  align="center"><input type="text" name="txt_int_ref_no" id="txt_int_ref_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
           </tr>
           
           <tr>
            	<td colspan="15" align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
           <tr>
           		<td colspan="15" height="40" valign="middle" align="center">
                	<input type="button" class="formbutton" style="width:150px;display:none;" value="Generate Buyer Wise" onClick="fnc_generate_report_main(3,3)" id="btn_buyer_task_report_generate" />
                    <input type="button" class="formbutton" style="width:150px;display:none;" value="Generate Task Wise" onClick="fnc_generate_report_main(3,2)" id="btn_task_report_generate" />
                    <input type="button" class="formbutton" style="width:120px;display:none;" value="Generate Report" onClick="fnc_generate_report_main(3,1)" id="btn_report_generate"/>
                    <input type="button" class="formbutton" style="width:120px;display:none;" value="Overdue Task" onClick="fnc_generate_report_main(3,4)" id="btn_due_task" />
                    <input type="button" class="formbutton" style="width:100px;display:none;" value="Penalty" onClick="fnc_generate_report_main(3,5)" id="btn_penalty" />
                    <input type="button" class="formbutton" style="display:none;" value="TNA With Commitment" onClick="fnc_generate_report_main(3,6)" id="btn_commitment" />
                    
                    <input type="button" class="formbutton" style="width:150px;display:none;" value="Generate Style Wise" onClick="fnc_generate_report_main(3,7)" id="btn_report_generate_7" />
                    
                    <input type="button" class="formbutton" style="width:130px;display:none;" value="Style Follow Up" onClick="fnc_generate_report_main(3,8)" id="btn_report_generate_8" />
                    <input type="button" class="formbutton" style="width:140px;display:none;" value="Style Follow Up Wvn" onClick="fnc_generate_report_main(3,9)" id="btn_report_generate_9" />
                 
                	<!--Button action 10 for knit asis live. Don't active in devlop-->
                    <input type="button" class="formbutton" style="width:100px;display:none;" value="Report V2" onClick="fnc_generate_report_main(3,10)" id="btn_report_generate_10"/>
                    <input type="button" class="formbutton" style="width:150px;display:none;" value="Style Follow Up Short" onClick="fnc_generate_report_main(3,11)" id="btn_report_generate_11"/>
					<input type="button" class="formbutton" style="width:170px;display:none;" value="Style Wise By First Ship" onClick="fnc_generate_report_main(3,12)" id="btn_report_generate_12"/>
                </td>
           </tr>
           
        </table>
        <div id="print_button"></div>
        <div style="margin-top:5px" id="report_container"></div>
        <div style=" display:none;" id="data_panel"></div>
        
        <br>
        <div style="margin-left:30px;width:1150px;height: 370px; border: solid 1px; display:none" id="chartContainer"></div>
        <br>
        <table  style="margin-left:30px"width="1150" border="1"  cellpadding="0" cellspacing="0">
        <tr>
        <td>
         <div style=" margin-right:80px;width:450px;height:370px;" id="container_dhu"></div>
         </td>
          <td>
         <div style=" margin-left:30px;width:400px;height: 370px;" id="container_ratio"></div>
         </td>
         </tr>
         <tr>
        <td>
       <div  id="dhu_show" style=" display:none">
        
        <table  width="100%" border="0" style=" border:solid 1px; display:none">
       <caption> <b style="text-align:center">Quality Overview For Sewing Quality<hr>For <b id="pass_qty"> </b> Pcs Checked Pieces</b> </caption>
        <tr>
        <td width="80">DHU(?)</td>
        <td width="80">RFT(?)</td>
        <td width="80">Reject Rate(?)</td>
        <td width="80">Defected Rate(?)</td>
        </tr>
        <tr>
        <td width="80" ><b id="dhu_per"> </b> </td>
        <td width="80" id=""><b id="rft_per"> </b></td>
        <td width="80" id=""><b id="rej_per"> </b></td>
        <td width="80" id=""><b id="defect_per"> </b></td>
        </tr>
        <tr>
        <td width="80" id=""><b>Defect:</b> <b id="defect_dhu_per"> </b> </td>
        <td width="80" id=""><b>Defect:</b> <b id="defect_rft_per"> </td>
        <td width="80" id=""><b>Defect:</b> <b id="defect_rej_per"> </td>
        <td width="80" id=""><b>Defect:</b> <b id="defect_defect_per"> </td>
        </tr>
        
        <tr>
        <td width="100" id=""><div id="defect_dhu_box"> </div> </td>
        <td width="100" id=""><div id="defect_rft_box">  </div></td>
        <td width="100" id=""><div id="defect_rej_box"> </div></td>
        <td width="100" id=""><div id="defect_defect_box"> </div></td>
        </tr>
        
        </table>
        <div id="on_style">
         <table  width="100%" border="0" style=" border:solid 0px;">
         <tr>
         <td>
         <table style="font-size:12px" align="center">
         	<caption><b> DHU on Style…</b> </caption>
                <tr>
                    <td bgcolor="#33CCCC" width="10"></td>
                    <td>DHU &nbsp;&nbsp;</td>
                    <td bgcolor="#FFA500" width="10"></td>
                    <td>DHU THRESHOLD&nbsp;&nbsp;</td>
                </tr>
            </table>
         </td>
         </tr>
          <tr>
       		<td><div style=" margin-right:100px;width:550px;height: 370px; border: solid 1px;" id="container_style">  </div></td>
          </tr>
           </table>
        </div>
         </div>
         </td>
         </tr>
         </table>
    </fieldset>
</div>
</body>

<script  type="text/javascript">
 
 function hs_chart_mm(Dataset)
 {
    //alert(Dataset);
    // window.onload = function() {
     
		 $("#chartContainer").show();
		var chart = new CanvasJS.Chart("chartContainer", {
			
			animationEnabled: true,
			title:{
				text: "Showing Top 10 Defects Ranked By Number Of Defects On Style"
			},
			axisY: {
				title: "Defect",
				includeZero: true
				//prefix: "$",
				//suffix:  "k"
			},
			data: [{
				type: "bar",
				//yValueFormatString: "$#,##0K",
				indexLabel: "{y}",
				indexLabelPlacement: "inside",
				indexLabelFontWeight: "bolder",
				indexLabelFontColor: "white",
				dataPoints: eval(Dataset)
			}]
		});
		chart.render();
		 
   //} //button chk
	
}
//Sewing Dhu Per
function canvas_dhu_line_chart(Dataset)
 {
 	//alert(Dataset);
    $("#dhu_show").show();
    var chart = new CanvasJS.Chart("container_style", {
    	title: {
    		text: " DHU "
    	},
    	axisY: {
    		title: "DHU %"
    	},
    	data: [{
    		type: "line",
    		dataPoints: eval(Dataset)
    	}]
    });
    chart.render();
	
}

function canvas_chart_mm(cuting,sewing,finishing)
{
	$(function () {
    // Create the chart
    $('#container_dhu').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'DHU  %:'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total DHU'
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:1f}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b> of total<br/>'
        },

        series: [{
            name: 'DHU',
            colorByPoint: true,
            data: [{
                name: 'Cut',
                y: eval(cuting),
                drilldown: ''
            }, {
                name: 'Sew',
                y:  eval(sewing),
                drilldown: ''
            }, {
                name: 'Fin.',
                y: eval(finishing),
                drilldown: ''
            }]
        }],
        drilldown: {
            series: [{
                name: 'Issue Recevied Today',
                id: 'Issue Recevied Today',
                data: [
                    [eval(cuting)]
                ]
            }, {
                name: 'Feedback Given',
                id: 'Feedback Given',
                data: [
                    [eval(sewing)
                    ]
                ]
            }, {
                name: 'Feedback Pending',
                id: 'FeedBack Pending',
                data: [
                    [eval(finishing)
                    ]
                ]
            } ]
        }
    });
});

}

function canvas_ratio_chart_mm(c_to_ratio,f_to_ratio)
{
	$(function () {
    // Create the chart
    $('#container_ratio').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Ratio  %:'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: ''
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 1,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:1f}'
                }
            }
			
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b> of total<br/>'
        },

        series: [{
            name: 'Ratio',
            colorByPoint: true,
            data: [{
				 y: eval(c_to_ratio),
                name: 'C To S Ratio('+eval(c_to_ratio)+'%)',
				className: 'highcharts-color-bad',
               
                drilldown: ''
            }, {
                y:  eval(f_to_ratio),
				 name: 'F To S Ratio('+eval(f_to_ratio)+'%)',
				// className: 'highcharts-color-good',
                drilldown: ''
            }]
        }],
        drilldown: {
            series: [{
                name: '',
                id: '',
                data: [
                    []
                ]
            }, {
                name: '',
                id: '',
                data: [
                    []
                ]
            } ]
        }
    });
});

}
function canvas_dhu_line_chart_mm(sew_dhu_per,reject_rate_per,defect_rate_per,rft_defect,sew_reject_qty,sewing_defect_qty,rft_per,sew_out)
 {
	 
	 $("#dhu_show").show();
	// alert(defect_rate_per);
	// document.getElementById(dhu_per).value =sew_dhu_per;
	document.getElementById("dhu_per").innerHTML =sew_dhu_per;
	document.getElementById("rft_per").innerHTML =rft_per;
	document.getElementById("rej_per").innerHTML =reject_rate_per;
	document.getElementById("defect_per").innerHTML =defect_rate_per;
	
	document.getElementById("defect_dhu_per").innerHTML =sewing_defect_qty;
	document.getElementById("defect_rft_per").innerHTML =rft_defect;
	document.getElementById("defect_rej_per").innerHTML =sew_reject_qty;
	document.getElementById("defect_defect_per").innerHTML =sewing_defect_qty;
	document.getElementById("pass_qty").innerHTML =sew_out;
	// $("#dhu_per").val(sew_dhu_per);
 }

 </script>
 

<style>
#defect_dhu_box{  
  width: 110px;
  height: 40px;
  border: 1px solid red;
 
}
#defect_rft_box{  
  width: 110px;
  height: 40px;
  border: 1px solid red;
 
}
#defect_rej_box{  
  width: 110px;
  height: 40px;
  border: 1px solid red;
 
}
#defect_defect_box{  
  width: 110px;
  height: 40px;
  border: 1px solid red;
 
}

.highcharts-color-good {
  fill: green;
}
.highcharts-color-bad {
  fill: red;
}

</style>
   <!-- <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
   <script src="../../canvasjs-3.2.11/canvasjs.min.js"></script>
   <script src="../../chart/highcharts.js"></script>
	<script src="../../chart/data.js"></script>
    <script>
		//set_multiselect('cbo_company_name','0','0','0','0');
		//set_multiselect('cbo_task_group','0','0','0','0');
    </script>
    <style>
	.canvasjs-chart-credit{ display:none;
		}
		.canvasjs-chart-credit{ display:none;
		}
	</style> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
 
 
 

 
 

