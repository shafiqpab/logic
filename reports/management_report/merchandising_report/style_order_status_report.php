<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Order Status Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10-01-2015
Updated by 		:   Samiur		
Update date		: 	09-03-2020	   
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
echo load_html_head_contents("Style Order Closing Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		var txt_style_ref = $("#txt_style_ref").val();
		
		var txt_order = $("#txt_order").val();
		var cbo_date_category = $("#cbo_date_category").val();
		//var txt_ex_date_form = $("#txt_ex_date_form").val();
		//var txt_ex_date_to = $("#txt_ex_date_to").val();
		if(type!=1 && cbo_date_category==3)
		{
			alert('Only For Show Button');return;
		}
		
		var txt_ref_no = $("#txt_ref_no").val();
		var txt_file_no = $("#txt_file_no").val();
		
		if(txt_style_ref!="" || txt_order!="" || txt_ref_no!="" || txt_file_no!="" || txt_booking_no!="")
		{
			if(form_validation('cbo_company_name*cbo_search_type','Company Name*Search Type')==false)
			{
				return;
			}
		}
		else
		{
			if(type==1 || type==2 || type==4 || type==5 || type==6 || type==7)
			{
				if(cbo_date_category==3)
				{
					if(form_validation('cbo_company_name*cbo_search_type*txt_date_from*txt_date_to','Company Name*Search Type*Form Date*To Date')==false)
					{
						return;
					}
				}
				else
				{
					if(form_validation('cbo_company_name*cbo_search_type*txt_date_from*txt_date_to','Company Name*Search Type*Shipment Form Date*Shipment To Date')==false)
					{
						return;
					}
				}
			}
			else 
			{
				if(txt_style_ref!="" || txt_order!="" || txt_ref_no!="" || txt_file_no!="" ||txt_booking_no !="")
				{
					if(form_validation('cbo_company_name*cbo_search_type','Company Name*Search Type')==false)
					{
						return;
					}
				}
				else
				{
					if(form_validation('cbo_company_name*txt_style_ref','Company Name*Job No')==false)
					{
						return;
					}	
				}
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*txt_ref_no*cbo_date_category',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type==2)
		{
			var data="action=order_report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*cbo_date_category*txt_ref_no',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type==3) {
			var data="action=job_report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*cbo_date_category*txt_ref_no',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type==4) {
			var data="action=job_report_generate_new"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*cbo_date_category*txt_ref_no',"../../../")+'&report_title='+report_title+'&type='+type;
		}	else if(type==5) {
			var data="action=report_generate5"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*txt_ref_no*cbo_date_category',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type==6)
		{
			var data="action=report_generate6"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*txt_ref_no*cbo_date_category',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type==7)
		{
			var data="action=order_report_generate7"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_order*txt_order_id*txt_date_from*txt_date_to*cbo_date_category*txt_ref_no*txt_booking_no',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/style_order_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[3]);
			console.log(reponse);
			$('#report_container2').html(reponse[0]);
			//var tot_rows=$('#table_body tr').length;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			/*document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';*/
			/*if(tot_rows>1)
			{*/
			//alert(reponse[2]+'='+reponse[3]);
			if(reponse[3]==1)
			{
				if(reponse[2]==1)
				{
					var tableFilters = {
						col_operation: {
						id: ["td_order_qty","td_order_val","td_yarn_req_qty","td_yarn_issIn_qty","td_yarn_issOut_qty","td_yarn_trnsInq_qty","td_yarn_trnsOut_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_trnsIn_qty","td_grey_transOut_qty","td_grey_qty","td_grey_rec_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_wovenReqQty","td_wovenRecQty","td_wovenRecBalQty","td_wovenIssueQty","td_wovenIssueBalQty","td_gmt_qty","td_cutting_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty","td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_washRcvBal_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtrej_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_shortExcess_exFactory_qty","td_prLoss_qty","td_prLossDye_qty","td_prLossCut_qty"],
						col: [6,8,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,75,76,77,78,79,80],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
					setFilterGrid("table_body",-1,tableFilters);
				}
				else
				{
					var tableFilters = {
						col_operation: {
						id: ["td_order_qty","td_yarn_req_qty","td_yarn_issIn_qty","td_yarn_issOut_qty","td_yarn_trnsInq_qty","td_yarn_trnsOut_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_trnsIn_qty","td_grey_transOut_qty","td_grey_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_gmt_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty",	"td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_washRcvBal_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_rjtPrint_qty","td_leftOverFin_qty","td_leftOverGmtFin_qty","td_leftOverTrm_qty","td_rjtPrint_qty","td_rjtEmb_qty","td_rjtSew_qty","td_rjtFin_qty","td_prLoss_qty","td_prLossFin_qty","td_prLossCut_qty"],
						col: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,	46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[3]==2)
			{
				if(reponse[2]==1)
				{  
				//alert("right.");
					var tableFilters2 = 
					{
						col_operation: 
						{ //td_fin_issRet_qty
							id: ["td_order_qty","td_yarn_req_qty","td_yarn_req_qty_booking","td_yarn_issIn_qty","td_yarn_issRetIn_qty","td_yarn_issReject_qty","td_yarn_issOut_qty","td_yarn_issRetout_qty","td_yarn_issue_qty","td_yarn_issRettot_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_qty","td_grey_knit_under_overPord","td_grey_processLossInSide","td_grey_total_processLossOutSide","td_grey_total_processLossKG","td_grey_rec_qty","td_grey_reject_fab_qty","td_grey_rec_qty_outside","td_grey_rec_qty_inside_trans","td_grey_rec_qty_outside_trans","td_grey_rec_qty_total","td_recv_under_over_prod_qty","td_grey_issDye_qty_inside","td_grey_issDye_qty_outside","td_grey_issDye_qty_total","td_grey_lftOver_qty","td_aop_req_qty","td_aop_deli_qty","td_aop_rec_qty","td_aop_balance_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_total_prod_qty","td_fin_prod_under_over_qty","td_fin_prod_process_loss_inside_qty","td_fin_prod_process_loss_outside_qty","td_fin_total_knit_process_qty","td_fin_recv_qty_inside","td_fin_recv_qty_outside","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_issRet_qty","td_fin_lftOver_qty","td_wovenReqQty","td_wovenRecQty","td_wovenRecBalQty","td_wovenIssueQty","td_wovenIssueBalQty","td_gmt_qty","td_cutting_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_embroIssueIn_qty","td_embroIssueSuncon_qty","td_embroIssueTotal_qty","td_embroRcvIn_qty","td_embroRcvSubcon_qty","td_embroRcvTotal_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty","td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtrej_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_shortExcess_exFactory_qty","td_prLoss_qty","td_prLossDye_qty","td_prLossCut_qty"],
							col: [8,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,105,106],
							operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
							write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
					setFilterGrid("table_body2",-1,tableFilters2);
				}
				else
				{
					
				}
				//setFilterGrid("table_body2",-1,tableFilters2);
			}
			else if(reponse[3]==3)
			{
				setFilterGrid("table_body3",-1);
			}
			else if(reponse[3]   ==4)//show button 4
			{
				var tableFilters4= {
					col_operation: {
					id           : ["td_order_qty","td_yarn_req_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_gmt_qty","td_cutting_qty","td_printIssue_qty","td_printRcv_qty","td_printRjt_qty","td_sewIn_qty","td_sewInBal_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtrej_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_shortExcess_exFactory_qty","td_prLoss_qty","td_prLossDye_qty","td_prLossCut_qty"],
					
					col          : [6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30, 32,33,34,35,36,37,38,39,40,41,42,43,44],
					operation    : ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method : ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body2",-1,tableFilters4);	
				
			}
			else if(reponse[3]   ==7)//show button 7
			{
				
				var tableFilters7= {
					col_operation: {
					id           : ["td_order_qty","td_yarn_req_qty","td_grey_req_qty","tot_yarn_issue_qty","td_grey_qty","td_knit_processLogssKg","td_grey_rec_qty","td_gray_sub_total","td_gray_blnce_total","td_b_qty","td_fin_req_qty","td_total_rv","td_total_feb_trans","td_total_cutting_feb_recv","td_total_del_issue_qty","td_total_feb_blnc","td_cutting_qty","td_total_rej_qty","td_printIssue_qty","td_gmt_qt","td_total_tot_emb_recvQty","td_total_rej_gmt_qty","td_printRjt_qty","td_sewIn_qty","td_total_sew_total","td_total_fin_iron","td_total_packing_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty"],
					
					col          : [7,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,25,26,28,29,30,31,32,33,34,35,36,37,38],
					operation    : ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method : ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body_7",-1,tableFilters7);	
				
			}
	 		show_msg('3');
		}
	}
	
	function new_window(type)
	{
		
		//alert(type);
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=3)
		{
			$('#table_body tr:first').hide();
			$('#table_body2 tr:first').hide();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type!=3)
		{
			$('#table_body tr:first').show();
			$('#table_body2 tr:first').show();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	function new_window2()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage_style()
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
		var page_link='requires/style_order_status_report_controller.php?action=style_refarence_surch&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	function openmypage_Booking()
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
		var txt_booking_no = $("#txt_booking_no").val();
		var txt_order_id = $("#txt_order_id").val();
		//alert( txt_order_id);
		var page_link='requires/style_order_status_report_controller.php?action=style_refarence_surch_for_booking&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year+'&txt_booking_no='+txt_booking_no+'&txt_order_id='+txt_order_id;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_booking_no").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage_order(type_id)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/style_order_status_report_controller.php?action=order_surch&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type_id='+type_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			//alert(type_id);
			if(type_id==1)
			{
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
			}
			else
			{
				$("#txt_ref_no").val(style_des); 
			}
		}
	}

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_order').attr("disabled",true);
		}
		else
		{
			$('#txt_order').attr("disabled",false);
		}
	}
	function fn_date_chack(str)
	{
		var search_type=$("#cbo_search_type").val();
		if(search_type==1)
		{
			var ship_date=$('#txt_date_from').val();
			if(ship_date!="")
			{
				$('#txt_ex_date_form').val("");
				$('#txt_ex_date_to').val("");
			}
		}
		else
		{
			var ex_fact_date=$('#txt_ex_date_form').val();
			if(ex_fact_date!="")
			{
				$('#txt_date_from').val("");
				$('#txt_date_to').val("");
			}
		}
	}
	
	function open_trims_dtls(po_break_down_id,tot_po_qnty,ratio,page_title,action)
	{
		//alert(po_break_down_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?po_break_down_id='+po_break_down_id+'&tot_po_qnty='+tot_po_qnty+'&ratio='+ratio+'&action='+action, page_title, 'width=670px,height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_ex_factory_popup(action,job,id,width)
	{
		var cbo_date_category=$("#cbo_date_category").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?action='+action+'&job='+job+'&id='+id+'&cbo_date_category='+cbo_date_category+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
		function generate_batch_popup(action,job,id,width)
	{
		
		var cbo_date_category=$("#cbo_date_category").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?action='+action+'&job='+job+'&id='+id+'&cbo_date_category='+cbo_date_category+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_ex_factory_popup_show4(action,job,id,width,from,to)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?action='+action+'&job='+job+'&id='+id+'&from='+from+'&to='+to, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_rej(po_id,company,action,reportType)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?po_id='+po_id+'&company='+company+'&action='+action+'&reportType='+reportType, 'Reject Quantity', 'width=600px,height=350px,center=1,resize=0,scrolling=0','../../');
	}


	function generate_popup(action,company,job_no,po_id,title)
	{
		// alert(action+'*'+company+'*'+po_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?po_id='+po_id+'&job_no='+job_no+'&company='+company+'&action='+action, title, 'width=600px,height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function fn_order_date_type(type_id)
	{
		
		if(type_id==1)
		{
			document.getElementById('search_td').innerHTML="Shipment Date";
			$('#search_td').css('color','blue');
		}
		else if(type_id==2)
		{
			document.getElementById('search_td').innerHTML="Ex-factory Date";
			$('#search_td').css('color','blue');
		}
		else if(type_id==3)
		{
			document.getElementById('search_td').innerHTML="Ref.Closing Date";
			$('#search_td').css('color','blue');
		}
		else
		{
			document.getElementById('search_td').innerHTML="Shipment Date";
		}
	}

	function fnc_open_view(action,title,company_id,job_id,job_no,po_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_order_status_report_controller.php?job_id='+job_id+'&job_no='+job_no+'&po_id='+po_id+'&company_id='+company_id+'&action='+action, title, 'width=1000px,height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function print_button_setting()
	{
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/style_order_status_report_controller' );
	}            
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1230px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1470px;">
                <table class="rpt_table" width="1470" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="130">Company Name</th>
                        <th width="130">Buyer Name</th>
                        <th width="100" class="must_entry_caption">Type</th>
                        <th width="50">Job Year</th>
						<th  width="100">Booking No</th>
                        <th  width="100">Job No</th>
                        <th  width="120">Order No</th>
                        <th  width="80">Internal Ref.</th>
                        <th  width="80">File No</th>

                        <th width="160" class="must_entry_caption">Date Category</th>
                        <th width="160" class="must_entry_caption" id="search_td">Shipment Date</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:50px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_order_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );print_button_setting()" );
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td>
							<?
                            $search_style_arr=array(1=>"Order Wise");//,2=>"Style/Job Wise"
                            echo create_drop_down( "cbo_search_type", 100, $search_style_arr,"", 0,"", 1, "fn_order_disable(this.value);",0,"" ); 
                            ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	</td>
						<td align="center">
                            <input style="width:100px;" name="txt_booking_no" id="txt_booking_no" onDblClick="openmypage_Booking()" class="text_boxes" placeholder="Browse" readonly/>
							<input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>                
                        </td>
                        <td align="center">
                            <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Browse" readonly/>
                         
                        </td>
                        <td align="center">
                            <input style="width:120px;" name="txt_order" id="txt_order" onDblClick="openmypage_order(1)" class="text_boxes" placeholder="Browse" readonly />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                        </td>
                        <td align="center">
                            <input style="width:80px;" name="txt_ref_no" id="txt_ref_no"  class="text_boxes" placeholder="Write" />   
                                        
                        </td>
                        <td align="center">
                            <input style="width:80px;" name="txt_file_no" id="txt_file_no"  class="text_boxes" placeholder="Write" />   
                                        
                        </td>
                          <td>
							<?
                            $search_type_arr=array(1=>"Shipment Date",2=>"Ex-factory Date",3=>"Ref.Closing Date"); 
                            echo create_drop_down( "cbo_date_category", 100, $search_type_arr,"", 0,"", 1, "fn_order_date_type(this.value);",0,"" ); 
                            ?></td>
                            
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px"  readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
							
                        </td>
                        <!--<td>
                            <input type="text" id="txt_ex_date_form" name="txt_ex_date_form" class="datepicker" style="width:60px" onChange="fn_date_chack(2)" readonly>To
                            <input type="text" id="txt_ex_date_to" name="txt_ex_date_to" class="datepicker" style="width:60px" readonly>
                        </td>-->
						<td id="button_data_panel" align="center">
						    <input type="button" id="show_button1" class="formbutton" style="width:43px;display:none" value="Show" onClick="fn_report_generated(1)"/>
							<input type="button" id="show_button2" class="formbutton" style="width:43px;display:none" value="Show 2" onClick="fn_report_generated(2)"/>
							<input type="button" id="show_button3" class="formbutton" style="width:43px;display:none" value="Show 3" onClick="fn_report_generated(3)"/>
							<input type="button" id="show_button4" class="formbutton" style="width:43px;display:none" value="Show 4" onClick="fn_report_generated(4)"/>
							<input type="button" id="show_button5" class="formbutton" style="width:43px;display:none" value="Show 5" onClick="fn_report_generated(5)"/>
							<input type="button" id="show_button6" class="formbutton" style="width:43px;display:none" value="Show 6" onClick="fn_report_generated(6)"/>
							<input type="button" id="show_button7" class="formbutton" style="width:43px;display:none" value="Show 7" onClick="fn_report_generated(7)"/>
						</td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>