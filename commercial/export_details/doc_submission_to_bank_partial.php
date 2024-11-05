<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Doc. Submission to Bank Partial				
Functionality	:				
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	 
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
$commercial_head_reverse=array();
foreach($commercial_head as $key=>$val)
{
	$commercial_head_reverse[csf($val)]=csf($key);
	//$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
}
echo load_html_head_contents("Doc. Submission to Bank Partial","../../", 1, 1, $unicode,'','');
?>	
 
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';

	var str_commercial_head = [<?  echo substr(return_library_autocomplete_fromArr( $commercial_head ), 0, -1); ?>];
	function add_auto_complete(i)
	{
		$("#cbo_account_head_"+i).autocomplete({
			source: str_commercial_head
		});
	}

	function fn_value_check(seq_no,fld_value,fld_names)
	{
		//alert(fld_names+"="+seq_no+"="+commercial_head_reverse[fld_value]);return;
		if (form_validation('cbo_company_name','Company')==false )
		{
			$('#'+fld_names+'_'+seq_no).val("");
			return;
		}
		fld_value=fld_value.toUpperCase();
		
		var commercial_head_reverse = JSON.parse('<?  echo json_encode($commercial_head_reverse); ?>');
		//alert(fld_value+"="+commercial_head_reverse[fld_value]);
		if(commercial_head_reverse[fld_value]=="" || commercial_head_reverse[fld_value]==undefined)
		{
			alert("Transaction Account Head Not Found");
			$('#'+fld_names+'_'+seq_no).val("").attr('title','0').focus();
		}
		else
		{
			//alert(fld_names+"="+seq_no+"="+commercial_head_reverse[fld_value]);
			$('#'+fld_names+'_'+seq_no).attr('title',commercial_head_reverse[fld_value]);
		}
		get_php_form_data(commercial_head_reverse[fld_value]+'**'+seq_no+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_lien_bank').value, 'populate_acc_loan_no_data', 'requires/doc_submission_to_bank_partial_controller' );
	}

	function fn_commercial_head_display(seq_no,fld_name)
	{
		if (form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var page_link='requires/doc_submission_to_bank_partial_controller.php?action=commercial_head_popup';
		var title='Account Head';
		$('#'+fld_name+"_"+seq_no).removeAttr('onblur');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=380px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hdn_head_id=this.contentDoc.getElementById("hdn_head_id").value;
			var hdn_head_val=this.contentDoc.getElementById("hdn_head_val").value;
			$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr('title',hdn_head_id).attr("onblur","fn_value_check("+seq_no+",this.value,"+"'"+fld_name+"')");
			get_php_form_data(hdn_head_id+'**'+seq_no+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_lien_bank').value, 'populate_acc_loan_no_data', 'requires/doc_submission_to_bank_partial_controller' );
			//$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr("onblur","fn_value_check("+seq_no+","+head_from+",this.value,"+"'"+fld_name+"')");
		}
	}

	function change_caption(import_btb)
	{
		if(import_btb==1)
		{
			document.getElementById('buyer_td').innerHTML='<? echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, " Display ", 0, "",1 ); ?>';
		}
		else
		{
			document.getElementById('buyer_td').innerHTML='<? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 ); ?>';
		}
	}

	function fnc_export_doc_sub_entry(operation)
	{
		
		
		if (form_validation('cbo_company_name*cbo_buyer_name*lcsc_no*txt_submit_date*txt_negotiation_date','Company Name*Buyer Name*LC/SC No*Submission Date*Nagotiate Date')==false )
		{
			return;
		}
	
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][40]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][40]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][40]); ?>')==false) {return;}
		}
				
		var txtbalance_neg_amount=$('#txtbalance_neg_amount').val()*1;
		var total_foreign_curr_hid=$('#total_foreign_curr_hid').val()*1;

		// alert(txt_total+"__"+total_foreign_curr_hid);
		// return
		if(txtbalance_neg_amount<total_foreign_curr_hid)
		{
			alert("Total LC/SC Currency Exceeds Total Balance Neg. Amount.");
			return;
		}
		
		var dataString="";$('ss').attr('title')
		var transRow = $('#trans_details tbody tr').length;	
		for(var i=1;i<=transRow;i++)
		{
			try
			{
				if (form_validation('cbo_account_head_'+i+'*'+'txt_domestic_curr_'+i+'*'+'txt_conversion_rate_'+i+'*'+'txt_negotiation_dtls_date_'+i,'Account Head*Domestic Currency*Conversation Rate*Negotiation Date')==false )
				{
					return;
				}
				dataString += "&cbo_account_head_"+i+"="+$("#cbo_account_head_"+i).attr('title');
				dataString += "&txt_negotiation_dtls_date_"+i+"="+$("#txt_negotiation_dtls_date_"+i).val();
				dataString += "&txt_ac_loan_no_"+i+"="+$("#txt_ac_loan_no_"+i).val();
				dataString += "&txt_loan_no_"+i+"="+$("#txt_loan_no_"+i).val();
				dataString += "&txt_domestic_curr_"+i+"="+$("#txt_domestic_curr_"+i).val();
				dataString += "&txt_conversion_rate_"+i+"="+$("#txt_conversion_rate_"+i).val();
				dataString += "&txt_lcsc_currency_"+i+"="+$("#txt_lcsc_currency_"+i).val();
			}
			catch(e)
			{
				//got error no operation
			}
		}
	// alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&transRow='+transRow+dataString+get_submitted_data_string('cbo_company_name*cbo_buyer_name*lcsc_no*lc_sc_id*txt_submit_date*cbo_submit_to*txt_bank_ref*txt_bank_ref_date*cbo_submission_type*txt_negotiation_date*txt_day_to_realize*txt_possible_reali_date*courier_receipt_no*txt_courier_company*txt_courier_date*txt_bnk_to_bnk_cour_no*txt_bnk_to_bnk_cour_date*cbo_lien_bank*cbo_currency*cbo_pay_term*txt_remarks*total_dom_curr_hid*total_foreign_curr_hid*mst_tbl_id*invoice_tbl_id*invoice_id_string*txt_import_btb*txt_issue_bank_info_dtls*txt_ref_id*txt_partial_sys_id',"../../");
		// alert(data);return;
		freeze_window(operation);		
		http.open("POST","requires/doc_submission_to_bank_partial_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_export_doc_sub_entry_Reply_info;
	}


	function fnc_export_doc_sub_entry_Reply_info()
	{
		$("#btn_print_letter").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#btn_print_letter").addClass( "formbutton"); //To make enable print to button
		if(http.readyState == 4) 
		{
			//release_freezing();alert(http.responseText);return;
			var reponse=http.responseText.split('**');	
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}		 
			
			if( reponse[0]==0 || reponse[0]==1)
			{		
				//get_php_form_data(reponse[1], "populate_master_from_data", "requires/doc_submission_to_bank_partial_controller");
				//get_php_form_data(reponse[2]+"**"+reponse[1], "populate_master_from_data_par", "requires/doc_submission_to_bank_partial_controller");
				$('#txt_partial_sys_id').val(reponse[1]);
				set_button_status(1, permission, 'fnc_export_doc_sub_entry',1); 
				show_msg(trim(reponse[0])); 
				release_freezing();			 
			}
			if(reponse[0]==2)
			{
				$('#transaction_container').html("");
				$('#invoice_container').html("");
				$('#total_dom_curr_hid').val("");
				$('#total_foreign_curr_hid').val("");
				// reset_form('docsubmFrm_1','','','','$(\'#transaction_container tr:not(:first)\').remove();','');
				set_button_status(0, permission, 'fnc_export_doc_sub_entry',1); 
				show_msg(trim(reponse[0])); 
				release_freezing();			 
			}
			else if(reponse[0]==35)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else
			{
				show_msg(trim(reponse[0])); 
				release_freezing();
			}
			//fnResetForm();
			release_freezing();
			$(".printReport").removeClass( "formbutton_disabled");//To make disable print to button
			$(".printReport").addClass( "formbutton");//To make enable print to button
			
		}
	}


	function pop_doc_submission()
	{
		if (form_validation('cbo_company_name*cbo_submission_type','Company Name*submission type')==false )
		{
			return;
		}
		var company_name = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/doc_submission_to_bank_partial_controller.php?action=doc_sub_popup&company_name='+company_name+'&buyer_name='+buyer_name; 
		var title="Search System Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mstID=this.contentDoc.getElementById("hidden_system_number").value.split("**"); // master table id
			$("#cbo_submission_type").val('');
			$("#cbo_submission_type").attr("disabled",false);
			$("#txt_negotiation_date").attr("disabled",false);
			$("#is_posted_account").val(mstID[2]);
			if( mstID[2]==1 ) $("#posted_status_td").text("Already Posted In Accounting.");
			else $("#posted_status_td").text("");
								
			get_php_form_data(mstID[0], "populate_master_from_data", "requires/doc_submission_to_bank_partial_controller");
			$('#dtls_sub_ref_id').val(mstID[1]);
			// set_button_status(1, permission, 'fnc_export_doc_sub_entry',1);	
			release_freezing();
			//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
			//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
			$(".printReport").removeClass( "formbutton_disabled"); //To make disable print to button
			$(".printReport").addClass( "formbutton"); //To make enable print to button	
		}
	}
	
	function pop_doc_submission_par()
	{
		if (form_validation('cbo_company_name*cbo_submission_type','Company Name*submission type')==false )
		{
			return;
		}
		var company_name = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/doc_submission_to_bank_partial_controller.php?action=doc_sub_popup_par&company_name='+company_name+'&buyer_name='+buyer_name; 
		var title="Search System Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mstID=this.contentDoc.getElementById("hidden_system_number").value.split("**"); // master table id
			$("#cbo_submission_type").val('');
			$("#cbo_submission_type").attr("disabled",false);
			$("#txt_negotiation_date").attr("disabled",false);
			$("#is_posted_account").val(mstID[2]);
			$("#txt_partial_sys_id").val(mstID[3]);
			if( mstID[2]==1 ) $("#posted_status_td").text("Already Posted In Accounting.");
			else $("#posted_status_td").text("");
								
			get_php_form_data(mstID[0]+"**"+mstID[3], "populate_master_from_data_par", "requires/doc_submission_to_bank_partial_controller");
			$('#dtls_sub_ref_id').val(mstID[1]);
			set_button_status(1, permission, 'fnc_export_doc_sub_entry',1); 
			release_freezing();
			//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
			//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
			$(".printReport").removeClass( "formbutton_disabled"); //To make disable print to button
			$(".printReport").addClass( "formbutton"); //To make enable print to button	
		}
	}
		
	function fn_negotiation()
	{	     
		var submit_type = $('#cbo_submission_type').val();
		if(submit_type=='1')
		{
			$('#txt_negotiation_date').attr('disabled','disabled');
			$('#txt_negotiated_ammount').attr('disabled','disabled');
			$('#transaction_container').find('tr').remove();
			$('#transaction_container').append('<tr id="tr0"><td colspan="7"><b><center>Please Select LC/SC to view Transaction</center></b></td> </tr>');
		}                       
		else if(submit_type=='2')
		{
			var rowNo=0;
			$('#txt_negotiation_date').removeAttr('disabled');
			$('#txt_negotiated_ammount').removeAttr('disabled');
			var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/doc_submission_to_bank_partial_controller" );
			$('#transaction_container').find('tr').remove();
			$('#transaction_container').append(response);
		}
	}
		
	function fn_inc_decr_row(DelrowNo,type)
	{
		if(type=='decrease')
		{
			$('#total_foreign_curr_hid').val(number_format(($('#total_foreign_curr_hid').val()*1)-($('#txt_lcsc_currency_'+DelrowNo).val()*1),4,".",""));
			$('#total_dom_curr_hid').val(number_format(($('#total_dom_curr_hid').val()*1)-($('#txt_domestic_curr_'+DelrowNo).val()*1),4,".",""));
		}
		
		var rownumber = $("#transaction_container tr:last").attr('id');
		var rowNo = rownumber.substr(2,rownumber.length);
		if(type=='increase')
		{
			var last_rate=$('#txt_conversion_rate_'+DelrowNo).val();
			var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/doc_submission_to_bank_partial_controller" );
				$('#transaction_container').append(response);
			var last_row_no=DelrowNo+1;
			$('#txt_conversion_rate_'+last_row_no).val(last_rate);
		}
		else if(type=='decrease')
		{
		var tblRow = $("#transaction_container tr").length; 
		if(tblRow*1!=1 && rowNo==DelrowNo)
		{ 
				$("#tr"+DelrowNo).remove();
		}
		}		
	}	
		
	function realy_days(field_id)
	{   
	var from_date = $('#txt_bank_ref_date').val();
	var days_to_realize = $('#txt_day_to_realize').val();
	var to_date = $('#txt_possible_reali_date').val();	  
	days_to_realize = days_to_realize*1-1;
	
	if(days_to_realize=="" || days_to_realize*1==-1) return;
	if(from_date !="" )
	{		
			if( (field_id == 'txt_day_to_realize' || field_id == 'txt_bank_ref_date') && days_to_realize!="")
			{ 
				var res_date = add_days( from_date, days_to_realize );
				$('#txt_possible_reali_date').val(res_date);
			}
			else if(field_id == 'txt_possible_reali_date' &&  to_date!="")
			{ 				 
				var datediff = date_diff( 'd', from_date, to_date )+1;					 
				$('#txt_day_to_realize').val(datediff);
			}
			
	}               
	}	
		
	function fn_calculate(id,rowNo)
	{
		var domesticCurr 	= $("#txt_domestic_curr_"+rowNo).val();
		var domesticRate 	= $("#txt_conversion_rate_"+rowNo).val();
		var lcscCurr 		= $("#txt_lcsc_currency_"+rowNo).val();
			
		if(id=="txt_domestic_curr_"+rowNo)
		{
			if( ($("#txt_conversion_rate_"+rowNo).val()=="" || $("#txt_conversion_rate_"+rowNo).val()==0) && ( $("#txt_lcsc_currency_"+rowNo).val()!="" || $("#txt_lcsc_currency_"+rowNo).val()>0) )
			{
				domesticRate = domesticCurr*1/lcscCurr*1;
				$("#txt_conversion_rate_"+rowNo).val(number_format_common(domesticRate,4,'',''));
			}
			else if( ($("#txt_conversion_rate_"+rowNo).val()!="" || $("#txt_conversion_rate_"+rowNo).val()>0) && ($("#txt_lcsc_currency_"+rowNo).val()=="" || $("#txt_lcsc_currency_"+rowNo).val()==0))
			{
				lcscCurr = domesticCurr*1/domesticRate*1;
				$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
			}
			else
			{
				lcscCurr = domesticCurr*1/domesticRate*1;
				$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
			}
		} 
		else if(id=="txt_conversion_rate_"+rowNo)
		{
			if( ($("#txt_domestic_curr_"+rowNo).val()=="" || $("#txt_domestic_curr_"+rowNo).val()==0) && ($("#txt_lcsc_currency_"+rowNo).val()!="" || $("#txt_lcsc_currency_"+rowNo).val()>0) )
			{
				domesticCurr = domesticRate*1*lcscCurr*1;
				$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,4,'',''));
			}
			else if( ($("#txt_domestic_curr_"+rowNo).val()!="" || $("#txt_domestic_curr_"+rowNo).val()>0) && ($("#txt_lcsc_currency_"+rowNo).val()=="" || $("#txt_lcsc_currency_"+rowNo).val()==0) )
			{
				lcscCurr = domesticCurr*1/domesticRate*1;
				$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
			}
			else
			{
				lcscCurr = domesticCurr*1/domesticRate*1;
				$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
			}
		} 
		else if(id=="txt_lcsc_currency_"+rowNo)
		{ 
			if( ($("#txt_domestic_curr_"+rowNo).val()=="" || $("#txt_domestic_curr_"+rowNo).val()==0) && ($("#txt_conversion_rate_"+rowNo).val()!="" || $("#txt_conversion_rate_"+rowNo).val()>0) )
			{
				domesticCurr = lcscCurr*1*domesticRate*1;
				$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,5,'',''));
			}
			else if( ($("#txt_domestic_curr_"+rowNo).val()!="" || $("#txt_domestic_curr_"+rowNo).val()>0) && ($("#txt_conversion_rate_"+rowNo).val()=="" || $("#txt_conversion_rate_"+rowNo).val()==0) )
			{
				domesticRate = domesticCurr*1/lcscCurr*1;
				$("#txt_conversion_rate_"+rowNo).val(number_format_common(domesticRate,4,'',''));
			}
			else
			{
				domesticCurr = lcscCurr*1*domesticRate*1;
				$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,5,'',''));
			}
		} 
		//total domestic and foreign currency function call
		sum_of_currency();
	}	
		
	function sum_of_currency()
	{
		//total domestic and foreign currency
		var txtbalance_neg_amount=$("#txtbalance_neg_amount").val()*1;
		var totalDomCurr = totalForeignCurr = 0;
		$("#trans_details tbody tr").each(function() {
			totalDomCurr += $(this).find("input[name='txt_domestic_curr[]']").val()*1; 
			totalForeignCurr += $(this).find("input[name='txt_lcsc_currency[]']").val()*1; 
		});
		
		if(totalForeignCurr>txtbalance_neg_amount)
		{
			alert("Total Negotiate Amount Not Allow Over Balance");
			$("#trans_details tbody tr").each(function() {
				$(this).find("input[name='txt_domestic_curr[]']").val("");
				$(this).find("input[name='txt_lcsc_currency[]']").val("");
			});
			$("#total_dom_curr_hid").val("");
			$("#total_foreign_curr_hid").val("");
			return;
		}
		
		$("#total_dom_curr_hid").val(number_format_common(totalDomCurr,4,'',''));
		$("#total_foreign_curr_hid").val(number_format_common(totalForeignCurr,5,'',''));
		
	}	
		
	//reset/refresh function 
	function fnResetForm()
	{
		reset_form('docsubmFrm_1','','','','$(\'#transaction_container tr:not(:first)\').remove();','');
		$('#invoice_container').append('<tr id="tr0"><td colspan="6"><b><center>Please Select LC/SC to view invoice List</center></b></td></tr>');
		$('#trans_details').find('tr:gt(0)').remove();
		$('#trans_details thead').after('<tbody id="transaction_container"><tr id="tr0"><td colspan="7"><b><center>Please Select LC/SC to view Transaction</center></b></td></tr></tbody><tfoot><th colspan="3">Sum&nbsp;&nbsp;</th><td><input type="text" id="total_dom_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th>&nbsp;</th>	<td><input type="text" id="total_foreign_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th></th></tfoot>');
			set_button_status(0, permission, 'fnc_export_doc_sub_entry',1);	
		$("#posted_status_td").text("");
	}
			
	function print_to_html_report(type)
	{
		var msg="";
		if (type != 8)
		{
			if (type != 7)
			{
				if (confirm("Press  \"Cancel\"  to hide  Discounting As  Per  LC Clause \nPress  \"OK\"  to Show Discounting As  Per  LC Clause ")) 
				{
					msg=1;
				} 
				else 
				{
					msg=0;
				}
			}
		}
		
		if (form_validation('mst_tbl_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			var action='bank_submit_letter'
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('mst_tbl_id*cbo_company_name',"../../")+'&report_title='+report_title+'&type='+type+'&msg='+msg;
			http.open("POST","requires/doc_submission_to_bank_partial_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;	
		}
	}	

	function generate_trim_report_reponse(){
		if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("####");
			//  alert(file_data[0]);
			$('#data_panel').html(file_data[0]);
			if(file_data[2]==8){
				$('#print_excel19').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
					//$('#print_excel19')[0].click();
				document.getElementById('print_excel19').click();
			}
			var report_title=$( "div.form_caption" ).html();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
		}
	}
						
	function open_terms_acc_popup(title)
	{
		var txt_bank_ref=document.getElementById('txt_bank_ref').value;
		if (txt_bank_ref=="")
		{
			alert("Save The Data First")
			return;
		}	
		else
		{
			page_link='requires/doc_submission_to_bank_partial_controller.php?action=acc_popup'+get_submitted_data_string('txt_bank_ref*mst_tbl_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=470px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				//var theform=this.contentDoc.forms[0];
				//var theemail=this.contentDoc.getElementById("selected_data").value;
				//document.getElementById('trims_acc_hidden_data').value=theemail;
			}
		}
	}   
			
	function print_button_setting()
	{
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/doc_submission_to_bank_partial_controller' );
	}

	function fn_bank_info()
	{
		var txt_issue_bank_info_dtls=$('#txt_issue_bank_info_dtls').val();
		//var page_link='requires/doc_submission_to_bank_partial_controller.php?action=doc_sub_popup&company_name='+company_name+'&buyer_name='+buyer_name; 
		var page_link='requires/doc_submission_to_bank_partial_controller.php?action=bank_info_popup&txt_issue_bank_info_dtls='+txt_issue_bank_info_dtls;
		var title="Issuing Bank Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=300px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hdn_issue_bank_info_dtls").value;
			document.getElementById('txt_issue_bank_info_dtls').value=theemail;
			var txt_issue_bank_info_dtls_ref=theemail.split("__");
			document.getElementById('txt_issue_bank_info').value=txt_issue_bank_info_dtls_ref[0];
		}
	}          
	
	function calculateBalance() {
            // Get the values from the input fields
            var billValue = parseFloat(document.getElementById("txt_bill_value").value) || 0;
            var cumlNegAmount = parseFloat(document.getElementById("txt_Cuml_Neg_Amount").value) || 0;
            var balanceNegAmount = billValue - cumlNegAmount;

            document.getElementById("txtbalance_neg_amount").value = balanceNegAmount.toFixed(2);
        }
</script>
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?><br/>
        <fieldset style="width:930px; margin-bottom:10px;">
        <form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >
          <fieldset style="width:950px;">
                <legend>Commercial Module</legend>
				<table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master"> 
                	<tr>
						<td colspan="3" align="right">System Id</td>
						<td colspan="3"><input style="width:140px " name="txt_partial_sys_id" id="txt_partial_sys_id" class="text_boxes" placeholder="Browse"  ondblclick="pop_doc_submission_par();" readonly></td>
					</tr>
					<tr>
						<td  width="130" align="right" class="must_entry_caption">Company Name </td>
						<td width="170">
							<?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "print_button_setting();" );
							?> 
						</td>
						<td width="130" align="right" class="must_entry_caption" >Buyer</td>
						<td width="160" id="buyer_td">
							<? 
								//echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", $selected, "" );
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
							?>
						</td>
						<td width="130" align="right" class="must_entry_caption">LC/SC No </td>
						<td width="170">
							<input type="text" name="lcsc_no" id="lcsc_no" class="text_boxes" style="width:140px"  readonly disabled  />
							<input type="hidden" name="lc_sc_id" id="lc_sc_id" readonly />
							<input type="hidden" name="invoice_id_string" id="invoice_id_string" readonly />
							<input type="hidden" name="is_posted_account" id="is_posted_account"/>
							<input type="hidden" name="txt_import_btb" id="txt_import_btb"/>
							<input type="hidden" name="txt_ref_id" id="txt_ref_id"/>
						</td>
					</tr>
					<tr>                                       
						<td align="right" class="must_entry_caption">Submission Date</td>
						<td>
							<input style="width:140px " name="txt_submit_date" id="txt_submit_date" value="<?echo date('d-m-Y')?>" class="datepicker"  placeholder="Select Date" />
						</td>
						<td align="right" > Submitted To </td>
						<td>
							<? 
								echo create_drop_down( "cbo_submit_to", 150, $submited_to,"", 1, "-- Select --", 2, "",1 );
							?>
						</td>
						<td align="right">Bank Ref/ Bill No </td>
						<td>
							<input style="width:140px " name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" placeholder="Double Click to Update"  ondblclick="pop_doc_submission();"><input type="hidden" name="dtls_sub_ref_id" id="dtls_sub_ref_id" readonly />
						</td>
					</tr>
					<tr>
						<td align="right">Bank Ref Date </td>
						<td>
							<input type="text" name="txt_bank_ref_date" id="txt_bank_ref_date" class="datepicker" onChange="realy_days(this.id)" style="width:140px"   />
						</td>
						<td align="right" class="must_entry_caption">Submission Type </td>
						<td> 
							<? 
								echo create_drop_down( "cbo_submission_type", 150, $submission_type,"", 1, "-- Select --", 0, "fn_negotiation()","",2);
							?>
						</td>
						<td align="right">Negotiation Date</td>
						<td> 
							<input type="text" name="txt_negotiation_date" id="txt_negotiation_date" class="datepicker" style="width:140px"   />
						</td>
					</tr> 
					<tr>                                   
						<td align="right">Days to Realize </td>
						<td>
							<input type="text" name="txt_day_to_realize" id="txt_day_to_realize" class="text_boxes_numeric" style="width:140px" onChange="realy_days(this.id)" />	
						</td>
						<td align="right" >Possible Reali Date</td>
						<td>  
							<input type="text" name="txt_possible_reali_date" id= "txt_possible_reali_date" class="datepicker" onChange="realy_days(this.id)" style="width:140px" />
						</td>
						<td align="right">Courier Receipt No.</td>
						<td><input type="text" name="courier_receipt_no" id="courier_receipt_no" class="text_boxes" style="width:140px" /></td>                                   
					</tr>
					<tr>                                   
						<td align="right">Courier Company </td>
						<td><input type="text" name="txt_courier_company" id="txt_courier_company" class="text_boxes" style="width:140px" /></td>
						<td align="right">GSP Courier Date</td>
						<td><input type="text" name="txt_courier_date" id="txt_courier_date" class="datepicker" style="width:140px" /> </td>
						<td align="right">Bnk-2-Bnk Cour No</td>
						<td><input type="text" name="txt_bnk_to_bnk_cour_no" id="txt_bnk_to_bnk_cour_no" class="text_boxes" style="width:140px" /></td>                                   
					</tr>
					<tr>                                   
						<td align="right">Bnk-2-Bnk Cour Date</td>
						<td><input type="text" name="txt_bnk_to_bnk_cour_date" id="txt_bnk_to_bnk_cour_date" class="datepicker" style="width:140px" /></td>
						<td align="right" >Lien Bank </td>
						<td>  
							<? 
							echo create_drop_down( "cbo_lien_bank", 150, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where lien_bank=1 and is_deleted = 0 AND status_active = 1 order by bank_name","id,bank_name", 1, "-- Select --", $selected, "",1 );
							?>
						</td>
						<td align="right">LC/SC Currency</td>
						<td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --", $currencyID, "",1 ); ?></td>                                   
					</tr>
					<tr>                                   
						<td align="right">Remarks</td>
						<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px" /></td>
						<td align="right">Pay Term</td>
						<td><? echo create_drop_down("cbo_pay_term", 150, $pay_term, "", 1, "--- Select ---", 0, "", 0, '1,2');?></td>
						<td align="right">Issuing Bank Info</td>
						<td>
						<input type="text" name="txt_issue_bank_info" id="txt_issue_bank_info" class="text_boxes" style="width:140px" onDblClick="fn_bank_info()" placeholder="Browse" readonly />
						<input type="hidden" name="txt_issue_bank_info_dtls" id="txt_issue_bank_info_dtls" />
						</td>
					</tr>
					<tr>                                   
						<td align="right">Bill Value</td>
						<td><input type="text" name="txt_bill_value" id="txt_bill_value" class="text_boxes" style="width:140px" /></td>
						<td align="right">Cuml. Neg. Amount	</td>
						<td> <input type="text" name="txt_Cuml_Neg_Amount" id="txt_Cuml_Neg_Amount" class="text_boxes" style="width:140px" /></td>
						<td align="right">Balance Neg. Amount</td>
						<td>
						<input type="text" name="txtbalance_neg_amount" id="txtbalance_neg_amount" class="text_boxes" style="width:140px" readonly disabled   />       
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>                                   
						<td><input type="button" id="set_button" class="image_uploader" style="width:140px;" value="Terms" onClick="open_terms_acc_popup('Terms Dtls')" /></td>                       
						<td>&nbsp;</td>
						<td><input type="button" id="image_button" class="image_uploader" style="width:151px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('mst_tbl_id').value, '', 'Doc. Submission to Bank', 2, 1)" /></td>
						<td id="posted_status_td" style="color:red; font-size:24px; text-align:center"></td>
						<td>&nbsp;</td>
					</tr>         
				</table>
            </fieldset>                  
            <br />              
            <fieldset>
                <legend>Transaction Details</legend>
                <table id='trans_details' width="1000" class="rpt_table" rules="all">
                    <thead id='trans'>
                    	<tr>
                            <th width="220" >Account Head</th>
                            <th width="130" >Negotiation Date</th>
                            <th width="120" >AC No.</th>
                            <th width="120" >Loan No.</th>
                            <th width="120" >Domestic Currency</th>
                            <th width="120" >Conversation Rate</th>
                            <th width="120" >LC/SC Currency</th>
                            <th width="80" >Action</th>
                        </tr>    
                    </thead>
                    <tbody id="transaction_container">                         
                        <tr id="tr0">
                            <td colspan="8"><b><center>Please Select LC/SC to view Transaction</center></b></td>
                        </tr>
                    </tbody> 
                    <tfoot>
                    	<th colspan="3">Sum&nbsp;&nbsp;</th> 	                        	
                        <td><input type="text" id="total_dom_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td>	
                        <th>&nbsp;</th>	
                        <td><input type="text" id="total_foreign_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td>	
                        <th></th>
                     </tfoot>
                 </table>
            </fieldset>         
                      
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr> 
                   <td colspan="7" align="center"></td>
                </tr>
                <tr>
                    <td align="center" colspan="7" valign="middle" class="button_container">
                          <!-- details table id for update -->
                          <input type="hidden" id="mst_tbl_id" name="mst_tbl_id" value="" />
                          <input type="hidden" id="invoice_tbl_id" name="invoice_tbl_id" value="" />&nbsp;<a id="print_excel19" href="" style="text-decoration:none" download hidden>PP</a>					
                           <!-- -->
                         <? echo load_submit_buttons( $permission, "fnc_export_doc_sub_entry", 0,0,"fnResetForm()",1);?>
                         
                         <span id="button_data_panel"></span>
                    </td>
               </tr> 
            </table>          	
         </form>
        </fieldset> 
 	</div>
	 <div id="data_panel"></div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>